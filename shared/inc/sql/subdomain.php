<?php

////////////////////////////
// Sub-domains management //
////////////////////////////
if(isset($_REQUEST["delsubdomain"]) && $_REQUEST["delsubdomain"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	if(!checkSubdomainFormat($_REQUEST["delsubdomain_name"])){
		die("Incorrect subdomain name format...");
	}
	// Del subdomain in database
	$adm_query = "DELETE FROM $pro_mysql_subdomain_table WHERE subdomain_name='".$_REQUEST["delsubdomain_name"]."' AND domain_name='$edit_domain' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
}
///////////////////////////////////////////////////////////
if(isset($_REQUEST["subdomaindefault"]) && $_REQUEST["subdomaindefault"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	if(!checkSubdomainFormat($_REQUEST["subdomaindefault_name"])){
		die("Incorrect subdomain name format...");
	}	$adm_query = "UPDATE $pro_mysql_domain_table SET default_subdomain='".$_REQUEST["subdomaindefault_name"]."' WHERE name='$edit_domain' LIMIT 1;";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes'");
}
//////////////////////////////////////////////////////////
// addrlink=example.com%2Fsubdomains&edit_domain=example.com&whatdoiedit=subdomains&subdomaindefault_name=www&delsubdomain_name=dtc&
if(isset($_REQUEST["edit_one_subdomain"]) && $_REQUEST["edit_one_subdomain"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	if(!checkSubdomainFormat($_REQUEST["subdomain_name"])){
		die("Incorrect subdomain name format...");
	}
	// Verify it's an valid IP or CNAME value
	if(!isIP($_REQUEST["newsubdomain_ip"]) && !isHostnameOrIP($_REQUEST["newsubdomain_ip"])){
		$newsubdomain_ip = "default";
	}else{
		if(isIP($_REQUEST["newsubdomain_ip"])){
			$newsubdomain_ip = $_REQUEST["newsubdomain_ip"];
		}else{
			$server = $_REQUEST["newsubdomain_ip"];
			// echo "Checking POP3<br>";
			if(($server_ip = gethostbynameFalse($server)) == false){
				echo "Cannot resolv your server: ".$_REQUEST["newsubdomain_ip"]."<br>";
				$newsubdomain_ip = "default";
			}else{
				$newsubdomain_ip = $_REQUEST["newsubdomain_ip"];
			}
		}
	}

// =yes&webalizer=yes&w3_alias=yes
	if($_REQUEST["register_globals"] == "yes")	$reg_globs = ", register_globals='yes'";
	else		$reg_globs = ", register_globals='no'";
	if($_REQUEST["webalizer"] == "yes")	$webalizer = ", webalizer_generate='yes'";
	else		$webalizer = ", webalizer_generate='no'";
	if($_REQUEST["w3_alias"] == "yes")	$w3alias = ", w3_alias='yes'";
	else		$w3alias = ", w3_alias='no'";
	$add_vals .= $reg_globs.$webalizer.$w3alias;
	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	if(isFtpLogin($_REQUEST["subdomain_dynlogin"]) && isDTCPassword($_REQUEST["subdomain_dynpass"])){
		$add_vals .= ", login='".$_REQUEST["subdomain_dynlogin"]."', pass='".$_REQUEST["subdomain_dynpass"]."'";
	}else{
		$add_vals .= ", login=NULL, pass=NULL ";
	}
	$domupdate_query = "UPDATE $pro_mysql_subdomain_table SET ip='".$_REQUEST["newsubdomain_ip"]."'$add_vals,
	associated_txt_record='".addslashes($_REQUEST["associated_txt_record"])."' WHERE domain_name='$edit_domain' AND subdomain_name='".$_REQUEST["subdomain_name"]."' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named='yes'");
}
/////////////////////////////////////////////////////
if(isset($_REQUEST["newsubdomain"]) && $_REQUEST["newsubdomain"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	// This can be added : it's a mater of the admin's choice...
//	if($_REQUEST["newsubdomain_name"] == "pop" || $_REQUEST["newsubdomain"] == "smtp" || $_REQUEST["newsubdomain_name"] == "ftp"){
//		die("You cannot add \"pop\", \"smtp\" or \"ftp\" as subdomain names, because they are reserved for the corresponding services.");
//	}

	// Verify string validity
	if(!checkSubdomainFormat($_REQUEST["newsubdomain_name"])){
		die("Incorect subdomain name format...");
	}

	// We have now to get the user directory and use it ! :)
	$query = "SELECT path FROM $pro_mysql_admin_table WHERE adm_login='$adm_login'";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$testnum_rows = mysql_num_rows($result);
	if($testnum_rows != 1){
		die("Cannot fetch user !!!");
	}
	$row = mysql_fetch_array($result);
	$admin_path = $row["path"];

	// Make the directorys
	$newsubdomain_dirpath = "$admin_path/$edit_domain/subdomains/".$_REQUEST["newsubdomain_name"];
	if($conf_demo_version == "no"){
		if(!file_exists("$newsubdomain_dirpath"))
			mkdir("$newsubdomain_dirpath", 0750);
		if(!file_exists("$newsubdomain_dirpath/html"))
			mkdir("$newsubdomain_dirpath/html", 0750);
		if(!file_exists("$newsubdomain_dirpath/cgi-bin"))
			mkdir("$newsubdomain_dirpath/cgi-bin", 0750);
		if(!file_exists("$newsubdomain_dirpath/logs"))
			mkdir("$newsubdomain_dirpath/logs", 0750);
		exec("cp -fulpRv $conf_chroot_path/* $newsubdomain_dirpath");
	}
	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	// Verify it's an valid IP
	if(!isIP($_REQUEST["newsubdomain_ip"]) && !isHostnameOrIP($_REQUEST["newsubdomain_ip"])){
		$newsubdomain_ip = "default";
	}else{
		if(isIP($_REQUEST["newsubdomain_ip"])){
			$newsubdomain_ip = $_REQUEST["newsubdomain_ip"];
		}else{
			$server = $_REQUEST["newsubdomain_ip"];
			// echo "Checking POP3<br>";
			if(($server_ip = gethostbynameFalse($server)) == false){
				echo "Cannot resolv your server: ".$_REQUEST["newsubdomain_ip"]."<br>";
				$newsubdomain_ip = "default";
			}else{
				$newsubdomain_ip = $_REQUEST["newsubdomain_ip"];
			}
		}
	}

	if(isFtpLogin($_REQUEST["newsubdomain_dynlogin"]) && isDTCPassword($_REQUEST["newsubdomain_dynpass"])){
		$add_field = ",login,pass";
		$add_values = ",'".$_REQUEST["newsubdomain_dynlogin"]."','".$_REQUEST["newsubdomain_dynpass"]."'";
	}else{
		$add_field = "";
		$add_values = "";
	}
	$adm_query = "INSERT INTO $pro_mysql_subdomain_table (id,domain_name,subdomain_name,associated_txt_record,ip".$add_field.") VALUES ('','$edit_domain','".$_REQUEST["newsubdomain_name"]."','".addslashes($_REQUEST["associated_txt_record"])."','$newsubdomain_ip'".$add_values.");";
	mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

        // Create the new site html front page
        if($conf_demo_version == "no"){
                system ("cp -rf $conf_generated_file_path/template/* $admin_path/$edit_domain/subdomains/".$_REQUEST["newsubdomain_name"]."/html");
        }

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes'");
}

?>