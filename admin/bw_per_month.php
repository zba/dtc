<?php

require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

session_name("wallid");
//header ("Content-type: image/png");

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
$lightblue_color = ImageColorAllocate ($im, 210, 210, 235);
$black = ImageColorAllocate ($im, 0, 0, 0);

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

$q = "SELECT adm_login FROM $pro_mysql_admin_table WHERE id_client='".$_REQUEST["cid"]."';";
$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__);
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$a = mysql_fetch_array($r);
	$q2 = "SELECT name FROM $pro_mysql_domain_table WHERE owner='".$a["adm_login"]."';";
	$r2 = mysql_query($q2)or die("Cannot query $q2 in ".__FILE__." line ".__LINE__);
	$n2 = mysql_num_rows($r2);
	for($j=0;$j<$n2;$j++){
		$a2 = mysql_fetch_array($r2);
		$q3 = "SELECT subdomain_name FROM $pro_mysql_subdomain_table WHERE domain_name='".$a2["name"]."';";
		$r3 = mysql_query($q3)or die("Cannot query $q3 in ".__FILE__." line ".__LINE__);
		$n3 = mysql_num_rows($r3);
		for($k=0;$k<$n3;$k++){
			$a3 = mysql_fetch_array($r3);
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
				$q4 = "SELECT bytes_sent FROM $pro_mysql_acc_http_table
WHERE vhost='".$a3["subdomain_name"]."' AND domain='".$a2["name"]."'
AND year='$year' AND month='$month';";
				$r4 = mysql_query($q4)or die("Cannot query $q4 in ".__FILE__." line ".__LINE__);
				$n4 = mysql_num_rows($r4);
				if($n4 == 1){
					$a4 = mysql_fetch_array($r4);
					$tr_tbl[$m] += $a4["bytes_sent"];
				}
			}
		}
	}
}
$bpquota = 1024 * 1024 * 1024;
//$tr_tbl[11] = 512 * 1024 * 1024;
for($m=0;$m<12;$m++){
//	echo $m.":".$tr_tbl[$m]."<br>";
	$x1 = $m*10;
	$y1 = 1*($height-( ($tr_tbl[$m] * $height ) / ($bpquota) ));
	$x2 = $m*10+9;
	$y2 = $height;
	imagefilledrectangle ( $im, $x1, $y1, $x2, $y2, $black);
}


ImagePng ($im);
ImageDestroy($im);

?>