<?php

function checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name){
  global $pro_mysql_vps_table;
  checkLoginPass($adm_login,$adm_pass);
  $q = "SELECT * FROM $pro_mysql_vps_table WHERE owner='$adm_login' AND vps_server_hostname='".addslashes($vps_node)."' AND vps_xen_name='".addslashes($vps_name)."';";
  $r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  if($n == 1){
    return true;
  }else{
    return false;
  }
}

function connectToVPSServer($vps_node,$vps_name){
  global $pro_mysql_vps_server_table;
  $q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='$vps_node';";
  $r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
  $n = mysql_num_rows($r);
  if($n != 1){
    die("Cannot find hostname of VPS server line ".__LINE__." file ".__FILE__);
  }
  $a = mysql_fetch_array($r);
  $port = 8089;
  $soap_client = new nusoapclient("https://$vps_node:$port/");
  $soap_client->setCredentials($a["soap_login"], $a["soap_pass"]);
  $err = $soap_client->getError();
  if($err){
    echo "Error: ".$err;
    return false;
  }
  return $soap_client;
}

function getVPSInfo($vps_node,$vps_name,$soap_client){
  global $vps_soap_err;
  $r = $soap_client->call("getVPSState",array("vpsname" => "xen".$vps_name),"","","");
  $err = $soap_client->getError();
  if($err){
    $vps_soap_err = "Could not get virtual machine info. Error: ".$err;
    return false;
  }else{
    $out = array();
    $rez = $r["Result"];
    $n = sizeof($rez);
    for($i=0;$i<$n;$i++){
      $a = $rez[$i];
      if(is_array($a)){
        switch($a[0]){
          case "id":
            $out["id"] = $a[1];
            break;
          case "name":
            $out["name"] = $a[1];
            break;
          case "memory":
            $out["memory"] = $a[1];
            break;
          case "maxmem":
            $out["maxmem"] = $a[1];
            break;
          case "state":
            $out["state"] = $a[1];
            break;
          case "cpu":
            $out["cpu"] = $a[1];
            break;
          case "up_time":
            $out["up_time"] = $a[1];
            break;
          case "start_time":
            $out["start_time"] = $a[1];
            break;
          default:
            break;
        }
      }
    }
    return $out;
  }
}

?>