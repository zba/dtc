<?php

// Return the ID of the registrar module in the $registry_api_modules
// depending on $domain
// Returns false if not found
function find_registry_id($domain){
	global $registry_api_modules;
	global $pro_mysql_registrar_domains_table;

	$exten = find_domain_extension($domain);
	if($exten === FALSE){
		return FALSE;
	}
	$q = "SELECT * FROM $pro_mysql_registrar_domains_table WHERE tld='".$exten."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		return FALSE;
	}
	$a = mysql_fetch_array($r);
	$registrar_name = $a["registrar"];
	$n = sizeof($registry_api_modules);
	for($i=0;$i<$n;$i++){
		if($registry_api_modules[$i]["name"] == $registrar_name){
			return $i;
		}
	}
	return FALSE;
}

function registry_check_availability($domain_name){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_check_availability"]($domain_name);
//	return SRSregistry_check_availability($domain_name);
}

function registry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers,$new_user="yes"){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	$ret = $registry_api_modules[$id]["registry_register_domain"]($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers,$new_user);
	return $ret;
//	return SRSregistry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers);
}

function registry_get_domain_price($domain_name,$period){
	global $pro_mysql_registrar_domains_table;
	$exten = find_domain_extension($domain_name);
	if($exten === FALSE){
		return FALSE;
	}
	$q = "SELECT * FROM $pro_mysql_registrar_domains_table WHERE tld='".$exten."';";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		return FALSE;
	}
	$a = mysql_fetch_array($r);
	//SRSregistry_get_domain_price($domain_name,$period);
	return $a["price"];
}

function registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_add_nameserver"]($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers);
}

function registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_modify_nameserver"]($adm_login,$adm_pass,$subdomain,$domain_name,$ip);
//	return SRSregistry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip);
}

function registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_delete_nameserver"]($adm_login,$adm_pass,$subdomain,$domain_name);
//	return SRSregistry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name);
}

function registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_update_whois_info"]($adm_login,$adm_pass,$domain_name,$contacts);
}

function registry_get_whois($domain_name){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_get_whois"]($domain_name);
}

function registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns){
	global $registry_api_modules;
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_get_whois"]($adm_login,$adm_pass,$domain_name,$dns);
}

function registry_check_transfer($domain){
	global $registry_api_modules;
	$id = find_registry_id($domain);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules[$id]["registry_check_transfer"]($domain);
	//return SRSregistry_check_transfer($domain);
}

function registry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period){
	return SRSregistry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period);
}

function registry_change_password($adm_login,$adm_pass,$domain,$new_pass){
	return SRSregistry_change_password($adm_login,$adm_pass,$domain);
}

?>
