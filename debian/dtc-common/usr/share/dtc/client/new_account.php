<?php


// This one is moved before the includes so we can use $extapi_pay_id in the string files.
if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "return_from_pay" || $_REQUEST["action"] == "enets-success")){
	switch($_REQUEST["action"]){
	case "return_from_pay":
		$extapi_pay_id = $_REQUEST["regid"];
		break;
	case "enets-success":
		$extapi_pay_id = $_REQUEST["txnRef"];
		break;
	default:
		$extapi_pay_id = -1;
		break;
	}
}

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("new_account_form.php");
require_once("new_account_renewal.php");

get_secpay_conf();

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC");
if(isset($txt_top_menu_entrys)){
	$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);
}
$anotherLanguageSelection = anotherLanguageSelection();
$lang_sel = skin($conf_skin,$anotherLanguageSelection, _("Language") );

$form = "";

if(!isset($_REQUEST["action"])){
	$action = "reg_new_user";
}else{
	$action = $_REQUEST["action"];
}

switch($action){
// Renew a contact (or buy SSL token)
//Customer is renewing with funds already in his accounts.
case "contract_renewal":
case "renew_myaccount":
	$ret = renew_form();
	$form = $ret["mesg"];
	break;
// Return from payment API (and maybe validate the payment)
case "return_from_pay":
case "enets-success":
	// Here are paypal return parameters:
	// [action] => return_from_pay
	// [regid] => 50
	// [payment_date] => 06:56:27 Jan 06, 2005 PST
	// [txn_type] => web_accept
	// [last_name] => nymous
	// [payment_gross] => 26.21
	// [mc_currency] => USD
	// [item_name] => Multidomain Shared hosting 1GB
	// [payment_type] => instant
	// [business] => shop@gplhost.fr
	// [verify_sign] => AFtU8hb3ziAYPkUJ8R4GQPFdbI4aA9TkFyW9lEc1zVI4hyqkw0ZBOvm2
	// [payer_status] => verified
	// [test_ipn] => 1
	// [payer_email] => client@gplhost.fr
	// [tax] => 0.00
	// [txn_id] => 0NW35863KJ3304804
	// [first_name] => ano
	// [quantity] => 1
	// [receiver_email] => shop@gplhost.fr
	// [payer_id] => CHVT9B3VUVULC
	// [receiver_id] => 2F3WTPYL6SJM2
	// [item_number] => 13
	// [payment_status] =>
	// Completed [mc_fee] => 1.32
	// [payment_fee] => 1.32
	// [mc_gross] => 26.21
	// [custom] =>
	// [notify_version] => 1.6

	// Here are the eNETS parameters:
	// action=enets-success&
	// amount=20.84&
	// txnRef=12&
	// payment=credit&
	// txnDate=2006%2F05%2F04&
	// txnTime=17%3A07%3A09&
	// errorCode=00&
	// status=succ&
	// no_shipping=1&
	// mid=616&
	// item_name=Test+product1&
	// curCode=USD&
	// submit.x=127&
	// submit.y=18&
	// currency_code=USD


	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$extapi_pay_id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$form .= _("Cannot reselect transaction: registration failed!") ;//"Cannot reselect transaction for id $extapi_pay_id: registration failed!";
	}else{
		$a = mysql_fetch_array($r);
		$form .= "<h2>Your transaction status is now:</h2>";
		if($a["valid"] != "yes"){
			$form .= "<h3><font color=\"red\">". _("NOT VALIDATED") ."<!-- NOT VALIDATED --></font></h3>
			That might need that your payment has been canceled or that it is still being proceed.
			If you have confirmed the payment then check a bit later here.<br><br>
			If the payment status was to stay like that, please contact customer support.";
		}else{
			$form .= "<h3><font color=\"green\">". _("TRANSACTION FINISHED AND APPROVED") ."<!-- TRANSACTION FINISHED AND APPROVED--></font></h3>";
			if($a["new_account"] == "yes"){
				$q2 = "SELECT * FROM $pro_mysql_new_admin_table WHERE paiement_id='$extapi_pay_id';";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$form .= _("Cannot reselect user: registration failed!") ;//"Cannot reselect user: registration failed!";
				}else{
					$a2 = mysql_fetch_array($r2);
					validateWaitingUser($a2["id"]);
					$form .= "Your account has just been created. Please login <a href=\"/dtc\">here</a> to
					start using your account.<br><br>
					If you have registered your domain name yourself, then you should set the
					whois to point to the following name servers:<br>
					ns1: $conf_addr_primary_dns<br>
					ns2: $conf_addr_secondary_dns";
				}
			// If it's not a new account, then it's a renewal and there must be a record of it
			}else{
				$q2 = "SELECT * FROM $pro_mysql_pending_renewal_table WHERE pay_id='$extapi_pay_id';";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$form .= "Could not find your renewal order in the database!";
				}else{
					$a2 = mysql_fetch_array($r2);
					$ret = validateRenewal($a2["id"]);
					if($ret != true){
						$form .= $submit_err;
					}else{
						$form .= "Your renewal order has been processed!";
					}
				}
			}
		}
	}
	break;
// A cancel occured (currently only from eNETS)
case "enets-cancel":
	$form .= "<h3><font color=\"red\">". _("PAYMENT CANCELLED") ."<!-- PAYMENT CANCELED --></font></h3>".
_("You have canceled the payment, your account wont be validated. To start again the registration procedure, follow the link here:") ."<br>
<a href=\"new_account.php\">". _("Register a new account") ."</a>";
	break;
case "enets-failed":
// The transaction have failed (currently only eNETS)
	$form .= "<h3><font color=\"red\">". _("PAYMENT FAILED") ."<!-- PAYMENT FAILED --></font></h3>".
_("Payment gateway reports that your payment failed. Contact us, we also accept checks and wire transfers.");
	break;
// The customer wants to add: a shared account if he doesn't have one, a new dedicated or vps
case "add_new_service":
	if( !isRandomNum($_REQUEST["product_id"]) ){
		$form = _("The product ID is not a valid integer number.");
		break;
	}
	if( !isFtpLogin($_REQUEST["adm_login"])){
		$form = _("The requested login is not a valid login.");
		break;
	}
	if( !isHostnameOrIP($_REQUEST["vps_location"]) ){
		$form = _("Location is not a valid hostname.");
		break;
	}
	if( !isset($_REQUEST["vps_os"]) || ($_REQUEST["vps_os"] != "debian"
						&& $_REQUEST["vps_os"] != "centos"
						&& $_REQUEST["vps_os"] != "gentoo"
						&& $_REQUEST["vps_os"] != "netbsd")){
		$form = _("VPS operating system not recognized");
		break;
	}
	// Product
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$form = _("Cannot reselect product: registration failed.") ;
		break;
	}
	$product = mysql_fetch_array($r);

	if($product["heb_type"] == "vps"){
		$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='".$_REQUEST["vps_location"]."'";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		if($n != 1){
			$form = _("Cannot reselect product: registration failed.") ;//"Cannot reselect product: registration failed.";
			break;
		}else{
			$vps_server = mysql_fetch_array($r);
			$service_location = $vps_server["country_code"];
		}
	}else{
		$service_location = $conf_this_server_country_code;
	}

	// Admin
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["adm_login"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$form .= _("Cannot reselect user: registration failed.");
		break;
	}
	$admin = mysql_fetch_array($r);

	// Client
	$q = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$form .= _("Cannot reselect client: registration failed.");
		break;
	}
	$client = mysql_fetch_array($r);

	$q = "INSERT INTO $pro_mysql_new_admin_table (id,reqadm_login,reqadm_pass,domain_name,family_name,first_name,
	comp_name,iscomp,email,
	phone,fax,addr1,addr2,addr3,
	zipcode,city,state,country,

	product_id,
	custom_notes,vps_location,vps_os,
	
	vat_num,shopper_ip,date,time,add_service)

	VALUES ('','".$_REQUEST["adm_login"]."','','example.com','". mysql_real_escape_string($client["familyname"]) ."','". mysql_real_escape_string($client["christname"]) ."',
	'". mysql_real_escape_string($client["company_name"]) ."','".$client["is_company"]."','".$client["email"]."',
	'". mysql_real_escape_string($client["phone"]) ."','". mysql_real_escape_string($client["fax"]) ."','". mysql_real_escape_string($client["addr1"]) ."','". mysql_real_escape_string($client["addr2"]) ."','". mysql_real_escape_string($client["addr3"])."',
	'". mysql_real_escape_string($client["zipcode"]) ."','". mysql_real_escape_string($client["city"]) ."','". mysql_real_escape_string($client["state"]) ."','".$client["country"]."',
	
	'".$_REQUEST["product_id"]."',
	'".mysql_real_escape_string($_REQUEST["custom_notes"])."','".$_REQUEST["vps_location"]."','".$_REQUEST["vps_os"]."',
	
	'". mysql_real_escape_string($client["vat_num"]) ."','".$_SERVER["REMOTE_ADDR"]."','".date("Y-m-d")."','".date("H:i:s")."','yes')";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$insert_id = mysql_insert_id();

	if($product["heb_type"] == "vps"){
		$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='".$_REQUEST["vps_location"]."'";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		if($n != 1){
			$form = _("Cannot reselect product: registration failed.") ;//"Cannot reselect product: registration failed.";
			break;
		}else{
			$vps_server = mysql_fetch_array($r);
			$service_location = $vps_server["country_code"];
		}
	}else{
		$service_location = $conf_this_server_country_code;
	}

	$company_invoicing_id = findInvoicingCompany ($service_location,$client["country"]);
	$q = "SELECT * FROM $pro_mysql_companies_table WHERE id='$company_invoicing_id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	if($n != 1){
		$form = "Cannot find company invoicing line ".__LINE__." file ".__FILE__;
		break;
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
	$payid = createCreditCardPaiementID($product["price_dollar"] + $product["setup_fee"],$insert_id,$product["name"]." (login: ".$_REQUEST["adm_login"].")","yes",$product["id"],$vat_rate);
	$q = "UPDATE $pro_mysql_new_admin_table SET paiement_id='$payid' WHERE id='$insert_id';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$return_url = htmlentities($_SERVER["PHP_SELF"])."?action=return_from_pay&regid=$payid";
	$paybutton =paynowButton($payid,$product["price_dollar"] + $product["setup_fee"],$product["name"]." (login: ".$_REQUEST["adm_login"].")",$return_url,$vat_rate,$secpayconf_use_paypal_recurring);

	$master_total = $product["price_dollar"] + $product["setup_fee"];
	$form = "<h4>". _("New service registered successfully.") ."<!--Registration succesfull.--></h4>
<u>". _("Product name:") . "</u> " . $product["name"] ."<br>
<u>". _("Product price:") . "</u> " . $product["price_dollar"] ." $secpayconf_currency_letters<br>
<u>". _("Setup fees:") . "</u> " . $product["setup_fee"] ." $secpayconf_currency_letters<br>
<u>". _("Product net price before VAT and payment gateway:") . "</u> " . $master_total . " $secpayconf_currency_letters<br><br><br>
<b>". _("Please now click on the following button to go for payment:") ."</b><br>
<br>$paybutton";

/*	$form .= "This part is not finished! To add a new package, please register with another username until we have finished the feature.";
	$reguser = register_user("yes");
	// If err=0 then it's already in the new_admin form!
	if($reguser["err"] == 0){
	}
*/	break;
// This is a new user registration
default:
case "reg_new_user":
	$print_form = "yes";
	// Register form
	$reguser = register_user();
	// If err=0 then it's already in the new_admin form!
	if($reguser["err"] == 0){
		$form .= _("Your registration has been recorded in our database.")."<br>";
		$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE id='".$reguser["id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$form .= _("Cannot reselect user: registration failed.") ;//"Cannot reselect user: registration failed.";
		}else{
			// Get the recorded new admin in the new_admin table, and process the display of payment buttons
			$newadmin = mysql_fetch_array($r);
			$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$newadmin["product_id"]."';";
			$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				$form = _("Cannot reselect product: registration failed.") ;//"Cannot reselect product: registration failed.";
				$print_form = "no";
				$service_location = $conf_this_server_country_code;
			}else{
				$product = mysql_fetch_array($r);
			}
			switch($product["heb_type"]){
			default:
			case "shared":	// -> Something has to be done to select dedicated servers location in the form !!!
			case "server":
				$service_location = $conf_this_server_country_code;
				break;
			case "vps":
				$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='".$newadmin["vps_location"]."'";
				$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				if($n != 1){
					$form = _("Cannot reselect product: registration failed.") ;//"Cannot reselect product: registration failed!";
					$print_form = "no";
					$service_location = $conf_this_server_country_code;
				}else{
					$vps_server = mysql_fetch_array($r);
					$service_location = $vps_server["country_code"];
				}
				break;
			}
			if($print_form == "yes"){
				$company_invoicing_id = findInvoicingCompany ($service_location,$newadmin["country"]);
				$q = "SELECT * FROM $pro_mysql_companies_table WHERE id='$company_invoicing_id';";
				$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				if($n != 1){
					$form = "Cannot find company invoicing line ".__LINE__." file ".__FILE__;
					$print_form = "no";
				}else{
					$company_invoicing = mysql_fetch_array($r);
					// If VAT is set, use it.
					if($company_invoicing["vat_rate"] == 0 || $company_invoicing["vat_number"] == ""){
						$vat_rate = 0;
						$use_vat = "no";
					}else{
					        // Both companies are in europe, in different countries, and customer as a VAT number,
					        // then there is no VAT and the customer shall pay the VAT in it's own country
						// These are the VAT rules in the European Union...
						if($newadmin["iscomp"] == "yes" && $newadmin["vat_num"] != ""
								&& isset($cc_europe[ $newadmin["country"] ]) && isset($cc_europe[ $company_invoicing["country"] ])
								&& $newadmin["country"] != $company_invoicing["country"]){
							$vat_rate = 0;
							$use_vat = "no";
						}else{
				        	        $use_vat = "yes";
							$vat_rate = $company_invoicing["vat_rate"];
						}
					}
					$payid = createCreditCardPaiementID($product["price_dollar"] + $product["setup_fee"],$reguser["id"],$product["name"]." (login: ".$newadmin["reqadm_login"].")","yes",$product["id"],$vat_rate);
					$q = "UPDATE $pro_mysql_new_admin_table SET paiement_id='$payid' WHERE id='".$reguser["id"]."';";
					$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
					$return_url = htmlentities($_SERVER["PHP_SELF"])."?action=return_from_pay&regid=$payid";
					$paybutton =paynowButton($payid,$product["price_dollar"] + $product["setup_fee"],$product["name"]." (login: ".$newadmin["reqadm_login"].")",$return_url,$vat_rate,$secpayconf_use_paypal_recurring);
				}
				if($print_form == "yes"){
					$master_total = $product["price_dollar"] + $product["setup_fee"];
					$form = $reguser["mesg"]."<br><h4>". _("Registration successful.") ."<!--Registration successfull!--></h4>
<u>". _("Product name:") . "</u> " . $product["name"] ."<br>
<u>". _("Product price:") . "</u> " . $product["price_dollar"] ." $secpayconf_currency_letters<br>
<u>". _("Setup fees:") . "</u> " . $product["setup_fee"] ." $secpayconf_currency_letters<br>
<u>". _("Product net price before VAT and payment gateway:") . "</u> " . $master_total . " $secpayconf_currency_letters<br><br><br>
<b>". _("Please now click on the following button to go for payment:") . "</b><br>
<br>$paybutton";
				}
			}
		}
	}else if($reguser["err"] == 1){
		$form = registration_form();
	}else{
		$form = "<font color=\"red\">".$reguser["mesg"]."</font><br>"
		.registration_form();
	}
	break;
}

$login_skined = skin($conf_skin,$form, _("Register a new account") );
$mypage = layout_login_and_languages($login_skined,$lang_sel);
if(function_exists("skin_NewAccountPage")){
	skin_NewAccountPage($login_skined);
}else{
	echo anotherPage("Client:","","",makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));
}

?>
