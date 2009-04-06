<?php

if(!isset($submit_err)){
	$submit_err = "";
}
if(!isset($commit_flag)){
	$commit_flag = "yes";
}

function checkDedicatedAdmin($adm_login,$adm_pass,$dedicated_server_hostname){
	global $pro_mysql_dedicated_table;
	checkLoginPass($adm_login,$adm_pass);
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE owner='$adm_login' AND server_hostname='".addslashes($dedicated_server_hostname)."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		return true;
	}else{
		return false;
	}
}

function validateRenewal($renew_id){
	global $pro_mysql_pending_renewal_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_completedorders_table;
	global $pro_mysql_client_table;
	global $pro_mysql_ssl_ips_table;

	global $commit_flag;
	global $submit_err;

	global $conf_webmaster_email_addr;
	global $conf_message_subject_header;

	$q = "SELECT * FROM $pro_mysql_pending_renewal_table WHERE id='$renew_id';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$submit_err = "Could not find pending renewal in table line ".__LINE__." file ".__FILE__;
		$commit_flag = "no";
		return false;
	}
	$renew_entry = mysql_fetch_array($r);

	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$renew_entry["product_id"]."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$submit_err = "Could not find product in table line ".__LINE__." file ".__FILE__;
		$commit_flag = "no";
		return false;
	}
	$product = mysql_fetch_array($r);

	switch($renew_entry["heb_type"]){
	case "vps":
		$q = "SELECT * FROM $pro_mysql_vps_table WHERE id='".$renew_entry["renew_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find VPS id in table line ".__LINE__." file ".__FILE__;
			$commit_flag = "no";
			return false;
		}
		$vps_entry = mysql_fetch_array($r);
		$old_expire = $vps_entry["expire_date"];
		$date_expire = calculateExpirationDate($old_expire,$product["period"]);
		$q = "UPDATE $pro_mysql_vps_table SET expire_date='$date_expire' WHERE id='".$renew_entry["renew_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		break;
	case "shared":
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$renew_entry["adm_login"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find admin login in table line ".__LINE__." file ".__FILE__;
			$commit_flag = "no";
			return false;
		}
		$admin = mysql_fetch_array($r);
		$old_expire = $admin["expire"];
		$date_expire = calculateExpirationDate($old_expire,$product["period"]);
		$q = "UPDATE $pro_mysql_admin_table SET expire='$date_expire' WHERE adm_login='".$renew_entry["adm_login"]."'";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		break;
	case "ssl":
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$renew_entry["adm_login"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find admin login in table line ".__LINE__." file ".__FILE__;
			$commit_flag = "no";
			return false;
		}
		$admin = mysql_fetch_array($r);
		$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE available='yes' LIMIT 1;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find any free IP for adding SSL.";
			return false;
		}
		$ssl_token = mysql_fetch_array($r);
		$old_expire = date("Y-m-d");
		$date_expire = calculateExpirationDate(date("Y-m-d"),$product["period"]);
		$q = "UPDATE $pro_mysql_ssl_ips_table SET available='no',adm_login='".$admin["adm_login"]."',expire='$date_expire' WHERE id='".$ssl_token["id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		break;
	case "server":
		$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE id='".$renew_entry["renew_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find dedicated server id in table line ".__LINE__." file ".__FILE__;
			$commit_flag = "no";
			return false;
		}
		$dedicated_entry = mysql_fetch_array($r);
		$old_expire = $dedicated_entry["expire_date"];
		$date_expire = calculateExpirationDate($old_expire,$product["period"]);
		$q = "UPDATE $pro_mysql_dedicated_table SET expire_date='$date_expire' WHERE id='".$renew_entry["renew_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		break;
	case "ssl_renew":
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$renew_entry["adm_login"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find admin login in table line ".__LINE__." file ".__FILE__;
			$commit_flag = "no";
			return false;
		}
		$admin = mysql_fetch_array($r);
		$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE available='no' AND id='".$renew_entry["renew_id"]."' LIMIT 1;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$submit_err = "Could not find the SSL IP to renew (id: ".$renew_entry["renew_id"].").";
			$commit_flag = "no";
			return false;
		}
		$ssl_ip = mysql_fetch_array($r);
		$old_expire = $ssl_ip["expire"];
		$date_expire = calculateExpirationDate($old_expire,$product["period"]);
		$q = "UPDATE $pro_mysql_ssl_ips_table SET expire='$date_expire' WHERE  id='".$ssl_ip["id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		break;
	default:
		die("Unknown heb type line ".__LINE__." file ".__FILE__);
		break;
	}

	global $secpayconf_currency_letters;
	$txt_renewal_approved = "

A renewal have been paid! Here is the details of the renewal:

login: ".$renew_entry["adm_login"]."
Product: ".$product["name"]."(".$product["price_dollar"]." ".$secpayconf_currency_letters.")
Date: ".$renew_entry["renew_date"]." ".$renew_entry["renew_time"]."

";

	$headers = "From: ".$conf_webmaster_email_addr;
	mail($conf_webmaster_email_addr,"$conf_message_subject_header Renewal approved!",$txt_renewal_approved,$headers);

	$q = "SELECT id_client FROM $pro_mysql_admin_table WHERE adm_login='".$renew_entry["adm_login"]."'";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		echo "Cannot find admin line ".__LINE__." file ".__FILE__;
	}
	$admin = mysql_fetch_array($r);
	$cid = $admin["id_client"];

	// Now add a command to the user so we keep tracks of payments
	$q = "INSERT INTO $pro_mysql_completedorders_table (id,id_client,domain_name,quantity,date,product_id,payment_id,country_code,last_expiry_date)
	VALUES ('','$cid','','1','".date("Y-m-d")."','".$product["id"]."','".$renew_entry["pay_id"]."','".$renew_entry["country_code"]."','$old_expire');";
	mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	$q = "DELETE FROM $pro_mysql_pending_renewal_table WHERE id='$renew_id';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	return true;
}

function validateWaitingUser($waiting_login_id){
	global $conf_administrative_site;
	global $conf_use_ssl;
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_new_admin_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;
	global $pro_mysql_completedorders_table;
	global $pro_mysql_domain_table;

	global $dtcshared_path;

	global $txt_userwaiting_account_activated_subject;
	global $txt_userwaiting_account_activated_text_header;

	global $conf_site_root_host_path;
	global $conf_demo_version;
	global $conf_use_ssl;
	global $conf_webmaster_email_addr;
	global $conf_this_server_country_code;
	global $conf_message_subject_header;
	global $console;

	//get affiliate cookie
	if( isset($_COOKIE["affiliate"]) && isMailbox($affiliatename)){
		$affiliatename = $_COOKIE["affiliate"];
	}

	if (isset($affiliatename)) {
		//Step 1: validate that the affiliatename exists
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".mysql_real_escape_string($affiliatename)."';";
		$r = mysql_query($q) or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		if (mysql_num_rows($r) != 1) { unset($affiliatename); }
		// at this point, we should have an affiliatename
	}

	// Get the informations from the user waiting table
	$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE id='$waiting_login_id';";
//	$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE reqadm_login='$waiting_login';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)die("I can't find username with id $waiting_login_id in the userwaiting table line: ".__LINE__." file: ".__FILE__."!");
	$a = mysql_fetch_array($r);
	$waiting_login = $a["reqadm_login"];

	// Check if there is a user by that name
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$waiting_login';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($a["add_service"] == "yes"){
		if($n != 1)die("There is no user with name $waiting_login in database: I can't add a service to it line: ".__LINE__." file: ".__FILE__."!");
		$existing_admin = mysql_fetch_array($r);
		$cid = $existing_admin["id_client"];
		$vps_root_pass = $existing_admin["adm_pass"];
	}else{
		if($n != 0)die("There is already a user with name $waiting_login in database: I can't add another one line: ".__LINE__." file: ".__FILE__."!");
		$vps_root_pass = $a["reqadm_pass"];
	}

	// Calculate user's path with default path
	$newadmin_path = $conf_site_root_host_path."/".$waiting_login;

	// Create admin's directory
	if($conf_demo_version == "no" && $a["add_service"] != "yes"){
		$oldumask = umask(0);
		if(!file_exists($newadmin_path)){
			mkdir("$newadmin_path", 0750);
			$console .= "mkdir $newadmin_path;<br>";
		}
	}

	// Get the informations from the product table
	$q2 = "SELECT * FROM $pro_mysql_product_table WHERE id='".$a["product_id"]."'";
	$r2 = mysql_query($q2)or die("Cannot execute query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1)die("I can't find the product in the table line: ".__LINE__." file: ".__FILE__."!");
	$a2 = mysql_fetch_array($r2);

	// Add customer's info to production table
	if($a["add_service"] != "yes"){
		$adm_query = "INSERT INTO $pro_mysql_client_table
(id,is_company,company_name,vat_num,familyname,christname,addr1,addr2,addr3,
city,zipcode,state,country,phone,fax,email,
disk_quota_mb,bw_quota_per_month_gb,special_note) VALUES ('','".$a["iscomp"]."',
'".addslashes($a["comp_name"])."','".addslashes($a["vat_num"])."','".addslashes($a["family_name"])."','".addslashes($a["first_name"])."',
'".addslashes($a["addr1"])."','".addslashes($a["addr2"])."','".addslashes($a["addr3"])."','".addslashes($a["city"])."',
'".addslashes($a["zipcode"])."','".addslashes($a["state"])."','".addslashes($a["country"])."','".addslashes($a["phone"])."',
'".addslashes($a["fax"])."','".addslashes($a["email"])."','".$a2["quota_disk"]."','". $a2["bandwidth"]/1024 ."',
'".addslashes($a["custom_notes"])."');";
		$r = mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$cid = mysql_insert_id();
	}
	// Add user in database
        $expires = calculateExpirationDate(date("Y-m-d"),$a2["period"]);
        if($a2["heb_type"] == "vps"){
        	$admtbl_added1 = ",expire,prod_id";
        	$admtbl_added2 = ",'0000-00-00','0'";
	}else if($a2["heb_type"] == "server"){
		$admtbl_added1 = ",expire,prod_id";
		$admtbl_added2 = ",'0000-00-00','0'";
        }else{
        	$admtbl_added1 = ",expire,prod_id";
        	$admtbl_added2 = ",'$expires','".$a2["id"]."'";
        	$admtbl_added3 = ", expire='$expires', prod_id='".$a2["id"]."' ";
        }
        if($a["add_service"] != "yes"){
		$adm_query = "INSERT INTO $pro_mysql_admin_table
(adm_login        ,adm_pass         ,path            ,id_client,bandwidth_per_month_mb,quota,nbrdb,allow_add_domain,max_email$admtbl_added1) VALUES
('$waiting_login','".$a["reqadm_pass"]."','$newadmin_path','$cid','".$a2["bandwidth"]."','".$a2["quota_disk"]."','".$a2["nbr_database"]."','".$a2["allow_add_domain"]."','".$a2["nbr_email"]."'$admtbl_added2);";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	}else{
		if($a2["heb_type"] == "shared"){
			$adm_query = "UPDATE $pro_mysql_admin_table
			SET bandwidth_per_month_mb='".$a2["bandwidth"]."', quota='".$a2["quota_disk"]."', nbrdb='".$a2["nbr_database"]."',
			allow_add_domain='".$a2["allow_add_domain"]."', max_email='".$a2["nbr_email"]."' $admtbl_added3
			WHERE adm_login='$waiting_login';";
			mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		}
	}

        if($a2["heb_type"] == "vps"){
		$vps_xen_name = addVPSToUser($waiting_login,$a["vps_location"],$a2["id"],$a["vps_os"]);
		$soap_client = connectToVPSServer($a["vps_location"]);
		if($soap_client == false){
			echo "Could not connect to the VPS server for doing the setup: please contact the administrator!";
		}else{
			$image_type = "lvm";
			if (isVPSNodeLVMEnabled($a["vps_location"]) == "no"){
                                $image_type = "vbd";
                        }

			$r = $soap_client->call("setupLVMDisks",array("vpsname" => $vps_xen_name, "hddsize" => $a2["quota_disk"], "swapsize" => $a2["memory_size"], "imagetype" => $image_type),"","","");
			$qvps = "SELECT * FROM $pro_mysql_vps_ip_table WHERE vps_server_hostname='".$a["vps_location"]."' AND vps_xen_name='$vps_xen_name' LIMIT 1;";
			$rvps = mysql_query($qvps)or die("Cannot execute query \"$qvps\" line ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$nvps = mysql_num_rows($rvps);
			if($nvps != 1){
				echo "Cannot find VPS IP: wont be able to setup the os, please get in touch with the administrator!";
			}else{
				$avps = mysql_fetch_array($rvps);
				$r = $soap_client->call("reinstallVPSos",array(
					"vpsname" => $vps_xen_name,
					"ostype" => $a["vps_os"],
					"hddsize" => $a2["quota_disk"],
					"ramsize" => $a2["memory_size"],
					"ipaddr" => $avps["ip_addr"],
					"password" => $vps_root_pass),"","","");
				$qcountry = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='".$a["vps_location"]."';";
				$rcountry = mysql_query($qcountry)or die("Cannot execute query \"$qcountry\" line ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$ncountry = mysql_num_rows($rcountry);
				if($ncountry != 1){
					echo "Cannot find VPS server country!";
					$country = 'US';
				}else{
					$acountry = mysql_fetch_array($rcountry);
					$country = $acountry["country_code"];
				}
			}
		}

		// Read the (customizable) registration message to send
		if(file_exists("/etc/dtc/registration_msg/vps_open.txt")){
			$fname = "/etc/dtc/registration_msg/vps_open.txt";
		}else{
			$fname = "$dtcshared_path/registration_msg/vps_open.txt";
		}
		$fp = fopen($fname,"r");
		$txt_welcome_message = fread($fp,filesize($fname));
		fclose($fp);
	}else if($a2["heb_type"] == "server"){
		// As there is currently no dedicated server provision system, we just do this:
		$country = $conf_this_server_country_code;
		addDedicatedToUser($waiting_login,$a["domain_name"],$a2["id"]);

		// Read the (customizable) registration message to send
		if(file_exists("/etc/dtc/registration_msg/dedicated_open.txt")){
			$fname = "/etc/dtc/registration_msg/dedicated_open.txt";
		}else{
			$fname = "$dtcshared_path/registration_msg/dedicated_open.txt";
		}
		$fp = fopen($fname,"r");
		$txt_welcome_message = fread($fp,filesize($fname));
		fclose($fp);
        }else{
        	$country = $conf_this_server_country_code;
		addDomainToUser($waiting_login,$a["reqadm_pass"],$a["domain_name"]);

		// Read the (customizable) registration message to send
		if(file_exists("/etc/dtc/registration_msg/shared_open.txt")){
			$fname = "/etc/dtc/registration_msg/shared_open.txt";
		}else{
			$fname = "$dtcshared_path/registration_msg/shared_open.txt";
		}
		$fp = fopen($fname,"r");
		$txt_welcome_message = fread($fp,filesize($fname));
		fclose($fp);

		$q = "UPDATE $pro_mysql_domain_table SET max_email='".$a2["nbr_email"]."',quota='".$a2["quota_disk"]."' WHERE name='".$a["domain_name"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
        }

	// Send a mail to user with how to login and use interface.
	$txt_userwaiting_account_activated_subject = "$conf_message_subject_header Account $waiting_login has been activated!";

	// Manage the signature of all registration messages
	if(file_exists("/etc/dtc/signature.txt")){
		$fname = "/etc/dtc/signature.txt";
	}else{
		$fname = "/usr/local/www/dtc/etc/signature.txt";
	}
	if(file_exists($fname)){
		$fp = fopen($fname,"r");
		$signature = fread($fp,filesize($fname));
		fclose($fp);
	}else{
		$signature = "";
	}
	$msg_2_send = str_replace("%%%SIGNATURE%%%",$signature,$txt_welcome_message);

	// Manage the login info part of the message
	if($conf_use_ssl == "yes"){
		$surl = "s";
	}else{
		$surl = "";
	}
	$dtc_login_info = "URL: http$surl://$conf_administrative_site/dtc/
Login: $waiting_login
Password: ".$a["reqadm_pass"];
	$msg_2_send = str_replace("%%%DTC_LOGIN_INFO%%%",$dtc_login_info,$msg_2_send);

	// Manage the header of the messages
	if(file_exists("/etc/dtc/messages_header.txt")){
		$fname = "/etc/dtc/messages_header.txt";
	}else{
		$fname = "/usr/local/www/dtc/etc/messages_header.txt";
	}
	if(file_exists($fname)){
		$fp = fopen($fname,"r");
		$head = fread($fp,filesize($fname));
		fclose($fp);
	}else{
		$head = "";
	}
	$msg_2_send = $head."
".$msg_2_send;

	$headers = "From: ".$conf_webmaster_email_addr;
	mail($a["email"],$txt_userwaiting_account_activated_subject,$msg_2_send,$headers);

	// Now add a command to the user so we keep tracks of payments
	$q = "INSERT INTO $pro_mysql_completedorders_table (id,id_client,domain_name,quantity,date,product_id,payment_id,country_code,last_expiry_date)
	VALUES ('','$cid','".$a["domain_name"]."','1','".date("Y-m-d")."','".$a["product_id"]."','".$a["paiement_id"]."','$country','".date("Y-d-m")."');";
	mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	if (isset($affiliatename)) {
		// Step 2) retrieve the kickback from the products table
		$kickback = $a2["affiliate_kickback"];
		$orderid = mysql_insert_id();
		if ($kickback) {
			// Step 3) if a kickback exists, store it in the affiliate transaction table
			$kickback = 1.0 + $kickback - 1.0; //cast to float.  I hate PHP.
			$xxs = "INSERT INTO affiliate_payments (adm_login,order_id,kickback) VALUES('$affiliatename',$orderid,$kickback);";
			mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		}
	}

	// Finaly delete the user from the userwaiting table
	$q = "DELETE FROM $pro_mysql_new_admin_table WHERE id='$waiting_login_id';";
	mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

}

// Get the path of a mailbox. pass_check_email() MUST have been called prior to call this function !!!
// Sets "box" with the box infos;
function get_mailbox_complete_path($user,$host){
	global $pro_mysql_pop_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;

	$q = "SELECT $pro_mysql_admin_table.path
FROM $pro_mysql_domain_table,$pro_mysql_admin_table
WHERE $pro_mysql_domain_table.name='$host'
AND $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1) die("Cannot find domain path in database ! line: ".__LINE__." file: ".__FILE__);
	$a = mysql_fetch_array($r);

	$boxpath = $a["path"]."/$host/Mailboxs/$user";
	return $boxpath;
}

// Get the path of a mailinglist. pass_check_email() MUST have been called prior to call this function !!!
// Sets "box" with the box infos;
function get_mailingbox_complete_path($listname,$host){
	global $pro_mysql_pop_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;

	$q = "SELECT $pro_mysql_admin_table.path
FROM $pro_mysql_domain_table,$pro_mysql_admin_table
WHERE $pro_mysql_domain_table.name='$host'
AND $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1) die("Cannot find domain path in database ! line: ".__LINE__." file: ".__FILE__);
	$a = mysql_fetch_array($r);

	$boxpath = $a["path"]."/$host/lists/$listname";
	return $boxpath;
}

function writeDotQmailFile($user,$host){
	global $pro_mysql_pop_table;
	global $conf_unix_type;
	global $conf_demo_version;

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host';";
	$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$box = mysql_fetch_array($res_mailbox);

	// Fetch the path of the mailbox
	$boxpath = get_mailbox_complete_path($user,$host);

	// Write .qmail file
	$oldumask = umask(0);
	if($conf_demo_version == "no"){
		if(!file_exists($boxpath)){
			mkdir($boxpath, 0775);
		}
		mk_Maildir($boxpath);
	}
	$qmail_file_content = "";
	if($box["localdeliver"] == "yes"){
		$qmail_file_content = "./Maildir/\n";
	}
	if($box["redirect1"] != "" && isset($box["redirect1"]) ){
		$qmail_file_content .= '&'.$box["redirect1"]."\n";
	}
	if($box["redirect2"] != "" && isset($box["redirect2"]) ){
		$qmail_file_content .= '&'.$box["redirect2"]."\n";
	}
	if($conf_demo_version == "no"){
		$fp = fopen ( "$boxpath/.qmail", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
		chmod ( "$boxpath/.qmail", 0644);
	}
	umask($oldumask);
}

function writeCatchallDotQmailFile($user,$host){
	global $pro_mysql_pop_table;
	global $conf_demo_version;

	$qmail_file_content = "";

	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$user' AND mbox_host='$host';";
	$res_mailbox = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$box = mysql_fetch_array($res_mailbox);

	// Fetch the path of the mailbox
	$boxpath = get_mailbox_complete_path($user,$host);

	// Write .qmail file
	$oldumask = umask(0);
	if($conf_demo_version == "no"){
		if(!file_exists($boxpath)){
			mkdir($boxpath, 0775);
		}
		mk_Maildir($boxpath);
	}
	if($box["localdeliver"] == "yes"){
		$qmail_file_content = "./$user/Maildir/\n";
	}
	if(isset($box["redirect1"]) && $box["redirect1"] != ""){
		$qmail_file_content .= '&'.$box["redirect1"]."\n";
	}
	if(isset($box["redirect2"]) && $box["redirect2"] != ""){
		$qmail_file_content .= '&'.$box["redirect2"]."\n";
	}
	if($conf_demo_version == "no"){
		$fp = fopen ( "$boxpath/../.qmail-default", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
		chmod ( "$boxpath/../.qmail-default", 0644);
	}
	umask($oldumask);
}

function writeMlmmjQmailFile($boxpath){
	global $conf_demo_version;

	// Write .qmail file
	$qmail_file_content = "|preline -f /usr/bin/mlmmj-recieve -L $boxpath\n";
	if($conf_demo_version == "no"){
		$fp = fopen ( "$boxpath/.qmail-default", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
		chmod ( "$boxpath/.qmail-default", 0644);
	}
}

if($panel_type!="email"){
	require("$dtcshared_path/inc/sql/dns.php");
	require("$dtcshared_path/inc/sql/database.php");
	require("$dtcshared_path/inc/sql/domain_info.php");
	require("$dtcshared_path/inc/sql/domain_stats.php");
	require("$dtcshared_path/inc/sql/subdomain.php");
	require("$dtcshared_path/inc/sql/ssh.php");
	require("$dtcshared_path/inc/sql/email.php");
	require("$dtcshared_path/inc/sql/lists.php");
	require("$dtcshared_path/inc/sql/reseller.php");
	require("$dtcshared_path/inc/vps.php");
	require("$dtcshared_path/inc/sql/vps.php");
	require("$dtcshared_path/inc/sql/ticket.php");
	require("$dtcshared_path/inc/sql/dedicated.php");
}else{
	require("submit_to_sql_dtcemail.php");
}

if(isset($submit_err) && $submit_err != ""){
	echo "<font color=\"red\">$submit_err</font>";
}

?>
