<?php
////////////////////////////
// SETUP OF DTC VARIABLES //
////////////////////////////
require("$dtcshared_path/vars/table_names.php");	// The table names DTC is using
require("$dtcshared_path/vars/global_vars.php");	// Make basic checks on standard DTC params and set them as global vars
require("$dtcshared_path/vars/lang.php");			// Setup the $lang global variable (to en, en-us, fr, etc... : whatever is translated !)
require("$dtcshared_path/vars/strings.php");		// Contain all the translated string

////////////////////////
// Some sql functions //
////////////////////////
require("$dtcshared_path/inc/accounting.php");		// ftp_sum() and http_sum()

//////////////////////////////////////////////
// DRAWING PAGE AND FORMS LIBRARY FUNCTIONS //
//////////////////////////////////////////////
require("$dtcshared_path/drawlib/cc_code_popup.php");	// The country code popup global vars and function
require("$dtcshared_path/drawlib/dtc_functions.php");	// Some usefull functions
require("$dtcshared_path/drawlib/anotherDtc.php");		// Contain all anotherXXX() functions plus user menu, pagetop, some layout, etc...
// If you have time, please fix bugs in user menu rewritting skin.php by
// reading working code in tree_menu.php so that it can be realy recusive,
// and internationalisation is possible.
require("$dtcshared_path/drawlib/tree_menu.php");		// New version of menu working fully, but not yet integrated. Should replace the function in skin.php
require("$dtcshared_path/drawlib/skin.php");			// Contains curent usermenu building plus some layout functions and rollover/preloads of images
require("$dtcshared_path/drawlib/skinLib.php");			// This is the new modular skin function using gfx/skin folder and skin code registration/css
require("$dtcshared_path/inc/paiement.php");			// The draw paiement button function

////////////////////////////////////////
// THE USER TOOLS FOR THEY'RE ACCOUNT //
////////////////////////////////////////
// SQL FORM SUBMITION
require("$dtcshared_path/inc/submit_to_sql.php");		// Submit all user actions, could be used by user's scripts...
if(file_exists($dtcshared_path."/dtcrm")){
	include("$dtcshared_path/dtcrm/submit_to_sql.php");	// Some domain name registrations sql submition (most are included in draw module...)
}
// FORMS DRAWING
require("$dtcshared_path/inc/fetch.php");		// Code that fetch all the data of one admin, to be used for calling draw.php functions
if(file_exists($dtcshared_path."/dtcrm")){
	include("$dtcshared_path/dtcrm/draw.php");	// Functions Draws the forms for domain-name registration and paiement
}
require("$dtcshared_path/inc/draw.php");		// Functions that draws all user  forms

?>
