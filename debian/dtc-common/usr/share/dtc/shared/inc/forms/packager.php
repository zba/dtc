<?php

////////////////////////////////////////////////////
// One domain name ftp account collection edition //
////////////////////////////////////////////////////
function drawAdminTools_PackageInstaller($domain,$adm_path){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $addrlink;
	global $dtcshared_path;

	global $pro_mysql_subdomain_table;

	global $conf_mysql_db;

	global $conf_user_mysql_type;
	global $conf_user_mysql_host;
	global $conf_user_mysql_root_login;
	global $conf_user_mysql_root_pass;

	global $package_installer_console;
	global $dtcpkg_db_login;

	global $pkg_info;

	$txt = "";
	$dir = $dtcshared_path."/package-installer";

	if(isset($_REQUEST["pkg"]) && !preg_match("/^([_a-z0-9]+)([_.a-z0-9-]*)([_.a-z0-9]+)\$/",$_REQUEST["pkg"])){
		die("Requested package name is invalid");
	}

	if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "do_install" || $_REQUEST["action"] == "prepareinstall")){
		$pkg_path = $dir."/".$_REQUEST["pkg"];
		$dtc_pkg_info = $pkg_path."/dtc-pkg-info.php";
		if(!file_exists($dtc_pkg_info)){
			die("Package $dtc_pkg_info not found line ".__LINE__." file ".__FILE__);
		}
		include($dtc_pkg_info);
	}
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "do_install"){
		$pkg_path = $dir."/".$_REQUEST["pkg"];
		$dtc_pkg_install = $pkg_path."/dtc-pkg-install.php";

		if(file_exists($dtc_pkg_install)){
			include($dtc_pkg_install);
		}
		// Check if user has enough rights
		checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
		if( !checkSubdomainFormat($_REQUEST["subdomain"])){
			die( _("Target subdomain is wrong."));
		}
		$admin_path = getAdminPath($adm_login);
		$target = "$admin_path/$edit_domain/subdomains/".$_REQUEST["subdomain"]."/html";
		if(!is_dir($target)){
			die("Destination directory does not exists line ".__LINE__." file ".__FILE__);
		}

		// Unpack the distribution package in target folder
		if($pkg_info["unpack_type"] == "tar.gz"){
			$cmd = "tar -C $target -xvzf $pkg_path/".$pkg_info["file"];
			$x = "=> tar -C $target -xvzf $pkg_path/".$pkg_info["file"]."\n";
			exec($cmd,$exec_out,$return_val);
		}else if($pkg_info["unpack_type"] == "tar.bz2"){
			$cmd = "tar -C $target -xvjf $pkg_path/".$pkg_info["file"];
			$x = "=> tar -xvjf ".$pkg_info["file"]."\n";
			exec($cmd,$exec_out,$return_val);
		}else{
			die("Package methode not supported yet");
		}

		// Rename folder to the destination folder name (eg remove version out of package.X.X.X folder name if exists)
		if(isset($pkg_info["renamedir_to"]) && isset($pkg_info["resulting_dir"]) && $pkg_info["resulting_dir"] != $pkg_info["renamedir_to"]){
			$cmd = "mv $target/".$pkg_info["resulting_dir"]." $target/".$pkg_info["renamedir_to"];
			$x .= "=> Moving ".$pkg_info["resulting_dir"]." to ".$pkg_info["renamedir_to"]."<br>";
			exec($cmd,$exec_out,$return_val);
		}
// https://dtc.gpl-host.com/dtc/index.php?adm_login=zigo&adm_pass=toto&addrlink=gpl-host.com/package-installer&action=prepareinstall&pkg=phpbb
		// Move the folder to the requested name dtcpkg_directory=bla
		if($pkg_info["can_select_directory"] == "yes"){
			if($_REQUEST["dtcpkg_directory"] == ""){
				$cmd = "mv $target/".$pkg_info["renamedir_to"]."/* $target/";
				$x .= "=> Moving ".$pkg_info["renamedir_to"]." to folder /<br>";
				exec($cmd,$exec_out,$return_val);
				$realtarget = "$target";
			}else{
				if( checkSubdomainFormat($_REQUEST["dtcpkg_directory"]) == false && $_REQUEST["dtcpkg_directory"] != ""){
					die(_("Target directory is wrong."));
				}
				$cmd = "mv $target/".$pkg_info["renamedir_to"]." $target/".$_REQUEST["dtcpkg_directory"];
				$x .= "=> Moving ".$pkg_info["renamedir_to"]." to ".$_REQUEST["dtcpkg_directory"]."<br>";
				exec($cmd,$exec_out,$return_val);
				$realtarget = "$target/".$_REQUEST["dtcpkg_directory"];
			}
		}

		if($pkg_info["need_database"] == "yes"){
			if($conf_user_mysql_type=="distant"){
				$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user host");
			}
			// Get the database infos beffore calling the custom package installer
			$q = "SELECT DISTINCT db.Db,db.User FROM mysql.user,mysql.db WHERE user.dtcowner='$adm_login' AND db.User=user.User AND db.Db='".mysql_real_escape_string($_REQUEST["database_name"])."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1)die("Cannot find database line ".__LINE__." file ".__FILE__);
			$a = mysql_fetch_array($r);
			$dtcpkg_db_login = $a["User"];

			if($conf_user_mysql_type=="distant"){
				mysql_close($newid) or die("Cannot disconnect to user database");
				connect2base();
			}
		}

		// Call the package specific installer php script
		$install_ret = do_package_install();
		// reselect the current dtc db in case another have been set
		mysql_select_db($conf_mysql_db);

		if($install_ret == 0){
			$package_installer_console .= "Install successful !<br>";
		}
		$nbr_remove = sizeof($pkg_info["remove_folder_path"]);
		if($nbr_remove > 0){
		//	print_r($pkg_info["remove_folder_path"]);
			$nbr_remove = sizeof($pkg_info["remove_folder_path"]);
			$package_installer_console .= "Removing install folders...<br>";
			for($i=0;$i<$nbr_remove;$i++){
				$cmd = "rm -r $realtarget/".$pkg_info["remove_folder_path"][$i];
			//	$package_installer_console .= $cmd."<br>";
				exec($cmd,$exec_out,$return_val);
			}
		}

		// Print the results
		$txt .= "<h3>Installation of ".$pkg_info["name"].":</h3><br><pre>".$x.$package_installer_console."</pre>";
		return $txt;
	}

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "prepareinstall"){
		$txt = "<h3>You are about to install ".$pkg_info["name"].":</h3><br>
		<u>Description:</u> ".$pkg_info["long_desc"]."<br>
		<u>Version:</u> ".$pkg_info["version"]."<br><br>";

		$txt .= "<form action=\"?\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";

		if($pkg_info["need_database"] == "yes"){
			$txt .= "<h3>Choose a database name for setup:</h3><br>";

			if($conf_user_mysql_type=="distant"){
				$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user SQL host");
			}
			mysql_select_db("mysql")or die ("Cannot select db: mysql");
			$q = "SELECT db.Db,db.User FROM user,db
			WHERE user.dtcowner='$adm_login'
			AND db.User=user.User";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$txt .= "You don't have any database yet. Please create one using the database tool
				(click database in the menu, then create a user and a database for this user).";
				if($conf_user_mysql_type=="distant"){
					mysql_close($newid) or die("Cannot disconnect to user database");
					connect2base();
				}
				mysql_select_db($conf_mysql_db);
				return $txt;
			}
			$txt .= "Database name: <select name=\"database_name\">";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$txt .= "<option value=\"".$a["Db"]."\">".$a["Db"]." (login: ".$a["User"].")"."</option>";
			}
			$txt .= "</select><br>
				Database password: <input type=\"password\" name=\"dtcpkg_db_pass\" value=\"\"><br><br>";
			if($conf_user_mysql_type=="distant"){
				mysql_close($newid) or die("Cannot disconnect to user database");
				connect2base();
			}
			mysql_select_db($conf_mysql_db)or die ("Cannot select db: $conf_mysql_db line ".__LINE__." file ".__FILE__);
		}

		if($pkg_info["need_admin_email"] == "yes"){
			$txt .= "<h3>Enter email for the admin of this package:</h3><br>";
			$txt .= "Email: <input type=\"text\" name=\"dtcpkg_email\" value=\"\"><br><br>";
		}

		if($pkg_info["need_admin_login"] == "yes"){
			$txt .= "<h3>Enter login informations for the admin of this package:</h3><br>";
			$txt .= "Login: <input type=\"text\" name=\"dtcpkg_login\" value=\"\"><br>";
			if($pkg_info["need_admin_pass"] == "yes"){
				$txt .= "Pass: <input type=\"text\" name=\"dtcpkg_pass\" value=\"\"><br>";
			}
			$txt .= "<br>";
		}


		$txt .= "<h3>Choose the subdomain and install :</h3><br>";
		$txt .= "<input type=\"hidden\" name=\"action\" value=\"do_install\">
		<input type=\"hidden\" name=\"pkg\" value=\"".$_REQUEST["pkg"]."\">
		Subdomain: <select name=\"subdomain\">";
//		echo "<pre>";
//		print_r($domain);
//		echo "</pre>";
		$n = sizeof($domain["subdomains"]);
		for($i=0;$i<$n;$i++){
			$txt .= "<option value=\"".$domain["subdomains"][$i]["name"]."\">".$domain["subdomains"][$i]["name"]."</option>";
		}
		$txt .= "</select><br><br>";

		if($pkg_info["can_select_directory"] == "yes"){
			$txt .= "<h3>Enter the directory where you want to install this package:</h3><br>";
			$txt .= "Directory (blank for /): <input type=\"text\" name=\"dtcpkg_directory\" value=\"\"><br><br>";
		}

		$txt .= "<input type=\"submit\" value=\"Install\">";
		$txt .= "</form>";
		return $txt;
	}

	$txt = "<h3>". _("Choose a package to install") .":</h3>";

	$txt .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"1\">";
	$txt .= "<tr><td>". _("Package name") ."</td><td>". _("Description") ."</td><td>". _("Package version") ."</td><td>". _("Requires a database") ."</td><td>". _("Unpack size") ."</td><td>". _("Install") ."</td></tr>";
	
	$packages=array();
	if (is_dir($dir) && ($dh = opendir($dir))) {
		while (($file = readdir($dh)) !== false) {
			if(is_dir($dir."/".$file) && $file != "." && $file != ".."){
				$packages[]=$file;
			}
		}
		closedir($dh);
	}

	sort($packages);

	foreach($packages as $package) {
		if(file_exists($dir."/".$package."/dtc-pkg-info.php")){
			include($dir."/".$package."/dtc-pkg-info.php");
			$txt .= "<tr><td>".$pkg_info["name"]."</td>
				<td>".$pkg_info["short_desc"]."</td>
				<td>".$pkg_info["version"]."</td>
				<td>".$pkg_info["need_database"]."</td>
				<td style=\"white-space:nowrap;text-align=right\" nowrap>".smartByte($pkg_info["unpack_disk_usage"])."</td>
				<td><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&action=prepareinstall&pkg=$package\">". _("Install") ."</a></td></tr>";
		}
	}

	$txt .= "</table>";
	return $txt;
}

?>
