<?php

function drawAdminTools_DomainInfo($admin,$eddomain){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $dtcshared_path;
	global $conf_administrative_site;
	global $pro_mysql_domain_table;
	$out = "";

	// TODO : fetch the expiration in the database
//	$webname = $eddomain["name"];
//	$query = "SELECT * FROM $pro_mysql_command_table WHERE nom_domaine='$webname'";

	// Retrive domain config
	$quota = $eddomain["quota"];
	$max_email = $eddomain["max_email"];
	$max_ftp = $eddomain["max_ftp"];
	$max_subdomain = $eddomain["max_subdomain"];
	$domain_parking = $eddomain["domain_parking"];
	$domain_parking_type = $eddomain["domain_parking_type"];

	$adm_path = $admin["info"]["path"];
	$webname = $eddomain["name"];

	// Retrive disk usage
//	$du_string = exec("du -sm $adm_path/$webname --exclude=access.log",$retval);
//	$du_state = explode("\t",$du_string);
//	$du = $du_state[0];

	// The upper version might be too slow and give a bad feeling to the user. This one should be a lot better:
	$du_stat = $eddomain["du_stat"];
	$du = $du_stat;

	// Retrive number of mailbox
	if(isset($eddomain["emails"]))	$email_nbr = sizeof($eddomain["emails"]);
	else	$email_nbr = 0;
	// Retrive number of ftp account
	if(isset($eddomain["ftps"]))	$ftp_nbr = sizeof($eddomain["ftps"]);
	else	$ftp_nbr = 0;
	// Retrive number of ftp account
	$subdomain_nbr = sizeof($eddomain["subdomains"]);

	$total_http_transfer = fetchHTTPInfo($webname);
	$total_ftp_transfer = fetchFTPInfo($webname);
	$total_pop_transfer = fetchPOPInfo($webname);
	$total_imap_transfer = fetchIMAPInfo($webname);
	$total_smtp_transfer = fetchSMTPInfo($webname);
	$total_transfer = smartByte($total_http_transfer + $total_ftp_transfer + $total_smtp_transfer + $total_pop_transfer + $total_imap_transfer);

	$out .= "<h3>". _("Your domain:") ."</h3><br>
	". _("Total transfered bytes this month:") ." $total_transfer<br>
	". _("Your area disk usage:") ." ".smartByte($du)." / $quota MBytes<br>
	". _("Mailboxes:") ." $email_nbr / $max_email<br>
	". _("FTP accounts:") ." $ftp_nbr / $max_ftp<br>
	". _("Subdomains:") ." $subdomain_nbr / $max_subdomain<br><br>";

	$out .= "<h3>". _("Your domain") ."</h3>
	". _("Use") ." http(s)://".$conf_administrative_site."/www.".$_REQUEST["addrlink"]." ". _("aliasing") .":";

	if($eddomain["gen_unresolved_domain_alias"] == "yes"){
		$radio_yes = " checked";
		$radio_no = "";
	}else{
		$radio_no = " checked";
		$radio_yes = "";
	}

	$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"radio\" name=\"domain_gen_unresolv_alias\" value=\"yes\"$radio_yes>"._("Yes")."
<input type=\"radio\" name=\"domain_gen_unresolv_alias\" value=\"no\"$radio_no>"._("No")."
<input type=\"hidden\" name=\"change_unresolv_alias\" value=\"Ok\">
<input type=\"image\" src=\"gfx/stock_apply_20.png\"></form><br>";

	$out .= _("This domain will be the alias of the following domain (domain parking):");
	$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"set_domain_parcking\" value=\"Ok\">
<select name=\"domain_parking_value\">
<option value=\"no-parking\">". _("No parking") ."</option>
";
	$q = "SELECT name FROM $pro_mysql_domain_table WHERE owner='$adm_login' AND domain_parking='no-parking' AND name NOT LIKE '".$_REQUEST["addrlink"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." in file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($domain_parking == $a["name"]){
			$checked = " selected ";
		}else{
			$checked = "";
		}
		$out .= "<option value=\"".$a["name"]."\"$checked>".$a["name"]."</option>";
	}

	$out .= "</select><br>";
	if($domain_parking_type == "redirect"){
		$redirect_selected = " checked ";
		$same_docroot_selected = " ";
	}else{
		$redirect_selected = " ";
		$same_docroot_selected = " checked ";
	}
	$out .= "<input type=\"radio\" name=\"domain_parking_type\" value=\"redirect\" $redirect_selected>" ._("Redirection")." ";
	$out .= "<input type=\"radio\" name=\"domain_parking_type\" value=\"same_docroot\" $same_docroot_selected>" ._("Same DocumentRoot")." ";
	$out .= "<input type=\"image\" src=\"gfx/stock_apply_20.png\"></form>";
	$out .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&action=export_domain&addrlink=".$_REQUEST["addrlink"]."\" target=\"_blank\">". _("Export this domain to a file") ."</a>";
	return $out;
}

?>
