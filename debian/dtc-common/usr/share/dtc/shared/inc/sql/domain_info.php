<?php

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "export_my_account"){
	checkLoginPass($adm_login,$adm_pass);
	$file_name = "dtcuser_".$adm_login.".dtc.xml";
	$xml = exportAllDomain($adm_login);
	header('Content-type: application/dtc+xml');
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	echo($xml);
	die();
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "export_domain"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	$file_name = $edit_domain.'.dtc.xml';
	$xml = exportDomain($edit_domain,$adm_login);
	header('Content-type: application/dtc+xml');
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	echo($xml);
	die();
}

if(isset($_REQUEST["set_domain_parcking"]) && $_REQUEST["set_domain_parcking"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if($_REQUEST["domain_parking_value"] != "no-parking"){
		// Check for mysql insertion and that the user owns the domain he wants to send it's domain to parking to
		if(!isHostname($_REQUEST["domain_parking_value"]) || $_REQUEST["domain_parking_value"] == $edit_domain){
			if($_REQUEST["domain_parking_value"] == $edit_domain){
				echo "You cannot set a domain to be parked to itself";
			}else{
				echo "Not a hostname: ".$_REQUEST["domain_parking_value"];
			}
			$set_to = "no-parking";
		}else{
			checkLoginPassAndDomain($adm_login,$adm_pass,$_REQUEST["domain_parking_value"]);
			// Check that the aimed domain is not in parking as well: this could happen only with "hacking the URL", but who knows...
			$q = "SELECT domain_parking FROM $pro_mysql_domain_table WHERE name='".$_REQUEST["domain_parking_value"]."' AND domain_parking='no-parking'";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				echo "Target domain ".$_REQUEST["domain_parking_value"]." is already in parking ".__LINE__." file ".__FILE__;
				$set_to = "no-parking";
			}else{
				$set_to = $_REQUEST["domain_parking_value"];
			}
		}
	}else{
		$set_to = "no-parking";
	}

	switch ($_REQUEST["domain_parking_type"]) {
		case "same_docroot":
			$domain_parking_type = "same_docroot";
			break;
		case "serveralias":
			$domain_parking_type = "serveralias";
			break;
		default:
			// redirect is the sql default
			$domain_parking_type = "redirect";
	}


	// Update the flag so we regenerate the serial for bind
	$domupdate_query = "UPDATE $pro_mysql_domain_table SET domain_parking='$set_to', gen_unresolved_domain_alias='no',domain_parking_type='$domain_parking_type' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes'");
}

// http://dtc.iglobalwall.com/dtc/index.php?adm_login=dtc&addrlink=test.net&adm_pass=root99&domain_gen_unresolv_alias=yes&
if(isset($_REQUEST["change_unresolv_alias"]) && $_REQUEST["change_unresolv_alias"] == "Ok"){
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
