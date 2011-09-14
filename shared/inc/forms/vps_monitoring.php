<?php

function drawAdminTools_VPSMonitor($admin,$vps){
	global $vps_name;
	global $vps_node;

	global $adm_login;
	global $adm_pass;
	global $rub;
	global $addrlink;

	global $vps_soap_err;

	global $pro_mysql_product_table;
	global $pro_mysql_vps_ip_table;

	global $pro_mysql_vps_stats_table;
	global $secpayconf_currency_letters;

	global $panel_type;

	$reinstall_os = 1;

	get_secpay_conf();

	$out = "";

	$checker = checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
	if($checker != true){
		return _("Credential not correct: can't display in file ") .__FILE__." line ".__LINE__;
	}

	$vps_out = "";

	$vps_out_net_stats = "";
	$vps_out_hdd_stats = "";
	$vps_out_swap_stats = "";
	$vps_out_cpu_stats = "";


	// Calculate last month
	$cur_year = date("Y");
	$cur_month = date("m");

	$last_month = $cur_month - 1;
	if($last_month == 0){
		$last_month_year = $cur_year - 1;
		$last_month = 12;
	}else{
		$last_month_year = $cur_year;
	}

	$tow_month_ago = $last_month - 1;
	if($tow_month_ago == 0){
		$tow_month_ago = 12;
		$tow_month_ago_year = $last_month_year - 1;
	}else{
		$tow_month_ago_year = $last_month_year;
	}

	// 3 months ago
	$month_ago_3 = $tow_month_ago - 1;
	if($month_ago_3 == 0){
		$month_ago_3 = 12;
		$month_ago_3_year = $tow_month_ago_year - 1;
	}else{
		$month_ago_3_year = $tow_month_ago_year;
	}
	// 4 months ago
	$month_ago_4 = $month_ago_3 - 1;
	if($month_ago_4 == 0){
		$month_ago_4 = 12;
		$month_ago_4_year = $month_ago_3_year - 1;
	}else{
		$month_ago_4_year = $month_ago_3_year;
	}
	// 5 months ago
	$month_ago_5 = $month_ago_4 - 1;
	if($month_ago_5 == 0){
		$month_ago_5 = 12;
		$month_ago_5_year = $month_ago_4_year - 1;
	}else{
		$month_ago_5_year = $month_ago_4_year;
	}
	// 6 months ago
	$month_ago_6 = $month_ago_5 - 1;
	if($month_ago_6 == 0){
		$month_ago_6 = 12;
		$month_ago_6_year = $month_ago_5_year - 1;
	}else{
		$month_ago_6_year = $month_ago_5_year;
	}

	$q = "SELECT * FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='$vps_node' AND vps_xen_name='xen$vps_name'
	AND year='".$month_ago_6_year."' AND month='".$month_ago_6."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$net_total = $a["network_in_count"] + $a["network_out_count"];
		$vps_out_net_stats .= _("6 months ago: "). smartByte($net_total)."<br>";
		$vps_out_cpu_stats .= _("6 months ago: ").$a["cpu_usage"]._(" CPU seconds")."<br>";
		$vps_out_swap_stats .= _("6 months ago: "). smartByte( $a["swapio_count"] )."<br>";
		$vps_out_hdd_stats .= _("6 months ago: "). smartByte( $a["diskio_count"] )."<br>";
	}

	$q = "SELECT * FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='$vps_node' AND vps_xen_name='xen$vps_name'
	AND year='".$month_ago_5_year."' AND month='".$month_ago_5."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$net_total = $a["network_in_count"] + $a["network_out_count"];
		$vps_out_net_stats .= _("5 months ago: "). smartByte($net_total)."<br>";
		$vps_out_cpu_stats .= _("5 months ago: ").$a["cpu_usage"]._(" CPU seconds")."<br>";
		$vps_out_swap_stats .= _("5 months ago: "). smartByte( $a["swapio_count"] )."<br>";
		$vps_out_hdd_stats .= _("5 months ago: "). smartByte( $a["diskio_count"] )."<br>";
	}

	$q = "SELECT * FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='$vps_node' AND vps_xen_name='xen$vps_name'
	AND year='".$month_ago_4_year."' AND month='".$month_ago_4."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$net_total = $a["network_in_count"] + $a["network_out_count"];
		$vps_out_net_stats .= _("4 months ago: "). smartByte($net_total)."<br>";
		$vps_out_cpu_stats .= _("4 months ago: ").$a["cpu_usage"]._(" CPU seconds")."<br>";
		$vps_out_swap_stats .= _("4 months ago: "). smartByte( $a["swapio_count"] )."<br>";
		$vps_out_hdd_stats .= _("4 months ago: "). smartByte( $a["diskio_count"] )."<br>";
	}

	$q = "SELECT * FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='$vps_node' AND vps_xen_name='xen$vps_name'
	AND year='".$month_ago_3_year."' AND month='".$month_ago_3."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$net_total = $a["network_in_count"] + $a["network_out_count"];
		$vps_out_net_stats .= _("3 months ago: "). smartByte($net_total)."<br>";
		$vps_out_cpu_stats .= _("3 months ago: ").$a["cpu_usage"]._(" CPU seconds")."<br>";
		$vps_out_swap_stats .= _("3 months ago: "). smartByte( $a["swapio_count"] )."<br>";
		$vps_out_hdd_stats .= _("3 months ago: "). smartByte( $a["diskio_count"] )."<br>";
	}

	$q = "SELECT * FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='$vps_node' AND vps_xen_name='xen$vps_name'
	AND year='$tow_month_ago_year' AND month='$tow_month_ago';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$net_total = $a["network_in_count"] + $a["network_out_count"];
		$vps_out_net_stats .= _("2 months ago: "). smartByte($net_total)."<br>";
		$vps_out_cpu_stats .= _("2 months ago: ").$a["cpu_usage"]._(" CPU seconds")."<br>";
		$vps_out_swap_stats .= _("2 months ago: "). smartByte( $a["swapio_count"] )."<br>";
		$vps_out_hdd_stats .= _("2 months ago: "). smartByte( $a["diskio_count"] )."<br>";
	}

	$q = "SELECT * FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='$vps_node' AND vps_xen_name='xen$vps_name'
	AND year='$last_month_year' AND month='$last_month';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$net_total = $a["network_in_count"] + $a["network_out_count"];
		$vps_out_net_stats .= _("Last month: "). smartByte($net_total)."<br>";
		$vps_out_cpu_stats .= _("Last month: ").$a["cpu_usage"]._(" CPU seconds")."<br>";
		$vps_out_swap_stats .= _("Last month: "). smartByte( $a["swapio_count"] )."<br>";
		$vps_out_hdd_stats .= _("Last month: "). smartByte( $a["diskio_count"] )."<br>";
	}

	$q = "SELECT * FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='$vps_node' AND vps_xen_name='xen$vps_name'
	AND year='$cur_year' AND month='$cur_month';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$a = mysql_fetch_array($r);
		$net_total = $a["network_in_count"] + $a["network_out_count"];
		$vps_out_net_stats .= _("This month: "). smartByte($net_total);
		$vps_out_cpu_stats .= _("This month: ").$a["cpu_usage"]._(" CPU seconds");
		$vps_out_swap_stats .= _("This month: "). smartByte( $a["swapio_count"] );
		$vps_out_hdd_stats .= _("This month: "). smartByte( $a["diskio_count"] );
	}

	// Display the stats of the VPS
	$vps_stat_out = "";
	$vps_stat_out .= "<table cellspacing=\"2\" cellpaddig=\"2\" border=\"0\">";
	$vps_stat_out .= "<tr><td>"._("Network:")."<br>";
	$vps_stat_out .= "<img width=\"120\" height=\"48\" src=\"vps_stats_network.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_name\"></td>";

	$vps_stat_out .= "<td>"._("CPU Time:")."<br>";
	$vps_stat_out .= "<img width=\"120\" height=\"48\" src=\"vps_stats_cpu.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_name\"></td></tr>";

	$vps_stat_out .= "<tr><td>".$vps_out_net_stats."</td><td>$vps_out_cpu_stats</td></tr>";

	$vps_stat_out .= "<tr><td>"._("Swap I/O:")."<br>";
	$vps_stat_out .= "<img width=\"120\" height=\"48\" src=\"vps_stats_swap.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_name\"></td>";

	$vps_stat_out .= "<td>"._("HDD I/O:")."<br>";
	$vps_stat_out .= "<img width=\"120\" height=\"48\" src=\"vps_stats_hdd.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_name\"></td></tr>";

	$vps_stat_out .= "<tr><td>".$vps_out_swap_stats."</td><td>".$vps_out_hdd_stats."</td></tr></table>";

	// VPS (remote SOAP) Status
	$soap_client = connectToVPSServer($vps_node);

	if($soap_client != false){
		$vps_remote_info = getVPSInfo($vps_node,$vps_name,$soap_client);

		if($vps_remote_info == false){
			if(strstr($vps_soap_err,_("Method getVPSState failed"))){
				$vps_out .= _("Could not get remote status (Method getVPSState() failed). Maybe the VPS is not running?") ."<br><br>";
			}else if(strstr($vps_soap_err,_("couldn't connect to host"))){
				$vps_out .= _("Could not get remote status: could not connect to the SOAP server (HTTP error).") ."<br><br>";
			}else{
				$vps_out .= _("Could not get remote status. Unkown error: ") ."$vps_soap_err<br><br>";
			}
		}else if($vps_remote_info == "fsck"){
			$vps_out .= _("Checking filesystem...") ."<br><br>";
		}else if($vps_remote_info == "mkos"){
			$vps_out .= _("Reinstalling operating system...") ."<br><br>";
		}else{
			if (isset($vps_remote_info["id"]))
			{
				$vps_out .= _("VM id: ") .$vps_remote_info["id"]."<br>";
			}
			if (isset($vps_remote_info["name"]))
			{
				$vps_out .= _("Name: ") .$vps_remote_info["name"]."<br>";
			}
			if (isset($vps_remote_info["memory"]))
			{
				$vps_out .= _("Memory: ") .$vps_remote_info["memory"]."<br>";
			}
			if(isset($vps_remote_info["maxmem"])){
				$vps_out .= _("Max memory: ") .$vps_remote_info["maxmem"]."<br>";
			}else{
				$vps_out .= _("Maxmem: cannot fetch (maybe boot in progress?)") ."<br>";
			}
			if(isset($vps_remote_info["cpu"])){
				$vps_out .= _("Number of CPU: ") .$vps_remote_info["cpu"]."<br>";
			}else{
				$vps_out .= _("Number of CPU: cannot fetch (maybe boot in progress?)") ."<br>";
			}
			if(isset($vps_remote_info["state"])){
				$vps_out .= _("VPS State: ") .$vps_remote_info["state"]."<br>";
			}else{
				$vps_out .= _("State: cannot fetch (maybe boot in progress?)") ."<br>";
			}
			if($vps_remote_info["xen_type"] == 2 && isset($vps_remote_info["up_time"])){
				$uptime = substr($vps_remote_info["up_time"],0,strpos($vps_remote_info["up_time"],"."));
				$uptime_s = $uptime % 60;
				$uptime_m = round($uptime/60) % 60;
				$uptime_h = round($uptime/3600) % 24;
				$uptime_j = round($uptime/86400);
				if($uptime_s > 1)	$upt_s_s = "s";	else	$upt_s_s = "";
				if($uptime_m > 1)	$upt_s_m = "s";	else	$upt_s_m = "";
				if($uptime_h > 1)	$upt_s_h = "s";	else	$upt_s_h = "";
				if($uptime_j > 1)	$upt_s_j = "s";	else	$upt_s_j = "";

				$vps_out .= _("Up time: ") ."$uptime_j day$upt_s_j $uptime_h hour$upt_s_h $uptime_m minute$upt_s_m $uptime_s seconde$upt_s_s<br>";
				$vps_out .= _("Last boot date: ") .date("Y-m-d H:i:s",substr($vps_remote_info["start_time"],0,strlen($vps_remote_info["start_time"])-2))."<br>";
			}
			$vps_out .= "<br>";
		}
	}else{
		$vps_out .= _("Could not connect to the VPS SOAP Server.") ;
	}

	$frm_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";

	// The ip address(es)
	$out .= "<br><h3>"._("IP address(es) of your VPS:")."</h3>";
	$vps_ips = $vps["ip_addr"];
	$n = sizeof($vps_ips);
	if($n > 1){
		$out .= _("IP addresses: ") ;
	}else{
		$out .= _("IP address: ") ;
	}
	for($i=0;$i<$n;$i++){
		if($i != 0){
			$out .= ", ";
		}
		$out .= $vps_ips[$i];
	}
	$out .= "<br><br>";

	// VPS status
	$out .= $vps_stat_out;
	$out .= "<h3>". _("Current VPS status:") ."</h3><br>";
	$out .= $vps_out;

	// VPS Monitoring
	$out .= "<br><h3>". _("Service monitoring:") ."</h3><br>";

	$frm_start = dtcFormTableAttrs() ."<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";

	$out .= $frm_start . "
<input type=\"hidden\" name=\"action\" value=\"set_vps_monitoring_values\">
";
	if($vps["monitor_ping"] == "yes"){
		$monitor_ping = " checked ";
	}else{
		$monitor_ping = " ";
	}
	if($vps["monitor_ssh"] == "yes"){
		$monitor_ssh = " checked ";
	}else{
		$monitor_ssh = " ";
	}
	if($vps["monitor_http"] == "yes"){
		$monitor_http = " checked ";
	}else{
		$monitor_http = " ";
	}
	if($vps["monitor_smtp"] == "yes"){
		$monitor_smtp = " checked ";
	}else{
		$monitor_smtp = " ";
	}
	if($vps["monitor_pop3"] == "yes"){
		$monitor_pop3 = " checked ";
	}else{
		$monitor_pop3 = " ";
	}
	if($vps["monitor_imap4"] == "yes"){
		$monitor_imap4 = " checked ";
	}else{
		$monitor_imap4 = " ";
	}
	if($vps["monitor_ftp"] == "yes"){
		$monitor_ftp = " checked ";
	}else{
		$monitor_ftp = " ";
	}
	$out .= dtcFormLineDraw( _("VPS monitoring alerts email address: "),"<input type=\"text\" name=\"email_addr\" value=\"".$vps["monitoring_email"]."\">",1);
	$out .= dtcFormLineDraw( _("Ping: "),"<input type=\"checkbox\" name=\"monitor_ping\" value=\"yes\" $monitor_ping>",0);
	$out .= dtcFormLineDraw( _("SSH: "),"<input type=\"checkbox\" name=\"monitor_ssh\" value=\"yes\" $monitor_ssh>",1);
	$out .= dtcFormLineDraw( _("HTTP: "),"<input type=\"checkbox\" name=\"monitor_http\" value=\"yes\" $monitor_http>",0);
	$out .= dtcFormLineDraw( _("SMTP: "),"<input type=\"checkbox\" name=\"monitor_smtp\" value=\"yes\" $monitor_smtp>",1);
	$out .= dtcFormLineDraw( _("POP3: "),"<input type=\"checkbox\" name=\"monitor_pop3\" value=\"yes\" $monitor_pop3>",0);
	$out .= dtcFormLineDraw( _("IMAP4: "),"<input type=\"checkbox\" name=\"monitor_imap4\" value=\"yes\" $monitor_imap4>",1);
	$out .= dtcFormLineDraw( _("FTP: "),"<input type=\"checkbox\" name=\"monitor_ftp\" value=\"yes\" $monitor_ftp>",0);
	$out .= dtcFormLineDraw( "",dtcApplyButton(),1);
	$out .= "</form></table>";
	return $out;
}

?>
