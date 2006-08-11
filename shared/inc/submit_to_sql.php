<?php

if(!isset($submit_err)){
	$submit_err = "";
}
if(!isset($commit_flag)){
	$commit_flag = "yes";
}

function validateRenewal($renew_id){
	global $pro_mysql_pending_renewal_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_admin_table;

	global $commit_flag;
	global $submit_err;

	$q = "SELECT * FROM $pro_mysql_pending_renewal_table WHERE id='$renew_id';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$submit_err = "Could not find pending renewal in table line ".__LINE__." file ".__FILE__;
		$commit_flag = "no";
		return false;
	}
	$renew_entry = mysql_fetch_array($r);

	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$renew_entry["product_id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$submit_err = "Could not find product in table line ".__LINE__." file ".__FILE__;
		$commit_flag = "no";
		return false;
	}
	$product = mysql_fetch_array($r);

	switch($renew_entry["heb_type"]){
	case "vps":
		$q = "SELECT * FROM $pro_mysql_vps_table WHERE id='".$renew_entry["renew_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find VPS id in table line ".__LINE__." file ".__FILE__;
			$commit_flag = "no";
			return false;
		}
		$vps_entry = mysql_fetch_array($r);
		$date_expire = calculateExpirationDate($vps_entry["expire_date"],$product["period"]);
		$q = "UPDATE $pro_mysql_vps_table SET expire_date='$date_expire' WHERE id='".$renew_entry["renew_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		break;
	case "shared":
	case "ssl":
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$renew_entry["adm_login"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find admin login in table line ".__LINE__." file ".__FILE__;
			$commit_flag = "no";
			return false;
		}
		$admin = mysql_fetch_array($r);
		$date_expire = calculateExpirationDate($admin["expire"],$product["period"]);
		$q = "UPDATE $pro_mysql_admin_table SET expire='$date_expire' WHERE adm_login='".$renew_entry["adm_login"]."'";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		break;
	default:
		die("Unknown heb type line ".__LINE__." file ".__FILE__);
		break;
	}

	$q = "DELETE FROM $pro_mysql_pending_renewal_table WHERE id='$renew_id';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	return true;
}

function validateWaitingUser($waiting_login){
	global $conf_administrative_site;
	global $conf_use_ssl;
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_new_admin_table;
	global $pro_mysql_product_table;

	global $txt_userwaiting_account_activated_subject;
	global $txt_userwaiting_account_activated_text_header;

	global $conf_site_root_host_path;
	global $conf_demo_version;
	global $conf_use_ssl;
	global $conf_webmaster_email_addr;
	global $console;

	// Check if there is a user by that name
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$waiting_login';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 0)die("There is already a user with name $waiting_login in database: I can't add another one line: ".__LINE__." file: ".__FILE__."!");

	// Get the informations from the user waiting table
	$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE reqadm_login='$waiting_login';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)die("I can't find username $waiting_login in the userwaiting table line: ".__LINE__." file: ".__FILE__."!");
	$a = mysql_fetch_array($r);

	// Calculate user's path with default path
	$newadmin_path = $conf_site_root_host_path."/".$waiting_login;

	// Create admin's directory
	if($conf_demo_version == "no"){
		$oldumask = umask(0);
		if(!file_exists($newadmin_path)){
			mkdir("$newadmin_path", 0750);
			$console .= "mkdir $newadmin_path;<br>";
		}
	}

	// Get the informations from the product table
	$q2 = "SELECT * FROM $pro_mysql_product_table WHERE id='".$a["product_id"]."'";
	$r2 = mysql_query($q2)or die("Cannot execute query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1)die("I can't find the product in the table line: ".__LINE__." file: ".__FILE__."!");
	$a2 = mysql_fetch_array($r2);

	// Add customer's info to production table
	$adm_query = "INSERT INTO $pro_mysql_client_table
(id,is_company,company_name,familyname,christname,addr1,addr2,addr3,
city,zipcode,state,country,phone,fax,email,
disk_quota_mb,bw_quota_per_month_gb,special_note) VALUES ('','".$a["iscomp"]."',
'".addslashes($a["comp_name"])."','".addslashes($a["family_name"])."','".addslashes($a["first_name"])."',
'".addslashes($a["addr1"])."','".addslashes($a["addr2"])."','".addslashes($a["addr3"])."','".addslashes($a["city"])."',
'".addslashes($a["zipcode"])."','".addslashes($a["state"])."','".addslashes($a["country"])."','".addslashes($a["phone"])."',
'".addslashes($a["fax"])."','".addslashes($a["email"])."','".$a2["quota_disk"]."','". $a2["bandwidth"]/1024 ."',
'".addslashes($a["custom_notes"])."');";
	$r = mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$cid = mysql_insert_id();

	// Add user in database
        $expires = calculateExpirationDate(date("Y-m-d"),$a2["period"]);
        if($a2["heb_type"] == "vps"){
        	$admtbl_added1 = ",expire,prod_id";
        	$admtbl_added2 = ",'0000-00-00','0'";
        }else{
        	$admtbl_added1 = ",expire,prod_id";
        	$admtbl_added2 = ",'$expires','".$a2["id"]."'";
        }
	$adm_query = "INSERT INTO $pro_mysql_admin_table
(adm_login        ,adm_pass         ,path            ,id_client,bandwidth_per_month_mb,quota,nbrdb,max_email$admtbl_added1) VALUES
('$waiting_login','".$a["reqadm_pass"]."','$newadmin_path','$cid','".$a2["bandwidth"]."','".$a2["quota_disk"]."','".$a2["nbr_database"]."','".$a2["nbr_email"]."'$admtbl_added2);";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

        if($a2["heb_type"] == "vps"){
        	addVPSToUser($a["reqadm_pass"],$a["vps_location"],$a2["id"]);
        }else{
		addDomainToUser($waiting_login,$a["reqadm_pass"],$a["domain_name"]);
        }

	// Send a mail to user with how to login and use interface.
	$txt_userwaiting_account_activated_subject = "GPLHost:>_ Account $waiting_login has been activated!";
	if($conf_use_ssl == "yes"){
		$surl = "s";
	}else{
		$surl = "";
	}
	$txt_userwaiting_account_activated_text_header = "DTC hosting account opened!

Hello,

This is Domain Technologie Control panel robot.
The shared hosting account you have ordered is now
ready to be used. You can login to the control
panel using the following informations:

URL: http$surl://$conf_administrative_site/dtc/
Login: $waiting_login
Password: ".$a["reqadm_pass"]."

GPLHost:>_ Open-source hosting worldwide.
http://www.gplhost.com
";
	$headers = "From: ".$conf_webmaster_email_addr;
	mail($a["email"],$txt_userwaiting_account_activated_subject,
		$txt_userwaiting_account_activated_text_header,$headers);

	// Delete the user from the userwaiting table
	$q = "DELETE FROM $pro_mysql_new_admin_table WHERE reqadm_login='$waiting_login';";
	mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
}

// Get the path of a mailbox. pass_check_email() MUST have been called prior to call this function !!!
// Sets "box" with the box infos;
function get_mailbox_complete_path($user,$host){
	global $pro_mysql_pop_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;

	$q = "SELECT $pro_mysql_admin_table.path
FROM $pro_mysql_domain_table,$pro_mysql_admin_table
WHERE $pro_mysql_domain_table.name='$host'
AND $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1) die("Cannot find domain path in database ! line: ".__LINE__." file: ".__FILE__);
	$a = mysql_fetch_array($r);

	$boxpath = $a["path"]."/$host/Mailboxs/$user";
	return $boxpath;
}

// Get the path of a mailinglist. pass_check_email() MUST have been called prior to call this function !!!
// Sets "box" with the box infos;
function get_mailingbox_complete_path($listname,$host){
	global $pro_mysql_pop_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;

	$q = "SELECT $pro_mysql_admin_table.path
FROM $pro_mysql_domain_table,$pro_mysql_admin_table
WHERE $pro_mysql_domain_table.name='$host'
AND $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1) die("Cannot find domain path in database ! line: ".__LINE__." file: ".__FILE__);
	$a = mysql_fetch_array($r);

	$boxpath = $a["path"]."/$host/lists/$listname";
	return $boxpath;
}

function writeDotQmailFile($user,$host){
	global $pro_mysql_pop_table;
	global $conf_unix_type;
	global $conf_demo_version;

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host';";
	$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$box = mysql_fetch_array($res_mailbox);

	// Fetch the path of the mailbox
	$boxpath = get_mailbox_complete_path($user,$host);

	// Write .qmail file
	$oldumask = umask(0);
	if($conf_demo_version == "no"){
		if(!file_exists($boxpath)){
			mkdir($boxpath, 0775);
		}
		mk_Maildir($boxpath);
	}
	$qmail_file_content = "";
	if($box["localdeliver"] == "yes"){
		$qmail_file_content = "./Maildir/\n";
	}
	if($box["redirect1"] != "" && isset($box["redirect1"]) ){
		$qmail_file_content .= '&'.$box["redirect1"]."\n";
	}
	if($box["redirect2"] != "" && isset($box["redirect2"]) ){
		$qmail_file_content .= '&'.$box["redirect2"]."\n";
	}
	if($conf_demo_version == "no"){
		$fp = fopen ( "$boxpath/.qmail", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
		chmod ( "$boxpath/.qmail", 0644);
	}
	umask($oldumask);
}

function writeCatchallDotQmailFile($user,$host){
	global $pro_mysql_pop_table;
	global $conf_demo_version;

	$qmail_file_content = "";

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host';";
	$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$box = mysql_fetch_array($res_mailbox);

	// Fetch the path of the mailbox
	$boxpath = get_mailbox_complete_path($user,$host);

	// Write .qmail file
	$oldumask = umask(0);
	if($conf_demo_version == "no"){
		if(!file_exists($boxpath)){
			mkdir($boxpath, 0775);
		}
		mk_Maildir($boxpath);
	}
	if($box["localdeliver"] == "yes"){
		$qmail_file_content = "./$user/Maildir/\n";
	}
	if(isset($box["redirect1"]) && $box["redirect1"] != ""){
		$qmail_file_content .= '&'.$box["redirect1"]."\n";
	}
	if(isset($box["redirect2"]) && $box["redirect2"] != ""){
		$qmail_file_content .= '&'.$box["redirect2"]."\n";
	}
	if($conf_demo_version == "no"){
		$fp = fopen ( "$boxpath/../.qmail-default", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
		chmod ( "$boxpath/../.qmail-default", 0644);
	}
	umask($oldumask);
}

function writeMlmmjQmailFile($boxpath){
	global $conf_demo_version;

	// Write .qmail file
	$qmail_file_content = "|preline -f /usr/bin/mlmmj-recieve -L $boxpath\n";
	if($conf_demo_version == "no"){
		$fp = fopen ( "$boxpath/.qmail-default", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
		chmod ( "$boxpath/.qmail-default", 0644);
	}
}

// action=change_adm_pass&new_pass1=blabla&new_pass2=blabla
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_adm_pass"){
	if(!isDTCPassword($_REQUEST["new_pass1"]) || !isDTCPassword($_REQUEST["new_pass2"])){
		$submit_err .= "This is not a valid password!<br>\n";
		$commit_flag = "no";
	}
	if($_REQUEST["new_pass1"] != $_REQUEST["new_pass2"]){
		$submit_err .= "Password 1 does not match password 2!<br>\n";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$q = "UPDATE $pro_mysql_admin_table SET adm_pass='".$_REQUEST["new_pass1"]."' WHERE adm_login='$adm_login';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}

if($panel_type!="email"){
	require("$dtcshared_path/inc/sql/dns.php");
	require("$dtcshared_path/inc/sql/database.php");
	require("$dtcshared_path/inc/sql/domain_info.php");
	require("$dtcshared_path/inc/sql/subdomain.php");
	require("$dtcshared_path/inc/sql/ftp.php");
	require("$dtcshared_path/inc/sql/ssh.php");
	require("$dtcshared_path/inc/sql/email.php");
	require("$dtcshared_path/inc/sql/lists.php");
	require("$dtcshared_path/inc/sql/reseller.php");
	require("$dtcshared_path/inc/vps.php");
	require("$dtcshared_path/inc/sql/vps.php");
}else{
	require("submit_to_sql_dtcemail.php");
}

if(isset($submit_err) && $submit_err != ""){
	echo "<font color=\"red\">$submit_err</font>";
}

?>
