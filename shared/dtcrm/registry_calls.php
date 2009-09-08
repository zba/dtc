<?php

require_once "$dtcshared_path/dtcrm/opensrs.php";

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

function registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1) return $SRScookie_errno;
	$fqdn = $subdomain. "." .$domain_name;

	$srs_result = SRSregistry_create_nameserver($cookie,$fqdn,$ip);
	if($srs_result["is_success"] != 1 && $srs_result["response_code"] != 485){
		SRSdelete_cookie($cookie);
		return $srs_result;
	}

	$srs_result = SRSregistry_add_nameserver($fqdn);
	SRSdelete_cookie($cookie);
	return $srs_result;
}

function registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	return SRSregistry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip);
}

function registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
	return SRSregistry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name);
}

function registry_check_availability($domain_name){
	$id = find_registry_id($domain_name);
	if($id === FALSE){
		return FALSE;
	}
	return $registry_api_modules["registry_check_availability"]($domain_name);
//	return SRSregistry_check_availability($domain_name);
}

function registry_get_domain_price($domain_name,$period){
	return SRSregistry_get_domain_price($domain_name,$period);
}

function registry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers){
	return SRSregistry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers);
}

function SRSregistry_update_whois_infoz($adm_login,$adm_pass,$domain_name,$contacts){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1) return $SRScookie_errno;

	// Make the contact set
	$owner = SRScreate_contact_array($contacts["owner"]);
	$billing = SRScreate_contact_array($contacts["billing"]);
	$admin = SRScreate_contact_array($contacts["admin"]);
	$contact_set = array(
		'owner' => $owner,
		'billing' => $billing,
		'admin' => $admin);

	$cmd = array(
		'protocol' => "XCP",
		'action' => "modify",
		'cookie' => $cookie,
		'object' => "domain",
		'attributes' => array(
			'data' => "contact_info",
			'org_name' => "GPLHost",
			'affect_domains' => 0,
			'contact_set' => $contact_set
			)
		);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	SRSdelete_cookie($cookie);
	return $srs_result;
}

function registry_update_whois_infoz($adm_login,$adm_pass,$domain_name,$contacts){
	return SRSregistry_update_whois_infoz($adm_login,$adm_pass,$domain_name,$contacts);
}

function SRScreate_dns_array_from_piped_list($dns){
	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;

	$dns_array = explode("|",$dns);
	$nbr_other_dns = sizeof($dns_array);

	if($dns_array[0] == "default"){
		$dns_array[0] = $conf_addr_primary_dns;
	}
	if($dns_array[1] == "default"){
		$dns_array[1] = $conf_addr_secondary_dns;
	}
	for($z=0;$z<sizeof($dns_array);$z++){
		$nameserver_list[$z] = array(
			"sortorder" => $z,
			"action" => "update",
			"name" => $dns_array[$z]);
	}
	return $nameserver_list;
}

function SRSregistry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1) return $SRScookie_errno;

	$dns_array = SRScreate_dns_array_from_piped_list($dns);
	// Make the DNS array
	$cmd = array(
		'protocol' => "XCP",
		'action' => "modify",
		'cookie' => $cookie,
		'object' => "domain",
		'attributes' => array(
			'data' => "nameserver_list",
			'org_name' => "GPLHost",
			'affect_domains' => 0,
			'nameserver_list' => $dns_array
			)
		);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	SRSdelete_cookie($cookie);
	return $srs_result;
}

function registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns){
	return SRSregistry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns);
}

function SRSregistry_check_transfer($domain){
	$cmd = array(
		'protocol' => "XCP",
		'action' => "check_transfer",
		'object' => "domain",
		'attributes' => array(
			'domain' => $domain,
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	return $srs_result;
}

function registry_check_transfer($domain){
	return SRSregistry_check_transfer($domain);
}
// Returns:
// request_address:
// transferable: 0 = no, 1 = yes, if 0 see "reason" text value.
// status:
//	- pending_owner: awaiting approval by domain's admin contact
//		(every transfer begins with this status) if approval
//		is not given within five days, the transfer is cancelled
//		by OpenSRS.
//	- pending_admin: waiting for approval by OpenSRS support staff
//		(most transfers go through this stage, because of compliance
//		policies).
//	- pending_registry: awaiting registry approval (the transfer will
//		be cancelled if the registry fails to approve it within 9
//		days).
//	- completed: the transfer has been completed successfully.
//	- cancelled: the Reseller or OpenSRS has stopped the transfer.
//	- undef: no transfer exists for this domain.
// Please note that resellers can only request status for transfers that were
// initiated by them (any checks for transfers initiated by other resellers
// will return 'undef').

function SRSregistry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period){
	$cmd = array(
		'protocol' => "XCP",
		'action' => "renew",
		'object' => "domain",
		'handle' => "process",
		'attributes' => array(
			'domain' => $domain,
			'currentexpirationyear' => $curent_year_expir,
			'period' => $period,
			'auto_renew' => 0,
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	return $srs_result;
}

function registry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period){
	return SRSregistry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period);
}

function SRSregistry_change_password($adm_login,$adm_pass,$domain,$new_pass){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1) return $SRScookie_errno;
	$cmd = array(
		'protocol' => "XCP",
		'action' => "change",
		'object' => "password",
		'cookie' => $cookie,
		'attributes' => array(
			'reg_password' => 'mynewpassword'
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	SRSdelete_cookie($cookie);
	return $srs_result;
}

function registry_change_password($adm_login,$adm_pass,$domain,$new_pass){
	return SRSregistry_change_password($adm_login,$adm_pass,$domain);
}

?>
