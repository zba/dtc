<?php

//
// Main panel
//
function drawAdminTools_Aliases($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $edit_mailbox;
	global $addrlink;

	global $pro_mysql_pop_table;
	global $pro_mysql_mailaliasgroup_table;

	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$out = "";
	$dsc = array(
		"title" => _("List of your mail groups") ,
		"new_item_title" => _("Create New Mail Group") ,
		"new_item_link" => _("Create Mail Group") ,
		"edit_item_title" => _("Edit Mail Group") ,
		"table_name" => $pro_mysql_mailaliasgroup_table,
		"action" => "aliasgroup",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "autoinc",
		"list_fld_show" => "id",
		"max_item" => $domain["max_email"],
		"num_item_txt" => _("Number of active mailboxes:"),
		"create_item_callback" => "emailAliasesCreateCallback",
		"delete_item_callback" => "emailAliasesDeleteCallback",
		"edit_item_callback" => "emailAliasesEditCallback",
		"order_by" => "id",
		"where_list" => array(
			"domain_parent" => $domain["name"]),
		"cols" => array(
			"autoinc" => array(
				"type" => "id",
				"display" => "no",
				"legend" => _("Login:") ),
			"id" => array(
				"type" => "text",
				"check" => "dtc_login_or_email",
				"disable_edit" => "yes",
				"happen" => "@".$domain["name"],
				"legend" => _("Email:") ),
			"delivery_group" => array(
				"type" => "textarea",
				"check" => "mail_alias_group",
				"legend" => _("Delivery Group:") ,
				"cols" => "40",
				"rows" => "7")
			),
		"check_unique" => array( "id"),
		"check_unique_msg" => _("Email address is already in use.")
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

///////////////////
// Few callbacks //
///////////////////

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
