<?php

/**
 * 
 * @package DTC
 * @version $Id: aliases.php,v 1.1 2007/06/12 05:24:57 thomas Exp $
 * @param unknown_type $mailalias
 * @return unknown
 */
/*
function drawAdminTools_AliasPanel($mailbox){
	global $conf_skin;
	global $addrlink;

	global $txt_mail_edit;
	global $lang;

	global $adm_email_login;
	global $adm_email_pass;

	global $txt_user_menu_email;
	global $txt_user_menu_fetchmail;
	global $txt_logout;
	global $pro_mysql_mailaliasgroup_table;

	echo "<pre>";print_r($mailbox);echo "</pre>";

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
*/

//
// Main panel
//
function drawAdminTools_Aliases($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $edit_mailbox;
	global $addrlink;

	global $lang;
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_number_of_active_mailbox;
	global $txt_mail_alias_delivery_group;
	global $txt_draw_client_info_email;

	global $pro_mysql_pop_table;
	global $pro_mysql_mailaliasgroup_table;
	global $txt_mail_alias_list_current_aliases;
	global $txt_mail_alias_new_item_title;
	global $txt_mail_alias_new_item_link;
	global $txt_mail_alias_edit_item_title;
	
	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$out = "";
	$dsc = array(
		"title" => $txt_mail_alias_list_current_aliases[$lang],
		"new_item_title" => $txt_mail_alias_new_item_title[$lang],
		"new_item_link" => $txt_mail_alias_new_item_link[$lang],
		"edit_item_title" => $txt_mail_alias_edit_item_title[$lang],
		"table_name" => $pro_mysql_mailaliasgroup_table,
		"action" => "aliasgroup",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "autoinc",
		"list_fld_show" => "id",
		"max_item" => $domain["max_email"],
		"num_item_txt" => $txt_number_of_active_mailbox[$lang],
		"create_item_callback" => "emailAliasesCreateCallback",
		"delete_item_callback" => "emailAliasesDeleteCallback",
		"edit_item_callback" => "emailAliasesEditCallback",
		"where_list" => array(
			"domain_parent" => $domain["name"]),
		"cols" => array(
			"autoinc" => array(
				"type" => "id",
				"display" => "no",
				"legend" => $txt_login_login[$lang]),
			"id" => array(
				"type" => "text",
				"check" => "dtc_login_or_email",
				"disable_edit" => "yes",
				"happen" => "@".$domain["name"],
				"legend" => $txt_draw_client_info_email[$lang]),
		    "delivery_group" => array(
				"type" => "textarea",
				"check" => "mail_alias_group",
				"legend" => $txt_mail_alias_delivery_group[$lang],
				"cols" => "40",
				"rows" => "7")
			),
		"check_unique" => array( "id"),
		"check_unique_msg" => "Email address is already in use!"
		);
        $list_items = dtcListItemsEdit($dsc);

        // We have to query again, in case something has changed
        $q = "SELECT id FROM $pro_mysql_mailaliasgroup_table WHERE domain_parent='".$domain["name"]."';";
        $r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
        $n = mysql_num_rows($r);
	
	$out .= $list_items;
	$out .= helpLink("PmWiki/Email-Accounts");
	return $out;

}

function emailAliasesEditCallback ($id){
	global $pro_mysql_pop_table;
	global $pro_mysql_mailaliasgroup_table;
	
	$q = "SELECT * FROM $pro_mysql_mailaliasgroup_table WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created email line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);

	$q = "UPDATE $pro_mysql_mailaliasgroup_table SET delivery_group='".trim($_REQUEST['delivery_group'])."' WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());

	//writeDotQmailFile($a["id"],$a["mbox_host"]);
	triggerMXListUpdate();
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");

	return "";
}

function emailAliasesCreateCallback ($id){
	global $pro_mysql_list_table;
	global $conf_dtc_system_uid;
	global $conf_dtc_system_gid;
	global $adm_login;
	global $edit_domain;
	global $pro_mysql_mailaliasgroup_table;
	global $pro_mysql_pop_table;
	
	$q = "SELECT * FROM $pro_mysql_mailaliasgroup_table WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created email line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);

	$test_query = "SELECT * FROM $pro_mysql_list_table WHERE name='".$a["id"]."' AND domain='$edit_domain'";
	$test_result = mysql_query ($test_query) or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows >= 1){
		$q = "DELETE FROM $pro_mysql_mailaliasgroup_table WHERE autoinc='$id';";
		$r = mysql_query($q) or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
		return "<font color=\"red\">Error: Email address already exists with this name!</font><br />";
	}
	
	$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$a["id"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query) or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows >= 1){
		$q = "DELETE FROM $pro_mysql_mailaliasgroup_table WHERE autoinc='$id';";
		$r = mysql_query($q) or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
		return "<font color=\"red\">Error: Email address already exists with this name!</font><br />";
	}
	
	triggerMXListUpdate();
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	return "";
}

function emailAliasesDeleteCallback ($id){
	global $pro_mysql_mailaliasgroup_table;
	
	triggerMXListUpdate();
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");

	$q = "DELETE FROM $pro_mysql_mailaliasgroup_table WHERE autoinc='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	if($r != true ){
		die("Cannot find created email line ".__LINE__." file ".__FILE__);
	}
	triggerMXListUpdate();
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");

	return "";
}

?>
