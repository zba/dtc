<?php

function paynowButton($pay_id,$amount,$item_name,$return_url){
	global $conf_use_worldpay;

	global $secpayconf_use_paypal;
	global $secpayconf_paypal_rate;
	global $secpayconf_paypal_flat;

	$out .= "<table width=\"100%\" height=\"1\">";
	$out .= "<tr><td>Paiement system</td><td>Amount</td><td>Gateway cost</td><td>Total</td><td>Instant account</td></tr>\n";
	if($secpayconf_use_paypal == "yes"){
		$total = $amount * (1 + $secpayconf_paypal_rate/100) + $secpayconf_paypal_flat;
		$cost = $total - $amount;
		$out .= "<tr><td>".paypalButton($pay_id,$total,$item_name,$return_url)."</td>";
		$out .= "<td>\$$amount</td><td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
	}
	if($conf_use_worldpay == "yes"){
		$total = $amount * 1.0475 + 0.017;
		$cost = $total - $amount;
		$out .= "<tr><td>".worldPayButton($pay_id,$total,$button_text,$button_text)."</td>";
		$out .= "<td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
//		$out .= "<tr><td>".worldPayButton($pay_id,$amount,$button_text,$button_text)."</td>";
//		$out .= "<td>$cost<td><td>".$amount+$cost."</td><td>Yes</td></tr>\n";
	}
	$out .= "</table>";
	return $out;
}

function createCreditCardPaiementID($amount_paid,$client_id,$label,$new_account="yes"){
	global $pro_mysql_pay_table;
	$q = "INSERT INTO $pro_mysql_pay_table (id,id_client,label,currency,refund_amount,paiement_type,date,time,valid,new_account)
		VALUES ('','$client_id','label','USD','$amount_paid','online','".date("Y-m-j")."','".date("H:i:s")."','no','$new_account');";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__);
	$n = mysql_insert_id();
	return $n;
}

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
