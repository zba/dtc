<?php

/////////////////////////////////////////////////////
// Subscribe / Unsubscribe users to a mailing list //
/////////////////////////////////////////////////////
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "unsubscribe_user"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$name = $_REQUEST["edit_mailbox"];
	if(!isMailbox($name)){
		die("Mailbox format not correct !");
	}
	if(!isValidEmail($_REQUEST["subscriber_email"])){
		die("Incorrect format for subscriber!");
	}

	//Check if list exists...
	$test_query = "SELECT * FROM $pro_mysql_list_table WHERE name='".$_REQUEST["edit_mailbox"]."' AND domain='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 1){
		die("Mailing list doesn't exist in database !");
	}

	// Read the file
	$admin_path = getAdminPath($adm_login);
	$list_path = $admin_path."/".$edit_domain."/lists/".$edit_domain."_".$_REQUEST["edit_mailbox"]."/subscribers.d/";
	$filename = $list_path.substr($_REQUEST["subscriber_email"],0,1);
	$subs = file($filename);

	// Remove subscriber from content
	$n = sizeof($subs);
	$newsubs = array();
	for($i=0;$i<$n;$i++){
		if($subs[$i] != $_REQUEST["subscriber_email"]."\n" && $subs[$i] != ""){
			$newsubs[] = $subs[$i];
		}
	}

	if( sizeof($subs) == 1){
		// Remove the file if no subscriber
		unlink($filename);
	}else{
		// Write the file if there are some remaining subscribers
		$towrite = implode("",$newsubs);
		$fp = fopen($filename,"w+");
		fwrite($fp,$towrite);
		fclose($fp);
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "subscribe_new_user"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$name = $_REQUEST["edit_mailbox"];
	if(!isMailbox($name)){
		die("Mailbox format not correct !");
	}
	if(!isValidEmail($_REQUEST["subscriber_email"])){
		die("Incorrect format for subscriber!");
	}

	//Check if list exists...
	$test_query = "SELECT * FROM $pro_mysql_list_table WHERE name='".$_REQUEST["edit_mailbox"]."' AND domain='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 1){
		die("Mailing list doesn't exist in database !");
	}

	// Read the file
	$admin_path = getAdminPath($adm_login);
	$list_path = $admin_path."/".$edit_domain."/lists/".$edit_domain."_".$_REQUEST["edit_mailbox"]."/subscribers.d/";
	$filename = $list_path.substr($_REQUEST["subscriber_email"],0,1);
	if(is_file($filename)){
		$subs = file($filename);
	}else{
		$subs = array();
	}

	// Check if subscriber exists in the list
	$n = sizeof($subs);
	$newsubs = array();
	$exists = "false";
	for($i=0;$i<$n;$i++){
		if($subs[$i] == $_REQUEST["subscriber_email"]."\n"){
			$exists = "true";
		}
	}
	if($exists == "true"){
		echo "<font color=\"red\">". _("Subscriber exists already in the list") ."!</font>";
	}else{
		$subs[] = $_REQUEST["subscriber_email"]."\n";
		sort($subs);
		// Write the file if there are some remaining subscribers
		$towrite = implode("",$subs);
		$fp = fopen($filename,"w+");
		fwrite($fp,$towrite);
		fclose($fp);
	}
}

///////////////////////////////////////////////
// Email account submition to mysql database //
///////////////////////////////////////////////
//$edit_domain $newmail_login $newmail_redirect1 $newmail_pass $newmail_redirect2 $newmail_deliver_localy
if(isset($_REQUEST["addnewlisttodomain"]) && $_REQUEST["addnewlisttodomain"] == "Ok"){
	global $conf_mta_type;
	global $conf_webmaster_email_addr;
	global $conf_recipient_delimiter;

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

	// Check if mail exists in mailbox database...
	$test_query = "SELECT * FROM $pro_mysql_pop_table WHERE id='".$_REQUEST["newlist_name"]."' AND mbox_host='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Email address already exists in 'mailbox' database, it can't be used for the mailing list!");
	}
	
	// Check if mail exists in mailalias database...
	$test_query = "SELECT * FROM $pro_mysql_mailaliasgroup_table WHERE id='".$_REQUEST["newlist_name"]."' AND domain_parent='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\"");
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Email address already exists in 'mail alias' database, it can't be used for the mailing list!");
	}

	//Check if list exists...
	$test_query = "SELECT * FROM $pro_mysql_list_table
				WHERE name='".$_REQUEST["newlist_name"]."' AND domain='$edit_domain'";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line "
				.__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$testnum_rows = mysql_num_rows($test_result);
	if($testnum_rows != 0){
		die("Mailing list already exist in database!");
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

	$cmd = "echo \"$conf_recipient_delimiter\" > ".$list_path."/".$folder_name."/control/delimiter";
        exec($cmd);

	$fileName3 = $list_path.'/'.$folder_name.'/control/listaddress';
	$newLine3 = $name . "@" . $edit_domain;
	$fp3 = fopen($fileName3,"w");
	fwrite($fp3,$newLine3);
	fclose($fp3);

	// Adds a subonlyget tunable by default
	$command = "touch $list_full_path/control/subonlyget";
	exec($command);

	if (!preg_match("/\@/", $owner)){
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
	
	//dtc send an email to the owner
	$subject = str_replace("#name#",$name, _("List #name#@#domain# successfully created.") );
	$subject = str_replace("#domain#",$edit_domain,$subject);
	$msg = str_replace("#name#",$name, _("You can control your mailing list by email or by control panel.
You can send a email to the following addresses to execute the listed command:
  
  #name##DELIMITER#help@#domain# -> return the most common list's commands
  #name##DELIMITER#list@#domain# -> return the subscribers's list
  #name##DELIMITER#get-N@#domain# -> return the N list's message
  #name##DELIMITER#owner@#domain# -> you can contact the list's owner by this address
  #name##DELIMITER#unsubscribe@#domain# -> unsubscribe the email's sender from the list
  #name##DELIMITER#subscribe@#domain# -> subscribe the email's sender to the list
  #name##DELIMITER#subscribe-digest@#domain# -> subscribe the email's sender to the list's digest version
  #name##DELIMITER#subscribe-nomail@#domain# -> subscribe the email's sender to the list's nomail version
  
For more informations go to mlmmj official web site at http://mlmmj.mmj.dk") );
	$msg = str_replace("#DELIMITER#",$conf_recipient_delimiter,$msg);
	$msg = str_replace("#domain#",$edit_domain,$msg);
	$headers = "FROM: $conf_webmaster_email_addr\n";
	$headers .= "Return-Path: $conf_webmaster_email_addr\n";
	mail($owner, $subject, $msg, $headers);
	
	updateUsingCron("qmail_newu='yes',gen_qmail='yes'");
	triggerMXListUpdate();
}

function tunablesBooleanRequestCheck($ctrl_path,$tunable_name){
	$option_file = $ctrl_path."/".$tunable_name;
	if (isset($_REQUEST[$tunable_name])){
		//if file !exist -> i create
		if (!file_exists($option_file)){
			$touch = "touch ".$option_file;
			exec($touch);
		} 
	}else{
		if (file_exists($option_file)){
			$rem = "rm ".$option_file;
			exec($rem);
		} 
	}
}

function tunablesValueRequestCheck($ctrl_path,$tunable_name,$regexp=""){
	$option_file = $ctrl_path."/".$tunable_name;
	if ($_REQUEST[$tunable_name]!=""){
		//i write in the file
		$fp = fopen($option_file,"w+");
		if($fp != NULL){
			if($regexp!=""){
				if( preg_match($regexp,$_REQUEST[$tunable_name]) ){
					fwrite($fp,$_REQUEST[$tunable_name]."\n");
				}
			}else{
				fwrite($fp,$_REQUEST[$tunable_name]."\n");
			}
			fclose($fp);
		}
	}else{ //i remove the file
		if (file_exists($option_file)){
		$rem = "rm ".$option_file;
		exec($rem);
		} 
	}
}

function tunablesListRequestCheck($ctrl_path,$tunable_name,$regexp=""){
	$option_file = $ctrl_path."/".$tunable_name;
	if (file_exists($option_file) && ($tunable_name!="owner")){
		$rem = "rm ".$option_file;
		exec($rem);
	} 		
	$fp = fopen($option_file,"w+");
	if($fp != NULL){
		for($i=0;$i<sizeof($_REQUEST[$tunable_name]);$i++){
			if ($_REQUEST[$tunable_name][$i]!=""){
				if($regexp!=""){
					if( preg_match($regexp,$_REQUEST[$tunable_name][$i]) ){
						fwrite($fp,$_REQUEST[$tunable_name][$i]."\n");
					}
				}else{
					fwrite($fp,$_REQUEST[$tunable_name][$i]."\n");
				}
			}
		}
		fclose($fp);
	}
}

function tunablesTextareaRequestCheck($ctrl_path,$tunable_name){
	$option_file = $ctrl_path."/".$tunable_name;
	$buf_in = str_replace("\r","",$_REQUEST[$tunable_name]);
	$num_chars = strlen($buf_in);
	if($num_chars > 0){
		if(substr($buf_in,$num_chars-1) != "\n"){
			$buf_in .= "\n";
		}
		$fp = fopen($option_file,"w+");
		fwrite($fp,stripslashes($buf_in));
		fclose($fp);
	}else{
		$rem = "rm ".$option_file;
		exec($rem);
	}
}

function tunablesWABooleanRequestCheck($list_dir,$tunable_name){
	global $adm_login;
	global $pro_mysql_list_table;
	global $edit_domain;
	$name = $_REQUEST["edit_mailbox"];
	$admin_path = getAdminPath($adm_login);
	if($tunable_name == "webarchive"){
		$test_query = "SELECT webarchive FROM $pro_mysql_list_table WHERE domain='$edit_domain' AND name='$name' LIMIT 1";
		$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
		$test = mysql_fetch_array($test_result);
		// If no webarchive before and we want one now, we might need to create it's folders.
		if ($test[0] == "no" && isset($_REQUEST["webarchive"])){
			// Check if the list folder exists
			$list_web_path = $admin_path."/".$edit_domain."/subdomains/www/html/lists";
			if (!file_exists($list_web_path)){
				$create_web_path = "mkdir ".$list_web_path;
				exec($create_web_path);
			}
			// Check if the lists/listname folder exists
			$this_list_web_path = $admin_path."/".$edit_domain."/subdomains/www/html/lists/".$name;
			if (!file_exists($this_list_web_path)){
				$create_this_web_path = "mkdir ".$this_list_web_path;
				exec($create_this_web_path);
			}
			$this_list_rcfile = $list_dir."/rcfile";	
			if (file_exists($this_list_rcfile)){
				$rcfile = " -rcfile ".$this_list_rcfile." ";
			}else{
				$rcfile = " ";
			}
			if(isset($_REQUEST["spammode"])){
				$spammode = " -spammode ";
			}else{
				$spammode = " -nospammode ";
			}
			// Create the archive
			$archive_dir = $list_dir."/archive";
			$createwa = "mhonarc".$rcfile."-outdir ".$this_list_web_path.$spammode.$archive_dir;
			exec($createwa);
			$adm_query = "UPDATE $pro_mysql_list_table SET webarchive='yes' WHERE domain='$edit_domain' AND name='$name';";
			mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
		}
		if ($test[0]== "yes" && !isset($_REQUEST["webarchive"])){
			$adm_query = "UPDATE $pro_mysql_list_table SET webarchive='no' WHERE domain='$edit_domain' AND name='$name';";
			mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		}
	}else{
		if(isset($_REQUEST[$tunable_name])){
			$tun_value = "yes";
		}else{
			$tun_value = "no";
		}
		$q = "UPDATE $pro_mysql_list_table SET $tunable_name='$tun_value' WHERE domain='$edit_domain' AND name='$name';";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	}
}

function tunablesWABooleanActionsRequestCheck($list_dir){
	global $adm_login;
	global $pro_mysql_list_table;
	global $edit_domain;
	$name = $_REQUEST["edit_mailbox"];
	$admin_path = getAdminPath($adm_login);
	$this_list_web_path = $admin_path."/".$edit_domain."/subdomains/www/html/lists/".$name;
	$test_query = "SELECT webarchive FROM $pro_mysql_list_table	WHERE domain='$edit_domain' AND name='$name' LIMIT 1";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$test = mysql_fetch_array($test_result);
	if ($test[0]== "no"){
		if (isset($_REQUEST["deletewa"])){
			if (file_exists($this_list_web_path)){
			$delete_this_web_path = "rm -rf ".$this_list_web_path;
			exec($delete_this_web_path);
			}
		}
	}
	if ($test[0]== "yes"){
		if (isset($_REQUEST["recreatewa"])){
			if (file_exists($this_list_web_path)){
			$delete_this_web_path = "rm -rf ".$this_list_web_path;
			exec($delete_this_web_path);
			}
			if (!file_exists($this_list_web_path)){
			$create_this_web_path = "mkdir ".$this_list_web_path;
			exec($create_this_web_path);
			}
		$this_list_rcfile = $list_dir."/rcfile";	
			if (file_exists($this_list_rcfile)){
			$rcfile = " -rcfile ".$this_list_rcfile." ";
			}else{
			$rcfile = " ";
			}
		$archive_dir = $list_dir."/archive";
		$createwa = "mhonarc".$rcfile."-outdir ".$this_list_web_path." ".$archive_dir;
		exec($createwa);
		}
	}
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
	$newLine3 = "echo ".$new_list_owner." > ".$fileName3;
	exec($newLine3);

	//submit to sql
	$adm_query = "UPDATE $pro_mysql_list_table SET owner='$new_list_owner' WHERE domain='$edit_domain' AND name='$name';";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
	
	$list_dir = $list_path."/".$edit_domain."_".$name;
	$ctrl_dir = $list_dir."/control";

	//Now i do all options commands!!
	// 1 closedlist
	tunablesBooleanRequestCheck($ctrl_dir,"closedlist");
	tunablesBooleanRequestCheck($ctrl_dir,"moderated");
	tunablesListRequestCheck($ctrl_dir,"moderators","/(^([a-zA-Z0-9])|^([a-zA-Z0-9]+)([._a-zA-Z0-9-]*))@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+)\$/");
	tunablesBooleanRequestCheck($ctrl_dir,"subonlypost");
	tunablesBooleanRequestCheck($ctrl_dir,"notifysub");
	tunablesBooleanRequestCheck($ctrl_dir,"nosubconfirm");
	tunablesBooleanRequestCheck($ctrl_dir,"noarchive");
	tunablesBooleanRequestCheck($ctrl_dir,"noget");
	tunablesBooleanRequestCheck($ctrl_dir,"subonlyget");
	tunablesBooleanRequestCheck($ctrl_dir,"tocc");
	tunablesBooleanRequestCheck($ctrl_dir,"addtohdr");
	tunablesBooleanRequestCheck($ctrl_dir,"notoccdenymails");
	tunablesBooleanRequestCheck($ctrl_dir,"noaccessdenymails");
	tunablesBooleanRequestCheck($ctrl_dir,"nosubonlydenymails");
	tunablesValueRequestCheck($ctrl_dir,"prefix");
	tunablesValueRequestCheck($ctrl_dir,"memorymailsize","/^([0-9]+)\$/");
	if($conf_use_advanced_lists_tunables == "yes"){
		tunablesValueRequestCheck($ctrl_dir,"relayhost",'/^((([a-z0-9]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*(([a-z]([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$/');
		tunablesValueRequestCheck($ctrl_dir,"verp");
		tunablesValueRequestCheck($ctrl_dir,"maxverprecips","/^([0-9]+)\$/");
		tunablesValueRequestCheck($ctrl_dir,"delimiter","/^([+-])\$/");
		tunablesValueRequestCheck($ctrl_dir,"bouncelife","/^([0-9]+)\$/");
		tunablesTextareaRequestCheck($ctrl_dir,"access");
	}
	tunablesValueRequestCheck($ctrl_dir,"digestinterval","/^([0-9]+)\$/");
	tunablesValueRequestCheck($ctrl_dir,"digestmaxmails","/^([0-9]+)\$/");
	tunablesListRequestCheck($ctrl_dir,"owner","/(^([a-zA-Z0-9])|^([a-zA-Z0-9]+)([._a-zA-Z0-9-]*))@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+)\$/");
	tunablesTextareaRequestCheck($ctrl_dir,"customheaders");
	tunablesListRequestCheck($ctrl_dir,"delheaders","/(^([a-zA-Z0-9])|^([a-zA-Z0-9]+)([._a-zA-Z0-9-]*))@([a-z0-9]+)([-a-z0-9.]*)\.([a-z0-9-]*)([a-z0-9]+):\$/");
	tunablesTextareaRequestCheck($ctrl_dir,"footer");
	tunablesWABooleanRequestCheck($list_dir,"webarchive");
	tunablesTextareaRequestCheck($list_dir,"rcfile");
	tunablesWABooleanRequestCheck($ctrl_dir,"spammode");
	tunablesWABooleanActionsRequestCheck($list_dir);
	
}
//////////////////////////////////
// $edit_domain $editmail_login
//////////////////////////////////
if(isset($_REQUEST["dellist"]) && $_REQUEST["dellist"] == "Del"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	
	// Verify strings given
	if(!isMailbox($_REQUEST["edit_mailbox"])){
		die($_REQUEST["edit_mailbox"]. _(" does not look like a mailbox login...") );
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
	
	// i need to add the postfix's aliases deletion
	// very important!!!
	
	// Delete list from sql database
	$adm_query="DELETE FROM $pro_mysql_list_table WHERE domain='$edit_domain' AND name='$name' LIMIT 1";
	unset($edit_mailbox);
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	if($edit_domain == $conf_main_domain){
		$adm_query = "DELETE FROM $pro_mysql_vps_server_lists_table WHERE list_name='$name';";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	}

	triggerMXListUpdate();
	updateUsingCron("gen_qmail='yes', qmail_newu='yes'");
}

?>
