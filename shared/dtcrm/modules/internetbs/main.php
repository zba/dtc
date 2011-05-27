<?php
/**
 * Internet.bs registry plugin for DTC.
 *
 * Go to http://internet.bs/ and sign up for an account. Credit some funds
 * and then you can request an API key. 
 *
 * @author Darren Poulson <daz@tinynetworks.co.uk>
 * @version 1.0
 * @package domain_registrar_plugin
 */

$tag = "INTERNET-BS";

/**
 * Post the string to the API server
 * @param string $url URL to post to
 * @param string $data Data to post to URL
 * @return string
 */
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

/**
 * Format submition string for server
 *
 * @param string $post_url Path to post to on the server
 * @param array $post_params_hash Array of paramaters to pass to server
 * @param string $use_post Force use of POST
 * @return string 
 */
function internetbs_submit($post_url, $post_params_hash, $use_post="yes"){
	global $conf_internetbs_server_url;
	global $conf_internetbs_username;
        global $conf_internetbs_password;

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

/**
 * Checks the availability of the domain
 * 
 * @param string $domain_name the domain name to check
 * @return array
 */
function internetbs_registry_check_availability($domain_name){
	$post_params_hash["domain"] = $domain_name;
	$internetbs_ret = json_decode(internetbs_submit("/Domain/Check", $post_params_hash));
        $ret["is_success"] = 1;
        if ( $internetbs_ret->{'status'} == "AVAILABLE" ) {
            $ret["attributes"]["status"] = "available";
	    $ret["attributes"]["minperiod"] = $internetbs_ret->{'minregperiod'};
	    $ret["attributes"]["maxperiod"] = $internetbs_ret->{'maxregperiod'};
        }
	$ret["response_text"] = $internetbs_ret->{'status'};
	return $ret;
}

/**
 * Prepare a list of whois contacts for passing to the registrar
 *
 * @param array $contacts array of contacts
 * @return array
 */
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
	$post_params_hash["registrant_phonenumber"] = $contacts["owner"]["phone_num"];
	$post_params_hash["registrant_fax"] = $contacts["owner"]["fax_num"];
	$post_params_hash["registrant_countrycode"] = $contacts["owner"]["country"];
	$post_params_hash["registrant_language"] = $contacts["owner"]["language"];
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
	$post_params_hash["admin_language"] = $contacts["admin"]["language"];
	$post_params_hash["admin_email"] = $contacts["admin"]["email"];
	$post_params_hash["technical_organization"] = $contacts["teck"]["company"];
	$post_params_hash["technical_firstname"] = $contacts["teck"]["firstname"];
	$post_params_hash["technical_lastname"] = $contacts["teck"]["lastname"];
	$post_params_hash["technical_street"] = $contacts["teck"]["addr1"];
	$post_params_hash["technical_street2"] = $contacts["teck"]["addr2"]." ".$contacts["teck"]["addr3"];
	$post_params_hash["technical_city"] = $contacts["teck"]["city"];
	$post_params_hash["technical_postalcode"] = $contacts["teck"]["zipcode"];
	$post_params_hash["technical_phonenumber"] = $contacts["teck"]["phone_num"];
	$post_params_hash["technical_fax"] = $contacts["teck"]["fax_num"];
	$post_params_hash["technical_countrycode"] = $contacts["teck"]["country"];
	$post_params_hash["technical_language"] = $contacts["teck"]["language"];
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
	$post_params_hash["billing_language"] = $contacts["billing"]["language"];
	$post_params_hash["billing_email"] = $contacts["billing"]["email"];
	return $post_params_hash;
}

/**
 * Register a domain name
 *
 * @param string $adm_login Admin login (not used)
 * @param string $adm_pass Admin password (no used)
 * @param string $domain_name Domain name to register
 * @param integer $period Length of registration period requested
 * @param array $contacts array of contacts to associate with this domain
 * @param array $dns_servers array of DNS servers to use
 * @param string $new_user Is this a new user?
 * @return array
 */
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
		$ret["attributes"]["expiration"] = $internetbs_ret->product[0]->{'expiration'};
	}else{
		$ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
	}
	return $ret;
}

/**
 * Register a new domain name server with the top level domains
 * 
 * @param string $adm_login Admin login (not used)
 * @param string $adm_pass Admin password (not used)
 * @param string $subdomain Subdomain to register
 * @param string $domain_name Domain name on which to register the subdomain
 * @param integer $ip IP address of subdomain
 * @return array
 */
function internetbs_registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	$post_params_hash["host"] = $subdomain.".".$domain_name;
	$post_params_hash["ip_list"] = $ip;
        # echo "Adding: ".$post_params_hash['host']." with IP ".$post_params_hash['ip_list']."<br />";
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Host/Create", $post_params_hash));
        if($internetbs_ret->{'status'} == "SUCCESS"){
                $ret["is_success"] = 1;
        }else{
                $ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
        }
        return $ret;
	
}

/**
 * Update an existing domain name server with the top level domains
 *
 * @param string $adm_login Admin login (not used)
 * @param string $adm_pass Admin password (not used)
 * @param string $subdomain Subdomain to register
 * @param string $domain_name Domain name on which to register the subdomain
 * @param integer $ip IP address of subdomain
 * @return array
 */
function internetbs_registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
	$post_params_hash["host"] = $subdomain.".".$domain_name;
        $post_params_hash["ip_list"] = $ip;
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Host/Update", $post_params_hash));
        if($internetbs_ret->{'status'} == "SUCCESS"){
                $ret["is_success"] = 1;
        }else{
                $ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
        }
        return $ret;
}

/**
 * Delete a domain name server from the top level domains
 *
 * @param string $adm_login Admin login (not used)
 * @param string $adm_pass Admin password (not used)
 * @param string $subdomain Subdomain to register
 * @param string $domain_name Domain name on which to register the subdomain
 * @param integer $ip IP address of subdomain
 * @return array
 */
function internetbs_registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
        $post_params_hash["host"] = $subdomain.".".$domain_name;
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Host/Delete", $post_params_hash));
        if($internetbs_ret->{'status'} == "SUCCESS"){
                $ret["is_success"] = 1;
        }else{
                $ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
        }
        return $ret;

}

/**
 * Get the WHOIS information for a given domain name
 * 
 * @param string $domain_name
 * @return array
 */
function internetbs_registry_get_whois($domain_name){
	$post_params_hash["domain"] = $domain_name;
	$post_params_hash["responseformat"] = "TEXT";
	$internetbs_ret = internetbs_submit("/Domain/Info", $post_params_hash,"no");
	$ret["is_success"] = 1;
	$ret["response_text"] = $internetbs_ret;
	return $ret;
}

/** 
 * Update the WHOIS information for the given domain name
 * 
 * @param string $adm_login Admin login (not used)
 * @param string $adm_pass Admin password (not used)
 * @param string $domain_name Domain name to edit
 * @param array $contacts Array of contacts to send
 * @return array 
 */
function internetbs_registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts){
	global $conf_internetbs_username;
	global $conf_internetbs_password;

	$post_params_hash = internetbs_prepar_whois_params($contacts);
	$post_params_hash["domain"] = $domain_name;
	if (find_domain_extension($domain_name) == ".eu"){
		unset($post_params_hash['registrant_organization']);
	}

	$internetbs_ret = json_decode(internetbs_submit("/Domain/Update", $post_params_hash));
        if($internetbs_ret->{'status'} == "SUCCESS"){
                $ret["is_success"] = 1;
        }else{
                $ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
        }
        return $ret;
}

/**
 * 
 */
function internetbs_registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns){
        global $conf_internetbs_username;
        global $conf_internetbs_password;
        global $conf_addr_primary_dns;
        global $conf_addr_secondary_dns;
        # echo "Testing";

        if ($dns[0] == "default") {
             $dns1 = $conf_addr_primary_dns;
        }

        if ($dns[1] == "default") {
             $dns2 = $conf_addr_secondary_dns;
        }

        $post_params_hash["domain"] = $domain_name;
        $post_params_hash["ns_list"] = "$dns1,$dns2";
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Update", $post_params_hash));
        if($internetbs_ret->{'status'} == "SUCCESS"){
                $ret["is_success"] = 1;
        }else{
                $ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
        }
        return $ret;

}


/**
 * Check the status of the Registrar Lock on a given domain name
 *
 * @param string $domain_name
 * @return array 
 */
function internetbs_registry_check_transfer($domain_name){
        $post_params_hash["domain"] = $domain_name;
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Info", $post_params_hash));
        $ret["is_success"] = 1;
        if ( $internetbs_ret->{'registrarlock'} == "DISABLED" ) 
              $ret["attributes"]["transferrable"] = 1;
        else
              $ret["attributes"]["transferrable"] = 1;
        $ret["attributes"]["reason"] = "Registrar Lock: ".$internetbs_ret->{'registrarlock'};
        
        return $ret;

}

/**
 * Renew a domain name with the registrar
 * 
 * @param string $domain_name Domain name to be renewed
 * @param integer $period Length of time to renew for
 * @return array
 */
function internetbs_registry_renew_domain($domain_name,$period){
        $post_params_hash["domain"] = $domain_name;
        $post_params_hash["period"] = $period."Y";
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Renew", $post_params_hash));
        if ( $internetbs_ret->{'status'} == "SUCCESS" )
              $ret["is_success"] = 1;
        else
              $ret["is_success"] = 0;
        $ret["message"] = $internetbs_ret->{'message'};

        return $ret;

}

/**
 *
 */
function internetbs_registry_change_password($adm_login,$adm_pass,$domain_name,$new_pass){
}


/**
 * Get the transfer auth code for the given domain from the registrar
 * 
 * @param string $domain_name Domain name for request
 * @return array
 */
function internetbs_registry_get_auth_code($domain_name){
        $post_params_hash["domain"] = $domain_name;
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Info", $post_params_hash,"no"));
        $ret["is_success"] = 1;
        $ret["response_text"] = $internetbs_ret->{'transferauthinfo'};
        return $ret;
}

/**
 * Set the domain name transfer protection status
 * 
 * @param string $domain_name Domain name to set protection level on
 * @param string $sel Protection status to set
 * @return array
 */
function internetbs_registry_set_domain_protection($domain_name,$sel) {
        $post_params_hash["domain"] = $domain_name;
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
	$internetbs_ret = json_decode(internetbs_submit($cmd, $post_params_hash,"no"));
	$ret["is_success"] = 1;
        $ret["response_text"] = $internetbs_ret->{'status'};
        return $ret;

}

/**
 * Transfer a domain name from an existing registrar to this one
 *
 * @param string $adm_login
 * @param string $adm_pass
 * @param string $domain_name
 * @param array $contacts
 * @param array $dns_servers
 * @param string $new_user
 * @return array
 */
function internetbs_registry_transfer_domain($adm_login,$adm_pass,$domain_name,$contacts,$dns_servers,$new_user,$authcode) {
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
        $post_params_hash["transferauthinfo"] = $authcode;
        $post_params_hash["domain"] = $domain_name;
        $post_params_hash["ns_list"] = "$dns1,$dns2";
        $internetbs_ret = json_decode(internetbs_submit("/Domain/Transfer/Initiate", $post_params_hash));
        if($internetbs_ret->product[0]->{'status'} == "SUCCESS"){
                $ret["is_success"] = 1;
        }else{
                $ret["is_success"] = 0;
                $ret["response_text"] = $internetbs_ret->{'message'};
        }
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
			"size" => "20"),
		)
	);

$registry_api_modules[] = array(
"name" => "internetbs",
"configure_descriptor" => $configurator,
"registry_check_availability" => "internetbs_registry_check_availability",
"registry_add_nameserver" => "internetbs_registry_add_nameserver",
"registry_modify_nameserver" => "internetbs_registry_modify_nameserver",
"registry_delete_nameserver" => "internetbs_registry_delete_nameserver",
"registry_register_domain" => "internetbs_registry_register_domain",
"registry_update_whois_info" => "internetbs_registry_update_whois_info",
"registry_update_whois_dns" => "internetbs_registry_update_whois_dns",
"registry_check_transfer" => "internetbs_registry_check_transfer",
"registry_renew_domain" => "internetbs_registry_renew_domain",
"registry_change_password" => "internetbs_registry_change_password",
"registry_get_whois" => "internetbs_registry_get_whois",
"registry_get_auth_code" => "internetbs_registry_get_auth_code",
"registry_set_domain_protection" => "internetbs_registry_set_domain_protection",
"registry_transfert_domain" => "internetbs_registry_transfer_domain"
);

?>
