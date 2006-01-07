<?php

$script_start_time = time();
$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");


function make_stats(){
	global $conf_mysql_db;
	global $pro_mysql_domain_table;
	global $dtcshared_path;

	$q = "SELECT admin.adm_login,admin.path,domain.name
	FROM admin,domain,subdomain
	WHERE domain.owner=admin.adm_login
	AND subdomain.domain_name=domain.name 
	AND subdomain.webalizer_generate='yes'
	AND subdomain.ip='default'
	AND subdomain.generate_vhost='yes'
	ORDER BY admin.adm_login,domain.name";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		echo "<pre>";
		print_r($a);
		echo "</pre>";
		die();
	}
//	mysql_select_db("apachelogs");

}

?>
