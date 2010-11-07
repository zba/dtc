<?php

$panel_type = "none";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

if(!isHostnameOrIP($_REQUEST["vps_server_hostname"])){
	die("VPS node name has wrong format: dying.");
}
if(isset($_REQUEST["vps_name"])){
	if(!checkSubdomainFormat($_REQUEST["vps_name"])){
		die("VPS name has wrong format: dying.");
	}
}

if( $_SERVER["SCRIPT_NAME"] != "/dtc/vm-cpu-all.php"){
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

	// List all rrd files in the folder
	$all_rrd_files = array();
	$xen_vps_numbers = array();
	if(is_dir("/var/lib/dtc/dtc-xenservers-rrds/".$_REQUEST["vps_server_hostname"])){
		if ($handle = opendir("/var/lib/dtc/dtc-xenservers-rrds/".$_REQUEST["vps_server_hostname"])) {
			while (false !== ($file = readdir($handle))) {
				if(preg_match("/^xen([0-9])([0-9])-cpu.rrd\$/",$file)){
					$all_rrd_files[] = $file;
					$xen_vps_numbers[] = substr($file,3,2);
				}
			}
		}
	}
	sort($all_rrd_files);
	$num_rrd = sizeof($all_rrd_files);

	$filename = tempnam("/tmp","dtc_cpugraph");
	$cmd = "rrdtool graph $filename --imgformat PNG --width $xpoints --height $ypoints --start $range --end now --vertical-label '$vert_label' --title '$title' --lazy --interlaced ";

	$colors = array("000000", "FF0000", "00FF00", "0000FF", "DEDE00", "00FFFF", "FF90FF", "FF8040", "C040A0", "A0A0A0", "40A0A0", "40A0FF", "FFA040");
	$num_cols = sizeof($colors);

	for($i=0;$i<$num_rrd;$i++){
		$rrd = "/var/lib/dtc/dtc-xenservers-rrds/".$_REQUEST["vps_server_hostname"]."/".$all_rrd_files[$i];
		$cmd .= "DEF:cpu$i=$rrd:cpuseconds:AVERAGE ";
		$cmd .= "CDEF:percentcpu$i=cpu$i,1.5,* ";
		$cmd .= "CDEF:percentcpuzero$i=percentcpu$i,DUP,UN,EXC,0,EXC,IF ";
	}
	for($i=0;$i<$num_rrd;$i++){
		if($i == 0){
			$plop = "AREA";
		}else{
			$plop = "STACK";
		}
		$color = $colors[ $i % $num_cols ];
		$cmd .= "$plop:percentcpuzero$i#$color:xen".substr($all_rrd_files[$i],3,2)." ";
		$cmd .= "GPRINT:percentcpu$i:'AVERAGE:CPU\:%02.0lf%%\\j' ";
	}
//	echo $cmd;
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
