<?php

$conf_use_paypal = "yes";
$paypal_account = "paypal@gplhost.com";

$conf_use_worldpay = "yes";
$wp_instid = "60943";
$wp_curency = "USD";
$wp_testmode = "100";
$wp_callback_pass = ""; // Leave to blank if you don't want callbackPW to be used
$wp_servers_ip = "195.35.90.61|195.35.90.62";
$wp_accId1 = "";
$wp_callback_url = "/dtc/wp_callbackscript.php";
$wt_md5_secret = "ILoveMD5Seccrets";

if($conf_use_woldpay == "yes"){
	include("$dtcshared_path/securepay/worldpay.php");
}
if($conf_use_paypal == "yes"){
	include("$dtcshared_path/securepay/paypal.php");
}

function paynowButton($pay_id,$amount){
	$out .= "<table width=\"100%\" height=\"1\">";
	$out .= "<td>Paiement system</td><td>Cost</td><td>Total</td><td>Instant account</td>";
	if($conf_use_paypal == "yes"){
		$cost = 2;
		$out .= "<td>".paypalButton($command_id,$amout,$button_text)."</td>";
		$out .= "<td>\$$cost<td><td>".$amount+$cost."</td><td>No</td>";
	}
	if($conf_use_woldpay == "yes"){
		$cost = ceil($amount * 4.5 / 100);
		$out .= "<td>".worldPayButton($command_id,$amount,$button_text,$button_text)."</td>";
		$out .= "<td>\$$cost<td><td>".$amount+$cost."</td><td>Yes</td>";
	}
	$out .= "</table>";
	return $out;
}

?>
