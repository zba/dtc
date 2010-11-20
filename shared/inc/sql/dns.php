<?php

/////////////////////////////////////////////////////////////
// Submit a new DNS and MX config for a domain to database //
/////////////////////////////////////////////////////////////
// adm_login=test&adm_pass=test&edit_domain=toto.com&addrlink=test.com&new_dns_1=default&new_dns_2=t0x.aegis-corp.org&new_dns_3=ns1.namebay.com&new_dns_4=ns2.namebay.com&new_dns_5=&new_mx_1=default&new_mx_2=mx1.anotherlight.com&new_mx_3=mx1.namebay.com&new_mx_4=mx2.namebay.com&new_mx_5=&new_dns_and_mx_config=Ok
if(isset($_REQUEST["new_dns_and_mx_config"]) && $_REQUEST["new_dns_and_mx_config"] == "Ok"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);

	if(isset($_REQUEST["new_dns_1"]) && isHostnameOrIP($_REQUEST["new_dns_1"])){
		$new_dns_1 = $_REQUEST["new_dns_1"];
	}else{
		$new_dns_1 = "default";
	}
	if(isset($_REQUEST["new_dns_2"]) && isHostnameOrIP($_REQUEST["new_dns_2"])){
		$new_dns_2 = $_REQUEST["new_dns_2"];
	}else{
		$new_dns_2 = $new_dns_2 = "default";
	}
	if(isset($_REQUEST["new_dns_3"]) && isHostnameOrIP($_REQUEST["new_dns_3"])){
		$new_dns_3 = $_REQUEST["new_dns_3"];
	}else{
		$new_dns_3 = $new_dns_3 = "";
	}
	if(isset($_REQUEST["new_dns_4"]) && isHostnameOrIP($_REQUEST["new_dns_4"])){
		$new_dns_4 = $_REQUEST["new_dns_4"];
	}else{
		$new_dns_4 = $new_dns_4 = "";
	}
	if(isset($_REQUEST["new_dns_5"]) && isHostnameOrIP($_REQUEST["new_dns_5"])){
		$new_dns_5 = $_REQUEST["new_dns_5"];
	}else{
		$new_dns_5 = $new_dns_5 = "";
	}
	if(isset($_REQUEST["new_dns_6"]) && isHostnameOrIP($_REQUEST["new_dns_6"])){
		$new_dns_6 = $_REQUEST["new_dns_6"];
	}else{
		$new_dns_6 = $new_dns_6 = "";
	}

	if(isset($_REQUEST["new_mx_1"]) && isHostnameOrIP( strtolower($_REQUEST["new_mx_1"]) )){
		$new_mx_1 = strtolower($_REQUEST["new_mx_1"]);
	}else{
		$new_mx_1 = "default";
	}
	if(isset($_REQUEST["new_mx_2"]) && isHostnameOrIP( strtolower($_REQUEST["new_mx_2"]) )){
		$new_mx_2 = strtolower($_REQUEST["new_mx_2"]);
	}else{
		$new_mx_2 = "default";
	}
	if(isset($_REQUEST["new_mx_3"]) && isHostnameOrIP( strtolower($_REQUEST["new_mx_3"]) )){
		$new_mx_3 = strtolower($_REQUEST["new_mx_3"]) ;
	}else{
		$new_mx_3 = "";
	}
	if(isset($_REQUEST["new_mx_4"]) && isHostnameOrIP( strtolower($_REQUEST["new_mx_4"]) )){
		$new_mx_4 = strtolower($_REQUEST["new_mx_4"]);
	}else{
		$new_mx_4 = "";
	}
	if(isset($_REQUEST["new_mx_5"]) && isHostnameOrIP( strtolower($_REQUEST["new_mx_5"]) )){
		$new_mx_5 = strtolower($_REQUEST["new_mx_5"]);
	}else{
		$new_mx_5 = "";
	}
	if(isset($_REQUEST["new_mx_6"]) && isHostnameOrIP( strtolower($_REQUEST["new_mx_6"]) )){
		$new_mx_6 = strtolower($_REQUEST["new_mx_6"]);
	}else{
		$new_mx_6 = "";
	}

	// Trims the eventual last . of the string for MX, as this is a common mistake
	if( substr($new_mx_1, strlen($new_mx_1)-1 ) == "."){
		$new_mx_1 = substr($new_mx_1,0,strlen($new_mx_1)-1);
	}
	if( substr($new_mx_2, strlen($new_mx_2)-1 ) == "."){
		$new_mx_2 = substr($new_mx_2,0,strlen($new_mx_2)-1);
	}
	if( substr($new_mx_3, strlen($new_mx_3)-1 ) == "."){
		$new_mx_3 = substr($new_mx_3,0,strlen($new_mx_3)-1);
	}
	if( substr($new_mx_4, strlen($new_mx_4)-1 ) == "."){
		$new_mx_4 = substr($new_mx_4,0,strlen($new_mx_4)-1);
	}
	if( substr($new_mx_5, strlen($new_mx_5)-1 ) == "."){
		$new_mx_5 = substr($new_mx_5,0,strlen($new_mx_5)-1);
	}
	if( substr($new_mx_6, strlen($new_mx_6)-1 ) == "."){
		$new_mx_6 = substr($new_mx_6,0,strlen($new_mx_6)-1);
	}

	if($new_dns_2 != "default" && isset($new_dns_2) && $new_dns_2 != ""){
		if(isset($new_dns_3) && $new_dns_3 != ""){
			$new_dns_2 .= "|".$new_dns_3;
		}
		if(isset($new_dns_4) && $new_dns_4 != ""){
			$new_dns_2 .= "|".$new_dns_4;
		}
		if(isset($new_dns_5) && $new_dns_5 != ""){
			$new_dns_2 .= "|".$new_dns_5;
		}
		if(isset($new_dns_6) && $new_dns_6 != ""){
			$new_dns_2 .= "|".$new_dns_6;
		}
	}
	if($new_mx_2 != "default" && isset($new_mx_2) && $new_mx_2 != ""){
		if(isset($new_mx_3) && $new_mx_3 != ""){
			$new_mx_2 .= "|".$new_mx_3;
		}
		if(isset($new_mx_4) && $new_mx_4 != ""){
			$new_mx_2 .= "|".$new_mx_4;
		}
		if(isset($new_mx_5) && $new_mx_5 != ""){
			$new_mx_2 .= "|".$new_mx_5;
		}
		if(isset($new_mx_6) && $new_mx_6 != ""){
			$new_mx_2 .= "|".$new_mx_6;
		}
	}

	// If domain whois is hosted here, change the whois value using a registry call.
	if(file_exists($dtcshared_path."/dtcrm")){
		$query = "SELECT * FROM $pro_mysql_domain_table WHERE name='$edit_domain';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
		$row = mysql_fetch_array($result);
		if($row["whois"] == "here"){
			$regz = registry_update_whois_dns($adm_login,$adm_pass,$edit_domain,"$new_dns_1|$new_dns_2");
			if($regz["is_success"] != 1){
				die("<font color=\"red\"><b>Whois update failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i>");
			}
		}
	}

	$query = "UPDATE $pro_mysql_domain_table SET primary_dns='$new_dns_1',other_dns='$new_dns_2',primary_mx='$new_mx_1',other_mx='$new_mx_2',txt_root_entry='".mysql_real_escape_string($_REQUEST["txt_root_entry"])."',txt_root_entry2='".mysql_real_escape_string($_REQUEST["txt_root_entry2"])."' WHERE owner='$adm_login' AND name='$edit_domain';";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());

	$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	$domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

	updateUsingCron("gen_vhosts='yes',restart_apache='yes',gen_named='yes',reload_named ='yes',restart_qmail='yes',qmail_newu='yes',gen_qmail='yes',gen_fetchmail='yes'");
	triggerDomainListUpdate();
}

?>