<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
get_secpay_conf();

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
// $paypal_server_hostname = "www.paypal.com";
$paypal_server_hostname = "www.eliteweaver.co.uk";
$paypal_server_script = "/cgi-bin/webscr";

// post back to PayPal system to validate
$header .= "POST $paypal_server_script HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ($paypal_server_hostname, 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if (!$fp) {
	// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
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
			$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$item_number';";
			$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1)	die("Cannot reselect payid!");
			$a = mysql_fetch_array($r);
			if($_POST["payment_amount"] < $a["refund_amount"]){
				die("Incorrect amount!");
			}
			validatePaiement($item_number,$_POST["payment_amount"],"online","paypal",$txn_id);
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
		}
	}
	fclose ($fp);
}
?>
