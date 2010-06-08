#!/usr/bin/env php
<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());


$script_start_time = time();
$start_stamps = mktime();
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

function cleanTempFolder ($subdomain_path){
	if( is_dir("$subdomain_path/tmp") ){
		$cmd = "find $subdomain_path/tmp -iname 'sess*' -mtime +2 -exec rm {} \\;";
		echo $cmd."\n";
		exec($cmd);
	}
}

function cleanSPAMFolder($path){
	global $conf_spam_keep_days;
	if( is_dir($path) ){
		$cmd = "if [ -f \"$path/courierimapacl\" ] ; then touch \"$path/courierimapacl\" ; fi";
		echo $cmd."\n";
		exec($cmd);
		$cmd = "find '$path' -type f -mtime +$conf_spam_keep_days -exec rm {} \\;";
		echo $cmd."\n";
		exec($cmd);
	}
}

function daily_maintenance (){
	global $pro_mysql_admin_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_pop_table;

	$q = "SELECT $pro_mysql_admin_table.adm_login,$pro_mysql_admin_table.path,$pro_mysql_subdomain_table.subdomain_name,$pro_mysql_subdomain_table.domain_name
	FROM $pro_mysql_admin_table,$pro_mysql_domain_table,$pro_mysql_subdomain_table
	WHERE $pro_mysql_domain_table.owner = $pro_mysql_admin_table.adm_login
	AND $pro_mysql_subdomain_table.domain_name = $pro_mysql_domain_table.name";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$adm_path = $a["path"];
		$domain = $a["domain_name"];
		$subdomain = $a["subdomain_name"];
		$path = "$adm_path/$domain/subdomains/$subdomain";
		cleanTempFolder ($path);
	}

	$q = "SELECT $pro_mysql_pop_table.fullemail,$pro_mysql_admin_table.adm_login,$pro_mysql_admin_table.path,$pro_mysql_pop_table.id,$pro_mysql_pop_table.spam_mailbox,$pro_mysql_pop_table.mbox_host
	FROM $pro_mysql_admin_table,$pro_mysql_domain_table,$pro_mysql_subdomain_table,$pro_mysql_pop_table
	WHERE $pro_mysql_domain_table.owner = $pro_mysql_admin_table.adm_login
	AND $pro_mysql_pop_table.mbox_host = $pro_mysql_domain_table.name
	GROUP BY $pro_mysql_pop_table.fullemail";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$adm_path = $a["path"];
		$domain_name = $a["mbox_host"];
		$boxid = $a["id"];
		$spambox_name = $a["spam_mailbox"];
		$path = "$adm_path/$domain_name/Mailboxs/$boxid/Maildir/.$spambox_name";
		echo "Cleaning $path\n";
		cleanSPAMFolder($path);
	}
}

daily_maintenance();

?>
