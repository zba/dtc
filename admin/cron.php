<?php

$script_start_time = time();
$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

require_once("genfiles/genfiles.php");

$keep_mail_generate_flag = "no";
$keep_dns_generate_flag = "no";

// Set here your apachectl path if you need it fully (like for example
// /usr/sbin/apachectl for debian, or /usr/local/sbin/apachectl for FreeBSD)
if($conf_unix_type == "debian" || $conf_unix_type == "redhat" || $conf_unix_type == "osx" || $conf_unix_type == "gentoo"){
	if(file_exists("/usr/sbin/apachectl")){
		$APACHECTL = "/usr/sbin/apachectl";
	}else if(file_exists("/usr/sbin/apachectl2")){
		$APACHECTL = "/usr/sbin/apachectl2";
	// Those are in case you compile apache yourself...
	}else if(file_exists("/usr/local/sbin/apachectl")){
		$APACHECTL = "/usr/local/sbin/apachectl";
	}else if(file_exists("/usr/local/sbin/apachectl2")){
		$APACHECTL = "/usr/local/sbin/apachectl2";
	}else if(file_exists("/usr/sbin/apache2ctl")){
		$APACHECTL = "/usr/sbin/apache2ctl";
	}
// This should be the FreeBSD case
}else{
	if(file_exists("/usr/local/sbin/apachectl")){
		$APACHECTL = "/usr/local/sbin/apachectl";
	}else if(file_exists("/usr/local/sbin/apachectl2")){
		$APACHECTL = "/usr/local/sbin/apachectl2";
	}
}

if(!isset($APACHECTL) || !file_exists($APACHECTL)){
	echo "FATAL ERROR: APACHECTL NOT FOUND, DTC CAN'T RELOAD APACHE !!!\n";
	die();
}

// Set to yes if you want to check for qmail pop3d availability and relaunch
// it if needed. Note that you can have to customise that cron script part
// for your qmail installation. This is done for debian standard start script.
$CHECK_QMAIL_POP3D = "no";

echo date("Y m d / H:i:s T",$script_start_time)." Starting DTC cron job\n";
// Let's see if DTC's mysql_config.php is OK and lock back the shared folder
// and mysql_config.php to root:root
if($conf_mysql_conf_ok=="yes" && $conf_demo_version  == "no"){
	exec("chown root:$conf_nobody_group_name $dtcshared_path");
	exec("chown root:$conf_nobody_group_name $dtcshared_path/mysql_config.php");
}

$query = "SELECT * FROM $pro_mysql_cronjob_table WHERE 1 LIMIT 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
$num_rows = mysql_num_rows($result);
if($num_rows != 1)	die("No data in the cronjob table !!!");
$cronjob_table_content = mysql_fetch_array($result);


// Lock the cron flag, in case the cron script takes more than 10 minutes
if($cronjob_table_content["lock_flag"] != "finished"){
	echo "DB flag says that last cron job is not finished: exiting.\n
If no cronjob is running, then please please type:\n
mysql -uroot -Ddtc -p --execute=\"UPDATE $pro_mysql_cronjob_table SET lock_flag='finished';\"\n";
	die("Exiting NOW!");
}
echo "Setting-up lock flag\n";
$query = "UPDATE $pro_mysql_cronjob_table SET lock_flag='inprogress' WHERE 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());

// This function call the apropriate server to tell that this server may have
// some change in his domain list
function commitTriggerToRemote($a)
{
	commitTriggerToRemote($a, 0);
}


// This function call the apropriate server to tell that this server may have
// some change in his domain list
// $recipients 1 - just mx recipient list
// $recipients 0 - everything
function commitTriggerToRemoteInternal($a, $recipients)
{
	$flag = false;
	$retry = 0;
	if ($recipients == 1){
		$url = $a["server_addr"].'/dtc/list_domains.php?action=trigger_update_mx_recipients&login='.$a["server_login"].'&pass='.$a["server_pass"];
	} else {
		$url = $a["server_addr"].'/dtc/list_domains.php?action=update_request&login='.$a["server_login"].'&pass='.$a["server_pass"];
	}
        while($retry < 3 && $flag == false){
		$a_vers = explode(".",phpversion());
		if(strncmp("https://",$a["server_addr"],strlen("https://")) == 0 && $a_vers[0] <= 4 && $a_vers[1] < 3){
			echo "using lynx -source...";
			$result = exec("lynx -source \"$url\"",$lines,$return_val);
		}else{
			echo "using file()...";
			$lines = file ($url);
		}
		$nline = sizeof($lines);
		if ($recipients == 1){
			if(strstr($lines[0],"Successfuly recieved trigger for MX!") != false){
				$flag = true;
			}

		} else {
			if(strstr($lines[0],"Successfuly recieved trigger!") != false){
				$flag = true;
			}
		}
		$retry ++;
		if($flag == false)      sleep(3);
	}
	return $flag;
}


//$recipients 0 = trigger all changes
//$recipients 1 = trigger only MX changes
//returns 1 if the trigger was done
//returns 0 if the trigger wasn't done
function checkTriggers($recipients)
{
	global $pro_mysql_backup_table;
	$returnValue = 0;
	if ($recipients == 0){
		$query = "SELECT * FROM $pro_mysql_backup_table WHERE type='trigger_changes' AND status='pending';";
	} else {
		$query = "SELECT * FROM $pro_mysql_backup_table WHERE type='trigger_mx_changes' AND status='pending';";
	}
	$r = mysql_query($query)or die("Cannot query \"$query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		echo "Triggering the change to the backup server ".$a["server_addr"]." with login ".$a["server_login"]."...";
		if(commitTriggerToRemoteInternal($a,$recipients)){
			$q2 = "UPDATE $pro_mysql_backup_table SET status='done' WHERE id='".$a["id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			echo "success!\n";
			$returnValue = 1;
		}else{
			echo "failed!\n";
			$returnValue = 0;
		}
	}
	return $returnValue;
}


//first check to see if any domains OR MX have changed
$trigger_update_done = checkTriggers(0);

//if we haven't done a full update, see if we need to do just a mx update
if ($trigger_update_done == 0)
{
	checkTriggers(1);
}

$start_stamps = mktime();
////////////////////////////////////////////////////////////
// First find if it's time for long statistic generation. //
// Do it all the time, for (debuging) the moment... :)    //
////////////////////////////////////////////////////////////
function updateAllDomainsStats(){
	global $pro_mysql_admin_table;
	global $pro_mysql_domain_table;
	global $conf_unix_type;

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE 1;";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error()." in file ".__FILE__." line ".__LINE__);
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$admin = mysql_fetch_array($result);
		$adm_login = $admin["adm_login"];
		echo "===> Updating statistic for user $adm_login\n";
		$adm_path = $admin["path"];
		$q = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
		$r = mysql_query($q)or die("Cannot query \"$q\" !!!".mysql_error()." in file ".__FILE__." line ".__LINE__);
		$n = mysql_num_rows($r);
		for($j=0;$j<$n;$j++){
			$ar = mysql_fetch_array($r);
			$domain_name = $ar["name"];
			echo "Calculating usage of $domain_name:";
			echo " disk...";
			if($conf_unix_type == "bsd"){
				$du_string = exec("du -sk $adm_path/$domain_name --exclude=access.log",$retval);
				$du_state = explode("\t",$du_string);
				$domain_du = $du_state[0] * 1024;
			}else{
				$du_string = exec("du -sb $adm_path/$domain_name --exclude=access.log",$retval);
				$du_state = explode("\t",$du_string);
				$domain_du = $du_state[0];
			}
			$q2 = "UPDATE $pro_mysql_domain_table SET du_stat='$domain_du' WHERE name='$domain_name';";
			mysql_query($q2)or die("Cannot query \"$q2\" !!!".mysql_error()." in file ".__FILE__." line ".__LINE__);
// Not needed as we do those realtime now ! :)
//			echo "email...";
//			sum_email($domain_name);
			echo "ftp...";
			sum_ftp($domain_name);
//			echo "http...";
//			sum_http($domain_name);
			echo "done!\n";
		}
	}
}

function updateAllListWebArchive(){
	global $pro_mysql_list_table;
	global $pro_mysql_domain_table;

	$query = "SELECT * FROM $pro_mysql_list_table WHERE webarchive='yes'";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$number = mysql_num_rows($result);
//	$row = mysql_fetch_array($result);
	echo "Update of $number webarchive:\n";
	for($j=0;$j<$number;$j++){
		$row = mysql_fetch_array($result);
		$list_domain = $row["domain"];
		$list_name = $row["name"];
		$query2 = "SELECT owner FROM $pro_mysql_domain_table WHERE name='$list_domain' LIMIT 1";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
		$row2 = mysql_fetch_array($result2);
		$list_admin = $row2[0];
		echo "...webarchive updating of $list_name on $list_domain\n";
		$admin_path = getAdminPath($list_admin);
		$list_dir = $admin_path."/".$list_domain."/lists/".$list_domain."_".$list_name;
		$web_path = $admin_path."/".$list_domain."/subdomains/www/html/lists/".$list_name;
		$archive_dir = $list_dir."/archive";
		$list_rcfile = $list_dir."/rcfile";
		if (file_exists($list_rcfile)){
			$rcfile = " -rcfile ".$list_rcfile." ";
		}else{
			$rcfile = " ";
		}
		if($row["spammode"] == "yes"){
			$spammode = " -spammode ";
		}else{
			$spammode = " -nospammode ";
		}
		$updatewa = "mhonarc".$rcfile."-outdir ".$web_path." -add ".$spammode.$archive_dir;
		exec($updatewa);
	}
}

// This will set each day at 0:00
if(($start_stamps%(60*60*24))< 60*10)	updateAllDomainsStats();
// This one is each hours
// if(($start_stamps%(60*60))< 60*10){	updateAllDomainsStats();	}
// This is each time the script is launched (all 10 minutes)
// updateAllDomainsStats();

// Update all list archives
if(($start_stamps%(60*60))< 60*10){	updateAllListWebArchive();	}

// Re-read cronjob values as long as they could have change
// during this long job calculation !
$query = "SELECT * FROM $pro_mysql_cronjob_table WHERE 1 LIMIT 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
$num_rows = mysql_num_rows($result);
if($num_rows != 1)	die("No data in the cronjob table !!!");
$cronjob_table_content = mysql_fetch_array($result);



///////////////////////////////////////////////////////
// First, see if we have to regenerate deamons files //
///////////////////////////////////////////////////////
if($cronjob_table_content["gen_vhosts"] == "yes"){
	echo "Generating Apache vhosts\n";
	pro_vhost_generate();
}
if($cronjob_table_content["gen_named"] == "yes"){
	echo "Generating Named zonefile\n";
	named_generate();
}
if($cronjob_table_content["gen_qmail"] == "yes"){
	echo "Generating mail accounts\n";
	mail_account_generate();
}
if($cronjob_table_content["gen_backup"] == "yes"){
	echo "Generating backup script\n";
	backup_script_generate();
}
if($cronjob_table_content["gen_webalizer"] == "yes"){
	echo "Generating Webalizer stats script\n";
	stat_script_generate();
}
if($cronjob_table_content["gen_ssh"] == "yes"){
	echo "Generating SSH accounts\n";
	ssh_account_generate();
}

///////////////////////////////////////////////////////////////////////////////////////
// This script should be launched as root, so we have to chown the generated files ! //
// (otherwise, the web interface wont be able to write them)                         //
///////////////////////////////////////////////////////////////////////////////////////
system("chown -R $conf_dtc_system_username:$conf_nobody_group_id $conf_generated_file_path");
system("./checkbind.sh $conf_generated_file_path");
//system("chmod -R 777 $conf_generated_file_path/zones");

if($cronjob_table_content["qmail_newu"] == "yes"){
	echo "Starting qmail-newu\n";
	switch($conf_mta_type){
	case "qmail":
		system("/var/qmail/bin/qmail-newu");
		break;
	case "postfix":
		//not sure what newu equiv in postfix
		break;
	}
}

if($cronjob_table_content["restart_qmail"] == "yes"){
	switch($conf_mta_type){
	case "postfix":
		echo "Reloading postfix\n";
		system("/etc/init.d/postfix reload");
		echo "Reloading amavis\n";
		if( file_exists ("/etc/init.d/amavis") ){
			system("/etc/init.d/amavis force-reload");
		}else if( file_exists ("/etc/init.d/amavisd") ){
			// Seems a restart is best (gentoo needs it)
			system("/etc/init.d/amavisd restart");
		}
		break;
	case "qmail":
	default:
		echo "Sending qmail-send a HUP\n";	// This runs well on stock debian woody qmail-src package. Anyone had trouble with it ?
		system("killall -HUP qmail-send");
//		echo "Restarting qmail\n";		// I've read somewhere it cannot reload correcly to do qmail-send a HUP, but I've tested and it does ! :)
//		system("/etc/init.d/qmail stop");
//		sleep(2);
//		system("/etc/init.d/qmail start");
		break;
	}
}

// Check if pop is running, restart qmail if not
if($conf_mta_type == "qmail" && $CHECK_QMAIL_POP3D == "yes"){
	$fp = fsockopen ($conf_addr_mail_server, 110, $errno, $errstr, 30);
	if(!fp){
		echo "$errno/$errstr: POP3 is not running ! Restarting qmail !!!\n";
		system("/etc/init.d/qmail stop");
		sleep(2);
		system("/etc/init.d/qmail start");
	}else{
		fclose ($fp);
	}

}

if($cronjob_table_content["reload_named"] == "yes"){
	echo "Reloading name-server\n";
	system("killall -HUP named");
}

if($cronjob_table_content["restart_apache"] == "yes"){
	$plop = array();
	// Now this echo is in the script itself!
	//echo ("Testing and creating directories for vhosts...\n");
	system("chmod +x \"$conf_generated_file_path/vhost_check_dir\"");
	system("$conf_generated_file_path/vhost_check_dir");
	echo "Testing apache conf\n";
	exec ("$APACHECTL configtest", $plop, $return_var);
	if($return_var == false){
		echo "Config is OK : restarting Apache\n";
		$pid_file_was_there = is_file("$conf_generated_file_path/apache.pid");
		clearstatcache();
		system("$APACHECTL stop");

		if ($pid_file_was_there)
		{
			echo "PidFile existed, so will wait until it's gone before restarting...\n";
		}
		// DISABLE PID detection for now:
		$pid_file_was_there = false;
		// only wait if the pid file is still around... don't want to sleep if it has already shut down...
		if (!$pid_file_was_there || is_file("$conf_generated_file_path/apache.pid"))
		{
			echo "Waiting 4... ";	// With 800 domains, 5 SSL sites on a celeron 800 and fast hard drives, 5 seconds is just the right value...
			sleep(1);
			clearstatcache();
		}
		if (!$pid_file_was_there || is_file("$conf_generated_file_path/apache.pid"))
                {
			echo "3... ";
			sleep(1);
			clearstatcache();
		}
		if (!$pid_file_was_there || is_file("$conf_generated_file_path/apache.pid"))
                {
			echo "2... ";
			sleep(1);
			clearstatcache();
		}
		if (!$pid_file_was_there || is_file("$conf_generated_file_path/apache.pid"))
                {
			echo "1... ";
			sleep(1);
			clearstatcache();
		}
		echo "0\n";
		$ctl_retry = 0;		// We have to continue going on, even if apache don't restart...
		$ctl_return = system("$APACHECTL start");
		// Check that apache is really started, because experience showed sometimes it's not !!!
		//while( strstr($ctl_return,"httpd started") == false && $ctl_retry++ < 15){
		// This new version should work on OS where apachectl start is quiet
		while( strstr($ctl_return,"httpd started") == false && $ctl_retry++ < 15 && !empty($ctl_return)){
			echo "Warning: apache not started, will retry in 3 seconds...\n";
			sleep(3);
			$ctl_return = system("$APACHECTL start", $return_var);
		}
		// change to graceful apache restart, rather than a hard stop and start
		// WARNING !!! Experience showed that it doesn't work sometimes !!!
		//system("$APACHECTL graceful");
	}else{
		echo "Config not OK : I can't reload apache !!!\n";
	}
}

// If 00:00 and check the frequency of the bacup and launch it if needed
if(($start_stamps%(60*60*24))< 60*10 && $conf_ftp_backup_activate == "yes"){
	$do_ftp_backup = "no";
	switch($conf_ftp_backup_frequency){
	case "day":
		$do_ftp_backup = "yes";
		break;
	case "week":
		if(date("N",$start_stamps) == "1"){
			$do_ftp_backup = "yes";
		}
		break;
	case "month":
		if(date("j",$start_stamps) == "1"){
			$do_ftp_backup = "yes";
		}
		break;
	default:
		break;
	}
	if($do_ftp_backup == "yes"){
		echo "Launching ftp backup script !\n";
		system("$conf_generated_file_path/net_backup.sh &");
	}
}

$exec_time = time() - $script_start_time;
echo "Resetting all cron flags\n";
$to_reset = "";
if($keep_mail_generate_flag == "no"){
	$to_reset .= " restart_qmail='no', gen_qmail='no', ";
}
if($keep_dns_generate_flag == "no"){
	$to_reset .= " gen_named='no', reload_named='no', ";
}

$query = "UPDATE cron_job SET lock_flag='finished', last_cronjob=NOW(), $to_reset  qmail_newu='no', restart_apache='no', gen_vhosts='no', gen_webalizer='no', gen_backup='no', gen_ssh='no' WHERE 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
if($exec_time > 60){
	$ex_sec = $exec_time % 60;
	$ex_min = round($exec_time / 60);
}else{
	$ex_sec = $exec_time;
	$ex_min = 0;
}
echo date("Y m d / H:i:s T")." DTC cron job finished (exec time=".$ex_min.":".$ex_sec.")\n\n";
exit();

?>
