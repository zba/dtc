<?php

$panel_type = "none";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");


// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

$xpoints = 640;
$ypoints = 200;
$vert_label = "Sales statistics";

if( isset($_REQUEST["graph"]) ){

	switch($_REQUEST["graph"]){
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

	$filename = tempnam("/tmp","stat_total_active_prods");
	$cmd = "rrdtool graph $filename --imgformat PNG --width $xpoints --height $ypoints --start $range --end now --vertical-label '$vert_label' --title '$title' --lazy --interlaced ";

	$colors = array("000000", "FF0000", "00FF00", "0000FF", "DEDE00", "00FFFF", "FF90FF", "FF8040", "C040A0", "A0A0A0", "40A0A0", "40A0FF", "FFA040");
	$num_cols = sizeof($colors);

	$cmd .= "DEF:myshared=/var/lib/dtc/etc/stat_total_active_prods.rrd:shared:AVERAGE ";
	$cmd .= "CDEF:mysharedzero=myshared,DUP,UN,EXC,0,EXC,IF ";
	$cmd .= "DEF:myvps=/var/lib/dtc/etc/stat_total_active_prods.rrd:vps:AVERAGE ";
	$cmd .= "CDEF:myvpszero=myvps,DUP,UN,EXC,0,EXC,IF ";
	$cmd .= "DEF:mydedicated=/var/lib/dtc/etc/stat_total_active_prods.rrd:vps:AVERAGE ";
	$cmd .= "CDEF:mydedicatedzero=mydedicated,DUP,UN,EXC,0,EXC,IF ";

	$cmd .= "AREA:mysharedzero#00FF00:shared ";
	$cmd .= "STACK:myvpszero#0000FF:vps ";
	$cmd .= "STACK:mydedicatedzero#FF0000:dedicated ";

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
