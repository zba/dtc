#!/usr/bin/env php
<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

$script_start_time = time();
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

$date_now = date("Y-m-d");

// Total VPS
$total_vps = 0;
$q = "SELECT * FROM product WHERE renew_prod_id = '0' AND heb_type = 'vps'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$product = mysql_fetch_array($r);

	$q2 = "SELECT COUNT(id) AS num_vps FROM vps WHERE product_id='".$product["id"]."' AND expire_date > NOW();";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1){
		die("Not one row line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r2);
	$num_vps = $a["num_vps"];
	$period_array = explode("-",$product["period"]);
	$year = $period_array[0];
	$month = $period_array[1];
	if($year != 0){
		$month = $month + $year * 12;
	}
	$price_per_month = $product["price_dollar"] / $month;
	$total_vps += $num_vps * $price_per_month;
//	echo $product["name"] . "(" . $price_per_month . "USD)" . ": ". $num_vps . "   ". $total_vps ."\n";
}
$total_vps = round($total_vps,2);
//echo "Total VPS: $total_vps\n";

// Total dedicated
$total_dedicated = 0;
$q = "SELECT * FROM product WHERE renew_prod_id = '0' AND heb_type = 'server'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$product = mysql_fetch_array($r);

	$q2 = "SELECT COUNT(id) AS num_dedicated FROM dedicated WHERE product_id='".$product["id"]."' AND expire_date > NOW();";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1){
		die("Not one row line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r2);
	$num_dedicated = $a["num_dedicated"];
	$period_array = explode("-",$product["period"]);
	$year = $period_array[0];
	$month = $period_array[1];
	if($year != 0){
		$month = $month + $year * 12;
	}
	if($month != 0){
		$price_per_month = $product["price_dollar"] / $month;
		$total_dedicated += $num_dedicated * $price_per_month;
	}
//	echo $product["name"] . "(" . $price_per_month . "USD)" . ": ". $num_dedicated . "   ". $total_dedicated ."\n";
}
$total_dedicated = round($total_dedicated,2);

// Total shared
$total_shared = 0;
$q = "SELECT * FROM product WHERE renew_prod_id = '0' AND heb_type = 'shared'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$product = mysql_fetch_array($r);

	$q2 = "SELECT COUNT(adm_login) AS num_shared FROM admin WHERE prod_id='".$product["id"]."' AND expire > NOW();";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1){
		die("Not one row line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r2);
	$num_shared = $a["num_shared"];
	$period_array = explode("-",$product["period"]);
	$year = $period_array[0];
	$month = $period_array[1];
	if($year != 0){
		$month = $month + $year * 12;
	}
	if($month == 0){
		$month = 1 / $period_array[2] / 30;
	}
	$price_per_month = $product["price_dollar"] / $month;
	$total_shared += $num_shared * $price_per_month;
}
$total_shared = round($total_shared,2);

//echo $cmd."\n";
if ( file_exists("/usr/bin/rrdtool") ){
	$RRDTOOL = "/usr/bin/rrdtool";
}else if ( file_exists("/usr/local/bin/rrdtool") ){
	$RRDTOOL = "/usr/local/bin/rrdtool";
}else if ( file_exists("/opt/local/bin/rrdtool") ){
	$RRDTOOL = "/opt/local/bin/rrdtool";
}else{
	die( "Could not find the rrdtool binary in file ".__FILE__ );
}

$cmd = "$RRDTOOL update $conf_generated_file_path/stat_total_active_prods.rrd N:$total_shared:$total_vps:$total_dedicated";
// echo $cmd."\n";
$result = exec($cmd,$lines,$return_val);

?>
