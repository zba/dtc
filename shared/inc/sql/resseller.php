<?php

/////////////////////////////
// Ftp accounts management //
/////////////////////////////
// new_adm_login=test&new_adm_pass=test
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_child_account"){
	checkLoginPass($adm_login,$adm_pass);
	if(!isDTCPassword($_REQUEST["new_adm_pass"])){
		$submit_err .= "Incorrect FTP password: from 6 to 16 chars, a-z A-Z 0-9<br>\n";
		$commit_flag = "no";
	}
	if(!isFtpLogin($_REQUEST["new_adm_login"])){
		$submit_err .= "Incorrect DTC login: a-z A-Z 0-9<br>\n";
		$commit_flag = "no";
	}

	if($commit_flag == "yes"){
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["new_adm_login"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 0){
			$submit_err .= "There is already an admin with that name. Please pickup another name!<br>\n";
			$commit_flag = "no";
		}
	}

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Cannot find user $adm_login line ".__LINE__." file ".__FILE__);
	$a = mysql_fetch_array($r);
	if($commit_flag == "yes"){
		// Create the admin's path
		$new_adm_path = $conf_site_root_host_path."/".$_REQUEST["new_adm_login"];
		if($conf_demo_version == "no"){
			$oldumask = umask(0);
			if(!file_exists($new_adm_path)){
				mkdir("$new_adm_path", 0750);
				$console .= "mkdir $new_adm_path;<br>";
			}
			umask($oldumask);
		}

		// Insert the new admin
		$q = "INSERT INTO $pro_mysql_admin_table (adm_login, adm_pass, path, ob_next, ob_head, ob_tail)
		VALUES ('".$_REQUEST["new_adm_login"]."','".$_REQUEST["new_adm_pass"]."', '$new_adm_path','".$a["adm_login"]."','0','0');";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo $q."<br>";
		// If this admin had no child account
		echo "ob_head: ".$a["ob_head"]."<br>"."ob_tail: ".$a["ob_tail"]."<br>";
		if($a["ob_head"] == "0" && $a["ob_tail"] == "0"){
			// Simply update the main account ob_head and tail
			$q = "UPDATE $pro_mysql_admin_table SET ob_head='".$_REQUEST["new_adm_login"]."',ob_tail='".$_REQUEST["new_adm_login"]."' WHERE adm_login='$adm_login';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo $q."<br>";
		// If this admin has children
		}else{
			// Update the last child's ob_next to point to the added admin
			$q = "UPDATE $pro_mysql_admin_table SET ob_next='".$_REQUEST["new_adm_login"]."' WHERE adm_login='".$a["ob_tail"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo $q."<br>";

			// Simply update the main account ob_tail, ob_head is good already
			$q = "UPDATE $pro_mysql_admin_table SET ob_tail='".$_REQUEST["new_adm_login"]."' WHERE adm_login='$adm_login';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo $q."<br>";
		}
	}
}

// adm_login=zigo&addrlink=resseller&adm_pass=513411410&action=delete_child_account&account_name=bbbb
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_child_account"){
	checkLoginPass($adm_login,$adm_pass);
	// Search for child to delete
}

?>
