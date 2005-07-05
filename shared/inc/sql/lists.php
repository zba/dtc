<?php

///////////////////////////////////////////////
// Email account submition to mysql database //
///////////////////////////////////////////////
//$edit_domain $newmail_login $newmail_redirect1 $newmail_pass $newmail_redirect2 $newmail_deliver_localy
if(isset($_REQUEST["addnewlisttodomain"]) && $_REQUEST["addnewlisttodomain"] == "Ok"){
	global $conf_mta_type;

	// This has to be done BEFORE any other sql requests using login/pass or edit_domain !!!
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	$name = $_REQUEST["newlist_name"];
	$owner = $_REQUEST["newlist_owner"];

	// YOU HAVE TO DO BASIC FIELD CHECKING otherwise it can lead to mysql insertion hack by a customer that has a valid pass!!!
	if(!isMailbox($name)){
		die("Mailbox format not correct !");
	}
	if(!isValidEmail($owner)){
		die("Owner is not a valid email !");
	}

	// Check if mail exists...
	$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["newlist_name"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Mailbox allready exist in database then you can't use it for mailing list!");
	}
	
	//Check if list exists...
	$test_query = "SELECT * FROM $pro_mysql_list_table
				WHERE name='".$_REQUEST["newlist_name"]."' AND domain='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line "
				.__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Mailing list already exist in database !");
	}

	//Path of user's mailing lists
	$admin_path = getAdminPath($adm_login);
	$list_path = "$admin_path/$edit_domain/lists";
	 
	// T: Please lucas comments in english !!!
	//Aggiungere validity string
	// 


	// T: Please lucas comments in english !!!
	//Inserimento nel db ed estrazione id inserito
	$adm_query = "INSERT INTO $pro_mysql_list_table(domain, name, owner)
VALUES ('$edit_domain','".$_REQUEST["newlist_name"]."','".$_REQUEST["newlist_owner"]."');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
	
	// T: Please lucas comments in english !!!
	//prendo l'id autoincrementale che della ml che ho inserito
	$query_last_ml = mysql_query ( "SELECT id from $pro_mysql_list_table order by id DESC limit 1" ) ;
	$lastml = mysql_fetch_array($query_last_ml) ;
	
	// T: Please lucas comments in english !!!
	//Azioni su disco

	$command = "(echo $edit_domain; echo ".$owner."; echo N;) | /usr/bin/mlmmj-make-ml -L $name -s $list_path";
	exec($command);

	$fileName3 = $list_path.'/'.$name.'/control/listaddress';
	$newLine3 = $name . "@" . $edit_domain;
	$fp3 = fopen($fileName3,"w");
	fwrite($fp3,$newLine3);
	fclose($fp3);

	switch($conf_mta_type){
	case "postfix":
		$postfix_name = $edit_domain . "_" . $name;
		$owner .= "@" . $edit_domain;
			
		$fileName1 = "$conf_generated_file_path" . "/postfix_virtual";
		$newLine1 = ''.$_REQUEST["newlist_name"].'@'.$edit_domain.' '.$postfix_name.'';
		$fp1 = fopen($fileName1,"a");
		fwrite($fp1,"\n");
		fwrite($fp1,$newLine1);
		fclose($fp1);
				
		$fileName2 = "$conf_generated_file_path" . "/postfix_aliases";
		$newLine2 = $postfix_name.': "|/usr/bin/mlmmj-recieve -L '.$list_path.'/'.$name.'/"';
		$fp2 = fopen($fileName2,"a");
		fwrite($fp2,"\n");
		fwrite($fp2,$newLine2);
		fclose($fp2);

		sleep(2);
		exec("postmap $conf_generated_file_path" . "/postfix_virtual");
		exec("postalias $conf_generated_file_path" . "/postfix_aliases");
		break;
	default:
	case "qmail":
		writeMlmmjQmailFile($name,$edit_domain);
		break;
	}
}

/////////////////////////////////////////////////////////
// $edit_domain $edit_mailbox $editmail_owner
/////////////////////////////////////////////////////////
if(isset($_REQUEST["modifylistdata"]) && $_REQUEST["modifylistdata"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	$admin_path = getAdminPath($adm_login);
        $list_path = "$admin_path/$edit_domain/lists";

	$new_list_owner = $_REQUEST["editmail_owner"];

	$name = $_REQUEST["edit_mailbox"];

	if(!isMailbox($name)){
		die("Mailbox format not correct !");
	}
	if(!isValidEmail($new_list_owner)){
		die("Owner is not a valid email !");
	}


	//now need to edit the owner to $_REQUEST["editmail_owner"]; 
	$fileName3 = $list_path.'/'. $edit_domain . '_' . $name.'/control/owner';
	$newLine3 = $new_list_owner . "@" . $edit_domain;
	$fp3 = fopen($fileName3,"w");
	fwrite($fp3,$newLine3);
	fclose($fp3);

	// submit to sql
	$adm_query = "UPDATE $pro_mysql_list_table SET owner='$new_list_owner' WHERE domain='$edit_domain' AND name='$name';";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	if(!isMailbox($_REQUEST["edit_mailbox"])){
		die($_REQUEST["edit_mailbox"]." does not look like a mailbox login...");
	}
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
}
//////////////////////////////////
// $edit_domain $editmail_login
//////////////////////////////////
if(isset($_REQUEST["dellist"]) && $_REQUEST["dellist"] == "Del"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	
	// Verify strings given
	if(!isMailbox($_REQUEST["edit_mailbox"])){
		die($_REQUEST["edit_mailbox"]." does not look like a mailbox login...");
	}

	// Submit to sql database
	$adm_query="DELETE FROM $pro_mysql_list_table WHERE domain='$edit_domain' AND name='".$_REQUEST["edit_mailbox"]."' LIMIT 1";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
}

?>
