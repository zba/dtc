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
	global $pro_mysql_companies_table;
	global $pro_mysql_pending_renewal_table;


	global $secpayconf_currency_letters;
	global $conf_this_server_country_code;

	if(!isset($secpayconf_currency_letters)){
		get_secpay_conf();
	}


	$out = "";
	$nowrap = 'style="white-space:nowrap"';

	$frm_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"upgrade_myaccount\">
";
	$client = $admin["client"];
	$out .= "<b><u>". _("Upgrade my account:") ."</u></b><br>";
	if($admin["info"]["prod_id"] != 0){
		$out .= "<i><u>". _("Past account refunds") ."</u></i><br>";
		$out .= _("Your last command expired on the: ") .$admin["info"]["expire"].".<br>";
		$out .= _("Today is the: ") .date("Y-m-d")."<br>";
		$today = mktime (0,0,0,date("m"),date("d"),date("Y"));
		$ar = explode("-",$admin["info"]["expire"]);
		$expire = mktime (0,0,0,$ar[1],$ar[2],$ar[0]);
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
		$prod_period = mktime (0,0,0,$ar[1]+1,1,1970+$ar[0]);
		$prod_days =  $prod_period / (60*60*24);
		$price_per_days = $prod["price_dollar"] / $prod_days;

		$refundal = floor($days_remaining * $price_per_days);
		$owing = floor($days_outstanding * $price_per_days);

		$out .= _("Your past account was: ") .$prod["price_dollar"]." ".$secpayconf_currency_letters." " . _("for") . " ".smartDate($prod["period"])."<br>";
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
		$q = "SELECT * FROM $pro_mysql_product_table WHERE (quota_disk > '".$admin["info"]["quota"]."' OR bandwidth > '".$admin["info"]["bandwidth_per_month_mb"]."' or max_domain>".$admin["info"]["max_domain"].") and heb_type='shared' AND private='no';";
		$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "$frm_start";
		$out .= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"1\">";
		$out .= "<tr><td></td><td>". _("Product") ."</td><td>". _("Storage") ."</td><td>". _("Bandwidth/month") ."</td>
			<td>". _("Max. Domains") ."</td><td>". _("Price") ."</td><td>". _("Period") ."</td></tr>";
		if ($n > 0 ) {
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
				$out .= "<td $color $nowrap >$fnt1".$ro["max_domain"].$fnt2.'</td>';
				$out .= "<td $color $nowrap >$fnt1".$ro["price_dollar"].$fnt2.'</td>';
	
				$out .= "<td $color $nowrap >$fnt1".smartDate($ro["period"]).$fnt2.'</td></tr>';
			}
			$out .= '</table><center><input type="submit" value="' . _("Calculate price") . '"></center></form>';
		} else {
			$out .= '</table><center>' . _("There is no product with greater capacity available, please contact Customer Support.") . '</center></form>' ;
		}
		return $out;
	}
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["prod_id"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Product not found !!!");
	$ro = mysql_fetch_array($r);
	$q = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["info"]["id_client"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Client not found !!!");
	$rocli = mysql_fetch_array($r);

	$frm_start .= '<input type="hidden" name="prod_id" value="'.$ro["id"].'">';
	$out .= _("You have selected") . ": ".$ro["name"];
	$out .= " (". _("Storage"). ": ".smartByte($ro["quota_disk"]*1024*1024);
	$out .= ", " . _("Transfer") . ": ".smartByte($ro["bandwidth"]*1024*1024).', ';
	$out .= ", " . _("Max. Domains") . ": ".$ro["max_domain"].'), ';
	$out .= '$'.$ro["price_dollar"].' ' . _("each") . ' '.smartDate($ro["period"]);

	$out .=  "<br><br><i><u>" . _("Step 2: proceed to upgrade") . "</u></i><br>";
	$remaining = $admin["client"]["dollar"];

	$ze_price = $ro["price_dollar"];
	$heber_price = $ze_price - $refundal;

	if(isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "return_from_paypal_upgrade_account"){
		$ze_refund = isPayIDValidated(mysql_real_escape_string($_REQUEST["pay_id"]));
		if($ze_refund == 0){
			$out .= "<font color=\"red\">" . _("The transaction failed, please try again.") . "</font>";
		}else{
			$out .= "<font color=\"green\">" . _("Funds added to your account.") . "</font><br>";
			$q = "UPDATE $pro_mysql_client_table SET dollar = dollar+".$ze_refund." WHERE id='".$admin["info"]["id_client"]."';";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$admin["client"]["dollar"] += $ze_refund;
			$remaining += $ze_refund;
		}
	}

	if(isset($_REQUEST["inner_action"]) && $_REQUEST["inner_action"] == "toreg_confirm_register"){
		$q = "UPDATE $pro_mysql_client_table SET dollar = ". $_REQUEST["amount"] . " WHERE id='".$admin["info"]["id_client"]."';";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		validateRenewal($_REQUEST["renew_id"]);

		$out .= '<BR><BR>' . _("Your Product has been Upgraded.");

		return $out;
 	}
	
	$out .= _("Remaining on your account") . ": " . $remaining . "$secpayconf_currency_letters<br>
" . _("New account price") . ": ". $ze_price . "$secpayconf_currency_letters<br>
" . _("Past account refundal") . ": ". $refundal . "$secpayconf_currency_letters<br>
" . _("Total price") . ": ". $heber_price . "$secpayconf_currency_letters<br>";

	$payid = createCreditCardPaiementID($heber_price,$admin["info"]["id_client"],
		"Account upgrade: ".$ro["name"],"no");
	$return_url = htmlentities($_SERVER["PHP_SELF"])."?adm_login=$adm_login&adm_pass=$adm_pass"
	."&addrlink=$addrlink&action=upgrade_myaccount&prod_id=9&inner_action=return_from_paypal_upgrade_account&payid=$payid";

	$service_location = $conf_this_server_country_code;

       	$company_invoicing_id = findInvoicingCompany ($conf_this_server_country_code,$rocli["country"]);
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

	// Save the values in SQL and process the paynow buttons
	$q = "INSERT INTO $pro_mysql_pending_renewal_table (id,adm_login,renew_date,renew_time,product_id,renew_id,heb_type,country_code)
	VALUES ('','".$_REQUEST["adm_login"]."',now(),now(),'".$ro["id"]."','".$rocli["id"]."','shared-upgrade','".$rocli["country"]."');";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$renew_id = mysql_insert_id();

	if($heber_price > $remaining){
		$to_pay = $heber_price - $remaining;

		$payid = createCreditCardPaiementID($to_pay,$renew_id,"Account upgrade: ".$ro["name"]." (login: ".$_REQUEST["adm_login"].")","no",$prod_id,$vat_rate);

		$q = "UPDATE $pro_mysql_pending_renewal_table SET pay_id='$payid' WHERE id='$renew_id';";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

		$payButton = paynowButton($payid,$to_pay,"Account upgrade: ".$ro["name"],$return_url,$vat_rate);

		$out .= "<br>". _("You currently don't have enough funds in your account. You will be redirected to our payment system. Please click on the button below to pay.") ."<br><br>".$payButton;
		return $out;
	}
	else
	{
		$payid = createCreditCardPaiementID($heber_price,$renew_id,"Account upgrade: ".$ro["name"]." (login: ".$_REQUEST["adm_login"].")","no",$ro["id"],$vat_rate);

		$q = "UPDATE $pro_mysql_pending_renewal_table SET pay_id='$payid' WHERE id='$renew_id';";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

	$after_upgrade_remaining = $remaining - $heber_price;
	$out .= _("After upgrade, you will have") . ": " . $after_upgrade_remaining . " " .$secpayconf_currency_letters . "<br><br>";

	// Check for confirmation
	$out .= _("You have enough funds in your account to proceed with an account upgrade. Press the confirm button and your order will be processed.") ."<br><br>
$frm_start
<input type=\"hidden\" name=\"renew_id\" value=\"" . $renew_id . "\">
<input type=\"hidden\" name=\"amount\" value=\"" . $after_upgrade_remaining . "\">
<input type=\"hidden\" name=\"inner_action\" value=\"toreg_confirm_register\">
<input type=\"submit\" value=\"". _("Proceed to account upgrade") ."\">
</form>";
		return $out;

	}


	return $out;
}

?>
