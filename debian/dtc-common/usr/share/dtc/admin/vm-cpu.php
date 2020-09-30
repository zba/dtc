<?php

$panel_type = "none";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

if(!isHostnameOrIP($_REQUEST["vps_server_hostname"])){
	die("VPS node name has wrong format: dying.");
}
if(!checkSubdomainFormat($_REQUEST["vps_name"])){
	die("VPS name has wrong format: dying.");
}

if( $_SERVER["SCRIPT_NAME"] != "/dtc/vm-cpu.php"){
	require_once("authme.php");
}else{
	checkLoginPass($adm_login,$adm_pass);
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE owner='$adm_login' AND vps_server_hostname='".$_REQUEST["vps_server_hostname"]."' AND vps_xen_name='".$_REQUEST["vps_name"]."'";
	$r = mysql_query($q)or die();
	$n = mysql_num_rows($r);
	if($n != 1){
		die( _("Access not granted line ") .__LINE__. _(" file ") .__FILE__ );
	}
}

// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

checkLoginPass($adm_login,$adm_pass);

// Get all the stats info
$q = "SELECT * FROM $pro_mysql_vps_table WHERE vps_server_hostname='".$_REQUEST["vps_server_hostname"]."' AND vps_xen_name='".$_REQUEST["vps_name"]."' AND owner='$adm_login';";
//echo $q;
$r = mysql_query($q)or die("Cannot query $q !");
$n = mysql_num_rows($r);
if($n != 1)die("Client not found!");
$c = mysql_fetch_array($r);


// $rrd = '/var/lib/dtc/etc/cpu.rrd';
$rrd = "/var/lib/dtc/dtc-xenservers-rrds/".$_REQUEST["vps_server_hostname"]."/xen".$_REQUEST["vps_name"]."-cpu.rrd";

$xpoints = 320;
$ypoints = 100;
$vert_label = "CPU usage";

if( isset($_REQUEST["graph"]) ){

	switch($_REQUEST["graph"]){
		case "hour":
			$title = 'Hour graph';
			$steps = 3600;
			break;
		case "day":
			$title = 'Day Graph';
			$steps = 3600*24;
			break;
		case "week":
			$title = 'Week Graph';
			$steps = 3600*24*7;
			break;
		case "month":
			$title = 'Month Graph';
			$steps = 3600*24*31;
			break;
		case "year":
			$title = 'Year Graph';
			$steps = 3600*24*365;
			break;
		default:
			die("Nothing to do here...");
			break;
	}
	$range = - $steps;
	$filename = tempnam("/tmp","dtc_cpugraph");
	$cmd = "rrdtool graph $filename --imgformat PNG --width $xpoints --height $ypoints --start $range --end now --vertical-label '$vert_label' --title '$title' --lazy --interlaced ";
	$cmd .= "DEF:cpuseconds=$rrd:cpuseconds:AVERAGE ";
	$cmd .= "'LINE1:cpuseconds#ff0000:Seconds of CPU used/min:' 'GPRINT:cpuseconds:MAX:Max\: %0.0lf' 'GPRINT:cpuseconds:AVERAGE:Avg\: %0.0lf/min\\n' ";
	exec($cmd,$output);

	$filesize = filesize($filename);

	if( ($fp = fopen($filename,"rb")) != NULL ){
		header("Content-Type: image/png");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $filesize");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public", false);
		header("Expires: 0");
		while(!feof($fp) && connection_status() == 0){
			print(fread($fp,1024*8));
			flush();
		}
		fclose($fp);
	}
	unlink($filename);
}

?>
