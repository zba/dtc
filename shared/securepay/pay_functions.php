<?php

function validatePaiement($pay_id,$amount_paid,$paiement_type,$secpay_site="none",$secpay_custom_id="0"){
	global $pro_mysql_pay_table;
	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$pay_id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__);
	$n = mysql_num_rows($r);
	if($n != 1)die("Pay id $pay_id not found in file ".__FILE__." line ".__LINE__);
	$ar = mysql_fetch_array($r);
	if($ar["valid"] != "no")die("Paiement already validated in file ".__FILE__." line ".__LINE__);
	$cost = $amount_paid - $ar["refund_amount"];
	$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$paiement_type',
		secpay_site='$secpay_site',paiement_cost='$cost',paiement_total='$amount_paid',
		secpay_custom_id='$secpay_custom_id' WHERE id='$pay_id';";
	mysql_query($q)or die("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__);
}

?>
