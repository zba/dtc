<?php

require("/usr/share/dtc/shared/autoSQLconfig.php");
// Our main configuration file

// All shared files between DTCadmin and DTCclientM
require("$dtcshared_path/global_vars.php");

require("$dtcshared_path/lang.php");                    // Setup the $lang global variable (to en, en-us, fr, etc... : whatever is
require("deamons_state_strings.php");                 // Contain all the translated string
require("$dtcshared_path/table_names.php");

Function DateDiff ($interval, $date1,$date2) {

	// get the number of seconds between the two dates
	$timedifference =  $date2 - $date1;
	switch ($interval) {
	case "w":
		$retval  = bcdiv($timedifference ,604800);
		break;
	case "d":
		$retval  = bcdiv( $timedifference,86400);
		break;
	case "h":
		$retval = bcdiv ($timedifference,3600);
		break;
	case "n":
		$retval  = bcdiv( $timedifference,60);
		break;        
	case "s":
		$retval  = $timedifference;
		break;        

	}    
	return $retval;
    
}

function DateAdd ($interval,  $number, $date) {

	$date_time_array  = getdate($date);

	$hours =  $date_time_array["hours"];
	$minutes =  $date_time_array["minutes"];
	$seconds =  $date_time_array["seconds"];
	$month =  $date_time_array["mon"];
	$day =  $date_time_array["mday"];
	$year =  $date_time_array["year"];

	switch ($interval) {
	case "yyyy":
		$year +=$number;
		break;        
	case "q":
		$year +=($number*3);
		break;        
	case "m":
		$month +=$number;
		break;        
	case "y":
	case "d":
	case "w":
		$day+=$number;
		break;        
	case "ww":
		$day+=($number*7);
		break;        
	case "h":
		$hours+=$number;
		break;        
	case "n":
		$minutes+=$number;
		break;        
	case "s":
		$seconds+=$number;
		break;
	}    
	$timestamp =  mktime($hours ,$minutes, $seconds,$month ,$day, $year);
	return $timestamp;
}

function javascriptClock($minutes,$seconds){
	$out = '<SCRIPT language="JavaScript">
<!--
var left_minutes='.$minutes.';
var left_seconds='.$seconds.';
function JSClock() {
	left_seconds--;
	document.reloadForm.reloadField.value = left_minutes +":"+ left_seconds;
	if(left_seconds == 0){
		if(left_minutes == 0){
			document.reloadForm.reloadField.value = "NOW";
			return;
		}else{
			left_seconds=60;
			left_minutes--;
		}
	}
	id = setTimeout("JSClock()",1000);
}
//-->
</SCRIPT>
<FORM NAME="reloadForm"><INPUT TYPE="text" NAME="reloadField" SIZE="5" VALUE=""></FORM>';
	return $out;
}

function drawClock($last_cronjob_epoch){
	$next_cron = DateAdd("n",10,$last_cronjob_epoch);
	$curdate = time();
	$seconds = DateDiff ("s", $curdate, $next_cron);
	$minutes = DateDiff ("n", $curdate, $next_cron);
	$seconds = $seconds - $minutes*60;
	if($minutes < 0) $minutes = 0;
	if($seconds <= 0)$seconds = 1;
	return javascriptClock($minutes,$seconds);
}

function drawDeamonStates(){
	global $pro_mysql_cronjob_table;

	$pen = '<font color="#FF0000">PENDING</font>';
	$done = '<font color="#00FF00">OK</font>';

	// Fetch the cron_job table to see what's going on ! :)
	$query = "SELECT * FROM $pro_mysql_cronjob_table WHERE 1;";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1) die("No cronjob table row !!!");
	$row = mysql_fetch_array($result);
	$cron = $row;
	$date_last_cron = $row["last_cronjob"];

	$query = "SELECT UNIX_TIMESTAMP(last_cronjob) as epoch_time FROM $pro_mysql_cronjob_table";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$row = mysql_fetch_array($result);
	$last_cronjob_epoch .= $row[0];

	$clock = drawClock($last_cronjob_epoch);

	if($cron["restart_qmail"] == "yes")	$state_qm_restart = $pen;
	else	$state_qm_restart = $done;

	if($cron["qmail_newu"] == "yes")	$state_qm_newu = $pen;
	else	$state_qm_newu = $done;

	if($cron["gen_qmail"] == "yes")	$state_qm_genfile = $pen;
	else	$state_qm_genfile = $done;

	if($cron["restart_apache"] == "yes")	$state_apa_restart = $pen;
	else	$state_apa_restart = $done;

	if($cron["reload_named"] == "yes")	$state_bind_reload = $pen;
	else	$state_bind_reload = $done;
 
	$out = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" width=\"100%\" height=\"1\" id=\"skinSimpleGreen2Content\">
<tr>
	<td align=\"center\" width=\"16%\">Qmail restart</td>
	<td align=\"center\" width=\"16%\">Qmail-newu</td>
	<td align=\"center\" width=\"16%\">Qmail gen files</td>
	<td align=\"center\" width=\"16%\">Apache restart</td>
	<td align=\"center\" width=\"16%\">Bind reload</td>
	<td align=\"center\" width=\"17%\">Next cronjob</td>
</tr><tr>
	<td align=\"center\">$state_qm_restart</td>
	<td align=\"center\">$state_qm_newu</td>
	<td align=\"center\">$state_qm_genfile</td>
	<td align=\"center\">$state_apa_restart</td>
	<td align=\"center\">$state_bind_reload</td>
	<td align=\"center\">$clock</td>
</tr></table>";

	return $out;
}

echo "<html><head>
<META HTTP-EQUIV=\"Refresh\" CONTENT=\"15;URL=$PHP_SELF\">
<body bgcolor=\"#000000\" onLoad=\"JSClock();\">
<link rel=\"stylesheet\" href=\"gfx/dtc.css\" type=\"text/css\">
<link rel=\"stylesheet\" href=\"gfx/skin/simple/green2/skin.css\" type=\"text/css\">
".drawDeamonStates()."</body></html>";

?>