<?php

// This is production website
$paypal_host = "www.paypal.com";
$paypal_cgi = "/cgi-bin/webscr";

// This is test sandbox site
//$paypal_host = "www.sandbox.paypal.com";
//$paypal_cgi = "/us/cgi-bin/webscr";

function enetsNotifyPostbackScript(){
	global $paypal_host;
	global $paypal_cgi;

	// Create an URL to post to it.
	// read the post from PayPal system and add 'cmd' to the url posted var
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}

	// post back to PayPal system to validate
	$header .= "POST $paypal_cgi HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ($paypal_host, 80, $errno, $errstr, 30);

	// assign posted variables to local variables
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];

	if (!$fp){
		// HTTP ERROR
		return $error;
	}else{
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
		}
		fclose ($fp);
		if(strcmp ($res, "INVALID") == 0){
			// log for manual investigation
			return $error;
		}
		if (!strcmp ($res, "VERIFIED") == 0) {
			return $error;
		}
		// check the payment_status is Completed
		// check that txn_id has not been previously processed
		// check that receiver_email is your Primary PayPal email
		// check that payment_amount/payment_currency are correct
		// process payment
// $payment_status
// $txn_id
	}
}

function enetsButton($payid,$amount,$item_name,$return_url){
	global $secpayconf_enets_mid_id;
	global $secpayconf_use_enets_test;
	global $secpayconf_enets_test_mid_id;
	global $conf_use_ssl;

	if($secpayconf_use_enets_test == "yes"){
		// This is test server URL
		$enets_url = "http://ezpayd.consumerconnect.com.sg/masterMerchant/collectionPage.jsp";
		$enets_url = "https://test.enets.sg/enets2/enps.do";
		$enets_mid = $secpayconf_enets_test_mid_id;
	}else{
		// This is production website
		$enets_url = "https://www.enetspayments.com.sg/masterMerchant/collectionPage.jsp";
		$enets_url = "https://www.enets.sg/enets2/enps.do";
		$enets_mid = $secpayconf_enets_mid_id;
	}



	// https://www.paypal.com/xclick/business=thomas%40goirand.fr&item_name=Domain+name+registration+.com&
	// item_number=1&amount=11.50&no_note=1&currency_code=USD

	$out = '<form action="'.$enets_url.'" method="post">
<input type="hidden" name="mid" value="'.$enets_mid.'">
<input type="hidden" name="amount" value="'.$amount.'">

<input type="hidden" name="txnRef" value="'.$payid.'">

<input type="hidden" name="item_name" value="'.$item_name.'">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="no_shipping" value="1">
<input type="image" src="enets_pay_icon.gif" border="0"
name="submit" alt="Pay with eNETS using your credit card!">
</form>';
	return $out;

//	$paypal_scripturl = "https://www.paypal.com/xclick/";
//	$paypal_url = $paypal_scripturl.
//		"business=$paypal_account&item_name=$item_name&item_number=$product_id".
//		"&amount=$amount&no_note=1&currency_code=USD&return=$return_url";
//	$img_src = "https://www.paypal.com/en_US/i/btn/x-click-but01.gif";
//	$img_alt = "Make payments with PayPal - it's fast, free and secure!";
//	$out = "<a  target=\"blank\" name=\"submit\" href=\"$paypal_url\"><img border=\"0\" src=\"$img_src\" alt=\"$img_alt\"></a>";
//	return $out;
}

?>
