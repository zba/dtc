<?php

$script_start_time = time();
$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

require_once("genfiles/genfiles.php");

// Set here your apachectl path if you need it fully (like for example
// /usr/sbin/apachectl for debian, or /usr/local/sbin/apachectl for FreeBSD)
if($conf_unix_type == "debian" || $conf_unix_type == "redhat"){
	$APACHECTL = "/usr/sbin/apachectl";
}else{
	$APACHECTL = "/usr/local/sbin/apachectl";
}

// Set to yes if you want to check for qmail pop3d availability and relaunch
// it if needed. Note that you can have to customise that cron script part
// for your qmail installation. This is done for debian standard start script.
$CHECK_QMAIL_POP3D = "no";

echo date("Y m d / H:i:s T",$script_start_time)." Starting DTC cron job\n";
// Let's see if DTC's mysql_config.php is OK and lock back the shared folder
// and mysql_config.php to root:root
if($conf_mysql_conf_ok=="yes" && $conf_demo_version  == "no"){
	exec("chown root:65534 $dtcshared_path");
	exec("chown root:65534 $dtcshared_path/mysql_config.php");
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
function commitTriggerToRemote($a){
	$flag = false;
        $url = $a["server_addr"].'/dtc/list_domains.php?action=update_request&login='.$a["server_login"].'&pass='.$a["server_pass"];
        while($retry < 3 && $flag == false){
		$lines = file ($url);
		$nline = sizeof($lines);
		if(strstr($lines[0],"Successfuly recieved trigger!") != false){
			$flag = true;
		}
		$retry ++;
		if($flag == false)      sleep(3);
	}
	return flag;
}

$query = "SELECT * FROM $pro_mysql_backup_table WHERE type='trigger_changes' AND status='pending';";
$r = mysql_query($query)or die("Cannot query \"$query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$a = mysql_fetch_array($r);
	echo "Triggering the change to the backup server ".$a["server_addr"]." with login ".$a["server_login"]."...";
	if(commitTriggerToRemote($a)){
		$q2 = "UPDATE $pro_mysql_backup_table SET status='done' WHERE id='".$a["id"]."';";
		$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		echo "success!\n";
	}else{
		echo "failed!\n";
	}
}

$start_stamps = mktime();
////////////////////////////////////////////////////////////
// First find if it's time for long statistic generation. //
// Do it all the time, for (debuging) the moment... :)    //
////////////////////////////////////////////////////////////
function updateAllDomainsStats(){
	global $pro_mysql_admin_table;
	global $pro_mysql_domain_table;

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
			$du_string = exec("du -sb $adm_path/$domain_name --exclude=access.log",$retval);
			$du_state = explode("\t",$du_string);
			$domain_du = $du_state[0];
			$q2 = "UPDATE $pro_mysql_domain_table SET du_stat='$domain_du' WHERE name='$domain_name';";
			mysql_query($q2)or die("Cannot query \"$q2\" !!!".mysql_error()." in file ".__FILE__." line ".__LINE__);
			echo "email...";
			sum_email($domain_name);
			echo "http...";
			sum_http($domain_name);
			echo "ftp...";
			sum_ftp($domain_name);
			echo "done!\n";
		}
	}
}


// This will set each day at 0:00
if(($start_stamps%(60*60*24))< 60*10)	updateAllDomainsStats();
// This one is each hours
// if(($start_stamps%(60*60))< 60*10)	updateAllDomainsStats();
// This is each time the script is launched (all 10 minutes)
// updateAllDomainsStats();

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
	echo "Generating Qmail accounts\n";
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

///////////////////////////////////////////////////////////////////////////////////////
// This script should be launched as root, so we have to chown the generated files ! //
// (otherwise, the web interface wont be able to write them)                         //
///////////////////////////////////////////////////////////////////////////////////////
system("chown -R nobody:65534 $conf_generated_file_path");
system("./checkbind.sh $conf_generated_file_path");
system("chmod -R 770 $conf_generated_file_path/zones");

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
	echo ("Testing and creating directories for vhosts...\n");
	system("chmod +x \"$conf_generated_file_path/vhost_check_dir\"");
	system("$conf_generated_file_path/vhost_check_dir");
	echo "Testing apache conf\n";
	exec ("$APACHECTL configtest", $plop, $return_var);
	if($return_var == false){
		echo "Config is OK : restarting Apache\n";
		system("$APACHECTL stop");
		echo "Waiting 6... ";	// With 1000 domains on a celeron 800 and fast hard drives, 5 seconds is just the right value...
		sleep(1);
		echo "5... ";
		sleep(1);
		echo "4... ";
		sleep(1);
		echo "3... ";
		sleep(1);
		echo "2... ";
		sleep(1);
		echo "1... ";
		sleep(1);
		echo "0\n";
		system("$APACHECTL start");
		// change to graceful apache restart, rather than a hard stop and start
		//system("$APACHECTL graceful");
	}else{
		echo "Config not OK : I can't reload apache !!!\n";
	}
}

$exec_time = time() - $script_start_time;
echo "Resetting all cron flags\n";
$query = "UPDATE cron_job SET lock_flag='finished', last_cronjob=NOW(),qmail_newu='no', restart_qmail='no', reload_named='no', restart_apache='no', gen_vhosts='no', gen_named='no', gen_qmail='no', gen_webalizer='no', gen_backup='no' WHERE 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
if($exec_time > 60){
	$ex_sec = $exec_time % 60;
	$ex_min = $exec_time / 60;
}else{
	$ex_sec = $exec_time;
	$ex_min = 0;
}
echo date("Y m d / H:i:s T")." DTC cron job finished (exec time=".$ex_min.":".$ex_sec.")\n\n";
exit();

?>
