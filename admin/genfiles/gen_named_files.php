<?php

function get_remote_ns($a){
	$flag = false;
	$url = $a["server_addr"].'/dtc/list_domains.php?action=list_dns&login='.$a["server_login"].'&pass='.$a["server_pass"];
	while($retry < 3 && $flag == false){
		$lines = file ($url);
		$nline = sizeof($lines);

		if(strstr($lines[0],"// Start of DTC generated slave zone file for backuping") &&
			strstr($lines[$nline-1],"// End of DTC generated slave zone file for backuping")){
			for($j=0;$j<$nline;$j++){
				$named_file .= $lines[$j];
			}
			$flag = true;
		}
		$retry ++;
		if($flag == false)	sleep(5);
	}
	if($flag == false)	return false;
	else		return $named_file;
}

function get_remote_ns_domains(){
	global $pro_mysql_backup_table;
	global $conf_generated_file_path;
	global $console;

	// Get all domains from the servers for wich we act as backup MX
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='dns_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$retry = 0;
		$flag = false;
		$a = mysql_fetch_array($r);
		$u = remove_url_protocol($a["server_addr"]);
		if($u == false)	return false;
		$f = $conf_generated_file_path."/dns_domains.".$u;
		if($a["status"] == "pending" || !file_exists($f)){
			$console = "Getting dns domain list from ".$a["server_addr"]."/dtc/list_domains.php with login ".$a["server_login"]." and writting to disk...";
			$remote_file = get_remote_ns($a);
			if($remote_file != false){
				$fp = fopen($f,"w+");
				fwrite($fp,$remote_file);
				fclose($fp);
				$domain_list .= $remote_file;
				$q2 = "UPDATE $pro_mysql_backup_table SET status='done' WHERE id='".$a["id"]."';";
				$r2 = mysql_query($q2)or die("Cannot query $q2 ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
				$console .= "ok!<br>";
			}else{
				$console .= "failed!<br>";
			}
		}else{
			$console = "Using mail domain list from cache of ".$a["server_addr"]."...<br>";
			$fp = fopen($f,"r");
			fseek($fp,0,SEEK_END);
			$size = ftell($fp);
			fseek($fp,0,SEEK_START);
			$domain_list .= fread($fp,$size);
			fclose($fp);
		}
	}
	return $domain_list;
}

function named_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;
	global $pro_apache_vhost_path;
	global $pro_mysql_backup_table;

	global $console;

	global $conf_main_site_ip;

	global $conf_use_multiple_ip;
	global $conf_webmaster_email_addr;
	$bind_formated_webmaster_email_addr = str_replace('@',".",$conf_webmaster_email_addr).".";
	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;
	global $conf_addr_mail_server;
	global $conf_addr_backup_mail_server;
	global $conf_ip_slavezone_dns_server;

	global $conf_generated_file_path;

	global $conf_named_path;
	global $conf_named_zonefiles_path;
	global $conf_named_slavefile_path;
	global $conf_named_slavezonefiles_path;

	$todays_serial = date("YmdH");

	$djb_file = "";
	$named_file = "";

	$query = "SELECT * FROM $pro_mysql_domain_table WHERE primary_dns='default' ORDER BY name;";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){//		die("No account to generate");
	}
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user");
		$web_name = $row["name"];
		$web_owner = $row["owner"];
		$web_serial_flag = $row["generate_flag"];
		$ip_addr = $row["ip_addr"];
		
		// Get DNS addresses from database. Switch to $conf_* values if "default" is found
		if($row["primary_dns"] == "default"){
			$thisdomain_dns1 = $conf_addr_primary_dns;
		}else{
			$thisdomain_dns1 = $row["primary_dns"];
		}
		$more_dns_server = "";
		if($row["other_dns"] == "default"){
			$thisdomain_dns2 = $conf_addr_secondary_dns;
		}else{
			$all_dns = explode("|",$row["other_dns"]);
			$thisdomain_dns2 = $all_dns[0];
			$nbr_other_dns = sizeof($all_dns);
			for($z=1;$z<$nbr_other_dns;$z++){
				$more_dns_server .= "@	IN	NS	".$all_dns[$z].".\n";
			}
		}

		if($row["primary_mx"] == "default"){
			$thisdomain_mx1 = $conf_addr_mail_server;
		}else{
			$thisdomain_mx1 = $row["primary_mx"];
		}

		$more_mx_server = "";
		if($row["other_mx"] == "default"){
			if($conf_addr_backup_mail_server != ""){
				$all_mx = explode("|",$conf_addr_backup_mail_server);
				$nbr_other_mx = sizeof($all_mx);
				$MX_number = 10;
				for($z=0;$z<$nbr_other_mx;$z++){
					$more_mx_server .= "@	IN	MX	".$MX_number."	".$all_mx[$z].".\n";
					$MX_number += 5;
				}
			}
		}else{
			$all_mx = explode("|",$row["other_mx"]);
			$nbr_other_mx = sizeof($all_mx);
			$MX_number = 10;
			for($z=0;$z<$nbr_other_mx;$z++){
				$more_mx_server .= "@	IN	MX	".$MX_number."	".$all_mx[$z].".\n";
				$MX_number += 5;
			}
		}

		$web_extention = substr($web_name,-strpos(strrev($web_name),'.'));

		$web_default_subdomain = $row["default_subdomain"];
		
		// Get the owner informations
		$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$web_owner';";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 != 1){
			die("No user of that name !");
		}
		$webadmin = mysql_fetch_array($result2) or die ("Cannot fetch user");
		$web_path = $webadmin["path"];

		// Grab all subdomains
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 < 1){
			die("No subdomain for domain $web_name !");
		}
		if($conf_use_multiple_ip == "yes"){
			$ip_to_write = $ip_addr;
		}else{
			$ip_to_write = $conf_main_site_ip;
		}
		$named_file .= "zone \"$web_name\" IN {
	type master;
	file \"$conf_generated_file_path/$conf_named_zonefiles_path/$web_name\";
};
";
		$slave_file .= "zone \"$web_name\" {
	type slave;
	masters { $conf_ip_slavezone_dns_server; };
	file \"$conf_generated_file_path/$conf_named_slavezonefiles_path/$web_name\";
};
";

		$this_site_file = "\$TTL 7200
@               IN      SOA     $thisdomain_dns1. $bind_formated_webmaster_email_addr (
						$todays_serial; serial
                        6H ; refresh
                        60M ; retry
                        1W ; expire
                        24H ; default_ttl
                        )
@	IN	NS	$thisdomain_dns1.
@	IN	NS	$thisdomain_dns2.
$more_dns_server
@	IN	MX	5	$thisdomain_mx1.
$more_mx_server
	IN	A	$ip_to_write
";
		// Add all subdomains to it !
		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];
			if($subdomain["ip"] == "default"){
				$the_ip_writed = $ip_to_write;
			}else{
				$the_ip_writed = $subdomain["ip"];
			}
			if($newsubdomain_name == "pop"){
				$is_pop_subdomain_set = yes;
			}
			if($newsubdomain_name == "smtp"){
				$is_smtp_subdomain_set = yes;
			}
			if($newsubdomain_name == "ftp"){
				$is_ftp_subdomain_set = yes;
			}
			if($newsubdomain_name == "list"){
				$is_list_subdomain_set = yes;
			}
			$this_site_file .= "$web_subname	IN	A	$the_ip_writed\n";
		}
		if( $is_pop_subdomain_set != yes){
			$this_site_file .= "pop	IN	A	$ip_to_write\n";
		}
		if( $is_smtp_subdomain_set != yes){
			$this_site_file .= "smtp	IN	A	$ip_to_write\n";
		}
		if( $is_ftp_subdomain_set != yes){
			$this_site_file .= "ftp	IN	A	$ip_to_write\n";
		}
		if( $is_list_subdomain_set != yes){
			$this_site_file .= "list	IN	A	$ip_to_write\n";
		}

		if($web_serial_flag=="yes"){
			$console .= "Updating zone file for domain $web_name using serial : $todays_serial, ipaddr : $ip_to_write<br>";
			$filep = fopen("$conf_generated_file_path/$conf_named_zonefiles_path/$web_name", "w+");
			if( $filep == NULL){
				die("Cannot open file for writting");
			}
			fwrite($filep,$this_site_file);
			fclose($filep);
			$query_serial = "UPDATE $pro_mysql_domain_table SET generate_flag='no' WHERE name='$web_name' LIMIT 1;";
			$result_serial = mysql_query ($query_serial)or die("Cannot execute query \"$query_serial\"");
		}
	}

/*	// Get all domains for wich we will act as backup NS
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='dns_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$flag = false;
		$retry = 0;
		$a = mysql_fetch_array($r);
		while($retry < 3 && $flag == false){
			$lines = file ($a["server_addr"].'/dtc/list_domains.php?action=list_dns&login='.$a["server_login"].'&pass='.$a["server_pass"]);
			$nline = sizeof($lines);
			if(strstr($lines[0],"// Start of DTC generated slave zone file for backuping") &&
				strstr($lines[$nline-1],"// End of DTC generated slave zone file for backuping")){
				for($j=0;$j<$nline;$j++){
					$named_file .= $lines[$j];
				}
				$flag = true;
			}
			$retry++;
			if($flag == false)	sleep(5);
		}
	}
*/	$named_file .= get_remote_ns_domains();

	// Write of the master zone file
	$filep = fopen("$conf_generated_file_path/$conf_named_path", "w+");
	if( $filep == NULL){
		die("Cannot open file \"$conf_generated_file_path/$conf_named_path\" for writting");
	}
	fwrite($filep,$named_file);
	fclose($filep);

	// Write of the slave zone file
	$filep = fopen("$conf_generated_file_path/$conf_named_slavefile_path","w+");
	if( $filep == NULL){
		die("Cannot open file \"$conf_generated_file_path/$conf_named_slavefile_path\" for writting");
	}
	fwrite($filep,$slave_file);
	fclose($filep);

	return true;
}

?>
