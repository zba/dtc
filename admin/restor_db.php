#!/usr/bin/env php

<?php

# This script updates the strutures of the database of the control
# panel so it keeps compatibility with backward versions
# it normaly doesn't alter the CONTENT of the db itself.

echo "==> Restor DB script for DTC\n";

$pro_mysql_host="localhost";
$pro_mysql_login="root";
$pro_mysql_db="dtc";

$usages = "Usage: php restor_db.php [ options ] db_password

Options are:
  -h host (default to localhost)
  -u user (default to root)
  -d database (default to dtc)
";

if($argc !=2 && $argc !=4 && $argc !=6 && $argc !=8)    die($usages);
$pro_mysql_pass = $argv[$argc-1];
if($argc > 2){
        switch($argv[1]){
        case "-h":      $pro_mysql_host = $argv[2];
                        break;
        case "-u":      $pro_mysql_login = $argv[2];
                        break;
        case "-d":      $pro_mysql_db = $argv[2];
                        break;
        default:        die("Incorrect param1: ".$usages);
        }
}
if($argc > 4){
        switch($argv[3]){
        case "-h":      $pro_mysql_host = $argv[4];
                        break;
        case "-u":      $pro_mysql_login = $argv[4];
                        break;
        case "-d":      $pro_mysql_db = $argv[4];
                        break;
        default:        die("Incorrect param3: ".$usages);
        }
}
if($argc > 6){
        switch($argv[5]){
        case "-h":      $pro_mysql_host = $argv[6];
                        break;
        case "-u":      $pro_mysql_login = $argv[6];
                        break;
        case "-d":      $pro_mysql_db = $argv[6];
                        break;
        default:        die("Incorrect param5: ".$usages);
        }
}

chdir(dirname(__FILE__));
require("dtc_db.php");
require("../shared/dtc_version.php");

mysql_connect("$pro_mysql_host", "$pro_mysql_login", "$pro_mysql_pass")or die ("Cannot connect to $pro_mysql_host");
mysql_select_db("$pro_mysql_db")or die ("Cannot select db: $pro_mysql_db");

function mysql_table_exists($table){
	$exists = mysql_query("SELECT 1 FROM $table LIMIT 0");
	if ($exists) return true;
	return false;
}

// Return true=field found, false=field not found
function findFieldInTable($table,$field){
	$q = "SELECT * FROM $table LIMIT 0;";
	$res = mysql_query($q) or die("Could not query $q!");;
	$num_fields = mysql_num_fields($res);
	for($i=0;$i<$num_fields;$i++){
		if( strtolower(mysql_field_name($res,$i)) == strtolower($field)){
			mysql_free_result($res);
			return true;
		}
	}
	mysql_free_result($res);
	return false;
}

function findKeyInTable($table,$key){
	$q = "SHOW INDEX FROM $table";
	$res = mysql_query($q) or die("Could not query $q!");;
	$num_keys = mysql_num_rows($res);
	for($i=0;$i<$num_keys;$i++){
		$a = mysql_fetch_array($res);
		if(strtolower($a["Key_name"]) == strtolower($key)){
			mysql_free_result($res);
			return true;
		}
	}
	mysql_free_result($res);
	return false;
}


$tables = $dtc_database["tables"];
$nbr_tables = sizeof($tables);
echo "Checking and updating $nbr_tables table structures:";
$tblnames = array_keys($tables);
for($i=0;$i<$nbr_tables;$i++){
	echo " ".$tblnames[$i];
	$allvars = $tables[$tblnames[$i]]["vars"];
	$varnames = array_keys($allvars);
	$numvars = sizeof($allvars);
	if( !mysql_table_exists($tblnames[$i]) ){
		if(strstr($allvars[$varnames[0]],"auto_increment") != NULL)
			$q = "CREATE TABLE IF NOT EXISTS ".$tblnames[$i]."(
".$varnames[0]." ".$allvars[$varnames[0]].",PRIMARY KEY (".$varnames[0]."));";
		else
			$q = "CREATE TABLE IF NOT EXISTS ".$tblnames[$i]."(
".$varnames[0]." ".$allvars[$varnames[0]].");";
		echo $q;
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	}

	for($j=0;$j<$numvars;$j++){
		if(!findFieldInTable($tblnames[$i],$varnames[$j])){
			if( strstr($allvars[$varnames[$j]], "auto_increment") != FALSE){
				// In case there was a primary key, drop it!
				$q = "ALTER IGNORE TABLE ".$tblnames[$i]." DROP PRIMARY KEY;";
				// Don't die, in some case it can fail!
				$r = mysql_query($q); // or die("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
				$q = "ALTER TABLE ".$tblnames[$i]." ADD ".$varnames[$j]." ".$allvars[$varnames[$j]]." PRIMARY KEY;";
				$r = mysql_query($q)or print("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error()."\n");
			}else{
				$q = "ALTER TABLE ".$tblnames[$i]." ADD ".$varnames[$j]." ".$allvars[$varnames[$j]]." ;";
				$r = mysql_query($q)or print("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error()."\n");
			}
		}
	}

	if( isset($tables[$tblnames[$i]]["keys"]) ){
		$allvars = $tables[$tblnames[$i]]["keys"];
		$numvars = sizeof($allvars);
		if($numvars > 0){
			$varnames = array_keys($allvars);
			for($j=0;$j<$numvars;$j++){
				if(!findKeyInTable($tblnames[$i],$varnames[$j])){
					$var_2_add = "UNIQUE KEY ".$varnames[$j];
					$q = "ALTER TABLE ".$tblnames[$i]." ADD $var_2_add ".$allvars[$varnames[$j]].";";
					$r = mysql_query($q)or die("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
				}
			}
		}
	}

	if( isset($tables[$tblnames[$i]]["primary"]) ){
		$allvars = $tables[$tblnames[$i]]["primary"];
		// Check if we have a auto_increment value somewhere, it which case we don't touch the PRIMARY key
		// Simply because it has been done just above !
		$to_check_var = substr($allvars,1,strlen($allvars)-2);
		$skip_primary_key_set = "no";
		if(isset($tables[$tblnames[$i]]["vars"][ $to_check_var ]) ){
			$to_check_var_content = $tables[$tblnames[$i]]["vars"][ $to_check_var ];
			if( strstr($to_check_var_content,"auto_increment") != FALSE){
				$skip_primary_key_set = "yes";
			}
		}
		if($skip_primary_key_set != "yes"){
			// Always remove and readd the PRIMARY KEY in case it has changed
			$q = "ALTER IGNORE TABLE ".$tblnames[$i]." DROP PRIMARY KEY;";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
			$q = "ALTER IGNORE TABLE ".$tblnames[$i]." ADD PRIMARY KEY dtcprimary ".$allvars.";";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		}
	}

	if( isset($tables[$tblnames[$i]]["index"]) ){
		$allvars = $tables[$tblnames[$i]]["index"];
		$numvars = sizeof($allvars);
		if($numvars > 0){
			$varnames = array_keys($allvars);
			for($j=0;$j<$numvars;$j++){
				// We have to rebuild indexes in order to get rid of past mistakes in the db in case of panel upgrade
				if(findKeyInTable($tblnames[$i],$varnames[$j])){
					$q = "ALTER TABLE ".$tblnames[$i]." DROP INDEX ".$varnames[$j]."";
					$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
				}
				$q = "ALTER TABLE ".$tblnames[$i]." ADD INDEX ".$varnames[$j]." ".$allvars[$varnames[$j]].";";
				$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
			}
		}
	}
}
echo "\n";

// Converstion from 0.17.0-R3 and earlier versions
$q = "SHOW TABLES FROM apachelogs";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$n = mysql_num_rows($r);
if($n > 0){
	echo "=> Converting apachelogs table names from # to \$: ";
}
for($i=0;$i<$n;$i++){
	$a = mysql_fetch_array($r);
	$name = $a["Tables_in_apachelogs"];
	if(strstr($name,"#")){
		echo "$name ";
		$q2 = "SET SQL_QUOTE_SHOW_CREATE = 1;";
		$r2 = mysql_query($q2)or die("Cannot query \"$q2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q2 = "SHOW CREATE TABLE apachelogs.`$name`;";
		$r2 = mysql_query($q2)or die("Cannot query \"$q2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$a2 = mysql_fetch_array($r2);
		$c_tbl = $a2["Create Table"];
		$c_tbl = strstr($c_tbl,"\n");
		$new_name = str_replace ( "#", '$', $name);
		$q2 = "CREATE TABLE IF NOT EXISTS apachelogs.`$new_name`(";
		$q2 .= $c_tbl.";\n";
		$r2 = mysql_query($q2)or die("Cannot query \"$q2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q2 = "INSERT INTO apachelogs.`$new_name` SELECT * FROM apachelogs.`$name`;\n";
		$r2 = mysql_query($q2)or die("Cannot query \"$q2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q2 = "DROP TABLE apachelogs.`$name`;\n";
		$r2 = mysql_query($q2)or die("Cannot query \"$q2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}
if($n > 0){
	echo "\n";
}

# Check all expiration date that are 0000-00-00 and set them to the current date + 10 years
$year = date("Y");
$year = $year + 10;
$q = "UPDATE admin SET expire='".$year."-".date("m-d")."' WHERE expire='0000-00-00';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Period was a date before, but it doesn't work with MySQL 5, so we are switching it to a varchar //
$q = "ALTER TABLE `product` CHANGE `period` `period` VARCHAR( 12 ) NOT NULL DEFAULT '0001-00-00';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Adding a new field in pending_renewal
$q = "ALTER TABLE `pending_renewal` CHANGE `heb_type` `heb_type` enum('shared', 'ssl', 'vps', 'server','ssl_renew') NOT NULL default 'shared'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Add the pending flag for payments
$q = "ALTER TABLE `paiement` CHANGE `valid` `valid` enum('yes','no','pending') NOT NULL default 'no';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Change VAT rate fld
$q = "ALTER TABLE `paiement` CHANGE `vat_rate` `vat_rate` decimal(9,2) NOT NULL default '0.00';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Change VAT rate fld
$q = "ALTER TABLE `companies` CHANGE `vat_rate` `vat_rate` decimal(9,2) NOT NULL default '0.00';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Fill the new quota_couriermaildrop with values
$q = "UPDATE pop_access SET quota_couriermaildrop=CONCAT(1024000*quota_size,'S,',quota_files,'C')";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Update the VPS stats table
$q = "ALTER TABLE vps_stats CHANGE `network_in_count` `network_in_count` bigint(22) default NULL";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$q = "ALTER TABLE vps_stats CHANGE `network_out_count` `network_out_count` bigint(22) default NULL";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$q = "ALTER TABLE vps_stats CHANGE `swapio_count` `swapio_count` bigint(22) default NULL";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$q = "ALTER TABLE vps_stats CHANGE `diskio_count` `diskio_count` bigint(22) default NULL";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Set the correct default last_used_lang feild
$q = "ALTER TABLE new_admin CHANGE last_used_lang last_used_lang varchar(32) NOT NULL default 'en_US.UTF-8'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$q = "ALTER TABLE admin CHANGE last_used_lang last_used_lang varchar(32) NOT NULL default 'en_US.UTF-8'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Change domain parking types
$q = "ALTER TABLE `domain` CHANGE `domain_parking_type` `domain_parking_type` enum('redirect','same_docroot','serveralias') NOT NULL default 'redirect'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Change ip pool table enum
$q = "ALTER TABLE ip_pool CHANGE `zone_type` `zone_type` enum('support_ticket','ip_per_ip','ip_per_ip_cidr','one_zonefile') default 'one_zonefile'";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Increase password lenght to 255 chars in thedb
$q = "ALTER TABLE admin CHANGE adm_pass adm_pass varchar(255) NOT NULL";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Alter the default shell value for FreeBSD, as the path will be in /usr/local
if($conf_unix_type == "bsd"){
	$q = "ALTER TABLE ssh_access CHANGE `shell` `shell` varchar(64) NOT NULL default '/usr/local/bin/dtc-chroot-shell'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$q = "ALTER TABLE ftp_access CHANGE `shell` `shell` varchar(64) NOT NULL default '/usr/local/bin/bash'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
}

$q = "SELECT * FROM config";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Cannot read config table: not one and only one row...\n");
}
$config_vals = mysql_fetch_array($r);

// Get rid of old skins...
$zeskin = $config_vals["skin"];
if( $zeskin == "green2" || $zeskin == "iglobal" || $zeskin == "green_gpl" || $zeskin == "darkblue" || $zeskin == "frame" || $zeskin == "green" || $zeskin == "ruffdogs_mozilla" || $zeskin == "tex" || $zeskin == "muedgrey"){
	$q = "UPDATE config SET skin='bwoup';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
}

// Iterate on all mailing lists to set the correct recipient delimiter
echo "-> Changing all recipient delimiter for mailing lists: ";
$q = "SELECT * FROM mailinglist";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$a = mysql_fetch_array($r);

	echo $a["name"];
	$q2 = "SELECT * FROM domain WHERE name='".$a["domain"]."';";
	$r2 = mysql_query($q2)or die("Cannot query ".$q2." line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1){
		echo "Could not found domain of list ".$a["name"]."@".$a["domain"]."\n";
		break;
	}
	$a2 = mysql_fetch_array($r2);

	$q3 = "SELECT * FROM admin WHERE adm_login='".$a2["owner"]."'";
	$r3 = mysql_query($q3)or die("Cannot query ".$q3." line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n3 = mysql_num_rows($r3);
	if($n3 != 1){
		echo "Could not found owner of list ".$a["name"]."@".$a["domain"]."\n";
	}
	$a3 = mysql_fetch_array($r3);

	$path = $a3["path"]."/".$a["domain"]."/lists/".$a["domain"]."_".$a["name"]."/control/delimiter";
	if(file_exists($path)){
		$fp = fopen($path,"wb");
		if($fp != NULL){
			fwrite($fp,$config_vals["recipient_delimiter"]);
			fclose($fp);
		}else{
			echo "Could not open file: ".$path." to change the recipient delimiter!\n";
		}
	}else{
		echo "Could not find file: ".$path." to change the recipient delimiter!\n";
	}
}
echo "\n";


//////////////////////////////////////////
// Repair the bad http_accounting table //
//////////////////////////////////////////
echo "=> Repairing broken http_accounting table...";
// Make a copy of the table with the highest value that must be the good one, without any key...

echo "Copy back http_tmp_table into real table just in case...";
$q = "INSERT IGNORE INTO http_accounting SELECT * FROM http_tmp_table;";
$r = mysql_query($q);
if (!$r)
{
	//echo "[Warning] Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error();
	echo "[OK, not present]\n";
}


echo "drop existing http_tmp_table...";
$q = "DROP TABLE http_tmp_table;";
$r = mysql_query($q);
if (!$r)
{
	//echo "[Warning] Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error();
	echo "[OK, not present]\n";
}

echo "copy...";
$q = "CREATE TABLE http_tmp_table
SELECT min( id ) as id, vhost, bytes_sent, count_hosts, count_visits, count_status_200, count_status_404, count_impressions, last_run, `month`, `year`, domain, bytes_receive
FROM http_accounting
GROUP BY `vhost`, `month` , `year`, `domain`;";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

echo "drop existing http_accounting_tmp...";
$q = "DROP TABLE http_accounting_tmp;";
$r = mysql_query($q);
if (!$r)
{
	//echo "[Warning] Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error();
	echo "[OK, not present]\n";
}

echo "rename...";
$q = "RENAME TABLE http_accounting TO http_accounting_tmp;";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

echo "truncate...";
$q = "TRUNCATE http_accounting_tmp;";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

echo "Remove indexes...";
$q = "SHOW INDEX FROM http_accounting_tmp;";
$r_indexes = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$n_indexes = mysql_num_rows($r_indexes);
for ($i = 0; $i < $n_indexes; $i++){
	$a_indexes = mysql_fetch_array($r_indexes);
	$table_name = $a_indexes[0];
	$index_name = $a_indexes[2];
	if ($index_name != "PRIMARY"){
		echo "Removing $index_name from $table_name...";
		$q = "ALTER TABLE $table_name DROP INDEX $index_name;";
		$r = mysql_query($q);
		if (!$r)
		{
			echo "[Warning] Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error();
		}
		
	}
}

echo "alter...";
$q = "ALTER TABLE http_accounting_tmp ADD UNIQUE (`vhost` ,`month` ,`year` ,`domain`);";
$r = mysql_query($q);
if (!$r)
{
	//echo "[Warning] Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error();
}

echo "insert...";
$q = "INSERT IGNORE INTO http_accounting_tmp SELECT * FROM http_tmp_table;";
$r = mysql_query($q);
if (!$r)
{
	//echo "[Warning] Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error();
}

echo "rename...";
$q = "RENAME TABLE http_accounting_tmp TO http_accounting;";
$r = mysql_query($q);
if (!$r)
{
	echo "[ERROR] Failed to rename table, please report this as a bug, and copy paste the output";
	die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());	
}

echo "drop...";
$q = "DROP TABLE http_tmp_table;";
$r = mysql_query($q);
if (!$r)
{
	//echo "[Warning] Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error();
	echo "[Warning] Can't drop http_tmp_table, please do so manually...";
}
echo "done!\n";

?>
