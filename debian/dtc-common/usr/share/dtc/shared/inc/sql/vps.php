<?php

function remoteVPSAction($vps_node,$vps_name,$action){
	$soap_client = connectToVPSServer($vps_node);
	if($soap_client === false){
		echo "<font color=\"red\">". _("Could not connect to VPS server.") ."</font>";
		return;
	}
	switch($action){
	case "start_vps":
		$r = $soap_client->call("startVPS",array("vpsname" => "xen".$vps_name),"","","");
		break;
	case "destroy_vps":
		$r = $soap_client->call("destroyVPS",array("vpsname" => "xen".$vps_name),"","","");
		break;
	case "shutdown_vps":
		$r = $soap_client->call("shutdownVPS",array("vpsname" => "xen".$vps_name),"","","");
		break;
	case "kill_vps_disk":
		$r = $soap_client->call("killVPS",array("vpsname" => $vps_name),"","","");
		break;
	default:
		break;
	}
	$err = $soap_client->getError();
	if(!$err){
	//    echo "Result: ".print_r($r);
	}else{
		echo "Error: ".$err;
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "set_vps_monitoring_values"){
// email_addr monitor_ping monitor_ssh monitor_http monitor_smtp monitor_pop3 monitor_imap4
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
	}
	if(!isValidEmail($_REQUEST["email_addr"])){
		$submit_err = _("Wrong email format for the monitoring email address");
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		if(isset($_REQUEST["monitor_ping"]) && $_REQUEST["monitor_ping"] == "yes"){
			$monitor_ping = "yes";
		}else{
			$monitor_ping = "no";
		}
		if(isset($_REQUEST["monitor_ssh"]) && $_REQUEST["monitor_ssh"] == "yes"){
			$monitor_ssh = "yes";
		}else{
			$monitor_ssh = "no";
		}
		if(isset($_REQUEST["monitor_http"]) && $_REQUEST["monitor_http"] == "yes"){
			$monitor_http = "yes";
		}else{
			$monitor_http = "no";
		}
		if(isset($_REQUEST["monitor_smtp"]) && $_REQUEST["monitor_smtp"] == "yes"){
			$monitor_smtp = "yes";
		}else{
			$monitor_smtp = "no";
		}
		if(isset($_REQUEST["monitor_pop3"]) && $_REQUEST["monitor_pop3"] == "yes"){
			$monitor_pop3 = "yes";
		}else{
			$monitor_pop3 = "no";
		}
		if(isset($_REQUEST["monitor_imap4"]) && $_REQUEST["monitor_imap4"] == "yes"){
			$monitor_imap4 = "yes";
		}else{
			$monitor_imap4 = "no";
		}
		if(isset($_REQUEST["monitor_ftp"]) && $_REQUEST["monitor_ftp"] == "yes"){
			$monitor_ftp = "yes";
		}else{
			$monitor_ftp = "no";
		}
		$q = "UPDATE $pro_mysql_vps_table SET monitoring_email='".$_REQUEST["email_addr"]."',
monitor_ping='$monitor_ping', monitor_ssh='$monitor_ssh', monitor_http='$monitor_http', monitor_smtp='$monitor_smtp', monitor_pop3='$monitor_pop3',
monitor_imap4='$monitor_imap4', monitor_ftp='$monitor_ftp' WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

		updateUsingCron("gen_nagios='yes'");
	}
}

if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "shutdown_vps" || $_REQUEST["action"] == "destroy_vps" || $_REQUEST["action"] == "start_vps")){
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) == true){
		$q = "SELECT * FROM $pro_mysql_vps_table WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node' AND locked='no';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		if($n != 1){
			$submit_err = _("Access not granted (VPS is locked), line ") .__LINE__. _(" file ") .__FILE__;
		}
		remoteVPSAction($vps_node,$vps_name,$_REQUEST["action"]);
	}else{
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
	}
}
if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "set_ip_reverse_dns")){
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) == true){
		if(!isIP($_REQUEST["ip_addr"])){
			$submit_err = _("This is not a correct IP line ") .__LINE__. _(" file ") .__FILE__;
		}else{
			if(!isHostnameOrIP($_REQUEST["rdns"])){
				$submit_err = _("This is not a correct hostname or IP line ") .__LINE__. _(" file ") .__FILE__;
			}else{
				$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_addr='".$_REQUEST["ip_addr"]."' AND vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1){
					$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
				}else{
					$q = "UPDATE $pro_mysql_vps_ip_table SET rdns_addr='".$_REQUEST["rdns"]."',rdns_regen='yes' WHERE ip_addr='".$_REQUEST["ip_addr"]."';";
					$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$q = "SELECT $pro_mysql_ip_pool_table.zone_type
					FROM $pro_mysql_vps_ip_table,$pro_mysql_ip_pool_table
					WHERE $pro_mysql_vps_ip_table.ip_addr='".$_REQUEST["ip_addr"]."'
					AND $pro_mysql_ip_pool_table.id=$pro_mysql_vps_ip_table.ip_pool_id;";
					$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$n = mysql_num_rows($r);
					if($n != 1){
						$submit_err = _("Could not find the corresponding IP pool");
					}else{
						$a = mysql_fetch_array($r);
						if($a["zone_type"] == "support_ticket"){
							$submit_err = _("This IP pool can't be changed automatically, because our upstream network provider doesn't support it. Please open a support ticket to request this RDNS request.");
						}
					}
					updateUsingCron("gen_named='yes',gen_reverse='yes',reload_named='yes'");
				}
			}
		}
	}else{
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_xm_console_ssh_passwd"){
	if(!isDTCPassword($_REQUEST["new_password"])){
		$submit_err = _("The password you have submited is not a valid password") ;
		$commit_flag = "no";
	}
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$soap_client = connectToVPSServer($vps_node);
		if($soap_client === false){
			echo "<font color=\"red\">". _("Could not connect to VPS server.") ."</font>";
			return;
		}
		$r = $soap_client->call("changeVPSxmPassword",array("vpsname" => "xen".$vps_name,"password" => $_REQUEST["new_password"]),"","","");
		$err = $soap_client->getError();
		if(!$err){
		}else{
			echo _("Error: ") .$err;
		}
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_xm_console_ssh_key"){
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
	}
	if( !isSSHKey($_REQUEST["new_key"])){
		$commit_flag = "no";
		$submit_err = "Need to add the code for checking ssh key string validity.";
	}
	if($commit_flag == "yes"){
		$soap_client = connectToVPSServer($vps_node);
		if($soap_client === false){
			echo "<font color=\"red\">". _("Could not connect to VPS server.") ."</font>";
			return;
		}
		$r = $soap_client->call("changeVPSsshKey",array("vpsname" => "xen".$vps_name,"keystring" => $_REQUEST["new_key"]),"","","");
		$err = $soap_client->getError();
		if(!$err){
		}else{
			echo "Error: ".$err;
		}
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "fsck_vps"){
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$soap_client = connectToVPSServer($vps_node);
		if($soap_client === false){
			echo "<font color=\"red\">". _("Could not connect to VPS server.") ."</font>";
			return;
		}
		$r = $soap_client->call("fsckVPSpartition",array("vpsname" => "xen".$vps_name),"","","");
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "reinstall_os"){
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
	}
	// Os name checking is now more relaxed as this is customizable by the dtc-xen server
	if(!isFtpLogin($_REQUEST["os_type"])){
		$submit_err = "OS type is not corret ".__LINE__." file ".__FILE__;
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["root_password"])){
		$submit_err = "Root password is not a valid password";
		$commit_flag = "no";
	}
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$commit_flag = "no";
		$submit_err = _("Cannot get VPS information line ") .__LINE__. _(" file ") .__FILE__;
	}
	$ze_vps = mysql_fetch_array($r);

	// Get all IP addresses
	$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node' AND available='no';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$commit_flag = "no";
		$submit_err = _("Cannot get VPS IP addresses information line ") .__LINE__. _(" file ") .__FILE__;
	}
	$NICS = "";
	for($i=0;$i<$n;$i++){
		$iparray = mysql_fetch_array($r);
		$vps_ip = $iparray["ip_addr"];
		$pool_id = $iparray["ip_pool_id"];
		$q2 = "SELECT * FROM $pro_mysql_ip_pool_table WHERE id='$pool_id';";
		$r2 = mysql_query($q2)or die("Cannot execute query \"".$q2."\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			$commit_flag = "no";
			$submit_err = _("Cannot get VPS IP pool addresses information line ") .__LINE__. _(" file ") .__FILE__;
		}else{
			$a2 = mysql_fetch_array($r2);
			$nic = $vps_ip . "," . $a2["netmask"] . "," . $a2["broadcast"];
			if($i == 0){
				$gateway = $a2["gateway"];
				$dns = $a2["dns"];
				$NICS = $nic;
			}else{
				$NICS .= "+".$nic;
			}
		}
	}

	if($commit_flag == "yes"){
		$soap_client = connectToVPSServer($vps_node);
		if($soap_client === false){
			echo "<font color=\"red\">". _("Could not connect to VPS server.") ."</font>";
			return;
		}

		$q = "UPDATE $pro_mysql_vps_table SET operatingsystem='".$_REQUEST["os_type"]."' WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		if($_REQUEST["os_type"] != "netbsd"){
			if (isVPSNodeLVMEnabled($vps_node) == "no"){
				$image_type = "vbd";	
			}else{
				$image_type = "lvm";
			}
			// On this one we pass only "XX" and not "xenXX" as parameter !
			$r = $soap_client->call("reinstallVPSos",array(
				"vpsname" => $vps_name,
				"ostype" => $_REQUEST["os_type"],
				"ramsize" => $ze_vps["ramsize"],
				"password" => $_REQUEST["root_password"],
				"nics" => $NICS,
				"gateway" => $gateway,
				"dns" => $dns),"","","");
		}
	}
}

if( isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_xenhvm_boot_iso"){
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
	}
	$soap_client = connectToVPSServer($vps_node);
	if($soap_client === false){
		$submit_err = _("Could not connect to VPS server.");
		$commit_flag = "no";
	}
	if( $commit_flag == "yes" && $_REQUEST["xenhvm_iso"] != "hdd"){
		$r = $soap_client->call("reportInstalledIso",array("vpsname" => "xen".$vps_name),"","","");
		$err = $soap_client->getError();
                if($err){
                	$submit_err = _("Could not get installed ISO files.");
                	$commit_flag = "no";
		}else{
			if( ! in_array($_REQUEST["xenhvm_iso"],$r)){
				$submit_err = _("The ISO file is not in the server.");
				$commit_flag = "no";
			}
		}
	}
	if( $_REQUEST["vnc_console_activate"] == "no" || !isDTCPassword( $_REQUEST["vnc_console_pass"])){
		$vnc_console_pass = "no_vnc";
	}else{
		$vnc_console_pass = $_REQUEST["vnc_console_pass"];
	}
	if( !isDTCPassword( $_REQUEST["vnc_console_pass"]) && $_REQUEST["vnc_console_activate"] == "yes"){
		echo "<font color=\"yes\">" . _("Warning: incorrect password format, DTC will disable VNC console.");
	}
	if( $commit_flag == "yes"){
		$q = "UPDATE $pro_mysql_vps_table SET vncpassword='".mysql_real_escape_string($vnc_console_pass)."',howtoboot='".mysql_real_escape_string($_REQUEST["xenhvm_iso"])."' WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$q = "SELECT * FROM $pro_mysql_vps_table WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$commit_flag = "no";
			$submit_err = _("Could not fetch the VPS data.");
		}else{
			$a = mysql_fetch_array($r);
			$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE vps_server_hostname='" . $a["vps_server_hostname"]. "' AND vps_xen_name='" .$a["vps_xen_name"]. "';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$n_ip = mysql_num_rows($r);
			$ips = "";
			for($i=0;$i<$n_ip;$i++){
				if($i != 0){
					$ips .= " ";
				}
				$a2 = mysql_fetch_array($r);
				$ips .= $a2["ip_addr"];
			}
			// echo "Now calling writeXenHVMconf with" . "xen".$vps_name . " " . $a["ramsize"] . " '$ips' ". $_REQUEST["vnc_console_pass"] . " " . $_REQUEST["xenhvm_iso"];
			$r = $soap_client->call("writeXenHVMconf",array(
					"vpsname" => $vps_name,
					"ramsize" => $a["ramsize"],
					"allipaddrs" => $ips,
					"vncpassword" => $vnc_console_pass,
					"howtoboot" => $_REQUEST["xenhvm_iso"]),
					"","","");
		}
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_bsd_kernel_type"){
	if(checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name) != true){
		$submit_err = _("Access not granted line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
	}
	switch($_REQUEST["bsdkernel"]){
	case "normal":
	case "install":
		break;
	default:
		$submit_err = _("BSD kernel type is not correct line ") .__LINE__. _(" file ") .__FILE__;
		$commit_flag = "no";
		break;
	}

	$q = "SELECT * FROM $pro_mysql_vps_table WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$commit_flag = "no";
		$submit_err = _("Cannot get VPS information line ") .__LINE__. _(" file ") .__FILE__;
	}
	$ze_vps = mysql_fetch_array($r);

	// Get all IP addresses
	$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node' AND available='no';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$commit_flag = "no";
		$submit_err = _("Cannot get VPS IP addresses information line ") .__LINE__. _(" file ") .__FILE__;
	}
	$vps_all_ips = "";
	for($i=0;$i<$n;$i++){
		$iparray = mysql_fetch_array($r);
		if($i == 0){
			$ze_vps_ip = $iparray;
		}else{
			$vps_all_ips .= " ";
		}
		$vps_all_ips .= $iparray["ip_addr"];
	}

	if($commit_flag == "yes"){
		$soap_client = connectToVPSServer($vps_node);
		if($soap_client === false){
			echo "<font color=\"red\">". _("Could not connect to VPS server.") ."</font>";
			return;
		}
		$q = "UPDATE $pro_mysql_vps_table SET bsdkernel='".$_REQUEST["bsdkernel"]."' WHERE vps_xen_name='$vps_name' AND vps_server_hostname='$vps_node';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$r = $soap_client->call("changeBSDkernel",array(
			"vpsname" => $vps_name,
			"ramsize" => $ze_vps["ramsize"],
			"kerneltype" => $_REQUEST["bsdkernel"],
			"allipaddrs" => $vps_all_ips),"","","");
	}
}

?>
