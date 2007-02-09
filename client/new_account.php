<?php
/**
 * @package DTC
 * @version $Id: new_account.php,v 1.30 2007/02/09 17:49:16 thomas Exp $
 * @abstract Localization must go on ... ;) seeb
 * @todo repair bug for 
 * "Cannot reselect transaction for id $extapi_pay_id: registration failed!" 
 * ".$txt_err_register_cant_reselect_trans[$lang]."
 * now $extapi_pay_id must be 
 * global $extapi_pay_id;
 */

	/*
	chages:
	NOT VALIDATED $txt_err_payment_not_valid[$lang]
	TRANSACTION FINISHED AND APPROVED moved to $txt_err_payment_finish_approved[$lang]
	PAYMENT CANCELED moved to $txt_err_payment_cancel[$lang]
	PAYMENT FAILED moved to $txt_err_payment_failed[$lang]

	"Cannot reselect transaction for id $extapi_pay_id: registration failed!" moved to $txt_err_register_cant_reselect_trans[$lang]
	"Cannot reselect user: registration failed!" moved to $txt_err_register_cant_reselect_user[$lang]
	"Cannot reselect product: registration failed!" moved to  $txt_err_register_cant_reselect_product[$lang]
	"Registration Succes...!" moved to ".$txt_err_register_succ[$lang]."
	*/


global $txt_register_new_account;

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
$lang_sel = skin($conf_skin,$anotherLanguageSelection,$txt_select_lang_title[$lang]);

$form = "";

// Renew a contact (or buy SSL token)
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "contract_renewal"){
	$ret = renew_form();
	$form = $ret["mesg"];
// Return from payment API (and maybe validate the payment)
}else if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "return_from_pay" || $_REQUEST["action"] == "enets-success")){
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
		$form .= $txt_err_register_cant_reselect_trans[$lang];//"Cannot reselect transaction for id $extapi_pay_id: registration failed!";
	}else{
		$a = mysql_fetch_array($r);
		$form .= "<h2>Your transaction status is now:</h2>";
		if($a["valid"] != "yes"){
			$form .= "<h3><font color=\"red\">".$txt_err_payment_not_valid[$lang]."<!-- NOT VALIDATED --></font></h3>
			That might need that your payment has been canceled or that it is still being proceed.
			If you have confirmed the payment then check a bit later here.<br><br>
			If the payment status was to stay like that, please contact customer support.";
		}else{
			$form .= "<h3><font color=\"green\">".$txt_err_payment_finish_approved[$lang]."<!-- TRANSACTION FINISHED AND APPROVED--></font></h3>";
			if($a["new_account"] == "yes"){
				$q2 = "SELECT * FROM $pro_mysql_new_admin_table WHERE paiement_id='$extapi_pay_id';";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$form .= $txt_err_register_cant_reselect_user[$lang];//"Cannot reselect user: registration failed!";
				}else{
					$a2 = mysql_fetch_array($r2);
					validateWaitingUser($a2["reqadm_login"]);
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
// A cancel occured (currently only from eNETS)
}else if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "enets-cancel"){
	$form .= "<h3><font color=\"red\">".$txt_err_payment_cancel[$lang]."<!-- PAYMENT CANCELED --></font></h3>
You have canceled the payment, your account wont be validated.
To start again the registration procedure, follow the link here:<br>
<a href=\"new_account.php\">".$txt_register_new_account[$lang]."</a>";
// The transaction have failed (currently only eNETS)
}else if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "enets-failed"){
	$form .= "<h3><font color=\"red\">".$txt_err_payment_failed[$lang]."<!-- PAYMENT FAILED --></font></h3>
The payment gateway have reported that your payment has failed. Contact us,
we also accept checks and wire transfers.";
// This is a new user registration
}else{
	$print_form = "yes";
	// Register form
	$reguser = register_user();
	// If err=0 then it's already in the new_admin form!
	if($reguser["err"] == 0){
		$form = "";
		$form .= "Your registration has been recorded in our database.<br>";
		$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE id='".$reguser["id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$form .= $txt_err_register_cant_reselect_user[$lang];//"Cannot reselect user: registration failed!";
		}else{
			// Get the recorded new admin in the new_admin table, and process the display of payment buttons
			$newadmin = mysql_fetch_array($r);
			$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$newadmin["product_id"]."';";
			$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				$form = $txt_err_register_cant_reselect_product[$lang];//"Cannot reselect product: registration failed!";
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
					$form = $txt_err_register_cant_reselect_product[$lang];//"Cannot reselect product: registration failed!";
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
					if($company_invoicing["vat_rate"] != 0 && $company_invoicing["vat_number"] != ""){
						$vat_rate = $company_invoicing["vat_rate"];
					}else{
						$vat_rate = 0;
					}
					$payid = createCreditCardPaiementID($product["price_dollar"],$reguser["id"],$product["name"],"yes",$product["id"],$vat_rate);
					$q = "UPDATE $pro_mysql_new_admin_table SET paiement_id='$payid' WHERE id='".$reguser["id"]."';";
					$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
					$return_url = $_SERVER["PHP_SELF"]."?action=return_from_pay&regid=$payid";
					$paybutton =paynowButton($payid,$product["price_dollar"],$product["name"],$return_url,$vat_rate);
				}
				if($print_form == "yes"){
					$form = $reguser["mesg"]."<br><h4>".$txt_err_register_succ[$lang]."<!--Registration successfull!--></h4>
Please now click on the following button to go for paiment:<br>
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
}
$login_skined = skin($conf_skin,$form,$txt_register_new_account[$lang]);
$mypage = layout_login_and_languages($login_skined,$lang_sel);
// Output the result !

//echo anotherPage($txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$HTML_admin_edit_data,$anotherFooter);
echo anotherPage("Client:","","",makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));

?>
