<?php

// Do basic field checking before execution of SQL on the password
// adm_email_login=toto%40iglobalwall.com&adm_email_pass=titi&
if(!isValidEmail($_REQUEST["adm_email_login"]))
	die("Check: Incorrect email format!");
if(!isDTCPassword($_REQUEST["adm_email_pass"]))
	die("Check: Incorrect password format!");

$tbl = explode('@',$_REQUEST["adm_email_login"]);
$user = $tbl[0];
$host = $tbl[1];
$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host';";
$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($res_mailbox);
if($n != 1)	die("User not found!");

switch($_REQUEST["action"]){

// action=dtcemail_change_pass&newpass1=&newpass2=&submit=Ok
case "dtcemail_change_pass":
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
	if($_REQUEST["setval"] == "no"){
		$q = "UPDATE $pro_mysql_pop_table SET local_deliver='no' WHERE id='$user' AND mbox_host='$host';";
	}else{
		$q = "UPDATE $pro_mysql_pop_table SET local_deliver='yes' WHERE id='$user' AND mbox_host='$host';";
	}
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	break;

// action=dtcemail_edit_redirect&redirect1=&redirect2=&submit=Ok
case "dtcemail_edit_redirect":
	// Fetch the path of the mailbox
	$box = mysql_fetch_array($res_mailbox);
	$q = "SELECT $pro_mysql_admin_table.path
FROM $pro_mysql_domain_table,$pro_mysql_admin_table
WHERE $pro_mysql_domain_table.name='$host'
AND $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        if($n != 1) die("Cannot find domain path in database ! line: ".__LINE__." file: ".__FILE__);
	$a = mysql_fetch_array($r);

	$boxpath = $a["path"]."/$host/Mailboxs/$user";

	if($_REQUEST["redirect1"] != ""){
		if(!isValidEmail($_REQUEST["redirect1"])){
			die("Incorect redirection 1");
		}
	}
	if($_REQUEST["redirect2"] != ""){
		if(!isValidEmail($_REQUEST["redirect2"])){
			die("Incorect redirection 2");
		}
	}
	// Write .qmail file
	$oldumask = umask(0);
	if($conf_demo_version == "no"){
		mk_Maildir($boxpath);
	}
	if($box["localdeliver"] == "yes"){
                $qmail_file_content = "./Maildir/\n";
        }

	if($_REQUEST["redirect1"] != "" && isset($_REQUEST["redirect1"]) ){
		$qmail_file_content .= '&'.$_REQUEST["redirect1"]."\n";
	}
	if($_REQUEST["redirect2"] != "" && isset($_REQUEST["redirect2"]) ){
		$qmail_file_content .= '&'.$_REQUEST["redirect2"]."\n";
	}
	if($conf_demo_version == "no"){
		$fp = fopen ( "$boxpath/.qmail", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
	}
	umask($oldumask);

	$q = "UPDATE $pro_mysql_pop_table SET redirect1='".$_REQUEST["redirect1"]."',redirect2='".$_REQUEST["redirect2"]."' WHERE id='$user' AND mbox_host='$host' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	break;
default:
	break;
}

?>