<?php

function drawDTCConfigForm(){
	global $conf_demo_version;
	global $conf_main_site_ip;
	global $conf_site_addrs;
	global $conf_use_multiple_ip;

	global $conf_addr_mail_server;
	global $conf_webmaster_email_addr;
	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;
	global $conf_ip_slavezone_dns_server;
	global $conf_administrative_site;

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

	global $conf_use_javascript;
	global $conf_use_ssl;

	global $conf_nated_vhost_ip;
	global $conf_use_nated_vhost;

	global $lang;

	if("$conf_demo_version" == "yes"){
		$conf_demo_version_yes = " checked";
		$conf_demo_version_no = "";
	}else{
		$conf_demo_version_yes = "";
		$conf_demo_version_no = " checked";
	}

	if($conf_use_multiple_ip == "yes"){
		$conf_use_multiple_ip_yes = " checked";
		$conf_use_multiple_ip_no = "";
	}else{
		$conf_use_multiple_ip_yes = "";
		$conf_use_multiple_ip_no = " checked";
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

	if($conf_use_nated_vhost == "yes"){
		$conf_use_nated_vhost_yes = " checked";
		$conf_use_nated_vhost_no = "";
	}else{
		$conf_use_nated_vhost_yes = "";
		$conf_use_nated_vhost_no = " checked";
	}

	global $txt_cfg_general;
	global $txt_cfg_demo_version;
	global $txt_cfg_use_multiple_ip;
	global $txt_cfg_full_hostname;
	global $txt_cfg_main_site_ip;
	global $txt_cfg_site_addrs;
	global $txt_cfg_use_javascript;
	global $txt_cfg_use_ssl;
	global $txt_cfg_use_nated_vhost;
	global $txt_cfg_nated_vhost_ip;

	$general = "<h3>".$txt_cfg_general[$lang]."</h3>
<table with=\"100%\" height=\"1\">";
	if($conf_demo_version == "yes"){
		$general .= "<tr><td align=\"right\" nowrap>".$txt_cfg_demo_version[$lang]."</td><td width=\"100%\" nowrap>
<input type=\"radio\" value=\"yes\" name=\"new_demo_version\" checked>Yes [ ]No</td></tr>";
	}else{
		$general .= "<tr><td align=\"right\" nowrap>
	".$txt_cfg_demo_version[$lang]."</td><td width=\"100%\" nowrap><input type=\"radio\" value=\"yes\" name=\"new_demo_version\"$conf_demo_version_yes>Yes
	<input type=\"radio\" value=\"no\" name=\"new_demo_version\"$conf_demo_version_no>No
</td></tr>";
	}
	$general .= "
<tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_javascript[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_javascript\"$conf_use_javascript_yes>Yes<input type=\"radio\" value=\"no\" name=\"new_use_javascript\"$conf_use_javascript_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_ssl[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_ssl\"$conf_use_ssl_yes>Yes
	<input type=\"radio\" value=\"no\" name=\"new_use_ssl\"$conf_use_ssl_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_nated_vhost[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_nated_vhost\"$conf_use_nated_vhost_yes>Yes<input type=\"radio\" value=\"no\" name=\"new_use_nated_vhost\"$conf_use_nated_vhost_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>
".$txt_cfg_nated_vhost_ip[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_nated_vhost_ip\" name=\"new_nated_vhost_ip\"></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_use_multiple_ip[$lang]."</td>
	<td nowrap><input type=\"radio\" value=\"yes\" name=\"new_use_multiple_ip\"$conf_use_multiple_ip_yes>Yes<input type=\"radio\" value=\"no\" name=\"new_use_multiple_ip\"$conf_use_multiple_ip_no>No</td>
</tr><tr>
	<td align=\"right\" nowrap>
".$txt_cfg_main_site_ip[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_main_site_ip\" name=\"new_main_site_ip\"></td>
</tr><tr>
	<td align=\"right\" nowrap>".$txt_cfg_site_addrs[$lang]."</td>
	<td nowrap><input type=\"text\" size =\"40\" value=\"$conf_site_addrs\" name=\"new_site_addrs\"></td>
</tr><tr>
	<td align=\"right\" nowrap>
	".$txt_cfg_full_hostname[$lang]."</td><td nowrap><input type=\"text\" size =\"40\" value=\"$conf_administrative_site\" name=\"new_administrative_site\"></td>
</tr>
</table>";

	global $txt_cfg_name_zonefileconf_title;
	global $txt_cfg_main_mx_addr;
	global $txt_cfg_mail_addr_webmaster;
	global $txt_cfg_primary_dns_server_addr;
	global $txt_cfg_secondary_dns_server_addr;
	global $txt_cfg_slave_dns_ip;
	$namedZonefile = "<h3>".$txt_cfg_name_zonefileconf_title[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_main_mx_addr[$lang]."</td><td width=\"100%\"><input type=\"text\" size =\"40\" value=\"$conf_addr_mail_server\" name=\"new_addr_mail_server\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_mail_addr_webmaster[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_webmaster_email_addr\" name=\"new_webmaster_email_addr\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_primary_dns_server_addr[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_addr_primary_dns\" name=\"new_addr_primary_dns\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_secondary_dns_server_addr[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_addr_secondary_dns\" name=\"new_addr_secondary_dns\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_cfg_slave_dns_ip[$lang]."</td><td><input type=\"text\" size =\"40\" value=\"$conf_ip_slavezone_dns_server\" name=\"new_ip_slavezone_dns_server\"><br>
</td></tr></table>";

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
	".$txt_cfg_named_main_zonefile[$lang]."<input type=\"text\" size =\"40\" value=\"$conf_named_slavezonefiles_path\" name=\"new_named_slavezonefiles_path\"><br>
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_named_cache_slave_zonefile[$lang]."<input type=\"text\" size =\"40\" value=\"$conf_named_zonefiles_path\" name=\"new_named_zonefiles_path\"><br>
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

	global $txt_cfg_main_software_config;
	global $txt_cfg_path_conf_title;
	global $txt_cfg_mainpath_conf_title;
	global $txt_cfg_dtc_shared_folder;
	global $txt_cfg_new_account_defaultpath;
	global $txt_cfg_generated_file_path;
	$server_global_conf .= "
	<h2>".$txt_cfg_main_software_config[$lang]."</h2>
<table><tr><td>$general
</td><td>$namedZonefile</td></tr></table>

<h2>".$txt_cfg_path_conf_title[$lang]."</h2>
<h3>".$txt_cfg_mainpath_conf_title[$lang]."</h3>
<table with=\"100%\" height=\"1\">
<tr><td align=\"right\" nowrap>
	".$txt_cfg_dtc_shared_folder[$lang]."</td><td nowrap><input type=\"text\" size =\"40\" value=\"$conf_dtcshared_path\" name=\"new_dtcshared_path\">
</td></tr><tr><td align=\"right\" nowrap>
	".$txt_cfg_new_account_defaultpath[$lang]."</td><td nowrap><input type=\"text\" size =\"40\" value=\"$conf_site_root_host_path\" name=\"new_site_root_host_path\">
</td></tr></table>
".$txt_cfg_generated_file_path[$lang]."<br>
<input type=\"text\" size =\"60\" value=\"$conf_generated_file_path\" name=\"new_generated_file_path\"><br><br>

<table width=\"100%\"><tr><td>$qmailPath
</td><td>$apachePath</td></tr>
<tr><td>$namedPath
</td><td>$webalizerAndBackupPath</td></tr></table>
";

	return "<form action=\"".$_SERVER["PHP_SELF"]."\"><input type=\"hidden\" name=\"rub\" value=\"config\">$server_global_conf
	<center><input type=\"submit\" name=\"install_new_config_values\" value=\"Ok\"></center>
	</form>";

}

function saveDTCConfigInMysql(){
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

	$query = "UPDATE config SET 
	demo_version='".$_REQUEST["new_demo_version"]."',
	main_site_ip='".$_REQUEST["new_main_site_ip"]."',
	site_addrs='".$_REQUEST["new_site_addrs"]."',
	use_multiple_ip='".$_REQUEST["new_use_multiple_ip"]."',
	use_javascript='".$_REQUEST["new_use_javascript"]."',
	use_ssl='".$_REQUEST["new_use_ssl"]."',
	addr_mail_server='".$_REQUEST["new_addr_mail_server"]."',
	webmaster_email_addr='".$_REQUEST["new_webmaster_email_addr"]."',
	addr_primary_dns='".$_REQUEST["new_addr_primary_dns"]."',
	addr_secondary_dns='".$_REQUEST["new_addr_secondary_dns"]."',
	ip_slavezone_dns_server='".$_REQUEST["new_ip_slavezone_dns_server"]."',
	administrative_site='".$_REQUEST["new_administrative_site"]."',
	site_root_host_path='".$_REQUEST["new_site_root_host_path"]."',
	generated_file_path='".$_REQUEST["new_generated_file_path"]."',
	dtcshared_path='".$_REQUEST["new_dtcshared_path"]."',
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
	nated_vhost_ip='".$_REQUEST["new_nated_vhost_ip"]."',
	use_nated_vhost='".$_REQUEST["new_use_nated_vhost"]."',
	webalizer_stats_script_path='".$_REQUEST["new_webalizer_stats_script_path"]."'
	WHERE 1 LIMIT 1";
	mysql_query($query)or die("Cannot query : \"$query\" !!!".mysql_error());

	// Tell the cron job to activate the changes
        $adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes', restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes' WHERE 1;";
        mysql_query($adm_query);


}

?>
