<?php

$conf_use_paypal = "yes";
$paypal_account = 'paypal@gplhost.com';

$conf_use_worldpay = "yes";
$wp_instid = "60943";
$wp_curency = "USD";
$wp_testmode = "100";
$wp_callback_pass = ""; // Leave to blank if you don't want callbackPW to be used
$wp_servers_ip = "195.35.90.61|195.35.90.62";
$wp_accId1 = "";
$wp_callback_url = "/dtc/secpaycallback_worldpay.php";
$wt_md5_secret = "ILoveMD5Seccrets";

require("$dtcshared_path/securepay/pay_functions.php");

if($conf_use_worldpay == "yes"){
	include("$dtcshared_path/securepay/worldpay.php");
}
if($conf_use_paypal == "yes"){
	include("$dtcshared_path/securepay/paypal.php");
}

function paynowButton($pay_id,$amount){
	global $conf_use_paypal;
	global $conf_use_worldpay;
	$out .= "<table width=\"100%\" height=\"1\">";
	$out .= "<tr><td>Paiement system</td><td>Cost</td><td>Total</td><td>Instant account</td></tr>\n";
	if($conf_use_paypal == "yes"){
		$cost = 2;
		$total = $cost + $amount;
		$out .= "<tr><td>".paypalButton($pay_id,$amount,$button_text)."</td>";
		$out .= "<td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
	}
	if($conf_use_worldpay == "yes"){
		$cost = ceil($amount * 4.5 / 100);
		$total = $cost + $amount;
		$out .= "<tr><td>".worldPayButton($pay_id,$total,$button_text,$button_text)."</td>";
		$out .= "<td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
//		$out .= "<tr><td>".worldPayButton($pay_id,$amount,$button_text,$button_text)."</td>";
//		$out .= "<td>$cost<td><td>".$amount+$cost."</td><td>Yes</td></tr>\n";
	}
	$out .= "</table>";
	return $out;
}
 
?>
