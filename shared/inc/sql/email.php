<?php

///////////////////////////////////////////////
// Email account submition to mysql database //
///////////////////////////////////////////////
//$edit_domain $newmail_login $newmail_redirect1 $newmail_pass $newmail_redirect2 $newmail_deliver_localy
if(isset($_REQUEST["addnewmailtodomain"]) && $_REQUEST["addnewmailtodomain"] == "Ok"){
	// Check if mail exists...
	$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["newmail_login"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		$submit_err .= "Mailbox allready exist in database !<br>\n";
		$commit_flag = "no";
	}

	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// Check for strings validity ($newmail_deliver_localy does not need to be tested because of lately test...)
	//allow * for catch-all redirects
	if(!isMailbox($_REQUEST["newmail_login"]) && $_REQUEST["newmail_login"] != "*"){
		$submit_err .= "Incorect mail login format: it should be made only with lowercase letters or numbers or the \"-\" sign.<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["newmail_pass"]) && $newmail_deliver_localy == "no"){
		$submit_err .= "Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n";
		$commit_flag = "no";
		die("");
	}
	// if there is redirection, check for it's format
	if($_REQUEST["newmail_redirect1"] != ""){
		if(!isValidEmail($_REQUEST["newmail_redirect1"])){
			$submit_err .= "Incorect redirection 1: this is not a correct emailbox format.<br>\n";
			$commit_flag = "no";
		}
	}
	if($_REQUEST["newmail_redirect2"] != ""){
		if(!isValidEmail($_REQUEST["newmail_redirect2"])){
			$submit_err .= "Incorect redirection 2: this is not a correct emailbox format.<br>\n";
			$commit_flag = "no";
		}
	}
	// Submit to the sql dtabase
	if($_REQUEST["newmail_deliver_localy"] == "yes"){
		$dolocal_deliver = "yes";
	}else{
		$dolocal_deliver = "no";
	}
	$crypted_pass = crypt($_REQUEST["newmail_pass"]);
	$mailbox_path = get_mailbox_complete_path($_REQUEST["newmail_login"],$edit_domain);
	if($commit_flag == "yes"){
		$adm_query = "INSERT INTO $pro_mysql_pop_table(
        id,              fullemail, home,           mbox_host,     crypt,        passwd,         redirect1,            redirect2            ,localdeliver)
VALUES ('".$_REQUEST["newmail_login"]."','".$_REQUEST["newmail_login"]."@".$edit_domain."','$mailbox_path','$edit_domain','$crypted_pass','".$_REQUEST["newmail_pass"]."','".$_REQUEST["newmail_redirect1"]."','".$_REQUEST["newmail_redirect2"]."','$dolocal_deliver');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

		writeDotQmailFile($_REQUEST["newmail_login"],$edit_domain);
		updateUsingCron("qmail_newu='yes',gen_qmail='yes'");
	}
}

/////////////////////////////////////////////////////////
// $edit_domain $edit_mailbox $editmail_pass $editmail_redirect1 $editmail_redirect2 $editmail_deliver_localy
/////////////////////////////////////////////////////////
if(isset($_REQUEST["modifymailboxdata"]) && $_REQUEST["modifymailboxdata"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if(!isMailbox($_REQUEST["edit_mailbox"])){
		$submit_err .= $_REQUEST["edit_mailbox"]." does not look like a mailbox login...";
		$commit_flag = "no";
	}

	// Fetch the path of the mailbox
	$test_query = "SELECT id FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["edit_mailbox"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 1){
		$submit_err .= "Mailbox does not exist in database !";
		$commit_flag = "no";
	}

	// Check for strings validity
	if(!isDTCPassword($_REQUEST["editmail_pass"])){
		$submit_err .= "Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.";
		$commit_flag = "no";
	}
	if($_REQUEST["editmail_redirect1"] != ""){
		if(!isValidEmail($_REQUEST["editmail_redirect1"])){
			$submit_err .= "Incorect redirection 1: this is not a correct emailbox format.<br>\n";
			$commit_flag = "no";
		}
	}
	if($_REQUEST["editmail_redirect2"] != ""){
		if(!isValidEmail($_REQUEST["editmail_redirect2"])){
			$submit_err .= "Incorect redirection 1: this is not a correct emailbox format.<br>\n";
			$commit_flag = "no";
		}
	}

	// Submit to sql database
	if($_REQUEST["editmail_deliver_localy"] == "yes"){
		$dolocal_deliver = "yes";
	}else{
		$dolocal_deliver = "no";
	}
	$crypted_pass = crypt($_REQUEST["editmail_pass"]);
	if($commit_flag == "yes"){
		$adm_query = "UPDATE $pro_mysql_pop_table SET
	crypt='$crypted_pass',passwd='".$_REQUEST["editmail_pass"]."',redirect1='".$_REQUEST["editmail_redirect1"]."',redirect2='".$_REQUEST["editmail_redirect2"]."',localdeliver='$dolocal_deliver' WHERE
	id='".$_REQUEST["edit_mailbox"]."' AND mbox_host='$edit_domain' LIMIT 1;";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

		writeDotQmailFile($_REQUEST["edit_mailbox"],$edit_domain);

		updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	}
}
//////////////////////////////////
// $edit_domain $editmail_login
//////////////////////////////////
if(isset($_REQUEST["delemailaccount"]) && $_REQUEST["delemailaccount"] == "Del"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// Verify strings given
	if(!isMailbox($_REQUEST["edit_mailbox"])){
		$submit_err .= $_REQUEST["edit_mailbox"]." does not look like a mailbox login...";
		$commit_flag = "no";
	}

	// Submit to sql database
	if($commit_flag == "yes"){
		$adm_query="DELETE FROM $pro_mysql_pop_table WHERE mbox_host='$edit_domain' AND id='".$_REQUEST["edit_mailbox"]."' LIMIT 1";
		unset($edit_mailbox);
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

		updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	}
}

?>