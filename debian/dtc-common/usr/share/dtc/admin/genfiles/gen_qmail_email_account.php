<?php

/**********************************************
 * Generate the following files:              *
 * /var/qmail/control/rcpthosts               *
 * domain.com                                 *
 *                                            *
 * /var/qmail/control/virtualdomains          *
 * domain.com:domain-com                      *
 *                                            *
 * /etc/poppasswd                             *
 * pop_login:crypted_password:real_login:path *
 *                                            *
 * /var/qmail/users/assign                    *
 * =domain-com-joe:nobody:888:888:path:::     *
 **********************************************/

function mail_account_generate_qmail(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_backup_table;

	global $console;

	global $conf_generated_file_path;
	global $conf_qmail_rcpthost_path;
	global $conf_qmail_virtualdomains_path;
	global $conf_qmail_assign_path;
	global $conf_qmail_poppasswd_path;

	global $conf_nobody_user_id;
	global $conf_nobody_group_id;

	global $conf_dtc_system_gid;
	global $conf_dtc_system_uid;
	global $conf_dtc_system_username;

	$rcpthosts_file = "";
	$local_domains_file = "";
	$virtualdomains_file = "";
	$poppasswd_file = "";
	$assign_file = "";
	$more_rcpt = "";

	$query = "SELECT * FROM $pro_mysql_admin_table ORDER BY adm_login;";
	$result = mysql_query ($query)or die("Cannot execute query : \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){
		die("No account to generate");
	}

	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user-admin");
		$user_admin_name = $row["adm_login"];
		$user_admin_pass = $row["adm_pass"];
		$admin = fetchAdmin($user_admin_name,$user_admin_pass);

		$admin_path = getAdminPath($user_admin_name);

		if(($error = $admin["err"]) != 0){
			die("Error fetching admin : $error");
		}
		$info = $admin["info"];

		if(isset($admin["data"])){
			$data = $admin["data"];
			$nbr_domain = sizeof($data);
		}else{
			$nbr_domain = 0;
		}

		for($j=0;$j<$nbr_domain;$j++){
			$domain = $data[$j];
			$domain_full_name = $domain["name"];
			$domain_qmail_name = strtr($domain_full_name, ".", "-");
			$rcpthosts_file .= "$domain_full_name\n";
			$local_domains_file .= "$domain_full_name\n";
			$more_rcpt .= "$domain_full_name\n";

			if ($domain["primary_mx"] == "" || $domain["primary_mx"] == "default"){
				$virtualdomains_file .= "$domain_full_name:$domain_qmail_name\n";
				$primary_mx=1;
			}else{
				$primary_mx=0;
			}


			if(isset($domain["mailinglists"]) && $primary_mx){
				$lists = $domain["mailinglists"];
				$nbr_boites = sizeof($lists);
				// go through each of these lists and add accounts to it
				for($k=0;$k<$nbr_boites;$k++){
					$list = $lists[$k];
					$list_id = $list["id"];
					$list_name = $list["name"];
					$list_owner = $list["owner"];
					$list_domain = $list["domain"];
					// add the missing domain to the list owner
					if (!preg_match("/\@/", $list_owner))
					{
						$list_owner .= "@" . $list_domain;
					}
					$list_path = "$admin_path/$list_domain/lists/$list_domain" . "_" . "$list_name";
					writeMlmmjQmailFile($admin_path);
					$assign_file .= "+$domain_qmail_name-$list_name:$conf_dtc_system_username:$conf_dtc_system_uid:$conf_dtc_system_gid:$list_path:::\n";
				}
			}
			if($primary_mx && isset($domain["emails"])){
				$emails = $domain["emails"];
				$catch_all = $domain["catchall_email"];
				$nbr_boites = sizeof($emails);
				$catch_all_flag = "no";
				// Handles all domain parking nicely
				if($domain["domain_parking"] != "no-parking"){
					for($b=0;$b<$nbr_domain;$b++){
						if($data[$b]["name"] == $domain["domain_parking"]){
							$domain["emails"] = $data[$b]["emails"];
						}
					}
				}
				// Loop for all mailboxes
				for($k=0;$k<$nbr_boites;$k++){
					$email = $emails[$k];
					$id = $email["id"];
					$home = $email["home"];
//					$box_path = "$admin_path/Mailboxs/$id";
					$qmail_id = strtr($id,".",":");
					$passwdtemp = $email["passwd"];
					$passwd = crypt($passwdtemp, dtc_makesalt());
					// This one is if you use the jedi's checkpassword programm
					// $poppasswd_file .= "$id@$domain_full_name:$passwd:nobody:$home\n";
					// This one is for cmd5checkpw
					$poppasswd_file .= "$id@$domain_full_name:$passwdtemp\n";
					if($catch_all == $id){
						$catch_all_flag = "yes";
						$catchall_home = $home;
					}else{
						$assign_file .= "=$domain_qmail_name-$id:$conf_dtc_system_username:$conf_dtc_system_uid:$conf_dtc_system_gid:$home:::\n";
					}
				}
				// Gen the catchall if there is a box like that
				if($catch_all_flag == "yes"){
					$assign_file .= "+$domain_qmail_name:$conf_dtc_system_username:$conf_dtc_system_ui:$conf_dtc_system_gid:".getAdminPath($user_admin_name)."/".$domain["name"]."/Mailboxs:::\n";
				}
			}
		}
	}

	$rcpthosts_file .= get_remote_mail_domains();

	$assign_file .= ".\n";
	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_rcpthost_path", "w");
	fwrite ($fp,$rcpthosts_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/local_domains", "w");
	fwrite ($fp,$local_domains_file);
	fclose($fp);



	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_virtualdomains_path", "w");
	fwrite ($fp,$virtualdomains_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_poppasswd_path", "w");
	fwrite ($fp,$poppasswd_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_assign_path", "w");
	fwrite ($fp,$assign_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/morercpthosts", "w");
	fwrite ($fp,$more_rcpt);
	fclose($fp);
}

?>
