#!/usr/bin/env php
<?php

$pro_mysql_host="localhost";
$pro_mysql_login="root";
$pro_mysql_db="dtc";

$usages = "Usage: php backup.php [ options ] db_password

Options are:
  -h host (default to localhost)
  -u user (default to root)
  -d database (default to dtc)
";

if($argc !=2 && $argc !=4 && $argc !=6 && $argc !=8)	die($usages);
$pro_mysql_pass = $argv[$argc-1];
if($argc > 2){
	switch($argv[1]){
	case "-h":	$pro_mysql_host = $argv[3];
			break;
	case "-u":	$pro_mysql_login = $argv[3];
			break;
	case "-d":	$pro_mysql_db = $argv[3];
			break;
	default:	die($usages);
	}
}
if($argc > 4){
	switch($argv[4]){
	case "-h":	$pro_mysql_host = $argv[5];
			break;
	case "-u":	$pro_mysql_login = $argv[5];
			break;
	case "-d":	$pro_mysql_db = $argv[5];
			break;
	default:	die($usages);
	}
}
if($argc > 6){
	switch($argv[6]){
	case "-h":	$pro_mysql_host = $argv[7];
			break;
	case "-u":	$pro_mysql_login = $argv[7];
			break;
	case "-d":	$pro_mysql_db = $argv[7];
			break;
	default:	die($usages);
	}
}

mysql_connect("$pro_mysql_host", "$pro_mysql_login", "$pro_mysql_pass")or die ("Cannot connect to $pro_mysql_host");
mysql_select_db("$pro_mysql_db")or die ("Cannot select db: $pro_mysql_db");

$result = mysql_list_tables($pro_mysql_db);

if (!$result) {
   echo "DB Error, could not list tables\n";
   echo 'MySQL Error: ' . mysql_error();
   exit;
}

$out .= "<?php
// Automatic database array generation for DTC
// Generation date: ".date("Y-m(M)-d l H:i")."
\$dtc_database = array(
\"version\" => \"1.0.0\",
\"tables\" => array(\n";
$num = mysql_num_rows($result);
for($j=0;$j<$num;$j++){
	$row = mysql_fetch_row($result);
	$out .= "\t\"$row[0]\" => array(\n";
	$q = "DESCRIBE $row[0];";
	$r = mysql_query($q)or die("Cannot query \"$q\" !\nError in ".__FILE__." line ".__LINE__.": ".mysql_error());
	$n = mysql_num_rows($r);
	$out .= "\t\t\"vars\" => array(\n";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
//		echo $a['Field'].": ".$a['Type']."\n";
		$out .= "\t\t\t\"".$a['Field']."\" => \"".$a['Type'];
		if($a['Null'] == 'YES')
			$out .= " NULL";
		else
			$out .= " NOT NULL";
		if($a['Default'] != NULL)
			$out .= " default '".$a['Default']."'";
		$out .= " ".$a['Extra']."\"";
		if($i < $n-1)
			$out .= ",\n";
		else
			$out .= "\n\t\t\t)";
	}
	$q = "SHOW INDEX FROM $row[0];";
        $r = mysql_query($q)or die("Cannot query \"$q\" !\nError in ".__FILE__." line ".__LINE__.": ".mysql_error());
        $n = mysql_num_rows($r);
	if($i > 0){
		$out .= ",\n";
		// Get all the keys and index in memory for the given table
		unset($primaries);
		unset($keys);
		unset($indexes);
		for($i=0;$i<$n;$i++){
        	        $a = mysql_fetch_array($r);
			if($a['Key_name'] == "PRIMARY"){
				$primaries[] = $a['Column_name'];
			}else{
				if($a['Non_unique'] == "0"){
					$keys[ $a['Key_name'] ][] = $a['Column_name'];
				}else{
					$indexes[ $a['Key_name'] ][] = $a['Column_name'];
				}
			}
		}
		// Produce the array of index and keys
		if(sizeof($primaries) > 0){
			$out .= "\t\t\"primary\" => \"(".$primaries[0];

			// Display all the keys here...
			for($i=1;$i<sizeof($primaries);$i++){
				$out .= ",".$primaries[$i];
			}
			$out .= ")\"";
			if(sizeof($keys) > 0 || sizeof($indexes) > 0){
				$out .= ",\n";
			}else{
				$out .= "\n";
			}
		}
		if(sizeof($keys) > 0){
			$out .= "\t\t\"keys\" => array(\n";
			
			// Backup all the UNIC keys here
			$kkeys = @array_keys($keys);
			for($i=0;$i<sizeof($kkeys);$i++){
				$cur = $keys[ $kkeys[$i] ];
				$out .= "\t\t\t\"".$kkeys[$i]."\" => \"(";
				for($k=0;$k<sizeof($cur);$k++){
					if($k>0)	$out .= ",";
					$out .= $cur[$k];
				}
				if($i<sizeof($kkeys)-1)
					$out .= ")\",\n";
				else
					$out .= ")\"\n";
			}
			
			$out .= "\t\t\t)";
			if(sizeof($indexes) > 0){
				$out .= ",\n";
			}else{
				$out .= "\n";
			}
		}
		if(sizeof($indexes) > 0){
			$out .= "\t\t\"index\" => array(\n";
			
			// Backup all the INDEX keys here
			$kkeys = @array_keys($indexes);
			for($i=0;$i<sizeof($kkeys);$i++){
				$cur = $indexes[ $kkeys[$i] ];
				$out .= "\t\t\t\"".$kkeys[$i]."\" => \"(";
				for($k=0;$k<sizeof($cur);$k++){
					if($k>0)	$out .= ",";
					$out .= $cur[$k];
				}
				if($i<sizeof($kkeys)-1)
					$out .= ")\",\n";
				else
					$out .= ")\"\n";
			}


			$out .= "\t\t\t)\n";
		}
	}else{
		$out .= "\n";
	}
	if($j < $num-1)
		$out .= "\t\t),\n";
	else
		$out .= "\t\t)\n";
	mysql_free_result($r);
}
$out .= "\t)\n);\n?>\n";
mysql_free_result($result);

$fp = fopen("dtc_db.php","w+b");
fwrite($fp,$out);
fclose($fp);

?>