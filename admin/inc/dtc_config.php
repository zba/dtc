<?php
/**
 * @package DTC
 * @version $Id: dtc_config.php,v 1.84 2007/01/22 05:48:04 thomas Exp $
 * @todo intrenationalize menus
 * @return forms
 * 
 */

function configEditorTemplate ($dsc,$conftype="config"){
	global $pro_mysql_config_table;
	global $pro_mysql_secpayconf_table;
	$out = "";

	if($conftype == "config"){
		$sql_table = $pro_mysql_config_table;
		$prefix = "conf_";
	}else{
		$sql_table = $pro_mysql_secpayconf_table;
		$prefix = "secpayconf_";
	}

	$keys = array_keys($dsc["cols"]);
	$n = sizeof($keys);

	// Do the sql stuff here!
	if( isset($_REQUEST["action"]) && $_REQUEST["action"] == $dsc["action"] ){
		$vals = "";
		for($i=0;$i<$n;$i++){
			if($i != 0){
				$vals .= ", ";
			}
			$vals .= $keys[$i]."='".$_REQUEST[ $keys[$i] ]."'";
		}
		$q = "UPDATE $sql_table SET $vals WHERE 1;";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
	}

	if($conftype == "config"){
		getConfig();
	}else{
		get_secpay_conf();
	}

	$out .= "<h3><u>".$dsc["title"]."</u></h3>";
	if( isset($dsc["desc"]) ){
		$out .= $dsc["desc"]."<br><br>";
	}

	$nbr_forwards = sizeof($dsc["forward"]);
	$fw = "";
	for($i=0;$i<$nbr_forwards;$i++){
		$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$_REQUEST[ $dsc["forward"][$i] ]."\">";
	}

	$out .= dtcFormTableAttrs();
	$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."\">$fw";
	for($i=0;$i<$n;$i++){
		$fld = $prefix.$keys[$i];
		global $$fld;
		switch($dsc["cols"][ $keys[$i] ]["type"]){
		case "radio":
			$nb_choices = sizeof($dsc["cols"][ $keys[$i] ]["values"]);
			$control = "";
			for($j=0;$j<$nb_choices;$j++){
				if($$fld == $dsc["cols"][ $keys[$i] ]["values"][$j]){
					$selected = " checked ";
				}else{
					$selected = "";
				}
				if( isset($dsc["cols"][ $keys[$i] ]["display_replace"][$j]) ){
					$text = $dsc["cols"][ $keys[$i] ]["display_replace"][$j];
				}else{
					$text = $dsc["cols"][ $keys[$i] ]["values"][$j];
				}
				$control .= "<input type=\"radio\" name=\"".$keys[$i]."\" value=\"".$dsc["cols"][ $keys[$i] ]["values"][$j]."\" $selected> $text";
			}
			break;
		case "popup":
			$nb_choices = sizeof($dsc["cols"][ $keys[$i] ]["values"]);
			$control = "";
			for($j=0;$j<$nb_choices;$j++){
				if($$fld == $dsc["cols"][ $keys[$i] ]["values"][$j]){
					$selected = " selected ";
				}else{
					$selected = "";
				}
				if( isset($dsc["cols"][ $keys[$i] ]["display_replace"][$j]) ){
					$text = $dsc["cols"][ $keys[$i] ]["display_replace"][$j];
				}else{
					$text = $dsc["cols"][ $keys[$i] ]["values"][$j];
				}
				$control .= "<option value=\"".$dsc["cols"][ $keys[$i] ]["values"][$j]."\" $selected>$text</option>";
			}
			$control = "<select name=\"".$keys[$i]."\">".$control."</select>";
			break;
		case "text":
		default:
			if( isset($dsc["cols"][ $keys[$i] ]["size"]) ){
				$size = " size=\"".$dsc["cols"][ $keys[$i] ]["size"]."\" ";
			}else{
				$size = "";
			}
			$control = "<input $size type=\"text\" name=\"".$keys[$i]."\" value=\"".$$fld."\">";
			break;
		}
		$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$control);
	}
	$out .= dtcFromOkDraw()."</form></table>";
	return $out;
}

function drawRenewalsConfig(){
	global $conf_dtcadmin_path;
	$out = "";

	$dsc = array(
		"title" => "VPS renewal email reminders periodicity",
		"action" => "vps_renewal_period",
		"forward" => array("rub","sousrub"),
		"desc" => "These numbers represent the days before and after expiration.
Warnings before and after expiration can be listed separated by |,
while others are made of a unique value. The message templates
are stored in: ".$conf_dtcadmin_path."/reminders_msg/",
		"cols" => array(
			"vps_renewal_before" => array(
				"legend" => "Warnings before expiration:",
				"type" => "text",
				"size" => "16"),
			"vps_renewal_after" => array(
				"legend" => "Warnings after expiration:",
                                "type" => "text",
                                "size" => "16"),
			"vps_renewal_lastwarning" => array(
				"legend" => "Last Warnings:",
                                "type" => "text",
                                "size" => "16"),
			"vps_renewal_shutdown" => array(
				"legend" => "Shutdown warnings:",
                                "type" => "text",
                                "size" => "16")
			)
		);
	return configEditorTemplate ($dsc);
}

function drawSSLIPConfig(){
	global $lang;
	global $pro_mysql_ssl_ips_table;
	global $rub;
	global $sousrub;

	$out = "<h3>Manage IPs for SSL (https):</h3>";
	$out .= "<font color=\"#FF0000\">NOT AVAILABLE YET: STILL IN DEVELOPMENT</font><br><i>Take care not to add the control panel IP if you don't want to have conflicts</i><br>";

	$dsc = array(
		"table_name" => $pro_mysql_ssl_ips_table,
		"title" => "SSL dedicated IPs:",
		"action" => "ssl_ip_list",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"ip_addr" => array(
				"type" => "text",
				"legend" => "IP addr"),
			"adm_login" => array(
				"type" => "text",
				"legend" => "Admin login"),
			"available" => array(
				"type" => "radio",
				"legend" => "Available",
				"values" => array("yes","no"))
			)
		);
	$out .= dtcDatagrid($dsc);
	return $out;

}

function drawTicketConfig(){
	global $lang;
	global $pro_mysql_tik_admins_table;
	global $pro_mysql_tik_cats_table;
	global $rub;
	global $sousrub;

	$out = "<h3>Support ticket configuration</h3>";
	$dsc = array(
		"table_name" => "$pro_mysql_tik_admins_table",
		"title" => "Suport ticket administrator list:",
		"action" => "tik_admins",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"pseudo" => array(
				"type" => "text",
				"legend" => "Nick name"),
			"realname" => array(
				"type" => "text",
				"legend" => "Real name"),
			"email" => array(
				"type" => "text",
				"legend" => "Email addr"),
			"available" => array(
				"type" => "radio",
				"legend" => "Available",
				"values" => array("yes","no"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_tik_cats_table,
		"title" => "Ticket categories:",
		"action" => "tik_cats",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"catname" => array(
				"type" => "text",
				"legend" => "Nick name"),
			"catdescript" => array(
				"type" => "text",
				"size" => "50",
				"legend" => "Real name")));
	$out .= dtcDatagrid($dsc);
	return $out;
}


function drawFTPBacupConfig(){
	global $txt_yes;
	global $txt_no;
	global $lang;
	$dsc = array(
		"title" => "FTP backup configuration",
		"action" => "ftp_backup_configuration",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"ftp_backup_activate" => array(
				"legend" => "Activate FTP backups:",
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"ftp_backup_host" => array(
				"legend" => "Hostname:",
				"type" => "text",
				"size" => "30"),
			"ftp_backup_login" => array(
				"legend" => "FTP login:",
				"type" => "text",
				"size" => "30"),
			"ftp_backup_pass" => array(
				"legend" => "FTP password:",
				"type" => "text",
				"size" => "30"),
			"ftp_backup_frequency" => array(
				"legend" => "Backup frequency:",
				"type" => "popup",
				"values" => array("day","week","month"),
				"display_replace" => array("daily","weekly","monthly")),
			"ftp_backup_dest_folder" => array(
				"legend" => "Destination folder:",
				"type" => "text",
				"size" => "30")));
	return configEditorTemplate ($dsc);
}

function drawVPSServerConfig(){
	global $pro_mysql_vps_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;
	global $rub;


	$out = "<h3>VPS Server registry edition</h3>";
	$dsc = array(
		"table_name" => $pro_mysql_vps_server_table,
		"title" => "VPS Server list:",
		"action" => "vps_server_list",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"edithost" => array(
				"type" => "hyperlink",
				"legend" => "IPs addrs",
				"text" => "Edit IPs"),
			"hostname" => array(
				"type" => "text",
				"legend" => "Hostname"),
			"location" => array(
				"type" => "text",
				"legend" => "Location"),
			"soap_login" => array(
				"type" => "text",
				"size" => "10",
				"legend" => "Soap login"),
			"soap_pass" => array(
				"type" => "text",
				"size" => "10",
				"legend" => "Soam password"),
			"lvmenable" => array(
				"type" => "radio",
				"legend" => "Use LVM backend",
				"values" => array("yes","no"))));
	$vps_server_list = dtcDatagrid($dsc);
	$out .= $vps_server_list;

	if(isset($_REQUEST["edithost"])){
		$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE id='".$_REQUEST["edithost"]."';";
		$r = mysql_query($q);
		$a = mysql_fetch_array($r);
		$dsc = array(
			"table_name" => $pro_mysql_vps_ip_table,
			"title" => "VPS IPs for ".$a["hostname"].":",
			"where_condition" => "vps_server_hostname='".$a["hostname"]."'",
			"action" => "vps_server_ip_list",
			"forward" => array("rub","sousrub","edithost"),
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"vps_xen_name" => array(
					"type" => "text",
					"legend" => "VPS xen number"),
				"ip_addr" => array(
					"type" => "text",
					"legend" => "IP addr"),
				"available" => array(
					"type" => "radio",
					"legend" => "available",
					"values" => array("yes","no")))
			);
		$out .= dtcDatagrid($dsc);
	}
	return $out;
}

function drawRegistrySelection(){
	global $pro_mysql_registry_table;
	global $txt_registry_selection;
	global $lang;
	$out = "<b><u>".$txt_registry_selection[$lang]."</u></b>";
	$out .= "";
	$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
<input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
<input type=\"hidden\" name=\"action\" value=\"add_mx_trigger_backup\">
<input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
<tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"http://dtc.\"></td>";
	$out .= "<td><input type=\"text\" name=\"server_login\" value=\"\"></td>";
	$out .= "<td><input type=\"text\" name=\"server_pass\" value=\"\"></td>";
	$out .= "<td><input type=\"submit\" name=\"add\" value=\"add\"></td></tr></form>\n";
	return $out;
}

function drawGeneralConfig(){
	global $txt_cfg_general;
	global $txt_cfg_demo_version;
	global $txt_cfg_use_javascript;
	global $txt_cfg_use_ssl;
	global $txt_cfg_use_domain_based_ftp_logins;
	global $txt_cfg_session_expir_time;
	global $txt_cfg_select_type_of_skin;
	global $txt_cfg_daemon;
	global $txt_cfg_skin_chooser;
	global $txt_yes;
	global $txt_no;
	global $lang;

	global $conf_skin;
	global $dtcshared_path;
	$dir = $dtcshared_path."/gfx/skin/";

	$out = "";

	$dsc = array(
		"title" => $txt_cfg_general[$lang],
		"action" => "general_config_editor",
		"forward" => array("rub"),
		"cols" => array(
			"use_javascript" => array(
				"legend" => $txt_cfg_use_javascript[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"use_ssl" => array(
				"legend" => $txt_cfg_use_ssl[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"domain_based_ftp_logins" => array(
				"legend" => $txt_cfg_use_domain_based_ftp_logins[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"session_expir_minute" => array(
				"legend" => $txt_cfg_session_expir_time[$lang],
				"type" => "text",
				"size" => "10"),
			"selling_conditions_url" => array(
				"legend" => "Selling condition URL:",
				"type" => "text",
				"size" => "40")));
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"title" => $txt_cfg_daemon[$lang],
		"action" => "general_config_daemon",
		"forward" => array("rub"),
		"cols" => array (
			"mta_type" => array(
				"legend" => "MTA (Mail Transport Agent):",
				"type" => "radio",
				"values" => array("qmail","postfix")),
			"webalizer_country_graph" => array(
				"legend" => "Webalizer contry graph:",
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"user_mysql_type" => array(
				"legend" => "Location of user's database:",
				"type" => "radio",
				"values" => array("localhost","distant"),
				"display_replace" => array("Same as for DTC","Another location")),
			"user_mysql_host" => array(
				"legend" => "User MySQL host:",
				"type" => "text"),
			"user_mysql_root_login" => array(
				"legend" => "User MySQL root login:",
				"type" => "text"),
			"user_mysql_root_pass" => array(
				"legend" => "User MySQL root password:",
				"type" => "text")));
	$out .= configEditorTemplate ($dsc);

	// Open a known directory, and proceed to read its contents
	$skin_list = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(is_dir($dtcshared_path."/gfx/skin/".$file) && $file != "." && $file != ".."){
					$skin_list[] = $file;
				}
			}
			closedir($dh);
		}
	}
	$dsc = array(
		"title" => $txt_cfg_skin_chooser[$lang],
		"action" => "general_config_skin_chooser",
		"forward" => array("rub"),
		"cols" => array(
			"skin" => array(
				"legend" => $txt_cfg_select_type_of_skin[$lang],
				"type" => "popup",
				"values" => $skin_list)));
	$out .= configEditorTemplate ($dsc);

	return $out;
}

function drawNetworkConfig(){
	global $conf_main_site_ip;
	global $conf_site_addrs;
	global $conf_use_multiple_ip;
	global $conf_nated_vhost_ip;
	global $conf_use_nated_vhost;
	global $conf_administrative_site;

	global $txt_cfg_general;
	global $txt_cfg_use_nated_vhost;
	global $txt_cfg_nated_vhost_ip;
	global $txt_cfg_use_multiple_ip;
	global $txt_cfg_full_hostname;
	global $txt_cfg_main_site_ip;
	global $txt_cfg_site_addrs;
	global $txt_cfg_main_software_config;

	global $lang;
	global $txt_yes;
	global $txt_no;

	$dsc = array(
		"title" => $txt_cfg_main_software_config[$lang],
		"action" => "general_config_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"main_site_ip" => array(
				"legend" => $txt_cfg_main_site_ip[$lang],
				"type" => "text",
				"size" => "20"),
			"site_addrs" => array(
				"legend" => $txt_cfg_site_addrs[$lang],
				"type" => "text",
				"size" => "50"),
			"use_multiple_ip" => array(
				"legend" => $txt_cfg_use_multiple_ip[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"use_nated_vhost" => array(
				"legend" => $txt_cfg_use_nated_vhost[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"nated_vhost_ip" => array(
				"legend" => $txt_cfg_nated_vhost_ip[$lang],
				"type" => "text",
				"size" => "20"),
			"administrative_site" => array(
				"legend" => $txt_cfg_full_hostname[$lang],
				"type" => "text",
				"size" => "50")));
	return configEditorTemplate ($dsc);
}

function drawNamedConfig(){
	global $conf_addr_mail_server;
	global $conf_addr_backup_mail_server;
	global $conf_webmaster_email_addr;
	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;
	global $conf_ip_slavezone_dns_server;
	global $conf_ip_allowed_dns_transfer;
	global $conf_use_cname_for_subdomains;
	global $lang;
	global $txt_yes;
	global $txt_no;

	
	global $txt_cfg_name_zonefileconf_title;
	global $txt_cfg_main_mx_addr;
	global $txt_cfg_mail_addr_webmaster;
	global $txt_cfg_primary_dns_server_addr;
	global $txt_cfg_secondary_dns_server_addr;
	global $txt_cfg_slave_dns_ip;
	global $txt_cfg_allowed_dns_transfer_list;
	global $txt_backup_mx_servers;
	global $txt_cfg_use_cname_for_subdomains;

	$dsc = array(
		"title" => $txt_cfg_name_zonefileconf_title[$lang],
		"action" => "named_zonefile_config_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"addr_mail_server" => array(
				"legend" => $txt_cfg_main_mx_addr[$lang],
				"type" => "text",
				"size" => "40"),
			"addr_backup_mail_server" => array(
				"legend" => $txt_backup_mx_servers[$lang],
				"type" => "text",
				"size" => "50"),
			"webmaster_email_addr" => array(
				"legend" => $txt_cfg_mail_addr_webmaster[$lang],
				"type" => "text",
				"size" => "40"),
			"addr_primary_dns" => array(
				"legend" => $txt_cfg_primary_dns_server_addr[$lang],
				"type" => "text",
				"size" => "40"),
			"addr_secondary_dns" => array(
				"legend" => $txt_cfg_secondary_dns_server_addr[$lang],
				"type" => "text",
				"size" => "40"),
			"ip_slavezone_dns_server" => array(
				"legend" => $txt_cfg_slave_dns_ip[$lang],
				"type" => "text",
				"size" => "20"),
			"use_cname_for_subdomains" => array(
				"legend" => $txt_cfg_use_cname_for_subdomains[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"ip_allowed_dns_transfer" => array(
				"legend" => $txt_cfg_allowed_dns_transfer_list[$lang],
				"type" => "text",
				"size" => "50")));
	return configEditorTemplate ($dsc);
}

function drawBackupConfig(){
        global $pro_mysql_backup_table;
        global $txt_cfg_allow_following_servers_to_list;
        global $txt_cfg_make_request_to_server_for_update;
        global $txt_cfg_make_request_to_server_mx_update;
        global $txt_cfg_act_as_backup_mail_server;
        global $txt_cfg_act_as_backup_dns_server;
        global $lang;
        global $txt_cmenu_password;
	global $txt_action;
	global $txt_domain_tbl_config_ip;
        global $txt_cfg_server_address;		


	$out = "";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => $txt_cfg_allow_following_servers_to_list[$lang],
		"action" => "backup_grant_access_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='grant_access'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"legend" => $txt_domain_tbl_config_ip[$lang]),
			"server_login" => array(
				"type" => "text",
				"legend" => "Login"),
			"server_pass" => array(
				"type" => "text",
				"legend" => $txt_cmenu_password[$lang])
			)
		);
	$out .= dtcDatagrid($dsc);

	$out .= "<hr width=\"100%\">";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => $txt_cfg_make_request_to_server_for_update[$lang],
		"action" => "trigger_dns_update_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='trigger_changes'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "50",
				"legend" => "Server address"),
			"server_login" => array(
				"type" => "text",
				"legend" => "Login"),
			"server_pass" => array(
				"type" => "text",
				"legend" => $txt_cmenu_password[$lang])
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => $txt_cfg_make_request_to_server_mx_update[$lang],
		"action" => "trigger_mx_update_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='trigger_mx_changes'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "50",
				"legend" => "Server address"),
			"server_login" => array(
				"type" => "text",
				"legend" => "Login"),
			"server_pass" => array(
				"type" => "text",
				"legend" => $txt_cmenu_password[$lang])
			)
		);
	$out .= dtcDatagrid($dsc);

	$out .= "<hr width=\"100%\">";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => $txt_cfg_act_as_backup_mail_server[$lang],
		"action" => "trigger_mx_update_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='mail_backup'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "50",
				"legend" => "Server address"),
			"server_login" => array(
				"type" => "text",
				"legend" => "Login"),
			"server_pass" => array(
				"type" => "text",
				"legend" => $txt_cmenu_password[$lang])
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => $txt_cfg_act_as_backup_dns_server[$lang],
		"action" => "trigger_mx_update_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='dns_backup'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "50",
				"legend" => "Server address"),
			"server_login" => array(
				"type" => "text",
				"legend" => "Login"),
			"server_pass" => array(
				"type" => "text",
				"legend" => $txt_cmenu_password[$lang])
			)
		);
	$out .= dtcDatagrid($dsc);

	return $out;
}

function drawRegistryApiConfig(){
	global $lang;
	global $txt_yes;
	global $txt_no;

	global $pro_mysql_config_table;

	global $txt_cfg_registry_api_title;
	global $txt_cfg_use_test_or_live;
	global $txt_cfg_tucows_username;
	global $txt_cfg_tucows_live_server_key;
	global $txt_cfg_tucows_test_server_key;
	global $txt_cfg_use_des_or_blowfish;

	global $conf_srs_user;
	global $conf_srs_live_key;
	global $conf_srs_test_key;
	global $conf_srs_crypt;
	global $conf_srs_enviro;
	global $conf_use_registrar_api;

	$dsc = array(
		"title" => $txt_cfg_registry_api_title[$lang],
		"action" => "domain_registry_config_editor",
		"forward" => array("rub","sousrub"),
		"desc" => "<img src=\"gfx/tucows.jpg\"><br>Note: you must have a Tucows reseller account.",
		"cols" => array(
			"use_registrar_api" => array(
				"legend" => "Use registrar API:",
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"srs_crypt" => array(
				"legend" => $txt_cfg_use_des_or_blowfish[$lang],
				"type" => "radio",
				"values" => array("DES","BLOWFISH")),
			"srs_enviro" => array(
				"legend" => $txt_cfg_use_test_or_live[$lang],
				"type" => "radio",
				"values" => array("LIVE","TEST")),
			"srs_user" => array(
				"legend" => $txt_cfg_tucows_username[$lang],
				"type" => "text",
				"size" => "30"),
			"srs_test_key" => array(
				"legend" => $txt_cfg_tucows_test_server_key[$lang],
				"type" => "text",
				"size" => "50"),
			"srs_live_key" => array(
				"legend" => $txt_cfg_tucows_live_server_key[$lang],
				"type" => "text",
				"size" => "50")));
	return configEditorTemplate ($dsc);
}

function drawDTCpayConfig(){
	global $lang;
  	global $txt_yes;
  	global $txt_no;
	global $txt_currency;
	global $txt_currency_symbol;
	global $txt_currency_ltr;
	
	global $txt_cfg_use_paypal;
	global $txt_cfg_paytitle;
	global $txt_cfg_paypal_email;
	global $txt_cfg_paypal_ratefee;
	global $txt_cfg_paypal_flatfee;
	global $txt_cfg_paypal_autovalid;
	global $txt_cfg_paypal_sandbox_email;
	global $txt_cfg_paypal_use_sandbox;

	global $pro_mysql_secpayconf_table;

	$out = "";

	$dsc = array(
		"title" => $txt_cfg_paytitle[$lang],
		"action" => "payment_gateway_currency_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"currency_symbol" => array(
				"legend" => $txt_currency_symbol[$lang],
				"type" => "text",
				"size" => "6"),
			"currency_letters" => array(
				"legend" => $txt_currency_ltr[$lang],
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => "PayPal:",
		"action" => "payment_gateway_paypal_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_paypal" => array(
				"legend" => $txt_cfg_use_paypal[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"paypal_autovalidate" => array(
				"legend" => $txt_cfg_paypal_autovalid[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"paypal_email" => array(
				"legend" => $txt_cfg_paypal_email[$lang],
				"type" => "text",
				"size" => "30"),
			"paypal_rate" => array(
				"legend" => $txt_cfg_paypal_ratefee[$lang],
				"type" => "text",
				"size" => "6"),
			"paypal_flat" => array(
				"legend" => $txt_cfg_paypal_flatfee[$lang],
				"type" => "text",
				"size" => "6"),
			"paypal_sandbox" => array(
				"legend" => $txt_cfg_paypal_use_sandbox[$lang],
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"paypal_sandbox_email" => array(
				"legend" => $txt_cfg_paypal_sandbox_email[$lang],
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => "eNETS:",
		"action" => "payment_gateway_enets_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_enets" => array(
				"legend" => "Use eNETS:",
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang])),
			"use_enets_test" => array(
				"legend" => "eNETS server:",
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array("Test server","Production server")),
			"enets_mid_id" => array(
				"legend" => "eNETS mid:",
				"type" => "text",
				"size" => "6"),
			"enets_test_mid_id" => array(
				"legend" => "eNETS test mid:",
				"type" => "text",
				"size" => "6"),
			"enets_rate" => array(
				"legend" => "eNETS rate:",
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	return $out;
}

function drawDTCradiusConfig(){
	global $conf_dtcshared_path;
	global $lang;

	$out = "";
	$dsc = array(
		"title" => "NAS config",
		"new_item_title" => "Add new NAS:",
		"new_item_link" => "Add new NAS",
		"edit_item_title" => "Edit a NAS:",
		"table_name" => "nas",
		"action" => "nas_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "nasname",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"nasname" => array(
				"type" => "text",
				"disable_edit" => "yes",
				"legend" => "Name:"),
			"shortname" => array(
				"type" => "text",
				"legend" => "Short name:"),
			"type" => array(
				"type" => "popup",
				"legend" => "Type:",
				"values" => array("cisco","computone","livingston","max40xx","multitech","netserver","pathras","patton","portslave","tc","usrhiper","other")),
			"ports" => array(
				"type" => "text",
				"legend" => "Port number:",
				"check" => "number"),
			"secret" => array(
				"type" => "password",
				"legend" => "Secret:",
				"check" => "dtc_pass"),
			"community" => array(
				"type" => "text",
				"legend" => "SNMP community:"),
			"description" => array(
				"type" => "text",
				"legend" => "Description:")));
	$out .= dtcListItemsEdit($dsc);
	return $out;
}

function drawDTCpathConfig(){
	global $conf_dtcshared_path;
	global $conf_site_root_host_path;
	global $conf_generated_file_path;

	global $conf_qmail_rcpthost_path;
	global $conf_qmail_virtualdomains_path;
	global $conf_qmail_assign_path;
	global $conf_qmail_poppasswd_path;

	global $conf_apache_vhost_path;
	global $conf_php_additional_library_path;
	global $conf_php_library_path;

	global $conf_named_path;
	global $conf_named_slavefile_path;
	global $conf_named_slavezonefiles_path;
	global $conf_named_zonefiles_path;

	global $conf_backup_script_path;
	global $conf_bakcup_path;
	global $conf_webalizer_stats_script_path;

	global $conf_chroot_path;

	global $lang;

	global $txt_cfg_path_conf_title;
	global $txt_cfg_mainpath_conf_title;
	global $txt_cfg_dtc_shared_folder;
	global $txt_cfg_new_account_defaultpath;
	global $txt_cfg_generated_file_path;
	global $txt_cfg_new_chroot_path_path;

	$out = "";

	$dsc = array(
		"title" => $txt_cfg_path_conf_title[$lang],
		"action" => "main_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"dtcshared_path" => array(
				"legend" => $txt_cfg_dtc_shared_folder[$lang],
				"type" => "text"),
			"site_root_host_path" => array(
				"legend" => $txt_cfg_new_account_defaultpath[$lang],
				"type" => "text"),
			"chroot_path" => array(
				"legend" => $txt_cfg_new_chroot_path_path[$lang],
				"type" => "text"),
			"generated_file_path" => array(
				"legend" => $txt_cfg_generated_file_path[$lang],
				"type" => "text")));
	$out .= configEditorTemplate ($dsc);


	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_mail.gif\">Qmail path:",
		"action" => "qmail_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"qmail_rcpthost_path" => array(
				"legend" => "rcpthosts:",
				"type" => "text",
				"size" => "26"),
			"qmail_virtualdomains_path" => array(
				"legend" => "eNETS test mid:",
				"type" => "text",
				"size" => "26"),
			"qmail_assign_path" => array(
				"legend" => "eNETS test mid:",
				"type" => "text",
				"size" => "26"),
			"qmail_poppasswd_path" => array(
				"legend" => "eNETS rate:",
				"type" => "text",
				"size" => "26")));
	$out .= configEditorTemplate ($dsc);

	global $txt_cfg_apache_file_names;
	global $txt_cfg_vhost_file_path;
	global $txt_cfg_phplib_path;
	global $txt_cfg_phplib2_path;
	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_web.gif\">".$txt_cfg_apache_file_names[$lang],
		"action" => "apache_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"apache_vhost_path" => array(
				"legend" => $txt_cfg_vhost_file_path[$lang],
				"type" => "text",
				"size" => "20"),
			"php_library_path" => array(
				"legend" => $txt_cfg_phplib_path[$lang],
				"type" => "text",
				"size" => "60"),
			"php_additional_library_path" => array(
				"legend" => $txt_cfg_phplib2_path[$lang],
				"type" => "text",
				"size" => "60")));
	$out .= configEditorTemplate ($dsc);

	global $txt_cfg_named_filenames_title;
	global $txt_cfg_named_main_file;
	global $txt_cfg_named_slave_file;
	global $txt_cfg_named_main_zonefile;
	global $txt_cfg_named_cache_slave_zonefile;
	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_named.gif\"> ".$txt_cfg_named_filenames_title[$lang],
		"action" => "named_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"named_path" => array(
				"legend" => $txt_cfg_named_main_file[$lang],
				"type" => "text",
				"size" => "30"),
			"named_slavefile_path" => array(
				"legend" => $txt_cfg_named_slave_file[$lang],
				"type" => "text",
				"size" => "30"),
			"named_zonefiles_path" => array(
				"legend" => $txt_cfg_named_main_zonefile[$lang],
				"type" => "text",
				"size" => "30")));
	$out .= configEditorTemplate ($dsc);

	return $out;
}

function drawCompaniesConfig(){
	global $pro_mysql_companies_table;
	global $cc_code_array;
	global $conf_generated_file_path;

	$out = "";

	$country_codes = array_keys($cc_code_array);
	$country_fullnames = array_values($cc_code_array);
	$dsc = array(
		"title" => "List of your companies:",
		"new_item_title" => "Add a new company:",
		"new_item_link" => "Add a new company",
		"edit_item_title" => "Edit a company:",
		"table_name" => $pro_mysql_companies_table,
		"action" => "hosting_company_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "name",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"name" => array(
				"type" => "text",
				"size" => "30",
				"legend" => "Name:"),
			"address" => array(
				"type" => "textarea",
				"cols" => "70",
				"rows" => "5",
				"legend" => "Address:"),
			"country" => array(
				"type" => "popup",
				"legend" => "Country:",
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"registration_number" => array(
				"type" => "text",
				"size" => "30",
				"legend" => "Registration number:"),
			"vat_number" => array(
				"type" => "text",
				"size" => "30",
				"legend" => "VAT number:"),
			"vat_rate" => array(
				"type" => "text",
				"size" => "10",
				"legend" => "VAT rate:"),
			"logo_path" => array(
				"type" => "text",
                                "size" => "30",
                                "legend" => "Logo path relative to<br>$conf_generated_file_path/invoice_pics/:"),
			"text_after" => array(
				"type" => "textarea",
				"cols" => "70",
				"rows" => "5",
				"legend" => "Invoice free text:"),
			"footer" => array(
				"type" => "textarea",
				"cols" => "70",
				"rows" => "5",
				"legend" => "Invoice footer:")));
	$out .= dtcListItemsEdit($dsc);
	return $out;
}

function drawInvoicingConfig(){
	global $pro_mysql_companies_table;
	global $pro_mysql_invoicing_table;
	global $cc_code_array;
	$out = "";

	$q = "SELECT * FROM $pro_mysql_companies_table WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$comp_names = array("Please select");
	$comp_ids = array(0);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$comp_names[] = $a["name"];
		$comp_ids[] = $a["id"];
	}

	$dsc = array(
		"title" => "Default company invoicing:",
		"action" => "default_company_invoicing_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"default_company_invoicing" => array(
				"legend" => "Default company invoicing:",
				"type" => "popup",
				"values" => $comp_ids,
				"display_replace" => $comp_names)));
	$out .= configEditorTemplate ($dsc);

	$country_codes = array_keys($cc_code_array);
	$country_fullnames = array_values($cc_code_array);
	$country_codes = array_reverse($country_codes);
	$country_fullnames = array_reverse($country_fullnames);
	$country_codes[] = "00";
	$country_fullnames[] = "none";
	$country_codes = array_reverse($country_codes);
	$country_fullnames = array_reverse($country_fullnames);

	$dsc = array(
		"table_name" => $pro_mysql_invoicing_table,
		"title" => "Customer and service country vs company:",
		"action" => "cust_and_serv_country_vs_comp",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"customer_country_code" => array(
				"type" => "popup",
				"legend" => "Customer country",
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"service_country_code" => array(
				"type" => "popup",
				"legend" => "Service country",
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"company_id" => array(
				"type" => "popup",
				"legend" => "Company name",
				"values" => $comp_ids,
				"display_replace" => $comp_names)
			)
		);
	$out .= dtcDatagrid($dsc);

	return $out;
}

function drawDTCConfigMenu(){
	global $txt_cfg_path_conf_title;
	global $txt_cfg_name_zonefileconf_title;
	global $txt_cfg_payconf_title;
	global $lang;

	global $txt_cfg_general_menu_entry;
	global $txt_cfg_ip_and_network;
	global $txt_cfg_backup_and_mx_menu_entry;
	global $txt_cfg_registryapi_menu_entry;
	

	if(!isset($_REQUEST["sousrub"])){
		$sousrub = "general";
        }else{
          $sousrub = $_REQUEST["sousrub"];
        }

	$out = "<br><table><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "general")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config\">";
	$out .= $txt_cfg_general_menu_entry[$lang];
	if($sousrub != "general")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "ip")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=ip\">";
	$out .= $txt_cfg_ip_and_network[$lang];
	if($sousrub != "ip")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "sslip")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=sslip\">";
	$out .= "SSL IPs";
	if($sousrub != "sslip")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "zonefile")
		$out .= " <a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=zonefile\">";
	$out .= $txt_cfg_name_zonefileconf_title[$lang];
	if($sousrub != "zonefile")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "backup")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=backup\">";
	$out .= $txt_cfg_backup_and_mx_menu_entry[$lang];
	if($sousrub != "backup")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "registryapi")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=registryapi\">";
	$out .=  $txt_cfg_registryapi_menu_entry[$lang];
	if($sousrub != "registryapi")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "payconf")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=payconf\">";
	$out .=  $txt_cfg_payconf_title[$lang];
	if($sousrub != "payconf")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "companies")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=companies\">";
	$out .=  "Companies";
	if($sousrub != "companies")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "invoicing")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=invoicing\">";
	$out .=  "Invoicing";
	if($sousrub != "invoicing")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "radius")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=radius\">";
	$out .=  "radius";
	if($sousrub != "radius")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "ftpbackup")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=ftpbackup\">";
	$out .=  "ftpbackup";
	if($sousrub != "ftpbackup")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "path")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=path\">";
	$out .= $txt_cfg_path_conf_title[$lang];
	if($sousrub != "path")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "renewals")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=renewals\">";
	$out .= "Renewals";
	if($sousrub != "renewals")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "ticket")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=ticket\">";
	$out .= "Support tickets";
	if($sousrub != "ticket")
		$out .= "</a>";
	$out .= "</td></tr><tr><td style=\"white-space:nowrap\" nowrap>";
	if($sousrub != "vps")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=vps\">";
	$out .= "VPS servers";
	if($sousrub != "vps")
		$out .= "</a>";

	$out .= "</td></tr></table>";
	return $out;
}

function drawDTCConfigForm(){
	if(!isset($_REQUEST["sousrub"])){
		$sousrub = "general";
	}else{
		$sousrub = $_REQUEST["sousrub"];
	}

	switch($sousrub){
	default:
	case "general":
		return drawGeneralConfig();
	case "ip":
		return drawNetworkConfig();
	case "sslip":
		return drawSSLIPConfig();
	case "zonefile":
		return drawNamedConfig();
        case "backup":
                return drawBackupConfig();
        case "registryapi":
		return drawRegistryApiConfig();
        case "payconf":
                return drawDTCpayConfig();
	case "companies":
		return drawCompaniesConfig();
	case "invoicing":
		return drawInvoicingConfig();
        case "radius":
                return drawDTCradiusConfig();
        case "ftpbackup":
		return drawFTPBacupConfig();
	case "path":
		return drawDTCpathConfig();
	case "ticket":
		return drawTicketConfig();
        case "renewals":
		return drawRenewalsConfig();
        case "vps":
		return drawVPSServerConfig();
	}
}

?>