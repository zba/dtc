<?php

function remoteVPSAction($vps_node,$vps_name,$action){
  $soap_client = connectToVPSServer($vps_node,$vps_name);
  if($soap_client === false){
    return;
  }
  echo $action;
  switch($action){
  case "start_vps":
    $r = $soap_client->call("startVPS",array("vpsname" => "xen".$vps_name),"","","");
    break;
  case "destroy_vps":
    $r = $soap_client->call("destroyVPS",array("vpsname" => "xen".$vps_name),"","","");
    break;
  case "shutdown_vps":
    $r = $soap_client->call("shutdownVPS",array("vpsname" => "xen".$vps_name),"","","");
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

if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "shutdown_vps" || $_REQUEST["action"] == "destroy_vps" || $_REQUEST["action"] == "start_vps")){
  if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) == true){
    remoteVPSAction($vps_node,$vps_name,$_REQUEST["action"]);
  }else{
    $submit_err = "Access not granted!";
  }
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "destroy_vps"){
  checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "start_vps"){
  checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
}

?>