<?php

function drawNameTransfer($admin,$given_fqdn="none"){
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $registration_added_price;

	global $pro_mysql_domain_table;
	global $pro_mysql_client_table;
	global $registry_api_modules;

	global $form_enter_dns_infos;
	global $form_enter_auth_code;
	global $whois_forwareded_params;
	global $secpayconf_currency_letters;

        global $allTLD;

	get_secpay_conf();

	$out = "";

	if(isset($_REQUEST["toreg_domain"])){
		$toreg_domain = $_REQUEST["toreg_domain"];
	}
	if(isset($_REQUEST["toreg_extention"])){
		$toreg_extention = $_REQUEST["toreg_extention"];
	}
	if($given_fqdn != "none" && !isset($toreg_extention)){
		$c = strrpos($given_fqdn,".");
		$toreg_extention = find_domain_extension($given_fqdn);
		$toreg_domain = str_replace($toreg_extention, "", $given_fqdn);
                # echo "extension: $toreg_extention    domain: $toreg_domain<br />";
                
	}

	// Step 1: enter domain name and check domain transferability
	$form_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"dtcrm_action\" value=\"transfer_domain\">
<input type=\"hidden\" name=\"add_regortrans\" value=\"transfer\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
";

	$out .= "<br><h3>". _("Transfer from another registrar to this server:") ."</h3>
<i><u>". _("Step1: check if domain is transferable") ."</u></i>";

         # echo "Checking1 $toreg_domain$toreg_extention<br />";

	if(!isset($toreg_extention) || $toreg_extention == "" ||
	!isset($toreg_domain) || $toreg_domain == "" ||
	($toreg_extention != ".com" && $toreg_extention != ".net" &&
	$toreg_extention != ".org" && $toreg_extention != ".biz" &&
	$toreg_extention != ".name" && $toreg_extention != ".info" &&
	$toreg_extention != ".co.uk")		){
		$out .= "$form_start<br>
". _("Please enter the domain name you wish to transfer:") ."<br>
".make_registration_tld_popup();
		return $out;
	}

	$form_start .= "<input type=\"hidden\" name=\"toreg_domain\" value=\"$toreg_domain\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"$toreg_extention\">";

	$regz = registry_check_transfer($toreg_domain.$toreg_extention);
       # echo "Checking2 $toreg_domain$toreg_extention<br />";
	if($regz["is_success"] != 1){
		die("<font color=\"red\">". _("TRANSFER CHECK FAILED: registry server did not respond correctly.") ."</font>");
	}

	if($regz["attributes"]["transferrable"] != 1){
		$out .= "<br><font color=\"red\">". _("TRANSFER CHECK FAILED") ."</font><br>
". _("Server said: ") .$regz["attributes"]["reason"]."<br>
$form_start<br>
". _("Please enter the domain name you wish to transfer:") ."<br>
".make_registration_tld_popup();
		return $out;
	}
	$out .= "<br><font color=\"green\">". _("TRANSFER CHECK SUCCESSFUL") ."</font><br><br>";

	// Step 2: enter whois infos
	$out .= "<i><u>". _("Step 2: select contacts for domain transfer") ."</u></i><br>";
	if(	!isset($_REQUEST["dtcrm_owner_hdl"]) || $_REQUEST["dtcrm_owner_hdl"] == "" ||
		!isset($_REQUEST["dtcrm_admin_hdl"]) || $_REQUEST["dtcrm_admin_hdl"] == "" ||
		!isset($_REQUEST["dtcrm_billing_hdl"]) || $_REQUEST["dtcrm_billing_hdl"] == "" ||
		!isset($_REQUEST["toreg_dns1"]) || $_REQUEST["toreg_dns1"] == "" ||
		!isset($_REQUEST["toreg_dns2"]) || $_REQUEST["toreg_dns2"] == ""){
		$out .= $form_start . whoisHandleSelection($admin);
		$out .= $form_enter_dns_infos;
                $out .= $form_enter_auth_code;
		$out .= "<br>".submitButtonStart(). _("Proceed to transfer") .submitButtonEnd() ."</form>";
		return $out;
	}
	$form_start .= $whois_forwareded_params;
		
	$out .= "DNS1: ".$_REQUEST["toreg_dns1"]."<br>";
	$out .= "DNS2: ".$_REQUEST["toreg_dns2"]."<br><br>";

	$fqdn = $toreg_domain . $toreg_extention;
	$fqdn_price = $price = find_domain_price($toreg_extention);

	if($admin["info"]["id_client"] != 0){
		$remaining = $admin["client"]["dollar"];
	}else{
		$out .= _("You don't have a client ID. Please contact us.") ;
		$remaining = 0;
		return $out;
	}

	// Step 3: check account balance and transfer the domain name after transaction aprooval
	$out .= "<i><u>Step3: Proceed for transfer</u></i><br>";
	$out .= _("Remaining on your account: ") ." " . $remaining . " $secpayconf_currency_letters<br>
". _("Total price: ") ." ". $fqdn_price . " $secpayconf_currency_letters<br><br>";

        if ( !isset($_REQUEST["authcode"])) {
		$out .= $form_enter_auth_code;
        } else {
                $out .= ("Auth Code:")." ".$_REQUEST["authcode"]."<br />";
        }
	if(isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "return_from_paypal_domain_add"){
		$ze_refund = isPayIDValidated(mysql_real_escape_string($_REQUEST["pay_id"]));
		if($ze_refund == 0){
			$out .= "<font color=\"red\">". _("The transaction failed, please try again!") ."</font>";
		}else{
			$out .= "<font color=\"green\">". _("Your account has been credited.") ."</font><br>";
			$q = "UPDATE $pro_mysql_client_table SET dollar = dollar+".$ze_refund." WHERE id='".$admin["info"]["id_client"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$remaining += $ze_refund;
		}
	}
	if($fqdn_price > $remaining){
		$payid = createCreditCardPaiementID($fqdn_price,$admin["info"]["id_client"],
			"Domain name registration ".$_REQUEST["toreg_extention"],"no");
		$return_url = htmlentities($_SERVER["PHP_SELF"])."?adm_login=$adm_login&adm_pass=$adm_pass"
			."&addrlink=$addrlink&add_domain_type=".$_REQUEST["add_domain_type"]
			."&add_regortrans=".$_REQUEST["add_regortrans"]."&toreg_domain=".$_REQUEST["toreg_domain"]
			."&toreg_extention=".$_REQUEST["toreg_extention"]."&dtcrm_owner_hdl=".$_REQUEST["dtcrm_owner_hdl"]
			."&dtcrm_admin_hdl=".$_REQUEST["dtcrm_admin_hdl"]."&dtcrm_billing_hdl=".$_REQUEST["dtcrm_billing_hdl"]
			."&toreg_dns1=".$_REQUEST["toreg_dns1"]."&toreg_dns2=".$_REQUEST["toreg_dns2"]
			."&toreg_dns3=".$_REQUEST["toreg_dns3"]."&toreg_dns4=".$_REQUEST["toreg_dns4"]
			."&toreg_dns5=".$_REQUEST["toreg_dns5"]."&toreg_dns6=".$_REQUEST["toreg_dns6"]
			."&toreg_period=1&inner_action=return_from_paypal_domain_add&payid=$payid";
		if(isset($_REQUEST["action"]))
			$return_url .= "&action=".$_REQUEST["action"];
		if(isset($_REQUEST["dtcrm_action"]))
			$return_url .= "&dtcrm_action=".$_REQUEST["dtcrm_action"];

		$paybutton = paynowButton($payid,$fqdn_price,
			"Domain name registration ".$_REQUEST["toreg_extention"],$return_url);

		$out .= _("You currently don't have enough funds in your account. You will be redirected to our payment system. Please click on the button below to pay.") ."<br><br>
$paybutton";
		return $out;
	}
	// Check for confirmation
	if(!isset($_REQUEST["toreg_confirm_transfert"]) || $_REQUEST["toreg_confirm_transfert"] != "yes"){
		$out .= _("You have enough funds in your account to proceed with transfer. Press the confirm button to continue.") ."<br><br>
$form_start
<input type=\"hidden\" name=\"toreg_confirm_transfert\" value=\"yes\">
<input type=\"hidden\" name=\"authcode\" value=\"".$_REQUEST['authcode']."\">
".submitButtonStart(). _("Proceed with name transfer") .submitButtonEnd() ."
</form>";		return $out;
	}
	///////////////////////////////////////
// START OF DOMAIN NAME TRANSFERT //
	$owner_id = $_REQUEST["dtcrm_owner_hdl"];
	$billing_id = $_REQUEST["dtcrm_billing_hdl"];
	$admin_id = $_REQUEST["dtcrm_admin_hdl"];
	$teck_id = $_REQUEST["dtcrm_teck_hdl"];
        $authcode = $_REQUEST["authcode"];
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
	$q = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$new_user = "no";
	}else{
		$new_user = "yes";
	}
//	sleep(2);
	$regz = registry_transfert_domain($adm_login,$adm_pass,$fqdn,$contacts,$dns_servers,$new_user,$authcode);

	if($regz["is_success"] != 1){
		$out .= "<font color=\"red\"><b>". _("Transfer failed") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i>";
		return $out;
	}
	$out .= "<font color=\"green\"><b>Transfert succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";

	$operation = $remaining - $fqdn_price;
	$query = "UPDATE $pro_mysql_client_table SET dollar='$operation' WHERE id='".$admin["info"]["id_client"]."';";
	mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());


	$q = "SELECT * FROM $pro_mysql_domain_table WHERE domain='$fqdn';";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $n = mysql_num_rows($r);

        // Is this a transfer of a domain already hosted?
        if ($n == 0) 
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
		newWhois($fqdn,$owner_id,$billing_id,$admin_id,$teck_id,$period="1",$ns_ar,$registry_api_modules[$id]["name"]);
	}



	$out .= "<font color=\"green\"><b>". _("Successfully added your domain name to the hosting database") ."</b></font><br>";

	$out .= _("Click") . " " ."<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">". _("here") ."</a>". " " . _("to refresh the menu or add another domain name.") ;

// END OF DOMAIN NAME TRANSFERT //
/////////////////////////////////////

	return $out;
}

?>
