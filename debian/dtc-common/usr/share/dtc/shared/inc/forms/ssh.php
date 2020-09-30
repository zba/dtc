<?php

function sshAccountsCallback ($id){
	global $pro_mysql_ssh_table;
	global $conf_dtc_system_uid;
	global $conf_dtc_system_gid;

	$q = "UPDATE $pro_mysql_ssh_table SET uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid' WHERE id='$id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	return "";
}

////////////////////////////////////////////////////
// One domain name ssh account collection edition //
////////////////////////////////////////////////////
function drawAdminTools_SSH($domain,$adm_path){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;

	global $edssh_account;
	global $addrlink;

	global $conf_hide_password;
	global $conf_domain_based_ssh_logins;
	global $pro_mysql_ssh_table;

	$txt = "";

        // Build the popup values and display values arrays
	$path_popup_vals = array();
	$path_popup_disp = array();
	//$path_popup_vals[] = "$adm_path";
	//$path_popup_disp[] = "/ [ uses www ]";
	//$path_popup_vals[] = "$adm_path/$edit_domain";
	//$path_popup_disp[] = "/$edit_domain [ uses www ]";
	$nbr_subdomains = sizeof($domain["subdomains"]);
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub_name = $domain["subdomains"][$i]["name"];
		$path_popup_vals[] = "$adm_path/$edit_domain/subdomains/$sub_name";
		$path_popup_disp[] = "/$edit_domain/subdomains/$sub_name";
	}

	$dsc = array(
		"title" => _("List of your SSH accounts:") ,
		"new_item_title" => _("New SSH account:") ,
		"new_item_link" => _("new SSH account") ,
		"edit_item_title" => _("SSH account configuration:") ,
		"table_name" => $pro_mysql_ssh_table,
		"action" => "ssh_access_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "login",
		"max_item" => $domain["max_ssh"],
		"num_item_txt" => _("Number of active ssh accounts:") ,
		"create_item_callback" => "sshAccountsCallback",
		"where_list" => array(
			"hostname" => $domain["name"]),
		"check_unique" => array( "login" ),
		"check_unique_msg" => _("There is already an ssh login by that name") ,
		"check_unique_use_where_list" => "no",
		"order_by" => "login",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"login" => array(
				"type" => "text",
				"check" => "dtc_login_or_email",
				"legend" => _("Login:") ),
			"password" => array(
				"type" => "password",
				"check" => "dtc_pass",
				"cryptfield" => "crypt",
				"legend" => _("Password:") ),
			"homedir" => array(
				"type" => "popup",
				"values" => $path_popup_vals,
				"display_replace" => $path_popup_disp,
				"legend" => _("Path:") )
			)
		);
	if($conf_domain_based_ssh_logins == "yes"){
		$dsc["cols"]["login"]["happen_domain"] = "@".$domain["name"];
	}
	$txt .= dtcListItemsEdit($dsc);
	$txt .= helpLink("PmWiki/Ssh-Accounts");
	return $txt;

}

?>
