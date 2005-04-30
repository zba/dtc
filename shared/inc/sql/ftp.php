<?php

/////////////////////////////
// Ftp accounts management //
/////////////////////////////
if(isset($_REQUEST["newftpaccount"]) && $_REQUEST["newftpaccount"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_path = getAdminPath($adm_login);

	if(!ereg("^$adm_path",$_REQUEST["newftp_path"]) || strstr($_REQUEST["newftp_path"],'..')){
		$submit_err .= "Your path is restricted to $adm_path<br>\n";
		$commit_flag = "no";
	}

	// If no @ in the login, append the domain name
	// if not, check that it's owner's domain name at end of login
	if($conf_domain_based_ftp_logins == "yes"){
		$pos = strpos($_REQUEST["newftp_login"],$edit_domain);
		if($pos === false){
			$_REQUEST["newftp_login"] .= $edit_domain;
		}else{
			if(!ereg($edit_domain."\$",$_REQUEST["newftp_login"])){
				$submit_err .= "Your login must be in the form login@domain.com";
				$commit_flag = "no";
			}
		}
	}
	if(!isFtpLogin($_REQUEST["newftp_login"])){
		$submit_err .= "Incorrect FTP login form: please enter another login and try again.<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["newftp_pass"])){
		$submit_err .= "<font color=\"red\">Incorrect FTP password: from 6 to 16 chars, a-z A-Z 0-9<br>\n";
		$commit_flag = "no";
	}
	$_REQUEST["newftp_path"] = addslashes($_REQUEST["newftp_path"]);

	if($commit_flag == "yes"){
		$adm_query = " INSERT INTO $pro_mysql_ftp_table
(login,           password, homedir, count, fhost, faddr, ftime, fcdir, fstor, fretr, bstor, bretr, creation, ts, frate, fcred, brate, bcred, flogs, size, shell, hostname)VALUES
('".$_REQUEST["newftp_login"]."', '".$_REQUEST["newftp_pass"]."', '".$_REQUEST["newftp_path"]."','NULL', NULL, NULL, NOW(NULL), NULL, 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', NULL, '5',
'15', '5','1', NULL, '', '/bin/bash', '$edit_domain') ";
		// $newftp_login $newftp_pass $edit_domain
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
	}
}

// $edftp_account $edit_domain
if(isset($_REQUEST["deleteftpaccount"]) && $_REQUEST["deleteftpaccount"] == "Delete"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_query = "DELETE FROM $pro_mysql_ftp_table WHERE hostname='$edit_domain' AND login='".$_REQUEST["edftp_account"]."' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
}

// $edftp_account $edit_domain $edftp_pass
if(isset($_REQUEST["update_ftp_account"]) && $_REQUEST["update_ftp_account"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$adm_path = getAdminPath($adm_login);

	if(0 != strncmp($adm_path,$_REQUEST["edftp_path"],strlen($adm_path)-1) || strstr($_REQUEST["edftp_path"],'..') || strstr($_REQUEST["edftp_path"],"'") || strstr($_REQUEST["edftp_path"],"\\")){
		$submit_err .= "Your path is restricted to &quot;$adm_path&quot;<br>\n";
		$commit_flag = "no";
	}

	if(!isFtpLogin($_REQUEST["edftp_account"])){
		$submit_err .= "Incorrect ftp login : this is not a good string for a ftp login, please enter a new one.";
		$commit_flag = "no";
	}

	if(!isDTCPassword($_REQUEST["edftp_pass"])){
		$submit_err .= "Incorrect FTP password: from 6 to 16 chars, a-z A-Z 0-9";
		$commit_flag = "no";
	}
	$_REQUEST["edftp_path"] = addslashes($_REQUEST["edftp_path"]);

	if($commit_flag == "yes"){
		$adm_query = "UPDATE $pro_mysql_ftp_table SET homedir='".$_REQUEST["edftp_path"]."', password='".$_REQUEST["edftp_pass"]."' WHERE login ='".$_REQUEST["edftp_account"]."' AND hostname='$edit_domain' LIMIT 1;";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");
	}
}

?>
