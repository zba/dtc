<?php

$paypal_account = "thomas@goirand.fr";

function paypalButton($product_id,$amout,$item_name){
	global $paypal_account;
	// https://www.paypal.com/xclick/business=thomas%40goirand.fr&item_name=Domain+name+registration+.com&
	// item_number=1&amount=11.50&no_note=1&currency_code=USD
	$paypal_scripturl = "https://www.paypal.com/xclick/";
	$paypal_url = $paypal_scripturl.
		"business=$paypal_account&item_name=$item_name&item_number=$product_id".
		"&amount=$amount&no_note=1&currency_code=USD";
	$img_src = "https://www.paypal.com/en_US/i/btn/x-click-but01.gif";
	$img_alt = "Make payments with PayPal - it's fast, free and secure!";
	$out = "<a  target=\"blank\" name=\"submit\" href=\"$paypal_url\"><img border=\"0\" src=\"$img_src\" alt=\"$img_alt\"></a>";
	return $out;
}

function paynowButton($product_id,$amount=-1){
	return paypalButton($product_id,$amout,"Test product, do not buy !");
}

?>
