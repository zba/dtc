<?php

// Switch to "yes" to use paypal stuff
$conf_use_paypal = "no";
// Enter here your paypal email address
$paypal_account = '';

$conf_use_worldpay = "no";	// Switch to "yes" to use worldpay stuff
$wp_instid = "00000";		// Enter here your install ID
$wp_curency = "USD";		// Account curency. Values could be USD, GBP, EUR...
$wp_testmode = "100";		// "0" = live account, "100"=successfull test, "101"=unsuccessfull test
$wp_callback_pass = "";	// Enter here the password you provided for your callbakcs in the admin panel of worldpay
$wp_servers_ip = "195.35.90.61|195.35.90.62";		// This are the worldpay's server IP for basic checks
$wp_accId1 = "";					// This value is not mandatory
$wp_callback_url = "/dtc/secpaycallback_worldpay.php";	// This is the URL of the callback script for automatic paiement validation
$wt_md5_secret = "";					// MD5 password, not mandatory

?>
