<?php
$dtcshared_path = "/usr/share/dtc/shared";
$autoconf_configfile = "mysql_config.php";

$conf_mysql_host="localhost";
$conf_mysql_login="ENTER YOUR LOGIN";
$conf_mysql_pass="ENTER YOUR PASSWORD";
$conf_mysql_db="dtc";

function saveConfigFile(){
	global $conf_mysql_host;
	global $conf_mysql_login;
	global $conf_mysql_pass;
	global $conf_mysql_db;
	global $dtcshared_path;
	global $autoconf_configfile;
	global $conf_mysql_conf_ok;

	$default_conf_content = '<?php
	$conf_mysql_host="'.$conf_mysql_host.'";
	$conf_mysql_login="'.$conf_mysql_login.'";
	$conf_mysql_pass="'.$conf_mysql_pass.'";
	$conf_mysql_db="'.$conf_mysql_db.'";
	$conf_mysql_conf_ok="'.$conf_mysql_conf_ok.'";
?>';

	$fp = fopen("$dtcshared_path/$autoconf_configfile","w");
	fwrite($fp,$default_conf_content);
	fclose($fp);
}

function drawMysqlDBConfigForm(){
	global $conf_mysql_host;
	global $conf_mysql_login;
	global $conf_mysql_pass;
	global $conf_mysql_db;
	global $PHP_SELF;

	$out = "<form action=\"$PHP_SELF\"><h1>Cannot connect to database.</h1>
	<i>Please a correct enter your login information for connecting to mysql.</i><br>
	<table><tr>
	<td align=\"right\">Hostname:</td>		<td><input size=\"30\" type=\"text\" name=\"conf_host\" value=\"$conf_mysql_host\"></td></tr>
	<tr><td align=\"right\">Login:</td>		<td><input size=\"30\" type=\"text\" name=\"conf_login\" value=\"$conf_mysql_login\"></td></tr>
	<tr><td align=\"right\">Password:</td>	<td><input size=\"30\" type=\"text\" name=\"conf_pass\" value=\"$conf_mysql_pass\"></td></tr>
	<tr><td align=\"right\">DataBase:</td>	<td><input size=\"30\" type=\"text\" name=\"conf_db\" value=\"$conf_mysql_db\"></td></tr>
	<tr><td align=\"right\"></td>			<td><input type=\"submit\" name=\"update_conf\" value=\"Ok\"></td></tr>
	<input type=\"hidden\" name=\"conf_mysql_conf_ok\" value=\"no\">
	</form></table>";
	return $out;
}

function connect2base(){
	global $conf_mysql_host;
	global $conf_mysql_login;
	global $conf_mysql_pass;
	global $conf_mysql_db;

	$ressource_id = @mysql_connect("$conf_mysql_host", "$conf_mysql_login", "$conf_mysql_pass");
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
				$table_name = ereg_replace (".sql", "", $file);
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
if(!file_exists("$dtcshared_path/$autoconf_configfile")){
	saveConfigFile();
}
require("$dtcshared_path/$autoconf_configfile");

if($conf_mysql_conf_ok != "yes"){
	$console .= "This is the first launch of DTC : it will attempt to create all tables (if not exists)
and fill 'groups' and 'config' tables with it's default values.<br>";

	// If we changed the values in the form, save the config file with new values
	if($_REQUEST["update_conf"] == "Ok"){
		$conf_mysql_host = $_REQUEST["conf_host"];
		$conf_mysql_login = $_REQUEST["conf_login"];
		$conf_mysql_pass = $_REQUEST["conf_pass"];
		$conf_mysql_db = $_REQUEST["conf_db"];
		saveConfigFile();
	}

	// Check connection to database
	if(connect2base() == false){
		echo drawMysqlDBConfigForm();
		die();
	}

	createTableIfNotExists();
	$conf_mysql_conf_ok = "yes";
	saveConfigFile();
	$console .= "DTC has saved mysql configuration to $autoconf_configfile.
Edit it if you need to change your mysql password in the futur.<br>";
}else{
	if(connect2base() == false){
		die("Cannot connect to database !!!");
	}
}
getConfig();

if($conf_demo_version == 'yes'){
	session_register("demo_version_has_started");
	if($demo_version_has_started != "started"){
		$demo_version_has_started = "started";
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
		<a href=\"$PHP_SELF\">Ok, let's try !</a>
		");
	}
}

?>
