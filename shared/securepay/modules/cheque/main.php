<?php

// Should return a decimal with added gateway fees.
function cheque_calculate_fee($amount){
	global $cheques_flat_fees;
	$total = $amount+$cheques_flat_fees;
	return $total;
}

// Display the payment link option
function cheque_display_icon($product_id,$amount,$item_name,$return_url,$use_recurring = "no"){
	global $paypal_account;
	global $conf_administrative_site;

	global $secpayconf_currency_letters;
	global $conf_use_ssl;

	if($conf_use_ssl == "yes"){
		$goback_start = "https://";
	}else{
		$goback_start = "http://";
	}

	$add_to_form = '<input type="hidden" name="amount" value="'.str_replace(",",".",$amount).'">';
	$out = '<form action="'.$goback_start.$conf_administrative_site."/dtc/cheques_and_transfers.php".'" method="post" target="_top">
<input type="hidden" name="item_name" value="'.$item_name.'">
<input type="hidden" name="item_number" value="'.$product_id.'">
<input type="hidden" name="currency_code" value="'.$secpayconf_currency_letters.'">
'.$add_to_form.'
<input type="image" src="cheque.gif" border="0"
name="submit" alt="'. "Pay by cheque" .'">
</form>';
	return $out;

}

$secpay_modules[] = array(
	"display_icon" => "cheque_display_icon",
	"use_module" => $secpayconf_accept_cheques,
	"calculate_fee" => "cheque_calculate_fee",
	"instant_account" => _("No")
);

?>
