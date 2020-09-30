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
	global $pro_mysql_custom_product_table;

	global $conf_webmaster_email_addr;
	global $conf_message_subject_header;
	global $conf_this_server_country_code;

	global $secpayconf_currency_letters;

	global $cc_europe;

	get_secpay_conf();

	// Do field format checking and escaping for all fields
	if(!isFtpLogin($_REQUEST["adm_login"])){
		$ret["err"] = 2;
		$ret["mesg"] = _("User login format incorrect. Please use letters and numbers only and from 4 to 16 chars.");
		return $ret;
	}

	$q = "SELECT adm_login,id_client FROM $pro_mysql_admin_table WHERE adm_login='".mysql_real_escape_string($_REQUEST["adm_login"])."';";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = "Username not found in database! Try again.";
		return $ret;
	}else{
		$admin = mysql_fetch_array($r);
		$adm_login = $admin["adm_login"];
	}

	// This is the case of multiple services renewed at once
	if(isset($_REQUEST["renew_type"]) && $_REQUEST["renew_type"] == "multiple-services"){
		if( !is_array($_REQUEST["service_host"]) ){
			$ret["err"] = 3;
			$ret["mesg"] = "<font color=\"red\">"._("Error: wrong service_host format.")."</font>";
			return $ret;
		}
		$the_prod = "";
		$country = "";
		$price = 0;
		$services = "";
		$n = sizeof($_REQUEST["service_host"]);
		for($i=0;$i<$n;$i++){
			$onehost = $_REQUEST["service_host"][$i];
			$onehost_ar = explode(":",$onehost);
			$n_parms = sizeof($onehost_ar);
			if(sizeof($onehost_ar) < 2){
				$ret["err"] = 3;
				$ret["mesg"] = "<font color=\"red\">"._("Error: wrong service_host format.")."</font>";
				return $ret;
			}
			switch($onehost_ar[0]){
			case "vps":
				$node = $onehost_ar[1];
				$vps_num = $onehost_ar[2];
				if(!isHostname($node) || !isRandomNum($vps_num)){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: wrong service_host format.")."</font>";
					return $ret;
				}
				// Check if the VPS is really owned by $adm_login
				$q = "SELECT $pro_mysql_vps_table.product_id AS product_id,$pro_mysql_vps_server_table.country_code AS country_code
				FROM $pro_mysql_vps_table,$pro_mysql_vps_server_table
				WHERE owner='$adm_login' AND vps_server_hostname='$node' AND vps_xen_name='$vps_num'
				AND $pro_mysql_vps_server_table.hostname = $pro_mysql_vps_table.vps_server_hostname;";
				$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
				$num = mysql_num_rows($r);
				if($num != 1){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: you do not own this VPS.")."</font>";
					return $ret;
				}
				$vps = mysql_fetch_array($r);
				if($i > 0){
					$country .= "|";
				}
				$country .= $vps["country_code"];
				if(!isRandomNum($_REQUEST["vps:".str_replace(".","_",$node).":".$vps_num])){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: VPS renewal product format is wrong.")."</font>";
					return $ret;
				}
				$pid = $_REQUEST["vps:".str_replace(".","_",$node).":".$vps_num];
				$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$vps["product_id"]."' AND id='$pid';";
				$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
				$num = mysql_num_rows($r);
				if($num != 1){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: VPS renewal product ID not found.")."</font>";
					return $ret;
				}
				$p = mysql_fetch_array($r);
				if($i > 0){
					$services .= "|";
				}
				$services .= "vps:".$node.":".$vps_num.":".$pid;
				break;
			case "server":
				$host = $onehost_ar[1];
				if(!isHostname($host)){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: wrong service_host format.")."</font>";
					return $ret;
				}
				// Check if the dedicated is really owned by $adm_login
				$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE owner='$adm_login' AND server_hostname='$host';";
				$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
				$num = mysql_num_rows($r);
				if($num != 1){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: you do not own this dedicated server.")."</font>";
					return $ret;
				}
				$dedi = mysql_fetch_array($r);
				if($i > 0){
					$country .= "|";
				}
				$country .= $dedi["country_code"];
				if(!isRandomNum($_REQUEST["server:".str_replace(".","_",$host)])){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: dedicated server renewal product format is wrong.")."</font>";
					return $ret;
				}
				$pid = $_REQUEST["server:".str_replace(".","_",$host)];
				$q = "SELECT * FROM $pro_mysql_product_table WHERE renew_prod_id='".$dedi["product_id"]."' AND id='$pid';";
				$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
				$num = mysql_num_rows($r);
				if($num != 1){
					$ret["err"] = 3;
					$ret["mesg"] = "<font color=\"red\">"._("Error: dedicated server renewal product ID not found.")."</font>";
					return $ret;
				}
				$p = mysql_fetch_array($r);
				if($i > 0){
					$services .= "|";
				}
				$services .= "server:".$host.":".$pid;
				break;
			default:
				$ret["err"] = 3;
				$ret["mesg"] = "<font color=\"red\">"._("Error: only the renewal of multiple VPS and dedicated servers is supported.")."</font>";
				return $ret;
			}
			if($i > 0){
				$the_prod .= ", ";
			}
			$the_prod .= $p["name"]." (".number_format($p["price_dollar"], 2)." $secpayconf_currency_letters)";
			$price += $p["price_dollar"];
		}
	// General case, only one service is renewed
	}else{
		if(isset($_REQUEST["renew_type"]) && ($_REQUEST["renew_type"] == "ssl" || $_REQUEST["renew_type"] == "ssl_renew")){
			$q = "SELECT * FROM $pro_mysql_product_table WHERE heb_type ='ssl';";
		}else{
			$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".mysql_real_escape_string($_REQUEST["product_id"])."';";
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
		$the_prod = $a["name"]." (".number_format($a["price_dollar"], 2)." $secpayconf_currency_letters)";
		$prod_id = $a["id"];
		$price = $a["price_dollar"];
	}

	$form = "<b><u>". _("Renewal for login:") ."</u></b> ".$_REQUEST["adm_login"]."<br>";
	$form .= "<b><u>". _("Product to renew:") ."</u></b> ".$the_prod."<br><br>";

	switch($_REQUEST["renew_type"]){
	case "multiple-services":
		$client_id = $admin["id_client"];
		break;
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
	case "custom":
		if(!isRandomNum($_REQUEST["custom_id"])){
			$ret["err"] = 3;
			$ret["mesg"] = "<font color=\"red\">Custom id is not a valid number!</font>";
			return $ret;
		}
		$client_id = $_REQUEST["custom_id"];
		$q = "SELECT country_code FROM $pro_mysql_custom_product_table WHERE id='".$_REQUEST["custom_id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$ret["err"] = 3;
			$ret["mesg"] = "<font color=\"red\">Cannot find custom service country</font>";
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
		$ret["mesg"] = _("Error: admin does not link to a client ID.");
		return $ret;
	}

	// Get the client ID so we can get the country
	$q = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."'";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = _("Error: client ID not found in database.");
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


	if ( isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "toreg_confirm_renew" ) {
		if(!isDTCPassword($_REQUEST["validate_password"])){
			$ret["err"] = 3;
			$ret["mesg"] = _("Error: wrong password format.");
			return $ret;
		}
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='".$_REQUEST["validate_password"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$ret["err"] = 3;
			$ret["mesg"] = _("Error: wrong login or password.");
			return $ret;
		}
		if($client["dollar"] < $price){
			$ret["err"] = 3;
			$ret["mesg"] = _("Error: not enough money on your account.");
			return $ret;
		}
		// Adjust money remaining on account after confirmation.
		$form .= "<br>"._("Client: ").$client["id"];
		$form .= "<br>"._("Remaining on your account before payment: ").$client["dollar"];
		$form .= "<br>"._("Price: ").$price;
		$q = "UPDATE $pro_mysql_client_table SET dollar=dollar-" . $product["price_dollar"] . " WHERE id='".$client["id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());

		if(!isset($_REQUEST["renew_id"]) || !isRandomNum($_REQUEST["renew_id"])){
			die("Renew ID is not a number line ".__LINE__." file ".__FILE__);
		}
		validateRenewal($_REQUEST["renew_id"]);

		$form .= "<br><br><br><a href=\"/dtc/\">" . _("Continue") . "</a>";

		$ret["err"] = 0;
		$ret["mesg"] = $form;
		return $ret;
	}else{
		$headers = "From: DTC Robot <$conf_webmaster_email_addr>";
		if($_REQUEST["renew_type"] == "multiple-services"){
			$prodsub = "multiple services";
		}else{
			$prodsub = $the_prod;
		}
		$subject = $admin["adm_login"] . " tried to renew ".$prodsub;
		mail($conf_webmaster_email_addr, "$conf_message_subject_header $subject", $mail_content, $headers);

		if($_REQUEST["renew_type"] == "multiple-services"){
			$prod_id = 0;
		}else{
			$services = "";
		}

		// Save the values in SQL and process the paynow buttons
		$q = "INSERT INTO $pro_mysql_pending_renewal_table (id,adm_login,renew_date,renew_time,product_id,renew_id,heb_type,country_code,services)
		VALUES ('','".$_REQUEST["adm_login"]."',now(),now(),'".$prod_id."','".$client_id."','".$_REQUEST["renew_type"]."','$country','$services');";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$renew_id = mysql_insert_id();

		if($price > $client["dollar"]){
			$payid = createCreditCardPaiementID($price,$renew_id,$prodsub." (login: ".$_REQUEST["adm_login"].")","no",$prod_id,$vat_rate,$services);
	
			$q = "UPDATE $pro_mysql_pending_renewal_table SET pay_id='$payid' WHERE id='$renew_id';";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	
			$return_url = htmlentities($_SERVER["PHP_SELF"])."?action=return_from_pay&regid=$payid";
			$paybutton = paynowButton($payid,$price,$prodsub." (login: ".$_REQUEST["adm_login"].")",$return_url,$vat_rate);
			$form .= _("Please click on the button below to renew your account:") ."<br><br>". $paybutton;

			$ret["err"] = 0;
			$ret["mesg"] = $form;
			return $ret;
		}else{
			$payid = createCreditCardPaiementID($price,$renew_id,$prodsub." (login: ".$_REQUEST["adm_login"].")","no",$prod_id,$vat_rate,$services);

			$q = "UPDATE $pro_mysql_pending_renewal_table SET pay_id='$payid' WHERE id='$renew_id';";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

			$after_upgrade_remaining = $client["dollar"] - $price;
			$out = _("After renewal, you will have") . ": " . $after_upgrade_remaining . " " .$secpayconf_currency_letters . "<br><br>";

			// Check for confirmation
			$frm_start = "<form action=\"?\">
<input type=\"hidden\" name=\"action\" value=\"renew_myaccount\">
";
			$out .= _("You have sufficient funds in your account. Press the confirm button and your order will be processed.") ."<br><br>
$frm_start
<input type=\"hidden\" name=\"renew_type\" value=\"" . $_REQUEST["renew_type"]. "\">
<input type=\"hidden\" name=\"adm_login\" value=\"" . $adm_login. "\">
<input type=\"hidden\" name=\"product_id\" value=\"" . $prod_id. "\">
<input type=\"hidden\" name=\"client_id\" value=\"" . $client_id. "\">
<input type=\"hidden\" name=\"renew_id\" value=\"" . $renew_id . "\">
<input type=\"hidden\" name=\"inner_action\" value=\"toreg_confirm_renew\">
"._("Enter your password to accept the payment:")." <input type=\"password\" name=\"validate_password\" value=\"\">
<input type=\"submit\" value=\"". _("Proceed to account renewal") ."\">
</form>";
			$ret["err"] = 0;
			$ret["mesg"] = $out;
			return $ret;

		}
	}
}

?>
