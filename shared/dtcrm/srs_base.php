<?php

require_once "$dtcshared_path/dtcrm/srs/openSRS_base.php";

class openSRS extends openSRS_base {
	var $USERNAME         = 'zigo';            # your OpenSRS username
	var $TEST_PRIVATE_KEY = 'bb3c825180005bf60fc801564d4be9505d2602cc7a59fbfd100cf778afb266e99456609e7d1da54559a86ce3215e2fb117a2905b1811e8b3';  # your private key on the test (horizon) server
	var $LIVE_PRIVATE_KEY = 'abcdef1234567890';  # your private key on the live server
	var $environment      = 'TEST';              # 'TEST' or 'LIVE'
	var $crypt_type       = 'DES';               # 'DES' or 'BLOWFISH';
	var $ext_version      = 'Foobar';            # anything you want

	function initAuth(){
		global $conf_srs_user;
		global $conf_srs_test_key;
		global $conf_srs_live_key;
		global $conf_srs_crypt;
		global $conf_srs_enviro;
		$this->USERNAME = $conf_srs_user;
		$this->TEST_PRIVATE_KEY = $conf_srs_test_key;
		$this->LIVE_PRIVATE_KEY = $conf_srs_live_key;
		$this->crypt_type = $conf_srs_crypt;
		$this->environment = $conf_srs_enviro;
	}
}

// Return an SRS cookie value
$SRScookie_errno=0;
function SRSget_cookie($adm_login,$adm_pass,$domain_name){
	global $SRScookie_errno;
	// Get the SRS cookie for domain update
	$cmd = array(
		'protocol' => "XCP",
		'action' => "set",
		'object' => "cookie",
		'attributes' => array(
			'domain' => $domain_name,
			'reg_username' => $adm_login,
			'reg_password' => $adm_pass
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	$SRScookie_errno = $srs_result;
	if($srs_result["is_success"] != 1){
		return -1;
	}
	return $srs_result["attributes"]["cookie"];
}

function SRSdelete_cookie($cookie){
	$cmd = array(
		'protocol' => "XCP",
		'action' => "delete",
		'object' => "cookie",
		'attributes' => array(
			'cookie' => $cookie
		)
	);
	$O = new openSRS('test','XCP');
	$srs_result = $O->send_cmd($cmd);
	return $srs_result;
}       

function SRScreate_contact_array($contact){
	$out = array(
		'first_name' => $contact["firstname"],
		'last_name' => $contact["lastname"],
		'address1' => $contact["addr1"],
		'address2' => $contact["addr2"],
		'address3' => $contact["addr3"],
		'city' => $contact["city"],
		'state' => $contact["state"],
		'country' => $contact["country"],
		'postal_code' => $contact["zipcode"],
		'email' => $contact["email"],
		'phone' => $contact["phone_num"],
		'fax' => $contact["fax_num"],
	);
	if($contact["company"] != "" && $contact["company"] != NULL){
		$out['org_name'] = $contact["company"];
	}else{
		$out['org_name'] = "none";
	}
	return $out;
}

?>
