#!/usr/bin/env php
<?php

// This tiny script will add a hash key to all of your old tickets that were comming from
// a DTC version prior 0.30.1 (git version of may 2009)
// Drop this file in /usr/share/dtc/admin, then run php fix_tickets.php and you are good.

$script_start_time = time();
$start_stamps = mktime();
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE initial_ticket='yes';";
$r = mysql_query($q);
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$a = mysql_fetch_array($r);
	$qz = "UPDATE $pro_mysql_tik_queries_table SET hash='".createSupportHash()."' WHERE id='".$a["id"]."';";
	$rz = mysql_query($qz);
}

?>