<?php

///////////////////////////////////////////////
// Email account submition to mysql database //
///////////////////////////////////////////////
//$edit_domain $newmail_login $newmail_redirect1 $newmail_pass $newmail_redirect2 $newmail_deliver_localy
if(isset($_REQUEST["addnewlisttodomain"]) && $_REQUEST["addnewlisttodomain"] == "Ok"){
	global $conf_mta_type;
	$name = $_REQUEST["newlist_name"];
	$owner = $_REQUEST["newlist_owner"];

	// Check if mail exists...
	$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["newlist_name"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Mailbox allready exist in database then you can't use it for mailing list!");
	}
	
	//Check if list exists...
	$test_query = "SELECT * FROM $pro_mysql_list_table WHERE name='".$_REQUEST["newlist_name"]."' AND domain='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Mailing list allready exist in database !");
	}

	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	
	//Path of user's mailing lists
	$admin_path = getAdminPath($adm_login);
	$list_path = "$admin_path/$edit_domain/lists";
	 
	//Aggiungere validity string
	// 


	//Inserimento nel db ed estrazione id inserito
	$adm_query = "INSERT INTO $pro_mysql_list_table(domain, name, owner)
VALUES ('$edit_domain','".$_REQUEST["newlist_name"]."','".$_REQUEST["newlist_owner"]."');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
	
	//prendo l'id autoincrementale che della ml che ho inserito
	$query_last_ml = mysql_query ( "SELECT id from $pro_mysql_list_table order by id DESC limit 1" ) ;
	$lastml = mysql_fetch_array($query_last_ml) ;
	
	//Azioni su disco

	switch($conf_mta_type){
	case "postfix":
	
	$command = "(echo $edit_domain; echo $owner; echo N;) | /usr/bin/mlmmj-make-ml -L $name -s $list_path";
	exec($command);

	$fileName1 = '/usr/share/dtc/etc/postfix_virtual';
	$newLine1 = ''.$_REQUEST["newlist_name"].'@'.$edit_domain.' ml'.$lastml['id'].'';
	$fp1 = fopen($fileName1,"a");
	fwrite($fp1,"\n");
	fwrite($fp1,$newLine1);
	fclose($fp1);
	
	$fileName2 = '/usr/share/dtc/etc/postfix_aliases';
	$newLine2 = 'ml'.$lastml['id'].': "|/usr/bin/mlmmj-recieve -L '.$list_path.'/'.$_REQUEST["newlist_name"].'/"';
	$fp2 = fopen($fileName2,"a");
	fwrite($fp2,"\n");
	fwrite($fp2,$newLine2);
	fclose($fp2);
	
	sleep(2);
	exec('postmap /usr/share/dtc/etc/postfix_virtual');
	exec('postalias /usr/share/dtc/etc/postfix_aliases');
	
		break;
	default:
	case "qmail":
		
		break;
	}
	
	
	
}

/////////////////////////////////////////////////////////
// $edit_domain $edit_mailbox $editmail_pass $editmail_redirect1 $editmail_redirect2 $editmail_deliver_localy
/////////////////////////////////////////////////////////
if(isset($_REQUEST["modifylistdata"]) && $_REQUEST["modifylistdata"] == "Ok"){
	die("Modifica ml");
	/*checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

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

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");*/
}
//////////////////////////////////
// $edit_domain $editmail_login
//////////////////////////////////
if(isset($_REQUEST["dellist"]) && $_REQUEST["dellist"] == "Del"){
	echo "lol";
	die("cancella ml");
	/*checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// Verify strings given
	if(!isMailbox($_REQUEST["edit_mailbox"])){
		die($_REQUEST["edit_mailbox"]." does not look like a mailbox login...");
	}

	// Submit to sql database
	$adm_query="DELETE FROM $pro_mysql_pop_table WHERE mbox_host='$edit_domain' AND id='".$_REQUEST["edit_mailbox"]."' LIMIT 1";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");*/
}

?>
