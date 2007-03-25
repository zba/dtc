<?php
require("$dtcshared_path/inc/forms/root_admin_strings.php");

////////////////////////////////////////////////////////////////////////////
// Draw the form for configuring global admin account info (path, etc...) //
////////////////////////////////////////////////////////////////////////////
function drawEditAdmin($admin){
	global $lang;

	global $pro_mysql_vps_server_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_product_table;
	global $pro_mysql_dedicated_table;
	global $cc_code_popup;

	global $txt_password;

	global $txt_path;
	global $txt_id_client;
	global $txt_del_user;
	global $txt_del_user_confirm;
	global $txt_del_user_domain;
	global $txt_del_user_domain_confirm;
	global $txt_new_domain_for_user;
	global $txt_allowed_data_transferMB;
	global $txt_domain_tbl_config_quotaMB;
	global $txt_allowed_data_transferMB;
	global $txt_expiration_date;
	global $txt_heb_prod_id;
	global $txt_can_have_subadmins_reseller;
	global $txt_can_have_ssh_login_for_vhosts;
	global $txt_allow_to_add_domains;
	global $txt_number_of_database;
	global $txt_import_a_domain_for_this_user;
	global $txt_import_button;

	global $txt_root_admin_no_product;
	global $txt_root_admin_configuration_of_the_vpses;

	global $adm_login;
	global $adm_pass;
	global $txt_no;
	global $txt_yes;
	global $rub;

	global $conf_hide_password;
	global $txt_root_admin_vps_server_hostname;
	global $txt_root_admin_delete_one_of_the_admin_vps;
	global $txt_root_admin_to_add_a_vps_you_have_to;
	global $txt_root_admin_delete_one_of_the_admin_dedicated_server;
	global $txt_root_admin_add_a_dedicated_server_to_admin;

	global $txt_root_admin_country;
	global $txt_root_admin_hostname;
	global $txt_root_admin_product;

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
	$resseller_flag = $info["resseller_flag"];
	$ssh_login_flag = $info["ssh_login_flag"];

	if($resseller_flag == "yes"){
		$resflag_yes = " checked ";
		$resflag_no = "";
	}else{
		$resflag_yes = " ";
		$resflag_no = " checked ";
	}
	$res_selector = "<input type=\"radio\" name=\"resseller_flag\" value=\"yes\"$resflag_yes> ".$txt_yes[$lang]."
	<input type=\"radio\" name=\"resseller_flag\" value=\"no\"$resflag_no> ".$txt_no[$lang]."</div>";

	if($ssh_login_flag == "yes"){
		$sshlogin_yes = " checked ";
		$sshlogin_no = "";
	}else{
		$sshlogin_yes = "";
		$sshlogin_no = " checked ";
	}
	$sshlog_selector = "<input type=\"radio\" name=\"ssh_login_flag\" value=\"yes\"$sshlogin_yes> ".$txt_yes[$lang]."
	<input type=\"radio\" name=\"ssh_login_flag\" value=\"no\"$sshlogin_no> ".$txt_no[$lang];

	if($allow_add_domain == "yes")	$adyes = "selected";	else $adyes = "";
	if($allow_add_domain == "check")$adcheck = "selected";	else $adcheck = "";
	if($allow_add_domain == "no")	$adno = "selected";	else $adno = "";
	$aldom_popup = "<select class=\"dtcDatagrid_input_color\" name=\"allow_add_domain\">
<option value=\"yes\" $adyes>Yes</option>
<option value=\"check\" $adcheck>Check</option>
<option value=\"no\" $adno>No</option>
</select>
";

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
		$ctrl = "<input type=\"text\" name=\"changed_pass\" value=\"$adm_cur_pass\">$genpass";
	}
	$user_data .= dtcFormLineDraw($txt_password[$lang],$ctrl);

	// The product popup
	$q = "SELECT * FROM $pro_mysql_product_table WHERE (heb_type='shared' OR heb_type='ssl') AND renew_prod_id='0' ORDER BY id;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$prodsid = "";
	$prodsid .= "<select class=\"dtcDatagrid_input_color\" name=\"heb_prod_id\"><option value=\"0\">".$txt_root_admin_no_product[$lang]."</option>";
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

	$user_data .= dtcFormLineDraw($txt_path[$lang],"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"changed_path\" value=\"$adm_path\">",0);
	$user_data .= dtcFormLineDraw($txt_id_client[$lang],"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"changed_id_client\" value=\"$adm_id_client\"><a href=\"?rub=crm&id=$adm_id_client\">client</a>");
	$user_data .= dtcFormLineDraw($txt_domain_tbl_config_quotaMB[$lang],"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"adm_quota\" value=\"$adm_quota\">",0);
	$user_data .= dtcFormLineDraw($txt_allowed_data_transferMB[$lang],"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"bandwidth_per_month\" value=\"$bandwidth_per_month_mb\">");
	$user_data .= dtcFormLineDraw($txt_expiration_date[$lang],"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"expire\" value=\"$expire\">",0);
	$user_data .= dtcFormLineDraw($txt_heb_prod_id[$lang],$prodsid);
	$user_data .= dtcFormLineDraw($txt_number_of_database[$lang],"<input class=\"dtcDatagrid_input_alt_color\" type=\"text\" name=\"nbrdb\" value=\"".$info["nbrdb"]."\">",0);
	$user_data .= dtcFormLineDraw($txt_allow_to_add_domains[$lang],$aldom_popup);
	$user_data .= dtcFormLineDraw($txt_can_have_subadmins_reseller[$lang],$res_selector,0);
	$user_data .= dtcFormLineDraw($txt_can_have_ssh_login_for_vhosts[$lang],$sshlog_selector);
	$user_data .= dtcFromOkDraw()."</table></form>";
/*	$user_data .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"updateuserinfo\" value=\"Ok\">
</td></tr></table></form>";*/

	// Generate the admin tool configuration module
	// Deletion of domains :
	$url = "".$_SERVER["PHP_SELF"]."?delete_admin_user=$adm_login&rub=$rub";
	$confirmed_url = dtcJavascriptConfirmLink($txt_del_user_confirm[$lang],$url);
	$domain_conf = "<a href=\"$confirmed_url\"><b>".$txt_del_user[$lang]."</b></a><br><br>";
	if(isset($data)){
		$domain_conf .= "<h3>".$txt_del_user_domain[$lang]."</h3><br>";
		$nbr_domain = sizeof($data);
		for($i=0;$i<$nbr_domain;$i++){
			$dom = $data[$i]["name"];
			if($i != 0){
				$domain_conf .= " - ";
			}
			$url = "?adm_login=$adm_login&adm_pass=$adm_pass&deluserdomain=$dom&rub=$rub";
			$js_url = dtcJavascriptConfirmLink($txt_del_user_domain_confirm[$lang],$url);
			$domain_conf .= "<a href=\"$js_url\">$dom</a>";
		}
		$domain_conf .= "</b><br><br>";
	}
	// Creation of domains :
	$domain_conf .= "<h3>".$txt_new_domain_for_user[$lang]."</h3>";

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

	$domain_conf .= "<h3>".$txt_import_a_domain_for_this_user[$lang]."<h3></b>
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
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"".$txt_import_button[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr></table></form>";

	// Deletion of VPS
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE owner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$domain_conf .= "<h3>".$txt_root_admin_delete_one_of_the_admin_vps[$lang]."</h3><br>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			if($i > 0){
				$domain_conf .= " - ";
			}
			$domain_conf .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=$rub&action=delete_a_vps&id=".$a["id"]."\"><b>".$a["vps_server_hostname"].":".$a["vps_xen_name"]."</b></a>";
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

	$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE available='yes' ORDER BY vps_server_hostname;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$vps_srvs = "";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$vps_srvs .= "<option value=\"".$a["ip_addr"]."\">".$a["vps_server_hostname"].":".$a["vps_xen_name"]." (".$a["ip_addr"].")</option>";
	}
	if($n > 0 && $num_prods_vps > 0){
		$domain_conf .= "<h3>".$txt_root_admin_add_a_vps_for_this_admin[$lang]."</h3>
		<form action=\"?\">
		<input type=\"hidden\" name=\"rub\" value=\"$rub\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		<input type=\"hidden\" name=\"action\" value=\"add_vps_to_user\">
		<table border=\"0\">
		<tr><td style=\"text-align: right; white-space: nowrap;\">".$txt_root_admin_vps_server_hostname[$lang]."</td>
		<td><select name=\"vps_server_ip\">$vps_srvs</select></td></tr>
		<tr><td style=\"text-align: right; white-space: nowrap;\">Product:</td>
		<td><select name=\"product_id\">$vps_prods</select></td></tr>
		<tr><td style=\"text-align: right; white-space: nowrap;\">Setup physical VPS (LVM):</td>
		<td><input type=\"radio\" name=\"physical_setup\" value=\"yes\">Yes
		<input type=\"radio\" name=\"physical_setup\" value=\"no\" checked>No</td></tr>
		<tr><td></td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"Add VPS\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr></table></form>";
	}else{
		$domain_conf .= $txt_root_admin_to_add_a_vps_you_have_to[$lang];
	}

	// Deletion of dedicated
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE owner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$domain_conf .= "<br><br><h3>".$txt_root_admin_delete_one_of_the_admin_dedicated_server[$lang]."</h3><br>";
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
	$domain_conf .= "<br><br><h3>".$txt_root_admin_add_a_dedicated_server_to_admin[$lang]."</h3>
	<form action=\"?\">
	<input type=\"hidden\" name=\"rub\" value=\"$rub\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"action\" value=\"add_dedicated_to_user\">
	<table border=\"0\">
	<tr><td style=\"text-align: right; white-space: nowrap;\">".$txt_root_admin_product[$lang]."</td>
		<td><select name=\"product_id\">$server_prods</select></td></tr>
	<tr><td style=\"text-align: right; white-space: nowrap;\">".$txt_root_admin_hostname[$lang]."</td>
		<td><input type=\"text\" name=\"server_hostname\" value=\"\"></td>
	<tr><td style=\"text-align: right; white-space: nowrap;\">".$txt_root_admin_country[$lang]."</td>
		<td><select name=\"country\">$cc_code_popup</select></td>
	<tr><td></td><td>".dtcApplyButton()."</td></tr></table></form>";

	$out = "<font size=\"-1\">
<table>
 <tr>
  <td>$domain_conf</td><td background=\"gfx/skin/frame/border_2.gif\">&nbsp;</td>
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
	global $lang;
	global $rub;
	global $txt_domain_tbl_config_dom_name;
	global $txt_domain_tbl_config_quota;
	global $txt_domain_tbl_config_max_email;
	global $txt_domain_tbl_config_max_ftp;
	global $txt_domain_tbl_config_max_subdomain;
	global $txt_domain_tbl_config_ip;
	global $txt_domain_tbl_config_backup_ip;
	global $txt_domain_tbl_config_max_lists;
	global $txt_root_admin_country2;
	global $cc_code_array;

	global $pro_mysql_product_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_dedicated_table;

	global $conf_site_addrs;
	$site_addrs = explode("|",$conf_site_addrs);

	global $adm_login;
	global $adm_pass;
	global $txt_yes;
	global $txt_no;
	global $txt_root_admin_max_ssh;
	global $txt_root_admin_cgi_bin_protection;
	global $txt_root_admin_configuration_of_the_vpses;

	global $txt_root_admin_product;
	global $txt_root_admin_domain_config_table_title;
	global $txt_root_admin_country;
	global $txt_root_admin_vps_name;
	global $txt_root_admin_vps_server;
	global $txt_root_admin_registration;
	global $txt_root_admin_expiration;
	global $txt_root_admin_configuration_of_the_dedicated_servers;
	global $txt_root_admin_server_name;

	$ret = "";

	if(isset($admin["data"])){
		$domains = $admin["data"];
		$nbr_domain = sizeof($domains);
	}else{
		$nbr_domain = 0;
	}

	if($nbr_domain > 0){
		if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_domain_config_edit"){
			$q = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='".$_REQUEST["name"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
		}

		$dsc = array(
			"table_name" => $pro_mysql_domain_table,
			"title" => $txt_root_admin_domain_config_table_title[$lang],
			"action" => "change_domain_config",
			"forward" => array("rub","adm_login","adm_pass"),
			"skip_deletion" => "yes",
			"skip_creation" => "yes",
			"where_condition" => "owner='$adm_login'",
			"cols" => array(
				"name" => array(
					"type" => "id",
					"display" => "yes",
					"legend" => $txt_domain_tbl_config_dom_name[$lang]),
				"safe_mode" => array(
					"type" => "checkbox",
					"legend" => "PHP safe_mode",
					"values" => array("yes","no"),
					"display_replace" => array($txt_no[$lang],$txt_yes[$lang])),
				"sbox_protect" => array(
					"type" => "checkbox",
					"legend" => $txt_root_admin_cgi_bin_protection[$lang],
					"values" => array("yes","no"),
					"display_replace" => array($txt_no[$lang],$txt_yes[$lang])),
				"quota" => array(
					"type" => "text",
					"legend" => $txt_domain_tbl_config_quota[$lang],
					"size" => "6"),
				"max_email" => array(
					"type" => "text",
					"legend" => $txt_domain_tbl_config_max_email[$lang],
					"size" => "3"),
				"max_lists" => array(
					"type" => "text",
					"legend" => $txt_domain_tbl_config_max_lists[$lang],
					"size" => "3"),
				"max_ftp" => array(
					"type" => "text",
					"legend" => $txt_domain_tbl_config_max_ftp[$lang],
					"size" => "3"),
				"max_subdomain" => array(
					"type" => "text",
					"legend" => $txt_domain_tbl_config_max_subdomain[$lang],
					"size" => "3"),
				"max_ssh" => array(
					"type" => "text",
					"legend" => $txt_root_admin_max_ssh[$lang],
					"size" => "3"),
				"ip_addr" => array(
					"type" => "popup",
					"legend" => $txt_domain_tbl_config_ip[$lang],
					"values" => $site_addrs),
				"backup_ip_addr" => array(
					"type" => "text",
					"legend" => $txt_domain_tbl_config_backup_ip[$lang],
					"size" => "14")
				)
			);
		$ret .= dtcDatagrid($dsc);
	}

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
			$prod_id = $a["id"];			
		}
		$dsc = array(
			"table_name" => $pro_mysql_vps_table,
			"title" => $txt_root_admin_configuration_of_the_vpses[$lang],
			"action" => "change_vps_config",
			"forward" => array("rub","adm_login","adm_pass"),
			"skip_deletion" => "yes",
			"skip_creation" => "yes",
			"where_condition" => "owner='$adm_login'",
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"vps_server_hostname" => array(
					"type" => "info",
					"legend" => $txt_root_admin_vps_server[$lang]),
				"vps_xen_name" => array(
					"type" => "info",
					"legend" => $txt_root_admin_vps_name[$lang]),
				"start_date" => array(
					"type" => "text",
					"size" => "10",
					"legend" => $txt_root_admin_registration[$lang]),
				"expire_date" => array(
					"type" => "text",
					"size" => "10",
					"legend" => $txt_root_admin_expiration[$lang]),
				"hddsize" => array(
					"type" => "text",
					"size" => "5",
					"legend" => "HDD"),
				"ramsize" => array(
					"type" => "text",
					"size" => "5",
					"legend" => "RAM"),
				"product_id" => array(
					"type" => "popup",
					"legend" => $txt_root_admin_product[$lang],
					"values" => $prod_id,
					"display_replace" => $prod_name)
				));
		$ret .= dtcDatagrid($dsc);
	}
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
			"action" => $txt_root_admin_configuration_of_the_dedicated_servers[$lang],
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
					"type" => "info",
					"legend" => $txt_root_admin_server_name[$lang]),
				"start_date" => array(
					"type" => "text",
					"size" => "10",
					"legend" => $txt_root_admin_registration[$lang]),
				"expire_date" => array(
					"type" => "text",
					"size" => "10",
					"legend" => $txt_root_admin_expiration[$lang]),
				"hddsize" => array(
					"type" => "text",
					"size" => "5",
					"legend" => "HDD"),
				"ramsize" => array(
					"type" => "text",
					"size" => "5",
					"legend" => "RAM"),
				"country_code" => array(
					"type" => "popup",
					"legend" => $txt_root_admin_country2[$lang],
					"values" => array_keys($cc_code_array),
					"display_replace" => array_values($cc_code_array)),
				"product_id" => array(
					"type" => "popup",
					"legend" => $txt_root_admin_product[$lang],
					"values" => $prod_id,
					"display_replace" => $prod_name)
				));
		$ret .= dtcDatagrid($dsc);
	}
	return $ret;
}

?>
