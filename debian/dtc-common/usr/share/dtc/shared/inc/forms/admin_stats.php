<?php

function drawAdminTools_AdminStats($admin){
	global $adm_login;
	global $conf_mysql_db;
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;

	$out = "";
	$nowrap = " style=\"white-space:nowrap\" nowrap";

	$stats = fetchAdminStats($admin);
// ["domains"][]["name"]
//              ["du"]
//              ["ftp"]
//              ["http"]
//              ["smtp"]
//              ["pop"]
//              ["total_transfer"]
// ["total_http"]
// ["total_ftp"]
// ["total_email"]
// ["total_transfer"]
// ["total_du_domains"]
// ["db"][]["name"]
//         ["du"]
// ["total_db_du"]
// ["total_du"]
	$id_client = $admin["info"]["id_client"];

	// Print the transfer overall total for this month
	$out .= "<h3>". _("Total transfered bytes this month:") ."</h3>";
	if (!isset($stats["total_http"]))
	{
		$stats["total_http"] = 0;
	}
	$out .= "<br>HTTP: ".smartByte($stats["total_http"])." ".$stats["total_hit"]." " . _("hits");
	if (!isset($stats["total_ftp"]))
	{
		$stats["total_ftp"] = 0;
	}
	$out .= "<br>FTP: ".smartByte($stats["total_ftp"]);
	if (!isset($stats["total_email"]))
	{
		$stats["total_email"] = 0;
	}
	$out .= "<br>Email: ".smartByte($stats["total_email"]);
	if (!isset($stats["total_transfer"]))
	{
		$stats["total_transfer"] = 0;
	}
	$out .= "<br>Total: ". smartByte($stats["total_transfer"]);

	if($id_client != 0){
		$bw_quota = $admin["info"]["bandwidth_per_month_mb"]*1024*1024;
		$out .= " / ".smartByte($bw_quota)."<br>";
		$out .= drawPercentBar($stats["total_transfer"],$bw_quota);
	}

	// Print disk usage
	$out .= "<br><h3>". _("Total disk usage:") ."</h3>";
	if (!isset($stats["total_du_domains"]))
	{
		$stats["total_du_domains"] = 0;
	}
	$out .= "<br>". _("Domain name files:") ." ".smartByte($stats["total_du_domains"]);
	if(isset($stats["total_du_db"])){
		$out .= "<br>". _("Database files:") ." ".smartByte($stats["total_du_db"]);
	}else{
		$out .= "<br>". _("Database files:") ." ".smartByte(0);
	}
	$out .= "<br>". _("Total disk usage:") ." ".smartByte($stats["total_du"]);

	if($id_client != 0){
		$du_quota = $admin["info"]["quota"]*1024*1024;
		$out .= " / ".smartByte($du_quota)."<br>";
		$out .= drawPercentBar($stats["total_du"],$du_quota);
	}

	$out .= "<br><br><h3>". _("Databases disk usage:") ."</h3>";
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="0" cellspacing="1">';
	$out .= "<tr><td$nowrap class=\"dtcDatagrid_table_titles\"><b>"._("Database Name")."</b></td><td$nowrap class=\"dtcDatagrid_table_titles\"><b>"._("Disk usage")."</b></tr>";
	if(isset($stats["db"])){
		$n = sizeof($stats["db"]);
	}else{
		$n = 0;
	}
	for($i=0;$i<$n;$i++){
		if($i % 2){
			$bgcolor = "$nowrap ";
		}else{
			$bgcolor = "$nowrap class=\"alternatecolorline\" ";
		}
		$out .= "<tr>";
		$out .= "<td$bgcolor>".$stats["db"][$i]["name"]."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["db"][$i]["du"])."</td>";
		$out .= "</tr>";
	}
	$out .= '</table>';

	$out .= "<br><br><h3>"._("Domain name traffic and disk usage:")."</h3>";
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="0" cellspacing="1">';
	$out .= "<tr><td class=\"dtcDatagrid_table_titles\"><b>"._("Domain name")."</b></td><td$nowrap class=\"dtcDatagrid_table_titles\"><b>"._("Disk usage")."</b></td>
	<td class=\"dtcDatagrid_table_titles\"><b>POP3</b></td><td class=\"dtcDatagrid_table_titles\"><b>IMAP</b></td><td class=\"dtcDatagrid_table_titles\"><b>SMTP</b></td><td class=\"dtcDatagrid_table_titles\"><b>FTP</b></td><td class=\"dtcDatagrid_table_titles\"><b>HTTP</b></td>
	<td$nowrap class=\"dtcDatagrid_table_titles\"><b>HTTP HITS</b></td>
	<td$nowrap class=\"dtcDatagrid_table_titles\"><b>". _("Total traffic") ."</b></td></tr>";
	if (isset($stats["domains"]))
	{
		for($ad=0;$ad<sizeof($stats["domains"]);$ad++){
			if($ad % 2){
				$bgcolor = "$nowrap ";
			}else{
				$bgcolor = "$nowrap class=\"alternatecolorline\" ";
			}
			$out .= "<tr>";
			$out .= "<td$bgcolor>".$stats["domains"][$ad]["name"]."</td>";
			$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["du"])."</td>";
			$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["pop"])."</td>";
			$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["imap"])."</td>";
			$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["smtp"])."</td>";
			$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["ftp"])."</td>";
			$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["http"])."</td>";
			$out .= "<td$bgcolor>".$stats["domains"][$ad]["hit"]."</td>";
			$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["total_transfer"])."</td>";
			$out .= "</tr>";
		}
	}
	$out .= '</table>';
	return $out;
}

?>
