<?php

// $post_url is usually something like: pn_reg.cgi
// and with the help of the configurator, it will be transformed into: https://pay.web.cc/new/cgi-bin/pn_reg.cgi
// $source is the webnic username
// $post_params_hash is a hashtable of the POST parameters

// returns 99 if there are no $source values present in the request
// returns error code from URL
// returns actual content from URL

function webnic_submit($post_url, $post_params_hash){
	global $conf_webnic_server_url;
	global $conf_webnic_username;

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

	$postfield=$strContent;
	if ($debug == 1){
		echo $postfield . "\n";
		echo "Post URL: $post_url\n";
	}
	$url = $post_url."?".$strContent;
	$httprequest = new HTTPRequest("$url");
	$lines = $httprequest->DownloadToStringArray();
	if($lines === FALSE){
		return "98 Could not open connection to the remote server (fsockopen error)\n";
	}
	return $lines[0];
}

function webnic_return_code($return){
	switch($webcc_ret){
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

function webnic_registry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers){
	$post_params_hash["otime"] = date("Y-m-d H:m:i");
	print_r($contacts);
	die("Debug die");

	$post_params_hash["domainname"] = $domain_name;
	$post_params_hash["ochecksum"] = "I don't know what's that, please FIXME";
	$post_params_hash["encoding"] = "iso8859-1";
	$post_params_hash["term"] = $period;
	$post_params_hash["ns1"] = "FIXME";
	$post_params_hash["ns2"] = "FIXME";
	$post_params_hash["reg_company"] = "FIXME";
	$post_params_hash["reg_fname"] = "FIXME";
	$post_params_hash["reg_lname"] = "FIXME";
	$post_params_hash["reg_addr1"] = "FIXME";
	$post_params_hash["reg_addr2"] = "FIXME";
	$post_params_hash["reg_state"] = "FIXME";
	$post_params_hash["reg_city"] = "FIXME";
	$post_params_hash["reg_postcode"] = "FIXME";
	$post_params_hash["reg_telephone"] = "FIXME";
	$post_params_hash["reg_fax"] = "FIXME";
	$post_params_hash["reg_country"] = "FIXME";
	$post_params_hash["reg_email"] = "FIXME";
	$post_params_hash["flag_adm"] = "FIXME";
	$post_params_hash["adm_company"] = "FIXME";
	$post_params_hash["adm_fname"] = "FIXME";
	$post_params_hash["adm_lname"] = "FIXME";
	$post_params_hash["adm_addr1"] = "FIXME";
	$post_params_hash["adm_addr2"] = "FIXME";
	$post_params_hash["adm_state"] = "FIXME";
	$post_params_hash["adm_city"] = "FIXME";
	$post_params_hash["adm_postcode"] = "FIXME";
	$post_params_hash["adm_telephone"] = "FIXME";
	$post_params_hash["adm_fax"] = "FIXME";
	$post_params_hash["adm_country"] = "FIXME";
	$post_params_hash["adm_email"] = "FIXME";
	$post_params_hash["tec_company"] = "FIXME";
	$post_params_hash["tec_fname"] = "FIXME";
	$post_params_hash["tec_lname"] = "FIXME";
	$post_params_hash["tec_addr1"] = "FIXME";
	$post_params_hash["tec_addr2"] = "FIXME";
	$post_params_hash["tec_state"] = "FIXME";
	$post_params_hash["tec_city"] = "FIXME";
	$post_params_hash["tec_postcode"] = "FIXME";
	$post_params_hash["tec_telephone"] = "FIXME";
	$post_params_hash["tec_fax"] = "FIXME";
	$post_params_hash["tec_country"] = "FIXME";
	$post_params_hash["tec_email"] = "FIXME";
	$post_params_hash["bil_company"] = "FIXME";
	$post_params_hash["bil_fname"] = "FIXME";
	$post_params_hash["bil_lname"] = "FIXME";
	$post_params_hash["bil_addr1"] = "FIXME";
	$post_params_hash["bil_addr2"] = "FIXME";
	$post_params_hash["bil_state"] = "FIXME";
	$post_params_hash["bil_city"] = "FIXME";
	$post_params_hash["bil_postcode"] = "FIXME";
	$post_params_hash["bil_telephone"] = "FIXME";
	$post_params_hash["bil_fax"] = "FIXME";
	$post_params_hash["bil_country"] = "FIXME";
	$post_params_hash["bil_email"] = "FIXME";
	$post_params_hash["username"] = "FIXME";
	$post_params_hash["password"] = "FIXME";
	$post_params_hash["newuser"] = "FIXME";	// old / new
}

function webnic_registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
}

function webnic_registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
}

function webnic_registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
}

function webnic_registry_get_domain_price($domain_name,$period){
}

function webnic_registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts){
}

function webnic_registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns){
}

function webnic_registry_check_transfer($domain){
}

function webnic_registry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period){
}

function webnic_registry_change_password($adm_login,$adm_pass,$domain,$new_pass){
}

$configurator = array(
	"title" => _("Wenic configuration"),
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
"registry_get_domain_price" => "webnic_registry_get_domain_price",
"registry_register_domain" => "webnic_registry_register_domain",
"registry_update_whois_info" => "webnic_registry_update_whois_info",
"registry_update_whois_dns" => "webnic_registry_update_whois_dns",
"registry_check_transfer" => "webnic_registry_check_transfer",
"registry_renew_domain" => "webnic_registry_renew_domain",
"registry_change_password" => "webnic_registry_change_password"
);

?>
