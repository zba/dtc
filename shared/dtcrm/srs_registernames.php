<?php

function SRSregistry_check_availability($domain_name){
	$O = new openSRS('test','XCP');
	$cmd = array(
		'action' => 'lookup',
		'object' => 'domain',
		'attributes' => array(
			'domain' => $domain_name,
			'affiliate_id'  => '12345'
			)
		);
	$srs_result = $O->send_cmd($cmd);
	return $srs_result;
}

function SRSregistry_get_domain_price($domain_name,$period){
	$O = new openSRS('test','XCP');
	$cmd = array(
		protocol => "XCP",
		action => "get_price",
		object => "domain",
		attributes => array(
			domain => $domain_name,
			period => $period,
			reg_type => 'new',
			)
		);
	$srs_result = $O->send_cmd($cmd);
//	print_r($srs_result);
	return $srs_result;
}

function SRSregistry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers){

	$owner = SRScreate_contact_array($contacts["owner"]);
	$billing = SRScreate_contact_array($contacts["billing"]);
	$admin = SRScreate_contact_array($contacts["admin"]);

	$contact_set = array(
		'owner' => $owner,
		'billing' => $billing,
		'admin' => $admin);

	$nameservers = array();

	$cmd = array(
		'protocol' => 'XCP',
		'action' => 'SW_REGISTER',
		'object' => 'DOMAIN',
		'attributes' => array(
			'reg_domain' => $domain_name,
			'domain' => $domain_name,
			'period' => $period,
			'reg_username' => $adm_login,
			'reg_password' => $adm_pass,
			'auto_renew' => '0',
			'custom_tech_contact' => '0',
			'link_domains' => '0',
			'f_lock_domain' => '0',
			'reg_type' => 'new',
			'custom_nameservers' => '0',
			'nameserver_list' => $nameservers,
			'contact_set' => $contact_set
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	return $srs_result;
}

?>
