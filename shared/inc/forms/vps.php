<?php

require("$dtcshared_path/inc/forms/vps_strings.php");

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

	global $lang;
	global $txt_credential_not_correct;
	global $txt_method_getvpsstate_failed_not_running;
	global $txt_couldnot_connect_to_soap_server_http_error;
	global $txt_couldnot_connect_unknown_error;
	global $txt_checking_filesystem;
	global $txt_reinstalling_operating_system;

	global $txt_state_cannot_fetch;
	global $txt_cpu_cannot_fetch;
	global $txt_maxmem_cannot_fetch;
	global $txt_vps_state;
	global $txt_number_of_cpu;
	global $txt_vps_maxmem;
	global $txt_vps_memory;
	global $txt_vps_name;
	global $txt_vm_id;
	global $txt_vps_uptime;
	global $txt_vps_last_boot_date;
	global $txt_could_not_connect_to_vps_soap_server;

	global $txt_current_vps_contract;
	global $txt_vps_expiration_date;
	global $txt_your_vps_was_first_registered_on_the;

	global $txt_your_vps_has_expired_on_the;
	global $txt_please_renew_with_one_of_the_following_options;
	global $txt_your_vps_will_expire_on_the;
	global $txt_cpu_and_network_usage ;
	global $txt_ip_addresses;
	global $txt_ip_address;
	global $txt_current_vps_status;
	global $txt_start_stop_vps;
	global $txt_please_wait_until_fsck_finished;
	global $txt_please_wait_until_reinstall_os_finished;
	global $txt_gracefully_shutdown_xm_shutdown;
	global $txt_immediate_kill_xm_destroy;
	global $txt_to_do_a_file_system_check_or_operating_system_reinstallation;
	global $txt_boot_up_xm_start;
	global $txt_file_system_check;
	global $txt_file_system_check_fsck;
	global $txt_operating_system_type_not_supported;
	global $txt_reinstall_operating_system;
	global $txt_reinstall_operating_system_button;
	global $txt_currently_installed_operating_system;
	global $txt_change_bsd_kernel;
	global $txt_physical_console_last_display_and_ssh_access;
	global $txt_new_ssh_password;
	global $txt_new_ssh_key;
	global $txt_to_access_to_your_console_first_setup_a_ssh_password;

	$out = "";

	$checker = checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
	if($checker != true){
		return $txt_credential_not_correct[$lang].__FILE__." line ".__LINE__;
	}

	// VPS (remote SOAP) Status
	$soap_client = connectToVPSServer($vps_node);

	$vps_out = "";
	if($soap_client != false){
		$vps_remote_info = getVPSInfo($vps_node,$vps_name,$soap_client);

		if($vps_remote_info == false){
			if(strstr($vps_soap_err,"Method getVPSState failed")){
				$vps_out .= $txt_method_getvpsstate_failed_not_running[$lang]."<br><br>";
			}else if(strstr($vps_soap_err,"couldn't connect to host")){
				$vps_out .= $txt_couldnot_connect_to_soap_server_http_error[$lang]."<br><br>";
			}else{
				$vps_out .= $txt_couldnot_connect_unknown_error[$lang]."$vps_soap_err<br><br>";
			}
		}else if($vps_remote_info == "fsck"){
			$vps_out .= $txt_checking_filesystem[$lang]."<br><br>";
		}else if($vps_remote_info == "mkos"){
			$vps_out .= $txt_reinstalling_operating_system[$lang]."<br><br>";
		}else{
			if (isset($vps_remote_info["id"]))
			{
				$vps_out .= $txt_vm_id[$lang].$vps_remote_info["id"]."<br>";
			}
			if (isset($vps_remote_info["name"]))
			{
				$vps_out .= $txt_vps_name[$lang].$vps_remote_info["name"]."<br>";
			}
			if (isset($vps_remote_info["memory"]))
			{
				$vps_out .= $txt_vps_memory[$lang].$vps_remote_info["memory"]."<br>";
			}
			if(isset($vps_remote_info["maxmem"])){
				$vps_out .= $txt_vps_maxmem[$lang].$vps_remote_info["maxmem"]."<br>";
			}else{
				$vps_out .= $txt_maxmem_cannot_fetch[$lang]."<br>";
			}
			if(isset($vps_remote_info["cpu"])){
				$vps_out .= $txt_number_of_cpu[$lang].$vps_remote_info["cpu"]."<br>";
			}else{
				$vps_out .= $txt_cpu_cannot_fetch[$lang]."<br>";
			}
			if(isset($vps_remote_info["state"])){
				$vps_out .= $txt_vps_state[$lang].$vps_remote_info["state"]."<br>";
			}else{
				$vps_out .= $txt_state_cannot_fetch[$lang]."<br>";
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

				$vps_out .= $txt_vps_uptime[$lang]."$uptime_j day$upt_s_j $uptime_h hour$upt_s_h $uptime_m minute$upt_s_m $uptime_s seconde$upt_s_s<br>";
				$vps_out .= $txt_vps_last_boot_date[$lang].date("Y-m-d H:i:s",substr($vps_remote_info["start_time"],0,strlen($vps_remote_info["start_time"])-2))."<br>";
			}
			$vps_out .= "<br>";
		}
	}else{
		$vps_out .= $txt_could_not_connect_to_vps_soap_server[$lang];
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
		$contact = "not found!";
	}
	$out .= "<b><u>".$txt_current_vps_contract[$lang]."</u></b><br>$contract<br><br>";

	// Expiration management !
	$ar = explode("-",$vps["expire_date"]);
	$out .= "<b><u>".$txt_vps_expiration_date[$lang]."</u></b><br>";
	$out .= $txt_your_vps_was_first_registered_on_the[$lang].$vps["start_date"]."<br>";
	if(date("Y") > $ar[0] ||
			(date("Y") == $ar[0] && date("m") > $ar[1]) ||
			(date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
		$out .= "<font color=\"red\">".$txt_your_vps_has_expired_on_the[$lang].$vps["expire_date"]."</font>"
			."<br>".$txt_please_renew_with_one_of_the_following_options[$lang]."<br>";
	}else{
		$out .= $txt_your_vps_will_expire_on_the[$lang].$vps["expire_date"];
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
<input type=\"submit\" value=\"".$a["name"]." (".$a["price_dollar"]." USD)"."\">
</form>";
	}

	$out .= "<b><u>".$txt_cpu_and_network_usage[$lang]."</u></b><br>
<a target=\"_blank\" href=\"http://".$vps["vps_server_hostname"]."/dtc-xen/\">http://".$vps["vps_server_hostname"]."/dtc-xen/</a><br>";

	// The ip address(es)
	$vps_ips = $vps["ip_addr"];
	$n = sizeof($vps_ips);
	if($n > 1){
		$out .= $txt_ip_addresses[$lang];
	}else{
		$out .= $txt_ip_address[$lang];
	}
	for($i=0;$i<$n;$i++){
		if($i != 0){
			$out .= " - ";
		}
		$out .= $vps_ips[$i];
	}
	$out .= "<br><br>";

	// VPS status
	$out .= "<b><u>".$txt_current_vps_status[$lang]."</b></u><br>";
	$out .= $vps_out;

	// Start / stop VPS
	$out .= "<b><u>".$txt_start_stop_vps[$lang]."</u></b><br>";
	if($vps_remote_info == "fsck"){
		$out .= $txt_please_wait_until_fsck_finished[$lang]."<br><br>";
	}else if($vps_remote_info == "mkos"){
		$out .= $txt_please_wait_until_reinstall_os_finished[$lang]."<br><br>";
	}else if($vps_remote_info == true){
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"shutdown_vps\"
<input type=\"submit\" value=\"".$txt_gracefully_shutdown_xm_shutdown[$lang]."\">
</form>";
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"destroy_vps\"
<input type=\"submit\" value=\"".$txt_immediate_kill_xm_destroy[$lang]."\">
</form>";
		$out .= $txt_to_do_a_file_system_check_or_operating_system_reinstallation[$lang]."<br><br>";
	}else{
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"start_vps\">
<input type=\"submit\" value=\"".$txt_boot_up_xm_start[$lang]."\">
</form>";
		// FSCK
		$out .= "<b><u>".$txt_file_system_check[$lang]."</u></b><br>";
		$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"fsck_vps\">
<input type=\"submit\" value=\"".$txt_file_system_check_fsck[$lang]."\">
</form>";
		// OS reinstall
		$out .= "<b><u>".$txt_reinstall_operating_system[$lang]."</u></b><br>";
		$out .= $txt_currently_installed_operating_system[$lang].$vps["operatingsystem"]."<br>";
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
			die($txt_operating_system_type_not_supported[$lang]);
			break;
		}
		// Operating system selection popup and reinstallation button
		$out .= $frm_start."<select name=\"os_type\">
    <option value=\"debian\" $deb_selected>Debian</option>
    <option value=\"centos\" $cent_selected>CentOS</option>
    <option value=\"gentoo\" $gen_selected>Gentoo</option>
    <option value=\"netbsd\" $bsd_selected>NetBSD</option>
    </select><input type=\"hidden\" name=\"action\" value=\"reinstall_os\">
  <input type=\"submit\" value=\"".$txt_reinstall_operating_system_button[$lang]."\">
  </form>";
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
    <input type=\"submit\" value=\"".$txt_change_bsd_kernel[$lang]."\">
    </form>";
		}
	}

	// SSH Physical console password changing
	$out .= "<b><u>".$txt_physical_console_last_display_and_ssh_access[$lang]."</u></b><br>";

	$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_passwd\">
".$txt_new_ssh_password[$lang]."<input type=\"text\" name=\"new_password\" value=\"\"><input type=\"submit\" value=\"Ok\">
</form>";
	$out .= $frm_start."<input type=\"hidden\" name=\"action\" value=\"change_xm_console_ssh_key\">
".$txt_new_ssh_key[$lang]."<input size=\"40\" type=\"text\" name=\"new_key\" value=\"\"><input type=\"submit\" value=\"Ok\">
</form>";
	$out .= $txt_to_access_to_your_console_first_setup_a_ssh_password[$lang]."<br>xen".$vps_name."@".$vps_node."<br><br>";

	$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
<tr><td bgcolor=\"black\"><font color=\"white\">$vps_node:$vps_name</font></td>
<tr><td bgcolor=\"black\"><font color=\"white\"><pre>...</pre></font></td>
</table>";
	return $out;
}

?>
