<?php

function drawAdminTools_Custom($admin,$custom_id){
	global $adm_login;
	global $adm_pass;
	global $rub;
	global $addrlink;

	global $pro_mysql_product_table;
	global $pro_mysql_custom_product_table;

	global $secpayconf_currency_letters;

	global $submit_err_custom;

	get_secpay_conf();

	$out = "<font color=\"red\">$submit_err_custom</font>";

	// Check owner and fetch!
	checkCustomAdmin($adm_login,$adm_pass,$custom_id);
	$q = "SELECT * FROM $pro_mysql_custom_product_table WHERE id='$custom_id';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$out .= _("Custom id not found!");
	}
	$custom_prod = mysql_fetch_array($r);

	// Display the current contract
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$custom_prod["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$server_prod = mysql_fetch_array($r);
		$contract = $server_prod["name"];
	}else{
		$contact = _("Not found!");
	}
	$out .= "<h3>". _("Custom product contract:") ."</h3><br>$contract<br><br>";

	$ar = explode("-",$custom_prod["expire_date"]);
	$out .= "<b><u>". _("Custom product expiration dates:") ."</u></b><br>";
	$out .= _("Your custom product was first registered on the:") ." ".$custom_prod["start_date"]."<br>";
	if(date("Y") > $ar[0] ||
			(date("Y") == $ar[0] && date("m") > $ar[1]) ||
			(date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
		$out .= "<font color=\"red\">". _("Your custom product has expired on the: ") .$custom_prod["expire_date"]."</font>"
		."<br>". _("Please renew it with one of the following options") ."<br>";
	}else{
		$out .= _("Your custom product will expire on the: ") .$custom_prod["expire_date"];
	}

	$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$custom_prod["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= "<br><form action=\"/dtc/new_account.php\">
		<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
		<input type=\"hidden\" name=\"renew_type\" value=\"custom\">
		<input type=\"hidden\" name=\"product_id\" value=\"".$a["id"]."\">
		<input type=\"hidden\" name=\"custom_id\" value=\"".$custom_prod["id"]."\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		".submitButtonStart().$a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)".submitButtonEnd()."
		</form><br>";
	}

	return $out;
}

?>
