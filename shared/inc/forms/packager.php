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

	$txt = "<h3>Choose a package to install:</h3>";

	$txt .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
	$dir = $dtcshared_path."/package-installer";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(is_dir($dir."/".$file) && $file != "." && $file != ".."){
					$txt .= "<td>".$file."</td></tr>";
				}
			}
			closedir($dh);
		}
	}

	$txt .= "</table>";
	return $txt;
}

?>
