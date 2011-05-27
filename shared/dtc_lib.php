<?php
////////////////////////////
// SETUP OF DTC VARIABLES //
////////////////////////////
require("$dtcshared_path/vars/table_names.php");	// The table names DTC is using
require("$dtcshared_path/vars/global_vars.php");	// Make basic checks on standard DTC params and set them as global vars
require("$dtcshared_path/vars/lang.php");		// Setup the $lang global variable (to en, en-us, fr, etc... : whatever is translated !)

// CYRUS STUFF
require("$dtcshared_path/cyradm.php");
require("$dtcshared_path/cyrus.php");

////////////////////////
// Some sql functions //
////////////////////////
require("$dtcshared_path/inc/accounting.php");		// ftp_sum() and http_sum()
require("$dtcshared_path/inc/tree_mem_to_db.php");	// Tree <-> Memory functions

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
require("$dtcshared_path/gfx/skin/default_layout.php");

// Fetchmail functions to importe mail from remote accounts
require("$dtcshared_path/inc/fetchmail.php");

// The soap API to connect to xen servers
if (!class_exists("nusoapclient")){
	if( file_exists("/usr/share/php/nusoap/nusoap.php")){
		require_once("/usr/share/php/nusoap/nusoap.php");
	}else{
		require_once("$dtcshared_path/inc/nusoap.php");
	}
}

// The maxmind API
require("$dtcshared_path/maxmind/HTTPBase.php");
require("$dtcshared_path/maxmind/CreditCardFraudDetection.php");

/////////////////////////////////////////
// The secure paiement buttons and api //
/////////////////////////////////////////
require("$dtcshared_path/securepay/pay_functions.php");			// The draw paiement button functions and modules

//////////////////////////
// Domain import/export //
//////////////////////////
require("$dtcshared_path/inc/domain_export.php");

////////////////////////////////////////
// THE USER TOOLS FOR THEY'RE ACCOUNT //
////////////////////////////////////////
// Registrar API calls
require_once "$dtcshared_path/dtcrm/registry_calls.php";
// SQL FORM SUBMITION
require("$dtcshared_path/inc/delete_user.php");
// Registrar APIs
require("$dtcshared_path/dtcrm/registry_modulator.php");
require("$dtcshared_path/inc/submit_to_sql.php");		// Submit all user actions, could be used by user's scripts...
include("$dtcshared_path/dtcrm/submit_to_sql.php");	// Some domain name registrations sql submition (most are included in draw module...)
include("$dtcshared_path/dtcrm/draw.php");	// Functions Draws the forms for domain-name registration and paiement
require("$dtcshared_path/drawlib/templates.php");
// FORMS DRAWING
require("$dtcshared_path/inc/fetch.php");		// Code that fetch all the data of one admin, to be used for calling draw.php functions
#include "$dtcshared_path/cyradm.php";
#include "$dtcshared_path/cyrus.php";
require("$dtcshared_path/inc/draw.php");		// Functions that draws all user  forms

# HTTPRequest for use instead of file(URL)
require_once("$dtcshared_path/inc/HTTPRequestClass.php");

?>
