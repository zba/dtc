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
	global $pro_mysql_ip_pool_table;

	global $pro_mysql_vps_stats_table;
	global $secpayconf_currency_letters;

	global $panel_type;
	global $submit_err;

	$reinstall_os = 1;

	get_secpay_conf();

	$out = "<font color=\"red\">$submit_err $vps_soap_err</font>";

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
		$ip_title = _("IP address: ");
	}
	$out .= "<br><h3>". $ip_title ."</h3>";
	$out .= dtcFormTableAttrs();
	$out .= '<tr><th>' . $ip_title . '</th><th>' . _("Netmask: ") . '</th><th>' . _("Gateway: ") . '</th><th>' . _("DNS: ") . '</th><th>' . _("RevDNS: ") . '</th></tr>';
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

			$q = "SELECT * FROM $pro_mysql_ip_pool_table WHERE id = '".$a['ip_pool_id']."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$ip_pool_row = mysql_fetch_array($r);

			$out .= dtcFormLineDraw($vps_ips[$i] . '</th><th class="alternatecolorline">' . $ip_pool_row['netmask'] . '</th><th class="alternatecolorline">' . $ip_pool_row['gateway'] . '</th><th class="alternatecolorline">' . $ip_pool_row['dns'],
	"$frm_start<input type=\"hidden\" name=\"action\" value=\"set_ip_reverse_dns\">
	<input type=\"hidden\" name=\"ip_addr\" value=\"".$vps_ips[$i]."\">
	<input size=\"40\" type=\"text\" name=\"rdns\" value=\"".$a["rdns_addr"]."\">
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
". submitButtonStart() . _("Gracefully shutdown (xm shutdown)") . submitButtonEnd() ."
</form><br><br>";
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"destroy_vps\">
" . submitButtonStart() . _("Immediate kill (xm destroy)") . submitButtonEnd() ."
</form><br><br>";
		$out .= _("To do a file system check or an operating system reinstallation, you need to shutdown or destroy your server first.") ."<br><br>";
	}else{
		if($expired == "yes"){
			$out .= _("You cannot start your VPS if it has expired. Please renew it if you want the boot up (xm start) button to appear here.");
		}else{
			$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"start_vps\">".
submitButtonStart() . _("Boot up (xm start)") . submitButtonEnd()."<br><br></form>";
		}
		// FSCK
		$out .= "<h3>". _("File-system check:") ."</h3><br>";
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"fsck_vps\">".
submitButtonStart() . _("File system check (fsck)") . submitButtonEnd() ."
<br><br></form>";
		// OS reinstall
		$out .= "<h3>". _("Reinstall operating system:") ."</h3><br>";
		$out .= _("Currently installed operating system: ") .$vps["operatingsystem"]."<br>";
		$deb_selected = " ";
		$debdtc_selected = " ";
		$cent_selected = " ";
		$gen_selected = " ";
		$bsd_selected = " ";
		$xenhvm_selected = " ";
		switch($vps["operatingsystem"]){
		case "debian":
			$deb_selected = " selected ";
			break;
		case "debian-dtc":
			$debdtc_selected = " selected ";
			break;
		case "centos":
			$cent_selected = " selected ";
			break;
		case "netbsd":
			$bsd_selected = " selected ";
			break;
		case "xenhvm":
			$xenhvm_selected = " selected ";
			break;
		default:
			break;
		}
		// Operating system selection popup and reinstallation button
		$out .= $frm_start."<table><tr><td>"._("Operating system:")." </td><td><select name=\"os_type\">
<option value=\"debian\" $deb_selected>Debian (" . _("network install with debootstrap") .")</option>
<option value=\"debian-dtc\" $debdtc_selected>Debian with DTC panel (" . _("network install with debootstrap") .")</option>
<option value=\"centos\" $cent_selected>CentOS (" . _("network install with yum") .")</option>
<option value=\"netbsd\" $bsd_selected>NetBSD (" . _("network setup with install kernel") .")</option>
<option value=\"xenhvm\" $xenhvm_selected>Xen HVM (" . _("boot your own .iso image") .")</option>";
		$installable_os = getInstallableOS($soap_client);
		$nbr_os = sizeof($installable_os);
		for($i=0;$i<$nbr_os;$i++){
			$os_name = $installable_os[$i];
			if($vps["operatingsystem"] == $os_name){
				$selected = " selected ";
			}else{
				$selected = "";
			}
			$out .= "<option value=\"$os_name\" $selected>$os_name ("._("operating system image").")</option>";
		}
		$installable_app = getInstallableAPP($soap_client);
		$nbr_app = sizeof($installable_app);
		for($i=0;$i<$nbr_app;$i++){
			$app_name = $installable_app[$i];
			if($vps["operatingsystem"] == $os_name){
				$selected = " selected ";
			}else{
				$selected = "";
			}
			$out .= "<option value=\"$app_name\" $selected>$app_name ("._("applicance builder").")</option>";
		}
		$out .= "</select></td></tr>
<tr><td>".("VPS root password:")." </td><td><input type=\"password\" name=\"root_password\"><input type=\"hidden\" name=\"action\" value=\"reinstall_os\"></td></tr>
<tr><td></td><td>" . submitButtonStart() . _("Reinstall operating system") . submitButtonEnd() ."
</td></tr></table></form>";
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
    " . submitButtonStart() . _("Change NetBSD kernel") . submitButtonEnd() ."
    </form>";
		}
		if($vps["operatingsystem"] == "xenhvm"){
			$vps_xenhvm_iso = getVPSIso($vps_node,$vps_name,$soap_client);
			$out .= "<br><br>" . _("To upload .iso files so they appear below and you can boot on them, you can upload them using ftp, ") ."xen$vps_name@$vps_node" ._(" using the password of your physical console.")."<br><br>";
			$boot_device_selector = "<select name=\"xenhvm_iso\">
<option value=\"hdd\">". _("Boot on hard drive"). "</option>";
			$n_iso = sizeof($vps_xenhvm_iso);
			if( is_array($vps_xenhvm_iso) ){
				for($i=0;$i<$n_iso;$i++){
					$iso = $vps_xenhvm_iso[$i];
					if($vps["howtoboot"] == $iso){
						$selected = " selected ";
					}else{
						$selected = " ";
					}
					$boot_device_selector .= "<option value=\"".htmlspecialchars($iso)."\" $selected>".htmlspecialchars($iso)."</option>";
				}
			}
			$boot_device_selector .= "</select>";

			if($vps["vncpassword"] == "no_vnc"){
				$vncons_act_yes_selected = " ";
				$vncons_act_no_selected = " checked ";
			}else{
				$vncons_act_yes_selected = " ";
				$vncons_act_no_selected = " checked ";
			}

			$out .= dtcFormTableAttrs();
			$out .= dtcFormLineDraw( $frm_start. _("Boot device: "), "<input type=\"hidden\" name=\"action\" value=\"change_xenhvm_boot_iso\">".$boot_device_selector ,1);
			$out .= dtcFormLineDraw( _("VNC console password: "), "<input type=\"text\" name=\"vnc_console_pass\" value=\"\">", 0);
			$out .= dtcFormLineDraw( _("VNC console activation: "), "<input type=\"radio\" name=\"vnc_console_activate\" value=\"yes\" $vncons_act_yes_selected>"._("yes")." <input type=\"radio\" name=\"vnc_console_activate\" value=\"no\" $vncons_act_no_selected>"._("no"),1);
			$out .= dtcFormLineDraw( "" , submitButtonStart(). _("Write parameters") . submitButtonEnd() , 0);
			$out .= "</table></form>";
		}
	}
	$out .= "<br><br>";

	// SSH Physical console password changing
	$out .= "<h3>". _("Physical console last display and ssh access:") ."</h3><br>";

	$out .= ("Once your VPS is installed, ssh to the physical console to use it for the first time.")."<br><br>";

	$out .= dtcFormTableAttrs();
	$out .= dtcFormLineDraw( $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_passwd\">". _("New SSH password: "),
		"<input size=\"40\" type=\"text\" name=\"new_password\" value=\"\"></td><td>" . submitButtonStart() . _("Ok") . submitButtonEnd() ."</form>",1);
	$out .= dtcFormLineDraw( $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_key\">". _("New SSH key: "),
		"<input size=\"40\" type=\"text\" name=\"new_key\" value=\"\"></td><td>" . submitButtonStart() . _("Ok") . submitButtonEnd() ."</form>",0);
	$out .= "</table>";

	$out .= "<br><br>"._("To access to your console, first enter a ssh password or key above, and then ssh to:") ."<br>xen".$vps_name."@".$vps_node."<br><br>";
	$out .= "<br>" ._("You should then install sshd in your VPS and use the physical console only for debugging purposes.");
	$out .= "<br>".helpLink("PmWiki/Setup-A-VPS-Once-DTC-Xen-Installed-It");

/* FIXME probably don't need any of this stuff'

	if($reinstall_os == 1){
		if($panel_type == "admin"){
			$path_url = "/dtcadmin";
		}else{
			$path_url = "/dtc";
		}
		$ajax_url = "https://".$_SERVER["SERVER_NAME"].$path_url."/xanjaxPushlet.php?";
		$ajax_auth = "adm_login=".$adm_login."&adm_pass=".$adm_pass."&vps_node=".$vps_node."&vps_name=".$vps_name;
		$r = "";
	}else{
		$r = $soap_client->call("getVPSInstallLog",array("vpsname" => $vps_name,"numlines" => "20"),"","","");
		$err = $soap_client->getError();
		if($err){
			$r = _("Could not get VPS install log. Error: ").$err._(" maybe there are no logs yet?");
		}
		// print_r($r);
		$r = str_replace("\n\n","\n",$r);
	}
*/

// tested AJAX stuff starts here

	$out .= "<script language=\"javascript\" src=\"xanjaxXHR.js\"></script>";

	$out .= "<h3>". _("Installation log (last 20 lines):") ."</h3><br>";

	$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
<tr><td bgcolor=\"black\"><font color=\"white\">$vps_node:$vps_name</font></td>
<tr><td bgcolor=\"black\"><font color=\"white\"><pre id=\"reinstall_os_log\" class=\"reinstall_os_log\"><font color=\"red\">dtc-xen</font>@<font color=\"blue\">$vps_node</font>&gt;_ #<br>...</pre></font></td>
</table>";

	$out .= "
		<script type=\"text/javascript\">
			xanGet(logPushlet,\"logPushlet.php?vps_node=".$vps_node."&vps_name=".$vps_name."\");
		</script>";

	return $out;
}

?>
