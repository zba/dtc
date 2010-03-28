<?php

$panel_type="admin";
require_once("../shared/autoSQLconfig.php");
require_once("authme.php");

$rrd = $conf_generated_file_path.'/netusage.rrd';
$xpoints = 800;
$ypoints = 160;
$vert_label = "Network transfer";

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
//	$filename = "/tmp/network_usage_".$_REQUEST["graph"].".png";
	$cmd = "rrdtool graph $filename --imgformat PNG --width $xpoints --height $ypoints --start $range --end now --vertical-label '$vert_label' --title '$title' --lazy --interlaced ";
	$cmd .= "DEF:bytesin=$rrd:bytesin:AVERAGE DEF:bytesout=$rrd:bytesout:AVERAGE ";
	$cmd .= "'LINE2:bytesin#00ff00:Incoming network traffic in bytes:' 'GPRINT:bytesin:MAX:Maximum\: %0.0lf' 'GPRINT:bytesin:AVERAGE:Average\: %0.0lf/min\\n' ";
	$cmd .= "'LINE1:bytesout#0000ff:Outgoing network traffic in bytes:' 'GPRINT:bytesout:MAX:Maximum\: %0.0lf' 'GPRINT:bytesout:AVERAGE:Average\: %0.0lf/min\l' ";
	$cmd;
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
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=hour" ALT="Hour Netusage Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=day" ALT="Day Netusage Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=week" ALT="Week Netusage Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=month" ALT="Month Netusage Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=year" ALT="Year Netusage Graph" width="897" height="253">
</center>
</body>
</html>';
}

?>
