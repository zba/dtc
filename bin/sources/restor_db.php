#!/usr/bin/php4
<?php

$pro_mysql_host="localhost";
$pro_mysql_login="root";
$pro_mysql_db="dtc";

$usages = "Usage: php4 backup.php [ options ] db_password

Options are:
  -h host (default to localhost)
  -u user (default to root)
  -d database (default to dtc)
";

if($argc !=2 && $argc !=4 && $argc !=6 && $argc !=8)    die($usages);
$pro_mysql_pass = $argv[$argc-1];
if($argc > 2){
        switch($argv[1]){
        case "-h":      $pro_mysql_host = $argv[3];
                        break;
        case "-u":      $pro_mysql_login = $argv[3];
                        break;
        case "-d":      $pro_mysql_db = $argv[3];
                        break;
        default:        die($usages);
        }
}
if($argc > 4){
        switch($argv[4]){
        case "-h":      $pro_mysql_host = $argv[5];
                        break;
        case "-u":      $pro_mysql_login = $argv[5];
                        break;
        case "-d":      $pro_mysql_db = $argv[5];
                        break;
        default:        die($usages);
        }
}
if($argc > 6){
        switch($argv[6]){
        case "-h":      $pro_mysql_host = $argv[7];
                        break;
        case "-u":      $pro_mysql_login = $argv[7];
                        break;
        case "-d":      $pro_mysql_db = $argv[7];
                        break;
        default:        die($usages);
        }
}

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
		if(mysql_field_name($res,$i) == $field){
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
		if($a["Key_name"] == $key){
			mysql_free_result($res);
			return true;
		}
	}
	mysql_free_result($res);
	return false;
}

$nbr_tables = sizeof($dtc_database);
echo "$nbr_tables tables:";
$tblnames = array_keys($dtc_database);
for($i=0;$i<$nbr_tables;$i++){
	echo " ".$tblnames[$i];
	if( !mysql_table_exists($tblnames[$i]) ){
		$q = "CREATE TABLE ".$tblnames[$i]."(
  ".$varnames[0]." ".$allvars[$varnames[0]]."
);\n";
		echo $q;
	}

	$allvars = $dtc_database[$tblnames[$i]]["vars"];
	$varnames = array_keys($allvars);
	$numvars = sizeof($allvars);
	for($j=0;$j<$numvars;$j++){
		if(!findFieldInTable($tblnames[$i],$varnames[$j])){
			$q = "ALTER TABLE ".$tblnames[$i]." ADD ".$varnames[$j]." ".$allvars[$varnames[$j]]." ;";
			echo "$q\n";
		}
	}

	$allvars = $dtc_database[$tblnames[$i]]["keys"];
	$varnames = array_keys($allvars);
	$numvars = sizeof($allvars);
	for($j=0;$j<$numvars;$j++){
		if(!findKeyInTable($tblnames[$i],$varnames[$j])){
			if($varnames[$j] == "PRIMARY")
				$var_2_add = "PRIMARY KEY";
			else
				$var_2_add = "UNIQUE KEY ".$varnames[$j];
			$q = "ALTER INGNORE TABLE ".$tblnames[$i]." ADD $var_2_add ".$allvars[$varnames[$j]].";";
			echo "$q\n";
		}
	}


}
echo "\n";

?>