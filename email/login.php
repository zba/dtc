<?php

function login_emailpanel_form(){
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_use_text_menu;
	global $txt_login_title;

	global $conf_skin;

	global $lang;

	$HTML_admin_edit_data = "<a href=\"/dtc\">Go to client panel</a>
<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
<table>
<tr>
	<td align=\"right\">".$txt_login_login[$lang]."</td>
	<td><input type=\"text\" name=\"adm_email_login\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_login_pass[$lang]."</td>
	<td><input type=\"password\" name=\"adm_email_pass\" value=\"\"></td>
</tr><tr>
	<td></td><td><input type=\"submit\" name=\"Login\" value=\"login\">
</td></tr>
</table></form>";

//	$login_skined = skin($conf_skin,$HTML_admin_edit_data,$txt_login_title[$lang]);
//	return $login_skined;
	return $HTML_admin_edit_data;
}

function layout_login_and_languages($login_skined,$language_selection_skined){
	return "
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">
<tr>
	<td width=\"1\" height=\"1\">$language_selection_skined</td>
	<td width=\"100%\"></td>
</tr><tr>
	<td colspan=\"2\">$login_skined</td>
</tr></table>";
}

?>