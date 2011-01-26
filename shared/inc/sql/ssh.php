<?php

function hasSSHLoginFlag($adm_login){
	global $pro_mysql_admin_table;

	$q = "SELECT ssh_login_flag FROM $pro_mysql_admin_table WHERE adm_login='$adm_login'";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$a = mysql_fetch_array($r);
	if($a["ssh_login_flag"] != "yes"){
		return false;
	}else{
		return true;
	}
}

/////////////////////////////
// SSH accounts management //
/////////////////////////////
if(isset($_REQUEST["newsshaccount"]) && $_REQUEST["newsshaccount"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_path = getAdminPath($adm_login);

	if(!hasSSHLoginFlag($adm_login)){
		$submit_err .= _("You don't have SSH login permissions.") ;
		$commit_flag = "no";
	}

	if (preg_match("/^$adm_path\\/$edit_domain\\/\$/", $_REQUEST["newssh_path"]) || preg_match("/^$adm_path\\/\$", $_REQUEST["newssh_path"])){
		// all good here, can go ahead
	}else if(!preg_match("/^$adm_path\\/$edit_domain\\/subdomains",$_REQUEST["newssh_path"]) || strstr($_REQUEST["newssh_path"],'..')){
		$submit_err .= _("Your path is restricted to ") ."$adm_path/$edit_domain/subdomains<br>\n";
		$commit_flag = "no";
	}

	// If no @ in the login, append the domain name
	// if not, check that it's owner's domain name at end of login
	if($conf_domain_based_ssh_logins == "yes"){
		$pos = strpos($_REQUEST["newssh_login"],$edit_domain);
		if($pos == false){
			$_REQUEST["newssh_login"] .= '@' . $edit_domain;
		}else{
			if(!preg_match("/".$edit_domain."\$/",$_REQUEST["newssh_login"])){
				$submit_err .= "Your login must be in the form login@domain.com";
				$commit_flag = "no";
			}
		}
	}
	if(!isFtpLogin($_REQUEST["newssh_login"])){
		$submit_err .= _("Incorrect SSH login form: please enter another login and try again.<br>\n") ;
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["newssh_pass"])){
		$submit_err .= _("Incorrect SSH password: from 6 to 16 characters, a-z A-Z 0-9<br>\n") ;
		$commit_flag = "no";
	}
	$_REQUEST["newssh_path"] = mysql_real_escape_string($_REQUEST["newssh_path"]);

	$crypt_ssh_password = crypt($_REQUEST["newssh_pass"], dtc_makesalt());

	if($commit_flag == "yes"){
		$adm_query = " INSERT INTO $pro_mysql_ssh_table
(login, uid, gid, crypt, password, homedir, count, fhost, faddr, ftime, fcdir, fstor, fretr, bstor, bretr, creation, ts, frate, fcred, brate, bcred, flogs, size, hostname)VALUES
('".$_REQUEST["newssh_login"]."', $conf_nobody_user_id, $conf_nobody_group_id, '" . $crypt_ssh_password . "', '".$_REQUEST["newssh_pass"]."', '".$_REQUEST["newssh_path"]."','NULL', NULL, NULL, NOW(NULL), NULL, 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', NULL, '5',
'15', '5','1', NULL, '', '$edit_domain') ";
		// $newssh_login $newssh_pass $edit_domain
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	updateUsingCron("gen_ssh='yes'");
}

// $edssh_account $edit_domain
if(isset($_REQUEST["deletesshaccount"]) && $_REQUEST["deletesshaccount"] == "Delete"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	if(!hasSSHLoginFlag($adm_login)){
		$submit_err .= "You don't have the SSH login flag!";
		$commit_flag = "no";
	}
	$adm_query = "DELETE FROM $pro_mysql_ssh_table WHERE hostname='$edit_domain' AND login='".$_REQUEST["edssh_account"]."' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
	updateUsingCron("gen_ssh='yes'");
}

// $edssh_account $edit_domain $edssh_pass
if(isset($_REQUEST["update_ssh_account"]) && $_REQUEST["update_ssh_account"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_path = getAdminPath($adm_login);

	if(!hasSSHLoginFlag($adm_login)){
		$submit_err .= "You don't have the SSH login flag!";
		$commit_flag = "no";
	}

	if(0 != strncmp($adm_path,$_REQUEST["edssh_path"],strlen($adm_path)-1) || strstr($_REQUEST["edssh_path"],'..') || strstr($_REQUEST["edssh_path"],"'") || strstr($_REQUEST["edssh_path"],"\\")){
		$submit_err .= _("Your path is restricted to ") ."&quot;$adm_path/$edit_domain/subdomains&quot;<br>\n";
		$commit_flag = "no";
	}

	$new_path = $_REQUEST["edssh_path"];

	if(!isFtpLogin($_REQUEST["edssh_account"])){
		$submit_err .= _("Incorrect ssh login : this is not a good string for a ssh login, please enter a new one.") ;
		$commit_flag = "no";
	}

	if(!isDTCPassword($_REQUEST["edssh_pass"])){
		$submit_err .= _("Incorrect SSH password: from 6 to 16 characters, a-z A-Z 0-9") ;
		$commit_flag = "no";
	}

	$crypt_ssh_password = crypt($_REQUEST["edssh_pass"], dtc_makesalt());

	if($commit_flag == "yes"){
		$adm_query = "UPDATE $pro_mysql_ssh_table SET homedir='".mysql_real_escape_string($new_path)."', crypt='".$crypt_ssh_password."', password='".$_REQUEST["edssh_pass"]."' WHERE login ='".$_REQUEST["edssh_account"]."' AND hostname='$edit_domain' LIMIT 1;";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
	}
	updateUsingCron("gen_ssh='yes'");
}

?>
