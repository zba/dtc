<?php

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
			$submit_err .= _("Incorect mail login format: it should consist of only lowercase letters, numbers, or the \"-\" sign.<br>\n") ;
			$commit_flag = "no";
		}else{
			// Check if mail exists...
			if($_REQUEST["catchall_popup"] != "no-mail-account"){
				$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["catchall_popup"]."' AND mbox_host='$edit_domain'";
				$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
				$testnum_rows = mysql_num_rows($test_result);
				if($testnum_rows != 1){
					$submit_err .= _("Mailbox does not exist in database.<br>\n") ;
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

?>
