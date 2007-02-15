<?php

function drawAdminTools_MyAccount($admin){
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $pro_mysql_pay_table;
	global $dtcshared_path;

	global $pro_mysql_ssl_ips_table;
	global $pro_mysql_product_table;

	global $cc_code_array;

	global $lang;

	global $txt_please_tell_if_info_not_ok;
	global $txt_refund_my_account;
	global $txt_remaining_money;
	global $txt_renew_my_account_button;
	global $txt_upgrade_my_account_button;
	global $txt_expiration_date;
	global $txt_allowed_data_transfer;
	global $txt_storage_space;
	global $txt_your_hosting_account;
	global $txt_total_disk_usage;
	global $txt_transfer_this_month;
	global $txt_transfer_du;
	global $txt_draw_client_info_familyname;
	global $txt_draw_client_info_firstname;
	global $txt_draw_client_info_comp_name;
	global $txt_draw_client_info_addr;
	global $txt_draw_client_info_zipcode;
	global $txt_draw_client_info_country;
	global $txt_draw_client_info_city;
	global $txt_draw_client_info_state;
	global $txt_draw_client_info_phone;
	global $txt_draw_client_info_fax;
	global $txt_draw_client_info_email;

	$frm_start = "<form action=\"$PHP_SELF\">
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
		if(isset($_REQUEST["inneraction"]) && $_REQUEST["return_from_paypal_refund_my_account"]){
			$ze_refund = isPayIDValidated(addslashes($_REQUEST["pay_id"]));
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
			$payid = createCreditCardPaiementID(addslashes($_REQUEST["refund_amount"]),$admin["info"]["id_client"],
				"Refund my account","no");
			$return_url = $_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass"
				."&addrlink=$addrlink&action=refund_myaccount&inneraction=return_from_paypal_refund_my_account&payid=$payid";
			$paybutton = paynowButton($payid,addslashes($_REQUEST["refund_amount"]),"Refund my account",$return_url);
			$out .= "<b><u>Pay \$".$_REQUEST["refund_amount"]." on my account:</u></b><br>";
			$out .=" Please click on the button below to pay your acount.<br><br>$paybutton";
			return $out;
		}
	}

	if($id_client != 0){
		$client = $admin["client"];
	}

	if(isset($admin["data"])){
		$out .= "<b><u>".$txt_transfer_du[$lang]."</u></b>";
		// Draw overall this month usage
		// if there is no usage, set to 0
		if (!isset($stats["total_transfer"]))
		{
			$stats["total_transfer"] = 0;
		}
		$overall = "<br>".$txt_transfer_this_month[$lang].smartByte($stats["total_transfer"]);
		if($id_client != 0){
			$bw_quota = $admin["info"]["bandwidth_per_month_mb"]*1024*1024;
			$overall .= " / ".smartByte($bw_quota)."<br>";
			$overall .= drawPercentBar($stats["total_transfer"],$bw_quota);
		}
		$overall .= $txt_total_disk_usage[$lang].smartByte($stats["total_du"]);
		if($id_client != 0 && isset($admin["data"])){
			$du_quota = $admin["info"]["quota"]*1024*1024;
			$overall .= " / ".smartByte($du_quota)."<br>";
			$overall .= drawPercentBar($stats["total_du"],$du_quota);
		}

		if($id_client != 0){
			$out .= '<table><td>'.$overall.'</td><td><img src="bw_per_month.php?cid='.$id_client.'&adm_login='.$adm_login.'"></td></tr></table>';
		}else{
			$out .= $overall;
		}
	}

	if($id_client != 0){

		// If the customer has domains (he could have only a VPS...).
		if(isset($admin["data"])){
			$out .= "<br><b><u>".$txt_your_hosting_account[$lang]."</u></b>";
			$out .= "<table width=\"100%\" height=\"1\" cellpadding=\"4\" cellspacing=\"0\" border=\"1\">
<tr>
	<td><b>".$txt_storage_space[$lang]."</b></td><td><b>".$txt_allowed_data_transfer[$lang]."</b></td><td><b>".$txt_expiration_date[$lang]."</b></td>
</tr>
<tr>
	<td>".smartByte($du_quota)."</td><td>".smartByte($bw_quota)."</td><td>".$admin["info"]["expire"]."</td>
</tr>
</table>";

			if(file_exists($dtcshared_path."/dtcrm")){
				$out .= "<br><center>$frm_start<input type=\"hidden\" name=\"action\" value=\"upgrade_myaccount\">
<input type=\"submit\" value=\"".$txt_upgrade_my_account_button[$lang]."\">
</form>";
				$out .= "<form action=\"/dtc/new_account.php\">
<input type=\"hidden\" name=\"action\" value=\"contract_renewal\">
<input type=\"hidden\" name=\"renew_type\" value=\"shared\">
<input type=\"hidden\" name=\"product_id\" value=\"".$admin["info"]["prod_id"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"client_id\" value=\"$id_client\">
<input type=\"submit\" value=\"".$txt_renew_my_account_button[$lang]."\">
</form></center><br>";
			}

			$out .= "<h3>SSL tokens</h3><br>";
			$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE adm_login='$adm_login' AND available='no';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n == 0){
				$out .= "You currently don't have any SSL tokens.<br><br>";
			}else{
				$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">";
				$out .= "<tr><td>IP address</td><td>Used by</td><td>Expire</td><td>Action</td></tr>";
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
//							echo "<pre>"; print_r($admin["data"][$j]["subdomains"][$k]); echo "</pre>";
						}
					}
					$q = "SELECT * FROM $pro_mysql_product_table WHERE heb_type='ssl';";
					$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$n = mysql_num_rows($r);
					if($n != 1){
						$ssl_renew_form = "No ssl product defined.";
					}else{
						$prod = mysql_fetch_array($r);
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
				$out .= "No SSL token available: contact your administrator to request it.<br><br>";
			}else{
				$q = "SELECT * FROM $pro_mysql_product_table WHERE heb_type='ssl';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1){
					$out .= "No ssl product defined.";
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

			$out .=  "<b><u>".$txt_remaining_money[$lang]."</u></b><br>
<table width=\"100%\" height=\"1\" cellpadding=\"4\" cellspacing=\"0\" border=\"0\">
<tr>
	<td><font size=\"+1\">\$".$client["dollar"]."</font></td>
	<td><font size=\"-1\">".$txt_refund_my_account[$lang]."</font><br>
$frm_start<input type=\"hidden\" name=\"action\" value=\"refund_myaccount\">
\$<input size=\"8\" type=\"text\" name=\"refund_amount\" value=\"\">
<input type=\"submit\" value=\"Ok\">
</form></td></tr>
</table>
<hr width=\"90%\">
";
		}


		$out .= "<center><b>".$txt_please_tell_if_info_not_ok[$lang]."</b></center>";

		if($client["is_company"] == "yes"){
			$out .= $txt_draw_client_info_comp_name[$lang].$client["company_name"]."<br>";
		}

		$out .= $txt_draw_client_info_firstname[$lang].$client["christname"]."<br>";
		$out .= $txt_draw_client_info_familyname[$lang].$client["familyname"]."<br>";
		$out .= $txt_draw_client_info_addr[$lang].$client["addr1"]."<br>";
		$out .= $client["addr2"]."<br>";
		$out .= $txt_draw_client_info_zipcode[$lang].$client["zipcode"]."<br>";
		$out .= $txt_draw_client_info_city[$lang].$client["city"]."<br>";
		$out .= $txt_draw_client_info_state[$lang].$client["state"]."<br>";
		$out .= $txt_draw_client_info_country[$lang].$cc_code_array[ $client["country"] ] ."<br>";
		$out .= $txt_draw_client_info_phone[$lang].$client["phone"]."<br>";
		$out .= $txt_draw_client_info_fax[$lang].$client["fax"]."<br>";
		$out .= $txt_draw_client_info_email[$lang].$client["email"]."<br>";
	}else{
		$out .= "You do not have a client account, so there
is no money in your account.";
	}
	return $out;

}


?>
