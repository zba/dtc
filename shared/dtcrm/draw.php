<?php

$registration_added_price = 3;
$renew_added_price = 2;

require_once "$dtcshared_path/dtcrm/draw_register_forms.php";
require_once "$dtcshared_path/dtcrm/draw_handle.php";
require_once "$dtcshared_path/dtcrm/draw_whois.php";
require_once "$dtcshared_path/dtcrm/draw_nameservers.php";
require_once "$dtcshared_path/dtcrm/draw_transferdomain.php";
require_once "$dtcshared_path/dtcrm/draw_adddomain.php";
require_once "$dtcshared_path/dtcrm/draw_renewdomain.php";

function draw_UpgradeAccount($admin){
	global $adm_pass;
	global $adm_login;
	global $addrlink;

	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_product_table;

	global $secpayconf_currency_letters;

	if(!isset($secpayconf_currency_letters)){
		get_secpay_conf();
	}


	$out = "";
	$nowrap = 'style="white-space:nowrap"';

	$frm_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"upgrade_myaccount\">
";
	$client = $admin["client"];
	$out .= "<b><u>". _("Upgrade my account:") ."</u></b><br>";
	if($admin["info"]["prod_id"] != 0){
		$out .= "<i><u>". _("Past account refundal") ."</u></i><br>";
		$out .= _("Your last command expire on the: ") .$admin["info"]["expire"].".<br>";
		$out .= _("Today is the: ") .date("Y-m-d")."<br>";
		$today = mktime (0,0,0,date("m"),date("d"),date("Y"));
		$ar = explode("-",$admin["info"]["expire"]);
		$expire = mktime (0,0,0,$ar[1],0,$ar[0]);
		$remaining_seconds = $expire - $today;
		$days_remaining = $remaining_seconds / (60*60*24);
		$days_outstanding = 0;
		// don't give credit if there are negative days remaining
		if ($days_remaining < 0)
		{
			$days_outstanding = $days_remaining;
			$days_remaining = 0;
		}	

		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$admin["info"]["prod_id"]."';";
		$r = mysql_query($q)or die("Cannot querry: \"$q\" !!!".mysql_error()." line ".__LINE__." in file ".__FILE__);
		$prod = mysql_fetch_array($r);
		$ar = explode("-",$prod["period"]);
		$prod_period = mktime (0,0,0,$ar[1],0,1970+$ar[0]);
		$prod_days =  $prod_period / (60*60*24);
		$price_per_days = $prod["price_dollar"] / $prod_days;

		$refundal = floor($days_remaining * $price_per_days);
		$owing = floor($days_outstanding * $price_per_days);

		$out .= _("Your past account was: ") .$prod["price_dollar"]." ".$secpayconf_currency_letters." for ".smartDate($prod["period"])."<br>";
		$out .= _("Refund")." (". $days_remaining. _(" days) for upgrading will be: "). "$refundal ".$secpayconf_currency_letters."<br><br>";
		$out .= _("You have")." (".$days_outstanding._(" days), with ") ."$owing". " " . $secpayconf_currency_letters ._(" remaining to be paid") ."<br>";
	}else{
		$out .= _("You currently don't have a validated account. Please contact customer support.") ;
		return $out;
	}
	$out .= "<i><u>". _("Step 1: choose your upgrade") ."</u></i><br>";
	if(!isset($_REQUEST["prod_id"]) || $_REQUEST["prod_id"] == ""){
		$out .= _("Your current account is ").smartByte($admin["info"]["quota"]*1024*1024). _(" disk storage and ")
.smartByte($admin["info"]["bandwidth_per_month_mb"]*1024*1024). _(" of data transfer each month.") ."<br><br>".
_("To what capacity would you like to upgrade to?") ."<br>";
		$q = "SELECT * FROM $pro_mysql_product_table WHERE quota_disk > '".$admin["info"]["quota"]."' OR bandwidth > '".$admin["info"]["bandwidth_per_month_mb"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "$frm_start";
		$out .= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"1\">";
		$out .= "<tr><td></td><td>". _("Product") ."</td><td>". _("Storage") ."</td><td>". _("Bandwidth/month") ."</td>
			<td>". _("Price") ."</td><td>". _("Period") ."</td></tr>";
		for($i=0;$i<$n;$i++){
			$ro = mysql_fetch_array($r);
			if($i % 2){
				$color = " bgcolor=\"#000000\" ";
				$fnt1 = "<font color=\"#FFFFFF\"> ";
				$fnt2 = "</font>";
			}else{
				$color = "";
				$fnt1 = "";
				$fnt2 = "";
			}
			$out .= '<tr><td>'.$fnt1.'<input type="radio" name="prod_id" value="'.$ro["id"].'">'.$fnt2.'</td>';
			$out .= "<td $color $nowrap >$fnt1".$ro["name"].$fnt2.'</td>';
			$out .= "<td $color $nowrap >$fnt1".smartByte($ro["quota_disk"]*1024*1024).$fnt2.'</td>';
			$out .= "<td $color $nowrap >$fnt1".smartByte($ro["bandwidth"]*1024*1024).$fnt2.'</td>';
			$out .= "<td $color $nowrap >$fnt1".$ro["price_dollar"].$fnt2.'</td>';

			$out .= "<td $color $nowrap >$fnt1".smartDate($ro["period"]).$fnt2.'</td></tr>';
		}
		$out .= '</table><center><input type="submit" value="Calculate price"></center></form>';
		return $out;
	}
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["prod_id"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Product not found !!!");
	$ro = mysql_fetch_array($r);

	$frm_start .= '<input type="hidden" name="prod_id" value="'.$ro["id"].'">';
	$out .= "You have selected: ".$ro["name"];
	$out .= " (Storage: ".smartByte($ro["quota_disk"]*1024*1024);
	$out .= ", Transfer: ".smartByte($ro["bandwidth"]*1024*1024).'), ';
	$out .= '$'.$ro["price_dollar"].' each '.smartDate($ro["period"]);

	$out .=  "<br><br><i><u>Step 2: proceed to upgrade</u></i><br>";
	$remaining = $admin["client"]["dollar"];

	$ze_price = $ro["price_dollar"];
	$heber_price = $ze_price - $refundal;

	if(isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "return_from_paypal_upgrade_account"){
		$ze_refund = isPayIDValidated(addslashes($_REQUEST["pay_id"]));
		if($ze_refund == 0){
			$out .= "<font color=\"red\">The transaction failed, please try again!</font>";
		}else{
			$out .= "<font color=\"green\">Funds added to your account!</font><br>";
			$q = "UPDATE $pro_mysql_client_table SET dollar = dollar+".$ze_refund." WHERE id='".$admin["info"]["id_client"]."';";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$admin["client"]["dollar"] += $ze_refund;
			$remaining += $ze_refund;
		}
	}

	$out .= "Remaining on your account: " . $remaining . "$secpayconf_currency_letters<br>
New account price: ". $ze_price . "$secpayconf_currency_letters<br>
Past account refundal: ". $refundal . "$secpayconf_currency_letters<br>
Total price: ". $heber_price . "$secpayconf_currency_letters<br>";

	if($heber_price > $remaining){
		$to_pay = $heber_price - $remaining;

		$payid = createCreditCardPaiementID($to_pay,$admin["info"]["id_client"],
			"Account upgrade: ".$ro["name"],"no");
		$return_url = $_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass"
		."&addrlink=$addrlink&action=upgrade_myaccount&prod_id=9&inner_action=return_from_paypal_upgrade_account&payid=$payid";
		$payButton = paynowButton($payid,$heber_price,"Account upgrade: ".$ro["name"],$return_url);

		$out .= "<br>". _("You currently don't have enough funds on your account. You will be redirected to our payment system. Please click on the button below to pay.") ."<br><br>".$payButton;
		return $out;
	}

	$after_upgrade_remaining = $remaining - $heber_price;
	$out .= "After upgrade, you will have: " . $after_upgrade_remaining . " " .$secpayconf_currency_letters . "<br><br>";

	// Check for confirmation
	if(isset($_REQUEST["toreg_confirm_register"]) && $_REQUEST["toreg_confirm_register"] != "yes"){
		$out .= _("You have enough funds on your account to proceed account upgrade. Press the confirm button and your order will be proceeded.") ."<br><br>
$frm_start
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
<input type=\"submit\" value=\"". _("Proceed to account upgrade") ."\">
</form>";
		return $out;
	}




	return $out;
}

?>
