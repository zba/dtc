<?php

function setZoneToGenerate($id){
	global $pro_mysql_subdomain_table;
	global $pro_mysql_domain_table;

	$q = "SELECT domain_name FROM $pro_mysql_subdomain_table WHERE id='$id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		echo "<font color=\"red\">". _("Could not found subdomain in table for folder deletion") ."</font>";
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
		echo "<font color=\"red\">". _("Could not found subdomain in table for folder deletion") ."</font>";
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

	global $conf_administrative_site;
	global $conf_hide_password;

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
	$txt .= _("Default subdomain: ") ."</td><td><select name=\"subdomaindefault_name\">";
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
		"title" => _("List of your subdomains: ") ,
		"new_item_title" => _("Create a subdomain: ") ,
		"new_item_link" => _("New subdomain") ,
		"edit_item_title" => _("Edit one of your subdomains") ,
		"table_name" => $pro_mysql_subdomain_table,
		"action" => "subdomain_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "subdomain_name",
		"max_item" => $max_subdomain,
		"num_item_txt" => _("Number of active subdomains:") ,
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
				"legend" => _("Subdomain name: ") ),
			"ip" => array(
				"type" => "text",
				"check" => "subdomain_or_ip",
				"can_be_empty" => "yes",
				"empty_makes_default" => "yes",
				"legend" => _("IP address or CNAME: ") ),
			"generate_vhost" => array(
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array ( _("Yes") , _("No") ),
				"legend" => _("Generate a vhost entry for this subdomain:") ),
			"add_default_charset" => array(
				"type" => "popup",
				"values" => array("dtc-wont-add","Off","ISO-8859-1","ISO-8859-2","ISO-8859-3","ISO-8859-4","ISO-8859-5","ISO-8859-6","ISO-8859-7","ISO-8859-8","ISO-8859-9","ISO-8859-10","ISO-8859-11","ISO-8859-12","ISO-8859-13","ISO-8859-14","ISO-8859-15","ISO-8859-16","ISO-2022-JP","ISO-2022-KR","ISO-2022-CN","Big5","cn-Big5","WINDOWS-1251","CP866","KOI8","KOI8-E","KOI8-r","KOI8-U","KOI8-ru","ISO-10646-UCS-2","ISO-10646-UCS-4","UTF-7","UTF-8","UTF-16","UTF-16BE","UTF-16LE","UTF-32","UTF-32BE","UTF-32LE","euc-cn","euc-gb","euc-jp","euc-kr","EUC-TW","gb2312","iso-10646-ucs-2","iso-10646-ucs-4","shift_jis"),
				"legend" => "AddDefaultCharset"),
			"srv_record" => array(
				"type" => "text",
				"can_be_empty" => "yes",
				"legend" => _("This subdomain is a service (SRV)<br>entry for the following port: ") ),
			"register_globals" => array(
				"type" => "radio",
				"values" => array("yes","no"),
				"default" => "no",
				"display_replace" => array ( _("Yes") , _("No") ),
				"legend" => _("Use register_globals=1: ") ),
			"associated_txt_record" => array(
				"type" => "text",
				"legend" => _("TXT field of the subdomain: ") ),
			"nameserver_for" => array(
				"type" => "text",
				"check" => "domain_or_ip",
				"can_be_empty" => "yes",
				"legend" => _("This subdomain is a nameserver (NS)<br>entry for the following subdomain: ") )
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
				"legend" => _("Sbox cgi-bin protection: ") );
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
			"legend" => _("Use an SSL vhost using this IP: ")
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
				"legend" => _("Dynamic IP update login: ") );
	$dsc["cols"]["pass"] = array(
				"type" => "password",
				"check" => "dtc_pass",
				"empty_makes_sql_null" => "yes",
				"can_be_empty" => "yes",
				"legend" => _("Dynamic IP update password: ") );

	$txt .= dtcListItemsEdit($dsc);
	$txt .= helpLink("PmWiki/Subdomains");
	return $txt;
}


?>
