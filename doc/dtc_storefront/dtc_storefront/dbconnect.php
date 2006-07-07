<?php

$mysql_host = "localhost";
$mysql_user = "root";
$mysql_pass = "XXXXXXXXXXXXXXX";
$mysql_db = "dtc";

mysql_connect($mysql_host, $mysql_user, $mysql_pass)or die ("Cannot connect to $mysql_host");
mysql_select_db($mysql_db)or die ("Cannot select db: $mysql_db");

function fetchAllRawsInArray($query_string){
	$result = mysql_query($query_string) or die("Cannot query : \"$query_string\" !".mysql_error());
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$table[] = mysql_fetch_array($result);
	}
	return $table;
}

?>
