<?php

// Those are supposed to be filled at eatch queries, therefore they
// are global variables.

if($_REQUEST["rub"] == "crm"){
	$id_client = $_REQUEST["id_client"];
	if($id_client != "" && isset($id_client) && !ereg("^([0-9]+)\$",$id_client)){
		die("The provided id_client does not seems to be in the correct format.
						It should be a single number.");
	}
}


$adm_login = $_REQUEST["adm_login"];
$adm_pass = $_REQUEST["adm_pass"];
$addrlink = $_REQUEST["addrlink"];

if(!ereg("^([a-zA-Z0-9]+)([.a-zA-Z0-9-]+)\$",$adm_login) && isset($adm_login) && $adm_login != ""){
	die("Requested login does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" sign.");
}
if(!ereg("^([a-zA-Z0-9]+)([.a-zA-Z0-9-]+)([a-zA-Z0-9])\$",$adm_pass) && isset($adm_pass) && $adm_pass != ""){
	die("Requested pass does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" sign.");
}
if($addrlink != "" && isset($addrlink)){
        $exploded = explode("/",$addrlink);
        if($addrlink != "help" && $addrlink != "database"){
                $whatdoiedit = $exploded[1];
                $edit_domain = $exploded[0];
        }
}

if($whatdoiedit == "nickhandles"){
	$edit_id = $_REQUEST["edit_id"];
}

if(!ereg("^([a-z0-9]+)([.a-z0-9-]+)([a-z0-9])\$",$edit_domain) && isset($edit_domain) && $edit_domain != ""){
	die("The domain provided does not look like a correct domain name...");
}

// Save menu style preference in session
session_register("dtc_use_text_menu");
if(!is_string($_SESSION["dtc_use_text_menu"])){
	unset($dtc_use_text_menu);
}
$dtc_use_text_menu = $_SESSION["dtc_use_text_menu"];
if($_REQUEST["use_text_menu"] == "yes"){
	$dtc_use_text_menu = "yes";
}
if($_REQUEST["use_text_menu"] == "no"){
	$dtc_use_text_menu = "no";
}
if(!is_string($dtc_use_text_menu)){
	$dtc_use_text_menu = "no";
}
$_SESSION["dtc_use_text_menu"] = $dtc_use_text_menu;


?>
