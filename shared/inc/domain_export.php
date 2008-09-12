<?php

require_once 'XML/Serializer.php';
require_once "XML/Unserializer.php";

function getExDomTableData($table,$w_cond,$key,$vars){
	$my_ar = array();

	$vars_ar = explode(",",$vars);
	$n_vars = sizeof($vars_ar);

	$q = "SELECT $key,$vars FROM $table WHERE $w_cond;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$my_ar[ $i ][ $key ] = $a[ $key ];
		for($j=0;$j<$n_vars;$j++){
			$my_ar[ $i ][ $vars_ar[$j] ] = $a[ $vars_ar[$j] ];
		}
	}
	return $my_ar;
}

function getExDomRowValues($table,$w_cond,$vars){
	$my_ar = array();

	$vars_ar = explode(",",$vars);
	$n_vars = sizeof($vars_ar);

	$q = "SELECT $vars FROM $table WHERE $w_cond;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find raw line when calling $q ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	for($j=0;$j<$n_vars;$j++){
		$my_ar[ $vars_ar[$j] ] = $a[ $vars_ar[$j] ];
	}
	return $my_ar;
}

function removePathFromArray($ftp_array,$adm_login,$field){
	$adm_path = getAdminPath($adm_login);
	$adm_path_size = strlen($adm_path);
	$keys = array_keys($ftp_array);
	$n = sizeof($ftp_array);
	for($i=0;$i<$n;$i++){
		$ftp_array[ $keys[$i] ][$field] = substr($ftp_array[ $keys[$i] ][$field],$adm_path_size);
	}
	return $ftp_array;
}

function getDomainData($domain,$adm_login){
	global $pro_mysql_pop_table;
	global $pro_mysql_mailaliasgroup_table;
	global $pro_mysql_list_table;
	global $pro_mysql_ftp_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_ssh_table;
	global $pro_mysql_subdomain_table;

	unset($dom);
	$dom = array();

	$dom["domain_config"] = getExDomRowValues($pro_mysql_domain_table,"name='$domain'",
					"safe_mode,sbox_protect,owner,default_subdomain,quota,max_email,max_lists,max_ftp,max_subdomain,max_ssh,ip_addr,backup_ip_addr,primary_dns,other_dns,primary_mx,other_mx,whois,hosting,gen_unresolved_domain_alias,txt_root_entry,txt_root_entry2,catchall_email,domain_parking,registrar_password,ttl,stats_login,stats_pass,stats_subdomain,wildcard_dns,domain_parking_type");
	$dom["subdomains"] = getExDomTableData($pro_mysql_subdomain_table,"domain_name='$domain'","subdomain_name",
					"safe_mode,sbox_protect,subdomain_name,ip,register_globals,login,pass,associated_txt_record,generate_vhost,ssl_ip,nameserver_for,ttl,srv_record,add_default_charset,customize_vhost");
	$pop_access = getExDomTableData($pro_mysql_pop_table,"mbox_host='$domain'","id",
					"id,home,passwd,crypt,redirect1,redirect2,localdeliver,vacation_flag,bounce_msg,vacation_text,spam_mailbox_enable");
	$dom["mailboxes"] = removePathFromArray($pop_access,$adm_login,"home");
	$dom["alias_group"] = getExDomTableData($pro_mysql_mailaliasgroup_table,"domain_parent='$domain'","id",
					"delivery_group");
	$dom["lists"] = getExDomTableData($pro_mysql_list_table,"domain='$domain'","id",
					"name,owner,spammode,webarchive");
	$ftp_access = getExDomTableData($pro_mysql_ftp_table,"hostname='$domain'","login",
					"password,homedir,hostname");
	$dom["ftp"] = removePathFromArray($ftp_access,$adm_login,"homedir");
	$ssh_access = getExDomTableData($pro_mysql_ssh_table,"hostname='$domain'","login",
					"crypt,password,homedir,hostname");
	$dom["ssh"] = removePathFromArray($ssh_access,$adm_login,"homedir");
	return $dom;
}


function exportDomain($domain_name,$adm_login){
	// Get the domain info
	$dom_ar = array(
		"domains" => array(
			$domain_name => getDomainData($domain_name,$adm_login)
			)
		);

	// Serialize into a XML document
	$options = array(
		"indent"          => "\t",
		"linebreak"       => "\n",
		"addDecl"         => true,
		"encoding"        => "UTF-8",
		"rootAttributes"  => array("version" => "0.1"),
		"rootName"        => "dtc-export-file",
		"defaultTagName"  => "item",
		"attributesArray" => "_attributes"
	);
	$serializer = new XML_Serializer($options);
	$serializer->serialize($dom_ar);
	$xml = $serializer->getSerializedData();
	return $xml;
}

function exportAllDomain($adm_login){
	global $pro_mysql_domain_table;

	$dom_ar = array(
		"domains" => array()
		);

	$q = "SELECT name FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$dom_ar["domains"][] = array( $a["name"] => getDomainData($a["name"],$adm_login) );
	}
	// Serialize into a XML document
	$options = array(
		"indent"          => "\t",
		"linebreak"       => "\n",
		"addDecl"         => true,
		"encoding"        => "UTF-8",
		"rootAttributes"  => array("version" => "0.1"),
		"rootName"        => "dtc-export-file",
		"defaultTagName"  => "item",
		"attributesArray" => "_attributes"
	);
	$serializer = new XML_Serializer($options);
	$serializer->serialize($dom_ar);
	$xml = $serializer->getSerializedData();
	return $xml;
}

function updateRowValue($table,$w_cond,$ar,$vars){
	$vars_ar = explode(",",$vars);
	$nbr_vars = sizeof($vars_ar);
	$sets = "";
	for($i=0;$i<$nbr_vars;$i++){
		// The if() bellow makes it possible to do imports between DTC versions
		if( isset( $ar[ $vars_ar[$i] ] ) ){
			if($i != 0){
				$sets .= ",";
			}
			$sets .= $vars_ar[$i] . "='". $ar[ $vars_ar[$i] ] ."'";
		}
	}
	$q = "UPDATE $table SET $sets WHERE $w_cond;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

function recreateAllRows($table,$delete_cond,$ar,$vars,$added_var,$added_val){
	// Delete old records if any...
	$q = "DELETE FROM $table WHERE $delete_cond;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	$vars_ar = explode(",",$vars);
	$nbr_vars = sizeof($vars_ar);

	if( isset($ar["item"][0]) ){
		$n = sizeof($ar["item"]);
		for($j=0;$j<$n;$j++){
			$cur_item = $ar["item"][$j];

			$vars = "";
			$values = "";
			for($i=0;$i<$nbr_vars;$i++){
				if( isset( $cur_item[ $vars_ar[$i] ] ) ){
					if($i != 0){
						$vars .= ",";
						$values .= ",";
					}
					$vars .= $vars_ar[$i];
					$values .= "'" . $cur_item[ $vars_ar[$i] ] ."'";
				}
			}
			$q = "INSERT INTO $table ($vars $added_var) VALUES ($values $added_val);";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		}
	}elseif(isset($ar["item"])){
		$vars = "";
		$values = "";
		$cur_item = $ar["item"];
		for($i=0;$i<$nbr_vars;$i++){

			if( isset( $cur_item[ $vars_ar[$i] ] ) ){
				if($i != 0){
					$vars .= ",";
					$values .= ",";
				}
				$vars .= $vars_ar[$i];
				$values .= "'" . $cur_item[ $vars_ar[$i] ] ."'";
			}
		}
		$q = "INSERT INTO $table ($vars $added_var) VALUES ($values $added_val);";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	}
}

function addOwnerPathToArray($ar,$adm_path,$item_name,$fld_name){
	if( !isset($ar[$item_name]["item"]) ){
		return $ar;
	}

	if( !isset($ar[$item_name]["item"][0]) ){
		$ar[$item_name]["item"][$fld_name] = $adm_path . $ar[$item_name]["item"][$fld_name];
	}else{
		$n = sizeof($ar[$item_name]["item"]);
		for($i=0;$i<$n;$i++){
			$ar[$item_name]["item"][$i][$fld_name] = $adm_path . $ar[$item_name]["item"][$i][$fld_name];
		}
	}
	return $ar;
}

function domainImport($path_from,$adm_login,$adm_pass){
	global $pro_mysql_domain_table;
	global $pro_mysql_pop_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_pop_table;
	global $pro_mysql_mailaliasgroup_table;
	global $pro_mysql_list_table;
	global $pro_mysql_ftp_table;
	global $pro_mysql_ssh_table;

	// Read the file
	$fp = fopen($path_from, "r+");
	$xml_content = fread($fp, filesize($path_from));
	fclose($fp);

	// Unserialize
	$options = array(
                    'tagMap'            => array( 'util' => 'XML_Util' ),
                    'classAttribute'    => '_classname'
                );
	$unserializer = new XML_Unserializer($options);
	$result = $unserializer->unserialize($xml_content);
	$dom_ar = $unserializer->getUnserializedData();

	// Iterate on all domains of the file
	if( isset($dom_ar["domains"]["item"]) ){
		$all_domains = array_keys($dom_ar["domains"]["item"][$dom]);
		$nbr_domains = sizeof($dom_ar["domains"]["item"]);
	}else{
		$all_domains = array_keys($dom_ar["domains"]);
		$nbr_domains = sizeof($all_domains);
	}
	for($doms=0;$doms<$nbr_domains;$doms++){
		// We will work on each domains one by one
		if( isset($dom_ar["domains"]["item"]) ){
			$all_domains = array_keys($dom_ar["domains"]["item"][$doms]);
			$dom_name = $all_domains[0];
			$cur_dom = $dom_ar["domains"]["item"][$doms][$dom_name];
		}else{
			$dom_name = $all_domains[$doms];
			$cur_dom = $dom_ar["domains"][$dom_name];
		}

		// Check if the domain exists, if not, add it to the user
		$q = "SELECT * FROM $pro_mysql_domain_table WHERE name='$dom_name';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 0){
			addDomainToUser($adm_login,$adm_pass,$dom_name);
		}

		// Add owner path to ftp & ssh accounts
		$adm_path = getAdminPath($adm_login);
		$cur_dom = addOwnerPathToArray($cur_dom,$adm_path,"ftp","homedir");
		$cur_dom = addOwnerPathToArray($cur_dom,$adm_path,"ssh","homedir");
		$cur_dom = addOwnerPathToArray($cur_dom,$adm_path,"mailboxes","home");

		// Reimport all the stuff
		updateRowValue($pro_mysql_domain_table,"name='$dom_name'",$cur_dom["domain_config"],
			"safe_mode,sbox_protect,owner,default_subdomain,quota,max_email,max_lists,max_ftp,max_subdomain,max_ssh,ip_addr,backup_ip_addr,primary_dns,other_dns,primary_mx,other_mx,whois,hosting,gen_unresolved_domain_alias,txt_root_entry,txt_root_entry2,catchall_email,domain_parking,registrar_password,ttl,stats_login,stats_pass,stats_subdomain,wildcard_dns,domain_parking_type");
		recreateAllRows($pro_mysql_subdomain_table,"domain_name='$dom_name'",$cur_dom["subdomains"],
			"safe_mode,sbox_protect,subdomain_name,ip,register_globals,login,pass,associated_txt_record,generate_vhost,ssl_ip,nameserver_for,ttl,srv_record,add_default_charset,customize_vhost",
			",domain_name",",'$dom_name'");
		recreateAllRows($pro_mysql_pop_table,"mbox_host='$dom_name'",$cur_dom["mailboxes"],
			"id,home,passwd,crypt,redirect1,redirect2,localdeliver,vacation_flag,bounce_msg,vacation_text,spam_mailbox_enable",
			",mbox_host",",'$dom_name'");
		recreateAllRows($pro_mysql_mailaliasgroup_table,"domain_parent='$dom_name'",$cur_dom["alias_group"],
			"id,delivery_group",",domain_parent",",'$dom_name'");
		recreateAllRows($pro_mysql_list_table,"domain='$dom_name'",$cur_dom["lists"],
			"name,owner,spammode,webarchive",",domain",",'$dom_name'");
		recreateAllRows($pro_mysql_ftp_table,"hostname='$dom_name'",$cur_dom["ftp"],
			"password,homedir,hostname",",hostname='$dom_name'",",'$dom_name'");
		recreateAllRows($pro_mysql_ssh_table,"hostname='$dom_name'",$cur_dom["ssh"],
			"crypt,password,homedir,hostname",",hostname",",'$dom_name'");
	}
	return;
}

?>
