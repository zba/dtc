<?php


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


?>
