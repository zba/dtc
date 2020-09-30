<?php

// Do basic field checking before execution of SQL on the password
// adm_email_login=toto%40iglobalwall.com&adm_email_pass=titi&
function pass_check_email(){
	global $pro_mysql_pop_table;
	global $user;
	global $host;
	global $conf_session_expir_minute;
	global $adm_email_random_pass;
	global $adm_email_login;
	global $adm_email_pass;

	if(!isValidEmail($adm_email_login))	die("Check: Incorrect email format!");
	if(!isDTCPassword($adm_email_pass))	die("Check: Incorrect password format!");
	$q = "SELECT * FROM $pro_mysql_pop_table WHERE ";

	$tbl = explode('@',$adm_email_login);
	$user = $tbl[0];
	$host = $tbl[1];
	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host' AND (passwd='".$adm_email_pass."' OR (pass_next_req='".$adm_email_pass."' AND pass_expire > '".mktime()."') );";
	$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($res_mailbox);
	if($n == 1){
		if( !isset($adm_email_random_pass)){
			$adm_email_random_pass = getRandomValue();
			$adm_email_pass = $adm_email_random_pass;
			$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
			$q = "UPDATE $pro_mysql_pop_table SET pass_next_req='$adm_email_random_pass', pass_expire='$expirationTIME' WHERE id='$user' AND mbox_host='$host'";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		}
		return true;
	}
	else		return false;
}


if(isset($_REQUEST["action"])){
if($_REQUEST["action"] != "logout"){
	if(pass_check_email()==false)   die("User not found!");
}
switch($_REQUEST["action"]){
/*
// action=add_whitelist_rule&mail_from_user=toto&mail_from_domain=toto.com&mail_to=
case "add_whitelist_rule":
	if((isValidEmail($_REQUEST["mail_from_user"].'@'.$_REQUEST["mail_from_domain"]) && $_REQUEST["mail_to"] == "")||
		((isHostnameOrIP($_REQUEST["mail_from_domain"]) && $_REQUEST["mail_from_user"] == "") && $_REQUEST["mail_to"] == "") ||
		(isHostnameOrIP($_REQUEST["mail_to"]) && $_REQUEST["mail_from_user"] == "" && $_REQUEST["mail_from_domain"] == "")){
		$q = "INSERT INTO $pro_mysql_whitelist_table (id,pop_user,mbox_host,mail_from_user,mail_from_domain,mail_to) VALUES('','$user','$host',
			'".$_REQUEST["mail_from_user"]."','".$_REQUEST["mail_from_domain"]."','".$_REQUEST["mail_to"]."');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		echo "<font color=\"red\">This is not a valid rule!</font>";
	}
	break;

// ruleid=1&action=delete_whitelist_rule
case "delete_whitelist_rule":
	$q = "DELETE FROM $pro_mysql_whitelist_table WHERE id='".$_REQUEST["ruleid"]."' AND pop_user='$user' AND mbox_host='$host'";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	break;

// action=edit_whitelist_rule&ruleid=1&mail_from_user=toto&mail_from_domain=toto.com&mail_to=
case "edit_whitelist_rule":
	if((isValidEmail($_REQUEST["mail_from_user"].'@'.$_REQUEST["mail_from_domain"]) && $_REQUEST["mail_to"] == "")||
		((isHostnameOrIP($_REQUEST["mail_from_domain"]) && $_REQUEST["mail_from_user"] == "") && $_REQUEST["mail_to"] == "") ||
		(isHostnameOrIP($_REQUEST["mail_to"]) && $_REQUEST["mail_from_user"] == "" && $_REQUEST["mail_from_domain"] == "")){
		$q = "UPDATE $pro_mysql_whitelist_table
			SET  mail_from_user='".$_REQUEST["mail_from_user"]."',
			mail_from_domain='".$_REQUEST["mail_from_domain"]."',mail_to='".$_REQUEST["mail_to"]."'
			WHERE id='".$_REQUEST["ruleid"]."' AND pop_user='$user' AND mbox_host='$host';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		echo "<font color=\"red\">This is not a valid rule!</font>";
	}
	break;

// addrlink=antispam&action=activate_spf&spf_on=yes
case "activate_spf":
	if($_REQUEST["spf_on"] == "yes"){
		$q = "UPDATE $pro_mysql_pop_table SET spf_protect='yes' WHERE id='$user' AND mbox_host='$host' AND passwd='".$_REQUEST["adm_email_pass"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		$q = "UPDATE $pro_mysql_pop_table SET spf_protect='no' WHERE id='$user' AND mbox_host='$host' AND passwd='".$_REQUEST["adm_email_pass"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}
	break;

case "activate_clamav":
	if($_REQUEST["clamav_on"] == "yes"){
		$q = "UPDATE $pro_mysql_pop_table SET clamav_protect='yes' WHERE id='$user' AND mbox_host='$host' AND passwd='".$_REQUEST["adm_email_pass"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		$q = "UPDATE $pro_mysql_pop_table SET clamav_protect='no' WHERE id='$user' AND mbox_host='$host' AND passwd='".$_REQUEST["adm_email_pass"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}
	break;

// ?adm_email_login=toto%40iglobalwall.com&adm_email_pass=toto&addrlink=fetchmail
// &action=add_fetchmail&email_addr=zigo%40pplchat.com&mailbox_type=POP3&server_addr=pop.gplhost.com&login=zigo%40pplchat.com&pass=master&use=yes
case "add_fetchmail":
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
// action=modify_fetchmail&boxid=5&email_addr=zigo%40pplchat.com&mailbox_type=POP3&server_addr=gplhost.com&login=zigo%40pplchat.com&server_addr=master
// action=dtcemail_change_pass&newpass1=&newpass2=&submit=Ok
case "modify_fetchmail":
	if(!isRandomNum($_REQUEST["boxid"]))	die("Box id is not a number!");
	if(checkMailbox($user,$host,$_REQUEST["email_addr"],
		$_REQUEST["mailbox_type"],$_REQUEST["server_addr"],
		$_REQUEST["login"],$_REQUEST["pass"])){
		$q = "UPDATE $pro_mysql_fetchmail_table SET pop3_email='".$_REQUEST["email_addr"]."',
		pop3_server='".$_REQUEST["server_addr"]."',pop3_login='".$_REQUEST["login"]."',pop3_pass='".$_REQUEST["pass"]."' WHERE domain_user='$user' AND domain_name='$host' AND id='".$_REQUEST["boxid"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}
	break;

case "del_fetchmail":
	if(!isRandomNum($_REQUEST["boxid"]))	die("Box id is not a number!");
	$q = "DELETE FROM $pro_mysql_fetchmail_table WHERE domain_user='$user' AND domain_name='$host' AND id='".$_REQUEST["boxid"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	break;
*/
case "edit_bounce_msg":
//&action=edit_bounce_msg&bounce_msg=Hello%2C%0D%0AYou+have+tried+to+write+an+email+to+me%2C+and+because+of+the+big+amount%0D%0Aof+spam+I+recieved%2C+I+use+an+antispam+software+that+require+a+message%0D%0Aconfirmation.+This+is+very+easy%2C+and+you+will+have+to+do+it+only+once.%0D%0AJust+click+on+the+following+link%2C+copy+the+number+you+see+on+the%0D%0Ascreen+and+I+will+recieve+the+message+you+sent+me.+If+you+do+not%0D%0Aclick%2C+then+your+message+will+be+considered+as+advertising+and+I+will%0D%0ANOT+recieve+it.%0D%0A%0D%0A***URL***%0D%0A%0D%0AThank+you+for+your+understanding.%0D%0A
	if(strstr($_REQUEST["bounce_msg"],"***URL***")){
		$q = "UPDATE $pro_mysql_pop_table SET bounce_msg='".mysql_real_escape_string($_REQUEST["bounce_msg"])."' WHERE id='$user' AND mbox_host='$host';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		echo "Bounce message MUST contain ***URL***";
	}
	break;


case "dtcemail_change_pass":
	if(!isDTCPassword($_REQUEST["newpass1"]))	die("Incorrect password format!");
	if(!isDTCPassword($_REQUEST["newpass2"]))	die("Incorrect password format!");
	if($_REQUEST["newpass1"] != $_REQUEST["newpass2"])	die("Password 1 does not match password 2!");
	$crypted_pass = crypt($_REQUEST["newpass1"]);
	$q = "UPDATE $pro_mysql_pop_table SET crypt='$crypted_pass', passwd='".$_REQUEST["newpass1"]."' WHERE id='$user' AND mbox_host='$host';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$q = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',gen_qmail='yes',restart_qmail='yes' WHERE 1";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$adm_email_pass = $_REQUEST["newpass1"];
	$_REQUEST["adm_email_pass"] = $_REQUEST["newpass1"];
	break;

// action=dtcemail_set_deliver_local&setval=no
case "dtcemail_set_deliver_local":
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
	if(isValidEmail($_REQUEST["redirect1"]))	$redir1 = $_REQUEST["redirect1"];	else	$redir1 = "";
	if(isValidEmail($_REQUEST["redirect2"]))	$redir2 = $_REQUEST["redirect2"];	else	$redir2 = "";

	$q = "UPDATE $pro_mysql_pop_table SET redirect1='$redir1',redirect2='$redir2' WHERE id='$user' AND mbox_host='$host' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	writeDotQmailFile($user,$host);
	break;
case "dtcemail_vacation_msg":
	if($_REQUEST["use_vacation_msg"] == "yes"){
		$use_vacation_msg = "yes";
	}else{
		$use_vacation_msg = "no";
	}
	$q = "UPDATE $pro_mysql_pop_table SET vacation_flag='$use_vacation_msg',vacation_text='".mysql_real_escape_string($_REQUEST["vacation_msg_txt"])."'
	WHERE id='$user' AND mbox_host='$host' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$q = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',gen_qmail='yes' WHERE 1";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	break;
case "dtcemail_spambox":
	if($_REQUEST["spam_mailbox_enable"] == "yes"){
		$spambox_enable = "yes";
	}else{
		$spambox_enable = "no";
	}
	if(!isDTCPassword($_REQUEST["spam_mailbox"])){
		echo "Wrong spam folder format";
		break;
	}
	$q = "UPDATE $pro_mysql_pop_table SET spam_mailbox_enable='$spambox_enable',spam_mailbox='".$_REQUEST["spam_mailbox"]."'
	WHERE id='$user' AND mbox_host='$host' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$q = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',gen_qmail='yes' WHERE 1";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	break;
default:
	break;
}
}

?>
