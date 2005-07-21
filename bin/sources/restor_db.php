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
			$r = mysql_query($q)or print("\nCannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error()."\n");
		}
	}

	// THIS CODE IS TO BE REWRITTED WITH THE NEWER PRIMARY KEY STUFF. IF YOU HAVE TIME, PLEASE DO IT!
	$allvars = $tables[$tblnames[$i]]["keys"];
	$numvars = sizeof($allvars);
	if($numvars > 0){
		$varnames = array_keys($allvars);
		for($j=0;$j<$numvars;$j++){
			if(!findKeyInTable($tblnames[$i],$varnames[$j])){
				if($varnames[$j] == "PRIMARY")
					$var_2_add = "PRIMARY KEY";
				else
					$var_2_add = "UNIQUE KEY ".$varnames[$j];
				$q = "ALTER TABLE ".$tblnames[$i]." ADD $var_2_add ".$allvars[$varnames[$j]].";";
//				echo "$q\n";
				$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
			}
		}
	}

	$allvars = $tables[$tblnames[$i]]["index"];
	$numvars = sizeof($allvars);
	if($numvars > 0){
		$varnames = array_keys($allvars);
		for($j=0;$j<$numvars;$j++){
			// We have to rebuild indexes in order to get rid of past mistakes: this should go away when releasing v1.0
			if(findKeyInTable($tblnames[$i],$varnames[$j])){
				$q = "ALTER TABLE ".$tblnames[$i]." DROP INDEX ".$varnames[$j]."";
				$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
			}
			$q = "ALTER TABLE ".$tblnames[$i]." ADD INDEX ".$varnames[$j]." ".$allvars[$varnames[$j]].";";
			$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
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

?>
