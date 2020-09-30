<?php 
//require("$dtcshared_path/inc/sql/domain_stats_strings.php");

///////////////////////////////////////////////
// Stats account submition to mysql database //
///////////////////////////////////////////////
//action=add_Stats_login&stats_login=statslogin&stats_password=pass&stats_subdomains=
$txt_dbsql_password_are_made_only_with_standards_chars_and_numbers_and_size = _("Login and Password can only contain standard chars and numbers and must have a length of 4 or more.") . "<br>\n";

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_stats_login"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	
	if(isset($_REQUEST["stats_subdomain"])){
		$stats_subdomain_flag = "yes";
	}else{
		$stats_subdomain_flag = "no";
	}
	
	if(!isDTCPassword($_REQUEST["stats_login"]) || !isDTCPassword($_REQUEST["stats_pass"])){
		$submit_err .= $txt_dbsql_password_are_made_only_with_standards_chars_and_numbers_and_size;
		$commit_flag = "no";
	}
	
	if($commit_flag == "yes"){		
		$admin_path=getAdminPath($adm_login);
		$htaccess="AuthName \"Webstats Login!\" \nAuthType Basic \nAuthUserFile ".$admin_path."/".$edit_domain."/.htpasswd \nrequire valid-user";

		$q = "UPDATE $pro_mysql_domain_table SET stats_login='".$_REQUEST["stats_login"]."',stats_pass='".$_REQUEST["stats_pass"]."',stats_subdomain='$stats_subdomain_flag'  WHERE name='$edit_domain';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		// What's commented below is wrong because it shows the password in a "ps" call, so it's now replaced by crypt() and fwrite().
		// exec("$conf_htpasswd_path -cb $admin_path/$edit_domain/.htpasswd ".$_REQUEST["stats_login"]." ".$_REQUEST["stats_pass"]."");
		$encrypted = crypt($_REQUEST["stats_pass"]);
		$fp = fopen("$admin_path/$edit_domain/.htpasswd","wb");
		if($fp != NULL){
			fwrite($fp,$_REQUEST["stats_login"].":".$encrypted);
			fclose($fp);
		}
		if($stats_subdomain_flag == "yes"){	
			$q="SELECT subdomain_name,generate_vhost FROM subdomain where domain_name='".$edit_domain."' and generate_vhost='yes';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$num_rows = mysql_num_rows($r);
			for($i=0;$i<$num_rows;$i++){
				$a=mysql_fetch_array($r);
				$filename=$admin_path."/".$edit_domain."/subdomains/".$a["subdomain_name"]."/logs/.htaccess";
				$handle = fopen($filename, 'w');

				if($handle != NULL){
					if (fwrite($handle, $htaccess) === FALSE) {
					    echo "Cannot write to file ($filename)";
					    exit;
					}
					fclose($handle);
				}else{
					echo "Could not open $filename !";
				}
			}
		}else{
			$filename=$admin_path."/".$edit_domain."/subdomains/www/logs/.htaccess";
			if ( file_exists($filename)){
				$handle = fopen($filename,'w');		

				if($handle != NULL){
					if (fwrite($handle, $htaccess) === FALSE) {
				    	echo "Cannot write to file ($filename)";
				    	exit;
					}
				fclose($handle);
				}else{
					echo "Could not open file $filename !";
				}
			}
		}	
	} 
}

//action=modify_stats_login_pass&stats_login=statslogin&stats_password=pass&stats_subdomains=
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "modify_stats_login_pass"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	
	if(isset($_REQUEST["stats_subdomain"])){
		$stats_subdomain_flag = "yes";
	}else{
		$stats_subdomain_flag = "no";
	}

	$admin_path=getAdminPath($adm_login);
	$htaccess="AuthName \"Webstats Login!\" \nAuthType Basic \nAuthUserFile ".$admin_path."/".$edit_domain."/.htpasswd \nrequire valid-user";

	if(!isDTCPassword($_REQUEST["stats_login"]) || !isDTCPassword($_REQUEST["stats_pass"])){
		$submit_err .= $txt_dbsql_password_are_made_only_with_standards_chars_and_numbers_and_size;
		$commit_flag = "no";
	}	
	

	if($commit_flag == "yes"){
		$q = "UPDATE $pro_mysql_domain_table SET stats_login='".$_REQUEST["stats_login"]."',stats_pass='".$_REQUEST["stats_pass"]."',stats_subdomain='$stats_subdomain_flag'  WHERE name='$edit_domain';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		exec("$conf_htpasswd_path -cb $admin_path/$edit_domain/.htpasswd ".$_REQUEST["stats_login"]." ".$_REQUEST["stats_pass"]."");	

		if($stats_subdomain_flag == "yes"){			
			$q="SELECT subdomain_name FROM subdomain where domain_name='".$edit_domain."' and generate_vhost='yes';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$num_rows = mysql_num_rows($r);
			for($i=0;$i<$num_rows;$i++){
				$a=mysql_fetch_array($r);
				$filename=$admin_path."/".$edit_domain."/subdomains/".$a["subdomain_name"]."/logs/.htaccess";
				$handle = fopen($filename, 'w');			

				if($handle != NULL){
					if (fwrite($handle, $htaccess) === FALSE) {
					    	echo "Cannot write to file ($filename)";
					}
					fclose($handle);							
				}else{
					echo "Could not open file $filename !";
				}
			}
		}
	}else{	
		$q="SELECT subdomain_name FROM subdomain where domain_name='".$edit_domain."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$num_rows = mysql_num_rows($r);
		for($i=0;$i<$num_rows;$i++){
			$a=mysql_fetch_array($r);
			$filename=$admin_path."/".$edit_domain."/subdomains/".$a["subdomain_name"]."/logs/.htaccess";		
			
			if(file_exists($filename)){
				unlink($filename);
			}
		}
	}

/* Seems this is not to be done, it's not logic. Can this be checked???
	$filename=$admin_path."/".$edit_domain."/subdomains/www/logs/.htaccess";
	$handle = fopen($filename,'w') or die("Cannot open file $filename");
	
	if($handle != NULL){
		if (fwrite($handle, $htaccess) === FALSE) {
		    echo "Cannot write to file ($filename)";
//	    exit;
		}
	
		fclose($handle);					
		}
	}else{
		echo "Could not open file $filename !";
	}
	}
*/
}

//action=del_tats_login&stats_login=statslogin&stats_password=pass&stats_subdomains=
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "del_stats_login"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	
	if(isset($_REQUEST["stats_subdomain"])){
		$stats_subdomain_flag = "yes";
	}else{
		$stats_subdomain_flag = "no";
	}
	$admin_path=getAdminPath($adm_login);
	
	if(!isDTCPassword($_REQUEST["stats_login"])){
		$submit_err .= $txt_dbsql_password_are_made_only_with_standards_chars_and_numbers_and_size;
		$commit_flag = "no";
	}
	
	if($commit_flag == "yes"){
		$q = "UPDATE $pro_mysql_domain_table SET stats_login='',stats_pass='',stats_subdomain='no'  WHERE name='$edit_domain';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());	
		if($stats_subdomain_flag == "yes"){
			$q="SELECT subdomain_name FROM subdomain where domain_name='".$edit_domain."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$num_rows = mysql_num_rows($r);
			for($i=0;$i<$num_rows;$i++){
				$a=mysql_fetch_array($r);
				$filename=$admin_path."/".$edit_domain."/subdomains/".$a["subdomain_name"]."/logs/.htaccess";
				if(file_exists($filename)){
					unlink($filename);
				}			
			}
	}else{
		$filename=$admin_path."/".$edit_domain."/subdomains/www/logs/.htaccess";
		if(file_exists($filename)){
			unlink($filename);
		}
	}	
	$htaccess="$admin_path/$edit_domain/.htpasswd";
	if(file_exists($htaccess)){
		unlink($htaccess);
	}
	}
}

?>
