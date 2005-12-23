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
	$list_path = $admin_path."/".$edit_domain."/lists";
	 
	// Mailing list will be created in /var/www/sites/USERNAME/DOMAIN-NAME/lists/DOMAIN-NAME_LIST-NAME/
	$folder_name = $edit_domain."_".$name;
	$list_full_path = $list_path."/".$folder_name;

	// Insert the record in the sql
	$adm_query = "INSERT INTO $pro_mysql_list_table(domain, name, owner) VALUES ('$edit_domain','".$_REQUEST["newlist_name"]."','".$_REQUEST["newlist_owner"]."');";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__line__." file ".__FILE__." sql said ".mysql_error());
	
	// Call the mlmmj-make-ml command to create the mailing list
	$command = "(echo $edit_domain; echo ".$owner."; echo N;) | /usr/bin/mlmmj-make-ml -L $folder_name -s $list_path";
	exec($command);
	
	//Create a symbolik link in /var/spool/mlmmj
	$symlink = "ln -s ".$list_path."/".$folder_name." /var/spool/mlmmj/".$folder_name;
	exec($symlink);

	$fileName3 = $list_path.'/'.$folder_name.'/control/listaddress';
	$newLine3 = $name . "@" . $edit_domain;
	$fp3 = fopen($fileName3,"w");
	fwrite($fp3,$newLine3);
	fclose($fp3);

	if (!ereg("\@", $owner)){
		$owner .= "@" . $edit_domain;
	}

	switch($conf_mta_type){
	case "postfix":
			
		$fileName1 = "$conf_generated_file_path" . "/postfix_virtual";
		$newLine1 = ''.$_REQUEST["newlist_name"].'@'.$edit_domain.' '.$folder_name.'';
		$fp1 = fopen($fileName1,"a");
		fwrite($fp1,"\n");
		fwrite($fp1,$newLine1);
		fclose($fp1);
				
		$fileName2 = "$conf_generated_file_path" . "/postfix_aliases";
		$newLine2 = $folder_name.': "|/usr/bin/mlmmj-recieve -L '.$list_full_path.'/"';
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
		writeMlmmjQmailFile($list_full_path);
		break;
	}
	updateUsingCron("qmail_newu='yes',gen_qmail='yes'");
}

/////////////////////////////////////////////////////////
// $edit_domain $edit_mailbox $editmail_owner
/////////////////////////////////////////////////////////
if(isset($_REQUEST["modifylistdata"]) && $_REQUEST["modifylistdata"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	$admin_path = getAdminPath($adm_login);
   $list_path = $admin_path."/".$edit_domain."/lists";

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
	$newLine3 = $new_list_owner;
	$fp3 = fopen($fileName3,"w");
	fwrite($fp3,$newLine3);
	fclose($fp3);

	//submit to sql
	$adm_query = "UPDATE $pro_mysql_list_table SET owner='$new_list_owner' WHERE domain='$edit_domain' AND name='$name';";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	
	//Now i do all options commands!!
	// 1 closedlist
	if (isset($_REQUEST["closedlist"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/closedlist";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/closedlist";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 2 moderated
	if (isset($_REQUEST["moderated"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/moderated";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/moderated";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 3 subonlypost
	if (isset($_REQUEST["subonlypost"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/subonlypost";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/subonlypost";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 4 notifysub
	if (isset($_REQUEST["notifysub"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/notifysub";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/notifysub";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 5 nosubconfirm
	if (isset($_REQUEST["nosubconfirm"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/nosubconfirm";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/nosubconfirm";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 6 noarchive
	if (isset($_REQUEST["noarchive"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/noarchive";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/noarchive";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 7 noget
	if (isset($_REQUEST["noget"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/noget";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/noget";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 8 subonlyget
	if (isset($_REQUEST["subonlyget"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/subonlyget";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/subonlyget";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 9 tocc
	if (isset($_REQUEST["tocc"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/tocc";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/tocc";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 10 addtohdr
	if (isset($_REQUEST["addtohdr"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/addtohdr";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/addtohdr";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 11 notoccdenymails
	if (isset($_REQUEST["notoccdenymails"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/notoccdenymails";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/notoccdenymails";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 12 noaccessdenymails
	if (isset($_REQUEST["noaccessdenymails"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/noaccessdenymails";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/noaccessdenymails";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	// 13 nosubonlydenymails
	if (isset($_REQUEST["nosubonlydenymails"])){
		//if file !exist -> i create
		$option_file = $list_path."/".$edit_domain."_".$name."/nosubonlydenymails";
		if (!file_exists($option_file)){
		$touch = "touch ".$option_file;
		exec($touch);
		} 
	}else{
		$option_file = $list_path."/".$edit_domain."_".$name."/nosubonlydenymails";
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
	
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
	
	//Some vars
	$name = $_REQUEST["edit_mailbox"];
	$admin_path = getAdminPath($adm_login);
	$folder_name = $edit_domain."_".$name;
	$list_full_path = $admin_path."/".$edit_domain."/lists/".$folder_name;
	
	//I delete all files of this mailing list
	$del_spool = "rm /var/spool/mlmmj/".$folder_name;
	exec($del_spool);
	$del_etc = "rm -rf /etc/mlmmj/lists/".$folder_name;
	exec($del_etc);
	$del_ml = "rm -rf ".$list_full_path;
	exec($del_ml);
	
	// Delete list from sql database
	$adm_query="DELETE FROM $pro_mysql_list_table WHERE domain='$edit_domain' AND name='$name' LIMIT 1";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
}

?>
