<?

require_once "$dtcshared_path/dtcrm/opensrs.php";

function registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1)	return $SRScookie_errno;

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
	return SRSregistry_check_availability($domain_name);
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
