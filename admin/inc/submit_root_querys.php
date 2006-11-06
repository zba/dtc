<?php

if(!isset($submit_err)){
	$submit_err = "";
}

// action=edit_vps_config&vps_server_hostname=node0106.gplhost.com&vps_xen_name=14&start_date=2006-04-24&expire_date=2006-10-24&hddsize=25600&ramsize=256&product_id=22
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

// VPS management
// action=delete_a_vps&id=2
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_a_vps"){
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE id='".$_REQUEST["id"]."';";
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
// $pro_mysql_vps_table = "vps";
// $pro_mysql_vps_ip_table = "vps_ip";
// $pro_mysql_vps_server_table = "vps_server";
	$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_addr='".$_REQUEST["vps_server_ip"]."' AND available='yes';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Didn't find the IP address you want to add line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);

	$q = "UPDATE $pro_mysql_vps_ip_table SET vps_xen_name='".$a["vps_xen_name"]."', available='no' WHERE ip_addr='".$_REQUEST["vps_server_ip"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());


	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["product_id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Didn't find the IP address you want to add line ".__LINE__." file ".__FILE__);
	}
	$prod = mysql_fetch_array($r);

	$exp_date = calculateExpirationDate(date("Y-m-d"),$prod["period"]);

	$q = "INSERT INTO $pro_mysql_vps_table (id,owner,vps_server_hostname,vps_xen_name,start_date,expire_date,hddsize,ramsize,product_id)
	VALUES('','$adm_login','".$a["vps_server_hostname"]."','".$a["vps_xen_name"]."','".date("Y-m-d")."','$exp_date','".$prod["quota_disk"]."','".$prod["memory_size"]."','".$_REQUEST["product_id"]."');";
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
	domainImport($uploaded_full_path,$adm_login);

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
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_vhosts='yes',gen_named='yes',reload_named='yes',restart_apache='yes' WHERE 1;";
	mysql_query($adm_query);
}

/////////////////////////////////////
// Domain name database management //
/////////////////////////////////////
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
function deleteUserDomain($adm_login,$adm_pass,$deluserdomain,$delete_directories = false){
	global $pro_mysql_admin_table;
	global $pro_mysql_pop_table;
	global $pro_mysql_ftp_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_domain_table;
	global $conf_demo_version;

	global $conf_root_admin_random_pass;
	global $conf_pass_expire;

	if($conf_root_admin_random_pass == $adm_pass &&  $conf_pass_expire > mktime()){
		$adm_query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	}else{
		$adm_query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	}
	$result = mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__LINE__." file ".__FILE__."sql said ".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1) die("User not found for deletion of domain $deluserdomain !!!");
	$row = mysql_fetch_array($result);
	$the_admin_path = $row["path"];

	// Delete all mail accounts
	$adm_query = "DELETE FROM $pro_mysql_pop_table WHERE mbox_host='$deluserdomain';";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!");

	// Delete all mailboxs
	$adm_query = "DELETE FROM $pro_mysql_ftp_table WHERE hostname='$deluserdomain';";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!");
	
	// Delete all subdomains
	$domupdate_query = "DELETE FROM $pro_mysql_subdomain_table WHERE domain_name='$deluserdomain';";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	// Delete the domain
	$adm_query = "DELETE FROM $pro_mysql_domain_table WHERE name='$deluserdomain' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!");

	// Delete the files of the domain name
	if($delete_directories == true && $conf_demo_version == "no"){
		system("rm -rf $the_admin_path/$deluserdomain");
	}
	triggerDomainListUpdate();
}

///////////////////////////////////////////////////////////////

function deleteMysqlUserAndDB($mysql_user){
	global $conf_mysql_db;
	mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");

	$query = "SELECT * FROM db WHERE User='$mysql_user';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!");
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		$db = $row["Db"];
		// Prevent system db from deletion
		if($db != $conf_mysql_db && $db != "mysql"){
			$query2 = "DROP DATABASE $db";
			mysql_query($query2)or die("Cannot execute query \"$query\" !!!");
		}
	}

	// Prevent system user from deletion
	if($mysql_user != "mysql" && $mysql_user != "root"){
		$query = "DELETE FROM db WHERE User='$mysql_user';";
		mysql_query($query)or die("Cannot execute query \"$query\" !!!");
		$query = "DELETE FROM user WHERE User='$mysql_user';";
		mysql_query($query)or die("Cannot execute query \"$query\" !!!");
	}
	$query = "FLUSH PRIVILEGES";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!");
	mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\" in deleteMysqlUserAndDB() !!!");
}


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
	$adm_query = "UPDATE $pro_mysql_admin_table SET id_client='".$_REQUEST["changed_id_client"]."',
		adm_pass='".$_REQUEST["changed_pass"]."',path='".$_REQUEST["changed_path"]."',
		quota='".$_REQUEST["adm_quota"]."', bandwidth_per_month_mb='".$_REQUEST["bandwidth_per_month"]."',
		expire='".$_REQUEST["expire"]."',allow_add_domain='".$_REQUEST["allow_add_domain"]."',
		nbrdb='".$_REQUEST["nbrdb"]."',prod_id='".$_REQUEST["heb_prod_id"]."',
		resseller_flag='".$_REQUEST["resseller_flag"]."',
		ssh_login_flag='".$_REQUEST["ssh_login_flag"]."'
		WHERE adm_login='$adm_login';";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__LINE__." file ".__FILE__." ".mysql_error());

	// Tell the cron job to activate the changes (because the account might now be (not) expiring)
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_vhosts='yes',restart_apache='yes' WHERE 1;";
	mysql_query($adm_query);
}

// $newadmin_login $newadmin_pass $newadmin_path $newadmin_maxemail $newadmin_maxftp $newadmin_quota
if(isset($_REQUEST["newadminuser"]) && $_REQUEST["newadminuser"]=="Ok"){
	// Check for admin existance
	// Create admin directorys
	if(!isFtpLogin($_REQUEST["newadmin_login"])){
		$submit_err .= $txt_err_dtc_login_format[$lang];
		$commit_flag = "no";
		die("Username not valid: it should be only made of letters and numbers");
	}
	if(!isDTCPassword($_REQUEST["newadmin_pass"])){
		$submit_err .= $txt_err_password_format[$lang];
		$commit_flag = "no";
	}
	$newadmin_path = $_REQUEST["newadmin_path"]."/".$_REQUEST["newadmin_login"];
	if($conf_demo_version == "no"){
		$oldumask = umask(0);
		if(!file_exists($newadmin_path)){
			mkdir("$newadmin_path", 0750);
			$console .= "mkdir $newadmin_path;<br>";
		}
		umask($oldumask);
	}

	// Add user in database
	if($commit_flag != "no"){
		$adm_query = "INSERT INTO $pro_mysql_admin_table
(adm_login        ,adm_pass         ,path            )VALUES
('".$_REQUEST["newadmin_login"]."', '".$_REQUEST["newadmin_pass"]."','$newadmin_path') ";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}
}

// action=delete_waiting_user&reqadm_login=tom
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete_waiting_user"){
	$q = "DELETE FROM $pro_mysql_new_admin_table WHERE reqadm_login='".$_REQUEST["reqadm_login"]."';";
	mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
}

// action=valid_waiting_user&reqadm_login=tom
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="valid_waiting_user"){
	validateWaitingUser($_REQUEST["reqadm_login"]);
	triggerDomainListUpdate();
}

if(isset($_REQUEST["delete_admin_user"]) && $_REQUEST["delete_admin_user"] != ""){
	$adm_query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["delete_admin_user"]."'";
	$result = mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!");
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1) die("User not found for deletion of domain $deluserdomain !!!");
	$row_virtual_admin = mysql_fetch_array($result);
	$the_admin_path = $row_virtual_admin["path"];

	// delete the user also mailboxs, ftp accounts, domains and subdomains in database
	$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='".$_REQUEST["delete_admin_user"]."';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!");
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		//echo "Deleting ".$_REQUEST["delete_admin_user"]." / ".$row_virtual_admin["adm_pass"].$row["name"];
		deleteUserDomain($_REQUEST["delete_admin_user"],$row_virtual_admin["adm_pass"],$row["name"]);
	}

	$adm_query = "DELETE FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["delete_admin_user"]."'";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!");

	if($conf_demo_version == "no"){
		system("rm -rf $the_admin_path");

		// Delete all databases of the user
		mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
		$query = "SELECT Host,Db,User FROM db WHERE User='".$_REQUEST["delete_admin_user"]."';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		for($i=0;$i<$num_rows;$i++){
			$row = mysql_fetch_array($result);
			echo $row["Db"];
			$db_name[] = $row["Db"];
		}
		for($i=0;$i<$num_rows;$i++){
			$query = "DROP DATABASE ".$db_name[$i];
			mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		}

		$query = "DELETE FROM db WHERE User='".$_REQUEST["delete_admin_user"]."';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$query = "DELETE FROM user WHERE User='".$_REQUEST["delete_admin_user"]."'";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	        mysql_query("FLUSH PRIVILEGES");
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\"	!!!");
	}

	deleteMysqlUserAndDB($_REQUEST["delete_admin_user"]);

	// Tell the cron job to activate the changes
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes',
	restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes',gen_ssh='yes' WHERE 1;";
	mysql_query($adm_query);
	triggerDomainListUpdate();
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "switch_generate_flag"){
	$query = "UPDATE $pro_mysql_domain_table SET generate_flag='".$_REQUEST["switch_to"]."' WHERE name='".$_REQUEST["domain"]."';";
	mysql_query($query)or die("Cannot execute query \"$query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "switch_safe_mode_flag"){
	$query = "UPDATE $pro_mysql_domain_table SET safe_mode='".$_REQUEST["switch_to"]."' WHERE name='".$_REQUEST["domain"]."';";
	mysql_query($query)or die("Cannot execute query \"$query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	// Tell the cron job to activate the changes (because ip could have change)
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_vhosts='yes',restart_apache='yes' WHERE 1;";
	mysql_query($adm_query);
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "switch_sbox_protect_flag"){
	$query = "UPDATE $pro_mysql_domain_table SET sbox_protect='".$_REQUEST["switch_to"]."' WHERE name='".$_REQUEST["domain"]."';";
	mysql_query($query)or die("Cannot execute query \"$query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	// Tell the cron job to activate the changes (because ip could have change)
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_vhosts='yes',restart_apache='yes' WHERE 1;";
	mysql_query($adm_query);
}

//////////////////////////////
// MySQL account management //
//////////////////////////////
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_mysql_user"){
	deleteMysqlUserAndDB($adm_login);
}

// ?adm_login=test&adm_pass=test&action=create_mysql_account
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "create_mysql_account"){
	mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
	$query = "INSERT INTO `user` (`Host`, `User`, `Password`, `Select_priv`, `Insert_priv`,
	`Update_priv`, `Delete_priv`, `Create_priv`, `Drop_priv`, `Reload_priv`, `Shutdown_priv`,
	`Process_priv`, `File_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`)
	VALUES ('localhost', '$adm_login', PASSWORD('$adm_pass'), 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N',
	'N', 'N', 'N', 'N', 'N')";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!");
	mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\"	!!!");
}

// adm_login=test&adm_pass=test&new_mysql_database_name=test2&new_mysql_database=Ok
if(isset($_REQUEST["new_mysql_database"]) && $_REQUEST["new_mysql_database"] == "Ok"){
	mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
	$query = "CREATE DATABASE ".$_REQUEST["new_mysql_database_name"].";";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!");

	$query = "INSERT INTO `db` (`Host`, `Db`, `User`, `Select_priv`, `Insert_priv`, `Update_priv`, `Delete_priv`, `Create_priv`,
	`Drop_priv`, `Grant_priv`, `References_priv`, `Index_priv`, `Alter_priv`) VALUES ('localhost', '".$_REQUEST["new_mysql_database_name"]."',
	'$adm_login', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'N', 'Y', 'Y', 'Y')";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!");

	mysql_query("FLUSH PRIVILEGES");

	mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\"	!!!".mysql_error());
}
// ?adm_login=test&adm_pass=test&action=delete_one_db&db_name=azerty2
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_one_db"){
	mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");

	$query = "DELETE FROM db WHERE User='$adm_login' AND Db='".$_REQUEST["db_name"]."';";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$query = "DROP DATABASE ".$_REQUEST["db_name"].";";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	mysql_query("FLUSH PRIVILEGES");

	mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\"	!!!");
}
?>
