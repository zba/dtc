<?php
/**
 * @package DTC
 * @version $Id: subdomain.php,v 1.21 2007/02/24 06:05:53 thomas Exp $
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
function drawAdminTools_Subdomain($admin,$domain){

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
	global $pro_mysql_ssl_ips_table;

	$txt = "";

	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$nbr_subdomain = sizeof($domain["subdomains"]);
	$max_subdomain = $domain["max_subdomain"];

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
				"legend" => $txt_subdom_ip[$lang]),
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

	// Get all SSL IPs asigned to this customer
	$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE adm_login='$adm_login' AND available='no';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 0){
		$ssl_ips = array();
		$ssl_ips[] = "none";
		// Check if some SSL certs are free, or used by current subdomain
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$nbr_domains = sizeof($admin["data"]);
			$used_by = "none";
			for($j=0;$j<$nbr_domains;$j++){
				$nbr_subdomains = sizeof($admin["data"][$j]["subdomains"]);
				for($k=0;$k<$nbr_subdomains;$k++){
					if($admin["data"][$j]["subdomains"][$k]["ssl_ip"] == $a["ip_addr"]){
						// The cert is used by current subdomain
						if(isset($_REQUEST["item"]) && isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "subdomain_editor_edit_item" && $_REQUEST["item"] == $admin["data"][$j]["subdomains"][$k]["id"]){
							$ssl_ips[] = $a["ip_addr"];
						// The cert is used by another subdomain, don't show it...
						}else{
							$used_by = $admin["data"][$j]["name"].$admin["data"][$j]["subdomains"][$k]["name"];
						}
					}
				}
			}
			if($used_by == "none"){
				$ssl_ips[] = $a["ip_addr"];
			}
		}
		$dsc["cols"]["ssl_ip"] = array(
			"type" => "popup",
			"values" => $ssl_ips,
			"legend" => "Use an SSL vhost using this IP:"
			);
	}

	// Check to see if there is some SSL IPs for that customer
	$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE adm_login='$adm_login' AND available='no';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

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
}


?>
