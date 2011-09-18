<?php

if(!isset($submit_err)){
	$submit_err = "";
}

//////////////////////////////////
// Dedicated servers management //
//////////////////////////////////
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_a_dedicated"){
	$q = "DELETE FROM $pro_mysql_dedicated_table WHERE id='".$_REQUEST["id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_dedicated_to_user"){
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["product_id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Didn't find the product you want to add line ".__LINE__." file ".__FILE__);
	}
	$prod = mysql_fetch_array($r);

	$exp_date = calculateExpirationDate(date("Y-m-d"),$prod["period"]);

	$q = "INSERT INTO $pro_mysql_dedicated_table (id,owner,server_hostname,start_date,expire_date,hddsize,ramsize,product_id,country_code)
	VALUES('','$adm_login','".$_REQUEST["server_hostname"]."','".date("Y-m-d")."','$exp_date','".$prod["quota_disk"]."','".$prod["memory_size"]."','".$_REQUEST["product_id"]."','".$_REQUEST["country"]."');";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

////////////////////
// VPS management //
////////////////////
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit_vps_config"){
	$q = "UPDATE $pro_mysql_vps_table
	SET start_date='".$_REQUEST["start_date"]."',
	expire_date='".$_REQUEST["expire_date"]."',
	hddsize='".$_REQUEST["hddsize"]."',
	ramsize='".$_REQUEST["ramsize"]."',
	product_id='".$_REQUEST["product_id"]."'
	WHERE vps_server_hostname='".$_REQUEST["vps_server_hostname"]."' AND vps_xen_name='".$_REQUEST["vps_xen_name"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

function deleteVPS($id){
	global $pro_mysql_vps_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_stats_table;
	global $pro_mysql_cronjob_table;

	$q = "SELECT * FROM $pro_mysql_vps_table WHERE id='$id';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Didn't find the VPS id you want to delete line ".__LINE__." file ".__FILE__);
	}
	$vps = mysql_fetch_array($r);
	$q = "UPDATE $pro_mysql_vps_ip_table SET available='yes' WHERE vps_server_hostname='".$vps["vps_server_hostname"]."' AND vps_xen_name='".$vps["vps_xen_name"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	$q = "DELETE FROM $pro_mysql_vps_table WHERE id='".$_REQUEST["id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	$q = "DELETE FROM $pro_mysql_vps_stats_table WHERE vps_server_hostname='".$vps["vps_server_hostname"]."' AND vps_xen_name='xen".$vps["vps_xen_name"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	remoteVPSAction($vps["vps_server_hostname"],$vps["vps_xen_name"],"destroy_vps");
	remoteVPSAction($vps["vps_server_hostname"],$vps["vps_xen_name"],"kill_vps_disk");

	VPS_Server_Subscribe_To_Lists($vps["vps_server_hostname"]);

	$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_nagios='yes' WHERE 1;";
	mysql_query($adm_query);
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_a_vps"){
	deleteVPS($_REQUEST["id"]);
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "vps_server_list_remove"){
	$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE id='".$_REQUEST["edithost"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());;
	$a = mysql_fetch_array($r);
	$q = "DELETE FROM $pro_mysql_vps_server_lists_table WHERE hostname='".$a["hostname"]."' AND list_name='".$_REQUEST["list_name"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	resubscribe_VPS_server_list_users($_REQUEST["list_name"]);
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "vps_server_list_add"){
	$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE id='".$_REQUEST["edithost"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());;
	$a = mysql_fetch_array($r);
	$q = "INSERT INTO $pro_mysql_vps_server_lists_table (id,hostname,list_name) VALUES ('','".$a["hostname"]."','".$_REQUEST["name"]."');";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	resubscribe_VPS_server_list_users($_REQUEST["name"]);
}

/////////////////////////
// Renewal managements //
/////////////////////////
// Delete a pending
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_renewal"){
	$q = "DELETE FROM $pro_mysql_pending_renewal_table WHERE id='".$_REQUEST["id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

// Validate a renew
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "validate_renewal"){
	validateRenewal($_REQUEST["id"]);
}

// action=add_vps_to_user&vps_server_ip=66.251.193.60&vps_mem=1&vps_hdd=1&vps_duration=0000-01-00
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_vps_to_user"){
	$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_addr='".$_REQUEST["vps_server_ip"]."' AND available='yes';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Didn't find the IP address you want to add line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);

	$q = "UPDATE $pro_mysql_vps_ip_table SET available='no' WHERE vps_xen_name='".$a["vps_xen_name"]."' AND vps_server_hostname='".$a["vps_server_hostname"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["product_id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Didn't find the IP address you want to add line ".__LINE__." file ".__FILE__);
	}
	$prod = mysql_fetch_array($r);

	$exp_date = calculateExpirationDate(date("Y-m-d"),$prod["period"]);

	$q = "INSERT INTO $pro_mysql_vps_table (id,owner,vps_server_hostname,vps_xen_name,start_date,expire_date,hddsize,ramsize,bandwidth_per_month_gb,product_id)
	VALUES('','$adm_login','".$a["vps_server_hostname"]."','".$a["vps_xen_name"]."','".date("Y-m-d")."','$exp_date','".$prod["quota_disk"]."','".$prod["memory_size"]."','".$prod["bandwidth"]."','".$_REQUEST["product_id"]."');";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	// Setup the physical VPS (do the lvcreate remotly)
	if($_REQUEST["physical_setup"] == "yes"){
		$soap_client = connectToVPSServer($a["vps_server_hostname"]);
		if($soap_client == false){
			echo "Could not connect to the VPS server for doing the setup: please contact the administrator!";
		}else{
			$image_type = "lvm";
                        if (isVPSNodeLVMEnabled($a["vps_server_hostname"]) == "no")
                        {
                                $image_type = "vbd";
                        }

			$r = $soap_client->call("setupLVMDisks",array("vpsname" => $a["vps_xen_name"], "hddsize" => $prod["quota_disk"], "swapsize" => $prod["memory_size"], "imagetype" => $image_type),"","","");
		}
	}
}

// Import of domain config
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "import_domain"){
	$adm_path = getAdminPath($adm_login);
	$uploaded_file = basename($_FILES['domain_import_file']['name']);
	$uploaded_full_path = $adm_path."/".$uploaded_file;
//	echo "Importing domain file: ".$_FILES["domain_import_file"]["name"]." for user $adm_login";
	move_uploaded_file($_FILES["domain_import_file"]["tmp_name"],$uploaded_full_path);
	domainImport($uploaded_full_path,$adm_login,$adm_pass);
	unlink($uploaded_full_path);

	// Tell the cron job to activate the changes
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes',
	restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes',gen_ssh='yes' WHERE 1;";
	mysql_query($adm_query);
	triggerDomainListUpdate();
}

///////////////////////////////////////////////////////////////
// Mark all named zone file for generation and serial update //
///////////////////////////////////////////////////////////////
if(isset($_REQUEST["reinit_named_zones"]) && $_REQUEST["reinit_named_zones"] == "1"){
	$adm_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!");
}

// Edit one domain attribute
if(isset($_REQUEST["modify_domain_config"]) && $_REQUEST["modify_domain_config"]=="Ok"){
/*	if(!$_REQUEST["new_quota"] || !$_REQUEST["new_max_email"] || !$_REQUEST["new_max_ftp"] || !$_REQUEST["max_subdomain"] || !$_REQUEST["new_ip_addr"]){
		die("Incorrect script parameters");
	}*/
	$adm_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes',
	quota='".$_REQUEST["new_quota"]."',max_email='".$_REQUEST["new_max_email"]."',
	max_lists='".$_REQUEST["new_max_lists"]."', max_ftp='".$_REQUEST["new_max_ftp"]."',
	max_subdomain='".$_REQUEST["new_max_subdomain"]."',ip_addr='".$_REQUEST["new_ip_addr"]."',backup_ip_addr='".$_REQUEST["new_backup_ip_addr"]."'
	WHERE owner='$adm_login' AND name='".$_REQUEST["user_domain_to_modify"]."';";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!");

	// Tell the cron job to activate the changes (because ip could have change)
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_vhosts='yes',gen_named='yes',reload_named='yes',restart_apache='yes',gen_backup='yes',gen_webalizer='yes' WHERE 1;";
	mysql_query($adm_query);
}

/////////////////////////////////////
// Domain name database management //
/////////////////////////////////////
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "set_vhost_custom_directives"){
	$q = "UPDATE $pro_mysql_subdomain_table SET customize_vhost='".$_REQUEST["custom_directives"]."' WHERE domain_name='".$_REQUEST["edithost"]."' AND subdomain_name='".$_REQUEST["subdomain"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_vhosts='yes',restart_apache='yes' WHERE 1;";
	mysql_query($adm_query);
}

if(isset($_REQUEST["newdomain"]) && $_REQUEST["newdomain"] == "Ok"){
	if(isHostname($_REQUEST["newdomain_name"])){
		addDomainToUser($adm_login,$adm_pass,$_REQUEST["newdomain_name"]);
		triggerDomainListUpdate();
	}else{
		echo "<font color=\"red\">Hostname is not a valid domain name!</font>";
	}
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "valid_waiting_domain_to_user"){
	$q = "SELECT * FROM $pro_mysql_pending_queries_table WHERE id='".$_REQUEST["reqid"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("ID of pending domain not found!");
	$pending = mysql_fetch_array($r);

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$pending["adm_login"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("adm_login of pending domain not found!");
	$a = mysql_fetch_array($r);

	addDomainToUser($a["adm_login"],$a["adm_pass"],$pending["domain_name"]);
	triggerDomainListUpdate();

	$q = "DELETE FROM $pro_mysql_pending_queries_table WHERE id='".$_REQUEST["reqid"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_waiting_domain_to_user"){
	$q = "DELETE FROM $pro_mysql_pending_queries_table WHERE id='".$_REQUEST["reqid"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
}

///////////////////////////////////////////////////////////////

if(isset($_REQUEST["deluserdomain"]) && $_REQUEST["deluserdomain"] != ""){
	deleteUserDomain($adm_login,$adm_pass,$_REQUEST["deluserdomain"],true);

	// Tell the cron job to activate the changes
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes',
	restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes',gen_ssh='yes' WHERE 1;";
	mysql_query($adm_query);
	triggerDomainListUpdate();
}

////////////////////////////////////////////////
// Management of new users (eg virtual admins //
////////////////////////////////////////////////
if(isset($_REQUEST["updateuserinfo"]) && $_REQUEST["updateuserinfo"] == "Ok"){
	$q = "SELECT adm_pass FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		echo "Cannot find adm_login";
	}else{
		$ad = mysql_fetch_array($r);
		$old_pass = $ad["adm_pass"];
		if($conf_enforce_adm_encryption == "yes" && $old_pass != $_REQUEST["changed_pass"]){
			$new_encrypt_dtcadm_pass = "SHA1('".$_REQUEST["changed_pass"]."')";
		}else{
			$new_encrypt_dtcadm_pass = "'".$_REQUEST["changed_pass"]."'";
		}
		$adm_query = "UPDATE $pro_mysql_admin_table SET id_client='".$_REQUEST["changed_id_client"]."',
			adm_pass=$new_encrypt_dtcadm_pass,path='".$_REQUEST["changed_path"]."',
			quota='".$_REQUEST["adm_quota"]."', bandwidth_per_month_mb='".$_REQUEST["bandwidth_per_month"]."',
			expire='".$_REQUEST["expire"]."',allow_add_domain='".$_REQUEST["allow_add_domain"]."',max_domain='".$_REQUEST["max_domain"]."',
			nbrdb='".$_REQUEST["nbrdb"]."',prod_id='".$_REQUEST["heb_prod_id"]."',
			resseller_flag='".$_REQUEST["resseller_flag"]."',
			ssh_login_flag='".$_REQUEST["ssh_login_flag"]."',
			ftp_login_flag='".$_REQUEST["ftp_login_flag"]."',
			restricted_ftp_path='".$_REQUEST["restricted_ftp_path"]."',
			allow_dns_and_mx_change='".$_REQUEST["allow_dns_and_mx_change"]."',
			allow_mailing_list_edit='".$_REQUEST["allow_mailing_list_edit"]."',
			allow_subdomain_edit='".$_REQUEST["allow_subdomain_edit"]."',
			pkg_install_flag='".$_REQUEST["pkg_install_flag"]."',
			shared_hosting_security='".$_REQUEST["shared_hosting_security"]."'
			WHERE adm_login='$adm_login';";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__LINE__." file ".__FILE__." ".mysql_error());

		// Tell the cron job to activate the changes (because the account might now be (not) expiring)
		$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_vhosts='yes',restart_apache='yes' WHERE 1;";
		mysql_query($adm_query);
	}

}

// $newadmin_login $newadmin_pass $newadmin_path $newadmin_maxemail $newadmin_maxftp $newadmin_quota
if(isset($_REQUEST["newadminuser"]) && $_REQUEST["newadminuser"]=="Ok"){
	// Check for admin existance
	// Create admin directorys
	if(!isFtpLogin($_REQUEST["newadmin_login"])){
		$submit_err .= _("Incorrect admin login format: it should consist of only lowercase letters or numbers or the \"-\" sign, and should be between 4 and 16 chars long.<br>\n");
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["newadmin_pass"])){
		$submit_err .= _("Password may only contain letters and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n") ;
		$commit_flag = "no";
	}
	$newadmin_path = $_REQUEST["newadmin_path"]."/".$_REQUEST["newadmin_login"];
	if($conf_demo_version == "no"){
		$oldumask = umask(0);
		if(!file_exists($newadmin_path)){
			mkdir("$newadmin_path", 0750,1);
			$console .= "mkdir -p $newadmin_path;<br>";
		}
		umask($oldumask);
	}

	// Add user in database
	if($commit_flag != "no"){
		$adm_query = "INSERT INTO $pro_mysql_admin_table
(adm_login        ,adm_pass         ,path            )VALUES
('".$_REQUEST["newadmin_login"]."', '".$_REQUEST["newadmin_pass"]."','$newadmin_path') ";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		echo $submit_err;
	}
}

// action=delete_waiting_user&reqadm_login=tom
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete_waiting_user"){
	$q = "DELETE FROM $pro_mysql_new_admin_table WHERE id='".$_REQUEST["reqadm_id"]."';";
	mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="archive_waiting_user"){
	$q = "UPDATE $pro_mysql_new_admin_table SET archive='yes' WHERE id='".$_REQUEST["reqadm_id"]."';";
	mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
}

// action=valid_waiting_user&reqadm_login=tom
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="valid_waiting_user"){
	validateWaitingUser($_REQUEST["reqadm_id"]);
	triggerDomainListUpdate();
}

if(isset($_REQUEST["delete_admin_user"]) && $_REQUEST["delete_admin_user"] != ""){
	DTCdeleteAdmin($_REQUEST["delete_admin_user"]);
}

?>
