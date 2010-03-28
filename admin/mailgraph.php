<?php

$panel_type="admin";
require_once("../shared/autoSQLconfig.php");
require_once("authme.php");

$rrd = $conf_generated_file_path.'/mailqueues.rrd';
$xpoints = 800;
$ypoints = 160;
$vert_label = "Mail queue statistics";

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
	$filename = tempnam("/tmp","dtc_mailgraph");
//	$filename = "/tmp/mail_queue_usage_".$_REQUEST["graph"].".png";
	$cmd = "rrdtool graph $filename --imgformat PNG --width $xpoints --height $ypoints --start $range --end now --vertical-label '$vert_label' --title '$title' --lazy --interlaced ";
	$cmd .= "DEF:active=$rrd:active:AVERAGE DEF:deferred=$rrd:deferred:AVERAGE ";
	$cmd .= "'LINE2:active#00ff00:Active+incoming+maildrop:' 'GPRINT:active:MAX:Maximum\: %0.0lf' 'GPRINT:active:AVERAGE:Average\: %0.0lf/min\\n' ";
	$cmd .= "'LINE1:deferred#ff0000:Deferred:' 'GPRINT:deferred:MAX:Maximum\: %0.0lf' 'GPRINT:deferred:AVERAGE:Average\: %0.0lf/min\l' ";
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
<TITLE>Mail queue statistics for '.$_SERVER["SERVER_NAME"].'</TITLE>
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
<H1>Mail queue Statistics for '.$_SERVER["SERVER_NAME"].'</H1>
<center>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=hour" ALT="Hour Mail Queue Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=day" ALT="Day Mail Queue Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=week" ALT="Week Mail Queue Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=month" ALT="Month Mail Queue Graph" width="897" height="253"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=year" ALT="Year Mail Queue Graph" width="897" height="253">
</center>
</body>
</html>';
}

?>
