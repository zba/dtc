<?php

error_reporting(E_ALL);

$console = "";

// AUTO SQL CONFIG
// This file automates the use of a mysql database. It creates connection
// to it, and fetch all software config from the config table

$dtcshared_path = dirname(__FILE__);
$autoconf_configfile = "mysql_config.php";

require("$dtcshared_path/dtc_version.php");

function connect2base(){
	global $conf_mysql_host;
	global $conf_mysql_login;
	global $conf_mysql_pass;
	global $conf_mysql_db;

	$ressource_id = mysql_connect("$conf_mysql_host", "$conf_mysql_login", "$conf_mysql_pass");
	if($ressource_id == false)	return false;
	return @mysql_select_db($conf_mysql_db)or die("Cannot select db: $conf_mysql_db");
}

function createTableIfNotExists(){
	global $console;
	if ($handle = opendir('tables/')) {
		// Create all tables with stored table creation SQL script in .../dtc/admin/tables/*.sql
		while (false !== ($file = readdir($handle))){
			if($file != "." && $file != ".." && $file != "tables/.." && $file != 'CVS'){
				$fp = fopen("tables/$file","r");
				$table_create_query = fread($fp,filesize("tables/$file"));
				fclose($fp);
				$table_name = preg_replace ("/.sql/", "", $file);
				$query = "SELECT * FROM $table_name WHERE 1 LIMIT 1;";
				$result = @mysql_query($query);
				if($result == false){
					mysql_query($table_create_query)or die("Cannot create table $table_name when querying :<br><font color=\"#FF0000\">$table_create_query</font> !!!".mysql_error());
					$console .= "Table ".$table_name." has been created<br>";
				}else{
					// echo "Table ".$table_name." can be selected<br>";
				}
			}
		}
		closedir($handle); 

		// Verify that the groups and config tables have at least one record. If not, create it using default values.
		$query = "SELECT * FROM groups WHERE 1;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$query = "INSERT INTO groups (members) VALUES ('zigo')";
			$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
			$console .= "Default values has been inserted in groups table.<br>";
		}

		$query = "SELECT * FROM config WHERE 1;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$query = "INSERT INTO config () VALUES ()";
			$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
			$console .= "Default values has been inserted in config table.<br>";
		}

		$query = "SELECT * FROM cron_job WHERE 1;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$query = "INSERT INTO cron_job () VALUES ()";
			$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
			$console .= "Default values has been inserted in cron_job table.<br>";
		}
	}
}

// This function get all field from unic row "config" and convert them to
// global variables using the name of that field. Like if a field name is foo,
// then a global variable called $conf_foo will be created.
function getConfig(){
	global $conf_mysql_db;
	$query = "SELECT * FROM config WHERE 1 LIMIT 1;";
	$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("No config values in table !!!");
	$row = mysql_fetch_array($result);

	$fields = mysql_list_fields($conf_mysql_db, "config");
	$columns = mysql_num_fields($fields);

	for($i=0;$i<$columns;$i++){
		$field_name = mysql_field_name($fields, $i);
		$toto = "conf_".$field_name;
		global $$toto;
		$$toto = $row["$field_name"];
	}
}

//////////////////////////////////////
//////////////////////////////////////
////                              ////
////   AUTOCONF STARTS HERE !!!   ////
////                              ////
//////////////////////////////////////
//////////////////////////////////////
// Include the config file, create it if not found
require("$dtcshared_path/$autoconf_configfile");

if(connect2base() == false){
	die("Cannot connect to database !!!");
}
getConfig();

// Do all the updates according to upgrade_sql.php
if(!isset($conf_db_version)){
	$conf_db_version = 0;
}

if($conf_demo_version == 'yes'){
	@session_start();
	if(isset($demo_version_has_started)) $_SESSION["demo_version_has_started"]=$demo_version_has_started;
	if(!isset($_SESSION["demo_version_has_started"]) || $_SESSION["demo_version_has_started"] != "started"){
		$_SESSION["demo_version_has_started"] = "started";
		$query = "DELETE FROM admin;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$query = "DELETE FROM clients;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$query = "DELETE FROM commande;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$query = "DELETE FROM domain;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$query = "DELETE FROM ftp_access;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$query = "DELETE FROM pop_access;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());
		$query = "DELETE FROM subdomain;";
		$result = mysql_query($query)or die("Cannot query $query !!!".mysql_error());

		die("Welcom to DTC demo version. In demo version, all tables are erased at
		launch time.<br><br>
		<a href=\"?\">Ok, let's try !</a>
		");
	}
}

?>
