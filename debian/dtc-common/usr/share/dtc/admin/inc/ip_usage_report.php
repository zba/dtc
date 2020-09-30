<?php

require("Net/IPv4.php");

function calculate_pool_size($netmask){
	switch($netmask){
	case "128.0.0.0":	return 65536*32768;
	case "192.0.0.0":	return 65536*16384;
	case "224.0.0.0":	return 65536*8192;
	case "240.0.0.0":	return 65536*4096;
	case "248.0.0.0":	return 65536*2048;
	case "252.0.0.0":	return 65536*1024;
	case "254.0.0.0":	return 65536*512;
	case "255.0.0.0":	return 65536*256;
	case "255.128.0.0":	return 65536*128;
	case "255.192.0.0":	return 65536*64;
	case "255.224.0.0":	return 65536*32;
	case "255.240.0.0":	return 65536*16;
	case "255.248.0.0":	return 65536*8;
	case "255.252.0.0":	return 65536*3;
	case "255.254.0.0":	return 65536*2;
	case "255.255.0.0":	return 65536;
	case "255.255.128.0":	return 32768;
	case "255.255.192.0":	return 16384;
	case "255.255.224.0":	return 8192;
	case "255.255.240.0":	return 4096;
	case "255.255.248.0":	return 2048;
	case "255.255.252.0":	return 1024;
	case "255.255.254.0":	return 512;
	case "255.255.255.0":	return 256;
	case "255.255.255.128":	return 128;
	case "255.255.255.192":	return 64;
	case "255.255.255.224":	return 32;
	case "255.255.255.240":	return 16;
	case "255.255.255.248":	return 8;
	case "255.255.255.252":	return 4;
	case "255.255.255.254":	return 2;
	case "255.255.255.255":	return 1;
	}
}

function findPoolID($ip){
	global $pro_mysql_ip_pool_table;

	$ip_calc = new Net_IPv4();
	$ip_calc2 = new Net_IPv4();
	$q = "SELECT * FROM $pro_mysql_ip_pool_table WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$ip_calc->ip = $a["ip_addr"];
		$ip_calc->netmask = $a["netmask"];
		$ret = $ip_calc->calculate();
		if (!is_object($ret)){
			$ip_calc2->ip = $ip;
			$ip_calc2->netmask = $a["netmask"];
			$ip_calc2->calculate();
			$ret2 = $ip_calc2->calculate();
			if (!is_object($ret2)){
				if($ip_calc->network == $ip_calc2->network){
					return $a["id"];
				}
			}
		}else{
			echo "Error for IP pool ".$a["id"]." please check IP and netmask.";
		}
	}
	return 0;
}

// Returns:
// array(
//	 ip_pool_id => array(
//		"netmask" => "255.255.255.0",
//		"location" => "M1 Singapore",
//		"zone_type"
//		"gateway"
//		"broadcast"
//		"dns"
//		"nbr_vps"
//		"nbr_provision"
//		"nbr_total_used"
//		"ip_remaining"
//		"pool_size"
//		"all_ips" == array (
//			"1.2.3.4" => array(
//				"type" => "vps" | "dedicated" | "dom0" | "ssl",
//				"available" => "yes" | "no"
//				"vps_xen_name" =>
//				"vps_server_hostname" =>
//				)
//			)
//	)	);

function fullIPUsage() {
	global $pro_mysql_ip_pool_table;
	global $pro_mysql_vps_server_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_dedicated_ips_table;
	global $pro_mysql_ssl_ips_table;

	$q = "SELECT * FROM $pro_mysql_ip_pool_table WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$pools = array();
	$pools["unpooled"] = array();
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$pools[ $a["id"] ] = $a;
		$pools[ $a["id"] ] ["all_ips"] = array();
		$pools[ $a["id"] ] ["nbr_vps"] = 0;
		$pools[ $a["id"] ] ["nbr_dom0"] = 0;
		$pools[ $a["id"] ] ["nbr_ssl"] = 0;
		$pools[ $a["id"] ] ["nbr_dedicated"] = 0;
		$pools[ $a["id"] ] ["nbr_provision"] = 0;
		$pools[ $a["id"] ] ["nbr_total_used"] = 0;
		$pool_size = calculate_pool_size($a["netmask"]);
		$pools[ $a["id"] ] ["pool_size"] = $pool_size;
		$pools[ $a["id"] ] ["ip_remaining"] = $pool_size;
	}


	$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$ips = $a["dom0_ips"];
		$ipsa = explode("|",$ips);
		$nb = sizeof($ipsa);
		for($j=0;$j<$nb;$j++){
			$ip = $ipsa[$j];
			$poolid = findPoolId($ip);
			if($poolid){
				$pools[ $poolid ] ["all_ips"] [$ip]["type"] = "dom0";
				$pools[ $poolid ] ["all_ips"] [$ip]["available"] = "no";
				$pools[ $poolid ] ["all_ips"] [$ip]["hostname"] = $a["hostname"];
				$pools[ $poolid ] ["nbr_dom0"] += 1;
				$pools[ $poolid ] ["ip_remaining"] -= 1;
				$pools[ $poolid ] ["nbr_total_used"] += 1;
			}else{
				$pools["unpooled"][$ip]["type"] = "dom0";
				$pools["unpooled"][$ip]["available"] = "no";
				$pools["unpooled"][$ip]["hostname"] = $a["hostname"];
			}
		}

		$q2 = "SELECT * FROM $pro_mysql_vps_ip_table WHERE vps_server_hostname='".$a["hostname"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		for($j=0;$j<$n2;$j++){
			$a2 = mysql_fetch_array($r2);
			$ip = $a2["ip_addr"];
			if($a2["ip_pool_id"] != 0){
				$pools[ $a2["ip_pool_id"] ] ["all_ips"] [$ip]["type"] = "vps";
				$pools[ $a2["ip_pool_id"] ] ["all_ips"] [$ip]["available"] = $a2["available"];
				$pools[ $a2["ip_pool_id"] ] ["all_ips"] [$ip]["vps_xen_name"] = $a2["vps_xen_name"];
				$pools[ $a2["ip_pool_id"] ] ["all_ips"] [$ip]["vps_server_hostname"] = $a2["vps_server_hostname"];
				$pools[ $a2["ip_pool_id"] ] ["nbr_vps"] += 1;
				$pools[ $a2["ip_pool_id"] ] ["ip_remaining"] -= 1;
				if($a2["available"] == "yes"){
					$pools[ $a2["ip_pool_id"] ] ["nbr_provision"] += 1;
				}
				$pools[ $a2["ip_pool_id"] ] ["nbr_total_used"] += 1;
			}
		}
	}

	$q2 = "SELECT * FROM $pro_mysql_dedicated_ips_table WHERE 1;";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	for($j=0;$j<$n2;$j++){
		$a2 = mysql_fetch_array($r2);
		$ip = $a2["ip_addr"];
		if($a2["ip_pool_id"] != 0){
			$pools[ $a2["ip_pool_id"] ] ["all_ips"] [$ip]["type"] = "dedicated";
			$pools[ $a2["ip_pool_id"] ] ["all_ips"] [$ip]["available"] = "no";
			$pools[ $a2["ip_pool_id"] ] ["all_ips"] [$ip]["dedicated_server_hostname"] = $a2["dedicated_server_hostname"];
			$pools[ $a2["ip_pool_id"] ] ["nbr_dedicated"] += 1;
			$pools[ $a2["ip_pool_id"] ] ["ip_remaining"] -= 1;
			$pools[ $a2["ip_pool_id"] ] ["nbr_total_used"] += 1;
		}
	}

	$q2 = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE 1;";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	for($j=0;$j<$n2;$j++){
		$a2 = mysql_fetch_array($r2);
		$ip = $a2["ip_addr"];
		$poolid = findPoolId($a2["ip_addr"]);
		if($poolid){
			$pools[ $poolid ] ["all_ips"] [$ip]["type"] = "ssl";
			$pools[ $poolid ] ["all_ips"] [$ip]["available"] = $a2["available"];
			$pools[ $poolid ] ["nbr_ssl"] += 1;
			$pools[ $poolid ] ["ip_remaining"] -= 1;
			if($a2["available"] == "yes"){
				$pools[ $poolid ] ["nbr_provision"] += 1;
			}
			$pools[ $poolid ] ["nbr_total_used"] += 1;
		}else{
			$pools["unpooled"][$ip]["type"] = "ssl";
		}
	}
	return $pools;
}

function ip_compare_func($a,$b){
	$ip_calc = new Net_IPv4();
	if ($a == $b)	return 0;
	$a_double = $ip_calc->ip2double($a);
	$b_double = $ip_calc->ip2double($b);
	return ($a_double < $b_double) ? -1 : 1;
}

function drawIPUsageReport(){
	$pools = fullIPUsage();

	$out = "";

	$nbr_pools = sizeof($pools);
	$pools_ids = array_keys($pools);
	for($j=0;$j<$nbr_pools;$j++){
		if($pools_ids[$j] == "unpooled"){
			$nbr_unpooled = sizeof($pools["unpooled"]);
			if($nbr_unpooled >= 0){
				$out = "<h2>Unpooled IPs</h2>";
				$out .= "<table border=\"1\"><tr><th>IP address</th><th>Type</th><th>Name</th></tr>";
				$keys = array_keys($pools["unpooled"]);
				for($i=0;$i<$nbr_unpooled;$i++){
					$out .= "<tr><td>".$keys[$i]."</td><td>".$pools["unpooled"][ $keys[$i]  ]["type"]."</td><td>".$pools["unpooled"][ $keys[$i]  ]["hostname"]."</td></tr>";
				}
				$out .= "</table>";
			}
		}else{
			$out .= "<h2>".$pools[$j]["location"] .": ".$pools[$j]["ip_addr"] . " / " . $pools[$j]["netmask"] . "</h2>";
			$all_ips = $pools[$j]["all_ips"];
			$nbr_ip = sizeof($all_ips);
			$keys = array_keys($all_ips);
			usort  ( &$keys  , "ip_compare_func" );
			$out .= "<table border=\"1\"><tr><th>IP address</th><th>Type</th><th>Name</th></tr>";
			for($i=0;$i<$nbr_ip;$i++){
				$ip = $keys[$i];
				$out .= "<tr><td>". $ip ."</td>";
				switch($all_ips[ $ip ]["type"]){
				case "vps":
					$out .= "<td>"._("vps")."</td><td>".$all_ips[ $ip ]["vps_xen_name"].":".$all_ips[ $ip ]["vps_server_hostname"]."</td>";
					break;
				case "ssl":
					$out .= "<td>"._("ssl")."</td><td>-</td>";
					break;
				case "dedicated":
					$out .= "<td>"._("dedicated")."</td><td>".$all_ips[ $ip ]["dedicated_server_hostname"]."</td>";
					break;
				case "dom0":
					$out .= "<td>"._("dom0")."</td><td>".$all_ips[ $ip ]["hostname"]."</td>";
					break;
				default:
					$out .= "<td>".$all_ips[ $ip ]["type"]."</td><td>-</td>";
					break;
				}
			}
			$out .= "</table>";
		}
	}

	return $out;
}

function drawPool($id){
	$pools = fullIPUsage();
	$mypool = $pools[$id];
	$out = "";
	$out .= "<h2>".$mypool["location"] .": ".$mypool["ip_addr"]."/".$mypool["netmask"]."</h2>";
	$out .= _("IP pool size:")." ".$mypool["pool_size"]."<br>";
	$out .= _("Number of SSL IP(s):")." ".$mypool["nbr_ssl"]."<br>";
	$out .= _("Number of dom0 IP(s):")." ".$mypool["nbr_dom0"]."<br>";
	$out .= _("Number of VPS IP(s):")." ".$mypool["nbr_vps"]."<br>";
	$out .= _("Number of dedicated IP(s):")." ".$mypool["nbr_dedicated"]."<br>";
	$out .= _("Total number of used IPs:")." ".$mypool["nbr_total_used"]."<br>";
	$out .= _("Number of IPs remaining in the pool:")." ".$mypool["ip_remaining"]."<br>";

	$ip_calc = new Net_IPv4();
	$ip_long = $ip_calc->ip2double($mypool["ip_addr"]);
	$out .= "<table class=\"dtcDatagrid_table_props\" border=\"1\"><tr><td class=\"dtcDatagrid_table_titles\">" . _("IP address") . "</td><td class=\"dtcDatagrid_table_titles\">". _("Type") . "</td><td class=\"dtcDatagrid_table_titles\">" . _("Name") . "</td><td class=\"dtcDatagrid_table_titles\">" . _("Provision") . "</td></tr>";
	for($i=0;$i<$mypool["pool_size"];$i++){
		$ip = long2ip($ip_long);
		if($i % 2){
			$class = "dtcDatagrid_table_flds_alt";
		}else{
			$class = "dtcDatagrid_table_flds";
		}
		$out .= "<tr><td class=\"$class\" style=\"text-align:right;\">". $ip ."</td>";
		if( isset( $mypool["all_ips"][ $ip ] )){
			$all_ips = $mypool["all_ips"];
			$out .= "<td class=\"$class\" style=\"text-align:center;\">". $mypool["all_ips"][ $ip ]["type"] ."</td>";

			if(isset($all_ips[ $ip ]["available"])){
				if($all_ips[ $ip ]["available"] == "yes"){
					$av = "<b>"._("FREE")."</b>";
				}else{
					$av = _("no");
				}
			}else{
				$av = "-";
			}

			switch($all_ips[ $ip ]["type"]){
			case "vps":
				$out .= "<td class=\"$class\" style=\"text-align:center;\">".$all_ips[ $ip ]["vps_xen_name"].":".$all_ips[ $ip ]["vps_server_hostname"]."</td><td class=\"$class\" style=\"text-align:center;\">".$av."</td>";
				break;
			case "ssl":
				$out .= "<td class=\"$class\" style=\"text-align:center;\">-</td><td class=\"$class\" style=\"text-align:center;\">".$av."</td>";
				break;
			case "dedicated":
				$out .= "<td class=\"$class\">".$all_ips[ $ip ]["dedicated_server_hostname"]."</td><td class=\"$class\" style=\"text-align:center;\">-</td>";
				break;
			case "dom0":
				$out .= "<td class=\"$class\">".$all_ips[ $ip ]["hostname"]."</td><td class=\"$class\" style=\"text-align:center;\">-</td>";
				break;
			default:
				$out .= "<td class=\"$class\" style=\"text-align:center;\">-</td><td class=\"$class\" style=\"text-align:center;\">-</td>";
				break;
			}
			$out .= "</tr>";
		}else{
			$out .= "<td class=\"$class\" colspan=\"3\" style=\"text-align:center;\"><b>". _("FREE") ."</b></td></tr>";
		}
		$ip_long++;
	}
	$out .= "</table>";
	return $out;
}

//echo drawIPUsageReport();
//echo drawPool(3);
//$pools = fullIPUsage();
//echo "<pre>"; print_r($pools); echo "</pre>";

?>
