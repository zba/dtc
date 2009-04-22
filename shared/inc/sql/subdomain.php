<?php

if(isset($_REQUEST["subdomaindefault"]) && $_REQUEST["subdomaindefault"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	if(!checkSubdomainFormat($_REQUEST["subdomaindefault_name"])){
		$submit_err .= _("Incorrect sub-domain format.") ;
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		if($_REQUEST["wildcard_dns"] == "yes"){
			$wild = ",wildcard_dns='yes'";
		}else{
			$wild = ",wildcard_dns='no'";
		}
		if($_REQUEST["default_sub_server_alias"] == "yes"){
			$srvalias = ",default_sub_server_alias='yes'";
		}else{
			$srvalias = ",default_sub_server_alias='no'";
		}
		$adm_query = "UPDATE $pro_mysql_domain_table SET default_subdomain='".$_REQUEST["subdomaindefault_name"]."'".$wild.$srvalias." WHERE name='$edit_domain' LIMIT 1;";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\"");

		updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named='yes'");
	}
}

?>
