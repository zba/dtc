<?php

require_once "$dtcshared_path/cc_code_popup.php";

////////////////////////////////////////////////////////////////////////////////////
// Verify that someone is not trying to modify another account (nasty hacker !!!) //
// Fetch the admin real path stored in the database
//
////////////////////////////////////////////////////////////////////////////////////
function checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name){
	global $pro_mysql_admin_table;
	global $pro_mysql_domain_table;
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)      die("User or password is incorrect !");

	$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login' AND name='$domain_name';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("Cannot update DNS or MX the user does not own the domain name !");
}
////////////////////////////////////////////////////////
// Some ereg check functions to be sure of all inputs //
////////////////////////////////////////////////////////
// This is the RFC ereg as seen in most servers...
// Todo: extract rulles for other functions.
// $reg = '^(([^<>;()[\]\\.,;:@"]+(\.[^<>()[\]\\.,;:@"]+)*)|(".+"))@((([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$';
function isIP($ip){
	$reg = "^([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}\$";
	if(!ereg($reg,$ip))	return false;
	else			return true;
}

// The subdomain string allowed to be hosted by DTC
function checkSubdomainFormat($name){
	if(ereg("^([a-z0-9]+)([.a-z0-9-]*)([a-z0-9]+)\$",$name))
		return true;
	else{
		if(ereg("^([a-z0-9])\$",$name))
			return true;
		else
			return false;
	}
}

// Check for email addr we allow to create using DTC
function isMailbox($mailbox){
	$reg = "^([a-z0-9]+)([.a-z0-9-]+)\$";
	if(!ereg($reg,$mailbox))	return false;
	else			return true;
}

// Check for valid (but maybe non-RFC) email addr we allow forwarding to
function isValidEmail($email){
	$reg = "^([a-z0-9]+)([_.a-z0-9-]+)@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+)\$";
	if(!ereg($reg,$email))	return false;
	else			return true;
}

function isHostnameOrIP($hostname){
	$reg = '^((([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$';
	if(!ereg($reg,$hostname))	return false;
	else			return true;
}

function isHostname($hostname){
	$reg = '^((([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$';
	if(!ereg($reg,$hostname))	return false;
	else			return true;
}

// Check for email addr we allow to create using DTC
function isFtpLogin($mailbox){
	$reg = "^([a-z0-9]+)([.a-z0-9-]+)\$";
	if(!ereg($reg,$mailbox))	return false;
	else			return true;
}

function isDTCPassword($pass){
	$reg = "^([a-zA-Z0-9]){4,16}\$";
	if(!ereg($reg,$pass))	return false;
	else			return true;
}

/////////////////////////////////////////////////
// Create mailbox direcotry if does not exists //
/////////////////////////////////////////////////
function mk_Maildir($mailbox_path){
		if(!file_exists("$mailbox_path/Maildir"))
			mkdir("$mailbox_path/Maildir", 0755);
		if(!file_exists("$mailbox_path/Maildir/cur"))
			mkdir("$mailbox_path/Maildir/cur", 0755);
		if(!file_exists("$mailbox_path/Maildir/new"))
			mkdir("$mailbox_path/Maildir/new", 0755);
		if(!file_exists("$mailbox_path/Maildir/tmp"))
			mkdir("$mailbox_path/Maildir/tmp", 0755);
}

///////////////////////////////////////////////////////////
// Update the "cron_job" table so when the cron.php will //
// do what we ask.                                       //
///////////////////////////////////////////////////////////
function updateUsingCron($changes){
	global $pro_mysql_cronjob_table;
	// Tell the cron job to activate the changes
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET $changes WHERE 1;";
	mysql_query($adm_query);
}

// Return the path of one admin giving his path as argument
function getAdminPath($adm_login){
	global $pro_mysql_admin_table;

	// We have now to get the user directory and use it ! :)
	$query = "SELECT path FROM $pro_mysql_admin_table WHERE adm_login='$adm_login'";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$testnum_rows = mysql_num_rows($result);
	if($testnum_rows != 1){
		die("Cannot fetch user to get his path !!!");
	}
	$row = mysql_fetch_array($result);
	return $row["path"];
}


///////////////////////////////////////////////////////////////////////
// Bellow are functions needed by client interface if dtcrm is added //
// and must be present in admin (even without dtcrm)                 //
///////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////
// Make a domain directory, giving it's path in argument //
///////////////////////////////////////////////////////////
function make_new_adm_domain_dir($path){
	global $console;
	// Create subdirectorys
	$oldumask = umask(0);
	if(!file_exists("$path")){
		mkdir("$path", 0750);
		$console .= "mkdir $path;<br>";
	}

	if(!file_exists("$path/Mailboxs")){
		mkdir("$path/Mailboxs", 0750);
		$console .= "mkdir $path/mailbox;<br>";
	}

	if(!file_exists("$path/mysql")){
		mkdir("$path/mysql", 0750);
		$console .= "mkdir $path/mysql;<br>";
	}

	if(!file_exists("$path/subdomains")){
		mkdir("$path/subdomains", 0750);
		$console .= "mkdir $path/subdomains;<br>";
	}

	if(!file_exists("$path/subdomains/www")){
		mkdir("$path/subdomains/www", 0750);
		$console .= "mkdir $path/subdomains/www;<br>";
	}

	if(!file_exists("$path/subdomains/www/cgi-bin")){
		mkdir("$path/subdomains/www/cgi-bin", 0750);
		$console .= "mkdir $path/subdomains/www/cgi-bin;<br>";
	}

	if(!file_exists("$path/subdomains/www/html")){
		mkdir("$path/subdomains/www/html", 0750);
		$console .= "mkdir $path/subdomains/www/html;<br>";
	}

	if(!file_exists("$path/subdomains/www/logs")){
		mkdir("$path/subdomains/www/logs", 0750);
		$console .= "mkdir $path/mailbox;<br>";
	}
	umask($oldumask);
}
///////////////////////////////
// Add a domain to one admin //
///////////////////////////////
function addDomainToUser($adm_login,$adm_pass,$domain_name){
	global $pro_mysql_admin_table;
	global $conf_demo_version;
	global $pro_mysql_domain_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_cronjob_table;
	global $conf_main_site_ip;

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
	$result = mysql_query($query)or die("Cannot query : \"$query\" !");
	$numrows = mysql_num_rows($result);
	if($numrows != 1){
		die("Cannot fetch admin path !");
	}
	$row = mysql_fetch_array($result);
	$admin_path = $row["path"];

	// Create subdirectorys & html front page
	if($conf_demo_version == "no"){
		make_new_adm_domain_dir("$admin_path/$domain_name");
		system ("cp -rf /usr/share/dtc/shared/template/* $admin_path/$domain_name/subdomains/www/html");
	}

	// Create domain in database
	$domupdate_query = "INSERT INTO $pro_mysql_domain_table (name,owner,default_subdomain,ip_addr) VALUES ('".$domain_name."','$adm_login','www','".$conf_main_site_ip."');";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	// Create default domain www
	$adm_query = "INSERT INTO $pro_mysql_subdomain_table (id,domain_name,subdomain_name,path) VALUES ('','".$domain_name."','www','www');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!".mysql_error());

	// Tell the cron job to activate the changes
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes',restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes' WHERE 1;";
	mysql_query($adm_query);
}

function smartByte($bytes){
	if($bytes>1024*1024*1024)	return round(($bytes / 1073741824),3) ."GBytes";
	if($bytes>1024*1024)		return round(($bytes / 1048567),3) ." MBytes";
	if($bytes>1024)			return round(($bytes / 1024),3) ." kBytes";
	return $bytes." Bytes";
}

?>
