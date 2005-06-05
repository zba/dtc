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

	$txt = "<b><u>Choose a package to install:</u></b>";

	$txt .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"1\">";
	$txt .= "<tr><td>Name</td><td>Description</td><td>Version</td><td>Need a database</td><td>Install</td></tr>";
	$dir = $dtcshared_path."/package-installer";
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
							<td><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">INSTALL</a></td></tr>";
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
