<?php

/////////////////////////////////////////////////////////////
// Submit a new DNS and MX config for a domain to database //
/////////////////////////////////////////////////////////////
// adm_login=test&adm_pass=test&edit_domain=toto.com&addrlink=test.com&new_dns_1=default&new_dns_2=t0x.aegis-corp.org&new_dns_3=ns1.namebay.com&new_dns_4=ns2.namebay.com&new_dns_5=&new_mx_1=default&new_mx_2=mx1.anotherlight.com&new_mx_3=mx1.namebay.com&new_mx_4=mx2.namebay.com&new_mx_5=&new_dns_and_mx_config=Ok
if($_REQUEST["new_dns_and_mx_config"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	$new_dns_1 = $_REQUEST["new_dns_1"];
	$new_dns_2 = $_REQUEST["new_dns_2"];
	$new_dns_3 = $_REQUEST["new_dns_3"];
	$new_dns_4 = $_REQUEST["new_dns_4"];
	$new_dns_5 = $_REQUEST["new_dns_5"];
	$new_dns_6 = $_REQUEST["new_dns_6"];

	$new_mx_1 = $_REQUEST["new_mx_1"];
	$new_mx_2 = $_REQUEST["new_mx_2"];
	$new_mx_3 = $_REQUEST["new_mx_3"];
	$new_mx_4 = $_REQUEST["new_mx_4"];
	$new_mx_5 = $_REQUEST["new_mx_5"];
	$new_mx_6 = $_REQUEST["new_mx_6"];

	// Verify input validity
	if(!isHostnameOrIP($new_dns_1))	$new_dns_1 = "default";
	if(!isHostnameOrIP($new_dns_2))	$new_dns_2 = "default";
	if(!isHostnameOrIP($new_dns_3))	$new_dns_3 = "";
	if(!isHostnameOrIP($new_dns_4))	$new_dns_4 = "";
	if(!isHostnameOrIP($new_dns_5))	$new_dns_5 = "";
	if(!isHostnameOrIP($new_dns_6))	$new_dns_6 = "";

	if(!isHostnameOrIP($new_mx_1))	$new_mx_1 = "default";
	if(!isHostnameOrIP($new_mx_2))	$new_mx_2 = "";
	if(!isHostnameOrIP($new_mx_3))	$new_mx_3 = "";
	if(!isHostnameOrIP($new_mx_4))	$new_mx_4 = "";
	if(!isHostnameOrIP($new_mx_5))	$new_mx_5 = "";
	if(!isHostnameOrIP($new_mx_6))	$new_mx_6 = "";

	if($new_dns_2 != "default" && isset($new_dns_2) && $new_dns_2 != ""){
		if(isset($new_dns_3) && $new_dns_3 != ""){
			$new_dns_2 .= "|".$new_dns_3;
		}
		if(isset($new_dns_4) && $new_dns_4 != ""){
			$new_dns_2 .= "|".$new_dns_4;
		}
		if(isset($new_dns_5) && $new_dns_5 != ""){
			$new_dns_2 .= "|".$new_dns_5;
		}
		if(isset($new_dns_6) && $new_dns_6 != ""){
			$new_dns_2 .= "|".$new_dns_6;
		}
	}
	if($new_mx_2 != "default" && isset($new_mx_2) && $new_mx_2 != ""){
		if(isset($new_mx_3) && $new_mx_3 != ""){
			$new_mx_2 .= "|".$new_mx_3;
		}
		if(isset($new_mx_4) && $new_mx_4 != ""){
			$new_mx_2 .= "|".$new_mx_4;
		}
		if(isset($new_mx_5) && $new_mx_5 != ""){
			$new_mx_2 .= "|".$new_mx_5;
		}
		if(isset($new_mx_6) && $new_mx_6 != ""){
			$new_mx_2 .= "|".$new_mx_6;
		}
	}
	if($new_mx_1 == "")	$new_mx_1 = "default";
	if($new_mx_2 == "")	$new_mx_2 = "default";

	// If domain whois is hosted here, change the whois value using a registry call.
	if(file_exists($dtcshared_path."/dtcrm")){
		$query = "SELECT * FROM $pro_mysql_domain_table WHERE name='$edit_domain';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
		$row = mysql_fetch_array($result);
		if($row["whois"] == "here"){
			$regz = registry_update_whois_dns($adm_login,$adm_pass,$edit_domain,"$new_dns_1|$new_dns_2");
			if($regz["is_success"] != 1){
				die("<font color=\"red\"><b>Whois update failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i>");
			}
		}
	}

	$query = "UPDATE $pro_mysql_domain_table SET primary_dns='$new_dns_1',other_dns='$new_dns_2',primary_mx='$new_mx_1',other_mx='$new_mx_2' WHERE owner='$adm_login' AND name='$edit_domain';";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());

	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
}

///////////////////////////
// MySQL password change //
///////////////////////////
if($_REQUEST["change_mysql_password"] == "Ok"){
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("User or password is incorrect !");

	if(!isDTCPassword($_REQUEST["new_mysql_password"])){
		die("Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.");
	}

	mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
	$query = "UPDATE user SET Password=PASSWORD('".$_REQUEST["new_mysql_password"]."') WHERE User='$adm_login';";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	mysql_query("FLUSH PRIVILEGES");
	mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\" !!!");
}

///////////////////////////////////////////////
// Email account submition to mysql database //
///////////////////////////////////////////////
//$edit_domain $newmail_login $newmail_redirect1 $newmail_pass $newmail_redirect2 $newmail_deliver_localy
if($_REQUEST["addnewmailtodomain"] == "Ok"){
	// Check if mail exists...
	$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["newmail_login"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Mailbox allready exist in database !");
	}

	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// We have now to get the user directory and use it ! :)
	$admin_path = getAdminPath($adm_login);
	$mailbox_path = "$admin_path/$edit_domain/Mailboxs/".$_REQUEST["newmail_login"];

	// Check for strings validity ($newmail_deliver_localy does not need to be tested because of lately test...)
	if(!isMailbox($_REQUEST["newmail_login"])){
		die("Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.");
	}
	if(!isDTCPassword($_REQUEST["newmail_pass"])){
		die("Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.");
	}
	// if there is redirection, check for it's format
	if($_REQUEST["newmail_redirect1"] != ""){
		if(!isValidEmail($_REQUEST["newmail_redirect1"])){
			die("Incorect redirection 1");
		}
	}
	if($_REQUEST["newmail_redirect2"] != ""){
		if(!isValidEmail($_REQUEST["newmail_redirect2"])){
			die("Incorect redirection 2");
		}
	}

	// Create mail directory
	//$oldumask = umask(1777);
	if($conf_demo_version == "no"){
		if(!file_exists("$mailbox_path") && $conf_demo_version == "no"){
			mkdir("$mailbox_path", 0775);
		}
	}

	// Write the .qmail file
	if($_REQUEST["newmail_deliver_localy"] == "yes" && $conf_demo_version == "no"){
		// Create mailbox direcotry if does not exists
		mk_Maildir($mailbox_path);
		$qmail_file_content = "./Maildir/\n";
	}
	if($_REQUEST["newmail_redirect1"] != "" && isset($_REQUEST["newmail_redirect1"]) ){
		$qmail_file_content .= '&'.$_REQUEST["newmail_redirect1"]."\n";
	}
	if($_REQUEST["newmail_redirect2"] != "" && isset($_REQUEST["newmail_redirect2"]) ){
		$qmail_file_content .= '&'.$_REQUEST["newmail_redirect2"]."\n";
	}
	if($conf_demo_version == "no"){
		$fp = fopen ( "$mailbox_path/.qmail", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
	}
	//umask($oldumask);

	// Submit to the sql dtabase
	if($_REQUEST["newmail_deliver_localy"] == "yes"){
		$dolocal_deliver = "yes";
	}else{
		$dolocal_deliver = "no";
	}
	$crypted_pass = crypt($_REQUEST["newmail_pass"]);
	$adm_query = "INSERT INTO $pro_mysql_pop_table(
        id,              home,           mbox_host,     crypt,        passwd,         redirect1,            redirect2            ,localdeliver)
VALUES ('".$_REQUEST["newmail_login"]."','$mailbox_path','$edit_domain','$crypted_pass','".$_REQUEST["newmail_pass"]."','".$_REQUEST["newmail_redirect1"]."','".$_REQUEST["newmail_redirect2"]."','$dolocal_deliver');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("qmail_newu='yes',gen_qmail='yes'");
}

// $edit_domain $edit_mailbox $editmail_pass $editmail_redirect1 $editmail_redirect2 $editmail_deliver_localy
if($_REQUEST["modifymailboxdata"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if(!isMailbox($_REQUEST["edit_mailbox"])){
		die($_REQUEST["edit_mailbox"]." does not look like a mailbox login...");
	}

	// Fetch the path of the mailbox
	$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["edit_mailbox"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 1){
		die("Mailbox does not exist in database !");
	}
	$mysqlmailbox = mysql_fetch_array($test_result) or die ("Cannot fetch user-admin");
	$editmail_boxpath = $mysqlmailbox["home"];

	// Check for strings validity
	if(!isDTCPassword($_REQUEST["editmail_pass"])){
		die("Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.");
	}
	if($_REQUEST["editmail_redirect1"] != ""){
		if(!isValidEmail($_REQUEST["editmail_redirect1"])){
			die("Incorect redirection 1");
		}
	}
	if($_REQUEST["editmail_redirect2"] != ""){
		if(!isValidEmail($_REQUEST["editmail_redirect2"])){
			die("Incorect redirection 2");
		}
	}

	// Write .qmail file
	$oldumask = umask(0);
	if($_REQUEST["editmail_deliver_localy"] == "yes" && $conf_demo_version == "no"){
		// Create mailbox direcotry if does not exist
		mk_Maildir($editmail_boxpath);
		$qmail_file_content = "./Maildir/\n";
	}
	if($_REQUEST["editmail_redirect1"] != "" && isset($_REQUEST["editmail_redirect1"]) ){
		$qmail_file_content .= '&'.$_REQUEST["editmail_redirect1"]."\n";
	}
	if($_REQUEST["newmail_redirect2"] != "" && isset($_REQUEST["editmail_redirect2"]) ){
		$qmail_file_content .= '&'.$_REQUEST["editmail_redirect2"]."\n";
	}

	if($conf_demo_version == "no"){
		$fp = fopen ( "$editmail_boxpath/.qmail", "w");
		fwrite ($fp,$qmail_file_content);
		fclose($fp);
	}
	umask($oldumask);

	// Submit to sql database
	if($_REQUEST["editmail_deliver_localy"] == "yes"){
		$dolocal_deliver = "yes";
	}else{
		$dolocal_deliver = "no";
	}
	$crypted_pass = crypt($_REQUEST["editmail_pass"]);
	$adm_query = "UPDATE $pro_mysql_pop_table SET
	crypt='$crypted_pass',passwd='".$_REQUEST["editmail_pass"]."',redirect1='".$_REQUEST["editmail_redirect1"]."',redirect2='".$_REQUEST["editmail_redirect2"]."',localdeliver='$dolocal_deliver' WHERE
	id='".$_REQUEST["edit_mailbox"]."' AND mbox_host='$edit_domain' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
}

// $edit_domain $editmail_login
if($_REQUEST["delemailaccount"] == "Del"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// Verify strings given
	if(!isMailbox($_REQUEST["edit_mailbox"])){
		die($_REQUEST["edit_mailbox"]." does not look like a mailbox login...");
	}

	// Submit to sql database
	$adm_query="DELETE FROM $pro_mysql_pop_table WHERE mbox_host='$edit_domain' AND id='".$_REQUEST["edit_mailbox"]."' LIMIT 1";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
}

////////////////////////////
// Sub-domains management //
////////////////////////////
if($_REQUEST["delsubdomain"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	if(!checkSubdomainFormat($_REQUEST["delsubdomain_name"])){
		die("Incorrect subdomain name format...");
	}
	// Del subdomain in database
	$adm_query = "DELETE FROM $pro_mysql_subdomain_table WHERE subdomain_name='".$_REQUEST["delsubdomain_name"]."' AND domain_name='$edit_domain' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
}

if($_REQUEST["subdomaindefault"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	if(!checkSubdomainFormat($_REQUEST["subdomaindefault_name"])){
		die("Incorrect subdomain name format...");
	}	$adm_query = "UPDATE $pro_mysql_domain_table SET default_subdomain='".$_REQUEST["subdomaindefault_name"]."' WHERE name='$edit_domain' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes'");
}
// addrlink=example.com%2Fsubdomains&edit_domain=example.com&whatdoiedit=subdomains&subdomaindefault_name=www&delsubdomain_name=dtc&
if($_REQUEST["edit_one_subdomain"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	if(!checkSubdomainFormat($_REQUEST["subdomain_name"])){
		die("Incorrect subdomain name format...");
	}
	// Verify it's an valid IP
	if(!isIP($newsubdomain_ip)){
		$newsubdomain_ip = "default";
	}
// =yes&webalizer=yes&w3_alias=yes
	if($_REQUEST["register_globals"] == "yes")	$reg_globs = ", register_globals='yes'";
	else		$reg_globs = ", register_globals='no'";
	if($_REQUEST["webalizer"] == "yes")	$webalizer = ", webalizer_generate='yes'";
	else		$webalizer = ", webalizer_generate='no'";
	if($_REQUEST["w3_alias"] == "yes")	$w3alias = ", w3_alias='yes'";
	else		$w3alias = ", w3_alias='no'";
	$add_vals .= $reg_globs.$webalizer.$w3alias;
	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	if(isFtpLogin($_REQUEST["subdomain_dynlogin"]) && isDTCPassword($_REQUEST["subdomain_dynpass"])){
		$add_vals .= ", login='".$_REQUEST["subdomain_dynlogin"]."', pass='".$_REQUEST["subdomain_dynpass"]."'";
	}else{
		$add_vals .= ", login=NULL, pass=NULL ";
	}
	$domupdate_query = "UPDATE $pro_mysql_subdomain_table SET ip='".$_REQUEST["newsubdomain_ip"]."'$add_vals WHERE domain_name='$edit_domain' AND subdomain_name='".$_REQUEST["subdomain_name"]."' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named='yes'");
}

if($_REQUEST["newsubdomain"] == "Ok"){

	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// This can be added : it's a mater of the admin's choice...
//	if($_REQUEST["newsubdomain_name"] == "pop" || $_REQUEST["newsubdomain"] == "smtp" || $_REQUEST["newsubdomain_name"] == "ftp"){
//		die("You cannot add \"pop\", \"smtp\" or \"ftp\" as subdomain names, because they are reserved for the corresponding services.");
//	}

	// Verify string validity
	if(!checkSubdomainFormat($_REQUEST["newsubdomain_name"])){
		die("Incorect subdomain name format...");
	}

	// We have now to get the user directory and use it ! :)
	$query = "SELECT path FROM $pro_mysql_admin_table WHERE adm_login='$adm_login'";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$testnum_rows = mysql_num_rows($result);
	if($testnum_rows != 1){
		die("Cannot fetch user !!!");
	}
	$row = mysql_fetch_array($result);
	$admin_path = $row["path"];

	// Make the directorys
	$newsubdomain_dirpath = "$admin_path/$edit_domain/subdomains/".$_REQUEST["newsubdomain_name"];
	if($conf_demo_version == "no"){
		if(!file_exists("$newsubdomain_dirpath"))
			mkdir("$newsubdomain_dirpath", 0750);
		if(!file_exists("$newsubdomain_dirpath/html"))
			mkdir("$newsubdomain_dirpath/html", 0750);
		if(!file_exists("$newsubdomain_dirpath/cgi-bin"))
			mkdir("$newsubdomain_dirpath/cgi-bin", 0750);
		if(!file_exists("$newsubdomain_dirpath/logs"))
			mkdir("$newsubdomain_dirpath/logs", 0750);
	}
	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	// Verify it's an valid IP
	if(!isIP($_REQUEST["newsubdomain_ip"])){
		$newsubdomain_ip = "default";
	}else{
		$newsubdomain_ip = $_REQUEST["newsubdomain_ip"];
	}

	if(isFtpLogin($_REQUEST["newsubdomain_dynlogin"]) && isDTCPassword($_REQUEST["newsubdomain_dynpass"])){
		$add_field = ",login,pass";
		$add_values = ",'".$_REQUEST["newsubdomain_dynlogin"]."','".$_REQUEST["newsubdomain_dynpass"]."'";
	}else{
		$add_field = "";
		$add_values = "";
	}
	$adm_query = "INSERT INTO $pro_mysql_subdomain_table (id,domain_name,subdomain_name,ip".$add_field.") VALUES ('','$edit_domain','".$_REQUEST["newsubdomain_name"]."','$newsubdomain_ip'".$add_values.");";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

        // Create the new site html front page
        if($conf_demo_version == "no"){
                system ("cp -rf $conf_dtcshared_path/template/* $admin_path/$edit_domain/subdomains/".$_REQUEST["newsubdomain_name"]."/html");
        }

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
}

/////////////////////////////
// Ftp accounts management //
/////////////////////////////
if($_REQUEST["newftpaccount"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_path = getAdminPath($adm_login);

	if(!ereg("^$adm_path",$_REQUEST["newftp_path"]) || strstr($_REQUEST["newftp_path"],'..')){
		die("Your path is restricted to $adm_path");
	}

	if(!isFtpLogin($_REQUEST["newftp_login"])){
		die("Incorrect FTP login");
	}

	if(!isDTCPassword($_REQUEST["newftp_pass"])){
		die("Incorrect FTP password: from 6 to 16 chars, a-z A-Z 0-9");
	}

	$_REQUEST["newftp_path"] = addslashes($_REQUEST["newftp_path"]);

	$adm_query = " INSERT INTO $pro_mysql_ftp_table
(login,           password, homedir, count, fhost, faddr, ftime, fcdir, fstor, fretr, bstor, bretr, creation, ts, frate, fcred, brate, bcred, flogs, size, shell, hostname)VALUES
('".$_REQUEST["newftp_login"]."', '".$_REQUEST["newftp_pass"]."', '".$_REQUEST["newftp_path"]."', 'NUL', NULL, NULL, NOW(NULL), NULL, 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', NULL, '5',
'15', '5','1', NULL, '', '/bin/bash', '$edit_domain') ";
	// $newftp_login $newftp_pass $edit_domain
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
}

// $edftp_account $edit_domain
if($_REQUEST["deleteftpaccount"] == "Delete"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_query = "DELETE FROM $pro_mysql_ftp_table WHERE hostname='$edit_domain' AND login='".$_REQUEST["edftp_account"]."' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
}

// $edftp_account $edit_domain $edftp_pass
if($_REQUEST["update_ftp_account"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_path = getAdminPath($adm_login);

	if(0 != strncmp($adm_path,$_REQUEST["edftp_path"],strlen($adm_path)-1) || strstr($_REQUEST["edftp_path"],'..') || strstr($_REQUEST["edftp_path"],"'") || strstr($_REQUEST["edftp_path"],"\\")){
		die("Your path is restricted to &quot;$adm_path&quot;");
	}

	if(!isFtpLogin($_REQUEST["edftp_account"])){
		die("Incorrect FTP login");
	}

	if(!isDTCPassword($_REQUEST["edftp_pass"])){
		die("Incorrect FTP password: from 6 to 16 chars, a-z A-Z 0-9");
	}
	$_REQUEST["edftp_path"] = addslashes($_REQUEST["edftp_path"]);

	$adm_query = "UPDATE $pro_mysql_ftp_table SET homedir='".$_REQUEST["edftp_path"]."', password='".$_REQUEST["edftp_pass"]."' WHERE login ='".$_REQUEST["edftp_account"]."' AND hostname='$edit_domain' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
}

?>
