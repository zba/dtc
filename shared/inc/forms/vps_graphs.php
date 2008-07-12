<?php

function drawAdminTools_VPSRRDGraphs($admin,$vps){
	global $vps_name;
	global $vps_node;

	global $adm_login;
	global $adm_pass;
	global $rub;
	global $addrlink;

	global $vps_soap_err;

	global $pro_mysql_product_table;
	global $pro_mysql_vps_ip_table;

	global $pro_mysql_vps_stats_table;
	global $secpayconf_currency_letters;

	global $panel_type;

	$reinstall_os = 1;

	get_secpay_conf();

	$out = "";

	$checker = checkVPSAdmin($adm_login,$adm_pass,$vps_node,$vps_name);
	if($checker != true){
		return _("Credential not correct: can't display in file ") .__FILE__." line ".__LINE__;
	}

	// RRD tools graphing
	// CPU rrd
	$out .= "<br><h3>"._("Hourly VPS usage statistics:")."</h3>";
	$out .= "<table cellspacing=\"4\" cellpadding=\"0\" border=\"0\">
<tr><td><img src=\"vm-cpu.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=hour\"></td></tr>
<tr><td><img src=\"vm-net.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=hour\"></td></tr>
<tr><td><img src=\"vm-io.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=hour\"></td></tr>
</table>";
	$out .= "<br><h3>"._("Daily VPS usage statistics:")."</h3>";
	$out .= "<table cellspacing=\"4\" cellpadding=\"0\" border=\"0\">
<tr><td><img src=\"vm-cpu.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=day\"></td></tr>
<tr><td><img src=\"vm-net.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=day\"></td></tr>
<tr><td><img src=\"vm-io.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=day\"></td></tr>
</table>";
	$out .= "<br><h3>"._("Weekly VPS usage statistics:")."</h3>";
	$out .= "<table cellspacing=\"4\" cellpadding=\"0\" border=\"0\">
<tr><td><img src=\"vm-cpu.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=week\"></td></tr>
<tr><td><img src=\"vm-net.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=week\"></td></tr>
<tr><td><img src=\"vm-io.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=week\"></td></tr>
</table>";

	$out .= "<br><h3>"._("Monthly VPS usage statistics:")."</h3>";
	$out .= "<table cellspacing=\"4\" cellpadding=\"0\" border=\"0\">
<tr><td><img src=\"vm-cpu.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=month\"></td></tr>
<tr><td><img src=\"vm-net.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=month\"></td></tr>
<td><img src=\"vm-io.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=month\"></td></tr>
</table>";
	$out .= "<br><h3>"._("Yearly VPS usage statistics:")."</h3>";
	$out .= "<table cellspacing=\"4\" cellpadding=\"0\" border=\"0\">
<tr><td><img src=\"vm-cpu.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=year\"></td><td></td></tr>
<tr><td><img src=\"vm-net.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=year\"></td><td></td></tr>
<tr><td><img src=\"vm-io.php?adm_login=$adm_login&adm_pass=$adm_pass&vps_server_hostname=$vps_node&vps_name=$vps_name&graph=year\"></td><td></td></tr>
</table>
";

	return $out;
}

?>
