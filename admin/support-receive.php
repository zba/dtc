#!/usr/bin/env php
<?php

chdir(dirname(__FILE__));

$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

// This comes from the Mail_Mime PEAR package, under Debian, you need
// the php-mail-mime package to have this script work.
require_once 'Mail/mimeDecode.php';

// Email header parsing
function decodeEmail($input){
	$params['include_bodies'] = true;
	$params['decode_bodies']  = true;
	$params['decode_headers'] = true;
	$decoder = new Mail_mimeDecode($input);
	$structure = $decoder->decode($params);
	return $structure;
}

// Read the email from standard input
$msg = "";
$fp = fopen('php://stdin', 'r');
while($line = fgets($fp, 4096) ){
	$msg .= $line;
}

// Decode the msg using php-mail-mime
$stt = decodeEmail($msg);

// Get the From: header email
$flag = preg_match_all("/[\._a-zA-Z0-9+-]+@[\._a-zA-Z0-9-]+/i", $stt->headers["from"], $matches);
if($flag == 0 || sizeof($matches) != 1){
	echo("No email found in From! :(\n");
	exit(1);
}
$email_from = $matches[0][0];

// Do nothing if there's a mail from an auto-responder
if( isset($stt->headers["X-AutoReply-From"]) || isset($stt->headers["X-Mail-Autoreply") ){
	exit 0
}

// TODO: Check the Cc as well
// Get the To: header email
$flag = preg_match_all("/[\._a-zA-Z0-9+-]+@[\._a-zA-Z0-9-]+/i", $stt->headers["to"], $matches);
if($flag == 0){
	echo("No email found in To! :(\n");
	exit(1);
}
$email_to = $matches[0][0];
echo "From: $email_from To: $email_to\n";

// Build the support ticket email regexp
if( !isset($conf_support_ticket_domain) || $conf_support_ticket_domain == "default"){
	$tik_domain = $conf_main_domain;
}else{
	$tik_domain = $conf_support_ticket_domain;
}
$tik_regexp = '^' . $conf_support_ticket_email . "[-+]([a-f0-9]*)@" . $tik_domain . '$';

// This is to avoid any hacking with support mail looping to itself.
if( ereg($tik_regexp,$email_from) ){
	exit(1);
}

// Check if the To: has the support ID number in it
// emails are sent to something like: support-3bc8212a0@dtc.example.com
// and that a record really exists for it
if( ereg($tik_regexp,$email_to) ){
	// If the To: match an existing ID of a previous ticket, then we should search for that ticket
	$start = strlen($conf_support_ticket_email) + 1;
	$end = strlen($email_to) - $start - strlen($tik_domain) - 1; // Size of the email - size of "support+" - size of "@domain.tld"
	$ticket_hash = substr($email_to,$start,$end);
	if( isRandomNum($ticket_hash) ){
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE hash='$ticket_hash';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 1){
			// We have a match, we should consider inserting this ticket as a reply...
			$start_tik = mysql_fetch_array($r);
			$last_id = findLastTicketID($ticket_hash);
			if($last_id != 0){
				$q = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,in_reply_of_id,reply_id,admin_or_user,text,initial_ticket)
				VALUES('','".$start_tik["adm_login"]."','".date('Y-m-d')."','".date('H-m-i')."','$last_id','0','user','". mysql_real_escape_string($stt->body) ."','no');";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$new_id = mysql_insert_id();
				$q = "UPDATE $pro_mysql_tik_queries_table SET reply_id='$new_id' WHERE id='$last_id';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				mailTicketToAllAdmins($start_tik["subject"],$stt->body,$start_tik["adm_login"]);
				exit(0);
			}
		}
	}
}
echo "Not an old ticket, searching for a matching customer\n";
$q = "SELECT id FROM $pro_mysql_client_table WHERE email='$email_from';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
// A matching email has been found
if($n == 1){
	$a = mysql_fetch_array($r);
	$q = "SELECT adm_login FROM $pro_mysql_admin_table WHERE id_client='".$a["id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	// At this point, we got an exact match: let's create a new ticket for this adm_login!
	if($n == 1){
		$adm = mysql_fetch_array($r);
		$q = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,in_reply_of_id,reply_id,admin_or_user,text,initial_ticket,hash,subject)
		VALUES('','".$adm["adm_login"]."','".date('Y-m-d')."','".date('H-m-i')."','0','0','user','". mysql_real_escape_string($stt->body) ."','yes','".createSupportHash()."','". mysql_real_escape_string($stt->headers["subject"]) ."');";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		mailTicketToAllAdmins($stt->headers["subject"],$stt->body,$adm["adm_login"]);
		exit(0);
	}
// If nothing matches, then we want to create a new ticket associated with
// this email address.
}else{
	$q = "INSERT INTO $pro_mysql_tik_queries_table (id,customer_email,date,time,in_reply_of_id,reply_id,admin_or_user,text,initial_ticket,hash,subject)
	VALUES('','$email_from','".date('Y-m-d')."','".date('H-m-i')."','0','0','user','". mysql_real_escape_string($stt->body) ."','yes','".createSupportHash()."','". mysql_real_escape_string($stt->headers["subject"]) ."');";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	mailTicketToAllAdmins($stt->headers["subject"],$stt->body,$email_from);
}
exit(0);

?>