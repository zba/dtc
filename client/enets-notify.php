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
logPay("Received query from eNETS: ".$req."\n");

// eNETS params:
// cmd=_notify-validate&
// amount=20.84&
// TxnRef=13&
// TxnDate=20060504&
// TxnTime=20%3A34%3A26&
// PayMethod=credit&
// txnStatus=succ&errorCode=0000&
// no_shipping=1&
// mid=616&
// item_name=Test+product1&
// curCode=USD&
// submit_x=116&submit_y=17&
// currency_code=USD&
// NETS_signature=icbfv62esnlCGylZya91VL8xy+6unH0SuSqute3CaN0dr5KeBt7xVTC69Q1BSet2myyMoaJpr%2FrY%0D%0AGUhUFVIRnm34omisbiSRsdGiM2Yblv%2Fhlo%2Fjn3zN+3Vn0nNi9FxX3r2Q5fbPyzpJMdiF7syXrzxw%0D%0An%2FkoynkXagSoL2b6H7I%3D

$pay_id = $_REQUEST["TxnRef"];
$status = $_REQUEST["txnStatus"];
$error_code = $_REQUEST["errorCode"];
$amount = $_REQUEST["amount"];

if($status != "succ"){
	logPay("Status not success line ".__LINE__." file ".__FILE__."\n");
	die();
}
if($_SERVER["REMOTE_ADDR"] != "203.116.94.3" && $_SERVER["REMOTE_ADDR"] != "203.116.61.131" && $_SERVER["REMOTE_ADDR"] != "203.116.94.76" && $_SERVER["REMOTE_ADDR"] != "203.116.94.74"){
	logPay("Recieved notify from an unkonwn IP addr ".__LINE__." file ".__FILE__."\n");
	$content="Recieved notify from an unkonwn IP addr ".$_SERVER["REMOTE_ADDR"];
	Mail($conf_webmaster_email_addr,"[DTC Robot]: Recieved notify from an unkonwn IP",$content);
}
$pay_fee = $amount * $secpayconf_enets_rate / 100;
$amount_paid = $amount - $pay_fee;
logPay("Payment success from enets: calling validate()\n");
validatePaiement($pay_id,$amount_paid,"online","enets",0,$amount);
/*
This is the paypal API that must be changed for eNETS

// post back to PayPal system to validate
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
			if($_REQUEST["business"] != $secpayconf_paypal_email){
				logPay("db:".$secpayconf_paypal_email."/request:".$_REQUEST["business"]);
				logPay("Business paypal email do not match !");
				die("This is not our business paypal email!");
			}
			if($_REQUEST["payment_status"] != "Completed"){
				logPay("Status is not completed !");
				die("Status not completed...");
			}
			if($_REQUEST["mc_currency"] != "USD"){
				logPay("Currency is not USD !");
				die("Incorrect currency!");
			}
			logPay("Calling validate()");
			validatePaiement($item_number,$_REQUEST["mc_gross"]-$_REQUEST["mc_fee"],"online","paypal",$txn_id,$_POST["payment_gross"]);
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			logPay("Recieved INVALID: sending mail to webmaster !!");
			die("Invalid!");
		}
	}
	fclose ($fp);
}
*/
?>