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
 * /usr/share/dtc/etc/postfix_relay_recipients
 * emailaddress@domain.com	OK
 * emailaddress2@domain.com	OK
 *
 * /usr/share/dtc/etc/postfix_aliases
 * mailinglist:		"|/usr/bin/mlmmj-recieve"
 *
 * /usr/share/dtc/etc/recipient_lists/$domain
 * ($conf_postfix_recipient_lists_path/$domain)
 * email@$domain	OK
 *
 **********************************************/

function mail_account_generate_postfix(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $console;

	global $conf_generated_file_path;
	global $conf_addr_mail_server;

	global $conf_unix_type;

	global $conf_nobody_user_id;

	//global $conf_postfix_virtual_mailbox_domains_path;
	//global $conf_postfix_virtual_path;
	//global $conf_postfix_vmailbox_path;
	//global $conf_postfix_virtual_uid_mapping_path;

	// prepend the configured path here

	$conf_postfix_virtual_mailbox_domains_path = $conf_generated_file_path . "/postfix_virtual_mailbox_domains";
	$conf_local_domains_path = $conf_generated_file_path . "/local_domains";
	$conf_postfix_virtual_path = $conf_generated_file_path . "/postfix_virtual";
	$conf_postfix_aliases_path = $conf_generated_file_path . "/postfix_aliases";
	$conf_postfix_vmailbox_path = $conf_generated_file_path . "/postfix_vmailbox";
	$conf_postfix_virtual_uid_mapping_path = $conf_generated_file_path . "/postfix_virtual_uid_mapping";

	$conf_postfix_relay_domains_path = $conf_generated_file_path . "/postfix_relay_domains";
	$conf_postfix_relay_recipients_path = $conf_generated_file_path . "/postfix_relay_recipients";
	$conf_postfix_recipient_lists_path = $conf_generated_file_path . "/recipientlists";

	// now for our variables to write out the db info to

	$domains_file = "";
	$local_domains_file = "";
	$domains_postmasters_file = "";
	$aliases_file = "";
	$vmailboxes_file = "";
	$uid_mappings_file = "";
	$relay_domains_file = "";
	$relay_recipients_file = "";
	//store ALL of the domains we know about
	//if we manage to get better information later, don't worry about the entry on this one
	$relay_recipients_all_domains = "";

	$data = ""; // init var for use later on

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

		//Path of user's mailing lists
		$admin_path = getAdminPath($user_admin_name);

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

			//$console .= "Processing $domain_full_name ...\n";
			//if we are primary mx, add to domains
			//else add to relay
			$primary_mx=0;
			if ($domain["primary_mx"] == "" || $domain["primary_mx"] == "default")
			{
				$primary_mx=1;
				$domains_file .= "$domain_full_name virtual\n";
				$local_domains_file .="$domain_full_name\n";
			} else {
				$relay_domains_file .= "$domain_full_name\n";
				$relay_recipients_all_domains .= "$domain_full_name\n";
			}

			$store_catch_all = "";
			//$store_catch_all_md = "";
			$catch_all_id = $domain["catchall_email"];
			$abuse_address = 0;
			$postmaster_address = 0;
			// This should handle domain parking without a lot of code! :)
			if($domain["domain_parking"] != "no-parking"){
				for($b=0;$b<$nbr_domain;$b++){
					if($data[$b]["name"] == $domain["domain_parking"]){
						$domain["emails"] = $data[$b]["emails"];
					}
				}
			}
			if(isset($domain["emails"]) && $primary_mx){
				$emails = $domain["emails"];
				$nbr_boites = sizeof($emails);
				// go through each of these emails and build the vmailbox file
				//also create our sasldb2 if we have a saslpasswd2 exe
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
					$passwd = crypt($passwdtemp);
					$spam_mailbox = $email["spam_mailbox"];
					$spam_mailbox_enable = $email["spam_mailbox_enable"];
					$vacation_flag = $email["vacation_flag"];
					$vacation_text = stripslashes($email["vacation_text"]);

					$spam_stuff_done = 0;

					system("/bin/mkdir -p $home");

					// if we have a $id equal to abuse
					if ($id == "abuse"){
						$abuse_address++;
					}
					if ($id == "postmaster"){
						$postmaster_address++; 
					}
					# first try and see if we have postfix in a chroot, else just put it in it's default location
					if ($localdeliver == "yes" || $localdeliver == "true"){
						# only generate sasl logins for local accounts
						system("./genfiles/gen_sasl.sh $domain_full_name $id $passwdtemp $conf_addr_mail_server");
						# setup the catch_all for locally delivered email addresses
						if ($id == $catch_all_id)
						{
							//$store_catch_all_md .= "@$domain_full_name        $home/Maildir/\n";
							$store_catch_all .= "@$domain_full_name	$id@$domain_full_name\n";
						} 
						$vmailboxes_file .= "$id@$domain_full_name $home/Maildir/\n";
						$uid_mappings_file .= "$id@$domain_full_name $uid\n";				
						if (isset($catch_all_id) || $catch_all_id != "")
						{
							//just so we can deliver to our vmailboxs if we have set a catch-all (otherwise postfix gets confused, and delivers all mail to the catch all)
							$domains_postmasters_file .= "$id@$domain_full_name $id@$domain_full_name\n";
						}
					}
					if(isset($redirect1) && $redirect1 != ""){
						unset($extra_redirects);
						if ($localdeliver == "yes" || $localdeliver == "true"){
							//need to generate .mailfilter file with "cc" and also local delivery
							system("./genfiles/gen_mailfilter.sh $home $id $domain_full_name $spam_mailbox_enable $spam_mailbox $vacation_flag $redirect1");
							if($vacation_flag == "yes"){
								$vac_fp = fopen("$home/.vacation.msg","w+");
								fwrite($vac_fp,$vacation_text);
								fclose($vac_fp);
							}
							$spam_stuff_done = 1;
						} else {
							$extra_redirects = " $redirect1 ";
						}
						if ($redirect2 != "" && isset($redirect2)){
							if ($localdeliver == "yes" || $localdeliver == "true"){
								//need to generate .mailfilter file with "cc" and also local delivery
								system("./genfiles/gen_mailfilter.sh $home $id $domain_full_name $spam_mailbox_enable $spam_mailbox $vacation_flag $redirect1 $redirect2");
								if($vacation_flag == "yes"){
									$vac_fp = fopen("$home/.vacation.msg","w+");
									fwrite($vac_fp,$vacation_text);
									fclose($vac_fp);
								}
								$spam_stuff_done = 1;
							} else if (isset($extra_redirects)) {
								$extra_redirects .= " , $redirect2";
							}
						}
						if ($store_catch_all == "" && ($id == "*" || $id == $catch_all_id)){
							if(isset($extra_redirects)){
								$store_catch_all .= "@$domain_full_name        $extra_redirects\n";
}	
						} else if (isset($extra_redirects)) {
							$domains_postmasters_file .= "$id@$domain_full_name	$extra_redirects\n";
						}
						unset($extra_redirects);
					} 
					//if we haven't added the spam mailbox yet, do it here
					if ($spam_stuff_done == 0)
					{
						system("./genfiles/gen_mailfilter.sh $home $id $domain_full_name $spam_mailbox_enable $spam_mailbox");
					}
				}
			}
			//add support for creation of mailing lists
			if(isset($domain["mailinglists"]) && $primary_mx)
			{
				$lists = $domain["mailinglists"];
				$nbr_boites = sizeof($lists);
				// go through each of these lists and add to virtual maps and normal aliases
				for($k=0;$k<$nbr_boites;$k++){
					$list = $lists[$k];
					$list_id = $list["id"];
					$list_name = $list["name"];
					if ($list_name == "abuse")
					{
						$abuse_address++;
					}
					else if ($list_name == "postmaster")
					{
						$postmaster_address++;
					}
					$list_owner = $list["owner"];
					$list_domain = $list["domain"];
			
					$list_path = "$admin_path/$list_domain/lists";
					$name = $list_domain . "_" . $list_name;
					if (!ereg("\@", $list_owner))
                                        {
						$owner = $list_owner . "@" . $list_domain;
                                        } else {
						$owner = $list_owner;
					}
					$domains_postmasters_file .= $list_name . "@" . $list_domain . " " . $name . "\n";
					$aliases_file .= $name.': "|/usr/bin/mlmmj-recieve -L '.$list_path.'/'.$name.'/"' . "\n";
				}
			}
			// if an abuse@ email hasn't been set, set one here to go to postmaster
			if ($abuse_address == 0 && $primary_mx)
			{
				$domains_postmasters_file .= "abuse@$domain_full_name postmaster\n";
			}
			if ($postmaster_address == 0 && $primary_mx){
				$domains_postmasters_file .= "postmaster@$domain_full_name postmaster\n";
			}

			//always store catch all last... :)
			if(isset($store_catch_all) && $store_catch_all != ""){
				$domains_postmasters_file .= $store_catch_all;
			}
			//now store the Maildir version
			if(isset($store_catch_all_md) && $store_catch_all_md != ""){
				$vmailboxes_file .= $store_catch_all_md;
			}
			
		}
	}


	//check to see if the domain is in our local recipients first before adding to allowed relay domains
	$relay_domains_file_temp_list = explode("\n", get_remote_mail_domains());
	foreach($relay_domains_file_temp_list as $domain)
	{
		if (isset($domain) && strlen($domain) > 0)
		{
			if (!preg_match("/^$domain\s/", $domains_file))
			{
				$relay_domains_file .= "$domain\n";
			}
		}
	}

	$relay_recipients_list = explode("\n", get_remote_mail_recipients());

	foreach($relay_recipients_list as $email)
	{
		if (isset($email) && strlen($email) > 0){
			// echo "Stage 1 - adding $email";
			$relay_recipients_file .= $email . " OK\n";
		}
	}

	// if we haven't added the following domains to the $relay_recipients_file, then we need to add a wildcard, bad, but necessary for domains we don't have email lists for
	$relay_recipients_all_domains_list  = explode("\n", $relay_recipients_all_domains);
	foreach($relay_recipients_all_domains_list as $domain)
	{
		// if the $domain isn't set here, keep going
		if (!(isset($domain) && strlen($domain) >0))
		{
			continue;
		}
		//$console .= "$domain is being backed up\n";
		//try and read a file here, and see if we have a list already created
		if (is_file("$conf_postfix_recipient_lists_path/$domain")){
			//$console .= "File found with domain info - $conf_postfix_recipient_lists_path/$domain\n";
			//check to see if we have already got this domain... 
			//if we do, then it means that we have a rogue $domain file, and it should be deleted! :)

			if (preg_match("/\@$domain\s+OK/", $relay_recipients_file))
			{
				unlink("$conf_postfix_recipient_lists_path/$domain");
			} else {
				// echo "Reading $domain from recip file...";
				$fp = fopen( "$conf_postfix_recipient_lists_path/$domain", "r");
				$contents = fread($fp, filesize("$conf_postfix_recipient_lists_path/$domain"));
				fclose($fp);
				//now we have found some domain email list, append it here
				$relay_recipients_file .= $contents;
				// echo "Stage 2 - adding $contents";
			}
		}
		//finally check to see if we haven't got any entries for this domain
		if (!preg_match("/\@$domain\s+OK/", $relay_recipients_file))
		{
			//$console .= "Faking domain entry for $domain...\n";
			$relay_recipients_file .= "@$domain OK\n";
			// echo "Stage 3 - adding $domain OK";
			//write this to a file, so admin/users can edit later
			if (!file_exists("$conf_postfix_recipient_lists_path"))
			{
				//make a directory here if it doesn't exist yet
				mkdir("$conf_postfix_recipient_lists_path");
			}
			$fp = fopen( "$conf_postfix_recipient_lists_path/$domain", "w");
			fwrite($fp, "@$domain OK\n");
			fclose($fp);	
		}
	}

	//write out our config files
	$fp = fopen ( "$conf_postfix_virtual_mailbox_domains_path", "w");
	fwrite($fp, $domains_file);
	fclose($fp);

	$fp = fopen ( "$conf_local_domains_path", "w");
	fwrite($fp, $local_domains_file);
	fclose($fp);

	$fp = fopen ( "$conf_postfix_virtual_path", "w");
	fwrite($fp, $domains_postmasters_file);
	fclose($fp);

	$fp = fopen ( "$conf_postfix_aliases_path", "w");
	fwrite($fp, $aliases_file);
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

	$fp = fopen ( "$conf_postfix_relay_recipients_path", "w");
	fwrite($fp, $relay_recipients_file);
	fclose($fp);


	//now that we have our base files, go and rebuild the db's
	if($conf_unix_type == "freebsd"){
		$POSTMAP_BIN = "/usr/local/sbin/postmap";
		$POSTALIAS_BIN = "/usr/local/sbin/postalias";
	}else{
		$POSTMAP_BIN = "/usr/sbin/postmap";
		$POSTALIAS_BIN = "/usr/sbin/postalias";
	}

	system("$POSTMAP_BIN $conf_postfix_virtual_mailbox_domains_path");
	system("$POSTMAP_BIN $conf_postfix_virtual_path");
	system("$POSTALIAS_BIN $conf_postfix_aliases_path");
	system("$POSTMAP_BIN $conf_postfix_vmailbox_path");
	system("$POSTMAP_BIN $conf_postfix_virtual_uid_mapping_path");
	system("$POSTMAP_BIN $conf_postfix_relay_recipients_path");

	//in case our relay_domains file hasn't been created correctly, we should touch it
	system("touch $conf_postfix_relay_domains_path");
}

?>
