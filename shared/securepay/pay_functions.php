<?php

function calculateVATtotal ($amount,$vat_rate){
	if($vat_rate == 0){
		$big_total = $amount;
	}else{
		$big_total = round($amount * (1 + ($vat_rate/100)),2);
	}
	return $big_total;
}

function paynowButton($pay_id,$amount,$item_name,$return_url,$vat_rate=0,$use_recurring = "no"){
	global $conf_use_worldpay;

	global $secpayconf_use_enets;
	global $secpayconf_use_paypal;
	global $secpayconf_paypal_rate;
	global $secpayconf_paypal_flat;
	global $secpayconf_enets_rate;
	global $secpayconf_use_paypal_recurring;
	global $secpayconf_currency_symbol;
	global $secpayconf_currency_letters;

	global $txt_recurring_payment;
	global $lang;

	if(!isset($secpayconf_use_paypal) || ( $secpayconf_use_paypal != "no" && $secpayconf_use_paypal != "yes" )){
		get_secpay_conf();
	}

	if($vat_rate != 0){
		$vat_legend = "<td>Taxes (VAT or GST) $vat_rate%</td>";
	}else{
		$vat_legend = "";
	}

	$out = "<table width=\"100%\" height=\"1\">";
	$out .= "<tr><td>Payment system</td><td>Amount</td><td>Gateway cost</td>$vat_legend<td>Total</td><td>Instant account</td></tr>\n";
	if($secpayconf_use_paypal == "yes"){
		$total = round((($amount+$secpayconf_paypal_flat+0.005) / (1 - ($secpayconf_paypal_rate/100))+0.005),2);
		$cost = $total - $amount;
		if($vat_rate != 0){
			$big_total = calculateVATtotal ($total,$vat_rate);
			$vat = $big_total - $total;
			$vat_total = "<td>".$vat." ".$secpayconf_currency_letters."</td>";
			$total = $big_total;
		}else{
			$vat_total = "";
		}
		$out .= "<tr><td>".paypalButton($pay_id,$total,$item_name,$return_url)."</td>";
		$out .= "<td>".$amount." ".$secpayconf_currency_letters."</td><td>".$cost." ".$secpayconf_currency_letters."</td>".$vat_total."<td>".$total." ".$secpayconf_currency_letters."</td>
		<td>Yes</td></tr>\n";
	}
	if($secpayconf_use_paypal == "yes" && $secpayconf_use_paypal_recurring == "yes" && $use_recurring == "yes"){
		$total = round((($amount+$secpayconf_paypal_flat+0.005) / (1 - ($secpayconf_paypal_rate/100))+0.005),2);
		$cost = $total - $amount;
		if($vat_rate != 0){
			$big_total = calculateVATtotal ($total,$vat_rate);
			$vat = $big_total - $total;
			$vat_total = "<td>".$vat." ".$secpayconf_currency_letters."</td>";
			$total = $big_total;
		}else{
			$vat_total = "";
		}
		$out .= "<tr><td>".paypalButton($pay_id,$total,$item_name,$return_url,"yes")."<br><i>".$txt_recurring_payment[$lang]."</i></td>";
		$out .= "<td>".$amount." ".$secpayconf_currency_letters."</td><td>".$cost." ".$secpayconf_currency_letters."</td>".$vat_total."<td>".$total." ".$secpayconf_currency_letters."</td><td>Yes</td></tr>\n";
	}
	if($secpayconf_use_enets == "yes"){
		$total = round(($amount / (1 - ($secpayconf_enets_rate/100))+0.005),2);
		$cost = $total - $amount;
		if($vat_rate != 0){
			$big_total = calculateVATtotal ($total,$vat_rate);
			$vat = $big_total - $total;
			$vat_total = "<td>".$vat."</td>";
			$total = $big_total;
		}else{
			$vat_total = "";
		}
		$out .= "<tr><td>".enetsButton($pay_id,$total,$item_name,$return_url)."</td>";
		$out .= "<td>".$amount." ".$secpayconf_currency_letters."</td><td>".$cost." ".$secpayconf_currency_letters."</td>".$vat_total." ".$secpayconf_currency_letters."<td>".$total." ".$secpayconf_currency_letters."</td><td>Yes</td></tr>\n";
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

// Return the amount of money that has been added to the account if payid has been validated
function isPayIDValidated($pay_id){
	global $pro_mysql_pay_table;
	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$pay_id' AND valid='yes';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__);
	$n = mysql_insert_id();
	if($n != 1){
		return 0;
	}else{
		$a = mysql_fetch_array($r);
		return $a["refund_amount"];
	}
}

function createCreditCardPaiementID($amount_paid,$client_id,$label,$new_account="yes",$product_id=0,$vat_rate=0){
	global $secpayconf_currency_letters;
	global $pro_mysql_pay_table;
	$q = "INSERT INTO $pro_mysql_pay_table (id,id_client,label,currency,refund_amount,paiement_type,date,time,valid,new_account,shopper_ip,product_id,paiement_total,vat_rate)
		VALUES ('','$client_id','label','{$secpayconf_currency_letters}','$amount_paid','online','".date("Y-m-j")."','".date("H:i:s")."','no','$new_account','".$_SERVER["REMOTE_ADDR"]."','$product_id','$amount_paid','$vat_rate');";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__);
	$n = mysql_insert_id();
	return $n;
}
function setPaiemntAsPending($pay_id,$reason,$paiement_type="online",$secpay_site="paypal"){
	global $pro_mysql_pay_table;
	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$pay_id';";
	logPay("Querying: $q");
	$r = mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));
	$n = mysql_num_rows($r);
	if($n != 1)die(logPay("Pay id $pay_id not found in file ".__FILE__." line ".__LINE__));
	$ar = mysql_fetch_array($r);
	if($ar["valid"] != "no" && $ar["valid"] != "pending")die(logPay("Paiement already validated or pending in file ".__FILE__." line ".__LINE__));
	logPay("Setting item $pay_id as pending");
	$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$paiement_type',secpay_site='$secpay_site',valid='pending',pending_reason='$reason' WHERE id='$pay_id';";
	logPay($q);
	mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));
}

function validatePaiement($pay_id,$amount_paid,$paiement_type,$secpay_site="none",$secpay_custom_id="0",$total_payed=-1){
	global $pro_mysql_pay_table;
	global $conf_webmaster_email_addr;
	global $pro_mysql_new_admin_table;

	global $secpayconf_currency_letters;
	global $conf_message_subject_header;

	if(!isset($secpayconf_currency_letters)){
		get_secpay_conf();
	}

	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$pay_id';";
	logPay("Querying: $q");
	$r = mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));
	$n = mysql_num_rows($r);
	if($n != 1)die(logPay("Pay id $pay_id not found in file ".__FILE__." line ".__LINE__));
	$ar = mysql_fetch_array($r);

	if($ar["valid"] != "no" && $ar["valid"] != "pending")die(logPay("Paiement already validated in file ".__FILE__." line ".__LINE__));
	logPay("Ammount paid: $amount_paid");
	if($amount_paid < $ar["refund_amount"])die(logPay("Amount paid on gateway lower than refund ammount file ".__FILE__." line ".__LINE__));
	if($total_payed != -1){
		$cost = $total_payed - $amount_paid;
		$total = $total_payed;
	}else{
		$cost = $amount_paid - $ar["refund_amount"];
		$total = $amount_paid;
	}
	$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$paiement_type',
		secpay_site='$secpay_site',paiement_cost='$cost',paiement_total='$total',
		valid_date='".date("Y-m-j")."', valid_time='".date("H:i:s")."',
		secpay_custom_id='$secpay_custom_id',valid='yes' WHERE id='$pay_id';";
	logPay($q);
	mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));

	$txt_userwaiting_account_activated_subject = "$conf_message_subject_header ".$amount_paid." $secpayconf_currency_letters payment occured";

	if($ar["new_account"] == "yes"){
		$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE paiement_id='".$ar["id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$a = mysql_fetch_array($r);
		$added_comments = "Login: ".$a["reqadm_login"]."
Email: ".$a["email"]."
Company: ".$a["comp_name"]."
Customer: ".$a["first_name"].", ".$a["family_name"]."
City: ".$a["city"]."
Country: ".$a["country"]."";
	}else{
		$added_comments = "";
	}

	$txt_mail = "Hello,

This is Domain Technologie Control panel robot.
A ".$amount_paid." $secpayconf_currency_letters payment has just occured.

Payid: ".$pay_id."
$added_comments

GPLHost:>_ Open-source hosting worldwide.
http://www.gplhost.com
";
	$headers = "From: ".$conf_webmaster_email_addr;
	mail($conf_webmaster_email_addr,$txt_userwaiting_account_activated_subject,$txt_mail,$headers);
}

?>
