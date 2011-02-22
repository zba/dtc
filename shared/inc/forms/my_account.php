<?php

function drawAdminTools_MyAccount($admin){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $dtcshared_path;

	global $pro_mysql_pay_table;
	global $pro_mysql_client_table;
	global $pro_mysql_ssl_ips_table;
	global $pro_mysql_product_table;
	global $pro_mysql_pending_renewal_table;
	global $secpayconf_currency_letters;

	global $cc_code_array;

	get_secpay_conf();

	$frm_start = "<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
";

	$out = "";

	$id_client = $admin["info"]["id_client"];

	$stats = fetchAdminStats($admin);

	if(isset($_REQUEST["action"]) && $id_client != 0 && $_REQUEST["action"] == "upgrade_myaccount"){
		return draw_UpgradeAccount($admin);
	}

	if(isset($_REQUEST["action"]) && $id_client != 0 && $_REQUEST["action"] == "refund_myaccount"){
		if(isset($_REQUEST["inneraction"]) && $_REQUEST["inneraction"] == "return_from_paypal_refund_my_account"){
			$ze_refund = isPayIDValidated(mysql_real_escape_string($_REQUEST["payid"]));
			if($ze_refund == 0){
				$out .= "<font color=\"red\">The transaction failed, please try again!</font>";
				return $out;
			}else{
				$out .= "<font color=\"green\">Funds added to your account!</font><br><br>";
				$q = "UPDATE $pro_mysql_client_table SET dollar = dollar+".$ze_refund." WHERE id='".$admin["info"]["id_client"]."';";
				$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
				$admin["client"]["dollar"] += $ze_refund;
				$out .= "Your account now has \$".$admin["client"]["dollar"];
				return $out;
			}
		}else{
			// Save the values in SQL and process the paynow buttons
			$q = "INSERT INTO $pro_mysql_pending_renewal_table (id,adm_login,renew_date,renew_time,product_id,renew_id,heb_type,country_code)
			VALUES ('','".$_REQUEST["adm_login"]."',now(),now(),'".$ro["id"]."','".$rocli["id"]."','shared-upgrade','$country');";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$renew_id = mysql_insert_id();

			$payid = createCreditCardPaiementID(mysql_real_escape_string($_REQUEST["refund_amount"]),$admin["info"]["id_client"],
				"Refund my account","no",$prod_id,$vat_rate);

			$q = "UPDATE $pro_mysql_pending_renewal_table SET pay_id='$payid' WHERE id='$renew_id';";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());

			$return_url = htmlentities($_SERVER["PHP_SELF"])."?adm_login=$adm_login&adm_pass=$adm_pass"
				."&addrlink=$addrlink&action=refund_myaccount&inneraction=return_from_paypal_refund_my_account&payid=$payid";
			$paybutton = paynowButton($payid,mysql_real_escape_string($_REQUEST["refund_amount"]),"Refund my account",$return_url);
			$out .= "<b><u>Pay \$".$_REQUEST["refund_amount"]." on my account:</u></b><br>";
			$out .=" Please click on the button below to pay your acount.<br><br>$paybutton";
			return $out;
		}
	}

	if($id_client != 0){
		$client = $admin["client"];
	}

	if(isset($admin["data"])){
		$out .= "<br><h3>". _("Transfer and disk usage:") ."</h3>";
		// Draw overall this month usage
		// if there is no usage, set to 0
		if (!isset($stats["total_transfer"]))
		{
			$stats["total_transfer"] = 0;
		}
		$overall = "<br>". _("Transfer this month:") .smartByte($stats["total_transfer"]);
		if($id_client != 0){
			$bw_quota = $admin["info"]["bandwidth_per_month_mb"]*1024*1024;
			$overall .= " / ".smartByte($bw_quota)."<br>";
			$overall .= drawPercentBar($stats["total_transfer"],$bw_quota);
		}
		$overall .= "<br>" . _("Total disk usage:").smartByte($stats["total_du"]);
		if($id_client != 0 && isset($admin["data"])){
			$du_quota = $admin["info"]["quota"]*1024*1024;
			$overall .= " / ".smartByte($du_quota)."<br>";
			$overall .= drawPercentBar($stats["total_du"],$du_quota);
		}

		if($id_client != 0){
			$out .= '<table><td>'.$overall.'</td><td><img src="bw_per_month.php?cid='.$id_client.'&adm_login='.$adm_login.'&adm_pass='.$adm_pass.'"></td></tr></table>';
		}else{
			$out .= $overall;
		}
	}

	$out .= "<h3>" . _("Export configuration:") . "</h3>";
	$out .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&action=export_my_account&addrlink=".$_REQUEST["addrlink"]."\" target=\"_blank\">". _("Export all my domains configuration to a file") ."</a>";

	if($id_client != 0){

		// If the customer has domains (he could have only a VPS...).
		if(isset($admin["data"])){
			$out .= "<br><h3>". _("Your hosting account:") ."</h3>";
			$out .= "<table width=\"100%\" height=\"1\" cellpadding=\"4\" cellspacing=\"0\" border=\"1\">
<tr>
	<td><b>". _("Storage space") ."</b></td><td><b>". _("Allowed bandwidth per month") ."</b></td><td><b>". _("Expiry date") ."</b></td>
</tr>
<tr>
	<td>".smartByte($du_quota)."</td><td>".smartByte($bw_quota)."</td><td>".$admin["info"]["expire"]."</td>
</tr>
</table>";

			if(file_exists($dtcshared_path."/dtcrm")){
				$out .= "<br><center>$frm_start<input type=\"hidden\" name=\"action\" value=\"upgrade_myaccount\">
<input type=\"submit\" value=\"". _("Upgrade my account") ."\">
</form>";
				$out .= "<form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
<input type=\"hidden\" name=\"renew_type\" value=\"shared\">
<input type=\"hidden\" name=\"product_id\" value=\"".$admin["info"]["prod_id"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"client_id\" value=\"$id_client\">
<input type=\"submit\" value=\"". _("Renew my account") ."\">
</form></center><br>";
			}

			$out .= "<h3>". _("SSL tokens") ."</h3><br>";
			$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE adm_login='$adm_login' AND available='no';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n == 0){
				$out .= _("You currently don't have any SSL tokens.") ."<br><br>";
			}else{
				$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">";
				$out .= "<tr><td>". _("IP address") ."</td><td>". _("Used by") ."</td><td>". _("Expire"). "</td><td>". _("Action") ."</td></tr>";
				for($i=0;$i<$n;$i++){
					$a = mysql_fetch_array($r);
					$nbr_domains = sizeof($admin["data"]);
					$used_by = "Not used";
					for($j=0;$j<$nbr_domains;$j++){
						$nbr_subdomains = sizeof($admin["data"][$j]["subdomains"]);
						for($k=0;$k<$nbr_subdomains;$k++){
							if($admin["data"][$j]["subdomains"][$k]["ssl_ip"] == $a["ip_addr"]){
								$used_by = $admin["data"][$j]["subdomains"][$k]["name"].".".$admin["data"][$j]["name"];
							}
						}
					}
					$prodq = "SELECT * FROM $pro_mysql_product_table WHERE heb_type='ssl';";
					$prodr = mysql_query($prodq)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$prodn = mysql_num_rows($prodr);
					if($prodn != 1){
						$ssl_renew_form = _("No ssl product defined.") ;
					}else{
						$prod = mysql_fetch_array($prodr);
						$ssl_renew_form = "<form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
<input type=\"hidden\" name=\"renew_type\" value=\"ssl_renew\">
<input type=\"hidden\" name=\"ssl_ip_id\" value=\"".$a["id"]."\">
<input type=\"hidden\" name=\"product_id\" value=\"".$prod["id"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"client_id\" value=\"$id_client\">
<input type=\"submit\" value=\"Renew SSL IP\"></form>";
					}

					$out .= "<tr><td>".$a["ip_addr"]."</td><td>$used_by</td><td>".$a["expire"]."</td><td>$ssl_renew_form</td></tr>";
				}
				$out .= "</table><br><br>";
			}
			$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE available='yes';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n == 0){
				$out .= _("No SSL token available: contact your administrator to request it.") ."<br><br>";
			}else{
				$q = "SELECT * FROM $pro_mysql_product_table WHERE heb_type='ssl';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1){
					$out .= _("No ssl product defined.") ;
				}else{
					$prod = mysql_fetch_array($r);
					$out .= "<form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
<input type=\"hidden\" name=\"renew_type\" value=\"ssl\">
<input type=\"hidden\" name=\"product_id\" value=\"".$prod["id"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"client_id\" value=\"$id_client\">
<input type=\"submit\" value=\"Buy an SSL IP\">
</form></center><br>";
				}
			}

			$out .=  "<h3>". _("Remaining money on my account:") ."</h3>";
			$out .= dtcFormTableAttrs();
			$out .= dtcFormLineDraw( _("Money remaining: "), $client["dollar"]." $secpayconf_currency_letters",1);
			$out .= dtcFormLineDraw( _("Refund my account:"), "$frm_start<input type=\"hidden\" name=\"action\" value=\"refund_myaccount\">
<input size=\"8\" type=\"text\" name=\"refund_amount\" value=\"\"> $secpayconf_currency_letters",0);
			$out .= dtcFormLineDraw( "", submitButtonStart()._("Add money").submitButtonEnd()."</form>",1);
			$out .= "</table>";

		}


		$out .= "<h3>". _("Your address (please alert us if the following is incorrect):") ."</h3>";

		if($client["is_company"] == "yes"){
			$out .= _("Company name:") .$client["company_name"]."<br>";
			$out .= _("VAT / GST number:") .$client["vat_num"]."<br>";
		}

		$out .= _("First name:")	.$client["christname"]."<br>";
		$out .= _("Last name:")	.$client["familyname"]."<br>";
		$out .= _("Address:")		.$client["addr1"]."<br>";
		$out .= $client["addr2"]."<br>";
		$out .= _("Zipcode:")		.$client["zipcode"]."<br>";
		$out .= _("City:")		.$client["city"]."<br>";
		$out .= _("State:")		.$client["state"]."<br>";
		$out .= _("Country:")		.$cc_code_array[ $client["country"] ] ."<br>";
		$out .= _("Phone number:")	.$client["phone"]."<br>";
		$out .= _("Fax:")		.$client["fax"]."<br>";
		$out .= _("Email:")		.$client["email"]."<br>";

		$sql = "SELECT SUM(kickback) as kickbacks FROM affiliate_payments WHERE adm_login = '{$adm_login}' and date_paid IS NULL; ";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$afftotal = $row["kickbacks"];

		if ($afftotal) {

			global $pro_mysql_completedorders_table;

			$sql = "SELECT * FROM affiliate_payments INNER JOIN $pro_mysql_completedorders_table on (affiliate_payments.order_id = $pro_mysql_completedorders_table.id) WHERE adm_login = '{$adm_login}' and date_paid IS NULL; ";
			$result = mysql_query($sql);
			$out .= "". _("Outstanding payments:")."<br><table><tr><th>"._("Date")."</th><th>"._("Amount")."</th></tr>";
			while ($row = mysql_fetch_array($result)) $out .= "<tr><td>{$row['date']}</td><td>{$row['kickback']}</td></tr>";
			$out .= "<tr><td></td><th>{$afftotal}</th></tr>";
			$out .= "</table>";

		}

		$out .= "<h3>"._("Affiliation")."</h3>";
		$out .= _("If you want to earn money, all you have to do is place a link on your site, pointing to:").
		"<pre>https://{$_SERVER['SERVER_NAME']}/dtc/affiliation.php?affiliate={$adm_login}&amp;return=/hosting-vps.html</pre>"
		._("You can customize the <code>return</code> variable to redirect the user to any particular landing page that exists on our Web site (though we recommend the product page as per the example).  Then, when one of your visitors clicks on that link to buy a product from us, he will be redirected to our Web site.  Once he buys, you will automatically be credited a payment depending on the product that your visitor bought.");

	}else{
		$out .= "<br>" . _("You do not have a client account, so there is no money in your account.");
	}
	return $out;

}


?>
