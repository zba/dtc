#!/usr/bin/env php
<?php

$START_INVOICE="2011-01-01";
$END_INVOICE="2011-03-31";

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

$script_start_time = time();
$start_stamps = gmmktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");
require_once("genfiles/genfiles.php");

get_secpay_conf();

if($conf_invoice_scp_addr == ""){
	echo "Not scping invoices because address is not set\n";
	exit(0);
}else{
	echo "Preparing invoices to scp to $conf_invoice_scp_addr\n";
}
// Calculate start and end date (inclusive of this date)
$YEAR = date("Y");
$MONTH = date("m");
$DAY = date("d");
if($conf_invoice_scp_when == "day"){
	$DAY = $DAY - 1;
	if($DAY == 0){
		$MONTH = $MONTH - 1;
		if($MONTH == 0){
			$MONTH = 12;
			$YEAR = $YEAR -1;
			$DAY_IN_MONTH = cal_days_in_month(CAL_GREGORIAN,$MONTH,$YEAR);
			$DAY = $DAY_IN_MONTH;
		}
	}
	$START_INVOICE = $YEAR."-".$MONTH."-".$DAY;
	$END_INVOICE = $START_INVOICE;
}else{
	if($DAY != 1){
		// If we do monthly scp of invoices, then we exit if we aren't the 1st of the month
		exit(0);
	}
	$MONTH = $MONTH - 1;
	if($MONTH == 0){
		$MONTH = 12;
		$YEAR = $YEAR -1;
	}
	$DAY_IN_MONTH = cal_days_in_month(CAL_GREGORIAN,$MONTH,$YEAR);
	$START_INVOICE = "$YEAR-$MONTH-01";
	$END_INVOICE = "$YEAR-$MONTH-$DAY_IN_MONTH";
}
echo "Period: $START_INVOICE to $END_INVOICE\n";


$q = "SELECT * FROM $pro_mysql_completedorders_table WHERE date >= '".$START_INVOICE."' AND DATE <= '".$END_INVOICE."' ORDER BY date;";
echo $q."\n";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);

if($n == 0){
	echo "Found no invoices to save for this period: exiting\n";
	exit(0);
}

$temp_dir = "/tmp/invoices_".getRandomValue();
mkdir($temp_dir);

echo "Found $n invoices\n";
for($i=0;$i<$n;$i++){
	$comp = mysql_fetch_array($r);
	$q2 = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$comp["payment_id"]."';";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1){
		echo "Completed order ".$comp["id"]." has no corresponding payment entry in payment table!\n";
		continue;
	}
	$payment = mysql_fetch_array($r2);
	$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$comp["id_client"]."';";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1){
		echo "Completed order ".$comp["id"]." has no corresponding client entry in client table!\n";
		continue;
	}
	$client = mysql_fetch_array($r2);

	$random_val = getRandomValue();
	$q2 = "UPDATE $pro_mysql_completedorders_table SET download_pass='".$random_val."' WHERE id='".$comp["id"]."';";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	if($client["is_company"] == "yes"){
		$client_name = $client["company_name"]."_".$client["familyname"]."_".$client["christname"];
	}else{
		$client_name = $client["familyname"]."_".$client["christname"];
	}
	$client_name = str_replace(" ","_",$client_name);
	$client_name = str_replace("'","_",stripslashes($client_name));
	$client_name = str_replace(":","_",$client_name);
	$client_name = str_replace(",","_",$client_name);
	$client_name = str_replace(".","_",$client_name);
	$client_name = str_replace("(","_",$client_name);
	$client_name = str_replace(")","_",$client_name);
	$client_name = escapeshellarg($client_name);
	$file = "$temp_dir/".$comp["date"]."_".$comp["id"]."_".$client_name."_".$payment["paiement_total"]."_".$secpayconf_currency_letters.".pdf";
	echo $file."\n";
	system("wget --no-check-certificate -q 'https://$conf_administrative_site/dtc/invoice.php?download_pass=$random_val&id=".$comp["id"]."' -O $file");
}
echo "Doing scp of all invoices\n";
system("scp $temp_dir/* $conf_invoice_scp_addr 2>&1");
echo "Deleting temp folder\n";
system("rm -rf $temp_dir");
?>