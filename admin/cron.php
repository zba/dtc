#!/usr/bin/env php
<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

// 5 minute timeout for the cron.php run... if it runs longer, then we need to know about it!
$_timelimit = 300;
set_time_limit($_timelimit); 

// keep track whether we have finished script execution or not
$_inprogress = TRUE;
register_shutdown_function('clean_shutdown_cron');

function clean_shutdown_cron(){
	global $_inprogress;
	global $_timelimit;
	if ($_inprogress){
		echo "WARNING: cron.php execution took longer than $_timelimit seconds\n";
		printEndTime ();
	}
}


$script_start_time = time();
$start_stamps = gmmktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

require_once("genfiles/genfiles.php");

echo date("Y m d / H:i:s T",$script_start_time)." Starting DTC cron job\n";
$keep_mail_generate_flag = "no";
$keep_dns_generate_flag = "no";

// Set to yes if you want to check for qmail pop3d availability and relaunch
// it if needed. Note that you can have to customise that cron script part
// for your qmail installation. This is done for debian standard start script.
$CHECK_QMAIL_POP3D = "no";

function markCronflagOk ($flag) {
	$query = "UPDATE cron_job SET $flag  WHERE 1;";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
}

function searchApachectl () {
	global $conf_apache_version;
	// Set here your apachectl path if you need it fully (like for example
	// /usr/sbin/apachectl for debian, or /usr/local/sbin/apachectl for FreeBSD)
	if($conf_apache_version == "2"){
		if(file_exists("/usr/sbin/apache2ctl")){
			$APACHECTL = "/usr/sbin/apache2ctl";
		}else if(file_exists("/usr/local/sbin/apache2ctl")){
			$APACHECTL = "/usr/local/sbin/apache2ctl";
		}else if(file_exists("/usr/sbin/apachectl2")){
			$APACHECTL = "/usr/sbin/apachectl2";
		}else if(file_exists("/usr/local/sbin/apachectl2")){
			$APACHECTL = "/usr/local/sbin/apachectl2";
		}else if(file_exists("/usr/local/sbin/apachectl")){
                	 $APACHECTL = "/usr/local/sbin/apachectl";
		}else if(file_exists("/usr/sbin/apachectl")){
                	 $APACHECTL = "/usr/sbin/apachectl";
		}
	}else{
		if(file_exists("/usr/sbin/apachectl")){
			$APACHECTL = "/usr/sbin/apachectl";
		}else if(file_exists("/usr/local/sbin/apachectl")){
			$APACHECTL = "/usr/local/sbin/apachectl";
		}
	}
	if(!isset($APACHECTL) || !file_exists($APACHECTL)){
		echo "FATAL ERROR: APACHECTL NOT FOUND, DTC CAN'T RELOAD APACHE !!!\n";
		die();
	}
	return $APACHECTL;
}

function getCronFlags () {
	global $pro_mysql_cronjob_table;
	$query = "SELECT * FROM $pro_mysql_cronjob_table WHERE 1 LIMIT 1;";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("No data in the cronjob table !!!");
	$cronjob_table_content = mysql_fetch_array($result);
	return $cronjob_table_content;
}

function checkLockFlag () {
	global $cronjob_table_content;
	global $pro_mysql_cronjob_table;

	$cronjob_table_content = getCronFlags();
	// Lock the cron flag, in case the cron script takes more than 10 minutes
	if($cronjob_table_content["lock_flag"] != "finished"){
		echo "DB flag says that last cron job is not finished: exiting.\n
If no cronjob is running, then please please type:\n
mysql -uroot -Ddtc -p --execute=\"UPDATE $pro_mysql_cronjob_table SET lock_flag='finished';\"\n
If runing Debian, you can directly run:\n
mysql --defaults-file=/etc/mysql/debian.cnf -Ddtc --execute=\"UPDATE cron_job SET lock_flag='finished';\"\n
";
		die("Exiting NOW!");
	}
	echo "Setting-up lock flag\n";
	$query = "UPDATE $pro_mysql_cronjob_table SET lock_flag='inprogress' WHERE 1;";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
}

function resetLockFlag () {
	global $pro_mysql_cronjob_table;
	$query = "UPDATE $pro_mysql_cronjob_table SET lock_flag='finished' WHERE 1;";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
}

// This function call the apropriate server to tell that this server may have
// some change in his domain list
function commitTriggerToRemote($a){
	commitTriggerToRemote($a, 0);
}


// This function call the apropriate server to tell that this server may have
// some change in his domain list
// $recipients 1 - just mx recipient list
// $recipients 0 - everything
function commitTriggerToRemoteInternal($a, $recipients){
	$flag = false;
	$retry = 0;
	if ($recipients == 1){
		$url = $a["server_addr"].'/dtc/list_domains.php?action=trigger_update_mx_recipients&login='.$a["server_login"].'&pass='.$a["server_pass"];
	} else {
		$url = $a["server_addr"].'/dtc/list_domains.php?action=update_request&login='.$a["server_login"].'&pass='.$a["server_pass"];
	}
        while($retry < 3 && $flag == false){
		$a_vers = explode(".",phpversion());
		
		if($a_vers[0] <= 4 && $a_vers[1] < 3){
			echo "using lynx -source...";
			$result = exec("lynx -source \"$url\"",$lines,$return_val);
		}else{
			$httprequest = new dtc_HTTPRequest("$url");
			$lines = $httprequest->DownloadToStringArray();
		}
		if($lines != FALSE){
			if ($recipients == 1){
				if(strstr($lines[0],"Successfuly recieved trigger for MX!") != false){
					$flag = true;
				}
			} else {
				if(strstr($lines[0],"Successfuly recieved trigger!") != false){
					$flag = true;
				}
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
function checkTriggers($recipients){
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

////////////////////////////////////////////////////////////
// First find if it's time for long statistic generation. //
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
				$du_string = exec("du -sk -I access.log $adm_path/$domain_name",$retval);
				$du_state = explode("\t",$du_string);
				$domain_du = $du_state[0] * 1024;
			}else{
				$du_string = exec("du -sb $adm_path/$domain_name --exclude=access.log --exclude=subdomains.aufs",$retval);
				$du_state = explode("\t",$du_string);
				$domain_du = $du_state[0];
			}
			$q2 = "UPDATE $pro_mysql_domain_table SET du_stat='$domain_du' WHERE name='$domain_name';";
			mysql_query($q2)or die("Cannot query \"$q2\" !!!".mysql_error()." in file ".__FILE__." line ".__LINE__);
			echo "ftp...";
			sum_ftp($domain_name);
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
		$list_admin = $row2["owner"];
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

function get_apache_pid() {
	global $conf_generated_file_path;
	$pid = @file_get_contents("$conf_generated_file_path/apache.pid");
	if ($pid) { return (int) $pid; }
}

if( !function_exists("posix_kill")){
	function posix_kill($pid, $sig=1){
		system('kill -'.$sig.' '. $pid, $st); 
		return !$st;
	}
}

function restartApache () {
	global $conf_generated_file_path;
	global $conf_dtc_system_username;
	global $conf_dtc_system_groupname;
	global $dtcshared_path;

	$APACHECTL = searchApachectl();
	$plop = array();
	// Now this echo is in the script itself!
	system("chmod +x \"$conf_generated_file_path/vhost_check_dir\"");
	system("chown $conf_dtc_system_username \"$conf_generated_file_path/vhost_check_dir\"");
	system("$conf_generated_file_path/vhost_check_dir");
	system("$dtcshared_path/../admin/remount_aufs");
	echo "Checking SSL certificates...";
	system("chown $conf_dtc_system_username \"$conf_generated_file_path/vhost_check_ssl_cert\"");
	$return_string = exec("$conf_generated_file_path/vhost_check_ssl_cert",$output,$return_val);
	if($return_val == 0){
		echo "ok!\n";
        }else{
		echo "problem with a certificate, please start $conf_generated_file_path/vhost_check_ssl_cert manualy\n";
		echo "Will not restart apache!\n";
		return;
        }
	echo "Testing apache conf\n";
	exec ("$APACHECTL configtest 2>&1 | grep -qF 'Syntax OK'", $plop, $return_var);
	if($return_var == false){
// 		this code works fine without interrupting the service.  it might take a while until all apache processes have the new configuration (config reload for each apache process waits until the request they are serving is finished), but what we care is that the NEW clients connecting will have it, and that is true in this case
// 		root@xen011106:~# kill -HUP 29153
// 		root@xen011106:~# [Sun Aug 31 12:44:53 2008] [notice] SIGHUP received.  Attempting to restart
// 		[Sun Aug 31 12:44:53 2008] [notice] Apache/2.2.3 (Debian) PHP/5.2.0-8+etch11 configured -- resuming normal operations
		$pid = get_apache_pid();
		if ($pid) {
			echo "Apache PID: $pid\n";
		 	$ret = posix_kill($pid,1);
			if ($ret) print "Apache successfully SIGHUPped -- configuration should have been reloaded\n";
		}
		else { $ret = FALSE; /* so brutal restart takes place */ }
		if ($ret === FALSE) {
			echo "Apache is not running -- switching to restart mode\n";
			$pid = get_apache_pid();
			if ($pid && posix_kill($pid,0)) {
				/* apache is running, we stop it */
				$ret = system("$APACHECTL stop");
				if ($ret != 0) { echo "$APACHECTL stop failed with return status $ret\n"; }
				/* wait 20 seconds or until it is down */
				for ($m = 0; $m < 80 && posix_kill($pid,0); $m++) { usleep(250000); }
				if (posix_kill($pid,0)) { echo "error: apache still running with PID $pid\n"; }
			}
			/* we now start Apache */
			for ($x = 0; $x < 10; $x++) {
				$ret = system("$APACHECTL start");
				if ($ret != 0) echo "$APACHECTL start failed with return status $ret\n";
				$suc = 0;
				for ($m = 0; $m < 80; $m++) { /* wait 20 seconds or until it is up */
					$pid = get_apache_pid();
					if (($pid) && posix_kill($pid,0)) { /* apache is now running */
						echo "Apache restarted\n";
						$suc = 1; break;
					}
					else { usleep(250000); }
				}
				if ($suc == 1) { break; }
				$pid = get_apache_pid();
				if (!$pid || !posix_kill($pid,0)) { echo "error: apache never started\n"; }
			}
		}
	}else{
		echo "Config not OK - apache can't be restarted\n";
	}
}

function checkPop3dStarted () {
	global $conf_mta_type;
	global $CHECK_QMAIL_POP3D;
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
}

function checkTimeAndLaunchNetBackupScript () {
	global $start_stamps;
	global $conf_ftp_backup_activate;
	global $conf_ftp_backup_frequency;
	global $conf_generated_file_path;
	if(($start_stamps%(60*60*24))< 60*10 && $conf_ftp_backup_activate == "yes"){	// If 00:00 and check the frequency of the bacup and launch it if needed
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
}

function cronMailSystem () {
	global $keep_mail_generate_flag;
	global $conf_mta_type;
	global $conf_dtc_system_username;
	global $conf_generated_file_path;
	global $conf_unix_type;

	$cronjob_table_content = getCronFlags();
	if($cronjob_table_content["gen_qmail"] == "yes"){
		echo "Generating mail accounts\n";
		mail_account_generate();
		if($keep_mail_generate_flag == "no"){
			markCronflagOk ("gen_qmail='no'");
		}
		if( file_exists($conf_generated_file_path."/postfix_virtual_mailbox_domains") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/postfix_virtual_mailbox_domains");
		}
		if( file_exists($conf_generated_file_path."/local_domains") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/local_domains");
		}
		if( file_exists($conf_generated_file_path."/postfix_virtual") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/postfix_virtual");
		}
		if( file_exists($conf_generated_file_path."/postfix_aliases") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/postfix_aliases");
		}
		if( file_exists($conf_generated_file_path."/postfix_vmailbox") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/postfix_vmailbox");
		}
		if( file_exists($conf_generated_file_path."/postfix_virtual_uid_mapping") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/postfix_virtual_uid_mapping");
		}
		if( file_exists($conf_generated_file_path."/postfix_relay_domains") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/postfix_relay_domains");
		}
		if( file_exists($conf_generated_file_path."/postfix_relay_recipients") ){
			system("chown $conf_dtc_system_username $conf_generated_file_path/postfix_relay_recipients");
		}
	}
	/* Didnt want it to put to seperate function, because I think its fit well in mail system's cronjob */
	if ($cronjob_table_content["gen_fetchmail"] == "yes") {
	    echo "Generating fetchmailrc";
	    fetchmail_generate();
	}
	
	if($cronjob_table_content["qmail_newu"] == "yes"){
		echo "Starting qmail-newu\n";
		switch($conf_mta_type){
		case "qmail":
			//system("/var/qmail/bin/qmail-newu");
			system("$conf_qmail_newu_path");
			break;
		case "postfix":
			//not sure what newu equiv in postfix
			break;
		}
		markCronflagOk ("qmail_newu='no'");
	}
	if($cronjob_table_content["restart_qmail"] == "yes"){
		switch($conf_mta_type){
		case "postfix":
			echo "Reloading postfix\n";
			if( file_exists("/etc/init.d/postfix")){
				$PATH_POSTFIX_SCRIPT = "/etc/init.d/postfix";
			}else if( file_exists("/usr/local/etc/rc.d/postfix")){
				$PATH_POSTFIX_SCRIPT = "/usr/local/etc/rc.d/postfix";
			}else if( file_exists("/etc/rc.d/rc.postfix")){
				$PATH_POSTFIX_SCRIPT = "/etc/rc.d/rc.postfix";
			}
			if($conf_unix_type == "gentoo" && file_exists("/etc/init.d/postfix")) {
				$PATH_POSTFIX_SCRIPT = "/usr/sbin/postfix";
			}
			if ($conf_unix_type == "bsd" && file_exists("/usr/local/etc/rc.d/postfix")) {
				$PATH_POSTFIX_SCRIPT = "/usr/local/sbin/postfix";
			}
			system("$PATH_POSTFIX_SCRIPT reload");

			$PATH_POSTSUPER = "/usr/sbin/postsuper";
                        if( file_exists("/usr/sbin/postsuper")){
                                $PATH_POSTSUPER = "/usr/sbin/postsuper";
                        }else if( file_exists("/usr/local/sbin/postsuper")){
                                $PATH_POSTSUPER = "/usr/local/sbin/postsuper";
                        }else if( file_exists("/usr/bin/postsuper")){
                                $PATH_POSTSUPER = "/usr/bin/postsuper";
                        }

                        // first stop queue processing in postfix
                        echo "Stopping postfix queue...\n";
                        system("$PATH_POSTSUPER -h ALL 2>&1");

			echo "Reloading amavis\n";
			if( file_exists ("/etc/init.d/amavis") ){
				system("/etc/init.d/amavis force-reload");
			}else if( file_exists ("/etc/init.d/amavisd") ){
				// Seems a restart is best (gentoo needs it)
				system("/etc/init.d/amavisd restart");
			// This one seems ok for slackware
			}else if(file_exists("/etc/rc.d/rc.amavisd")){
				system("/etc/rc.d/rc.amavisd restart");
			// This is for FreeBSD
			}else if(file_exists("/usr/local/etc/rc.d/amavisd")){
				system("/usr/local/etc/rc.d/amavisd restart");
			}

			if( file_exists ("/etc/init.d/dkimproxy") ){
				echo "Reloading dkfilter to reload it's domains...\n";
				system("/etc/init.d/dkimproxy stop");
				sleep(1);
				system("/etc/init.d/dkimproxy start");
			}
			
			echo "Starting postfix queue...\n";
                        system("$PATH_POSTSUPER -H ALL 2>&1");

			echo "Flushing the queue now, to make sure we have some mail delivery happening after amavisd restart...\n";
                        system("$PATH_POSTFIX_SCRIPT flush");

			break;
		case "qmail":
		default:
			echo "Sending qmail-send a HUP\n";	// This runs well on stock debian woody qmail-src package. Anyone had trouble with it ?
			system("killall -HUP qmail-send");
			break;
		}
		if($keep_mail_generate_flag == "no"){
			markCronflagOk ("restart_qmail='no'");
		}
	}
}
function checkOtherServerTriggers () {
	//first check to see if any domains OR MX have changed
	$trigger_update_done = checkTriggers(0);

	//if we haven't done a full update, see if we need to do just a mx update
	if ($trigger_update_done == 0){
		checkTriggers(1);
	}
}

function checkNamedCronService () {
	global $conf_generated_file_path;
	global $conf_dtc_system_username;
	global $conf_dtc_system_groupname;
	global $keep_dns_generate_flag;
	$cronjob_table_content = getCronFlags();
	///////////////////////////////////////////////////////
	// First, see if we have to regenerate deamons files //
	///////////////////////////////////////////////////////
	if($cronjob_table_content["gen_named"] == "yes" || $cronjob_table_content["gen_reverse"] == "yes" ){
		echo "Generating Named zonefile ... this may take a while\n";
		named_generate();
		system("chgrp $conf_dtc_system_groupname $conf_generated_file_path/named.conf $conf_generated_file_path/named.slavezones.conf");
		system("chmod 770 $conf_generated_file_path/named.conf $conf_generated_file_path/named.slavezones.conf");
		system("chgrp -R $conf_dtc_system_groupname \"$conf_generated_file_path/zones\"");
		system("chmod 770 \"$conf_generated_file_path/zones\"");
		system("./checkbind.sh $conf_generated_file_path");
		if($keep_dns_generate_flag == "no"){
			markCronflagOk ("gen_named='no'");
		}
	}
	if($cronjob_table_content["reload_named"] == "yes"){
		echo "Reloading name-server\n";
		if (system("pgrep named") != ""){
			if(file_exists("/usr/sbin/rndc")){
				system("/usr/sbin/rndc reload");
			}else{
				system("killall -HUP named");
			}
			if($keep_dns_generate_flag == "no"){
				markCronflagOk ("reload_named='no'");
			}
		}else{
			echo "named NOT RUNNING\n";
			if (system("uname -s") == "FreeBSD" ){
				system("/etc/rc.d/named start");
			}
		}
	}
}

function checkSSHCronService () {
	$cronjob_table_content = getCronFlags();
	if($cronjob_table_content["gen_ssh"] == "yes"){
		echo "Generating SSH accounts\n";
		ssh_account_generate();
		markCronflagOk ("gen_ssh='no'");
	}
}

function checkApacheCronService () {
	global $conf_dtc_system_groupname;
	global $conf_generated_file_path;

	$cronjob_table_content = getCronFlags();
	if($cronjob_table_content["gen_vhosts"] == "yes"){
		echo "Generating Apache vhosts\n";
		pro_vhost_generate();
		system("chgrp $conf_dtc_system_groupname \"$conf_generated_file_path/vhosts.conf\"");
		system("chmod 660 \"$conf_generated_file_path/vhosts.conf\"");
		markCronflagOk ("gen_vhosts='no'");
	}
	if($cronjob_table_content["restart_apache"] == "yes"){
		restartApache ();
		markCronflagOk ("restart_apache='no'");
	}
}

function checkNagiosCronService () {
	global $conf_nagios_host;
	global $conf_nagios_username;
	global $conf_nagios_config_file_path;
	global $conf_nagios_restart_command;

	if ( 	! $conf_nagios_host or
		! $conf_nagios_username or  
		! $conf_nagios_config_file_path or  
		! $conf_nagios_restart_command ) {
		markCronflagOk ("gen_nagios='no'");
		return;
	}

	$cronjob_table_content = getCronFlags();
	if($cronjob_table_content["gen_nagios"] == "yes"){
		echo "Generating Nagios configuration\n";
		$config = nagios_generate();
		$tmpfile = tempnam("/somedirthatdoesntexist","newnagiosconfig");
		file_put_contents($tmpfile,$config);

		// FIXME: To work, this code requires that:
		// 1) the nagios monitor host be in the SSH keyring for the DTC user
		// 2) the destination file be writable for the user DTC uses to log into the nagios monitor host.  We cannot do that from here.

		$returnvar = 0;

		echo "Copying Nagios configuration to monitor host\n";
		system("scp -B $tmpfile $conf_nagios_username@$conf_nagios_host:$conf_nagios_config_file_path",$return_var);
		if ($return_var) {
			echo "Failed (return value $return_var) to install Nagios configuration in username@$conf_nagios_host:$conf_nagios_config_file_path";
			unlink($tmpfile);
			return;
		}

		echo "Reloading Nagios configuration\n";
		system("ssh -o 'BatchMode yes' $conf_nagios_username@$conf_nagios_host $conf_nagios_restart_command",$return_var);
		if ($return_var) {
			echo "Failed (return value $return_var) to reload Nagios configuration in $conf_nagios_host using command $conf_nagios_restart_command";
			unlink($tmpfile);
			return;
		}

		unlink($tmpfile);
		markCronflagOk ("gen_nagios='no'");
	}
}

function checkWebalizerCronService () {
	$cronjob_table_content = getCronFlags();
	if($cronjob_table_content["gen_webalizer"] == "yes"){
		echo "Generating Webalizer stats script\n";
		stat_script_generate();
		markCronflagOk ("gen_webalizer='no'");
	}
}

function checkUserCronJob () {
	$cronjob_table_content = getCronFlags();
	if($cronjob_table_content["gen_user_cron"] == "yes"){
		echo "Generating user cron jobs\n";
		user_cron_generate();
	}
}

function printEndTime () {
	global $script_start_time;
	$exec_time = time() - $script_start_time;
	if($exec_time > 60){
		$ex_sec = $exec_time % 60;
		$ex_min = round($exec_time / 60);
	}else{
		$ex_sec = $exec_time;
		$ex_min = 0;
	}
	echo date("Y m d / H:i:s T")." DTC cron job finished (exec time=".$ex_min.":".$ex_sec.")\n\n";
}



// Edit the following if you want to disable some services...
checkLockFlag();
checkOtherServerTriggers();
checkNamedCronService();
cronMailSystem();
checkPop3dStarted();
checkSSHCronService();
checkApacheCronService();
checkNagiosCronService();
$cronjob_table_content = getCronFlags();
if($cronjob_table_content["gen_backup"] == "yes"){
	echo "Generating backup script\n";
	backup_script_generate();
	markCronflagOk ("gen_backup='no'");
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
checkWebalizerCronService();
checkUserCronJob();
$cronjob_table_content = getCronFlags();
checkTimeAndLaunchNetBackupScript();
resetLockFlag();
// Echo the console:
echo("Report for this job:\n");
echo( str_replace("<br>","\n",$console));
printEndTime();
$_inprogress = FALSE;
exit();

?>
