<?php

function ftpAccountsCallback ($id){
	global $pro_mysql_ftp_table;
	global $conf_dtc_system_uid;
	global $conf_dtc_system_gid;

	$q = "UPDATE $pro_mysql_ftp_table SET uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid' WHERE id='$id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	return "";
}

////////////////////////////////////////////////////
// One domain name ftp account collection edition //
////////////////////////////////////////////////////
function drawAdminTools_Ftp($domain,$adm_path){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;

	global $edftp_account;
	global $addrlink;

	global $conf_hide_password;
	global $conf_domain_based_ftp_logins;

	global $pro_mysql_ftp_table;
	global $pro_mysql_admin_table;

	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$q = "SELECT restricted_ftp_path,ftp_login_flag FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("adm_login $adm_login not found line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	if($a["ftp_login_flag"] == "no"){
		die("adm_login $adm_login had no rights to edit FTP accounts line ".__LINE__." file ".__FILE__);
	}

	$txt = "";

	// Build the popup values and display values arrays
	$path_popup_vals = array();
	$path_popup_disp = array();
	if($a["restricted_ftp_path"] != "yes"){
		$path_popup_vals[] = "$adm_path";
		$path_popup_disp[] = "/";
		$path_popup_vals[] = "$adm_path/$edit_domain";
		$path_popup_disp[] = "/$edit_domain";
	}
	$nbr_subdomains = sizeof($domain["subdomains"]);
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub_name = $domain["subdomains"][$i]["name"];
		if($a["restricted_ftp_path"] != "yes"){
			$path_popup_vals[] = "$adm_path/$edit_domain/subdomains/$sub_name";
			$path_popup_disp[] = "/$edit_domain/subdomains/$sub_name";
		}
		$path_popup_vals[] = "$adm_path/$edit_domain/subdomains/$sub_name/html";
		$path_popup_disp[] = "/$edit_domain/subdomains/$sub_name/html";
	}

	// Just create the list editor now...
	$dsc = array(
		"title" => _("List of your FTP accounts: ") ,
		"new_item_title" => _("New FTP account: ") ,
		"new_item_link" => _("new FTP account") ,
		"edit_item_title" => _("FTP account configuration: "),
		"table_name" => $pro_mysql_ftp_table,
		"action" => "ftp_access_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "login",
		"max_item" => $domain["max_ftp"],
		"num_item_txt" => _("Number of active ftp accounts:") ,
		"create_item_callback" => "ftpAccountsCallback",
		"where_list" => array(
			"hostname" => $domain["name"]),
		"check_unique" => array( "login" ),
		"check_unique_msg" => _("There is already an ftp login by that name") ,
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
				"legend" => _("Login: ")),
			"password" => array(
				"type" => "password",
				"check" => "dtc_pass",
				"legend" => _("Password: ") ),
			"homedir" => array(
				"type" => "popup",
				"values" => $path_popup_vals,
				"display_replace" => $path_popup_disp,
				"legend" => _("Path: "))
			)
		);
	if($conf_domain_based_ftp_logins == "yes"){
		$dsc["cols"]["login"]["happen_domain"] = "@".$domain["name"];
	}
	$txt .= dtcListItemsEdit($dsc);
	return $txt;

}

?>
