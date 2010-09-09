<?php

function login_emailpanel_form(){
	global $conf_skin;
	
	$HTML_admin_edit_data = "<a href=\"/dtc/\">"._("Client panel")."</a> -
". _("Email panel") ." -
<a href=\"/dtc/new_account.php\">". _("Register a new account") ."</a> -
<a href=\"/dtc/recover_pass.php\">". _("Recover password"). "</a>

<form action=\"?\" method=\"post\">
<table>
<tr>
	<td align=\"right\">". _("Email: ") ."</td>
	<td><input type=\"text\" name=\"adm_email_login\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">". _("Password: ") ."</td>
	<td><input type=\"password\" name=\"adm_email_pass\" value=\"\"></td>
</tr><tr>
	<td></td><td><input type=\"submit\" name=\"Login\" value=\"". _("Ok") ."\">
</td></tr>
</table></form>";

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
