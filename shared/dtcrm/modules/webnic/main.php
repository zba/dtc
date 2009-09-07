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
	global $webnic_username;

	$source = $webnic_username;
	$post_url = $conf_webnic_server_url.$post_url;

	$strContent = "";	
	if (isset($source)){
		$strContent.="source=$source";
	}else if (isset($post_params_hash["source"])){
		$strContent.="source=" . $post_params_hash["source"];
	}else{
		return "99\nNo source (Webnic.cc partner username specified\n";			
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
	$ch = curl_init();
	// $url2="https://pay.web.cc/new/cgi-bin/pn_whois.cgi";
	curl_setopt($ch, CURLOPT_URL,$post_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
	curl_exec ($ch);
	$strReturn= ob_get_contents ( );
	curl_close ($ch);
	return $strReturn;
}


function webnic_registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
}

function webnic_registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip){
}

function webnic_registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name){
}

function webnic_registry_check_availability($domain_name){
}

function webnic_registry_get_domain_price($domain_name,$period){
}

function webnic_registry_register_domain($adm_login,$adm_pass,$domain_name,$period,$contacts,$dns_servers){
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
	"desc" => _("Webnic"),
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
