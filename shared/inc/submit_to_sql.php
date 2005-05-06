<?php

if(!isset($submit_err)){
	$submit_err = "";
}
if(!isset($commit_flag)){
	$commit_flag = "yes";
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
disk_quota_mb,bw_quota_per_month_gb) VALUES ('','".$a["iscomp"]."',
'".addslashes($a["comp_name"])."','".addslashes($a["family_name"])."','".addslashes($a["first_name"])."',
'".addslashes($a["addr1"])."','".addslashes($a["addr2"])."','".addslashes($a["addr3"])."','".addslashes($a["city"])."',
'".addslashes($a["zipcode"])."','".addslashes($a["state"])."','".$a["country"]."','".addslashes($a["phone"])."',
'".$a["fax"]."','".$a["email"]."','".$a2["quota_disk"]."','". $a2["bandwidth"]/1024 ."');";
	$r = mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$cid = mysql_insert_id();

	// Add user in database
	$adm_query = "INSERT INTO $pro_mysql_admin_table
(adm_login        ,adm_pass         ,path            ,id_client)VALUES
('$waiting_login','".$a["reqadm_pass"]."','$newadmin_path','$cid') ";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	addDomainToUser($waiting_login,$a["reqadm_pass"],$a["domain_name"]);

	// Send a mail to user with how to login and use interface.
	$txt_userwaiting_account_activated_subject = "GPLHost:>_ Account $waiting_login has been activated!";
	if($conf_use_ssl == "yes")	$surl = "s";
	$txt_userwaiting_account_activated_text_header = "DTC hosting account opened!

Hello,

This is Domain Technologie Control panel robot.
The hosting account you have ordered is now
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
	if($box["redirect1"] != "" && isset($box["redirect1"]) ){
		$qmail_file_content .= '&'.$box["redirect1"]."\n";
	}
	if($box["redirect2"] != "" && isset($box["redirect2"]) ){
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

if($panel_type!="email"){
	require("$dtcshared_path/inc/sql/dns.php");
	require("$dtcshared_path/inc/sql/database.php");
	require("$dtcshared_path/inc/sql/domain_info.php");
	require("$dtcshared_path/inc/sql/subdomain.php");
	require("$dtcshared_path/inc/sql/ftp.php");
	require("$dtcshared_path/inc/sql/email.php");
}else{
	require("submit_to_sql_dtcemail.php");
}

if(isset($submit_err) && $submit_err != ""){
	echo "<font color=\"red\">$submit_err</font>";
}

?>
