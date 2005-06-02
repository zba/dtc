<?php

$panel_type="client";
require_once("../shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("login.php");

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC");
//$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

$anotherLanguageSelection = anotherLanguageSelection();
$lang_sel = skin($conf_skin,$anotherLanguageSelection,$txt_select_lang_title[$lang]);

if($adm_login != "" && isset($adm_login) && $adm_pass != "" && isset($adm_pass)){
	// Fetch all the user informations, Print a nice error message if failure.
	$admin = fetchAdmin($adm_login,$adm_pass);
	if(($error = $admin["err"]) != 0){
		$mesg = $admin["mesg"];
		$login_txt = "<font color=\"red\" Wrong login or password !</font><br>";
		$login_txt .= "Error $error fetching admin : $mesg";
		$login_txt .= login_form();
		$login_skined = skin($conf_skin,$login_txt,$txt_client_panel_title[$lang]." ".$txt_login_title[$lang]);
		$mypage = layout_login_and_languages($login_skined,$lang_sel);
	}else{
		// Draw the html forms
		$HTML_admin_edit_data = drawAdminTools($admin);
		$mypage = $HTML_admin_edit_data;
	}
}else{
	$login_txt = login_form();
	$login_skined = skin($conf_skin,$login_txt,$txt_client_panel_title[$lang]." ".$txt_login_title[$lang]);
	$mypage = layout_login_and_languages($login_skined,$lang_sel);
}
// Output the result !
if(!isset($anotherHilight)) $anotherHilight = "";

//echo anotherPage($txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$HTML_admin_edit_data,$anotherFooter);
echo anotherPage("Client:","",$anotherHilight,makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));

?>
