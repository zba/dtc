<?php

require("$dtcshared_path/securepay/paiement_config.php");
require("$dtcshared_path/securepay/pay_functions.php");

if($conf_use_worldpay == "yes")	include("$dtcshared_path/securepay/worldpay.php");
if($conf_use_paypal == "yes")	include("$dtcshared_path/securepay/paypal.php");

function paynowButton($pay_id,$amount){
	global $conf_use_paypal;
	global $conf_use_worldpay;
	$out .= "<table width=\"100%\" height=\"1\">";
	$out .= "<tr><td>Paiement system</td><td>Cost</td><td>Total</td><td>Instant account</td></tr>\n";
	if($conf_use_paypal == "yes"){
		$cost = $amount * 0.04;
		$total = $cost + $amount;
		$out .= "<tr><td>".paypalButton($pay_id,$amount,$button_text)."</td>";
		$out .= "<td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
	}
	if($conf_use_worldpay == "yes"){
		$cost = $amount * (0.0475 + 0.017);
		$total = $cost + $amount;
		$out .= "<tr><td>".worldPayButton($pay_id,$total,$button_text,$button_text)."</td>";
		$out .= "<td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
//		$out .= "<tr><td>".worldPayButton($pay_id,$amount,$button_text,$button_text)."</td>";
//		$out .= "<td>$cost<td><td>".$amount+$cost."</td><td>Yes</td></tr>\n";
	}
	$out .= "</table>";
	return $out;
}
 
?>
