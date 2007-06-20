<?php

function drawAdminTools_DomainDNS($admin,$eddomain){
	global $lang;

	global $txt_other_mx_servers;
	global $txt_primary_mx_server;
	global $txt_other_dns_ip;
	global $txt_primari_dns_ip;
	global $txt_comment_confirurate_your_domain_name;
	global $txt_confirurate_your_domain_name;

	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $conf_generated_file_path;

	// The domain DNS configuration
	$domain_dns_mx_conf_form = "
<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$eddomain["name"]."\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"new_dns_and_mx_config\" value=\"Ok\">".
dtcFormTableAttrs().
dtcFormLineDraw($txt_primari_dns_ip[$lang],"<input type=\"text\" name=\"new_dns_1\" value=\"".$eddomain["primary_dns"]."\">");
	if($eddomain["other_dns"] != "default"){
		$other_dns = explode("|",$eddomain["other_dns"]);
		$dns2 = $other_dns[0];
		$nbr_other_dns = sizeof($other_dns);
	}else{
		$dns2 = "default";
		$nbr_other_dns = 1;
	}
	$domain_dns_mx_conf_form .= dtcFormLineDraw($txt_other_dns_ip[$lang],"<input type=\"text\" name=\"new_dns_2\" value=\"$dns2\">");

	$new_dns_num = 3;
	for($z=1;$z<$nbr_other_dns;$z++){
		if($z != 1){
			$domain_dns_mx_conf_form .= "<tr><td></td>";
		}
		$domain_dns_mx_conf_form .= dtcFormLineDraw("","<input type=\"text\" name=\"new_dns_$new_dns_num\" value=\"".$other_dns[$z]."\">");
		$new_dns_num += 1;
	}
	$domain_dns_mx_conf_form .= dtcFormLineDraw("","<input type=\"text\" name=\"new_dns_$new_dns_num\" value=\"\">");
//	$domain_dns_mx_conf_form .= "<tr><td></td><td><input type=\"text\" name=\"new_dns_$new_dns_num\" value=\"\"><br><br></td></tr>";

	// The domain MX configuration
	$domain_dns_mx_conf_form .= dtcFormLineDraw($txt_primary_mx_server[$lang],"<input type=\"text\" name=\"new_mx_1\" value=\"".$eddomain["primary_mx"]."\">");
	if($eddomain["other_mx"] == "default" && $eddomain["primary_dns"] == "default"){
		$domain_dns_mx_conf_form .= dtcFormLineDraw($txt_other_mx_servers[$lang],"<input type=\"text\" name=\"new_mx_2\" value=\"\">");
	}else{
		$new_mx_num = 2;
		$other_mx = explode("|",$eddomain["other_mx"]);
		$nbr_other_mx = sizeof($other_mx);
		for($z=0;$z<$nbr_other_mx;$z++){
			if($z != 0){
				$domain_dns_mx_conf_form .= dtcFormLineDraw("","<input type=\"text\" name=\"new_mx_$new_mx_num\" value=\"".$other_mx[$z]."\">");
			}else{
				$domain_dns_mx_conf_form .= dtcFormLineDraw($txt_other_mx_servers[$lang],"<input type=\"text\" name=\"new_mx_$new_mx_num\" value=\"".$other_mx[$z]."\">");
			}
			$new_mx_num += 1;
		}
		$domain_dns_mx_conf_form .= dtcFormLineDraw("","<input type=\"text\" name=\"new_mx_$new_mx_num\" value=\"\">");
	}

	$domain_dns_mx_conf_form .= dtcFormLineDraw("Domain root TXT record:","<input type=\"text\" name=\"txt_root_entry\" value=\"".$eddomain["txt_root_entry"]."\">");
	$domain_dns_mx_conf_form .= dtcFormLineDraw("Domain root TXT record2:","<input type=\"text\" name=\"txt_root_entry2\" value=\"".$eddomain["txt_root_entry2"]."\">");
	$domain_dns_mx_conf_form .= dtcFromOkDraw();
	$domain_dns_mx_conf_form .= "</form></table>";

	$handle = @fopen($conf_generated_file_path."/zones/".$eddomain["name"], "r");
	if ($handle) {
		while (!feof($handle)) {
			$lines[] = fgets($handle, 4096);
		}
		fclose($handle);
		$zonefile_content = "<pre>";
		foreach ($lines as $line_num => $line) {
			$zonefile_content .= '<b>' . $line_num . '</b>: ' . htmlspecialchars($line);
		}
		$zonefile_content .= "</pre>";
	}else{
		$zonefile_content = "Could not load zonefile: permission denied or file not existant?";
	}

	return "<h3>".$txt_confirurate_your_domain_name[$lang]."</h3><br><br>
	$txt_comment_confirurate_your_domain_name[$lang]<br>
	$domain_dns_mx_conf_form<br>
	<h3>Named zonefile:</h3>
	$zonefile_content";
}

?>
