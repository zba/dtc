<?php
/**
 * @package DTC
 * @version $Id: dtc_config.php,v 1.78 2007/01/19 07:20:22 thomas Exp $
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
	$out .= dtcFromOkDraw();
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
				"size" => "6"),
			"vps_renewal_after" => array(
				"legend" => "Warnings after expiration:",
                                "type" => "text",
                                "size" => "6"),
			"vps_renewal_lastwarning" => array(
				"legend" => "Last Warnings:",
                                "type" => "text",
                                "size" => "6"),
			"vps_renewal_shutdown" => array(
				"legend" => "Shutdown warnings:",
                                "type" => "text",
                                "size" => "6")
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
				"display_replace" => array($txt_yes[$lang],$txt_no[$lang]))));
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
		
	$out = "<h3>".$txt_cfg_allow_following_servers_to_list[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='grant_access';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>".$txt_domain_tbl_config_ip[$lang]."</td><td>Login</td><td>".$txt_cmenu_password[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
	for($i=0;$i<$n;$i++){
	        $a = mysql_fetch_array($r);
	        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
	        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
	        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
	        <input type=\"hidden\" name=\"action\" value=\"modify_grant_backup\">
                <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
                <input type=\"hidden\" name=\"id\" value=\"".$a["id"]."\">
	        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"".$a["server_addr"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"".$a["server_login"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"".$a["server_pass"]."\"></td>";
	        $out .= "<td><input type=\"submit\" name=\"todo\" value=\"save\"><input type=\"submit\" name=\"todo\" value=\"del\"></td></tr></form>\n";
        }
        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
        <input type=\"hidden\" name=\"action\" value=\"add_grant_backup\">
        <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"\"></td>";
        $out .= "<td><input type=\"submit\" name=\"add\" value=\"add\"></td></tr></form>\n";
        $out .= "</table>";

	//list of servers to update for domain or mail changes
	$out .= "<h3>".$txt_cfg_make_request_to_server_for_update[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='trigger_changes';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>".$txt_cfg_server_address[$lang]."</td><td>Login</td><td>".$txt_cmenu_password[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
	for($i=0;$i<$n;$i++){
	        $a = mysql_fetch_array($r);
	        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
	        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
	        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
	        <input type=\"hidden\" name=\"action\" value=\"modify_trigger_backup\">
                <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
                <input type=\"hidden\" name=\"id\" value=\"".$a["id"]."\">
	        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"".$a["server_addr"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"".$a["server_login"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"".$a["server_pass"]."\"></td>";
	        $out .= "<td><input type=\"submit\" name=\"todo\" value=\"save\"><input type=\"submit\" name=\"todo\" value=\"del\"></td></tr></form>\n";
        }
        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
        <input type=\"hidden\" name=\"action\" value=\"add_trigger_backup\">
        <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"http://dtc.\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"\"></td>";
        $out .= "<td><input type=\"submit\" name=\"add\" value=\"add\"></td></tr></form>\n";
        $out .= "</table>";

        $this_srv_backup = $out;
        $out = "";

	//servers to trigger when there are MX recipient changes (for backup MX, though not backup NS)
	$out .= "<h3>".$txt_cfg_make_request_to_server_mx_update[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='trigger_mx_changes';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>".$txt_cfg_server_address[$lang]."</td><td>Login</td><td>".$txt_cmenu_password[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
	for($i=0;$i<$n;$i++){
	        $a = mysql_fetch_array($r);
	        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
	        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
	        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
	        <input type=\"hidden\" name=\"action\" value=\"modify_mx_trigger_backup\">
                <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
                <input type=\"hidden\" name=\"id\" value=\"".$a["id"]."\">
	        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"".$a["server_addr"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"".$a["server_login"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"".$a["server_pass"]."\"></td>";
	        $out .= "<td><input type=\"submit\" name=\"todo\" value=\"save\"><input type=\"submit\" name=\"todo\" value=\"del\"></td></tr></form>\n";
        }
        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
        <input type=\"hidden\" name=\"action\" value=\"add_mx_trigger_backup\">
        <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"http://dtc.\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"\"></td>";
        $out .= "<td><input type=\"submit\" name=\"add\" value=\"add\"></td></tr></form>\n";
        $out .= "</table>";
	//append this to the server backup list
        $this_srv_backup .= $out;
        $out = "";

	//list of servers to backup the email
	$out .= "<h3>".$txt_cfg_act_as_backup_mail_server[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='mail_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>Server hostname</td><td>Login</td><td>".$txt_cmenu_password[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
	for($i=0;$i<$n;$i++){
	        $a = mysql_fetch_array($r);
	        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
	        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
	        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
	        <input type=\"hidden\" name=\"action\" value=\"modify_mail_backup\">
                <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
                <input type=\"hidden\" name=\"id\" value=\"".$a["id"]."\">
	        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"".$a["server_addr"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"".$a["server_login"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"".$a["server_pass"]."\"></td>";
	        $out .= "<td><input type=\"submit\" name=\"todo\" value=\"save\"><input type=\"submit\" name=\"todo\" value=\"del\"></td></tr></form>\n";
        }
        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
        <input type=\"hidden\" name=\"action\" value=\"add_mail_backup\">
        <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"http://dtc.\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"\"></td>";
        $out .= "<td><input type=\"submit\" name=\"add\" value=\"add\"></td></tr></form>\n";
        $out .= "</table>";

	$out .= "<h3>".$txt_cfg_act_as_backup_dns_server[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='dns_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>Server address</td><td>Login</td><td>".$txt_cmenu_password[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
	for($i=0;$i<$n;$i++){
	        $a = mysql_fetch_array($r);
	        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
	        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
	        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
	        <input type=\"hidden\" name=\"action\" value=\"modify_dns_backup\">
                <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
                <input type=\"hidden\" name=\"id\" value=\"".$a["id"]."\">
	        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"".$a["server_addr"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"".$a["server_login"]."\"></td>";
	        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"".$a["server_pass"]."\"></td>";
	        $out .= "<td><input type=\"submit\" name=\"todo\" value=\"save\"><input type=\"submit\" name=\"todo\" value=\"del\"></td></tr></form>\n";
        }
        $out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
        <input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
        <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
        <input type=\"hidden\" name=\"action\" value=\"add_dns_backup\">
        <input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
        <tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"http://dtc.\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_login\" value=\"\"></td>";
        $out .= "<td><input type=\"text\" name=\"server_pass\" value=\"\"></td>";
        $out .= "<td><input type=\"submit\" name=\"add\" value=\"add\"></td></tr></form>\n";
        $out .= "</table>";

        $other_server_backup = $out;
        $out = skin("frame",$this_srv_backup,"This server backup");
        $out .= skin("frame",$other_server_backup,"This server backup");

//	$out .= "<h3>Backup all files to this ftp server each weeks:</h3>";
        return $out;
}

function drawRegistryApiConfig(){
	global $lang;
// seeb ...
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
// seeb ...
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

	$out = "<h3>NAS config</h3>";
	// Nass server list:
	$out .= "<b><u>Your NAS server list:</u></b><br>";  
	$q = "SELECT * FROM nas";
	$r = mysql_query($q)or die("Cannot query : \"$q\" ! line: ".__LINE__." file: ".__file__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($i != 0)
			$out .= " - ";
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&nas_id=".$a["id"]."\">".$a["nasname"]."</a>";
	}

	$out .= "<br><br>";

	if(!isset($_REQUEST["nas_id"]) || $_REQUEST["nas_id"] != "new"){
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&nas_id=new\">Add a new NAS</a><br><br>\n\n";
	}
	// NAS properties editor:
	if(isset($_REQUEST["nas_id"])){
		$hidden = "<input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
<input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">";
		if($_REQUEST["nas_id"] == "new"){
			$hidden .= "<input type=\"hidden\" name=\"nas_id\" value=\"new\">
<input type=\"hidden\" name=\"action\" value=\"add_new_nas\">";
			$out .= "<b><u>New NAS properties:</u></b><br>\n";
			$ed_nas_name = "";
			$ed_nas_short_name = "";
			$ed_nas_type = "cisco";
			$ed_nas_port = "";
			$ed_nas_secret = "";
			$ed_nas_community = "";
			$ed_nas_description = "";
		}else{
			$hidden .= "<input type=\"hidden\" name=\"nas_id\" value=\"".$_REQUEST["nas_id"]."\">
			<input type=\"hidden\" name=\"action\" value=\"edit_nas\">";
			$out .= "<b><u>Edit NAS properties:</u></b><br>\n";
			$q = "SELECT * FROM nas WHERE id='".$_REQUEST["nas_id"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$a = mysql_fetch_array($r);
			$ed_nas_name = $a["nasname"];
			$ed_nas_short_name = $a["shortname"];
			$ed_nas_type = $a["type"];
			$ed_nas_port = $a["ports"];
			$ed_nas_secret = $a["secret"];
			$ed_nas_community = $a["community"];
			$ed_nas_description = $a["description"];
		}

		$nastype_cisco_sel = " ";
		$nastype_computone_sel = " ";
		$nastype_livingston_sel = " ";
		$nastype_max40xx_sel = " ";
		$nastype_multitech_sel = " ";
		$nastype_netserver_sel = " ";
		$nastype_pathras_sel = " ";
		$nastype_patton_sel = " ";
		$nastype_portslave_sel = " ";
		$nastype_tc_sel = " ";
		$nastype_usrhiper_sel = " ";
		$nastype_other_sel = " ";

		switch($ed_nas_type){
		default:
		case "cisco":
			$nastype_cisco_sel = " selected ";
			break;
		case "computone":
			$nastype_computone_sel = " selected ";
			break;
		case "livingston":
			$nastype_livingston_sel = " selected ";
			break;
		case "max40xx":
			$nastype_max40xx_sel = " selected ";
			break;
		case "multitech":
			$nastype_multitech_sel = " selected ";
			break;
		case "netserver":
			$nastype_netserver_sel = " selected ";
			break;
		case "pathras":
			$nastype_pathras_sel = " selected ";
			break;
		case "patton":
			$nastype_patton_sel = " selected ";
			break;
		case "portslave":
			$nastype_portslave_sel = " selected ";
			break;
		case "tc":
			$nastype_tc_sel = " selected ";
			break;
		case "usrhiper":
			$nastype_usrhiper_sel = " selected ";
			break;
		case "other":
			$nastype_other_sel = " selected ";
			break;
		}

		$out .="<table with=\"100%\" height=\"1\">
<tr>
  <td align=\"right\" nowrap>Name:</td>
  <td width=\"100%\"><form action=\"".$_SERVER["PHP_SELF"]."\">$hidden<input type=\"text\" value=\"$ed_nas_name\" name=\"nas_name\"></td>
</tr><tr>
  <td align=\"right\" nowrap>short name:</td>
  <td width=\"100%\"><input type=\"text\" value=\"$ed_nas_short_name\" name=\"nas_short_name\"></td>
</tr><tr>
  <td align=\"right\" nowrap>Type:</td>
  <td width=\"100%\"><select name=\"nas_type\"><option value=\"cisco\"$nastype_cisco_sel>cisco</option>
<option value=\"computone\"$nastype_computone_sel>computone</option>
<option value=\"livingston\"$nastype_livingston_sel>livingston</option>
<option value=\"max40xx\"$nastype_max40xx_sel>max40xx</option>
<option value=\"multitech\"$nastype_multitech_sel>multitech</option>
<option value=\"netserver\"$nastype_netserver_sel>netserver</option>
<option value=\"pathras\"$nastype_pathras_sel>pathras</option>
<option value=\"patton\"$nastype_patton_sel>patton</option>
<option value=\"portslave\"$nastype_portslave_sel>portslave</option>
<option value=\"tc\"$nastype_tc_sel>tc</option>
<option value=\"usrhiper\"$nastype_usrhiper_sel>usrhiper</option>
<option value=\"other\"$nastype_other_sel>other</option>
</select</td>
</tr><tr>
  <td align=\"right\" nowrap>Port number:</td>
  <td width=\"100%\"><input type=\"text\" size =\"8\" value=\"$ed_nas_port\" name=\"nas_port_num\"></td>
</tr><tr>
  <td align=\"right\" nowrap>Secret</td>
  <td width=\"100%\"><input type=\"text\" size =\"10\" value=\"$ed_nas_secret\" name=\"nas_secret\"></td>
</tr><tr>
  <td align=\"right\" nowrap>SNMP community</td>
  <td width=\"100%\"><input type=\"text\" size =\"20\" value=\"$ed_nas_community\" name=\"nas_snmp_com\"></td>
</tr><tr>
  <td align=\"right\" nowrap>Description</td>
  <td width=\"100%\"><input type=\"text\" size =\"20\" value=\"$ed_nas_description\" name=\"nas_description\"></td>
</tr><tr>
  <td></td>
  <td><input type=\"submit\" name=\"install_new_config_values\" value=\"Ok\"></td>
</tr>
</table>
";
	}

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

	$qmailPath = "<h3><img src=\"gfx/dtc/generate_mail.gif\"> Qmail path</h3>
<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	rcpthosts:</td><td width=\"100%\"><input type=\"text\" size =\"40\" value=\"$conf_qmail_rcpthost_path\" name=\"new_qmail_rcpthost_path\">
</td></tr><tr><td align=\"right\">
	virtualdomains:</td><td><input type=\"text\" size =\"40\" value=\"$conf_qmail_virtualdomains_path\" name=\"new_qmail_virtualdomains_path\">
</td></tr><tr><td align=\"right\">
	assign:</td><td><input type=\"text\" size =\"40\" value=\"$conf_qmail_assign_path\" name=\"new_qmail_assign_path\">
</td></tr><tr><td align=\"right\">
	poppasswd:</td><td><input type=\"text\" size =\"40\" value=\"$conf_qmail_poppasswd_path\" name=\"new_qmail_poppasswd_path\">
</td></tr></table>";

	global $txt_cfg_apache_file_names;
	global $txt_cfg_vhost_file_path;
	global $txt_cfg_phplib_path;
	global $txt_cfg_phplib2_path;
	$apachePath = "<h3><img src=\"gfx/dtc/generate_web.gif\">".$txt_cfg_apache_file_names[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_vhost_file_path[$lang]."</td><td width=\"100%\"><input type=\"text\" size =\"60\" value=\"$conf_apache_vhost_path\" name=\"new_apache_vhost_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_phplib_path[$lang]."</td><td><input type=\"text\" size =\"60\" value=\"$conf_php_library_path\" name=\"new_php_library_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_phplib2_path[$lang]."</td><td><input type=\"text\" size =\"60\" value=\"$conf_php_additional_library_path\" name=\"new_php_additional_library_path\"><br>
</td></tr></table>";

	global $txt_cfg_named_filenames_title;
	global $txt_cfg_named_main_file;
	global $txt_cfg_named_slave_file;
	global $txt_cfg_named_main_zonefile;
	global $txt_cfg_named_cache_slave_zonefile;
	$namedPath = "<h3><img src=\"gfx/dtc/generate_named.gif\"> ".$txt_cfg_named_filenames_title[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_named_main_file[$lang]."<input type=\"text\" size =\"40\" value=\"$conf_named_path\" name=\"new_named_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_named_slave_file[$lang]."<input type=\"text\" size =\"40\" value=\"$conf_named_slavefile_path\" name=\"new_named_slavefile_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_named_main_zonefile[$lang]."<input type=\"text\" size =\"40\" value=\"$conf_named_zonefiles_path\" name=\"new_named_zonefiles_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_named_cache_slave_zonefile[$lang]."<input type=\"text\" size =\"40\" value=\"$conf_named_slavezonefiles_path\" name=\"new_named_slavezonefiles_path\"><br>
</td></tr></table>";

	global $txt_cfg_backup_webalizer_title;
	global $txt_cfg_backup_script_filename;
	global $txt_cfg_backup_destination_folder;
	global $txt_cfg_webalizer_script_filename;
	$webalizerAndBackupPath = "<h3><img src=\"gfx/dtc/generate_stats.gif\"> ".$txt_cfg_backup_webalizer_title[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_backup_script_filename[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_backup_script_path\" name=\"new_backup_script_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_backup_destination_folder[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_bakcup_path\" name=\"new_bakcup_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_webalizer_script_filename[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_webalizer_stats_script_path\" name=\"new_webalizer_stats_script_path\">
</td></tr></table>";


	$out .= "<h2>".$txt_cfg_path_conf_title[$lang]."</h2>
<h3>".$txt_cfg_mainpath_conf_title[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr>
	<td align=\"right\" nowrap>".$txt_cfg_dtc_shared_folder[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_dtcshared_path\" name=\"new_dtcshared_path\"></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_new_account_defaultpath[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_site_root_host_path\" name=\"new_site_root_host_path\"></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_new_chroot_path_path[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_chroot_path\" name=\"new_chroot_path\"></td>
</tr></table>
".$txt_cfg_generated_file_path[$lang]."<br>
<input type=\"text\" size =\"60\" value=\"$conf_generated_file_path\" name=\"new_generated_file_path\"><br><br>

<table width=\"100%\"><tr><td>$qmailPath</td></tr>
<tr><td>$apachePath</td></tr>
<tr><td>$namedPath</td></tr>
<tr><td>$webalizerAndBackupPath</td></tr></table>";
	return $out;
}

function drawDTCConfigForm(){

	global $lang;

	if(!isset($_REQUEST["sousrub"]))
		$sousrub = "general";
        else
	  $sousrub = $_REQUEST["sousrub"];

	switch($sousrub){
	case "general":
		return drawGeneralConfig();
		break;
	case "ip":
		return drawNetworkConfig();
		break;
	case "sslip":
		return drawSSLIPConfig();
		break;
	case "zonefile":
		return drawNamedConfig();
		break;
        case "backup":
                return "<form action=\"".$_SERVER["PHP_SELF"]."\"><input type=\"hidden\" name=\"rub\" value=\"config\">
<input type=\"hidden\" name=\"sousrub\" value=\"$sousrub\">".drawBackupConfig()."</form>";
		$global_conf = drawBackupConfig();
                break;
        case "registryapi":
		return drawRegistryApiConfig();
		break;
        case "payconf":
                return drawDTCpayConfig();
                break;
        case "radius":
                return drawDTCradiusConfig();
                break;
        case "ftpbackup":
		return drawFTPBacupConfig();
		break;
	case "path":
		$global_conf = drawDTCpathConfig();
		break;
	case "ticket":
		return drawTicketConfig();
        case "renewals":
          return drawRenewalsConfig();
          break;
        case "vps":
          return drawVPSServerConfig();
	}

	return "<form action=\"".$_SERVER["PHP_SELF"]."\"><input type=\"hidden\" name=\"rub\" value=\"config\">
<input type=\"hidden\" name=\"sousrub\" value=\"$sousrub\">$global_conf
	<center><input type=\"submit\" name=\"install_new_config_values\" value=\"Ok\"></center>
	</form>";
}

function saveDTCConfigInMysql(){
	global $pro_mysql_cronjob_table;
        global $pro_mysql_backup_table;
        global $pro_mysql_secpayconf_table;

	global $new_demo_version;
	global $new_main_site_ip;
	global $new_site_addrs;
	global $new_use_multiple_ip;
	global $new_use_cname_for_subdomains;
	global $new_use_javascript;
	global $new_use_ssl;

	global $new_addr_mail_server;
	global $new_webmaster_email_addr;
	global $new_addr_primary_dns;
	global $new_addr_secondary_dns;
	global $new_ip_slavezone_dns_server;
	global $new_administrative_site;

	global $new_dtcshared_path;
	global $new_site_root_host_path;
	global $new_generated_file_path;

	global $new_qmail_rcpthost_path;
	global $new_qmail_virtualdomains_path;
	global $new_qmail_assign_path;
	global $new_qmail_poppasswd_path;

	global $new_apache_vhost_path;
	global $new_php_additional_library_path;
	global $new_php_library_path;

	global $new_named_path;
	global $new_named_slavefile_path;
	global $new_named_slavezonefiles_path;
	global $new_named_zonefiles_path;

	global $new_backup_script_path;
	global $new_bakcup_path;
	global $new_webalizer_stats_script_path;

	$sousrub = $_REQUEST["sousrub"];
	if(!isset($sousrub) || $sousrub == "")
		$sousrub = "general";

	switch($sousrub){
	case "vps":
	  break;
        case "backup":
                switch($_REQUEST["action"]){
                case "modify_grant_backup":
                      switch($_REQUEST["todo"]){
                      case "del":
                              $query = "DELETE FROM $pro_mysql_backup_table WHERE id='".$_REQUEST["id"]."';";
                              break;
                      case "save":
                              $query = "UPDATE $pro_mysql_backup_table SET
                              server_addr='".$_REQUEST["server_addr"]."',
                              server_login='".$_REQUEST["server_login"]."',
                              server_pass='".$_REQUEST["server_pass"]."' WHERE id='".$_REQUEST["id"]."';";
                              break;
                      default:
                              break;
                      }
                      break;
                case "add_grant_backup":
                      $query = "INSERT INTO $pro_mysql_backup_table (server_addr,server_login,server_pass,type)
                      VALUES('".$_REQUEST["server_addr"]."',
                      '".$_REQUEST["server_login"]."',
                      '".$_REQUEST["server_pass"]."',
                      'grant_access');";
                      break;
                case "modify_mx_trigger_backup":
                      switch($_REQUEST["todo"]){
                      case "del":
                        $query = "DELETE FROM $pro_mysql_backup_table WHERE id='".$_REQUEST["id"]."';";
                        break;
                      case "save":
                        $query = "UPDATE $pro_mysql_backup_table SET
                        server_addr='".$_REQUEST["server_addr"]."',
                        server_login='".$_REQUEST["server_login"]."',
                        server_pass='".$_REQUEST["server_pass"]."' WHERE id='".$_REQUEST["id"]."';";
                        break;
                      }
                      break;
                case "add_mx_trigger_backup":
                      $query = "INSERT INTO $pro_mysql_backup_table (server_addr,server_login,server_pass,type)
                      VALUES('".$_REQUEST["server_addr"]."',
                      '".$_REQUEST["server_login"]."',
                      '".$_REQUEST["server_pass"]."',
                      'trigger_mx_changes');";
                      break;
                case "modify_trigger_backup":
                      switch($_REQUEST["todo"]){
                      case "del":
                        $query = "DELETE FROM $pro_mysql_backup_table WHERE id='".$_REQUEST["id"]."';";
                        break;
                      case "save":
                        $query = "UPDATE $pro_mysql_backup_table SET
                        server_addr='".$_REQUEST["server_addr"]."',
                        server_login='".$_REQUEST["server_login"]."',
                        server_pass='".$_REQUEST["server_pass"]."' WHERE id='".$_REQUEST["id"]."';";
                        break;
                      }
                      break;
                case "add_trigger_backup":
                      $query = "INSERT INTO $pro_mysql_backup_table (server_addr,server_login,server_pass,type)
                      VALUES('".$_REQUEST["server_addr"]."',
                      '".$_REQUEST["server_login"]."',
                      '".$_REQUEST["server_pass"]."',
                      'trigger_changes');";
                      break;
                case "modify_mail_backup":
                      switch($_REQUEST["todo"]){
                      case "del":
                              $query = "DELETE FROM $pro_mysql_backup_table WHERE id='".$_REQUEST["id"]."';";
                              break;
                      case "save":
                              $query = "UPDATE $pro_mysql_backup_table SET
                              server_addr='".$_REQUEST["server_addr"]."',
                              server_login='".$_REQUEST["server_login"]."',
                              server_pass='".$_REQUEST["server_pass"]."' WHERE id='".$_REQUEST["id"]."';";
                              break;
                      default:
                              break;
                      }
                      break;
                case "add_mail_backup":
                      $query = "INSERT INTO $pro_mysql_backup_table (server_addr,server_login,server_pass,type)
                      VALUES('".$_REQUEST["server_addr"]."',
                      '".$_REQUEST["server_login"]."',
                      '".$_REQUEST["server_pass"]."',
                      'mail_backup');";
                      break;
                case "modify_dns_backup":
                      switch($_REQUEST["todo"]){
                      case "del":
                              $query = "DELETE FROM $pro_mysql_backup_table WHERE id='".$_REQUEST["id"]."';";
                              break;
                      case "save":
                              $query = "UPDATE $pro_mysql_backup_table SET
                              server_addr='".$_REQUEST["server_addr"]."',
                              server_login='".$_REQUEST["server_login"]."',
                              server_pass='".$_REQUEST["server_pass"]."' WHERE id='".$_REQUEST["id"]."';";
                              break;
                      default:
                              break;
                      }
                      break;
                case "add_dns_backup":
                      $query = "INSERT INTO $pro_mysql_backup_table (server_addr,server_login,server_pass,type)
                      VALUES('".$_REQUEST["server_addr"]."',
                      '".$_REQUEST["server_login"]."',
                      '".$_REQUEST["server_pass"]."',
                      'dns_backup');";
                      break;
                default:
                      break;
                }
                break;
	case "path":
		$query = "UPDATE config SET 
	site_root_host_path='".$_REQUEST["new_site_root_host_path"]."',
	generated_file_path='".$_REQUEST["new_generated_file_path"]."',
	dtcshared_path='".$_REQUEST["new_dtcshared_path"]."',
	chroot_path='".$_REQUEST["new_chroot_path"]."',
	qmail_rcpthost_path='".$_REQUEST["new_qmail_rcpthost_path"]."',
	qmail_virtualdomains_path='".$_REQUEST["new_qmail_virtualdomains_path"]."',
	qmail_assign_path='".$_REQUEST["new_qmail_assign_path"]."',
	qmail_poppasswd_path='".$_REQUEST["new_qmail_poppasswd_path"]."',
	apache_vhost_path='".$_REQUEST["new_apache_vhost_path"]."',
	php_additional_library_path='".$_REQUEST["new_php_additional_library_path"]."',
	php_library_path='".$_REQUEST["new_php_library_path"]."',
	named_path='".$_REQUEST["new_named_path"]."',
	named_slavefile_path='".$_REQUEST["new_named_slavefile_path"]."',
	named_slavezonefiles_path='".$_REQUEST["new_named_slavezonefiles_path"]."',
	named_zonefiles_path='".$_REQUEST["new_named_zonefiles_path"]."',
	backup_script_path='".$_REQUEST["new_backup_script_path"]."',
	bakcup_path='".$_REQUEST["new_bakcup_path"]."',
	webalizer_stats_script_path='".$_REQUEST["new_webalizer_stats_script_path"]."'
	WHERE 1 LIMIT 1";
		break;
        case "radius":
          $query = "";
          if($_REQUEST["action"] == "add_new_nas"){
          // action=add_new_nas&nas_name=bla&nas_short_name=blabla&nas_type=computone&nas_port_num=76&nas_secret=pas
          // &nas_snmp_com=toto&nas_description=didi
            $query = "INSERT INTO nas ( id,nasname,shortname,type,ports,secret,community,description) VALUES
              ('','".$_REQUEST["nas_name"]."','".$_REQUEST["nas_short_name"]."','".$_REQUEST["nas_type"]."',
              '".$_REQUEST["nas_port_num"]."','".$_REQUEST["nas_secret"]."','".$_REQUEST["nas_snmp_com"]."',
              '".$_REQUEST["nas_description"]."');";
          }
          if($_REQUEST["action"] == "edit_nas"){
            $query = "UPDATE nas SET nasname = '".$_REQUEST["nas_name"]."',
            shortname='".$_REQUEST["nas_short_name"]."',
            type='".$_REQUEST["nas_type"]."',
            ports='".$_REQUEST["nas_port_num"]."',
            secret='".$_REQUEST["nas_secret"]."',
            community='".$_REQUEST["nas_snmp_com"]."',
            description='".$_REQUEST["nas_description"]."'
            WHERE id='".$_REQUEST["nas_id"]."'";
          }
          break;
	}

        mysql_query($query)or die("Cannot query : \"$query\" ! line: ".__LINE__." file: ".__file__." sql said: ".mysql_error());
	// Tell the cron job to activate the changes
        $adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes', restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes',gen_ssh='yes' WHERE 1;";
        mysql_query($adm_query);
}

?>
