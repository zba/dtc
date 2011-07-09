<?php

function exportTransactions(){
	global $secpayconf_currency_letters;
	global $pro_mysql_pay_table;
	global $pro_mysql_completedorders_table;
	global $pro_mysql_client_table;
	global $conf_administrative_site;
	global $conf_default_company_invoicing;
	global $pro_mysql_companies_table;
	global $cc_europe;
	get_secpay_conf();
	//$secpayconf_currency_letters = "EUR";

	// Calculate start and end date depending on the request
	$request_date = $_REQUEST["date"];
	$rda = explode("-",$request_date);
	$days_in_month = date("t",mktime(1,1,1,$rda[1],2,$rda[0]));
	$start_date = $request_date . "-01";
	$end_date = $request_date . "-" . $days_in_month;

	$out = "";

	// TODO: replace this by something which checks what country has been used for the transaction
	$q = "SELECT country FROM $pro_mysql_companies_table WHERE id='$conf_default_company_invoicing';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find invoicing company ID $conf_default_company_invoicing: please check that you have selected a default invoicing company!");
	}
	$a = mysql_fetch_array($r);
	$selling_country = $a["country"];

	// TODO: Make it take values from the config table
	$acct_paypal_charges = "Expenses:Bank Servivce Charges:Paypal Charges";
	$acct_enets_charges = "Expenses:Bank Servivce Charges:eNETS Charges";
	$acct_with_vat_in_eec = "Income:Sales:Services with VAT in EEC";
	$acct_without_vat_in_eec = "Income:Sales:Services without VAT in EEC";
	$acct_in_own_country = "Income:Sales:Services in $selling_country";
	$acct_outside_eec = "Income:Sales:Sales outside EEC";
	$acct_vat_on_sales = "VAT:Output:VAT on Sales";

	// TODO: Make it take values from variables above
	$end_acct_paypal_charges = "Paypal Charges";
	$end_acct_enets_charges = "eNETS Charges";
	$end_acct_with_vat_in_eec = "Services with VAT in EEC";
	$end_acct_without_vat_in_eec = "Services without VAT in EEC";
	$end_acct_in_own_country = "Services in $selling_country";
	$end_acct_outside_eec = "Sales outside EEC";
	$end_acct_vat_on_sales = "VAT on Sales";

	$out .= "!Type:Cat
N$acct_paypal_charges
DPaypal Charges
E
^
N$acct_enets_charges
DeNETS Charges
E
^
N$acct_with_vat_in_eec
DServices with VAT in EEC
I
^
N$acct_without_vat_in_eec
DServices without VAT in EEC
I
^
N$acct_in_own_country
DServices in own country
I
^
N$acct_outside_eec
DSales in rest of world
I
^
N$acct_vat_on_sales
DVAT on Sales
^
";

	$out .= "!Account
TBank
N$end_acct_paypal_charges
DPaypal Charges
E
^
TBank
N$end_acct_enets_charges
DeNETS Charges
E
^
N$end_acct_with_vat_in_eec
DServices with VAT in EEC
I
^
N$end_acct_without_vat_in_eec
DServices without VAT in EEC
I
^
N$end_acct_in_own_country
DServices in own country
I
^
N$end_acct_outside_eec
DSales in rest of world
I
^
N$end_acct_vat_on_sales
DVAT on Sales
^
";

	// Fetch all completed orders for the period
	$q = "SELECT $pro_mysql_completedorders_table.id AS id,
		$pro_mysql_completedorders_table.id_client AS id_client,
		$pro_mysql_completedorders_table.date AS date,
		$pro_mysql_completedorders_table.product_id AS product_id,
		$pro_mysql_completedorders_table.payment_id AS payment_id,
		$pro_mysql_completedorders_table.country_code AS country_code FROM $pro_mysql_completedorders_table,$pro_mysql_pay_table
		WHERE $pro_mysql_completedorders_table.payment_id = $pro_mysql_pay_table.id
		AND $pro_mysql_completedorders_table.date >= '$start_date' AND $pro_mysql_completedorders_table.date <= '$end_date'
		ORDER BY $pro_mysql_pay_table.valid_date,$pro_mysql_pay_table.valid_time;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		die("No transactions for this period: $start_date to $end_date: $q");
	}
	for($i=0;$i<$n;$i++){
		// Fetch the corresponding payment
		$completed_order = mysql_fetch_array($r);
		$q2 = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$completed_order["payment_id"]."'";
		$r2 = mysql_query($q2)or die("Cannot execute query \"$q2\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 =! 1){
			die("Completed order ".$completed_order["id"]." has no corresponding payment ID");
		}
		$pay = mysql_fetch_array($r2);

		$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$completed_order["id_client"]."';";
		$r2 = mysql_query($q2)or die("Cannot execute query \"$q2\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 =! 1){
			$client_name = "Client name could not be fetched";
			$client_country = "";
		}else{
			$client = mysql_fetch_array($r2);
			$client_name = "";
			if($client["is_company"] == "yes"){
				$client_name .= $client["company_name"].": ";
			}
			$client_name .= $client["familyname"].", ".$client["christname"];
			$client_country = $client["country"];
		}
		$date = $completed_order["date"];
		$date_a = explode("-",$date);

		$pt_date = $date_a[0].$date_a[1].$date_a[2];
		$pt_date_plus = 100000000 + $completed_order["id"];
		$text_number = $pt_date . $pt_date_plus;

		$memo = $conf_administrative_site." - ".$text_number." - ".$client_name;

		// If the invoicing company is within EU, then check what region is the client in
		if (isset($cc_europe[ $selling_country ])){
			// Client is in the same country as seller: charge VAT
			if( $client_country == $selling_country ){
				$sales_account = $acct_in_own_country;
				$case = "own-country";
			// Client is in Europe, but not in the same country
			}elseif( isset($cc_europe[ $client_country ])){
				if( $client["is_company"] == "yes" && isset($client["vat_num"]) && $client["vat_num"] != ""){
					$sales_account = $acct_with_vat_in_eec;
					$case = "no-vat-eec";
				}else{
					$sales_account = $acct_without_vat_in_eec;
					$case = "vat-in-eec";
				}
			}else{
				$sales_account = $acct_outside_eec;
				$case = "vat-not-eec";
			}
		}else{
			$sales_account = "Sales";
			$case = "company-no-in-eec";
		}

		$wrote_cat = "no";

		$gate_charges = $pay["paiement_cost"];
		$pay_total = $pay["paiement_total"];
		$income = $pay_total - $gate_charges;

		// Paypal / eNETS charges
		switch($pay["secpay_site"]){
		case "paypal":
			$out .= "!Account
TBank
NPaypal
DPaypal
^
";
			$gate_acct = $acct_paypal_charges;
			break;
		case "enets":
			$out .= "!Account
TBank
NeNETS
DeNETS
^
";
			$gate_acct = $acct_enets_charges;
			break;
		default:
			$out .= "!Account
TBank
NUnkown processor
DUnkown processor
^
";
			$gate_acct = "Unknown processor";
			break;
		}

		// Start of transaction
		$out .= "!Type:Bank\n";
		// Date
		$out .= "D".$date_a[1]."/".$date_a[2]."/".$date_a[0]."\n";
		// Total payment from customer
		$out .= "T".$pay_total."\n";
		// Comment
		$out .= "P".$memo."\n";
		// Item number (used to order transctions...)
		$out .= "N".$pay["id"]."\n";
		// Cleared
		$out .= "C*\n";
		// VAT
		switch($case){
		case "own-country":
		case "vat-in-eec":
		case "vat-not-eec":
			$without_vat = round(($pay["paiement_total"] / (1 + ($pay["vat_rate"] / 100))),2);
			$vat = $pay["paiement_total"] - $without_vat;
			$out .= "L".$acct_vat_on_sales."\n";
			$out .= "S".$acct_vat_on_sales."\n";
			$out .= '$'.$vat."\n";
			$wrote_cat = "yes";
			break;
		// TODO: write code for VAT when invoicing country isn't in EEC!
		default:
			$without_vat = $pay["paiement_total"];
			break;
		}

		if($wrote_cat == "no"){
			$wrote_cat = "yes";
			$out .= "L".$gate_acct."\n";
		}
		$out .= "S".$gate_acct."\n";
		$out .= '$-'.$pay["paiement_cost"]."\n";

		// Sales account
		if($wrote_cat == "no"){
			$wrote_cat = "yes";
			$out .= "L".$sales_account."\n";
		}
		$out .= "S".$sales_account."\n";
		$out .= '$'.$without_vat."\n";

/*		// Memo
		$out .= "M".$memo."\n";
		// Category
		$out .= "L[$sales_account]\n";
		// First account in split
		$out .= "S[Paypal]\n";
		// First account amount
		$out .= '$'.$pay["refund_amount"]."\n";
		// Second account in split
		$out .= "S[Paypal Charges]\n";
		// Second account amount
		$out .= '$'.$pay["paiement_cost"]."\n";*/
		// End of transaction
		$out .= "^\n";
	}
	return $out;
}

function exportTransactions_vat_report($fmonth,$lmonth){
	global $secpayconf_currency_letters;
	global $pro_mysql_pay_table;
	global $pro_mysql_completedorders_table;
	global $pro_mysql_client_table;
	global $conf_administrative_site;
	global $conf_default_company_invoicing;
	global $pro_mysql_companies_table;
	global $cc_europe;
	get_secpay_conf();

	$out = "";

	// Calculate start and end date depending on the request
	$rda = explode("-",$lmonth);
	$days_in_month = date("t",mktime(1,1,1,$rda[1],2,$rda[0]));
	$end_date = $lmonth . "-" . $days_in_month;
	$start_date = $fmonth . "-01";

	// TODO: replace this by something which checks what country has been used for the transaction
	$q = "SELECT country FROM $pro_mysql_companies_table WHERE id='$conf_default_company_invoicing';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find invoicing company ID $conf_default_company_invoicing: please check that you have selected a default invoicing company!");
	}
	$a = mysql_fetch_array($r);
	$selling_country = $a["country"];

	// Fetch all completed orders for the period
	$q = "SELECT * FROM $pro_mysql_completedorders_table WHERE date >= '$start_date' AND date <= '$end_date' ORDER BY date;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		die("No transactions for this period: $start_date to $end_date: $q");
	}
	$ar = array();
	for($i=0;$i<$n;$i++){
		// Fetch the corresponding payment
		$completed_order = mysql_fetch_array($r);
		$q2 = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$completed_order["payment_id"]."'";
		$r2 = mysql_query($q2)or die("Cannot execute query \"$q2\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 =! 1){
			die("Completed order ".$completed_order["id"]." has no corresponding payment ID");
		}
		$pay = mysql_fetch_array($r2);

		$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$completed_order["id_client"]."';";
		$r2 = mysql_query($q2)or die("Cannot execute query \"$q2\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 =! 1){
			continue;
		}else{
			$client = mysql_fetch_array($r2);
			$client_country = $client["country"];
		}

		// If the invoicing company is within EU, then check what region is the client in
		if (isset($cc_europe[ $selling_country ])){
			// Client is in the same country as seller: charge VAT
			if( $client_country == $selling_country ){
				$case = "own-country";
			// Client is in Europe, but not in the same country
			}elseif( isset($cc_europe[ $client_country ])){
				if( $client["is_company"] == "yes" && isset($client["vat_num"]) && $client["vat_num"] != ""){
					$case = "vat-in-eec";
				}else{
					$case = "no-vat-eec";
				}
			}else{
				$case = "vat-not-eec";
			}
		}else{
			$sales_account = "Sales";
			$case = "company-no-in-eec";
		}
		if($case == "vat-in-eec"){
			if($pay["paiement_total"] != 0){
				$vat_num = str_replace(" ","",$client["vat_num"]);
				if( !isset($ar[$vat_num]) ){
					$ar[$vat_num] = 0;
				}
				$ar[$vat_num] += $pay["paiement_total"];
			}
		}
	}
	ksort(&$ar);
	$n = sizeof($ar);
	$keys = array_keys($ar);
	$out = "";
	//echo "<pre>"; print_r($ar); print_r($keys); echo "</pre>";
	for($i=0;$i<$n;$i++){
		$vatnum = $keys[$i];
		$amount = $ar[ $vatnum ];
		// First 2 chars are the country code of customer
		$out .= strtoupper(substr($vatnum,0,2));
		$out .= ",";
		// Rest of should be the actual number
		$out .= str_replace(" ","",substr($vatnum,2));
		$out .= ",";
		// Total value of suply
		$out .= $amount;
		$out .= ",";
		// This is a B2B service, so code is 3
		$out .= "3\n";
	}
	return $out;
}

if($_REQUEST["format"] == "qif"){
	$transactions = exportTransactions();

	header ("Content-type: application/x-qexqif");
	header('Content-disposition: attachment; filename="dtc_sales_'.$_REQUEST["date"].'.qif"');
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");	// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");	// HTTP/1.0

	die( $transactions."\n" );
}

if($_REQUEST["format"] == "csv_vat"){
	$transactions = exportTransactions_vat_report($_REQUEST["first_month"],$_REQUEST["last_month"]);
	header ("Content-type: application/csv");
	header('Content-disposition: attachment; filename="dtc_no_vat_report_'.$_REQUEST["first_month"]."_".$_REQUEST["last_month"].'.csv"');
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	die( $transactions."\n" );
}

?>