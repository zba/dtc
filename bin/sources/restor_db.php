<?php

echo "==> Restor DB script for DTC\n";

$pro_mysql_host="localhost";
$pro_mysql_login="root";
$pro_mysql_db="dtc";

$usages = "Usage: php4 restor_db.php [ options ] db_password

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

require("dtc_db.php");

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
			$q = "ALTER TABLE ".$tblnames[$i]." ADD ".$varnames[$j]." ".$allvars[$varnames[$j]]." ;";
//			echo "$q\n";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		}
	}

	$allvars = $tables[$tblnames[$i]]["keys"];
	$numvars = sizeof($allvars);
	$varnames = array_keys($allvars);
	for($j=0;$j<$numvars;$j++){
		if(!findKeyInTable($tblnames[$i],$varnames[$j])){
			if($varnames[$j] == "PRIMARY")
				$var_2_add = "PRIMARY KEY";
			else
				$var_2_add = "UNIQUE KEY ".$varnames[$j];
			$q = "ALTER TABLE ".$tblnames[$i]." ADD $var_2_add ".$allvars[$varnames[$j]].";";
//			echo "$q\n";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		}
	}
}
echo "\n";

?>

