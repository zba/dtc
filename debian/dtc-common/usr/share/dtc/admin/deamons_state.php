<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

$panel_type = "none";
require("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

require_once("authme.php");

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
	$out = '<FORM NAME="reloadForm"><INPUT TYPE="text" NAME="reloadField" SIZE="5" VALUE=""></FORM>';
	return $out;
}

function drawClock($last_cronjob_epoch){
	$minute=date("i");
	$seconds=date("s");
	$minute=10-($minute%10);
	$seconds=60-$seconds;
	return javascriptClock($minute,$seconds);
}

function drawDeamonStates(){
	global $pro_mysql_cronjob_table;

	$pen = '<font color="#FF0000">' ._("Pending") .'</font>';
	$done = '<font color="#00FF00">' ._("Ok") .'</font>';

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
	$last_cronjob_epoch = $row[0];

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
 
	if($cron["gen_ssh"] == "yes")	$state_gen_ssh = $pen;
	else	$state_gen_ssh = $done;
 
	$out = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" width=\"100%\" height=\"1\" id=\"skinSimpleGreen2Content\">
<tr>
	<td align=\"center\" height=\"1\" width=\"16%\"><font color=\"#FFFFFF\">". _("Mail restart") ."</font></td>
	<td align=\"center\" width=\"14%\"><font color=\"#FFFFFF\">". _("Mail gen users") ."</font></td>
	<td align=\"center\" width=\"14%\"><font color=\"#FFFFFF\">". _("Mail gen conf") ."</font></td>
	<td align=\"center\" width=\"14%\"><font color=\"#FFFFFF\">". _("Apache restart") ."</font></td>
	<td align=\"center\" width=\"14%\"><font color=\"#FFFFFF\">". _("Bind reload") ."</font></td>
	<td align=\"center\" width=\"14%\"><font color=\"#FFFFFF\">". _("SSH gen pass") ."</font></td>
	<td align=\"center\" width=\"16%\"><font color=\"#FFFFFF\">". _("Next cronjob") ."</font></td>
</tr><tr>
	<td height=\"1\" align=\"center\">$state_qm_restart</td>
	<td align=\"center\">$state_qm_newu</td>
	<td align=\"center\">$state_qm_genfile</td>
	<td align=\"center\">$state_apa_restart</td>
	<td align=\"center\">$state_bind_reload</td>
	<td align=\"center\">$state_gen_ssh</td>
	<td align=\"center\" style=\"white-space:nowrap\" nowrap>$clock</td>
</tr></table>";

	return $out;
}

function checkSMTP(){
	global $errTxt;

	$server = "localhost";
	// echo "Checking SMTP<br>";
	if(($server_ip = gethostbynameFalse($server)) == false){
		$errTxt = _("Cannot resolve hostname:") ." ".$server." ". _("SMTP server") .".";
		return false;
	}

	$soc = fsockopen($server_ip,25,$erno,$errstring,10);
	if($soc == false){
		$errTxt = _("Could not connect to SMTP server (timed out): ") .$server;
		return false;
	}
	// echo "Checking ok after connect<br>";
	$popline = fgets($soc,1024);
	if(!strstr($popline,"220")){
		$errTxt = _("Server did not send OK after connect, wrong host or port?: ") .$popline;
		return false;
	}
	// echo "Sending login<br>";
	if(!fwrite($soc,"HELO gplhost.com\n")){
		$errTxt = _("Could not send HELO command to server");
		return false;
	}
	// echo "Checking ok after login<br>";
	$popline = fgets($soc,1024);
	if(!strstr($popline,"250")){
		$errTxt = _("Server did not send OK after HELO: ") .$popline;
		return false;
	}
	//echo "Closing socket<br>";
	fclose($soc);

	return true;
}

function checkFTP(){
	global $errTxt;
	$errTxt = "";

	global $pro_mysql_ftp_table;

	$q = "SELECT * FROM $pro_mysql_ftp_table WHERE 1 LIMIT 1";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said ".mysql_error());
	$a = mysql_fetch_array($r);

	$server = "localhost";
	// echo "Checking FTP<br>";
	if(($server_ip = gethostbynameFalse($server)) == false){
		$errTxt = _("Cannot resolve FTP server hostname: ") .$server;
		return false;
	}

	$soc = fsockopen($server_ip,21,$erno,$errstring,10);
	if($soc == false){
		$errTxt = ("Could not connect to FTP server (timed out): ") .$server;
		return false;
	}
	// echo "Checking ok after connect<br>";
	$popline = fgets($soc,1024);
	if(!strstr($popline,"220")){
		$errTxt = ("Server did not send 220 after connect, wrong host or port?: ") .$popline;
		return false;
	}
	// echo "Sending login<br>";
	if(!fwrite($soc,"USER ".$a["login"]."\n")){
		$errTxt = _("Could not send USER command to server");
		return false;
	}
	// echo "Checking ok after login<br>";
	$popline = fgets($soc,1024);
	if(!strstr($popline,"331")){
		if(!strstr($popline,"220")){
			$errTxt = _("Server did not send 331 or 220 after USER command: ") .$popline;
			return false;
		}
	}
	// echo "Sending pass<br>";
	if(!fwrite($soc,"PASS ".$a["password"]."\n")){
		$errTxt = _("Could not send PASS to server");
		return false;
	}
	// echo "Checking ok after login<br>";
	$popline = fgets($soc,1024);
	if(!strstr($popline,"230")){
		if(!strstr($popline,"220")){
			$errTxt = _("Server did not send 230 after PASS: ") .$popline." ". _("At least one ftp user must exist for this check to complete.");
			return false;
		}
	}
	//echo "Closing socket<br>";
	fclose($soc);

	return true;
}

function checkDNS(){
	global $errTxt;

	global $pro_mysql_domain_table;

	$q = "SELECT * FROM $pro_mysql_domain_table WHERE 1 LIMIT 1";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said ".mysql_error());
	$a = mysql_fetch_array($r);

	$server = $a["name"];
	// echo "Checking DNS<br>";
	if(($server_ip = gethostbynameFalse($server)) == false){
		$errTxt = "Cannot resolve host ".$server;
		return false;
	}
	$server_ip_db = $a["ip_addr"];
	if($server_ip_db != $server_ip){
		$errTxt = "$server ". _("IP Resolved") ." [$server_ip] ". _("does not match configured IP"). " [$server_ip_db]!";
		return false;
	}
	return true;
}

function checkPOP3(){
	global $pro_mysql_pop_table;

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id NOT LIKE 'cyr%' LIMIT 1";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said ".mysql_error());
	$a = mysql_fetch_array($r);

	if(checkMailbox($a["id"],$a["mbox_host"],$a["id"].'@'.$a["mbox_host"],
                                "POP3","localhost",$a["id"].'@'.$a["mbox_host"],$a["passwd"])){
		return true;
	}else{
		return false;
	}
}

function drawServerStatus(){
	global $errTxt;

	if(checkPOP3()){
		$pop3_status = '<font color="#00FF00">'. _("Running Normally") .'</font>';
	}else{
		$pop3_status = '<font color="#FF0000">'. _("ERROR") .$errTxt.'</font>';
	}

	if(checkSMTP()){
		$smtp_status = '<font color="#00FF00">'. _("Running Normally") .'</font>';
	}else{
		$smtp_status = '<font color="#FF0000">'. _("ERROR!") .$errTxt.'</font>';
	}

	if(checkDNS()){
		$dns_status = '<font color="#00FF00">'. _("Running Normally") .'</font>';
	}else{
		$dns_status = '<font color="#FF0000">'. _("ERROR!") .$errTxt.'</font>';
	}

	if(checkFTP()){
		$ftp_status = '<font color="#00FF00">'. _("Running Normally") .'</font>';
	}else{
		$ftp_status = '<font color="#FF0000">'. _("ERROR!") .$errTxt.'</font>';
	}

	$out = "<br><table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"1\">
<tr>
	<td width=\"25%\" align=\"center\"><font color=\"#FFFFFF\">SMTP</font></td>
	<td width=\"25%\" align=\"center\"><font color=\"#FFFFFF\">POP3</font></td>
	<td width=\"25%\" align=\"center\"><font color=\"#FFFFFF\">DNS</font></td>
	<td width=\"25%\" align=\"center\"><font color=\"#FFFFFF\">FTP</font></td>
</tr><tr>
	<td align=\"center\">$smtp_status</td>
	<td align=\"center\">$pop3_status</td>
	<td align=\"center\">$dns_status</td>
	<td align=\"center\">$ftp_status</td>
</tr></table>";
	return $out;
}

echo "<html><head>
<META HTTP-EQUIV=\"Refresh\" CONTENT=\"15;URL=?\">".'<SCRIPT language="JavaScript">
<!--
var left_minutes='.(9-(date("i") % 10)).';
var left_seconds='.(60-date("s")).';
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
</SCRIPT>'."
<body bgcolor=\"#000000\" onLoad=\"JSClock();\" text=\"#FFFFFF\">
<link rel=\"stylesheet\" href=\"gfx/dtc.css\" type=\"text/css\">
<link rel=\"stylesheet\" href=\"gfx/skin/".$conf_skin."/skin.css\" type=\"text/css\">
".drawDeamonStates().drawServerStatus().$errTxt."</body></html>";

?>
