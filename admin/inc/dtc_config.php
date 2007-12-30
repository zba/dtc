<?php

function skin_DTCConfigMenu_Default ($dsc){
	if(!isset($_REQUEST["sousrub"])){
		$sousrub = "general";
        }else{
          $sousrub = $_REQUEST["sousrub"];
        }

        $out = "<table border=\"0\">";

        $keys = array_keys($dsc);
        $nbr_entry = sizeof($dsc);
        for($i=0;$i<$nbr_entry;$i++){
        	if($keys[$i] == $sousrub){
			$out .= "<tr><td style=\"white-space:nowrap\"><img border=\"0\" width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/".$dsc[ $keys[$i] ]["icon"]."\">".$dsc[ $keys[$i] ]["text"]."</td></tr>";
		}else{
			$out .= "<tr><td style=\"white-space:nowrap\"><a href=\"?rub=config&sousrub=".$keys[$i]."\"><img border=\"0\" width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/".$dsc[ $keys[$i] ]["icon"]."\">".$dsc[ $keys[$i] ]["text"]."</a></td></tr>";
		}
        }
        $out .= "</table>";
        return $out;
}


function drawDTCConfigMenu(){
	$dsc = array(
		"general" => array(
			"text" => gettext("General"),
			"icon" => "box_wnb_nb_picto-general.gif"),
		"ip" => array(
			"text" => gettext("IP addresses and network"),
			"icon" => "box_wnb_nb_picto-ipaddresses.gif"),
		"sslip" => array(
			"text" => gettext("SSL IPs addresses"),
			"icon" => "box_wnb_nb_picto-sslip.gif"),
		"zonefile" => array(
			"text" => gettext("Named zonefiles"),
			"icon" => "box_wnb_nb_picto-namedzonefiles.gif"),
		"backup" => array(
			"text" => gettext("MX and NS backup servers"),
			"icon" => "box_wnb_nb_picto-mxnsservers.gif"),
		"ftpbackup" => array(
			"text" => gettext("FTP backup"),
			"icon" => "box_wnb_nb_picto-ftpbackup.gif"),
		"ticket" => array(
			"text" => gettext("Support ticket"),
			"icon" => "box_wnb_nb_picto-supportickets.gif"),
		"vps" => array(
			"text" => gettext("VPS servers"),
			"icon" => "box_wnb_nb_picto-vpsservers.gif"),
		"radius" => array(
			"text" => "Radius",
			"icon" => "box_wnb_nb_picto-sslip.gif"),
		"path" => array(
			"text" => gettext("Paths"),
			"icon" => "box_wnb_nb_picto-paths.gif"),
		"payconf" => array(
			"text" => gettext("Payment gateway"),
			"icon" => "box_wnb_nb_picto-payementgateway.gif"),
		"renewals" => array(
			"text" => _("Renewals"),
			"icon" => "box_wnb_nb_picto-renewals.gif"),
		"registryapi" => array(
			"text" => _("Domain name registration"),
			"icon" => "box_wnb_nb_picto-domainnamereg.gif"),
		"companies" => array(
			"text" => _("Companies"),
			"icon" => "box_wnb_nb_picto-companies.gif"),
		"invoicing" => array(
			"text" => _("Invoicing"),
			"icon" => "box_wnb_nb_picto-invoicing.gif")
	);
	if(function_exists("skin_DTCConfigMenu")){
		return skin_DTCConfigMenu ($dsc);
	}else{
		return skin_DTCConfigMenu_Default ($dsc);
	}
}

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

	if(isset($dsc["edit_callback"])){
		$dsc["edit_callback"]();
	}

	if($conftype == "config"){
		getConfig();
	}else{
		get_secpay_conf();
	}

	$out .= "<h3>".$dsc["title"]."</h3>";
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
		if($i%2 == 1){
			$input_class = "dtcDatagrid_input_alt_color";
		}else{
			$input_class = "dtcDatagrid_input_color";
		}
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
			$control = "<select class=\"$input_class\" name=\"".$keys[$i]."\">".$control."</select>";
			break;
		case "text":
		default:
			if( isset($dsc["cols"][ $keys[$i] ]["size"]) ){
				$size = " size=\"".$dsc["cols"][ $keys[$i] ]["size"]."\" ";
			}else{
				$size = "";
			}
			$control = "<input class=\"$input_class\" $size type=\"text\" name=\"".$keys[$i]."\" value=\"".$$fld."\">";
			break;
		}
		$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$control,!($i%2));
	}
	$out .= dtcFromOkDraw()."</form></table>";
	return $out;
}

function drawRenewalsConfig(){

	global $conf_dtcadmin_path;
	$out = "";

	$dsc = array(
		"title" => _("VPS renewal email reminders periodicity"),
		"action" => "vps_renewal_period",
		"forward" => array("rub","sousrub"),
		"desc" => _("These numbers represent the days before and after expiration.
Warnings before and after expiration can be listed separated by |,
while others are made of a unique value. The message templates
are stored in /etc/dtc, and if not present in: ").$conf_dtcadmin_path."/reminders_msg/",
		"cols" => array(
			"vps_renewal_before" => array(
				"legend" => _("Warnings before expiration: "),
				"type" => "text",
				"size" => "16"),
			"vps_renewal_after" => array(
				"legend" => _("Warnings after expiration: "),
                                "type" => "text",
                                "size" => "16"),
			"vps_renewal_lastwarning" => array(
				"legend" => _("Last warning: "),
                                "type" => "text",
                                "size" => "16"),
			"vps_renewal_shutdown" => array(
				"legend" => _("Shutdown warning: "),
                                "type" => "text",
                                "size" => "16"),
			"message_subject_header" => array(
				"legend" => "Reminders and registration message subject header:",
                                "type" => "text",
                                "size" => "16")
			)
		);
	return configEditorTemplate ($dsc);
}

function drawSSLIPConfig(){
	global $pro_mysql_ssl_ips_table;
	global $rub;
	global $sousrub;

	$out = "";
	$dsc = array(
		"table_name" => $pro_mysql_ssl_ips_table,
		"title" => _("SSL dedicated IP addresses: "),
		"action" => "ssl_ip_list",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"ip_addr" => array(
				"type" => "text",
				"legend" => _("IPs addrs")),
			"port" => array(
				"type" => "text",
				"legend" => _("NAT Port")),
			"adm_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"expire" => array(
				"type" => "text",
				"size" => "14",
				"legend" => _("Expire")),
			"available" => array(
				"type" => "radio",
				"legend" => _("Available"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")))
			)
		);
	$out .= dtcDatagrid($dsc);
	$out .= "<i>"._("Take care not to add the control panel SSL IP itself if you don't want to have conflicts (and prevent apache from restarting)!")."</i><br>";
	return $out;
}

function drawTicketConfig(){
	global $pro_mysql_tik_admins_table;
	global $pro_mysql_tik_cats_table;
	global $rub;
	global $sousrub;

	$out = "<h3>"._("Support ticket configuration")."</h3>";
	$dsc = array(
		"table_name" => $pro_mysql_tik_admins_table,
		"title" => _("List of the administrators receiving request for support messages:"),
		"action" => "tik_admins",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"pseudo" => array(
				"type" => "text",
				"legend" => _("Nick name")),
			"realname" => array(
				"type" => "text",
				"legend" => _("Real name")),
			"email" => array(
				"type" => "text",
				"legend" => _("Email addr")),
			"available" => array(
				"type" => "radio",
				"legend" => _("Available"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")))
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_tik_cats_table,
		"title" => _("Ticket categories:"),
		"action" => "tik_cats",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"catname" => array(
				"type" => "text",
				"legend" => _("Category")),
			"catdescript" => array(
				"type" => "text",
				"size" => "50",
				"legend" => _("Category description"))));
	$out .= dtcDatagrid($dsc);
	return $out;
}

function drawFTPBacupConfig(){
	global $lang;

	$dsc = array(
		"title" => _("FTP backup configuration"),
		"action" => "ftp_backup_configuration",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"ftp_backup_activate" => array(
				"legend" => _("Activate FTP backups:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"ftp_backup_host" => array(
				"legend" => _("Hostname:"),
				"type" => "text",
				"size" => "30"),
			"ftp_backup_login" => array(
				"legend" => _("FTP login:"),
				"type" => "text",
				"size" => "30"),
			"ftp_backup_pass" => array(
				"legend" => _("FTP password:"),
				"type" => "text",
				"size" => "30"),
			"ftp_backup_frequency" => array(
				"legend" => _("Backup frequency:"),
				"type" => "popup",
				"values" => array("day","week","month"),
				"display_replace" => array(_("Daily"),_("Weekly"),_("Monthly"))),
			"ftp_backup_dest_folder" => array(
				"legend" => _("Destination folder:"),
				"type" => "text",
				"size" => "30")));
	return configEditorTemplate ($dsc);
}

function drawVPSServerConfig(){
	global $pro_mysql_vps_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;
	global $pro_mysql_list_table;
	global $pro_mysql_vps_server_lists_table;
	global $rub;
	global $sousrub;
	global $cc_code_array;

	global $conf_main_domain;

	$out = "<h3>"._("VPS Server and IP addresses registry edition")."</h3>";
	$dsc = array(
		"table_name" => $pro_mysql_vps_server_table,
		"title" => _("VPS Server list:"),
		"action" => "vps_server_list",
		"forward" => array("rub","sousrub"),
		"order_by" => "hostname",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"edithost" => array(
				"type" => "hyperlink",
				"legend" => _("IPs addrs"),
				"text" => "Edit IPs"),
			"hostname" => array(
				"type" => "text",
				"size" => "15",
				"legend" => _("Hostname")),
			"location" => array(
				"type" => "text",
				"size" => "10",
				"legend" => _("Location")),
			"soap_login" => array(
				"type" => "text",
				"size" => "7",
				"legend" => _("Soap login")),
			"soap_pass" => array(
				"type" => "password",
				"size" => "7",
				"legend" => _("SOAP password")),
			"country_code" => array(
				"type" => "popup",
				"legend" => _("Country"),
				"values" => array_keys($cc_code_array),
				"display_replace" => array_values($cc_code_array)),
			"lvmenable" => array(
				"type" => "radio",
				"legend" => _("Use LVM backend"),
				"display_replace" => array(_("Yes"),_("No")),
				"values" => array("yes","no"))));
	$vps_server_list = dtcDatagrid($dsc);
	$out .= $vps_server_list;

	if(isset($_REQUEST["edithost"])){
		$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE id='".$_REQUEST["edithost"]."';";
		$r = mysql_query($q);
		$a = mysql_fetch_array($r);
		$dsc = array(
			"table_name" => $pro_mysql_vps_ip_table,
			"title" => _("VPS IPs for ").$a["hostname"].":",
			"where_condition" => "vps_server_hostname='".$a["hostname"]."'",
			"order_by" => "vps_xen_name",
			"action" => "vps_server_ip_list",
			"forward" => array("rub","sousrub","edithost"),
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"vps_xen_name" => array(
					"type" => "text",
					"legend" => _("VPS xen number")),
				"ip_addr" => array(
					"type" => "text",
					"legend" => _("IPs addrs")),
				"available" => array(
					"type" => "radio",
					"legend" => _("Available"),
					"display_replace" => array(_("Yes"),_("No")),
					"values" => array("yes","no")))
			);
		$out .= dtcDatagrid($dsc);
		$out .= "<br><br>";
		$q = "SELECT name FROM $pro_mysql_list_table WHERE domain='$conf_main_domain';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 0){
			$out .= "Create a mailing list @".$conf_main_domain." if you want to write to all users of this VPS server.";
		}else{
			$out .= "<h3>Owners of the VPS of <i>".$a["hostname"]."</i> are subscribed automatically to the following mailing list:</h3>";
			$q2 = "SELECT * FROM $pro_mysql_vps_server_lists_table WHERE hostname='".$a["hostname"]."' ORDER BY list_name;";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			$out .= "Click on the list name to remove the list from the server:<br><br>";
			$conditions = "";
			for($i=0;$i<$n2;$i++){
				$a2 = mysql_fetch_array($r2);
				if($i != 0){
					$out .= " - ";
				}
				$conditions .= " AND name!='".$a2["list_name"]."'";
				$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?action=vps_server_list_remove&rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&edithost=".$_REQUEST["edithost"]."&list_name=".$a2["list_name"]."\">".$a2["list_name"]."</a>";
			}
			$out .= "<br><br>";
			$q = "SELECT * FROM $pro_mysql_list_table WHERE domain='$conf_main_domain' $conditions ORDER BY name;";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			$out .= "Click on the list name to add the list to the server:<br><br>";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				if($i != 0){
					$out .= " - ";
				}
				$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?action=vps_server_list_add&rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&edithost=".$_REQUEST["edithost"]."&name=".$a["name"]."\">".$a["name"]."</a>";
			}
		}
	}
	return $out;
}

function drawRegistrySelection(){
	global $pro_mysql_registry_table;
	global $txt_registry_selection;
	global $lang;
	$out = "<h3>".$txt_registry_selection[$lang]."</h3>";
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
	global $txt_cfg_demo_version;

	global $cc_code_array;

	global $conf_skin;
	global $dtcshared_path;
	$dir = $dtcshared_path."/gfx/skin/";

	$out = "";

	$dsc = array(
		"title" => _("General"),
		"action" => "general_config_editor",
		"forward" => array("rub"),
		"cols" => array(
			"use_javascript" => array(
				"legend" => _("Use javascript:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_ssl" => array(
				"legend" => _("Use SSL:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"domain_based_ftp_logins" => array(
				"legend" => _("Use @domain.com ftp logins:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"domain_based_ssh_logins" => array(
				"legend" => _("Use @domain.com ssh logins:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"session_expir_minute" => array(
				"legend" => _("User session expire time (min):"),
				"type" => "text",
				"size" => "10"),
			"this_server_country_code" => array(
				"type" => "popup",
				"legend" => _("This server location:"),
				"values" => array_keys($cc_code_array),
				"display_replace" => array_values($cc_code_array)),
			"selling_conditions_url" => array(
				"legend" => _("Selling conditions URL:"),
				"type" => "text",
				"size" => "40")));
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"title" => _("Daemon"),
		"action" => "general_config_daemon",
		"forward" => array("rub"),
		"cols" => array (
			"mta_type" => array(
				"legend" => "MTA<a href=\"http://pl.wikipedia.org/wiki/MTA\" target=\"_blank\">*</a>:",
				"type" => "radio",
				"values" => array("qmail","postfix")),
			"use_cyrus" => array(
				"type" => "radio",
				"legend" => _("Use cyrus:"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_webalizer" => array(
				"type" => "radio",
				"legend" => _("Use webalizer"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"webalizer_country_graph" => array(
				"legend" => _("Webalizer country graph:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_awstats" => array(
				"type" => "radio",
				"legend" => _("Use awstats"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_visitors" => array(
				"type" => "radio",
				"legend" => _("Use visitors"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"user_mysql_type" => array(
				"legend" => _("Location of user's database:"),
				"type" => "radio",
				"values" => array("localhost","distant"),
				"display_replace" => array(_("Same as for DTC"),_("Another location"))),
			"user_mysql_host" => array(
				"legend" => _("User MySQL host:"),
				"type" => "text"),
			"user_mysql_root_login" => array(
				"legend" => _("User MySQL root login:"),
				"type" => "text"),
			"user_mysql_root_pass" => array(
				"legend" => _("User MySQL root password:"),
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
		"title" => _("DTC Skin chooser"),
		"action" => "general_config_skin_chooser",
		"forward" => array("rub"),
		"cols" => array(
			"skin" => array(
				"legend" => _("Select the type of skin:"),
				"type" => "popup",
				"values" => $skin_list)));
	$out .= configEditorTemplate ($dsc);

	return $out;
}

function drawNetworkConfig(){
	$dsc = array(
		"title" => _("Main software configuration of DTC"),
		"action" => "general_config_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"main_site_ip" => array(
				"legend" => _("Main ip address of the server:"),
				"type" => "text",
				"size" => "20"),
			"site_addrs" => array(
				"legend" => _("Host IP addresses (separated by \"|\"):"),
				"type" => "text",
				"size" => "50"),
			"use_multiple_ip" => array(
				"legend" => _("Use multiple IPs:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_nated_vhost" => array(
				"legend" => _("Generate all apache vhosts on local network ip (NAT)"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"nated_vhost_ip" => array(
				"legend" => _("Local network area ip adress of the vhost using NAT"),
				"type" => "text",
				"size" => "20"),
			"administrative_site" => array(
				"legend" => _("Full hostname of DTC admin panel:"),
				"type" => "text",
				"size" => "50"),
			"administrative_ssl_port" => array(
				"legend" => _("SSL Port:"),
				"type" => "text",
				"size" => "10")));
	return configEditorTemplate ($dsc);
}

function namedEditionCallback(){
	global $pro_mysql_domain_table;
	global $pro_mysql_cronjob_table;
	$q = "UPDATE $pro_mysql_domain_table SET generate_flag='yes';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$q = "UPDATE $pro_mysql_cronjob_table SET reload_named='yes', gen_named='yes';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

function drawNamedConfig(){
	$dsc = array(
		"title" => _("Named zonefiles"),
		"action" => "named_zonefile_config_editor",
		"forward" => array("rub","sousrub"),
		"edit_callback" => "namedEditionCallback",
		"cols" => array(
			"addr_mail_server" => array(
				"legend" => _("Address of your main MX server:"),
				"type" => "text",
				"size" => "40"),
			"addr_backup_mail_server" => array(
				"legend" => _("List your backup MX servers separated by &quot;|&quot; (pipe)<br>
(leave blank if you don't have backup MX server):"),
				"type" => "text",
				"size" => "50"),
			"webmaster_email_addr" => array(
				"legend" => _("Email address of your webmaster:"),
				"type" => "text",
				"size" => "40"),
			"addr_primary_dns" => array(
				"legend" => _("Primary DNS server address:"),
				"type" => "text",
				"size" => "40"),
			"addr_secondary_dns" => array(
				"legend" => _("Secondary DNS servers address<br>
(separated by &quot;|&quot; (pipe) if more than one):"),
				"type" => "text",
				"size" => "40"),
			"ip_slavezone_dns_server" => array(
				"legend" => _("IP of the master DNS to be written in the named.slavezones.conf:"),
				"type" => "text",
				"size" => "20"),
			"use_cname_for_subdomains" => array(
				"legend" => _("Use CNAME instead of A record for subdomains:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"autogen_default_subdomains" => array(
				"legend" => _("Auto-generate default subdomains (mail, pop, imap, smtp, ftp, list):"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"ip_allowed_dns_transfer" => array(
				"legend" => _("List the DNS server IPs allowed to do zone<br>
transfers separated by &quot;|&quot; (pipe)<br>
(leave blank if you don't have backup DNS server):"),
				"type" => "text",
				"size" => "50")));
	return configEditorTemplate ($dsc);
}

function drawBackupConfig(){
        global $pro_mysql_backup_table;

	$out = "";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Allow those servers to list this server's domain names for doing backup:"),
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
				"size" => "14",
				"legend" => _("IP address")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$out .= "<hr width=\"100%\">";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Tell the following servers when a domain is added or removed :"),
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
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Tell the following servers when an email is added or removed :"),
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
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$out .= "<hr width=\"100%\">";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Act as backup mail server for the following servers:"),
		"action" => "act_as_backup_mail",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='mail_backup'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Act as backup DNS server for the following servers:"),
		"action" => "act_as_backup_dns",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='dns_backup'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);
	return $out;
}

function drawRegistryApiConfig(){

	global $pro_mysql_config_table;

	$dsc = array(
		"title" => _("Domain name registry API configuraiton"),
		"action" => "domain_registry_config_editor",
		"forward" => array("rub","sousrub"),
		"desc" => "<img src=\"gfx/tucows.jpg\"><br>Note: you must have a Tucows reseller account.",
		"cols" => array(
			"use_registrar_api" => array(
				"legend" => "Use registrar API:",
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"srs_crypt" => array(
				"legend" => _("Type of encryption for connecting to Tucows server:"),
				"type" => "radio",
				"values" => array("DES","BLOWFISH")),
			"srs_enviro" => array(
				"legend" => _("Use the LIVE server (and not the test one) :"),
				"type" => "radio",
				"values" => array("LIVE","TEST")),
			"srs_user" => array(
				"legend" => _("Your SRS username:"),
				"type" => "text",
				"size" => "30"),
			"srs_test_key" => array(
				"legend" => _("Your key to access the test server:"),
				"type" => "text",
				"size" => "50"),
			"srs_live_key" => array(
				"legend" => _("Your key to access the LIVE server:"),
				"type" => "text",
				"size" => "50")));
	return configEditorTemplate ($dsc);
}

function drawDTCpayConfig(){
	global $lang;
	global $txt_currency;

	global $pro_mysql_secpayconf_table;

	$out = "";

	$dsc = array(
		"title" => _("Secure payment configuration"),
		"action" => "payment_gateway_currency_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"currency_symbol" => array(
				"legend" => _("Currency symbol:"),
				"type" => "text",
				"size" => "6"),
			"currency_letters" => array(
				"legend" => _("Currency letters:"),
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => "PayPal:",
		"action" => "payment_gateway_paypal_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_paypal" => array(
				"legend" => _("Use paypal:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"paypal_autovalidate" => array(
				"legend" => _("Validate new account if paid:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"paypal_email" => array(
				"legend" => _("PayPal business account email:"),
				"type" => "text",
				"size" => "30"),
			"paypal_rate" => array(
				"legend" => _("PayPal fee rate:"),
				"type" => "text",
				"size" => "6"),
			"paypal_flat" => array(
				"legend" => _("PayPal flat fee:"),
				"type" => "text",
				"size" => "6"),
			"paypal_sandbox" => array(
				"legend" => _("Use the sandbox test server:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_paypal_recurring" => array(
				"legend" => _("Use paypal recuring:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"paypal_sandbox_email" => array(
				"legend" => _("PayPal test account email (sandbox):"),
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => "eNETS:",
		"action" => "payment_gateway_enets_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_enets" => array(
				"legend" => _("Use eNETS:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_enets_test" => array(
				"legend" => _("eNETS server:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Test server"),_("Production server"))),
			"enets_mid_id" => array(
				"legend" => _("eNETS merchant ID:"),
				"type" => "text",
				"size" => "6"),
			"enets_test_mid_id" => array(
				"legend" => _("eNETS test merchant ID:"),
				"type" => "text",
				"size" => "6"),
			"enets_rate" => array(
				"legend" => _("eNETS rate:"),
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => "Maxmind API:",
		"action" => "maxmind_api_conf_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_maxmind" => array(
				"legend" => "Use Maxmind API:",
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"maxmind_login" => array(
				"legend" => "Maxmind login:",
				"type" => "text",
				"size" => "30"),
			"maxmind_license_key" => array(
				"legend" => "Maxmind license key:",
				"type" => "text",
				"size" => "30")));
	$out .= configEditorTemplate ($dsc,"secpay");

	return $out;
}

function drawDTCradiusConfig(){
	global $conf_dtcshared_path;
	global $lang;

	$out = "";
	$dsc = array(
		"title" => _("NAS config"),
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
				"legend" => _("Name:")),
			"shortname" => array(
				"type" => "text",
				"legend" => _("Short name:")),
			"type" => array(
				"type" => "popup",
				"legend" => _("Type:"),
				"values" => array("cisco","computone","livingston","max40xx","multitech","netserver","pathras","patton","portslave","tc","usrhiper","other")),
			"ports" => array(
				"type" => "text",
				"legend" => _("Port number:"),
				"check" => "number"),
			"secret" => array(
				"type" => "password",
				"legend" => _("Password:"),
				"check" => "dtc_pass"),
			"community" => array(
				"type" => "text",
				"legend" => "SNMP community:"),
			"description" => array(
				"type" => "text",
				"legend" => _("Description:"))));
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
	
	global $conf_htpasswd_path;

	global $lang;

	$out = "";

	$dsc = array(
		"title" => _("Paths"),
		"action" => "main_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"dtcshared_path" => array(
				"legend" => _("Path of your DTC shared directory:"),
				"type" => "text"),
			"site_root_host_path" => array(
				"legend" => _("Your default new account directory:"),
				"type" => "text"),
			"chroot_path" => array(
				"legend" => _("Path of the cgi-wrapper disk template (chroot for SBOX):"),
				"type" => "text"),
			"generated_file_path" => array(
				"legend" => _("Path where DTC will be restricted for generating it's configuration files for daemons.<br>
Each of the following (qmail, apache and named) path will be concatened to this:"),
				"type" => "text"),
			"htpasswd_path" => array(
				"legend" => "Apache htpasswd path",
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
				"legend" => "virtualdomains:",
				"type" => "text",
				"size" => "26"),
			"qmail_assign_path" => array(
				"legend" => "assign:",
				"type" => "text",
				"size" => "26"),
			"qmail_poppasswd_path" => array(
				"legend" => "poppasswd:",
				"type" => "text",
				"size" => "26")));
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_web.gif\">" . _("Apache file names"),
		"action" => "apache_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"apache_vhost_path" => array(
				"legend" => _("Virtual host config-file:"),
				"type" => "text",
				"size" => "20"),
			"php_library_path" => array(
				"legend" => _("PHP libraries open_basedir<br>
(separated by \":\", reset on each dtc install):"),
				"type" => "text",
				"size" => "60"),
			"php_additional_library_path" => array(
				"legend" => _("PHP open_basedir additional libraries path<br>
(survives reinstallation):"),
				"type" => "text",
				"size" => "60")));
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_named.gif\"> " . _("Named file names"),
		"action" => "named_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"named_path" => array(
				"legend" => _("Named main file:"),
				"type" => "text",
				"size" => "30"),
			"named_slavefile_path" => array(
				"legend" => _("Named slave file:"),
				"type" => "text",
				"size" => "30"),
			"named_zonefiles_path" => array(
				"legend" => _("Named main zonefiles folder:"),
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
		"title" => _("List of your companies:"),
		"new_item_title" => _("Add a new company:"),
		"new_item_link" => _("Add a new company"),
		"edit_item_title" => _("Edit a company:"),
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
				"legend" => _("Company name:")),
			"address" => array(
				"type" => "textarea",
				"cols" => "60",
				"rows" => "5",
				"legend" => _("Address:")),
			"country" => array(
				"type" => "popup",
				"legend" => _("Country:"),
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"registration_number" => array(
				"type" => "text",
				"size" => "30",
				"legend" => _("Registration number:")),
			"vat_number" => array(
				"type" => "text",
				"size" => "30",
				"legend" => _("VAT number:")),
			"vat_rate" => array(
				"type" => "text",
				"size" => "10",
				"legend" => _("VAT rate:")),
			"logo_path" => array(
				"type" => "text",
                                "size" => "30",
                                "legend" => _("Logo path relative to") . "<br>$conf_generated_file_path/invoice_pics/:"),
			"text_after" => array(
				"type" => "textarea",
				"cols" => "60",
				"rows" => "5",
				"legend" => _("Invoice free text:")),
			"footer" => array(
				"type" => "textarea",
				"cols" => "60",
				"rows" => "5",
				"legend" => _("Invoice footer:") )));
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
		"title" => _("Default company invoicing:"),
		"action" => "default_company_invoicing_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"default_company_invoicing" => array(
				"legend" => _("Default company invoicing:"),
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
		"title" => _("Customer and service country vs company:"),
		"action" => "cust_and_serv_country_vs_comp",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"service_country_code" => array(
				"type" => "popup",
				"legend" => _("Service country"),
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"customer_country_code" => array(
				"type" => "popup",
				"legend" => _("Customer country"),
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"company_id" => array(
				"type" => "popup",
				"legend" => _("Company name"),
				"values" => $comp_ids,
				"display_replace" => $comp_names)
			)
		);
	$out .= dtcDatagrid($dsc);

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
