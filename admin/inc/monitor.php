<?php

function drawAdminMonitor (){
	global $pro_mysql_client_table;
	global $pro_mysql_admin_table;
	global $conf_mysql_db;
	global $adm_realpass;
	global $adm_pass;
	global $adm_random_pass;

	$out = "";
	// For each clients
	$q = "SELECT * FROM $pro_mysql_client_table WHERE 1 ORDER BY familyname,christname";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
	$nr = mysql_num_rows($r);
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="1" cellspacing="1">';
	$out .=
"<tr><td><b>". _("User") ."</b></td><td><b>". _("Transfer") ." / ". _("BW Quota") ."</b></td><td><b>". _("Transfer per Month") ."</b></td><td><b>". _("Disk Usage") ." / ". _("Quota") ."</b></td></tr>";
	$total_box_transfer = 0;
	$total_box_hits = 0;
	for($i=0;$i<$nr;$i++){
		$ar = mysql_fetch_array($r);
		$transfer = 0;
		$total_hits = 0;
		$du = 0;
		// make sure we are selecting the correct DB
		// there is a condition where we have lost the link to the main DB
		// this may hide another bug, but at least it will show things to the user
		mysql_select_db($conf_mysql_db);
		// For each of it's admins
		$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='".$ar["id"]."';";
		$r2 = mysql_query($q2)or die("Cannot query: \"$q2\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
		$nr2 = mysql_num_rows($r2);
		$admin=array();
		$admin_stats=array();
		for($j=0;$j<$nr2;$j++){
			$ar2 = mysql_fetch_array($r2);
			$adm_realpass = $ar2["adm_pass"];
			$adm_pass = $ar2["adm_pass"];
			$adm_random_pass = $ar2["adm_pass"];
			$oneadmin = fetchAdmin($ar2["adm_login"],$ar2["adm_pass"]);
			$admin_stats = fetchAdminStats($oneadmin);
			$admin=array_merge($admin,$oneadmin);


			
			if (isset($admin_stats["total_transfer"])){
				$transfer += $admin_stats["total_transfer"];
			}
			if( isset($admin_stats["total_du"]) ){
				$du += $admin_stats["total_du"];
			}
			if (isset($admin_stats["total_hit"])){
				$hits = $admin_stats["total_hit"];
				$total_hits += $hits;
			}
		}
		if($i % 2){
			$back = " bgcolor=\"#000000\" style=\"white-space:nowrap;color:#FFFFFF\" nowrap";
		}else{
			$back = " style=\"white-space:nowrap;\" nowrap";
		}

		$nbr_row = 0;
		if(!isset($admin["vps"])){
                	$nbr_vps = 0;
		}else{
			$nbr_vps = sizeof($admin["vps"]);
			$nbr_row += $nbr_vps;
		}
		if( isset($admin["data"])){
			$nbr_row += 1;
		}
		if($nbr_row > 1){
			$rowspan_entry = "rowspan=\"$nbr_row\"";
		}else{
			$rowspan_entry = "";
		}
		// Admin name
		$out .= "<tr><td$back $rowspan_entry><u>".$ar["company_name"].":</u><br>
".$ar["familyname"].", ".$ar["christname"]."</td>";
		if( isset($admin["data"])){
			// Transfer this month
			$out .= "<td$back>".drawPercentBar($transfer,$ar["bw_quota_per_month_gb"]*1024*1024*1024,"no")."<br>
".smartByte($transfer)." / ".smartByte($ar["bw_quota_per_month_gb"]*1024*1024*1024)." ($total_hits hits)</td>";
			// Per month transfer graph
			$out .= "<td$back><img width=\"120\" height=\"48\" src=\"bw_per_month.php?adm_login=".$ar2["adm_login"]."&adm_pass=".$ar2["adm_pass"]."\"></td>";
			// Share hosing hard disk space
			$out .= "<td$back>".drawPercentBar($du,$ar["disk_quota_mb"]*1024*1024,"no")."<br>
".smartByte($du)." / ".smartByte($ar["disk_quota_mb"]*1024*1024)."</td></tr>";
		}
		for($j=0;$j<$nbr_vps;$j++){
			if( isset($admin["data"]) || $j > 1){
				$out .= "<tr>";
			}
			$out .= "<td $back colspan=\"3\">".$admin["vps"][$j]["vps_server_hostname"].":".$admin["vps"][$j]["vps_xen_name"];
			
			$out .= "<table border=\"1\" width=\"100%\" height=\"1\" cellpadding=\"1\" cellspacing=\"1\">";
			$out .= "<tr><td $back>" . _("Network") . "</td><td $back>" . _("HDD") . "</td><td $back>" . _("Swap") . "</td><td $back>" . _("CPU") . "</td></tr>";
			$out .= "<tr><td $back><img width=\"120\" height=\"48\" src=\"vps_stats_network.php?vps_node=".$admin["vps"][$j]["vps_server_hostname"]."&vps_name=".$admin["vps"][$j]["vps_xen_name"]."\"></td>
<td $back><img width=\"120\" height=\"48\" src=\"vps_stats_hdd.php?vps_node=".$admin["vps"][$j]["vps_server_hostname"]."&vps_name=".$admin["vps"][$j]["vps_xen_name"]."\"></td>
<td $back><img width=\"120\" height=\"48\" src=\"vps_stats_swap.php?vps_node=".$admin["vps"][$j]["vps_server_hostname"]."&vps_name=".$admin["vps"][$j]["vps_xen_name"]."\"></td>
<td $back><img width=\"120\" height=\"48\" src=\"vps_stats_cpu.php?vps_node=".$admin["vps"][$j]["vps_server_hostname"]."&vps_name=".$admin["vps"][$j]["vps_xen_name"]."\"></td>";
			$out .= "</tr>";
			$out .= "</table>";

			$out .= "</td></tr>";

		}
		$total_box_transfer += $transfer;
		$total_box_hits += $total_hits;
//fetchAdminStats($admin)
	}
	$out .= "</table>";
	$out .= _("Total transfer this month: ").smartByte($total_box_transfer)." ($total_box_hits hits)";
	return $out;
}

?>
