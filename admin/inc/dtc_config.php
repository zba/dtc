<?php

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
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=general\">";
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
	if($sousrub != "path")
		$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config&sousrub=path\">";
	$out .= $txt_cfg_path_conf_title[$lang];
	if($sousrub != "path")
		$out .= "</a>";
	$out .= "</td></tr></table>";
	return $out;
}

function drawGeneralConfig(){
	global $conf_demo_version;
	global $conf_use_javascript;
	global $conf_use_ssl;

	global $txt_cfg_general;
	global $txt_cfg_demo_version;
	global $txt_cfg_use_javascript;
	global $txt_cfg_use_ssl;

	global $txt_cfg_use_domain_based_ftp_logins;
	global $txt_cfg_session_expir_time;
	global $txt_cfg_select_type_of_skin;

	global $conf_session_expir_minute;
	global $conf_domain_based_ftp_logins;

	//additions for hide_password support (for ftp logins etc)
	global $txt_cfg_hide_password;
	global $conf_hide_password;

	global $conf_mta_type;

	global $lang;

	global $conf_skin;
	global $dtcshared_path;
	$dir = $dtcshared_path."/gfx/skin/";

	// Open a known directory, and proceed to read its contents
	$skin_choose = "";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(is_dir($dtcshared_path."/gfx/skin/".$file) && $file != "." && $file != ".."){
					if($file == $conf_skin){
						$skin_choose .= "<option name=\"".$file."\" selected>".$file."</option>";
					}else{
						$skin_choose .= "<option name=\"".$file."\">".$file."</option>";
					}
				}
			}
			closedir($dh);
		}
	}

	if("$conf_demo_version" == "yes"){
		$conf_demo_version_yes = " checked";
		$conf_demo_version_no = "";
	}else{
		$conf_demo_version_yes = "";
		$conf_demo_version_no = " checked";
	}

	if($conf_use_javascript == "yes"){
		$conf_use_javascript_yes = " checked";
		$conf_use_javascript_no = "";
	}else{
		$conf_use_javascript_yes = "";
		$conf_use_javascript_no = " checked";
	}

	if($conf_use_ssl == "yes"){
		$conf_use_ssl_yes = " checked";
		$conf_use_ssl_no = "";
	}else{
		$conf_use_ssl_yes = "";
		$conf_use_ssl_no = " checked";
	}

	if($conf_domain_based_ftp_logins == "yes"){
		$conf_domftplog_yes = " checked";
		$conf_domftplog_no = "";
	}else{
		$conf_domftplog_yes = "";
		$conf_domftplog_no = " checked";
	}

	if($conf_hide_password == "yes"){
		$conf_hdpasswd_yes = " checked";
		$conf_hdpasswd_no = "";
	}else{
		$conf_hdpasswd_yes = "";
		$conf_hdpasswd_no = " checked";
	}

	if($conf_mta_type == "qmail"){
		$conf_mtatype_qmail = " checked";
		$conf_mtatype_postfix = "";
	}else{
		$conf_mtatype_qmail = "";
		$conf_mtatype_postfix = " checked";
	}

	$out = "<h3>".$txt_cfg_general[$lang]."</h3>
<table with=\"100%\" height=\"1\">";
	if($conf_demo_version == "yes"){
		$out .= "<tr><td align=\"right\" nowrap>".$txt_cfg_demo_version[$lang]."</td><td width=\"100%\" nowrap>
<input type=\"radio\" value=\"yes\" name=\"new_demo_version\" checked>Yes [ ]No</td></tr>";
	}else{
		$out .= "<tr><td align=\"right\" nowrap>
	".$txt_cfg_demo_version[$lang]."</td><td width=\"100%\" nowrap><input type=\"radio\" value=\"yes\" name=\"new_demo_version\"$conf_demo_version_yes>Yes
	<input type=\"radio\" value=\"no\" name=\"new_demo_version\"$conf_demo_version_no>No
</td></tr>";
	}

	$out .= "
<tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_javascript[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_javascript\"$conf_use_javascript_yes>Yes <input type=\"radio\" value=\"no\" name=\"new_use_javascript\"$conf_use_javascript_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_ssl[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_ssl\"$conf_use_ssl_yes>Yes
	<input type=\"radio\" value=\"no\" name=\"new_use_ssl\"$conf_use_ssl_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_domain_based_ftp_logins[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_domain_based_ftp_logins\"$conf_domftplog_yes>Yes
	<input type=\"radio\" value=\"no\" name=\"new_domain_based_ftp_logins\"$conf_domftplog_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_session_expir_time[$lang]."</td>
	<td nowrap><input type=\"text\" size=\"4\" value=\"$conf_session_expir_minute\" name=\"new_session_expir_minute\"></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_hide_password[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_hidepasswd\"$conf_hdpasswd_yes>Yes
	<input type=\"radio\" value=\"no\" name=\"new_hidepasswd\"$conf_hdpasswd_no>No</td>
</tr><tr>
	<td colspan=\"2\"><h3>Daemon</h3></td>
</tr><tr>
	<td align=\"right\" nowrap>MTA (Mail Transport Agent):</td>
	<td nowrap><input type=\"radio\" value=\"qmail\" name=\"new_mta_type\"$conf_mtatype_qmail>Qmail
	<input type=\"radio\" value=\"postfix\" name=\"new_mta_type\"$conf_mtatype_postfix>Postfix</td>
</tr><tr>
	<td colspan=\"2\"><h3>DTC Skin chooser</h3></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_select_type_of_skin[$lang]."</td>
	<td nowrap><select name=\"skin_type\">$skin_choose</select></td>
</tr>
</table>
";
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

	if($conf_use_multiple_ip == "yes"){
		$conf_use_multiple_ip_yes = " checked";
		$conf_use_multiple_ip_no = "";
	}else{
		$conf_use_multiple_ip_yes = "";
		$conf_use_multiple_ip_no = " checked";
	}


	if($conf_use_nated_vhost == "yes"){
		$conf_use_nated_vhost_yes = " checked";
		$conf_use_nated_vhost_no = "";
	}else{
		$conf_use_nated_vhost_yes = "";
		$conf_use_nated_vhost_no = " checked";
	}

	$out = "<h3>".$txt_cfg_main_software_config[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr>
	<td align=\"right\" nowrap>
".$txt_cfg_main_site_ip[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_main_site_ip\" name=\"new_main_site_ip\"></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_site_addrs[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_site_addrs\" name=\"new_site_addrs\"></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_multiple_ip[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_multiple_ip\"$conf_use_multiple_ip_yes>Yes <input type=\"radio\" value=\"no\" name=\"new_use_multiple_ip\"$conf_use_multiple_ip_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_nated_vhost[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_nated_vhost\"$conf_use_nated_vhost_yes>Yes <input type=\"radio\" value=\"no\" name=\"new_use_nated_vhost\"$conf_use_nated_vhost_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_nated_vhost_ip[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_nated_vhost_ip\" name=\"new_nated_vhost_ip\"></td>
</tr><tr>
	<td align=\"right\" nowrap>
	".$txt_cfg_full_hostname[$lang]."</td><td nowrap><input type=\"text\" size =\"40\" value=\"$conf_administrative_site\" name=\"new_administrative_site\"></td>
</tr></table>";
	return $out;
}

function drawNamedConfig(){
	global $conf_addr_mail_server;
	global $conf_addr_backup_mail_server;
	global $conf_webmaster_email_addr;
	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;
	global $conf_ip_slavezone_dns_server;

	global $lang;

	global $txt_cfg_name_zonefileconf_title;
	global $txt_cfg_main_mx_addr;
	global $txt_cfg_mail_addr_webmaster;
	global $txt_cfg_primary_dns_server_addr;
	global $txt_cfg_secondary_dns_server_addr;
	global $txt_cfg_slave_dns_ip;
	global $txt_backup_mx_servers;

	$out = "<h3>".$txt_cfg_name_zonefileconf_title[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_main_mx_addr[$lang]."</td><td width=\"100%\"><input type=\"text\" size =\"40\" value=\"$conf_addr_mail_server\" name=\"new_addr_mail_server\"><br>
<tr><td align=\"right\" nowrap>
".$txt_backup_mx_servers[$lang]."
</td><td width=\"100%\"><input type=\"text\" size =\"40\" value=\"$conf_addr_backup_mail_server\" name=\"new_addr_backup_mail_server\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_mail_addr_webmaster[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_webmaster_email_addr\" name=\"new_webmaster_email_addr\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_primary_dns_server_addr[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_addr_primary_dns\" name=\"new_addr_primary_dns\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_secondary_dns_server_addr[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_addr_secondary_dns\" name=\"new_addr_secondary_dns\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_slave_dns_ip[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_ip_slavezone_dns_server\" name=\"new_ip_slavezone_dns_server\"><br>
</td></tr></table>
";
	return $out;
}

function drawBackupConfig(){
        global $pro_mysql_backup_table;
        global $txt_cfg_allow_following_servers_to_list;
        global $txt_cfg_make_request_to_server_for_update;
        global $txt_cfg_act_as_backup_mail_server;
        global $txt_cfg_act_as_backup_dns_server;
        global $lang;

	$out = "<h3>".$txt_cfg_allow_following_servers_to_list[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='grant_access';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>IP address</td><td>Login</td><td>Pass</td><td>Action</td></tr>";
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

	$out .= "<h3>".$txt_cfg_make_request_to_server_for_update[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='trigger_changes';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>Server address</td><td>Login</td><td>Pass</td><td>Action</td></tr>";
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

	$out .= "<h3>".$txt_cfg_act_as_backup_mail_server[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='mail_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! Line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        $out .= "<table><tr><td>Server hostname</td><td>Login</td><td>Pass</td><td>Action</td></tr>";
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
        $out .= "<table><tr><td>Server address</td><td>Login</td><td>Pass</td><td>Action</td></tr>";
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

  $out = "";
        $out .= "<h2><u>".$txt_cfg_registry_api_title[$lang]."</u></h2>
        <b><u>Tucows</u></b><br><img src=\"gfx/tucows.jpg\"><br>Note: you must have a Tucows reseller account.";

        if($conf_srs_crypt == 'DES'){
          $use_des = " checked ";
          $use_blowfish = "";
        }else{
          $use_des = "";
          $use_blowfish = " checked ";
        }

        if($conf_srs_enviro == "LIVE"){
          $use_live_system_yes = " checked ";
          $use_live_system_no = "";
        }else{
          $use_live_system_yes = "";
          $use_live_system_no = " checked ";
        }

	$out .= "<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_use_des_or_blowfish[$lang]."</td><td width=\"100%\"><input type=\"radio\" value=\"DES\" name=\"srs_crypt\"$use_des> DES <input type=\"radio\" value=\"BLOWFISH\" name=\"srs_crypt\"$use_blowfish> Blowfish </td>
</tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_use_test_or_live[$lang]."</td><td width=\"100%\"><input type=\"radio\" value=\"LIVE\" name=\"srs_enviro\"$use_live_system_yes> Yes <input type=\"radio\" value=\"TEST\" name=\"srs_enviro\"$use_live_system_no> No</td>
</tr><tr>
  <td align=\"right\" nowrap>".$txt_cfg_tucows_username[$lang]."</td>
  <td width=\"100%\"><input type=\"text\" size =\"40\" value=\"$conf_srs_user\" name=\"srs_username\"></td>
</tr><tr>
  <td align=\"right\" nowrap>".$txt_cfg_tucows_test_server_key[$lang]."</td>
  <td width=\"100%\"><input type=\"text\" size =\"40\" value=\"$conf_srs_test_key\" name=\"srs_test_key\"></td>
</tr><tr>
  <td align=\"right\" nowrap>".$txt_cfg_tucows_live_server_key[$lang]."</td>
  <td width=\"100%\"><input type=\"text\" size =\"40\" value=\"$conf_srs_live_key\" name=\"srs_live_key\"></td>
</tr>
</table>";
  return $out;
}

function drawDTCpayConfig(){
	global $lang;
	
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

	$q = "SELECT * FROM $pro_mysql_secpayconf_table";
	$r = mysql_query($q)or die("Cannot query : \"$q\" ! line: ".__LINE__." file: ".__file__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
        if($n != 1)	die("Error line: ".__LINE__." file: ".__file__." secpayconf table should have one and only one line!");
        $a = mysql_fetch_array($r);
        if($a["use_paypal"] == "yes"){
          $use_paypal_check_yes = " checked";
          $use_paypal_check_no = "";
        }else{
          $use_paypal_check_yes = "";
          $use_paypal_check_no = " checked";
        }

        if($a["paypal_autovalidate"] == "yes"){
          $auto_paypal_check_yes = " checked";
          $auto_paypal_check_no = "";
        }else{
          $auto_paypal_check_yes = "";
          $auto_paypal_check_no = " checked";
        }
	if($a["paypal_sandbox"] == "yes"){
	  $paypal_sandbox_check_yes = " checked ";
	  $paypal_sandbox_check_no = "";
        }else{
	  $paypal_sandbox_check_yes = "";
	  $paypal_sandbox_check_no = " checked ";
        }
	$out .= "<h2><u>".$txt_cfg_paytitle[$lang]."</u></h2>
	<h3>PayPal:</h3>";
	
	$out .="<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_use_paypal[$lang]."</td><td width=\"100%\"><input type=\"radio\" value=\"yes\" name=\"use_paypal\"$use_paypal_check_yes> Yes <input type=\"radio\" value=\"no\" name=\"use_paypal\"$use_paypal_check_no> No</td>
</tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_paypal_autovalid[$lang]."</td><td width=\"100%\"><input type=\"radio\" value=\"yes\" name=\"autovalid_paypal\"$auto_paypal_check_yes> Yes <input type=\"radio\" value=\"no\" name=\"autovalid_paypal\"$auto_paypal_check_no> No</td>
</tr><tr>
  <td align=\"right\" nowrap>".$txt_cfg_paypal_email[$lang]."</td>
  <td width=\"100%\"><input type=\"text\" size =\"40\" value=\"".$a["paypal_email"]."\" name=\"paypal_email\"></td>
</tr><tr>
  <td align=\"right\" nowrap>".$txt_cfg_paypal_ratefee[$lang]."</td>
  <td width=\"100%\"><input type=\"text\" size =\"6\" value=\"".$a["paypal_rate"]."\" name=\"paypal_rate\"></td>
</tr><tr>
  <td align=\"right\" nowrap>".$txt_cfg_paypal_flatfee[$lang]."</td>
  <td width=\"100%\"><input type=\"text\" size =\"6\" value=\"".$a["paypal_flat"]."\" name=\"paypal_flat\"></td>
</tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_paypal_use_sandbox[$lang]."</td><td width=\"100%\"><input type=\"radio\" value=\"yes\" name=\"sandbox_paypal\"$paypal_sandbox_check_yes> Yes <input type=\"radio\" value=\"no\" name=\"sandbox_paypal\"$paypal_sandbox_check_no> No</td>
</tr><tr>
  <td align=\"right\" nowrap>".$txt_cfg_paypal_sandbox_email[$lang]."</td>
  <td width=\"100%\"><input type=\"text\" size =\"40\" value=\"".$a["paypal_sandbox_email"]."\" name=\"paypal_sandbox_email\"></td>
</tr>
</table>
";
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
          if($i != 0)	$out .= " - ";
          $out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&nas_id=".$a["id"]."\">".$a["nasname"]."</a>";
        }

        $out .= "<br><br>";

        if(!isset($_REQUEST["nas_id"]) || $_REQUEST["nas_id"] != "new"){
          $out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&nas_id=new\">Add a new NAS</a><br><br>\n\n";
        }
        // NAS properties editor:
        if(isset($_REQUEST["nas_id"])){
          $hidden = "<input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
          <input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
          ";
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
		$global_conf = drawGeneralConfig();
		break;
	case "ip":
		$global_conf = drawNetworkConfig();
		break;
	case "zonefile":
		$global_conf = drawNamedConfig();
		break;
        case "backup":
                return "<form action=\"".$_SERVER["PHP_SELF"]."\"><input type=\"hidden\" name=\"rub\" value=\"config\">
<input type=\"hidden\" name=\"sousrub\" value=\"$sousrub\">".drawBackupConfig()."</form>";
		$global_conf = drawBackupConfig();
                break;
        case "registryapi":
          $global_conf = drawRegistryApiConfig();
          break;
        case "payconf":
                $global_conf = drawDTCpayConfig();
                break;
        case "radius":
                return drawDTCradiusConfig();
                break;
	case "path":
		$global_conf = drawDTCpathConfig();
		break;
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
	case "general":
		$query = "UPDATE config SET 
	demo_version='".$_REQUEST["new_demo_version"]."',
	use_javascript='".$_REQUEST["new_use_javascript"]."',
	use_ssl='".$_REQUEST["new_use_ssl"]."',
	mta_type='".$_REQUEST["new_mta_type"]."',
	domain_based_ftp_logins='".$_REQUEST["new_domain_based_ftp_logins"]."',
	session_expir_minute='".$_REQUEST["new_session_expir_minute"]."',
	hide_password='".$_REQUEST["new_hidepasswd"]."',
	skin='".$_REQUEST["skin_type"]."'
	WHERE 1 LIMIT 1";
		break;
	case "ip":
		$query = "UPDATE config SET 
	main_site_ip='".$_REQUEST["new_main_site_ip"]."',
	site_addrs='".$_REQUEST["new_site_addrs"]."',
	use_multiple_ip='".$_REQUEST["new_use_multiple_ip"]."',
	use_nated_vhost='".$_REQUEST["new_use_nated_vhost"]."',
	nated_vhost_ip='".$_REQUEST["new_nated_vhost_ip"]."',
	administrative_site='".$_REQUEST["new_administrative_site"]."'
	WHERE 1 LIMIT 1";
		break;
	case "zonefile":
		$query = "UPDATE config SET 
	addr_mail_server='".$_REQUEST["new_addr_mail_server"]."',
	addr_backup_mail_server='".$_REQUEST["new_addr_backup_mail_server"]."',
	addr_primary_dns='".$_REQUEST["new_addr_primary_dns"]."',
	addr_secondary_dns='".$_REQUEST["new_addr_secondary_dns"]."',
	ip_slavezone_dns_server='".$_REQUEST["new_ip_slavezone_dns_server"]."',
	webmaster_email_addr='".$_REQUEST["new_webmaster_email_addr"]."'
	WHERE 1 LIMIT 1";
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

	case "payconf":
		$query = "UPDATE $pro_mysql_secpayconf_table SET
         use_paypal='".$_REQUEST["use_paypal"]."',
  	 paypal_rate='".$_REQUEST["paypal_rate"]."',
  	 paypal_flat='".$_REQUEST["paypal_flat"]."',
  	 paypal_autovalidate='".$_REQUEST["autovalid_paypal"]."',
  	 paypal_sandbox='".$_REQUEST["sandbox_paypal"]."',
  	 paypal_sandbox_email='".$_REQUEST["paypal_sandbox_email"]."',
  	 paypal_email='".$_REQUEST["paypal_email"]."'
         WHERE 1 LIMIT 1;";
                break;
        case "registryapi":
          // srs_enviro=TEST&srs_username=&srs_test_key=&srs_live_key=&install_new_config_values=Ok
          $query = "UPDATE config SET
          srs_enviro='".$_REQUEST["srs_enviro"]."',
          srs_user='".$_REQUEST["srs_username"]."',
          srs_test_key='".$_REQUEST["srs_test_key"]."',
          srs_live_key='".$_REQUEST["srs_live_key"]."',
          srs_crypt='".$_REQUEST["srs_crypt"]."' WHERE 1;";
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
        $adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes', restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes' WHERE 1;";
        mysql_query($adm_query);
}

?>
