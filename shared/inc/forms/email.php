<?php

function drawImportedMail($mailbox){
	global $adm_email_login;
	global $adm_email_pass;
	global $errTxt;

	global $pro_mysql_fetchmail_table;

	$out = "";

	if(isset($errTxt) && $errTxt != ""){
		$out .= "<font color=\"red\">$errTxt</font><br>";
	}

	$url_start = "<a href=\"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

	$q = "SELECT * FROM $pro_mysql_fetchmail_table WHERE domain_user='".$mailbox["data"]["id"]."' AND domain_name='".$mailbox["data"]["mbox_host"]."';";
	$r = mysql_query($q)or die("Cannot query $q ! line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	$out .= "<table border=\"1\">
<tr><td>Address email</td><td>Mailbox type</td><td>Server addr</td><td>Login</td><td>Pass</td><td>Use</td><td>Action</td></tr>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$pop3_selected = "";
		$imap4_selected = "";
		$msn_selected = "";
		$hotmail_selected = "";
		$yahoo_selected = "";
		$gmail_selected = "";
		switch($a["mailbox_type"]){
		case "POP3":
			$pop3_selected = " selected ";
			break;
		case "IMAP4":
			$imap4_selected = " selected ";
			break;
		case "MSN":
			$msn_selected = " selected ";
			break;
		case "HOTMAIL":
			$hotmail_selected = " selected ";
			break;
		case "YAHOO":
			$yahoo_selected = " selected ";
			break;
		case "GMAIL":
			$yahoo_selected = " selected ";
		default:
			break;
		}
		$popup_boxtype = "<select name=\"mailbox_type\">
			<option value=\"POP3\"$pop3_selected>POP3</option>
			<option value=\"IMAP4\"$imap4_selected>IMAP4</option>
			<option value=\"MSN\"$msn_selected>MSN</option>
			<option value=\"HOTMAIL\"$hotmail_selected>HOTMAIL</option>
			<option value=\"YAHOO\"$yahoo_selected>YAHOO</option>
			<option value=\"GMAIL\"$yahoo_selected>GMAIL</option>";
		if($a[""] == "yes"){
			$useit = "";
		}
		$out .= "<tr>
			<td>$form_start<input type=\"hidden\" name=\"action\" value=\"modify_fetchmail\"><input type=\"text\" name=\"email_addr\" value=\"".$a["pop3_email"]."\"></td>
			<td>$popup_boxtype</td>
			<td><input type=\"text\" name=\"server_addr\" value=\"".$a["pop3_server"]."\"></td>
			<td><input type=\"text\" name=\"login\" value=\"".$a["pop3_login"]."\"></td>
			<td><input type=\"text\" name=\"server_addr\" value=\"".$a["pop3_pass"]."\"></td>
			<td>Use</td>
			<td><input type=\"submit\" value=\"Save\"></form>$form_start<input type=\"hidden\" name=\"action\" value=\"modify_fetchmail\">
			<input type=\"hidden\" name=\"id\" value=\"".$a["id"]."\"><input type=\"submit\" value=\"delete\"></form></td></tr>";
	}
	$out .= "<tr>
		<td>$form_start<input type=\"hidden\" name=\"action\" value=\"add_fetchmail\"><input type=\"text\" name=\"email_addr\" value=\"\"></td>
		<td><select name=\"mailbox_type\">
			<option value=\"POP3\">POP3</option>
			<option value=\"IMAP4\">IMAP4</option>
			<option value=\"MSN\">MSN</option>
			<option value=\"HOTMAIL\">HOTMAIL</option>
			<option value=\"YAHOO\">YAHOO</option>
			<option value=\"GMAIL\">GMAIL</option>
		</select></td>
		<td><input type=\"text\" name=\"server_addr\" value=\"\"></td>
		<td><input type=\"text\" name=\"login\" value=\"\"></td>
		<td><input type=\"text\" name=\"pass\" value=\"\"></td>
		<td><input type=\"checkbox\" name=\"use\" value=\"yes\" checked\"></td>
		<td><input type=\"submit\" value=\"Add\"></form></td></tr>";
	$out .= "</table>";

	return $out;
}

function drawAntispamRules($mailbox){
	global $adm_email_login;
	global $adm_email_pass;

	$out = "";

	$url_start = "<a href=\"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

	if($mailbox["data"]["iwall_protect"] == "yes")	$checked = " checked "; else $checked = "";
	$out = $form_start."<input type=\"hidden\" name=\"action\" value=\"activate_antispam\"><input type=\"checkbox\" name=\"iwall_on\" value=\"yes\"$checked>Activate iGlobalWall protection
	<input type=\"submit\" value=\"Ok\"></form>";

	if($mailbox["data"]["iwall_protect"] == "yes"){
		$out .= "<br><h3>Your white list:</h3>";
	}


	return $out;
}

function drawQuarantine($mailbox){
	global $adm_email_pass;

	$out = "";

	$url_start = "<a href=\"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

//	$out = $form_start."<input type=\"hidden\" name=\"action\" value=\"activate_antispam\"><input type=\"checkbox\" name=\"iwall_on\" value=\"yes\"$checked>Activate iGlobalWall protection
//	<input type=\"submit\" value=\"Ok\"></form>";
	$out = "Here will go quarantinized messages";

	return $out;
}

function drawAdminTools_emailAccount($mailbox){
	global $txt_change_your_password_title;
	global $txt_repeate_password;
	global $txt_mail_deliver_localy;
	global $txt_mail_redirection1;
	global $txt_mail_redirection2;
	global $txt_password;
	global $txt_mail_edit;
	global $txt_mailbox_redirection_edition;
	global $lang;

	global $adm_email_login;
	global $adm_email_pass;

	$url_start = "<a href=\"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

	$change_pass_form = "<br><b><u>".$txt_change_your_password_title[$lang]."</b></u><br><br><br>
<table cellpadding=\"0\" cellspacing=\"0\">
<tr>
	<td align=\"right\">".$form_start.$txt_password[$lang]."</td>
	<td><input type=\"hidden\" name=\"action\" value=\"dtcemail_change_pass\"><input type=\"password\" name=\"newpass1\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_repeate_password[$lang]."</td>
	<td><input type=\"password\" name=\"newpass2\" value=\"\"> <input type=\"submit\" name=\"submit\" value=\"Ok\"></form></td>
</tr></table>
<br><br>";

	if($mailbox["data"]["localdeliver"] == "yes"){
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=no\"><font color=\"green\">yes</font></a>";
	}else{
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=yes\"><font color=\"red\">no</font></a>";
	}

	$redirect_form = "<br><b><u>".$txt_mailbox_redirection_edition[$lang]."</b></u><br><br>
".$txt_mail_deliver_localy[$lang]." $deliverUrl
<table cellpadding=\"0\" cellspacing=\"0\">
<tr>
	<td align=\"right\">".$form_start.$txt_mail_redirection1[$lang]."</td>
	<td><input type=\"hidden\" name=\"action\" value=\"dtcemail_edit_redirect\"><input type=\"text\" name=\"redirect1\" value=\"".$mailbox["data"]["redirect1"]."\"></td>
</tr><tr>
	<td>".$txt_mail_redirection2[$lang]."</td>
	<td><input type=\"text\" name=\"redirect2\" value=\"".$mailbox["data"]["redirect2"]."\"><input type=\"submit\" name=\"submit\" value=\"Ok\"></form></td>
</tr></table><br><br>";


	if($mailbox["data"]["iwall_protect"] == "yes"){
	}

	$out = "<table width=\"100%\" heigh=\"1\">
<tr>
	<td width=\"50%\">".skin("frame",$change_pass_form,"")."</td>
	<td>".skin("frame",$redirect_form,"")."</td>
</tr>
</table>
";
	return $out;
}

function drawAdminTools_emailPanel($mailbox){
	global $conf_skin;
	global $addrlink;

	global $txt_mail_edit;
	global $lang;

	global $adm_email_login;
	global $adm_email_pass;

	$user_menu[] = array(
		"text" => "My Email",
		"type" => "link",
		"link" => "My Email");
	$user_menu[] = array(
		"text" => "fetchmail",
		"type" => "link",
		"link" => "fetchmail");
	$user_menu[] = array(
		"text" => "antispam",
		"type" => "link",
		"link" => "antispam");
	$user_menu[] = array(
		"text" => "quarantine",
		"type" => "link",
		"link" => "quarantine");

	$logout = "<a href=\"".$_SERVER["PHP_SELF"]."?action=logout\">Logout</a>";

	$mymenu = makeTreeMenu($user_menu,$addrlink,"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass","addrlink");
	$left_menu = skin($conf_skin,"<br>".$mymenu."<center>$logout</center>",$adm_email_login);

	$left_menu = "<table width=\"1\" height=\"100%\"><tr>
		<td width=\"1\" height=\"1\">$left_menu</td>
</tr><tr>
		<td height=\"100%\">&nbsp;</td>
</tr></table>";

	switch($addrlink){
	case "My Email":
		$title = $txt_mail_edit[$lang];
		$panel = drawAdminTools_emailAccount($mailbox);
		break;
	case "fetchmail":
		$title = "Your list of imported mail";
		$panel = drawImportedMail($mailbox);
		break;
	case "antispam":
		$title = "Protect your mailbox with efficient tools:";
		$panel = drawAntispamRules($mailbox);
		break;
	case "quarantine":
		$title = "Those mail are in quarantine, and were not delivered to your pop account:";
		$panel = drawQuarantine($mailbox);
		break;
	default:
		$title = "Welcom to the email panel!";
		$panel = "Login successfull. Please select a menu entry on the left...";
		break;
	}

	$right = skin($conf_skin,$panel,$title);

	$right = "<table width=\"100%\" height=\"100%\"><tr>
		<td width=\"100%\" height=\"100%\">$right</td>
</tr><tr>
	<td height=\"1\">&nbsp;</td>
</tr></table>";

	$content = "<table width=\"100%\" height=\"100%\"><tr>
		<td width=\"1\"  height=\"100%\">$left_menu</td>
		<td width=\"100%\" height=\"100%\">$right</td>
</tr></table>";

	return $content;

	return drawAdminTools_emailAccount($mailbox);
}

/////////////////////////////////////////
// One domain email collection edition //
/////////////////////////////////////////
function drawAdminTools_Emails($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $edit_mailbox;
	global $addrlink;

	global $lang;
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_mail_liste_of_your_box;
	global $txt_mail_new_mailbox;
	global $txt_mail_redirection1;
	global $txt_mail_redirection2;
	global $txt_mail_deliver_localy;
	global $txt_mail_edit;
	global $txt_mail_new_mailbox_link;
	global $txt_number_of_active_mailbox;
	global $txt_maximum_mailbox_reach;

	global $conf_hide_password;


	$nbr_email = sizeof($domain["emails"]);
	$max_email = $domain["max_email"];
	if($nbr_email >= $max_email){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = $txt_number_of_active_mailbox[$lang];
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_email ."</font> / <font size=\"-1\">" . $max_email . "</font><br><br>";

	$txt .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><b><u>".$txt_mail_liste_of_your_box[$lang]."</u><br>";
	$emails = $domain["emails"];
	$nbr_boites = sizeof($emails);
	for($i=0;$i<$nbr_boites;$i++){
		$email = $emails[$i];
		$id = $email["id"];
		if($id == $_REQUEST["edit_mailbox"]){
			$mailbox_name = $id;
			$home = $email["home"];
			$passwd = $email["passwd"];
			$redir1 = $email["redirect1"];
			$redir2 = $email["redirect2"];
			$localdeliver = $email["localdeliver"];
			if($localdeliver == yes){
				$checkbox_state = " checked";
			}else{
				$checkbox_state = "";
			}
		}
		if($i != 0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails&edit_mailbox=$id\">$id</a>";
	}

	if($_REQUEST["edit_mailbox"] != "" && isset($_REQUEST["edit_mailbox"])){
		$txt .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails\">".$txt_mail_new_mailbox_link[$lang]."</a> ";
		$txt .= "<br><br><u>".$txt_mail_edit[$lang]."</u><br><br>";

		$txt .= "
<table border=\"1\"><tr><td align=\"right\">
<form action=\"?\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<input type=\"hidden\" name=\"edit_mailbox\" value=\"".$_REQUEST["edit_mailbox"]."\">
	".$txt_login_login[$lang]."</td><td><b>$mailbox_name</b>@$edit_domain
</td><td align=\"right\">
	".$txt_mail_redirection1[$lang]."</td><td><input type=\"text\" name=\"editmail_redirect1\" value=\"$redir1\">
</td></tr><tr><td align=\"right\">";
	if ($conf_hide_password == "yes")
	{
	$txt .= $txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"editmail_pass\" value=\"$passwd\">";
	} else {
	$txt .= $txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"editmail_pass\" value=\"$passwd\">";
	}
$txt .= "
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"editmail_redirect2\" value=\"$redir2\">
</td></tr><tr><td align=\"right\">
".$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"editmail_deliver_localy\" value=\"yes\"$checkbox_state></td>
<td>&nbsp;</td><td><input type=\"submit\" name=\"modifymailboxdata\" value=\"Ok\">&nbsp;
<input type=\"submit\" name=\"delemailaccount\" value=\"Del\">
</td></tr>
</table>
</form>
";
	}else{
		$txt .= "<br><br><u>".$txt_mail_new_mailbox[$lang]."</u><br>";

		if($nbr_email < $max_email){
			$txt .= "
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<table border=\"1\"><tr><td align=\"right\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newmail_login\" value=\"$mailbox_name\">
</td><td align=\"right\">
	".$txt_mail_redirection1[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect1\" value=\"\">
</td></tr><tr><td align=\"right\">";
	if ($conf_hide_password == "yes")
	{
		$txt .= $txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"newmail_pass\" value=\"$passwd\">";
	} else {
		$txt .= $txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"newmail_pass\" value=\"$passwd\">";
	}
$txt .= "
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect2\" value=\"\">
</td></tr><tr><td align=\"right\">
".$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"newmail_deliver_localy\" value=\"yes\" checked></td>
<td></td>
<td><input type=\"submit\" name=\"addnewmailtodomain\" value=\"Ok\">
</td></tr>
</table>
</form>
";
		}else{
			$txt .= $txt_maximum_mailbox_reach[$lang]."<br>";
		}
	}
	$txt .= "</b></font></font>";
	return $txt;
}

?>
