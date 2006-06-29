<?php

function drawAdminTools_VPS($admin,$vps){
  global $vps_name;
  global $vps_node;

  global $adm_login;
  global $adm_pass;
  global $rub;
  global $addrlink;

  $out = "";

//  echo "<pre>";print_r($vps);echo "</pre>";

  $out .= "<b><u>Expiration date:</u></b><br>";
  $out .= "Your VPS was first registered on the: ".$vps["start_date"]."<br>";
  $out .= "Your VPS will expire on the: ".$vps["expire_date"]."";

  $out .= "<form action=\"?\">
  <input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
  <input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
  <input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
  <input type=\"hidden\" name=\"action\" value=\"renew_vps\"
  <input type=\"submit\" value=\"renew\">
  </form>";

  $out .= "<b><u>CPU usage:</u></b><br>";
  $out .= "<b><u>Network usage:</u></b><br>";
  $out .= "<b><u>Start/stop VPS:</u></b><br>";
  $out .= "Current VPS status:";
  $out .= "<form action=\"?\">
  <input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
  <input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
  <input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
  <input type=\"hidden\" name=\"action\" value=\"shutdown_vps\"
  <input type=\"submit\" value=\"stop\">
  </form>";

  $out .= "<b><u>Console last display:</u></b><br>";

  $out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
  <tr><td bgcolor=\"black\"><font color=\"white\">$vps_node:$vps_name</font></td>
  <tr><td bgcolor=\"black\"><font color=\"white\"><pre>...</pre></font></td>
  </table>";
  return $out;
}

?>