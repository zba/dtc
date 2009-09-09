<?php

require("$dtcshared_path/dtcrm/modules/tucows/openSRS_base.php");
require("$dtcshared_path/dtcrm/modules/tucows/srs_base.php");

function SRSregistry_create_nameserver($cookie,$fqdn,$ip){
	$cmd = array(
		'protocol' => "XCP",
		'action' => "create",
		'object' => "nameserver",
		'cookie' => $cookie,
		'attributes' => array(
			'name' => $fqdn,
			'ipaddress' => $ip
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	return $srs_result;
}

function tucows_registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1) return $SRScookie_errno;
	$fqdn = $subdomain. "." .$domain_name;

	$srs_result = SRSregistry_create_nameserver($cookie,$fqdn,$ip);
	if($srs_result["is_success"] != 1 && $srs_result["response_code"] != 485){
		SRSdelete_cookie($cookie);
		return $srs_result;
	}

	$srs_result = SRSregistry_add_nameserver($cookie,$fqdn,$ip);
	SRSdelete_cookie($cookie);
	return $srs_result;
}

/*
function SRSregistry_add_nameserver($fqdn){
	$cmd = array(
		'action' => 'registry_add_ns',
		'object' => 'nameserver',
		'attributes' => array(
			'fqdn' => $fqdn,
			'tld' => '.com',
			'all' => 1
			)
		);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	return $srs_result;
}

function SRSregistry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1) return $SRScookie_errno;

	$fqdn = $subdomain. "." .$domain_name;

	$cmd = array(
		'protocol' => "XCP",
		'action' => "modify",
		'object' => "nameserver",
		'cookie' => $cookie,
		'attributes' => array(
			'name' => $fqdn,
			'ipaddress' => $ip
			)
		);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	SRSdelete_cookie($cookie);
	return $srs_result;
}

function SRSregistry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
	global $SRScookie_errno;
	if(($cookie = SRSget_cookie($adm_login,$adm_pass,$domain_name)) == -1) return $SRScookie_errno;

	$cmd = array(
		'protocol' => "XCP",
		'action' => "delete",
		'object' => "nameserver",
		'cookie' => $cookie,
		'attributes' => array(
			name => $subdomain . "." .$domain_name,
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	SRSdelete_cookie($cookie);
	return $srs_result;
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

*/

function tucows_registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
}

function tucows_registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
}

function tucows_registry_check_availability($domain_name){
}

function tucows_registry_get_domain_price($domain_name,$period){
}

function tucows_registry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers){
}

function tucows_registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts){
}

function tucows_registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns){
}

function tucows_registry_check_transfer($domain){
}

function tucows_registry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period){
}

function tucows_registry_change_password($adm_login,$adm_pass,$domain,$new_pass){
}

$configurator = array(
	"title" => _("Tucows configuration"),
	"action" => "configure_tucows_editor",
	"forward" => array("rub","sousrub"),
	"cols" => array(
		"srs_crypt" => array(
			"legend" => _("Type of encryption for connecting to Tucows server: "),
			"type" => "radio",
			"values" => array("DES","BLOWFISH")),
		"srs_enviro" => array(
			"legend" => _("Use the LIVE server (and not the test one) :"),
			"type" => "radio",
			"values" => array("LIVE","TEST")),
		"srs_user" => array(
			"legend" => _("Your SRS username:"),
			"type" => "text",
			"size" => "30"),
		"srs_test_key" => array(
			"legend" => _("Your key to access the test server:"),
			"type" => "text",
			"size" => "50"),
		"srs_live_key" => array(
			"legend" => _("Your key to access the LIVE server:"),
			"type" => "text",
			"size" => "50")));

$registry_api_modules[] = array(
"name" => "tucows",
"configure_descriptor" => $configurator,
"registry_check_availability" => "tucows_registry_check_availability",
"registry_add_nameserver" => "tucows_registry_add_nameserver",
"registry_modify_nameserver" => "tucows_registry_modify_nameserver",
"registry_delete_nameserver" => "tucows_registry_delete_nameserver",
"registry_get_domain_price" => "tucows_registry_get_domain_price",
"registry_register_domain" => "tucows_registry_register_domain",
"registry_update_whois_info" => "tucows_registry_update_whois_info",
"registry_update_whois_dns" => "tucows_registry_update_whois_dns",
"registry_check_transfer" => "tucows_registry_check_transfer",
"registry_renew_domain" => "tucows_registry_renew_domain",
"registry_change_password" => "tucows_registry_change_password"
);

?>
