<?php

function drawNameTransfer($admin,$given_fqdn="none"){
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $registration_added_price;

	global $form_enter_dns_infos;
	global $whois_forwareded_params;

	$out = "";

	if(isset($_REQUEST["toreg_domain"])){
		$toreg_domain = $_REQUEST["toreg_domain"];
	}
	if(isset($_REQUEST["toreg_extention"])){
		$toreg_extention = $_REQUEST["toreg_extention"];
	}
	if($given_fqdn != "none" && !isset($toreg_extention)){
		$c = strrpos($given_fqdn,".");
		$toreg_extention = substr($given_fqdn,$c);
		$toreg_domain = substr($given_fqdn,0,$c);
	}

	// Step 1: enter domain name and check domain transferability
	$form_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"dtcrm_action\" value=\"transfer_domain\">
<input type=\"hidden\" name=\"add_regortrans\" value=\"transfer\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
";

	$out .= "<b><u>". _("Transfer from another registrar to this server:") ."</u></b><br>
<i><u>". _("Step1: check if domain is transferable") ."</u></i>";

	if(!isset($toreg_extention) || $toreg_extention == "" ||
	!isset($toreg_domain) || $toreg_domain == "" ||
	($toreg_extention != ".com" && $toreg_extention != ".net" &&
	$toreg_extention != ".org" && $toreg_extention != ".biz" &&
	$toreg_extention != ".name" && $toreg_extention != ".info")		){
		$out .= "$form_start<br>
". _("Please enter the domain name you wish to transfer:") ."<br>
".make_registration_tld_popup();
		return $out;
	}

	$form_start .= "<input type=\"hidden\" name=\"toreg_domain\" value=\"$toreg_domain\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"$toreg_extention\">";

	$regz = registry_check_transfer($toreg_domain.$toreg_extention);
	if($regz["is_success"] != 1){
		die("<font color=\"red\">". _("TRANSFER CHECK FAILED: registry server didn't reply successfuly.") ."</font>");
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
		$out .= "<br><input type=\"submit\" value=\"". _("Proceed to transfer") ."\"></form>";
		return $out;
	}
	$form_start .= $whois_forwareded_params;
		
	$out .= "DNS1: ".$_REQUEST["toreg_dns1"]."<br>";
	$out .= "DNS2: ".$_REQUEST["toreg_dns2"]."<br><br>";

	$fqdn = $toreg_domain . $toreg_extention;
	$price = registry_get_domain_price($fqdn,1);
	$fqdn_price = $price["attributes"]["price"] + $registration_added_price;

	if($admin["info"]["id_client"] != 0){
		$remaining = $admin["client"]["dollar"];
	}else{
		$out .= _("You don't have a client ID. Please contact us.") ;
		$remaining = 0;
		return $out;
	}

	// Step 3: check account balance and transfer the domain name after transaction aprooval
	$out .= "<i><u>Step3: Proceed for transfer</u></i><br>";
	$out .= _("Remaining on your account: ") ." \$" . $remaining . "<br>
". _("Total price: ") ." \$". $fqdn_price . "<br><br>";

	if(isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "return_from_paypal_domain_add"){
		$ze_refund = isPayIDValidated(addslashes($_REQUEST["pay_id"]));
		if($ze_refund == 0){
			$out .= "<font color=\"red\">". _("The transaction failed, please try again!") ."</font>";
		}else{
			$out .= "<font color=\"green\">". _("Your account has been credited!") ."</font><br>";
			$q = "UPDATE $pro_mysql_client_table SET dollar = dollar+".$ze_refund." WHERE id='".$admin["info"]["id_client"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$remaining += $ze_refund;
		}
	}
	if($fqdn_price > $remaining){
		$payid = createCreditCardPaiementID($fqdn_price,$admin["info"]["id_client"],
			"Domain name registration ".$_REQUEST["toreg_extention"],"no");
		$return_url = $_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass"
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

		$out .= _("You currently don't have enough funds on your account. You will be redirected to our payment system. Please click on the button below to pay.") ."<br><br>
$paybutton";
		return $out;
	}
	if(!isset($_REQUEST["toreg_confirm_reg"]) || $_REQUEST["toreg_confirm_reg"] == "yes"){
		$out .= "$form_start
<input type=\"hidden\" name=\"toreg_confirm_transfer\" value=\"yes\">
<input type=\"submit\" value=\"Proceed transfer\">
</form>
";
		return $out;
	}
	return $out;
}

?>
