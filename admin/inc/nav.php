<?php

if(isset($_REQUEST["gen_named_files"]) && $_REQUEST["gen_named_files"]==1){
	named_generate();
	$console .= _("Named conf files generated !") ;
	$console .= "\n" ;
}

// Executes the command liste bellow
if(isset($_REQUEST["gen_perso_vhost"]) && $_REQUEST["gen_perso_vhost"]==1){
	perso_vhost_generate();
	$console .= _("Apache vhost file for personal account generated !") ;
	$console .= "\n" ;
}
if(isset($_REQUEST["gen_pro_vhost"]) && $_REQUEST["gen_pro_vhost"]==1){
	pro_vhost_generate();
	$console .= _("Apache vhost file generated !") ;
	$console .= "\n" ;
}

if(isset($_REQUEST["gen_email_account"]) && $_REQUEST["gen_email_account"] == 1){
	mail_account_generate();
	$console .= _("Email user and domain files generated !") ;
	$console .= "\n" ;
}

if(isset($_REQUEST["gen_backup_script"]) && $_REQUEST["gen_backup_script"] == 1){
	backup_script_generate();
	$console .= _("Backup script generated !") ;
	$console .= "\n" ;
}

if(isset($_REQUEST["gen_stat_script"]) && $_REQUEST["gen_stat_script"] == 1){
	stat_script_generate();
	$console .= _("Stat script generated !") ;
	$console .= "\n" ;
}
if(isset($_REQUEST["gen_fetchmail_script"]) && $_REQUEST["gen_fetchmail_script"] == 1){
	fetchmail_generate();
	$console .= _("Fetchmail script generated !") ;
	$console .= "\n" ;
}


if($conf_demo_version == "true"){
	$browse_files_button = "
<a href=\"../etc\">". _("Browse all generated files") ."</a>";
}else{
	$browse_files_button = "";
}

// Links to the command executed on top
$top_commands = "
<br><b><font size=\"-2\">

<table border=\"0\" width=\"100%\" height=\"1\">
<tr><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"?rub=generate&gen_pro_vhost=1&gen_stat_script=1&gen_named_files=1&gen_backup_script=1&gen_email_account=1&gen_fetchmail_script=1\">
	<img border=\"0\" src=\"gfx/dtc/all_scripts.gif\"><br>
	<font face=\"Arial\" size=\"-2\">". _("ALL FILES AND SCRIPTS") ."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"?rub=generate&gen_pro_vhost=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_web.gif\"><br>
	<font face=\"Arial\" size=\"-2\">". _("APACHE VHOST") ."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"?rub=generate&gen_email_account=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_mail.gif\"><br>
	<font face=\"Arial\" size=\"-2\">". _("E-MAIL ACCOUNTS") ."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"?rub=generate&gen_named_files=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_named.gif\"><br>
	<font face=\"Arial\" size=\"-2\">". _("NAMED ZONES FILES") ."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"?rub=generate&reinit_named_zones=1\">
	<img border=\"0\" src=\"gfx/dtc/reinit_named.gif\"><br>
	<font face=\"Arial\" size=\"-2\">". _("REINIT NAMED ZONES") ."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"?rub=generate&gen_backup_script=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_backup.gif\"><br>
	<font face=\"Arial\" size=\"-2\">". _("BACKUP SCRIPTS") ."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"?rub=generate&gen_fetchmail_script=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_mail.gif\"><br>
	<font face=\"Arial\" size=\"-2\">". _("FETCHMAIL SCRIPTS") ."</font></a>
</div>
</td></tr></table>
$browse_files_button</font></b>";


function listTypePopup(){
	global $admlist_type;
	global $cur_admlist_type;
	global $panel_type;

	if($panel_type!="cronjob"){
		if(isset($_REQUEST["admlist_type"]) && $_REQUEST["admlist_type"] != ""){
			$_SESSION["cur_admlist_type"] = $_REQUEST["admlist_type"];
			$admlist_type = $_REQUEST["admlist_type"];
		}else{
			if(isset($_SESSION["cur_admlist_type"]) && $_SESSION["cur_admlist_type"] != ""){
				$admlist_type = $_SESSION["cur_admlist_type"];
			}else{
				$admlist_type = "Logins";
				$_SESSION["cur_admlist_type"] = "Logins";
			}
		}
	}
	$selectedlist_logins = "";
	$selectedlist_name = "";
	$selectedlist_domain = "";
	if($admlist_type == "Logins"){
		$selectedlist_logins = " selected";
	}else if($admlist_type == "Names"){
		$selectedlist_name = " selected";
	}else if($admlist_type == "Domains"){
        	$selectedlist_domain = " selected";
	}

	$admins = "<div class=\"box_wnb_nb_content\">
<div style=\"white-space: nowrap\" nowrap><form action=\"?\"><font size=\"-2\">". _("Display and sort by:")  ."<br>
<select class=\"box_wnb_nb_input\" name=\"admlist_type\">
<option value=\"Logins\"$selectedlist_logins>" . _("Logins") . "
<option value=\"Names\"$selectedlist_name>" . _("Names") . "
<option value=\"Domains\"$selectedlist_domain>" . _("Domains") . "
</select>
<div class=\"box_wnb_nb_input_btn_container\" onMouseOver=\"this.className='box_wnb_nb_input_btn_container-hover';\" onMouseOut=\"this.className='box_wnb_nb_input_btn_container';\">
 <div class=\"box_wnb_nb_input_btn_left\"></div>
 <div class=\"box_wnb_nb_input_btn_mid\"><input class=\"box_wnb_nb_input_btn\" type=\"submit\" value=\""._("Ok")."\"></div>
 <div class=\"box_wnb_nb_input_btn_right\"></div>
</div></form><br></div>
<div class=\"voider\"></div>
</div>
";
	return $admins;
}

function adminList($password=""){
	global $adm_login;
	global $adm_pass;
	global $admlist_type;
	global $rub;

	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_dedicated_table;

	global $panel_type;
	global $cur_admlist_type;
	global $conf_mysql_db;

	if($password != ""){
		$zepass = $password;
	}else{
		$zepass = $adm_login;
	}

	if($panel_type!="cronjob"){
		@session_start();
		if(isset($cur_admlist_type)) $_SESSION["cur_admlist_type"]=$cur_admlist_type;
	}
	$list_popup = listTypePopup();

	$dsc = array(
		"text_new_admin" => _("New virtual admin") ,
		"list_type" => $admlist_type,
		"admins" => array(
			));
	$dsc["admins"][] = array(
		"text" => _("New virtual admin") ,
		"adm_login" => "",
		"adm_pass" => "");

	$admins = "<a href=\"?\">". _("New virtual admin")  ."</a><br>";
	if(isset($rub)){
		$added_rub = "&rub=".$_REQUEST["rub"];
	}else{
		$added_rub = "";
	}
	switch($admlist_type){
	default:
	case "Logins":
		$dsc["rub"] = "user";
		mysql_select_db($conf_mysql_db);
		// Fetch a list of all name admins
		$query = "SELECT * FROM $pro_mysql_admin_table ORDER BY adm_login";
		$result = mysql_query ($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);

		for($j=0;$j<$num_rows;$j++){
			$admin = mysql_fetch_array($result) or die ("Cannot fetch user");
			$admin_login = $admin["adm_login"];
			$admin_pass = $admin["adm_pass"];
			$admin_owner = $admin["ob_next"];
			if (isset($admin_owner) && strlen($admin_owner) > 0){
				$admin_owner = "[ $admin_owner ]";
			} else {
				$admin_owner = "";
			}
			$admins .= "<br><a href=\"?adm_login=$admin_login&adm_pass=$zepass$added_rub\">$admin_login $admin_owner</a>";
			$dsc["admins"][] = array(
				"text" => $admin_login,
				"adm_login" => $admin["adm_login"],
				"adm_pass" => "$zepass");
		}
		break;
	case "Names":
		$admins .= "<br>";
		// Display all admins wich has no login.
		$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='0';";
		$result2 = mysql_query($query2) or die("Cannot execute query : \"$query2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 > 0){
			$admins .= "Nologins:<br>";
			for($j=0;$j<$num_rows2;$j++){
				$row2 = mysql_fetch_array($result2);
				$linkadm_login = $row2["adm_login"];
				$linkadm_pass = $row2["adm_pass"];
				$admins .= "&nbsp;&nbsp;&nbsp;<a href=\"?adm_login=$linkadm_login&adm_pass=$linkadm_pass$added_rub\">$linkadm_login</a><br>";
			}
		}

		$query7 = "SELECT * FROM $pro_mysql_client_table ORDER BY familyname,christname";
		$result7 = mysql_query($query7) or die("Cannot execute query : \"$query7\" !");
		$num_rows7 = mysql_num_rows($result7);
		for($i=0;$i<$num_rows7;$i++){
			$row7 = mysql_fetch_array($result7);
			$id_client = $row7["id"];
			$lastname = $row7["familyname"];
			$firstname = $row7["christname"];
			$company = $row7["company_name"];
			$is_company = $row7["is_company"];

			$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='$id_client';";
			$result2 = mysql_query($query2) or die("Cannot execute query : \"$query2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_rows2 = mysql_num_rows($result2);
			$admins .= "$lastname, $firstname<br>";
			if($num_rows2 < 1){
				$admins .= "&nbsp;&nbsp;&nbsp;<font color=\"red\">No login found !</font><br>";
			}else{
				for($j=0;$j<$num_rows2;$j++){
					$row2 = mysql_fetch_array($result2);
					$linkadm_login = $row2["adm_login"];
					$linkadm_pass = $row2["adm_pass"];
					$admins .= "&nbsp;&nbsp;&nbsp;<a href=\"?adm_login=$linkadm_login&adm_pass=$linkadm_pass$added_rub\">$linkadm_login</a><br>";
					if($is_company){
						$text = $company.": ";
					}else{
						$text = "";
					}
					$text .= $firstname.", ".$lastname." (".$linkadm_login.")";
					$dsc["admins"][] = array(
						"text" => $text,
						"adm_login" => $linkadm_login,
						"adm_pass" => "$zepass");
				}
			}
		}
		break;
	case "Domains":
		$admins .= "<br>";
		$query7 = "SELECT * FROM $pro_mysql_domain_table ORDER BY name";
		$result7 = mysql_query($query7) or die("Cannot execute query : \"$query7\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows7 = mysql_num_rows($result7);
		for($i=0;$i<$num_rows7;$i++){
			$row7 = mysql_fetch_array($result7);
			$domain_name = $row7["name"];
			$owner = $row7["owner"];
			
			$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$owner';";
			$result2 = mysql_query($query2) or die("Cannot execute query : \"$query2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_rows2 = mysql_num_rows($result2);
			if($num_rows2 != 1){
				$admins .= "$domain_name<br>&nbsp;&nbsp;&nbsp;<font color=\"red\">Domain without owner !</font><br>";
			}else{
				$row2 = mysql_fetch_array($result2);
				$linkadm_login = $row2["adm_login"];
				$linkadm_pass = $row2["adm_pass"];
				$admins .= "<a href=\"?adm_login=$linkadm_login&adm_pass=$zepass$added_rub\">$domain_name</a><br>";
				$dsc["admins"][] = array(
					"text" => $domain_name,
					"adm_login" => $linkadm_login,
					"adm_pass" => "$zepass");
			}
		}
		$query7 = "SELECT * FROM $pro_mysql_vps_table ORDER BY vps_server_hostname,vps_xen_name";
		$result7 = mysql_query($query7) or die("Cannot execute query : \"$query7\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows7 = mysql_num_rows($result7);
		for($i=0;$i<$num_rows7;$i++){
			$row7 = mysql_fetch_array($result7);
			$vps_name = $row7["vps_server_hostname"].":".$row7["vps_xen_name"];
			$owner = $row7["owner"];
			$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$owner';";
			$result2 = mysql_query($query2) or die("Cannot execute query : \"".$query2."\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_rows2 = mysql_num_rows($result2);
			if($num_rows2 != 1){
				$admins .= "$vps_name<br>&nbsp;&nbsp;&nbsp;<font color=\"red\">VPS without owner !</font><br>";
			}else{
				$row2 = mysql_fetch_array($result2);
				$linkadm_login = $row2["adm_login"];
				$linkadm_pass = $row2["adm_pass"];
				$admins .= "<a href=\"?adm_login=$linkadm_login&adm_pass=$zepass$added_rub\">$vps_name</a><br>";
				$dsc["admins"][] = array(
					"text" => $vps_name,
					"adm_login" => $linkadm_login,
					"adm_pass" => "$zepass");
			}
		}
		$query7 = "SELECT * FROM $pro_mysql_dedicated_table ORDER BY server_hostname";
		$result7 = mysql_query($query7) or die("Cannot execute query : \"$query7\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows7 = mysql_num_rows($result7);
		for($i=0;$i<$num_rows7;$i++){
			$row7 = mysql_fetch_array($result7);
			$server_hostname = $row7["server_hostname"];
			$owner = $row7["owner"];
			$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$owner';";
			$result2 = mysql_query($query2) or die("Cannot execute query : \"".$query2."\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_rows2 = mysql_num_rows($result2);
			if($num_rows2 != 1){
				$admins .= "$vps_name<br>&nbsp;&nbsp;&nbsp;<font color=\"red\">Dedicated without owner !</font><br>";
			}else{
				$row2 = mysql_fetch_array($result2);
				$linkadm_login = $row2["adm_login"];
				$linkadm_pass = $row2["adm_pass"];
				$admins .= "<a href=\"?adm_login=$linkadm_login&adm_pass=$zepass$added_rub\">$server_hostname</a><br>";
				$dsc["admins"][] = array(
					"text" => $server_hostname,
					"adm_login" => $linkadm_login,
					"adm_pass" => "$zepass");
			}
		}
		break;
	}
	if(function_exists("skin_displayAdminList")){
		return $list_popup.skin_displayAdminList($dsc);
	}else{
		return $list_popup.$admins;
	}
}

?>
