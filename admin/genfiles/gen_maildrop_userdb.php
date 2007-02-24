<?php

/*******************************************************************
 * This will generate the appropriate files for maildrop operation *
 * Will do the following:              *****************************
 * 1) check to see if the /etc/courier/userdb exists
 * 2) fix perms for userdb
 * 3) create the userdb entry
 *    # userdb "<hostname>/<user>@<hostname>" set home=<path just before
Maildir> mail=<path just before Maildir> uid=<uid> gid=<uid>
 * 4) update the db using makeuserdb
 *    # makeuserdb 
 **********************************************/

function mail_account_generate_maildrop(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_pop_table;
	global $conf_nobody_user_id;
	global $conf_nobody_group_id;
	global $conf_nobody_user_name;

	global $conf_dtc_system_uid;
	global $conf_dtc_system_username;
	global $conf_dtc_system_gid;
	global $conf_dtc_system_groupname;

	global $console;

	global $conf_generated_file_path;
	global $conf_addr_mail_server;

	if( ! is_file("/etc/courier/userdb") ){
		$console .= "Maildrop /etc/courier/userdb is not a file: cannot generate!";
		return false;
	}

	// This is a rewrite of this function that should be faster and better.
	$q2 = "SELECT name FROM $pro_mysql_domain_table";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	$userdb_file = "";
	for($j=0;$j<$n2;$j++){
		$a2 = mysql_fetch_array($r2);
		$name = $a2["name"];

		$q = "SELECT $pro_mysql_admin_table.path,$pro_mysql_domain_table.name,$pro_mysql_pop_table.id,$pro_mysql_pop_table.uid,$pro_mysql_pop_table.gid
		FROM $pro_mysql_admin_table,$pro_mysql_pop_table,$pro_mysql_domain_table
		WHERE $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner
		AND $pro_mysql_domain_table.name=$pro_mysql_pop_table.mbox_host
		AND $pro_mysql_domain_table.name='$name'
		ORDER BY $pro_mysql_pop_table.id";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$boxpath = $a["path"]."/".$a["name"]."/Mailboxs/".$a["id"];
			$userdb_file .= $a["id"]."@".$a["name"]."\t".'home='.$boxpath.'|mail='.$boxpath."|uid=".$a["uid"].'|gid='.$a["gid"]."\n";
		}

	}

	// Write the file
	$path_userdb = "/etc/courier/userdb";

	if(!is_writable($path_userdb)){
		$console .= "$path_userdb is not writable: please fix!";
		return;
	}
	$fp = fopen($path_userdb,"w+");
	if(!$fp){
		$console .= "Could not open $path_userdb in line ".__LINE__." file ".__FILE__;
		return;
	}
	if(fwrite($fp,$userdb_file) === FALSE){
		$console .= "Could not write $path_userdb in line ".__LINE__." file ".__FILE__;
		return;
	}
	$chmod_return = chmod ( $path_userdb, 0600 );
	fclose($fp);

	// Create the binary database
	system("/usr/sbin/makeuserdb");
	return;

/*
	// check to see if we have maildrop installed
	// if we don't yet, don't run this

	if(!file_exists("/usr/sbin/userdb")){
		echo "/usr/sbin/userdb exists as a file! Please remove...";
		return;
	}


	// now for our variables to write out the db info to

	$data = ""; // init var for use later on

	// go through each admin login and find the domains associated 
	$query = "SELECT * FROM $pro_mysql_admin_table ORDER BY adm_login;";
	$result = mysql_query ($query)or die("Cannot execute query : \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){
// This is TOTALY forbidden to die in the cron script!!!
// I know there should always be an admin, but still...
// Damien, did you write this one???
//		die("No account to generate");
	}

	if (!file_exists("/etc/courier/userdb/")){
		mkdir("/etc/courier/userdb/",0700);
		// (from thomas) I'm not sure about this one, so I first leave it commented with corrections...
		// chown("/etc/courier/userdb/", "nobody");
		chown("/etc/courier/userdb/", $conf_dtc_system_username);
	}

	$userdb_file = "";
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user-admin");
		$user_admin_name = $row["adm_login"];
		$user_admin_pass = $row["adm_pass"];
		$admin = fetchAdmin($user_admin_name,$user_admin_pass);
		if(($error = $admin["err"]) != 0){
			die("Error fetching admin : $error");
		}
		$info = $admin["info"];
		$nbr_domain = 0;
		if (isset($admin["data"])){
                        $data = $admin["data"];
                        $nbr_domain = sizeof($data);
                }

		for($j=0;$j<$nbr_domain;$j++){
			$domain = $data[$j];
			$domain_full_name = $domain["name"];
			//if we are primary mx, add to userdb
			//else add to relay
			if (!($domain["primary_mx"] == "" || $domain["primary_mx"] == "default"){
				continue;
			}

			if(isset($domain["emails"])){
				$emails = $domain["emails"];
				$nbr_boites = sizeof($emails);
				// go through each of these emails and build the vmailbox file
				//also create our sasldb2 if we have a saslpasswd2 exe
				$catch_all = $domain["catchall_email"];
				for($k=0;$k<$nbr_boites;$k++){
					$email = $emails[$k];
					$id = $email["id"];
					$uid = $email["uid"];

					// if our uid is 65534, make sure it's the correct uid as per the OS (99 for redhat)
                                        if ($uid == 65534)
                                        {
                                                $uid = $conf_nobody_user_id;
                                        }

					$localdeliver = $email["localdeliver"];
					$redirect1 = $email["redirect1"];
					$redirect2 = $email["redirect2"];
					$_id = strtr($id,".",":");
					$home = $email["home"];
					$passwdtemp = $email["passwd"];
					$passwd = crypt($passwdtemp, dtc_makesalt());

					if ($localdeliver == "yes"){
//						system("/usr/sbin/userdb \"$domain_full_name/$id@$domain_full_name\" set home=$home mail=$home uid=$conf_nobody_user_id gid=$conf_nobody_group_id");
						system("/usr/sbin/userdb \"$domain_full_name/$id@$domain_full_name\" set home=$home mail=$home uid=$conf_dtc_system_uid gid=$conf_dtc_system_gid");
						//if ($id == $catch_all)
						//{
						//	system("/usr/sbin/userdb \"$domain_full_name/@$domain_full_name\" set home=$home mail=$home uid=$conf_nobody_user_id gid=$conf_nobody_group_id");
						//}
					} 
				}
			}
		}
	}

	//after we have added all the users to the userdb
	system("/usr/sbin/makeuserdb");
//	chown("/etc/courier/userdb/", "$conf_nobody_user_name");
	chown("/etc/courier/userdb/", "$conf_dtc_system_username");
//	recurse_chown_chgrp("/etc/courier/userdb/", "$conf_nobody_user_name", $conf_nobody_group_id);
	recurse_chown_chgrp("/etc/courier/userdb/", "$conf_dtc_system_username", $conf_dtc_system_gid);
	*/
}

?>
