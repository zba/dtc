<?php 

function ovh_registry_check_availability($domain_name) {
$ovh_server_url = "https://www.ovh.com/soapi/soapi-re-1.8.wsdl";
$ovh_username = "";
$ovh_password = "";
$ovh_langage = "fr";
try {
 $soap = new SoapClient("$ovh_server_url");

 //login
 $session = $soap->login("$ovh_username", "$ovh_password","$ovh_langage", false);
 echo "login successfull\n";

 //domainCheck
 $result = $soap->domainCheck($session, "$domain_name");
 echo "domainCheck successfull\n";
 print_r($result); 


 //logout
 $soap->logout($session);
 echo "logout successfull\n";

} catch(SoapFault $fault) {
 echo $fault;
}

$ret["is_success"] = 1;
$ret["response_text"] = $result[0]->reason;
	if($result[0]->value == 1){
        	$ret["attributes"]["status"] = "available";
	}
	return $ret;
	
}

function ovh_registry_add_nameserver(){
}

function ovh_registry_modify_nameserver (){
}

function ovh_registry_delete_nameserver(){
}

function ovh_registry_get_domain_price($domain_name,$period){
}

function ovh_registry_register_domain ($adm_login,$adm_pass,$fqdn,$period,$contacts,$dns_servers,$new_user){
$ovh_domain_name = $fqdn;
$ovh_server_url = "https://www.ovh.com/soapi/soapi-re-1.8.wsdl";
$ovh_username = "";
$ovh_password = "";
$ovh_langage = "fr";
$ovh_nicadmin = "cj42134-ovh";
$ovh_nictech = "cg18768-ovh";
$ovh_nicowner = "cg18768-ovh";
$ovh_nicbilling = "cj42134-ovh";
$ovh_dns1 = $dns_servers[0];
$ovh_dns2 = $dns_servers[1];
$ovh_dns3 = $dns_servers[2];
$ovh_dns4 = $dns_servers[3];
$ovh_dns5 = $dns_servers[4];
$regz["is_success"] = 0;

try {
 $soap = new SoapClient("$ovh_server_url");

 //login
 $session = $soap->login("$ovh_username", "$ovh_password","$ovh_langage", false);
 echo "login successfull\n";


 //resellerDomainCreate
 $result = $soap->resellerDomainCreate( $session, $ovh_domain_name, "none", "gold", "whiteLabel", "no", "$ovh_nicowner", "$ovh_nicadmin", "$ovh_nictech", "$ovh_nicbilling", "$ovh_dns1", "$ovh_dns2", "$ovh_dns3", "$ovh_dns4", "$ovh_dns5", "method : seulement pour les .fr (AFNIC) : méthode d'identification (siren | inpi | birthPlace | afnicIdent)", "legalName : seulement pour les .fr (AFNIC) : nom de la société / propriétaire de la marque", "legalNumber : seulement pour les .fr (AFNIC) : numéro SIREN/SIRET/INPI", "afnicIdent : seulement pour les .fr (AFNIC) : clé d'identification AFNIC (format XXXXXXXX-999)", "birthDate : seulement pour les .fr (AFNIC) : date d'anniversaire du propriétaire", "birthCity : seulement pour les .fr (AFNIC) : ville de naissance du propriétaire", "birthDepartement : seulement pour les .fr (AFNIC) : département de naissance du propriétaire", "birthCountry : seulement pour les .fr (AFNIC) : pays de naissance du propriétaire", true );
 echo "resellerDomainCreate successfull\n";
 $regz["is_success"] = 1;

 //logout
 $soap->logout($session);
 echo "logout successfull\n";

} catch(SoapFault $fault) {
 echo $fault;
}
 $regz["response_text"] = $result;
 return $regz;
}

function ovh_registry_update_whois_dns(){
}

function ovh_registry_update_whois_info(){
}
 function ovh_registry_check_transfer($domain) {
	 $domain_name = $domain;
	 $ovh_server_url = "https://www.ovh.com/soapi/soapi-re-1.8.wsdl";
$ovh_username = "";
$ovh_password = "";
$ovh_langage = "fr";
try {
 $soap = new SoapClient("$ovh_server_url");

 //login
 $session = $soap->login("$ovh_username", "$ovh_password","$ovh_langage", false);
 echo "login successfull\n";

 //domainCheck
 $result = $soap->domainCheck($session, "$domain_name");
 echo "domainCheck successfull\n";
 print_r($result); 


 //logout
 $soap->logout($session);
 echo "logout successfull\n";

} catch(SoapFault $fault) {
 echo $fault;
}

$ret["is_success"] = 1;
$ret["attributes"]["reason"] = $result[1]->reason;
	if($result[1]->value == 1){
		    $ret["attributes"]["transferrable"] = 1;
					}
	return $ret;
	}
/* function ovh_registry_check_transfer($domain) {
	$ovh_domain_name = $domain;
	$domain_name = $domain;
	$ovh_server_url = "https://www.ovh.com/soapi/soapi-re-1.8.wsdl";
    $ovh_username = "";
    $ovh_password = "";
    $ovh_langage = "fr";
    $ovh_dns1 = $dns_servers[0];
    $ovh_dns2 = $dns_servers[1];
    $ovh_dns3 = $dns_servers[2];
    $ovh_dns4 = $dns_servers[3];
    $ovh_dns5 = $dns_servers[4];
	$ovh_nicadmin = "cj42134-ovh";
    $ovh_nictech = "cg18768-ovh";
    $ovh_nicowner = "cg18768-ovh";
    $ovh_nicbilling = "cj42134-ovh";
	$regz["is_success"] = 0; 
	
	$regz = ovh_registry_check_availability($domain_name);
    			
	 try {
 $soap = new SoapClient("$ovh_server_url");

 //login
 $session = $soap->login("$ovh_username", "$ovh_password","$ovh_langage", false);
 echo "login successfull\n";

 //resellerDomainTransfer
 $result = $soap->resellerDomainTransfer($session, "$ovh_domain_name", "authinfo", "none", "gold", "whiteLabel", "no", "$ovh_nicowner", "$ovh_nicadmin", "$ovh_nictech", "$ovh_nicbilling", "$ovh_dns1", "$ovh_dns2", "$ovh_dns3", "$ovh_dns4", "$ovh_dns5", "(siren | inpi | birthPlace | afnicIdent)", "nom de la société / propriétaire de la marque", "numéro SIREN/SIRET/INPI", "clé d'identification AFNIC (format XXXXXXXX-999)", "date d'anniversaire du propriétaire", "ville de naissance du propriétaire", "département de naissance du propriétaire", "pays de naissance du propriétaire", true);
 echo "resellerDomainTransfer successfull\n";
 $regz["is_success"] = 1;
 //logout
 $soap->logout($session);
 echo "logout successfull\n";

} catch(SoapFault $fault) {
 echo $fault;
} 
 $regz["attributes"]["reason"] = $regz[1]->reason;
 return $regz;
 echo "ICI A REGARDER :  ".$toreg_extention;
 }  */

function ovh_registry_renew_domain() {
 }

function ovh_registry_get_whois($domain_name) {
$post_params_hash["domain"] = $domain_name;
$ovh_server_url = "https://www.ovh.com/soapi/soapi-re-1.8.wsdl";
$ovh_username = "";
$ovh_password = "";
$ovh_langage = "fr";
try {
 $soap = new SoapClient("$ovh_server_url");

 //login
 $session = $soap->login("$ovh_username", "$ovh_password","$ovh_langage", false);
 echo "login successfull\n";

 //domainInfo
 $result = $soap->domainInfo($session, "$domain_name");
 echo "domainInfo successfull\n";
 print_r($result); // your code here ...

 //logout
 $soap->logout($session);
 echo "logout successfull\n";

} catch(SoapFault $fault) {
 echo $fault;
}

$ret["is_success"] = 1;
$ret["response_text"] ="";

if (isset($result->domain)) {
$ret["response_text"] ="\n Domain name : ".$result->domain;
}
if (isset($result->creation)) {
$ret["response_text"] .="\n \n Creation date : ".$result->creation;
}
if (isset($result->expiration)) {
$ret["response_text"] .="\n Expiration date : ".$result->expiration;
}
if (isset($result->expiration)) {
$ret["response_text"] .="\n \n Owner name : ".$result->nicowner;
}
if (isset($result->expiration)) {
$ret["response_text"] .="\n Admin name : ".$result->nicadmin;
}
if (isset($result->expiration)) {
$ret["response_text"] .="\n Tech name : ".$result->nictech;
}
if (isset($result->expiration)) {
$ret["response_text"] .="\n Billing name : ".$result->nicbilling;
}
if (isset($result->expiration)) {
$ret["response_text"] .="\n \n Authinfo : ".$result->authinfo;
}
if (isset($result->dns[0]->name)) {
$ret["response_text"] .="\n \n Name server 1 : ".$result->dns[0]->name;
}
if (isset($result->dns[0]->ip)) {
$ret["response_text"] .="\n ip ns1 : ".$result->dns[0]->ip;
}
if (isset($result->dns[1]->ip)) {
$ret["response_text"] .="\n Name server 2 : ".$result->dns[1]->name;
}
if (isset($result->dns[1]->ip)) {
$ret["response_text"] .="\n ip ns2 : ".$result->dns[1]->ip;
}
if (isset($result->dns[2]->name)) {
$ret["response_text"] .="\n Name server 3 : ".$result->dns[1]->name;
}
if (isset($result->dns[2]->ip)) {
$ret["response_text"] .="\n ip ns3 : ".$result->dns[1]->ip;
}
if (isset($result->dns[3]->name)) {
$ret["response_text"] .="\n Name server 4 : ".$result->dns[3]->name;
}
if (isset($result->dns[3]->ip)) {
$ret["response_text"] .="\n ip ns4 : ".$result->dns[3]->ip;
}
if (isset($result->dns[4]->name)) {
$ret["response_text"] .="\n Name server 5 : ".$result->dns[4]->name;
}
if (isset($result->dns[4]->ip)) {
$ret["response_text"] .="\n ip ns5 : ".$result->dns[4]->ip;
}

return $ret;
}

function ovh_registry_change_password(){
}

$configurator = array(
	"title" => _("OVH configuration"),
	"action" => "configure_ovh_editor",
	"forward" => array("rub","sousrub"),
	"desc" => _("Use boolean false for the live server, boolean true for the test one. You have to code"),
	"cols" => array(
                 "ovh_server_url" => array(
			"legend" => _("Server address: "),
			"type" => "text",
			"size" => "20"),
		"ovh_username" => array(
			"legend" => _("Username: "),
			"type" => "text",
			"size" => "20"),
		"ovh_password" => array(
			"legend" => _("Password: "),
			"type" => "text",
			"size" => "20")
		)
	);

$registry_api_modules[] = array(
"name" => "ovh",
"configure_descriptor" => $configurator,
"registry_check_availability" => "ovh_registry_check_availability",
"registry_add_nameserver" => "ovh_registry_add_nameserver",
"registry_modify_nameserver" => "ovh_registry_modify_nameserver",
"registry_delete_nameserver" => "ovh_registry_delete_nameserver",
"registry_get_domain_price" => "ovh_registry_get_domain_price",
"registry_register_domain" => "ovh_registry_register_domain",
"registry_update_whois_info" => "ovh_registry_update_whois_info",
"registry_update_whois_dns" => "ovh_registry_update_whois_dns",
"registry_check_transfer" => "ovh_registry_check_transfer",
"registry_renew_domain" => "ovh_registry_renew_domain",
"registry_change_password" => "ovh_registry_change_password",
"registry_get_whois" => "ovh_registry_get_whois"
);?>
