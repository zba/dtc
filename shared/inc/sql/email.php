<?php

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
	//allow * for catch-all redirects
	if(!isMailbox($_REQUEST["newmail_login"]) && $_REQUEST["newmail_login"] != "*"){
		die("Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.");
	}
	if(!isDTCPassword($_REQUEST["newmail_pass"]) && $newmail_deliver_localy == "no"){
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

/////////////////////////////////////////////////////////
// $edit_domain $edit_mailbox $editmail_pass $editmail_redirect1 $editmail_redirect2 $editmail_deliver_localy
/////////////////////////////////////////////////////////
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
//////////////////////////////////
// $edit_domain $editmail_login
//////////////////////////////////
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

?>
