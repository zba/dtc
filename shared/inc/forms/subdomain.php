<?php

/////////////////////////////////////////////////////
// Draw the form for editing a domain's subdomains //
/////////////////////////////////////////////////////
function drawAdminTools_Subdomain($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $addrlink;

	global $lang;
	global $txt_subdom_list;
	global $txt_subdom_default_sub;
	global $txt_subdom_errase;
	global $txt_subdom_create;

	global $txt_subdom_newname;
	global $txt_subdom_ip;

	global $conf_administrative_site;
	global $conf_hide_password;

	global $txt_number_of_active_subdomains;
	global $txt_subdom_limit_reach;

	global $txt_subdom_newname;
	global $txt_subdom_txtrec;
	global $txt_subdom_dynip_logpass;
	global $txt_subdom_dynip_logpass;
	global $txt_subdom_dynip_login;
	global $txt_subdom_dynip_pass;
	global $txt_subdom_register_global;
	global $txt_subdom_edita;
	global $txt_subdom_scriptadvice;
	global $txt_subdom_windowsusers;
	global $txt_subdom_wwwalias;
	global $txt_subdom_generate_webalizer;

	global $edit_a_subdomain;

	global $dtcshared_path;
	global $pro_mysql_nameservers_table;

	$nbr_subdomain = sizeof($domain["subdomains"]);
	$max_subdomain = $domain["max_subdomain"];
	if($nbr_subdomain >= $max_subdomain){
		$max_color = "color=\"#440000\"";
	}else{
		$max_color = "";
	}
	$nbrtxt = $txt_number_of_active_subdomains[$lang];
	$txt = "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_subdomain ."</font> / <font size=\"-1\">" . $max_subdomain . "</font><br><br>";


	$default_subdomain = $domain["default_subdomain"];
	$webname = $domain["name"];
	$subdomains = $domain["subdomains"];
	$nbr_subdomains = sizeof($subdomains);

	// Print a simple list of available sub-domains
	$txt .= "<font face=\"Verdana, Arial\"><font size=\"-1\"><b><u>".$txt_subdom_list[$lang]."</u><br>";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		$ip = $subdomains[$i]["ip"];
		if($i!=0){
			$txt .= " - ";
		}
		if(isset($_REQUEST["edit_a_subdomain"]) && $sub == $_REQUEST["edit_a_subdomain"]){
			$ip_domain_to_edit = $ip;
			if(isset($subdomains[$i]["login"])){
				$login_to_edit = $subdomains[$i]["login"];
				$pass_to_edit = $subdomains[$i]["pass"];
			}else{
				$login_to_edit = "";
				$pass_to_edit = "";
			}
			$webalizer_to_edit = $subdomains[$i]["webalizer_generate"];
			$w3_alias_to_edit = $subdomains[$i]["w3_alias"];
			$register_globals_to_edit = $subdomains[$i]["register_globals"];
			$txt_rec = $subdomains[$i]["associated_txt_record"];
		}else{
		}
		$txt .= "<a href=\"http://$sub.$webname\" target=\"_blank\">";
		$txt .= $sub;
		$txt .= "</a>";
	}
	$txt .= "<hr width=\"90%\">";

	// Let's start a form !
	$txt .= "<form action=\"?\" methode=\"post\">";
	$txt .= "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">";
	$txt .= "<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">";
	$txt .= "<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";
	$txt .= "<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">";
	$txt .= "<input type=\"hidden\" name=\"whatdoiedit\" value=\"subdomains\">";

	// Popup for choosing default subdomain.
	$txt .= "<table><tr><td align=\"right\">";
	$txt .= $txt_subdom_default_sub[$lang]."</td><td><select name=\"subdomaindefault_name\">";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($domain["default_subdomain"] == "$sub"){
			$txt .= "<option value=\"$sub\" selected>$sub</option>";
		}else{
			$txt .= "<option value=\"$sub\">$sub</option>";
		}
	}
	$txt .= "</select></td><td><input type=\"submit\" name=\"subdomaindefault\" value=\"Ok\"></td></tr>";

	// Print list of subdomains, allow creation of new ones, and destruction of existings.
	$txt .= "<tr><td align=\"right\">".$txt_subdom_errase[$lang]."</td><td><select name=\"delsubdomain_name\">";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($domain["default_subdomain"] == "$sub"){
//			$txt .= "<option value=\"$sub\" selected>$sub</option>";
		}else{
			// Check that the subdomain is not used for a nameserver (in which case it cannot be deleted befor nameserver is deleted from registry)
			if(file_exists($dtcshared_path."/dtcrm")){
				$query = "SELECT * FROM $pro_mysql_nameservers_table WHERE domain_name='$webname' AND subdomain='$sub';";
				$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
				$num_rows = mysql_num_rows($result);
				if($num_rows < 1){
					$txt .= "<option value=\"$sub\">$sub</option>";
				}
			}else{
				$txt .= "<option value=\"$sub\">$sub</option>";
			}
		}
	}
	$txt .= "</select></td><td><input type=\"submit\" name=\"delsubdomain\" value=\"Ok\"></td></tr>";
	$txt .= "<tr><td colspan=\"3\"><hr width=\"90%\"></td></tr>";

	$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>Edit one of your subdomains:</u></b></font></td></tr>";
	$txt .= "<tr><td collspan=\"3\">";
	$in_new = true;
	// List of subdomains to edit
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($i!=0){
			$txt .= " - ";
		}
		if(!isset($_REQUEST["edit_a_subdomain"]) || $sub != $_REQUEST["edit_a_subdomain"]){
			$txt .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&edit_a_subdomain=$sub&edit_domain=$webname&addrlink=$addrlink\">";
		}else{
			$in_new = false;
		}
		$txt .= $sub;
		if(!isset($_REQUEST["edit_a_subdomain"]) || $sub != $_REQUEST["edit_a_subdomain"]){
			$txt .= "</a>";
		}
	}
	if($in_new == false){
		$txt .= "<br><br><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&edit_domain=$webname&addrlink=$addrlink\">new subdomain</a>";
	}else{
		$txt .= "<br><br>new subdomain";
	}
	$txt .= "</td></tr>";

	if(!isset($_REQUEST["edit_a_subdomain"]) || $_REQUEST["edit_a_subdomain"] == ""){
		$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>".$txt_subdom_create[$lang]."</u></b></font></td></tr>";

		// Allow creation of new sub-domains
		if($nbr_subdomain < $max_subdomain){
			$txt .= "<tr><td align=\"right\">".$txt_subdom_newname[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_name\" value=\"\"></td><td></td></tr>";
			$txt .= "<tr><td colspan=\"3\">";
			$txt .= $txt_subdom_ip[$lang]."</td></tr>";
			$txt .= "<tr><td align=\"right\">IP/CNAME:</td><td><input type=\"text\" name=\"newsubdomain_ip\" value=\"\"></td>";
			$txt .= "<tr><td align=\"right\">".$txt_subdom_txtrec[$lang]."</td><td><input type=\"text\" name=\"associated_txt_record\" value=\"\">";
			$txt .= "<tr><td colspan=\"3\">";
			$txt .= $txt_subdom_dynip_logpass[$lang]."</td></tr>";
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_login[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_dynlogin\" value=\"\"></td></tr>";
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_pass[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_dynpass\" value=\"\"></td></tr>";
			$txt .= "<td><input type=\"submit\" name=\"newsubdomain\" value=\"Ok\"></td></tr>";
		}else{
			$txt .= "<td colspan=\"3\">".$txt_subdom_limit_reach[$lang]."</td>";
		}
	}else{
		// Edition of existing subdomains
		$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>".$txt_subdom_edita[$lang]."</u></b></font></td></tr>";
		$txt .= "<tr><td align=\"right\">".$txt_subdom_newname[$lang]."</td><td>".$_REQUEST["edit_a_subdomain"]."</td><td></td></tr>";
		if($register_globals_to_edit == "yes"){
			$checked_yes = " checked";
			$checked_no = "";
		}else{
			$checked_yes = "";
			$checked_no = " checked";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_register_global[$lang]."</td>";
		$txt .= "<td>Yes<input type=\"radio\" name=\"register_globals\" value=\"yes\" $checked_yes>
No<input type=\"radio\" name=\"register_globals\" value=\"no\" $checked_no></td></tr>";
		if($webalizer_to_edit == "yes"){
			$checked_yes = " checked";
			$checked_no = "";
		}else{
			$checked_yes = "";
			$checked_no = " checked";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_generate_webalizer[$lang]."</td>";
		$txt .= "<td>Yes<input type=\"radio\" name=\"webalizer\" value=\"yes\" $checked_yes>
No<input type=\"radio\" name=\"webalizer\" value=\"no\" $checked_no></td></tr>";
		if($w3_alias_to_edit == "yes"){
			$checked_yes = " checked";
			$checked_no = "";
		}else{
			$checked_yes = "";
			$checked_no = " checked";
		}
		$txt .= "<tr><td align=\"right\">".$txt_subdom_wwwalias[$lang].$_REQUEST["edit_a_subdomain"]." alias:</td>";
		$txt .= "<td>Yes<input type=\"radio\" name=\"w3_alias\" value=\"yes\" $checked_yes>
No<input type=\"radio\" name=\"w3_alias\" value=\"no\" $checked_no></td></tr>";
		$txt .= "<tr><td colspan=\"3\">";
		$txt .= $txt_subdom_ip[$lang]."</td></tr>";
		
		$txt .= "<tr><td align=\"right\">IP/CNAME:</td><td><input type=\"hidden\" name=\"subdomain_name\" value=\"".$_REQUEST["edit_a_subdomain"]."\">
		<input type=\"hidden\" name=\"edit_a_subdomain\" value=\"".$_REQUEST["edit_a_subdomain"]."\"><input type=\"text\" name=\"newsubdomain_ip\" value=\"$ip_domain_to_edit\"></td></tr>";
		$txt .= "<tr><td align=\"right\">".$txt_subdom_txtrec[$lang]."</td><td><input type=\"text\" name=\"associated_txt_record\" value=\"$txt_rec\">";
		$txt .= "<tr><td colspan=\"3\">";
		$txt .= $txt_subdom_dynip_logpass[$lang]."</td></tr>";

		$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_login[$lang]."</td><td><input type=\"text\" name=\"subdomain_dynlogin\" value=\"$login_to_edit\"></td></tr>";
		if ($conf_hide_password)
		{	
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_pass[$lang]."</td><td><input type=\"password\" name=\"subdomain_dynpass\" value=\"$pass_to_edit\"></td></tr>";
		} else {
			$txt .= "<tr><td align=\"right\">".$txt_subdom_dynip_pass[$lang]."</td><td><input type=\"text\" name=\"subdomain_dynpass\" value=\"$pass_to_edit\"></td></tr>";
		}
		$txt .= "<tr><td>&nbsp;</td><td><input type=\"submit\" name=\"edit_one_subdomain\" value=\"Ok\"></td></tr>";
		if($login_to_edit != "" && isset($login_to_edit)){
			$txt .= "<tr><td colspan=\"3\">".$txt_subdom_scriptadvice[$lang]."<br>
<pre>
#!/bin/sh

LYNX=/usr/bin/lynx
DOMAIN=$edit_domain
LOGIN=$login_to_edit
PASS=$pass_to_edit
SCRIPT_URL=\"https://$conf_administrative_site/dtc/\"

\$LYNX -source \$SCRIPT_URL\"dynip.php?login=\"\$LOGIN\"&pass=\"\$PASS\"&domain=\"\$DOMAIN
</pre><br>
".$txt_subdom_windowsusers[$lang]."<br>
https://$conf_administrative_site/dtc/dynip.php?login=$login_to_edit&pass=$pass_to_edit&domain=$edit_domain
</td></tr>
";
		}
	}
	$txt .= "</table></form>";

	$txt .= "</b></font></font>";
	return $txt;
}


?>
