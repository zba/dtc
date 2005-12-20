<?php

function fetchTable($query){
	$result = mysql_query($query) or die("Cannot query \"$query\" !");
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$table[] = mysql_fetch_array($result);
	}
	return $table;
}

function fetchMailboxInfos($adm_email_login,$adm_email_pass){
	global $pro_mysql_pop_table;
	global $lang;
	global $txt_wrong_user_or_password_or_timeout_expire;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$a = explode('@',$adm_email_login);
	$mailbox = $a[0];
	$domain = $a[1];
	$q = "SELECT * FROM $pro_mysql_pop_table WHERE id='$mailbox' AND mbox_host='$domain';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" !".mysql_error()." line ".__LINE__." file ".__FILE__);
	if(mysql_num_rows($r) != 1){
		$ret["mesg"] = $txt_wrong_user_or_password_or_timeout_expire[$lang];
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
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
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
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);
 	if($num_rows > 1){
		die("More than one user with the name \"$adm_login\"");
	}else if($num_rows < 1){
		$ret["mesg"] = "User not found !";
		$ret["err"] = -1;
		return $ret;
	}
	$row = mysql_fetch_array($result) or die ("Cannot fetch user");
	$ret["data"] = $row;
	return $ret;
}

function fetchAdminStats($admin){
	global $adm_login;
	global $conf_mysql_db;
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;
	global $pro_mysql_acc_email_table;

	$ret["total_http"] = 0;
	$ret["total_hit"] = 0;
	$adm_path = $admin["info"]["path"];
	$query = "SELECT name,du_stat FROM ".$pro_mysql_domain_table." WHERE owner='".$admin["info"]["adm_login"]."' ORDER BY name";
	$result = mysql_query($query)or die("Cannot execute query \"$query\"".mysql_error());
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
		$result_http = mysql_query($query_http)or die("Cannot execute query \"$query_http\"");
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
		$result_ftp = mysql_query($query_ftp)or die("Cannot execute query \"$query\" !".mysql_error()." line ".__LINE__." file ".__FILE__);
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
		$r = mysql_query($q)or die("Cannot execute query \"$q\" !".mysql_error()." line ".__LINE__." file ".__FILE__);
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

	$dbdu_amount = 0;
	mysql_select_db("mysql");
	$q = "SELECT Db FROM db WHERE User='".$admin["info"]["adm_login"]."'";
	$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error()." line ".__LINE__." file ".__FILE__);
	$db_nbr = mysql_num_rows($r);
	$ret["total_du_db"] = 0;
	for($i=0;$i<$db_nbr;$i++){
		$db_name = mysql_result($r,$i,"Db");

		$query = "SHOW TABLE STATUS FROM $db_name;";
		$result = mysql_query($query)or die("Cannot query \"$q\" !".mysql_error());
		$num_tbl = mysql_num_rows($result);
		$ret["db"][$i]["du"] = 0;
		for($j=0;$j<$num_tbl;$j++){
			$db_du = mysql_result($result,$j,"Data_length");
			$ret["db"][$i]["du"] += $db_du;
			$ret["total_du_db"] += $db_du;
		}
		$ret["db"][$i]["name"] = $db_name;
	}
	mysql_select_db($conf_mysql_db);

	// reset to 0, and add total_du_db and total_du_domains
	$ret["total_du"] = 0;
	if (isset($ret["total_du_db"]))
	{
		$ret["total_du_db"] += $ret["total_du_db"];	
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

function fetchAdminData($adm_login,$adm_input_pass){
        global $pro_mysql_domain_table;
        global $pro_mysql_admin_table;
        global $pro_mysql_list_table;
        global $pro_mysql_pop_table;
	global $pro_mysql_ftp_table;
        global $pro_mysql_subdomain_table;
        global $pro_mysql_config_table;
        global $panel_type;

	global $conf_session_expir_minute;

//	global $adm_login;
	global $adm_realpass;
	global $adm_pass;
	global $adm_random_pass;

	global $txt_wrong_user_or_password_or_timeout_expire;
	global $lang;

	// This one is used by the root GUI so that you can browse your user
	// account at the same time as him without destroying his session.
	global $DONOT_USE_ROTATING_PASS;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$query = "SELECT * FROM $pro_mysql_admin_table
WHERE adm_login='$adm_login' AND (adm_pass='$adm_input_pass'
OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";

	$result = mysql_query ($query)or die("Cannot execute query for password line ".__LINE__." file ".__FILE__." (error
	message removed for security reasons).");
	$num_rows = mysql_num_rows($result);

	if($num_rows != 1){
		$q = "SELECT * FROM $pro_mysql_config_table WHERE root_admin_random_pass='$adm_input_pass' AND pass_expire > '".mktime()."';";
		$r = mysql_query($q)or die("Cannot execute query for password line ".__LINE__." file ".__FILE__." (error message removed for security reasons).");
		$n = mysql_num_rows($r);
		if($n == 1){
			$is_root_admin = "yes";
			$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
			$result = mysql_query ($query)or die("Cannot execute query for password line ".__LINE__." file ".__FILE__." (error message removed for security reasons).");
			$num_rows = mysql_num_rows($result);

			if($num_rows != 1){
				$ret["mesg"] = $txt_wrong_user_or_password_or_timeout_expire[$lang];
				$ret["err"] = -1;
				return $ret;
			}
		}else{
			$ret["mesg"] = $txt_wrong_user_or_password_or_timeout_expire[$lang];
			$ret["err"] = -1;
			return $ret;
		}
	}else{
		$is_root_admin = "no";
	}

	$row = mysql_fetch_array($result) or die ("Cannot fetch user");

	// This stuff is rotating passwords helping NOT to save passwords on users browsers.
	$rand = getRandomValue();
	$adm_random_pass = $rand;
	$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
//	if($DONOT_USE_ROTATING_PASS != "yes"){
		if($is_root_admin == "yes"){
			$q = "UPDATE $pro_mysql_config_table SET root_admin_random_pass='$rand', pass_expire='$expirationTIME';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		}else{
			$q = "UPDATE $pro_mysql_admin_table SET pass_next_req='$rand', pass_expire='$expirationTIME' WHERE adm_login='$adm_login'";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		}
		$adm_pass = $rand;
//	}
	$adm_realpass = $row["adm_pass"];

	$adm_path = $row["path"];
	$adm_max_ftp = $row["max_ftp"];
	$adm_max_email = $row["max_email"];
	$adm_quota = $row["quota"];

	// Get all domains of the user
	$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login' ORDER BY name;";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);
	/*if($num_rows < 1){
		$ret["mesg"] = "No domain for this user.";
		$ret["err"] = -2;
		return $ret;
	}*/
	for($i=0;$i<$num_rows;$i++){
		//echo "$i<br>";
		$row = mysql_fetch_array($result) or die ("Cannot fetch domain");
		unset($domain);
		$domain["name"] = $row["name"];
		$domain["safe_mode"] = $row["safe_mode"];
		$domain["max_email"] = $row["max_email"];
		$domain["max_ftp"] = $row["max_ftp"];
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
		$domain["gen_unresolved_domain_alias"] = $row["gen_unresolved_domain_alias"];
		$domain["txt_root_entry"] = $row["txt_root_entry"];
		$domain["txt_root_entry2"] = $row["txt_root_entry2"];
		$domain["catchall_email"] = $row["catchall_email"];

		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$name';";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows < 1 && $domain["default_subdomain"] == NULL){
			$ret["mesg"] = "There is a default subdomain, but there is no subdomain in the database.";
			$ret["err"] = -3;
			return $ret;
		}
		unset($subs);
		for($j=0;$j<$num_rows2;$j++){
			$row2 = mysql_fetch_array($result2) or die ("Cannot fetch subdomain");
			$subdomain["name"] = $row2["subdomain_name"];
			$subdomain["safe_mode"] = $row2["safe_mode"];
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
		$query4 = "SELECT * FROM $pro_mysql_pop_table WHERE mbox_host='$name' ORDER BY id LIMIT 800;";
		$result4 = mysql_query ($query4)or die("Cannot execute query \"$query4\"");
		$num_rows4 = mysql_num_rows($result4);
		unset($emails);
		for($j=0;$j<$num_rows4;$j++){
			$row4 = mysql_fetch_array($result4) or die ("Cannot fetch mailbox");
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
			$emails[] = $email;
		}	
		if(isset($emails)){
			$domain["emails"] = $emails;
		}

		//now to add all the mailing lists
		$query4 = "SELECT * FROM $pro_mysql_list_table WHERE domain='$name' ORDER BY id LIMIT 800;";
		$result4 = mysql_query ($query4)or die("Cannot execute query \"$query4\"");
    $num_rows4 = mysql_num_rows($result4);
    unset($mailinglists);
		for($j=0; $j < $num_rows4; $j++)
		{
			$row4 = mysql_fetch_array($result4) or die ("Cannot fetch mailing list");
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

		$query4 = "SELECT * FROM $pro_mysql_ftp_table WHERE hostname='$name' ORDER BY login LIMIT 800";
		$result4 = mysql_query($query4)or die("Cannot execute query \"$query4\"");
		$num_rows4 = mysql_num_rows($result4);
		unset($ftps);
		for($j=0;$j<$num_rows4;$j++){
			$row4 = mysql_fetch_array($result4) or die ("Cannot fetch ftp account");
			$ftp["login"] = $row4["login"];
			$ftp["passwd"] = $row4["password"];
			$ftp["path"] = $row4["homedir"];
			// Remove the "/./" that is used to chroot pure-ftpd-mysql
			if( false != ereg("/./\$",$ftp["path"]) ){
				$ftp["path"] = substr($ftp["path"],0,strlen($ftp["path"]) - 3);
			}
			$ftps[] = $ftp;
		}
		if(isset($ftps)){
			$domain["ftps"] = $ftps;
		}

// Now we have :
// $user_domains = [0-n]["default_subdomain"]
//                      ["name"]
//                      ["quota"]
//                      ["max_email"]
//                      ["max_ftp"]
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
		$user_domains[] = $domain;
	}
	if(isset($user_domains)){
		$ret["data"] = $user_domains;
	}
	return $ret;
}

function fetchClientData($id_client){
		global $pro_mysql_client_table;

		$query4 = "SELECT * FROM $pro_mysql_client_table WHERE id='$id_client'";
		$result4 = mysql_query($query4)or die("Cannot execute query \"$query4\"");
		$num_rows4 = mysql_num_rows($result4);
		if($num_rows4 != 1){
			$ret["err"] = -1;
			$ret["msg"] = "Could not fetch commercial information for that user.";
			$ret["data"] = NULL;
			return $ret;
		}

		$row4 = mysql_fetch_array($result4) or die ("Cannot fetch ftp account");
		$ret["err"] = 0;
		$ret["msg"] = "No error";
		$ret["data"] = $row4;
		return $ret;
}

function fetchAdmin($adm_login,$adm_pass){
	//by default use the admin panel...
	//not sure how to determine this yet
	return fetchAdmin_2($adm_login, $adm_pass, 1);
}

function fetchAdmin_2($adm_login, $adm_pass, $adm_panel)
{
	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$data = fetchAdminData($adm_login,$adm_pass);
	if($data["err"] != 0){
		$ret["err"] = $data["err"];
		$ret["mesg"] = $data["mesg"];
		$http_auth_worked = 0;
		//if we have PHP_AUTH_USER or PHP_AUTH_PW, try to use them here
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="DTC Panel"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'You have not entered a correct user or password, please try again...';
			exit;
		} else if (
				isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
		){
			//we should try and grab the admin data based on the PHP_AUTH_PW and PHP_AUTH_USER (only do this if we are not the admin panel) this is OK for user panels
			if (!$adm_panel && $adm_login != $_SERVER['PHP_AUTH_USER'])
			{
				header('WWW-Authenticate: Basic realm="DTC Panel"');
				header('HTTP/1.0 401 Unauthorized');
				echo 'You have not entered a correct user or password, please try again...';
				exit;
			}
			$data = fetchAdminData($adm_login,$_SERVER['PHP_AUTH_PW']);
			if($data["err"] != 0){
				$http_auth_worked = 0;
				$err_msg = $data["err"] . $data["mesg"];
				echo "DTC Timeout Error: $err_msg\n";
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
	}
	//since we are here, our login/password combo must be valid
	$_SERVER['PHP_AUTH_USER'] = $adm_login;
	$_SERVER['PHP_AUTH_PW'] = $adm_pass;

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
	return $ret;
}


?>
