<?php

// http://dtc.iglobalwall.com/dtc/index.php?adm_login=dtc&addrlink=test.net&adm_pass=root99&domain_gen_unresolv_alias=yes&
if($_REQUEST["change_unresolv_alias"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if($_REQUEST["domain_gen_unresolv_alias"] == "yes"){
		$flag = "yes";
	}else{
		$flag = "no";
	}

	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET gen_unresolved_domain_alias='$flag' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes'");
}


?>
