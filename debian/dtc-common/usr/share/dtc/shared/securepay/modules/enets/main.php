<?php

function enets_calculate_fee($amount){
	global $secpayconf_enets_rate;
	$total = round(($amount / (1 - ($secpayconf_enets_rate/100))+0.005),2);
	return $total;
}

function enets_display_icon($payid,$amount,$item_name,$return_url){
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

	$out = '<form action="'.$enets_url.'" method="post">
<input type="hidden" name="mid" value="'.$enets_mid.'">
<input type="hidden" name="amount" value="'.str_replace(",",".",$amount).'">

<input type="hidden" name="txnRef" value="'.$payid.'">

<input type="hidden" name="item_name" value="'.$item_name.'">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="no_shipping" value="1">
<input type="image" src="enets_pay_icon.gif" border="0"
name="submit" alt="Pay with eNETS using your credit card!">
</form>';
	return $out;
}

$secpay_modules[] = array(
	"display_icon" => "enets_display_icon",
	"use_module" => $secpayconf_use_enets,
	"calculate_fee" => "enets_calculate_fee",
	"instant_account" => _("Yes")
);

?>
