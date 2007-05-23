<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
require_once("$dtcshared_path/dtc_lib.php");
get_secpay_conf();

logPay("Script reached !");

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_REQUEST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
logPay("Received query from eNETS: ".$req."\n");

// eNETS params:
// cmd=_notify-validate&
// amount=20.84&
// TxnRef=13&
// TxnDate=20060504&
// TxnTime=20%3A34%3A26&
// PayMethod=credit&
// txnStatus=succ&errorCode=0000&
// no_shipping=1&
// mid=616&
// item_name=Test+product1&
// curCode=USD&
// submit_x=116&submit_y=17&
// currency_code=USD&
// NETS_signature=icbfv62esnlCGylZya91VL8xy+6unH0SuSqute3CaN0dr5KeBt7xVTC69Q1BSet2myyMoaJpr%2FrY%0D%0AGUhUFVIRnm34omisbiSRsdGiM2Yblv%2Fhlo%2Fjn3zN+3Vn0nNi9FxX3r2Q5fbPyzpJMdiF7syXrzxw%0D%0An%2FkoynkXagSoL2b6H7I%3D

$pay_id = $_REQUEST["TxnRef"];
$status = $_REQUEST["txnStatus"];
$error_code = $_REQUEST["errorCode"];
$amount = $_REQUEST["amount"];

if($status != "succ"){
	logPay("Status not success line ".__LINE__." file ".__FILE__."\n");
	die();
}
if($_SERVER["REMOTE_ADDR"] != "203.116.94.3" && $_SERVER["REMOTE_ADDR"] != "203.116.61.131" && $_SERVER["REMOTE_ADDR"] != "203.116.94.76" && $_SERVER["REMOTE_ADDR"] != "203.116.94.74" && $_SERVER["REMOTE_ADDR"] != "203.116.94.6"){
	logPay("Recieved notify from an unkonwn IP addr ".__LINE__." file ".__FILE__."\n");
	$content="Recieved notify from an unkonwn IP addr ".$_SERVER["REMOTE_ADDR"];
	Mail($conf_webmaster_email_addr,"[DTC Robot]: Recieved notify from an unkonwn IP",$content);
}
$pay_fee = $amount * $secpayconf_enets_rate / 100;
$amount_paid = $amount - $pay_fee;
logPay("Payment success from enets: calling validate()\n");

// Todo: add more checkings to verify that the payment notify is originated by eNETS
validatePaiement($pay_id,$amount_paid,"online","enets",0,$amount);

?>
