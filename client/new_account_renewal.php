<?php

function renew_form(){
	global $pro_mysql_admin_table;
	global $pro_mysql_new_admin_table;
	global $pro_mysql_product_table;
	global $pro_mysql_pending_renewal_table;
	global $conf_webmaster_email_addr;


	// Do field format checking and escaping for all fields
	if(!isFtpLogin($_REQUEST["adm_login"])){
		$ret["err"] = 2;
		$ret["mesg"] = "User login format incorrect. Please use letters and numbers only and from 4 to 16 chars.";
		return $ret;
	}

	$q = "SELECT adm_login FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["adm_login"]."';";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = "Username not found in database! Try again.";
		return $ret;
	}

	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".addslashes($_REQUEST["product_id"])."';";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 3;
		$ret["mesg"] = "<font color=\"red\">Cannot find product id!</font>";
		return $ret;
	}else{
		$a = mysql_fetch_array($r);
		$the_prod = $a["name"]." (".$a["price_dollar"]." USD)";
	}


	$form = "<b><u>Renewal for login:</u></b> ".$_REQUEST["adm_login"]."<br>";
	$form .= "<b><u>Product to renew:</u></b> ".$a["name"]." (".$a["price_dollar"]." USD)<br><br>";

	switch($_REQUEST["renew_type"]){
		case "vps":
			if(!isRandomNum($_REQUEST["vps_id"])){
				$ret["err"] = 3;
				$ret["mesg"] = "<font color=\"red\">VPS id is not a valid number!</font>";
				return $ret;
			}
			$client_id = $_REQUEST["vps_id"];
			break;
		case "shared":
		case "ssl":
			if(!isRandomNum($_REQUEST["client_id"])){
				$ret["err"] = 3;$ret["mesg"] = "<font color=\"red\">Client id is not a valid number!</font>";
				return $ret;
			}
			$client_id = $_REQUEST["client_id"];
			break;
		case "server":
			if(!isRandomNum($_REQUEST["server_id"])){
				$ret["err"] = 3;
				$ret["mesg"] = "<font color=\"red\">Server id is not a valid number!</font>";
				return $ret;
			}
			$client_id = $_REQUEST["server_id"];
			break;
		default:
			die("Renew type unknown line ".__LINE__." file ".__FILE__);	// To be implemented for other means!
			break;
	}

	$mail_content = "
Somebody tried to renew a contract. Here is the details of the renewal:

login: ".$_REQUEST["adm_login"]."
Product id: $the_prod
Renew product type: ".$_REQUEST["renew_type"]."

";

	$headers = "From: DTC Robot <$conf_webmaster_email_addr>";
	mail($conf_webmaster_email_addr, "[DTC] Somebody tried to renew", $mail_content, $headers);

	// Save the values in SQL and process the paynow buttons
	$q = "INSERT INTO $pro_mysql_pending_renewal_table (id,adm_login,renew_date,renew_time,product_id,renew_id,heb_type)
	VALUES ('','".$_REQUEST["adm_login"]."',now(),now(),'".$_REQUEST["product_id"]."','".$client_id."','".$_REQUEST["renew_type"]."');";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$renew_id = mysql_insert_id();

	$payid = createCreditCardPaiementID($a["price_dollar"],$renew_id,$a["name"],"no",$_REQUEST["product_id"]);

	$q = "UPDATE $pro_mysql_pending_renewal_table SET pay_id='$payid' WHERE id='$renew_id';";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	$return_url = $_SERVER["PHP_SELF"]."?action=return_from_pay&regid=$payid";
	$paybutton = paynowButton($payid,$a["price_dollar"],$a["name"],$return_url);
	$form .= "Please click on the button below to send money in your acount:<br><br>$paybutton";

	$ret["err"] = 0;
	$ret["mesg"] = $form;
	return $ret;
}

?>