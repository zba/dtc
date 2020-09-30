<?php
function drawNameRenew($domain_name,$admin){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $renew_added_price;
	global $registry_api_modules;
	global $pro_mysql_client_table;
	global $pro_mysql_domain_table;
	
	$form_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"dtcrm_action\" value=\"renew_domain\">
<input type=\"hidden\" name=\"add_regortrans\" value=\"renew\">
";
	//first step : renewable ?
	
		$domlookup = registry_check_renew($domain_name);
		
	if($domlookup["is_success"] != 1){
		$out = "<font color=\"red\">". _("Could not connect to domain renewal server: please try again later.") 
			."</font><br>".$domlookup['response_text'];
	return $out;
	}
	if($domlookup["attributes"]["renewable"] != 1){
		$out = "<br>
". _("Sorry, the domain name ") ." <b>$domain_name</b> ". _("is NOT renewable. The registration server returned: ") 
."<br><font color=\"red\">" . $domlookup["response_text"] . "</font>
<br><br>";
		return $out;
	}
	$out = "<br><font color=\"green\">". _("RENEW CHECK SUCCESSFUL") ."</font><br><br>";
	
	// second step : price and payment
	
if($admin["info"]["id_client"] != 0){
		$remaining = $admin["client"]["dollar"];
	}else{
		$out .= _("You don't have a client ID. Please contact us.") ;
		$remaining = 0;
		return $out;
	}
	
    $fqdn = $domain_name;
	$price = registry_get_domain_price($fqdn,1);
    $fqdn_price = $price + $renew_added_price;
	
	$out .= "<i><u>Step3: Proceed for renew</u></i><br>";
	$out .= _("Remaining on your account: ") ." \$" . $remaining . "<br>
". _("Total price: ") ." \$". $fqdn_price . "<br><br>";

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
			"Domain name renew ".$_REQUEST["toreg_extention"],"no");
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
			"Domain name renew ".$_REQUEST["toreg_extention"],$return_url);

		$out .= _("You currently don't have enough funds in your account. You will be redirected to our payment system. Please click on the button below to pay.") ."<br><br>
$paybutton";
		return $out;
	}	


	// Check for confirmation
	if(!isset($_REQUEST["toreg_confirm_renew"]) || $_REQUEST["toreg_confirm_renew"] != "yes"){
		$out .= _("You have enough funds in your account to proceed with transfer. Press the confirm button to continue.") ."<br><br>
$form_start
<input type=\"hidden\" name=\"toreg_confirm_renew\" value=\"yes\">
<input type=\"submit\" value=\"". _("Proceed to name renewal") ."\">
</form><br><br>";		
return $out;
	}
	///////////////////////////////////////
// START OF DOMAIN NAME RENEW           //

	$q = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$new_user = "no";
	}else{
		$new_user = "yes";
	}
//	sleep(2);
	$regz = registry_renew_domain($domain_name);

	if($regz["is_success"] != 1){
		$out .= "<font color=\"red\"><b>". _("Renewal failed") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i>";
		return $out;
	}
	$out .= "<font color=\"green\"><b>Renew succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";

	$operation = $remaining - $fqdn_price;
	$query = "UPDATE $pro_mysql_client_table SET dollar='$operation' WHERE id='".$admin["info"]["id_client"]."';";
	mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());

	$out .= "<font color=\"green\"><b>". _("Successfully renewed your domain name") ."</b></font><br><br>";


// END OF DOMAIN NAME RENEW //
/////////////////////////////////////

	return $out;
}

?>
