<?php

function drawAdminTools_Invoices($admin){
	global $pro_mysql_completedorders_table;
	global $pro_mysql_invoicing_table;
	global $pro_mysql_companies_table;
	global $pro_mysql_product_table;
	global $pro_mysql_pay_table;
	global $adm_login;
	global $adm_pass;

	$out = "";

	$q = "SELECT * FROM $pro_mysql_completedorders_table WHERE id_client='".$admin["client"]["id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		// Update the password for enabling downloads of PDF
		$q = "UPDATE $pro_mysql_completedorders_table SET download_pass='$adm_pass' WHERE id_client='".$admin["client"]["id"]."';";
		$r2 = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

		$out .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"1\">
<tr><td>". _("Date") ."</td><td>". _("Product") ."</td><td>" ._("Price"). "</td><td>". _("Payment gateway cost") ."</td><td>". _("Total Price") ."</td><td>". _("Invoice") ."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$a["product_id"]."';";
			$r2 = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$product_name = "Not found!";
			}else{
				$prod = mysql_fetch_array($r2);
				$product_name = $prod["name"];
			}
			$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$a["payment_id"]."';";
			$r2 = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$total = _("Not found!");
				$gate = _("Not found!");
				$refund = _("Not found!");
			}else{
				$pay = mysql_fetch_array($r2);
				$refund = $pay["refund_amount"];
				$gate = $pay["paiement_cost"];
				$total = $pay["paiement_total"];
			}
			$out .= "<tr><td>".$a["date"]."</td><td>$product_name</td><td>$refund</td><td>$gate</td><td>$total</td><td><a target=\"_blank\" href=\"/dtc/invoice.php?id=".$a["id"]."&download_pass=$adm_pass\">PDF</a></td></tr>";
		}
		$out .= "</table>";
	}else{
		$out .= _("No completed orders found.") ;
	}
	return $out;
}

?>
