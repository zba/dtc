<?php

function registration_form(){
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_use_text_menu;
	global $txt_login_title;

	global $conf_skin;

	global $lang;
	global $txt_draw_client_info_familyname;
	global $txt_draw_client_info_firstname;
	global $txt_draw_client_info_comp_name;
	global $txt_draw_client_info_addr;
	global $txt_draw_client_info_email;
	global $txt_draw_client_info_fax;
	global $txt_draw_client_info_phone;
	global $txt_draw_client_info_zipcode;
	global $txt_draw_client_info_city;
	global $txt_draw_client_info_state;
	global $txt_draw_client_info_country;

	$login_info = "<table>
<tr>
	<td align=\"right\">".$txt_login_login[$lang]."</td>
	<td><input type=\"text\" name=\"adm_login\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_login_pass[$lang]."</td>
	<td><input type=\"password\" name=\"adm_pass\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">Confirm pass:</td>
	<td><input type=\"password\" name=\"adm_pass2\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">Desired domain name:</td>
	<td><input type=\"text\" name=\"adm_pass\" value=\"\"></td>
</tr></table>";
	$login_skined = skin("frame",$login_info,"");

	$client_info = "<table>
<tr>
	<td align=\"right\">".$txt_draw_client_info_familyname[$lang]."</td>
	<td><input type=\"text\" name=\"familyname\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_firstname[$lang]."</td>
	<td><input type=\"text\" name=\"firstname\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">Is company</td>
	<td><input type=\"radio\" name=\"iscomp\" value=\"yes\">Yes
<input type=\"radio\" name=\"iscomp\" value=\"no\">No</td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_comp_name[$lang]."</td>
	<td><input type=\"text\" name=\"compname\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_email[$lang]."</td>
	<td><input type=\"text\" name=\"email\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_phone[$lang]."</td>
	<td><input type=\"text\" name=\"phone\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_fax[$lang]."</td>
	<td><input type=\"text\" name=\"fax\" value=\"\"></td>
</tr></table>";
	$client_skined = skin("frame",$client_info,"");

	$client_addr = "<table>
<tr>
	<td align=\"right\">".$txt_draw_client_info_addr[$lang]."</td>
	<td><input type=\"text\" name=\"addresse1\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_addr[$lang]." 2</td>
	<td><input type=\"text\" name=\"addresse2\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_addr[$lang]." 3</td>
	<td><input type=\"text\" name=\"addresse3\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_zipcode[$lang]."</td>
	<td><input type=\"text\" name=\"zipcode\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_city[$lang]."</td>
	<td><input type=\"text\" name=\"phone\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_state[$lang]."</td>
	<td><input type=\"text\" name=\"fax\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_country[$lang]."</td>
	<td><select name=\"country\">".cc_code_popup()."</select></td>
</tr></table>";
	$addr_skined = skin("frame",$client_addr,"");

	$HTML_admin_edit_data = "<a href=\"/dtc\">Go to login</a>
<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
<table>
<tr>
	<td>Login info:$login_skined</td>
	<td>Client info:$client_skined</td>
	<td>Client info:$addr_skined</td>
</tr></table>
<center><input type=\"submit\" name=\"Login\" value=\"Register\"></center>
</form>";

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