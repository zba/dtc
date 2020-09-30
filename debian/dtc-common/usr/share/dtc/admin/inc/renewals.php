<?php

// Returns an array as follow:
// $ret["text"] html code to display
//     ["where_condition"] the SQL filter to user in your later queries.
function dateSelector($table,$mysql_date_field,$http_query_field){
	global $rub;
	global $sousrub;
	$out = array();
	$out["text"] = "<h3>"._("Date selection")."</h3>";

	if( !isset($_REQUEST[$http_query_field])){
		$q = "SELECT DISTINCT(CONCAT_WS('-',YEAR($mysql_date_field),MONTH($mysql_date_field))) FROM `$table` WHERE 1 ORDER BY $mysql_date_field DESC LIMIT 1";
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
			$out["text"] .= "<a href=\"?rub=$rub&sousrub=$sousrub&$http_query_field=all\">" . _("all") . "</a>";
			$date = $using_date;
			$where_condition = " $mysql_date_field LIKE '$date%' ";
		}else{
			$out["text"] .= _("all");
			$date = "all";
			$where_condition = " 1 ";
		}
	}else if( $_REQUEST[$http_query_field] == "all"){
		$out["text"] .= _("all");
		$date = "all";
		$where_condition = " 1 ";
	}else{
		$out["text"] .= "<a href=\"?rub=$rub&sousrub=$sousrub&$http_query_field=all\">" . _("all") . "</a>";
		$date = $_REQUEST["$http_query_field"];
		$where_condition = " $mysql_date_field LIKE '$date%' ";
	}

	$q = "SELECT DISTINCT(CONCAT_WS('-',YEAR($mysql_date_field),MONTH($mysql_date_field))) FROM `$table` WHERE 1 ORDER BY $mysql_date_field ";
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
			$out["text"] .= " - <a href=\"?rub=$rub&sousrub=$sousrub&$http_query_field=".$using_date."\">".$using_date."</a>";
		}else{
			$out["text"] .= " - $using_date";
		}
	}
	$out["text"] .= "<br><br>";

	$out["date"] = $date;
	$out["where_condition"] = $where_condition;

	return $out;
}


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
	global $pro_mysql_client_table;
	global $pro_mysql_new_admin_table;

	global $secpayconf_currency_letters;
	global $rub;

	global $conf_vps_renewal_shutdown;

	get_secpay_conf();

	if(!isset($_REQUEST["sousrub"]) || $_REQUEST["sousrub"] == ""){
		$sousrub = "renewalreport";
	}else{
		$sousrub = $_REQUEST["sousrub"];
	}

	if( isset($_REQUEST["action"]) && $_REQUEST["action"] == "export"){
		include("inc/transaction_export.php");
	}

	$out = '<ul class="box_wnb_content_nb">';
	if( $sousrub == "renewalreport"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("Renewal Report") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("Renewal Report") ."</a></li>";
	}
	$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
	if($sousrub == "spent"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=spent\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Money Spent") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=spent\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Money Spent") ."</a></li>";
	}
	$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
	if($sousrub == "bank"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=bank\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Bank Accounts and Payments") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=bank\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-payementgateway.gif\" align=\"absmiddle\" border=\"0\">". _("Bank Accounts and Payments") ."</a></li>";
	}
	$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
	if($sousrub == "provideredit"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=provideredit\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_admineditor.gif\" align=\"absmiddle\" border=\"0\">". _("Upstream Provider Editor") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=provideredit\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_admineditor.gif\" align=\"absmiddle\" border=\"0\">". _("Upstream Provider Editor") ."</a></li>";
	}
	$out .= "</ul>";



	switch($sousrub){
	case "spent":
		$ret = dateSelector("spent_moneyout","invoice_date","date_selector");
		$out .= $ret["text"];
		$where_condition = $ret["where_condition"];

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
			"title" => _("List of your hosting company's outgoing payments"),
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
					"legend" => _("Company Paying"),
					"type" => "popup",
					"values" => $company_paying_popup_id,
					"display_replace" => $company_paying_names),
				"id_provider" => array(
					"legend" => _("Company Paid"),
					"type" => "popup",
					"values" => $prov_popup_id,
					"display_replace" => $prov_popup_names),
				"expenditure_type" => array(
					"legend" => _("Expenditure Type"),
					"type" => "popup",
					"values" => $spent_type_popup_id,
					"display_replace" => $spent_type_names),
				"payment_type" => array(
					"legend" => _("Payment Method"),
					"type" => "popup",
					"values" => array("none", "credit_card", "wire_transfer", "paypal", "check", "cash"),
					"display_replace" => array( _("Unknown"), _("Credit cCrd"), _("Wire Transfer"),
									_("Paypal"), _("Check"), _("Cash") ) ),
				"payment_total" => array(
					"type" => "text",
					"size" => 6,
					"legend" => _("Total Cost")),
				"vat_rate" => array(
					"type" => "text",
					"size" => 4,
					"legend" => _("Tax Rate")),
				"vat_total" => array(
					"type" => "text",
					"size" => 4,
					"legend" => _("Total Tax")),
				"currency_type" => array(
					"type" => "text",
					"size" => 4,
					"legend" => _("Currency")),
				"bank_acct_id" => array(
					"type" => "popup",
					"values" => $bank_popup_id,
					"display_replace" => $bank_names,
					"legend" => _("Bank Account")),
				"amount" => array(
					"type" => "text",
					"size" => 6,
					"legend" => _("Bank Amount")),
				"invoice_date" => array(
					"type" => "text",
					"size" => 10,
					"legend" => _("Invoice Date")
					),
				"paid_date" => array(
					"type" => "text",
					"size" => 10,
					"legend" => _("Payment Date")
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
			"title" => _("View/Add Bank Accounts"),
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
					"legend" => _("Account Name")),
				"id_company" => array(
					"legend" => _("Company"),
					"type" => "popup",
					"values" => $company_paying_popup_id,
					"display_replace" => $company_paying_names),
				"sort_code" => array(
					"legend" => _("Sort Code"),
					"type" => "text",
					"size" => "8"),
				"acct_number" => array(
					"legend" => _("Account Number"),
					"type" => "text"),
				"swift" => array(
					"legend" => _("SWIFT Code"),
					"type" => "text",
					"size" => "8"),
				"bank_addr" => array(
					"legend" => _("Bank Address"),
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
					"legend" => _("Type of Payment"),
					"type" => "text",
					"size" => "32")
				)
			);
		$out .= dtcDatagrid($dsc);
		break;
	case "provideredit":
		$dsc = array(
			"title" => _("View/Add Upstream Providers"),
			"table_name" => $pro_mysql_spent_providers_table,
			"action" => "provider_list_editor",
			"forward" => array("rub","sousrub"),
			"id_fld" => "id",
			"list_fld_show" => "quick_name",
			"new_item_title" => _("New Upstream Provider").":",
			"new_item_link" => _("New upsream Provider"),
			"edit_item_title" => _("Edit Upstream Provider").":",
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
					"legend" => _("Short Name:") ),
				"company_name" => array(
					"legend" => _("Company Name:"),
					"type" => "text"),
				"is_company" => array(
					"type" => "checkbox",
					"values" => array( "Yes","No"),
					"default" => "yes",
					"legend" => _("Company Account:") ),
				"familyname" => array(
					"legend" => _("Contact First Name:"),
					"type" => "text"),
				"christname" => array(
					"legend" => _("Contact Last Name:"),
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
		if(isset($_REQUEST["display_date"])){
			$ret = dateSelector($pro_mysql_pay_table,"date","display_date");
			$out .= $ret["text"];
			$where_condition = $ret["where_condition"];

			$q = "SELECT id,name FROM $pro_mysql_product_table ";
			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			$prod_ids = array();
			$prod_names = array();
			$prod_ids[] = 0;
			$prod_names[] = _("Multiple renewals");
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$prod_ids[] = $a["id"];
				$prod_names[] = $a ["name"];
			}

			$out .= "<h3>"._("Payments for the Period: ").$_REQUEST["display_date"]."</h3>";
			$out .= _("Display not validated transactions:")." ";
			if( !isset($_REQUEST["display_notvalid"]) ){
				$out .= "<a href=\"?rub=renewal&display_date=".$_REQUEST["display_date"]."&display_notvalid=yes\">"._("Yes")."</a> ";
				$out .= _("No");
				$where_condition .= " AND valid='yes'";
				$forward = array("rub","display_date");
			}else{
				$out .= _("Yes")." ";
				$out .= "<a href=\"?rub=renewal&display_date=".$_REQUEST["display_date"]."\">"._("No")."</a>";
				$forward = array("rub","display_date","display_notvalid");
			}
			$out .= "<br><br>";

			$dsc = array(
				"title" => _("Payment History"),
				"table_name" => $pro_mysql_pay_table,
				"action" => "payment_history_list_editor",
				"forward" => $forward,
				"order_by" => "date",
				"skip_deletion" => "yes",
				"skip_creation" => "yes",
				"print_where_condition" => $where_condition,
				"cols" => array(
					"id" => array(
						"type" => "id",
						"display" => "no",
						"legend" => "id"),
					"date" => array(
						"type" => "text",
						"size" => "8",
						"legend" => _("Date")),
					"id_client" => array(
						"type" => "double_forkey",
						"forkey_type" => "info",

						// payment_table ["id"] gives us a key to search in completed oders
						// for the client_id. Then we use that client_id to find the customer infos.
						"table_1st_ind" => $pro_mysql_completedorders_table,
						"fldwhere_1st_ind_orig" => "id",
						"fldwhere_1st_ind" => "payment_id",
						"searchkey_1st_ind" => "id_client",
						"table_2nd_ind" => $pro_mysql_client_table,
						"display_flds_2nd_ind" => "CONCAT(company_name,': ',familyname,', ',christname)",
						"link_start" => "?rub=crm&id=",

						// If not found, we fall back to search in the new_admin table
						"table_back" => $pro_mysql_new_admin_table,
						"fldwhere_back_orig" => "id_client",
						"fldwhere_back" => "id",
						"display_flds_back" => "CONCAT(comp_name,': ',family_name,', ',first_name)",
						"link_start" => "view_waitingusers.php?reqadm_id=",

						"legend" => _("Customer Name")),
					/*"id_client" => array(
						"type" => "forkey",
						"forkey_type" => "info",
						"table" => $pro_mysql_client_table,
						"other_table_fld" => "CONCAT(company_name,': ',familyname,', ',christname)",
						"other_table_key" => "id",
						"this_table_field" => "id_client",
						"link" => "?rub=crm&id=",

						"bk_table" => $pro_mysql_new_admin_table,
						"bk_other_table_fld" => "CONCAT(comp_name,': ',family_name,', ',first_name)",
						"bk_other_table_key" => "id",
						"bk_this_table_field" => "id_client",

						"legend" => _("Customer name")),*/
					"product_id" => array(
						"type" => "popup",
						"values" => $prod_ids,
						"display_replace" => $prod_names,
						"legend" => _("Product")),
					"refund_amount" => array(
						"type" => "text",
						"size" => "8",
						"legend" => _("Refund Amount")),
					"paiement_cost" => array(
						"type" => "text",
						"size" => "4",
						"legend" => _("Gateway Cost")),
					"vat_rate" => array(
						"type" => "text",
						"size" => "4",
						"legend" => _("VAT Rate")),
					"vat_total" => array(
						"type" => "text",
						"size" => "4",
						"legend" => _("VAT Total")),
					 "paiement_total" => array(
						"type" => "text",
						"size" => "6",
						"legend" => _("Grand Total")),
					"paiement_type" => array(
						"type" => "text",
						"size" => "6",
						"legend" => _("Type")),
					"secpay_site" => array(
						"type" => "text",
						"size" => "4",
						"legend" => _("Gateway Type")),
					"new_account" => array(
						"type" => "popup",
						"values" => array("no","yes"),
						"display_replace" => array( _("New Account"), _("Renewal") ),
						"legend" => _("Is Renewal")),
					"valid" => array(
						"type" => "popup",
						"values" => array("no","pending","yes"),
						"display_replace" => array( _("No"), _("Pending"), _("Yes") ),
						"legend" => _("Validated")),
					"pending_reason" => array(
						"type" => "text",
						"size" => "6",
						"legend" => _("Pending Reason"))
				));
			$out .= dtcDatagrid($dsc);
			return $out;

			$ret = dateSelector($pro_mysql_completedorders_table,"date","date");
			// Allow nuke of bad payment (hackers?) to have accounting done correctly
			if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "nuke_payment"){
				$q = "DELETE FROM $pro_mysql_completedorders_table WHERE id='".$_REQUEST["completedorders_id"]."';";
				$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			}

			$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$out .= _("No past payments for this period") ."<br>";
			}else{
				$out .= dtcFormTableAttrs()."
				<tr><td>"._("Product")."</td><td>". _("Client ID") ."</td><td>". _("Client")."</td><td>". _("Service Country")."</td>
				<td>"._("Client Country")."</td>
				<td>". _("VAT Collected")."</td><td>". _("Period")."</td><td>". _("Payment Date")."</td><td>"._("Total")."</td><td>". _("Payment Method")."</td>
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
						$payment_type = _("Payment not found");
						$vat_collected = _("VAT not found");
					}else{
						$a2 = mysql_fetch_array($r2);
						$payment_txt = $a2["paiement_total"]. " " . $a2["currency"];
						$payment_type = $a2["paiement_type"];
						if($payment_type == "online"){
							$payment_type .= ": ".$a2["secpay_site"];
						}
						$vat_collected = $a2["paiement_total"] * $a2["vat_rate"] / 100 ;
					}
					if($a["last_expiry_date"] == "0000-00-00"){
						$last_expiry_date = $a["date"];
					}else{
						$last_expiry_date = $a["last_expiry_date"];
					}
					if($payment_type == 'wire') $pay=_("Wire"); else if ($payment_type == 'cheque') $pay=_("Cheque");
					else if ( $payment_type == 'online: none' ) $pay=_("Online: none"); else $pay=$payment_type;
					$new_expiry_date = calculateExpirationDate($last_expiry_date,$product_period_size);
					$out .= "<tr><td>$product_txt</td><td>$client_id_txt</td><td>$client_name</td><td>".$a["country_code"]."</td>
					<td>$client_country</td>
					<td>$vat_collected</td><td>$last_expiry_date -> $new_expiry_date</td><td>".$a["date"]."</td><td>$payment_txt</td>
					<td>$pay</td>
					<td><a href=\"?rub=$rub&date=".$_REQUEST["date"]."&action=nuke_payment&completedorders_id=".$a["id"]."\">"._("Delete")."</a></tr>";
				}
				$out .= "</table>";
			}
			return $out;
		}

		// Calculation of recuring totals
		$out .= "<h3>". _("Total Recurring Income per Month:") ."</h3>";
		// Monthly recurring for shared hosting:
		$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period,$pro_mysql_product_table.id
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
			$id = $a["id"];
			// Calculate the total number of months for a product
			$date_array_calc = explode("-",$period);
			$my_total_month = 0;
			if($date_array_calc[0] != "0"){
				$my_total_month += $date_array_calc[0] * 12;
			}
			if($date_array_calc[1] != 0){
				$my_total_month += $date_array_calc[1];
			}
			if($date_array_calc[2] != 0){
				$my_total_month += ($date_array_calc[2] / 30);
			}
			// Then the price per month
			if($my_total_month == 0){
				echo "Product $id has zero month.<br>";
			}else{
				$total_shared += $price / $my_total_month;
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
		$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period,$pro_mysql_product_table.id
		FROM $pro_mysql_product_table,$pro_mysql_vps_table
		WHERE $pro_mysql_product_table.id = $pro_mysql_vps_table.product_id";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$total_vps = 0;
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$period = $a["period"];
			$price = $a["price_dollar"];
			$id = $a["id"];

			// Calculate the total number of months for a product
			$date_array_calc = explode("-",$period);
			$my_total_month = 0;
			if($date_array_calc[0] != "0"){
				$my_total_month += $date_array_calc[0] * 12;
			}
			if($date_array_calc[1] != 0){
				$my_total_month += $date_array_calc[1];
			}
			if($date_array_calc[2] != 0){
				$my_total_month += ($date_array_calc[2] / 30);
			}
			// Then the price per month
			if($my_total_month == 0){
				echo "Product $id has zero month.<br>";
			}else{
				$total_vps += $price / $my_total_month;
			}
		}

		// Monthly recurring for dedicated servers:
		$q = "SELECT $pro_mysql_product_table.price_dollar,$pro_mysql_product_table.period,$pro_mysql_product_table.id
		FROM $pro_mysql_product_table,$pro_mysql_dedicated_table
		WHERE $pro_mysql_product_table.id = $pro_mysql_dedicated_table.product_id";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$total_dedicated = 0;
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$period = $a["period"];
			$price = $a["price_dollar"];
			$id = $a["id"];

			// Calculate the total number of months for a product
			$date_array_calc = explode("-",$period);
			$my_total_month = 0;
			if($date_array_calc[0] != "0"){
				$my_total_month += $date_array_calc[0] * 12;
			}
			if($date_array_calc[1] != 0){
				$my_total_month += $date_array_calc[1];
			}
			if($date_array_calc[2] != 0){
				$my_total_month += ($date_array_calc[2] / 30);
			}
			// Then the price per month
			if($my_total_month == 0){
				echo "Product $id has zero month.<br>";
			}else{
				$total_dedicated += $price / $my_total_month;
			}
		}

		$p_renewal = dtcFormTableAttrs();
		$p_renewal .= "<tr><td colspan=\"2\" style=\"white-space:nowrap; text-align:center;\" nowrap class=\"dtcDatagrid_table_titles\">"._("Total installed products")."</td></tr>";
		$p_renewal .= "<tr><td class=\"dtcDatagrid_table_flds_alt\" style=\"white-space:nowrap; text-align:right;\" nowrap>"._("Shared Hosting:")."</td>";
			$p_renewal .= "<td class=\"dtcDatagrid_table_flds_alt\" style=\"white-space:nowrap;\" nowrap>".round($total_shared,2)." $secpayconf_currency_letters</td></tr>";
		$p_renewal .= "<tr><td class=\"dtcDatagrid_table_flds\" style=\"white-space:nowrap; text-align:right;\" nowrap>"._("SSL IPs Renewals:")."</td>";
			$p_renewal .= "<td class=\"dtcDatagrid_table_flds\" style=\"white-space:nowrap;\" nowrap>".round($total_ssl,2)." $secpayconf_currency_letters</td></tr>";
		$p_renewal .= "<tr><td class=\"dtcDatagrid_table_flds_alt\" style=\"white-space:nowrap; text-align:right;\" nowrap>"._("VPS: ")."</td>";
			$p_renewal .= "<td class=\"dtcDatagrid_table_flds_alt\" style=\"white-space:nowrap;\" nowrap>".round($total_vps,2)." $secpayconf_currency_letters</td</tr>";
		$p_renewal .= "<tr><td class=\"dtcDatagrid_table_flds\" style=\"white-space:nowrap; text-align:right;\" nowrap>"._("Dedicated Servers: ")."</td>";
			$p_renewal .= "<td class=\"dtcDatagrid_table_flds\" style=\"white-space:nowrap;\" nowrap>".round($total_dedicated,2)." $secpayconf_currency_letters</td></tr>";
		$big_total = $total_shared + $total_vps + $total_dedicated + $total_ssl;
		$p_renewal .= "<tr><td class=\"dtcDatagrid_table_flds_alt\" style=\"white-space:nowrap; text-align:right;\" nowrap><b>". _("Total: ")."</b></td>";
			$p_renewal .= "<td class=\"dtcDatagrid_table_flds_alt\" style=\"white-space:nowrap;\" nowrap><b>".round($big_total,2)." $secpayconf_currency_letters</b></td></tr></table>";

		// Show a quick history of payments
		$year = date("Y");
		$month = date("m");
		$cur_year = $year - 2;
		$cur_month = $month;
		$p_history = "";
		$selected_country="";
		$country_array = array();
		if(isset($_REQUEST["country"])){
			$selected_country=$_REQUEST["country"];
		}
		$p_history .= dtcFormTableAttrs()."
<tr><td class=\"dtcDatagrid_table_titles\">". _("Period") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Amount") ."</td>
<td class=\"dtcDatagrid_table_titles\">"._("VAT Collected")."</td>
<td class=\"dtcDatagrid_table_titles\">"._("Payment Gateway Cost")."</td>
<td class=\"dtcDatagrid_table_titles\">"._("Profit")."</td>
<td class=\"dtcDatagrid_table_titles\">"._("Export")."</td>
<td class=\"dtcDatagrid_table_titles\">"._("No VAT repport")."</td>
</tr>";
		for($i=0;$i<25;$i++){
			$q2 = "SELECT $pro_mysql_pay_table.paiement_total,$pro_mysql_pay_table.vat_rate,$pro_mysql_pay_table.paiement_cost
			FROM $pro_mysql_pay_table,$pro_mysql_completedorders_table
			WHERE $pro_mysql_pay_table.vat_rate!='0.00'
			AND $pro_mysql_completedorders_table.payment_id = $pro_mysql_pay_table.id
			AND $pro_mysql_completedorders_table.date LIKE '".$cur_year."-".$cur_month."-%'
			AND $pro_mysql_pay_table.valid='yes'";
			if ($selected_country != "") $q2 .= " AND $pro_mysql_completedorders_table.country_code='$selected_country'";
			$q2 .= ";";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			$vat_collected = 0;
			$month_total =0;
			$cost_total=0;
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
			AND $pro_mysql_pay_table.vat_rate = '0.00'
			AND $pro_mysql_pay_table.valid='yes'";
			if ($selected_country != "") $q2 .= " AND $pro_mysql_completedorders_table.country_code='$selected_country'";
			$q2 .= ";";
			$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 > 0){
				$a2 = mysql_fetch_array($r2);
				$cost_total += $a2["paiement_cost"];
				$month_total += $a2["paiement_total"];
				$profit = $month_total - $cost_total - $vat_collected;
				if($i % 2){
					$td = "td  class=\"dtcDatagrid_table_flds\"";
				}else{
					$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
				}
				$p_history .= "<tr><$td style=\"text-align:right;\"><a href=\"?rub=$rub&display_date=".$cur_year."-".$cur_month."\">".$cur_year."-".$cur_month."</a></td>
				<$td style=\"text-align:right;\">".$month_total." $secpayconf_currency_letters</td>
				<$td style=\"text-align:right;\">".round($vat_collected,2)." $secpayconf_currency_letters</td>
				<$td style=\"text-align:right;\">".round($cost_total,2)." $secpayconf_currency_letters</td>
				<$td style=\"text-align:right;\">".round($profit,2)." $secpayconf_currency_letters</td>
				<$td style=\"text-align:right;\"><a href=\"?rub=renewal&action=export&format=qif&date=".$cur_year."-".$cur_month."\">QIF</a></td>";
				// Calculate the number of rows for the current CSV link display
				// 0 means no display at all
				switch($i){
				case 0:
					switch($cur_month){
					case "01":
					case "04":
					case "07":
					case "10":
						$num_month = 3;
						break;
					case "02":
					case "05":
					case "08":
					case "11":
						$num_month = 2;
						break;
					case "03":
					case "06":
					case "09":
					case "12":
						$num_month = 1;
						break;
					}
					break;
				case 23:
					switch($cur_month){
					case "01":
					case "04":
					case "07":
					case "010":
						$num_month = 2;
						break;
					default:
						$num_month = 0;
					}
				case 24:
					switch($cur_month){
					case "01":
					case "04":
					case "07":
					case "010":
						$num_month = 1;
						break;
					default:
						$num_month = 0;
					}
				default:
					switch($cur_month){
					case "01":
					case "04":
					case "07":
					case "010":
						$num_month = 3;
						break;
					default:
						$num_month = 0;
					}
				}
				// Calculate the last and first month of the period
				switch($cur_month){
				case "01":
				case "02":
				case "03":
					$csv_first_month = "01";
					$csv_last_month = "03";
					break;
				case "04":
				case "05":
				case "06":
					$csv_first_month = "04";
					$csv_last_month = "06";
					break;
				case "07":
				case "08":
				case "09":
					$csv_first_month = "07";
					$csv_last_month = "09";
					break;
				case "10":
				case "11":
				case "12":
					$csv_first_month = "10";
					$csv_last_month = "12";
					break;
				}
				$csv_link = "<a href=\"?rub=renewal&action=export&format=csv_vat&first_month=".$cur_year."-".$csv_first_month."&last_month=".$cur_year."-".$csv_last_month."\">CSV</a>";
				if($num_month != 0){
					$p_history .= "<$td rowspan=\"$num_month\" style=\"text-align:right;\">$csv_link</td>";
				}
				$p_history .= "</tr>";
			}
			$cur_month++;
			if($cur_month > 12){
				$cur_month = 1;
				$cur_year++;
			}
			if($cur_month < 10)	$cur_month = "0".$cur_month;
		}
		$p_history .= "</table>";
		$p_history .= _("Select country to report on: ");

		if( !isset($_REQUEST["country"])){
			$p_history .= _("ALL")." ";	
		}else{
			$p_history .= "<a href=\"?rub=$rub\">"._("ALL")."</a> ";	
		}
		$q2 = "SELECT distinct(country_code) from $pro_mysql_completedorders_table;";
		$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		for($j=0;$j<$n2;$j++){
			$a2 = mysql_fetch_array($r2);
			$country = $a2["country_code"];
			if(isset($_REQUEST["country"]) && $country == $_REQUEST["country"]){
				$p_history .= "$country ";
			}else{
				$p_history .= "<a href=\"?rub=$rub&country=$country\">$country</a> ";
			}
		}
		

		$p_active_prods = "<img src=\"active_prods_graph.php?graph=year\"><br>
<img src=\"active_prods_graph.php?graph=month\">";

		// Layout the recuring stat and the effective payment statistics
		// 		<td valign=\"top\">$p_renewal</td>
		$out .= "<table cellspacing=\"1\" cellpadding=\"4\" border=\"0\" width=\"100%\">
		<tr valign=\"top\"><td valign=\"top\">$p_history<br><br>$p_renewal</td>
		<td valign=\"top\" style=\"text-align:right\">$p_active_prods</td></tr></table>";

		$out .= "<h3>". _("Shared Hosting Renewals:") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE expire < '".date("Y-m-d")."' AND id_client!='0' AND expire !='0000-00-00' ORDER BY expire;";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__);
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No expired shared accounts.") ."<br>";
		}else{
			$out .= dtcFormTableAttrs()."
<tr><td class=\"dtcDatagrid_table_titles\">". _("Login") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Client") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Email") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Expiration Date") ."</td></tr>";
			for($i=0;$i<$n;$i++){
				if($i % 2){
					$td = "td  class=\"dtcDatagrid_table_flds\"";
				}else{
					$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
				}
				$a = mysql_fetch_array($r);
				$q2 = "SELECT * FROM $pro_mysql_client_table WHERE id='".$a["id_client"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					$client_name = _("Client name not found.");
				}else{
					$a2 = mysql_fetch_array($r2);
					$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
				}
				$q2 = "SELECT * FROM $pro_mysql_domain_table WHERE owner='".$a["adm_login"]."';";
				$r2 = mysql_query($q2)or die("Cannot querry $q2 line ".__LINE__." file ".__FILE__);
				$n2 = mysql_num_rows($r2);
				if($n2 > 0){
					$out .= "<tr><$td>".$a["adm_login"]."</td><$td>$client_name</td><$td>".$a2["email"]."</td><$td>".$a["expire"]."</td></tr>";
				}
			}
			$out .= "</table>";
		}

		// List of expired expired SSL IPs
		$out .= "<h3>". _("SSL IP Renewals:") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE expire < '".date("Y-m-d")."' AND available='no' ORDER BY expire";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No expired SSL IPs.") ."<br>";
		}else{
			$out .= dtcFormTableAttrs()."
<tr><td class=\"dtcDatagrid_table_titles\">". _("Login") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Client") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Email") ."</td>
<td class=\"dtcDatagrid_table_titles\">". _("Expiration date") ."</td></tr>";
			for($i=0;$i<$n;$i++){
				if($i % 2){
					$td = "td  class=\"dtcDatagrid_table_flds\"";
				}else{
					$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
				}
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
					$client_name = _("Client name not found.");
				}else{
					$a2 = mysql_fetch_array($r2);
					$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
				}
				$out .= "<tr><$td>".$a["adm_login"]."</td><$td>$client_name</td><$td>".$a2["email"]."</td><$td>".$a["expire"]."</td></tr>";
			}
			$out .= "</table>";
		}

		// List if expired VPS
		$out .= "<h3>". _("VPS Renewals:") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_vps_table WHERE expire_date < '".date("Y-m-d")."' ORDER BY expire_date";
		$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No VPS expired") ."<br>";
		}else{
			$out .= dtcFormTableAttrs()."
			<tr><td class=\"dtcDatagrid_table_titles\">"._("Login")."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("VPS") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Client") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Email") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Expiration Date") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Days Past Expiration") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Action") ."</td>
			</tr>";
			for($i=0;$i<$n;$i++){
				if($i % 2){
					$td = "td  class=\"dtcDatagrid_table_flds\"";
				}else{
					$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
				}
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
					$client_name = _("Client name not found.");
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
								$kill_owner_txt = "<a href=\"?action=kill_vps_and_owner&adm_login=".$admin["adm_login"]."&client_id=".$admin["id_client"]."&vps_name=".$a["vps_xen_name"]."&server_hostname=".$a["vps_server_hostname"]."&vps_id=".$a["id"]."\">"._("Delete VPS and Owner")."</a>";
							}else{
								$kill_owner_txt = _("Has active domains.");
							}
						}else{
							$kill_owner_txt = _("Has active dedicated servers.");
						}
					}else{
						$kill_owner_txt = _("More than one VPS");
					}
				}else{
					$kill_owner_txt = _("More than one login");
				}
				if( numOfDays($a["expire_date"]) >= $conf_vps_renewal_shutdown){
					$bgcolor = " style=\"color:red;\" ";
				}else{
					$bgcolor = " ";
				}
				$out .= "<tr><$td>".$a["owner"]."</td>
				<$td>".$a["vps_xen_name"].":".$a["vps_server_hostname"]."</td>
				<$td>$client_name</td>
				<$td>".$a2["email"]."</td>
				<$td $bgcolor>".$a["expire_date"]."</td>
				<$td $bgcolor>". calculateAge($a["expire_date"],"00:00:00") ."</td>
				<$td><a href=\"?rub=$rub&action=shutdown_expired_vps&server_hostname=".$a["vps_server_hostname"]."&vps_name=".$a["vps_xen_name"]."\">"._("Shutdown")."</a> - $kill_owner_txt</td></tr>";
			}
			$out .= "</table>";
		}

		// List expired dedicated servers
		$out .= "<h3>". _("Dedicated Server Renewals:") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_dedicated_table WHERE expire_date < '".date("Y-m-d")."' ORDER BY expire_date";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$out .= _("No expired dedicated servers.") ."<br>";
		}else{
			$out .= dtcFormTableAttrs()."
			<tr><td class=\"dtcDatagrid_table_titles\">". _("Login") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Server") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Client") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Email") ."</td>
			<td class=\"dtcDatagrid_table_titles\">". _("Expiration Date") ."</td></tr>";
			for($i=0;$i<$n;$i++){
				if($i % 2){
					$td = "td  class=\"dtcDatagrid_table_flds\"";
				}else{
					$td = "td  class=\"dtcDatagrid_table_flds_alt\"";
				}
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
					$client_name = _("Client name not found.");
				}else{
					$a2 = mysql_fetch_array($r2);
					$client_name = $a2["company_name"].":".$a2["christname"].", ".$a2["familyname"];
				}
				$out .= "<tr><$td>".$a["owner"]."</td><$td>".$a["server_hostname"]."</td><$td>$client_name</td><$td>".$a2["email"]."</td><$td>".$a["expire_date"]."</td></tr>";
			}
			$out .= "</table>";
		}
		break;
	}
	return $out;
}

?>
