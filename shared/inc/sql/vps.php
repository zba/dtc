<?php

function remoteVPSAction($vps_node,$vps_name,$action){
  $soap_client = connectToVPSServer($vps_node,$vps_name);
  if($soap_client === false){
    echo "<font color=\"red\">Could not connect to VPS server!</font>";
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
//    echo "Result: ".print_r($r);
  }else{
    echo "Error: ".$err;
  }
}

if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "shutdown_vps" || $_REQUEST["action"] == "destroy_vps" || $_REQUEST["action"] == "start_vps")){
  if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) == true){
    remoteVPSAction($vps_node,$vps_name,$_REQUEST["action"]);
  }else{
    $submit_err = "Access not granted line ".__LINE__." file ".__FILE__;
  }
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_xm_console_ssh_passwd"){
  if(!isDTCPassword($_REQUEST["new_password"])){
    $submit_err = "The password you have submited is not a valid password!";
    $commit_flag = "no";
  }
  if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
    $submit_err = "Access not granted line ".__LINE__." file ".__FILE__;
    $commit_flag = "no";
  }
  if($commit_flag == "yes"){
    $soap_client = connectToVPSServer($vps_node,$vps_name);
    if($soap_client === false){
      echo "<font color=\"red\">Could not connect to VPS server!</font>";
      return;
    }
    $r = $soap_client->call("changeVPSxmPassword",array("vpsname" => "xen".$vps_name,"password" => $_REQUEST["new_password"]),"","","");
    $err = $soap_client->getError();
    if(!$err){
    }else{
      echo "Error: ".$err;
    }
  }
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "fsck_vps"){
  if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
    $submit_err = "Access not granted line ".__LINE__." file ".__FILE__;
    $commit_flag = "no";
  }
  if($commit_flag == "yes"){
    $soap_client = connectToVPSServer($vps_node,$vps_name);
    if($soap_client === false){
      echo "<font color=\"red\">Could not connect to VPS server!</font>";
      return;
    }
//    $r = $soap_client->call("changeVPSxmPassword",array("vpsname" => "xen".$vps_name,"password" => $_REQUEST["new_password"]),"","","");
  }
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "reinstall_vps"){
  if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
    $submit_err = "Access not granted line ".__LINE__." file ".__FILE__;
    $commit_flag = "no";
  }
  if($commit_flag == "yes"){
    $soap_client = connectToVPSServer($vps_node,$vps_name);
    if($soap_client === false){
      echo "<font color=\"red\">Could not connect to VPS server!</font>";
      return;
    }
//    $r = $soap_client->call("changeVPSxmPassword",array("vpsname" => "xen".$vps_name,"password" => $_REQUEST["new_password"]),"","","");
  }
}

?>