<?php

/////////////////////////////////////////////////////
// Handles payment motifications from moneybookers //
/////////////////////////////////////////////////////
// List of parameters sent:
// pay_to_email
// pay_from_email
// merchant_id
// mb_transaction_id
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
if( md5($concat_str) != $_REQUEST["md5sig"]){
	die("md5sum not validated!");
}


?>