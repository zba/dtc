<?php

// Those are supposed to be filled at eatch queries, therefore they
// are global variables.

$send_email_header = "Content-Type: text/plain; charset=\"UTF-8\"\n";
// I'm not sure wich of the following 2 I should use when using UTF-8 ...
//$send_email_header .= "Content-Transfer-Encoding: 8bit\n";
$send_email_header .= "Content-Transfer-Encoding: quoted-printable\n";

if(isset($_REQUEST["rub"])){
	if(!preg_match("/^([a-z0-9]+)([.a-z_0-9-]*)([a-z0-9]+)\$/",$_REQUEST["rub"]) && $_REQUEST["rub"] != ""){
		die("Rub parameter not correct: XSS attempt?");
	}
	$rub = $_REQUEST["rub"];
}

if(isset($_REQUEST["rub"]) && $_REQUEST["rub"] == "crm"){
	if(isset( $_REQUEST["id_client"] )){
		$id_client = $_REQUEST["id_client"];
		if($id_client != "" && isset($id_client) && !preg_match("/^([0-9]+)\$/",$id_client)){
			die("The provided id_client does not seems to be in the correct format. It should be a single number.");
		}
	}
}

if(isset($_REQUEST["adm_login"]))	$adm_login = $_REQUEST["adm_login"];
else	$adm_login = "";
if(isset($_REQUEST["adm_pass"]))	$adm_pass = $_REQUEST["adm_pass"];
else	$adm_pass = "";
if(isset($_REQUEST["adm_email_login"]))	$adm_email_login = $_REQUEST["adm_email_login"];
else	$adm_email_login = "";
if(isset($_REQUEST["adm_email_pass"]))	$adm_email_pass = $_REQUEST["adm_email_pass"];
else	$adm_email_pass = "";
if(isset($_REQUEST["addrlink"]))	$addrlink = $_REQUEST["addrlink"];
else	$addrlink = "";

function isValidEmailLogin($email){
        $reg = "/^([a-z0-9]+)([_.a-z0-9-]+)@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+)\$/";
        if(!preg_match($reg,$email))  return false;
        else                    return true;
}


if(isset($adm_login) && !preg_match("/^([a-zA-Z0-9]+)([._a-zA-Z0-9-]+)\$/",$adm_login) && $adm_login != ""){
	die("DTC client login error: Requested login does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" signs.");
}
if(isset($adm_pass) && !preg_match("/^([a-zA-Z0-9]+)([._a-zA-Z0-9-]+)([a-zA-Z0-9])\$/",$adm_pass) && $adm_pass != ""){
	die("DTC client login error: Requested pass does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" signs.");
}
if(isset($adm_email_login) && !preg_match("/^([a-z0-9]+)([_.a-z0-9-]*)@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+)\$/",$adm_email_login) && $adm_email_login != ""){
	die("DTC email client login error: Requested login does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" signs.");
}
if(isset($adm_email_pass) && !preg_match("/^([a-zA-Z0-9]+)([._a-zA-Z0-9-]+)([a-zA-Z0-9])\$/",$adm_email_pass) && $adm_email_pass != ""){
	die("DTC email client login error: Requested pass does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" signs.");
}

if(isset($addrlink) && $addrlink != ""){
        $check = str_replace(":","",$addrlink);
        $check = str_replace("/","",$check);
        if(!preg_match("/^([a-z0-9]+)([.a-z0-9-]+)([a-z0-9])\$/",$check)){
                die("Parameter incorect in addrlink.");
        }

	$vps_exploded = explode(":",$addrlink);
	if(sizeof($vps_exploded) > 1){
		$server_subscreen = $vps_exploded[0];
		switch($server_subscreen){
		case "server":
			$dedicated_server_hostname = $vps_exploded[1];
			break;
		case "vps":
			$vps_node = $vps_exploded[1];
			$vps_last_value = explode("/",$vps_exploded[2]);
			if( sizeof($vps_last_value) > 1){
				$vps_subcommand = $vps_last_value[1];
				$vps_name = $vps_last_value[0];
			}else{
				$vps_name = $vps_exploded[2];
			}
			break;
		case "custom":
			$custom_id = $vps_exploded[1];
			break;
		default:
			die("No command recognized and a : in \$addrlink line ".__LINE__." file ".__FILE__);
		}
	}else{
	        $exploded = explode("/",$addrlink);
	        if($addrlink != "help" && $addrlink != "database"){
	        	if(sizeof($exploded) > 1)	$whatdoiedit = $exploded[1];
	                $edit_domain = $exploded[0];
	        }
	}
}

if(isset($whatdoiedit) && $whatdoiedit == "nickhandles" && isset($_REQUEST["edit_id"])){
	$edit_id = $_REQUEST["edit_id"];
}

if($panel_type != "email" && isset($edit_domain) && !preg_match("/^([a-z0-9]+)([.a-z0-9-]+)([a-z0-9])\$/",$edit_domain) && $edit_domain != ""){
	die("The domain provided does not look like a correct domain name...");
}

//don't save things into the session for cronjobs
if($panel_type!="cronjob"){
	// Save menu style preference in session
	@session_start();
	if(isset($dtc_use_text_menu)) $_SESSION["dtc_use_text_menu"]=$dtc_use_text_menu;
	if(isset($_SESSION["dtc_use_text_menu"]) && !is_string($_SESSION["dtc_use_text_menu"])){
		unset($dtc_use_text_menu);
	}
	if(isset($_SESSION["dtc_use_text_menu"])){
		$dtc_use_text_menu = $_SESSION["dtc_use_text_menu"];
	}
	if(isset($_REQUEST["use_text_menu"]) && $_REQUEST["use_text_menu"] == "yes"){
		$dtc_use_text_menu = "yes";
	}
	if(isset($_REQUEST["use_text_menu"]) && $_REQUEST["use_text_menu"] == "no"){
		$dtc_use_text_menu = "no";
	}
	if(!isset($dtc_use_text_menu) || !is_string($dtc_use_text_menu)){
		$dtc_use_text_menu = "no";
	}
	$_SESSION["dtc_use_text_menu"] = $dtc_use_text_menu;
}


?>
