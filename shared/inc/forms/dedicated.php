<?php

function drawAdminTools_Dedicated($admin,$dedicated_server_hostname){
	global $adm_login;
	global $adm_pass;
	global $rub;
	global $addrlink;

	global $pro_mysql_product_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_dedicated_ips_table;

	global $secpayconf_currency_letters;

	get_secpay_conf();

	$out = "";

	// Check owner and fetch!
	checkDedicatedAdmin($adm_login,$adm_pass,$dedicated_server_hostname);
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE server_hostname='$dedicated_server_hostname';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$out .= _("Server not found!");
	}
	$dedicated = mysql_fetch_array($r);

	// Display the current contract
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$dedicated["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 1){
		$server_prod = mysql_fetch_array($r);
		$contract = $server_prod["name"];
	}else{
		$contact = _("Not found!");
	}
	$out .= "<h3>". _("Dedicated server contract:") ."</h3><br>$contract<br><br>";

	$ar = explode("-",$dedicated["expire_date"]);
	$out .= "<b><u>". _("Dedicated server expiration dates:") ."</u></b><br>";
	$out .= _("Your dedicated server was first registered on the:") ." ".$dedicated["start_date"]."<br>";
	if(date("Y") > $ar[0] ||
			(date("Y") == $ar[0] && date("m") > $ar[1]) ||
			(date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
		$out .= "<font color=\"red\">". _("Your dedicated server has expired on the: ") .$dedicated["expire_date"]."</font>"
		."<br>". _("Please renew it with one of the following options") ."<br>";
	}else{
		$out .= _("Your dedicated server will expire on the: ") .$dedicated["expire_date"];
	}

	$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$dedicated["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= "<form action=\"/dtc/new_account.php\">
		<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
		<input type=\"hidden\" name=\"renew_type\" value=\"server\">
		<input type=\"hidden\" name=\"product_id\" value=\"".$a["id"]."\">
		<input type=\"hidden\" name=\"server_id\" value=\"".$dedicated["id"]."\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"submit\" value=\"".$a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)"."\">
		</form>";
	}

//	$out .= "Dedicated server content!";
	$out .= "<br><br><h3>"._("IP addresses: ")."</h3>";
	$frm_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";

	$q = "SELECT * FROM $pro_mysql_dedicated_ips_table WHERE dedicated_server_hostname='$dedicated_server_hostname'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($i % 2){
			$alt_color = 0;
		}else{
			$alt_color = 1;
		}
/*		$out .= dtcFormLineDraw($a["ip_addr"],"$frm_start<input type=\"hidden\" name=\"action\" value=\"set_dedicated_ip_rdns\">
<input type=\"hidden\" name=\"ip_addr\" value=\"".$a["ip_addr"]."\">
<input type=\"text\" name=\"rdns\" value=\"".$a["rdns_addr"]."\">
		                </td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\"
		                onMouseOut=\"this.className='input_btn_container';\">
		                 <div class=\"input_btn_left\"></div>
		                  <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\""._("Change RDNS")."\"></div>
		                   <div class=\"input_btn_right\"></div>
		                   </div></form>",$alt_color);*/
		if($i > 0){
			$out .= ", ";
		}
		$out .= $a["ip_addr"];
	}
	return $out;
}

?>
