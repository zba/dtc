<?php
/**
 * Don't remove this comment for control version
 * @package DTC
 * @copyright LGPL
 * @version $Id: gen_named_files.php,v 1.59 2007/06/22 19:46:23 seeb Exp $
 * $Log: gen_named_files.php,v $
 * Revision 1.59  2007/06/22 19:46:23  seeb
 * w3_alias support for domain
 *
 * Revision 1.58  2007/06/20 19:29:07  thomas
 * Hop!
 *
 * Revision 1.57  2007/06/15 21:39:10  seeb
 * add head to file
 *
 **/
function get_remote_ns($a){
	global $console;
	global $keep_dns_generate_flag;
	global $panel_type;
	$retry = 0;
	$flag = false;
	$named_file = ""; //init $named_file var
	$url = $a["server_addr"].'/dtc/list_domains.php?action=list_dns&login='.$a["server_login"].'&pass='.$a["server_pass"];
	while($retry < 3 && $flag == false){
		$a_vers = explode(".",phpversion());
		if($a_vers[0] <= 4 && $a_vers[1] < 3){
			if( $panel_type == "cronjob"){
				echo "\nUsing lynx -source on ".$a["server_addr"]." with login ".$a["server_login"].".\n";
			}else{
				$console .= "<br>Using lynx -source on ".$a["server_addr"]." with login ".$a["server_login"].".<br>\n";
			}
			$result = exec("lynx -source \"$url\"",$lines,$return_val);
		}else{
			if( $panel_type == "cronjob"){
				echo "\nUsing php HTTPRequest class on ".$a["server_addr"]." with login ".$a["server_login"].".";
			}else{
				$console .= "<br>Using php HTTPRequest class on ".$a["server_addr"]." with login ".$a["server_login"].".";
			}
			$httprequest = new HTTPRequest("$url");
			$lines = $httprequest->DownloadToStringArray();
		}
		$nline = sizeof($lines);

		if(strstr($lines[0],"// Start of DTC generated slave zone file for backuping") &&
			strstr($lines[$nline-1],"// End of DTC generated slave zone file for backuping")){
			for($j=0;$j<$nline;$j++){
				$named_file .= $lines[$j]."\n";
			}
			$flag = true;
			if( $panel_type == "cronjob"){
				echo "Success!\n";
			}else{
				$console .= "Success!<br>";
			}
		}
		$retry ++;
		if($flag == false){
			if( $panel_type == "cronjob"){
				$console .= "Failed: delaying 3s!\n";
			}else{
				$console .= "Failed: delaying 3s!<br>";
			}
			sleep(3);
		}
	}
	if($flag == false){
		$keep_dns_generate_flag = "yes";
		return false;
	}
	else		return $named_file;
}

function get_remote_ns_domains(){
	global $pro_mysql_backup_table;
	global $conf_generated_file_path;
	global $console;
	global $panel_type;

	$domain_list = "";

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
			if( $panel_type == "cronjob"){
				echo "Getting dns domain list from ".$a["server_addr"]."/dtc/list_domains.php with login ".$a["server_login"]." and writting to disk.\n";
			}else{
				$console .= "Getting dns domain list from ".$a["server_addr"]."/dtc/list_domains.php with login ".$a["server_login"]." and writting to disk.<br>";
			}
			$remote_file = get_remote_ns($a);
			if($remote_file != false){
				$fp = fopen($f,"w+");
				fwrite($fp,$remote_file);
				fclose($fp);

				// Check filesize before db update
				$fp = fopen($f,"r");
				$test = fseek($fp,0,SEEK_END);
				$size = ftell($fp);
				fclose($fp);

				if($size > 0){
					$domain_list .= $remote_file;
					$q2 = "UPDATE $pro_mysql_backup_table SET status='done' WHERE id='".$a["id"]."';";
					$r2 = mysql_query($q2)or die("Cannot query $q2 ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
					if( $panel_type == "cronjob"){
						echo "ok!\n";
					}else{
						$console .= "ok!<br>";
					}
					$flag = true;
				}else{
					if( $panel_type == "cronjob"){
						echo "wrong! File is empty!!!\n";
					}else{
						$console .= "wrong! File is empty!!!<br>";
					}
				}
			}else{
				if( $panel_type == "cronjob"){
					echo "failed!\n";
				}else{
					$console .= "failed!<br>";
				}
			}
		}
		if($flag == false){
			if (file_exists($f)){
				if( $panel_type == "cronjob"){
					echo "Using mail domain list from cache of ".$a["server_addr"]."...\n";
				}else{
					$console .= "Using mail domain list from cache of ".$a["server_addr"]."...<br>";
				}
				$fp = fopen($f,"r");
				$test = fseek($fp,0,SEEK_END);
				if ($test == -1){
					if( $panel_type == "cronjob"){
						echo "Failed to seek to end of $f\n";
					}else{
						$console .= "Failed to seek to end of $f<br>";
					}
				}
				$size = ftell($fp);
				if ($size > 0)
				{
					fseek($fp,0);
					$domain_list .= fread($fp,$size);
				} else {
					$console .= "File [" . $f . "] is empty\n";
				}
				fclose($fp);
			} else {
				if( $panel_type == "cronjob"){
					echo "Cache file not present, probably failed to read from remote host\n";
				}else{
					$console .= "Cache file not present, probably failed to read from remote host<br>";
				}
			}
		}
	}
	return $domain_list;
}

function calculate_reverse_end($ip_pool_ip,$ip_pool_netmask){
	$out = "";
	$ip_pool_ip_exploded = explode(".",$ip_pool_ip);
	switch($ip_pool_netmask){
	// Netblock: from /1 to /7
	case "128.0.0.0":
	case "192.0.0.0":
	case "224.0.0.0":
	case "240.0.0.0":
	case "248.0.0.0":
	case "252.0.0.0":
	case "254.0.0.0":
		$netmask_exploded = explode(".",$ip_pool_netmask);
		$end = $ip_pool_ip_exploded[0] + (255 - $netmask_exploded[0]);
		$out = $ip_pool_ip_exploded[1] . "-" . $end . ".in-addr.arpa";
		break;
	// Netblock: /8
	case "255.0.0.0":
		$out = $ip_pool_ip_exploded[0] . "in-addr.arpa";
		break;
	// Netblock: from /9 to /15
	case "255.128.0.0":
	case "255.192.0.0":
	case "255.224.0.0":
	case "255.240.0.0":
	case "255.248.0.0":
	case "255.252.0.0":
	case "255.254.0.0":
		$netmask_exploded = explode(".",$ip_pool_netmask);
		$end = $ip_pool_ip_exploded[1] + (255 - $netmask_exploded[1]);
		$out = $ip_pool_ip_exploded[1] . "-" . $end . "." . $ip_pool_ip_exploded[0] . ".in-addr.arpa";
		break;
	// Netblock: /16 = 65536 IPs
	case "255.255.0.0":
		$out = $ip_pool_ip_exploded[1] . "." . $ip_pool_ip_exploded[0] . ".in-addr.arpa";
		break;
	// Netblock: from /17 to /25
	case "255.255.128.0":
	case "255.255.192.0":
	case "255.255.224.0":
	case "255.255.240.0":
	case "255.255.248.0":
	case "255.255.252.0":
	case "255.255.254.0":
		$netmask_exploded = explode(".",$ip_pool_netmask);
		$end = $ip_pool_ip_exploded[2] + (255 - $netmask_exploded[2]);
		$out = $ip_pool_ip_exploded[2] . "-" . $end . "." . $ip_pool_ip_exploded[1] . "." . $ip_pool_ip_exploded[0] . ".in-addr.arpa";
		break;
	// Netblock: /24 = 256 IPs
	case "255.255.255.0":
		$out = $ip_pool_ip_exploded[2] . "." . $ip_pool_ip_exploded[1] . "." . $ip_pool_ip_exploded[0] . ".in-addr.arpa";
		break;
	// Netblock: from /25 to /31
	case "255.255.255.128":
	case "255.255.255.192":
	case "255.255.255.224":
	case "255.255.255.240":
	case "255.255.255.248":
	case "255.255.255.252":
	case "255.255.255.254":
		$netmask_exploded = explode(".",$ip_pool_netmask);
		$end = $ip_pool_ip_exploded[3] + (255 - $netmask_exploded[3]);
		$out = $ip_pool_ip_exploded[3] . "-" . $end . "." . $ip_pool_ip_exploded[2] . "." . $ip_pool_ip_exploded[1] . "." . $ip_pool_ip_exploded[0] . ".in-addr.arpa";
		break;
	// Netblock: /32 = 1 IP (Case of one zonefile per pool)
	case "255.255.255.255":
		$out = $ip_pool_ip_exploded[3] . "." . $ip_pool_ip_exploded[2] . "." . $ip_pool_ip_exploded[1] . "." . $ip_pool_ip_exploded[0] . ".in-addr.arpa";
		break;

	default:
		die("$netmask is not a netmask line ".__LINE__." file ".__FILE__);
	}
	return $out;
}

function rdns_zonefile_generate($ip_pool_id,$pool_ip_addr,$pool_netmask,$zone_type){
}

function rnds_generate(){
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_dedicated_ips_table;
	global $pro_mysql_ip_pool_table;

	global $conf_default_zones_ttl;
	global $conf_generated_file_path;
	global $conf_ip_allowed_dns_transfer;
	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;
	global $conf_webmaster_email_addr;
        $bind_formated_webmaster_email_addr = str_replace('@',".",$conf_webmaster_email_addr).".";

	global $conf_named_soa_refresh ;
	global $conf_named_soa_retry;
	global $conf_named_soa_expire;
	global $conf_named_soa_default_ttl;

        $todays_serial = date("YmdH");
	// Calculate the: allow-transfer { 203.174.86.120; 203.174.86.121; };
	// string that we have to add
	$allow_trans_str = "";
	if(strlen($conf_ip_allowed_dns_transfer) > 4){
		$more_allowed = explode("|",$conf_ip_allowed_dns_transfer);
		$v = sizeof($more_allowed);
		for($k=0; $k<$v; $k++){
			$allow_trans_str .= $more_allowed[$k] . "; ";
		}
	}
	if($allow_trans_str != ""){
		$allow_trans_str = "	allow-transfer { $allow_trans_str };\n";
	}

	$slave_dns_file = "// Reverse DNS zonefile generated by DTC
// do not edit, it will be generated again!
// instead, use DTC to enter any values
// This file is to be transfered in the
// DNS that will be slave of the current
// computer.
";
	$reverse_dns_file = "// Reverse DNS zonefile generated by DTC
// do not edit, it will be generated again!
// instead, use DTC to enter any values

";

	unset($tbl);
	$tbl = array();

	// Take only the zones for which a rdns_regen is set in the vps_ip table
	$q = "SELECT DISTINCT $pro_mysql_ip_pool_table.id,$pro_mysql_ip_pool_table.ip_addr,$pro_mysql_ip_pool_table.netmask,$pro_mysql_ip_pool_table.zone_type,$pro_mysql_ip_pool_table.custom_part
	FROM $pro_mysql_ip_pool_table,$pro_mysql_vps_ip_table
	WHERE $pro_mysql_vps_ip_table.rdns_regen='yes'
	AND $pro_mysql_ip_pool_table.id=$pro_mysql_vps_ip_table.ip_pool_id;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$num_vps = mysql_num_rows($r);
	for($i=0;$i<$num_vps;$i++){
		$a = mysql_fetch_array($r);
		$tbl_vps[$i] = $a;
		$tbl[$i] = $a;
	}
	// Update the table so it's not regenerated again, we consider
	$q = "UPDATE $pro_mysql_vps_ip_table SET rdns_regen='no';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	// Do same for the VPSes
	$q = "SELECT DISTINCT $pro_mysql_ip_pool_table.id,$pro_mysql_ip_pool_table.ip_addr,$pro_mysql_ip_pool_table.netmask,$pro_mysql_ip_pool_table.zone_type,$pro_mysql_ip_pool_table.custom_part
	FROM $pro_mysql_ip_pool_table,$pro_mysql_dedicated_ips_table
	WHERE $pro_mysql_dedicated_ips_table.rdns_regen='yes'
	AND $pro_mysql_ip_pool_table.id=$pro_mysql_dedicated_ips_table.ip_pool_id;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$num_ded = mysql_num_rows($r);
	unset($tbl);
	$tbl = array();
	$tbl_num_of_records = $num_vps + $num_ded;
	for($i=$num_vps;$i<$tbl_num_of_records;$i++){
		$a = mysql_fetch_array($r);
		$tbl_ded[$i] = $a;
		$tbl[$i] = $a;
	}
	$q = "UPDATE $pro_mysql_dedicated_ips_table SET rdns_regen='no';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	// Add code here for dedicated servers IPs
	for($i=0;$i<$tbl_num_of_records;$i++){
		$a = $tbl[$i];
		$ip_pool_id = $a["id"];
		$pool_ip_addr = $a["ip_addr"];
		$pool_netmask = $a["netmask"];
		$zone_type = $a["zone_type"];
		$custom_part = $a["custom_part"];

		switch($zone_type){
		case "ip_per_ip":
			unset($thiszoneIPs);
			unset($thiszoneVPSIPs);
			unset($thiszoneDEDIPs);
			$thiszoneIPs = array();
			$thiszoneVPSIPs = array();
			$thiszoneDEDIPs = array();
			$q2 = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_pool_id='$ip_pool_id';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_vps = mysql_num_rows($r2);
			for($j=0;$j<$num_vps;$j++){
				$a2 = mysql_fetch_array($r2);
				$thiszoneVPSIPs[] = $a2;
				$thiszoneIPs[] = $a2;
			}
			$q2 = "SELECT * FROM $pro_mysql_dedicated_ips_table WHERE ip_pool_id='$ip_pool_id';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_ded = mysql_num_rows($r2);
			for($j=0;$j<$num_ded;$j++){
				$a2 = mysql_fetch_array($r2);
				$thiszoneDEDIPs[] = $a2;
				$thiszoneIPs[] = $a2;
			}
			$num_of_IPs = sizeof($thiszoneIPs);
			for($j=0;$j<$num_of_IPs;$j++){
				$the_ip_addr = $thiszoneIPs[$j]["ip_addr"];
				$the_reverse = $thiszoneIPs[$j]["rdns_addr"];
				$zone_name = calculate_reverse_end($the_ip_addr,"255.255.255.255");
				$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$the_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";

				$zonefile_content = "\$TTL $conf_default_zones_ttl
@	$conf_default_zones_ttl	IN	SOA	".$conf_addr_primary_dns.". ".$bind_formated_webmaster_email_addr." (
			$todays_serial; serial
			$conf_named_soa_refresh ; refresh
			$conf_named_soa_retry ; retry
			$conf_named_soa_expire ; expire
			$conf_named_soa_default_ttl ; default_ttl
			)
	IN	NS	".$conf_addr_primary_dns.".
	IN	NS	".$conf_addr_secondary_dns.".
	PTR	".$the_reverse.".
";
				$filep = fopen("$conf_generated_file_path/reverse_zones/$the_ip_addr", "w+");
				if( $filep == NULL){
					die("Cannot open file \"$conf_generated_file_path/reverse_zones/$the_ip_addr\" for writting");
				}
				fwrite($filep,$zonefile_content);
				fclose($filep);
			}
			break;
		case "one_zonefile":
			$zone_name = calculate_reverse_end($pool_ip_addr,$pool_netmask);
			$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";
			// FIXME: need to use the same kind of technique for the zonefile serial increment as bellow...
			$zonefile_content = "\$TTL $conf_default_zones_ttl
@	$conf_default_zones_ttl	IN	SOA	".$conf_addr_primary_dns.". ".$bind_formated_webmaster_email_addr." (
			$todays_serial; serial
			$conf_named_soa_refresh ; refresh
			$conf_named_soa_retry ; retry
			$conf_named_soa_expire ; expire
			$conf_named_soa_default_ttl ; default_ttl
			)
	IN	NS	".$conf_addr_primary_dns.".
	IN	NS	".$conf_addr_secondary_dns.".
";
			unset($thiszoneIPs);
			unset($thiszoneVPSIPs);
			unset($thiszoneDEDIPs);
			$thiszoneVPSIPs = array();
			$thiszoneVPSIPs = array();
			$thiszoneDEDIPs = array();
			$q2 = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_pool_id='$ip_pool_id';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$num_vps = mysql_num_rows($r2);
			for($j=0;$j<$num_vps;$j++){
				$a = mysql_fetch_array($r2);
				$thiszoneVPSIPs[] = $a;
				$thiszoneIPs[] = $a;
			}
			$q2 = "SELECT * FROM $pro_mysql_dedicated_ips_table WHERE ip_pool_id='$ip_pool_id';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$num_ded = mysql_num_rows($r2);
			for($j=0;$j<$num_ded;$j++){
				$a = mysql_fetch_array($r2);
				$thiszoneVPSIPs[] = $a;
				$thiszoneIPs[] = $a;
			}
			$num_ip = $num_vps + $num_ded;
			for($j=0;$j<$num_ip;$j++){
				$a2 = $thiszoneIPs[$j];
				$the_ip_addr = $a2["ip_addr"];
				$the_reverse = $a2["rdns_addr"];
				// FIXME: works only for pool smaller than /24
				switch($pool_netmask){
				case "255.255.255.0":
				case "255.255.255.128":
				case "255.255.255.192":
				case "255.255.255.224":
				case "255.255.255.240":
				case "255.255.255.248":
				case "255.255.255.252":
				case "255.255.255.254":
					$the_ip_addr_exploded = explode(".",$the_ip_addr);
					$ip_to_reverse = $the_ip_addr_exploded[3];
					break;
				default:
					die("FIXME: works only for pool smaller than /24 line ".__LINE__." file ".__FILE__);
				}
				$zonefile_content .= "$ip_to_reverse	IN	PTR	".$the_reverse.".\n";
			}
			$zonefile_content .= $custom_part;
			// Write $conf_generated_file_path/reverse_zones/$pool_ip_addr
			$filep = fopen("$conf_generated_file_path/reverse_zones/$pool_ip_addr", "w+");
			if( $filep == NULL){
				die("Cannot open file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\" for writting");
			}
			fwrite($filep,$zonefile_content);
			fclose($filep);
			break;
		case "support_ticket":
			// Nothing to do here...
			break;
		}
		$q2 = "UPDATE $pro_mysql_vps_ip_table SET rdns_regen='no' WHERE ip_pool_id='$ip_pool_id';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}

	// Write the $reverse_dns_file
	$filep = fopen("$conf_generated_file_path/named.conf.reverse", "w+");
	if( $filep == NULL){
		die("Cannot open file \"$conf_generated_file_path/named.conf.reverse\" for writting");
	}
	fwrite($filep,$reverse_dns_file);
	fclose($filep);

	// FIXME: add the write of named.conf.slave.reverse CONTENT
	$filep = fopen("$conf_generated_file_path/named.conf.slave.reverse", "w+");
	if( $filep == NULL){
		die("Cannot open file \"$conf_generated_file_path/named.conf.slave.reverse\" for writting");
	}
	fwrite($filep,$reverse_dns_file);
	fclose($filep);
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
	global $conf_use_cname_for_subdomains;
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
	global $conf_ip_allowed_dns_transfer;
	global $conf_domainkey_publickey_filepath;
	global $conf_dtc_system_username;
	global $conf_dtc_system_groupname;
	global $conf_autogen_default_subdomains;
	global $conf_autogen_subdomain_list;
	global $conf_default_zones_ttl;
	global $conf_named_soa_refresh;
	global $conf_named_soa_retry;
	global $conf_named_soa_expire;
	global $conf_named_soa_default_ttl;

	$slave_file = "";
	$serial_prefix = date("Ymd");

	$djb_file = "";
	$named_file = "";

	$query = "SELECT * FROM $pro_mysql_domain_table WHERE primary_dns='default' OR other_dns='default' ORDER BY name;";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){//		die("No account to generate");
	}
	for($i=0;$i<$num_rows;$i++){
		unset($wildcard_dns_txt);
		$row = mysql_fetch_array($result) or die ("Cannot fetch user");
		$web_name = $row["name"];
		// for empty web_names, we need to skip
		// this is especially true for dedicated servers
		if (!isset($web_name) || $web_name=="")
		{
			continue;
		}
		$web_owner = $row["owner"];
		$web_serial_flag = $row["generate_flag"];
		$ip_addr = $row["ip_addr"];
		// domain wide TTL
		$domain_ttl = $conf_default_zones_ttl;
		if (isset($row["ttl"]))
		{
			$domain_ttl = $row["ttl"];	
		}
		$domain_parking = $row["domain_parking"];
		
		// Get DNS addresses from database. Switch to $conf_* values if "default" is found
		if($row["primary_dns"] == "default"){
			$thisdomain_dns1 = $conf_addr_primary_dns;
		}else{
			$thisdomain_dns1 = $row["primary_dns"];
		}
		$more_dns_server = "";
		if($row["other_dns"] == "default"){
			$all_dns = explode("|",$conf_addr_secondary_dns);
		}else{
			$all_dns = explode("|",$row["other_dns"]);
		}
		$thisdomain_dns2 = $all_dns[0];
		$nbr_other_dns = sizeof($all_dns);
		$all_ip = "";
		$temp_ip = gethostbyname($thisdomain_dns2);
		if(isIP($temp_ip)){
			$all_ip .= $temp_ip."; ";
		}
		for($z=1;$z<$nbr_other_dns;$z++){
			$more_dns_server .= "@	IN	NS	".$all_dns[$z].".\n";
			$temp_ip = gethostbyname($all_dns[$z]);
			if(isIP($temp_ip)){
				$all_ip .= $temp_ip."; ";
			}
		}

		if(strlen($conf_ip_allowed_dns_transfer) > 4){
			$more_allowed = explode("|",$conf_ip_allowed_dns_transfer);
			$v = sizeof($more_allowed);
			for($k=0; $k<$v; $k++){
				$all_ip .= $more_allowed[$k] . "; ";
			}
		}

		if(strlen($all_ip)>4){
			$allow_xfer = "allow-transfer { $all_ip };";
		}else{
			$allow_xfer = "";
		}

		if($row["primary_mx"] == "default"){
			$thisdomain_mx1 = $conf_addr_mail_server;
		}else{
			$thisdomain_mx1 = $row["primary_mx"];
		}

		$more_mx_server = "";
		$MX_number = 10;
		if($row["other_mx"] == "default"){
			if($conf_addr_backup_mail_server != ""){
				$all_mx = explode("|",$conf_addr_backup_mail_server);
				$nbr_other_mx = sizeof($all_mx);
				for($z=0;$z<$nbr_other_mx;$z++){
					$more_mx_server .= "@	IN	MX	".$MX_number."	".$all_mx[$z].".\n";
					$MX_number += 5;
				}
			}
		}else{
			$all_mx = explode("|",$row["other_mx"]);
			$nbr_other_mx = sizeof($all_mx);
			for($z=0;$z<$nbr_other_mx;$z++){
				$more_mx_server .= "@	IN	MX	".$MX_number."	".$all_mx[$z].".\n";
				$MX_number += 5;
			}
		}
		
		$root_txt_record = $row["txt_root_entry"];
		$root_txt_record2 = $row["txt_root_entry2"];


		$web_extention = substr($web_name,-strpos(strrev($web_name),'.'));

		$web_default_subdomain = $row["default_subdomain"];
		$wildcard_dns = $row["wildcard_dns"];

		// Get the owner informations
		$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$web_owner';";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 != 1){
			echo("No user of that name ($web_owner)!\n");
			continue;
		}
		$webadmin = mysql_fetch_array($result2) or die ("Cannot fetch user");
		$web_path = $webadmin["path"];

		// This should handle domain parking: need to get the target IP addr
		if($domain_parking != "no-parking"){
			$domain_to_get = $domain_parking;
			$qp = "SELECT ip_addr FROM $pro_mysql_domain_table WHERE name='$domain_parking'";
			$rp = mysql_query($qp)or die("Cannot query $qp line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$np = mysql_num_rows($rp);
			if($np != 1){
				echo "WARNING: error in your SQL table: target domain does not exists, will cancel domain parking!!!";
				$domain_to_get = $web_name;
				$domain_parking = "no-parking";
			}else{
				$ap = mysql_fetch_array($rp);
				$ip_addr = $ap["ip_addr"];
			}
		}else{
			$domain_to_get = $web_name;
		}

		// Grab all subdomains
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$domain_to_get' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 < 1){
			echo("WARNING: No subdomain for domain $domain_to_get !!!");
		}
		if($conf_use_multiple_ip == "yes"){
			$ip_to_write = $ip_addr;
		}else{
			$ip_to_write = $conf_main_site_ip;
		}
		if($row["primary_dns"] == "default"){
			$named_file .= "zone \"$web_name\" IN {
	type master;
	$allow_xfer
	allow-query { any; };
	file \"$conf_generated_file_path/$conf_named_zonefiles_path/$web_name\";
};
";
		}

		if($row["other_dns"] == "default" || $row["primary_dns"] == "default"){
			$slave_file .= "zone \"$web_name\" {
	type slave;
	allow-query { any; };
	masters { $conf_ip_slavezone_dns_server; };
	file \"$conf_generated_file_path/$conf_named_slavezonefiles_path/$web_name\";
};
";
		}

		if($row["primary_dns"] == "default"){

			// DNS serial
			$oldzonefile="$conf_generated_file_path/$conf_named_zonefiles_path/$web_name";
			if(file_exists($oldzonefile)) {
				$oldzonefile_contents = file_get_contents($oldzonefile);
				$matches=Array();
				if(preg_match("/${serial_prefix}([0-9]{2}); serial/", $oldzonefile_contents, $matches) >0) {
					$serial_incr = $matches[1] + 1;
					if($serial_incr > 99) {
						$serial_incr = 99;
						$console .= "<br />WARNING: DNS serial number for zone $web_name is already at the maximum for today (${serial_prefix}${serial_incr}), and therefore was not incremented.<br />\n";
					}
					$todays_serial = $serial_prefix . sprintf("%'02d", $serial_incr);
				}
				else {
					$todays_serial = $serial_prefix . "01";
				}
			}
			else {
				$todays_serial = $serial_prefix . "01";
			}

			$this_site_file = "\$TTL $domain_ttl
@               IN      SOA     $thisdomain_dns1. $bind_formated_webmaster_email_addr (
			$todays_serial; serial
                        $conf_named_soa_refresh ; refresh
                        $conf_named_soa_retry ; retry
                        $conf_named_soa_expire ; expire
                        $conf_named_soa_default_ttl ; default_ttl
                        )
@	IN	NS	$thisdomain_dns1.
@	IN	NS	$thisdomain_dns2.
$more_dns_server
@	IN	MX	5	$thisdomain_mx1.
$more_mx_server
@	IN	TXT	\"$root_txt_record\"
@	IN	TXT	\"$root_txt_record2\"
	IN	A	$ip_to_write
";
			// if we have the public.key for DomainKeys, write it into our zone file
			if (file_exists($conf_domainkey_publickey_filepath) && $row["primary_mx"] == "default"){
				$key_file_array = file($conf_domainkey_publickey_filepath, FILE_IGNORE_NEW_LINES);
				// skip the first and last lines (the ---PUBLIC---)
				$KEY = "";
				for ($key_file_array_count = 1; $key_file_array_count < count($key_file_array) - 1; $key_file_array_count++){
					$KEY .= $key_file_array[$key_file_array_count];
				}
				// This line is added for php4 support:
				$KEY = str_replace("\n","",$KEY);
				$SELECTOR="postfix";
				$DOMAIN=$web_name;
				$NSRECORD="$SELECTOR._domainkey IN TXT \"k=rsa;p=$KEY; t=y\"";
				$NSRECORDDEFAULT="_domainkey IN TXT \"o=~\"";
				$this_site_file .= "$NSRECORDDEFAULT\n";
				$this_site_file .= "$NSRECORD\n";
			}
			//
			// Add all subdomains to it !
			//

			// First, generate a list of "auto generated subdomains", preseed with "no"
			$autosubs = array();
			$list_autogen = explode("|",$conf_autogen_subdomain_list);
			$n_autogen = sizeof($list_autogen);
			for($autog=0;$autog<$n_autogen;$autog++){
				$autosubs[$list_autogen[$autog]] = "no";
			}
			for($j=0;$j<$num_rows2;$j++){
				$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
				$web_subname = $subdomain["subdomain_name"];
				// TTL support
				$sub_ttl = $conf_default_zones_ttl;
				if (isset($subdomain["ttl"])){
					$sub_ttl = $subdomain["ttl"];
				}
				// Check if it's an IP or not, to know if it's a CNAME record or a A record
				if(isIP($subdomain["ip"]) || $subdomain["ip"] == "default"){
					if($subdomain["ssl_ip"] != "none"){
						$the_ip_writed = "A\t".$subdomain["ssl_ip"];
					}else{
						if($subdomain["ip"] == "default"){
							$the_ip_writed = "A\t".$ip_to_write;
						}else{
							$the_ip_writed = "A\t".$subdomain["ip"];
						}
					}
				}else{
					$the_ip_writed = "CNAME\t".$subdomain["ip"].".";
				}
// Note from Thomas Goirand:
// This patch seems to 1/ produce some NOTICE like this:
// Notice: Undefined variable: seeb_alias in /usr/share/dtc/admin/genfiles/gen_named_files.php on line 442
// Notice: Undefined variable: seeb_alias in /usr/share/dtc/admin/genfiles/gen_named_files.php on line 441
// as $seeb_alias is never defined anywhere!!!
// and 2/ make the stuff unreachable. So I'm commenting out, sorry. Seeb: if you see this, patch this file
// so it always work, thanks!
//
// patch by seeb w3_alias
//if ($subdomain['w3_alias'] =="yes" && $subdomain['subdomain_name']!="www"){
//             $sub_alias="www.".$subdomain['subdomain_name'];
//  $console.="Generated w3alias: ".$seeb_alias.".".$subdomain['domain_name']."<br/>";
//     $this_site_file .= "$seeb_alias\tIN\tCNAME      ".$subdomain['subdomain_name'].".".$subdomain['domain_name'].".\n";
//}				
// end of patch 3w_alias				
				// See if the subdomain overrides the default for the zone
				if( isset($autosubs[ $web_subname ])){
					$autosubs[ $web_subname ] = "yes";
				}

				// if we have a srv_record here (ie a port, then we don't write the normal subdomain entry, just the SRV record
			 	if (isset($subdomain["srv_record"]) && $subdomain["srv_record"] != ""){
					$this_site_file .= "$web_subname	$sub_ttl	SRV	0	10	".$subdomain["srv_record"]."	".$subdomain["ip"]."\n";
				} else {	
					// write TTL values into subdomain
					if ($conf_use_cname_for_subdomains == "yes"){
						$this_site_file .= "$web_subname	$sub_ttl	IN	CNAME	@\n";
					}else{
						if($web_subname == $web_default_subdomain && $wildcard_dns == "yes"){
							$wildcard_dns_txt = "*        $sub_ttl        IN      $the_ip_writed\n";
						}
						$this_site_file .= "$web_subname	$sub_ttl	IN	$the_ip_writed\n";
					}
				}
				if($subdomain["associated_txt_record"] != "" && (isIP($subdomain["ip"]) || $subdomain["ip"] == "default")){
					$this_site_file .= "$web_subname	IN	TXT	\"".$subdomain["associated_txt_record"]."\"\n";
				}
				if(isset($subdomain["nameserver_for"]) && $subdomain["nameserver_for"] != ""){
					// add support for creating NS records
					$nameserver_for = $subdomain["nameserver_for"];
					$this_site_file .= "$nameserver_for	IN	NS	$web_subname.$web_name.\n";
				}
			}
			if( $conf_autogen_default_subdomains == "yes" ){
				// For each subdomains not yet defined, but in autogen, add an entry
				$autosubs_keys = array_keys($autosubs);
				$n_autogen = sizeof($autosubs);
				for($autog=0;$autog<$n_autogen;$autog++){
					if($autosubs[ $autosubs_keys[$autog]] == "no"){
						$zeautogen = $autosubs_keys[$autog];
						if($conf_use_cname_for_subdomains == "yes"){
							$this_site_file .= "$zeautogen	IN	CNAME	@\n";
						}else{
							$this_site_file .= "$zeautogen	IN	A	$ip_to_write\n";
						}
					}
				}
			}
			if(isset($wildcard_dns_txt)){
				$this_site_file .= $wildcard_dns_txt;
			}
			if($web_serial_flag=="yes"){
				$console .= "Updating zone file for domain $web_name using serial : $todays_serial, ipaddr : $ip_to_write<br>";
				$filep = fopen("$conf_generated_file_path/$conf_named_zonefiles_path/$web_name", "w+");
				if( $filep == NULL){
					print("Cannot open file $conf_generated_file_path/$conf_named_zonefiles_path/$web_name for writting");
					continue;
				}
				fwrite($filep,$this_site_file);
				fclose($filep);
				chown("$conf_generated_file_path/$conf_named_zonefiles_path/$web_name",$conf_dtc_system_username);
				chgrp("$conf_generated_file_path/$conf_named_zonefiles_path/$web_name",$conf_dtc_system_groupname);
				$query_serial = "UPDATE $pro_mysql_domain_table SET generate_flag='no' WHERE name='$web_name' LIMIT 1;";
				$result_serial = mysql_query ($query_serial)or die("Cannot execute query \"$query_serial\"");
			}
		}else{
			$temp_ip = gethostbyname($thisdomain_dns1);
			if(isIP($temp_ip)){
				$named_file .= "zone \"$web_name\" {
	type slave;
	allow-query { any; };
	masters { $temp_ip; };
	file \"$conf_generated_file_path/$conf_named_slavezonefiles_path/$web_name\";
};
";
			}
		}
	}

	$named_file .= get_remote_ns_domains();

	// include the slave zone file
//	$named_file .= "include \"$conf_generated_file_path/$conf_named_slavefile_path\";\n";

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

	if ( ! file_exists($conf_generated_file_path . "/" . $conf_named_slavezonefiles_path)){

		mkdir($conf_generated_file_path . "/" . $conf_named_slavezonefiles_path, 0775);
	}

	// make sure the slave directory is present
	fwrite($filep,$slave_file);
	fclose($filep);

	// Call the reverse DNS function now...
	rnds_generate();

	return true;
}

?>
