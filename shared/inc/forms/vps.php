<?php

function drawAdminTools_VPS($admin,$vps){
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

	global $reinstall_os = 1;

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

	// Display the current contract
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$vps["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$vps_prod = mysql_fetch_array($r);
		$contract = $vps_prod["name"];
	}else{
		$contract = "not found!";
	}
	$out .= "<h3>". _("Current contract: ") ."</h3><br>$contract<br><br>";

	// Expiration management !
	$ar = explode("-",$vps["expire_date"]);
	$out .= "<h3>". _("Expiry date:") ."</h3><br>";
	$out .= _("Your VPS was first registered on the: ") .$vps["start_date"]."<br>";
	if(date("Y") > $ar[0] ||
			(date("Y") == $ar[0] && date("m") > $ar[1]) ||
			(date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
		$out .= "<font color=\"red\">". _("Your VPS has expired on the: ") .$vps["expire_date"]."</font>"
			."<br>". _("Please renew with one of the following options: ") ."<br>";
	}else{
		$out .= _("Your VPS will expire on the: ") .$vps["expire_date"];
	}

	// Renewal buttons
	$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$vps["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= "<form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
<input type=\"hidden\" name=\"renew_type\" value=\"vps\">
<input type=\"hidden\" name=\"product_id\" value=\"".$a["id"]."\">
<input type=\"hidden\" name=\"vps_id\" value=\"".$vps["id"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"submit\" value=\"".$a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)"."\">
</form>";
	}

	$out .= "<h3>". _("CPU and Network usage:") ."</h3><br>
<a target=\"_blank\" href=\"http://".$vps["vps_server_hostname"]."/dtc-xen/\">http://".$vps["vps_server_hostname"]."/dtc-xen/</a><br>";

	// The ip address(es)
	$vps_ips = $vps["ip_addr"];
	$n = sizeof($vps_ips);
	if($n > 1){
		$out .= _("IP addresses: ") ;
	}else{
		$out .= _("IP address: ") ;
	}
	for($i=0;$i<$n;$i++){
		if($i != 0){
			$out .= " - ";
		}
		$out .= $vps_ips[$i];
	}
	$out .= "<br><br>";

	// VPS status
	$out .= $vps_stat_out;
	$out .= "<h3>". _("Current VPS status:") ."</h3><br>";
	$out .= $vps_out;

	// Start / stop VPS
	$out .= "<h3>". _("Start and stop of your VPS:") ."</h3><br>";
	if($vps_remote_info == "fsck"){
		$out .= _("Please wait until file system check is finished first.") ."<br><br>";
	}else if($vps_remote_info == "mkos"){
		$out .= _("Please wait until operating system reinstallation has completed.") ."<br><br>";
		$reinstall_os = 1;
	}else if($vps_remote_info == true){
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"shutdown_vps\">
<input type=\"submit\" value=\"". _("Gracefully shutdown (xm shutdown)") ."\">
</form>";
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"destroy_vps\">
<input type=\"submit\" value=\""._("Immediate kill (xm destroy)") ."\">
</form>";
		$out .= _("To do a file system check or an operating system reinstallation, you need to shutdown or destroy your server first.") ."<br><br>";
	}else{
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"start_vps\">
<input type=\"submit\" value=\"". _("Boot up (xm start)") ."\">
</form>";
		// FSCK
		$out .= "<h3>". _("File-system check:") ."</h3><br>";
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"fsck_vps\">
<input type=\"submit\" value=\"". _("File system check (fsck)") ."\">
</form>";
		// OS reinstall
		$out .= "<h3>". _("Reinstall operating system:") ."</h3><br>";
		$out .= _("Currently installed operating system: ") .$vps["operatingsystem"]."<br>";
/*		$installable_os = getInstallableOS($soap_client);
		if($installable_os != false){
			$nbr_os = sizeof($installable_os);
			$out .= $frm_start."<select name=\"os_type\">";
			for($i=0;$i<$nbr_os;$i++){
				if($vps["operatingsystem"] == $installable_os[$i]){
					$selected = " selected";
				}else{
					$selected = "";
				}
				$out .= "<option value=\"".$installable_os[$i]."\"$selected>".$installable_os[$i]."</option>";
			}
			$out .= "</select><input type=\"hidden\" name=\"action\" value=\"reinstall_os\">
<input type=\"submit\" value=\"".$txt_reinstall_operating_system_button[$lang]."\">
</form>";
		}else{
			$out .= "<font color=\"red\">Could not get remote installable OS: ask administrator to upgrade this
dtc-xen server to a higher version.</font><br>";
*/			$deb_selected = " ";
			$cent_selected = " ";
			$gen_selected = " ";
			$bsd_selected = " ";
			switch($vps["operatingsystem"]){
			case "debian":
				$deb_selected = " selected ";
				break;
			case "centos":
				$cent_selected = " selected ";
				break;
			case "gentoo":
				$gen_selected = " selected ";
				break;
			case "netbsd":
				$bsd_selected = " selected ";
				break;
			default:
				die( _("Operating system type not supported") );
				break;
			}
			// Operating system selection popup and reinstallation button
			$out .= $frm_start."<select name=\"os_type\">
<option value=\"debian\" $deb_selected>Debian</option>
<option value=\"centos\" $cent_selected>CentOS</option>
<option value=\"gentoo\" $gen_selected>Gentoo</option>
<option value=\"netbsd\" $bsd_selected>NetBSD</option>
</select><input type=\"hidden\" name=\"action\" value=\"reinstall_os\">
<input type=\"submit\" value=\"". _("Reinstall operating system") ."\">
</form>";
//		}

  		// BSD kernel change popup
		if($vps["operatingsystem"] == "netbsd"){
			if($vps["bsdkernel"] == "install"){
				$normal_selected = " ";
				$install_selected = " selected ";
			}else{
				$normal_selected = " selected ";
				$install_selected = " ";
			}
			$out .= $frm_start."<select name=\"bsdkernel\">
    <option value=\"normal\" $normal_selected>Normal</option>
    <option value=\"install\" $install_selected>Install</option>
    </select><input type=\"hidden\" name=\"action\" value=\"change_bsd_kernel_type\">
    <input type=\"submit\" value=\"". _("Change NetBSD kernel") ."\">
    </form>";
		}
	}

	// SSH Physical console password changing
	$out .= "<h3>". _("Physical console last display and ssh access:") ."</h3><br>";

	$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_passwd\">
". _("New SSH password: ") ."<input type=\"text\" name=\"new_password\" value=\"\"><input type=\"submit\" value=\"Ok\">
</form>";
	$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_key\">
". _("New SSH key: ") ."<input size=\"40\" type=\"text\" name=\"new_key\" value=\"\"><input type=\"submit\" value=\"Ok\">
</form>";
	$out .= _("To access to your console, first enter a ssh password or key above, and then ssh to:") ."<br>xen".$vps_name."@".$vps_node."<br><br>";

	// A bit of AJAX to have the sever's install log!
	if($reinstall_os == 1){
		if($panel_type == "admin"){
			$path_url = "/dtcadmin";
		}else{
			$path_url = "/dtc";
		}
		$ajax_call_url = "https://".$_SERVER["SERVER_NAME"]."$path_url/get_install_log.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_name";
		$out = '
<script language="javascript" src="dtc_ajax.js"></script>
<script type="text/javascript" language="javascript">

function dtc_ajax_callback(text) {
        document.getElementById("reinstall_os_log").innerHTML = text;
}
dtc_ajax = new dtc_ajax_submit_url("'.$path_url.'");
dtc_ajax.submit_url();
        
</script>';
	}

	$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
<tr><td bgcolor=\"black\"><font color=\"white\">$vps_node:$vps_name</font></td>
<tr><td bgcolor=\"black\"><font color=\"white\"><pre id=\"reinstall_os_log\" class=\"reinstall_os_log\">...</pre></font></td>
</table>";
	return $out;
}

?>
