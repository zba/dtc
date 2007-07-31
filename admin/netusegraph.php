<?php

$rrd = '/var/lib/dtc/etc/netusage.rrd';
$xpoints = 800;
$points_per_sample = 3;
$ypoints = 160;
$ypoints_err = 80;
$vert_label = "Network transfer";

/*$color = array(
	"sent"     => '000099', // rrggbb in hex
	"received" => '00FF00',
	"rejected" => '999999', 
	"bounced"  => '993399',
	"virus"    => 'FFFF00',
	"spam"     => 'FF0000');*/

if( isset($_REQUEST["graph"]) ){
	switch($_REQUEST["graph"]){
		case "day":
			$title = 'Day Graph';
			$seconds = 3600*24;
			break;
		case "week":
			$title = 'Week Graph';
			$seconds = 3600*24*7;
			break;
		case "month":
			$title = 'Month Graph';
			$seconds = 3600*24*31;
			break;
		case "year":
			$title = 'Year Graph';
			$seconds = 3600*24*365;
			break;
		default:
			die("Nothing to do here...");
			break;
	}
	$range = $seconds;
	$filename = "/tmp/network_usage_".$_REQUEST["graph"].".png";
	$cmd = "rrdtool graph $filename --imgformat PNG --width $xpoints --height $ypoints --start -$range --end 0 --vertical-label '$vert_label' --title '$title' --lazy --interlaced ";
	$cmd .= "DEF:bytesin=$rrd:bytesin:AVERAGE DEF:bytesout=$rrd:bytesout:AVERAGE ";
	$cmd .= "'LINE2:bytesin#00ff00:Incoming network traffic in bytes:' 'GPRINT:bytesin:MAX:Maximum\: %0.0lf' 'GPRINT:bytesin:AVERAGE:Average\: %0.0lf/min\\j' ";
	$cmd .= "'LINE1:bytesout#0000ff:Outgoing network traffic in bytes:' 'GPRINT:bytesout:MAX:Maximum\: %0.0lf' 'GPRINT:bytesout:AVERAGE:Average\: %0.0lf/min\l' ";
//	$cmd .= "'HRULE:0#000000 COMMENT:[\'".date("Y-m-d h:i:s")."\']'";
	//echo $cmd;
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
}else{
	echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
<TITLE>Network usage statistics for '.$_SERVER["SERVER_NAME"].'</TITLE>
<style type="text/css">
h1 {
font: 12px Helvetica, Arial, sans-serif;
font-weight: bold;
color: #1C3029;
}
</style>
</HEAD>
<BODY BGCOLOR="#FFFFFF">
<H1>Network Usage Statistics for '.$_SERVER["SERVER_NAME"].'</H1>\n<center>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=day" ALT="Day Netusage Graph"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=week" ALT="Week Netusage Graph"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=month" ALT="Month Netusage Graph"><br>
<IMG BORDER="0" SRC="'.$_SERVER["PHP_SELF"].'?graph=year" ALT="Year Netusage Graph"><br>
</body>
</html>';
}

?>
