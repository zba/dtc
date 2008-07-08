<?php

function domainNamePopup($domain_name=""){
	$out = "";

	$allTLD = array(".com", ".aero", ".asia", ".biz", ".cat", ".coop", ".edu", ".gov", ".info", ".int", ".jobs", ".mil", ".mobi", ".museum", ".name", ".net",".org", ".pro", ".tel", ".travel",
".ac",".ad",".ae",".af",".ag",".ai",".al",".am",".an",".ao",".aq",".ar",".as",".at",".au",".aw",".ax",".az",
".ba",".bb",".bd",".be",".bf",".bg",".bh",".bi",".bj",".bm",".bn",".bo",".br",".bs",".bt",".bw",".by",".bz",
".ca",".cc",".cd",".cf",".cg",".ch",".ci",".ck",".cl",".cm",".cn",".co",".cr",".cu",".cv",".cx",".cy",".cz",
".de",".dj",".dk",".dm",".do",".dz",
".ec",".ee",".eg",".er",".es",".et",".eu",
".fi",".fj",".fk",".fm",".fo",".fr",
".ga",".gb",".gd",".ge",".gf",".gg",".gh",".gi",".gl",".gm",".gn",".gp",".gq",".gr",".gs",".gt",".gu",".gw",".gy",
".hk",".hm",".hn",".hr",".ht",".hu",
".id",".ie",".il",".im",".in",".io",".iq",".ir",".is",".it",
".je",".jm",".jo",".jp",
".ke",".kg",".kh",".ki",".km",".kn",".kp",".kr",".kw",".ky",".kz",
".la",".lb",".lc",".li",".lk",".lr",".ls",".lt",".lu",".lv",".ly",
".ma",".mc",".md",".me",".mg",".mh",".mk",".ml",".mm",".mn",".mo",".mp",".mq",".mr",".ms",".mt",".mu",".mv",".mw",".mx",".my",".mz",
".na",".nc",".ne",".nf",".ng",".ni",".nl",".no",".np",".nr",".nu",".nz",".nc",
".om",
".pa",".pe",".pf",".pg",".ph",".pk",".pl",".pm",".pn",".pr",".ps",".pt",".pw",".py",
".qa",
".re",".ro",".rs",".ru",".rw",
".sa",".sb",".sc",".sd",".se",".sg",".sh",".si",".sk",".sl",".sm",".sn",".sr",".st",".sv",".sy",".sz",
".tc",".td",".tf",".tg",".th",".tj",".tk",".tl",".tm",".tn",".to",".tr",".tt",".tv",".tw",".tz",
".ua",".ug",".ac.uk",".co.uk",".gov.uk",".ltd.uk",".me.uk",".mod.uk",".net.uk",".org.uk",".plc.uk",".sch.uk",".us",".uy",".uz",
".va",".vc",".ve",".vg",".vi",".vn",".vu",
".wf",".ws",
".ye",".yt",".yu",
".za",".zm",".zw");

	$keys = array_keys($allTLD);
	$nbr_tld = sizeof($allTLD);
	for($i=0;$i<$nbr_tld;$i++){
		if( ereg("\\".$keys[$i]."\$",$domain_name)){
			$selected = " selected ";
		}else{
			$selected = "";
		}
		$out .= "<option value=\"".$keys[$i]."\" $selected>". $keys[$i] ."</option>";
	}
	return $out;
}

function vpsLocationSelector(){
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;

	$q = "SELECT $pro_mysql_vps_server_table.hostname,$pro_mysql_vps_server_table.location
	FROM $pro_mysql_vps_ip_table,$pro_mysql_vps_server_table
	WHERE $pro_mysql_vps_ip_table.vps_server_hostname=$pro_mysql_vps_server_table.hostname
	AND $pro_mysql_vps_ip_table.available='yes'
	GROUP BY $pro_mysql_vps_server_table.location;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$vps_location_popup = "<option value=\"-1\">Please select!</optioon>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if(isset($_REQUEST["vps_server_hostname"]) && $_REQUEST["vps_server_hostname"] == $a["hostname"]){
			$selected = " selected ";
		}else{
			$selected = "";
		}
		$vps_location_popup .= "<option value=\"".$a["hostname"]."\" $selected>".$a["location"]."</optioon>";
	}
	return $vps_location_popup;
}

function findLastUsedLangByUser($adm_login){
	global $pro_mysql_admin_table;
	global $pro_mysql_new_admin_table;

	$q = "SELECT last_used_lang FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$a = mysql_fetch_array($r);
		return $a["last_used_lang"];
	}else{
		$q = "SELECT last_used_lang FROM $pro_mysql_new_admin_table WHERE reqadm_login='$adm_login';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n > 0){
			$a = mysql_fetch_array($r);
			return $a["last_used_lang"];
		}else{
			return false;
		}
	}
}

function findInvoicingCompany ($service_location,$client_country_code){
	global $pro_mysql_invoicing_table;
	global $conf_default_company_invoicing;

	$q = "SELECT * FROM $pro_mysql_invoicing_table WHERE service_country_code='$service_location';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$a = mysql_fetch_array($r);
		$company_id = $a["company_id"];
	}else{
		$q = "SELECT * FROM $pro_mysql_invoicing_table WHERE customer_country_code='$client_country_code';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n > 0){
			$a = mysql_fetch_array($r);
			$company_id = $a["company_id"];
		}else{
			$company_id = $conf_default_company_invoicing;
		}	
	}
	return $company_id;
}

function mdpauto(){
	srand((double) microtime()*1000000);
	//This pools grant no mistake between 0, o or O for example...
	$pool = "abcdefghjkmnprstwxyz234589";
	$sid = "";
	for($index=0;$index<8;$index++){
		$sid .= substr($pool,(rand()%(strlen($pool))),1);
	}
	return $sid;
}

function autoGeneratePassButton($form_name,$field_name){
	global $gfx_icn_path_generate_pass;
	global $gfx_icn_path_seepass;
	global $jscript_gen_autopass;
	$mdp = mdpauto();
	$out = "";
	if(!isset($jscript_gen_autopass) && $jscript_gen_autopass != "yes"){
		$jscript_gen_autopass;
		$out .= "
<script language=\"JavaScript\">
function dtc_gen_passwd(frm_name,fld_name){
	document[frm_name][fld_name].value = '".$mdp."';
	document[frm_name][fld_name].type = 'text';
}
function dtc_see_password(frm_name,fld_name){
	document[frm_name][fld_name].type = 'text';
}
</script>";
	}
	if(isset($gfx_icn_path_generate_pass)){
		$genpath_img = $gfx_icn_path_generate_pass;
	}else{
		$genpath_img = "gfx/generate_pass.png";
	}
	if(isset($gfx_icn_path_seepass)){
		$seepath_img = $gfx_icn_path_seepass;
	}else{
		$seepath_img = "gfx/see_password.png";
	}
	$out .= "<img src=\"$genpath_img\" align=\"absmiddle\" onClick=\"dtc_gen_passwd('".$form_name."','".$field_name."');\" alt=\"GENPASS\">
<img src=\"$seepath_img\" align=\"absmiddle\" onClick=\"dtc_see_password('".$form_name."','".$field_name."');\" alt=\"SEEPASS\">";
	return $out;
}

function HTTP_Post($URL,$data, $referrer=""){
	$result = "";
	// parsing the given URL
	$URL_Info=parse_url($URL);

	// Building referrer
	if($referrer=="") // if not given use this script as referrer
		$referrer=$_SERVER["SCRIPT_URI"];

	// making string from $data
	foreach($data as $key=>$value)
		$values[]="$key=".urlencode($value);
	$data_string=implode("&",$values);

	// Find out which port is needed - if not given use standard (=80)
	if(!isset($URL_Info["port"]))
		$URL_Info["port"]=80;

	// building POST-request:
	$request = "";
	$request.="POST ".$URL_Info["path"]." HTTP/1.1\n";
	$request.="Host: ".$URL_Info["host"]."\n";
	$request.="Referer: $referrer\n";
	$request.="Content-type: application/x-www-form-urlencoded\n";
	$request.="Content-length: ".strlen($data_string)."\n";
	$request.="Connection: close\n";
	$request.="\n";
	$request.=$data_string."\n";

	$fp = fsockopen($URL_Info["host"],$URL_Info["port"]);
	fputs($fp, $request);
	while(!feof($fp)){
		$result .= fgets($fp, 128);
	}
	fclose($fp);
	return $result;
}

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
	global $pro_mysql_config_table;

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
	if($name == ""){
		return false;
	}
	if(ereg("^([a-z0-9\_]+)([.a-z0-9\_-]*)([.a-z0-9]+)\$",$name))
		return true;
	else{
		if(ereg("^([a-z0-9])\$",$name))
			return true;
		else
			return false;
	}
}

// Check if a string is an ssh key
// TO BE DONE!!!
function isSSHKey($ssh_key){
	if(ereg("^ssh-[rd]s[as] ([.a-zA-Z0-9+/=]+)\$",$ssh_key)){
		return true;
	}else{
		return false;
	}
}

// Check for email addr we allow to create using DTC
function isMailbox($mailbox){
	$reg = "^([a-zA-Z0-9])|([a-zA-Z0-9]+)([_.a-zA-Z0-9-]+)\$";
	if(!ereg($reg,$mailbox))	return false;
	else			return true;
}

// Check for valid (but maybe non-RFC) email addr we allow forwarding to
function isValidEmail($email){
	$reg = "^([a-z0-9]+)([_.a-z0-9-]*)@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+)\$";
	if(!ereg($reg,$email))	return false;
	else			return true;
}

function isHostnameOrIP($hostname){
	$reg = '^((([a-z0-9]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$';
	if(!ereg($reg,$hostname) && !isIP($hostname))	return false;
	else			return true;
}

function isHostname($hostname){
	$reg = '^((([a-z0-9]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$';
//	$reg = '^(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)$';
//	$reg = "^([.a-z0-9-]+)\$";
	if(!ereg($reg,$hostname))	return false;
	else			return true;
}

// Check for email addr we allow to create using DTC
function isFtpLogin($mailbox){
	if(isValidEmail($mailbox))	return true;
	$reg = "^([a-zA-Z0-9]+)([.a-zA-Z0-9-]+)\$";
	if(!ereg($reg,$mailbox))	return false;
	else			return true;
}

// Check for validity of a database name
function isDatabase($db){
	$reg = "(^[a-zA-Z0-9]+([\_]*)[a-zA-Z0-9]+)\$";
	if(!ereg($reg,$db))	return false;
	else			return true;
}

// Check any mail password for another server
function isMailPassword($login){
//	$reg = '^([<>()\\\/\?_\[;,;:%\^@"!a-zA-Z0-9-]){4,16}$';
	$reg = "^([_.a-zA-Z0-9-]){1,64}\$";
        if(!ereg($reg,$login))   return false;
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

function triggerMXListUpdate(){
	global $pro_mysql_backup_table;

	$q = "UPDATE $pro_mysql_backup_table SET status='pending' WHERE type='trigger_mx_changes';";
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
function addDedicatedToUser($adm_login,$server_hostname,$product_id){
	global $pro_mysql_product_table;
	global $pro_mysql_dedicated_table;
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='$product_id';";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find product line ".__LINE__." file ".__FILE__);
	}
	$product = mysql_fetch_array($r);
	
	$exp_date = calculateExpirationDate(date("Y-m-d"),$product["period"]);
	$q = "INSERT INTO $pro_mysql_dedicated_table (id,owner,server_hostname,start_date,expire_date,hddsize,ramsize,product_id,bandwidth_per_month_gb )
	VALUES('','$adm_login','$server_hostname','".date("Y-m-d")."','$exp_date','".$product["quota_disk"]."','".$product["memory_size"]."','$product_id','".$product["bandwidth"]."');";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	return ;
}

////////////////////////////
// Add a VPS to one admin //
////////////////////////////
// Redo the list of subscriber to a list: to be called when adding / removing a VPS user
function resubscribe_VPS_server_list_users($list_name){
	global $pro_mysql_vps_server_lists_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_list_table;
	global $pro_mysql_domain_table;

	global $conf_main_domain;

	$q = "SELECT * FROM $pro_mysql_list_table WHERE domain='$conf_main_domain' AND name='$list_name';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());;
	$n = mysql_num_rows($r);
	if($n != 1)	die("Mailing list not found line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$a = mysql_fetch_array($r);

	$q = "SELECT $pro_mysql_admin_table.path FROM $pro_mysql_admin_table,$pro_mysql_domain_table
	WHERE $pro_mysql_domain_table.name='$conf_main_domain'
	AND $pro_mysql_admin_table.adm_login = $pro_mysql_domain_table.owner";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());;
	$n = mysql_num_rows($r);
	if($n != 1)	die("Admin of main domain not found line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$a = mysql_fetch_array($r);
	$path = $a["path"]."/$conf_main_domain/lists/".$conf_main_domain."_".$list_name."/subscribers.d";

	$file_list = array();
	if (is_dir($path)) {
		if ($dh = opendir($path)) {
			while (($file = readdir($dh)) !== false) {
				$fullpath = $path . "/" . $file;
				if(filetype($fullpath) != "dir"){
					$file_list[] = $fullpath;
				}
			}
			closedir($dh);
		}
	}
	$nbr_file = sizeof($file_list);
	for($i=0;$i<$nbr_file;$i++){
		unlink($file_list[$i]);
	}

	$q = "SELECT $pro_mysql_client_table.email
	FROM $pro_mysql_vps_server_lists_table,$pro_mysql_vps_table,$pro_mysql_admin_table,$pro_mysql_client_table
	WHERE $pro_mysql_vps_server_lists_table.list_name = '$list_name'
	AND $pro_mysql_vps_server_lists_table.hostname = $pro_mysql_vps_table.vps_server_hostname
	AND $pro_mysql_admin_table.adm_login = $pro_mysql_vps_table.owner
	AND $pro_mysql_client_table.id = $pro_mysql_admin_table.id_client
	AND $pro_mysql_client_table.email!=''
	GROUP BY $pro_mysql_client_table.email ORDER BY $pro_mysql_client_table.email;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());;
	$n = mysql_num_rows($r);
	$old_file = "";
	$addr_list = "";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$fname = substr($a["email"],0,1);
		if($fname == $old_file || $old_file == ""){
			$addr_list .= $a["email"]."\n";
		}else{
			$fullpath = $path."/".$old_file;
			$fp = fopen($fullpath,"w+");
			fwrite($fp,$addr_list);
			fclose($fp);
			$addr_list = $a["email"]."\n";
		}
		$old_file = $fname;
	}
	if($n > 0){
		$fullpath = $path."/".substr($a["email"],0,1);
		$fp = fopen($fullpath,"w+");
		fwrite($fp,$addr_list);
		fclose($fp);
	}
}

function VPS_Server_Subscribe_To_Lists($vps_server_hostname){
	global $pro_mysql_vps_server_lists_table;
	$q = "SELECT * FROM $pro_mysql_vps_server_lists_table WHERE hostname='$vps_server_hostname';";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		resubscribe_VPS_server_list_users($a["list_name"]);
	}
}

function addVPSToUser($adm_login,$vps_server_hostname,$product_id,$operating_system="debian"){
	global $pro_mysql_product_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_table;
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='$product_id';";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find product line ".__LINE__." file ".__FILE__);
	}
	$product = mysql_fetch_array($r);
	$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE available='yes' AND vps_server_hostname='$vps_server_hostname' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		 die("Cannot find available IP and Xen name in $vps_server_hostname line ".__LINE__." file ".__FILE__);
	}
	$vps_ip = mysql_fetch_array($r);
	$q = "UPDATE $pro_mysql_vps_ip_table SET available='no' WHERE ip_addr='".$vps_ip["ip_addr"]."';";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	$exp_date = calculateExpirationDate(date("Y-m-d"),$product["period"]);
	$q = "INSERT INTO $pro_mysql_vps_table (id,owner,vps_server_hostname,vps_xen_name,start_date,expire_date,hddsize,ramsize,product_id,bandwidth_per_month_gb,operatingsystem)
	VALUES('','$adm_login','".$vps_ip["vps_server_hostname"]."','".$vps_ip["vps_xen_name"]."','".date("Y-m-d")."','$exp_date','".$product["quota_disk"]."','".$product["memory_size"]."','$product_id','".$product["bandwidth"]."','$operating_system');";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	// Subscribe user to the lists of the VPS
	VPS_Server_Subscribe_To_Lists($vps_server_hostname);

	return $vps_ip["vps_xen_name"];
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

	global $conf_root_admin_random_pass;
	global $conf_pass_expire;

	if($conf_root_admin_random_pass == $adm_pass &&  $conf_pass_expire > mktime()){
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	}else{
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	}

	$result = mysql_query($query)or die("Cannot query : \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$numrows = mysql_num_rows($result);
	if($numrows != 1){
		die("Cannot fetch admin path (maybe rotative random password expired) line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	$row = mysql_fetch_array($result);
	$admin_path = $row["path"];

	// Create subdirectorys & html front page
	if($conf_demo_version == "no"){

		if(!file_exists($admin_path)){
			mkdir($admin_path, 0755);
		}

		make_new_adm_domain_dir("$admin_path/$domain_name");
		exec("cp -fulpRv $conf_chroot_path/* $admin_path/$domain_name/subdomains/www");
		// create a link so that the user can log in via SSH to $admin_path or $admin_path/$domain_name
		exec("if [ ! -e $admin_path/$domain_name/bin ]; then ln -s subdomains/www/bin  $admin_path/$domain_name/bin; fi");
		exec("if [ ! -e $admin_path/$domain_name/var ]; then ln -s subdomains/www/var  $admin_path/$domain_name/var; fi");
		exec("if [ ! -e $admin_path/$domain_name/lib ]; then ln -s subdomains/www/lib  $admin_path/$domain_name/lib; fi");
		exec("if [ ! -e $admin_path/$domain_name/lib64 ]; then ln -s subdomains/www/lib  $admin_path/$domain_name/lib64; fi");
		exec("if [ ! -e $admin_path/$domain_name/sbin ]; then ln -s subdomains/www/sbin  $admin_path/$domain_name/sbin; fi");
		exec("if [ ! -e $admin_path/$domain_name/tmp ]; then ln -s subdomains/www/tmp  $admin_path/$domain_name/tmp; fi");
		exec("if [ ! -e $admin_path/$domain_name/usr ]; then ln -s subdomains/www/usr  $admin_path/$domain_name/usr; fi");
		exec("if [ ! -e $admin_path/$domain_name/dev ]; then ln -s subdomains/www/dev  $admin_path/$domain_name/dev; fi");
		exec("if [ ! -e $admin_path/$domain_name/etc ]; then ln -s subdomains/www/etc  $admin_path/$domain_name/etc; fi");

		exec("if [ `uname -m` = \"x86_64\" ] ; then if [ ! -e $admin_path/$domain_name/lib64 ] ; then ln -s subdomains/www/lib $admin_path/$domain_name/lib64 ; fi ; fi");

		// now for the admin user chroot links
		exec("if [ ! -e $admin_path/bin ]; then ln -s $domain_name/subdomains/www/bin  $admin_path/bin; fi");
		exec("if [ ! -e $admin_path/var ]; then ln -s $domain_name/subdomains/www/var  $admin_path/var; fi");
		exec("if [ ! -e $admin_path/lib ]; then ln -s $domain_name/subdomains/www/lib  $admin_path/lib; fi");
		exec("if [ ! -e $admin_path/lib64 ]; then ln -s $domain_name/subdomains/www/lib  $admin_path/lib64; fi");
		exec("if [ ! -e $admin_path/sbin ]; then ln -s $domain_name/subdomains/www/sbin  $admin_path/sbin; fi");
		exec("if [ ! -e $admin_path/tmp ]; then ln -s $domain_name/subdomains/www/tmp  $admin_path/tmp; fi");
		exec("if [ ! -e $admin_path/usr ]; then ln -s $domain_name/subdomains/www/usr  $admin_path/usr; fi");
		exec("if [ ! -e $admin_path/dev ]; then ln -s $domain_name/subdomains/www/dev  $admin_path/dev; fi");
		exec("if [ ! -e $admin_path/etc ]; then ln -s $domain_name/subdomains/www/etc  $admin_path/etc; fi");

		exec("if [ `uname -m` = \"x86_64\" ] ; then if [ ! -e $admin_path/lib64 ] ; then ln -s subdomains/www/lib $admin_path/lib64 ; fi ; fi");

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

function drawSubmitButton($text){
	return "<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"submit\" value=\"". $text. "\"></div>
 <div class=\"input_btn_right\"></div>
</div>";
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
	if($bytes>1024*1024*1024)	return round(($bytes / (1024*1024*1024)),3) ." ". _("GBytes");
	if($bytes>1024*1024)		return round(($bytes / (1024*1024)),3) ." ". _("MBytes");
	if($bytes>1024)				return round(($bytes / 1024),3) ." "._("kBytes");
	return $bytes." "._("Bytes");
}

function calculateExpirationDate($date,$period){
	$tbl = explode("-",$date);
	$year = $tbl[0];
	$month = $tbl[1];
	$day = $tbl[2];

	$period = explode("-",$period);
	$date_timestamp = mktime(1,1,1,$month+$period[1],$day+$period[2],$year+$period[0]);
	$calculated = date("Y-m-d",$date_timestamp);

	return $calculated;
}

function dtc_makesalt()  {
	$hash = '';
	for($i=0;$i<8;$i++) {
		$j = mt_rand(0,53);
		if($j<26)$hash .= chr(rand(65,90));
		else if($j<52)$hash .= chr(rand(97,122));
		else if($j<53)$hash .= '.';
		else $hash .= '/';
	}
	return '$1$'.$hash.'$';
}

?>
