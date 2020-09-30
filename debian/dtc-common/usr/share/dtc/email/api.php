<?php

require_once("../shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
$panel_type="email";
require_once("$dtcshared_path/dtc_lib.php");
require_once("login.php");

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////

// $_REQUEST["adm_email_login"]
// $_REQUEST["adm_email_pass"]

if(pass_check_email()){
	echo "Password correct";
}else{
	echo "Invalid password";
}

?>
