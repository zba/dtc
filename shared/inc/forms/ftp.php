<?php

function ftpAccountsCallback(){
	global $pro_mysql_ftp_table;
	global $conf_dtc_system_uid;
	global $conf_dtc_system_gid;

	// Todo: optimize by changing only the UID/GID of the revelant account.
	$q = "UPDATE $pro_mysql_ftp_table SET uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid' WHERE 1;";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	return;
}

////////////////////////////////////////////////////
// One domain name ftp account collection edition //
////////////////////////////////////////////////////
function drawAdminTools_Ftp($domain,$adm_path){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;

	global $edftp_account;
	global $addrlink;

	global $lang;
	global $txt_ftp_account_list;
	global $txt_ftp_new_account;
	global $txt_ftp_account_edit;
	global $txt_ftp_new_account_link;
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_path;
	global $conf_hide_password;

	global $txt_number_of_active_ftp;
	global $txt_maxnumber_of_ftp_account_reached;

	global $pro_mysql_ftp_table;

	checkLoginPassAndDomain($adm_login,$adm_pass,$domain["name"]);

	$txt = "";

	// Build the popup values and display values arrays
	$path_popup_vals = array();
	$path_popup_disp = array();
	$path_popup_vals[] = "$adm_path";
	$path_popup_disp[] = "/";
	$path_popup_vals[] = "$adm_path/$edit_domain";
	$path_popup_disp[] = "/$edit_domain";
	$nbr_subdomains = sizeof($domain["subdomains"]);
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub_name = $domain["subdomains"][$i]["name"];
		$path_popup_vals[] = "$adm_path/$edit_domain/subdomains/$sub_name";
		$path_popup_disp[] = "/$edit_domain/subdomains/$sub_name";
		$path_popup_vals[] = "$adm_path/$edit_domain/subdomains/$sub_name/html";
		$path_popup_disp[] = "/$edit_domain/subdomains/$sub_name/html";
	}

	// Just create the list editor now...
	$dsc = array(
		"title" => $txt_ftp_account_list[$lang],
		"new_item_title" => $txt_ftp_new_account[$lang],
		"new_item_link" => "New ftp login",
		"edit_item_title" => $txt_ftp_account_edit[$lang],
		"table_name" => $pro_mysql_ftp_table,
		"action" => "ftp_access_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "login",
		"max_item" => $domain["max_ftp"],
		"num_item_txt" => $txt_number_of_active_ftp[$lang],
		"create_item_callback" => "ftpAccountsCallback",
		"where_list" => array(
			"hostname" => $domain["name"]),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"login" => array(
				"type" => "text",
				"check" => "dtc_login",
				"legend" => $txt_login_login[$lang]),
			"password" => array(
				"type" => "password",
				"check" => "dtc_pass",
				"legend" => $txt_login_pass[$lang]),
			"homedir" => array(
				"type" => "popup",
				"values" => $path_popup_vals,
				"display_replace" => $path_popup_disp,
				"legend" => $txt_path[$lang])
			)
		);
	$txt .= dtcListItemsEdit($dsc);
	return $txt;

/*	if(isset($domain["ftps"])){
		$nbr_ftp = sizeof($domain["ftps"]);
		$ftps = $domain["ftps"];
		$nbr_account = $nbr_ftp;
	}else{
		$nbr_ftp = 0;
		$nbr_account = 0;
	}
	$max_ftp = $domain["max_ftp"];
	if($nbr_ftp >= $max_ftp){
		$max_color = "color=\"#440000\"";
	}else{
		$max_color = "";
	}
	$nbrtxt = $txt_number_of_active_ftp[$lang];
	$txt = "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_ftp ."</font> / <font size=\"-1\">" . $max_ftp . "</font><br><br>";

	$txt .= "<font face=\"Verdana, Arial\"><font size=\"-1\"><b><u>".$txt_ftp_account_list[$lang]."</u><br>";
	$pass = "";
	for($i=0;$i<$nbr_account;$i++){
		$ftp = $ftps[$i];
		$login = $ftp["login"];
		if(isset($_REQUEST["edftp_account"]) && $_REQUEST["edftp_account"] != "" && $login == $_REQUEST["edftp_account"]){
			$pass = $ftp["passwd"];
			$ftpath = $ftp["path"];
		}
		if($i != 0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=ftps&edftp_account=$login\">$login</a>";
	}

	if(isset($_REQUEST["edftp_account"]) && $_REQUEST["edftp_account"] != "" && isset($ftpath) && $ftpath == "$adm_path"){
		$is_selected = " selected";
	}else{
		$is_selected ="";
	}
	$path_popup = "<option value=\"$adm_path\"$is_selected>/</option>";
	if(isset($_REQUEST["edftp_account"]) && $_REQUEST["edftp_account"] != "" && isset($ftpath) && $ftpath == "$adm_path/$edit_domain"){
		$is_selected = " selected";
	}else{
		$is_selected ="";
	}
	$path_popup .= "<option value=\"$adm_path/$edit_domain\"$is_selected>/$edit_domain/</option>";
	$nbr_subdomains = sizeof($domain["subdomains"]);
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub_name = $domain["subdomains"][$i]["name"];
		if(isset($_REQUEST["edftp_account"]) && $_REQUEST["edftp_account"] != "" && isset($ftpath) && $ftpath == "$adm_path/$edit_domain/subdomains/$sub_name"){
			$is_selected = " selected";
		}else{
			$is_selected ="";
		}
		$path_popup .= "<option value=\"$adm_path/$edit_domain/subdomains/$sub_name\"$is_selected>/$edit_domain/subdomains/$sub_name/</option>";

		if(isset($_REQUEST["edftp_account"]) && $_REQUEST["edftp_account"] != "" && isset($ftpath) && $ftpath == "$adm_path/$edit_domain/subdomains/$sub_name/html"){
			$is_selected = " selected";
		}else{
			$is_selected ="";
		}
		$path_popup .= "<option value=\"$adm_path/$edit_domain/subdomains/$sub_name/html\"$is_selected>/$edit_domain/subdomains/$sub_name/html/</option>";
	}

	if(isset($_REQUEST["edftp_account"]) && $_REQUEST["edftp_account"] != "" && (!isset($_REQUEST["deleteftpaccount"]) || $_REQUEST["deleteftpaccount"] != "Delete")){
		$txt .= "<br><br><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=ftps\">".$txt_ftp_new_account_link[$lang]."</A><br>";
		$txt .= "
<br><u>".$txt_ftp_account_edit[$lang]."</u>
<table>
<tr><td align=\"right\">".$txt_login_login[$lang]."</td><td>".$_REQUEST["edftp_account"]."</td></tr>
<tr><td align=\"right\">
<form name=\"ftpfrm\" action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"ftps\">
	<input type=\"hidden\" name=\"edftp_account\" value=\"".$_REQUEST["edftp_account"]."\">";
	$genpass = autoGeneratePassButton("ftpfrm","edftp_pass");
	if ($conf_hide_password == "yes"){
		$txt .= $txt_login_pass[$lang]."</td><td style=\"white-space: nowrap;\"><input type=\"password\" name=\"edftp_pass\" value=\"$pass\">$genpass";
	}else{
		$txt .= $txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"edftp_pass\" value=\"$pass\">$genpass";
	}
$txt .= "
</td></tr><tr><td align=\"right\">
	".$txt_path[$lang]."</td><td><select name=\"edftp_path\">$path_popup</select>
</td></tr><tr><td align=\"right\">
	<input type=\"submit\" name=\"deleteftpaccount\" value=\"Delete\"></td><td><input type=\"submit\" name=\"update_ftp_account\" value=\"Ok\">
</td></tr>
</table>
</form>
<br>
";
	}else{
		$txt .= "
<br><br><u>".$txt_ftp_new_account[$lang]."</u>";
		if($nbr_ftp < $max_ftp){
			if(isset($_REQUEST["edftp_account"])){
				$edftp_account = $_REQUEST["edftp_account"];
			}else{
				$edftp_account = "";
			}
			$txt .= "
<table><tr><td align=\"right\">
<form name=\"ftpfrm\" action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"ftps\">
	<input type=\"hidden\" name=\"edftp_account\" value=\"".$edftp_account."\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newftp_login\" value=\"\">
</td></tr><tr><td align=\"right\">
	".$txt_login_pass[$lang]."</td><td style=\"white-space: nowrap;\"><input type=\"text\" name=\"newftp_pass\" value=\"\">".autoGeneratePassButton("ftpfrm","newftp_pass")."
</td></tr><tr><td align=\"right\">
	".$txt_path[$lang]."</td><td><select name=\"newftp_path\">$path_popup</select>
</td></tr><tr><td align=\"right\">
	</td><td><input type=\"submit\" name=\"newftpaccount\" value=\"Ok\">
</td></tr>
</table>
";
		}else{
			$txt .= "<br>".$txt_maxnumber_of_ftp_account_reached[$lang];
		}
	}
	if(isset($interface)){
		$txt .= "<br>$interface</b></font>";
	}else{
		$txt .= "<br></b></font>";
	}

	return $txt;
*/
}

?>
