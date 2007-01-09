<?php
/**
 * @package DTC
 * @version $Id: subdomain.php,v 1.18 2007/01/09 05:03:34 thomas Exp $
 * @param unknown_type $domain
 * @return unknown
 */

function setZoneToGenerate($id){
	global $pro_mysql_subdomain_table;
	global $pro_mysql_domain_table;

	$q = "SELECT domain_name FROM $pro_mysql_subdomain_table WHERE id='$id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		echo "<font color=\"red\">Could not found subdomain in table for folder deletion</font>";
	}else{
		$a = mysql_fetch_array($r);
		$q = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='".$a["domain_name"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	}
}

function subdomainCreateDirsCallBack($id){
	global $adm_login;
	global $conf_chroot_path;
	global $conf_demo_version;
	global $conf_generated_file_path;

	setZoneToGenerate($id);
	$adm_path = getAdminPath($adm_login);
	$doms = explode("/",$_REQUEST["addrlink"]);
	$domain = $doms[0];
	$newsubdomain_dirpath = $adm_path."/".$domain."/subdomains/".$_REQUEST["subdomain_name"];

	if($conf_demo_version == "no"){
		if(!file_exists("$newsubdomain_dirpath"))
			mkdir("$newsubdomain_dirpath", 0750);
		if(!file_exists("$newsubdomain_dirpath/html"))
			mkdir("$newsubdomain_dirpath/html", 0750);
		if(!file_exists("$newsubdomain_dirpath/cgi-bin"))
			mkdir("$newsubdomain_dirpath/cgi-bin", 0750);
		if(!file_exists("$newsubdomain_dirpath/logs"))
			mkdir("$newsubdomain_dirpath/logs", 0750);
		exec("cp -fulpRv $conf_chroot_path/* $newsubdomain_dirpath");
		system ("if [ ! -e $newsubdomain_dirpath/html/index.* ]; then cp -rf $conf_generated_file_path/template/* $newsubdomain_dirpath/html; fi");
		updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
	}
	return;
}

function subdomainDeleteDirsCallBack($id){
	global $adm_login;
	global $pro_mysql_subdomain_table;

	setZoneToGenerate($id);
	$adm_path = getAdminPath($adm_login);
	$doms = explode("/",$_REQUEST["addrlink"]);
	$domain = $doms[0];

	$q = "SELECT * FROM $pro_mysql_subdomain_table WHERE id='$id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		echo "<font color=\"red\">Could not found subdomain in table for folder deletion</font>";
	}else{
		$a = mysql_fetch_array($r);
		$subdom_name = $a["subdomain_name"];
		$subdomain_dirpath = $adm_path."/".$domain."/subdomains/$subdom_name";
		system("rm -rf $subdomain_dirpath");
	}
	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
}

function subdomainEditCallBack($id){
	setZoneToGenerate($id);
	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
}

/////////////////////////////////////////////////////
// Draw the form for editing a domain's subdomains //
/////////////////////////////////////////////////////
function drawAdminTools_Subdomain($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $addrlink;

	global $lang;
	global $txt_subdom_list;
	global $txt_subdom_default_sub;
	global $txt_subdom_errase;
	global $txt_subdom_create;

	global $txt_subdom_newname;
	global $txt_subdom_ip;

	global $conf_administrative_site;
	global $conf_hide_password;

	global $txt_number_of_active_subdomains;
	global $txt_subdom_limit_reach;

	global $txt_subdom_newname;
	global $txt_subdom_txtrec;
	global $txt_subdom_dynip_logpass;
	global $txt_subdom_dynip_logpass;
	global $txt_subdom_dynip_login;
	global $txt_subdom_dynip_pass;
	global $txt_subdom_register_global;
	global $txt_subdom_edita;
	global $txt_subdom_scriptadvice;
	global $txt_subdom_windowsusers;
	global $txt_subdom_wwwalias;
	global $txt_subdom_generate_webalizer;
	global $txt_subdom_generate_vhost;
	global $txt_subdom_nameserver_for;
	global $txt_subdom_edit_one;
	global $txt_subdom_new;
	global $txt_yes;
	global $txt_no;
	
	global $edit_a_subdomain;

	global $dtcshared_path;
	global $pro_mysql_nameservers_table;
	global $pro_mysql_subdomain_table;

	$txt = "";

	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$nbr_subdomain = sizeof($domain["subdomains"]);
	$max_subdomain = $domain["max_subdomain"];
//	if($nbr_subdomain >= $max_subdomain){
//		$max_color = "color=\"#440000\"";
//	}else{
//		$max_color = "";
//	}
//	$nbrtxt = $txt_number_of_active_subdomains[$lang];
//	$txt = "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_subdomain ."</font> / <font size=\"-1\">" . $max_subdomain . "</font><br><br>";

	// Let's start a form !
	$txt .= "<form action=\"?\" methode=\"post\">";
	$txt .= "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">";
	$txt .= "<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">";
	$txt .= "<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";
	$txt .= "<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">";
	$txt .= "<input type=\"hidden\" name=\"whatdoiedit\" value=\"subdomains\">";

	// Popup for choosing default subdomain.
	$subdomains = $domain["subdomains"];
	$txt .= "<table><tr><td align=\"right\">";
	$txt .= $txt_subdom_default_sub[$lang]."</td><td><select name=\"subdomaindefault_name\">";
	for($i=0;$i<$nbr_subdomain;$i++){
		$sub = $subdomains[$i]["name"];
		if($domain["default_subdomain"] == "$sub"){
			$txt .= "<option value=\"$sub\" selected>$sub</option>";
		}else{
			$txt .= "<option value=\"$sub\">$sub</option>";
		}
	}
	$txt .= "</select></td><td><input type=\"hidden\" name=\"subdomaindefault\" value=\"Ok\"><input type=\"image\" src=\"gfx/stock_apply_20.png\"></td></tr></table></form>";

	$dsc = array(
		"title" => $txt_subdom_list[$lang],
		"new_item_title" => $txt_subdom_create[$lang],
		"new_item_link" => $txt_subdom_new[$lang],
		"edit_item_title" => $txt_subdom_edit_one[$lang],
		"table_name" => $pro_mysql_subdomain_table,
		"action" => "subdomain_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "subdomain_name",
		"max_item" => $max_subdomain,
		"num_item_txt" => $txt_number_of_active_subdomains[$lang],
		"create_item_callback" => "subdomainCreateDirsCallBack",
		"delete_item_callback" => "subdomainDeleteDirsCallBack",
		"edit_item_callback" => "subdomainEditCallBack",
		"where_list" => array(
			"domain_name" => $domain["name"]),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"subdomain_name" => array(
				"type" => "text",
				"check" => "subdomain",
				"disable_edit" => "yes",
				"legend" => $txt_subdom_newname[$lang]),
			"ip" => array(
				"type" => "text",
				"check" => "subdomain_or_ip",
				"can_be_empty" => "yes",
				"empty_makes_default" => "yes",
				"legend" => "IP address or CNAME: "),
			"generate_vhost" => array(
				"type" => "radio",
				"values" => array("yes","no"),
				"legend" => $txt_subdom_generate_vhost[$lang]),
			"register_globals" => array(
				"type" => "radio",
				"values" => array("yes","no"),
				"default" => "no",
				"legend" => $txt_subdom_register_global[$lang]),
			"associated_txt_record" => array(
				"type" => "text",
				"legend" => $txt_subdom_txtrec[$lang]),
			"nameserver_for" => array(
				"type" => "text",
				"check" => "domain_or_ip",
				"can_be_empty" => "yes",
				"legend" => $txt_subdom_nameserver_for[$lang])
			)
		);
	if($domain["safe_mode"] == "no"){
		$dsc["cols"]["safe_mode"] = array(
				"type" => "radio",
				"values" => array("yes","no"),
				"legend" => "PHP safe mode: ");
	}
	if($domain["sbox_protect"] == "no"){
		$dsc["cols"]["sbox_protect"] = array(
				"type" => "radio",
				"values" => array("yes","no"),
				"legend" => "Sbox cgi-bin protection: ");
	}
	$dsc["cols"]["login"] = array(
				"type" => "text",
				"check" => "dtc_login",
				"empty_makes_sql_null" => "yes",
				"can_be_empty" => "yes",
				"legend" => $txt_subdom_dynip_login[$lang]);
	$dsc["cols"]["pass"] = array(
				"type" => "password",
				"check" => "dtc_pass",
				"empty_makes_sql_null" => "yes",
				"can_be_empty" => "yes",
				"legend" => $txt_subdom_dynip_pass[$lang]);

	$txt .= dtcListItemsEdit($dsc);
	$txt .= helpLink("PmWiki/Subdomains");
	return $txt;

/*	$default_subdomain = $domain["default_subdomain"];
	$webname = $domain["name"];
	$domain_safe_mode = $domain["safe_mode"];
	$domain_sbox_protect = $domain["sbox_protect"];
	$subdomains = $domain["subdomains"];
	$nbr_subdomains = sizeof($subdomains);

	// Print a simple list of available sub-domains
	$txt .= "<font face=\"Verdana, Arial\"><font size=\"-1\"><b><u>".$txt_subdom_list[$lang]."</u><br>";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		$ip = $subdomains[$i]["ip"];
		if(isset($_REQUEST["edit_a_subdomain"]) && $sub == $_REQUEST["edit_a_subdomain"]){
			$ip_domain_to_edit = $ip;
			if(isset($subdomains[$i]["login"])){
				$login_to_edit = $subdomains[$i]["login"];
				$pass_to_edit = $subdomains[$i]["pass"];
			}else{
				$login_to_edit = "";
				$pass_to_edit = "";
			}
			$safe_mode = $subdomains[$i]["safe_mode"];
			$sbox_protect = $subdomains[$i]["sbox_protect"];
			$webalizer_to_edit = $subdomains[$i]["webalizer_generate"];
			$generate_vhost_to_edit = $subdomains[$i]["generate_vhost"];
			if (isset($subdomains[$i]["nameserver_for"]) && $subdomains[$i]["nameserver_for"] != "")
			{
				$nameserver_for_to_edit = $subdomains[$i]["nameserver_for"];
			} else {
				$nameserver_for_to_edit = "";
			}
			$w3_alias_to_edit = $subdomains[$i]["w3_alias"];
			$register_globals_to_edit = $subdomains[$i]["register_globals"];
			$txt_rec = $subdomains[$i]["associated_txt_record"];
		}
		if($i!=0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"http://$sub.$webname\" target=\"_blank\">";
		$txt .= $sub;
		$txt .= "</a>";
	}
	$txt .= "<hr width=\"90%\">";


	// Print list of subdomains, allow creation of new ones, and destruction of existings.
	$txt .= "<tr><td align=\"right\">".$txt_subdom_errase[$lang]."</td><td><select name=\"delsubdomain_name\">";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($domain["default_subdomain"] == "$sub"){
//			$txt .= "<option value=\"$sub\" selected>$sub</option>";
		}else{
			// Check that the subdomain is not used for a nameserver (in which case it cannot be deleted befor nameserver is deleted from registry)
			if(file_exists($dtcshared_path."/dtcrm")){
				$query = "SELECT * FROM $pro_mysql_nameservers_table WHERE domain_name='$webname' AND subdomain='$sub';";
				$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
				$num_rows = mysql_num_rows($result);
				if($num_rows < 1){
					$txt .= "<option value=\"$sub\">$sub</option>";
				}
			}else{
				$txt .= "<option value=\"$sub\">$sub</option>";
			}
		}
	}
	$txt .= "</select></td><td><input type=\"submit\" name=\"delsubdomain\" value=\"Ok\"></td></tr>";
	$txt .= "<tr><td colspan=\"3\"><hr width=\"90%\"></td></tr>";

	$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>".$txt_subdom_edit_one[$lang]." :</u></b></font></td></tr>";
	$txt .= "<tr><td collspan=\"3\">";
	$in_new = true;
	// List of subdomains to edit
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($i!=0){
			$txt .= " - ";
		}
		if(!isset($_REQUEST["edit_a_subdomain"]) || $sub != $_REQUEST["edit_a_subdomain"]){
			$txt .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&edit_a_subdomain=$sub&edit_domain=$webname&addrlink=$addrlink\">";
		}else{
			$in_new = false;
		}
		$txt .= $sub;
		if(!isset($_REQUEST["edit_a_subdomain"]) || $sub != $_REQUEST["edit_a_subdomain"]){
			$txt .= "</a>";
		}
	}
	if($in_new == false){
		$txt .= "<br><br><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&edit_domain=$webname&addrlink=$addrlink\">".$txt_subdom_new[$lang]."</a>";
	}else{
		$txt .= "<br><br>".$txt_subdom_new[$lang];
	}
	$txt .= "</td></tr>";

	if(!isset($_REQUEST["edit_a_subdomain"]) || $_REQUEST["edit_a_subdomain"] == ""){
		$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>".$txt_subdom_create[$lang]."</u></b></font></td></tr>";

		// Allow creation of new sub-domains
		if($nbr_subdomain < $max_subdomain){
			$txt .= "<tr><td align=\"right\">".$txt_subdom_newname[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_name\" value=\"\"></td><td></td></tr>";
			$txt .= "<tr><td colspan=\"3\">";
			$txt .= $txt_subdom_ip[$lang]."</td></tr>";
			$txt .= "<tr><td align=\"right\">IP/CNAME:</td><td><input type=\"text\" name=\"newsubdomain_ip\" value=\"\"></td>";
			$txt .= "<tr><td align=\"right\">".$txt_subdom_txtrec[$lang]."</td><td><input type=\"text\" name=\"associated_txt_record\" value=\"\">";
			$txt .= "<tr><td colspan=\"3\">";
			$txt .= $txt_subdom_dynip_logpass[$lang]."</td></tr>";
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_login[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_dynlogin\" value=\"\"></td></tr>";
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_pass[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_dynpass\" value=\"\"></td></tr>";
			$txt .= "<tr><td align=\"right\">".$txt_subdom_nameserver_for[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_nameserver_for\" value=\"\"></td></tr>";
			$txt .= "<td><input type=\"submit\" name=\"newsubdomain\" value=\"Ok\"></td></tr>";
		}else{
			$txt .= "<td colspan=\"3\">".$txt_subdom_limit_reach[$lang]."</td>";
		}
	}else{
		// Edition of existing subdomains
		$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>".$txt_subdom_edita[$lang]."</u></b></font></td></tr>";
		$txt .= "<tr><td align=\"right\">".$txt_subdom_newname[$lang]."</td><td>".$_REQUEST["edit_a_subdomain"]."</td><td></td></tr>";
		if($domain_safe_mode == "no"){
			if($safe_mode == "yes"){
				$checked_yes = " checked";
				$checked_no = "";
			}else{
				$checked_yes = "";
				$checked_no = " checked";
			}
			$txt .= "<tr><td align=\"right\">safe_mode</td>";
			$txt .= "<td>".$txt_yes[$lang]."<input type=\"radio\" name=\"safe_mode\" value=\"yes\" $checked_yes>".$txt_no[$lang]."
<input type=\"radio\" name=\"safe_mode\" value=\"no\" $checked_no></td></tr>";
		}
		if($domain_sbox_protect == "no"){
			if($sbox_protect == "yes"){
				$checked_yes = " checked";
				$checked_no = "";
			}else{
				$checked_yes = "";
				$checked_no = " checked";
			}
			$txt .= "<tr><td align=\"right\">sbox_protect</td>";
			$txt .= "<td>".$txt_yes[$lang]."<input type=\"radio\" name=\"sbox_protect\" value=\"yes\" $checked_yes>".$txt_no[$lang]."
<input type=\"radio\" name=\"sbox_protect\" value=\"no\" $checked_no></td></tr>";
		}
		if($register_globals_to_edit == "yes"){
			$checked_yes = " checked";
			$checked_no = "";
		}else{
			$checked_yes = "";
			$checked_no = " checked";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_register_global[$lang]."</td>";
		$txt .= "<td>".$txt_yes[$lang]."<input type=\"radio\" name=\"register_globals\" value=\"yes\" $checked_yes>
".$txt_no[$lang]."<input type=\"radio\" name=\"register_globals\" value=\"no\" $checked_no></td></tr>";
		if($webalizer_to_edit == "yes"){
			$checked_yes = " checked";
			$checked_no = "";
		}else{
			$checked_yes = "";
			$checked_no = " checked";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_generate_webalizer[$lang]."</td>";
		$txt .= "<td>".$txt_yes[$lang]."<input type=\"radio\" name=\"webalizer\" value=\"yes\" $checked_yes>
".$txt_no[$lang]."<input type=\"radio\" name=\"webalizer\" value=\"no\" $checked_no></td></tr>";
		if($generate_vhost_to_edit == "yes"){
			$checked_yes = " checked";
			$checked_no = "";
		}else{
			$checked_yes = "";
			$checked_no = " checked";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_generate_vhost[$lang]."</td>";
		$txt .= "<td>".$txt_yes[$lang]."<input type=\"radio\" name=\"generate_vhost\" value=\"yes\" $checked_yes>
".$txt_no[$lang]."<input type=\"radio\" name=\"generate_vhost\" value=\"no\" $checked_no></td></tr>";
		if($w3_alias_to_edit == "yes"){
			$checked_yes = " checked";
			$checked_no = "";
		}else{
			$checked_yes = "";
			$checked_no = " checked";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_wwwalias[$lang].$_REQUEST["edit_a_subdomain"]." alias:</td>";
		$txt .= "<td>".$txt_yes[$lang]."<input type=\"radio\" name=\"w3_alias\" value=\"yes\" $checked_yes>
".$txt_no[$lang]."<input type=\"radio\" name=\"w3_alias\" value=\"no\" $checked_no></td></tr>";
		$txt .= "<tr><td colspan=\"3\">";
		$txt .= $txt_subdom_ip[$lang]."</td></tr>";
		
		$txt .= "<tr><td align=\"right\">IP/CNAME:</td><td><input type=\"hidden\" name=\"subdomain_name\" value=\"".$_REQUEST["edit_a_subdomain"]."\">
		<input type=\"hidden\" name=\"edit_a_subdomain\" value=\"".$_REQUEST["edit_a_subdomain"]."\"><input type=\"text\" name=\"newsubdomain_ip\" value=\"$ip_domain_to_edit\"></td></tr>";
		$txt .= "<tr><td align=\"right\">".$txt_subdom_txtrec[$lang]."</td><td><input type=\"text\" name=\"associated_txt_record\" value=\"$txt_rec\">";
		$txt .= "<tr><td colspan=\"3\">";
		$txt .= $txt_subdom_dynip_logpass[$lang]."</td></tr>";

		$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_login[$lang]."</td><td><input type=\"text\" name=\"subdomain_dynlogin\" value=\"$login_to_edit\"></td></tr>";
		if ($conf_hide_password)
		{	
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_pass[$lang]."</td><td><input type=\"password\" name=\"subdomain_dynpass\" value=\"$pass_to_edit\"></td></tr>";
		} else {
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_pass[$lang]."</td><td><input type=\"text\" name=\"subdomain_dynpass\" value=\"$pass_to_edit\"></td></tr>";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_nameserver_for[$lang]."</td><td><input type=\"text\" name=\"nameserver_for\" value=\"$nameserver_for_to_edit\"></td></tr>";
		$txt .= "<tr><td>&nbsp;</td><td><input type=\"submit\" name=\"edit_one_subdomain\" value=\"Ok\"></td></tr>";
		if($login_to_edit != "" && isset($login_to_edit)){
			$txt .= "<tr><td colspan=\"3\">".$txt_subdom_scriptadvice[$lang]."<br>
<pre>
#!/bin/sh

# You can use either links or lynx, sometimes only one of them works, depending on distribution.
LYNX=/usr/bin/links
DOMAIN=$edit_domain
LOGIN=$login_to_edit
PASS=$pass_to_edit
SCRIPT_URL=\"https://$conf_administrative_site/dtc/\"

\$LYNX -source \$SCRIPT_URL\"dynip.php?login=\"\$LOGIN\"&pass=\"\$PASS\"&domain=\"\$DOMAIN
</pre><br>
".$txt_subdom_windowsusers[$lang]."<br>
https://$conf_administrative_site/dtc/dynip.php?login=$login_to_edit&pass=$pass_to_edit&domain=$edit_domain
</td></tr>
";
		}
	}
	$txt .= "</table></form>";

	$txt .= "</b></font></font>";
	return $txt;*/
}


?>
