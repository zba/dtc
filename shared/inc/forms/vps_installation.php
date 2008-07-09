<?php

function drawAdminTools_VPSInstallation($admin,$vps){
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

	// Calculate last month dates
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

	// Check if the VPS has expired or not
	$ar = explode("-",$vps["expire_date"]);
	if(date("Y") > $ar[0] ||
			(date("Y") == $ar[0] && date("m") > $ar[1]) ||
			(date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
		$expired = "yes";
	}else{
		$expired = "no";
	}

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
	$vps_ips = $vps["ip_addr"];
	$n = sizeof($vps_ips);
	if($n > 1){
		$ip_title = _("IP addresses: ") ;
	}else{
		$ip_title = _("IP address: ") ;
	}
	$out .= "<br><h3>". $ip_title ."</h3>";
	$out .= dtcFormTableAttrs();
	for($i=0;$i<$n;$i++){
		if($i % 2){
			$alt_color = 0;
		}else{
			$alt_color = 1;
		}
		$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_addr='".$vps_ips[$i]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r);
		if($n2 != 1){
			$out .= _("Error line ".__LINE__." file ".__FILE__);
		}else{
			$a = mysql_fetch_array($r);
			$out .= dtcFormLineDraw($vps_ips[$i],
	"$frm_start<input type=\"hidden\" name=\"action\" value=\"set_ip_reverse_dns\">
	<input type=\"hidden\" name=\"ip_addr\" value=\"".$vps_ips[$i]."\">
	<input type=\"text\" name=\"rdns\" value=\"".$a["rdns_addr"]."\">
</td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\"
onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\""._("Change RDNS")."\"></div>
 <div class=\"input_btn_right\"></div>
</div></form>",$alt_color);
		}
	}
	$out .= "</table><br><br>";

	// VPS status
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
		if($expired == "yes"){
			$out .= _("You cannot start your VPS if it has expired. Please renew it if you want the boot up (xm start) button to appear here.");
		}else{
			$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"start_vps\">
<input type=\"submit\" value=\"". _("Boot up (xm start)") ."\">
</form>";
		}
		// FSCK
		$out .= "<h3>". _("File-system check:") ."</h3><br>";
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"fsck_vps\">
<input type=\"submit\" value=\"". _("File system check (fsck)") ."\">
</form>";
		// OS reinstall
		$out .= "<h3>". _("Reinstall operating system:") ."</h3><br>";
		$out .= _("Currently installed operating system: ") .$vps["operatingsystem"]."<br>";
		$deb_selected = " ";
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

	$out .= ("Once your VPS is installed, ssh to the physical console to use it for the first time.")."<br><br>";

	$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_passwd\">
". _("New SSH password: ") ."<input type=\"text\" name=\"new_password\" value=\"\"><input type=\"submit\" value=\"Ok\">
</form>";
	$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_key\">
". _("New SSH key: ") ."<input size=\"40\" type=\"text\" name=\"new_key\" value=\"\"><input type=\"submit\" value=\"Ok\">
</form>";
	$out .= "<br><br>"._("To access to your console, first enter a ssh password or key above, and then ssh to:") ."<br>xen".$vps_name."@".$vps_node."<br><br>";
	$out .= "<br>" ._("You should then install sshd in your VPS and use the physical console only for debugging purposes.");
	$out .= "<br>".helpLink("PmWiki/Setup-A-VPS-Once-DTC-Xen-Installed-It");

	// A bit of AJAX to have the sever's install log!
//	if($reinstall_os == 1){
		if($panel_type == "admin"){
			$path_url = "/dtcadmin";
		}else{
			$path_url = "/dtc";
		}
		$ajax_call_url = "https://".$_SERVER["SERVER_NAME"]."$path_url/get_install_log.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_node=$vps_node&vps_name=$vps_name";
		$out .= '
<script language="javascript" src="dtc_ajax.js"></script>
<script type="text/javascript" language="javascript">

function dtc_ajax_callback(text) {
        document.getElementById("reinstall_os_log").innerHTML = text;
}
function ajaxLogConsole() {
	dtc_ajax = new dtc_ajax_submit_url("'.$path_url.'");
	dtc_ajax.submit_url();
}
</script>';
//	}

	$r = $soap_client->call("getVPSInstallLog",array("vpsname" => $vps_name,"numlines" => "20"),"","","");
	$err = $soap_client->getError();
	if($err){
		$r = _("Could not get VPS install log. Error: ").$err._(" maybe there are no logs yet?");
	}
	// print_r($r);
	$r = str_replace("\n\n","\n",$r);
	$out .= "<h3>". _("Installation log (last 20 lines):") ."</h3><br>";

	$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
<tr><td bgcolor=\"black\"><font color=\"white\">$vps_node:$vps_name</font></td>
<tr><td bgcolor=\"black\"><font color=\"white\"><pre onLoad=\"ajaxLogConsole();\" id=\"reinstall_os_log\" class=\"reinstall_os_log\" style=\"overflow: auto\"><font color=\"red\">dtc-xen</font>@<font color=\"blue\">$vps_node</font>&gt;_ #<br>...<br>". $r ."</pre></font></td>
</table>";
	return $out;
}

?>
