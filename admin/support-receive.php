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

// Check if the To: has the support ID number in it
// emails are sent to something like: support-3bc8212a0@dtc.example.com

// $struct->headers["to"]




// If the To: doesn't match an existing ID, then we should search if the
// email of the From: matches one of the existing customers




// If nothing matches, then we want to create a new ticket associated with
// this email address.



?>