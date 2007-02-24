<?php

require("$dtcshared_path/inc/forms/dedicated_strings.php");

function drawAdminTools_Dedicated($admin,$dedicated_server_hostname){
	global $adm_login;
	global $adm_pass;
	global $rub;
	global $addrlink;

	global $pro_mysql_product_table;
	global $pro_mysql_dedicated_table;

	global $lang;
	global $txt_ded_dedicated_server_contract;
	global $txt_ded_dedicated_server_expiration_date;
	global $txt_ded_your_dedicated_was_first_registred_on_the;
	global $txt_ded_please_renew_it_with_one_of_the_following_options;
	global $txt_ded_your_ded_will_expire_on_the;

	global $lang;

	$out = "";

	// Check owner and fetch!
	checkDedicatedAdmin($adm_login,$adm_pass,$dedicated_server_hostname);
	$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE server_hostname='$dedicated_server_hostname';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$out .= "Server not found!";
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
		$contact = "not found!";
	}
	$out .= "<h3>".$txt_ded_dedicated_server_contract[$lang]."</h3><br>$contract<br><br>";

	$ar = explode("-",$dedicated["expire_date"]);
	$out .= "<b><u>".$txt_ded_dedicated_server_expiration_date[$lang]."</u></b><br>";
	$out .= $txt_ded_your_dedicated_was_first_registred_on_the[$lang].$dedicated["start_date"]."<br>";
	if(date("Y") > $ar[0] ||
			(date("Y") == $ar[0] && date("m") > $ar[1]) ||
			(date("Y") == $ar[0] && date("m") == $ar[1] && date("d") > $ar[2])){
		$out .= "<font color=\"red\">"."Your dedicated server has expired on the: ".$dedicated["expire_date"]."</font>"
		."<br>".$txt_ded_please_renew_it_with_one_of_the_following_options[$lang]."<br>";
	}else{
		$out .= $txt_ded_your_ded_will_expire_on_the[$lang].$dedicated["expire_date"];
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
		<input type=\"submit\" value=\"".$a["name"]." (".$a["price_dollar"]." USD)"."\">
		</form>";
	}

//	$out .= "Dedicated server content!";

	return $out;
}

?>
