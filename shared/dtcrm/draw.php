<?php

require_once "$dtcshared_path/dtcrm/registry_calls.php";

$registration_added_price = 1.5;

require_once "$dtcshared_path/dtcrm/draw_register_forms.php";
require_once "$dtcshared_path/dtcrm/draw_handle.php";
require_once "$dtcshared_path/dtcrm/draw_whois.php";
require_once "$dtcshared_path/dtcrm/draw_nameservers.php";

function getContactsArrayFromID($owner_id,$billing_id,$admin_id){
	$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$owner_id';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
	if(mysql_num_rows($result) != 1)	die("Handle ID not found !");
	$contacts["owner"] = mysql_fetch_array($result)or die("Cannot fetch array !");

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$billing_id';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
	if(mysql_num_rows($result) != 1)	die("Handle ID not found !");
	$contacts["billing"] = mysql_fetch_array($result)or die("Cannot fetch array !");

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$admin_id';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
	if(mysql_num_rows($result) != 1)	die("Handle ID not found !");
	$contacts["admin"] = mysql_fetch_array($result)or die("Cannot fetch array !");
	return $contacts;
}
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

	$form_check_domain = "
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
<input type=\"hidden\" name=\"add_regORtrans\" value=\"register\">
</form>
";

	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";

	if($_REQUEST["add_domain_type"] == "domregandhosting" ||
	$_REQUEST["add_domain_type"] == "domreg"){
		if($_REQUEST["add_regORtrans"] == "register"){
			if($_REQUEST["toreg_domain"] == "" || !isset($_REQUEST["toreg_domain"]) ||
			$_REQUEST["toreg_extention"] == "" || !isset($_REQUEST["toreg_extention"])){
				$out .= "<b><u>Register a domain name</u></b><br>
<i><u>Step 1: Verify availability</u></i><br><br>
Enter the domain name you want to register:<br>
$form_check_domain";
			}else{
				$fqdn = $_REQUEST["toreg_domain"] . $_REQUEST["toreg_extention"];
				$domlookup = registry_check_availability($fqdn);
				if($domlookup["is_success"] != 1){
					die("Could not connect to domain registration server: please try again later !!!");
				}
				if($domlookup["attributes"]["status"] == "available"){
					// DOMAIN IS AVAILABLE, PROCEED DO REGISTRATION
					$out = "<b><u>Register a domain name</u></b><br>
<i><u>Step 1: Verify availability</u></i><br><br>
Domain name <b>$fqdn</b> is available for registration.<br><br>

<i><u>Step 2: Enter whois informations</u></i><br><br>
";


					if(isset($_REQUEST["dtcrm_owner_hdl"]) && $_REQUEST["dtcrm_owner_hdl"] != "" &&
						isset($_REQUEST["dtcrm_admin_hdl"]) && $_REQUEST["dtcrm_admin_hdl"] != "" &&
						isset($_REQUEST["dtcrm_billing_hdl"]) && $_REQUEST["dtcrm_billing_hdl"] != "" &&
						isset($_REQUEST["toreg_dns1"]) && $_REQUEST["toreg_dns1"] != "" &&
						isset($_REQUEST["toreg_dns2"]) && $_REQUEST["toreg_dns2"] != "" &&
						$_REQUEST["toreg_period"] >= 1 && $_REQUEST["toreg_period"] <= 10){
						$out .= "Registration for <b>" . $_REQUEST["toreg_period"] . " years</b><br>";
						$out .= "DNS1: " . $_REQUEST["toreg_dns1"] . "<br>";
						$out .= "DNS2: " . $_REQUEST["toreg_dns2"] . "<br><br>";
						$out .= "<i><u>Step 3: Proceed to registration</u></i>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">   
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">     
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">     
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
<input type=\"hidden\" name=\"add_regORtrans\" value=\"register\">
<input type=\"hidden\" name=\"toreg_domain\" value=\"".$_REQUEST["toreg_domain"]."\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"".$_REQUEST["toreg_extention"]."\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
$whois_forwareded_params
<input type=\"hidden\" name=\"toreg_period\" value=\"".$_REQUEST["toreg_period"]."\">
";

						$price = registry_get_domain_price($fqdn,$_REQUEST["toreg_period"]);
						$fqdn_price = $price["attributes"]["price"];
						$fqdn_price += $_REQUEST["toreg_period"] * 2;

						if($admin["info"]["id_client"] != 0){
							$remaining = $admin["client"]["dollar"];
						}else{
							$out .= "You don't have a client ID. Please contact us.<br>";
							$remaining = 0;
						}
						$out .= "
Remaining on your account: \$" . $remaining . "<br>
Total price: \$". $fqdn_price . "<br><br>";
						if($fqdn_price > $remaining){
							$out .= "
You currently don't have enough funds on your account. You will be
redirected to our paiement system.<br><br>
<input type=\"submit\" value=\"Proceed to paiement\">
</form>";
						}else{
							if($_REQUEST["toreg_confirm_register"] == "yes"){

///////////////////////////////////////
// START OF DOMAIN NAME REGISTRATION //
	$owner_id = $_REQUEST["dtcrm_owner_hdl"];
	$billing_id = $_REQUEST["dtcrm_billing_hdl"];
	$admin_id = $_REQUEST["dtcrm_admin_hdl"];
	getContactsArrayFromID($owner_id,$billing_id,$admin_id);
	$regz = registry_register_domain($adm_login,$adm_pass,$fqdn,$period,$contacts,$dns_servers);

	if($regz["is_success"] == 0){
		$out .= "<font color=\"red\"><b>Registration failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
	}else{
		$out .= "<font color=\"green\"><b>Registration succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>

Click <a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">here</a>
to refresh the menu or add another domain name.
";
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
	}
//	print_r($regz);
// END OF DOMAIN NAME REGISTRATION //
/////////////////////////////////////

								$out .= "";
							}else{
								$out .= "
You have enough funds on your account to proceed registration. Press
the confirm button and your order will be proceeded.<br>
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
<input type=\"submit\" value=\"Proceed to registration\">
</form>
";
							}
						}
// https://dtc.gplhost.com/dtc/?adm_login=zigo&adm_pass=lazaro&addrlink=adddomain&action=dtcrm_add_domain&add_regORtrans=register&toreg_domain=yuglux24&toreg_extention=.com&add_domain_type=domreg&dtcrm_owner_hdl=1&dtcrm_billing_hdl=1&dtcrm_admin_hdl=1&toreg_dns1=default&toreg_dns2=default&toreg_dns3=&toreg_dns4=&toreg_dns5=&toreg_dns6=
// https://dtc.gplhost.com/dtc/?adm_login=zigo&adm_pass=lazaro&addrlink=adddomain&action=dtcrm_add_domain&add_regORtrans=register&toreg_domain=yuglux24&toreg_extention=.com&add_domain_type=domreg&dtcrm_owner_hdl=1&dtcrm_admin_hdl=1&dtcrm_billing_hdl=1&toreg_dns1=&toreg_dns2=&toreg_dns3=&toreg_dns4=&toreg_dns5=&toreg_dns6=
					}else{
						$out .= "
Please select the 3 contact handles you want to use for registering that
domain name.<br><br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">  
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">  
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
<input type=\"hidden\" name=\"add_regORtrans\" value=\"register\">
<input type=\"hidden\" name=\"toreg_domain\" value=\"".$_REQUEST["toreg_domain"]."\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"".$_REQUEST["toreg_extention"]."\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
";
						$out .= whoisHandleSelection($admin);
						$out .= "
<br>
$form_enter_dns_infos<br><br>
Select how long you want to register this domain name:<br>
$form_period_popup<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
					}
				}else{
					$out = "<b><u>Register a domain name</u></b><br>
<i><u>Step 1: Verify availability</u></i><br><br>
Sorry, the domain name <b>$fqdn</b> is
NOT available for registration. Registration server returned:<br><font color=\"red\">" . $srs_result["response_text"] . "</font>
<br><br>
Have another try:<br>
$form_check_domain";
				}
			}
		}else if($_REQUEST["add_regORtrans"] == "transfer"){
			return drawNameTransfer($admin);
		}else{
			$out .= "<b><u>Do you want to transfer an existing domain or register a new domain?</u></b><br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
<input type=\"radio\" name=\"add_regORtrans\" value=\"register\" checked>Register a new domain<br>
<input type=\"radio\" name=\"add_regORtrans\" value=\"transfer\">Transfer an existing domain from another registrar<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
		}
	}else if($_REQUEST["add_domain_type"] == "hosting"){
	}else{
		$out .= "<b><u>What do you want to do:</u></b><br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"radio\" name=\"add_domain_type\" value=\"domregandhosting\" checked>Hosting + name registration/transfer<br>
<input type=\"radio\" name=\"add_domain_type\" value=\"domreg\">Name registration/transfer<br>
<input type=\"radio\" name=\"add_domain_type\" value=\"hosting\">Hosting only<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
	}
	return $out;
}


?>
