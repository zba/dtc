<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("new_account_form.php");
require_once("new_account_renewal.php");

get_secpay_conf();

// The language stuff...
$anotherTopBanner = anotherTopBanner("DTC");
if(isset($txt_top_menu_entrys)){
	$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);
}
$anotherLanguageSelection = anotherLanguageSelection();
$lang_sel = skin($conf_skin,$anotherLanguageSelection, _("Language") );

$proceed = "yes";
if( !isset($_REQUEST["hash_check"]) || !isRandomNum($_REQUEST["hash_check"]) ){
	$form = _("Hash check not in correct format: cannot validate payment.");
	$proceed = "no";
}
if( !isset($_REQUEST["item_id"]) || !isRandomNum($_REQUEST["item_id"]) ){
	$form = _("Hash check not in correct format: cannot validate payment.");
	$proceed = "no";
}
if( $proceed == "yes"){
	$q = "SELECT * FROM $pro_mysql_pay_table WHERE hash_check_key='" . $_REQUEST["hash_check"] . "' AND id='" . $_REQUEST["item_id"] . "'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__);
	$n = mysql_num_rows($r);
	if($n != 1){
		$form = _("Could not find your registration in the database.");
		$proceed = "no";
	}
}

if( $proceed == "yes"){
	if( isset($_REQUEST["payment_type"]) && $_REQUEST["payment_type"] == "cheque"){
		$payment_type = 'cheque';
		$pending_reason = "Cheque";
	}else{
		$payment_type = 'wire';
		$pending_reason = "Wire transfer";
	}
	$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$payment_type',valid='pending',pending_reason='$pending_reason' WHERE hash_check_key='" . $_REQUEST["hash_check"] . "' AND id='" . $_REQUEST["item_id"] . "'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

// Display the payment infos
if( $proceed == "yes"){
	if( isset($_REQUEST["payment_type"]) && $_REQUEST["payment_type"] == "cheque"){
		$form = "<u>" . _("Cheques shall be written to be paid only to:") . "</u><br>" . $secpayconf_cheques_to_label . "<br><br>";
		$form .= "<u>" ._("Cheques shall be sent to:") . "</u><br><pre>" . $secpayconf_cheques_send_address . "</pre><br><br>";
	}else{
		$form = "<u>" . _("Wire transfers shall be made to: ") . "</u><br><pre>" . $secpayconf_wiretransfers_bank_details . "</pre><br><br>";
	}
	$form .= "<b>" . _("Thanks for your order. Your order has been placed on hold until your payment is verified.") . "</b>  <a href=\"/\">" . _("Continue") . "</a><br><br>";
}

$login_skined = skin($conf_skin,$form, _("Register a new account") );
$mypage = layout_login_and_languages($login_skined,$lang_sel);
if(function_exists("skin_NewAccountPage")){
	skin_NewAccountPage($login_skined);
}else{
	echo anotherPage("Client:","","",makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));
}
?>
