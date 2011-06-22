#!/usr/bin/env php
<?php

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

$script_start_time = time();
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

// Send a mail to the admin with the error message
function sendAdminWarning($message){
	global $conf_webmaster_email_addr;

	global $conf_message_subject_header;

	$headers = "From: ".$conf_webmaster_email_addr;
	mail($conf_webmaster_email_addr,"$conf_message_subject_header Reminder warning message!",$message,$headers);
}

function getCustomizedReminder($msg,$client,$remaining_days,$expiration_date,$adm_login){

	$msg_2_send = getCustomizedReminderNoHeader($msg,$client,$remaining_days,$expiration_date,$adm_login);

	// Add signature to the email
	$signature = readCustomizedMessage("signature",$adm_login);
	$msg_2_send = str_replace("%%%SIGNATURE%%%",$signature,$msg_2_send);

	// Manage the header of the messages
	$head = readCustomizedMessage("messages_header",$adm_login);
	$msg_2_send = $head.$msg_2_send;

	return $msg_2_send;
}

function getCustomizedReminderNoHeader($msg,$client,$remaining_days,$expiration_date,$adm_login){
	global $conf_administrative_site;
	global $conf_use_ssl;

	$msg_2_send = $msg;
	$msg_2_send = str_replace("%%%FIRST_NAME%%%",$client,$msg_2_send);
	if($remaining_days > 0){
		$msg_2_send = str_replace("%%%REMAINING_DAYS%%%",$remaining_days,$msg_2_send);
	}else if($remaining_days == 0){
	}else{
		$how_many_days = 0 - $remaining_days;
		$msg_2_send = str_replace("%%%EXPIRED_DAYS%%%",$how_many_days,$msg_2_send);
	}
	$msg_2_send = str_replace("%%%EXPIRATION_DATE%%%",$expiration_date,$msg_2_send);
	$msg_2_send = str_replace("%%%DTC_ADM_LOGIN%%%",$adm_login,$msg_2_send);
	if($conf_use_ssl == "yes"){
		$surl = "s";
	}else{
		$surl = "";
	}
	$msg_2_send = str_replace("%%%DTC_CLIENT_URL%%%","http".$surl."://".$conf_administrative_site."/dtc/",$msg_2_send);

	return $msg_2_send;
}

// Send all the mail for VPS for a given renew period
function sendVPSReminderEmail($remaining_days,$file,$send_webmaster_copy="no"){
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_vps_table;
	global $conf_webmaster_email_addr;
	global $conf_vps_renewal_lastwarning;

	global $conf_message_subject_header;
	global $dtcshared_path;

	global $send_email_header;

	$now_timestamp = mktime();
	$one_day = 3600 * 24;
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE expire_date='".date("Y-m-d",$now_timestamp + $one_day*$remaining_days)."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$vps = mysql_fetch_array($r);

		// Shutdown the VPS if it reaches the shutdown warning
		if($remaining_days == -$conf_vps_renewal_lastwarning){
			remoteVPSAction($vps["vps_server_hostname"],$vps["vps_xen_name"],"shutdown_vps");
		}

		// Get the admin
		$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$vps["owner"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			sendAdminWarning("Could not get admin_login ".$vps["owner"]." line ".__LINE__." file ".__FILE__);
			continue;
		}
		$admin = mysql_fetch_array($r2);
		if($admin["id_client"] == 0){
			sendAdminWarning("Admin ".$vps["owner"]." has no client id (".$admin["id_client"].") line ".__LINE__." file ".__FILE__);
			continue;
		}

		// Get the client
		$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			sendAdminWarning("Could not find id client ".$admin["id_client"]." for admin ".$vps["owner"]." line ".__LINE__." file ".__FILE__);
			continue;
		}
		$client = mysql_fetch_array($r2);

		// Write the email
		$msg_2_send = readCustomizedMessage($file,$admin["adm_login"]);
		$msg_2_send = getCustomizedReminder($msg_2_send,$client["christname"],$remaining_days,$vps["expire_date"],$admin["adm_login"]);
		$msg_2_send = str_replace("%%%VPS_NUMBER%%%",$vps["vps_xen_name"],$msg_2_send);
		$msg_2_send = str_replace("%%%VPS_NODE%%%",$vps["vps_server_hostname"],$msg_2_send);

		$headers = $send_email_header;
		$headers .= "From: ".$conf_webmaster_email_addr;
		$subject = readCustomizedMessage("reminders_msg/vps_subject",$admin["adm_login"]);
		$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
		mail($client["email"],"$conf_message_subject_header $subject",$msg_2_send,$headers);
		if($send_webmaster_copy == "yes"){
			$subject = readCustomizedMessage("reminders_msg/vps_subject_adm",$admin["adm_login"]);
			$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
			$subject = str_replace("%%%VPS_NUMBER%%%",$vps["vps_xen_name"],$subject);
			$subject = str_replace("%%%VPS_NODE%%%",$vps["vps_server_hostname"],$subject);
			mail($conf_webmaster_email_addr,"$conf_message_subject_header $subject",$msg_2_send,$headers);
		}
	}
}

////////////////////
// VPS EXPIRATION //
////////////////////
// Send reminders before expiration
$before = explode("|",$conf_vps_renewal_before);
$n = sizeof($before);
for($i=0;$i<$n;$i++){
	sendVPSReminderEmail($before[$i],"reminders_msg/vps_will_expire");
}
// Send reminders the day of the expiration
sendVPSReminderEmail(0,"reminders_msg/vps_expired_today","no");
// Send reminders after expiration
$after = explode("|",$conf_vps_renewal_after);
$n = sizeof($after);
for($i=0;$i<$n;$i++){
	$days = 0 - $after[$i];
	sendVPSReminderEmail($days,"reminders_msg/vps_expired_already");
}
// Send reminders for last warning
sendVPSReminderEmail(-$conf_vps_renewal_lastwarning,"reminders_msg/vps_expired_last_warning","yes");
// Send the shutdown message
sendVPSReminderEmail(-$conf_vps_renewal_shutdown,"reminders_msg/vps_expired_shutdown","yes");

// Send all the mail for dedicated server for a given renew period
function sendDedicatedReminderEmail($remaining_days,$file,$send_webmaster_copy="no"){
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_dedicated_table;
	global $conf_webmaster_email_addr;

	global $conf_message_subject_header;
	global $dtcshared_path;

	global $send_email_header;

	$now_timestamp = mktime();
	$one_day = 3600 * 24;
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE expire_date='".date("Y-m-d",$now_timestamp + $one_day*$remaining_days)."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$dedicated = mysql_fetch_array($r);

		// Get the admin
		$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$dedicated["owner"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			sendAdminWarning("Could not get admin_login ".$dedicated["owner"]." line ".__LINE__." file ".__FILE__);
			continue;
		}
		$admin = mysql_fetch_array($r2);
		if($admin["id_client"] == 0){
			sendAdminWarning("Admin ".$dedicated["owner"]." has no client id (".$admin["id_client"].") line ".__LINE__." file ".__FILE__);
			continue;
		}

		// Get the client
		$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			sendAdminWarning("Could not find id client ".$admin["id_client"]." for admin ".$dedicated["owner"]." line ".__LINE__." file ".__FILE__);
			continue;
		}
		$client = mysql_fetch_array($r2);

		// Write the email
		$msg_2_send = readCustomizedMessage($file,$admin["adm_login"]);
		$msg_2_send = getCustomizedReminder($msg_2_send,$client["christname"],$remaining_days,$dedicated["expire_date"],$admin["adm_login"]);
		$msg_2_send = str_replace("%%%SERVER_HOSTNAME%%%",$dedicated["server_hostname"],$msg_2_send);

		$headers = $send_email_header;
		$headers .= "From: ".$conf_webmaster_email_addr;
		$subject = readCustomizedMessage("reminders_msg/server_subject",$admin["adm_login"]);
		$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
		mail($client["email"],"$conf_message_subject_header $subject",$msg_2_send,$headers);
		if($send_webmaster_copy == "yes"){
			$subject = readCustomizedMessage("reminders_msg/server_subject_adm",$admin["adm_login"]);
			$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
			$subject = str_replace("%%%SERVER_NAME%%%",$dedicated["server_hostname"],$subject);
			mail($conf_webmaster_email_addr,"$conf_message_subject_header $subject",$msg_2_send,$headers);
		}
	}
}

//////////////////////////////////
// DEDICATED SERVERS EXPIRATION //
//////////////////////////////////
// Send reminders before expiration
$before = explode("|",$conf_vps_renewal_before);
$n = sizeof($before);
for($i=0;$i<$n;$i++){
	sendDedicatedReminderEmail($before[$i],"reminders_msg/server_will_expire");
}
// Send reminders the day of the expiration
sendDedicatedReminderEmail(0,"reminders_msg/server_expired_today","no");
// Send reminders after expiration
$after = explode("|",$conf_vps_renewal_after);
$n = sizeof($after);
for($i=0;$i<$n;$i++){
	$days = 0 - $after[$i];
	sendDedicatedReminderEmail($days,"reminders_msg/server_expired_already");
}
// Send reminders for last warning
sendDedicatedReminderEmail(-$conf_vps_renewal_lastwarning,"reminders_msg/server_expired_last_warning","yes");
// Send the shutdown message
sendDedicatedReminderEmail(-$conf_vps_renewal_shutdown,"reminders_msg/server_expired_shutdown","yes");

///////////////////////////////
// SHARED HOSTING EXPIRATION //
///////////////////////////////
function sendSharedHostingReminderEmail($remaining_days,$file,$send_webmaster_copy="no"){
	global $pro_mysql_admin_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_client_table;

	global $conf_webmaster_email_addr;
	global $conf_message_subject_header;
	global $dtcshared_path;

	global $send_email_header;

	$now_timestamp = mktime();
	$one_day = 3600 * 24;
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE expire='".date("Y-m-d",$now_timestamp + $one_day*$remaining_days)."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$admin = mysql_fetch_array($r);

		// Check to see if the user has some domain name hosted (it could be an admin only for VPS or Dedicated...)
		$q2 = "SELECT * FROM $pro_mysql_domain_table WHERE owner='".$admin["adm_login"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 < 0){
			continue;
		}

		$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			sendAdminWarning("Could not find id client ".$admin["id_client"]." for admin ".$vps["owner"]." line ".__LINE__." file ".__FILE__);
			continue;
		}
		$client = mysql_fetch_array($r2);

		// Write the email
		$msg_2_send = readCustomizedMessage($file,$admin["adm_login"]);
		$msg_2_send = getCustomizedReminder($msg_2_send,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);

		$headers = $send_email_header;
		$headers .= "From: ".$conf_webmaster_email_addr;
		$subject = readCustomizedMessage("reminders_msg/shared_subject",$admin["adm_login"]);
		$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
		mail($client["email"],"$conf_message_subject_header $subject",$msg_2_send,$headers);
		if($send_webmaster_copy == "yes"){
			$subject = readCustomizedMessage("reminders_msg/shared_subject_adm",$admin["adm_login"]);
			$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
			mail($conf_webmaster_email_addr,"$conf_message_subject_header $subject",$msg_2_send,$headers);
		}
	}
}

// Send reminders before expiration
$before = explode("|",$conf_shared_renewal_before);
$n = sizeof($before);
for($i=0;$i<$n;$i++){
	sendSharedHostingReminderEmail($before[$i],"reminders_msg/shared_will_expire");
}
// Send reminder the day of expiration
sendSharedHostingReminderEmail(0,"reminders_msg/shared_expired_today","no");
// Send reminders after expiration
$after = explode("|",$conf_shared_renewal_after);
$n = sizeof($after);
for($i=0;$i<$n;$i++){
	$days = 0 - $after[$i];
	sendSharedHostingReminderEmail($days,"reminders_msg/shared_expired_already");
}
// Send last warning
sendSharedHostingReminderEmail(-$conf_shared_renewal_lastwarning,"reminders_msg/shared_expired_last_warning","yes");
// Send rexpiration reminder
sendSharedHostingReminderEmail(-$conf_shared_renewal_shutdown,"reminders_msg/shared_expired_shutdown","yes");

// Send all the mail for custom products for a given renew period
function sendCustomProductsReminderEmail($remaining_days,$file,$cust_heb_type_id,$send_webmaster_copy="no"){
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_custom_product_table;
	global $conf_webmaster_email_addr;

	global $conf_message_subject_header;
	global $dtcshared_path;

	global $send_email_header;

	$now_timestamp = mktime();
	$one_day = 3600 * 24;
	$q = "SELECT * FROM $pro_mysql_custom_product_table WHERE expire_date='".date("Y-m-d",$now_timestamp + $one_day*$remaining_days)."' and custom_heb_type='".$cust_heb_type_id."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$cust_pr = mysql_fetch_array($r);

		// Get the admin
		$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$cust_pr["owner"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			sendAdminWarning("Could not get admin_login ".$cust_pr["owner"]." line ".__LINE__." file ".__FILE__);
			continue;
		}
		$admin = mysql_fetch_array($r2);
		if($admin["id_client"] == 0){
			sendAdminWarning("Admin ".$cust_pr["owner"]." has no client id (".$admin["id_client"].") line ".__LINE__." file ".__FILE__);
			continue;
		}

		// Get the client
		$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			sendAdminWarning("Could not find id client ".$admin["id_client"]." for admin ".$cust_pr["owner"]." line ".__LINE__." file ".__FILE__);
			continue;
		}
		$client = mysql_fetch_array($r2);

		// Write the email
		$msg_2_send = readCustomizedMessage($file,$admin["adm_login"]);
		$msg_2_send = getCustomizedReminder($msg_2_send,$client["christname"],$remaining_days,$cust_pr["expire_date"],$admin["adm_login"]);
		$msg_2_send = str_replace("%%%DOMAIN%%%",$cust_pr["domain"],$msg_2_send);

		$headers = $send_email_header;
		$headers .= "From: ".$conf_webmaster_email_addr;
		$subject = readCustomizedMessage("reminders_msg/custom_".$cust_heb_type_id."_subject",$admin["adm_login"]);
		$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
		mail($client["email"],"$conf_message_subject_header $subject",$msg_2_send,$headers);
		if($send_webmaster_copy == "yes"){
			$subject = readCustomizedMessage("reminders_msg/custom_".$cust_heb_type_id."_subject_adm",$admin["adm_login"]);
			$subject = getCustomizedReminderNoHeader($subject,$client["christname"],$remaining_days,$admin["expire"],$admin["adm_login"]);
			$subject = str_replace("%%%DOMAIN%%%",$cust_pr["domain"],$subject);
			mail($conf_webmaster_email_addr,"$conf_message_subject_header $subject",$msg_2_send,$headers);
		}
	}
}

////////////////////////////////////////
// CUSTOM PRODUCTS SERVERS EXPIRATION //
////////////////////////////////////////
// Send reminders before expiration
global $pro_mysql_custom_heb_types_table;

$qsq = "SELECT id FROM $pro_mysql_custom_heb_types_table;";
$rsq = mysql_query($qsq)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$nsq = mysql_num_rows($rsq);
for($nid=0;$nid<$n;$nid++){
	$cust_pr_id = mysql_fetch_array($rsq);
	$before = explode("|",$conf_custom_renewal_before);
	$n = sizeof($before);
	for($i=0;$i<$n;$i++){
		sendCustomProductsReminderEmail($before[$i],"reminders_msg/custom_".$cust_pr_id['id']."_will_expire",$cust_pr_id['id']);
	}
	// Send reminders the day of the expiration
	sendCustomProductsReminderEmail(0,"reminders_msg/custom_".$cust_pr_id['id']."_expired_today",$cust_pr_id['id'],"no");
	// Send reminders after expiration
	$after = explode("|",$conf_custom_renewal_after);
	$n = sizeof($after);
	for($i=0;$i<$n;$i++){
		$days = 0 - $after[$i];
		sendCustomProductsReminderEmail($days,"reminders_msg/custom_".$cust_pr_id['id']."_expired_already",$cust_pr_id['id']);
	}
	// Send reminders for last warning
	sendCustomProductsReminderEmail(-$conf_custom_renewal_lastwarning,"reminders_msg/custom_".$cust_pr_id['id']."_expired_last_warning",$cust_pr_id['id'],"yes");
	// Send the shutdown message
	sendCustomProductsReminderEmail(-$conf_custom_renewal_shutdown,"reminders_msg/custom_".$cust_pr_id['id']."_expired_shutdown",$cust_pr_id['id'],"yes");
}
?>
