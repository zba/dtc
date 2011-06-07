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

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

function get_remote_ns($a){
	global $console;
	global $keep_dns_generate_flag;
	global $panel_type;
	$retry = 0;
	$flag = FALSE;
	$named_file = ""; //init $named_file var
	$url = $a["server_addr"].'/dtc/list_domains.php?action=list_dns&login='.$a["server_login"].'&pass='.$a["server_pass"];
	while($retry < 3 && $flag == FALSE){
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
				echo "\nUsing php dtc_HTTPRequest class on ".$a["server_addr"]." with login ".$a["server_login"].".";
			}else{
				$console .= "<br>Using php dtc_HTTPRequest class on ".$a["server_addr"]." with login ".$a["server_login"].".";
			}
			$httprequest = new dtc_HTTPRequest("$url");
			$lines = $httprequest->DownloadToStringArray();
			if($lines === FALSE){
				$lines = array();
			}
		}
		if($lines != FALSE){
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
		}
		$retry ++;
		if($flag == FALSE){
			if( $panel_type == "cronjob"){
				$console .= "Failed: delaying 3s!\n";
			}else{
				$console .= "Failed: delaying 3s!<br>";
			}
			sleep(3);
		}
	}
	if($flag == FALSE){
		$keep_dns_generate_flag = "yes";
		return FALSE;
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

function calculate_reverse_mask_and_cidr($ip_pool_ip,$ip_pool_netmask){
	$ip_pool_ip_exploded = explode(".",$ip_pool_ip);

	$netmask_exploded = explode(".",$ip_pool_netmask);
	switch($ip_pool_netmask){
	case "255.255.0.0":
		$cird_mask = "/16";
		break;
	case "255.255.128.0":
		$cird_mask = "/17";
		break;
	case "255.255.192.0":
		$cird_mask = "/18";
		break;
	case "255.255.224.0":
		$cird_mask = "/19";
		break;
	case "255.255.240.0":
		$cird_mask = "/20";
		break;
	case "255.255.248.0":
		$cird_mask = "/22";
		break;
	case "255.255.254.0":
		$cird_mask = "/23";
		break;
	case "255.255.255.0":
		$cird_mask = "/24";
		break;
	case "255.255.255.128":
		$cird_mask = "/25";
		break;
	case "255.255.255.192":
		$cird_mask = "/26";
		break;
	case "255.255.255.224":
		$cird_mask = "/27";
		break;
	case "255.255.255.240":
		$cird_mask = "/28";
		break;
	case "255.255.255.248":
		$cird_mask = "/29";
		break;
	case "255.255.255.252":
		$cird_mask = "/30";
		break;
	case "255.255.255.254":
		$cird_mask = "/31";
		break;
	default:
		die("$netmask is not a netmask line ".__LINE__." file ".__FILE__);
	}
	$out = "." . $ip_pool_ip_exploded[3] . $cird_mask . "." . $ip_pool_ip_exploded[2] . "." . $ip_pool_ip_exploded[1] . "." . $ip_pool_ip_exploded[0] . ".in-addr.arpa";;
	return $out;
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

	global $conf_ip_slavezone_dns_server;

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
	$q = "SELECT DISTINCT $pro_mysql_ip_pool_table.id,$pro_mysql_ip_pool_table.ip_addr,$pro_mysql_ip_pool_table.netmask,$pro_mysql_ip_pool_table.zone_type,$pro_mysql_ip_pool_table.custom_part,$pro_mysql_ip_pool_table.location
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
	$q = "SELECT DISTINCT $pro_mysql_ip_pool_table.id,$pro_mysql_ip_pool_table.ip_addr,$pro_mysql_ip_pool_table.netmask,$pro_mysql_ip_pool_table.zone_type,$pro_mysql_ip_pool_table.custom_part,$pro_mysql_ip_pool_table.location
	FROM $pro_mysql_ip_pool_table,$pro_mysql_dedicated_ips_table
	WHERE $pro_mysql_dedicated_ips_table.rdns_regen='yes'
	AND $pro_mysql_ip_pool_table.id=$pro_mysql_dedicated_ips_table.ip_pool_id;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$num_ded = mysql_num_rows($r);
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
		case "ip_per_ip_cidr":
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
				if($zone_type == "ip_per_ip_cidr"){
					$zone_name_end = calculate_reverse_mask_and_cidr($pool_ip_addr,$pool_netmask);
					$ip_exploded = explode(".",$the_ip_addr);
					$zone_name = $ip_exploded[3] . $zone_name_end;
					$the_at = "$zone_name\t";
					$the_orig = "\$ORIGIN .\n";
				}else{
					$zone_name = calculate_reverse_end($the_ip_addr,"255.255.255.255");
					$the_at = "@\t";
					$the_orig = "";
				}
/*				$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$the_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";*/

				$zonefile_content = $the_orig."\$TTL $conf_default_zones_ttl
$the_at	$conf_default_zones_ttl	IN	SOA	".$conf_addr_primary_dns.". ".$bind_formated_webmaster_email_addr." (
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
/*			$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";*/
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
		case "one_zonefile_with_minus":
			$zone_name = calculate_reverse_mask_and_cidr($pool_ip_addr,$pool_netmask);
/*			$reverse_dns_file .= "zone \"$zone_name\" conmenos in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";*/
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
		case "one_zonefile_with_name":
			$zone_name = $a["location"].".".calculate_reverse_end($pool_ip_addr,$pool_netmask);
/*			$reverse_dns_file .= "zone \"$zone_name\" con nombre in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";*/
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
		case "one_zonefile_with_slash":
			$zone_name = calculate_reverse_mask_and_cidr($pool_ip_addr,$pool_netmask);
/*			$reverse_dns_file .= "zone \"$zone_name\" con slahs in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";*/
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


	$q = "SELECT id,ip_addr,netmask,zone_type,custom_part,location FROM $pro_mysql_ip_pool_table;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$ip_pool_id = $a["id"];
		$pool_ip_addr = $a["ip_addr"];
		$pool_netmask = $a["netmask"];
		$zone_type = $a["zone_type"];
		$custom_part = $a["custom_part"];

		switch($zone_type){
		case "ip_per_ip":
		case "ip_per_ip_cidr":
			unset($thiszoneIPs);
			unset($thiszoneVPSIPs);
			unset($thiszoneDEDIPs);
			$thiszoneIPs = array();
			$q2 = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_pool_id='$ip_pool_id';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_vps = mysql_num_rows($r2);
			for($j=0;$j<$num_vps;$j++){
				$a2 = mysql_fetch_array($r2);
				$thiszoneIPs[] = $a2;
			}
			$q2 = "SELECT * FROM $pro_mysql_dedicated_ips_table WHERE ip_pool_id='$ip_pool_id';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$num_ded = mysql_num_rows($r2);
			for($j=0;$j<$num_ded;$j++){
				$a2 = mysql_fetch_array($r2);
				$thiszoneIPs[] = $a2;
			}
			$num_of_IPs = sizeof($thiszoneIPs);
			for($j=0;$j<$num_of_IPs;$j++){
				$the_ip_addr = $thiszoneIPs[$j]["ip_addr"];
				if($zone_type == "ip_per_ip_cidr"){
					$zone_name_end = calculate_reverse_mask_and_cidr($pool_ip_addr,$pool_netmask);
					$ip_exploded = explode(".",$the_ip_addr);
					$zone_name = $ip_exploded[3] . $zone_name_end;
				}else{
					$zone_name = calculate_reverse_end($the_ip_addr,"255.255.255.255");
				}
				$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$the_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";
				$slave_dns_file .= "zone \"$zone_name\" in {
	type slave;
	file \"$conf_generated_file_path/slave_reverse_zones/$the_ip_addr\";
	masters { $conf_ip_slavezone_dns_server; };
	allow-query { any; };
};

";
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
			$slave_dns_file .= "zone \"$zone_name\" in {
	type slave;
	file \"$conf_generated_file_path/slave_reverse_zones/$pool_ip_addr\";
	masters { $conf_ip_slavezone_dns_server; };
	allow-query { any; };
};

";
			break;
		case "one_zonefile_with_minus":
			$zone_name = calculate_reverse_mask_and_cidr($pool_ip_addr,$pool_netmask);
			$zone_name = preg_replace('/\//','-',$zone_name);
			$zone_name = preg_replace('/^\./','',$zone_name);
			$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";
			$slave_dns_file .= "zone \"$zone_name\" in {
	type slave;
	file \"$conf_generated_file_path/slave_reverse_zones/$pool_ip_addr\";
	masters { $conf_ip_slavezone_dns_server; };
	allow-query { any; };
};

";
			break;
		case "one_zonefile_with_name":
			$zone_name = $a["location"].".".calculate_reverse_end($pool_ip_addr,$pool_netmask);
			$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";
			$slave_dns_file .= "zone \"$zone_name\" in {
	type slave;
	file \"$conf_generated_file_path/slave_reverse_zones/$pool_ip_addr\";
	masters { $conf_ip_slavezone_dns_server; };
	allow-query { any; };
};

";
			break;
		case "one_zonefile_with_slash":
			$zone_name = calculate_reverse_mask_and_cidr($pool_ip_addr,$pool_netmask);
			$zone_name = preg_replace('/^\./','',$zone_name);
			$reverse_dns_file .= "zone \"$zone_name\" in {
	type master;
	file \"$conf_generated_file_path/reverse_zones/$pool_ip_addr\";
$allow_trans_str	allow-query { any; };
};

";
			$slave_dns_file .= "zone \"$zone_name\" in {
	type slave;
	file \"$conf_generated_file_path/slave_reverse_zones/$pool_ip_addr\";
	masters { $conf_ip_slavezone_dns_server; };
	allow-query { any; };
};

";
			break;
		default:
			break;
		}
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
	fwrite($filep,$slave_dns_file);
	fclose($filep);
}

// Generates all the zonefiles for the VPSes on each nodes, so there's already a correct IP and hostname
// for the VPS that is configured by default, without doing anything.
function nodes_vps_generate(){
	global $pro_mysql_vps_server_table;
	global $pro_mysql_vps_table;
	global $conf_default_zones_ttl;
	global $conf_named_soa_refresh;
	global $conf_named_soa_retry;
	global $conf_named_soa_expire;
	global $conf_named_soa_default_ttl;
	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;
	global $pro_mysql_vps_table;
	global $conf_generated_file_path;
	global $conf_named_slavefile_path;
	global $pro_mysql_vps_ip_table;
	global $conf_dtc_system_username;
	global $conf_ip_allowed_dns_transfer;

	global $conf_ip_slavezone_dns_server;

	global $conf_webmaster_email_addr;

	$bind_formated_webmaster_email_addr = str_replace('@',".",$conf_webmaster_email_addr).".";

	$nodes_named_conf = "// WARNING: Automatic regeneration of this file, do not edit!\n";
	$nodes_named_conf_slave = "//WARNING: Automatic regeneration of this file, do not edit!\n";

	if(strlen($conf_ip_allowed_dns_transfer) > 4){
		$all_ip = "";
		$more_allowed = explode("|",$conf_ip_allowed_dns_transfer);
		$v = sizeof($more_allowed);
		for($k=0; $k<$v; $k++){
			$all_ip .= $more_allowed[$k] . "; ";
		}
	}

	$todays_serial = date("YmdH");
	$q = "SELECT hostname,dom0_ips FROM $pro_mysql_vps_server_table;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		@mkdir("$conf_generated_file_path/nodes_zones");
		@chown("$conf_generated_file_path/nodes_zones",$conf_dtc_system_username);
	}
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$srv_hostname = $a["hostname"];

		$nodes_named_conf_slave .= "zone \"$srv_hostname\" IN {
	type slave;
	file \"$conf_generated_file_path/nodes_zones/$srv_hostname\";
	masters { $conf_ip_slavezone_dns_server; };
	allow-query { any; };
};

";

		$nodes_named_conf .= "
zone \"$srv_hostname\" IN {
        type master;
        allow-transfer { $all_ip  };
        allow-query { any; };
        file \"$conf_generated_file_path/nodes_zones/$srv_hostname\";
};
";

		$node_zfile = "\$TTL $conf_default_zones_ttl
@	IN	SOA	$conf_addr_primary_dns. $bind_formated_webmaster_email_addr (
			$todays_serial; serial
			$conf_named_soa_refresh ; refresh
			$conf_named_soa_retry ; retry
			$conf_named_soa_expire ; expire
			$conf_named_soa_default_ttl ; default_ttl
			)
@	IN	NS	$conf_addr_primary_dns.
@	IN	NS	$conf_addr_secondary_dns."."\n";
		// Set the first IP of the dom0 IP list as the node name
		$ips = explode("|",$a["dom0_ips"]);
		$ip_dom0 = $ips[0];
		$node_zfile .= "	IN	A	".$ip_dom0."\n";

		$q2 = "SELECT vps_xen_name FROM $pro_mysql_vps_table WHERE vps_server_hostname='".$srv_hostname."';";
//		echo $q2;
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		for($j=0;$j<$n2;$j++){
			$a2 = mysql_fetch_array($r2);
			$vps_xen_name = $a2["vps_xen_name"];
			$q3 = "SELECT ip_addr FROM $pro_mysql_vps_ip_table WHERE vps_server_hostname='".$srv_hostname."' AND vps_xen_name='".$vps_xen_name."' LIMIT 1;";
			$r3 = mysql_query($q3)or die("Cannot query $q3 line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
			if(mysql_num_rows($r3) == 1){
				$a3 = mysql_fetch_array($r3);
				$ip_vps = $a3["ip_addr"];
				$node_zfile .= "xen".$vps_xen_name."        IN      A      ".$ip_vps."\n";
				$node_zfile .= "dtc.xen".$vps_xen_name."        IN      A      ".$ip_vps."\n";
				$node_zfile .= "mx.xen".$vps_xen_name."        IN      A      ".$ip_vps."\n";
			}
		}
		$filep = fopen("$conf_generated_file_path/nodes_zones/$srv_hostname","w+");
		fwrite($filep,$node_zfile);
		fclose($filep);
		@chown("$conf_generated_file_path/nodes_zones/$srv_hostname",$conf_dtc_system_username);
	}
	if($n > 0){
		$filep = fopen("$conf_generated_file_path/nodes_zones.conf","w+");
		fwrite($filep,$nodes_named_conf);
		fclose($filep);
		@chown("$conf_generated_file_path/nodes_zones.conf",$conf_dtc_system_username);

		$filep = fopen("$conf_generated_file_path/nodes_zones_slave.conf","w+");
		fwrite($filep,$nodes_named_conf_slave);
		fclose($filep);
		@chown("$conf_generated_file_path/nodes_zones.conf",$conf_dtc_system_username);
	}
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
				echo "WARNING: error in your SQL table: target domain $domain_parking for parking of $web_name does not exists, will cancel domain parking!!!<br>";
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
@	IN	TXT	\"$root_txt_record2\"\n";

			// Set the "root subdomain" IP as the same as the "default subdomain" IP
			$qd = "SELECT ip FROM $pro_mysql_subdomain_table WHERE subdomain_name='$web_default_subdomain' AND domain_name='$domain_to_get';";
			$rd = mysql_query($qd)or die("Cannot query $qd line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$nd = mysql_num_rows($rd);
			if($nd == 1){
				$ad = mysql_fetch_array($rd);
				if( $ad["ip"] == "default" ){
					$this_site_file .= "	IN	A	$ip_to_write\n";
				}else{
					// In case of a CNAME, add a final dot
					if( isIP($ad["ip"]) ){
						$this_site_file .= "	IN	A	".$ad["ip"]."\n";
					}else{
						$this_site_file .= "	IN	CNAME	".$ad["ip"].".\n";
					}
				}
			}else{
				$this_site_file .= "	IN	A	$ip_to_write\n";
			}
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
				$NSRECORD="$SELECTOR._domainkey IN TXT \"k=rsa;p=$KEY\"";
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
					$this_site_file .= "_$web_subname._".$subdomain["srv_record_protocol"]."	$sub_ttl	IN	SRV	0	10	".$subdomain["srv_record"]."	".$subdomain["ip"]."\n";
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
				if ($subdomain["ipv4_round_robin"] != ""){
					$rr_expl = explode("|",$subdomain["ipv4_round_robin"]);
					$nbr_rr = sizeof($rr_expl);
					for($rr_i=0;$rr_i<$nbr_rr;$rr_i++){
						$this_site_file .= "$web_subname\t$sub_ttl\tIN\tA\t".$rr_expl[$rr_i]."\n";
					}
				}
				if ($subdomain["ip6"] != "" && $subdomain["ip6"] != "default") {
                                        $this_site_file .= "$web_subname        $sub_ttl        IN      AAAA    ".$subdomain["ip6"]."\n";
                                }
				if($subdomain["associated_txt_record"] != "" && (isIP($subdomain["ip"]) || $subdomain["ip"] == "default")){
					$this_site_file .= "$web_subname	IN	TXT	\"".$subdomain["associated_txt_record"]."\"\n";
				}
				if(isset($subdomain["nameserver_for"]) && $subdomain["nameserver_for"] != ""){
					// add support for creating NS records
					$nameserver_for = $subdomain["nameserver_for"];
					$this_site_file .= "$web_subname	IN	NS	$nameserver_for.\n";
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
							if($zeautogen == "mysql1"){
								$this_site_file .= "$zeautogen	IN	A	127.0.0.1\n";
							}else{
								$this_site_file .= "$zeautogen	IN	A	$ip_to_write\n";
							}
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

	// include the reverse zone file
	$named_file .= "include \"$conf_generated_file_path/named.conf.reverse\";\n";

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

	nodes_vps_generate();

	return true;
}

?>
