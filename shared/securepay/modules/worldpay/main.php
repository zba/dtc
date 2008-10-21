<?php

$conf_use_worldpay = "no";	// Switch to "yes" to use worldpay stuff
$wp_instid = "00000";		// Enter here your install ID
$wp_curency = "USD";		// Account curency. Values could be USD, GBP, EUR...
$wp_testmode = "100";		// "0" = live account, "100"=successfull test, "101"=unsuccessfull test
$wp_callback_pass = "";	// Enter here the password you provided for your callbakcs in the admin panel of worldpay
$wp_servers_ip = "195.35.90.61|195.35.90.62";		// This are the worldpay's server IP for basic checks
$wp_accId1 = "";					// This value is not mandatory
$wp_callback_url = "/dtc/secpaycallback_worldpay.php";	// This is the URL of the callback script for automatic paiement validation
$wt_md5_secret = "";					// MD5 password, not mandatory

// Should return a decimal with added gateway fees.
function worldpay_calculate_fee($amount){
	$total = $amount * 1.0475 + 0.017;
	return $total;
}

// Display the payment link option
function worldpay_display_icon($product_id,$amount,$item_name,$return_url,$use_recurring = "no"){
	global $wp_instid;
	global $wp_curency;
	global $pro_mysql_client_table;
	global $wp_testmode;
	global $wp_accId1;
	global $lang;
	global $conf_administrative_site;
	global $wp_callback_url;

	global $pro_mysql_pay_table;

	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$pay_id';";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error().__FILE__." line ".__LINE__);
	$n = mysql_num_rows($r);
	if($n != 1)die("Client id not found in file ".__FILE__." line ".__LINE__);
	$pay_row = mysql_fetch_array($r);
	$client_id = $pay_row["id_client"];

	$q = "SELECT * FROM $pro_mysql_client_table WHERE id='$client_id';";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error().__FILE__." line ".__LINE__);
	$n = mysql_num_rows($r);
	if($n != 1)die("Client id not found in file ".__FILE__." line ".__LINE__);
	$ar = mysql_fetch_array($r);

	$out = '
<form action="https://select.worldpay.com/wcc/purchase" method="POST">
<input type="hidden" name="MC_callback" value="'.$conf_administrative_site.$wp_callback_url.'">
<input type="hidden" name="instId" value="'.$wp_instid.'">
<input type="hidden" name="cartId" value="'.$pay_id.'">
<input type="hidden" name="amount" value="'.$amount.'">
<input type="hidden" name="currency" value="'.$wp_curency.'">
<input type="hidden" name="desc" value="'.$text_info.'">
<input type="hidden" name="testMode" value="'.$wp_testmode.'">

<input type="hidden" name="fixContact" value="yes">
<input type="hidden" name="email" value="'.$ar["email"].'">
<input type="hidden" name="name" value="'.$ar["familyname"].', '.$ar["christname"].'">
<input type="hidden" name="address" value="'.$ar["addr1"].' '.$ar["addr2"].' '.$ar["addr3"].','.$ar["city"].' '.$ar["state"].'">
<input type="hidden" name="postcode" value="'.$ar["zipcode"].'">
<input type="hidden" name="country" value="'.$ar["country"].'">
<input type="hidden" name="tel" value="'.$ar["phone"].'">
<input type="hidden" name="fax" value="'.$ar["fax"].'">

<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="noLanguageMenu" value="yes">
';
	if($wp_accId1 != ""){
		$out .= '<input type="hidden" name="accId1" value="'.$wp_accId1.'">';
	}
	$out .= '<input type="image" src="gfx/securepay/poweredByWorldPay.gif" value="WorldPay"> </form>';
}

$secpay_modules[] = array(
	"display_icon" => "worldpay_display_icon",
	"use_module" => "no",
	"calculate_fee" => "worldpay_calculate_fee",
	"instant_account" => _("Yes")
);


?>
