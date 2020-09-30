<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

$panel_type = "none";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

if( $_SERVER["REQUEST_URI"] != "/dtc/bw_per_month.php"){
	require_once("authme.php");
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

if(isFtpLogin($_REQUEST["adm_login"])){
	$adm_login = $_REQUEST["adm_login"];
}else{
	die("No login in query");
}

if(isDTCLogin($_REQUEST["adm_pass"])){
	$adm_pass = $_REQUEST["adm_pass"];
}else{
        die("No pass in query");
}

checkLoginPass($adm_login,$adm_pass);

$q = "SELECT id_client FROM admin WHERE adm_login='$adm_login'";
$r = mysql_query($q);
$n = mysql_num_rows($r);
if($n != 1){
	die("Admin not found");
}else{
	$a = mysql_fetch_array($r);
	$cid = $a["id_client"];
}

if($cid == 0){
	die("No valid cid");
}

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


/*
$q = "SELECT bw_quota_per_month_gb FROM $pro_mysql_client_table WHERE id='".$_REQUEST["cid"]."';";
$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__.mysql_error());
$n = mysql_num_rows($r);
if($n!=1)die("Client not found!");
$c = mysql_fetch_array($r);
$bpquota = $c["bw_quota_per_month_gb"];
*/

$q = "SELECT * FROM $pro_mysql_client_table WHERE id='$cid';";
//echo $q;
$r = mysql_query($q)or die("Cannot query $q !");
$n = mysql_num_rows($r);
if($n != 1)die("Client not found!");
$c = mysql_fetch_array($r);
$bpquota = $c["bw_quota_per_month_gb"] * 1024 * 1024 * 1024;

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

	$q = "SELECT sum(bytes_sent) as sent
FROM admin,domain,subdomain,http_accounting
WHERE admin.adm_login='".$_REQUEST["adm_login"]."'
AND domain.owner=admin.adm_login
AND subdomain.domain_name=domain.name
AND http_accounting.vhost=subdomain.subdomain_name
AND http_accounting.domain=subdomain.domain_name
AND year='$year' AND month='$month'";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." MySql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$tr_tbl[$m] += $a["sent"];
	}

	$q = "SELECT sum(transfer) as sent FROM admin,domain,$pro_mysql_acc_ftp_table
WHERE admin.id_client='".$cid."'
AND domain.owner=admin.adm_login
AND $pro_mysql_acc_ftp_table.sub_domain=domain.name
AND year='$year' AND month='$month';";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." SQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$tr_tbl[$m] += $a["sent"];
	}

	$q = "SELECT sum(smtp_trafic+pop_trafic+imap_trafic) as sent
FROM admin,domain,$pro_mysql_acc_email_table
WHERE admin.id_client='".$cid."'
AND domain.owner=admin.adm_login
AND $pro_mysql_acc_email_table.domain_name=domain.name
AND $pro_mysql_acc_email_table.year='$year' AND $pro_mysql_acc_email_table.month='$month';";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." SQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$tr_tbl[$m] += $a["sent"];
//		$tr_tbl[$m] += $a["smtp_trafic"];
//		$tr_tbl[$m] += $a["pop_trafic"];
//		$tr_tbl[$m] += $a["imap_trafic"];
	}

}

//$bpquota = 1024 * 1024 * 1024;
//$tr_tbl[11] = 512 * 1024 * 1024;

$foundmax=0;
for($m=0;$m<12;$m++){
	if($tr_tbl[$m] > $foundmax){
		$foundmax = $tr_tbl[$m];
	}
}

$max = $bpquota;
if($foundmax > $bpquota)
	$max = $foundmax;
$max *= 1.05;

$quotaY = $height - (($bpquota * $height ) / ($max));
imageline ( $im, 0, $quotaY, $width, $quotaY, $red);
for($m=0;$m<12;$m++){
//	echo $m.":".$tr_tbl[$m]."<br>";
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
//	imageline ( $im, $x1, 0, $x1, $height, $black);
	imageline ( $im, $x2, 0, $x2, $height, $white);
}


ImagePng ($im);
ImageDestroy($im);

?>
