<?php

function drawRenewalTables (){	
	global $lang;
	global $pro_mysql_product_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_ssl_ips_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_ssl_ips_table;
	global $pro_mysql_client_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_completedorders_table;
	global $pro_mysql_pay_table;

	global $lang;
	global $txt_renew_total_recurring_incomes_per_month;
	global $txt_renew_total;
	global $txt_renew_dedicated_servers;
	global $txt_renew_vps;
	global $txt_renew_ssl_ip;
	global $txt_renew_shared_hosting;
	global $txt_renew_shared_renewals;
	global $txt_renew_no_shared_account_expired;
	global $txt_renew_expiration_date;
	global $txt_login_title;
	global $txt_client;
	global $txt_renew_email;
	global $txt_renew_vps_title;
	global $txt_renew_server_title;
	global $txt_renew_client_name_not_found;
	global $txt_renew_no_ssl_ip_expired;
	global $txt_renew_vps_renewals;
	global $txt_renew_no_vps_expired;
	global $txt_renew_dedicated_servers_renewals;
	global $txt_renew_no_dedicated_server_expired;
	global $txt_renew_ssl_ips_renewals;

	global $secpayconf_currency_letters;

	get_secpay_conf();

	$out = "<h3>".$txt_renew_total_recurring_incomes_per_month[$lang]."</h3>";
	// Monthly recurring for shared hosting:
	$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period
	FROM $pro_mysql_product_table,$pro_mysql_admin_table
	WHERE $pro_mysql_product_table.id = $pro_mysql_admin_table.prod_id
	AND $pro_mysql_product_table.heb_type='shared'
	AND $pro_mysql_admin_table.expire != '0000-00-00'";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$total_shared = 0;
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$period = $a["period"];
		$price = $a["price_dollar"];
		if($period == '0001-00-00'){
			$total_shared += $price / 12;
		}else{
			$papoum = explode('-',$period);
			$months = $papoum[1];
			$total_shared += $price / $months;
		}
	}

	// Calculate how much SSL IPs have been taken
	$q = "SELECT count(id) as num_ssl FROM $pro_mysql_ssl_ips_table WHERE available='no'";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$total_ssl = 0;
	if($n != 0){
		$a = mysql_fetch_array($r);
		$q = "SELECT price_dollar FROM $pro_mysql_product_table WHERE heb_type='ssl'";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 0){
			$b = mysql_fetch_array($r);
			$total_ssl = $a["num_ssl"] * $b["price_dollar"] / 12;
		}
	}

	// Monthly recurring for VPS:
	$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period
	FROM $pro_mysql_product_table,$pro_mysql_vps_table
	WHERE $pro_mysql_product_table.id = $pro_mysql_vps_table.product_id";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$total_vps = 0;
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$period = $a["period"];
		$price = $a["price_dollar"];
		if($period == '0001-00-00'){
			$total_shared += $price / 12;
		}else{
			$papoum = explode('-',$period);
			$months = $papoum[1];
			$total_vps += $price / $months;
		}
	}

	// Monthly recurring for dedicated servers:
	$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period
	FROM $pro_mysql_product_table,$pro_mysql_dedicated_table
	WHERE $pro_mysql_product_table.id = $pro_mysql_dedicated_table.product_id";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$total_dedicated = 0;
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$period = $a["period"];
		$price = $a["price_dollar"];
		if($period == '0001-00-00'){
			$total_shared += $price / 12;
		}else{
			$papoum = explode('-',$period);
			$months = $papoum[1];
			$total_dedicated += $price / $months;
		}
	}

	$p_renewal = "";
	$p_renewal .= $txt_renew_shared_hosting[$lang].round($total_shared,2)." $secpayconf_currency_letters<br>";
	$p_renewal .= $txt_renew_ssl_ip[$lang].round($total_ssl,2)." $secpayconf_currency_letters<br>";
	$p_renewal .= $txt_renew_vps[$lang].round($total_vps,2)." $secpayconf_currency_letters<br>";
	$p_renewal .= $txt_renew_dedicated_servers[$lang].round($total_dedicated,2)." $secpayconf_currency_letters<br>";
	$big_total = $total_shared + $total_vps + $total_dedicated + $total_ssl;
	$p_renewal .= "<b>".$txt_renew_total[$lang].round($big_total,2)." $secpayconf_currency_letters</b>";

	# Show a quick history of payments
	$year = date("Y");
	$month = date("m");
	$cur_year = $year - 1;
	$cur_month = $month;
	$p_history = "";
	$p_history .= "<table cellspacing=\"1\" cellpadding=\"1\" border=\"1\">
	<tr><td>Period</td><td>Amount</td></tr>";
	for($i=0;$i<13;$i++){
		$q2 = "SELECT sum(refund_amount) as refund_amount FROM $pro_mysql_completedorders_table,$pro_mysql_pay_table
		WHERE $pro_mysql_completedorders_table.date LIKE '".$cur_year."-".$cur_month."%'
		AND $pro_mysql_completedorders_table.payment_id = $pro_mysql_pay_table.id";
		$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 > 0){
			$a2 = mysql_fetch_array($r2);
			$p_history .= "<tr><td>".$cur_year."-".$cur_month."</td><td>".$a2["refund_amount"]." $secpayconf_currency_letters</td></tr>";
		}
		$cur_month++;
		if($cur_month > 12){
			$cur_month = 1;
			$cur_year++;
		}
		if($cur_month < 10)	$cur_month = "0".$cur_month;
	}
	$p_history .= "</table>";
	

	# Layout the recuring stat and the effective payment statistics
	$out .= "<table cellspacing=\"1\" cellpadding=\"4\" border=\"0\">
	<tr><td>$p_history</td>
	<td valign=\"top\">$p_renewal</td></tr></table>";

	$out .= "<h3>".$txt_renew_shared_renewals[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE expire < '".date("Y-m-d")."' AND id_client!='0' ORDER BY expire;";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__);
	$n = mysql_num_rows($r);
	if($n < 1){
		$out .= $txt_renew_no_shared_account_expired[$lang]."<br>";
	}else{
		$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
		<tr><td>".$txt_login_title[$lang]."</td><td>".$txt_client[$lang]."</td><td>".$txt_renew_email[$lang]."</td><td>".$txt_renew_expiration_date[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$a["id_client"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$client_name = $txt_renew_client_name_not_found[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
			}
			$q2 = "SELECT * FROM $pro_mysql_domain_table WHERE owner='".$a["adm_login"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__);
			$n2 = mysql_num_rows($r2);
			if($n2 > 0){
				$out .= "<tr><td>".$a["adm_login"]."</td><td>$client_name</td><td>".$a2["email"]."</td><td>".$a["expire"]."</td></tr>";
			}
		}
		$out .= "</table>";
	}

	$out .= "<h3>".$txt_renew_ssl_ips_renewals[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE expire < '".date("Y-m-d")."' AND available='no' ORDER BY expire";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$out .= $txt_renew_no_ssl_ip_expired[$lang]."<br>";
	}else{
		$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
		<tr><td>".$txt_login_title[$lang]."</td><td>".$txt_client[$lang]."</td><td>".$txt_renew_email[$lang]."</td><td>".$txt_renew_expiration_date[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["adm_login"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				die("Cannot find admin name ".$a["adm_login"]." line ".__LINE__." file ".__FILE__);
			}else{
				$admin = mysql_fetch_array($r2);
			}
			$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$client_name = $txt_renew_client_name_not_found[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
			}
			$out .= "<tr><td>".$a["adm_login"]."</td><td>$client_name</td><td>".$a2["email"]."</td><td>".$a["expire"]."</td></tr>";
		}
		$out .= "</table>";
	}

	$out .= "<h3>".$txt_renew_vps_renewals[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_vps_table WHERE expire_date < '".date("Y-m-d")."' ORDER BY expire_date";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$out .= $txt_renew_no_vps_expired[$lang]."<br>";
	}else{
		$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
		<tr><td>".$a["adm_login"]."</td><td>".$txt_renew_vps_title[$lang]."</td><td>".$txt_client[$lang]."</td><td>".$txt_renew_email[$lang]."</td><td>".$txt_renew_expiration_date[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);

			$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["owner"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				die("Cannot find admin name ".$a["owner"]." line ".__LINE__." file ".__FILE__);
			}else{
				$admin = mysql_fetch_array($r2);
			}
			$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$client_name = $txt_renew_client_name_not_found[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
			}
			$out .= "<tr><td>".$a["owner"]."</td><td>".$a["vps_xen_name"].":".$a["vps_server_hostname"]."</td><td>$client_name</td><td>".$a2["email"]."</td><td>".$a["expire_date"]."</td></tr>";
		}
		$out .= "</table>";
	}

	$out .= "<h3>".$txt_renew_dedicated_servers_renewals[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE expire_date < '".date("Y-m-d")."' ORDER BY expire_date";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$out .= $txt_renew_no_dedicated_server_expired[$lang]."<br>";
	}else{
		$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
		<tr><td>Login:</td><td>".$txt_renew_server_title[$lang]."</td><td>".$txt_client[$lang]."</td><td>".$txt_renew_email[$lang]."</td><td>".$txt_renew_expiration_date[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["owner"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				die("Cannot find admin name ".$a["owner"]." line ".__LINE__." file ".__FILE__);
			}else{
				$admin = mysql_fetch_array($r2);
			}
			$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$client_name = $txt_renew_client_name_not_found[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
			}
			$out .= "<tr><td>".$a["owner"]."</td><td>".$a["server_hostname"]."</td><td>$client_name</td><td>".$a2["email"]."</td><td>".$a["expire_date"]."</td></tr>";
		}
		$out .= "</table>";
	}
	return $out;
}
