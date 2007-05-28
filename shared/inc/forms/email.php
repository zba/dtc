<?php

/**
 * 
 * @package DTC
 * @version $Id: email.php,v 1.55 2007/05/28 21:40:05 thomas Exp $
 * @param unknown_type $mailbox
 * @return unknown
 */

function drawImportedMail($mailbox){
	global $adm_email_login;
	global $adm_email_pass;
	global $errTxt;
	global $pro_mysql_fetchmail_table;
	
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_use;
	global $txt_action;
	global $txt_cfg_server_address;
	global $txt_login_title;
	global $lang;
	
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
	
	<!-- to translate -->
<tr><td>Address email</td><td>Mailbox type</td><td>".$txt_cfg_server_address[$lang]."</td><td>".$txt_login_title[$lang]."</td><td>".$txt_login_pass[$lang]."</td><td>".$txt_use[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
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
			<option value=\"GMAIL\"$yahoo_selected>GMAIL</option></select>";
		if($a[""] == "yes"){
			$useit = "";
		}
		$popup_boxtype = $a["mailbox_type"]."<input type=\"hidden\" name=\"mailbox_type\" value=\"".$a["mailbox_type"]."\">";
		$out .= "<tr>
			<td>$form_start<input type=\"hidden\" name=\"action\" value=\"modify_fetchmail\"><input type=\"hidden\" name=\"boxid\" value=\"".$a["id"]."\"><input type=\"text\" name=\"email_addr\" value=\"".$a["pop3_email"]."\"></td>
			<td>$popup_boxtype</td>
			<td><input type=\"text\" name=\"server_addr\" value=\"".$a["pop3_server"]."\"></td>
			<td><input type=\"text\" name=\"login\" value=\"".$a["pop3_login"]."\"></td>
			<td><input type=\"text\" name=\"pass\" value=\"".$a["pop3_pass"]."\"></td>
			<td>Use</td>
			<td><input type=\"submit\" value=\"Save\"></form>$form_start<input type=\"hidden\" name=\"action\" value=\"del_fetchmail\">
			<input type=\"hidden\" name=\"boxid\" value=\"".$a["id"]."\"><input type=\"submit\" value=\"delete\"></form></td></tr>";
	}
	$out .= "<tr>
		<td>$form_start<input type=\"hidden\" name=\"action\" value=\"add_fetchmail\"><input type=\"text\" name=\"email_addr\" value=\"\"></td>
		<td><select name=\"mailbox_type\">
			<option value=\"POP3\">POP3</option>
			<option value=\"IMAP4\">IMAP4</option>
			<option value=\"MSN\">MSN</option>
			<option value=\"HOTMAIL\">HOTMAIL</option>
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
	global $pro_mysql_whitelist_table;
	global $pro_mysql_whitelist_table;

	$bnc_msg = "Hello,
You have tried to write an email to me, and because of the big amount
of spam I recieved, I use an antispam software that require a message
confirmation. This is very easy, and you will have to do it only once.
Just click on the following link, copy the number you see on the
screen and I will recieve the message you sent me. If you do not
click, then your message will be considered as advertising and I will
NOT recieve it.

***URL***

Thank you for your understanding.

Antispam software:
Grey listing+SPF, only available with Domain Technologie Control
http://www.gplhost.com/software-dtc.html
";

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
		$frm_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"".$_REQUEST["adm_email_login"]."\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"".$_REQUEST["adm_email_pass"]."\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

		if(strlen($mailbox["data"]["bounce_msg"]) < 9 || false == strstr($mailbox["data"]["bounce_msg"],"***URL***")){
			$zebounce = $bnc_msg;
		}else{
			$zebounce = $mailbox["data"]["bounce_msg"];
		}

		$out .= "<br><h3>Your bounce message:</h3>
		
		$frm_start<input type=\"hidden\" name=\"action\" value=\"edit_bounce_msg\">
		<textarea cols=\"80\" rows=\"16\" name=\"bounce_msg\">$zebounce</textarea><br><br>
		<input type=\"submit\" value=\"Save\"></form>";

		$out .= "<h3>Your white list:</h3>";

		$q = "SELECT * FROM $pro_mysql_whitelist_table";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"1\">
<tr><td>Mail user from</td><td>&nbsp;</td><td>Mail user domain</td><td>Mail user to (mailling list)</td><td>Action</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$out .= "<tr>$frm_start
			<input type=\"hidden\" name=\"action\" value=\"edit_whitelist_rule\">
			<input type=\"hidden\" name=\"ruleid\" value=\"".$a["id"]."\">";
			$out .= "<td><input type=\"text\" name=\"mail_from_user\" value=\"".$a["mail_from_user"]."\"></td>";
			$out .= "<td>@</td>";
			$out .= "<td><input type=\"text\" name=\"mail_from_domain\" value=\"".$a["mail_from_domain"]."\"></td>";
			$out .= "<td><input type=\"text\" size=\"30\" name=\"mail_to\" value=\"".$a["mail_to"]."\"></td>";
			$out .= "<td><input type=\"submit\"  value=\"Save\"></form>
			$frm_start<input type=\"hidden\" name=\"ruleid\" value=\"".$a["id"]."\">
			<input type=\"hidden\" name=\"action\" value=\"delete_whitelist_rule\">
			<input type=\"submit\" value=\"Delete\"></form></td></tr>";
		}
		$out .= "<tr>$frm_start
			<input type=\"hidden\" name=\"action\" value=\"add_whitelist_rule\">
			<td><input type=\"text\" name=\"mail_from_user\" value=\"\"></td>";
		$out .= "<td>@</td>";
		$out .= "<td><input type=\"text\" name=\"mail_from_domain\" value=\"\"></td>";
		$out .= "<td><input type=\"text\" size=\"30\" name=\"mail_to\" value=\"\"></td>";
		$out .= "<td><input type=\"submit\" value=\"Save\"></form></td></tr>";
		$out .= "</table>";
	}
	if($mailbox["data"]["clamav_protect"] == "yes")	$checked = " checked "; else $checked = "";
	$out .= $form_start."<input type=\"hidden\" name=\"action\" value=\"activate_clamav\"><input type=\"checkbox\" name=\"clamav_on\" value=\"yes\"$checked>Activate Clamav antivirus
	<input type=\"submit\" value=\"Ok\"></form>";
	return $out;
}

function drawQuarantine($mailbox){
	global $adm_email_login;
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
	global $txt_mail_spam_mailbox_enable;
	global $txt_mail_spam_mailbox;
	global $txt_mail_redirection1;
	global $txt_mail_redirection2;
	global $txt_password;
	global $txt_mail_edit;
	global $txt_mailbox_redirection_edition;
	global $lang;
	global $txt_yes;
	global $txt_no;
	
	global $adm_email_login;
	global $adm_email_pass;

	$url_start = "<a href=\"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

	// Draw the form for changing the password
	$left = "<h3>".$txt_change_your_password_title[$lang]."</h3>
<table cellpadding=\"0\" cellspacing=\"0\">
<tr>
	<td align=\"right\">".$form_start.$txt_password[$lang]."</td>
	<td><input type=\"hidden\" name=\"action\" value=\"dtcemail_change_pass\"><input type=\"password\" name=\"newpass1\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">".$txt_repeate_password[$lang]."</td>
	<td><input type=\"password\" name=\"newpass2\" value=\"\"></td>
</tr><tr>
	<td></td><td>
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"submit\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div></form></td>
</tr></table>
<br><br>";

	if($mailbox["data"]["vacation_flag"] == "yes"){
		$use_vacation_msg_yes_checked = " checked ";
		$use_vacation_msg_no_checked = " ";
	}else{
		$use_vacation_msg_yes_checked = " ";
		$use_vacation_msg_no_checked = " checked ";
	}

	$left .= "<h3>Vacation message</h3>
	".$form_start."<input type=\"hidden\" name=\"action\" value=\"dtcemail_vacation_msg\">
<input type=\"radio\" name=\"use_vacation_msg\" value=\"yes\" $use_vacation_msg_yes_checked>".$txt_yes[$lang]."<input type=\"radio\" name=\"use_vacation_msg\" value=\"no\" $use_vacation_msg_no_checked>".$txt_no[$lang]."
<br>
<textarea cols=\"40\" rows=\"7\" name=\"vacation_msg_txt\">".$mailbox["data"]["vacation_text"]."</textarea><br>
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"submit\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div>
</form>";

	if($mailbox["data"]["localdeliver"] == "yes"){
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=no\"><font color=\"green\">".$txt_yes[$lang]."</font></a>";
	}else{
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=yes\"><font color=\"red\">".$txt_no[$lang]."</font></a>";
	}
	$right = "<h3>".$txt_mailbox_redirection_edition[$lang]."</h3>
".$txt_mail_deliver_localy[$lang]." $deliverUrl
<table cellpadding=\"0\" cellspacing=\"0\">
<tr>
	<td align=\"right\">".$form_start.$txt_mail_redirection1[$lang]."</td>
	<td><input type=\"hidden\" name=\"action\" value=\"dtcemail_edit_redirect\"><input type=\"text\" name=\"redirect1\" value=\"".$mailbox["data"]["redirect1"]."\"></td>
</tr><tr>
	<td>".$txt_mail_redirection2[$lang]."</td>
	<td><input type=\"text\" name=\"redirect2\" value=\"".$mailbox["data"]["redirect2"]."\"></td>
</tr><tr>
	<td></td><td>
	<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"submit\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div></form></td>
</tr></table><br><br>";

	if($mailbox["data"]["spam_mailbox_enable"] == "yes"){
		$spambox_yes_checked = " checked ";
		$spambox_no_checked = " ";
	}else{
		$spambox_yes_checked = " ";
		$spambox_no_checked = " checked ";
	}

	$right .= "<h3>Anti-SPAM control</h3>
<table cellpadding=\"0\" cellspacing=\"0\">
<tr>
	<td align=\"right\">Deliver spam to spambox:</td><td>".$form_start."<input type=\"hidden\" name=\"action\" value=\"dtcemail_spambox\">
<input type=\"radio\" name=\"spam_mailbox_enable\" value=\"yes\" $spambox_yes_checked>".$txt_yes[$lang]."<input type=\"radio\" name=\"spam_mailbox_enable\" value=\"no\" $spambox_no_checked>".$txt_no[$lang]."</td>
</tr><tr>
	<td align=\"right\">SPAM box name:</td><td><input type=\"text\" name=\"spam_mailbox\" value=\"".$mailbox["data"]["spam_mailbox"]."\"></td>
</tr><tr>
	<td></td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"submit\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div>
</form></td></tr></table>";
	// Output the form
	$out = "<table width=\"100%\" heigh=\"1\">
<tr>
	<td width=\"50%\" valign=\"top\">".$left."</td>
	<td width=\"4\" background=\"gfx/skin/frame/border_2.gif\"></td>
	<td valign=\"top\">".$right."</td>
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

	global $txt_user_menu_email;
	global $txt_user_menu_fetchmail;
	global $txt_user_menu_antispam;
	global $txt_user_menu_quarantine;
	global $txt_logout;

//	echo "<pre>";print_r($mailbox);echo "</pre>";

	$user_menu[] = array(
		"text" => $txt_user_menu_email[$lang],
		"icon" => "box_wnb_nb_picto-mailboxes.gif",
		"type" => "link",
		"link" => "My Email");
	$user_menu[] = array(
		"text" => $txt_user_menu_fetchmail[$lang],
		"icon" => "box_wnb_nb_picto-mailinglists.gif",
		"type" => "link",
		"link" => "fetchmail");
/*	$user_menu[] = array(
		"text" => $txt_user_menu_antispam[$lang],
		"type" => "link",
		"link" => "antispam");
	$user_menu[] = array(
		"text" => $txt_user_menu_quarantine[$lang],
		"type" => "link",
		"link" => "quarantine");*/

	$logout = "<a href=\"".$_SERVER["PHP_SELF"]."?action=logout\">".$txt_logout[$lang]."</a>";

	$mymenu = makeTreeMenu($user_menu,$addrlink,"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass","addrlink");

	switch($addrlink){
	case "My Email":
		$title = $txt_mail_edit[$lang];
		$panel = drawAdminTools_emailAccount($mailbox);
		break;
	case "antispam":
		$title = "Protect your mailbox with efficient tools:";
		$panel = drawAntispamRules($mailbox);
		break;
	case "fetchmail":
		$title = "Your list of imported mail";
		$panel = drawImportedMail($mailbox);
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

	if(function_exists("layoutEmailPanel")){
		$content = layoutEmailPanel($adm_email_login,"<br>".$mymenu."<center>$logout</center>",$title,$panel);
	}else{
		$mymenu_skin = skin($conf_skin,"<br>".$mymenu."<center>$logout</center>",$adm_email_login);
		$left = "<table width=\"1\" height=\"100%\"><tr>
		<td width=\"1\" height=\"1\">$mymenu_skin</td>
</tr><tr>
		<td height=\"100%\">&nbsp;</td>
</tr></table>";

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
	}
	return $content;
//	return drawAdminTools_emailAccount($mailbox);
}

/////////////////////////////////////////
// Check the used quota for cyrus      //
/////////////////////////////////////////
function getCyrusUsedQuota ($id) {
	global $pro_mysql_pop_table;

	$q = "SELECT fullemail FROM $pro_mysql_pop_table WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		 die("Cannot find created email line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	$fullemail = $a["fullemail"];
	// login to cyradm
	$cyr_conn = new cyradm;
	$error=$cyr_conn -> imap_login();
	if ($error!=0){
		die ("imap_login Error $error");
	}
	// get the quota used
	$cyrus_quota=$cyr_conn->getquota("user/" . $fullemail);
	/*
	$max_quota=$cyrus_quota['qmax'];
	$quota_used=$cyrus_quota['used'];
	$percent=100*$quota_used/$max_quota;
	*/
	$value=$cyrus_quota['used'];
	$happen="/ ".$cyrus_quota['qmax']. " (" . round(100 * $cyrus_quota['used'] / $cyrus_quota['qmax'],2) . "%)";
	$cyrq = array(
		"value" => $value,
		"happen" => $happen);
	return $cyrq;
}

/////////////////////////////////////////
// One domain email collection edition //
/////////////////////////////////////////
function emailAccountsCreateCallback ($id){
	global $pro_mysql_pop_table;
	global $pro_mysql_list_table;
	global $conf_dtc_system_uid;
	global $conf_dtc_system_gid;
	global $adm_login;
	global $edit_domain;
	global $cyrus_used;

	global $CYRUS;

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created email line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);

	$test_query = "SELECT * FROM $pro_mysql_list_table WHERE name='".$a["id"]."' AND domain='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows >= 1){
		$q = "DELETE FROM $pro_mysql_pop_table WHERE autoinc='$id';";
		$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
		return "<font color=\"red\">Error: a mailing list already exists with this name!</font>";
	}
	$crypted_pass = crypt($a["passwd"], dtc_makesalt());
	writeDotQmailFile($a["id"],$a["mbox_host"]);
	$admin_path = getAdminPath($adm_login);
	$box_path = "$admin_path/$edit_domain/Mailboxs/".$a["id"];
	$q = "UPDATE $pro_mysql_pop_table SET crypt='$crypted_pass',home='$box_path',uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid',fullemail='".$a["id"].'@'.$a["mbox_host"]."',quota_couriermaildrop=CONCAT(quota_size,'S,',quota_files,'C') WHERE autoinc='$id';";
	$r2 = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	triggerMXListUpdate();
	if ($cyrus_used){
		# login to cyradm
		$cyr_conn = new cyradm;
		$error=$cyr_conn -> imap_login();
		if ($error!=0){
			die ("imap_login Error $error");
		}
		$result=$cyr_conn->createmb("user/" . $a["id"]."@".$edit_domain);
		$result=$cyr_conn->createmb("user/" . $a["id"]."/".$a["spam_mailbox"]."@".$edit_domain);
		$result = $cyr_conn->setacl("user/" . $a["id"]."@".$edit_domain, $CYRUS['ADMIN'], "lrswipcda");
		$result = $cyr_conn->setmbquota("user/" . $a["id"]."@".$edit_domain, $a["quota_size"]);
	}
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	return "";
}
function emailAccountsEditCallback ($id){
	global $cyrus_used;
	global $pro_mysql_pop_table;

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created email line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);

	$crypted_pass = crypt($a["passwd"], dtc_makesalt());
	$q = "UPDATE $pro_mysql_pop_table SET crypt='$crypted_pass',quota_couriermaildrop=CONCAT(quota_size,'S,',quota_files,'C') WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());

	writeDotQmailFile($a["id"],$a["mbox_host"]);
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");

	if ($cyrus_used){
		// login to cyradm
		$cyr_conn = new cyradm;
		$error=$cyr_conn -> imap_login();
		if ($error!=0){
			die ("imap_login Error $error");
		}
		if (!$a["quota_size"]){
                        die ("invalid quota");
                }
                $result = $cyr_conn->setmbquota("user/" . $a["fullemail"], $a["quota_size"]);
	}
	return "";
}

function emailAccountsDeleteCallback ($id){
	global $cyrus_used;
	global $pro_mysql_pop_table;

	triggerMXListUpdate();
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	if ($cyrus_used){
		# login to cyradm
		$q = "SELECT id, mbox_host FROM $pro_mysql_pop_table WHERE autoinc='$id';";
		$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("Cannot find created email line ".__LINE__." file ".__FILE__);
		}
                $v = mysql_fetch_row($r);
		$cyr_conn = new cyradm;
		$error=$cyr_conn -> imap_login();
		if ($error!=0){
			die ("imap_login Error $error");
		}
		$result=$cyr_conn->deletemb("user/" . $v[0]."@".$v[1]);
	}
	return "";
}
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
	global $txt_mail_spam_mailbox_enable;
	global $txt_mail_spam_mailbox;
	global $txt_mail_deliver_localy;
	global $txt_mail_edit;
	global $txt_mail_new_mailbox_link;
	global $txt_number_of_active_mailbox;
	global $txt_maximum_mailbox_reach;
	global $txt_mail_catch_no;
	global $txt_mail_catch_all_deliver;
	global $txt_mail_quota;
	global $txt_mail_quota_files;
	global $txt_used_quota;
	global $txt_mail_check_to_send_bounce_msg;
	global $txt_mail_bounce_msg_content;
	global $txt_name;

	global $cyrus_used;
	global $cyrus_default_quota;
	global $CYRUS;

	global $conf_hide_password;
	global $pro_mysql_pop_table;

	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$out = "";
	$dsc = array(
		"title" => $txt_mail_liste_of_your_box[$lang],
		"new_item_title" => $txt_mail_new_mailbox[$lang],
		"new_item_link" => $txt_mail_new_mailbox[$lang],
		"edit_item_title" => $txt_mail_edit[$lang],
		"table_name" => $pro_mysql_pop_table,
		"action" => "pop_access_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "autoinc",
		"list_fld_show" => "id",
		"max_item" => $domain["max_email"],
		"num_item_txt" => $txt_number_of_active_mailbox[$lang],
		"create_item_callback" => "emailAccountsCreateCallback",
		"delete_item_callback" => "emailAccountsDeleteCallback",
		"edit_item_callback" => "emailAccountsEditCallback",
		"where_list" => array(
			"mbox_host" => $domain["name"]),
		"cols" => array(
			"autoinc" => array(
				"type" => "id",
				"display" => "no",
				"legend" => $txt_login_login[$lang]),
			"id" => array(
				"type" => "text",
				"disable_edit" => "yes",
				"check" => "dtc_login_or_email",
				"happen" => "@".$domain["name"],
				"legend" => $txt_login_login[$lang]),
			"memo" => array (
				"type" => "text",
				"legend" => "$txt_name[$lang]"),
			"passwd" => array(
				"type" => "password",
				"check" => "dtc_pass",
				"legend" => $txt_login_pass[$lang]),
			"spam_mailbox_enable" => array(
				"type" => "checkbox",
				"values" => array( "yes","no"),
				"legend" => $txt_mail_spam_mailbox_enable[$lang]),
			"spam_mailbox" => array(
				"type" => "text",
				"default" => "SPAM",
				"legend" => $txt_mail_spam_mailbox[$lang]),
			)
		);
	if($cyrus_used) {
		$dsc["cols"]["quota_size"] = array(
			"type" => "text",
			"check" => "number",
			"default" => "$cyrus_default_quota",
			"legend" => $txt_mail_quota[$lang]);
		$dsc["cols"]["quota_used"] = array(
			"type" => "readonly",
			"hide_create" => "yes",
			"callback" => "getCyrusUsedQuota",
			"legend" => $txt_used_quota[$lang]);
	} else {
		$dsc["cols"]["quota_size"] = array(
			"type" => "text",
			"check" => "number",
			"default" => "0",
			"legend" => $txt_mail_quota[$lang]);
		$dsc["cols"]["quota_files"] = array(
			"type" => "text",
			"check" => "number",
			"default" => "0",
			"legend" => $txt_mail_quota_files[$lang]);
		$dsc["cols"]["redirect1"] = array(
			"type" => "text",
			"check" => "email",
			"can_be_empty" => "yes",
			"empty_makes_sql_null" => "yes",
			"legend" => $txt_mail_redirection1[$lang]);
		$dsc["cols"]["redirect2"] = array(
			"type" => "text",
			"check" => "email",
			"can_be_empty" => "yes",
			"empty_makes_sql_null" => "yes",
			"legend" => $txt_mail_redirection2[$lang]);
		$dsc["cols"]["localdeliver"] = array(
			"type" => "checkbox",
			"values" => array( "yes","no"),
			"legend" => $txt_mail_deliver_localy[$lang]);
		$dsc["cols"]["vacation_flag"] = array(
			"type" => "checkbox",
			"values" => array( "yes","no"),
			"default" => "no",
			"legend" => $txt_mail_check_to_send_bounce_msg[$lang]);
		$dsc["cols"]["vacation_text"] = array(
			"type" => "textarea",
			"legend" => $txt_mail_bounce_msg_content[$lang],
			"cols" => "40",
			"rows" => "7");
	}
        $list_items = dtcListItemsEdit($dsc);

        // We have to query again, in case something has changed
        $q = "SELECT id FROM $pro_mysql_pop_table WHERE mbox_host='".$domain["name"]."';";
        $r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
        $n = mysql_num_rows($r);
	$catch_popup = "<option value=\"no-mail-account\">".$txt_mail_catch_no[$lang]."</option>";
        for($i=0;$i<$n;$i++){
        	$a = mysql_fetch_array($r);
        	if($a["id"] == $domain["catchall_email"]){
        		$selected = " selected ";
		}else{
			$selected = " ";
		}
		$catch_popup .= "<option value=\"".$a["id"]."\" $selected>".$a["id"]."</option>";
        }
	$out .= "<b><u>".$txt_mail_catch_all_deliver[$lang].":</u></b><br>";
	$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	Catchall: <input type=\"hidden\" name=\"action\" value=\"set_catchall_account\">
	<select name=\"catchall_popup\">$catch_popup</select><input type=\"image\" src=\"gfx/stock_apply_20.png\">
</form>";
	
	$out .= $list_items;
	$out .= helpLink("PmWiki/Email-Accounts");
	return $out;


/*
	if ($cyrus_used)
	{
		# login to cyradm
		$cyr_conn = new cyradm;
		$error=$cyr_conn -> imap_login();
		if ($error!=0){
			die ("imap_login Error $error");
		}
	}

	if(isset($domain["emails"])){
		$nbr_email = sizeof($domain["emails"]);
		$emails = $domain["emails"];
		$catchall_email = $domain["catchall_email"];
	}
	else	$nbr_email = 0;

	$max_email = $domain["max_email"];
	if($nbr_email >= $max_email){
		$max_color = "color=\"#440000\"";
	}else{
		$max_color = "";
	}
	$nbrtxt = $txt_number_of_active_mailbox[$lang];
	$txt = "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_email ."</font> / <font size=\"-1\">" . $max_email . "</font><br><br>";

	$txt .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><b><u>".$txt_mail_catch_all_deliver[$lang].":</u><br>";
	$catch_popup = "<option value=\"no-mail-account\">".$txt_mail_catch_no[$lang]."</option>";

	$allmail_list = "";
	$allmail_list .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><b><u>".$txt_mail_liste_of_your_box[$lang]."</u><br>";
	for($i=0;$i<$nbr_email;$i++){
		$email = $emails[$i];
		$id = $email["id"];
		if(isset($_REQUEST["edit_mailbox"]) && $id == $_REQUEST["edit_mailbox"]){
			$mailbox_name = $id;
			$home = $email["home"];
			$passwd = $email["passwd"];
			$redir1 = $email["redirect1"];
			$redir2 = $email["redirect2"];
			$localdeliver = $email["localdeliver"];
			$spam_mailbox_enable = $email["spam_mailbox_enable"];
			if($localdeliver == "yes"){
				$checkbox_state = " checked";
				
			}else{
				$checkbox_state = "";
			}
			if ($spam_mailbox_enable  == "yes"){
                                $spam_checkbox_state = " checked";

                        }else{
                                $spam_checkbox_state = "";
                        }
			$spam_mailbox = $email["spam_mailbox"];
			if($email["vacation_flag"] == "yes"){
				$checkbox_vacation_state = " checked ";
			}else{
				$checkbox_vacation_state = "";
			}
		}
		if($i != 0){
			$allmail_list .= " - ";
		}
		if($id == $catchall_email){
			$catch_popup .= "<option value=\"$id\" selected>$id</option>";
		}else{
			$catch_popup .= "<option value=\"$id\">$id</option>";
		}
		$allmail_list .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails&edit_mailbox=$id\">$id</a>";
	}

	$txt .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"action\" value=\"set_catchall_account\">
	<select name=\"catchall_popup\">$catch_popup</select><input type=\"submit\" value=\"Ok\">
</form>";

	$txt .= "<br><br>".$allmail_list;

	if(!isset($_REQUEST["delemailaccount"]) && isset($_REQUEST["edit_mailbox"]) && $_REQUEST["edit_mailbox"] != ""){
		$txt .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails\">".$txt_mail_new_mailbox_link[$lang]."</a> ";
		$txt .= "<br><br><u>".$txt_mail_edit[$lang]."</u><br><br>";

		$txt .= "
<table border=\"1\"><tr><td align=\"right\">
<form name=\"emailfrm\" action=\"?\" methode=\"post\">
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
		$gen_button = autoGeneratePassButton("emailfrm","editmail_pass");
		if ($conf_hide_password == "yes"){
			$txt .= $txt_login_pass[$lang]."</td><td style=\"white-space: nowrap;\"><input type=\"password\" name=\"editmail_pass\" value=\"$passwd\">$gen_button";
		} else {
			$txt .= $txt_login_pass[$lang]."</td><td style=\"white-space: nowrap;\"><input type=\"text\" name=\"editmail_pass\" value=\"$passwd\">$gen_button";
		}
		$txt .= "
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"editmail_redirect2\" value=\"$redir2\">
</td></tr><tr><td align=\"right\">";

if ($cyrus_used)
{
	$cyrus_quota=$cyr_conn->getquota("user/" . $mailbox_name."@".$edit_domain);
	$max_quota=$cyrus_quota['qmax'];
	$used_quota=$cyrus_quota['used'];
	$txt=$txt.$txt_mail_quota[$lang]."</td><td><input type=\"text\" name=\"cyrus_quota\" value=\"$max_quota\"> Kb</td><td align=\"right\">$txt_used_quota[$lang]: </td><td><b>$used_quota </b>Kb
</td></tr>
<tr><td align=\"right\">";
}

$txt=$txt.$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"editmail_deliver_localy\" value=\"yes\"$checkbox_state></td></tr>
<tr>
<td align=\"right\">".$txt_mail_spam_mailbox_enable[$lang]."</td><td><input type=\"checkbox\" name=\"editmail_spam_mailbox_enable\" value=\"yes\"$spam_checkbox_state></td>
<td align=\"right\">".$txt_mail_spam_mailbox[$lang]."</td><td><input type=\"text\" name=\"editmail_spam_mailbox\" value=\"$spam_mailbox\"></td>
</tr>";
if (!$cyrus_used)
{
	$txt=$txt."<tr>
	<td align=\"right\">Send vacation message:</td><td colspan=\"3\"><input type=\"checkbox\" name=\"editmail_vacation_flag\" value=\"yes\"$checkbox_vacation_state></td>
	</tr><tr>
	<td align=\"right\">Vacation message:</td><td colspan=\"3\"><textarea name=\"editmail_vacation_text\"></textarea></td>
	</tr>";
}
else
{
	$txt=$txt."<input type=\"hidden\" name=\"editmail_vacation_text\" value=\"\">";
}
$txt=$txt."<tr><td>&nbsp;</td><td><input type=\"submit\" name=\"modifymailboxdata\" value=\"Ok\">&nbsp;
<input type=\"submit\" name=\"delemailaccount\" value=\"Del\">
</td></tr>
</table>
</form>
";
	}else{
		$txt .= "<br><br><u>".$txt_mail_new_mailbox[$lang]."</u><br>";

		if($nbr_email < $max_email){
			if(isset($mailbox_name))	$mn = $mailbox_name;
			else	$mn = "";
			$txt .= "
<form name=\"emailfrm\" action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<table border=\"1\"><tr><td align=\"right\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newmail_login\" value=\"$mn\">
</td><td align=\"right\">
	".$txt_mail_redirection1[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect1\" value=\"\">
</td></tr><tr><td align=\"right\">";
	if(isset($passwd)) $pd = $passwd;
	else $pd = "";
	if ($conf_hide_password == "yes"){
		$txt .= $txt_login_pass[$lang]."</td><td style=\"white-space: nowrap;\"><input type=\"password\" name=\"newmail_pass\" value=\"$pd\">".autoGeneratePassButton("emailfrm","newmail_pass");
	}else{
		$txt .= $txt_login_pass[$lang]."</td><td style=\"white-space: nowrap;\"><input type=\"text\" name=\"newmail_pass\" value=\"$pd\">".autoGeneratePassButton("emailfrm","newmail_pass");
	}
$txt .= "
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect2\" value=\"\">
</td></tr>
<tr><td align=\"right\">";

if ($cyrus_used)
{
	$txt=$txt.$txt_mail_quota[$lang]."</td><td><input type=\"text\" name=\"cyrus_quota\" value=\"$cyrus_default_quota\">
</td></tr>
<tr><td align=\"right\">";
}


$txt=$txt.$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"newmail_deliver_localy\" value=\"yes\" checked></td></tr>
<tr>
<td align=\"right\">".$txt_mail_spam_mailbox_enable[$lang]."</td><td><input type=\"checkbox\" name=\"newmail_spam_mailbox_enable\" value=\"no\"></td>
<td align=\"right\">".$txt_mail_spam_mailbox[$lang]."</td><td><input type=\"text\" name=\"newmail_spam_mailbox\" value=\"SPAM\"></td>
</tr>
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
*/
}

?>
