<?php

////////////////////////////////////////////////////////////////////////////
// Draw the form for configuring global admin account info (path, etc...) //
////////////////////////////////////////////////////////////////////////////
function drawEditAdmin($admin){
	global $lang;
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
	global $txt_allow_to_add_domains;
	global $txt_number_of_database;

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
	$resseller_flag = $info["resseller_flag"];

	if($resseller_flag == "yes"){
		$resflag_yes = " checked ";
		$resflag_no = "";
	}else{
		$resflag_yes = " ";
		$resflag_no = " checked ";
	}
	$res_selector = "<input type=\"radio\" name=\"resseller_flag\" value=\"yes\"$resflag_yes> Yes
	<input type=\"radio\" name=\"resseller_flag\" value=\"no\"$resflag_no> No";

	if($allow_add_domain == "yes")	$adyes = "selected";	else $adyes = "";
	if($allow_add_domain == "check")$adcheck = "selected";	else $adcheck = "";
	if($allow_add_domain == "no")	$adno = "selected";	else $adno = "";
	$aldom_popup = "<select name=\"allow_add_domain\">
<option name=\"yes\" $adyes>Yes</option>
<option name=\"check\" $adcheck>Check</option>
<option name=\"no\" $adno>No</option>
</select>
";

	// Generate the user configuration form
	$user_data = "
<form action=\"?\" methode=\"post\">
<input type=\"hidden\" name=\"rub\" value=\"$rub\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<table>
	<tr><td align=\"right\">".$txt_password[$lang]."</td>";
	if ($conf_hide_password == "yes"){
		$user_data .= "<td><input type=\"password\" name=\"changed_pass\" value=\"$adm_cur_pass\"></td></tr>";
	} else {
		$user_data .= "<td><input type=\"text\" name=\"changed_pass\" value=\"$adm_cur_pass\"></td></tr>";
	}
	$user_data .= "<tr><td align=\"right\">".$txt_path[$lang]."</td>
	<td><input type=\"text\" name=\"changed_path\" value=\"$adm_path\"></td></tr>
	<tr><td align=\"right\">".$txt_id_client[$lang]."</td>
	<td><input type=\"text\" name=\"changed_id_client\" value=\"$adm_id_client\"></td></tr>
	<tr><td align=\"right\">".$txt_domain_tbl_config_quotaMB[$lang]."</td>
	<td><input type=\"text\" name=\"adm_quota\" value=\"$adm_quota\"></td></tr>
	<tr><td align=\"right\">".$txt_allowed_data_transferMB[$lang]."</td>
	<td><input type=\"text\" name=\"bandwidth_per_month\" value=\"$bandwidth_per_month_mb\"></td></tr>
	<tr><td align=\"right\">".$txt_expiration_date[$lang]."</td>
	<td><input type=\"text\" name=\"expire\" value=\"$expire\"></td></tr>
	<tr><td align=\"right\">".$txt_heb_prod_id[$lang]."</td>
	<td><input type=\"text\" name=\"heb_prod_id\" value=\"$prod_id\"></td></tr>
	<tr><td align=\"right\">".$txt_number_of_database[$lang]."</td>
	<td><input type=\"text\" name=\"nbrdb\" value=\"".$info["nbrdb"]."\"></td></tr>
	<tr><td align=\"right\">".$txt_allow_to_add_domains[$lang]."</td>
	<td>$aldom_popup</td></tr>
	<tr><td align=\"right\">".$txt_can_have_subadmins_reseller[$lang]."</td>
	<td>$res_selector</td></tr>
	<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"updateuserinfo\" value=\"Ok\">
</td></tr></table></form>";

	// Generate the admin tool configuration module
	// Deletion of domains :
	$url = "".$_SERVER["PHP_SELF"]."?delete_admin_user=$adm_login&rub=$rub";
	$confirmed_url = dtcJavascriptConfirmLink($txt_del_user_confirm[$lang],$url);
	$domain_conf = "<a href=\"$confirmed_url\"><b>".$txt_del_user[$lang]."</b></a><br><br>";
	if(isset($data)){
		$domain_conf .= "<b><u>".$txt_del_user_domain[$lang]."</u><br>";
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
	$domain_conf .= "<b><u>".$txt_new_domain_for_user[$lang]."</u>";

	$domain_conf .= "<form action=\"?\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"rub\" value=\"$rub\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"text\" name=\"newdomain_name\" value=\"\">
	<input type=\"submit\" name=\"newdomain\" value=\"Ok\">
	</form>";

	$conf_user = "<font size=\"-1\"><table><tr><td>$domain_conf</td><td background=\"gfx/cadre04/trait06.gif\">&nbsp;</td><td>$user_data</td></tr></table>";
	$conf_user .= "</b></font> ";

	return $conf_user;
}
/////////////////////////////////////////////////////////////////
// Generate a tool for configuring all domain of one sub-admin //
/////////////////////////////////////////////////////////////////
function drawDomainConfig($admin){
	global $lang;
	global $txt_domain_tbl_config_dom_name;
	global $txt_domain_tbl_config_quota;
	global $txt_domain_tbl_config_max_email;
	global $txt_domain_tbl_config_max_ftp;
	global $txt_domain_tbl_config_max_subdomain;
	global $txt_domain_tbl_config_ip;

	global $conf_site_addrs;
	$site_addrs = explode("|",$conf_site_addrs);

	global $adm_login;
	global $adm_pass;

	if(isset($admin["data"])){
		$domains = $admin["data"];
		$nbr_domain = sizeof($domains);
	}else{
		$nbr_domain = 0;
	}

	$ret = "<table cellpadding=\"2\" cellspacing=\"0\" border=\"1\">
			<tr><td>".$txt_domain_tbl_config_dom_name[$lang]."</td><td>".$txt_domain_tbl_config_quota[$lang]."</td><td>".$txt_domain_tbl_config_max_email[$lang]."</td>
			<td>".$txt_domain_tbl_config_max_ftp[$lang]."</td><td>".$txt_domain_tbl_config_max_subdomain[$lang]."</td><td>Zone generation</td><td>".$txt_domain_tbl_config_ip[$lang]."</td><td>GO !</td></tr>";
	for($i=0;$i<$nbr_domain;$i++){
		$tobe_edited = $domains[$i];
		$webname = $tobe_edited["name"];
		$quota = $tobe_edited["quota"];
		$max_email = $tobe_edited["max_email"];
		$max_ftp = $tobe_edited["max_ftp"];
		$max_subdomain = $tobe_edited["max_subdomain"];
		$ip_addr = $tobe_edited["ip_addr"];
		if($tobe_edited["generate_flag"] == "yes"){
			$webalizer_gen_flag_txt = "<font color=\"#00FF00\">YES</font>";
			$what_to_switch = "no";
		}else{
			$webalizer_gen_flag_txt = "<font color=\"#FF0000\">NO</font>";
			$what_to_switch = "yes";
		}
		$ret .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
				<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
				<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
				<input type=\"hidden\" name=\"user_domain_to_modify\" value=\"$webname\"><tr>";

		$popup_txt = "<select name=\"new_ip_addr\">";
		$nbr_site_ip = sizeof($site_addrs);
		for($j=0;$j<$nbr_site_ip;$j++){
			$curr_ip = $site_addrs[$j];
			if($curr_ip == $ip_addr){
				$popup_txt .= "<option value=\"$curr_ip\" selected>$curr_ip";
			}else{
				$popup_txt .= "<option value=\"$curr_ip\">$curr_ip";
			}
		}
		$popup_txt .= "</select>";

		$ret .= "<td>$webname</td>
				<td><input type=\"text\" name=\"new_quota\" value=\"$quota\" size=\"5\"></td>
				<td><input type=\"text\" name=\"new_max_email\" value=\"$max_email\" size=\"5\"></td>
				<td><input type=\"text\" name=\"new_max_ftp\" value=\"$max_ftp\" size=\"5\"></td>
				<td><input type=\"text\" name=\"new_max_subdomain\" value=\"$max_subdomain\" size=\"5\"></td>
				<td><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&action=switch_generate_flag&domain=$webname&switch_to=$what_to_switch\">$webalizer_gen_flag_txt</a></td>
				<td>$popup_txt</td>
				";

		$ret .= "<td><input type=\"submit\" name=\"modify_domain_config\" value=\"Ok\"></tr></form>";
	}
	$ret .= "</table>";
	return $ret;
}


?>
