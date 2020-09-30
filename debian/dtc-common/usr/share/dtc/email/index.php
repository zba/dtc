<?php

$panel_type="email";
require_once("../shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("login.php");

if(function_exists("skin_ClientPage")){
	skin_EmailPage();
}else{
	skin_EmailPage_Default();
}

?>
