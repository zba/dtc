<?php

require_once "$dtcshared_path/dtcrm/registry_calls.php";

$registration_added_price = 1.5;

require_once "$dtcshared_path/dtcrm/draw_register_forms.php";
require_once "$dtcshared_path/dtcrm/draw_handle.php";
require_once "$dtcshared_path/dtcrm/draw_whois.php";
require_once "$dtcshared_path/dtcrm/draw_nameservers.php";

function drawAdminTools_AddDomain($admin){
	global $lang;
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $form_enter_dns_infos;
	global $form_enter_domain_name;
	global $whois_forwareded_params;
	global $form_period_popup;

	global $pro_mysql_handle_table;
	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";

$form_start = "
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
";

	// Registration, hosting, or both ?
	if($_REQUEST["add_domain_type"] != "domregandhosting" &&
		$_REQUEST["add_domain_type"] != "domreg" &&
		$_REQUEST["add_domain_type"] != "hosting"){
		$out .= "<b><u>What do you want to do:</u></b><br>
$form_start
<input type=\"radio\" name=\"add_domain_type\" value=\"domregandhosting\" checked>Hosting + name registration/transfer<br>
<input type=\"radio\" name=\"add_domain_type\" value=\"domreg\">Name registration/transfer<br>
<input type=\"radio\" name=\"add_domain_type\" value=\"hosting\">Hosting only<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
		return $out;
	}
	$form_start .= "<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">";
	if($_REQUEST["add_domain_type"] == "hosting"){
		// The don't want name registration or transfer,
		// Simply add the domain.
		return "To have a new domain name to host without domain
name registration, please write to: <a href=\"info@gplhost.com?subject=More domains\">info@gplhost.com</a>";
	}

	// Registration or domain transfer ?
	if($_REQUEST["add_regortrans"] != "register" &&
		$_REQUEST["add_regortrans"] != "transfer"){
		$out .= "<b><u>Do you want to transfer an existing domain or register a new domain?</u></b><br>
$form_start
<input type=\"radio\" name=\"add_regortrans\" value=\"register\" checked>Register a new domain<br>
<input type=\"radio\" name=\"add_regortrans\" value=\"transfer\">Transfer an existing domain from another registrar<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
		return $out;
	}
	if($_REQUEST["add_regortrans"] == "transfer") return drawNameTransfer($admin);
	$form_start .= "<input type=\"hidden\" name=\"add_regortrans\" value=\"register\">";

	// Start registration procedure (with or without hosting)

	$out .= "<b><u>Register a domain name</u></b><br>";
	$out .= "<i><u>Step 1: Verify availability</u></i><br>";
	if($_REQUEST["toreg_domain"] == "" || !isset($_REQUEST["toreg_domain"]) ||
	$_REQUEST["toreg_extention"] == "" || !isset($_REQUEST["toreg_extention"])){
		$out .= "<br>Enter the domain name you want to register:<br>
$form_start $form_enter_domain_name</form>";
		return $out;
	}

	$fqdn = $_REQUEST["toreg_domain"] . $_REQUEST["toreg_extention"];
	$domlookup = registry_check_availability($fqdn);
	if($domlookup["is_success"] != 1){
		die("Could not connect to domain registration server: please try again later !!!");
	}

	if($domlookup["attributes"]["status"] != "available"){
		$out .= "<br>
Sorry, the domain name <b>$fqdn</b> is
NOT available for registration. Registration server returned:<br><font color=\"red\">" . $srs_result["response_text"] . "</font>
<br><br>
Have another try:<br>$form_start $form_enter_domain_name</form>";
		return $out;
	}
	$form_start .= "<input type=\"hidden\" name=\"toreg_domain\" value=\"".$_REQUEST["toreg_domain"]."\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"".$_REQUEST["toreg_extention"]."\">";

	// DOMAIN IS AVAILABLE, PROCEED DO REGISTRATION
	$out .= "Domain name <b>$fqdn</b> is available for registration.<br><br>
<i><u>Step 2: Enter whois informations</u></i><br>
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

		$out .= "Please select the 3 contact handles you want to use for registering that
domain name.<br><br>$form_start";
		$out .= whoisHandleSelection($admin);
		$out .= "<br>$form_enter_dns_infos<br><br>
Select how long you want to register this domain name:<br>
<select name=\"toreg_period\">
<option value=\"1\">1 year</option>
<option value=\"2\">2 years</option>
<option value=\"3\">3 years</option>
<option value=\"4\">4 years</option>
<option value=\"5\">5 years</option>
<option value=\"6\">6 years</option>
<option value=\"7\">7 years</option>
<option value=\"8\">8 years</option>
<option value=\"9\">9 years</option>
<option value=\"10\">10 years</option>
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
	$out .= "<i><u>Step 3: Proceed to registration</u></i>
$form_start
";

	// Check billing to know if user has enough money on his account
	$price = registry_get_domain_price($fqdn,$_REQUEST["toreg_period"]);
	$fqdn_price = $price["attributes"]["price"];
	$fqdn_price += $_REQUEST["toreg_period"] * 2;

	if($admin["info"]["id_client"] != 0){
		$remaining = $admin["client"]["dollar"];
	}else{
		$out .= "You don't have a client ID. Please contact us.<br>";
		$remaining = 0;
		return $out;
	}
	$out .= "Remaining on your account: \$" . $remaining . "<br>
Total price: \$". $fqdn_price . "<br><br>";
	if($fqdn_price > $remaining){
		$to_pay = $fqdn_price - $remaining;

		$payButton = paynowButton($product_id,$amount);

		$out .= "You currently don't have enough funds on your account. You will be
redirected to our paiement system. Please click on the button bellow
to pay, and then click refresh button.<br><br>
<br><br>
$form_start<input type=\"submit\" value=\"Paiement done, let met checkout\">
</form>";
		return $out;
	}

	// Check for confirmation
	if($_REQUEST["toreg_confirm_register"] != "yes"){
		$out .= "
You have enough funds on your account to proceed registration. Press
the confirm button and your order will be proceeded.<br><br>
$form_start
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
<input type=\"submit\" value=\"Proceed to name-registration\">
</form>";
		return $out;
	}
///////////////////////////////////////
// START OF DOMAIN NAME REGISTRATION //
	$owner_id = $_REQUEST["dtcrm_owner_hdl"];
	$billing_id = $_REQUEST["dtcrm_billing_hdl"];
	$admin_id = $_REQUEST["dtcrm_admin_hdl"];
	getContactsArrayFromID($owner_id,$billing_id,$admin_id);
	$regz = registry_register_domain($adm_login,$adm_pass,$fqdn,$period,$contacts,$dns_servers);

	if($regz["is_success"] != 1){
		$out .= "<font color=\"red\"><b>Registration failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i>";
		return $out;
	}
	addDomainToUser($adm_login,$adm_pass,$fqdn);
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

	$out .= "<font color=\"green\"><b>Registration succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>

Click <a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">here</a>
to refresh the menu or add another domain name.
";

//	print_r($regz);
// END OF DOMAIN NAME REGISTRATION //
/////////////////////////////////////
	return $out;
}


?>
