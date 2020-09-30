<?php

////////////////////////////////////////////////////////////////////////////
// Draw the form for configuring global admin account info (path, etc...) //
////////////////////////////////////////////////////////////////////////////
function drawEditAdmin($admin){
	global $pro_mysql_vps_server_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_product_table;
	global $pro_mysql_dedicated_table;
	global $cc_code_popup;

	global $adm_login;
	global $adm_pass;
	global $rub;
	global $conf_hide_password;

	$info = $admin["info"];
	if(isset($admin["data"])){
		$data = $admin["data"];
	}

	$adm_cur_pass = $info["adm_pass"];
	$adm_path = $info["path"];
	$adm_max_email = $info["max_email"];
	$adm_max_ftp = $info["max_ftp"];
	$adm_quota = $info["quota"];
	$bandwidth_per_month_mb = $info["bandwidth_per_month_mb"];
	$adm_id_client = $info["id_client"];
	$expire = $info["expire"];
	$prod_id = $info["prod_id"];

	$allow_add_domain = $info["allow_add_domain"];
	$max_domain = $info["max_domain"];
	$restricted_ftp_path = $info["restricted_ftp_path"];
	$allow_dns_and_mx_change = $info["allow_dns_and_mx_change"];
	$allow_mailing_list_edit = $info["allow_mailing_list_edit"];
	$allow_subdomain_edit = $info["allow_subdomain_edit"];
	$resseller_flag = $info["resseller_flag"];
	$ssh_login_flag = $info["ssh_login_flag"];
	$ftp_login_flag = $info["ftp_login_flag"];
	$pkg_install_flag = $info["pkg_install_flag"];
	$shared_hosting_security = $info["shared_hosting_security"];

	if($resseller_flag == "yes"){
		$resflag_yes = " checked='checked' ";
		$resflag_no = "";
	}else{
		$resflag_yes = " ";
		$resflag_no = " checked='checked' ";
	}
	$res_selector = "<input type=\"radio\" name=\"resseller_flag\" value=\"yes\"$resflag_yes> "._("Yes")."
	<input type=\"radio\" name=\"resseller_flag\" value=\"no\"$resflag_no> "._("No")."</div>";

	if($ssh_login_flag == "yes"){
		$sshlogin_yes = " checked='checked' ";
		$sshlogin_no = "";
	}else{
		$sshlogin_yes = "";
		$sshlogin_no = " checked='checked' ";
	}
	$sshlog_selector = "<input type=\"radio\" name=\"ssh_login_flag\" value=\"yes\"$sshlogin_yes> "._("Yes")."
	<input type=\"radio\" name=\"ssh_login_flag\" value=\"no\"$sshlogin_no> "._("No");

	if($ftp_login_flag == "yes"){
		$ftplogin_yes = " checked='checked' ";
		$ftplogin_no = "";
	}else{
		$ftplogin_yes = "";
		$ftplogin_no = " checked='checked' ";
	}
	$ftplog_selector = "<input type=\"radio\" name=\"ftp_login_flag\" value=\"yes\"$ftplogin_yes> "._("Yes")."
	<input type=\"radio\" name=\"ftp_login_flag\" value=\"no\"$ftplogin_no> "._("No");

	if($pkg_install_flag == "yes"){
		$pkg_install_yes = " checked='checked' ";
		$pkg_install_no = "";
	}else{
		$pkg_install_yes = "";
		$pkg_install_no = " checked='checked' ";
	}
	$pkg_install_selector = "<input type=\"radio\" name=\"pkg_install_flag\" value=\"yes\"$pkg_install_yes> "._("Yes")."
	<input type=\"radio\" name=\"pkg_install_flag\" value=\"no\"$pkg_install_no> "._("No");

	if($allow_add_domain == "yes")	$adyes = "selected='selected'";	else $adyes = "";
	if($allow_add_domain == "check")$adcheck = "selected='selected'";	else $adcheck = "";
	if($allow_add_domain == "no")	$adno = "selected='selected'";	else $adno = "";
	$aldom_popup = "<select class=\"dtcDatagrid_input_color\" name=\"allow_add_domain\">
<option value=\"yes\" $adyes>" . _("Yes") ."</option>
<option value=\"check\" $adcheck>" . _("Check") . "</option>
<option value=\"no\" $adno>". _("No") . "</option>
</select>
";

	// Restriction of FTP path selection
	if($restricted_ftp_path == "yes"){
		$restricted_ftp_path_yes = " checked='checked' ";
		$restricted_ftp_path_no = "";
	}else{
		$restricted_ftp_path_yes = "";
		$restricted_ftp_path_no = " checked='checked' ";
	}
	$restricted_ftp_path_selector = "<input type=\"radio\" name=\"restricted_ftp_path\" value=\"yes\"$restricted_ftp_path_yes> "._("Yes")."
<input type=\"radio\" name=\"restricted_ftp_path\" value=\"no\"$restricted_ftp_path_no> "._("No");

	// Allowing change of DNS and MX
	if($allow_dns_and_mx_change == "yes"){
		$allow_dns_and_mx_change_yes = " checked='checked' ";
		$allow_dns_and_mx_change_no = "";
	}else{
		$allow_dns_and_mx_change_yes = "";
		$allow_dns_and_mx_change_no = " checked='checked' ";
	}
	$allow_dns_and_mx_change_selector = "<input type=\"radio\" name=\"allow_dns_and_mx_change\" value=\"yes\"$allow_dns_and_mx_change_yes> "._("Yes")."
<input type=\"radio\" name=\"allow_dns_and_mx_change\" value=\"no\"$allow_dns_and_mx_change_no> "._("No");

	// Allow users to edit mailing lists
	if($allow_mailing_list_edit == "yes"){
		$allow_mailing_list_edit_yes = " checked='checked' ";
		$allow_mailing_list_edit_no = "";
	}else{
		$allow_mailing_list_edit_yes = "";
		$allow_mailing_list_edit_no = " checked='checked' ";
	}
	$allow_mailing_list_edit_selector = "<input type=\"radio\" name=\"allow_mailing_list_edit\" value=\"yes\"$allow_mailing_list_edit_yes> "._("Yes")."
<input type=\"radio\" name=\"allow_mailing_list_edit\" value=\"no\"$allow_mailing_list_edit_no> "._("No");

	// Allow users to edit subdomains
	if($allow_subdomain_edit == "yes"){
		$allow_subdomain_edit_yes = " checked='checked' ";
		$allow_subdomain_edit_no = "";
	}else{
		$allow_subdomain_edit_yes = "";
		$allow_subdomain_edit_no = " checked='checked' ";
	}
	$allow_subdomain_edit_selector = "<input type=\"radio\" name=\"allow_subdomain_edit\" value=\"yes\"$allow_subdomain_edit_yes> "._("Yes")."
<input type=\"radio\" name=\"allow_subdomain_edit\" value=\"no\"$allow_subdomain_edit_no> "._("No");

	// The shared hosting security popup
	switch($shared_hosting_security){
	case "mod_php":
		$shared_hosting_security_mod_php_sel = " selected ";
		$shared_hosting_security_sbox_copy_sel = "";
		$shared_hosting_security_sbox_aufs_sel = "";
		break;
	case "sbox_copy":
		$shared_hosting_security_mod_php_sel = "";
		$shared_hosting_security_sbox_copy_sel = " selected ";
		$shared_hosting_security_sbox_aufs_sel = "";
		break;
	case "sbox_aufs":
		$shared_hosting_security_mod_php_sel = "";
		$shared_hosting_security_sbox_copy_sel = "";
		$shared_hosting_security_sbox_aufs_sel = "selected";
		break;
	default:
		break;
	}
	$shared_hosting_security_popup = "<select name=\"shared_hosting_security\">
	<option value=\"mod_php\"$shared_hosting_security_mod_php_sel>mod_php</option>
	<option value=\"sbox_copy\"$shared_hosting_security_sbox_copy_sel>sbox_copy</option>
	<option value=\"sbox_aufs\"$shared_hosting_security_sbox_aufs_sel>sbox_aufs</option>
	</select>";

	// Generate the user configuration form
	$user_data = "
<form name=\"admattrbfrm\" action=\"?\" methode=\"post\">
<input type=\"hidden\" name=\"rub\" value=\"$rub\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"updateuserinfo\" value=\"Ok\">
".dtcFormTableAttrs();
	$genpass = autoGeneratePassButton("admattrbfrm","changed_pass");
	if ($conf_hide_password == "yes"){
		$ctrl = "<input class=\"dtcDatagrid_input_color\" type=\"password\" name=\"changed_pass\" value=\"$adm_cur_pass\">$genpass";
	} else {
		$ctrl = "<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"changed_pass\" value=\"$adm_cur_pass\">$genpass";
	}
	$user_data .= dtcFormLineDraw( _("Password:") ,$ctrl);

	// The product popup
	$q = "SELECT * FROM $pro_mysql_product_table WHERE (heb_type='shared' OR heb_type='custom' OR heb_type='ssl') AND renew_prod_id='0' ORDER BY id;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$prodsid = "";
	$prodsid .= "<select class=\"dtcDatagrid_input_color\" name=\"heb_prod_id\"><option value=\"0\">". _("No product") ."</option>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($a["id"] == $prod_id){
			$prodsid_sel = " selected ";
		}else{
			$prodsid_sel = " ";
		}
		$prodsid .= "<option value=\"".$a["id"]."\"$prodsid_sel>".$a["id"].": ".$a["name"]."</option>";
	}
	$prodsid .= "</select>";


	$user_data .= dtcFormLineDraw( _("Path:") ,"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"changed_path\" value=\"$adm_path\">",0);
	$user_data .= dtcFormLineDraw( _("Client ID:") ,"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"changed_id_client\" value=\"$adm_id_client\"><a href=\"?rub=crm&id=$adm_id_client\">"._("client")."</a>");
	$user_data .= dtcFormLineDraw( _("Disk quota (MB):") ,"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"adm_quota\" value=\"$adm_quota\">",0);
	$user_data .= dtcFormLineDraw( _("Allowed bandwidth per month (MB):") ,"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"bandwidth_per_month\" value=\"$bandwidth_per_month_mb\">");
	$user_data .= dtcFormLineDraw( _("Expiry date:") ,"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"expire\" value=\"$expire\">",0);
	$user_data .= dtcFormLineDraw( _("Product ID:") ,$prodsid);
	$user_data .= dtcFormLineDraw( _("Number of databases:") ,"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"nbrdb\" value=\"".$info["nbrdb"]."\">",0);
	$user_data .= dtcFormLineDraw( _("Allow to add domains:") ,$aldom_popup);
	$user_data .= dtcFormLineDraw( _("Max domain:") ,"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"max_domain\" value=\"$max_domain\">",0);
	$user_data .= dtcFormLineDraw( _("Grant sub-account addition rights (reseller):") ,$res_selector);
	$user_data .= dtcFormLineDraw( _("Allow addition of SSH logins:") ,$sshlog_selector,0);
	$user_data .= dtcFormLineDraw( _("Allow addition of FTP logins:") ,$ftplog_selector);
	$user_data .= dtcFormLineDraw( _("Restrict FTP to the html folder:") ,$restricted_ftp_path_selector,0);
	$user_data .= dtcFormLineDraw( _("Allow edition of mailing lists and mail groups:") ,$allow_mailing_list_edit_selector);
	$user_data .= dtcFormLineDraw( _("Allow edition of DNS and MX:") ,$allow_dns_and_mx_change_selector,0);
	$user_data .= dtcFormLineDraw( _("Allow edition of subdomains:") ,$allow_subdomain_edit_selector);
	$user_data .= dtcFormLineDraw( _("Allow the use of the package installer:") ,$pkg_install_selector,0);
	$user_data .= dtcFormLineDraw( _("Shared hosting security:"),$shared_hosting_security_popup);
	$user_data .= dtcFromOkDraw()."</table></form>";

	// Generate the admin tool configuration module
	// Deletion of domains :
	$url = "?delete_admin_user=$adm_login&rub=$rub";
	$confirmed_url = dtcJavascriptConfirmLink( _("Are your sure you want to delete this user? This will DELETE all the user hosted domain names, files, and databases.") ,$url);
	$domain_conf = "<a href=\"$confirmed_url\"><b>". _("Delete the user") ."</b></a><br><br>";
	if(isset($data)){
		$domain_conf .= "<h3>". _("Delete a user domain:") ."</h3><br>";
		$nbr_domain = sizeof($data);
		for($i=0;$i<$nbr_domain;$i++){
			$dom = $data[$i]["name"];
			if($i != 0){
				$domain_conf .= " - ";
			}
			$url = "?adm_login=$adm_login&adm_pass=$adm_pass&deluserdomain=$dom&rub=$rub";
			$js_url = dtcJavascriptConfirmLink( _("Are you sure you want to delete this domain name? This will DELETE all hosted files for this domain.") ,$url);
			$domain_conf .= "<a href=\"$js_url\">$dom</a>";
		}
		$domain_conf .= "</b><br><br>";
	}
	// Creation of domains :
	$domain_conf .= "<h3>". _("Add a domain for this user:") ."</h3>";

	$domain_conf .= "<form action=\"?\"><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr><td><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"rub\" value=\"$rub\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"text\" name=\"newdomain_name\" value=\"\"></td>
	<td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"newdomain\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr></table>
	</form>";

	$domain_conf .= "<h3>". _("Import a domain file for this user:") ."<h3></b>
	<form action=\"?\" enctype=\"multipart/form-data\" method=\"post\">
	<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
	<tr><td><input type=\"hidden\" name=\"rub\" value=\"$rub\">
	<input type=\"hidden\" name=\"action\" value=\"import_domain\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"30000000\">
	<input type=\"file\" name=\"domain_import_file\" size=\"30\"></td>
	<td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"". _("Import") ."\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr></table></form>";

	// Deletion of VPS
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE owner='$adm_login' ORDER BY vps_server_hostname,vps_xen_name;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$domain_conf .= "<h3>". _("Delete one of the admin VPS: ") ."</h3><br>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			if($i > 0){
				$domain_conf .= " - ";
			}
			$delete_vps_url = dtcJavascriptConfirmLink( _("Are you sure you want to delete this VPS? This will also delete the partitions!"),"?adm_login=$adm_login&adm_pass=$adm_pass&rub=$rub&action=delete_a_vps&id=".$a["id"]);
			$domain_conf .= "<a href=\"".$delete_vps_url."\"><b>".$a["vps_server_hostname"].":".$a["vps_xen_name"]."</b></a>";
		}
		$domain_conf .= "<br><br>";
	}

	// Creation of VPS
	$q = "SELECT * FROM $pro_mysql_product_table WHERE heb_type='vps' AND renew_prod_id='0';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$num_prods_vps = $n;
	$vps_prods = "";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$vps_prods .= "<option value=\"".$a["id"]."\">".$a["name"]."</option>";
	}

	$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE available='yes' ORDER BY vps_server_hostname,vps_xen_name;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$vps_srvs = "";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$vps_srvs .= "<option value=\"".$a["ip_addr"]."\">".$a["vps_server_hostname"].":".$a["vps_xen_name"]." (".$a["ip_addr"].")</option>";
	}
	if($n > 0 && $num_prods_vps > 0){
		$domain_conf .= "<h3>". _("Add a VPS for this admin:") ."</h3>
		<form action=\"?\">
		<input type=\"hidden\" name=\"rub\" value=\"$rub\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		<input type=\"hidden\" name=\"action\" value=\"add_vps_to_user\">
		<table border=\"0\">
		<tr><td style=\"text-align: right; white-space: nowrap;\">". _("VPS Server hostname: ") ."</td>
		<td><select name=\"vps_server_ip\">$vps_srvs</select></td></tr>
		<tr><td style=\"text-align: right; white-space: nowrap;\">". _("Product: ") ."</td>
		<td><select name=\"product_id\">$vps_prods</select></td></tr>
		<tr><td style=\"text-align: right; white-space: nowrap;\">". _("Setup physical VPS (LVM): ") ."</td>
		<td><input type=\"radio\" name=\"physical_setup\" value=\"yes\">". _("Yes") ."
		<input type=\"radio\" name=\"physical_setup\" value=\"no\" checked='checked'>". _("No") ."</td></tr>
		<tr><td></td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"Add VPS\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr></table></form>";
	}else{
		$domain_conf .= _("To add a VPS, you need to add IPs to the VPS IPs section in the general config as well as create at least one VPS product.");
	}

	// Deletion of dedicated
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE owner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$domain_conf .= "<br><br><h3>". _("Delete one of the admin dedicated server:") ."</h3><br>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			if($i > 0){
				$domain_conf .= " - ";
			}
			$domain_conf .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=$rub&action=delete_a_dedicated&id=".$a["id"]."\"><b>".$a["server_hostname"]."</b></a>";
		}
	}
	// Creation of dedicated servers
	$q = "SELECT * FROM $pro_mysql_product_table WHERE heb_type='server' AND renew_prod_id='0';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$num_prods_vps = $n;
	$server_prods = "";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$server_prods .= "<option value=\"".$a["id"]."\">".$a["name"]."</option>";
	}
	$domain_conf .= "<br><br><h3>". _("Add a dedicated server for this admin:") ."</h3>
	<form action=\"?\">
	<input type=\"hidden\" name=\"rub\" value=\"$rub\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"action\" value=\"add_dedicated_to_user\">
	<table border=\"0\">
	<tr><td style=\"text-align: right; white-space: nowrap;\">". _("Product: ")."</td>
		<td><select name=\"product_id\">$server_prods</select></td></tr>
	<tr><td style=\"text-align: right; white-space: nowrap;\">". _("Hostname: ") ."</td>
		<td><input type=\"text\" name=\"server_hostname\" value=\"\"></td>
	<tr><td style=\"text-align: right; white-space: nowrap;\">". _("Country: ") ."</td>
		<td><select name=\"country\">$cc_code_popup</select></td>
	<tr><td></td><td>".dtcApplyButton()."</td></tr></table></form>";

	$out = "<font size=\"-1\">
<table>
 <tr>
  <td>$domain_conf</td><td background=\"gfx/border_2.gif\">&nbsp;</td>
  <td>$user_data</td>
 </tr>
</table>
</font>
";
	return $out;
}
/////////////////////////////////////////////////////////////////
// Generate a tool for configuring all domain of one sub-admin //
/////////////////////////////////////////////////////////////////
function drawDomainConfig($admin){
	global $rub;
	global $cc_code_array;

	global $pro_mysql_product_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_subdomain_table;

	global $conf_site_addrs;
	global $conf_use_shared_ssl;

	$site_addrs = explode("|",$conf_site_addrs);

	global $adm_login;
	global $adm_pass;

	$ret = "";

	if(isset($admin["data"])){
		$domains = $admin["data"];
		$nbr_domain = sizeof($domains);
	}else{
		$nbr_domain = 0;
	}

	// Shared hosting domain configuration
	if($nbr_domain > 0){
		if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_domain_config_edit"){
			$q = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='".$_REQUEST["name"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
		}
		$dsc = array(
			"table_name" => $pro_mysql_domain_table,
			"title" => _("Domain Configuration"),
			"action" => "change_domain_config",
			"forward" => array("rub","adm_login","adm_pass"),
			"skip_deletion" => "yes",
			"skip_creation" => "yes",
			"where_condition" => "owner='$adm_login'",
			"cols" => array(
				"name" => array(
					"type" => "id",
					"display" => "yes",
					"legend" => _("Domain name")),
				"edithost" => array(
					"type" => "hyperlink",
					"legend" => _("Vhost"),
					"text" => _("Customize") ),
				"safe_mode" => array(
					"type" => "checkbox",
					"help" => _("This will add a new subdomain switch yes/no in the client interface of this domain. Unticking this checkbox is NOT ENOUGH to disable the safe mode. Please go in the subdomains section of this domain name to finish the setup."),
					"legend" => _("PHP safe_mode"),
					"values" => array("yes","no"),
					"display_replace" => array( _("No") , _("Yes") )),
				"sbox_protect" => array(
					"type" => "checkbox",
					"help" => _("This will add a new subdomain switch yes/no in the client interface of this domain. Unticking this checkbox is NOT ENOUGH to disable the sbox CGI-BIN protection. Please go in the subdomains section of this domain name to finish the setup."),
					"legend" => _("CGI-BIN protection") ,
					"values" => array("yes","no"),
					"display_replace" => array(_("No"),_("Yes"))),
				"quota" => array(
					"type" => "text",
					"help" => _("Quota disk in MBytes"),
					"legend" => _("Disk quota") ,
					"size" => "6"),
				"max_email" => array(
					"type" => "text",
					"legend" => _("Email max") ,
					"size" => "3"),
				"max_lists" => array(
					"type" => "text",
					"legend" => _("Lists max") ,
					"size" => "3"),
				"max_ftp" => array(
					"type" => "text",
					"legend" => _("Max FTP") ,
					"size" => "3"),
				"max_subdomain" => array(
					"type" => "text",
					"legend" => _("Subdomain max") ,
					"size" => "3"),
				"max_ssh" => array(
					"type" => "text",
					"legend" => _("Max SSH") ,
					"size" => "3"),
				"ip_addr" => array(
					"type" => "popup",
					"legend" => _("IP address") ,
					"values" => $site_addrs),
				"expiration_date" => array(
					"type" => "text",
					"legend" => _("Expiration") ,
					"size" => "6"),
				"backup_ip_addr" => array(
					"type" => "text",
					"legend" => _("Backup Vhost IP address") ,
					"size" => "14")
				)
			);
		$ret .= dtcDatagrid($dsc);
		if( isset($_REQUEST["edithost"]) && isHostname($_REQUEST["edithost"]) ){
			$ret .= "<h3>". _("Custom Apache directives for") ." ".$_REQUEST["edithost"]."</h3>";
			$q = "SELECT subdomain_name FROM $pro_mysql_subdomain_table WHERE domain_name='".$_REQUEST["edithost"]."';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			for($j=0;$j<$n;$j++){
				$a = mysql_fetch_array($r);
				if($j != 0){
					$ret .= " - ";
				}
				$subname = $a["subdomain_name"];
				$ret .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=$rub&edithost=".$_REQUEST["edithost"]."&subdomain=$subname\">$subname</a>";
			}
			$ret .= "<br><br>";
			if( isset($_REQUEST["subdomain"]) && isHostname($_REQUEST["subdomain"]) ){
				$ret .= "<u>". _("Subdomain") .": ".$_REQUEST["subdomain"].":</u><br>";
				$ret .= _("IMPORTANT: No syntax checking is done on your custom directives - a mistake here could lead to your web server not restarting properly.")."<br>";
				$q = "SELECT customize_vhost FROM $pro_mysql_subdomain_table WHERE subdomain_name='".$_REQUEST["subdomain"]."' AND domain_name='".$_REQUEST["edithost"]."';";
				$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1){
					die("Domain name not found line ".__LINE__." file ".__FILE__);
				}
				$ze_dom = mysql_fetch_array($r);
				$customization = $ze_dom["customize_vhost"];
				/*$ret .= "<form action=\"?\">
				<input type=\"hidden\" name=\"rub\" value=\"$rub\">
				<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
				<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
				<input type=\"hidden\" name=\"edithost\" value=\"".$_REQUEST["edithost"]."\">
				<input type=\"hidden\" name=\"subdomain\" value=\"".$_REQUEST["subdomain"]."\">
				<input type=\"hidden\" name=\"action\" value=\"set_vhost_custom_directives\">
				<textarea cols=\"120\" rows=\"10\" name=\"custom_directives\">$customization</textarea><br>
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div>
				</form><br><br><br>";*/

		$cols=array(	"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => _("ID")),
				"customize_vhost" => array(
					"type"=> "textarea",
					"help"=> _("Custom apache directives. NOTE - There is *no* syntax checking on this field."),
					"cols"=> "40",
					"rows"=> "30",
					"legend" => _("Custom apache directives")),
				"redirect_url" => array(
					"type" => "text",
					"help" => _("Redirect URL"),
					"size" => 50,
					"legend" => _("Redirect to:")),
				"php_memory_limit" => array(
					"type" => "text",
					"help" => _("Maximum memory used by PHP session"),
					"size" => 3,
					"legend" => _("PHP memory limit")),
				"php_max_execution_time" => array(
					"type" => "text",
					"help" => _("Maximum time a PHP script can execute"),
					"size" => 3,
					"legend" => _("Execution time")),
				"php_upload_max_filesize" => array(
					"type" => "text",
					"help" => _("Maximum uploaded file size"),
					"size" => 2,
					"legend" => _("Max upload file size")),
				"php_post_max_size" => array(
					"type" => "text",
					"help" => _("Maximum POST data size"),
					"size" => 2,
					"legend" => _("Max POST file size")),
				"php_session_auto_start" => array(
					"type" => "checkbox",
					"help" => _("Auto start of php sessions"),
					"size" => 2,
					"legend" => _("Session autostart"),
					"values" => array("yes","no"),
					"display_replace" => array( _("No") , _("Yes") )),
				"php_allow_url_fopen" => array(
					"type" => "checkbox",
					"help" => _("Allows to open URLs with PHP's fopen() function."),
					"size" => 2,
					"legend" => _("Allow URL fOpen()"),
					"values" => array("yes","no"),
					"display_replace" => array( _("No") , _("Yes") ))
		);
		if ($conf_use_shared_ssl=="yes") {
		    $cols["use_shared_ssl"] = array(
					"type" => "checkbox",
					"help" => _("Use a shared SSL certificate for this subdomain."),
					"size" => 2,
					"legend" => _("SSL"),
					"values" => array("yes","no"),
					"display_replace" => array( _("No") , _("Yes") ));
		}



		$dsc = array(
			"table_name" => $pro_mysql_subdomain_table,
			"title" => _("Configuration of the subdomain"),
			"action" => "change_domain_config",
			"forward" => array("subdomain","edithost","rub","adm_login","adm_pass"),
			"skip_deletion" => "yes",
			"skip_creation" => "yes",
			"where_condition" => "subdomain_name='".$_REQUEST["subdomain"]."' AND domain_name='".$_REQUEST["edithost"]."'",
			"cols" => $cols
			);
			
			$ret.=dtcDatagrid($dsc);
				
				
			}
		}
	}

	// VPS configuration
	if(isset($admin["vps"])){
		$vpses = $admin["vps"];
		$nbr_vps = sizeof($vpses);
	}else{
		$nbr_vps = 0;
	}

	if($nbr_vps > 0){
		$q = "SELECT id,name FROM $pro_mysql_product_table WHERE heb_type='vps' AND renew_prod_id='0';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$prod_name = array();
		$prod_id = array();
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$prod_name[] = $a["name"];
			$prod_id[] = $a["id"];
		}
		$dsc = array(
			"table_name" => $pro_mysql_vps_table,
			"title" => _("Configuration of the VPSes") ,
			"action" => "change_vps_config",
			"forward" => array("rub","adm_login","adm_pass"),
			"skip_deletion" => "yes",
			"skip_creation" => "yes",
			"where_condition" => "owner='$adm_login'",
			"order_by" => "vps_server_hostname,vps_xen_name",
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"vps_server_hostname" => array(
					"type" => "info",
					"legend" => _("VPS Server") ),
				"vps_xen_name" => array(
					"type" => "info",
					"legend" => _("VPS Name") ),
				"start_date" => array(
					"type" => "text",
					"size" => "10",
					"help" => _("Format: YYYY-MM-DD."),
					"legend" => _("Registration") ),
				"expire_date" => array(
					"type" => "text",
					"help" => _("Format: YYYY-MM-DD."),
					"size" => "10",
					"legend" => _("Expiration") ),
				"hddsize" => array(
					"type" => "text",
					"help" => _("Hard drive space in MBytes. You will need to manually do a lvresize on the dom0 of your VPS server to activate the changes."),
					"size" => "5",
					"legend" => "HDD"),
				"ramsize" => array(
					"type" => "text",
					"help" => _("Memory size in MBytes. You will need to manually change the RAM size in the /etc/xen/xenXX startup configuration file and reboot the VPS to activate the changes."),
					"size" => "5",
					"legend" => "RAM"),
				"bandwidth_per_month_gb" => array(
					"type" => "text",
					"size" => "5",
					"help" => _("Bandwidth per month in MBytes."),
					"legend" => _("Bandwidth") ),
				"locked" => array(
					"type" => "checkbox",
					"values" => array("yes","no"),
					"display_replace" => array(_("Yes"),_("No")),
					"legend" => _("Locked") ),
				"product_id" => array(
					"type" => "popup",
					"legend" => _("Product ID") ,
					"values" => $prod_id,
					"display_replace" => $prod_name)
				));
		$ret .= dtcDatagrid($dsc);
	}

	// Dedicated servers configuration
	if(isset($admin["dedicated"])){
		$servers = $admin["dedicated"];
		$nbr_server = sizeof($servers);
	}else{
		$nbr_server = 0;
	}
	if($nbr_server > 0){
		$q = "SELECT id,name FROM $pro_mysql_product_table WHERE heb_type='server' AND renew_prod_id='0';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$prod_name = array();
		$prod_id = array();
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$prod_name[] = $a["name"];
			$prod_id[] = $a["id"];
		}

		$dsc = array(
			"table_name" => $pro_mysql_dedicated_table,
			"title" => "",
			"action" => _("Configuration of the dedicated servers") ,
			"forward" => array("rub","adm_login","adm_pass"),
			"skip_deletion" => "yes",
			"skip_creation" => "yes",
			"where_condition" => "owner='$adm_login'",
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"server_hostname" => array(
					"type" => "text",
					"legend" => _("Server name") ),
				"start_date" => array(
					"type" => "text",
					"help" => _("Format: YYYY-MM-DD."),
					"size" => "10",
					"legend" => _("Registration") ),
				"expire_date" => array(
					"type" => "text",
					"help" => _("Format: YYYY-MM-DD."),
					"size" => "10",
					"legend" => _("Expiration") ),
				"hddsize" => array(
					"type" => "text",
					"help" => _("Hard drive size in MBytes."),
					"size" => "5",
					"legend" => "HDD"),
				"ramsize" => array(
					"type" => "text",
					"help" => _("Memory size in MBytes."),
					"size" => "5",
					"legend" => "RAM"),
				"bandwidth_per_month_gb" => array(
					"type" => "text",
					"help" => _("Bandwidth per month in GBytes."),
					"size" => "5",
					"legend" => _("Bandwidth per month") ),
				"country_code" => array(
					"type" => "popup",
					"legend" => _("Country") ,
					"values" => array_keys($cc_code_array),
					"display_replace" => array_values($cc_code_array)),
				"product_id" => array(
					"type" => "popup",
					"legend" => _("Product") ,
					"values" => $prod_id,
					"display_replace" => $prod_name)
				));
		$ret .= dtcDatagrid($dsc);
	}
	return $ret;
}

?>
