<?php

require("/usr/share/dtc/shared/autoSQLconfig.php"); // Our main configuration file

// All shared files between DTCadmin and DTCclient
// require("$dtcshared_path/lang.php");			// Setup the $lang global variable (to en, en-us, fr, etc... : whatever is translated !)
require("$dtcshared_path/strings.php");			// Contain all the translated string
require("$dtcshared_path/table_names.php");
require("$dtcshared_path/dtc_functions.php");
include("$dtcshared_path/anotherDtc.php");	// Contain all anotherXXX() functions
include("$dtcshared_path/skin.php");
include("$dtcshared_path/skinLib.php");			// Contain all other disposition and skin layout functions
include("$dtcshared_path/inc/submit_to_sql.php");
include("$dtcshared_path/inc/fetch.php");
include("$dtcshared_path/inc/draw.php");

include("inc/gen_perso_vhost.php");
include("inc/gen_pro_vhost.php");
include("inc/gen_email_account.php");
include("inc/gen_named_files.php");
include("inc/gen_backup_script.php");
include("inc/gen_webalizer_stat.php");

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

echo date("Y m d / H:i:s T")." Starting DTC cron job\n";
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
		echo "Config is OK : restarting Apache<br>\n";
		system("/usr/sbin/apachectl stop");
		sleep(5);
		system("/usr/sbin/apachectl start");
	}else{
		echo "Config not OK : I can't reload apache !!!<br>\n";
	}
}

$query = "UPDATE cron_job SET last_cronjob=NOW(),qmail_newu= 'no', restart_qmail='no', reload_named='no', restart_apache='no', gen_vhosts='no', gen_named='no', gen_qmail='no', gen_webalizer='no', gen_backup='no' WHERE 1";
$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());

?>
