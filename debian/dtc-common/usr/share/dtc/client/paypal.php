<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
require_once("$dtcshared_path/dtc_lib.php");
get_secpay_conf();

logPay("Script reached !");

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_REQUEST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
logPay("Resending query to paypal: ".$req);
if($secpayconf_paypal_sandbox == "no"){
	$paypal_server_hostname = "www.paypal.com";
	$ze_paypal_email = $secpayconf_paypal_email;
}else{
	$paypal_server_hostname = "www.sandbox.paypal.com";
	$ze_paypal_email = $secpayconf_paypal_sandbox_email;
}
$paypal_server_script = "/cgi-bin/webscr";

// post back to PayPal system to validate
$header = "";
$header .= "POST $paypal_server_script HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ($paypal_server_hostname, 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_REQUEST['item_name'];
$item_number = $_REQUEST['item_number'];
$payment_amount = $_REQUEST['mc_gross'];
$payment_currency = $_REQUEST['mc_currency'];
$payer_email = $_REQUEST['payer_email'];

if (!$fp) {
	// HTTP ERROR
	logPay("Could not open site $paypal_server_hostname");
	die("HTTP error!");
} else {
	logPay("Connected to paypal site, sending validation req...");
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			logPay("Recieved VERIFIED: committing to sql !");
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			if($_REQUEST["business"] != $ze_paypal_email){
				logPay("db:".$ze_paypal_email."/request:".$_REQUEST["business"]);
				logPay("Business paypal email do not match !");
				die("This is not our business paypal email!");
			}
			if($_REQUEST["mc_currency"] != $secpayconf_currency_letters){
				logPay("Currency is not $secpayconf_currency_letters !");
				die("Incorrect currency!");
			}
			if($_REQUEST["payment_status"] != "Completed"){
				if($_REQUEST["payment_status"] == "Pending"){
					setPaiemntAsPending(mysql_real_escape_string($item_number),mysql_real_escape_string($_REQUEST["pending_reason"]));
				}else{
					logPay("Status is not completed or pending !");
					die("Status not completed or pending...");
				}
			}else{
				logPay("Calling validate()");
				// validatePaiement($item_number,$refund_amount,"online","paypal",$txn_id,$_POST["payment_gross"]);
				// This should work better:
				if($secpayconf_paypal_validate_with == "total"){
                                	$refund_amount = $_REQUEST["mc_gross"] - $_REQUEST["mc_fee"];
				}else{
					// Ensure amount tally according to cost before adding the paypal fees
					$refund_amount = $_REQUEST["mc_gross"];
				}
				validatePaiement(mysql_real_escape_string($item_number),$refund_amount,"online","paypal",mysql_real_escape_string($_REQUEST["txn_id"]),mysql_real_escape_string($_REQUEST["mc_gross"]));
			}
		}elseif (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			logPay("Recieved INVALID: sending mail to webmaster !!");
			die("Invalid!");
		}
	}
	fclose ($fp);
}
?>
