<?php

function ovh_open(){
      global $conf_ovh_server_url;
      $ovh_server_url = $conf_ovh_server_url;

$soap = new SoapClient($ovh_server_url); 

return $soap;
}

function login_ovh(){
	    global $conf_ovh_username;
           global $conf_ovh_password;
           global $conf_ovh_boolean;
	    global $conf_ovh_language;
	   
           $ovh_username = $conf_ovh_username;
           $ovh_password = $conf_ovh_password;
           $ovh_langage = $conf_ovh_language;
           $ovh_boolean = $conf_ovh_boolean;
	   $soap = ovh_open();
	   
 $session = $soap->login($ovh_username,$ovh_password,$ovh_langage,$ovh_boolean);
 return $session;
 }
 
function logout_ovh($soap,$session){
 $soap->logout($session);
 }
 
function ovh_registry_check_availability($domain_name) {
  try {
//login 
   $soap = ovh_open();
   $session = login_ovh();
   
 //domainCheck
 $result = $soap->domainCheck($session,"$domain_name");

 //logout
 logout_ovh($soap,$session);

} catch(SoapFault $fault) {
 echo $fault;
}

$ret["is_success"] = 1;
$ret["response_text"] = "Domain name already registred";
	if($result[0]->value == 1){
        	$ret["attributes"]["status"] = "available";
	} else {
		    $ret["attributes"]["status"] = "not available";
	}
	return $ret;
	
} 

function ovh_registry_add_nameserver ($adm_login,$adm_pass,$subdomain,$domain_name,$ip){

 $regz["is_success"] = 0;

try {
//login
   $soap = ovh_open();
   $session = login_ovh();

 //domainHostAdd
 $result = $soap->domainHostAdd($session,$domain_name,$subdomain,$ip);
 $regz["is_success"] = 1;
 //logout
 logout_ovh($soap,$session);

} catch(SoapFault $fault) {
 echo $fault;
 }
if($regz["is_success"] == 1){
$regz["response_text"] = $result;
} else{
$regz["response_text"] = "Add failed";
}
return $regz;

}
 
function ovh_registry_delete_nameserver ($adm_login,$adm_pass,$subdomain,$domain_name){

 $regz["is_success"] = 0;

try {
//login
   $soap = ovh_open();
   $session = login_ovh();
   
//domainHostDel
 $result = $soap->domainHostDel($session,$domain_name,$subdomain);
 $regz["is_success"] = 1;

 //logout
 logout_ovh($soap,$session);

} catch(SoapFault $fault) {
 echo $fault;
}
if($regz["is_success"] == 1){
$regz["response_text"] = $result;
} else{
$regz["response_text"] = "Delete failed";
}

return $regz;
}

function ovh_registry_modify_nameserver ($adm_login,$adm_pass,$subdomain,$domain_name,$ip){

$regz["is_success"] = 0;

try {
//login
   $soap = ovh_open();
   $session = login_ovh();
   
//domainHostUpdate
 $result = $soap->domainHostUpdate($session, "$domain_name", "$subdomain", "$ip");
 $regz["is_success"] = 1;
 
  //logout
 logout_ovh($soap,$session);

} catch(SoapFault $fault) {
 echo $fault;
}

if($regz["is_success"] == 1){
$regz["response_text"] = $result;
} else{
$regz["response_text"] = "Update failed";
}

return $regz;
}

function ovh_registry_register_domain ($adm_login,$adm_pass,$fqdn,$period,$contacts,$dns_servers,$new_user){
	
	global $conf_ovh_username;
	global $conf_ovh_nicadmin;
	global $conf_ovh_nictech;
	global $conf_ovh_nicowner;
	global $conf_ovh_nicbilling;
	global $conf_ovh_boolean;
	global $conf_addr_primary_dns;
        global $conf_addr_secondary_dns;

	$ovh_boolean = $conf_ovh_boolean;
	$ovh_username = $conf_ovh_username;
	$dnsfirst_ovh = $conf_addr_primary_dns;
        $dnssecond_ovh = $conf_addr_secondary_dns;

        $ovh_domain_name = $fqdn;
        $ovh_nicadmin = $conf_ovh_nicadmin;
        $ovh_nictech = $conf_ovh_nictech;
        $ovh_nicowner = $conf_ovh_nicowner;
        $ovh_nicbilling = $conf_ovh_nicbilling;

        $ovh_dns1 = $dns_servers[0];
        $ovh_dns2 = $dns_servers[1];

if($dns_servers[0] == "default"){
	$ovh_dns1 = $dnsfirst_ovh;
}
if($dns_servers[1] == "default"){
	$ovh_dns2 = $dnssecond_ovh;
}
if (isset($dns_servers[2])) {
       $ovh_dns3 = $dns_servers[2];
	   }else{
		$ovh_dns3 = "";   }
if (isset($dns_servers[3])) {
       $ovh_dns4 = $dns_servers[3];
	   }else{
		$ovh_dns4 = "";   }
if (isset($dns_servers[4])) {
       $ovh_dns5 = $dns_servers[4];
	   }else{
		$ovh_dns5 = "";   }

$regz["is_success"] = 0;

try {

//login
   $soap = ovh_open();
   $session = login_ovh();


 //resellerDomainCreate
  $soap->resellerDomainCreate( $session, $ovh_domain_name, "none", "gold", "whiteLabel", "no", "$ovh_nicowner", "$ovh_nicadmin", "$ovh_nictech", "$ovh_nicbilling", "$ovh_dns1", "$ovh_dns2", "$ovh_dns3", "$ovh_dns4", "$ovh_dns5", "method : seulement pour les .fr (AFNIC) : méthode d'identification (siren | inpi | birthPlace | afnicIdent)", "legalName : seulement pour les .fr (AFNIC) : nom de la société / propriétaire de la marque", "legalNumber : seulement pour les .fr (AFNIC) : numéro SIREN/SIRET/INPI", "afnicIdent : seulement pour les .fr (AFNIC) : clé d'identification AFNIC (format XXXXXXXX-999)", "birthDate : seulement pour les .fr (AFNIC) : date d'anniversaire du propriétaire", "birthCity : seulement pour les .fr (AFNIC) : ville de naissance du propriétaire", "birthDepartement : seulement pour les .fr (AFNIC) : département de naissance du propriétaire", "birthCountry : seulement pour les .fr (AFNIC) : pays de naissance du propriétaire", $ovh_boolean);
  $regz["is_success"] = 1;

 //logout
 logout_ovh($soap,$session);

} catch(SoapFault $fault) {
 echo $fault;
}
if($regz["is_success"] == 1){
	$regz["response_text"] = "Registration successful";
} else{
	$regz["response_text"] = "Registration failed";
	}

 return $regz;
}

function ovh_registry_update_whois_dns($adm_login,$adm_pass,$domain_name,$dns,$dns_ip=array()){
$regz["is_success"] = 0;

try {
//login
   $soap = ovh_open();
   $session = login_ovh();
   
//domainDnsUpdate
 $result = $soap->domainDnsUpdate($session, $domain-name, $dns[0], $dns_ip[0], $dns[1], $dns_ip[1], $dns[2], $dns_ip[2], $dns[3], $dns_ip[3], $dns[4], $dns_ip[4]);
 print_r($result);
  $regz["is_success"] = 1;
  
   //logout
 logout_ovh($soap,$session);

} catch(SoapFault $fault) {
  echo $fault;
}
if($regz["is_success"] == 1){
$regz["response_text"] = $result;
} else{
$regz["response_text"] = "Update DNS failed";
}

return $regz;
}

function ovh_registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts){
$ret["is_success"] = 0;

try {
//login
   $soap = ovh_open();
   $session = login_ovh();

 //Admin
 $soap->serviceModifyContact($session, $domain_name, "DOMAIN", "nicadmin", $contacts['admin']['ovh_id']);
 
 //Teck
 $soap->serviceModifyContact($session, $domain_name, "DOMAIN", "nictech", $contacts['teck']['ovh_id']);


 //Billing
 $soap->serviceModifyContact($session, $domain_name, "DOMAIN", "nicbilling", $contacts['billing']['ovh_id']);


   //logout
 logout_ovh($soap,$session);

$ret["is_success"] = 1;

} catch(SoapFault $fault) {
  echo $fault;
}
if($ret["is_success"] == 1){
$ret["response_text"] = "Contacts updated successfully";
}
if($ret["is_success"] == 0){
$ret["response_text"] = "Contacts update failed";
}
return $ret;
}

function ovh_registry_check_transfer($domain) {
	 
	try {
		
   $soap = ovh_open();
   $session = login_ovh();

 //domainCheck
 $result = $soap->domainCheck($session, "$domain");
 
 //logout
logout_ovh($soap,$session);

} catch(SoapFault $fault) {
  echo $fault;
}

$ret["is_success"] = 1;
$ret["attributes"]["reason"] = "Domain name can't be transfered";
	if($result[1]->value == 1){
		    $ret["attributes"]["transferrable"] = 1;
  } else {
		    $ret["attributes"]["transferrable"] = 0;
	}
		
	return $ret;
	}
	
function ovh_registry_transfert_domain($adm_login,$adm_pass,$domain_name,$contacts,$dns_servers,$new_user) {
	
	global $conf_ovh_username;
	global $conf_ovh_nicadmin;
	global $conf_ovh_nictech;
	global $conf_ovh_nicowner;
	global $conf_ovh_nicbilling;
	global $conf_ovh_boolean;
	global $conf_addr_primary_dns;
        global $conf_addr_secondary_dns;

	$ovh_boolean = $conf_ovh_boolean;
	$ovh_username = $conf_ovh_username;
	$dnsfirst_ovh = $conf_addr_primary_dns;
        $dnssecond_ovh = $conf_addr_secondary_dns;

        $ovh_domain_name = $domain_name;
        $ovh_nicadmin = $conf_ovh_nicadmin;
        $ovh_nictech = $conf_ovh_nictech;
        $ovh_nicowner = $conf_ovh_nicowner;
        $ovh_nicbilling = $conf_ovh_nicbilling;

        $ovh_dns1 = $dns_servers[0];
        $ovh_dns2 = $dns_servers[1];

if($dns_servers[0] == "default"){
	$ovh_dns1 = $dnsfirst_ovh;
}
if($dns_servers[1] == "default"){
	$ovh_dns2 = $dnssecond_ovh;
}
if (isset($dns_servers[2])) {
       $ovh_dns3 = $dns_servers[2];
	   }else{
		$ovh_dns3 = "";   }
if (isset($dns_servers[3])) {
       $ovh_dns4 = $dns_servers[3];
	   }else{
		$ovh_dns4 = "";   }
if (isset($dns_servers[4])) {
       $ovh_dns5 = $dns_servers[4];
	   }else{
		$ovh_dns5 = "";   }
	$regz["is_success"] = 0; 
	
    try {
   $soap = ovh_open();
   $session = login_ovh();

 //resellerDomainTransfer
$soap->resellerDomainTransfer($session, "$ovh_domain_name", "authinfo", "none", "gold", "whiteLabel", "no", "$ovh_nicowner", "$ovh_nicadmin", "$ovh_nictech", "$ovh_nicbilling", "$ovh_dns1", "$ovh_dns2", "$ovh_dns3", "$ovh_dns4", "$ovh_dns5", "(siren | inpi | birthPlace | afnicIdent)", "nom de la société / propriétaire de la marque", "numéro SIREN/SIRET/INPI", "clé d'identification AFNIC (format XXXXXXXX-999)", "date d'anniversaire du propriétaire", "ville de naissance du propriétaire", "département de naissance du propriétaire", "pays de naissance du propriétaire", $ovh_boolean);
 $regz["is_success"] = 1;
 
 //logout
logout_ovh($soap,$session);

} catch(SoapFault $fault) {
  echo $fault;
} 
if($regz["is_success"] == 1){
	$regz["response_text"] = "Transfert successful";
} else{
	$regz["response_text"] = "Transfert failed";
	}
return $regz;
 }  

function ovh_registry_check_renew($domain_name) {
	 	try {
		
   $soap = ovh_open();
   $session = login_ovh();

 //domainCheck
 $result = $soap->domainCheck($session,$domain_name);
 
 //logout
logout_ovh($soap,$session);

} catch(SoapFault $fault) {
  echo $fault;
}

$ret["is_success"] = 1;
$ret["response_text"] = "Domain name can't be renewed";
	if($result[2]->value == 1){
		    $ret["attributes"]["renewable"] = 1;
  } else {
		    $ret["attributes"]["renewable"] = 0;
	}
		
	return $ret;
	}


function ovh_registry_renew_domain($domain_name,$years) {
	global $conf_ovh_boolean;
	$ovh_boolean = $conf_ovh_boolean;
	
	$regz["is_success"] = 0;
	$regz["response_text"] = "Renew Failed";
	
	//login 
try {	
   $soap = ovh_open();
   $session = login_ovh();
   
	//resellerDomainRenew
 $soap->resellerDomainRenew($session, $domain_name, $ovh_boolean);
 if($years != 1){

return array( "attributes" => array("status" => 1), "response_text" => _("Only one year renewals with OVH.") );

}
 $regz["is_success"] = 1;
 $regz["attributes"]["status"]= 0;
 $regz["response_text"] = "Renew successful";

 //logout
   logout_ovh($soap,$session);

} catch(SoapFault $fault) {
  echo $fault;
} 
return $regz;
 }




function ovh_registry_get_whois($domain_name) {
$post_params_hash["domain"] = $domain_name;

try {
	
   $soap = ovh_open();
   $session = login_ovh();

 //domainInfo
 $result = $soap->domainInfo($session, "$domain_name");
 
 //logout
   logout_ovh($soap,$session);

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
if (isset($result->nicowner)) {
$ret["response_text"] .="\n \n Owner name : ".$result->nicowner;
}
if (isset($result->nicadmin)) {
$ret["response_text"] .="\n Admin name : ".$result->nicadmin;
}
if (isset($result->nictech)) {
$ret["response_text"] .="\n Tech name : ".$result->nictech;
}
if (isset($result->nicbilling)) {
$ret["response_text"] .="\n Billing name : ".$result->nicbilling;
}
if (isset($result->authinfo)) {
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
$ret["response_text"] .="\n Name server 3 : ".$result->dns[2]->name;
}
if (isset($result->dns[2]->ip)) {
$ret["response_text"] .="\n ip ns3 : ".$result->dns[2]->ip;
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

function ovh_registry_get_auth_code(){
}

function ovh_registry_add_nick_handle($a){
global $pro_mysql_handle_table;
$ovh_nic_mdp = mdpauto();
echo _("Your password : "), $ovh_nic_mdp;
/*
Parameters

    * stringpassword : the contact password
    * stringarea : the contact area
    * stringlanguage : the contact language (fr|en|pl|es|de)
    * booleanisOwner : is it an owner nic ? default false
    * stringlegalform : the contact legalform (corporation|individual|association|other)
    * stringlegalName : the contact legalname
    * stringlegalNumber : the contact legalnumber (SIRET/SIREN/...)
    * stringvat : the contact vat

Returns
the new contact handle id
*/
try {
	
   $soap = ovh_open();
   $session = login_ovh();

 //nicCreate
 $result = $soap->nicCreate($session, $a['lastname'], $a['firstname'], $ovh_nic_mdp, $a['email'], $a['phone_num'], $a['fax_num'], $a['addr1'].' '.$a['addr2'].' '.$a['addr3'], $a['city'], $a['state'], $a['zipcode'], $a['country'], "en", false, "other", $a['company'], "legalName", "legalNumber", "vat");
 
 //logout
   logout_ovh($soap,$session);

} catch(SoapFault $fault) {
  echo $fault;
}
	if (isset($result)){
        $query = "UPDATE `$pro_mysql_handle_table` SET `ovh_passwd`='$ovh_nic_mdp', `ovh_id`='$result' WHERE `id`='$a[id]';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
        }
}

function ovh_registry_modify_nick_handle($a){

           global $conf_ovh_server_url;
           global $conf_ovh_boolean;
	   global $conf_ovh_language;

try {
	
$soap = new SoapClient("$conf_ovh_server_url");

 //login
 $session = $soap->login($a['ovh_id'], $a['ovh_passwd'], $conf_ovh_language, $conf_ovh_boolean);

//nicModifyInfos
 $soap->nicModifyInfos($session, $a['lastname'], $a['firstname'], "other", $a['company'], $a['addr1'].' '.$a['addr2'].' '.$a['addr3'], $a['zipcode'], $a['city'], $a['country'], $a['phone_num'], $a['fax_num'], $a['email'], "spareEmail", "en", "vat");

//logout
   logout_ovh($soap,$session);

} catch(SoapFault $fault) {
  echo $fault;
}


}

function ovh_registry_delete_nick_handle($a){
echo "delete";
}

$configurator = array(
	"title" => _("OVH configuration"),
	"action" => "configure_ovh_editor",
	"forward" => array("rub","sousrub"),
	"cols" => array(
                 "ovh_server_url" => array(
			"legend" => _("Server address : "),
			"type" => "text",
			"size" => "20"),
		"ovh_username" => array(
			"legend" => _("Username : "),
			"type" => "text",
			"size" => "20"),
		"ovh_password" => array(
			"legend" => _("Password : "),
			"type" => "text",
			"size" => "20"),
		"ovh_language" => array(
			"legend" => _("Language : "),
			"type" => "popup",
			"values" => array("fr", "en", "es", "de", "pl", "it", "pt", "nl", "cz", "ie"),
			"display_replace" => array( _("French"), _("English"), _("Spanish"), _("German"), _("Polish"), _("Italian"), _("Portuguese"), _("Duch"), _("cz"), _("ie") )),
		"ovh_boolean" => array(
			"legend" => _("OVH Server : "),
			"type" => "radio",
			"values" => array("true","false"),
			"display_replace" => array( _("Live"), _("Test"))),
		"ovh_nicadmin" => array(
			"legend" => _("Nic admin : "),
			"type" => "text",
			"size" => "20"),
		"ovh_nictech" => array(
			"legend" => _("Nic tech : "),
			"type" => "text",
			"size" => "20"),
		"ovh_nicowner" => array(
			"legend" => _("Nic owner : "),
			"type" => "text",
			"size" => "20"),
		"ovh_nicbilling" => array(
			"legend" => _("Nic billing : "),
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
"registry_get_auth_code" => "ovh_registry_get_auth_code",
"registry_register_domain" => "ovh_registry_register_domain",
"registry_update_whois_info" => "ovh_registry_update_whois_info",
"registry_update_whois_dns" => "ovh_registry_update_whois_dns",
"registry_check_transfer" => "ovh_registry_check_transfer",
"registry_renew_domain" => "ovh_registry_renew_domain",
"registry_change_password" => "ovh_registry_change_password",
"registry_get_whois" => "ovh_registry_get_whois",
"registry_transfert_domain" => "ovh_registry_transfert_domain",
"registry_create_nick_handle" => "ovh_registry_add_nick_handle",
"registry_edit_nick_handle" => "ovh_registry_modify_nick_handle",
"registry_delete_nick_handle" => "ovh_registry_delete_nick_handle",
"registry_check_renew" => "ovh_registry_check_renew"
);?>
