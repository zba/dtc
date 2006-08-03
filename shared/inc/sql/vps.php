<?php

function remoteVPSAction($vps_node,$vps_name,$action){
  $soap_client = connectToVPSServer($vps_node,$vps_name);
  if($soap_client === false){
    return;
  }
  switch($action){
  case "start_vps":
    $r = $soap_client->call("startVPS",array("vpsname" => $vps_name),"","","");
    break;
  case "destroy_vps":
    $r = $soap_client->call("destroyVPS",array("vpsname" => $vps_name),"","","");
    break;
  case "start_vps":
    $r = $soap_client->call("shutdownVPS",array("vpsname" => $vps_name),"","","");
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