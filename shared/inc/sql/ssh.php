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
		$submit_err .= "You don't have the SSH login flag!";
		$commit_flag = "no";
	}

	if(!ereg("^$adm_path",$_REQUEST["newssh_path"]) || strstr($_REQUEST["newssh_path"],'..')){
		$submit_err .= "Your path is restricted to $adm_path<br>\n";
		$commit_flag = "no";
	}

	// If no @ in the login, append the domain name
	// if not, check that it's owner's domain name at end of login
	if($conf_domain_based_ssh_logins == "yes"){
		$pos = strpos($_REQUEST["newssh_login"],$edit_domain);
		if($pos == false){
			$_REQUEST["newssh_login"] .= '@' . $edit_domain;
		}else{
			if(!ereg($edit_domain."\$",$_REQUEST["newssh_login"])){
				$submit_err .= "Your login must be in the form login@domain.com";
				$commit_flag = "no";
			}
		}
	}
	if(!isFtpLogin($_REQUEST["newssh_login"])){
		$submit_err .= "Incorrect SSH login form: please enter another login and try again.<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["newssh_pass"])){
		$submit_err .= "Incorrect SSH password: from 6 to 16 chars, a-z A-Z 0-9<br>\n";
		$commit_flag = "no";
	}
	$_REQUEST["newssh_path"] = addslashes($_REQUEST["newssh_path"]);

	$crypt_ssh_password = crypt($_REQUEST["newssh_pass"]);

	if($commit_flag == "yes"){
		$adm_query = " INSERT INTO $pro_mysql_ssh_table
(login, crypt, password, homedir, count, fhost, faddr, ftime, fcdir, fstor, fretr, bstor, bretr, creation, ts, frate, fcred, brate, bcred, flogs, size, shell, hostname)VALUES
('".$_REQUEST["newssh_login"]."', '" . $crypt_ssh_password . "', '".$_REQUEST["newssh_pass"]."', '".$_REQUEST["newssh_path"]."','NULL', NULL, NULL, NOW(NULL), NULL, 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', NULL, '5',
'15', '5','1', NULL, '', '/bin/dtc-chroot-shell', '$edit_domain') ";
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
		$submit_err .= "Your path is restricted to &quot;$adm_path&quot;<br>\n";
		$commit_flag = "no";
	}

	$new_path = $_REQUEST["edssh_path"];

	if(!isFtpLogin($_REQUEST["edssh_account"])){
		$submit_err .= "Incorrect ssh login : this is not a good string for a ssh login, please enter a new one.";
		$commit_flag = "no";
	}

	if(!isDTCPassword($_REQUEST["edssh_pass"])){
		$submit_err .= "Incorrect SSH password: from 6 to 16 chars, a-z A-Z 0-9";
		$commit_flag = "no";
	}

	$crypt_ssh_password = crypt($_REQUEST["edssh_pass"]);

	if($commit_flag == "yes"){
		$adm_query = "UPDATE $pro_mysql_ssh_table SET homedir='".addslashes($new_path)."', crypt='".$crypt_ssh_password."', password='".$_REQUEST["edssh_pass"]."' WHERE login ='".$_REQUEST["edssh_account"]."' AND hostname='$edit_domain' LIMIT 1;";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
	}
	updateUsingCron("gen_ssh='yes'");
}

?>
