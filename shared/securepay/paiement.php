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
		$total = $amount * 1.034 + 0.30;
		$cost = $amount - $total;
		$out .= "<tr><td>".paypalButton($pay_id,$amount,$button_text)."</td>";
		$out .= "<td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
	}
	if($conf_use_worldpay == "yes"){
		$total = $amount * 1.0475+ 0.017;
		$cost = $total - $amount;
		$out .= "<tr><td>".worldPayButton($pay_id,$total,$button_text,$button_text)."</td>";
		$out .= "<td>\$$cost</td><td>\$$total</td><td>No</td></tr>\n";
//		$out .= "<tr><td>".worldPayButton($pay_id,$amount,$button_text,$button_text)."</td>";
//		$out .= "<td>$cost<td><td>".$amount+$cost."</td><td>Yes</td></tr>\n";
	}
	$out .= "</table>";
	return $out;
}

?>