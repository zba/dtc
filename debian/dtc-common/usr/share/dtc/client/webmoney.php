<?php

/**
 *  webmoney result gateway
 *
 * @copyright 2008 ZioN (c)
 */





require_once("../shared/autoSQLconfig.php");
$panel_type="client";
require_once("$dtcshared_path/dtc_lib.php");
get_secpay_conf();




$LMI_MODE = '1';

if( isset($_POST['LMI_PREREQUEST']) && $_POST['LMI_PREREQUEST'] == 1 ){

	$paiement_type ="online"; $secpay_site="webmoney"; $reason = "wmz:".$_POST['LMI_PAYER_PURSE'].", wmid:".$_POST['LMI_PAYER_WM'];

		$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".mysql_real_escape_string($_POST['LMI_PAYMENT_NO'])."'";
		$r = mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));

		$n = mysql_num_rows($r);
		if($n != 1)die(logPay("Pay id $pay_id not found in file ".__FILE__." line ".__LINE__));  else {

			$ar = mysql_fetch_array($r);
			if($ar["valid"] != "no" && $ar["valid"] != "pending")die(logPay("Paiement already validated or pending in file ".__FILE__." line ".__LINE__));


			$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$paiement_type',secpay_site='$secpay_site',valid='pending',pending_reason='$reason' WHERE id='".mysql_real_escape_string($_POST['LMI_PAYMENT_NO'])."'";
			mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));

			echo 'YES';
		}
		//setPaiemntAsPending(mysql_real_escape_string($_POST['LMI_PAYMENT_NO']),mysql_real_escape_string('Payer: '.$_POST['LMI_PAYER_PURSE'].', wmid'.$_POST['LMI_PAYER_WM']));

}

if(isset($_POST['LMI_HASH']) && $_POST['LMI_HASH']){

		$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".mysql_real_escape_string($_POST['LMI_PAYMENT_NO'])."'";
		$r = mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));

		$n = mysql_num_rows($r);
		if($n != 1)die(logPay("Pay id $pay_id not found in file ".__FILE__." line ".__LINE__));

		$ar = mysql_fetch_array($r);

		$chkstring =  $secpayconf_webmoney_wmz.$ar['refund_amount'].$ar['id'].
					  $_POST['LMI_MODE'].$_POST['LMI_SYS_INVS_NO'].$_POST['LMI_SYS_TRANS_NO'].$_POST['LMI_SYS_TRANS_DATE'].
			          $secpayconf_webmoney_license_key.$_POST['LMI_PAYER_PURSE'].$_POST['LMI_PAYER_WM'];

		$md5sum = strtoupper(md5($chkstring));
		$hash_check = ($_POST['LMI_HASH'] == $md5sum);




			    if($_POST['LMI_PAYMENT_NO'] == $ar['id'] # Check if payment id, purse number and amount correspond
				&& $_POST['LMI_PAYEE_PURSE'] == $secpayconf_webmoney_wmz
				&& $_POST['LMI_PAYMENT_AMOUNT'] == $ar['refund_amount']
				&& $_POST['LMI_MODE'] == $LMI_MODE
				&& $hash_check ) {

				$secpay_custom_id="0"; $paiement_type ="online"; $secpay_site="webmoney"; $reason = "wmz:".$_POST['LMI_PAYER_PURSE'].", wmid:".$_POST['LMI_PAYER_WM'];
				$total = mysql_real_escape_string($_POST['LMI_PAYMENT_AMOUNT']);

						$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$paiement_type',
							secpay_site='$secpay_site',paiement_cost='$cost',paiement_total='$total',
							valid_date='".date("Y-m-j")."', valid_time='".date("H:i:s")."',
							secpay_custom_id='$secpay_custom_id',valid='yes' WHERE id='".mysql_real_escape_string($_POST['LMI_PAYMENT_NO'])."'";

						logPay($q);
						mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));


				}




   }


?>