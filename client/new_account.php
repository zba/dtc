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
$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

$anotherLanguageSelection = anotherLanguageSelection();
$lang_sel = skin($conf_skin,$anotherLanguageSelection,$txt_select_lang_title[$lang]);

if($_REQUEST["action"] == "return_from_pay"){
	print_r($_REQUEST);
	$form = "Return from paiment API";
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
echo anotherPage("Client:".$txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$mypage,anotherFooter(""));

?>
