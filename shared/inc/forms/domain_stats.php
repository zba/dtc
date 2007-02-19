<?php

function drawAdminTools_DomainStats($admin,$eddomain){
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;
	global $pro_mysql_acc_email_table;

	global $txt_total_transfered_bytes_this_month;
	global $txt_stats_http_subdom;
	global $lang;

	$out = "";

//	sum_http($eddomain["name"]);
	$query_http = "SELECT bytes_sent FROM $pro_mysql_acc_http_table WHERE domain='".$eddomain["name"]."'
	AND month='".date("n")."' AND year='".date("Y")."'";
	$result_http = mysql_query($query_http)or die("Cannot execute query \"$query_http\"");
	$num_rows = mysql_num_rows($result_http);
	if($num_rows > 0)
		$http_amount = mysql_result($result_http,0,"bytes_sent");
	else
		$http_amount = 0;

//	sum_ftp($eddomain["name"]);
	$q = "SELECT transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$eddomain["name"]."'
	AND month='".date("m")."' AND year='".date("Y")."'";
	$r = mysql_query($q) or die("Cannot execute query \"$q\" !".mysql_error().
	" line ".__LINE__." file ".__FILE__);
	$num_rows = mysql_num_rows($r);
	if($num_rows > 0)
		$ftp_amount = mysql_result($r,0,"transfer");
	else
		$ftp_amount = 0;

//	sum_email($eddomain["name"]);
    $q = "SELECT smtp_trafic,pop_trafic,imap_trafic FROM $pro_mysql_acc_email_table WHERE domain_name='".$eddomain["name"]."'
	AND month='".date("m")."' AND year='".date("Y")."'";
    $r = mysql_query($q) or die("Cannot execute query \"$q\" !".mysql_error().
	" line ".__LINE__." file ".__FILE__);
    $num_rows = mysql_num_rows($r);
	if($num_rows > 0){
	    $smtp_trafic = mysql_result($r,0,"smtp_trafic");
	    $pop_trafic = mysql_result($r,0,"pop_trafic");
	    $imap_trafic = mysql_result($r,0,"imap_trafic");
	}else{
		$smtp_trafic = 0;
		$pop_trafic = 0;
		$imap_trafic = 0;
	}
	$out .= "<h3>".$txt_total_transfered_bytes_this_month[$lang]."</h3>";
	$out .= "<br>HTTP: ".smartByte($http_amount);
	$out .= "<br>FTP:  ".smartByte($ftp_amount);
	$out .= "<br>SMTP:  ".smartByte($smtp_trafic);
	$out .= "<br>POP3:  ".smartByte($pop_trafic);
	$out .= "<br>IMAP:  ".smartByte($imap_trafic);
	$out .= "<br>Total: ". smartByte($http_amount + $ftp_amount + $pop_trafic + $smtp_trafic + $imap_trafic);

	$out .= "<br><br><h3>".$txt_stats_http_subdom[$lang]."</h3><br>";
	for($i=0;$i<sizeof($eddomain["subdomains"]);$i++){
		if($i != 0)	$out .= " - ";
		$out .= "<a target=\"_blank\" href=\"http://".
		$eddomain["subdomains"][$i]["name"].".".$eddomain["name"]."/stats/\">";
		$out .= $eddomain["subdomains"][$i]["name"];
		$out .= "</a>";
	}

	return $out;
}


?>
