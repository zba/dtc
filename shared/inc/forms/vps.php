<?php

function drawAdminTools_VPS($admin,$vps){
	global $vps_name;
	global $vps_node;

	global $adm_login;
	global $adm_pass;
	global $rub;
	global $addrlink;

	global $vps_soap_err;

	global $pro_mysql_product_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;

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

	$vps_out = "";

	$vps_out_net_stats = "";
	$vps_out_hdd_stats = "";
	$vps_out_swap_stats = "";
	$vps_out_cpu_stats = "";


	// Calculate last month dates
	$cur_year = date("Y");
	$cur_month = date("m");

	$last_month = $cur_month - 1;
	if($last_month == 0){
		$last_month_year = $cur_year - 1;
		$last_month = 12;
	}else{
		$last_month_year = $cur_year;
	}

	$tow_month_ago = $last_month - 1;
	if($tow_month_ago == 0){
		$tow_month_ago = 12;
		$tow_month_ago_year = $last_month_year - 1;
	}else{
		$tow_month_ago_year = $last_month_year;
	}

	// Display the current contract
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$vps["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$vps_prod = mysql_fetch_array($r);
		$contract = $vps_prod["name"];
	}else{
		$contract = "not found!";
	}
	$out .= "<br><h3>". _("Description: ") ."</h3><br>". _("Current contract: ") ."$contract<br>";
	$q = "SELECT location FROM $pro_mysql_vps_server_table WHERE hostname='$vps_node';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$location = "Cannot find VPS server $vps_node<br>";
	}else{
		$vps_server = mysql_fetch_array($r);
		$location = $vps_server["location"];
	}
	$out .= _("Server location:")." ".$location."<br><br>";

	// Expiration management !
	$ar = explode("-",$vps["expire_date"]);
	$out .= "<h3>". _("Expiry date:") ."</h3><br>";
	$out .= _("Your VPS was first registered on the: ") .$vps["start_date"]."<br>";
	if(date("Y") > $ar[0] ||
			(date("Y") == $ar[0] && date("m") > $ar[1]) ||
			(date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
		$out .= "<font color=\"red\">". _("Your VPS has expired on the: ") .$vps["expire_date"]."</font>"
			."<br>". _("Please renew with one of the following options: ") ."<br>";
	}else{
		$out .= _("Your VPS will expire on the: ") .$vps["expire_date"];
	}

	// Renewal buttons
	$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$vps["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= "<br><br><form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
<input type=\"hidden\" name=\"renew_type\" value=\"vps\">
<input type=\"hidden\" name=\"product_id\" value=\"".$a["id"]."\">
<input type=\"hidden\" name=\"vps_id\" value=\"".$vps["id"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
".submitButtonStart().$a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)".submitButtonEnd()."
</form>";
	}

	return $out;
}

?>
