<?php

require("/usr/share/dtc/shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
require("$dtcshared_path/global_vars.php");

require("$dtcshared_path/lang.php");			// Setup the $lang global variable (to en, en-us, fr, etc... : whatever is translated !)
require("$dtcshared_path/strings.php");			// Contain all the translated string
require("$dtcshared_path/table_names.php");
require("$dtcshared_path/dtc_functions.php");
//require("top_menu_strings.php");
include("$dtcshared_path/anotherDtc.php");	// Contain all anotherXXX() functions
include("$dtcshared_path/tree_menu.php");
include("$dtcshared_path/skin.php");
include("$dtcshared_path/skinLib.php");
include("$dtcshared_path/inc/fetch.php");
if(file_exists($dtcshared_path."/dtcrm")){
	include("$dtcshared_path/dtcrm/draw.php");
}
include("$dtcshared_path/inc/draw.php");
include("$dtcshared_path/inc/submit_to_sql.php");
if(file_exists($dtcshared_path."/dtcrm")){
	include("$dtcshared_path/dtcrm/submit_to_sql.php");
}
//include("$dtcshared_path/inc/nav.php");

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC");
$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

$anotherLanguageSelection = anotherLanguageSelection();
$language_selection_skined = skin("simple/green",$anotherLanguageSelection,$txt_select_lang_title[$lang]);

if($adm_login != "" && isset($adm_login) && $adm_pass != "" && isset($adm_pass)){
	// Fetch all the user informations, Print a nice error message if failure.
	$admin = fetchAdmin($adm_login,$adm_pass);
	if(($error = $admin["err"]) != 0){
		$mesg = $admin["mesg"];
		if($error == -1)	echo "<font color=\"red\" Wrong login or password !</font><br>";
		die("Error $error fetching admin : $mesg");
	}

	// Draw the html forms
	$HTML_admin_edit_data .= drawAdminTools($admin);

	$mypage = $HTML_admin_edit_data;

}else{
	$HTML_admin_edit_data = "<form action=\"$PHP_SELF\" methode=\"post\">
<table><tr><td>
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"adm_login\" value=\"\">
</td></tr><tr><td>
	".$txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"adm_pass\" value=\"\">
</td></tr><tr><td>
	</td><td><input type=\"submit\" name=\"Login\" value=\"login\">
</tr></td></table>
	</form>";

$login_skined = skin("simple/green",$HTML_admin_edit_data,$txt_login_title[$lang]);

$mypage = "
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">
<tr>
<td width=\"1\" height=\"1\">
$language_selection_skined
</td>
<td width=\"100%\">
</td>
</tr>
<tr>
<td colspan=\"2\">
$login_skined
</td>
</tr>
</table>";

}
// Output the result !

//echo anotherPage($txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$HTML_admin_edit_data,$anotherFooter);
echo anotherPage($txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$mypage,$anotherFooter);

?>
