<?php
#include "$dtcshared_path/cyradm.php";
#include "$dtcshared_path/cyrus.php";

require("$dtcshared_path/inc/sql/email_strings.php");

///////////////////////////////////////////////
// Email account submition to mysql database //
///////////////////////////////////////////////
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "set_catchall_account"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if($_REQUEST["catchall_popup"] == "no-mail-account"){
		$q = "UPDATE $pro_mysql_domain_table SET catchall_email='' WHERE name='$edit_domain';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said :".mysql_error());
	}else{
		if(!isMailbox($_REQUEST["catchall_popup"])){
			$submit_err .= $txt_err_email_format[$lang];
			$commit_flag = "no";
		}else{
			// Check if mail exists...
			if($_REQUEST["catchall_popup"] != "no-mail-account"){
				$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["catchall_popup"]."' AND mbox_host='$edit_domain'";
				$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
				$testnum_rows = mysql_num_rows($test_result);
				if($testnum_rows != 1){
					$submit_err .= $txt_err_mailbox_does_not_exists_in_db[$lang];
					$commit_flag = "no";
				}else{
					$catch = $_REQUEST["catchall_popup"];
					writeCatchallDotQmailFile($catch,$edit_domain);
				}
			}else{
				$catch = "";
			}
		}	
		if($commit_flag == "yes"){
			$q = "UPDATE $pro_mysql_domain_table SET catchall_email='".$_REQUEST["catchall_popup"]."' WHERE name='$edit_domain';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			triggerMXListUpdate();
			updateUsingCron("qmail_newu='yes',gen_qmail='yes'");
		}
	}
}
//$edit_domain $newmail_login $newmail_redirect1 $newmail_pass $newmail_redirect2 $newmail_deliver_localy
if(isset($_REQUEST["addnewmailtodomain"]) && $_REQUEST["addnewmailtodomain"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if(!isMailbox($_REQUEST["newmail_login"])){
		$submit_err .= $txt_err_email_format[$lang];
		$commit_flag = "no";
	}else{
		// Check if mail exists...
		$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["newmail_login"]."' AND mbox_host='$edit_domain'";
		$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
		$testnum_rows = mysql_num_rows($test_result);
		if($testnum_rows != 0){
			$submit_err .= $txt_err_mailbox_does_not_exists_in_db[$lang];
			$commit_flag = "no";
		}

	        //Check if list exists...
	        $test_query = "SELECT * FROM $pro_mysql_list_table
	        	WHERE name='".$_REQUEST["newmail_login"]."' AND domain='$edit_domain'";
		$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
		$testnum_rows = mysql_num_rows($test_result);
		if($testnum_rows != 0){
			$submit_err .= $txt_err_email_exists_as_mailinglist[$lang];
			$commit_flag = "no";
		}
	}

	if(!isDTCPassword($_REQUEST["newmail_pass"]) && ( ( isset($_REQUEST["newmail_deliver_localy"]) && $_REQUEST["newmail_deliver_localy"] == "yes") || (isset($_REQUEST["editmail_deliver_localy"]) && $_REQUEST["editmail_deliver_localy"] == "yes" ) ) ){
		$submit_err .= $txt_err_password_format[$lang];
		$commit_flag = "no";
		die($submit_err);
	}
	// if there is redirection, check for it's format
	if($_REQUEST["newmail_redirect1"] != ""){
		if(!isValidEmail($_REQUEST["newmail_redirect1"])){
			$submit_err .= $txt_mailsql_incorrect_redirection1_thisis_not_a_correct_email_format[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}
	if($_REQUEST["newmail_redirect2"] != ""){
		if(!isValidEmail($_REQUEST["newmail_redirect2"])){
			$submit_err .= $txt_mailsql_incorrect_redirection2_thisis_not_a_correct_email_format[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}

	// Submit to the sql dtabase
	if(isset($_REQUEST["newmail_deliver_localy"]) && $_REQUEST["newmail_deliver_localy"] == "yes"){
		$dolocal_deliver = "yes";
	}else{
		$dolocal_deliver = "no";
	}
	if(isset($_REQUEST["newmail_spam_mailbox_enable"]) && $_REQUEST["newmail_spam_mailbox_enable"] == "yes"){
		$do_spam_mailbox_enable = "yes";
	}else{
		$do_spam_mailbox_enable = "no";
	}
	$crypted_pass = crypt($_REQUEST["newmail_pass"]);
	if($commit_flag == "yes"){
		$mailbox_path = get_mailbox_complete_path($_REQUEST["newmail_login"],$edit_domain);
		$adm_query = "INSERT INTO $pro_mysql_pop_table(
        id,              fullemail, home,           mbox_host,     crypt,        passwd,         redirect1,            redirect2            ,localdeliver, spam_mailbox_enable, spam_mailbox)
VALUES ('".$_REQUEST["newmail_login"]."','".$_REQUEST["newmail_login"]."@".$edit_domain."','$mailbox_path','$edit_domain','$crypted_pass','".$_REQUEST["newmail_pass"]."','".$_REQUEST["newmail_redirect1"]."','".$_REQUEST["newmail_redirect2"]."','$dolocal_deliver','$do_spam_mailbox_enable','".$_REQUEST["newmail_spam_mailbox"]."');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

		writeDotQmailFile($_REQUEST["newmail_login"],$edit_domain);
		if ($cyrus_used)
		{
			# login to cyradm
			$cyr_conn = new cyradm;
			$error=$cyr_conn -> imap_login();
			if ($error!=0){
				die ("imap_login Error $error");
			}
			$result=$cyr_conn->createmb("user/" . $_REQUEST["newmail_login"]."@".$edit_domain);
/*	this doesn't seem to work so lets just forget it for the moment
				if (!$result){
				echo "error creating mailbox user/" . $_REQUEST["newmail_login"]."@".$edit_domain;
			}
*/
			# create spam mailbox
			$result=$cyr_conn->createmb("user/" . $_REQUEST["newmail_login"]."/".$_REQUEST["newmail_spam_mailbox"]."@".$edit_domain);
			/*
			if (!$result){
				echo "error creating mailbox user/" . $_REQUEST["newmail_login"]."@".$edit_domain;
			}
			*/
			# set ACL so that admin can remove mailbox again
			$result = $cyr_conn->setacl("user/" . $_REQUEST["newmail_login"]."@".$edit_domain, $CYRUS['ADMIN'], "lrswipcda");
			/*
			if (!$result)
			{
				echo "could not set ACL for user/" . $_REQUEST["newmail_login"]."@".$edit_domain;
			}
			*/
			# CL ToDo change this ###
			if (!$_REQUEST["cyrus_quota"])
			{ die ("invalid quota"); }
			$quota=$_REQUEST["cyrus_quota"];
			$result = $cyr_conn->setmbquota("user/" . $_REQUEST["newmail_login"]."@".$edit_domain, $quota);
		}
		triggerMXListUpdate();
		updateUsingCron("qmail_newu='yes',gen_qmail='yes'");
	}
}

/////////////////////////////////////////////////////////
// $edit_domain $edit_mailbox $editmail_pass $editmail_redirect1 $editmail_redirect2 $editmail_deliver_localy
/////////////////////////////////////////////////////////
if(isset($_REQUEST["modifymailboxdata"]) && $_REQUEST["modifymailboxdata"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if(!isMailbox($_REQUEST["edit_mailbox"])){
		$submit_err .= $_REQUEST["edit_mailbox"].$txt_mailsql_does_not_look_like_a_mailbox_login[$lang];
		$commit_flag = "no";
	}

	// Fetch the path of the mailbox
	$test_query = "SELECT id FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["edit_mailbox"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 1){
		$submit_err .= $txt_mailsql_mailbox_does_not_exists_in_db[$lang];
		$commit_flag = "no";
	}

	// Check for strings validity
	if(!isDTCPassword($_REQUEST["editmail_pass"])){
		$submit_err .= $txt_mailsql_pass_are_made_only_with_std_chars_and_nums_and_should_be_6to16_long[$lang];
		$commit_flag = "no";
	}
	if($_REQUEST["editmail_redirect1"] != ""){
		if(!isValidEmail($_REQUEST["editmail_redirect1"])){
			$submit_err .= $txt_mailsql_incorrect_redirection1_thisis_not_a_correct_email_format[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}
	if($_REQUEST["editmail_redirect2"] != ""){
		if(!isValidEmail($_REQUEST["editmail_redirect2"])){
			$submit_err .= $txt_mailsql_incorrect_redirection2_thisis_not_a_correct_email_format[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}

	// Submit to sql database
	if(isset($_REQUEST["editmail_deliver_localy"]) && $_REQUEST["editmail_deliver_localy"] == "yes"){
		$dolocal_deliver = "yes";
	}else{
		$dolocal_deliver = "no";
	}
	if(isset($_REQUEST["editmail_spam_mailbox_enable"]) && $_REQUEST["editmail_spam_mailbox_enable"] == "yes"){
		$do_spam_mailbox_enable = "yes";
	}else{
		$do_spam_mailbox_enable = "no";
	}

	$admin_path = getAdminPath($adm_login);
	$box_path = "$admin_path/$edit_domain/Mailboxs/".$_REQUEST["edit_mailbox"];
	if(isset($_REQUEST["editmail_vacation_flag"]) && $_REQUEST["editmail_vacation_flag"] == "yes"){
		$vacflag="yes";
	}else{
		$vacflag="no";
		if(file_exists("$box_path/vacation.lst"))
		unlink("$box_path/vacation.lst");
	}

	$crypted_pass = crypt($_REQUEST["editmail_pass"]);
	if($commit_flag == "yes"){
		$adm_query = "UPDATE $pro_mysql_pop_table
	SET crypt='$crypted_pass',passwd='".$_REQUEST["editmail_pass"]."',redirect1='".$_REQUEST["editmail_redirect1"]."',redirect2='".$_REQUEST["editmail_redirect2"]."',localdeliver='$dolocal_deliver',spam_mailbox_enable='$do_spam_mailbox_enable',spam_mailbox='".$_REQUEST["editmail_spam_mailbox"]."',
	vacation_flag='$vacflag',vacation_text='".addslashes($_REQUEST["editmail_vacation_text"])."'
	WHERE id='".$_REQUEST["edit_mailbox"]."' AND mbox_host='$edit_domain' LIMIT 1;";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

		if ($cyrus_used){
			# login to cyradm
			$cyr_conn = new cyradm;
			$error=$cyr_conn -> imap_login();
			if ($error!=0){
				die ("imap_login Error $error");
			}
			if (!$_REQUEST["cyrus_quota"])
			{ die ("invalid quota"); }
			$quota=$_REQUEST["cyrus_quota"];
			$result = $cyr_conn->setmbquota("user/" . $_REQUEST["edit_mailbox"]."@".$edit_domain, $quota);
		}

		writeDotQmailFile($_REQUEST["edit_mailbox"],$edit_domain);
		triggerMXListUpdate();
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
		$submit_err .= $_REQUEST["edit_mailbox"].$txt_mailsql_does_not_look_like_a_mailbox_login[$lang];
		$commit_flag = "no";
	}

	// Submit to sql database
	if($commit_flag == "yes"){
		$adm_query="DELETE FROM $pro_mysql_pop_table WHERE mbox_host='$edit_domain' AND id='".$_REQUEST["edit_mailbox"]."' LIMIT 1";
		unset($edit_mailbox);
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
		triggerMXListUpdate();
		updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	}
	if ($cyrus_used)
	{
		$cyr_conn = new cyradm;
		$error = $cyr_conn->imap_login();
		if ($error != 0){
			die ("Error: " . $error);
		}
		$cyr_conn->deletemb("user/".$_REQUEST["edit_mailbox"]."@".$edit_domain);
	}
}

?>
