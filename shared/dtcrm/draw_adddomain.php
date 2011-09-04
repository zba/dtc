<?php

function drawAdminTools_AddDomain($admin){
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $registration_added_price;

	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;

	global $form_enter_dns_infos;
	global $form_enter_domain_name;
	global $whois_forwareded_params;
	global $form_period_popup;
	global $conf_webmaster_email_addr;
	global $conf_use_registrar_api;
	global $pro_mysql_pending_queries_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_client_table;
	global $pro_mysql_product_table;

	global $registry_api_modules;

	global $secpayconf_currency_letters;
	global $pro_mysql_handle_table;

	get_secpay_conf();

	$out = "";

$form_start = "
<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
";

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_one_domain" && !isset($_REQUEST["confirm_delete"])){
		if(!isHostnameOrIP($_REQUEST["to_delete_domain_name"])){
			return "<br><br><font color=\"red\">"._("Not a valid domain name")."</font>";
		}
		checkLoginPassAndDomain($adm_login,$adm_pass,$_REQUEST["to_delete_domain_name"]);
		$out .= "<br><h3>"._("Delete a domain name:") ."</h3>";
		if( !isset($_REQUEST["confirm_delete"]) || $_REQUEST["confirm_delete"] != "yes"){
			$out .= _("You are about to delete the following domain name: ")."<b>".$_REQUEST["to_delete_domain_name"]."</b><br>";
			$out .= _("This action will delete all files, mailboxes, ftp account, etc. of this domain.")."<br>".
			_("Please confirm.")."<br><br>";
			$out .= $form_start."<input type=\"hidden\" name=\"to_delete_domain_name\" value=\"".$_REQUEST["to_delete_domain_name"]."\">
			<input type=\"hidden\" name=\"confirm_delete\" value=\"yes\">
			<input type=\"hidden\" name=\"action\" value=\"delete_one_domain\">"
			.submitButtonStart(). _("Confirm deletion") .submitButtonEnd()."</form>";
			return $out;
		}
	}

	// User is trying to add a new service, let's complete the form!
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_new_service"){
		if(!isRandomNum($_REQUEST["product_id"])){
			$out .= _("The product ID is not a valid integer number.");
			return $out;
		}
		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["product_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
                $n = mysql_num_rows($r);
                if($n != 1){
                	$out .= _("Cannot reselect product: registration failed!") ;
                	return $out;
                }
                $product = mysql_fetch_array($r);
			switch($product["heb_type"]){
			default:
			case "shared":  // -> Something has to be done to select dedicated servers location in the form !!!
			case "server":
				$added1 = "<input type=\"hidden\" name=\"vps_location\" value=\"node0001.example.com\">
<input type=\"hidden\" name=\"vps_os\" value=\"debian\">";
				break;
			case "vps":
				$added1 = _("VPS location: ")."<select name=\"vps_location\">".vpsLocationSelector()."</select><br>".
				_("VPS OS: ")."<select name=\"vps_os\">
<option value=\"debian\">Debian</option>
<option value=\"centos\">CentOS</option>
<option value=\"gentoo\">Gentoo</option>
<option value=\"netbsd\">NetBSD</option>
</select><br>";
				break;
			}
		$out .= "<br><br><h3>"._("Add another service to your account:")."</h3>".
"<br><form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"product_id\" value=\"".$_REQUEST["product_id"]."\">
<input type=\"hidden\" name=\"action\" value=\"add_new_service\">".$added1."
" . _("Special setup notes:") . ":<textarea name=\"custom_notes\" cols=\"50\" rows=\"5\"></textarea><br>
".submitButtonStart(). _("Register") .submitButtonEnd()."
";
		
		return $out;
	}

	// Registration, hosting, or both ?
	if(!isset($_REQUEST["add_domain_type"]) || ($_REQUEST["add_domain_type"] != "domregandhosting" &&
		$_REQUEST["add_domain_type"] != "domreg" &&
		$_REQUEST["add_domain_type"] != "hosting")){

		$q = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login' ORDER BY name;";
		$r = mysql_query($q);
		$n = mysql_num_rows($r);
		if($n > 0){
			$out .= "<br><h3>". _("Delete a domain name:") ."</h3>
			$form_start<table border=\"0\"><tr><td><input type=\"hidden\" name=\"action\" value=\"delete_one_domain\"><select name=\"to_delete_domain_name\">";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$name = $a["name"];
				$out .= "<option value=\"$name\">$name</option>";
			}
			$out .= "</select></td><td>".submitButtonStart(). _("Ok") .submitButtonEnd();
			$out .= "</td></tr></table></form>";
		}

		$out .= "<br><h3>". _("What do you want to add:") ."</h3>
$form_start";
		if($conf_use_registrar_api == "yes"){
			$out .= "<input type=\"radio\" name=\"add_domain_type\" value=\"domregandhosting\" checked>". _("Hosting plus domain name registration or transfer")."<br>";
			$add_domain_type_checked = " ";
		}else{
			$add_domain_type_checked = " checked ";
		}
		$out .= "<input type=\"radio\" name=\"add_domain_type\" value=\"hosting\" checked>". _("Hosting only") ."<br>
".submitButtonStart(). _("Ok") .submitButtonEnd()."
</form>
";


		$out .= "<br><br><h3>"._("Add another service to your account:")."</h3>";
		if( isset($admin["data"])){
			$added_conditions = " AND heb_type NOT LIKE 'shared' ";
		}else{
			$added_conditions = "";
		}
		$q = "SELECT * FROM $pro_mysql_product_table WHERE private='no' AND renew_prod_id='0' AND heb_type NOT LIKE 'ssl' $added_conditions;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
//			if($i > 0){
//				$out .= " - ";
//			}
			$out .= "<form action=\"?\">
			<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
			<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
			<input type=\"hidden\" name=\"action\" value=\"add_new_service\">
			<input type=\"hidden\" name=\"product_id\" value=\"".$a["id"]."\">
			<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
			".submitButtonStart().$a["name"].submitButtonEnd()."</form>";
//			$out .= "<a href=\"/dtc/new_account.php?action=add_new_service&adm_login=$adm_login&product_id=".$a["id"]."\">".$a["name"]."</a>";
		}
		return $out;
	}
	$form_start .= "<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">";
//	$form_start .= "<input type=\"hidden\" name=\"add_domain_type\" value=\"domregandhosting\">";
	if($_REQUEST["add_domain_type"] == "hosting"){
		// The don't want name registration or transfer,
		// Simply add the domain.
		if($admin["info"]["allow_add_domain"] == "no"){
			return _("You curently don't have enough privileges to add domain names. If you often add domain names, you can ask the administrator to add this capability to your account. To request hosting for a new domain without domain name registration, please write to:")."<br>".
"<a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC] More domains\">$conf_webmaster_email_addr</a>.";
		}
		if($admin["info"]["max_domain"] != 0){
			$maxdomq = "SELECT COUNT(name) AS numofdomains FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
			$maxdomr = mysql_query($maxdomq)or die("Cannot query $maxdomq line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$maxdoma = mysql_fetch_array($maxdomr);
			$num_of_installed_domains = $maxdoma["numofdomains"];
			if($num_of_installed_domains >= $admin["info"]["max_domain"]){
				return _("You have reached the maximum number of domains that you are allowed to configure with this type of account.
If you want to add more domain names, you should get in touch by opening a new support ticket.");
			}
		}
		if(!isset($_REQUEST["domain_name"]) || $_REQUEST["domain_name"] == ""){
			return "<br><b><u>". _("Please enter the domain name you wish to add:") ."</u></b><br>
$form_start<input type=\"text\" name=\"domain_name\" value=\"\">
".submitButtonStart(). _("Ok") .submitButtonEnd()."
</form>";
		}
		if(!isHostname($_REQUEST["domain_name"])){
			return _("Domain name is not in correct format. Please enter another name.") ;
		}
		$q = "SELECT * FROM $pro_mysql_domain_table WHERE name='".$_REQUEST["domain_name"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n > 0){
			return _("This domain name already hosted here, please enter another name.") ;
		}
		if($admin["info"]["allow_add_domain"] == "check"){
			$q = "INSERT INTO $pro_mysql_pending_queries_table (adm_login,domain_name,date) VALUES ('$adm_login','".$_REQUEST["domain_name"]."','".date("Y-m-d H:i")."');";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			return "<br><u><b>". _("Your domain name will be soon validated:") ."</b></u><br>".
			_("An administrator will examine your request shortly, and validate the addition of this domain name to your account. You curently don't have enough privileges to add domain names. If you often add domain names, you can ask the administrator to grant you the privilege of automatic domain name addition. To request hosting for a new domain name, without administrator validation or domain name registration, please write to:") ."<br>
<a href=\"$conf_webmaster_email_addr?subject=[DTC] More domains\">$conf_webmaster_email_addr</a>.<br>
<br>
". _("You can add another domain name:") ."
$form_start<input type=\"text\" name=\"domain_name\" value=\"\">
".submitButtonStart(). _("Ok") .submitButtonEnd()."
</form>
";
		}
		addDomainToUser($adm_login,$adm_pass,$_REQUEST["domain_name"]);
		return "<br><u><b>". _("Your domain name is now ready:") ."</b></u><br>
". _("You may verify the configuration by clicking here:") ."<br>
<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=".$_REQUEST["domain_name"]."\">".$_REQUEST["domain_name"]."</a><br>
<br>
". _("You may add another domain name:") ."
$form_start<input type=\"text\" name=\"domain_name\" value=\"\">
".submitButtonStart(). _("Ok") .submitButtonEnd()."
</form>
";
	}

	// Registration or domain transfer ?
	if(!isset($_REQUEST["add_regortrans"]) || ($_REQUEST["add_regortrans"] != "register" &&
		$_REQUEST["add_regortrans"] != "transfer")){
		$out .= "<br><h3>". _("Do you want to transfer an existing domain or register a new domain?") ."</h3>
$form_start
<input type=\"radio\" name=\"add_regortrans\" value=\"register\" checked>". _("Register a new domain") ."<br>
<input type=\"radio\" name=\"add_regortrans\" value=\"transfer\">". _("Transfer an existing domain from another registrar") ."<br>
".submitButtonStart(). _("Ok") .submitButtonEnd()."
</form>
";
		return $out;
	}
	if($_REQUEST["add_regortrans"] == "transfer") return drawNameTransfer($admin);
	$form_start .= "<input type=\"hidden\" name=\"add_regortrans\" value=\"register\">";

	// Start registration procedure (with or without hosting)

	$out .= "<br><h3>". _("Register a domain name") ."</h3>";
	$out .= "<i><u>". _("Step 1: Verify availability") ."</u></i><br>";
	if(!isset($_REQUEST["toreg_domain"]) || $_REQUEST["toreg_domain"] == "" ||
	!isset($_REQUEST["toreg_extention"]) || $_REQUEST["toreg_extention"] == ""){
		$out .= "<br>". _("Enter the domain name you want to register:") ."<br>
$form_start ".make_registration_tld_popup()."</form>";
		return $out;
	}

	$fqdn = $_REQUEST["toreg_domain"] . $_REQUEST["toreg_extention"];
	$domlookup = registry_check_availability($fqdn);
	if($domlookup["is_success"] != 1){
		$out .= "<font color=\"red\">". _("Could not connect to domain registration server: please try again later.") 
			."</font><br>".$domlookup['response_text'];
		return $out;
	}

	if($domlookup["attributes"]["status"] != "available"){
		$out .= "<br>
". _("Sorry, the domain name ") ." <b>$fqdn</b> ". _("is NOT available for registration. The registration server returned: ") 
."<br><font color=\"red\">" . $domlookup["response_text"] . "</font>
<br><br>
Have another try:<br>$form_start ".make_registration_tld_popup()."</form>";
		return $out;
	}
	$form_start .= "<input type=\"hidden\" name=\"toreg_domain\" value=\"".$_REQUEST["toreg_domain"]."\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"".$_REQUEST["toreg_extention"]."\">";

	$q = "SELECT * FROM $pro_mysql_domain_table WHERE name='$fqdn';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 0){
		$out .= "<br>
". _("The domain name") . " <b>$fqdn</b> ". _("is already in use in this server: you can't register that domain name.")."<br>".
_("Try again:") . "<br>$form_start ".make_registration_tld_popup()."</form>";
		return $out;
	}

	// DOMAIN IS AVAILABLE, PROCEED DO REGISTRATION
	$out .= "Domain name <b>$fqdn</b> is available for registration.<br><br>
<i><u>". _("Step 2: Enter whois information") ."</u></i><br>
";
//http://dtc.example.com/dtc/index.php?adm_login=dtc&adm_pass=bemybest&
//addrlink=myaccount%2Fadddomain&
//action=dtcrm_add_domain&add_domain_type=domreg&add_regortrans=register&
//toreg_domain=yugluxrfvcd&toreg_extention=.com&
//dtcrm_owner_hdl=1&dtcrm_billing_hdl=1&dtcrm_admin_hdl=1&
//toreg_dns1=default&toreg_dns2=default&
//toreg_period=1
	if(!isset($_REQUEST["dtcrm_owner_hdl"]) || $_REQUEST["dtcrm_owner_hdl"] == "" ||
		!isset($_REQUEST["dtcrm_admin_hdl"]) || $_REQUEST["dtcrm_admin_hdl"] == "" ||
		!isset($_REQUEST["dtcrm_billing_hdl"]) || $_REQUEST["dtcrm_billing_hdl"] == "" ||
		!isset($_REQUEST["dtcrm_teck_hdl"]) || $_REQUEST["dtcrm_teck_hdl"] == "" ||
		!isset($_REQUEST["toreg_dns1"]) || $_REQUEST["toreg_dns1"] == "" ||
		!isset($_REQUEST["toreg_dns2"]) || $_REQUEST["toreg_dns2"] == "" ||
		$_REQUEST["toreg_period"] < 1 || $_REQUEST["toreg_period"] > 10){

		$year = _("year") ;
		$years = _("years") ;
		$out .= _("Please select registrant and the 3 contact handles (Admin, Tech, Billing) you want to use for registering the domain name.") ."<br><br>$form_start";
		$out .= whoisHandleSelection($admin);
		if ( isset($domlookup["attributes"]["minperiod"]) )
                     $minreg = str_replace("Y", "", $domlookup["attributes"]["maxperiod"]);
                else
                     $minreg = 1;
                if ( isset($domlookup["attributes"]["maxperiod"]) )
                     $maxreg = str_replace("Y", "", $domlookup["attributes"]["maxperiod"]);
		else
		     $maxreg = 10;
		$out .= "<br>$form_enter_dns_infos<br><br>
". _("Select the registration period for this domain name:") ."<br>
<select name=\"toreg_period\"><option value=\"1\">1 $year</option>";
		for ($p=2;$p<=$maxreg;$p++) {
                   $out .= "<option value=\"$p\"";
                   if ($p == $minreg)
                      $out .= " selected>Minimum";
		   else
		      $out .= ">";
                   $out .= " $p $years</option>";
                }
                $out .= "</select><br><br>
".submitButtonStart(). _("Ok") .submitButtonEnd()."
</form>
";
		return $out;
	}
	$form_start .= "$whois_forwareded_params
<input type=\"hidden\" name=\"toreg_period\" value=\"".$_REQUEST["toreg_period"]."\">";

	$out .= "Registration for <b>" . $_REQUEST["toreg_period"] . " years</b><br>";
	$out .= "DNS1: " . $_REQUEST["toreg_dns1"] . "<br>";
	$out .= "DNS2: " . $_REQUEST["toreg_dns2"] . "<br><br>";
	$out .= "<i><u>". _("Step 3: Proceed to registration") ."</u></i>
$form_start
";

	// Check if paiement has just occured !
	if(isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "return_from_paypal_domain_add"){
		$ze_refund = isPayIDValidated(mysql_real_escape_string($_REQUEST["pay_id"]));
		if($ze_refund == 0){
			$out .= "<font color=\"red\">The transaction failed, please try again!</font>";
		}else{
			$out .= "<font color=\"green\">Funds added to your account</font>";
			$q = "UPDATE $pro_mysql_client_table SET dollar = dollar+".$ze_refund." WHERE id='".$admin["info"]["id_client"]."';";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$admin["client"]["dollar"] += $ze_refund;
		}
	}

	// Check billing to know if user has enough money on his account
	$price = find_domain_price($_REQUEST["toreg_extention"]);
	$fqdn_price = $price;
	$fqdn_price *= $_REQUEST["toreg_period"];

	if($admin["info"]["id_client"] != 0){
		$remaining = $admin["client"]["dollar"];
	}else{
		$out .= _("You don't have a client ID. Please contact us.") ."<br>";
		$remaining = 0;
		return $out;
	}
	$out .= _("Remaining on your account: ") ." $secpayconf_currency_letters" . $remaining . "<br>
". _("Total price: ") ." ".$secpayconf_currency_letters."". $fqdn_price . "<br><br>";
	if($fqdn_price > $remaining){
		$to_pay = $fqdn_price - $remaining;

		$payid = createCreditCardPaiementID($to_pay,$admin["info"]["id_client"],
				"Domain name registration ".$_REQUEST["toreg_extention"],"no");
		$return_url = htmlentities($_SERVER["PHP_SELF"])."?adm_login=$adm_login&adm_pass=$adm_pass"
		."&addrlink=$addrlink&action=dtcrm_add_domain&add_domain_type=".$_REQUEST["add_domain_type"]
		."&add_regortrans=".$_REQUEST["add_regortrans"]."&toreg_domain=".$_REQUEST["toreg_domain"]
		."&toreg_extention=".$_REQUEST["toreg_extention"]."&dtcrm_owner_hdl=".$_REQUEST["dtcrm_owner_hdl"]
		."&dtcrm_admin_hdl=".$_REQUEST["dtcrm_admin_hdl"]."&dtcrm_billing_hdl=".$_REQUEST["dtcrm_billing_hdl"]
		."&dtcrm_teck_hdl=".$_REQUEST["dtcrm_teck_hdl"]
		."&toreg_dns1=".$_REQUEST["toreg_dns1"]."&toreg_dns2=".$_REQUEST["toreg_dns2"]
		."&toreg_dns3=".$_REQUEST["toreg_dns3"]."&toreg_dns4=".$_REQUEST["toreg_dns4"]
		."&toreg_dns5=".$_REQUEST["toreg_dns5"]."&toreg_dns6=".$_REQUEST["toreg_dns6"]
		."&toreg_period=".$_REQUEST["toreg_period"]."&inner_action=return_from_paypal_domain_add&payid=$payid";
		$paybutton = paynowButton($payid,$to_pay,
				"Domain name registration ".$_REQUEST["toreg_extention"],$return_url);

		$out .= _("You currently don't have enough funds in your account. You will be redirected to our payment system. Please click on the button below to pay.") ."<br>
<br><br>
$form_start
".submitButtonStart(). _("Payment complete. Proceed to checkout") .submitButtonEnd()."
</form> $paybutton";
		return $out;
	}

	// Check for confirmation
	if(!isset($_REQUEST["toreg_confirm_register"]) || $_REQUEST["toreg_confirm_register"] != "yes"){
		$out .= _("You have enough funds on your account to continue with registration. Press the confirm button to proceed.") ."<br><br>
$form_start
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
".submitButtonStart(). _("Proceed to name registration") .submitButtonEnd()."
</form>";
		return $out;
	}
///////////////////////////////////////
// START OF DOMAIN NAME REGISTRATION //
	$owner_id = $_REQUEST["dtcrm_owner_hdl"];
	$billing_id = $_REQUEST["dtcrm_billing_hdl"];
	$admin_id = $_REQUEST["dtcrm_admin_hdl"];
	$teck_id = $_REQUEST["dtcrm_teck_hdl"];
	$contacts = getContactsArrayFromID($owner_id,$billing_id,$admin_id,$teck_id);
	$dns_servers = array();
	for($i=1;$i<7;$i++){
		if(isset($_REQUEST["toreg_dns$i"]) && isHostname($_REQUEST["toreg_dns$i"])){
			$dns_servers[] = $_REQUEST["toreg_dns$i"];
		}else if($i == 1){
			$dns_servers[] = $conf_addr_primary_dns;
		}else if($i == 2){
			$dns_servers[] = $conf_addr_secondary_dns;
		}
	}
	$q = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login' AND whois='here';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$new_user = "no";
	}else{
		$new_user = "yes";
	}
//	sleep(2);
	$regz = registry_register_domain($adm_login,$adm_pass,$fqdn,$_REQUEST["toreg_period"],$contacts,$dns_servers,$new_user);

	if($regz["is_success"] != 1){
		$out .= "<font color=\"red\"><b>". _("Registration failed") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i>";
		return $out;
	}
	$out .= "<font color=\"green\"><b>Registration succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";

	$operation = $remaining - $fqdn_price;
	$query = "UPDATE $pro_mysql_client_table SET dollar='$operation' WHERE id='".$admin["info"]["id_client"]."';";
	mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());

	addDomainToUser($adm_login,$adm_pass,$fqdn,$adm_pass);

	if($regz["is_success"] == 1){
		$id = find_registry_id($fqdn);
		$q = "UPDATE $pro_mysql_domain_table SET registrar='".$registry_api_modules[$id]["name"]."' WHERE name='$fqdn';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

		unset($ns_ar);
		$ns_ar = array();
		$ns_ar[] = $_REQUEST["toreg_dns1"];
		$ns_ar[] = $_REQUEST["toreg_dns2"];
		if(isset($_REQUEST["toreg_dns3"]) && $_REQUEST["toreg_dns3"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns3"];
		if(isset($_REQUEST["toreg_dns4"]) && $_REQUEST["toreg_dns4"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns4"];
		if(isset($_REQUEST["toreg_dns5"]) && $_REQUEST["toreg_dns5"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns5"];
		if(isset($_REQUEST["toreg_dns6"]) && $_REQUEST["toreg_dns6"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns6"];
		newWhois($fqdn,$owner_id,$billing_id,$admin_id,$teck_id,$_REQUEST["toreg_period"],$ns_ar,$registry_api_modules[$id]["name"]);
	}



	$out .= "<font color=\"green\"><b>". _("Successfully added your domain name to the hosting database") ."</b></font><br>";

	$out .= _("Click") . " " ."<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">". _("here") ."</a>". " " . _("to refresh the menu or add another domain name.") ;

// END OF DOMAIN NAME REGISTRATION //
/////////////////////////////////////


	return $out;
}


?>
