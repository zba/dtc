<?php

// Do basic field checking before execution of SQL on the password
// adm_email_login=toto%40iglobalwall.com&adm_email_pass=titi&
function pass_check_email(){
	global $pro_mysql_pop_table;
	global $user;
	global $host;
	if(!isValidEmail($_REQUEST["adm_email_login"]))
		die("Check: Incorrect email format!");
	if(!isDTCPassword($_REQUEST["adm_email_pass"]))
		die("Check: Incorrect password format!");
	$q = "SELECT * FROM $pro_mysql_pop_table WHERE ";

	$tbl = explode('@',$_REQUEST["adm_email_login"]);
	$user = $tbl[0];
	$host = $tbl[1];
	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host' AND passwd='".$_REQUEST["adm_email_pass"]."';";
	$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($res_mailbox);
	if($n == 1)	return true;
	else		return false;
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

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host';";
	$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$box = mysql_fetch_array($res_mailbox);

	// Fetch the path of the mailbox
	$boxpath = get_mailbox_complete_path($user,$host);

	// Write .qmail file
	$oldumask = umask(0);
	if($conf_demo_version == "no"){
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
	}
	umask($oldumask);
}

switch($_REQUEST["action"]){

case "activate_antispam":
	if(pass_check_email()==false)	die("User not found!");
	if($_REQUEST["iwall_on"] == "yes"){
		$q = "UPDATE $pro_mysql_pop_table SET iwall_protect='yes' WHERE id='$user' AND mbox_host='$host' AND passwd='".$_REQUEST["adm_email_pass"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		$q = "UPDATE $pro_mysql_pop_table SET iwall_protect='no' WHERE id='$user' AND mbox_host='$host' AND passwd='".$_REQUEST["adm_email_pass"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}
	break;

// ?adm_email_login=toto%40iglobalwall.com&adm_email_pass=toto&addrlink=fetchmail
// &action=add_fetchmail&email_addr=zigo%40pplchat.com&mailbox_type=POP3&server_addr=pop.gplhost.com&login=zigo%40pplchat.com&pass=master&use=yes
case "add_fetchmail":
	if(pass_check_email()==false)	die("User not found!");
	if(checkMailbox($user,$host,$_REQUEST["email_addr"],
				$_REQUEST["mailbox_type"],$_REQUEST["server_addr"],
				$_REQUEST["login"],$_REQUEST["pass"])){
		$q = "INSERT INTO $pro_mysql_fetchmail_table (id,domain_user,domain_name,
		pop3_email,mailbox_type,pop3_server,pop3_login,pop3_pass)
		VALUES ('','$user','$host',
		'".$_REQUEST["email_addr"]."','".$_REQUEST["mailbox_type"]."','".$_REQUEST["server_addr"]."','".$_REQUEST["login"]."','".$_REQUEST["pass"]."');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}
	break;
//	$q = " WHERE id='$user' AND mbox_host='$host';";

// action=dtcemail_change_pass&newpass1=&newpass2=&submit=Ok
case "dtcemail_change_pass":
	if(pass_check_email()==false)	die("User not found!");
	if(!isDTCPassword($_REQUEST["newpass1"]))	die("Incorrect password format!");
	if(!isDTCPassword($_REQUEST["newpass2"]))	die("Incorrect password format!");
	if($_REQUEST["newpass1"] != $_REQUEST["newpass2"])	die("Password 1 does not match password 2!");
	$crypted_pass = crypt($_REQUEST["newpass1"]);
	$q = "UPDATE $pro_mysql_pop_table SET crypt='$crypted_pass', passwd='".$_REQUEST["newpass1"]."' WHERE id='$user' AND mbox_host='$host';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$adm_email_pass = $_REQUEST["newpass1"];
	$_REQUEST["adm_email_pass"] = $_REQUEST["newpass1"];
	break;

// action=dtcemail_set_deliver_local&setval=no
case "dtcemail_set_deliver_local":
	if(pass_check_email()==false)	die("User not found!");

	if($_REQUEST["setval"] == "no"){
		$q = "UPDATE $pro_mysql_pop_table SET localdeliver='no' WHERE id='$user' AND mbox_host='$host';";
	}else{
		$q = "UPDATE $pro_mysql_pop_table SET localdeliver='yes' WHERE id='$user' AND mbox_host='$host';";
	}
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	writeDotQmailFile($user,$host);
	break;

// action=dtcemail_edit_redirect&redirect1=&redirect2=&submit=Ok
case "dtcemail_edit_redirect":
	if(pass_check_email()==false)	die("User not found!");
	if(isValidEmail($_REQUEST["redirect1"]))	$redir1 = $_REQUEST["redirect1"];	else	$redir1 = "";
	if(isValidEmail($_REQUEST["redirect2"]))	$redir1 = $_REQUEST["redirect2"];	else	$redir2 = "";

	$q = "UPDATE $pro_mysql_pop_table SET redirect1='$redir1',redirect2='$redir2' WHERE id='$user' AND mbox_host='$host' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	writeDotQmailFile($user,$host);

	break;
default:
	break;
}

?>