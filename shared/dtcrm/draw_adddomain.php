<?php

function drawAdminTools_AddDomain($admin){
	global $lang;
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $registration_added_price;

	global $form_enter_dns_infos;
	global $form_enter_domain_name;
	global $whois_forwareded_params;
	global $form_period_popup;
	global $conf_webmaster_email_addr;
	global $conf_use_registrar_api;
	global $pro_mysql_pending_queries_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_client_table;

	global $pro_mysql_handle_table;

	global $lang;

	global $txt_dtcrm_what_to_do;
	global $txt_dtcrm_hosting_and_domain_reg;
	global $txt_dtcrm_hosting_only;
	global $txt_dtcrm_enter_domain_to_add;
	global $txt_dtcrm_your_domain_will_be_soon;
	global $txt_dtcrm_soon_an_admin_will_have_a_look;
	global $txt_dtcrm_add_another_domain;
	global $txt_dtcrm_transfer_existing_or_new;
	global $txt_dtcrm_register_new_domain;
	global $txt_dtcrm_transfer_from_another_registrar;
	global $txt_dtcrm_title_register_a_domain_name;
	global $txt_dtcrm_register_step1;
	global $txt_crm_enter_the_domain_name_you_wish_to_register;
	global $txt_dtcrm_not_enough_privileges;
	global $txt_dtcrm_not_correct_format;
	global $txt_domain_name_already_hosted_here;
	global $txt_dtcrm_domain_name_now_ready;
	global $txt_dtcrm_you_can_check_config;
	global $txt_dtcrm_or_you_can_add_another_domain;
	global $txt_dtcrm_could_not_connect_to_api;
	global $txt_sorry_domain_name_not_available1;
	global $txt_sorry_domain_name_not_available2;
	global $txt_please_select_the_3_contact_handles;
	global $txt_dtcrm_select_how_long_you_want_to_register;
	global $txt_dtcrm_year;
	global $txt_dtcrm_years;
	global $txt_dtcrm_you_dont_have_a_client_id;
	global $txt_dtcrm_register_domain_step3;
	global $txt_dtcrm_remaining_on_your_account;
	global $txt_dtcrm_total_price;
	global $txt_dtcrm_you_currently_dont_have_enough_funds;
	global $txt_dtcrm_you_have_enough_funds_proceed;
	global $txt_dtcrm_button_paiement_done_checkout;
	global $txt_dtcrm_button_proceed_to_registration;
	global $txt_dtcrm_registration_failed;
	global $txt_dtcrm_registration_succesfull;
	global $txt_dtcrm_server_said;
	global $txt_dtcrm_succesfully_added_domain;
	global $txt_dtcrm_click;
	global $txt_dtcrm_here;
	global $txt_dtcrm_to_refresh_the_menu_or_add_another_domain;
	global $txt_dtcrm_step2_enter_whois_info;

	get_secpay_conf();

	$out = "";

$form_start = "
<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
";

	// Registration, hosting, or both ?
	if(!isset($_REQUEST["add_domain_type"]) || ($_REQUEST["add_domain_type"] != "domregandhosting" &&
		$_REQUEST["add_domain_type"] != "domreg" &&
		$_REQUEST["add_domain_type"] != "hosting")){
		$out .= "<b><u>".$txt_dtcrm_what_to_do[$lang]."</u></b><br>
$form_start";
		if($conf_use_registrar_api == "yes"){
			$out .= "<input type=\"radio\" name=\"add_domain_type\" value=\"domregandhosting\" checked>".$txt_dtcrm_hosting_and_domain_reg[$lang]."<br>";
			$add_domain_type_checked = " ";
		}else{
			$add_domain_type_checked = " checked ";
		}
		$out .= "<input type=\"radio\" name=\"add_domain_type\" value=\"hosting\" checked>".$txt_dtcrm_hosting_only[$lang]."<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
		return $out;
	}
	$form_start .= "<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">";
//	$form_start .= "<input type=\"hidden\" name=\"add_domain_type\" value=\"domregandhosting\">";
	if($_REQUEST["add_domain_type"] == "hosting"){
		// The don't want name registration or transfer,
		// Simply add the domain.
		if($admin["info"]["allow_add_domain"] == "no"){
			return $txt_dtcrm_not_enough_privileges[$lang]."<br>".
"<a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC] More domains\">$conf_webmaster_email_addr</a>.";
		}
		if(!isset($_REQUEST["domain_name"]) || $_REQUEST["domain_name"] == ""){
			return "<br><b><u>".$txt_dtcrm_enter_domain_to_add[$lang]."</u></b><br>
$form_start<input type=\"text\" name=\"domain_name\" value=\"\">
<input type=\"submit\" value=\"ok\"></form>";
		}
		if(!isHostname($_REQUEST["domain_name"])){
			return $txt_dtcrm_not_correct_format[$lang];
		}
		$q = "SELECT * FROM $pro_mysql_domain_table WHERE name='".$_REQUEST["domain_name"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n > 0){
			return $txt_domain_name_already_hosted_here[$lang];
		}
		if($admin["info"]["allow_add_domain"] == "check"){
			$q = "INSERT INTO $pro_mysql_pending_queries_table (adm_login,domain_name,date) VALUES ('$adm_login','".$_REQUEST["domain_name"]."','".date("Y-m-d H:i")."');";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			return "<br><u><b>".$txt_dtcrm_your_domain_will_be_soon[$lang]."</b></u><br>".
			$txt_dtcrm_soon_an_admin_will_have_a_look[$lang]."<br>
<a href=\"$conf_webmaster_email_addr?subject=[DTC] More domains\">$conf_webmaster_email_addr</a>.<br>
<br>
".$txt_dtcrm_add_another_domain[$lang]."
$form_start<input type=\"text\" name=\"domain_name\" value=\"\">
<input type=\"submit\" value=\"ok\"></form>
";
		}
		addDomainToUser($adm_login,$adm_pass,$_REQUEST["domain_name"]);
		return "<br><u><b>".$txt_dtcrm_domain_name_now_ready[$lang]."</b></u><br>
".$txt_dtcrm_you_can_check_config[$lang]."<br>
<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=".$_REQUEST["domain_name"]."\">".$_REQUEST["domain_name"]."</a><br>
<br>
".$txt_dtcrm_or_you_can_add_another_domain[$lang]."
$form_start<input type=\"text\" name=\"domain_name\" value=\"\">
<input type=\"submit\" value=\"ok\"></form>
";
	}

	// Registration or domain transfer ?
	if(!isset($_REQUEST["add_regortrans"]) || ($_REQUEST["add_regortrans"] != "register" &&
		$_REQUEST["add_regortrans"] != "transfer")){
		$out .= "<b><u>".$txt_dtcrm_transfer_existing_or_new[$lang]."</u></b><br>
$form_start
<input type=\"radio\" name=\"add_regortrans\" value=\"register\" checked>".$txt_dtcrm_register_new_domain[$lang]."<br>
<input type=\"radio\" name=\"add_regortrans\" value=\"transfer\">".$txt_dtcrm_transfer_from_another_registrar[$lang]."<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
		return $out;
	}
	if($_REQUEST["add_regortrans"] == "transfer") return drawNameTransfer($admin);
	$form_start .= "<input type=\"hidden\" name=\"add_regortrans\" value=\"register\">";

	// Start registration procedure (with or without hosting)

	$out .= "<b><u>".$txt_dtcrm_title_register_a_domain_name[$lang]."</u></b><br>";
	$out .= "<i><u>".$txt_dtcrm_register_step1[$lang]."</u></i><br>";
	if(!isset($_REQUEST["toreg_domain"]) || $_REQUEST["toreg_domain"] == "" ||
	!isset($_REQUEST["toreg_extention"]) || $_REQUEST["toreg_extention"] == ""){
		$out .= "<br>".$txt_crm_enter_the_domain_name_you_wish_to_register[$lang]."<br>
$form_start $form_enter_domain_name</form>";
		return $out;
	}

	$fqdn = $_REQUEST["toreg_domain"] . $_REQUEST["toreg_extention"];
	$domlookup = registry_check_availability($fqdn);
	if($domlookup["is_success"] != 1){
		$out .= "<font color=\"red\">".$txt_dtcrm_could_not_connect_to_api[$lang]
			."</font><br>".$domlookup['response_text'];
		return $out;
//		die($txt_dtcrm_could_not_connect_to_api[$lang]);
	}

	if($domlookup["attributes"]["status"] != "available"){
		$out .= "<br>
".$txt_sorry_domain_name_not_available1[$lang]." <b>$fqdn</b> ".$txt_sorry_domain_name_not_available2[$lang]
."<br><font color=\"red\">" . $domlookup["response_text"] . "</font>
<br><br>
Have another try:<br>$form_start $form_enter_domain_name</form>";
		return $out;
	}
	$form_start .= "<input type=\"hidden\" name=\"toreg_domain\" value=\"".$_REQUEST["toreg_domain"]."\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"".$_REQUEST["toreg_extention"]."\">";

	// DOMAIN IS AVAILABLE, PROCEED DO REGISTRATION
	$out .= "Domain name <b>$fqdn</b> is available for registration.<br><br>
<i><u>".$txt_dtcrm_step2_enter_whois_info[$lang]."</u></i><br>
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
		!isset($_REQUEST["toreg_dns1"]) || $_REQUEST["toreg_dns1"] == "" ||
		!isset($_REQUEST["toreg_dns2"]) || $_REQUEST["toreg_dns2"] == "" ||
		$_REQUEST["toreg_period"] < 1 || $_REQUEST["toreg_period"] > 10){

		$year = $txt_dtcrm_year[$lang];
		$years = $txt_dtcrm_years[$lang];
		$out .= $txt_please_select_the_3_contact_handles[$lang]."<br><br>$form_start";
		$out .= whoisHandleSelection($admin);
		$out .= "<br>$form_enter_dns_infos<br><br>
".$txt_dtcrm_select_how_long_you_want_to_register[$lang]."<br>
<select name=\"toreg_period\">
<option value=\"1\">1 $year</option>
<option value=\"2\">2 $years</option>
<option value=\"3\">3 $years</option>
<option value=\"4\">4 $years</option>
<option value=\"5\">5 $years</option>
<option value=\"6\">6 $years</option>
<option value=\"7\">7 $years</option>
<option value=\"8\">8 $years</option>
<option value=\"9\">9 $years</option>
<option value=\"10\">10 $years</option>
</select><br><br>
<input type=\"submit\" value=\"Ok\">
</form>
";
		return $out;
	}
	$form_start .= "$whois_forwareded_params
<input type=\"hidden\" name=\"toreg_period\" value=\"".$_REQUEST["toreg_period"]."\">";

	$out .= "Registration for <b>" . $_REQUEST["toreg_period"] . " years</b><br>";
	$out .= "DNS1: " . $_REQUEST["toreg_dns1"] . "<br>";
	$out .= "DNS2: " . $_REQUEST["toreg_dns2"] . "<br><br>";
	$out .= "<i><u>".$txt_dtcrm_register_domain_step3[$lang]."</u></i>
$form_start
";

	// Check if paiement has just occured !
	if(isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "return_from_paypal_domain_add"){
		$ze_refund = isPayIDValidated(addslashes($_REQUEST["pay_id"]));
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
	$price = registry_get_domain_price($fqdn,$_REQUEST["toreg_period"]);
	$fqdn_price = $price["attributes"]["price"] + $registration_added_price;
	$fqdn_price *= $_REQUEST["toreg_period"];

	if($admin["info"]["id_client"] != 0){
		$remaining = $admin["client"]["dollar"];
	}else{
		$out .= $txt_dtcrm_you_dont_have_a_client_id[$lang]."<br>";
		$remaining = 0;
		return $out;
	}
	$out .= $txt_dtcrm_remaining_on_your_account[$lang]." \$" . $remaining . "<br>
".$txt_dtcrm_total_price[$lang]." \$". $fqdn_price . "<br><br>";
	if($fqdn_price > $remaining){
		$to_pay = $fqdn_price - $remaining;

		$payid = createCreditCardPaiementID($to_pay,$admin["info"]["id_client"],
				"Domain name registration ".$_REQUEST["toreg_extention"],"no");
		$return_url = $_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass"
		."&addrlink=$addrlink&action=dtcrm_add_domain&add_domain_type=".$_REQUEST["add_domain_type"]
		."&add_regortrans=".$_REQUEST["add_regortrans"]."&toreg_domain=".$_REQUEST["toreg_domain"]
		."&toreg_extention=".$_REQUEST["toreg_extention"]."&dtcrm_owner_hdl=".$_REQUEST["dtcrm_owner_hdl"]
		."&dtcrm_admin_hdl=".$_REQUEST["dtcrm_admin_hdl"]."&dtcrm_billing_hdl=".$_REQUEST["dtcrm_billing_hdl"]
		."&toreg_dns1=".$_REQUEST["toreg_dns1"]."&toreg_dns2=".$_REQUEST["toreg_dns2"]
		."&toreg_dns3=".$_REQUEST["toreg_dns3"]."&toreg_dns4=".$_REQUEST["toreg_dns4"]
		."&toreg_dns5=".$_REQUEST["toreg_dns5"]."&toreg_dns6=".$_REQUEST["toreg_dns6"]
		."&toreg_period=".$_REQUEST["toreg_period"]."&inner_action=return_from_paypal_domain_add&payid=$payid";
		$paybutton = paynowButton($payid,$to_pay,
				"Domain name registration ".$_REQUEST["toreg_extention"],$return_url);

		$out .= $txt_dtcrm_you_currently_dont_have_enough_funds[$lang]."<br>
<br><br>
$form_start<input type=\"submit\" value=\"".$txt_dtcrm_button_paiement_done_checkout[$lang]."\">
</form> $paybutton";
		return $out;
	}

	// Check for confirmation
	if(!isset($_REQUEST["toreg_confirm_register"]) || $_REQUEST["toreg_confirm_register"] != "yes"){
		$out .= $txt_dtcrm_you_have_enough_funds_proceed[$lang]."<br><br>
$form_start
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
<input type=\"submit\" value=\"".$txt_dtcrm_button_proceed_to_registration[$lang]."\">
</form>";
		return $out;
	}
///////////////////////////////////////
// START OF DOMAIN NAME REGISTRATION //
	$owner_id = $_REQUEST["dtcrm_owner_hdl"];
	$billing_id = $_REQUEST["dtcrm_billing_hdl"];
	$admin_id = $_REQUEST["dtcrm_admin_hdl"];
	$contacts = getContactsArrayFromID($owner_id,$billing_id,$admin_id);
	$regz = registry_register_domain($adm_login,$adm_pass,$fqdn,$_REQUEST["toreg_period"],$contacts,$dns_servers);

	if($regz["is_success"] != 1){
		$out .= "<font color=\"red\"><b>".$txt_dtcrm_registration_failed[$lang]."</b></font><br>
".$txt_dtcrm_server_said[$lang]."<i>" . $regz["response_text"] . "</i>";
		return $out;
	}
	$out .= "<font color=\"green\"><b>Registration succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";

	$operation = $remaining - $fqdn_price;
	$query = "UPDATE $pro_mysql_client_table SET dollar='$operation' WHERE id='".$admin["info"]["id_client"]."';";
	mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());

	addDomainToUser($adm_login,$adm_pass,$fqdn,$adm_pass);
	unset($ns_ar);
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
	newWhois($fqdn,$owner_id,$billing_id,$admin_id,$period,$ns_ar);

	$out .= "<font color=\"green\"><b>".$txt_dtcrm_succesfully_added_domain[$lang]."</b></font><br>";

	$out .= $txt_dtcrm_click[$lang]."<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">".$txt_dtcrm_here[$lang]."</a>".$txt_dtcrm_to_refresh_the_menu_or_add_another_domain[$lang];

// END OF DOMAIN NAME REGISTRATION //
/////////////////////////////////////
	return $out;
}


?>
