<?php

function fetchmailAccountsCreateCallback($id){
	updateUsingCron("gen_fetchmail='yes'");
}
function fetchmailAccountsDeleteCallback($id){
	updateUsingCron("gen_fetchmail='yes'");
}
function fetchmailAccountsEditCallback($id){
	updateUsingCron("gen_fetchmail='yes'");
}
function drawImportedMail($mailbox){
	global $adm_email_login;
	global $adm_email_pass;
	global $errTxt;
	global $pro_mysql_fetchmail_table;

	$mydomain = $mailbox["data"]["mbox_host"];
	$myuserid = $mailbox["data"]["id"];

	$out = "";
	$dsc = array(
		"title" => _("List of your fetchmail imported accounts:") ,
		"new_item_title" => _("New fetchmail address") ,
		"new_item_link" => _("new fetchmail address") ,
		"edit_item_title" => _("Fetchmail configuration:") ,
		"table_name" => $pro_mysql_fetchmail_table,
		"action" => "fetchmail_table_editor",
		"forward" => array("adm_email_login","adm_email_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "pop3_email",
		"max_item" => 3,
		"num_item_txt" => _("Number of active fetchmail imported email boxes:") ,
		"where_list" => array(
			"domain_name" => $mydomain,
			"domain_user" => $myuserid),
		"check_unique" => array( "pop3_email" ),
		"check_unique_msg" => _("There is already a mailbox by that name") ,
		"order_by" => "pop3_email",
		"create_item_callback" => "fetchmailAccountsCreateCallback",
		"delete_item_callback" => "fetchmailAccountsDeleteCallback",
		"edit_item_callback" => "fetchmailAccountsEditCallback",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => _("Login:") ),
			"pop3_email" => array(
				"type" => "text",
				"check" => "email",
				"legend" => _("Email to fetch:") ),
			"pop3_server" => array (
				"type" => "text",
				"check" => "subdomain_or_ip",
				"legend" => _("Mail server to import from:") ),
			"pop3_login" => array(
				"type" => "text",
				"check" => "dtc_login_or_email",
				"legend" => _("Login:") ),
			"pop3_pass" => array(
				"type" => "password",
				"legend" => _("Password:") ),
			"checkit" => array(
				"type" => "checkbox",
				"values" => array( "yes","no"),
				"default" => "no",
				"legend" => _("Use it:") ),
			)
		);
        $out = dtcListItemsEdit($dsc);
	return $out;
}
/*
function drawAntispamRules($mailbox){
	global $adm_email_login;
	global $adm_email_pass;
	global $pro_mysql_whitelist_table;
	global $pro_mysql_whitelist_table;

	$bnc_msg = _("Hello,
You have sent an email to me, but due to the large amount
of spam I recieve, I use antispam software that requires message
confirmation. This is very easy, and you will have to do it only once.
Just click on the following link, copy the number you see on the
screen and I will receive the message you sent me. If you do not
click, then your message will be considered spam and I will
NOT receive it.

***URL***

Thank you for your understanding.

Antispam software:
Grey listing+SPF, only available with Domain Technologie Control
http://www.gplhost.com/software-dtc.html
");

	$out = "";

	$url_start = "<a href=\"?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

	if($mailbox["data"]["iwall_protect"] == "yes")	$checked = " checked "; else $checked = "";
	$out = $form_start."<input type=\"hidden\" name=\"action\" value=\"activate_antispam\"><input type=\"checkbox\" name=\"iwall_on\" value=\"yes\"$checked>" . _("Activate iGlobalWall protection") . "
	". drawSubmitButton( _("Ok") ) ."</form>";

	if($mailbox["data"]["iwall_protect"] == "yes"){
		$frm_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"".$_REQUEST["adm_email_login"]."\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"".$_REQUEST["adm_email_pass"]."\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

		if(strlen($mailbox["data"]["bounce_msg"]) < 9 || false == strstr($mailbox["data"]["bounce_msg"],"***URL***")){
			$zebounce = $bnc_msg;
		}else{
			$zebounce = $mailbox["data"]["bounce_msg"];
		}

		$out .= "<br><h3>" . _("Your bounce message") . ":</h3>
		
		$frm_start<input type=\"hidden\" name=\"action\" value=\"edit_bounce_msg\">
		<textarea cols=\"80\" rows=\"16\" name=\"bounce_msg\">$zebounce</textarea><br><br>
		<input type=\"submit\" value=\"" . _("Save") . "\"></form>";

		$out .= "<h3>" . _("Your white list") . ":</h3>";

		$q = "SELECT * FROM $pro_mysql_whitelist_table";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"1\">
<tr><td>" . _("Mail user from") . "</td><td>&nbsp;</td><td>" . _("Mail user domain") . "</td><td>" . _("Mail user to (mailling list)") . "</td><td>" . _("Action") . "</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$out .= "<tr>$frm_start
			<input type=\"hidden\" name=\"action\" value=\"edit_whitelist_rule\">
			<input type=\"hidden\" name=\"ruleid\" value=\"".$a["id"]."\">";
			$out .= "<td><input type=\"text\" name=\"mail_from_user\" value=\"".$a["mail_from_user"]."\"></td>";
			$out .= "<td>@</td>";
			$out .= "<td><input type=\"text\" name=\"mail_from_domain\" value=\"".$a["mail_from_domain"]."\"></td>";
			$out .= "<td><input type=\"text\" size=\"30\" name=\"mail_to\" value=\"".$a["mail_to"]."\"></td>";
			$out .= "<td><input type=\"submit\"  value=\"". _("Save") . "\"></form>
			$frm_start<input type=\"hidden\" name=\"ruleid\" value=\"".$a["id"]."\">
			<input type=\"hidden\" name=\"action\" value=\"delete_whitelist_rule\">
			<input type=\"submit\" value=\"". _("Delete") . "\"></form></td></tr>";
		}
		$out .= "<tr>$frm_start
			<input type=\"hidden\" name=\"action\" value=\"add_whitelist_rule\">
			<td><input type=\"text\" name=\"mail_from_user\" value=\"\"></td>";
		$out .= "<td>@</td>";
		$out .= "<td><input type=\"text\" name=\"mail_from_domain\" value=\"\"></td>";
		$out .= "<td><input type=\"text\" size=\"30\" name=\"mail_to\" value=\"\"></td>";
		$out .= "<td><input type=\"submit\" value=\"" . _("Save") . "\"></form></td></tr>";
		$out .= "</table>";
	}
	if($mailbox["data"]["clamav_protect"] == "yes")	$checked = " checked "; else $checked = "";
	$out .= $form_start."<input type=\"hidden\" name=\"action\" value=\"activate_clamav\"><input type=\"checkbox\" name=\"clamav_on\" value=\"yes\"$checked>" . _("Activate Clamav antivirus") . "
	". drawSubmitButton( _("Ok") ) ."</form>";
	return $out;
}

function drawQuarantine($mailbox){
	global $adm_email_login;
	global $adm_email_pass;

	$out = "";

	$url_start = "<a href=\"?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

//	$out = $form_start."<input type=\"hidden\" name=\"action\" value=\"activate_antispam\"><input type=\"checkbox\" name=\"iwall_on\" value=\"yes\"$checked>Activate iGlobalWall protection
//	<input type=\"submit\" value=\"Ok\"></form>";
	$out = _("Here will go quarantinized messages");

	return $out;
}
*/
function drawAdminTools_emailAccount($mailbox){	
	global $adm_email_login;
	global $adm_email_pass;
	global $cyrus_used;

	$url_start = "<a href=\"?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass&addrlink=".$_REQUEST["addrlink"];
	$form_start = "<form action=\"?\" method=\"post\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";

	// Draw the form for changing the password
	$left = "<h3>". _("Change your password:") ."</h3>
<table cellpadding=\"0\" cellspacing=\"0\">
<tr>
	<td align=\"right\">".$form_start. _("Password: ") ."</td>
	<td><input type=\"hidden\" name=\"action\" value=\"dtcemail_change_pass\"><input type=\"password\" name=\"newpass1\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">". _("Confirm password: ") ."</td>
	<td><input type=\"password\" name=\"newpass2\" value=\"\"></td>
</tr><tr>
	<td></td><td>". drawSubmitButton( _("Ok") ) ."</form></td>
</tr></table>
<br><br>";

	if($mailbox["data"]["vacation_flag"] == "yes"){
		$use_vacation_msg_yes_checked = " checked ";
		$use_vacation_msg_no_checked = " ";
	}else{
		$use_vacation_msg_yes_checked = " ";
		$use_vacation_msg_no_checked = " checked ";
	}

	if (!$cyrus_used){
  	$left .= "<h3>" . _("Vacation message") . "</h3>
  	".$form_start."<input type=\"hidden\" name=\"action\" value=\"dtcemail_vacation_msg\">
  <input type=\"radio\" name=\"use_vacation_msg\" value=\"yes\" $use_vacation_msg_yes_checked>"._("Yes")."<input type=\"radio\" name=\"use_vacation_msg\" value=\"no\" $use_vacation_msg_no_checked>"._("No")."
  <br>
  <textarea cols=\"40\" rows=\"7\" name=\"vacation_msg_txt\">".$mailbox["data"]["vacation_text"]."</textarea><br>
  ". drawSubmitButton( _("Ok") ) ."</form>";
  }
	if($mailbox["data"]["localdeliver"] == "yes"){
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=no\"><font color=\"green\">"._("Yes")."</font></a>";
	}else{
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=yes\"><font color=\"red\">"._("No")."</font></a>";
	}
	if (!$cyrus_used){
    	$right = "<h3>". _("Edit your mailbox redirections:") ."</h3>
    ". _("Deliver messages locally in INBOX: ") ." $deliverUrl
    <table cellpadding=\"0\" cellspacing=\"0\">
    <tr>
    	<td align=\"right\">".$form_start. _("Redirection 1: ") ."</td>
    	<td><input type=\"hidden\" name=\"action\" value=\"dtcemail_edit_redirect\"><input type=\"text\" name=\"redirect1\" value=\"".$mailbox["data"]["redirect1"]."\"></td>
    </tr><tr>
    	<td>". _("Redirection 2: ") ."</td>
    	<td><input type=\"text\" name=\"redirect2\" value=\"".$mailbox["data"]["redirect2"]."\"></td>
    </tr><tr>
    	<td></td><td>". drawSubmitButton( _("Ok") ) ."</form></td>
    </tr></table><br><br>";

    	if($mailbox["data"]["spam_mailbox_enable"] == "yes"){
    		$spambox_yes_checked = " checked ";
    		$spambox_no_checked = " ";
    	}else{
    		$spambox_yes_checked = " ";
    		$spambox_no_checked = " checked ";
    	}

    	$right .= "<h3>" . _("Anti-SPAM control") . "</h3>
    <table cellpadding=\"0\" cellspacing=\"0\">
    <tr>
    	<td align=\"right\">Deliver spam to spambox:</td><td>".$form_start."<input type=\"hidden\" name=\"action\" value=\"dtcemail_spambox\">
    <input type=\"radio\" name=\"spam_mailbox_enable\" value=\"yes\" $spambox_yes_checked>"._("Yes")."<input type=\"radio\" name=\"spam_mailbox_enable\" value=\"no\" $spambox_no_checked>"._("No")."</td>
    </tr><tr>
    	<td align=\"right\">" . _("SPAM mailbox name") . ":</td><td><input type=\"text\" name=\"spam_mailbox\" value=\"".$mailbox["data"]["spam_mailbox"]."\"></td>
    </tr><tr>
    	<td></td><td>". drawSubmitButton( _("Ok") ) ."</form></td></tr></table>";
	}
	else { $right=""; }
	
	// Output the form
	$out = "<table width=\"100%\" heigh=\"1\">
<tr>
	<td width=\"50%\" valign=\"top\">".$left."</td>
	<td width=\"4\" background=\"gfx/border_2.gif\"></td>
	<td valign=\"top\">".$right."</td>
</tr>
</table>
";
	return $out;
}

function drawAdminTools_emailPanel($mailbox){
	global $conf_skin;
	global $addrlink;

	global $adm_email_login;
	global $adm_email_pass;

	$user_menu[] = array(
		"text" => _("My e-mail") ,
		"icon" => "box_wnb_nb_picto-mailboxes.gif",
		"type" => "link",
		"link" => "my-email");
	$user_menu[] = array(
		"text" => _("Fetchmail") ,
		"icon" => "box_wnb_nb_picto-mailinglists.gif",
		"type" => "link",
		"link" => "fetchmail");

	$logout = "<a href=\"?action=logout\">". _("Logout") ."</a>";
	$mymenu = makeTreeMenu($user_menu,$addrlink,"?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass","addrlink");

	switch($addrlink){
	case "my-email":
		$title = _("Mailbox configuration: ") ;
		$panel = drawAdminTools_emailAccount($mailbox);
		break;
	case "fetchmail":
		$title = _("Your list of imported mail") ;
		$panel = drawImportedMail($mailbox);
		break;
/*	case "antispam":
		$title = _("Protect your mailbox with efficient tools:") ;
		$panel = drawAntispamRules($mailbox);
		break;
	case "quarantine":
		$title = _("Those mail are in quarantine, and were not delivered to your pop account:") ;
		$panel = drawQuarantine($mailbox);
		break;*/
	default:
		$title = _("Welcom to the email panel!");
		$panel = _("Login successfull. Please select a menu entry on the left...");
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
		<td width=\"1\"  height=\"100%\">$left</td>
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
	global $pro_mysql_mailaliasgroup_table;
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
		return "<font color=\"red\">". _("Error: a mailing list already exists with this name!") ."</font>";
	}
	$test_query = "SELECT * FROM $pro_mysql_mailaliasgroup_table WHERE id='".$a["id"]."' AND domain_parent='$edit_domain'";
	$test_result = mysql_query ($test_query) or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows >= 1){
		$q = "DELETE FROM $pro_mysql_pop_table WHERE autoinc='$id';";
		$r = mysql_query($q) or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
		return "<font color=\"red\">". _("Error: Email group alias already exists with this name!") ."</font><br />";
	}
	$crypted_pass = crypt($a["passwd"], dtc_makesalt());
	if (!$cyrus_used){
		writeDotQmailFile($a["id"],$a["mbox_host"]);
	}
	$admin_path = getAdminPath($adm_login);
	$box_path = "$admin_path/$edit_domain/Mailboxs/".$a["id"];
	$q = "UPDATE $pro_mysql_pop_table SET crypt='$crypted_pass',home='$box_path',uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid',fullemail='".$a["id"].'@'.$a["mbox_host"]."',quota_couriermaildrop=CONCAT(1024000*quota_size,'S,',quota_files,'C') WHERE autoinc='$id';";
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
	$q = "UPDATE $pro_mysql_pop_table SET crypt='$crypted_pass',quota_couriermaildrop=CONCAT(1024000*quota_size,'S,',quota_files,'C') WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());

	if(!$cyrus_used){
		writeDotQmailFile($a["id"],$a["mbox_host"]);
	}
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
	global $pro_mysql_fetchmail_table;

	triggerMXListUpdate();
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	$q = "SELECT id, mbox_host, home FROM $pro_mysql_pop_table WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created email line ".__LINE__." file ".__FILE__);
	}
	$v = mysql_fetch_array($r);
	if ($cyrus_used){
		# login to cyradm
		$cyr_conn = new cyradm;
		$error=$cyr_conn -> imap_login();
		if ($error!=0){
			die ("imap_login Error $error");
		}
		$result=$cyr_conn->deletemb("user/" . $v["id"]."@".$v["mbox_host"]);
	}
	$cmd = "rm -rf " . $v["home"];
	exec($cmd,$exec_out,$return_val);
	$q = "DELETE FROM $pro_mysql_fetchmail_table WHERE domain_user='".$v["id"]."' AND domain_name='".$v["mbox_host"]."';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	updateUsingCron("qmail_newu='yes',restart_qmail='yes',gen_qmail='yes'");
	return "";
}
function drawAdminTools_Emails($domain){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $edit_mailbox;
	global $addrlink;

	global $cyrus_used;
	global $cyrus_default_quota;
	global $CYRUS;

	global $conf_hide_password;
	global $pro_mysql_pop_table;

	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$out = "";
	$dsc = array(
		"title" => _("List of your mailboxes:") ,
		"new_item_title" => _("New mailbox") ,
		"new_item_link" => _("new mailbox") ,
		"edit_item_title" => _("Mailbox configuration:") ,
		"table_name" => $pro_mysql_pop_table,
		"action" => "pop_access_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "autoinc",
		"list_fld_show" => "id",
		"max_item" => $domain["max_email"],
		"num_item_txt" => _("Number of active mailboxes:") ,
		"create_item_callback" => "emailAccountsCreateCallback",
		"delete_item_callback" => "emailAccountsDeleteCallback",
		"edit_item_callback" => "emailAccountsEditCallback",
		"where_list" => array(
			"mbox_host" => $domain["name"]),
		"check_unique" => array( "id" ),
		"check_unique_msg" => _("There is already a mailbox by that name") ,
		"order_by" => "id",
		"cols" => array(
			"autoinc" => array(
				"type" => "id",
				"display" => "no",
				"legend" => _("Login:") ),
			"id" => array(
				"type" => "text",
				"disable_edit" => "yes",
				"check" => "dtc_login_or_email",
				"happen" => "@".$domain["name"],
				"legend" => _("Login:") ),
			"memo" => array (
				"type" => "text",
				"help" => _("This text is just a memo for yourself, and will not really be used."),
				"legend" => _("Name:") ),
			"passwd" => array(
				"type" => "password",
				"check" => "dtc_pass",
				"legend" => _("Password:") ),
			"spam_mailbox_enable" => array(
				"type" => "checkbox",
				"help" => _("If selected, spam will be saved in a SPAM folder and won't reach your inbox. Later you may check this folder with webmail or an IMAP client."),
				"values" => array( "yes","no"),
				"legend" => _("Enable SPAM filtering: ") ),
			"spam_mailbox" => array(
				"type" => "text",
				"help" => _("Name of the SPAM folder (the above option has to be activated)."),
				"default" => "SPAM",
				"legend" => _("SPAM mailbox destination: ") ),
			)
		);
	if($cyrus_used) {
		$dsc["cols"]["quota_size"] = array(
			"type" => "text",
			"check" => "number",
			"default" => "$cyrus_default_quota",
			"legend" => _("Mailbox quota: ") );
		$dsc["cols"]["quota_used"] = array(
			"type" => "readonly",
			"hide_create" => "yes",
			"callback" => "getCyrusUsedQuota",
			"happen" => _("MBytes"),
			"legend" => _("Used quota: ") );
	} else {
		$dsc["cols"]["quota_size"] = array(
			"type" => "text",
			"check" => "max_value_2096",
			"default" => "10",
			"happen" => _("MBytes"),
			"help" => _("Setting BOTH the number of files and overall mailbox size to zero will disable quota."),
			"legend" => _("Mailbox quota: ") );
		$dsc["cols"]["quota_files"] = array(
			"type" => "text",
			"check" => "number",
			"default" => "1024",
			"happen" => _("files"),
			"legend" => _("Mailbox max files quota: ") );
		$dsc["cols"]["redirect1"] = array(
			"type" => "text",
			"check" => "email",
			"can_be_empty" => "yes",
			"empty_makes_sql_null" => "yes",
			"legend" => _("Redirection 1: ") );
		$dsc["cols"]["redirect2"] = array(
			"type" => "text",
			"check" => "email",
			"can_be_empty" => "yes",
			"empty_makes_sql_null" => "yes",
			"legend" => _("Redirection 2: ") );
		$dsc["cols"]["localdeliver"] = array(
			"type" => "checkbox",
			"values" => array( "yes","no"),
			"legend" => _("Deliver messages locally in INBOX: ") );
		$dsc["cols"]["vacation_flag"] = array(
			"type" => "checkbox",
			"values" => array( "yes","no"),
			"default" => "no",
			"legend" => _("Check to send a bounce (vacation) message: ") );
		$dsc["cols"]["vacation_text"] = array(
			"type" => "textarea",
			"legend" => _("Bounce message content: ") ,
			"cols" => "40",
			"rows" => "7");
	}
        $list_items = dtcListItemsEdit($dsc);

        // We have to query again, in case something has changed
        $q = "SELECT id FROM $pro_mysql_pop_table WHERE mbox_host='".$domain["name"]."';";
        $r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
        $n = mysql_num_rows($r);
	$catch_popup = "<option value=\"no-mail-account\">". _("No catch-all") ."</option>";
        for($i=0;$i<$n;$i++){
        	$a = mysql_fetch_array($r);
        	if($a["id"] == $domain["catchall_email"]){
        		$selected = " selected ";
		}else{
			$selected = " ";
		}
		$catch_popup .= "<option value=\"".$a["id"]."\" $selected>".$a["id"]."</option>";
        }
	$out .= "<b><u>". _("Catch-all email set to deliver to") .":</u></b><br>";
	$out .= "<form action=\"?\" method=\"post\">
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
}

?>
