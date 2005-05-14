<?php


require_once("../shared/autoSQLconfig.php");
$panel_type="client";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("new_account_form.php");

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
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "return_from_pay"){
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
	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id_client='".$_REQUEST["regid"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$form .= "Cannot reselect transaction: registration failed!";
	}else{
		$a = mysql_fetch_array($r);
		$form .= "<h2>Your transaction status is now:</h2>";
		if($a["valid"] != "yes"){
			$form .= "<h3><font color=\"red\">NOT VALIDATED</font></h3>
			That might need that your payment has been canceled or that it is still being proceed.
			If you have confirmed the payment then check a bit later here.<br><br>
			If the payment status was to stay like that, please contact customer support.";
		}else{
			$q2 = "SELECT * FROM $pro_mysql_new_admin_table WHERE id='".$_REQUEST["regid"]."';";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$form .= "Cannot reselect user: registration failed!";
			}else{
				$a2 = mysql_fetch_array($r2);
				validateWaitingUser($a2["reqadm_login"]);
				$form .= "<h3><font color=\"green\">TRANSACTION FINISHED AND APPROVED</font></h3>
				Your account has just been created. Please login <a href=\"/dtc\">here</a> to
				start using your account.";
			}
		}
	}
}else{
	$reguser = register_user();
	if($reguser["err"] == 0){
		$form .= "Your registration has been recorded in our database.<br>";
		$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE id='".$reguser["id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$form .= "Cannot reselect user: registration failed!";
		}else{
			$newadmin = mysql_fetch_array($r);
			$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$newadmin["product_id"]."';";
			$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				$form = "Cannot reselect product: registration failed!";
			}
			$product = mysql_fetch_array($r);
			$payid = createCreditCardPaiementID($product["price_dollar"],$reguser["id"],$product["name"],"yes");
			$q = "UPDATE $pro_mysql_new_admin_table SET paiement_id='$payid' WHERE id='".$reguser["id"]."';";
			$r = mysql_query($q)or die("Cannot query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
			$return_url = $_SERVER["PHP_SELF"]."?action=return_from_pay&regid=".$reguser["id"];
			$paybutton =paynowButton($payid,$product["price_dollar"],$product["name"],$return_url);
			$form = $reguser["mesg"]."<br><h4>Registration successfull!</h4>
Please now click on the following button to go for paiment:<br>
<br>$paybutton";
		}
	}else if($reguser["err"] == 1){
		$form = registration_form();
	}else{
		$form = "<font color=\"red\">".$reguser["mesg"]."</font><br>"
		.registration_form();
	}
}
$login_skined = skin($conf_skin,$form,"New account registration:");
$mypage = layout_login_and_languages($login_skined,$lang_sel);
// Output the result !

//echo anotherPage($txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$HTML_admin_edit_data,$anotherFooter);
echo anotherPage("Client:","","",makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));

?>
