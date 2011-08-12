<?php
/* Important! Can't use 'die' or any other mechanism
   that sends raw (not json encoded) text to browser.

   Also NOT ANY output permitted prior to sending of header. */

$panel_type="client";

// prepare to send JSON
header('Content-type: application/json');
// disable cookies (so script works for privacy conscious users too)
ini_set('session.use_cookies', false);

// start ongoing or new session
if (isset($_GET["PHPSESSID"]))
  session_id($_GET["PHPSESSID"]);
else
  session_id(date("dgis"));
@session_start();

// initialise $_SESSION on first run
if (!isset($_SESSION['callSID'])){
  $_SESSION['callSID'] = SID;
  $_SESSION['lastlog'] = "Starting Install!";
  $_SESSION['vps_name'] = $_REQUEST["vps_name"];
  $_SESSION['vps_node'] = $_REQUEST["vps_node"];
  $delay = 0;
} else $delay=2;

require_once("../shared/autoSQLconfig.php");
require_once("../shared/dtc_lib.php");

$errorset=false;

function terminate($errString){
global $errorset;
$_SESSION['lastlog'] = $errString;
$_SESSION['callSID']='';
$errorset=true;
}

$reg = '/^((([a-z0-9]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$/';
if(!preg_match($reg,$_SESSION['vps_node'])){
	die("Invalid vps_node");
}
if(!preg_match("/^([_a-z0-9]+)([_.a-z0-9-]*)([_.a-z0-9]+)\$/",$_SESSION["vps_name"])){
	die("Invalid vps_name");
}

$soap_client = connectToVPSServer($_SESSION['vps_node']);

if($soap_client != false){

  $err = $soap_client->getError();

  if($err)
    terminate("Could not get VPS install log. Error: ".$err);
  else{
    sleep($delay); // delay AJAX refresh 2 seconds - FIXME change to what you want
    $vps_remote_info = getVPSInfo($_SESSION['vps_node'],$_SESSION['vps_name'],$soap_client);
    if($vps_remote_info != "mkos")
      $_SESSION['callSID']=''; // install finished, set termination signal
    $_SESSION['lastlog'] = $soap_client->call("getVPSInstallLog",array("vpsname" => $_SESSION['vps_name'],"numlines" => "20"),"","",""); // get last 20 log lines
  }
}
else
  terminate("Couldn't connect to VPS node $vps_node !");

echo json_encode($_SESSION); // send callSID and lastlog back to the browser

if($_SESSION['callSID']=='') // kill session if done
	session_destroy();
?>
