<?php

function checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name){
	global $pro_mysql_vps_table;
	checkLoginPass($adm_login,$adm_pass);
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE owner='$adm_login' AND vps_server_hostname='".mysql_real_escape_string($vps_node)."' AND vps_xen_name='".mysql_real_escape_string($vps_name)."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		return true;
	}else{
		return false;
	}
}

function connectToVPSServer($vps_node){
	global $pro_mysql_vps_server_table;
	$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='$vps_node';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find hostname $vps_node of VPS server line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	$port = 8089;
	$soap_client = new nusoap_client("https://$vps_node:$port/");
	$err = $soap_client->getError();
	if($err){
		echo "Error: ".$err;
		return false;
	}

	$soap_client->setCredentials($a["soap_login"], $a["soap_pass"]);
	$err = $soap_client->getError();
	if($err){
		echo "Error: ".$err;
		return false;
	}
	return $soap_client;
}

function isVPSNodeLVMEnabled($vps_node){
	global $pro_mysql_vps_server_table;
	$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='$vps_node';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find hostname of VPS server line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	if (isset($a["lvmenable"])){
		return $a["lvmenable"];
	}else{
		// default to yes
		return "yes";
	}
}

function getVPSIso($vps_node,$vps_name,$soap_client){
	global $vps_soap_err;
	$r = $soap_client->call("reportInstalledIso",array("vpsname" => "xen".$vps_name),"","","");
	$err = $soap_client->getError();
	if($err){
		$vps_soap_err = _("Could not get installed .iso files. Error: ").$err;
	}
	return $r;
}

function getVPSInfo($vps_node,$vps_name,$soap_client){
	global $vps_soap_err;
	$r = $soap_client->call("getVPSState",array("vpsname" => "xen".$vps_name),"","","");
	$err = $soap_client->getError();
	if($err){
		$vps_soap_err = _("Could not get virtual machine info. Error: ").$err;
		return false;
	}else{
		$out = array();
		// To see what's happening, just do a print_r($r); and you will see...
		// This type of result is seen on Xen 2
		//echo "<pre>"; print_r($r); echo "</pre>";
		if($r == "Not running"){
			$vps_soap_err = "VPS server reported VPS not running";
			return false;
		}
		if($r == "fsck" || $r == "mkos"){
			return $r;
		}
		if(isset($r["Result"])){
			$out["xen_type"] = 2;
			$rez = $r["Result"];
		// This one on Xen 3
		}else{
			$out["xen_type"] = 3;
			$rez = $r;
		}
		$n = sizeof($rez);
		for($i=0;$i<$n;$i++){
			$a = $rez[$i];
			if(is_array($a)){
				switch($a[0]){
				case "id":
					$out["id"] = $a[1];
					break;
				case "domid":
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
				case "vcpus":
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

function getInstallableOS($soap_client){
	global $vps_soap_err;
	$r = $soap_client->call("getInstallableOS",array(),"","","");
	$err = $soap_client->getError();
	if($err){
		$vps_soap_err = "Could not get installable OS data. Error: ".$err;
		return false;
	}else{
		return $r;
	}
}

function getInstallableAPP($soap_client){
	global $vps_soap_err;
	$r = $soap_client->call("getInstallableAPP",array(),"","","");
	$err = $soap_client->getError();
	if($err){
		$vps_soap_err = "Could not get installable APP data. Error: ".$err;
		return false;
	}else{
		return $r;
	}
}

?>
