<?php

if(isset($_REQUEST["gen_named_files"]) && $_REQUEST["gen_named_files"]==1){
	named_generate();
	$console .= "Named conf files generated !";
}

// Executes the command liste bellow
if(isset($_REQUEST["gen_perso_vhost"]) && $_REQUEST["gen_perso_vhost"]==1){
	perso_vhost_generate();
	$console .= "Apache vhost file for personal account generated !";
}
if(isset($_REQUEST["gen_pro_vhost"]) && $_REQUEST["gen_pro_vhost"]==1){
	pro_vhost_generate();
	$console .= "Apache vhost file generated !";
}

if(isset($_REQUEST["gen_email_account"]) && $_REQUEST["gen_email_account"] == 1){
	mail_account_generate();
	$console .= "Qmail user and domain files generated !";
}

if(isset($_REQUEST["gen_backup_script"]) && $_REQUEST["gen_backup_script"] == 1){
	backup_script_generate();
	$console .= "Backup script generated !";
}

if(isset($_REQUEST["gen_stat_script"]) && $_REQUEST["gen_stat_script"] == 1){
	stat_script_generate();
	$console .= "Stat script generated !";
}

if($conf_demo_version == "true"){
	$browse_files_button = "
<a href=\"../etc\">Browse all generated files</a>";
}else{
	$browse_files_button = "";
}

// Links to the command executed on top
$top_commands = "
<br><b><font size=\"-2\">

<table border=\"0\" width=\"100%\" height=\"1\">
<tr><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate&gen_pro_vhost=1&gen_stat_script=1&gen_named_files=1&gen_backup_script=1&gen_email_account=1\">
	<img border=\"0\" src=\"gfx/dtc/all_scripts.gif\"><br>
	<font face=\"Arial\" size=\"-2\">".$txt_icon_all_files_and_scripts[$lang]."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate&gen_pro_vhost=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_web.gif\"><br>
	<font face=\"Arial\" size=\"-2\">".$txt_icon_apache_vhost[$lang]."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate&gen_email_account=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_mail.gif\"><br>
	<font face=\"Arial\" size=\"-2\">".$txt_icon_mailbox_account[$lang]."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate&gen_named_files=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_named.gif\"><br>
	<font face=\"Arial\" size=\"-2\">".$txt_icon_named_zones[$lang]."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate&reinit_named_zones=1\">
	<img border=\"0\" src=\"gfx/dtc/reinit_named.gif\"><br>
	<font face=\"Arial\" size=\"-2\">".$txt_icon_reinit_named_zones[$lang]."</font></a>
</div>
</td><td valign=\"bottom\">
<div align=\"center\">
	<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate&gen_backup_script=1\">
	<img border=\"0\" src=\"gfx/dtc/generate_backup.gif\"><br>
	<font face=\"Arial\" size=\"-2\">".$txt_icon_backup_scripts[$lang]."</font></a>
</div>
</td></tr></table>
$browse_files_button</font></b>";



function adminList($password=""){
	global $lang;
	global $adm_login;
	global $adm_pass;
	global $txt_sort_by;
	global $admlist_type;
	global $txt_admlist_new_admin;
	global $txt_admlist_sort_by_legend;
	global $rub;

	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_vps_table;
	global $panel_type;
	global $cur_admlist_type;

	if($password != ""){
		$zepass = $password;
	}else{
		$zepass = $adm_login;
	}

	if($panel_type!="cronjob"){
		// Find the current display type
		// Depreacted: session_register("cur_admlist_type");
		$_SESSION["cur_admlist_type"] = "";
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

	$txt_sort_by = $txt_admlist_sort_by_legend[$lang];
	$admins = "<div style=\"white-space: nowrap\" nowrap><form action=\"".$_SERVER["PHP_SELF"]."\"><font size=\"-2\">$txt_sort_by<br>
<select name=\"admlist_type\">
<option value=\"Logins\"$selectedlist_logins>Logins
<option value=\"Names\"$selectedlist_name>Names
<option value=\"Domains\"$selectedlist_domain>Domains
</select><input type=\"submit\" value=\"Ok\"></form><br>
";

	$txt_new_admin = $txt_admlist_new_admin[$lang];
	$admins .= "<a href=\"".$_SERVER["PHP_SELF"]."?\">$txt_new_admin</a><br>";
	if(isset($rub)){
		$added_rub = "&rub=".$_REQUEST["rub"];
	}else{
		$added_rub = "";
	}
	if($admlist_type == "Logins"){
		// Fetch a list of all name admins
		$query = "SELECT * FROM $pro_mysql_admin_table ORDER BY adm_login";
		$result = mysql_query ($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);

		for($j=0;$j<$num_rows;$j++){
			$admin = mysql_fetch_array($result) or die ("Cannot fetch user");
			$admin_login = $admin["adm_login"];
			$admin_pass = $admin["adm_pass"];
			$admin_owner = $admin["ob_next"];
			if (isset($admin_owner) && strlen($admin_owner) > 0)
			{
				$admin_owner = "[ $admin_owner ]";
			} else {
				$admin_owner = "";
			}
			$admins .= "<br><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$admin_login&adm_pass=$zepass$added_rub\">$admin_login $admin_owner</a>";
		}
	}else if($admlist_type == "Names"){
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
				$admins .= "&nbsp;&nbsp;&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$linkadm_login&adm_pass=$linkadm_pass$added_rub\">$linkadm_login</a><br>";
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
					$admins .= "&nbsp;&nbsp;&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$linkadm_login&adm_pass=$linkadm_pass$added_rub\">$linkadm_login</a><br>";
				}
			}
		}
	}else if($admlist_type == "Domains"){
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
				$admins .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$linkadm_login&adm_pass=$linkadm_pass$added_rub\">$domain_name</a><br>";
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
			$result2 = mysql_query($query2) or die("Cannot execute query : \"$query2\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_rows2 = mysql_num_rows($result2);
			if($num_rows2 != 1){
				$admins .= "$vps_name<br>&nbsp;&nbsp;&nbsp;<font color=\"red\">VPS without owner !</font><br>";
			}else{
				$row2 = mysql_fetch_array($result2);
				$linkadm_login = $row2["adm_login"];
				$linkadm_pass = $row2["adm_pass"];
				$admins .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$linkadm_login&adm_pass=$linkadm_pass$added_rub\">$vps_name</a><br>";
			}
		}
	}
	$admins .= "</font></div>";
	return $admins;
}

?>
