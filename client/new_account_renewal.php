<?php

function renew_form(){
	global $pro_mysql_admin_table;
	global $pro_mysql_new_admin_table;
	global $pro_mysql_product_table;
	global $pro_mysql_pending_renewal_table;
	global $pro_mysql_client_table;
	global $pro_mysql_companies_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_vps_server_table;

	global $conf_webmaster_email_addr;
	global $conf_message_subject_header;
	global $conf_this_server_country_code;

	global $secpayconf_currency_letters;

	global $cc_europe;

	get_secpay_conf();

	// Do field format checking and escaping for all fields
	if(!isFtpLogin($_REQUEST["adm_login"])){
		$ret["err"] = 2;
		$ret["mesg"] = "User login format incorrect. Please use letters and numbers only and from 4 to 16 chars.";
		return $ret;
	}

	$q = "SELECT adm_login,id_client FROM $pro_mysql_admin_table WHERE adm_login='".addslashes($_REQUEST["adm_login"])."';";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = "Username not found in database! Try again.";
		return $ret;
	}else{
		$admin = mysql_fetch_array($r);
	}

	if(isset($_REQUEST["renew_type"]) && ($_REQUEST["renew_type"] == "ssl" || $_REQUEST["renew_type"] == "ssl_renew")){
		$q = "SELECT * FROM $pro_mysql_product_table WHERE heb_type ='ssl';";
	}else{
		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".addslashes($_REQUEST["product_id"])."';";
	}
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = "<font color=\"red\">Cannot find product id!</font>";
		return $ret;
	}
	$a = mysql_fetch_array($r);
	$product = $a;
	$the_prod = $a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)";
	$prod_id = $a["id"];

	$form = "<b><u>". _("Renewal for login:") ."</u></b> ".$_REQUEST["adm_login"]."<br>";
	$form .= "<b><u>". _("Product to renew:") ."</u></b> ".$a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)<br><br>";

	switch($_REQUEST["renew_type"]){
	case "vps":
		if(!isRandomNum($_REQUEST["vps_id"])){
			$ret["err"] = 3;
			$ret["mesg"] = "<font color=\"red\">VPS id is not a valid number!</font>";
			return $ret;
		}
		$client_id = $_REQUEST["vps_id"];
		$q = "SELECT country_code  FROM $pro_mysql_vps_table,$pro_mysql_vps_server_table
		WHERE $pro_mysql_vps_table.id='".$_REQUEST["vps_id"]."' AND $pro_mysql_vps_server_table.hostname = $pro_mysql_vps_table.vps_server_hostname";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$ret["err"] = 3;
			$ret["mesg"] = "<font color=\"red\">Cannot find vps server country</font>";
			return $ret;
		}
		$ax = mysql_fetch_array($r);
		$country = $ax["country_code"];
		break;
	case "shared":
	case "ssl":
		if(!isRandomNum($_REQUEST["client_id"])){
			$ret["err"] = 3;$ret["mesg"] = "<font color=\"red\">Client id is not a valid number!</font>";
			return $ret;
		}
		$client_id = $_REQUEST["client_id"];
		$country = $conf_this_server_country_code;
		break;
	case "ssl_renew":
		if(!isRandomNum($_REQUEST["ssl_ip_id"])){
			$ret["err"] = 3;$ret["mesg"] = "<font color=\"red\">ssl_ip_id is not a valid number!</font>";
			return $ret;
		}
		$client_id = $_REQUEST["ssl_ip_id"];
		$country = $conf_this_server_country_code;
		break;
	case "server":
		if(!isRandomNum($_REQUEST["server_id"])){
			$ret["err"] = 3;
			$ret["mesg"] = "<font color=\"red\">Server id is not a valid number!</font>";
			return $ret;
		}
		$client_id = $_REQUEST["server_id"];
		$q = "SELECT country_code FROM $pro_mysql_dedicated_table WHERE id='".$_REQUEST["server_id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$ret["err"] = 3;
			$ret["mesg"] = "<font color=\"red\">Cannot find dedicated server country</font>";
			return $ret;
		}
		$ax = mysql_fetch_array($r);
		$country = $ax["country_code"];
		break;
	default:
		die("Renew type unknown line ".__LINE__." file ".__FILE__);	// To be implemented for other means!
		break;
	}

	$mail_content = "
Somebody tried to renew a contract. Here is the details of the renewal:

login: ".$_REQUEST["adm_login"]."
Product name: $the_prod
Renew product type: ".$_REQUEST["renew_type"]."
Service country: $country
";
	if($admin["id_client"] == 0){
		$ret["err"] = 3;
		$ret["mesg"] = "Admin does not link to a client.";
		return $ret;
	}

	// Get the client ID so we can get the country
	$q = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."'";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = "Client not found in database! Try again.";
		return $ret;
	}else{
		$client = mysql_fetch_array($r);
	}

	// Get the VAT from the invoicing company
	$company_invoicing_id = findInvoicingCompany ($country,$client["country"]);
	$q = "SELECT * FROM $pro_mysql_companies_table WHERE id='$company_invoicing_id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = "Cannot find company for invoicing.";
		return $ret;
	}
	$company_invoicing = mysql_fetch_array($r);

	// If VAT is set, use it.
	if($company_invoicing["vat_rate"] == 0 || $company_invoicing["vat_number"] == ""){
		$vat_rate = 0;
		$use_vat = "no";
	}else{
		// Both companies are in europe, in different countries, and customer as a VAT number,
		// then there is no VAT and the customer shall pay the VAT in it's own country
		// These are the VAT rules in the European Union...
		if($client["is_company"] == "yes" && $client["vat_num"] != ""
			&& isset($cc_europe[ $client["country"] ]) && isset($cc_europe[ $company_invoicing["country"] ])
			&& $client["country"] != $company_invoicing["country"]){
				$vat_rate = 0;
				$use_vat = "no";
		}else{
			$use_vat = "yes";
			$vat_rate = $company_invoicing["vat_rate"];
		}
	}

	$headers = "From: DTC Robot <$conf_webmaster_email_addr>";
	$subject = $admin["adm_login"] . " tried to renew $the_prod";
	mail($conf_webmaster_email_addr, "$conf_message_subject_header $subject", $mail_content, $headers);

	// Save the values in SQL and process the paynow buttons
	$q = "INSERT INTO $pro_mysql_pending_renewal_table (id,adm_login,renew_date,renew_time,product_id,renew_id,heb_type,country_code)
	VALUES ('','".$_REQUEST["adm_login"]."',now(),now(),'".$prod_id."','".$client_id."','".$_REQUEST["renew_type"]."','$country');";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$renew_id = mysql_insert_id();

	$payid = createCreditCardPaiementID($a["price_dollar"],$renew_id,$a["name"]." (login: ".$_REQUEST["adm_login"].")","no",$prod_id,$vat_rate);

	$q = "UPDATE $pro_mysql_pending_renewal_table SET pay_id='$payid' WHERE id='$renew_id';";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	$return_url = $_SERVER["PHP_SELF"]."?action=return_from_pay&regid=$payid";
	$paybutton = paynowButton($payid,$a["price_dollar"],$a["name"]." (login: ".$_REQUEST["adm_login"].")",$return_url,$vat_rate);
	$form .= _("Please click on the button below to send money in your account:") ."<br><br>". $paybutton;

	$ret["err"] = 0;
	$ret["mesg"] = $form;
	return $ret;
}

?>
