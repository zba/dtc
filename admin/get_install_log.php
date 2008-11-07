<?php

// get_install_log.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_name

$panel_type = "admin";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");

$vps_name = $_REQUEST["vps_name"];
$vps_node = $_REQUEST["vps_node"];
if(!isRandomNum($vps_name)){
	die("Not a vps number...");
}
if(!isHostname($vps_node)){
	die("Not a vps name...");
}

if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) == false){
	die("Login, pass, vps number or node name incorrect: access not granted!");
}
$soap_client = connectToVPSServer($vps_node);
if($soap_client != false){
	// getVPSInstallLog(vpsname,numlines)
	$r = $soap_client->call("getVPSInstallLog",array("vpsname" => $vps_name,"numlines" => "0"),"","","");
	$err = $soap_client->getError();
	if($err){
		die("Could not get VPS install log. Error: ".$err);
	}
	// prepare to send JSON
	header('Content-type: application/json');
	// disable cookies (so script works for privacy conscious users too)
	ini_set('session.use_cookies', false);
	// start ongoing or new session
	if (isset($_GET["PHPSESSID"])){
		session_id($_GET["PHPSESSID"]);
	}else{
		session_id(date("dgis"));
	}
	session_start();

	// initialise $_SESSION on first run
	if (!isset($_SESSION['callSID'])){
		$_SESSION['callSID'] = SID;
		$_SESSION['lastlog'] = "";
	}

	sleep(2); // delay AJAX refresh 2 seconds - FIXME change to what you want

	$vps_remote_info = getVPSInfo($vps_node,$vps_name,$soap_client);
	if($vps_remote_info != "mkos"){
		$_SESSION['callSID']=''; // install finished, set termination signal
	}
	$_SESSION['lastlog'] = $soap_client->call("getVPSInstallLog",array("vpsname" => $vps_name,"numlines" => "20"),"","",""); // get last 20 log lines

	echo json_encode($_SESSION); // send callSID and lastlog back to the browser
//	print_r($r);
}else{
	die("Couldn't connect to VPS node $vps_node !");
}



?>