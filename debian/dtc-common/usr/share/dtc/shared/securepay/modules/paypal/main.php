<?php

// Should return a decimal with added gateway fees.
function paypal_calculate_fee($amount){
	global $secpayconf_paypal_flat;
	global $secpayconf_paypal_rate;
	//$total = round((($amount+$secpayconf_paypal_flat+0.005) / (1 - ($secpayconf_paypal_rate/100))+0.005),2);
	// This will allow 0.00 gateway cost where desird.
	$total = round( $amount + $secpayconf_paypal_flat + ( $amount * ( $secpayconf_paypal_rate / 100 ) ) , 2);
	return $total;
}

// Display the payment link option
function paypal_display_icon($product_id,$amount,$item_name,$return_url,$use_recurring = "no"){
	global $paypal_account;
	global $conf_administrative_site;

	global $secpayconf_use_paypal;
	global $secpayconf_paypal_email;
	global $secpayconf_paypal_sandbox;
	global $secpayconf_paypal_sandbox_email;
	global $secpayconf_currency_letters;
	global $secpayconf_use_paypal_recurring;
	global $conf_use_ssl;

	if($secpayconf_paypal_sandbox == "yes"){
		// This is test sandbox site
		$paypal_host = "www.sandbox.paypal.com";
		$paypal_cgi = "/us/cgi-bin/webscr";
		$ze_paypal_email = $secpayconf_paypal_sandbox_email;
	}else{
		// This is production website
		$paypal_host = "www.paypal.com";
		$paypal_cgi = "/cgi-bin/webscr";
		$ze_paypal_email = $secpayconf_paypal_email;
	}

	// https://www.paypal.com/xclick/business=thomas%40goirand.fr&item_name=Domain+name+registration+.com&
	// item_number=1&amount=11.50&no_note=1&currency_code=USD

	if($conf_use_ssl == "yes"){
		$goback_start = "https://";
	}else{
		$goback_start = "http://";
	}

	if($secpayconf_use_paypal_recurring == "yes" && $use_recurring == "yes"){
		$add_to_form = '<input type="hidden" name="a3" value="'.str_replace(",",".",$amount).'">
		<input type="hidden" name="p3" value="1">
		<input type="hidden" name="t3" value="M">
		<input type="hidden" name="src" value="1">
		<input type="hidden" name="sra" value="1">';
	}else{
		$add_to_form = '<input type="hidden" name="amount" value="'.str_replace(",",".",$amount).'">';
	}
	$out = '<form action="https://'.$paypal_host.$paypal_cgi.'" method="post" target="_top">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="'.$ze_paypal_email.'">
<input type="hidden" name="item_name" value="'.$item_name.'">
<input type="hidden" name="item_number" value="'.$product_id.'">
<input type="hidden" name="currency_code" value="'.$secpayconf_currency_letters.'">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="no_note" value="1">
'.$add_to_form.'
<input type="hidden" name="return" value="'.$goback_start.$conf_administrative_site.$return_url.'">
<input type="hidden" name="notify_url" value="'.$goback_start.$conf_administrative_site.'/dtc/paypal.php">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but01.gif" border="0"
name="submit" alt="'. "Make payments with PayPal - it's fast, free and secure!" .'">
</form>';
	return $out;

}

$secpay_modules[] = array(
	"display_icon" => "paypal_display_icon",
	"use_module" => $secpayconf_use_paypal,
	"calculate_fee" => "paypal_calculate_fee",
	"instant_account" => _("Yes")
);

?>
