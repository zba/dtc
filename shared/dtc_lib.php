<?php
////////////////////////////
// SETUP OF DTC VARIABLES //
////////////////////////////
require("$dtcshared_path/global_vars.php");		// Make basic checks on standard DTC params and set them as global vars
require("$dtcshared_path/lang.php");			// Setup the $lang global variable (to en, en-us, fr, etc... : whatever is translated !)
require("$dtcshared_path/strings.php");			// Contain all the translated string
require("$dtcshared_path/table_names.php");

//////////////////////////////////////////////
// DRAWING PAGE AND FORMS LIBRARY FUNCTIONS //
//////////////////////////////////////////////
require("$dtcshared_path/dtc_functions.php");	// Some usefull functions
require("$dtcshared_path/inc/accounting.php");	// ftp_sum() and http_sum()
require("$dtcshared_path/anotherDtc.php");		// Contain all anotherXXX() functions plus user menu
require("$dtcshared_path/tree_menu.php");		// New version of menu working fully, but not yet integrated. Should replace the function in skin.php
require("$dtcshared_path/skin.php");			// Contains curent usermenu building plus some layout functions and rollover/preloads of images
require("$dtcshared_path/skinLib.php");			// This is the new modular skin function using gfx/skin folder and skin code registration/css

////////////////////////
// SQL FORM SUBMITION //
////////////////////////
require("$dtcshared_path/inc/submit_to_sql.php");
if(file_exists($dtcshared_path."/dtcrm")){
	include("$dtcshared_path/dtcrm/submit_to_sql.php");
}

////////////////////////////////////////
// THE USER TOOLS FOR THEY'RE ACCOUNT //
////////////////////////////////////////
require("$dtcshared_path/inc/fetch.php");		// Code that fetch all the data of one admin
if(file_exists($dtcshared_path."/dtcrm")){
	include("$dtcshared_path/dtcrm/draw.php");	// Functions Draws the forms for domain-name registration and paiement
}
require("$dtcshared_path/inc/draw.php");		// Functions that draws all user  forms

?>
