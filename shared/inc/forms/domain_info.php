<?php

function drawAdminTools_DomainInfo($admin,$eddomain){
	global $lang;
	global $txt_your_domain;
	global $txt_your_domain_quota;
	global $txt_your_domain_email;
	global $txt_your_domain_ftp;
	global $txt_your_domain_subdomain;

	global $txt_other_mx_servers;
	global $txt_primary_mx_server;
	global $txt_other_dns_ip;
	global $txt_primari_dns_ip;
	global $txt_comment_confirurate_your_domain_name;
	global $txt_confirurate_your_domain_name;
	global $txt_total_transfered_bytes_this_month;
	global $txt_are_disk_usage;
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $dtcshared_path;
	global $conf_administrative_site;

	// TODO : fetch the expiration in the database
//	$webname = $eddomain["name"];
//	$query = "SELECT * FROM $pro_mysql_command_table WHERE nom_domaine='$webname'";

	// Retrive domain config
	$quota = $eddomain["quota"];
	$max_email = $eddomain["max_email"];
	$max_ftp = $eddomain["max_ftp"];
	$max_subdomain = $eddomain["max_subdomain"];

	$adm_path = $admin["info"]["path"];
	$webname = $eddomain["name"];

	// Retrive disk usage
	$du_string = exec("du -sm $adm_path/$webname --exclude=access.log",$retval);
	$du_state = explode("\t",$du_string);
	$du = $du_state[0];

	// Retrive number of mailbox
	$email_nbr = sizeof($eddomain["emails"]);
	// Retrive number of ftp account
	$ftp_nbr = sizeof($eddomain["ftps"]);
	// Retrive number of ftp account
	$subdomain_nbr = sizeof($eddomain["subdomains"]);

	$total_http_transfer = fetchHTTPInfo($webname);
	$total_ftp_transfer = fetchFTPInfo($webname);
	$total_pop_transfer = fetchPOPInfo($webname);
	$total_imap_transfer = fetchIMAPInfo($webname);
	$total_smtp_transfer = fetchSMTPInfo($webname);
	$total_transfer = smartByte($total_http_transfer + $total_ftp_transfer + $total_smtp_transfer + $total_pop_transfer + $total_imap_transfer);

	$out .= "<b><u>".$txt_your_domain[$lang]."</u></b><br>
	".$txt_total_transfered_bytes_this_month[$lang]." $total_transfer<br>
	".$txt_are_disk_usage[$lang]." $du / $quota MBytes<br>
	".$txt_your_domain_email[$lang]." $email_nbr / $max_email<br>
	".$txt_your_domain_ftp[$lang]." $ftp_nbr / $max_ftp<br>
	".$txt_your_domain_subdomain[$lang]." $subdomain_nbr /
	$max_subdomain<br><br>";

	$out .= "<b><u>".$txt_your_domain[$lang]."</u></b><br>
	Use http(s)://".$conf_administrative_site."/".$_REQUEST["addrlink"]." aliasing:";

	if($eddomain["gen_unresolved_domain_alias"] == "yes"){
		$radio_yes = " checked";
		$radio_no = "";
	}else{
		$radio_no = " checked";
		$radio_yes = "";
	}

	$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">".$txt_password[$lang]."<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"radio\" name=\"domain_gen_unresolv_alias\" value=\"yes\"$radio_yes>Yes
<input type=\"radio\" name=\"domain_gen_unresolv_alias\" value=\"no\"$radio_no>No
<input type=\"submit\" name=\"change_unresolv_alias\" value=\"Ok\"></form>";

/*	if(file_exists($dtcshared_path."/dtcrm")){
		$out .= "<b><u>Domain registration info:</u></b><br><br>";
		if($eddomain["whois"] = "away"){
			$out .= "Domain has been registred using another registrar.<br>
			Click <a href=\"\">here</a> to order transfere";
		}else if($eddomain["whois"] == "linked"){
		}
	}
*/	return $out;
}

?>