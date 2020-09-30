<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

function find_domain_extension($domain){
        global $allTLD;

	# $pos = strrchr($domain,".");
	$nbr_tld = sizeof($allTLD);
        for($i=0;$i<$nbr_tld;$i++){
                if( preg_match("/\\".$allTLD[$i]."\$/",$domain)){
                        $pos = $allTLD[$i];
                }
        }

	if($pos === FALSE){
		return FALSE;
	}
	return $pos;
}

// If the admin has en_US.UTF-8, and filename is registration_msg/vps_open,
// then the function will try to open, in order:
// * /etc/dtc/registration_msg/vps_open_en_US.UTF-8.txt
// * /etc/dtc/registration_msg/vps_open_en_US.txt
// * /etc/dtc/registration_msg/vps_open_en.txt
// * /etc/dtc/registration_msg/vps_open.txt
// which ever is found first...
// Then this is repeated with other folders:
// /usr/local/www/dtc/etc/ and /usr/share/dtc/etc/
function readCustomizedMessage($filename,$adm_login){
	$mylang = findLastUsedLangByUser($adm_login);

	// 1st try /etc/dtc/filename_en_US.UTF-8.txt
	if( file_exists( "/etc/dtc/" . $filename . "_" . $mylang . ".txt" ) ){
		$to_open = "/etc/dtc/" . $filename . $mylang . ".txt";
	// Then /etc/dtc/filename_en_US.txt
	}else if( file_exists( "/etc/dtc/" . $filename . "_" . substr($mylang,0,5) . ".txt" ) ){
		$to_open = "/etc/dtc/" . $filename . "_" . substr($mylang,0,5) . ".txt";
	// Then /etc/dtc/filename_en.txt
	}else if( file_exists( "/etc/dtc/" . $filename . "_" . substr($mylang,0,2) . ".txt" ) ){
		$to_open = "/etc/dtc/" . $filename . "_" . substr($mylang,0,2) . ".txt";
	// then /etc/dtc/filename.txt
	}else if( file_exists( "/etc/dtc/" . $filename . ".txt" ) ){
		$to_open = "/etc/dtc/" . $filename . ".txt";
	// then /usr/local/etc/dtc/filename_en_US.UTF-8.txt
	}else if( file_exists( "/usr/local/etc/dtc/" . $filename . "_" . $mylang . ".txt" ) ){
		$to_open = "/usr/local/etc/dtc/" . $filename . $mylang . ".txt";
	// Then /usr/local/etc/dtc/filename_en_US.txt
	}else if( file_exists( "/usr/local/etc/dtc/" . $filename . "_" . substr($mylang,0,5) . ".txt" ) ){
		$to_open = "/usr/local/etc/dtc/" . $filename . "_" . substr($mylang,0,5) . ".txt";
	// Then /usr/local/etc/dtc/filename_en.txt
	}else if( file_exists( "/usr/local/etc/dtc/" . $filename . "_" . substr($mylang,0,2) . ".txt" ) ){
		$to_open = "/usr/local/etc/dtc/" . $filename . "_" . substr($mylang,0,2) . ".txt";
	// then /usr/local/etc/dtc/filename.txt
	}else if( file_exists( "/usr/local/etc/dtc/" . $filename . ".txt" ) ){
		$to_open = "/usr/local/etc/dtc/" . $filename . ".txt";
	// then /usr/local/www/dtc/etc/filename_en_US.UTF-8.txt
	}else if( file_exists( "/usr/local/www/dtc/etc/" . $filename . "_" . $mylang . ".txt" ) ){
		$to_open = "/usr/local/www/dtc/etc/" . $filename . "_" . $mylang . ".txt";
	// then /usr/local/www/dtc/etc/filename_en_US.txt
	}else if( file_exists( "/usr/local/www/dtc/etc/" . $filename . "_" . substr($mylang,0,5) . ".txt" ) ){
		$to_open = "/usr/local/www/dtc/etc/" . $filename . "_" . substr($mylang,0,5) . ".txt";
	// then /usr/local/www/dtc/etc/filename_en.txt
	}else if( file_exists( "/usr/local/www/dtc/etc/" . $filename . "_" . substr($mylang,0,2) . ".txt" ) ){
		$to_open = "/usr/local/www/dtc/etc/" . $filename . "_" . substr($mylang,0,2) . ".txt";
	// then /usr/local/www/dtc/etc/filename.txt
	}else if( file_exists( "/usr/local/www/dtc/etc/" . $filename . ".txt" ) ){
		$to_open = "/usr/local/www/dtc/etc/" . $filename . ".txt";
	// then /usr/share/dtc/etc/filename_en_US.UTF-8.txt
	}else if( file_exists( "/usr/share/dtc/etc/" . $filename . "_" . $mylang . ".txt" ) ){
		$to_open = "/usr/share/dtc/etc/" . $filename . "_" . $mylang . ".txt";
	// then /usr/share/dtc/etc/filename_en_US.txt
	}else if( file_exists( "/usr/share/dtc/etc/" . $filename . "_" . substr($mylang,0,5) . ".txt" ) ){
		$to_open = "/usr/share/dtc/etc/" . $filename . "_" . substr($mylang,0,5) . ".txt";
	// then /usr/share/dtc/etc/filename_en.txt
	}else if( file_exists( "/usr/share/dtc/etc/" . $filename . "_" . substr($mylang,0,2) . ".txt" ) ){
		$to_open = "/usr/share/dtc/etc/" . $filename . "_" . substr($mylang,0,2) . ".txt";
	// then /usr/share/dtc/etc/filename.txt
	}else if( file_exists( "/usr/share/dtc/etc/" . $filename . ".txt" ) ){
		$to_open = "/usr/share/dtc/etc/" . $filename . ".txt";
	// then it means we didn't find the file...
	}else{
		return "Customized message language file not found. Get in touch with your administrator.";
	}
	$fp = fopen($to_open, "r");
	$content = fread($fp,filesize($to_open));
	fclose($fp);
	return $content;
}

// Create a random hash, making sure that it doesn't exists in the DB already
function createSupportHash(){
	global $pro_mysql_tik_queries_table;
	$n = 1;
	while($n != 0){
		$hash = getRandomValue();
		$q = "SELECT id FROM $pro_mysql_tik_queries_table WHERE hash='$hash';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
	}
	return $hash;
}

// Returns 0 if not found, a valid ID otherwise
function findLastTicketID($hash){
	global $pro_mysql_tik_queries_table;

	$q = "SELECT id,reply_id FROM $pro_mysql_tik_queries_table WHERE hash='$hash';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		return 0;
	}
	$a = mysql_fetch_array($r);
	if( $a["reply_id"] == 0){
		return $a["id"];
	}
	$i = 100;
	while($a["reply_id"] != 0 && $i-- != 0){
		$q = "SELECT id,reply_id FROM $pro_mysql_tik_queries_table WHERE id='".$a["reply_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			return 0;
		}
		$a = mysql_fetch_array($r);
	}
	return $a["id"];
}

function getCustomizableMessage($file_name){
	if(file_exists("/etc/dtc/$file_name")){
		$fname = "/etc/dtc/$file_name";
	}else if(file_exists("/usr/local/www/dtc/etc/$file_name")){
		$fname = "/usr/local/www/dtc/etc/$file_name";
	}else{
		$fname = "/usr/share/dtc/etc/$file_name";
	}
	if(file_exists($fname)){
		$fp = fopen($fname,"r");
		if($fp != NULL){
			$out = fread($fp,filesize($fname));
			fclose($fp);
		}else{
			$out = "";
		}
	}else{
		$out = "";
	}
	return $out;
}

function headAndTailEmailMessage($msg){
	$msg = getCustomizableMessage("messages_header.txt") . $msg;
	$signature = getCustomizableMessage("signature.txt");
	return str_replace("%%%SIGNATURE%%%",$signature,$msg);
}

$allTLD = array(".com", ".aero", ".asia", ".biz", ".cat", ".coop", ".edu", ".gov", ".info", ".int", ".jobs", ".mil", ".mobi", ".museum", ".name", ".net",".org", ".pro", ".tel", ".travel",
".ac",".ad",".ae",".af",".ag",".ai",".al",".am",".an",".ao",".aq",
".ar",".com.ar",".net.ar",".mil.ar",".edu.ar",".org.ar",".tur.ar",".int.ar",".gov.ar",".gob.ar",
".as",".at",".au",".aw",".ax",".az",
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
".ac.za",".city.za",".co.za",".edu.za",".gov.za",".law.za",".mil.za",".nom.za",".org.za",".school.za",".zm",".zw");

function isTLD($tld){
	global $allTLD;
	return in_array ($tld, $allTLD);
}

function domainNamePopup($domain_name=""){
	global $allTLD;
	global $conf_this_server_default_tld;

	$out = "";

	$nbr_tld = sizeof($allTLD);
	for($i=0;$i<$nbr_tld;$i++){
		if( preg_match("/\\".$allTLD[$i]."\$/",$domain_name)){
			$selected = " selected ";
		}else{
			if ($allTLD[$i] == $conf_this_server_default_tld){
				$selected = " selected ";
			}else{
				$selected = "";
			}
		}
		$out .= "<option value=\"".$allTLD[$i]."\" $selected>". $allTLD[$i] ."</option>";
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
	$vps_location_popup = "<option value=\"-1\">" . _("Please select") . "!</optioon>";
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
			// Fallback to default english...
			return "en_US.UTF-8";
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
	for($index=0;$index<12;$index++){
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
//	document[frm_name][fld_name].type = 'text';
	dtc_see_password(frm_name,fld_name);
}
function dtc_see_password(frm_name,fld_name){
	var curObj = document[frm_name][fld_name];
	try {
		document[frm_name][fld_name].type = 'text';
	} catch (e) {
// type property read only on IE at the time of this writing,
// so replace the control with a new one
		if (curObj.getAttribute('type') != 'text') {
			var newObj=document.createElement('input');
			newObj.setAttribute('type','text');
			newObj.setAttribute('name',curObj.getAttribute('name'));
			newObj.setAttribute('class',curObj.getAttribute('class'));
			newObj.value = curObj.value;
			curObj.parentNode.replaceChild(newObj, curObj);
		}
	}
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

function HTTP_Get($URL,$data, $referrer=""){
	$result = "";
	// parsing the given URL
	$URL_Info=parse_url($URL);

	// Building referrer
	if($referrer=="") // if not given use this script as referrer
		$referrer=$_SERVER["SCRIPT_URI"];

	// making string from $data
	if (strlen($data)) {
		foreach($data as $key=>$value)
			$values[]="$key=".urlencode($value);
		$data_string=implode("&",$values);
	} else
		$data_string='';

	// Find out which port is needed - if not given use standard (=80)
	if(!isset($URL_Info["port"]))
		$URL_Info["port"]=80;

	// building POST-request:
	$request = "";
	$request.="GET ".$URL_Info["path"]."?".$data_string." HTTP/1.1\n";
	$request.="Host: ".$URL_Info["host"]."\n";
	$request.="Referer: $referrer\n";
	$request.="Connection: close\n";
	$request.="\n\n";

	$fp = fsockopen($URL_Info["host"],$URL_Info["port"]);
	fputs($fp, $request);
	while(!feof($fp)){
		$result .= fgets($fp, 128);
	}
	fclose($fp);
	return $result;
}

function logPay($txt){
	//$fp = fopen("/tmp/paylog.txt","a");
	//fwrite($fp,$txt."\n");
	//fclose($fp);
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
	global $pro_mysql_tik_admins_table;

	if(strlen($adm_pass) > 16){
	}

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND ((adm_pass='$adm_pass' OR adm_pass=SHA1('$adm_pass')) OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1){
		$query = "SELECT * FROM $pro_mysql_tik_admins_table WHERE pass_next_req='$adm_pass' AND pass_expire > '".mktime()."';";
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
	global $pro_mysql_tik_admins_table;

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND ((adm_pass='$adm_pass' OR adm_pass=SHA1('$adm_pass')) OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1){
		$query = "SELECT * FROM $pro_mysql_tik_admins_table WHERE pass_next_req='$adm_pass' AND pass_expire > '".mktime()."';";
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


//////////////////////////////////////////////////////////////
// Some preg_match check functions to be sure of all inputs //
//////////////////////////////////////////////////////////////
// This is the RFC preg_match as seen in most servers...
// Todo: extract rulles for other functions.
// $reg = '^(([^<>;()[\]\\.,;:@"]+(\.[^<>()[\]\\.,;:@"]+)*)|(".+"))@((([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$';
function isIP($ip){
	$reg = "/^([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}\$/";
	if(!preg_match($reg,$ip))	return false;
	else			return true;
}

function isIP6($ip){
	// This regular expression is shamefully taken from:
	// Test suite for IPv6 address validation Regular Expressions
	// Rich Brown <richard.e.brown at dartware.com> 25 Feb 2010
	// http://download.dartware.com/thirdparty/test-ipv6-regex.pl
	$reg = "/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/";
	if(!preg_match($reg,$ip))	return false;
	else	return true;
}


function isDTCLogin($login){
	$reg = "/^([a-zA-Z0-9]+)([._a-zA-Z0-9-]+)\$/";
	if(!preg_match($reg,$login))	return false;
	else			return true;
}

// The subdomain string allowed to be hosted by DTC
function checkSubdomainFormat($name){
	if($name == ""){
		return false;
	}
	if(preg_match("/^([_a-z0-9]+)([_.a-z0-9-]*)([_.a-z0-9]+)\$/",$name))
		return true;
	else{
		if(preg_match("/^([a-z0-9])\$/",$name))
			return true;
		else
			return false;
	}
}

// Check if a string is an ssh key
// TO BE DONE!!!
function isSSHKey($ssh_key){
	if(preg_match("/^ssh-[rd]s[as] ([.a-zA-Z0-9\+/\=]+)\$/",$ssh_key)){
		return true;
	}else{
		return false;
	}
}

// Check for email addr we allow to create using DTC
function isMailbox($mailbox){
	$reg = "/^([a-z0-9])\$|^([a-z0-9]+)([._a-z0-9-]+)\$/";
	if(!preg_match($reg,$mailbox))	return false;
	else			return true;
}

// Check for valid (but maybe non-RFC) email addr we allow forwarding to
function isValidEmail($email){
	$reg = "/(^([a-zA-Z0-9])|^([a-zA-Z0-9]+)([._a-zA-Z0-9-]*))@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+)\$/";
	if(!preg_match($reg,$email))	return false;
	else			return true;
}

function isHostnameOrIP($hostname){
	$reg = '/^((([a-z0-9]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$/';
	if(!preg_match($reg,$hostname) && !isIP($hostname))	return false;
	else			return true;
}

function isHostname($hostname){
	$reg = '/^((([a-z0-9]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$/';
//	$reg = '^(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)$';
//	$reg = "^([.a-z0-9-]+)\$";
	if(!preg_match($reg,$hostname))	return false;
	else			return true;
}

// Check for email addr we allow to create using DTC
function isFtpLogin($mailbox){
	if(isValidEmail($mailbox))	return true;
	$reg = "/^([a-zA-Z0-9]+)([._a-zA-Z0-9-]+)\$/";
	if(!preg_match($reg,$mailbox))	return false;
	else			return true;
}

// Check for validity of a database name
function isDatabase($db){
	$reg = "/^([a-zA-Z0-9]+([\_]*)[a-zA-Z0-9]+)\$/";
	if(!preg_match($reg,$db))	return false;
	else			return true;
}

// Check any mail password for another server
function isMailPassword($login){
//	$reg = '^([<>()\\\/\?_\[;,;:%\^@"!a-zA-Z0-9-]){4,16}$';
	$reg = "/^([_.a-zA-Z0-9-]){1,64}\$/";
        if(!preg_match($reg,$login))   return false;
	else                    return true;
}

function isDTCPassword($pass){
	$reg = "/^([a-zA-Z0-9]){4,255}\$/";
	if(!preg_match($reg,$pass))	return false;
	else			return true;
}

// Take care, check with FALSE === check_password($pass) as this function can return zero
// if it matches the first pass of the top_bad_passwords.txt database.
function check_password($pass){
	global $dtcshared_path;
	$bad_pass = file("$dtcshared_path/drawlib/top_bad_passwords.txt");
	$n = sizeof($bad_pass);
	for($i=0;$i<$n;$i++){  
		if($bad_pass[$i] == $pass."\n"){
			return $i;
		}
	}
	return FALSE;
}

// Check if it's only numbers
function isRandomNum($mailbox){
	$reg = "/^([0-9]+)\$/";
	if(!preg_match($reg,$mailbox))        return false;
	else                    return true;
}

// Check if it's only numbers
function isPageURL($mailbox){
	$reg = '/^\\/([=&amp;a-zA-Z_\\-0-9\\/\\.\\?]*)$/';
	if(!preg_match($reg,$mailbox))        return false;
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
		$console .= "mkdir $path/subdomains/www/logs;<br>";
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

	$q = "SELECT DISTINCT $pro_mysql_client_table.email
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
	$q = "UPDATE $pro_mysql_vps_ip_table SET available='no',rdns_addr='mx.xen".$vps_ip["vps_xen_name"].".".$vps_ip["vps_server_hostname"]."' WHERE vps_xen_name='".$vps_ip["vps_xen_name"]."' AND vps_server_hostname='".$vps_ip["vps_server_hostname"]."';";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	$exp_date = calculateExpirationDate(date("Y-m-d"),$product["period"]);
	$q = "INSERT INTO $pro_mysql_vps_table (id,owner,vps_server_hostname,vps_xen_name,start_date,expire_date,hddsize,ramsize,product_id,bandwidth_per_month_gb,operatingsystem)
	VALUES('','$adm_login','".$vps_ip["vps_server_hostname"]."','".$vps_ip["vps_xen_name"]."','".date("Y-m-d")."','$exp_date','".$product["quota_disk"]."','".$product["memory_size"]."','$product_id','".$product["bandwidth"]."','$operating_system');";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	updateUsingCron("gen_named='yes',reload_named ='yes'");

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
	global $conf_unix_type;

	checkLoginPass($adm_login,$adm_pass);
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$result = mysql_query($query)or die("Cannot query : \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$numrows = mysql_num_rows($result);
	if($numrows != 1){
		die("Cannot fetch admin path (maybe rotative random password expired) line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	$admin = mysql_fetch_array($result);
	$admin_path = $admin["path"];

	// Create subdirectorys & html front page
	if($conf_demo_version == "no"){

		if(!file_exists($admin_path)){
			mkdir($admin_path, 0755);
		}
		make_new_adm_domain_dir("$admin_path/$domain_name");
		if($admin["shared_hosting_security"] != "mod_php"){
			if($admin["shared_hosting_security"] != "sbox_copy"){
				exec("cp -flpRv /var/lib/dtc/sbox_copy/* $admin_path/$domain_name/subdomains/www");
			}
		}else{
			if ($conf_unix_type == "bsd") {			// no -u in freebsd, blows away custom changes, NEEDFIX: KC
				exec("cp -flpRv $conf_chroot_path/* $admin_path/$domain_name/subdomains/www");
				createSymLink("subdomains/www/libexec", "$admin_path/$domain_name/libexec");	// also symlink libexec for fbsd while we're here: KC
				createSymLink("$domain_name/subdomains/www/libexec", "$admin_path/libexec");
			}else{
				exec("cp -fulpRv $conf_chroot_path/* $admin_path/$domain_name/subdomains/www");
			}
			exec("cp -fulpRv $conf_chroot_path/* $admin_path/$domain_name/subdomains/www");
			// create a link so that the user can log in via SSH to $admin_path or $admin_path/$domain_name
			// typo renamed to foreach *steveetm*
			$folder_list = "bin var lib sbin tmp usr dev etc";
			$unamestring = exec("uname -m",$unameout,$unameret);
			$arch = $unameout[0];
			if($arch == "x86_64"){
				$folder_list .= " lib64";
			}
			foreach ( explode(" " , $folder_list) as $subdir) {
				createSymLink("subdomains/www/$subdir", "$admin_path/$domain_name/$subdir");
				createSymLink("$domain_name/subdomains/www/$subdir", "$admin_path/$subdir");
			}
		
			if ($conf_unix_type == "bsd") {			// no -u in freebsd, could blow away custom changes, NEEDFIX: KC
				$cp_opt = "p";
			}else{
				$cp_opt = "up";
			}
			system ("cp -r$cp_opt $conf_generated_file_path/template/* $admin_path/$domain_name/subdomains/www/html");
			if( file_exists("$conf_generated_file_path/template/.htaccess") ){
				system ("cp -$cp_opt $conf_generated_file_path/template/.htaccess $admin_path/$domain_name/subdomains/www/html");
			}
		}
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

/////////////////////////////////
// Add custom product to table //
/////////////////////////////////
function addCustomProductToUser($adm_login,$server_hostname,$product_id){
	global $pro_mysql_product_table;
	global $pro_mysql_custom_product_table;
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='$product_id';";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find product line ".__LINE__." file ".__FILE__);
	}
	$product = mysql_fetch_array($r);
	
	$exp_date = calculateExpirationDate(date("Y-m-d"),$product["period"]);
	$q = "INSERT INTO $pro_mysql_custom_product_table (id,owner,domain,start_date,expire_date,product_id,custom_heb_type,custom_heb_type_fld )
	VALUES('','$adm_login','$server_hostname','".date("Y-m-d")."','$exp_date','$product_id','".$product["custom_heb_type"]."','".$product["custom_heb_type_fld"]."');";
	$r = mysql_query($q)or die("Cannot query : \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	return ;
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
		$out .= $plop." " ;
		if($ar[0] > 1)	$out .= _("years"); else $out .= _("year") ;
	}
	if($ar[1] > 0 ){
		$out .= $ar[1]." ";
		if($ar[1] > 1)	$out .= "months"; else $out .= _("month") ;
	}
	if($ar[2] > 0 ){
		$out .= $ar[2]." ";
		if($ar[2] > 1)	$out .= "days"; else $out .= _("day") ;
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

function createSymLink($target, $link) {
	global $console;

	if(is_link("$link")) {
		if(!file_exists($link)) { // if this is a link and it points to a non-existing file
			$console.="<br/ >$link points to a non-existing file. Fixing that<br />";
			if(unlink($link)) {
				if(!symlink($target,$link)) {
					$console.="<br/ >WARNING: error encountered while trying to create $link<br />";
				}
				else {
					$console.="$link now points to $target<br />";
				}
			}
			else {
				$console.="<br/ >WARNING: error encountered while trying to delete $link<br />";
			}
		}
	} else {
		if(!file_exists($link)) {
			symlink($target,$link);
		}
	}
}

?>
