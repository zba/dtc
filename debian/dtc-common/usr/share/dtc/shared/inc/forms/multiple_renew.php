<?php

function drawAdminTools_MultipleRenew($admin){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $pro_mysql_product_table;
	global $secpayconf_currency_letters;

	get_secpay_conf();
	$out = "<br><br>";

	//echo "<pre>"; print_r($admin); echo "</pre>";
	$out .= "<form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
<input type=\"hidden\" name=\"renew_type\" value=\"multiple-services\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<table cellspacing=\"0\" cellpading=\"1\" border=\"0\">
<tr><td class=\"dtcDatagrid_table_titles\"></td>
<td class=\"dtcDatagrid_table_titles\">"._("Renewal product")."</td>
<td class=\"dtcDatagrid_table_titles\">"._("Hostname")."</td>
</tr>";

	$nbr_vps = sizeof($admin["vps"]);
	for($i=0;$i<$nbr_vps;$i++){
		if($i % 2){
			$td = "td  class=\"dtcDatagrid_table_flds\"";
		}else{
			$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
		}
		$vps = $admin["vps"][$i];
		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$vps["product_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			return _("Cannot find your VPS product ID.");
		}
		$prod = mysql_fetch_array($r);
		$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$vps["product_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			return _("Cannot find renewal product ID for your VPS.");
		}
		$pop = "";
		for($j=0;$j<$n;$j++){
			$a = mysql_fetch_array($r);
			$pop .= "<option value=\"".$a["id"]."\">".$a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)</option>";
		}
		$out .= "<$td><input type=\"checkbox\" name=\"service_host[]\" value=\"vps:".$vps["vps_server_hostname"].":".$vps["vps_xen_name"]."\"></td>
<$td><select name=\"vps:".$vps["vps_server_hostname"].":".$vps["vps_xen_name"]."\">$pop</option></select></td>
<$td>".$vps["vps_server_hostname"].":".$vps["vps_xen_name"]."</td>
</tr>";
	}

	// echo "<pre>" ; print_r($admin["dedicated"]); echo "</pre>";
	$nbr_dedi = sizeof($admin["dedicated"]);
	for($i=0;$i<$nbr_dedi;$i++){
		if(($i+$nbr_vps) % 2){
			$td = "td  class=\"dtcDatagrid_table_flds\"";
		}else{
			$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
		}
		$dedi = $admin["dedicated"][$i];
		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$dedi["product_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			return _("Cannot find your dedicated server product ID.");
		}
		$prod = mysql_fetch_array($r);
		$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$dedi["product_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			return _("Cannot find renewal product ID for your dedicated server.");
		}
		$pop = "";
		for($j=0;$j<$n;$j++){
			$a = mysql_fetch_array($r);
			$pop .= "<option value=\"".$a["id"]."\">".$a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)</option>";
		}
		$out .= "<$td><input type=\"checkbox\" name=\"service_host[]\" value=\"server:".$dedi["server_hostname"]."\"></td>
<$td><select name=\"server:".$dedi["server_hostname"]."\">$pop</option></select></td>
<$td>".$dedi["server_hostname"]."</td>
</tr>";
	}

	if(($nbr_dedi+$nbr_vps) % 2){
		$td = "td  class=\"dtcDatagrid_table_flds\"";
	}else{
		$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
	}
	$out .= "<tr><$td colspan=\"3\">".submitButtonStart()._("Renew").submitButtonEnd()."</td></tr>";

	$out .= "</table></form>";
	return $out;
}

?>
