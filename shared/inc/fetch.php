<?php

function fetchTable($query){
	$result = mysql_query($query) or die("Cannot query \"$query\" !");
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$table[] = mysql_fetch_array($result);
	}
	return $table;
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
	$ret["data"]["adm_login"] = $row["adm_login"];
	$ret["data"]["adm_pass"] = $row["adm_pass"];
	$ret["data"]["path"] = $row["path"];
	$ret["data"]["id_client"] = $row["id_client"];
	return $ret;
}

function fetchAdminData($adm_login,$adm_pass){
        global $pro_mysql_domain_table;
        global $pro_mysql_admin_table;
        global $pro_mysql_pop_table;
		global $pro_mysql_ftp_table;
        global $pro_mysql_subdomain_table;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

        $query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
        $result = mysql_query ($query)or die("Cannot execute query \"$query\"");
        $num_rows = mysql_num_rows($result);

 	if($num_rows != 1){
		$ret["mesg"] = "Wrong user or password.";
		$ret["err"] = -1;
		return $ret;
	}
	$row = mysql_fetch_array($result) or die ("Cannot fetch user");
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
		$domain["name"] = $row["name"];
		$domain["max_email"] = $row["max_email"];
		$domain["max_ftp"] = $row["max_ftp"];
		$domain["max_subdomain"] = $row["max_subdomain"];
		$domain["quota"] = $row["quota"];
		$domain["ip_addr"] = $row["ip_addr"];
		$domain["generate_flag"] = $row["generate_flag"];
		$name = $row["name"];
		$domain["default_subdomain"] = $row["default_subdomain"];
		$domain["primary_dns"] = $row["primary_dns"];
		$domain["other_dns"] = $row["other_dns"];
		$domain["primary_mx"] = $row["primary_mx"];
		$domain["other_mx"] = $row["other_mx"];
		$domain["whois"] = $row["whois"];
		$domain["hosting"] = $row["hosting"];

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
			$subdomain["path"] = $row2["path"];
			$subdomain["ip"] = $row2["ip"];
			$subdomain["login"] = $row2["login"];
			$subdomain["pass"] = $row2["pass"];
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
//                      ["subdomains"][0-n]["name"]
//                                         ["path"]

// Now Can add emails to all thoses domains !
		$query4 = "SELECT * FROM $pro_mysql_pop_table WHERE mbox_host='$name' ORDER BY id LIMIT 800;";
		$result4 = mysql_query ($query4)or die("Cannot execute query \"$query4\"");
		$num_rows4 = mysql_num_rows($result4);
		unset($emails);
		for($j=0;$j<$num_rows4;$j++){
			$row4 = mysql_fetch_array($result4) or die ("Cannot fetch mailbox");
			unset($email);
			$email["id"] = $row4["id"];
			$email["home"] = $row4["home"];
			$email["crypt"] = $row4["crypt"];
			$email["passwd"] = $row4["passwd"];
			$email["shell"] = $row4["shell"];
			$email["redirect1"] = $row4["redirect1"];
			$email["redirect2"] = $row4["redirect2"];
			$email["localdeliver"] = $row4["localdeliver"];
			$emails[] = $email;
		}	
		$domain["emails"] = $emails;

		$query4 = "SELECT * FROM $pro_mysql_ftp_table WHERE hostname='$name' ORDER BY login LIMIT 800";
		$result4 = mysql_query($query4)or die("Cannot execute query \"$query4\"");
		$num_rows4 = mysql_num_rows($result4);
		unset($ftps);
		for($j=0;$j<$num_rows4;$j++){
			$row4 = mysql_fetch_array($result4) or die ("Cannot fetch ftp account");
			$ftp["login"] = $row4["login"];
			$ftp["passwd"] = $row4["password"];
			$ftp["crypt"] = $row4["crypt"];
			$ftp["path"] = $row4["homedir"];
			$ftps[] = $ftp;
		}
		$domain["ftps"] = $ftps;

// Now we have :
// $user_domains = [0-n]["default_subdomain"]
//                      ["name"]
//                      ["quota"]
//                      ["max_email"]
//                      ["max_ftp"]
//                      ["max_subdomain"]
//                      ["ip_addr"]
//                      ["subdomains"][0-n]["name"]
//                                         ["path"]
//                      ["emails"][0-n]["id"]
//                                     ["home"]
//                                     ["crypt"]
//                                     ["passwd"]
//                                     ["shell"]
//                      ["ftps"]["login"]
//                              ["passwd"]
//                              ["path"]
		$user_domains[] = $domain;
	}
	$ret["data"] = $user_domains;
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
	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	$info = fetchAdminInfo($adm_login);
	if($info["err"] != 0){
		$ret["err"] = $info["err"];
		$ret["mesg"] = $info["mesg"];
		return $ret;
	}

	$data = fetchAdminData($adm_login,$adm_pass);
	if($data["err"] != 0){
		$ret["err"] = $data["err"];
		$ret["mesg"] = $data["mesg"];
		return $ret;
	}
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
	$ret["data"] = $data["data"];
	return $ret;
}


?>
