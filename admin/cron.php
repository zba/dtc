
<?php

require("/usr/share/dtc/shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

require_once("genfiles/genfiles.php");

echo date("Y m d / H:i:s T")." Starting DTC cron job\n";
// Let's see if DTC's mysql_config.php is OK and lock back the shared folder
// and mysql_config.php to root:root
if($conf_mysql_conf_ok=="yes" && $conf_demo_version  == "no"){
	exec("chown root:0 $dtcshared_path");
        exec("chown root:0 $dtcshared_path/mysql_config.php");
}

$query = "SELECT * FROM $pro_mysql_cronjob_table WHERE 1 LIMIT 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
$num_rows = mysql_num_rows($result);
if($num_rows != 1)	die("No data in the cronjob table !!!");
$cronjob_table_content = mysql_fetch_array($result);

// Lock the cron flag, in case the cron script takes more than 10 minutes
if($cronjob_table_content["lock_flag"] != "finished"){
	echo "DB flag says that last cron job is not finished: exiting.\n
If no cronjob is running, then please do \"UPDATE $pro_mysql_cronjob_table SET lock_flag='finished';\" !\n";
	die("Exiting NOW!");
}
echo "Setting-up lock flag\n";
$query = "UPDATE $pro_mysql_cronjob_table SET lock_flag='inprogress' WHERE 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());

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
			sum_ftp($domain_name);
			sum_http($domain_name);
			echo " disk...";
			$du_string = exec("du -sb $adm_path/$domain_name --exclude=access.log",$retval);
			$du_state = explode("\t",$du_string);
			$domain_du = $du_state[0];
			$q2 = "UPDATE $pro_mysql_domain_table SET du_stat='$domain_du' WHERE name='$domain_name';";
			mysql_query($q2)or die("Cannot query \"$q2\" !!!".mysql_error()." in file ".__FILE__." line ".__LINE__);
			echo "http...";
			sum_http($domain_name);
			echo "ftp...";
			sum_ftp($domain_name);
			echo "done!\n";
		}
	}
}

// This will set each day at 0:00
// if(($start_stamps%(60*60*24))< 60*10)	updateAllDomainsStats();
// This one is each hours
if(($start_stamps%(60*60))< 60*10)	updateAllDomainsStats();
// This is each time the script is launched (all 10 minutes)
// updateAllDomainsStats();

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
	mail_account_generate();
}

///////////////////////////////////////////////////////////////////////////////////////
// This script should be launched as root, so we have to chown the generated files ! //
///////////////////////////////////////////////////////////////////////////////////////
system("chown -R nobody:nogroup $conf_generated_file_path");

if($cronjob_table_content["qmail_newu"] == "yes"){
	echo "Starting qmail-newu\n";
	system("/var/qmail/bin/qmail-newu");
}

if($cronjob_table_content["restart_qmail"] == "yes"){
	echo "Sending qmail-send a HUP\n";
	system("killall -HUP qmail-send");
//	echo "Restarting qmail\n";
//	system("/etc/init.d/qmail stop");
//	sleep(2);
//	system("/etc/init.d/qmail start");
}
// Check if pop is running, restart qmail if not
$fp = fsockopen ($conf_addr_mail_server, 110, $errno, $errstr, 30);
if(!fp){
	echo "$errno/$errstr: POP3 is not running ! Restarting qmail !!!\n";
	system("/etc/init.d/qmail stop");
	sleep(2);
	system("/etc/init.d/qmail start");
}else{
	fclose ($fp);
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
	exec ("/usr/sbin/apachectl configtest", $plop, $return_var);
	if($return_var == false){
		echo "Config is OK : restarting Apache\n";
		system("/usr/sbin/apachectl stop");
		sleep(5);
		system("/usr/sbin/apachectl start");
	}else{
		echo "Config not OK : I can't reload apache !!!\n";
	}
}

echo "Resetting all cron flags\n";
$query = "UPDATE cron_job SET lock_flag='finished', last_cronjob=NOW(),qmail_newu='no', restart_qmail='no', reload_named='no', restart_apache='no', gen_vhosts='no', gen_named='no', gen_qmail='no', gen_webalizer='no', gen_backup='no' WHERE 1;";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
echo date("Y m d / H:i:s T")." DTC cron job finished\n\n";

?>
