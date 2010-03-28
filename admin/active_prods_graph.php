<?php

$panel_type="admin";
require_once("../shared/autoSQLconfig.php");
require_once("authme.php");

$rrd = $conf_generated_file_path.'/stat_total_active_prods.rrd';
$xpoints = 400;
$ypoints = 160;
$vert_label = "Active products";

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
	$filename = tempnam("/tmp","dtc_netgraph");
	$cmd = "rrdtool graph $filename --imgformat PNG --width $xpoints --height $ypoints --start $range --end now --vertical-label '$vert_label' --title '$title' --lazy --interlaced ";
	$cmd .= "DEF:vps=$rrd:vps:AVERAGE DEF:dedicated=$rrd:dedicated:AVERAGE DEF:shared=$rrd:shared:AVERAGE ";
	$cmd .= "'AREA:shared#0000ff:Active shared accounts:' 'GPRINT:shared:MAX:Maximum\: %0.0lf' 'GPRINT:shared:AVERAGE:Average\: %0.0lf/min\l' ";
	$cmd .= "'STACK:vps#00ff00:Active VPS:' 'GPRINT:vps:MAX:Maximum\: %0.0lf' 'GPRINT:vps:AVERAGE:Average\: %0.0lf/min\\n' ";
	$cmd .= "'STACK:dedicated#ff0000:Active dedicated servers:' 'GPRINT:dedicated:MAX:Maximum\: %0.0lf' 'GPRINT:dedicated:AVERAGE:Average\: %0.0lf/min\l' ";
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
}else{
	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
<TITLE>Network usage statistics for '.$_SERVER["SERVER_NAME"].'</TITLE>
<style type="text/css">
body{
	height:100%;
	margin:0;
	color: #000000;
}
h1 {
	font: 14px Arial, Helvetica, sans-serif;
	font-weight: bold;
	text-decoration: underline;
	color: #000000;
}
</style>
</HEAD>
<BODY BGCOLOR="#FFFFFF">
<H1>Network Usage Statistics for '.$_SERVER["SERVER_NAME"].'</H1>
<center>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=hour" ALT="Hour Sales Graph"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=day" ALT="Day Sales Graph"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=week" ALT="Week Sales Graph"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=month" ALT="Month Sales Graph"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=year" ALT="Year Sales Graph">
</center>
</body>
</html>';
}

?>
