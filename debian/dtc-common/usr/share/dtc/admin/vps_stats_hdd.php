<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

// vps_stats_network.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_node

$panel_type = "none";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

if( $_SERVER["REQUEST_URI"] != "/dtc/vps_stats_hdd.php" && $_SERVER["SCRIPT_NAME"] != "/dtc/vps_stats_hdd.php"){
	require_once("authme.php");
}

if(!isHostnameOrIP($_REQUEST["vps_node"])){
	die("VPS node name has wrong format: dying.");
}
if(!checkSubdomainFormat($_REQUEST["vps_name"])){
	die("VPS name has wrong format: dying.");
}

session_name("wallid");
header ("Content-type: image/png");

// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

$width = 120;
$height = 48;

$im = ImageCreate ($width, $height) or die ("Cannot Initialize new GD image stream");
$lightblue_color = ImageColorAllocate ($im, 190, 190, 212);
$black = ImageColorAllocate ($im, 0, 0, 0);
$white = ImageColorAllocate ($im, 255, 255, 255);
$red = ImageColorAllocate ($im, 255, 0, 0);

for($m=0;$m<12;$m++){
	$tr_tbl[$m] = 0;
}

// Get all the stats info
$q = "SELECT * FROM $pro_mysql_vps_table WHERE vps_server_hostname='".$_REQUEST["vps_node"]."' AND vps_xen_name='".$_REQUEST["vps_name"]."';";
//echo $q;
$r = mysql_query($q)or die("Cannot query $q !");
$n = mysql_num_rows($r);
if($n != 1)die("Client not found!");
$c = mysql_fetch_array($r);
$bpquota = $c["bandwidth_per_month_gb"] * 1024 * 1024 * 1024;

$cur_month = date("m");
$cur_year = date("Y");
for($m=0;$m<12;$m++){
	$month = $cur_month+$m+1;
	if($month > 12){
		$month -= 12;
		$year = $cur_year;
	}else{
		$year = $cur_year-1;
	}

	$q = "SELECT diskio_count FROM $pro_mysql_vps_stats_table
	WHERE vps_server_hostname='".$_REQUEST["vps_node"]."'
	AND vps_xen_name='xen".$_REQUEST["vps_name"]."'
	AND month='$month'
	AND year='$year';";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." MySql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		if(isset($a["diskio_count"])){
			$tr_tbl[$m] += $a["diskio_count"];
		}
	}
}

// Get the max value
$foundmax=0;
for($m=0;$m<12;$m++){
	if($tr_tbl[$m] > $foundmax){
		$foundmax = $tr_tbl[$m];
	}
}

$bpquota = 1000000000;
$max = 1000000000;	// 1 T seems a good value
if($foundmax > $bpquota)
	$max = $foundmax;
$max *= 1.05;

// Draw the actual image
$quotaY = $height - (($bpquota * $height ) / ($max));
imageline ( $im, 0, $quotaY, $width, $quotaY, $red);
for($m=0;$m<12;$m++){
	$x1 = $m*10;
	$y1 = 1*($height-( ($tr_tbl[$m] * $height ) / ($max) ));
	$x2 = $m*10+9;
	$y2 = $height;
	if($tr_tbl[$m] > $bpquota){
		imagefilledrectangle ( $im, $x1, $y1, $x2, $y2, $red);
	}else{
		imagefilledrectangle ( $im, $x1, $y1, $x2, $y2, $black);
	}
	$month = $cur_month+$m+1;
	if($month > 12){
		$month -= 12;
		$year = $cur_year;
	}else{
		$year = $cur_year-1;
	}

	$month_txt = date("M-Y",mktime(1, 1, 1, $month, 1, $year));
	imagestringup ( $im, 1, $m*10, $height-6, $month_txt, $white);
	imageline ( $im, $x2, 0, $x2, $height, $white);
}

ImagePng ($im);
ImageDestroy($im);

?>
