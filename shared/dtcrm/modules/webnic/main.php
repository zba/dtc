<?php

function webnicPostUsingCurl($url,$data) { 
	$headers = array();
	$headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
	$c = curl_init($url);
	curl_setopt($c, CURLOPT_HEADER, 1);
	curl_setopt($c, CURLOPT_HTTPHEADER, $headers); 
	curl_setopt($c, CURLOPT_TIMEOUT, 15);

	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($c, CURLOPT_POST, 1);
	curl_setopt($c, CURLOPT_POSTFIELDS, $data);
	$content = curl_exec($c);
	if($content === FALSE){
		return FALSE;
	}
	$info = curl_getinfo($c);
	$ret = substr($content,$info["header_size"]);
	// echo "Webnic return: $ret<br>";
	curl_close($c);
	return $ret;
}

// $post_url is usually something like: pn_reg.cgi
// and with the help of the configurator, it will be transformed into: https://pay.web.cc/new/cgi-bin/pn_reg.cgi
// $source is the webnic username
// $post_params_hash is a hashtable of the POST parameters

// returns 99 if there are no $source values present in the request
// returns error code from URL
// returns actual content from URL

function webnic_submit($post_url, $post_params_hash, $use_post="yes"){
	global $conf_webnic_server_url;
	global $conf_webnic_username;
	global $errno;
	global $errstr;

	// Print the variable to be posted yes/no
	$debug = 0;

	$source = $conf_webnic_username;
	$post_url = $conf_webnic_server_url.$post_url;

	$strContent = "";	
	if (isset($source)){
		$strContent.="source=$source";
	}else if (isset($post_params_hash["source"])){
		$strContent.="source=" . $post_params_hash["source"];
	}else{
		return "99 No source (No Webnic.cc partner username specified)\n";
	}

	foreach(array_keys($post_params_hash) as $key){
		if ($key != "source"){
			$strContent.= "&$key=". $post_params_hash[$key];
		}
	}

	return webnicPostUsingCurl($post_url,$strContent);
}

function webnic_return_code($return){
	switch($return){
	case 0:
		return "success";
	case 1:
		return "already-registered";
	case 2:
		return "management-violation";
	case 3:
		return "policy-error";
	case 4:
		return "system-error";
	case 5:
		return "IP-address-prohibited";
	case 6:
		return "wrong-user-password-or-checksum";
	case 7:
		return "system-error";
	case 8:
		return "communication-error";
	case 99:
		return "username-error";
	default:
		return "unknown-error";
	}
}

function response_text($webcc_ret){
	switch($webcc_ret){
	case 0:
	case 1:
	case 2:
	case 3:
	case 4:
	case 5:
	case 6:
	case 7:
	case 8:
	case 9:
		return substr($webcc_ret,1);
		break;
	default:
		return substr($webcc_ret,2);
		break;
	}
}

function webnic_checksum($post_params_hash=array()){
	global $conf_webnic_username;
	global $conf_webnic_password;

	$post_params_hash["otime"] = date("Y-m-d H:m:i");
	$post_params_hash["ochecksum"] = md5($conf_webnic_username.$post_params_hash["otime"].md5($conf_webnic_password));
	return $post_params_hash;
}

function webnic_registry_check_availability($domain_name){
	$post_params_hash["domain"] = $domain_name;
	$webcc_ret = webnic_submit("pn_qry.jsp", $post_params_hash);
	$ret["is_success"] = 1;
	$ret["response_text"] = response_text($webcc_ret);
	if($webcc_ret == 0){
        	$ret["attributes"]["status"] = "available";
	}else{
		$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	}
	return $ret;
}

function webnic_prepar_whois_params($contacts){
	if($contacts["owner"]["company"] == ""){
		$owner = $contacts["owner"]["firstname"]." ".$contacts["owner"]["lastname"];
	}else{
		$owner = $contacts["owner"]["company"];
	}
	$post_params_hash["reg_company"] = $owner;
	$post_params_hash["reg_fname"] = $contacts["owner"]["firstname"];
	$post_params_hash["reg_lname"] = $contacts["owner"]["lastname"];
	$post_params_hash["reg_addr1"] = $contacts["owner"]["addr1"];
	$post_params_hash["reg_addr2"] = $contacts["owner"]["addr2"]." ".$contacts["owner"]["addr3"];
	$post_params_hash["reg_state"] = $contacts["owner"]["state"];
	$post_params_hash["reg_city"] = $contacts["owner"]["city"];
	$post_params_hash["reg_postcode"] = $contacts["owner"]["zipcode"];
	$post_params_hash["reg_telephone"] = $contacts["owner"]["phone_num"];
	$post_params_hash["reg_fax"] = $contacts["owner"]["fax_num"];
	$post_params_hash["reg_country"] = $contacts["owner"]["country"];
	$post_params_hash["reg_email"] = $contacts["owner"]["email"];
	$post_params_hash["flag_adm"] = 0;
	$post_params_hash["adm_company"] = $contacts["admin"]["company"];
	$post_params_hash["adm_fname"] = $contacts["admin"]["firstname"];
	$post_params_hash["adm_lname"] = $contacts["admin"]["lastname"];
	$post_params_hash["adm_addr1"] = $contacts["admin"]["addr1"];
	$post_params_hash["adm_addr2"] = $contacts["admin"]["addr2"]." ".$contacts["admin"]["addr3"];
	$post_params_hash["adm_state"] = $contacts["admin"]["state"];
	$post_params_hash["adm_city"] = $contacts["admin"]["city"];
	$post_params_hash["adm_postcode"] = $contacts["admin"]["zipcode"];
	$post_params_hash["adm_telephone"] = $contacts["admin"]["phone_num"];
	$post_params_hash["adm_fax"] = $contacts["admin"]["fax_num"];
	$post_params_hash["adm_country"] = $contacts["admin"]["country"];
	$post_params_hash["adm_email"] = $contacts["admin"]["email"];
	$post_params_hash["tec_company"] = $contacts["teck"]["company"];
	$post_params_hash["tec_fname"] = $contacts["teck"]["firstname"];
	$post_params_hash["tec_lname"] = $contacts["teck"]["firstname"];
	$post_params_hash["tec_addr1"] = $contacts["teck"]["addr1"];
	$post_params_hash["tec_addr2"] = $contacts["teck"]["addr2"]." ".$contacts["teck"]["addr3"];
	$post_params_hash["tec_state"] = $contacts["teck"]["state"];
	$post_params_hash["tec_city"] = $contacts["teck"]["city"];
	$post_params_hash["tec_postcode"] = $contacts["teck"]["zipcode"];
	$post_params_hash["tec_telephone"] = $contacts["teck"]["phone_num"];
	$post_params_hash["tec_fax"] = $contacts["teck"]["fax_num"];
	$post_params_hash["tec_country"] = $contacts["teck"]["country"];
	$post_params_hash["tec_email"] = $contacts["teck"]["email"];
	$post_params_hash["bil_company"] = $contacts["billing"]["company"];
	$post_params_hash["bil_fname"] = $contacts["billing"]["firstname"];
	$post_params_hash["bil_lname"] = $contacts["billing"]["lastname"];
	$post_params_hash["bil_addr1"] = $contacts["billing"]["addr1"];
	$post_params_hash["bil_addr2"] = $contacts["billing"]["addr2"]." ".$contacts["billing"]["addr3"];
	$post_params_hash["bil_state"] = $contacts["billing"]["state"];
	$post_params_hash["bil_city"] = $contacts["billing"]["city"];
	$post_params_hash["bil_postcode"] = $contacts["billing"]["zipcode"];
	$post_params_hash["bil_telephone"] = $contacts["billing"]["phone_num"];
	$post_params_hash["bil_fax"] = $contacts["billing"]["fax_num"];
	$post_params_hash["bil_country"] = $contacts["billing"]["country"];
	$post_params_hash["bil_email"] = $contacts["billing"]["email"];
	return $post_params_hash;
}

function webnic_registry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers,$new_user){
	$post_params_hash = webnic_prepar_whois_params($contacts);
	$post_params_hash = webnic_checksum($post_params_hash);

	$post_params_hash["domainname"] = $domain_name;
	$post_params_hash["encoding"] = "iso8859-1";
	$post_params_hash["term"] = $period;
	$post_params_hash["ns1"] = $dns_servers[0];
	$post_params_hash["ns2"] = $dns_servers[1];
	$post_params_hash["username"] = $adm_login;
	$post_params_hash["password"] = $adm_pass;
	if($new_user == "yes"){
		$post_params_hash["newuser"] = "new";
	}else{
		$post_params_hash["newuser"] = "old";
	}

	$webcc_ret = webnic_submit("pn_reg.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	$post_params_hash = webnic_checksum();
	$post_params_hash["dns1"] = $subdomain.".".$domain_name;
	$post_params_hash["ip1"] = $ip[0];
	if( isset($ip[1]) ){
		$post_params_hash["ip2"] = $ip[1];
	}
	if( isset($ip[2]) ){
		$post_params_hash["ip3"] = $ip[2];
	}
	if( isset($ip[3]) ){
		$post_params_hash["ip4"] = $ip[3];
	}
	if( isset($ip[4]) ){
		$post_params_hash["ip5"] = $ip[4];
	}
	if( isset($ip[5]) ){
		$post_params_hash["ip6"] = $ip[5];
	}
	$post_params_hash["reg"] = "com";
	$webcc_ret = webnic_submit("pn_dnsreg.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	$post_params_hash = webnic_checksum();
	$post_params_hash["dns"] = $subdomain.".".$domain_name;
	$post_params_hash["ip1"] = $ip;
	if( isset($ip[1]) ){
		$post_params_hash["ip2"] = $ip[1];
	}
	if( isset($ip[2]) ){
		$post_params_hash["ip3"] = $ip[2];
	}
	if( isset($ip[3]) ){
		$post_params_hash["ip4"] = $ip[3];
	}
	if( isset($ip[4]) ){
		$post_params_hash["ip5"] = $ip[4];
	}
	if( isset($ip[5]) ){
		$post_params_hash["ip6"] = $ip[5];
	}
	$webcc_ret = webnic_submit("pn_dnsmod.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
	$post_params_hash = webnic_checksum();
	$post_params_hash["dns1"] = $subdomain.".".$domain_name;
	$post_params_hash["reg"] = "com";
	$webcc_ret = webnic_submit("pn_dnsdel.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_get_whois($domain_name){
	$post_params_hash["domain"] = $domain_name;
	$webcc_ret = webnic_submit("whois.jsp", $post_params_hash,"no");
	$ret["is_success"] = 1;
	$ret["response_text"] = $webcc_ret;
	return $ret;
}

function webnic_registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts){
	$post_params_hash = webnic_prepar_whois_params($contacts);
	$post_params_hash = webnic_checksum($post_params_hash);
	$post_params_hash["domainname"] = $domain_name;
	$webcc_ret = webnic_submit("pn_mod.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns,$dns_ip=array()){
	$post_params_hash = webnic_checksum();

	$post_params_hash["domain"] = $domain_name;
	$post_params_hash["ns1"] = $dns[0];
	$post_params_hash["ns2"] = $dns[1];
	if(isset($dns[2])){
		$post_params_hash["ns3"] = $dns[2];
	}
	if(isset($dns[3])){
		$post_params_hash["ns4"] = $dns[3];
	}
	if(isset($dns[4])){
		$post_params_hash["ns5"] = $dns[4];
	}
	if(isset($dns[5])){
		$post_params_hash["ns6"] = $dns[5];
	}
	if( isset( $dns_ip[0] )){
		$post_params_hash["nsip1"] = $dns_ip[0];
	}
	if( isset( $dns_ip[1] )){
		$post_params_hash["nsip2"] = $dns_ip[1];
	}
	if( isset( $dns_ip[2] )){
		$post_params_hash["nsip3"] = $dns_ip[2];
	}
	if( isset( $dns_ip[3] )){
		$post_params_hash["nsip4"] = $dns_ip[3];
	}
	if( isset( $dns_ip[4] )){
		$post_params_hash["nsip5"] = $dns_ip[4];
	}
	if( isset( $dns_ip[5] )){
		$post_params_hash["nsip6"] = $dns_ip[5];
	}
	$webcc_ret = webnic_submit("pn_dns.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_check_transfer($domain){
//	$post_params_hash = webnic_checksum();
//	$post_params_hash["domainname"] = $domain;
//	$webcc_ret = webnic_submit("pn_trfstatus.jsp", $post_params_hash);
	// No apparent way to test if a domain is transferable with webnice
	// so we always reply transferable.
	$webcc_ret = "0 Transferable";
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	$ret["attributes"]["transferrable"] = 1;
	return $ret;
}

function webnic_registry_transfert_domain($adm_login,$adm_pass,$domain,$contacts,$authinfo){
	$post_params_hash = webnic_prepar_whois_params($contacts);
	$post_params_hash = webnic_checksum($post_params_hash);
	$post_params_hash["domain"] = $domain;
	$post_params_hash["authinfo"] = $authinfo;
	$post_params_hash["userstatus"] = "NEW"; // Can be "OLD" as well
	$post_params_hash["username"] = $adm_login;
	$post_params_hash["password"] = $adm_pass;
	$post_params_hash["password2"] = $adm_pass;
	$webcc_ret = webnic_submit("pn_transfer.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_get_auth_code($domain){
//	$post_params_hash = webnic_checksum();
	$ret["response_text"] = _("Webnic doesn't support auth code retrieval, please contact support.");
	$ret["attributes"]["status"] = 0;
	$ret["is_success"] = 1;
	return $ret;
}

function webnic_registry_set_domain_protection($domain,$protection){
	$post_params_hash = webnic_checksum();
	$post_params_hash["domainname"] = $domain;
	switch($protection){
	case "unlocked":
		$post_params_hash["status"] = "A";
		break;
	case "transferprot":
		$post_params_hash["status"] = "N";
		break;
	case "locked":
	default:
		$post_params_hash["status"] = "L";
		break;
	}
	$webcc_ret = webnic_submit("pn_protect.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_renew_domain($domain,$years){
	$post_params_hash = webnic_checksum();
	$post_params_hash["domainname"] = $domain;
	$post_params_hash["term"] = $years;
	$post_params_hash["proxy"] = "0";
	$webcc_ret = webnic_submit("pn_renew.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_delete_domain($adm_login,$adm_pass,$domain){
	$post_params_hash = webnic_checksum();
	$post_params_hash["domainname"] = $domain;
	$webcc_ret = webnic_submit("pn_del.jsp", $post_params_hash);
	$ret["response_text"] = response_text($webcc_ret);
	$ret["attributes"]["status"] = webnic_return_code($webcc_ret);
	if($webcc_ret == 0){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
	}
	return $ret;
}

function webnic_registry_change_password($adm_login,$adm_pass,$domain,$new_pass){
}

$configurator = array(
	"title" => _("Webnic configuration"),
	"action" => "configure_webnic_editor",
	"forward" => array("rub","sousrub"),
	"desc" => _("Use https://my.webnic.cc/jsp/ for the live server, https://ote.webnic.cc/jsp/ for the test one."),
	"cols" => array(
		"webnic_server_url" => array(
			"legend" => _("Server address: "),
			"type" => "text",
			"size" => "20"),
		"webnic_username" => array(
			"legend" => _("Username: "),
			"type" => "text",
			"size" => "20"),
		"webnic_password" => array(
			"legend" => _("Password: "),
			"type" => "text",
			"size" => "20")
		)
	);

$registry_api_modules[] = array(
"name" => "webnic",
"configure_descriptor" => $configurator,
"registry_check_availability" => "webnic_registry_check_availability",
"registry_add_nameserver" => "webnic_registry_add_nameserver",
"registry_modify_nameserver" => "webnic_registry_modify_nameserver",
"registry_delete_nameserver" => "webnic_registry_delete_nameserver",
"registry_register_domain" => "webnic_registry_register_domain",
"registry_update_whois_info" => "webnic_registry_update_whois_info",
"registry_update_whois_dns" => "webnic_registry_update_whois_dns",
"registry_check_transfer" => "webnic_registry_check_transfer",
"registry_get_auth_code" => "webnic_registry_get_auth_code",
"registry_set_domain_protection" => "webnic_registry_set_domain_protection",
"registry_renew_domain" => "webnic_registry_renew_domain",
"registry_delete_domain" => "webnic_registry_delete_domain",
"registry_transfert_domain" => "webnic_registry_transfert_domain",
"registry_change_password" => "webnic_registry_change_password",
"registry_get_whois" => "webnic_registry_get_whois"
);

?>
