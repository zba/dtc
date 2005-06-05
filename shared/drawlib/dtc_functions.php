<?php

function logPay($txt){
	$fp = fopen("/tmp/paylog.txt","a");
	fwrite($fp,$txt."\n");
	fclose($fp);
	echo $txt."<br>";
}

function remove_url_protocol($url){
	if(strstr($url,"http://")){
		return substr($url,7);
	}else if(strstr($url,"https://")){
		return substr($url,8);
	}else
		echo "ERROR: no protocol in distant mail server addr!";
	return false;
}

function getRandomValue(){
	// seed with microseconds
	list($usec, $sec) = explode(' ', microtime());
	$seed = (float) $sec + ((float) $usec * 100000);
	// Randomise
	mt_srand($seed);
	// And get a value
	$rand = mt_rand(0,999999999);
	return $rand;
}

////////////////////////////////////////////////////////////////////////////////////
// Verify that someone is not trying to modify another account (nasty hacker !!!) //
// Fetch the admin real path stored in the database
//
////////////////////////////////////////////////////////////////////////////////////
function checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name){
	global $pro_mysql_admin_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_config_table;

	if(strlen($adm_pass) > 16){
	}

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1){
		$query = "SELECT * FROM $pro_mysql_config_table WHERE root_admin_random_pass='$adm_pass' AND pass_expire > '".mktime()."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows != 1){
			die("User or password is incorrect !");
		}
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows != 1){
			die("User or password is incorrect !");
		}
	}

	$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login' AND name='$domain_name';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("Cannot update: you are trying to do something on a domain name you don't own!");
}

function checkLoginPass($adm_login,$adm_pass){
	global $pro_mysql_admin_table;

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1){
		$query = "SELECT * FROM $pro_mysql_config_table WHERE root_admin_random_pass='$adm_pass' AND pass_expire > '".mktime()."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows != 1){
			die("User or password is incorrect !");
		}
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows != 1){
			die("User or password is incorrect !");
		}
	}
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
	if(!ereg($reg,$hostname) && !isIP($hostname))	return false;
	else			return true;
}

function isHostname($hostname){
	$reg = '^((([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$';
//	$reg = '^(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)$';
//	$reg = "^([.a-z0-9-]+)\$";
	if(!ereg($reg,$hostname))	return false;
	else			return true;
}

// Check for email addr we allow to create using DTC
function isFtpLogin($mailbox){
	if(isValidEmail($mailbox))	return true;
	$reg = "^([a-z0-9]+)([.a-z0-9-]+)\$";
	if(!ereg($reg,$mailbox))	return false;
	else			return true;
}

// Check for validity of a database name
function isDatabase($db){
	$reg = "^([a-z]+)([a-z]+)\$";
	if(!ereg($reg,$db))	return false;
	else			return true;
}

// Check any mail password for another server
function isMailPassword($login){
//	$reg = '^([<>()\\\/\?_\[;,;:%\^@"!a-zA-Z0-9-]){4,16}$';
	$reg = "^([_.a-zA-Z0-9-]){1,64}\$";
        if(!ereg($reg,$pass))   return false;
	else                    return true;
}

function isDTCPassword($pass){
	$reg = "^([a-zA-Z0-9]){4,16}\$";
	if(!ereg($reg,$pass))	return false;
	else			return true;
}

// Check if it's only numbers
function isRandomNum($mailbox){
	$reg = "^([0-9]+)\$";
	if(!ereg($reg,$mailbox))        return false;
	else                    return true;
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

// This function should be called whenever any domain is added to NS or MX,
// so that backup server can update the domain-list of this server.
function triggerDomainListUpdate(){
	global $pro_mysql_backup_table;

	$q = "UPDATE $pro_mysql_backup_table SET status='pending' WHERE type='trigger_changes';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
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
function addDomainToUser($adm_login,$adm_pass,$domain_name,$domain_password=""){
	global $pro_mysql_admin_table;
	global $conf_demo_version;
	global $pro_mysql_domain_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_cronjob_table;
	global $conf_main_site_ip;
	global $conf_chroot_path;
	global $conf_generated_file_path;

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query($query)or die("Cannot query : \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$numrows = mysql_num_rows($result);
	if($numrows != 1){
		die("Cannot fetch admin path line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	$row = mysql_fetch_array($result);
	$admin_path = $row["path"];

	// Create subdirectorys & html front page
	if($conf_demo_version == "no"){
		make_new_adm_domain_dir("$admin_path/$domain_name");
		exec("cp -fulpRv $conf_chroot_path/* $admin_path/$domain_name/subdomains/www");
		system ("cp -rup $conf_generated_file_path/template/* $admin_path/$domain_name/subdomains/www/html");
	}

	// Create domain in database
	$domupdate_query = "INSERT INTO $pro_mysql_domain_table (name,owner,default_subdomain,ip_addr,registrar_password) VALUES ('".$domain_name."','$adm_login','www','".$conf_main_site_ip."','$domain_password');";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"! line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	// Create default domain www
	$adm_query = "INSERT INTO $pro_mysql_subdomain_table (id,domain_name,subdomain_name,path) VALUES ('','".$domain_name."','www','www');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!".mysql_error());

	// Tell the cron job to activate the changes
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes',restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes' WHERE 1;";
	mysql_query($adm_query);
}

function drawPercentBar($value,$max,$double="yes"){
	$alts = "";
	$altn = "";
	if($double == "yes")	$dbl = 2;
	else	$dbl = 1;
	if($max != 0){
		$percent = $value * $dbl * 100 / $max;
		if($percent < 0)	$percent = 0;
		if($percent > $dbl * 100)	$percent = 100 * $dbl;
		$percent_val = round($percent/$dbl,2);
		$percent_graf = round($percent);
		$percent_graf2 = ($dbl * 100) - $percent_graf;
	}else{
		$percent_val = 0;
		$percent_graf = 0;
		$percent_graf2 = 0;
		$percent = 0;
	}
	for($i=0;$i<200;$i+=5){
		if($i < $percent_graf){
			$alts .= "*";
		}else{
			$altn .= "-";
		}
	}

	if($percent_graf < (60*$dbl)){
		$color = "green";
	}else if($percent_graf < (75*$dbl)){
		$color = "yellow";
	}else if($percent_graf < (90*$dbl)){
		$color = "orange";
	}else{
		$color = "red";
	}

	$table = "<table cellpadding=\"0\" cellspacing=\"0\" height=\"1\">
<tr>
	<td width=\"2\" height=\"13\"><img width=\"2\" height=\"13\" src=\"gfx/bar/start.gif\"></td>
	<td width=\"$percent_graf\" height=\"13\" background=\"gfx/bar/middle_$color.gif\"><img width=\"$percent_graf\" alt=\"$alts\" height=\"13\" src=\"gfx/bar/middle_$color.gif\"></td>
	<td width=\"$percent_graf2\" height=\"13\" background=\"gfx/bar/middle_umpty.gif\"><img width=\"$percent_graf2\" alt=\"$altn\" height=\"13\" src=\"gfx/bar/middle_umpty.gif\"></td>
	<td width=\"2\" height=\"13\"><img width=\"2\" height=\"13\" src=\"gfx/bar/end.gif\"></td>
	<td>".$percent_val."%</td></tr>
</table>";
	return $table;
}

function smartDate($date){
	$out = "";
	$ar = explode("-",$date);
	if($ar[0] > 0 ){
		$plop = $ar[0] +1;
		$plop -= 1;
		$out .= $plop." year";
		if($ar[0] > 1)	$out .= "s";
	}
	if($ar[1] > 0 ){
		$out .= $ar[1]." month";
		if($ar[1] > 1)	$out .= "s";
	}
	if($ar[2] > 0 ){
		$out .= $ar[2]." day";
		if($ar[2] > 1)	$out .= "s";
	}
	return $out;
}

function smartByte($bytes){
	if($bytes>1024*1024*1024)	return round(($bytes / (1024*1024*1024)),3) ." GBytes";
	if($bytes>1024*1024)		return round(($bytes / (1024*1024)),3) ." MBytes";
	if($bytes>1024)				return round(($bytes / 1024),3) ." kBytes";
	return $bytes." Bytes";
}

?>
