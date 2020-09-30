<?php

// function incPaymentModules(){
	global $dtcshared_path;
	global $secpay_modules;

	if(!isset($secpayconf_use_paypal) || ( $secpayconf_use_paypal != "no" && $secpayconf_use_paypal != "yes" )){
		get_secpay_conf();
	}

	if( !isset($secpay_modules) ){
		$secpay_modules = array();

		$mods_path = "$dtcshared_path/" . '/securepay/modules';

		if(FALSE === ($dir = scandir($mods_path))){
			echo "<font color=\"red\">"._("Could not list modules in")." $mods_path" . "</font>";
		}else{
			$nbr_file = sizeof($dir);
			$secpay_mods = array();
			for($i=0;$i<$nbr_file;$i++){
				$file = $dir[$i];
				$mymod_path = $mods_path . "/$file";
				if ($file != "." && $file != ".." && is_dir($mymod_path)){
					require($mymod_path. "/main.php");
				}
			}
		}
	}
// }

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
	global $secpayconf_use_dineromail;

	global $secpay_modules;

	if(!isset($secpayconf_use_paypal) || ( $secpayconf_use_paypal != "no" && $secpayconf_use_paypal != "yes" )){
		get_secpay_conf();
	}

//	incPaymentModules();

	if($vat_rate != 0){
		$vat_legend = "<th width=\"18%\">" ._("Taxes (VAT or GST)") ." $vat_rate%</th>";
	}else{
		$vat_legend = "";
	}

	$out = "<table width=\"100%\" height=\"1\">";
	$out .= "<tr><th>". _("Payment Method") ."</th>
	<th width=\"18%\">". _("Amount") ."</th>
	<th width=\"18%\">". _("Gateway cost") ."</th>
	$vat_legend
	<th width=\"18%\">". _("Total") ."</th>
	<th width=\"18%\">". _("Instant account") ."</th></tr>\n";

	$nbr_modules = sizeof($secpay_modules);
	for($i=0;$i<$nbr_modules;$i++){
		if($secpay_modules[$i]["use_module"] == "yes"){
			$total = $secpay_modules[$i]["calculate_fee"]($amount);
			$cost = $total - $amount;
			if($vat_rate != 0){
				$big_total = calculateVATtotal ($total,$vat_rate);
				$vat = $big_total - $total;
				$vat_total = "<td align=\"center\">".number_format($vat, 2)." ".$secpayconf_currency_letters."</td>";
				$total = $big_total;
			}else{
				$vat_total = "";
			}
			$out .= "<tr><td align=\"center\">".$secpay_modules[$i]["display_icon"]($pay_id,$total,$item_name,$return_url)."</td>";
			$out .= "<td align=\"center\">".number_format($amount, 2)." ".$secpayconf_currency_letters."</td>";
			$out .= "<td align=\"center\">".number_format($cost, 2)." ".$secpayconf_currency_letters."</td>";
			$out .= $vat_total;
			$out .= "<td align=\"center\">".number_format($total, 2)." ".$secpayconf_currency_letters."</td>";
			$out .= "<td align=\"center\">".$secpay_modules[$i]["instant_account"]."</td></tr>\n";
		}
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

function createCreditCardPaiementID($amount_paid,$client_id,$label,$new_account="yes",$product_id=0,$vat_rate=0,$services=""){
	global $secpayconf_currency_letters;
	global $pro_mysql_pay_table;

	$hash_check_key = getRandomValue();

	$q = "INSERT INTO $pro_mysql_pay_table (id,id_client,label,currency,refund_amount,paiement_type,date,time,valid,new_account,shopper_ip,product_id,paiement_total,vat_rate,hash_check_key,services)
		VALUES ('','$client_id','label','{$secpayconf_currency_letters}','$amount_paid','online','".date("Y-m-j")."','".date("H:i:s")."','no','$new_account','".$_SERVER["REMOTE_ADDR"]."','$product_id','$amount_paid','$vat_rate','$hash_check_key','$services');";
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
	global $secpayconf_maxmind_threshold;

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
	// Ensure the amt paid is inclusive of tax
	$payable_amt = $ar["refund_amount"] + ( $ar["refund_amount"] * ( $ar["vat_rate"] / 100 ));
	// Round the amount to the nearest 2 decimals
	$payable_amt = round($payable_amt, 2);
	if($amount_paid < $payable_amt)die(logPay("Amount paid on gateway lower than refund ammount file ".__FILE__." line ".__LINE__));
	if($total_payed != -1){
		$cost = $total_payed - $amount_paid;
		$total = $total_payed;
	}else{
		$cost = $amount_paid - $ar["refund_amount"];
		$total = $amount_paid;
	}

	$new_account_array;
	if($ar["new_account"] == "yes"){
                $q = "SELECT * FROM $pro_mysql_new_admin_table WHERE paiement_id='".$ar["id"]."';";
                $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
                $new_account_array = mysql_fetch_array($r);
	}
	$maxmind_hash = unserialize($new_account_array["maxmind_output"]);
	$maxmind_score = $maxmind_hash["riskScore"];
	if ($maxmind_score >= $secpayconf_maxmind_threshold){
		$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$paiement_type',
			secpay_site='$secpay_site',paiement_cost='$cost',paiement_total='$total',
			valid_date='".date("Y-m-j")."', valid_time='".date("H:i:s")."',
			secpay_custom_id='$secpay_custom_id',valid='pending',pending_reason='MaxMind' WHERE id='$pay_id';";
	} else {
	$q = "UPDATE $pro_mysql_pay_table SET paiement_type='$paiement_type',
		secpay_site='$secpay_site',paiement_cost='$cost',paiement_total='$total',
		valid_date='".date("Y-m-j")."', valid_time='".date("H:i:s")."',
		secpay_custom_id='$secpay_custom_id',valid='yes' WHERE id='$pay_id';";
	}
	logPay($q);
	mysql_query($q)or die(logPay("Cannot query \"$q\" ! ".mysql_error()." in file ".__FILE__." line ".__LINE__));

	$txt_userwaiting_account_activated_subject = "$conf_message_subject_header ".$amount_paid." $secpayconf_currency_letters payment occured";

	if($ar["new_account"] == "yes"){
		$a = $new_account_array;
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

function get_secpay_conf(){
	global $conf_mysql_db;
	global $pro_mysql_secpayconf_table;

        $query = "SELECT * FROM $pro_mysql_secpayconf_table WHERE 1 LIMIT 1;";
        $result = mysql_query($query)or die("Cannot query $query !!! line: ".__LINE__." file: ".__FULE__." sql said: ".mysql_error());
        $num_rows = mysql_num_rows($result);
        if($num_rows != 1)      die("No config values in table !!!");
        $row = mysql_fetch_array($result);

	$fields = mysql_list_fields($conf_mysql_db, $pro_mysql_secpayconf_table);
	$columns = mysql_num_fields($fields);

	for($i=0;$i<$columns;$i++){
		$field_name = mysql_field_name($fields, $i);
		$toto = "secpayconf_".$field_name;
		global $$toto;
		$$toto = $row["$field_name"];
        }
}
?>
