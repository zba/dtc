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

	// The domain DNS configuration
	$domain_dns_mx_conf_form = "<table><tr><td align=\"right\" nowrap><form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$eddomain["name"]."\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
".$txt_primari_dns_ip[$lang]."</td><td><input type=\"text\" name=\"new_dns_1\" value=\"".$eddomain["primary_dns"]."\"></td></tr>
<tr><td align=\"right\" nowrap>".$txt_other_dns_ip[$lang]."</td><td>";
	if($eddomain["other_dns"] != "default"){
		$other_dns = explode("|",$eddomain["other_dns"]);
		$dns2 = $other_dns[0];
		$nbr_other_dns = sizeof($other_dns);
	}else{
		$dns2 = "default";
		$nbr_other_dns = 1;
	}
	$domain_dns_mx_conf_form .= "<input type=\"text\" name=\"new_dns_2\" value=\"$dns2\"></td></tr>";
	$domain_dns_mx_conf_form .= "<tr><td align=\"right\" nowrap>".$txt_other_dns_ip[$lang]."</td>";

	$new_dns_num = 3;
	for($z=1;$z<$nbr_other_dns;$z++){
		if($z != 1){
			$domain_dns_mx_conf_form .= "<tr><td></td>";
		}
		$domain_dns_mx_conf_form .= "<td><input type=\"text\" name=\"new_dns_$new_dns_num\" value=\"".$other_dns[$z]."\"></td></tr>";
		$new_dns_num += 1;
	}
	$domain_dns_mx_conf_form .= "<tr><td></td><td><input type=\"text\" name=\"new_dns_$new_dns_num\" value=\"\"><br><br></td></tr>";

	// The domain MX configuration
	//if($eddomain["primary_dns"] == "default"){
		$domain_dns_mx_conf_form .= "<tr><td align=\"right\" nowrap>".$txt_primary_mx_server[$lang]."</td><td><input type=\"text\" name=\"new_mx_1\" value=\"".$eddomain["primary_mx"]."\"></td></tr>
<tr><td align=\"right\">".$txt_other_mx_servers[$lang]."</td><td>";
		if($eddomain["other_mx"] == "default"){
			$domain_dns_mx_conf_form .= "<input type=\"text\" name=\"new_mx_2\" value=\"\"></td></tr>";
		}else{
			$new_mx_num = 2;
			$other_mx = explode("|",$eddomain["other_mx"]);
			$nbr_other_mx = sizeof($other_mx);
			for($z=0;$z<$nbr_other_mx;$z++){
				if($z != 0)	$domain_dns_mx_conf_form .= "<tr><td></td><td>";
				$domain_dns_mx_conf_form .= "<input type=\"text\" name=\"new_mx_$new_mx_num\" value=\"".$other_mx[$z]."\"></td></tr>";
				$new_mx_num += 1;
			}
			$domain_dns_mx_conf_form .= "<tr><td></td><td><input type=\"text\" name=\"new_mx_$new_mx_num\" value=\"".$other_mx[$z]."\"></td></tr>";
		}
//	}
	$domain_dns_mx_conf_form .= "<tr><td></td><td><input type=\"submit\" name=\"new_dns_and_mx_config\" value=\"Ok\"></form></td></tr></table>";

	return "<b><u>".$txt_confirurate_your_domain_name[$lang]."</b></u><br><br>
	$txt_comment_confirurate_your_domain_name[$lang]<br>
	$domain_dns_mx_conf_form";
}

?>
