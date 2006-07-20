<?php

////////////////////////////////////////////////////
// One domain name ssh account collection edition //
////////////////////////////////////////////////////
function drawAdminTools_SSH($domain,$adm_path){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;

	global $edssh_account;
	global $addrlink;

	global $lang;
	global $txt_ssh_account_list;
	global $txt_ssh_new_account;
	global $txt_ssh_account_edit;
	global $txt_ssh_new_account_link;
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_path;
	global $conf_hide_password;

	global $txt_number_of_active_ssh;
	global $txt_maxnumber_of_ssh_account_reached;

	if(isset($domain["sshs"])){
		$nbr_ssh = sizeof($domain["sshs"]);
		$sshs = $domain["sshs"];
		$nbr_account = $nbr_ssh;
	}else{
		$nbr_ssh = 0;
		$nbr_account = 0;
	}
	$max_ssh = $domain["max_ssh"];
	if($nbr_ssh >= $max_ssh){
		$max_color = "color=\"#440000\"";
	}else{
		$max_color = "";
	}
	$nbrtxt = $txt_number_of_active_ssh[$lang];
	$txt = "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_ssh ."</font> / <font size=\"-1\">" . $max_ssh . "</font><br><br>";

	$txt .= "<font face=\"Verdana, Arial\"><font size=\"-1\"><b><u>".$txt_ssh_account_list[$lang]."</u><br>";
	for($i=0;$i<$nbr_account;$i++){
		$ssh = $sshs[$i];
		$login = $ssh["login"];
		if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && $login == $_REQUEST["edssh_account"]){
			$pass = $ssh["passwd"];
			$sshpath = $ssh["path"];
		}
		if($i != 0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=sshs&edssh_account=$login\">$login</a>";
	}

//	if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && isset($sshpath) && $sshpath == "$adm_path"){
//		$is_selected = " selected";
//	}else{
//		$is_selected ="";
//	}
//	$path_popup = "<option value=\"$adm_path\"$is_selected>/</option>";
//	if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && isset($sshpath) && $sshpath == "$adm_path/$edit_domain"){
//		$is_selected = " selected";
//	}else{
//		$is_selected ="";
//	}
//	$path_popup .= "<option value=\"$adm_path/$edit_domain\"$is_selected>/$edit_domain/</option>";
	$path_popup = "";
	$nbr_subdomains = sizeof($domain["subdomains"]);
	// first add the domains main directory, so a SSH user can access all base accounts
	if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && isset($sshpath) && $sshpath == "$adm_path/$edit_domain/"){
		$is_selected = " selected";
	}else{
		$is_selected ="";
	}
	$path_popup .= "<option value=\"$adm_path/$edit_domain/\"$is_selected>/$edit_domain/ [Uses www chroot]</option>";

	// then add the admin users directory
	if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && isset($sshpath) && $sshpath == "$adm_path/"){
		$is_selected = " selected";
	}else{
		$is_selected ="";
	}
	$path_popup .= "<option value=\"$adm_path/\"$is_selected>/ [Uses www chroot]</option>";
	
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub_name = $domain["subdomains"][$i]["name"];
		if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && isset($sshpath) && $sshpath == "$adm_path/$edit_domain/subdomains/$sub_name"){
			$is_selected = " selected";
		}else{
			$is_selected ="";
		}
		$path_popup .= "<option value=\"$adm_path/$edit_domain/subdomains/$sub_name\"$is_selected>/$edit_domain/subdomains/$sub_name/</option>";

//		if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && isset($sshpath) && $sshpath == "$adm_path/$edit_domain/subdomains/$sub_name/html"){
//			$is_selected = " selected";
//		}else{
//			$is_selected ="";
//		}
//		$path_popup .= "<option value=\"$adm_path/$edit_domain/subdomains/$sub_name/html\"$is_selected>/$edit_domain/subdomains/$sub_name/html/</option>";
	}

	if(isset($_REQUEST["edssh_account"]) && $_REQUEST["edssh_account"] != "" && (!isset($_REQUEST["deletesshaccount"]) || $_REQUEST["deletesshaccount"] != "Delete")){
		$txt .= "<br><br><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=sshs\">".$txt_ssh_new_account_link[$lang]."</A><br>";
		$txt .= "
<br><u>".$txt_ssh_account_edit[$lang]."</u>
<table>
<tr><td align=\"right\">".$txt_login_login[$lang]."</td><td>".$_REQUEST["edssh_account"]."</td></tr>
<tr><td align=\"right\">
<form name=\"sshfrm\" action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"sshs\">
	<input type=\"hidden\" name=\"edssh_account\" value=\"".$_REQUEST["edssh_account"]."\">";
	$genpass = autoGeneratePassButton("sshfrm","edssh_pass");
	if ($conf_hide_password == "yes"){
		$txt .= $txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"edssh_pass\" value=\"$pass\">$genpass";
	}else{
		$txt .= $txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"edssh_pass\" value=\"$pass\">$genpass";
	}
$txt .= "
</td></tr><tr><td align=\"right\">
	".$txt_path[$lang]."</td><td><select name=\"edssh_path\">$path_popup</select>
</td></tr><tr><td align=\"right\">
	<input type=\"submit\" name=\"deletesshaccount\" value=\"Delete\"></td><td><input type=\"submit\" name=\"update_ssh_account\" value=\"Ok\">
</td></tr>
</table>
</form>
<br>
";
	}else{
		$txt .= "
<br><br><u>".$txt_ssh_new_account[$lang]."</u>";
		if($nbr_ssh < $max_ssh){
			if(isset($_REQUEST["edssh_account"])){
				$edssh_account = $_REQUEST["edssh_account"];
			}else{
				$edssh_account = "";
			}
			$txt .= "
<table><tr><td align=\"right\">
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"sshs\">
	<input type=\"hidden\" name=\"edssh_account\" value=\"".$edssh_account."\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newssh_login\" value=\"\">
</td></tr><tr><td align=\"right\">
	".$txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"newssh_pass\" value=\"\">
</td></tr><tr><td align=\"right\">
	".$txt_path[$lang]."</td><td><select name=\"newssh_path\">$path_popup</select>
</td></tr><tr><td align=\"right\">
	</td><td><input type=\"submit\" name=\"newsshaccount\" value=\"Ok\">
</td></tr>
</table>
";
		}else{
			$txt .= "<br>".$txt_maxnumber_of_ssh_account_reached[$lang];
		}
	}
	if(isset($interface)){
		$txt .= "<br>$interface</b></font>";
	}else{
		$txt .= "<br></b></font>";
	}

	return $txt;
}

?>
