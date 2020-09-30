<?php

/////////////////////////////////////////////////////
// Handles payment motifications from moneybookers //
/////////////////////////////////////////////////////
// List of parameters sent:
// pay_to_email : our merchant account email
// pay_from_email : email of the customer
// merchant_id : only useful to claculate the MD5 check
// transaction_id : merchant's TXID
// mb_transaction_id : moneybookers TXID
// mb_amount
// mb_currency
// mb_status: -3 = chargeback, -2 = failed, 2 = processed, 0 = pending, -1 = canceled
// md5sig
// amount
// currency

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
require_once("$dtcshared_path/dtc_lib.php");
get_secpay_conf();

// Concatenate the strings and check for the md5sig
$concat_str = $_REQUEST["merchant_id"].$_REQUEST["transaction_id"]. strtoupper(md5($secpayconf_moneybookers_secret_word)) . $_REQUEST["mb_amount"] . $_REQUEST["mb_currency"] . $_REQUEST["status"];
if( strtoupper(md5($concat_str)) != $_REQUEST["md5sig"]){
	die("md5sum not validated!");
}

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
	
if($_REQUEST["pay_to_email"] != $ze_moneybookers_email){
	die("This is not our business moneybookers email!");
}

if($_REQUEST["mb_currency"] != $secpayconf_currency_letters){
	die("Incorrect currency!");
}

$item_number = mysql_real_escape_string($_REQUEST["transaction_id"]);
$amount = mysql_real_escape_string($_REQUEST["mb_amount"]);
if($_REQUEST["mb_status"] != "0"){
	setPaiemntAsPending($item_number,mysql_real_escape_string( "moneybookers" ));
}
if( $_REQUEST["mb_status"] != "2" ){
	validatePaiement($item_number,$amount,"online","moneybookers",mysql_real_escape_string($_REQUEST["mb_transaction_id"]));
}

?>
