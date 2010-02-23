<?php

// Should return a decimal with added gateway fees.
function moneybookers_calculate_fee($amount){
	global $secpayconf_moneybookers_flat;
	global $secpayconf_moneybookers_rate;
	//$total = round((($amount+$secpayconf_paypal_flat+0.005) / (1 - ($secpayconf_paypal_rate/100))+0.005),2);
	// This will allow 0.00 gateway cost where desird.
	$total = round( $amount + $secpayconf_moneybookers_flat + ( $amount * ( $secpayconf_moneybookers_rate / 100 ) ) , 2);
	return $total;
}

// Display the payment link option
function moneybookers_display_icon($product_id,$amount,$item_name,$return_url,$use_recurring = "no"){
	global $moneybookers_account;
	global $conf_administrative_site;

	global $secpayconf_use_moneybookers;
	global $secpayconf_moneybookers_email;
	global $secpayconf_moneybookers_sandbox;
	global $secpayconf_currency_letters;
	global $conf_use_ssl;
	global $lang;

	if($secpayconf_moneybookers_sandbox == "yes"){
		// This is test sandbox site
		$moneybookers_host = "www.moneybookers.com";
		$moneybookers_cgi = "/app/payment.pl";
		$ze_moneybookers_email = $secpayconf_moneybookers_sandbox_email;
	}else{
		// This is production website
		$moneybookers_host = "www.moneybookers.com";
		$moneybookers_cgi = "/app/payment.pl";
		$ze_moneybookers_email = $secpayconf_moneybookers_email;
	}

	if($conf_use_ssl == "yes"){
		$goback_start = "https://";
	}else{
		$goback_start = "http://";
	}

	// Moneybookers is VERY lame, and uses non-standard ISO codes, this works arounds it
	$cur_lang = substr($lang,0,2);
	switch($cur_lang){
	case "de":
	case "es":
	case "fr":
	case "it":
	case "pl":
	case "gr":
	case "ro":
	case "ru":
	case "tr":
	case "cz":
	case "nl":
	case "da":
	case "sv":
	case "fi":
		$mb_lang = strtoupper($cur_lang);
		break;
	case "zh":
		$mb_lang = "CN";
		break;
	default:
	case "en":
		$mb_lang = "EN";
		break;
	}

	$out = '<form action="https://'.$moneybookers_host.$moneybookers_cgi.'" method="POST">
<input type="hidden" name="pay_to_email" value="'.$ze_moneybookers_email.'">
<input type="hidden" name="status_url" value="'.$goback_start.$conf_administrative_site.'/dtc/moneybookers.php">
<input type="hidden" name="status_url" value="'.$ze_moneybookers_email.'">
<input type="hidden" name="transaction_id" value="'.$product_id.'">
<input type="hidden" name="return_url" value="'.$goback_start.$conf_administrative_site.$return_url.'">
<input type="hidden" name="language" value="'.$mb_lang.'">
<input type="hidden" name="amount" value="'.$amount.'">
<input type="hidden" name="currency" value="'.$secpayconf_currency_letters.'">
<input type="hidden" name="detail1_description" value="'.$item_name.'">
<input type="hidden" name="detail1_text" value="'.$item_name.'">
<input type="image" src="moneybookers.gif" border="0" name="submit" alt="'. "Moneybookers" .'">
<input type="submit" value="Pay!">
</form>';

	return $out;

}

$secpay_modules[] = array(
	"display_icon" => "moneybookers_display_icon",
	"use_module" => $secpayconf_use_moneybookers,
	"calculate_fee" => "moneybookers_calculate_fee",
	"instant_account" => _("Yes")
);

?>
