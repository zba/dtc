<?php

/********************************************************
 * This will generate the appropriate files for postfix *
 * Generate the following files:              ***********
 * /usr/share/dtc/etc/postfix_virtual_mailbox_domains       *
 * domain.com	virtual                       *
 *                                            *
 * /usr/share/dtc/etc/postfix_virtual                       *
 * postmaster@domain.com	postmaster    *
 *                                            *
 * /usr/share/dtc/etc/postfix_vmailbox                      *
 * emailaddress@domain.com	dtc/domain/Mailboxs/<emailaddress>/Maildir/
 *                                            *
 * /usr/share/dtc/etc/postfix_virtual_uid_mapping           
 * emailaddress@domain.com	65534         
 *
 * /usr/share/dtc/etc/postfix_relay_domains
 * domain.name
 * domain2.name
 *
 **********************************************/

function mail_account_generate_postfix(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $console;

	global $conf_generated_file_path;
	//global $conf_postfix_virtual_mailbox_domains_path;
	//global $conf_postfix_virtual_path;
	//global $conf_postfix_vmailbox_path;
	//global $conf_postfix_virtual_uid_mapping_path;

	// prepend the configured path here

	$conf_postfix_virtual_mailbox_domains_path = $conf_generated_file_path . "/postfix_virtual_mailbox_domains";
	$conf_postfix_virtual_path = $conf_generated_file_path . "/postfix_virtual";
	$conf_postfix_vmailbox_path = $conf_generated_file_path . "/postfix_vmailbox";
	$conf_postfix_virtual_uid_mapping_path = $conf_generated_file_path . "/postfix_virtual_uid_mapping";

	$conf_postfix_relay_domains_path = $conf_generated_file_path . "/postfix_relay_domains";

	// now for our variables to write out the db info to

	$domains_file = "";
	$domains_postmasters_file = "";
	$vmailboxes_file = "";
	$uid_mappings_file = "";
	$relay_domains_file = "";

	// go through each admin login and find the domains associated 
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
		if(($error = $admin["err"]) != 0){
			die("Error fetching admin : $error");
		}
		$info = $admin["info"];
		$data = $admin["data"];


		$nbr_domain = sizeof($data);
		for($j=0;$j<$nbr_domain;$j++){
			$domain = $data[$j];
			$domain_full_name = $domain["name"];
			//if we are primary mx, add to domains
			//else add to relay
			if ($domain["primary_mx"] == "" || $domain["primary_mx"] == "default")
			{
				$domains_file .= "$domain_full_name virtual\n";
			} else {
				$relay_domains_file .= "$domain_full_name\n";
			}
			$domains_postmasters_file .= "postmaster@$domain_full_name postmaster\n";
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
				$poppasswd_file .= "$id@$domain_full_name:$passwd:nobody:$home\n";
				# first try and see if we have postfix in a chroot, else just put it in it's default location
				system("./genfiles/gen_sasl.sh $domain_full_name $id $passwdtemp");
				//$assign_file .= "=$domain_postfix_name-$id:nobody:65534:65534:$home:::\n";
				//$console .= "=$domain_postfix_name-$id:nobody:65534:65534:$home:::\n";
				if ($localdeliver == yes)
				{
					$vmailboxes_file .= "$id@$domain_full_name $home/Maildir/\n";
					$uid_mappings_file .= "$id@$domain_full_name $uid\n";				
				} else {
					$extra_redirects = "";
					if ($redirect1 != "" && isset($redirect1))
					{
						$extra_redirects .= " $redirect1 ";
					}
					if ($redirect2 != "" && isset($redirect2))
					{
						if ($extra_redirects != ""){
							$extra_redirects .= " , $redirect2";
						} else {
							$extra_redirects .= " $redirect2 ";
						}
					}
					$domains_postmasters_file .= "$id@$domain_full_name	$extra_redirects\n";
				}
			}
		}
	}
	$assign_file .= ".\n";

	$relay_domains_file .= get_remote_mail_domains();

	//write out our config files
	$fp = fopen ( "$conf_postfix_virtual_mailbox_domains_path", "w");
	fwrite($fp, $domains_file);
	fclose($fp);

	$fp = fopen ( "$conf_postfix_virtual_path", "w");
	fwrite($fp, $domains_postmasters_file);
	fclose($fp);

	$fp = fopen ( "$conf_postfix_vmailbox_path", "w");
	fwrite($fp, $vmailboxes_file);
	fclose($fp);

	$fp = fopen ( "$conf_postfix_virtual_uid_mapping_path", "w");
	fwrite($fp, $uid_mappings_file);
	fclose($fp);

	$fp = fopen ( "$conf_postfix_relay_domains_path", "w");
	fwrite($fp, $relay_domains_file);
	fclose($fp);


	//now that we have our base files, go and rebuild the db's
	system("/usr/sbin/postmap $conf_postfix_virtual_mailbox_domains_path");
	system("/usr/sbin/postmap $conf_postfix_virtual_path");
	system("/usr/sbin/postmap $conf_postfix_vmailbox_path");
	system("/usr/sbin/postmap $conf_postfix_virtual_uid_mapping_path");
}

?>
