<?php

require_once("/usr/share/dtc/shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");


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
		echo "<font color=\"red\" Wrong login or password !</font><br>";
		die("Error $error fetching admin : $mesg <a href=\"$PHP_SELF\">try again</a>");
	}

	// Draw the html forms
	$HTML_admin_edit_data .= drawAdminTools($admin);

	$mypage = $HTML_admin_edit_data;

}else{
	$HTML_admin_edit_data = "<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<table><tr><td align=\"right\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"adm_login\" value=\"\">
</td></tr><tr><td align=\"right\">
	".$txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"adm_pass\" value=\"\">
</td></tr><tr><td align=\"right\">
	Use text menu</td><td><input type=\"checkbox\" name=\"use_text_menu\" value=\"yes\">
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
echo anotherPage("Client:".$txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$mypage,$anotherFooter);

?>
