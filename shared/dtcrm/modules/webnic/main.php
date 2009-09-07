<?php


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
	"title" => _("Nagios monitoring"),
	"action" => "configure_webnic",
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
"use_module" => $conf_use_webnic,
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