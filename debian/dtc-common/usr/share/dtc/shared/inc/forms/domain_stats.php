<?php

function drawAdminTools_DomainStats($admin,$eddomain){
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;
	global $pro_mysql_acc_email_table;
	
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	
	global $conf_htpasswd_path;

	$out = "";

//	sum_http($eddomain["name"]);
	$query_http = "SELECT sum(bytes_sent) as bytes_sent FROM $pro_mysql_acc_http_table WHERE domain='".$eddomain["name"]."'
	AND month='".date("n")."' AND year='".date("Y")."'";
	$result_http = mysql_query($query_http)or die("Cannot execute query \"$query_http\"");
	$num_rows = mysql_num_rows($result_http);
	if($num_rows > 0)
		$http_amount = mysql_result($result_http,0,"bytes_sent");
	else
		$http_amount = 0;

//	sum_ftp($eddomain["name"]);
	$q = "SELECT sum(transfer) as transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$eddomain["name"]."'
	AND month='".date("m")."' AND year='".date("Y")."'";
	$r = mysql_query($q) or die("Cannot execute query \"$q\" !".mysql_error().
	" line ".__LINE__." file ".__FILE__);
	$num_rows = mysql_num_rows($r);
	if($num_rows > 0)
		$ftp_amount = mysql_result($r,0,"transfer");
	else
		$ftp_amount = 0;

//	sum_email($eddomain["name"]);
    $q = "SELECT sum(smtp_trafic) as smtp_trafic,sum(pop_trafic) as pop_trafic,sum(imap_trafic) as imap_trafic FROM $pro_mysql_acc_email_table WHERE domain_name='".$eddomain["name"]."'
	AND month='".date("m")."' AND year='".date("Y")."'";
    $r = mysql_query($q) or die("Cannot execute query \"$q\" !".mysql_error().
	" line ".__LINE__." file ".__FILE__);
    $num_rows = mysql_num_rows($r);
	if($num_rows > 0){
	    $smtp_trafic = mysql_result($r,0,"smtp_trafic");
	    if (is_null($smtp_trafic)) $smtp_trafic = 0;
	    $pop_trafic = mysql_result($r,0,"pop_trafic");
	    if (is_null($pop_trafic)) $pop_trafic = 0;
	    $imap_trafic = mysql_result($r,0,"imap_trafic");
	    if (is_null($imap_trafic)) $imap_trafic = 0;
	}else{
		$smtp_trafic = 0;
		$pop_trafic = 0;
		$imap_trafic = 0;
	}
	$out .= "<h3>". _("Total transfered bytes this month") ."</h3>";
	$out .= "<br>HTTP: ".smartByte($http_amount);
	$out .= "<br>FTP:  ".smartByte($ftp_amount);
	$out .= "<br>SMTP:  ".smartByte($smtp_trafic);
	$out .= "<br>POP3:  ".smartByte($pop_trafic);
	$out .= "<br>IMAP:  ".smartByte($imap_trafic);
	$out .= "<br>Total: ". smartByte($http_amount + $ftp_amount + $pop_trafic + $smtp_trafic + $imap_trafic);

	$out .= "<br><br><h3>"._("Detailed web statistics (HTTP) of your subdomains:") ."</h3><br>";
	for($i=0;$i<sizeof($eddomain["subdomains"]);$i++){
		if($i != 0)	$out .= " - ";
		$out .= "<a target=\"_blank\" href=\"http://".
		$eddomain["subdomains"][$i]["name"].".".$eddomain["name"]."/stats/\">";
		$out .= $eddomain["subdomains"][$i]["name"];
		$out .= "</a>";
	}
	
	$q = "SELECT stats_login,stats_pass,stats_subdomain FROM $pro_mysql_domain_table  WHERE name='".$eddomain["name"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	$a = mysql_fetch_array($r);
	$out .= "<br><br><strong>". _("Protect your logs and stats folder with a password") ."</strong><br>";
	
	$out .= "<table>";
	$hidden = "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		<input type=\"hidden\" name=\"addrlink\" value=\"".$addrlink."\">
		<input type=\"hidden\" name=\"edit_domain\" value=\"".$eddomain["name"]."\">";
	
	if(empty($a["stats_login"])){
		$out .= "<tr><td><form action=\"?\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"add_stats_login\">
		". _("Login:") ." <input type=\"text\" name=\"stats_login\" value=\"\"> ". _("Password:"). " 
		<input type=\"text\" name=\"stats_pass\" value=\"\"></td></tr>
		<tr><td>". _("Copy to subdomains:") ." <input type=\"checkbox\" name=\"stats_subdomain\" value=\"\"></td></tr>
		<tr><td><input type=\"submit\" value=\"". _("Ok") ."\"></form></td></tr>";
	}else{
		$out .= "<tr><td><form action=\"?\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"modify_stats_login_pass\">
		". _("Login:") ." <input type=\"text\" name=\"stats_login\" value=\"".$a["stats_login"]."\">
		". _("Password:") ." <input type=\"password\" name=\"stats_pass\" value=\"".$a["stats_pass"]."\"></td></tr>
		<tr><td>". _("Copy to subdomains:") ." <input type=\"checkbox\" name=\"stats_subdomain\" value=\"\" ";
		if($a["stats_subdomain"]=='yes')
			$out .= "checked";
		$out .= "></td></tr>
		<tr><td><input type=\"submit\" value=\"". _("Save") ."\"></form>
		<form action=\"?\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"del_stats_login\">
		<input type=\"hidden\" name=\"stats_login\" value=\"".$a["stats_login"]."\">
		<input type=\"hidden\" name=\"stats_pass\" value=\"".$a["stats_pass"]."\">
		<input type=\"hidden\" name=\"stats_subdomain\" value=\" ".$a["stats_subdomain"]."\">
		<input type=\"submit\" value=\"". _("Delete") ."\"></form></td></tr>";
		
	}
	
	$out .= "</table>";
	return $out;
}


?>
