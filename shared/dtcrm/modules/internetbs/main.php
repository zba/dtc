<?php

// $post_url is usually something like: pn_reg.cgi
// and with the help of the configurator, it will be transformed into: https://pay.web.cc/new/cgi-bin/pn_reg.cgi
// $source is the internetbs username
// $post_params_hash is a hashtable of the POST parameters

// returns 99 if there are no $source values present in the request
// returns error code from URL
// returns actual content from URL

function InternetbsPostUsingCurl($url,$data) {
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
        echo "Internet.bs return: $ret<br>";
        curl_close($c);
        return $ret;
}


function internetbs_submit($post_url, $post_params_hash, $use_post="yes"){
	global $conf_internetbs_server_url;
	global $conf_internetbs_username;
        global $conf_internetbs_password;
	global $errno;
	global $errstr;

	// Print the variable to be posted yes/no

	$user = $conf_internetbs_username;
        $pass = $conf_internetbs_password;
	$post_url = $conf_internetbs_server_url.$post_url;

	$strContent = "ApiKey=$user&Password=$pass&responseformat=json";	

	foreach(array_keys($post_params_hash) as $key){
		if ($key != "source"){
			$strContent.= "&$key=". urlencode($post_params_hash[$key]);
		}
	}
        return InternetbsPostUsingCurl($post_url,$strContent);
}

function internetbs_registry_check_availability($domain_name){
	$post_params_hash["domain"] = $domain_name;
	$response = json_decode(internetbs_submit("/Domain/Check", $post_params_hash));
        $ret["is_success"] = 1;
        if ( $response->{'status'} == "AVAILABLE" ) {
            $ret["attributes"]["status"] = "available";
        }
	$ret["response_text"] = $response->{'status'};
	return $ret;
}

function internetbs_prepar_whois_params($contacts){
	if($contacts["owner"]["company"] == ""){
		$owner = $contacts["owner"]["firstname"]." ".$contacts["owner"]["lastname"];
	}else{
		$owner = $contacts["owner"]["company"];
	}
	$post_params_hash["registrant_organization"] = $owner;
	$post_params_hash["registrant_firstname"] = $contacts["owner"]["firstname"];
	$post_params_hash["registrant_lastname"] = $contacts["owner"]["lastname"];
	$post_params_hash["registrant_street"] = $contacts["owner"]["addr1"];
	$post_params_hash["registrant_street2"] = $contacts["owner"]["addr2"]." ".$contacts["owner"]["addr3"];
	$post_params_hash["registrant_city"] = $contacts["owner"]["city"];
	$post_params_hash["registrant_postalcode"] = $contacts["owner"]["zipcode"];
	$post_params_hash["registrant_phonenumber"] = "+44.123456789"; // $contacts["owner"]["phone_num"];
	$post_params_hash["registrant_fax"] = $contacts["owner"]["fax_num"];
	$post_params_hash["registrant_countrycode"] = $contacts["owner"]["country"];
	$post_params_hash["registrant_email"] = $contacts["owner"]["email"];
	$post_params_hash["admin_organization"] = $contacts["admin"]["company"];
	$post_params_hash["admin_firstname"] = $contacts["admin"]["firstname"];
	$post_params_hash["admin_lastname"] = $contacts["admin"]["lastname"];
	$post_params_hash["admin_street"] = $contacts["admin"]["addr1"];
	$post_params_hash["admin_street2"] = $contacts["admin"]["addr2"]." ".$contacts["admin"]["addr3"];
	$post_params_hash["admin_city"] = $contacts["admin"]["city"];
	$post_params_hash["admin_postalcode"] = $contacts["admin"]["zipcode"];
	$post_params_hash["admin_phonenumber"] = $contacts["admin"]["phone_num"];
	$post_params_hash["admin_fax"] = $contacts["admin"]["fax_num"];
	$post_params_hash["admin_countrycode"] = $contacts["admin"]["country"];
	$post_params_hash["admin_email"] = $contacts["admin"]["email"];
	$post_params_hash["technical_organization"] = $contacts["teck"]["company"];
	$post_params_hash["technical_firstname"] = $contacts["teck"]["firstname"];
	$post_params_hash["technical_lastname"] = $contacts["teck"]["lastname"];
	$post_params_hash["technical_street"] = $contacts["teck"]["addr1"];
	$post_params_hash["technical_street2"] = $contacts["teck"]["addr2"]." ".$contacts["tech"]["addr3"];
	$post_params_hash["technical_city"] = $contacts["teck"]["city"];
	$post_params_hash["technical_postalcode"] = $contacts["teck"]["zipcode"];
	$post_params_hash["technical_phonenumber"] = $contacts["teck"]["phone_num"];
	$post_params_hash["technical_fax"] = $contacts["teck"]["fax_num"];
	$post_params_hash["technical_countrycode"] = $contacts["teck"]["country"];
	$post_params_hash["technical_email"] = $contacts["teck"]["email"];
	$post_params_hash["billing_organization"] = $contacts["billing"]["company"];
	$post_params_hash["billing_firstname"] = $contacts["billing"]["firstname"];
	$post_params_hash["billing_lastname"] = $contacts["billing"]["lastname"];
	$post_params_hash["billing_street"] = $contacts["billing"]["addr1"];
	$post_params_hash["billing_street2"] = $contacts["billing"]["addr2"]." ".$contacts["billing"]["addr3"];
	$post_params_hash["billing_city"] = $contacts["billing"]["city"];
	$post_params_hash["billing_postalcode"] = $contacts["billing"]["zipcode"];
	$post_params_hash["billing_phonenumber"] = $contacts["billing"]["phone_num"];
	$post_params_hash["billing_fax"] = $contacts["billing"]["fax_num"];
	$post_params_hash["billing_countrycode"] = $contacts["billing"]["country"];
	$post_params_hash["billing_email"] = $contacts["billing"]["email"];
	return $post_params_hash;
}

function internetbs_registry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers,$new_user){
	global $conf_internetbs_username;
	global $conf_internetbs_password;
        global $conf_addr_primary_dns;
        global $conf_addr_secondary_dns;

        if ($dns_servers[0] == "default") {
             $dns1 = $conf_addr_primary_dns;
        }

        if ($dns_servers[1] == "default") {
             $dns2 = $conf_addr_secondary_dns;
        }

	$post_params_hash = internetbs_prepar_whois_params($contacts);
	$post_params_hash["domain"] = $domain_name;
	$post_params_hash["period"] = $period."Y";
        $post_params_hash["ns_list"] = "$dns1,$dns2";
	$internetbs_ret = json_decode(internetbs_submit("/Domain/Create", $post_params_hash));
	if($internetbs_ret->product[0]->{'status'} == "SUCCESS"){
		$ret["is_success"] = 1;
	}else{
		$ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
	}
	return $ret;
}

function internetbs_registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
}

function internetbs_registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
}

function internetbs_registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
}

function internetbs_registry_get_domain_price($domain_name,$period){
}

function internetbs_registry_get_whois($domain_name){
	$post_params_hash["domain"] = $domain_name;
	$post_params_hash["responseformat"] = "TEXT";
	$internetbs_ret = internetbs_submit("/Domain/Info", $post_params_hash,"no");
	$ret["is_success"] = 1;
	$ret["response_text"] = $internetbs_ret;
	return $ret;
}

function internetbs_registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts){
	global $conf_internetbs_username;
	global $conf_internetbs_password;

	$post_params_hash = internetbs_prepar_whois_params($contacts);
	$post_params_hash["domain"] = $domain_name;

	$internetbs_ret = json_decode(internetbs_submit("/Domain/Update", $post_params_hash));
        if($internetbs_ret->{'status'} == "SUCCESS"){
                $ret["is_success"] = 1;
        }else{
                $ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
        }
        return $ret;
}

function internetbs_registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns){
}

function internetbs_registry_check_transfer($domain){
}

function internetbs_registry_renew_domain($adm_login,$adm_pass,$domain,$curent_year_expir,$period){
}

function internetbs_registry_change_password($adm_login,$adm_pass,$domain,$new_pass){
}

function internetbs_registry_get_auth_code($domain){
        $post_params_hash["domain"] = $domain;
        $internetbs_ret = internetbs_submit("/Domain/Info", $post_params_hash,"no");
        $ret["is_success"] = 1;
        $ret["response_text"] = $internetbs_ret->{'transferauthinfo'};
        return $ret;
}

function internetbs_registry_set_domain_protection($domain,$sel) {
        $post_params_hash["domain"] = $domain;
        switch($sel){
             case "unlocked":
                $cmd = "/Domain/RegistrarLock/Disable";
                break;
             case "transferprot":
                $cmd = "/Domain/RegistrarLock/Enable";
                break;
             default:
             case "locked":
                $cmd = "/Domain/RegistrarLock/Enable";
                break;
        }
	$internetbs_ret = internetbs_submit($cmd, $post_params_hash,"no");
	$ret["is_success"] = 1;
        $ret["response_text"] = $internetbs_ret->{'status'};
        return $ret;

}

$configurator = array(
	"title" => _("InternetBS configuration"),
	"action" => "configure_internetbs_editor",
	"forward" => array("rub","sousrub"),
	"desc" => _("Use https://api.internet.bs/ for the live server, https://testapi.internet.bs/ for the test one."),
	"cols" => array(
		"internetbs_server_url" => array(
			"legend" => _("Server address: "),
			"type" => "text",
			"size" => "20"),
		"internetbs_username" => array(
			"legend" => _("Username: "),
			"type" => "text",
			"size" => "20"),
		"internetbs_password" => array(
			"legend" => _("Password: "),
			"type" => "text",
			"size" => "20")
		)
	);

$registry_api_modules[] = array(
"name" => "internetbs",
"configure_descriptor" => $configurator,
"registry_check_availability" => "internetbs_registry_check_availability",
"registry_add_nameserver" => "internetbs_registry_add_nameserver",
"registry_modify_nameserver" => "internetbs_registry_modify_nameserver",
"registry_delete_nameserver" => "internetbs_registry_delete_nameserver",
"registry_get_domain_price" => "internetbs_registry_get_domain_price",
"registry_register_domain" => "internetbs_registry_register_domain",
"registry_update_whois_info" => "internetbs_registry_update_whois_info",
"registry_update_whois_dns" => "internetbs_registry_update_whois_dns",
"registry_check_transfer" => "internetbs_registry_check_transfer",
"registry_renew_domain" => "internetbs_registry_renew_domain",
"registry_change_password" => "internetbs_registry_change_password",
"registry_get_whois" => "internetbs_registry_get_whois",
"registry_get_auth_code" => "internetbs_registry_get_auth_code",
"registry_set_domain_protection" => "internetbs_registry_set_domain_protection"
);

?>
