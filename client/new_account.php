<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("new_account_form.php");

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC");
$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

$anotherLanguageSelection = anotherLanguageSelection();
$lang_sel = skin($conf_skin,$anotherLanguageSelection,$txt_select_lang_title[$lang]);

$reguser = register_user();
if($reguser["err"] == 0){
	$form = $reguser["mesg"]."<br><h4>Registration successfull!</h4>
Please now click on the following button to go for paiment:<br>
<br>";
}else if($reguser["err"] == 1){
	$form = registration_form();
}else{
	$form = "<font color=\"red\">".$reguser["mesg"]."</font><br>"
	.registration_form();
}

$login_skined = skin($conf_skin,$form,"New account registration:");
$mypage = layout_login_and_languages($login_skined,$lang_sel);
// Output the result !

//echo anotherPage($txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$HTML_admin_edit_data,$anotherFooter);
echo anotherPage("Client:".$txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$mypage,anotherFooter(""));

?>