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
					"name,safe_mode,sbox_protect,owner,default_subdomain,quota,max_email,max_lists,max_ftp,max_subdomain,max_ssh,primary_dns,other_dns,primary_mx,other_mx,whois,hosting,gen_unresolved_domain_alias,txt_root_entry,txt_root_entry2,catchall_email,domain_parking,registrar_password,ttl,stats_login,stats_pass,stats_subdomain,wildcard_dns,domain_parking_type");
	$dom["subdomains"] = getExDomTableData($pro_mysql_subdomain_table,"domain_name='$domain'","subdomain_name",
					"safe_mode,sbox_protect,subdomain_name,ip,register_globals,associated_txt_record,generate_vhost,ssl_ip,nameserver_for,ttl,srv_record,add_default_charset,customize_vhost");
	$pop_access = getExDomTableData($pro_mysql_pop_table,"mbox_host='$domain'","id",
					"id,home,passwd,crypt,redirect1,redirect2,localdeliver,vacation_flag,bounce_msg,vacation_text,spam_mailbox_enable,quota_size,quota_files,quota_couriermaildrop");
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

	// Export all domains
	$q = "SELECT name FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$dom_ar["domains"][ $a["name"] ] = getDomainData($a["name"],$adm_login);
	}

	// Get the MySQL user infos
	$q = "SELECT DISTINCT User,Password FROM mysql.user WHERE dtcowner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		if($i == 0){
			$dom_ar["mysql"] = array();
		}
		$a = mysql_fetch_array($r);
		$dom_ar["mysql"][ $a["User"] ]["password"] = $a["Password"];

		$q2 = "SELECT DISTINCT Db FROM mysql.db WHERE User='".$a["User"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		for($j=0;$j<$n2;$j++){
			$a2 = mysql_fetch_array($r2);
			if( ! isset($dom_ar["mysql"][ $a["User"] ]["dbs"])){
				$dom_ar["mysql"][ $a["User"] ]["dbs"] = array();
			}
			$dom_ar["mysql"][ $a["User"] ]["dbs"][ $a2["Db"] ] = "yes";
		}
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
			$q = "INSERT IGNORE INTO $table ($vars $added_var) VALUES ($values $added_val);";
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
		$q = "INSERT IGNORE INTO $table ($vars $added_var) VALUES ($values $added_val);";
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

	global $conf_dtc_system_uid;
	global $conf_dtc_system_gid;

	global $console;

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
	if (PEAR::isError($result)){
		echo _("Method unserialize() failed, could not import your domain configuration: ").$result->getMessage();
		return;
	}
	$dom_ar = $unserializer->getUnserializedData();
	if (PEAR::isError($dom_ar)) {
		echo _("Method getUnserializedData() failed, could not import your domain configuration: ").$dom_ar->getMessage();
		return;
	}

	// Because of an issue of the programming of older versions of DTC,
	// if there was multiple domains in the XLM file, then we have things like this,
	// as PHP assotiative array, once Unserialize() is done:
	// <dtc-export-file version="0.1">
	//   <domains>
        //      <item>
	//         <example.com>
	//           ........
	//         </example.com>
	//      </item>
	//   </domains
	// </dtc-export-file>
	// the below code will remove the <item> thing that is on the way,
	// and quite annoying for using array_keys().
	if( isset($dom_ar["domains"]["item"]) ){
		$nbr_domains = sizeof($dom_ar["domains"]["item"]);
		$my_domains = array();
		for($doms=0;$doms<$nbr_domains;$doms++){
			$mykey = array_keys($dom_ar["domains"]["item"][$doms]);
			$my_domains["domains"][ $mykey[0] ] = $dom_ar["domains"]["item"][$doms][$mykey[0]];
		}
		$dom_ar = $my_domains;
		$all_domains = array_keys($dom_ar["domains"]);
	}else{
		$all_domains = array_keys($dom_ar["domains"]);
		$nbr_domains = sizeof($all_domains);
	}
	// Iterate on all domains of the file (if there's only one, it's fine too...)
	for($doms=0;$doms<$nbr_domains;$doms++){
		// We will work on each domains one by one
		$dom_name = $all_domains[$doms];
		if($dom_name == "" || $dom_name == "Array"){
			echo _("Domain name is empty in your export file: could not import domain number ").$doms;
			return;
		}
		$console .= "Importing domain: $dom_name<br>";
		
		$cur_dom = $dom_ar["domains"][$dom_name];
		$dom_name = $cur_dom["domain_config"]["name"];

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
			"safe_mode,sbox_protect,default_subdomain,quota,max_email,max_lists,max_ftp,max_subdomain,max_ssh,primary_dns,other_dns,primary_mx,other_mx,whois,hosting,gen_unresolved_domain_alias,txt_root_entry,txt_root_entry2,catchall_email,domain_parking,registrar_password,ttl,stats_login,stats_pass,stats_subdomain,wildcard_dns,domain_parking_type");
		recreateAllRows($pro_mysql_subdomain_table,"domain_name='$dom_name'",$cur_dom["subdomains"],
			"safe_mode,sbox_protect,subdomain_name,ip,register_globals,associated_txt_record,generate_vhost,ssl_ip,nameserver_for,ttl,srv_record,add_default_charset,customize_vhost",
			",domain_name",",'$dom_name'");
		recreateAllRows($pro_mysql_pop_table,"mbox_host='$dom_name'",$cur_dom["mailboxes"],
			"id,home,passwd,crypt,redirect1,redirect2,localdeliver,vacation_flag,bounce_msg,vacation_text,spam_mailbox_enable,quota_size,quota_files,quota_couriermaildrop",
			",mbox_host",",'$dom_name'");
		recreateAllRows($pro_mysql_mailaliasgroup_table,"domain_parent='$dom_name'",$cur_dom["alias_group"],
			"id,delivery_group",",domain_parent",",'$dom_name'");
		recreateAllRows($pro_mysql_list_table,"domain='$dom_name'",$cur_dom["lists"],
			"name,owner,spammode,webarchive",",domain",",'$dom_name'");
		recreateAllRows($pro_mysql_ftp_table,"hostname='$dom_name'",$cur_dom["ftp"],
			"login,password,homedir",",hostname",",'$dom_name'");
		recreateAllRows($pro_mysql_ssh_table,"hostname='$dom_name'",$cur_dom["ssh"],
			"login,crypt,password,homedir",",hostname",",'$dom_name'");
		// Fixes the UID / GID for ssh, ftp and email accounts
		$q = "UPDATE $pro_mysql_pop_table SET uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid' WHERE mbox_host='$dom_name';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$q = "UPDATE $pro_mysql_ftp_table SET uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid' WHERE hostname='$dom_name';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$q = "UPDATE $pro_mysql_ssh_table SET uid='$conf_dtc_system_uid',gid='$conf_dtc_system_gid' WHERE hostname='$dom_name';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		// Fixes the pop_access fullemail field.
		$q = "UPDATE $pro_mysql_pop_table SET fullemail = concat( `id`,  '@', `mbox_host` ) WHERE mbox_host='$dom_name';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	if( isset($dom_ar["mysql"])){
		$n_user = sizeof($dom_ar["mysql"]);
		$console .= _("Number of database users in this import: ").$n_user."<br>";
		$musers = array_keys($dom_ar["mysql"]);
		for($i=0;$i<$n_user;$i++){
			$username = $musers[$i];
			$console .= _("Importing database username: ").$username."<br>";
			unset($user);
			$user = $dom_ar["mysql"][ $musers[$i] ];
			$password = $user["password"];
			$q = "INSERT IGNORE INTO mysql.user
			(Host,User,Password,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Reload_priv,Shutdown_priv,Process_priv,File_priv,
			Grant_priv,References_priv,Index_priv,Alter_priv,Show_db_priv,Super_priv,Create_tmp_table_priv,Lock_tables_priv,
			Execute_priv,Repl_slave_priv,Repl_client_priv,Create_view_priv,Show_view_priv,Create_routine_priv,
			Alter_routine_priv,Create_user_priv,dtcowner)
			VALUES ('%','$username','$password','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N',
			'N','N','N','N','N','N','N','N','$adm_login');";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

			$q = "INSERT IGNORE INTO mysql.user
			(Host,User,Password,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Reload_priv,Shutdown_priv,Process_priv,File_priv,
			Grant_priv,References_priv,Index_priv,Alter_priv,Show_db_priv,Super_priv,Create_tmp_table_priv,Lock_tables_priv,
			Execute_priv,Repl_slave_priv,Repl_client_priv,Create_view_priv,Show_view_priv,Create_routine_priv,
			Alter_routine_priv,Create_user_priv,dtcowner)
			VALUES ('localhost','$username','$password','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N',
			'N','N','N','N','N','N','N','N','$adm_login');";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

			if( isset($user["dbs"])){
				$n_db = sizeof($user["dbs"]);
				$console .= _("Number of databases owned by user")." ".$username.": ".$n_db."<br>";
				$mdbs = array_keys($user["dbs"]);
				for($j=0;$j<$n_db;$j++){
					$db = $mdbs[$j];
					$q = "INSERT IGNORE INTO mysql.db (Host,Db,User,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Grant_priv,
					References_priv,Index_priv,Alter_priv,Create_tmp_table_priv,Lock_tables_priv,Create_view_priv,
					Show_view_priv,Create_routine_priv,Alter_routine_priv,Execute_priv)
					VALUES('%','$db','$username','Y','Y','Y','Y','Y','Y','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');";
					$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

					$q = "INSERT IGNORE INTO mysql.db (Host,Db,User,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Grant_priv,
					References_priv,Index_priv,Alter_priv,Create_tmp_table_priv,Lock_tables_priv,Create_view_priv,
					Show_view_priv,Create_routine_priv,Alter_routine_priv,Execute_priv)
					VALUES('localhost','$db','$username','Y','Y','Y','Y','Y','Y','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');";
					$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				}
			}
		}
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	}
	return;
}

?>
