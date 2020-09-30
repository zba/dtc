<?php

////////////////////////////////////////////////////
// One domain name ftp account collection edition //
////////////////////////////////////////////////////
$htaccess_edit_flag_selected_subdomain = "no";
function subdomainSelector($domain){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $htaccess_edit_flag_selected_subdomain;
	$out = "";

	$nbr_subdomains = sizeof($domain["subdomains"]);
	$subdomain_selected = "no";
	for($i=0;$i<$nbr_subdomains;$i++){
		if($i != 0){
			$out .= " - ";
		}
		if(isset($_REQUEST["edit_subdomain"]) && $_REQUEST["edit_subdomain"] == $domain["subdomains"][$i]["name"]){
			$htaccess_edit_flag_selected_subdomain = "yes";
			$link1 = "";
			$link2 = "";
		}else{
			$link1 = "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_subdomain=".$domain["subdomains"][$i]["name"]."\">";
			$link2 = "</a>";
		}
		$out .= $link1.$domain["subdomains"][$i]["name"].$link2;
	}
	$out .= "<br>";
	return $out;
}

function drawAdminTools_Tools($domain){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $lang;

	global $htaccess_edit_flag_selected_subdomain;

	$out = "";

	$out .= subdomainSelector($domain);
	
	if($htaccess_edit_flag_selected_subdomain == "yes"){
		if(isset($_REQUEST["edit_folder"]) && $_REQUEST["edit_folder"] == "/"){
			$link1 = "";
			$link2 = "";
		}else{
			$link1 = "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_subdomain=".$domain["subdomains"][$i]["name"]."&edit_folder=/\">";
			$link2 = "</a>";
		}
		$out .= "$link1 / $link2";

		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					$out .= "fichier : $file : type : " . filetype($dir . $file);
				}
				closedir($dh);
			}
		}
	}
	return $out;
}

/*function drawAdminTools_Tools($domain){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $lang;
        global $encrypt_pass;
        global $encrypt_user;
        global $encrypt_directory;
        global $encrypt_dir_list;
        global $encrypt;
        global $domain;
        global $addrlink;
        global $mode;
        global $conf_site_root_host_path;

        $view_result = "";

        if(isset($_REQUEST["encrypt_pass"]) && isset($_REQUEST["encrypt_user"]) && $_REQUEST["encrypt_pass"] != "" && $_REQUEST["encrypt_user"] != ""){
		$encrypt_user = $_REQUEST["encrypt_user"];
		$encrypt_pass = $_REQUEST["encrypt_pass"];
		$encrypt_directory = $_REQUEST["encrypt_directory"];
		$encrypt = crypt_password($encrypt_pass);
		$txt = "";
	}

	if(isset($_REQUEST["modify"])){
		$encrypt_user = "";
		$encrypt_pass = "";
		$encrypt = "";
		$txt ="";

		$txt .= "<table align=\"left\"><tr><td colspan=\"2\"><b>Password Protect Directories</b><br></td></tr>
	<form action=\"?\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<tr><td><b>Username:</b></td><td><input type=\"text\" name=\"encrypt_user\" value=\"\"></td></tr>
	<tr><td><b>Password:</b></td><td><input type=\"text\" name=\"encrypt_pass\" value=\"\"></td></tr>";
		$txt .= "<tr><td><b>Protect:</b></td><td><input type=\"checkbox\" name=\"mode\" value=\"create_files\"></td></tr>";
		$dir_clean = explode("html",$_REQUEST["encrypt_directory"]);
		if(isset($dir_clean[1])){
			$txt .= "<tr><td>".$dir_clean[1]."</td></tr>";
		}else{
			$txt .= "<tr><td>".$dir_clean[0]."</td></tr>";
		}
		$txt .= "<input type=\"hidden\" name=\"encrypt_directory\"  value=\"".$_REQUEST["encrypt_directory"]."\">";
		$txt .= "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"Create\"></form></td></tr>";

		$txt .= passwd_read($_REQUEST["encrypt_directory"].".htpasswd");
		$txt .= "<tr><td><FORM><INPUT TYPE=\"button\" VALUE=\"Back\" onClick=\"history.go(-1);return true;\"> </FORM></td></tr>";
		$txt .= "</table>";
	}

	if($encrypt != ""){
		$htaccess_contents = "AuthType Basic<br>
AuthName \"proxy\"<br>
AuthUserFile \"$encrypt_directory.htpasswd\"<br>
require valid-user";
		$htaccess_contents2 = "AuthType Basic\n
AuthName \"proxy\"\n
AuthUserFile \"$encrypt_directory.htpasswd\"\n
require valid-user";
		$view_result = "yes";
		$txt = "";
		$txt .= "<table>";
		$txt .= "<tr><td><font size=\"2\"><b>Create a file named .htaccess<br>with the below contents</br>and place into the $encrypt_directory</b></font></td></tr><tr><td><font size=\"2\">$encrypt_user:$encrypt</font></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>";
		$txt .= "<tr><td><font size=\"2\"><b>Create a file named .htpasswd<br>with the below contents</br>and place into the $encrypt_directory</b></td></tr><tr><td><font size=\"2\">$htaccess_contents</font></td></tr>";

		if((isset($_REQUEST["mode"]))&& ($_REQUEST["mode"]=="create_files")){
			$run1 = passwd_write("$encrypt_directory.htpasswd", "$encrypt_user:$encrypt:|","no");
			if(!file_exists("$encrypt_directory.htaccess")){
				$run2 = passwd_write("$encrypt_directory.htaccess", $htaccess_contents2,"no");
			}else{
				$run2 = passwd_write("$encrypt_directory.htaccess", $htaccess_contents2,"yes");
			}
		}
		$txt .= "<tr><td><FORM><INPUT TYPE=\"button\" VALUE=\"Back\" onClick=\"history.go(-1);return true;\"> </FORM></td></tr>";
		$txt .= "</table>";
	}

	if((!isset($_REQUEST["modify"]))&&($view_result != "yes")){
		$encrypt_user = "";
		$encrypt_pass = "";
		$encrypt = "";
		$txt ="";
		$txt .= "<table align=\"left\"><tr><td colspan=\"2\"><b>Password Protect Directories</b><br></td></tr>";
		$txt .= prot_dir_select();
		$txt .= "<tr><td><FORM><INPUT TYPE=\"button\" VALUE=\"Back\" onClick=\"history.go(-1);return true;\"> </FORM></td></tr>";
		$txt .= "</table>";
	}

	if(isset($_REQUEST["delete"])){
		$txt = "";
		$txt .= "<table align=\"left\"><tr><td colspan=\"2\"><b>Password Protect Directories</b><br></td></tr>";
		if(isset($_REQUEST["remuser"])){
			$txt .= remove_user($_REQUEST["file2"],$_REQUEST["remuser"]);
			$txt .= "<tr><td><FORM><INPUT TYPE=\"button\" VALUE=\"Back\" onClick=\"history.go(-1);return true;\"> </FORM></td></tr>";
		}else{
			$txt .= remove_user($_REQUEST["file2"],"");
		}
		$txt .= "<tr><td><FORM><INPUT TYPE=\"button\" VALUE=\"Back\" onClick=\"history.go(-1);return true;\"> </FORM></td></tr>";
		$txt .="</table>";
	}
	return $txt;
}
*/
function crypt_password($password) {
	if (empty($password))
		return "** EMPTY PASSWORD **";

	return crypt($password, dtc_makesalt());
}

function random() {
	srand ((double) microtime() * 1000000);
	return rand();
}


function prot_dir_select(){
	global $txt;
	global $domain;
	global $adm_login;
	global $addrlink;
	global $adm_pass;
	global $conf_site_root_host_path;
	global $subdir;

	$site = explode("/",$addrlink);
	if(isset($_REQUEST["subdir"])){
		$subdir = $_REQUEST["subdir"];
	}
	$ppath="$conf_site_root_host_path/$adm_login/$site[0]/subdomains/www/html/".$subdir;

	// directories
	$dirs[] = '/';
	// try to find directories in html directory
	if ($handle = opendir($ppath)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && is_dir($ppath.$file)) {
				$dirs[]=$ppath.$file;
			}
		}
		closedir($handle);
	}else{
		return "Failed to open directory structure: $ppath";
	}
	if (!is_array($dirs)){
		// no directories present under HTML directory
		$dirs=array($dirs);
	}
	sort($dirs,SORT_STRING);
	reset($dirs);

	$d=0;
	for ($c=0;$c<count($dirs);$c++) {
		if ($d==1) {
			$bgc='#BDD7F7';
			$d=0;
		} else {
			$bgc='#B6D2F2';
			$d=1;
		}

		$abs_path_raw=str_replace("../","",$dirs[$c]);
		$abs_path = str_replace("//","/",$abs_path_raw);
		$usr_path = explode("html",$abs_path);
		$txt .= "<tr><td align='left' bgcolor=".$bgc."><a href=\"?adm_login=$adm_login&addrlink=".$_REQUEST["addrlink"]."&adm_pass=$adm_pass&encrypt_directory=$abs_path/&modify=yes\">Modify</a>";
		$txt .= "</td>";
//        $txt .= "<td bgcolor=".$bgc."><FONT SIZE='1' FACE='verdana,sans-serif'>  ".str_replace("../","",$dirs[$c])."</td></tr>";
		if (isset($usr_path[1])){
			$txt .= "<td bgcolor=".$bgc."><FONT SIZE='1' FACE='verdana,sans-serif'><a href=\"?adm_login=$adm_login&addrlink=".$_REQUEST["addrlink"]."&adm_pass=$adm_pass&subdir=".$usr_path[1]."/\">".$usr_path[1]."</a></td></tr>";
		} else {
			$txt .= "<td bgcolor=".$bgc."><FONT SIZE='1' FACE='verdana,sans-serif'><a href=\"?adm_login=$adm_login&addrlink=".$_REQUEST["addrlink"]."&adm_pass=$adm_pass&subdir=".$usr_path[0]."/\">".$usr_path[0]."</a></td></tr>";
		}
	}
	return $txt;
}

function passwd_read($file2){
//$file2 = $path.".htpasswd";
	global $txt;
	global $adm_login;
	global $adm_pass;

	$output_form="";

	if (file_exists($file2)){
		$handle = fopen($file2, "r+");
		$contents = fread($handle, filesize($file2));
		fclose($handle);
		//$output_form .= str_replace(":|","<br>",$output);
		$linebrk_contents = explode(":|", $contents);
		$txt .= "<form action=\"?\">";
		$txt .= "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">";
		$txt .= "<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">";
		$txt .= "<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">";
		$txt .= "<input type=\"hidden\" name=\"file2\" value=\"$file2\">";
		$txt .= "<input type=\"hidden\" name=\"delete\" value=\"yes\">";
		$txt .= "<tr><td colspan=\"2\"><b>Delete User/Unprotect Directory</b></td></tr>";
		foreach ($linebrk_contents as $value) {
			$txt .= "<tr><td><input type=\"radio\" name=\"remuser\" value=\"".$value."\"></td><td>".$value."<br></td></tr>";
		}
		$txt .= "<tr><td><b>Unprotect:</b></td><td><input type=\"checkbox\" name=\"unprotect\" value=\"yes\"></td></tr>";
		$txt .= "<tr><td><input type=\"submit\"  value=\"Submit\"></tr></td></form>";
	}else{
		$txt .= "<tr><td><h3>Directory Not Protected</h3></tr></td>";
	}
	return $txt;
}

function remove_user($file2,$rem_username){
	global $adm_login;
	global $adm_pass;
	global $txt;

	if(!isset($_REQUEST["unprotect"])){
		$handle = fopen($file2, "r+");
		$contents = fread($handle, filesize($file2));
		$remuser = str_replace($rem_username.":|", " ", $contents);
		$buffer = $remuser;
		$remove = "yes";
		$protect = passwd_write($file2, $buffer, $remove);
	}elseif((isset($_REQUEST["unprotect"]))&&($_REQUEST["unprotect"] == "yes")){
		if (file_exists($file2)) {
			if(!$unpro = unlink($file2)){
				$path = explode(".htpasswd",$file2);
				echo "<tr><td><h3>Unable to unprotect directory ".$path[0]."</h3></tr></td>";
			}else{
				$path = explode(".htpasswd",$file2);
				$file2 = $path[0].'.htaccess';
				$unpro = unlink($file2);
				echo "<tr><td><h3>Directory ".$path[0]." is no longer protected</h3></tr></td>";
			}
		}
	}
}

function passwd_write($file2, $buffer, $remove){
// Let's make sure the file exists and is writable first.
	if (is_writable($file2)) {
		if($remove != "yes"){
			$handle = fopen($file2, "r+");
			$contents = fread($handle, filesize($file2));
		}else{
			$contents = "";
		}

		$handle = fopen($file2, "w");

		// In our example we're opening $filename in append mode.
		// The file pointer is at the bottom of the file hence
		// that's where $somecontent will go when we fwrite() it.
		if (!$handle = fopen($file2, 'a')) {
			echo "<h3>Cannot open file ($file2)</h3><font color=\"red\">Ignore any messages noting success</font><br>";
			exit;
		}
		// Write $somecontent to our opened file.
		if (fwrite($handle, "$contents$buffer") === FALSE) {
			echo "<h3>Cannot write to file ($file2)</h3><font color=\"red\">Ignore any messages noting success</font><br>";
			exit;
		}
		//   echo "Success, appended file ($file2)<br>";
		fclose($handle);
	}elseif($handle = fopen($file2, "w")) {
		if (!$handle = fopen($file2, 'a')) {
			echo "<h3>Cannot open file ($file2)</h3><font color=\"red\">Ignore any messages noting success</font><br>";
			exit;
		}
		// Write $somecontent to our opened file.
		if (fwrite($handle, $buffer) === FALSE) {
			echo "<h3>Cannot write to file ($file2)</h3><font color=\"red\">Ignore any messages noting success</font><br>";
			exit;
		}
		//   echo "Success, created and wrote  to file ($file2)<br>";
		fclose($handle);
	} else {
		echo "<h3>The file $file2 is not writable</h3><font color=\"red\">Ignore any messages noting success</font><br>";
	}
}

?>
