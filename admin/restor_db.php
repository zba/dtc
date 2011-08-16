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

#Set default timezona tog et rid of warnings...
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

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
	$curtbl = $tblnames[$i];
	$t = $tables[$curtbl];
	echo " ".$curtbl;
	$allvars = $t["vars"];
	$varnames = array_keys($allvars);
	$numvars = sizeof($allvars);
	// If no table exist, then build a CREATE TABLE statement
	if( !mysql_table_exists($curtbl) ){
		$qc = "CREATE TABLE IF NOT EXISTS ".$curtbl."(\n";
		for($j=0;$j<$numvars;$j++){
			if($j != 0){
				$qc .= ",\n";
			}
			$qc .= "  ".$varnames[$j] ." ".$allvars[$varnames[$j]];
		}
		if( isset( $t["primary"] ) ){
			// Todo: remove the parentesys from dtc_db.php and add them here
			$qc .= ",\n  PRIMARY KEY ".$t["primary"];
		}
		if( isset( $t["keys"] )){
			$nkeys = sizeof($t["keys"]);
			$ak = array_keys($t["keys"]);
			for($x=0;$x<$nkeys;$x++){
				// Todo: add parentesis here, remove them from the dtc_db.php file
				$qc .= ",\n  UNIQUE KEY ".$ak[$x]." ".$t["keys"][ $ak[$x] ];
			}
		}
		if( isset( $t["index"] )){
			$nidx = sizeof($t["index"]);
			$ai = array_keys($t["index"]);
			for($x=0;$x<$nidx;$x++){
				$qc .= ",\n  KEY ".$ai[$x]." ".$t["index"][ $ai[$x] ];
			}
		}
		if( isset( $t["max_rows"] )){
			$qc .= "\n)MAX_ROWS=1 TYPE=MyISAM\n";
		}else{
			$qc .= "\n)TYPE=MyISAM\n";
		}
		// echo $q;
		$r = mysql_query($qc)or die("Cannot execute query: \"$qc\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	// If the table exists already, then check all variables types, primary key, unique keys
	// and remove useless variables.
	// All this to make sure that we upgrade correctly each tables.
	}else{
		// First, we check if all feilds from dtc_db.php are present
		for($j=0;$j<$numvars;$j++){
			$v = $varnames[$j];
			$vc = $allvars[$v];
			// If the field is present, create it.
			$q = "SHOW COLUMNS FROM $curtbl WHERE Field='$v'";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n == 0){
				// If we are adding a new auto_increment field, then we must drop the current PRIMARY KEY
				// before adding this new field.
				if( strstr($vc, "auto_increment") != FALSE){
					// In case there was a primary key, drop it!
					$q = "ALTER IGNORE TABLE $curtbl DROP PRIMARY KEY;";
					// Don't die, in some case it can fail!
					$r = mysql_query($q); // or die("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
					$q = "ALTER TABLE $curtbl ADD $v $vc PRIMARY KEY;";
					$r = mysql_query($q)or print("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error()."\n");
				}else{
					$q = "ALTER TABLE $curtbl ADD $v $vc;";
					$r = mysql_query($q)or print("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error()."\n");
				}
			// If it is present in MySQL already, then we need to check if types are marching
			// if types don't match, then we issue an ALTER TABLE
			}else{
				$a = mysql_fetch_array($r);
				$a_extra = $a["Extra"];
				$a_type = $a["Type"];
				switch($a_type){
				case "blob":
				case "text":
					$type = $a_type;
					break;
				case "time":
					if($a["Null"] == "NO"){
						$type = $a_type." NOT NULL default '00:00:00'";
					}else{
						$type = $a_type." default NULL";
					}
					break;
				case "date":
					if($a["Null"] == "NO"){
						$type = $a_type." NOT NULL default '0000-00-00'";
					}else{
						$type = $a_type." default NULL";
					}
					break;
				case "datetime":
					if($a["Null"] == "NO"){
						$type = $a_type." NOT NULL default '0000-00-00 00:00:00'";
					}else{
						$type = $a_type." default NULL";
					}
					break;
				case "timestamp":
					if($a["Null"] == "NO"){
						$type = $a_type." NOT NULL default '0'";
					}else{
						$type = $a_type." default NULL";
					}
				default:
					if($a_extra == "auto_increment"){
						$type = $a_type." NOT NULL auto_increment";
					}else{
						if($a["Null"] == "NO"){
							$type = $a_type." NOT NULL default '".$a["Default"]."'";
						}else{
							$type = $a_type." default NULL";
						}
					}
				}
				// If MySQL and dtc_db.php don't match, it means we need to update the variable type
				if($type != $vc){
					echo "\n\nIn db: \"$type\"\n";
					echo "In file: $vc\n";
					$q = "ALTER TABLE $curtbl CHANGE $v $v $vc;";
					echo "Altering: $q\n";
					$r = mysql_query($q)or print("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error()."\n");
				}
			}
		}

		// Make sure all the unique keys of dtc_db.php are in MySQL
		if( isset($t["keys"]) ){
			$keys = $t["keys"];
			$numvars = sizeof($keys);
			$varnames = array_keys($keys);
			for($j=0;$j<$numvars;$j++){
				$key_name = $varnames[$j];
				if(!findKeyInTable($curtbl,$key_name)){
					$var_2_add = "UNIQUE KEY ".$key_name;
					$q = "ALTER TABLE ".$curtbl." ADD $var_2_add ".$keys[$key_name].";";
					$r = mysql_query($q)or die("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
				}
			}
		}
		// Make sure all keys in MySQL are also present in dtc_db.php
		// and dorps the one that aren't in both

		// First, check if primary keys in MySQL and in dtc_db.php are matching
		// So we first get the primary key from DB, and then compare.
		$q = "SHOW INDEX FROM $curtbl WHERE Key_name='PRIMARY'";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$pkey = "";
		for($j=0;$j<$n;$j++){
			$apk = mysql_fetch_array($r);
			if($j>0){
				$pkey .= ",";
			}
			$pkey .= $apk["Column_name"];
		}
		// Is this a primary key that is new in dtc_db.php?
		if($n == 0 && isset($t["primary"])){
			$q = "ALTER IGNORE TABLE $curtbl ADD PRIMARY KEY dtcprimary ".$t["primary"].";";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		// Does dtc_db.php drops a primary key?
		}elseif($n > 0 && !isset($t["primary"])){
			$q = "ALTER IGNORE TABLE $curtbl DROP PRIMARY KEY;";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		// If there's no primary key at all, do nothing...
		}elseif($n == 0 && !isset($t["primary"])){
			echo "";
		// Are the primary keys in dtc_db and in MySQL different? If yes, drop and add
		}elseif( "(".$pkey.")" != $t["primary"] ){
			$pk = $t["primary"];
			// Check if we have a auto_increment value somewhere, it which case we don't touch the PRIMARY key
			// Simply because it has been done just above !
			$nop_pk = substr($pk,1,strlen($pk)-2); // The string without the (parrentesys,between,field,names)
			if(isset($t["vars"][ $nop_pk ]) ){
				if( strstr($t["vars"][ $nop_pk ],"auto_increment") === FALSE){
					// Always remove and readd the PRIMARY KEY in case it has changed
					$q = "ALTER IGNORE TABLE $curtbl DROP PRIMARY KEY;";
					$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
					$q = "ALTER IGNORE TABLE $curtbl ADD PRIMARY KEY dtcprimary $pk;";
					$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
				}
			}
		}


		// We have to rebuild indexes in order to get rid of past mistakes in the db in case of panel upgrade
		$q = "SHOW INDEX FROM $curtbl WHERE Key_name NOT LIKE 'PRIMARY' AND Non_unique='1' and Seq_in_index='1';";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		for($j=0;$j<$n;$j++){
			$a = mysql_fetch_array($r);
			// Drop all indexes
			$q2 = "ALTER TABLE $curtbl DROP INDEX ".$a["Key_name"].";";
			$r2 = mysql_query($q2)or die("Cannot execute query: \"$q2\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		}
		// The readd all indexes
		if( isset($t["index"]) ){
			$indexes = $t["index"];
			$numvars = sizeof($indexes);
			if($numvars > 0){
				$varnames = array_keys($indexes);
				for($j=0;$j<$numvars;$j++){
					$v = $varnames[$j];
					// We have to rebuild indexes in order to get rid of past mistakes in the db in case of panel upgrade
					if(findKeyInTable($curtbl,$v)){
						$q = "ALTER TABLE $curtbl DROP INDEX ".$v."";
						$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
					}
					$q = "ALTER TABLE $curtbl ADD INDEX ".$v." ".$indexes[$v].";";
					$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
				}
			}
		}
	}
}
echo "\n";

# Check all expiration date that are 0000-00-00 and set them to the current date + 10 years
# This is a safety against user's stupidity...
$year = date("Y");
$year = $year + 10;
$q = "UPDATE admin SET expire='".$year."-".date("m-d")."' WHERE expire='0000-00-00';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Fill the new quota_couriermaildrop with values
$q = "UPDATE pop_access SET quota_couriermaildrop=CONCAT(1024000*quota_size,'S,',quota_files,'C')";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Sets the fullemail field correctly, as it might be wrong in some setups.
$q = "UPDATE pop_access SET fullemail = concat( `id`,  '@', `mbox_host` )";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

// Alter the default shell value for FreeBSD, as the path will be in /usr/local
if($conf_unix_type == "bsd"){
	$q = "ALTER TABLE ssh_access CHANGE shell shell varchar(64) NOT NULL default '/usr/local/bin/dtc-chroot-shell'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$q = "ALTER TABLE ftp_access CHANGE shell shell varchar(64) NOT NULL default '/usr/local/bin/bash'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
}

// Get all the config values from db
$q = "SELECT * FROM config";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Cannot read config table: not one and only one row...");
}
$config_vals = mysql_fetch_array($r);

// Get rid of old skins names if they were set previously in the db...
$zeskin = $config_vals["skin"];
if( $zeskin == "green2" || $zeskin == "iglobal" || $zeskin == "green_gpl" || $zeskin == "darkblue" || $zeskin == "frame" || $zeskin == "green" || $zeskin == "ruffdogs_mozilla" || $zeskin == "tex" || $zeskin == "muedgrey" || $zeskin == "grayboard"){
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

?>
