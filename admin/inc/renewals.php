<?php

function drawRenewalTables (){	
	global $pro_mysql_product_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_ssl_ips_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_ssl_ips_table;
	global $pro_mysql_client_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_completedorders_table;
	global $pro_mysql_pay_table;
	global $pro_mysql_spent_type_table;
	global $pro_mysql_spent_providers_table;
	global $pro_mysql_spent_moneyout_table;
	global $pro_mysql_companies_table;
	global $pro_mysql_spent_bank_table;

	global $secpayconf_currency_letters;
	global $rub;

	global $conf_vps_renewal_shutdown;

	get_secpay_conf();

	if(!isset($_REQUEST["sousrub"]) || $_REQUEST["sousrub"] == ""){
		$sousrub = "renewalreport";
	}else{
		$sousrub = $_REQUEST["sousrub"];
	}

	$out = '<ul class="box_wnb_content_nb">';
	if( $sousrub == "renewalreport"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("Renewal report") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("Renewal repport") ."</a></li>";
	}
	$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
	if($sousrub == "spent"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=spent\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Money spent") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=spent\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Money spent") ."</a></li>";
	}
	$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
	if($sousrub == "bank"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=bank\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Bank accounts & payments") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=bank\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Bank accounts & payments") ."</a></li>";
	}
	$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
	if($sousrub == "provideredit"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=provideredit\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_admineditor.gif\" align=\"absmiddle\" border=\"0\">". _("Upstream provider editor") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=provideredit\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_admineditor.gif\" align=\"absmiddle\" border=\"0\">". _("Upstream provider editor") ."</a></li>";
	}
	$out .= "</ul>";

	switch($sousrub){
	case "spent":
		$out .= "<h3>Date selection</h3>";

		if( !isset($_REQUEST["date_selector"])){
			// Check the last record to get the last entry by default.
			$q = "SELECT DISTINCT(CONCAT_WS('-',YEAR(invoice_date),MONTH(invoice_date))) FROM `spent_moneyout` WHERE 1 ORDER BY invoice_date DESC LIMIT 1";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 0){
				$a = mysql_fetch_array($r);
				$exploded = explode("-",$a[0]);
				$using_date = $exploded[0];
				if(strlen($exploded[1]) < 2){
					$using_date = $exploded[0] . "-0" . $exploded[1];
				}else{
					$using_date = $exploded[0] . "-" . $exploded[1];
				}
				$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=$rub&sousrub=$sousrub&date_selector=all\">all</a>";
				$date = $using_date;
				$where_condition = " invoice_date LIKE '$date%' ";
			}else{
				$out .= "all";
				$date = "all";
				$where_condition = " 1 ";
			}
		}else if( $_REQUEST["date_selector"] == "all"){
			$out .= "all";
			$date = "all";
			$where_condition = " 1 ";
		}else{
			$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=$rub&sousrub=$sousrub&date_selector=all\">all</a>";
			$date = $_REQUEST["date_selector"];
			$where_condition = " invoice_date LIKE '$date%' ";
		}

		$q = "SELECT DISTINCT(CONCAT_WS('-',YEAR(invoice_date),MONTH(invoice_date))) FROM `spent_moneyout` WHERE 1 ORDER BY invoice_date";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$exploded = explode("-",$a[0]);
			$using_date = $exploded[0];
			if(strlen($exploded[1]) < 2){
				$using_date = $exploded[0] . "-0" . $exploded[1];
			}else{
				$using_date = $exploded[0] . "-" . $exploded[1];
			}
			if($date != $using_date){
				$out .= " - <a href=\"".$_SERVER["PHP_SELF"]."?rub=$rub&sousrub=$sousrub&date_selector=".$using_date."\">".$using_date."</a>";
			}else{
				$out .= " - $using_date";
			}
		}

		$out .= "<br><br>";

		$q = "SELECT * FROM $pro_mysql_spent_providers_table ";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$prov_popup_id = array();
		$prov_popup_names = array();
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$prov_popup_id[] = $a["id"];
			$prov_popup_names[] = $a["quick_name"];
		}
		$q = "SELECT * FROM $pro_mysql_spent_type_table ";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$spent_type_popup_id = array();
		$spent_type_names = array();
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$spent_type_popup_id[] = $a["id"];
			$spent_type_names[] = $a["label"];
		}
		$q = "SELECT * FROM $pro_mysql_companies_table ";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$company_paying_popup_id = array();
		$company_paying_names = array();
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$company_paying_popup_id[] = $a["id"];
			$company_paying_names[] = $a["name"];
		}

		$q = "SELECT * FROM $pro_mysql_spent_bank_table ";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$bank_popup_id = array();
		$bank_names = array();
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$bank_popup_id[] = $a["id"];
			$bank_names[] = $a["acct_name"];
		}

		$dsc = array(
			"title" => _("List of payments done by your hosting company"),
			"table_name" => $pro_mysql_spent_moneyout_table,
			"action" => "money_out_editor",
			"forward" => array("rub","sousrub"),
			"print_where_condition" => $where_condition,
			"order_by" => "invoice_date",
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"label" => array(
					"type" => "text",
					"size" => "10",
					"legend" => _("Label") ),
				"id_company_spending" => array(
					"legend" => _("Company paying"),
					"type" => "popup",
					"values" => $company_paying_popup_id,
					"display_replace" => $company_paying_names),
				"id_provider" => array(
					"legend" => _("Company paid"),
					"type" => "popup",
					"values" => $prov_popup_id,
					"display_replace" => $prov_popup_names),
				"expenditure_type" => array(
					"legend" => _("Expenditure type"),
					"type" => "popup",
					"values" => $spent_type_popup_id,
					"display_replace" => $spent_type_names),
				"payment_type" => array(
					"legend" => _("Means of payment"),
					"type" => "popup",
					"values" => array("none", "credit_card", "wire_transfer", "paypal", "check", "cash"),
					"display_replace" => array( _("Unknown"), _("Credit card"), _("Wire transfer"),
									_("Paypal"), _("Check"), _("Cash") ) ),
				"payment_total" => array(
					"type" => "text",
					"size" => 6,
					"legend" => _("Total cost")),
				"vat_rate" => array(
					"type" => "text",
					"size" => 4,
					"legend" => _("Tax rate")),
				"vat_total" => array(
					"type" => "text",
					"size" => 4,
					"legend" => _("Total tax")),
				"currency_type" => array(
					"type" => "text",
					"size" => 4,
					"legend" => _("Currency")),
				"bank_acct_id" => array(
					"type" => "popup",
					"values" => $bank_popup_id,
					"display_replace" => $bank_names,
					"legend" => _("Bank account")),
				"amount" => array(
					"type" => "text",
					"size" => 6,
					"legend" => _("Bank amount")),
				"invoice_date" => array(
					"type" => "text",
					"size" => 10,
					"legend" => _("Invoice date")
					),
				"paid_date" => array(
					"type" => "text",
					"size" => 10,
					"legend" => _("Payment date")
					)
				)
			);
		if(isSet($_REQUEST["date_selector"])){
			$dsc["forward"][] = "date_selector";
		}
		$out .= dtcDatagrid($dsc);
		break;
	case "bank":
		$q = "SELECT * FROM $pro_mysql_companies_table ";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$company_paying_popup_id = array();
		$company_paying_names = array();
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$company_paying_popup_id[] = $a["id"];
			$company_paying_names[] = $a["name"];
		}

		$dsc = array(
			"title" => _("Bank accounts edition"),
			"table_name" => $pro_mysql_spent_bank_table,
			"action" => "bank_account_editor",
			"forward" => array("rub","sousrub"),
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"acct_name" => array(
					"type" => "text",
					"legend" => _("Account name")),
				"id_company" => array(
					"legend" => _("Company"),
					"type" => "popup",
					"values" => $company_paying_popup_id,
					"display_replace" => $company_paying_names),
				"sort_code" => array(
					"legend" => _("Sort code"),
					"type" => "text",
					"size" => "8"),
				"acct_number" => array(
					"legend" => _("Account number"),
					"type" => "text"),
				"swift" => array(
					"legend" => _("SWIFT"),
					"type" => "text",
					"size" => "8"),
				"bank_addr" => array(
					"legend" => _("Bank address"),
					"type" => "text"),
				"currency_type" => array(
					"legend" => _("Currency"),
					"type" => "text",
					"size" => "4")
				)
			);
		$out .= dtcDatagrid($dsc);

		// Payment type
		$dsc = array(
			"title" => _("Payment type edition"),
			"table_name" => $pro_mysql_spent_type_table,
			"action" => "payment_type_editor",
			"forward" => array("rub","sousrub"),
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"label" => array(
					"legend" => _("Type of payment"),
					"type" => "text",
					"size" => "32")
				)
			);
		$out .= dtcDatagrid($dsc);
		break;
	case "provideredit":
		$dsc = array(
			"title" => _("Upstream provider list edition"),
			"table_name" => $pro_mysql_spent_providers_table,
			"action" => "provider_list_editor",
			"forward" => array("rub","sousrub"),
			"id_fld" => "id",
			"list_fld_show" => "quick_name",
			"new_item_title" => "New upstream provider:",
			"new_item_link" => "New upsream provider",
			"edit_item_title" => "Edit upstream provider:",
			"check_unique" => array( "quick_name" ),
			"where_list" => array("always_yes" => "yes"),
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"quick_name" => array(
					"type" => "text",
					"disable_edit" => "yes",
					"check" => "dtc_login_or_email",
					"legend" => _("Short name:") ),
				"company_name" => array(
					"legend" => _("Company name:"),
					"type" => "text"),
				"is_company" => array(
					"type" => "checkbox",
					"values" => array( "yes","no"),
					"default" => "yes",
					"legend" => _("Is it a company:") ),
				"familyname" => array(
					"legend" => _("First name:"),
					"type" => "text"),
				"christname" => array(
					"legend" => _("Familly name:"),
					"type" => "text"),
				"addr1" => array(
					"legend" => _("Address:"),
					"type" => "text"),
				"addr2" => array(
					"legend" => _("Address (line2):"),
					"type" => "text"),
				"addr3" => array(
					"legend" => _("Address (line3):"),
					"type" => "text"),
				"city" => array(
					"legend" => _("City:"),
					"type" => "text"),
				"zipcode" => array(
					"legend" => _("Zipcode:"),
					"type" => "text"),
				"state" => array(
					"legend" => _("State:"),
					"type" => "text"),
				"country" => array(
					"legend" => _("Country:"),
					"type" => "text"),
				"phone" => array(
					"legend" => _("Phone:"),
					"type" => "text"),
				"fax" => array(
					"legend" => _("Fax:"),
					"type" => "text"),
				"email" => array(
					"legend" => _("Email:"),
					"type" => "text"),
				"special_note" => array(
					"legend" => _("Note:"),
					"type" => "textarea")
				)
			);
		$out .= dtcListItemsEdit($dsc);
		break;
	default:
	case "renewalreport":
		// Allow shutdown of expired VPS
		if(isset($_REQUEST["action"])){
			switch($_REQUEST["action"]){
			case "shutdown_expired_vps":
				// Perform a clean shutdown
				remoteVPSAction($_REQUEST["server_hostname"],$_REQUEST["vps_name"],"shutdown_vps");
				break;
			case "kill_vps_and_owner":
				// Do a brutal kill of the running instance
				deleteVPS($_REQUEST["vps_id"]);
				remoteVPSAction($_REQUEST["server_hostname"],$_REQUEST["vps_name"],"destroy_vps");
				remoteVPSAction($_REQUEST["server_hostname"],$_REQUEST["vps_name"],"kill_vps_disk");
				// Delete the admin
				$q = "DELETE FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["adm_login"]."';";
				$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				// And the client
				$q = "DELETE FROM $pro_mysql_client_table WHERE id='".$_REQUEST["client_id"]."';";
				$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				break;
			default:
				break;
			}
		}

		// Display of each month payment list
		if(isset($_REQUEST["date"])){
			// Allow nuke of bad payment (hackers?) to have accounting done correctly
			if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "nuke_payment"){
				$q = "DELETE FROM $pro_mysql_completedorders_table WHERE id='".$_REQUEST["completedorders_id"]."';";
				$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			}
			$out .= "<h3>"._("Payements for the period: ").$_REQUEST["date"]."</h3>";
			$q = "SELECT * FROM $pro_mysql_completedorders_table
			WHERE date LIKE '".$_REQUEST["date"]."%' ORDER BY date;";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$out .= _("No past payments for this period") ."<br>";
			}else{
				$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
				<tr><td>"._("Product")."</td><td>". _("Client ID") ."</td><td>". _("Client")."</td><td>". _("Service country")."</td>
				<td>"._("Client country")."</td>
				<td>". _("VAT collected")."</td><td>". _("Period")."</td><td>". _("Payment date")."</td><td>"._("Total")."</td>
				<td>". _("Action") ."</td></tr>";
				for($i=0;$i<$n;$i++){
					$a = mysql_fetch_array($r);
					if($a["id_client"] == 0){
						$client_name = _("No client id");
							$client_id_txt = _("No client id");
					}else{
						$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$a["id_client"]."';";
						$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
						$n2 = mysql_num_rows($r2);
						if($n2 != 1){
							$client_name = _("N/A");
							$client_id_txt = _("N/A");
							$client_country = _("N/A");
						}else{
							$a2 = mysql_fetch_array($r2);
							$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
							$client_id_txt = $a["id_client"];
							$client_country = $a2["country"];
						}
					}
					$q2 = "SELECT * FROM $pro_mysql_product_table WHERE id='".$a["product_id"]."';";
					$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$n2 = mysql_num_rows($r2);
					if($n2 != 1){
						$product_txt = _("Product not found");
					}else{
						$a2 = mysql_fetch_array($r2);
						$product_txt = $a2["name"];
						$product_period_size = $a2["period"];
					}
					$q2 = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$a["payment_id"]."';";
					$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$n2 = mysql_num_rows($r2);
					if($n2 != 1){
						$payment_txt = _("Payment not found");
						$vat_collected = _("VAT not found");
					}else{
						$a2 = mysql_fetch_array($r2);
						$payment_txt = $a2["paiement_total"]. " " . $a2["currency"];
						$vat_collected = $a2["paiement_total"] * $a2["vat_rate"] / 100 ;
					}
					if($a["last_expiry_date"] == "0000-00-00"){
						$last_expiry_date = $a["date"];
					}else{
						$last_expiry_date = $a["last_expiry_date"];
					}
					$new_expiry_date = calculateExpirationDate($last_expiry_date,$product_period_size);
					$out .= "<tr><td>$product_txt</td><td>$client_id_txt</td><td>$client_name</td><td>".$a["country_code"]."</td>
					<td>$client_country</td>
					<td>$vat_collected</td><td>$last_expiry_date -> $new_expiry_date</td><td>".$a["date"]."</td><td>$payment_txt</td>
					<td><a href=\"".$_SERVER["PHP_SELF"]."?rub=$rub&date=".$_REQUEST["date"]."&action=nuke_payment&completedorders_id=".$a["id"]."\">"._("Delete")."</a></tr>";
				}
				$out .= "</table>";
			}
			return $out;
		}

		// Calculation of recuring totals
		$out .= "<h3>". _("Total recurring incomes per month:") ."</h3>";
		// Monthly recurring for shared hosting:
		$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period
		FROM $pro_mysql_product_table,$pro_mysql_admin_table
		WHERE $pro_mysql_product_table.id = $pro_mysql_admin_table.prod_id
		AND $pro_mysql_product_table.heb_type='shared'
		AND $pro_mysql_admin_table.expire != '0000-00-00'";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$total_shared = 0;
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$period = $a["period"];
			$price = $a["price_dollar"];
			if($period == '0001-00-00'){
				$total_shared += $price / 12;
			}else{
				$papoum = explode('-',$period);
				$months = $papoum[1];
				$total_shared += $price / $months;
			}
		}

		// Calculate how much SSL IPs have been taken
		$q = "SELECT count(id) as num_ssl FROM $pro_mysql_ssl_ips_table WHERE available='no'";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$total_ssl = 0;
		if($n != 0){
			$a = mysql_fetch_array($r);
			$q = "SELECT price_dollar FROM $pro_mysql_product_table WHERE heb_type='ssl'";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 0){
				$b = mysql_fetch_array($r);
				$total_ssl = $a["num_ssl"] * $b["price_dollar"] / 12;
			}
		}

		// Monthly recurring for VPS:
		$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period
		FROM $pro_mysql_product_table,$pro_mysql_vps_table
		WHERE $pro_mysql_product_table.id = $pro_mysql_vps_table.product_id";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$total_vps = 0;
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$period = $a["period"];
			$price = $a["price_dollar"];
			if($period == '0001-00-00'){
				$total_shared += $price / 12;
			}else{
				$papoum = explode('-',$period);
				$months = $papoum[1];
				$total_vps += $price / $months;
			}
		}

		// Monthly recurring for dedicated servers:
		$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period
		FROM $pro_mysql_product_table,$pro_mysql_dedicated_table
		WHERE $pro_mysql_product_table.id = $pro_mysql_dedicated_table.product_id";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$total_dedicated = 0;
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$period = $a["period"];
			$price = $a["price_dollar"];
			if($period == '0001-00-00'){
				$total_shared += $price / 12;
			}else{
				$papoum = explode('-',$period);
				$months = $papoum[1];
				$total_dedicated += $price / $months;
			}
		}

		$p_renewal = "";
		$p_renewal .= _("Shared hosting: ") .round($total_shared,2)." $secpayconf_currency_letters<br>";
		$p_renewal .= _("SSL IPs renewals: ") .round($total_ssl,2)." $secpayconf_currency_letters<br>";
		$p_renewal .= _("VPS: ") .round($total_vps,2)." $secpayconf_currency_letters<br>";
		$p_renewal .= _("Dedicated servers: ") .round($total_dedicated,2)." $secpayconf_currency_letters<br>";
		$big_total = $total_shared + $total_vps + $total_dedicated + $total_ssl;
		$p_renewal .= "<b>". _("Total: ") .round($big_total,2)." $secpayconf_currency_letters</b>";

		// Show a quick history of payments
		$year = date("Y");
		$month = date("m");
		$cur_year = $year - 1;
		$cur_month = $month;
		$p_history = "";
		$p_history .= "<table cellspacing=\"1\" cellpadding=\"1\" border=\"1\">
		<tr><td>". _("Period") ."</td><td>". _("Amount") ."</td><td>"._("VAT collected")."</td><td>"._("Payment gateway cost")."</td><td>"._("Profit")."</td></tr>";
		for($i=0;$i<13;$i++){
			$q2 = "SELECT $pro_mysql_pay_table.paiement_total,$pro_mysql_pay_table.vat_rate,$pro_mysql_pay_table.paiement_cost
			FROM $pro_mysql_pay_table,$pro_mysql_completedorders_table
			WHERE $pro_mysql_pay_table.vat_rate!='0.00'
			AND $pro_mysql_completedorders_table.payment_id = $pro_mysql_pay_table.id
			AND $pro_mysql_completedorders_table.date LIKE '".$cur_year."-".$cur_month."-%';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			$vat_collected = 0;
			$month_total = 0;
			$cost_total = 0;
			for($j=0;$j<$n2;$j++){
				$a2 = mysql_fetch_array($r2);
				$tt = $a2["paiement_total"];
				$vat = $a2["vat_rate"];
				$vat_collected += $tt * $vat / 100;
				$month_total += $tt;
				$cost_total += $a2["paiement_cost"];
			}

			$q2 = "SELECT sum(paiement_total) as paiement_total, sum(paiement_cost) as paiement_cost FROM $pro_mysql_completedorders_table,$pro_mysql_pay_table
			WHERE $pro_mysql_completedorders_table.date LIKE '".$cur_year."-".$cur_month."%'
			AND $pro_mysql_completedorders_table.payment_id = $pro_mysql_pay_table.id
			AND $pro_mysql_pay_table.vat_rate = '0.00';";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 > 0){
				$a2 = mysql_fetch_array($r2);
				$cost_total += $a2["paiement_cost"];
				$month_total += $a2["paiement_total"];
				$profit = $month_total - $cost_total - $vat_collected;
				$p_history .= "<tr><td style=\"text-align:right;\"><a href=\"".$_SERVER["PHP_SELF"]."?rub=$rub&date=".$cur_year."-".$cur_month."\">".$cur_year."-".$cur_month."</a></td>
				<td style=\"text-align:right;\">".$month_total." $secpayconf_currency_letters</td>
				<td style=\"text-align:right;\">".round($vat_collected,2)." $secpayconf_currency_letters</td>
				<td style=\"text-align:right;\">".round($cost_total,2)." $secpayconf_currency_letters</td>
				<td style=\"text-align:right;\">".round($profit,2)." $secpayconf_currency_letters</td></tr>";
			}
			$cur_month++;
			if($cur_month > 12){
				$cur_month = 1;
				$cur_year++;
			}
			if($cur_month < 10)	$cur_month = "0".$cur_month;
		}
		$p_history .= "</table>";


		// Layout the recuring stat and the effective payment statistics
		$out .= "<table cellspacing=\"1\" cellpadding=\"4\" border=\"0\">
		<tr><td>$p_history</td>
		<td valign=\"top\">$p_renewal</td></tr></table>";

		$out .= "<h3>". _("Shared hosting renewals:") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE expire < '".date("Y-m-d")."' AND id_client!='0' AND expire !='0000-00-00' ORDER BY expire;";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__);
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No shared account expired.") ."<br>";
		}else{
			$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
			<tr><td>". _("Login") ."</td><td>". _("Client") ."</td><td>". _("Email") ."</td><td>". _("Expiration date") ."</td></tr>";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$a["id_client"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$client_name = _("Client name not found!");
				}else{
					$a2 = mysql_fetch_array($r2);
					$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
				}
				$q2 = "SELECT * FROM $pro_mysql_domain_table WHERE owner='".$a["adm_login"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__);
				$n2 = mysql_num_rows($r2);
				if($n2 > 0){
					$out .= "<tr><td>".$a["adm_login"]."</td><td>$client_name</td><td>".$a2["email"]."</td><td>".$a["expire"]."</td></tr>";
				}
			}
			$out .= "</table>";
		}

		// List of expired expired SSL IPs
		$out .= "<h3>". _("SSL IPs renewals") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE expire < '".date("Y-m-d")."' AND available='no' ORDER BY expire";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No SSL IP expired") ."<br>";
		}else{
			$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
			<tr><td>". _("Login") ."</td><td>". _("Client") ."</td><td>". _("Email") ."</td><td>". _("Expiration date") ."</td></tr>";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["adm_login"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					die("Cannot find admin name ".$a["adm_login"]." line ".__LINE__." file ".__FILE__);
				}else{
					$admin = mysql_fetch_array($r2);
				}
				$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$client_name = _("Client name not found!");
				}else{
					$a2 = mysql_fetch_array($r2);
					$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
				}
				$out .= "<tr><td>".$a["adm_login"]."</td><td>$client_name</td><td>".$a2["email"]."</td><td>".$a["expire"]."</td></tr>";
			}
			$out .= "</table>";
		}

		// List if expired VPS
		$out .= "<h3>". _("VPS renewals:") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_vps_table WHERE expire_date < '".date("Y-m-d")."' ORDER BY expire_date";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No VPS expired") ."<br>";
		}else{
			$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
			<tr><td>"._("Login")."</td><td>". _("VPS") ."</td>
			<td>". _("Client") ."</td>
			<td>". _("Email") ."</td>
			<td>". _("Expiration date") ."</td>
			<td>". _("Days of expiration") ."</td>
			<td>". _("Action") ."</td>
			</tr>";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);

				$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["owner"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					die("Cannot find admin name ".$a["owner"]." line ".__LINE__." file ".__FILE__);
				}else{
					$admin = mysql_fetch_array($r2);
				}
				$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$client_name = _("Client name not found!");
				}else{
					$a2 = mysql_fetch_array($r2);
					$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
				}
				$q2 = "SELECT adm_login FROM $pro_mysql_admin_table WHERE id_client='".$admin["id_client"]."'";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 == 1){
					$q2 = "SELECT * FROM $pro_mysql_vps_table WHERE owner='".$admin["adm_login"]."'";
					$r2 = mysql_query($q2)or die("Cannot querry ".$q2." line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$n2 = mysql_num_rows($r2);
					if($n2 == 1){
						$q2 = "SELECT * FROM $pro_mysql_dedicated_table WHERE owner='".$admin["adm_login"]."'";
						$r2 = mysql_query($q2)or die("Cannot querry ".$q2." line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
						$n2 = mysql_num_rows($r2);
						if($n2 == 0){
							$q2 = "SELECT * FROM $pro_mysql_domain_table WHERE owner='".$admin["adm_login"]."'";
							$r2 = mysql_query($q2)or die("Cannot querry ".$q2." line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
							$n2 = mysql_num_rows($r2);
							if($n2 == 0){
								$kill_owner_txt = "<a href=\"".$_SERVER["PHP_SELF"]."?action=kill_vps_and_owner&adm_login=".$admin["adm_login"]."&client_id=".$admin["id_client"]."&vps_name=".$a["vps_xen_name"]."&server_hostname=".$a["vps_server_hostname"]."&vps_id=".$a["id"]."\">"._("Kill VPS and owner")."</a>";
							}else{
								$kill_owner_txt = _("Has some domains");
							}
						}else{
							$kill_owner_txt = _("Has a dedicated");
						}
					}else{
						$kill_owner_txt = _("More than one VPS");
					}
				}else{
					$kill_owner_txt = _("More than one login");
				}
				if( numOfDays($a["expire_date"]) >= $conf_vps_renewal_shutdown){
					$bgcolor = " bgcolor=\"#FF8888\" ";
				}else{
					$bgcolor = " ";
				}
				$out .= "<tr><td>".$a["owner"]."</td>
				<td>".$a["vps_xen_name"].":".$a["vps_server_hostname"]."</td>
				<td>$client_name</td>
				<td>".$a2["email"]."</td>
				<td $bgcolor>".$a["expire_date"]."</td>
				<td $bgcolor>". calculateAge($a["expire_date"],"00:00:00") ."</td>
				<td><a href=\"".$_SERVER["PHP_SELF"]."?rub=$rub&action=shutdown_expired_vps&server_hostname=".$a["vps_server_hostname"]."&vps_name=".$a["vps_xen_name"]."\">"._("Shutdown")."</a> - $kill_owner_txt</td></tr>";
			}
			$out .= "</table>";
		}

		// List expired dedicated servers
		$out .= "<h3>". _("Dedicated servers renewals") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE expire_date < '".date("Y-m-d")."' ORDER BY expire_date";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No dedicated server expired") ."<br>";
		}else{
			$out .= "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
			<tr><td>". _("Login") ."</td><td>". _("Server") ."</td><td>". _("Client") ."</td><td>". _("Email") ."</td><td>". _("Expiration date") ."</td></tr>";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["owner"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					die("Cannot find admin name ".$a["owner"]." line ".__LINE__." file ".__FILE__);
				}else{
					$admin = mysql_fetch_array($r2);
				}
				$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$client_name = _("Client name not found!");
				}else{
					$a2 = mysql_fetch_array($r2);
					$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
				}
				$out .= "<tr><td>".$a["owner"]."</td><td>".$a["server_hostname"]."</td><td>$client_name</td><td>".$a2["email"]."</td><td>".$a["expire_date"]."</td></tr>";
			}
			$out .= "</table>";
		}
		break;
	}
	return $out;
}

?>
