<?php

function drawAdminTools_VPS($admin,$vps){
  global $vps_name;
  global $vps_node;

  global $adm_login;
  global $adm_pass;
  global $rub;
  global $addrlink;

  global $vps_soap_err;

  global $pro_mysql_product_table;

  $out = "";

  $checker = checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
  if($checker != true){
    return "Credential not correct: can't display in file ".__FILE__." line ".__LINE__;
  }

  $soap_client = connectToVPSServer($vps_node,$vps_name);

  $vps_out = "";
  if($soap_client != false){
    $vps_remote_info = getVPSInfo($vps_node,$vps_name,$soap_client);
//    print_r($vps_remote_info);

    if($vps_remote_info == false){
      $vps_out .= "Could not get remote status. Maybe the VPS is not running...<br><br>";
    }else{
      $uptime = substr($vps_remote_info["up_time"],0,strpos($vps_remote_info["up_time"],"."));
      $uptime_s = $uptime % 60;
      $uptime_m = round($uptime/60) % 60;
      $uptime_h = round($uptime/3600) % 24;
      $uptime_j = round($uptime/86400);
      if($uptime_s > 1)	$upt_s_s = "s";	else	$upt_s_s = "";
      if($uptime_m > 1)	$upt_s_m = "s";	else	$upt_s_m = "";
      if($uptime_h > 1)	$upt_s_h = "s";	else	$upt_s_h = "";
      if($uptime_j > 1)	$upt_s_j = "s";	else	$upt_s_j = "";

      $vps_out .= "VM id: ".$vps_remote_info["id"]."<br>";
      $vps_out .= "Name: ".$vps_remote_info["name"]."<br>";
      $vps_out .= "Memory: ".$vps_remote_info["memory"]."<br>";
      if(isset($vps_remote_info["maxmem"])){
        $vps_out .= "Maxmem: ".$vps_remote_info["maxmem"]."<br>";
      }else{
        $vps_out .= "Maxmem: cannot fetch (maybe boot in progress?)<br>";
      }
      if(isset($vps_remote_info["cpu"])){
        $vps_out .= "CPU: ".$vps_remote_info["cpu"]."<br>";
      }else{
        $vps_out .= "CPU: cannot fetch (maybe boot in progress?)<br>";
      }
      if(isset($vps_remote_info["state"])){
        $vps_out .= "State: ".$vps_remote_info["state"]."<br>";
      }else{
        $vps_out .= "State: cannot fetch (maybe boot in progress?)<br>";
      }
      $vps_out .= "Up time: $uptime_j day$upt_s_j $uptime_h hour$upt_s_h $uptime_m minute$upt_s_m $uptime_s seconde$upt_s_s<br>";
      $vps_out .= "Start date: ".date("Y-m-d H:i:s",substr($vps_remote_info["start_time"],0,strlen($vps_remote_info["start_time"])-2))."<br><br>";
    }
  }else{
    $vps_out .= "Could not connect to the VPS SOAP Server.";
  }
  
  $frm_start = "<form action=\"?\">
  <input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
  <input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
  <input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";

// Display the current contract
  $q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$vps["product_id"]."';";
  $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  if($n == 1){
    $vps_prod = mysql_fetch_array($r);
    $contract = $vps_prod["name"];
  }else{
    $contact = "not found!";
  }
  $out .= "<b><u>Current contract:</u></b><br>$contract<br><br>";

  // Expiration management !
  $ar = explode("-",$vps["expire_date"]);
  $out .= "<b><u>Expiration date:</u></b><br>";
  $out .= "Your VPS was first registered on the: ".$vps["start_date"]."<br>";
  if(date("Y") > $ar[0] ||
      (date("Y") == $ar[0] && date("m") > $ar[1]) ||
      (date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
    $out .= "<font color=\"red\">"."Your VPS has expired on the: ".$vps["expire_date"]."</font>"
      ."<br>Please renew with one of the following options:<br>";
  }else{
    $out .= "Your VPS will expire on the: ".$vps["expire_date"];
  }

  // Renewal buttons
  $q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$vps["product_id"]."';";
  $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  for($i=0;$i<$n;$i++){
    $a = mysql_fetch_array($r);
    $out .= "<form action=\"/dtc/new_account.php\">
  <input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
  <input type=\"hidden\" name=\"renew_type\" value=\"vps\">
  <input type=\"hidden\" name=\"product_id\" value=\"".$a["id"]."\">
  <input type=\"hidden\" name=\"vps_id\" value=\"".$vps["id"]."\">
  <input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
  <input type=\"submit\" value=\"".$a["name"]."\">
  </form>";
  }

  $out .= "<b><u>CPU and Network usage:</u></b><br>
<a target=\"_blank\" href=\"http://".$vps["vps_server_hostname"]."/dtc-xen/\">http://".$vps["vps_server_hostname"]."/dtc-xen/</a><br><br>";
  $out .= "<b><u>Current VPS status:</b></u><br>";
  $out .= $vps_out;
  $out .= "<b><u>Start/stop VPS:</u></b><br>";
  if($vps_remote_info == true){
    $out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"shutdown_vps\"
  <input type=\"submit\" value=\"Gracefully shutdown (xm shutdown)\">
  </form>";
    $out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"destroy_vps\"
  <input type=\"submit\" value=\"Immediate kill (xm destroy)\">
  </form>";
  }else{
    $out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"start_vps\"
  <input type=\"submit\" value=\"Boot up (xm start)\">
  </form>";
  }

  $out .= "<b><u>Physical console last display and ssh access:</u></b><br>";

  $out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_passwd\">
  New password: <input type=\"password\" name=\"new_password\" value=\"\"><input type=\"submit\" value=\"Ok\">
  </form>";
  $out .= "To access to your console, first setup a password above, and then ssh to:<br>xen".$vps_name."@".$vps_node."<br><br>";

  $out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
  <tr><td bgcolor=\"black\"><font color=\"white\">$vps_node:$vps_name</font></td>
  <tr><td bgcolor=\"black\"><font color=\"white\"><pre>...</pre></font></td>
  </table>";
  return $out;
}

?>