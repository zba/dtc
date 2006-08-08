<?php

$script_start_time = time();
$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

// This function is todo...
function sendAdminWarning($message){
  global $conf_webmaster_email_addr;
  $headers = "From: ".$conf_webmaster_email_addr;
  mail($conf_webmaster_email_addr,"[DTC] Reminder warning message!",$msg_2_send,$headers);
}

function sendVPSReminderEmail($remaining_days,$file){
  global $pro_mysql_admin_table;
  global $pro_mysql_client_table;
  global $pro_mysql_vps_table;
  global $conf_webmaster_email_addr;

  global $conf_use_ssl;
  global $conf_administrative_site;

  $now_timestamp = mktime();
  $one_day = 3600 * 24;
  $q = "SELECT * FROM $pro_mysql_vps_table WHERE expire_date='".date("Y-m-d",$now_timestamp + $one_day*$remaining_days)."';";
  echo $q."\n";

  $fname = "reminders_msg/".$file;
  $fp = fopen($fname,"r");
  $mesg = fread($fp,filesize($fname));
  fclose($fp);

  $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  for($i=0;$i<$n;$i++){
    $vps = mysql_fetch_array($r);

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
      sendAdminWarning("Admin has no client id (".$admin["id_client"].") line ".__LINE__." file ".__FILE__);
      continue;
    }

    // Get the client
    $q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
    $r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
    $n2 = mysql_num_rows($r2);
    if($n2 != 1){
      sendAdminWarning("Could not find id client ".$admin["id_client"]." line ".__LINE__." file ".__FILE__);
      continue;
    }
    $client = mysql_fetch_array($r2);

    // Write the email
    $msg_2_send = $mesg;
    $msg_2_send = str_replace("%%%FIRST_NAME%%%",$client["christname"],$msg_2_send);
    $msg_2_send = str_replace("%%%VPS_NUMBER%%%",$vps["vps_xen_name"],$msg_2_send);
    $msg_2_send = str_replace("%%%VPS_NODE%%%",$vps["vps_server_hostname"],$msg_2_send);
    if($remaining_days > 0){
      $msg_2_send = str_replace("%%%REMAINING_DAYS%%%",$remaining_days,$msg_2_send);
    }else if($remaining_days == 0){
    }else{
      $how_many_days = 0 - $remaining_days;
      $msg_2_send = str_replace("%%%VPS_EXPIRED_DAYS%%%",$how_many_days,$msg_2_send);
    }
    $msg_2_send = str_replace("%%%EXPIRATION_DATE%%%",$vps["expire_date"],$msg_2_send);
    if($conf_use_ssl == "yes"){
      $surl = "s";
    }else{
      $surl = "";
    }
    $msg_2_send = str_replace("%%%DTC_CLIENT_URL%%%","http".$surl."://".$conf_administrative_site."/dtc/",$msg_2_send);

    echo "Sending reminder to: ".$client["email"]."\n";
    $headers = "From: ".$conf_webmaster_email_addr;
    mail($client["email"],"[DTC] Your VPS expiration",$msg_2_send,$headers);
  }
}

// Get all the VPS that will expire in 10 and 5 days
sendVPSReminderEmail(10,"vps_will_expire.txt");
sendVPSReminderEmail(5,"vps_will_expire.txt");

// Get all the VPS that expire today
sendVPSReminderEmail(0,"vps_expired_today.txt");

// Get all the VPS that expired 3, 7 and 12 days ago
sendVPSReminderEmail(-3,"vps_expired_already.txt");
sendVPSReminderEmail(-7,"vps_expired_already.txt");
sendVPSReminderEmail(-12,"vps_expired_last_warning.txt");

// We now shutdown the VPS
sendVPSReminderEmail(-15,"vps_expired_shutdown.txt");

?>
