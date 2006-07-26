<?php

function checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name){
  global $pro_mysql_vps_table;
  checkLoginPass($adm_login,$adm_pass);
  $q = "SELECT * FROM $pro_mysql_vps_table WHERE owner='$adm_login' AND vps_server_hostname='".addslashes($vps_node)."' AND vps_xen_name='".addslashes($vps_node)."';";
  $r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  if($n == 1){
    return true;
  }else{
    die("You don't have the credential to operate on this VM!");
    return false;
  }
}

function remoteVPSAction($vps_node,$vps_name,$action){
  global $pro_mysql_vps_server_table;
  $q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='$vps_node';";
  $r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  if($n != 1){
    die("Cannot find hostname of VPS server line ".__LINE__." file ".__FILE__);
  }
  $a = mysql_fetch_array($r);
  $port = 8089;
  $soap_client = new soapclient("https://$hostname:$port/");
  $soap_client->setCredentials($a["soap_login"], $a["soap_pass"]);
  switch($action){
  case "start_vps":
    $sr = $soap_client->call("startVPS",array("vpsname" => $vps_name),"","","");
    break;
  case "destroy_vps":
    $sr = $soap_client->call("destroyVPS",array("vpsname" => $vps_name),"","","");
    break;
  case "start_vps":
    $sr = $soap_client->call("shutdownVPS",array("vpsname" => $vps_name),"","","");
    break;
  default:
    break;
  }
  $err = $soap_client->getError();
  if(!$err){
    echo "Result: ".print_r($r);
  }else{
    echo "Error: ".$err;
  }
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "shutdown_vps"){
  checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "destroy_vps"){
  checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "start_vps"){
  checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
}

?>