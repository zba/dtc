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
	global $lang;

	global $pro_mysql_subdomain_table;

	global $conf_mysql_db;

	$txt = "";
	$dir = $dtcshared_path."/package-installer";

	if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "do_install" || $_REQUEST["action"] == "prepareinstall")){
		$pkg_path = $dir."/".$_REQUEST["pkg"];
		$dtc_pkg_info = $pkg_path."/dtc-pkg-info.php";
		if(!file_exists($dtc_pkg_info)){
			die("Package $dtc_pkg_info not found line ".__LINE__." file ".__FILE__);
		}
		include($dtc_pkg_info);
	}
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "do_install"){
		checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
		checkSubdomainFormat($_REQUEST["subdomain"]);
		$admin_path = getAdminPath($adm_login);
		$target = "$admin_path/$edit_domain/subdomains/".$_REQUEST["subdomain"]."/html";
		if(!is_dir($target)){
			die("Destination directory does not exists line ".__LINE__." file ".__FILE__);
		}
		if($pkg_info["unpack_type"] == "tar.gz"){
			$cmd = "tar -C $target -xvzf $pkg_path/".$pkg_info["file"];
			$x = "tar -xvzf ".$pkg_info["file"]."\n";
			exec($cmd,$exec_out,$return_val);
		}else if($pkg_info["unpack_type"] == "tar.bz2"){
			$cmd = "tar -C $target -xvjf $pkg_path/".$pkg_info["file"];
			$x = "tar -xvjf ".$pkg_info["file"]."\n";
			exec($cmd,$exec_out,$return_val);
		}else{
			die("Package methode not supported yet");
		}
		if(isset($pkg_info["renamedir_to"]) && isset($pkg_info["resulting_dir"]) && $pkg_info["resulting_dir"] != $pkg_info["renamedir_to"]){
			$cmd = "mv $target/".$pkg_info["resulting_dir"]." $target/".$pkg_info["renamedir_to"];
			$x .= "mv".$pkg_info["resulting_dir"]." ".$pkg_info["renamedir_to"]."\n";
			exec($cmd,$exec_out,$return_val);
		}
		$txt .= "<b><u>Installation of ".$pkg_info["name"].":</u></b><br><pre>$x</pre>";
		return $txt;
	}

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "prepareinstall"){
		$txt = "<b><u>You are about to install ".$pkg_info["name"].":</u></b><br>
		<u>Description:</u> ".$pkg_info["long_desc"]."<br>
		<u>Version:</u> ".$pkg_info["version"]."<br><br>";

		$txt .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";
		if($pkg_info["need_database"] == "yes"){
			$txt .= "<b><u>Choose a database name for setup:</u></b><br>";
			mysql_select_db("mysql")or die ("Cannot select db: mysql");
			$q = "SELECT db.Db FROM user,db
			WHERE user.dtcowner='$adm_login'
			AND db.User=user.User";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$txt .= "You don't have any database yet. Please create one using the database tool
				(click database in the menu, then create a user and a database for this user).";
				return $txt;
			}
			$txt .= "<select name=\"database_name\">";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$txt .= "<option value=\"".$a["Db"]."\">".$a["Db"]."</option>";
			}
			$txt .= "</select><br><br>";
			mysql_select_db($conf_mysql_db)or die ("Cannot select db: $conf_mysql_db");
		}

		$txt .= "<b><u>Choose the subdomain and install :</u></b><br>";
		$txt .= "<input type=\"hidden\" name=\"action\" value=\"do_install\">
		<input type=\"hidden\" name=\"pkg\" value=\"".$_REQUEST["pkg"]."\">
		<select name=\"subdomain\">";
//		echo "<pre>";
//		print_r($domain);
//		echo "</pre>";
		$n = sizeof($domain["subdomains"]);
		for($i=0;$i<$n;$i++){
			$txt .= "<option value=\"".$domain["subdomains"][$i]["name"]."\">".$domain["subdomains"][$i]["name"]."</option>";
		}
		$txt .= "</select><input type=\"submit\" value=\"Install\"></form>";
		return $txt;
	}
	$txt = "<b><u>Choose a package to install:</u></b>";

	$txt .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"1\">";
	$txt .= "<tr><td>Name</td><td>Description</td><td>Version</td><td>Need a database</td><td>Install</td></tr>";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(is_dir($dir."/".$file) && $file != "." && $file != ".."){
					if(file_exists($dir."/".$file."/dtc-pkg-info.php")){
						include($dir."/".$file."/dtc-pkg-info.php");
						$txt .= "<tr><td>".$pkg_info["name"]."</td>
							<td>".$pkg_info["short_desc"]."</td>
							<td>".$pkg_info["version"]."</td>
							<td>".$pkg_info["need_database"]."</td>
							<td><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&action=prepareinstall&pkg=$file\">INSTALL</a></td></tr>";
					}
				}
			}
			closedir($dh);
		}
	}

	$txt .= "</table>";
	return $txt;
}

?>
