<?php

function drawAdminTools_AdminStats($admin){
	global $adm_login;
	global $conf_mysql_db;
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;

	global $lang;

	global $txt_total_transfered_bytes_this_month;
	global $txt_total_trafic;
	global $txt_disk_usage;
	global $txt_domain_name;
	global $txt_domain_name_trafic_du;
	global $txt_disk_usage;
	global $txt_database_name;
	global $txt_databases_disk_usage;
	global $txt_total_disk_usage;
	global $txt_database_files;
	global $txt_domain_name_files;
	global $txt_are_disk_usage;
	global $txt_total_transfered_bytes_this_month;

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

	$out .= "<u><b>".$txt_total_transfered_bytes_this_month[$lang]."</b></u>";
	$out .= "<br>HTTP: ".smartByte($stats["total_http"]);
	$out .= "<br>FTP: ".smartByte($stats["total_ftp"]);
	$out .= "<br>Email: ".smartByte($stats["total_email"]);
	$out .= "<br>Total: ". smartByte($stats["total_transfer"]);

	if($id_client != 0){
		$bw_quota = $admin["info"]["bandwidth_per_month_mb"]*1024*1024;
		$out .= " / ".smartByte($bw_quota)."<br>";
		$out .= drawPercentBar($stats["total_transfer"],$bw_quota);
	}
	$out .= "<br><u>".$txt_are_disk_usage[$lang]."<b></b></u>";
	$out .= "<br>".$txt_domain_name_files[$lang]." ".smartByte($stats["total_du_domains"]);
	if(isset($stats["total_db_du"])){
		$out .= "<br>".$txt_database_files[$lang]." ".smartByte($stats["total_db_du"]);
	}else{
		$out .= "<br>".$txt_database_files[$lang]." ".smartByte(0);
	}
	$out .= "<br>".$txt_total_disk_usage[$lang]." ".smartByte($stats["total_du"]);

	if($id_client != 0){
		$du_quota = $admin["info"]["quota"]*1024*1024;
		$out .= " / ".smartByte($du_quota)."<br>";
		$out .= drawPercentBar($stats["total_du"],$du_quota);
	}

	$out .= "<br><br><u><b>".$txt_databases_disk_usage[$lang]."</b></u>";
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="0" cellspacing="1">';
	$out .= "<tr><td$nowrap><b>".$txt_database_name[$lang]."</b></td><td$nowrap><b>".$txt_disk_usage[$lang]."</b></tr>";
	for($i=0;$i<sizeof($stats["db"]);$i++){
		if($i % 2){
			$bgcolor = "$nowrap nowrap bgcolor=\"#000000\" style=\"color:#FFFFFF\" ";
		}else{
			$bgcolor = $nowrap;
		}
		$out .= "<tr>";
		$out .= "<td$bgcolor>".$stats["db"][$i]["name"]."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["db"][$i]["du"])."</td>";
		$out .= "</tr>";
	}
	$out .= '</table>';

	$out .= "<br><br><u><b>".$txt_domain_name_trafic_du[$lang]."</b></u>";
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="0" cellspacing="1">';
	$out .= "<tr><td><b>".$txt_domain_name[$lang]."</b></td><td$nowrap><b>".$txt_disk_usage[$lang]."</b></td><td><b>POP3</b></td><td><b>IMAP</b></td><td><b>SMTP</b></td><td><b>FTP</b></td><td><b>HTTP</b></td><td$nowrap><b>".$txt_total_trafic[$lang]."</b></td></tr>";
	for($ad=0;$ad<sizeof($stats["domains"]);$ad++){
		if($ad % 2){
			$bgcolor = "$nowrap nowrap bgcolor=\"#000000\" style=\"color:#FFFFFF\" ";
		}else{
			$bgcolor = $nowrap;
		}
		$out .= "<tr>";
		$out .= "<td$bgcolor>".$stats["domains"][$ad]["name"]."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["du"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["pop"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["imap"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["smtp"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["ftp"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["http"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["total_transfer"])."</td>";
		$out .= "</tr>";
	}
	$out .= '</table>';
	return $out;
}

?>
