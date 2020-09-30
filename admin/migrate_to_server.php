#!/usr/bin/env php
<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

$script_start_time = time();
$start_stamps = gmmktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

define("BUF_SIZ", 1024);        # max buffer size
define("FD_WRITE", 0);        # stdin
define("FD_READ", 1);        # stdout
define("FD_ERR", 2);        # stderr
function my_proc_exec($cmd){
	$descriptorspec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w"),
		2 => array("pipe", "w")
	);

	$ptr = proc_open($cmd, $descriptorspec, $pipes, NULL, $_ENV);
	if (!is_resource($ptr))
		return false;

	while (($buffer = fgets($pipes[FD_READ], BUF_SIZ)) != NULL 
			|| ($errbuf = fgets($pipes[FD_ERR], BUF_SIZ)) != NULL) {
		if (!isset($flag)) {
			$pstatus = proc_get_status($ptr);
			$first_exitcode = $pstatus["exitcode"];
			$flag = true;
		}
		if (strlen($buffer))
			echo $buffer;
		if (strlen($errbuf))
			echo "ERR: " . $errbuf;
	}

	foreach ($pipes as $pipe)
		fclose($pipe);

	/* Get the expected *exit* code to return the value */
	$pstatus = proc_get_status($ptr);
	if (!strlen($pstatus["exitcode"]) || $pstatus["running"]) {
		/* we can trust the retval of proc_close() */
		if ($pstatus["running"])
			proc_terminate($ptr);
		$ret = proc_close($ptr);
	} else {
		if ((($first_exitcode + 256) % 256) == 255 
				&& (($pstatus["exitcode"] + 256) % 256) != 255)
			$ret = $pstatus["exitcode"];
		elseif (!strlen($first_exitcode))
			$ret = $pstatus["exitcode"];
		elseif ((($first_exitcode + 256) % 256) != 255)
			$ret = $first_exitcode;
		else
			$ret = 0; /* we "deduce" an EXIT_SUCCESS ;) */
		proc_close($ptr);
	}
	return ($ret + 256) % 256;
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


function migrate_dump_all_dbs($dir){
	global $conf_mysql_login;
	global $conf_mysql_pass;
	$imp = "#!/bin/sh\n\n";
	$imp .= "echo -n \"Importing all dbs:\"\n";
	$script = "#!/bin/sh\n\n";
	$script .= "echo -n \"Dumping all dbs to $dir:\"\n";

	$q = "SHOW DATABASES";
	$r = mysql_query($q)or die("Cannot execute query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$name = $a["Database"];
		if($name != "information_schema" && $name != "mysql" && $name != "test"){
			$cmd = "mysqldump -u$conf_mysql_login -p$conf_mysql_pass -c --add-drop-table --databases $name >$dir/$name.sql\n";
			$script .= "echo -n \" $name\"\n";
			$script .= "$cmd\n";
			$imp .= "echo -n \" $name\"\n";
			$imp .= "mysql --defaults-file=/etc/mysql/debian.cnf < ".$name.".sql\n";
		}
	}
	$imp .= "echo -n \" mysql\"\n";
	$imp .= "mysql --defaults-file=/etc/mysql/debian.cnf < mysql.sql\n";
	$imp .= "echo \" done!\"\n";
	$script .= "echo \" done!\"\n";

	// Save the dump script, execute it and delete it
	$dadb = "$dir/dtc_dump_all_dbs.sh";
	$fp = fopen($dadb,"w+");
	if($fp === FALSE){
		die("Cound not open backup script file\n");
	}
	fwrite($fp,$script);
	fclose($fp);
	chmod($dadb,0744);
	system($dadb);
	unlink($dadb);

	// Save the import script
	$impf = "$dir/dtc_import_all_dbs.sh";
	$fp = fopen($impf,"w+");
	if($fp === FALSE){
		die("Cound not open import script file\n");
	}
	fwrite($fp,$imp);
	fclose($fp);
	chmod($impf,0744);

	# Create a script for the mysql.mysql and mysql.db tables.
	$mdb = "# DTC MySQL dump
Use mysql;

";
	$q = "SELECT * FROM mysql.user WHERE User NOT LIKE 'root' AND User NOT LIKE 'debian-sys-maint' AND User NOT LIKE 'dtcdaemons'";
	$r = mysql_query($q)or die("Cannot execute query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$mdb .= "# User ".$a["User"]." host ".$a["Host"]." password ".$a["Password"]."

INSERT IGNORE INTO mysql.user
(Host, User, Password, Select_priv, Insert_priv,
Update_priv, Delete_priv, Create_priv,
Drop_priv, Reload_priv, Shutdown_priv, Process_priv,
File_priv, Grant_priv, References_priv, Index_priv,
Alter_priv, Show_db_priv, Super_priv, Create_tmp_table_priv,
Lock_tables_priv, Execute_priv, Repl_slave_priv, Repl_client_priv,
Create_view_priv, Show_view_priv, Create_routine_priv, Alter_routine_priv,
Create_user_priv, ssl_type, ssl_cipher, x509_issuer,
x509_subject, max_questions, max_updates, max_connections,
max_user_connections, dtcowner)
VALUES
('".$a["Host"]."', '".$a["User"]."', '".$a["Password"]."', '".$a["Select_priv"]."', '".$a["Insert_priv"]."',
'".$a["Update_priv"]."', '".$a["Delete_priv"]."', '".$a["Create_priv"]."',
'".$a["Drop_priv"]."', '".$a["Reload_priv"]."', '".$a["Shutdown_priv"]."', '".$a["Process_priv"]."',
'".$a["File_priv"]."', '".$a["Grant_priv"]."', '".$a["References_priv"]."', '".$a["Index_priv"]."',
'".$a["Alter_priv"]."', '".$a["Show_db_priv"]."', '".$a["Super_priv"]."', '".$a["Create_tmp_table_priv"]."',
'".$a["Lock_tables_priv"]."', '".$a["Execute_priv"]."', '".$a["Repl_slave_priv"]."', '".$a["Repl_client_priv"]."',
'".$a["Create_view_priv"]."', '".$a["Show_view_priv"]."', '".$a["Create_routine_priv"]."', '".$a["Alter_routine_priv"]."',
'".$a["Create_user_priv"]."', '".$a["ssl_type"]."', '".$a["ssl_cipher"]."', '".$a["x509_issuer"]."',
'".$a["x509_subject"]."', '".$a["max_questions"]."', '".$a["max_updates"]."', '".$a["max_connections"]."',
'".$a["max_user_connections"]."', '".$a["dtcowner"]."');

UPDATE mysql.user SET
Password='".$a["Password"]."', Select_priv='".$a["Select_priv"]."', Insert_priv='".$a["Insert_priv"]."',
Update_priv='".$a["Update_priv"]."', Delete_priv='".$a["Delete_priv"]."',
Create_priv='".$a["Create_priv"]."', Drop_priv='".$a["Drop_priv"]."',
Reload_priv='".$a["Reload_priv"]."', Shutdown_priv='".$a["Shutdown_priv"]."',
Process_priv='".$a["Process_priv"]."', File_priv='".$a["File_priv"]."',
Grant_priv='".$a["Grant_priv"]."', References_priv='".$a["References_priv"]."',
Index_priv='".$a["Index_priv"]."', Alter_priv='".$a["Alter_priv"]."',
Show_db_priv='".$a["Show_db_priv"]."', Super_priv='".$a["Super_priv"]."',
Create_tmp_table_priv='".$a["Create_tmp_table_priv"]."', Lock_tables_priv='".$a["Lock_tables_priv"]."',
Execute_priv='".$a["Execute_priv"]."', Repl_slave_priv='".$a["Repl_slave_priv"]."',
Repl_client_priv='".$a["Repl_client_priv"]."', Create_view_priv='".$a["Create_view_priv"]."',
Show_view_priv='".$a["Show_view_priv"]."', Create_routine_priv='".$a["Create_routine_priv"]."',
Alter_routine_priv='".$a["Alter_routine_priv"]."', Create_user_priv='".$a["Create_user_priv"]."',
ssl_type='".$a["ssl_type"]."', ssl_cipher='".$a["ssl_cipher"]."',
x509_issuer='".$a["x509_issuer"]."', x509_subject='".$a["x509_subject"]."',
max_questions='".$a["max_questions"]."', max_updates='".$a["max_updates"]."',
max_connections='".$a["max_connections"]."', max_user_connections='".$a["max_user_connections"]."', dtcowner='".$a["dtcowner"]."'
WHERE Host='".$a["Host"]."' AND User='".$a["User"]."';

";
	}

	$mdb .= "# Dumping table rights\n\n";
	$q = "SELECT * FROM mysql.db WHERE Db NOT LIKE 'test%'";
	$r = mysql_query($q)or die("Cannot execute query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$mdb .= "# User ".$a["User"]." db ".$a["Db"]." host ".$a["Host"]."
INSERT IGNORE INTO mysql.db
(Host,Db,User,Select_priv,Insert_priv,
Update_priv,Delete_priv,Create_priv,Drop_priv,
Grant_priv,References_priv,Index_priv,Alter_priv,
Create_tmp_table_priv, Lock_tables_priv, Create_view_priv,
Show_view_priv, Create_routine_priv, Alter_routine_priv,
Execute_priv) VALUES
('".$a["Host"]."', '".$a["Db"]."', '".$a["User"]."', '".$a["Select_priv"]."', '".$a["Insert_priv"]."',
'".$a["Update_priv"]."', '".$a["Delete_priv"]."', '".$a["Create_priv"]."', '".$a["Drop_priv"]."',
'".$a["Grant_priv"]."', '".$a["References_priv"]."', '".$a["Index_priv"]."', '".$a["Alter_priv"]."',
'".$a["Create_tmp_table_priv"]."', '".$a["Lock_tables_priv"]."', '".$a["Create_view_priv"]."',
'".$a["Show_view_priv"]."', '".$a["Create_routine_priv"]."', '".$a["Alter_routine_priv"]."',
'".$a["Execute_priv"]."');

UPDATE mysql.db SET Select_priv='".$a["Select_priv"]."', Insert_priv='".$a["Insert_priv"]."',
Update_priv='".$a["Update_priv"]."', Delete_priv='".$a["Delete_priv"]."',
Create_priv='".$a["Create_priv"]."', Drop_priv='".$a["Drop_priv"]."',
Grant_priv='".$a["Grant_priv"]."', References_priv='".$a["References_priv"]."',
Index_priv='".$a["Index_priv"]."', Alter_priv='".$a["Alter_priv"]."',
Create_tmp_table_priv='".$a["Create_tmp_table_priv"]."', Lock_tables_priv='".$a["Lock_tables_priv"]."',
Create_view_priv='".$a["Create_view_priv"]."', Show_view_priv='".$a["Show_view_priv"]."',
Create_routine_priv='".$a["Create_routine_priv"]."', Alter_routine_priv='".$a["Alter_routine_priv"]."',
Execute_priv='".$a["Execute_priv"]."'
WHERE Host='".$a["Host"]."' AND Db='".$a["Db"]."' AND User='".$a["User"]."';

";
	}
	$mdb .= "FLUSH PRIVILEGES;\n\n";
	$fp = fopen("$dir/mysql.sql","w+");
	if($fp === FALSE){
		die("Cound not open mysql.sql file\n");
	}
	fwrite($fp,$mdb);
	fclose($fp);
}

if($argc !=4)	die("Usage: migrate_to_server.php {bk_dir} {my_ip} {dst_ip}");

# ${bk_dir} ${my_ip} ${dst_ip}
$bk_dir = $argv[1];
$my_ip = $argv[2];
$rem_ip = $argv[3];

echo date("Y m d / H:i:s T",$script_start_time)." Starting DTC migrate job\n";

migrate_dump_all_dbs($bk_dir);
system("sed -i 's/$my_ip/$rem_ip/' $bk_dir/dtc.sql");

printEndTime();
exit();

?>
