<?php

function drawAdminTools_Invoices($admin){
	global $pro_mysql_completedorders_table;
	global $pro_mysql_invoicing_table;
	global $pro_mysql_companies_table;
	global $pro_mysql_product_table;
	global $pro_mysql_pay_table;
	global $pro_mysql_companies_table;
	global $conf_default_company_invoicing;

	global $adm_login;
	global $adm_pass;

	$out = "";

	$q = "SELECT * FROM $pro_mysql_companies_table WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 0){
		$out = _("There is no company defined: impossible to show invoices. Contact your administrator.");
		return $out;
	}

	$q = "SELECT * FROM $pro_mysql_companies_table WHERE id='".$conf_default_company_invoicing."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 0){
		$out = _("There is no default invoincing company set: impossible to show invoices. Contact your administrator.");
		return $out;
	}

	$q = "SELECT * FROM $pro_mysql_completedorders_table WHERE id_client='".$admin["client"]["id"]."' ORDER BY date;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		// Update the password for enabling downloads of PDF
		$q = "UPDATE $pro_mysql_completedorders_table SET download_pass='$adm_pass' WHERE id_client='".$admin["client"]["id"]."';";
		$r2 = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

		$out .= "<br><br>".dtcFormTableAttrs()."
<tr><td class=\"dtcDatagrid_table_titles\">". _("Date") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Product") ."</td>
<td class=\"dtcDatagrid_table_titles\">" ._("Price"). "</td>
<td class=\"dtcDatagrid_table_titles\">". _("Payment gateway cost") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Total Price") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Invoice") ."</td></tr>";
		for($i=0;$i<$n;$i++){
			if($i % 2){
				$td = "td  class=\"dtcDatagrid_table_flds\"";
			}else{
				$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
			}
			$a = mysql_fetch_array($r);
			if($a["product_id"] != 0){
				$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$a["product_id"]."';";
				$r2 = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$product_name = _("Product not found.");
				}else{
					$prod = mysql_fetch_array($r2);
					$product_name = $prod["name"];
				}
			}else{
				$servs = explode("|",$a["services"]);
				$n_servs = sizeof($servs);
				if($n_servs < 1){
					$product_name = _("Product not found.");
				}else{
					$product_name = "";
					for($j=0;$j<$n_servs;$j++){
						if($j>0){
							$product_name .= "<br>";
						}
						$attrs = explode(":",$servs[$j]);
						switch($attrs[0]){
						case "vps":
							$ind = 3;
							break;
						case "server":
							$ind = 2;
							break;
						default:
							$product_name .= _("Product not found.");
							continue;
							break;
						}
						$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$attrs[$ind]."';";
						$r2 = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
						$n2 = mysql_num_rows($r2);
						if($n2 != 1){
							$product_name .= _("Product not found.");
						}else{
							$prod = mysql_fetch_array($r2);
							$product_name .= $prod["name"];
						}
					}
				}
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
			$out .= "<tr><$td>".$a["date"]."</td><$td>$product_name</td><$td>$refund</td><$td>$gate</td><$td>$total</td><$td><a target=\"_blank\" href=\"/dtc/invoice.php?id=".$a["id"]."&download_pass=$adm_pass\">PDF</a></td></tr>";
		}
		$out .= "</table>";
	}else{
		$out .= _("No completed orders found.") ;
	}
	return $out;
}

?>
