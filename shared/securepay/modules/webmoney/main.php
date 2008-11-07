<?php

// Should return a decimal with added gateway fees.
function webmoney_calculate_fee($amount){
	$total = $amount + ($amount*0.008);
	return $total;
}

// Display the payment link option
function webmoney_display_icon($product_id,$amount,$item_name,$return_url,$use_recurring = "no"){
	global $paypal_account;
	global $conf_administrative_site;

	global $secpayconf_use_webmoney;
	global $secpayconf_webmoney_wmz;
	global $secpayconf_webmoney_license_key;
	global $conf_use_ssl;

	$amount = round(floatval(str_replace(",",".",$amount)), 2);

	$out = <<<HTML
<form method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp" target="_blank">
<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="{$amount}">
<input type="hidden" name="LMI_PAYMENT_DESC" value="Payment hosting">
<input type="hidden" name="LMI_PAYMENT_NO" value="{$product_id}">
<input type="hidden" name="LMI_PAYEE_PURSE" value="{$secpayconf_webmoney_wmz}">
<input type="hidden" name="LMI_SIM_MODE" value="0">
<input type="image" src="https://merchant.webmoney.ru/conf/img/logo2_3.gif" border="0" name="submit" alt="Payment WebMoney">
</form>
HTML;

	return $out;
}

$secpay_modules[] = array(
	"display_icon" => "webmoney_display_icon",
	"use_module" => $secpayconf_use_webmoney,
	"calculate_fee" => "webmoney_calculate_fee",
	"instant_account" => _("Yes")
);

?>
