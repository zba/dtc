<?php

require_once("../shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("login.php");

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC");
$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

$anotherLanguageSelection = anotherLanguageSelection();
$lang_sel = skin($conf_skin,$anotherLanguageSelection,$txt_select_lang_title[$lang]);

if($adm_email_login != "" && isset($adm_email_login) && $adm_email_pass != "" && isset($adm_email_pass)){
	// Fetch all the user informations, Print a nice error message if failure.
	$admin = fetchMailboxInfos($adm_email_login,$adm_email_pass);
	if(($error = $admin["err"]) != 0){
		$mesg = $admin["mesg"];
		$login_txt = "<font color=\"red\" Wrong login or password !</font><br>";
		$login_txt .= "Error $error fetching admin : $mesg";
		$login_txt .= login_emailpanel_form();
		$login_skined = skin($conf_skin,$login_txt,$txt_login_title[$lang]);
		$mypage = layout_login_and_languages($login_skined,$lang_sel);
	}else{
		// Draw the html forms



		$content = "<h2>A nice email-panel content!</h2><br><br>

<a href=\"".$_SERVER["PHP_SELF"]."?action=logout\">Logout</a><br>
<a href=\"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&action=newaction\">A link inside interface</a>
<br>

<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"action\" value=\"anotheraction\">
<input type=\"submit\" name=\"action\" value=\"Ok\">
</form>
";






		$mypage = skin($conf_skin,$content,$txt_login_title[$lang]);
	}
}else{
	$login_txt = login_emailpanel_form();
	$login_skined = skin($conf_skin,$login_txt,$txt_login_title[$lang]);
	$mypage = layout_login_and_languages($login_skined,$lang_sel);
}
// Output the result !

//echo anotherPage($txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$HTML_admin_edit_data,$anotherFooter);
echo anotherPage("Client:".$txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$mypage,anotherFooter(""));

?>
