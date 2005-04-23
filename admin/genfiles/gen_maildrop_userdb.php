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

	global $console;

	global $conf_generated_file_path;
	global $conf_addr_mail_server;

	// check to see if we have maildrop installed
	// if we don't yet, don't run this

	if(!file_exists("/usr/sbin/userdb"))
	{
		return;
	}


	// now for our variables to write out the db info to

	$data = ""; // init var for use later on

	// go through each admin login and find the domains associated 
	$query = "SELECT * FROM $pro_mysql_admin_table ORDER BY adm_login;";
	$result = mysql_query ($query)or die("Cannot execute query : \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){
		die("No account to generate");
	}

	if (!file_exists("/etc/courier/userdb/"))
	{
		mkdir("/etc/courier/userdb/",0700);
		chown("/etc/courier/userdb/", "nobody");
	}

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
		if (isset($admin["data"]))
                {
                        $data = $admin["data"];
                        $nbr_domain = sizeof($data);
                }

		for($j=0;$j<$nbr_domain;$j++){
			$domain = $data[$j];
			$domain_full_name = $domain["name"];
			//if we are primary mx, add to userdb
			//else add to relay
			if (!($domain["primary_mx"] == "" || $domain["primary_mx"] == "default"))
			{
				continue;
			} 
			
			if(isset($domain["emails"])){
				$emails = $domain["emails"];
				$nbr_boites = sizeof($emails);
				// go through each of these emails and build the vmailbox file
				//also create our sasldb2 if we have a saslpasswd2 exe
				for($k=0;$k<$nbr_boites;$k++){
					$email = $emails[$k];
					$id = $email["id"];
					$uid = $email["uid"];
					$localdeliver = $email["localdeliver"];
					$redirect1 = $email["redirect1"];
					$redirect2 = $email["redirect2"];
					$_id = strtr($id,".",":");
					$home = $email["home"];
					$passwdtemp = $email["passwd"];
					$passwd = crypt($passwdtemp);

					if ($localdeliver == "yes"){
						system("/usr/sbin/userdb \"$domain_full_name/$id@$domain_full_name\" set home=$home mail=$home uid=65534 gid=65534");
					} 
				}
			}
		}
	}

	//after we have added all the users to the userdb
	system("/usr/sbin/makeuserdb");
}

?>
