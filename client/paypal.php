<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
require_once("$dtcshared_path/dtc_lib.php");
get_secpay_conf();

function logPay($txt){
	$fp = fopen("/tmp/paylog.txt","a");
	fwrite($fp,$txt."\n");
	fclose($fp);
	echo $txt."<br>";
}

logPay("Script reached !");

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
logPay("Resending query to paypal: ".$req);
// $paypal_server_hostname = "www.paypal.com";
$paypal_server_hostname = "www.sandbox.paypal.com";
$paypal_server_script = "/cgi-bin/webscr";

// post back to PayPal system to validate
$header .= "POST $paypal_server_script HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ($paypal_server_hostname, 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$payer_email = $_POST['payer_email'];

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
			if($_POST["business"] != $secpayconf_paypal_email){
				die("This is not our business paypal email!");
			}
			if($_POST["payment_status"] != "Completed"){
				die("Status not completed...");
			}
			if($_POST["payment_currency"] != "USD"){
				die("Incorrect currency!");
			}
			validatePaiement($item_number,$_POST["payment_amount"],"online","paypal",$txn_id);
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			logPay("Recieved INVALID: sending mail to webmaster !!");
			die("Invalid!");
		}
	}
	fclose ($fp);
}
?>
