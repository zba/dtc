<?php

function fetchTable($query){
	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$result = mysql_query($query);
	if (!$result)
	{
		$ret["err"] = 1;
		$ret["mesg"] = "Cannot query \"$query\" !";
		return $ret;
	}
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$table[] = mysql_fetch_array($result);
	}
	return $table;
}

function fetchMailboxInfos($adm_email_login,$adm_email_pass){
	global $pro_mysql_pop_table;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$a = explode('@',$adm_email_login);
	$mailbox = $a[0];
	$domain = $a[1];
	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$mailbox' AND mbox_host='$domain';";
	$r = mysql_query($q);
	if (!$r)
	{
		$ret["err"] = 1;
		$ret["mesg"] = "Cannot execute query \"$q\" !".mysql_error()." line ".__LINE__." file ".__FILE__;
		return $ret;
	}
	if(mysql_num_rows($r) != 1){
		$ret["mesg"] = _("Wrong user or password, or timeout expired") ;
		$ret["err"] = -1;
		return $ret;
	}
	$ret["data"] = mysql_fetch_array($r);
	return $ret;
}

function fetchCommands($id_client){
	global $pro_mysql_command_table;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$query = "SELECT * FROM $pro_mysql_command_table WHERE id_client='$id_client';";
	$result = mysql_query ($query);
	if (!$result)
	{
		$ret["err"] = 1;
		$ret["mesg"] = "Cannot execute query \"$query\"";
		return $ret;
	}
	$num_rows = mysql_num_rows($result);
	if($num_rows < 1){
		$ret["err"] = -1;
		$ret["mesg"] = "No command for this user";
		return $ret;
	}
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		$commands[] = $row;
	}
	$ret["data"] = $commands;
	return $ret;
}

function fetchAdminInfo($adm_login){
        global $pro_mysql_admin_table;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$result = mysql_query ($query);
	if (!$result)
	{
		$ret["err"] = 1;
		$ret["mesg"] = "Cannot execute query \"$query\"";
		return $ret;
	}
	$num_rows = mysql_num_rows($result);
 	if($num_rows > 1){
		$ret["err"] = 2;
		$ret["mesg"] = "More than one user with the name \"$adm_login\"";
		return $ret;
	}else if($num_rows < 1){
		$ret["mesg"] = "User not found.";
		$ret["err"] = -1;
		return $ret;
	}
	$row = mysql_fetch_array($result);
	if (!$row)
	{
		$ret["err"] = 3;
		$ret["mesg"] = "Cannot fetch user line ".__LINE__." file ".__FILE__." sql said: ".mysql_error();
		return $ret;
	}
	$ret["data"] = $row;
	return $ret;
}

function fetchAdminStats($admin){
	global $adm_login;
	global $conf_mysql_db;
	global $conf_demo_version;
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;
	global $pro_mysql_acc_email_table;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$ret["total_http"] = 0;
	$ret["total_hit"] = 0;
	$adm_path = $admin["info"]["path"];
	$query = "SELECT name,du_stat FROM ".$pro_mysql_domain_table." WHERE owner='".$admin["info"]["adm_login"]."' ORDER BY name";
	$result = mysql_query($query);
	if (!$result)
	{
		$ret["err"] = 1;
		$ret["mesg"] = "Cannot execute query \"$query\"".mysql_error();
		return $ret;
	}
	$num_domains = mysql_num_rows($result);
	$ret["total_ftp"] = 0;
	for($ad=0;$ad<$num_domains;$ad++){
		$domain_name = mysql_result($result,$ad,"name");
		$ret["domains"][$ad]["name"] = $domain_name;

		// Retrive disk usage
// Use the following version if you want it to be calculated in real time
//		$du_string = exec("du -sb $adm_path/$domain_name --exclude=access.log",$retval);
//		$du_state = explode("\t",$du_string);
//		$ret["domains"][$ad]["du"] = $du_state[0];
//		$ret["total_du_domains"] += $du_state[0];
// The following get value from table. du_stat is setup by cron script each time you have setup it (curently each hours)
		$du_stat = mysql_result($result,$ad,"du_stat");
		$ret["domains"][$ad]["du"] = $du_stat;
		if(!isset($ret["total_du_domains"]))	$ret["total_du_domains"] = $du_stat;
		else	$ret["total_du_domains"] += $du_stat;

		// HTTP transfer
// Uncomment this if you want it in realtime
//		sum_http($domain_name);
		$query_http = "SELECT SUM(bytes_sent) as bytes_sent , SUM(count_impressions) as count_impressions FROM $pro_mysql_acc_http_table WHERE domain='$domain_name' AND month='".date("m",time())."' AND year='".date("Y",time())."'";
		$result_http = mysql_query($query_http);
		if (!$result_http)
		{	
			$ret["err"] = 2;
			$ret["mesg"] ="Cannot execute query \"$query_http\"";
			return $ret;
		}
		$num_rows = mysql_num_rows($result_http);
		if($num_rows == 1){
			$rez_array = mysql_fetch_array($result_http);
			$rez_http = $rez_array["bytes_sent"];
			$ret["total_http"] += $rez_http;
			$ret["domains"][$ad]["http"] = $rez_http;
			$ret["total_hit"] += $rez_array["count_impressions"];
			$ret["domains"][$ad]["hit"] = $rez_array["count_impressions"];
		}else{
			$rez_http = 0;
			$ret["domains"][$ad]["http"] = 0;
			$ret["domains"][$ad]["hit"] = 0;
		}

		// And FTP transfer
// Uncomment this if you want it in realtime (currently done in cron)
//		sum_ftp($domain_name);
		$query_ftp = "SELECT SUM(transfer) AS transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='$domain_name' AND month='".date("m",time())."' AND year='".date("Y",time())."'";
		$result_ftp = mysql_query($query_ftp);
		if (!$result_ftp)
		{
			$ret["err"] = 3;
			$ret["mesg"] = "Cannot execute query \"$query\" !".mysql_error()." line ".__LINE__." file ".__FILE__;
			return $ret;
		}
		$num_rows = mysql_num_rows($result_ftp);
		$rez_ftp = mysql_result($result_ftp,0,"transfer");
		if($rez_ftp == NULL){
			$ret["total_ftp"] += 0;
			$ret["domains"][$ad]["ftp"] = 0;
		}else{
			if(!isset($ret["total_ftp"]))	$ret["total_ftp"] = $rez_ftp;
			else	$ret["total_ftp"] += $rez_ftp;
			$ret["domains"][$ad]["ftp"] = $rez_ftp;
		}

		// Email accounting
		$q = "SELECT smtp_trafic,pop_trafic,imap_trafic FROM $pro_mysql_acc_email_table
		WHERE domain_name='$domain_name' AND month='".date("m")."' AND year='".date("Y")."'";
		$r = mysql_query($q);
		if (!$r)
		{
			$ret["err"] = 4;
			$ret["mesg"] = "Cannot execute query \"$q\" !".mysql_error()." line ".__LINE__." file ".__FILE__;
			return $ret;
		}
		$num_rows = mysql_num_rows($r);
		if($num_rows == 1){
			$smtp_bytes = mysql_result($r,0,"smtp_trafic");
			$pop_bytes = mysql_result($r,0,"pop_trafic");
			$imap_bytes = mysql_result($r,0,"imap_trafic");
		}else{
			$smtp_bytes = 0;
			$pop_bytes = 0;
			$imap_bytes = 0;
		}
		$email_bytes = $smtp_bytes + $pop_bytes + $imap_bytes;
		if(!isset($ret["total_email"]))	$ret["total_email"] = $email_bytes;
		else	$ret["total_email"] += $email_bytes;
		$ret["domains"][$ad]["smtp"] = $smtp_bytes;
		$ret["domains"][$ad]["pop"] = $pop_bytes;
		$ret["domains"][$ad]["imap"] = $imap_bytes;

		if(!isset($ret["domains"][$ad]["total_transfer"]))
			$ret["domains"][$ad]["total_transfer"] = $rez_http + $rez_ftp + $email_bytes;
		else
			$ret["domains"][$ad]["total_transfer"] += $rez_http + $rez_ftp + $email_bytes;

		if(!isset($ret["total_transfer"]))
			$ret["total_transfer"] = $rez_http + $rez_ftp + $email_bytes;
		else
			$ret["total_transfer"] += $rez_http + $rez_ftp + $email_bytes;
	}

	$ret["total_du_db"] = 0;
	if($conf_demo_version != "yes"){
		mysql_select_db("mysql");
		$qu = "SELECT User FROM user WHERE dtcowner='".$admin["info"]["adm_login"]."'";
		$ru = $r = mysql_query($qu);
		if (!$ru)
		{
			$ret["err"] = 5;
			$ret["mesg"] = "Cannot query \"$qu\" !".mysql_error()." line ".__LINE__." file ".__FILE__;
			mysql_select_db($conf_mysql_db);
			return $ret;
		}
		$nbr_mysql_user = mysql_num_rows($ru);
		for($j=0;$j<$nbr_mysql_user;$j++){
			$au = mysql_fetch_array($ru);
			$dtcowner_user = $au["User"];

			$q = "SELECT Db FROM db WHERE User='$dtcowner_user'";
			$r = mysql_query($q);
			if (!$r)
			{
				$ret["err"] = 6;
				$ret["mesg"] = "Cannot query \"$q\" !".mysql_error()." line ".__LINE__." file ".__FILE__;
				mysql_select_db($conf_mysql_db);
				return $ret;
			}
			$db_nbr = mysql_num_rows($r);
			for($i=0;$i<$db_nbr;$i++){
				$db_name = mysql_result($r,$i,"Db");

				$query = "SHOW TABLE STATUS FROM $db_name;";
				$result = mysql_query($query);
				if (!$result){
					// $ret["err"] = 7;
					// $ret["mesg"] = "Cannot query \"$q\" !".mysql_error();
					// mysql_select_db($conf_mysql_db);
					// return $ret;
				}else{
					$num_tbl = mysql_num_rows($result);
					$ret["db"][$i]["du"] = 0;
					for($k=0;$k<$num_tbl;$k++){
						$db_du = mysql_result($result,$k,"Data_length");
						$ret["db"][$i]["du"] += $db_du;
						$ret["total_du_db"] += $db_du;
					}
					$ret["db"][$i]["name"] = $db_name;
				}
			}
		}
		mysql_select_db($conf_mysql_db);
	}

	// reset to 0, and add total_du_db and total_du_domains
	$ret["total_du"] = 0;
	if (isset($ret["total_du_db"]))
	{
		$ret["total_du"] += $ret["total_du_db"];	
	}
	if (isset($ret["total_du_domains"]))
	{
		$ret["total_du"] += $ret["total_du_domains"];
	}
// ["domains"][0-n]["name"]
//                 ["du"]
//                 ["ftp"]
//                 ["http"]
//		   ["hit"]
//                 ["smtp"]
//                 ["pop"]
//                 ["total_transfer"]
// ["total_http"]
// ["total_hit"]
// ["total_ftp"]
// ["total_email"]
// ["total_transfer"]
// ["total_du_domains"]
// ["db"][0-n]["name"]
//            ["du"]
// ["total_db_du"]
// ["total_du"]
	return $ret;
}

function randomizePassword($adm_login,$adm_input_pass){
	global $pro_mysql_admin_table;
	global $pro_mysql_tik_admins_table;
	global $adm_realpass;
	global $adm_pass;
	global $adm_random_pass;
	global $conf_session_expir_minute;

	global $panel_type;
	global $gettext_lang;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	if(isset($adm_random_pass) && strlen($adm_random_pass) > 0 && isRandomNum($adm_random_pass)){
		return $ret;
	}

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND ((adm_pass='$adm_input_pass' OR adm_pass=SHA1('$adm_input_pass')) OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query ($query);
	if (!$result)
	{
		$ret["err"] = 1;
		$ret["mesg"] = "Cannot execute query for password line ".__LINE__." file ".__FILE__." (error message removed for security reasons).";
		return $ret;
	}
	$num_rows = mysql_num_rows($result);

	if($num_rows != 1){
		$q = "SELECT * FROM $pro_mysql_tik_admins_table WHERE pass_next_req='$adm_input_pass' AND pass_expire > '".mktime()."';";
		$r = mysql_query($q);
		if (!$r)
		{
			$ret["err"] = 2;
			$ret["mesg"] = "Cannot execute query for password line ".__LINE__." file ".__FILE__." (error message removed for security reasons).";
			return $ret;
		}
		$n = mysql_num_rows($r);
		if($n == 1){
			$is_root_admin = "yes";
			$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
			$result = mysql_query ($query);
			if (!$result)
			{
				$ret["err"] = 3;
				$ret["mesg"] = "Cannot execute query for password line ".__LINE__." file ".__FILE__." (error message removed for security reasons).";
				return $ret;
			}
			$num_rows = mysql_num_rows($result);

			if($num_rows != 1){
				$ret["mesg"] = _("Incorrect username or password, or timeout expired.") ;
				$ret["err"] = -1;
				return $ret;
			}
		}else{
			$ret["mesg"] = _("Incorrect username or password, or timeout expired.") ;
			$ret["err"] = -1;
			return $ret;
		}
		$is_root_admin = "yes";
	}else{
		$is_root_admin = "no";
	}
	$row = mysql_fetch_array($result);
	if (!$row)
	{
		$ret["err"] = 4;
		$ret["mesg"] = "Cannot fetch user line ".__LINE__." file ".__FILE__." sql said: ".mysql_error();
		return $ret;
	}

	// This stuff is rotating passwords helping NOT to save passwords on users browsers.
	$rand = getRandomValue();
	$adm_random_pass = $rand;
	$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
	if($panel_type == "admin" && $is_root_admin == "yes"){
		$q = "UPDATE $pro_mysql_tik_admins_table SET pass_next_req='$rand', pass_expire='$expirationTIME' WHERE pseudo='".$_SERVER["PHP_AUTH_USER"]."';";
		$r = mysql_query($q);
		if (!$r)
		{
			$ret["err"] = 5;
			$ret["mesg"] = "Cannot execute query \"$q\" !";
			return $ret;
		}
	}else{
		$q = "UPDATE $pro_mysql_admin_table SET pass_next_req='$rand', pass_expire='$expirationTIME' WHERE adm_login='$adm_login'";
		$r = mysql_query($q);
		if (!$r)
		{
			$ret["err"] = 6;
			$ret["mesg"] = "Cannot execute query \"$q\" !";
			return $ret;
		}
	}
	// Save the last used language, so we know for future email sendings what to use.
	if(isset($gettext_lang) && $panel_type == "client"){
		$q = "UPDATE $pro_mysql_admin_table SET last_used_lang='$gettext_lang' WHERE adm_login='$adm_login';";
		$r = mysql_query($q);
	}

	$adm_pass = $rand;
	$adm_realpass = $row["adm_pass"];
}

function fetchAdminData($adm_login,$adm_input_pass){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_list_table;
	global $pro_mysql_pop_table;
	global $pro_mysql_mailaliasgroup_table;
	global $pro_mysql_ftp_table;
	global $pro_mysql_ssh_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_config_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_custom_product_table;
	global $panel_type;

	global $conf_session_expir_minute;

	global $adm_realpass;
	global $adm_pass;
	global $adm_random_pass;

	// This one is used by the root GUI so that you can browse your user
	// account at the same time as him without destroying his session.
	global $DONOT_USE_ROTATING_PASS;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	if($panel_type == "cronjob"){
		$pass = $adm_input_pass;
	}else{
		randomizePassword($adm_login,$adm_input_pass);
		$pass = $adm_realpass;
	}
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$pass' OR adm_pass=SHA1('$pass'));";
	$result = mysql_query ($query);
	if (!$result){
		$ret["err"] = 1;
		$ret["mesg"] = "Cannot execute query for password line ".__LINE__." file ".__FILE__." (MySQL error message removed for security reasons).";
		return $ret;
	}
	$row = mysql_fetch_array($result);
	if (!$row){
		$ret["err"] = 2;
		$ret["mesg"]= _("Cannot fetch user:")." "._("either your username or password is not valid, or your session has expired (timed out).");
		return $ret;
	}

	$adm_path = $row["path"];
	$adm_max_ftp = $row["max_ftp"];
	$adm_max_ssh = $row["max_ssh"];
	$adm_max_email = $row["max_email"];
	$adm_quota = $row["quota"];

	// Get all the VPS of the user
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE owner='$adm_login' ORDER BY vps_server_hostname,vps_xen_name;";
	$r = mysql_query ($q);
	if (!$r)
	{
		$ret["err"] = 3;
		$ret["mesg"]="Cannot execute query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error();
		return $ret;
	}
	$n = mysql_num_rows($r);
	$user_vps = array();
	for($i=0;$i<$n;$i++){
		$one_vps = mysql_fetch_array($r);
		$q2 = "SELECT * FROM $pro_mysql_vps_ip_table WHERE vps_server_hostname='".$one_vps["vps_server_hostname"]."' AND vps_xen_name='".$one_vps["vps_xen_name"]."' AND available='no' ORDER BY ip_addr;";
		$r2 = mysql_query ($q2);
		if (!$r2)
		{
			$ret["err"] = 4;
			$ret["mesg"]="Cannot execute query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error();
			return $ret;
		}
		$n2 = mysql_num_rows($r2);
		unset($vps_ip);
		$vps_ip = array();
		for($j=0;$j<$n2;$j++){
			$a2 = mysql_fetch_array($r2);
			$vps_ip[] = $a2["ip_addr"];
		}
		$one_vps["ip_addr"] = $vps_ip;
		$user_vps[] = $one_vps;
	}

	// Get all the dedicated servers of the user
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE owner='$adm_login' ORDER BY server_hostname;";
	$r = mysql_query ($q);
	if (!$r){
		$ret["err"] = 3;
		$ret["mesg"]="Cannot execute query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error();
		return $ret;
	}
	$n = mysql_num_rows($r);
	$user_dedicated = array();
	for($i=0;$i<$n;$i++){
		$user_dedicated[] = mysql_fetch_array($r);
	}

	// Get all custom products of the user
	$q = "SELECT * FROM $pro_mysql_custom_product_table WHERE owner='$adm_login' ORDER BY id;";
	$r = mysql_query ($q);
	if (!$r){
		$ret["err"] = 3;
		$ret["mesg"]="Cannot execute query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error();
		return $ret;
	}
	$n = mysql_num_rows($r);
	$user_custom = array();
	for($i=0;$i<$n;$i++){
		$user_custom[] = mysql_fetch_array($r);
	}

	// Get all domains of the user
	$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login' ORDER BY name;";
	$result = mysql_query ($query);
	if (!$result)
	{
		$ret["err"] = 5;
		$ret["mesg"] = "Cannot execute query \"$query\"";
		return $ret;
	}
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		//echo "$i<br>";
		$row = mysql_fetch_array($result);
		if (!$row)
		{
			$ret["err"] = 6;
			$ret["mesg"] = "Cannot fetch domain";
			return $ret;
		}
		unset($domain);
		$domain["name"] = $row["name"];
		$domain["safe_mode"] = $row["safe_mode"];
		$domain["sbox_protect"] = $row["sbox_protect"];
		$domain["max_email"] = $row["max_email"];
		$domain["max_lists"] = $row["max_lists"];
		$domain["max_ftp"] = $row["max_ftp"];
		$domain["max_ssh"] = $row["max_ssh"];
		$domain["max_subdomain"] = $row["max_subdomain"];
		$domain["quota"] = $row["quota"];
		$domain["ip_addr"] = $row["ip_addr"];
		$domain["backup_ip_addr"] = $row["backup_ip_addr"];
		$domain["generate_flag"] = $row["generate_flag"];
		$name = $row["name"];
		$domain["default_subdomain"] = $row["default_subdomain"];
		$domain["primary_dns"] = $row["primary_dns"];
		$domain["other_dns"] = $row["other_dns"];
		$domain["primary_mx"] = $row["primary_mx"];
		$domain["other_mx"] = $row["other_mx"];
		$domain["whois"] = $row["whois"];
		$domain["hosting"] = $row["hosting"];
		$domain["du_stat"] = $row["du_stat"];
		$domain["gen_unresolved_domain_alias"] = $row["gen_unresolved_domain_alias"];
		$domain["txt_root_entry"] = $row["txt_root_entry"];
		$domain["txt_root_entry2"] = $row["txt_root_entry2"];
		$domain["catchall_email"] = $row["catchall_email"];
		$domain["domain_parking"] = $row["domain_parking"];
		$domain["domain_parking_type"] = $row["domain_parking_type"];
		$domain["wildcard_dns"] = $row["wildcard_dns"];
		$domain["default_sub_server_alias"] = $row["default_sub_server_alias"];

		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$name' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2);
		if (!$result2)
		{
			$ret["err"] = 7;
			$ret["mesg"] = "Cannot execute query \"$query2\"";
			return $ret;
		}
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows < 1 && $domain["default_subdomain"] == NULL){
			$ret["mesg"] = "There is a default subdomain, but there is no subdomain in the database.";
			$ret["err"] = -3;
			return $ret;
		}
		unset($subs);
		$subs = array();
		for($j=0;$j<$num_rows2;$j++){
			$row2 = mysql_fetch_array($result2);
			unset($subdomain);
			if (!$row2)
			{
				$ret["err"] = 8;
				$ret["mesg"] = "Cannot fetch subdomain";
			}
			$subdomain["id"] = $row2["id"];
			$subdomain["name"] = $row2["subdomain_name"];
			$subdomain["safe_mode"] = $row2["safe_mode"];
			$subdomain["sbox_protect"] = $row2["sbox_protect"];
			$subdomain["path"] = $row2["path"];
			$subdomain["ip"] = $row2["ip"];
			if(isset($row2["login"])){
				$subdomain["login"] = $row2["login"];
				$subdomain["pass"] = $row2["pass"];
			}
			$subdomain["w3_alias"] = $row2["w3_alias"];
			$subdomain["register_globals"] = $row2["register_globals"];
			$subdomain["webalizer_generate"] = $row2["webalizer_generate"];
			$subdomain["associated_txt_record"] = $row2["associated_txt_record"];
			if (isset($row2["generate_vhost"])){
				$subdomain["generate_vhost"] = $row2["generate_vhost"];
			} else {
				$subdomain["generate_vhost"] = "yes";
			}
			$subdomain["ssl_ip"] = $row2["ssl_ip"];

			// if we want to generate a NS entry with this subdomain as the nameserver
			if (isset($row2["nameserver_for"])){
				$subdomain["nameserver_for"] = $row2["nameserver_for"];
			} else {
				$subdomain["nameserver_for"] = NULL;
			}
			$subs[] = $subdomain;
		}
		$domain["subdomains"] = $subs;
		//echo "$i<br>";

		// Check that the default subdomain exist in the database
		/*if($domain["default_subdomain"] != NULL){
			$nbr_subdomains = sizeof($domain["subdomains"]);
			$is_default_sub_ok = false;
			for($j=0;$j<$nbr_subdomains;$j++){
				if($domain["subdomains"][$j]["name"] ==	$domain["default_subdomain"]){
					$is_default_sub_ok = true;
				}
			}
			if($is_default_sub_ok == false){
				$ret["mesg"] = "Default subdomain not found in database.";
				$ret["err"] = -4;
				return $ret;
			}
		}*/
// At this point the following shema is fetched :
// $user_domains = [0-n]["default_subdomain"]
//                      ["name"]
//                      ["quota"]
//                      ["max_email"]
//                      ["max_ftp"]
//                      ["max_ssh"]
//                      ["max_subdomain"]
//                      ["ip_addr"]
//                      ["backup_ip_addr"]
//                      ["subdomains"][0-n]["name"]
//                                         ["path"]
//                                         ["ip"]
//                                         ["login"]
//                                         ["pass"]
//                                         ["w3_alias"]
//                                         ["register_globals"]
//                                         ["webalizer_generate"]
//                                         ["generate_vhost"]
//                                         ["associated_txt_record"]
// Now Can add emails to all thoses domains !
		$query4 = "SELECT * FROM $pro_mysql_pop_table WHERE mbox_host='$name' ORDER BY id;";
		$result4 = mysql_query ($query4);
		if (!$result4)
		{
			$ret["err"] = 9;
			$ret["mesg"] = "Cannot execute query \"$query4\"";
			return $ret;
		}
		$num_rows4 = mysql_num_rows($result4);
		unset($emails);
		for($j=0;$j<$num_rows4;$j++){
			$row4 = mysql_fetch_array($result4);
			if (!$row4)
			{
				$ret["err"] = 10;
				$ret["mesg"] = "Cannot fetch mailbox";
				return $ret;
			}
			unset($email);
			$email["id"] = $row4["id"];
			$email["uid"] = $row4["uid"];
			$email["gid"] = $row4["gid"];
			$email["home"] = $row4["home"];
			$email["crypt"] = $row4["crypt"];
			$email["passwd"] = $row4["passwd"];
			$email["shell"] = $row4["shell"];
			$email["redirect1"] = $row4["redirect1"];
			$email["redirect2"] = $row4["redirect2"];
			$email["localdeliver"] = $row4["localdeliver"];
			$email["bounce_msg"] = $row4["bounce_msg"];
			$email["spam_mailbox"] = $row4["spam_mailbox"];
			$email["spam_mailbox_enable"] = $row4["spam_mailbox_enable"];
			$email["vacation_flag"] = $row4["vacation_flag"];
			$email["vacation_text"] = $row4["vacation_text"];
			$emails[] = $email;
		}	
		if(isset($emails)){
			$domain["emails"] = $emails;
		}

// Now Can add alias emails to all thoses domains !
		$query5 = "SELECT * FROM $pro_mysql_mailaliasgroup_table WHERE domain_parent='$name' ORDER BY id;";
		$result5 = mysql_query ($query5);
		if (!$result5)
		{
			$ret["err"] = 9;
			$ret["mesg"] = "Cannot execute query \"$query5\"";
			return $ret;
		}
		$num_rows4 = mysql_num_rows($result5);
		unset($aliases);
		for($j=0;$j<$num_rows4;$j++){
			$row5 = mysql_fetch_array($result5);
			if (!$row5)
			{
				$ret["err"] = 10;
				$ret["mesg"] = "Cannot fetch mailbox";
				return $ret;
			}
			unset($alias);
			$alias["autoinc"] = $row5["autoinc"];
			$alias["id"] = $row5["id"];
			$alias["domain_parent"] = $row5["domain_parent"];
			$alias["delivery_group"] = $row5["delivery_group"];
			$alias["active"] = $row5["active"];
			$alias["start_date"] = $row5["start_date"];
			$alias["expire_date"] = $row5["expire_date"];
			$alias["bounce_msg"] = $row5["bounce_msg"];
			$aliases[] = $alias;
		}	
		if(isset($aliases)){
			$domain["aliases"] = $aliases;
		}

		//now to add all the mailing lists
		$query4 = "SELECT * FROM $pro_mysql_list_table WHERE domain='$name' ORDER BY name;";
		$result4 = mysql_query ($query4);
		if (!$result4)
		{
			$ret["err"] = 11;
			$ret["mesg"] = "Cannot execute query \"$query4\"";
			return $ret;
		}
		$num_rows4 = mysql_num_rows($result4);
		unset($mailinglists);
		for($j=0; $j < $num_rows4; $j++){
			$row4 = mysql_fetch_array($result4);
			if (!$row4)
			{
				$ret["err"] = 12;
				$ret["mesg"] = "Cannot fetch mailing list";
				return $ret;
			}
			unset($mailinglist);
			$mailinglist["id"] = $row4["id"];
			$mailinglist["name"] = $row4["name"];
			$mailinglist["owner"] = $row4["owner"];
			$mailinglist["domain"] = $row4["domain"];
			$mailinglists[] = $mailinglist;
		}
		if(isset($mailinglists)){
			$domain["mailinglists"] = $mailinglists;
		}

		$query4 = "SELECT * FROM $pro_mysql_ftp_table WHERE hostname='$name' ORDER BY login";
		$result4 = mysql_query($query4);
		if (!$result4)
		{
			$ret["err"] = 13;
			$ret["mesg"] = "Cannot execute query \"$query4\"";
			return $ret;
		}
		$num_rows4 = mysql_num_rows($result4);
		unset($ftps);
		for($j=0;$j<$num_rows4;$j++){
			$row4 = mysql_fetch_array($result4);
			if (!$row4)
			{
				$ret["err"] = 14;
				$ret["mesg"] = "Cannot fetch ftp account";
				return $ret;
			}
			$ftp["login"] = $row4["login"];
			$ftp["passwd"] = $row4["password"];
			$ftp["path"] = $row4["homedir"];
			$ftps[] = $ftp;
		}
		if(isset($ftps)){
			$domain["ftps"] = $ftps;
		}

		$query4 = "SELECT * FROM $pro_mysql_ssh_table WHERE hostname='$name' ORDER BY login";
		$result4 = mysql_query($query4);
		if (!$result4)
		{
			$ret["err"] = 15;
			$ret["mesg"] = "Cannot execute query \"$query4\"";
			return $ret;
		}
		$num_rows4 = mysql_num_rows($result4);
		unset($sshs);
		for($j=0;$j<$num_rows4;$j++){
			$row4 = mysql_fetch_array($result4);
			if (!$row4)
			{
				$ret["err"] = 16;
				$ret["mesg"] = "Cannot fetch ssh account";
				return $ret;
			}
			$ssh["login"] = $row4["login"];
			$ssh["passwd"] = $row4["password"];
			$ssh["path"] = $row4["homedir"];
			$sshs[] = $ssh;
		}
		if(isset($sshs)){
			$domain["sshs"] = $sshs;
		}

// Now we have :
// $user_domains = [0-n]["default_subdomain"]
//                      ["name"]
//                      ["quota"]
//                      ["max_email"]
//                      ["max_ftp"]
//                      ["max_ssh"]
//                      ["max_subdomain"]
//                      ["ip_addr"]
//                      ["backup_ip_addr"]
//			["domain_parking"]
//                      ["subdomains"][0-n]["name"]
//                                         ["path"]
//                                         ["ip"]
//                                         ["login"]
//                                         ["pass"]
//                                         ["w3_alias"]
//                                         ["register_globals"]
//                                         ["webalizer_generate"]
//                                         ["generate_vhost"]
//                                         ["associated_txt_record"]
//                      ["emails"][0-n]["id"]
//                                     ["uid"]
//                                     ["gid"]
//                                     ["home"]
//                                     ["crypt"]
//                                     ["passwd"]
//                                     ["shell"]
//			["mailinglists"][0-n]["id"]
//					     ["name"]
//					     ["owner"]
//                      ["ftps"]["login"]
//                              ["passwd"]
//                              ["path"]
//                      ["sshs"]["login"]
//                              ["passwd"]
//                              ["path"]
		$user_domains[] = $domain;
	}
	if(isset($user_domains)){
		$ret["data"] = $user_domains;
	}
	if(isset($user_vps)){
		$ret["vps"] = $user_vps;
	}
	if(isset($user_dedicated)){
		$ret["dedicated"] = $user_dedicated;
	}
	if(isset($user_custom)){
		$ret["custom"] = $user_custom;
	}
	return $ret;
}

function fetchClientData($id_client){
		global $pro_mysql_client_table;

		$query4 = "SELECT * FROM $pro_mysql_client_table WHERE id='$id_client'";
		$result4 = mysql_query($query4);
		if (!$result4)
		{
			$ret["err"] = 1;
			$ret["mesg"] = "Cannot execute query \"$query4\"";
			return $ret;
		}
		$num_rows4 = mysql_num_rows($result4);
		if($num_rows4 != 1){
			$ret["err"] = -1;
			$ret["msg"] = "Could not fetch commercial information for that user.";
			$ret["data"] = NULL;
			return $ret;
		}

		$row4 = mysql_fetch_array($result4);
		if (!$row4)
		{
			$ret["err"] = 2;
			$ret["mesg"] = "Cannot fetch client account";
			return $ret;
		}
		$ret["err"] = 0;
		$ret["msg"] = "No error";
		$ret["data"] = $row4;
		return $ret;
}

function fetchAdmin($adm_login, $adm_pass){
	global $panel_type;
	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$data = fetchAdminData($adm_login,$adm_pass);
	if($data["err"] != 0){
		$ret["err"] = $data["err"];
		$ret["mesg"] = $data["mesg"];
		return $ret;
/* I'm disabling this peace of code, I think it's quite hugly


		$http_auth_worked = 0;
		//if we have PHP_AUTH_USER or PHP_AUTH_PW, try to use them here
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="DTC Panel"');
			header('HTTP/1.0 401 Unauthorized');
			$ret["err"] = 1;
			$ret["mesg"] = 'You have not entered a correct user or password, please try again...';
			return $ret;
		} else if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
			//we should try and grab the admin data based on the PHP_AUTH_PW and PHP_AUTH_USER (only do this if we are not the admin panel) this is OK for user panels
			if ($panel_type != "admin" && $adm_login != $_SERVER['PHP_AUTH_USER'])
			{
				header('WWW-Authenticate: Basic realm="DTC Panel"');
				header('HTTP/1.0 401 Unauthorized');
				$ret["err"] =  2;
				$ret["mesg"] = 'You have not entered a correct user or password, please try again...';
				return $ret;
			}
			$data = fetchAdminData($adm_login,$_SERVER['PHP_AUTH_PW']);
			if($data["err"] != 0){
				$http_auth_worked = 0;
				$data["mesg"] = "DTC Timeout Error:" . $data["mesg"] ."\n";
				return $data;
			} else {
				$http_auth_worked = 1;
				//echo "adm_login as $adm_login\n";
				$adm_pass = $_SERVER['PHP_AUTH_PW'];
				$adm_login = $_SERVER['PHP_AUTH_USER'];
				//echo "adm_login is now $adm_login\n";
				$ret["err"] = 0;
				$ret["mesg"] = "No error";
			}
		}
		if ($http_auth_worked == 0){
			return $ret;
		}
		*/
	}
	//since we are here, our login/password combo must be valid

// Note from Thomas:
// This one I really want to kill it!
// My code with MySQL rotating password is to avoid storing
// things in php sessions that are by definition unsafe.
//	$_SERVER['PHP_AUTH_USER'] = $adm_login;
//	$_SERVER['PHP_AUTH_PW'] = $adm_pass;

	$info = fetchAdminInfo($adm_login);
	if($info["err"] != 0){
		$ret["err"] = $info["err"];
		$ret["mesg"] = $info["mesg"];
		return $ret;
	}
	//echo "adm_login is now $adm_login\n";
	//echo "the array contains: " . $info["data"]["adm_login"] . "\n";

	$id_client = $info["data"]["id_client"];
	if($id_client != 0){
		$client = fetchClientData($id_client);
		if($client["err"] != 0){
			$ret["err"] = $client["err"];
			$ret["mesg"] = $client["mesg"];
			return $ret;
		}
		$ret["client"] = $client["data"];
	}else{
		$ret["client"] = "NULL";
	}
	$ret["info"] = $info["data"];
	if(isset($data["data"])){
		$ret["data"] = $data["data"];
	}
	if(isset($data["vps"])){
		$ret["vps"] = $data["vps"];
	}
	if(isset($data["dedicated"])){
		$ret["dedicated"] = $data["dedicated"];
	}
	if(isset($data["custom"])){
		$ret["custom"] = $data["custom"];
	}
	return $ret;
}


?>
