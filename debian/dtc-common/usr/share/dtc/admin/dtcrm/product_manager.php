<?php

function productManager(){
        global $pro_mysql_product_table;
        global $secpayconf_currency_symbol;
        global $pro_mysql_custom_heb_types_table;
        global $pro_mysql_custom_heb_types_fld_table;

        if(!isset($secpayconf_currency_symbol)){
        	get_secpay_conf();
        }

		$dsc = array(
			"table_name" => $pro_mysql_product_table,
			"title" => _("Product list editor") . _(" (shared)"),
			"action" => "hosting_product_list_shared",
			"forward" => array("rub"),
			"where_condition" => "heb_type='shared'",
			"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "yes",
				"legend" => "Id"
				),
			"name" => array(
				"type" => "text",
				"legend" => _("Product name") ,
				"size" => "30"
				),
			"period" => array(
				"type" => "text",
				"help" => _("Period for the product in the format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, enter 0001-02-03. "),
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"setup_fee" => array(
				"type" => "text",
				"legend" => _("Setup fee") ,
				"size" => "4"
				),
			"affiliate_kickback" => array(
				"type" => "text",
				"help" => _("This is the amount of money that you will give back to the affiliate account responsible for the sale."),
				"legend" => _("Commission"). " " . $secpayconf_currency_symbol,
				"size" => "4"
				),
			"quota_disk" => array(
				"type" => "text",
				"help" => _("Hard drive space in MBytes."),
				"legend" => _("Disk") ,
				"size" => "4"
				),
			"nbr_email" => array(
				"type" => "text",
				"legend" => _("Max mailboxes") ,
				"size" => "2"
				),
			"nbr_database" => array(
				"type" => "text",
				"legend" => _("Max databases") ,
				"size" => "2"
				),
			"bandwidth" => array(
				"type" => "text",
				"legend" => _("Traffic") ,
				"size" => "5"
				),
			"allow_add_domain" => array(
				"type" => "popup",
				"legend" => _("Add domain") ,
				"help" => _("If set to yes, the the admin can add a domain, if set to check, then the request will go through admin moderation, set to no, no domain addition to account is possible."),
				"values" => array("check","no","yes"),
				"display_replace" => array(_("check"),_("no"),_("yes"))
				),
			"max_domain" => array(
				"type" => "text",
				"legend" => _("Max domains"),
				"help" => _("Maximum number of domains a customer can add to his shared account. Setting a value of zero will mean no limit."),
				"size" => "3"
				),
			"allow_dns_and_mx_change" => array(
				"type" => "checkbox",
				"legend" => _("DNS & MX"),
				"help" => _("If set to no, users wont be able to edit the DNS and MX pointer of their domains."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no"
				),
			"ftp_login_flag" => array(
				"type" => "checkbox",
				"legend" => _("FTP"),
				"help" => _("If set to no, users wont be able to add/remove/edit FTP accounts."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "yes"
				),
			"restricted_ftp_path" => array(
				"type" => "checkbox",
				"legend" => _("Restricted FTP"),
				"help" => _("If set to no, users will only be able to create FTP accounts under the html folder of each vhost."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no"
				),
			"allow_mailing_list_edit" => array(
				"type" => "checkbox",
				"legend" => _("Lists"),
				"help" => _("If set to no, users wont be able to add/remove/edit mailing lists and mail alias groups."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "yes"
				),
			"allow_subdomain_edit" => array(
				"type" => "checkbox",
				"legend" => _("Subdomains"),
				"help" => _("If set to no, users won't be able to add/remove/edit subdomains."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "yes"
				),
			"pkg_install_flag" => array(
				"type" => "checkbox",
				"legend" => _("Installer"),
				"help" => _("If set to no, users won't be able to use the package installer."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "yes"
				),
			"shared_hosting_security" => array(
				"type" => "popup",
				"legend" => _("Security"),
				"help" => _("mod_php: considered unsafe as a customer has access to all files hosted as dtc:dtcgrp in /var/www/sites.")."<br>".
					_("sbox_copy: make a copy of /var/lib/dtc/full_chroot.")."<br>".
					_("sbox_aufs: uses aufs and autofs so that customer uses a unionfs in his chrooted disk"),
				"values" => array('mod_php','sbox_copy','sbox_aufs'),
				"default" => "sbox_copy"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"help" => _("If the private flag is set, then this product won't appear in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
        		)
        	);
	$out = dtcDatagrid($dsc);

	// Build the product ID popup
	$qp = "SELECT id,name FROM $pro_mysql_product_table WHERE renew_prod_id='0' AND heb_type='vps'";
	$rp = mysql_query($qp)or die("Cannot query \"$qp\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$np = mysql_num_rows($rp);
	$renew_id_popup = array();
	$renew_id_popup[] = 0;
	$renew_id_replace = array();
	$renew_id_replace[] = _("Not a renewal product");
	for($j=0;$j<$np;$j++){
		$ap = mysql_fetch_array($rp);
		$renew_id_popup[] = $ap["id"];
		if(strlen($ap["name"]) > 20){
			$renew_id_replace[] = $ap["id"]. ": " .substr($ap["name"],0,17)."...";
		}else{
			$renew_id_replace[] = $ap["id"]. ": " .$ap["name"];
		}
	}

		$dsc = array(
			"table_name" => $pro_mysql_product_table,
			"title" => _("Product list editor") . _(" (VPS)"),
			"action" => "hosting_product_list_vps",
			"forward" => array("rub"),
			"where_condition" => "heb_type='vps'",
			"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "yes",
				"legend" => "Id"
				),
			"renew_prod_id" => array(
				"type" => "popup",
				"help" => _("If you set the renewal ID, then this entry will be considered as a renewal product for the matching ID."),
				"legend" => _("Renewal-ID") ,
				"values" => $renew_id_popup,
				"display_replace" => $renew_id_replace
				),
			"name" => array(
				"type" => "text",
				"legend" => _("Name") ,
				"size" => "30"
				),
			"period" => array(
				"type" => "text",
				"help" => _("Period for the product in the format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, enter 0001-02-03. "),
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"setup_fee" => array(
				"type" => "text",
				"legend" => _("Setup fee") ,
				"size" => "4"
				),
			"affiliate_kickback" => array(
				"type" => "text",
				"help" => _("This is the amount of money that you will give back to the affiliate account responsible for the sale."),
				"legend" => _("Commission"). " " . $secpayconf_currency_symbol,
				"size" => "4"
				),
			"quota_disk" => array(
				"type" => "text",
				"help" => _("Hard drive space in MBytes."),
				"legend" => _("Disk") ,
				"size" => "4"
				),
			"memory_size" => array(
				"type" => "text",
				"help" => _("Memory size in MBytes."),
				"legend" => _("RAM") ,
				"size" => "4"
				),
			"bandwidth" => array(
				"type" => "text",
				"help" => _("Bandwidth per month in MBytes."),
				"legend" => _("Traffic") ,
				"size" => "5"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"help" => _("If the private flag is set, then this product won't appear in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
			)
		);
	$out .= dtcDatagrid($dsc);

	// Build the product ID popup
		$qp = "SELECT id,name FROM $pro_mysql_product_table WHERE renew_prod_id='0' AND heb_type='server'";
		$rp = mysql_query($qp)or die("Cannot query \"$qp\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$np = mysql_num_rows($rp);
		$renew_id_popup = array();
		$renew_id_popup[] = 0;
		$renew_id_replace = array();
		$renew_id_replace[] = _("Not a renewal product");
	for($j=0;$j<$np;$j++){
		$ap = mysql_fetch_array($rp);
		$renew_id_popup[] = $ap["id"];
		if(strlen($ap["name"]) > 20){
			$renew_id_replace[] = $ap["id"]. ": " .substr($ap["name"],0,17)."...";
		}else{
			$renew_id_replace[] = $ap["id"]. ": " .$ap["name"];
		}
	}

		$dsc = array(
			"table_name" => $pro_mysql_product_table,
			"title" => _("Product list editor") . _(" (Dedicated servers)"),
			"action" => "hosting_product_list_dedicated",
			"forward" => array("rub"),
			"where_condition" => "heb_type='server'",
			"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "yes",
				"legend" => "Id"
				),
			"renew_prod_id" => array(
				"type" => "popup",
				"help" => _("If you set the renewal ID, then this entry will be considered as a renewal product for the matching ID."),
				"legend" => _("Renewal-ID") ,
				"values" => $renew_id_popup,
				"display_replace" => $renew_id_replace
				),
			"name" => array(
				"type" => "text",
				"legend" => _("Name") ,
				"size" => "30"
				),
			"period" => array(
				"type" => "text",
				"help" => _("Period for the product in the format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, enter 0001-02-03. "),
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"setup_fee" => array(
				"type" => "text",
				"legend" => _("Setup fee") ,
				"size" => "4"
				),
			"affiliate_kickback" => array(
				"type" => "text",
				"help" => _("This is the amount of money that you will give back to the affiliate account responsible for the sale."),
				"legend" => _("Commission"). " " . $secpayconf_currency_symbol,
				"size" => "4"
				),
			"quota_disk" => array(
				"type" => "text",
				"help" => _("Hard drive space in MBytes."),
				"legend" => _("Disk") ,
				"size" => "4"
				),
			"memory_size" => array(
				"type" => "text",
				"help" => _("Memory size in MBytes."),
				"legend" => "RAM",
				"size" => "4"
				),
			"bandwidth" => array(
				"type" => "text",
				"help" => _("Bandwidth per month in GBytes."),
				"legend" => _("Traffic") ,
				"size" => "5"
				),
			"use_radius" => array(
				"type" => "checkbox",
				"legend" => _("Use Radius") ,
				"help" => _("If the Use Radius flag is set this service is used to check a radius user."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no"),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"help" => _("If the private flag is set, then this product won't appear in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
				)
			);
	$out .= dtcDatagrid($dsc);


	// Build the product ID popup
	$qp = "SELECT id,name FROM $pro_mysql_product_table WHERE renew_prod_id='0' AND heb_type='ssl'";
	$rp = mysql_query($qp)or die("Cannot query \"$qp\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$np = mysql_num_rows($rp);
	$renew_id_popup = array();
	$renew_id_popup[] = 0;
	$renew_id_replace = array();
	$renew_id_replace[] = _("Not a renewal product");
	for($j=0;$j<$np;$j++){
		$ap = mysql_fetch_array($rp);
		$renew_id_popup[] = $ap["id"];   
		if(strlen($ap["name"]) > 20){
			$renew_id_replace[] = $ap["id"]. ": " .substr($ap["name"],0,17)."...";
		}else{
			$renew_id_replace[] = $ap["id"]. ": " .$ap["name"];
		}
	}

		$dsc = array(
			"table_name" => $pro_mysql_product_table,
			"title" => _("Product list editor") . _(" (SSL IPs)"),
			"action" => "hosting_product_list_ssl",
			"forward" => array("rub"),
			"where_condition" => "heb_type='ssl'",
			"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "yes",
				"legend" => "Id"
				),
			"renew_prod_id" => array(
				"type" => "popup",
				"legend" => _("Renewal-ID") ,
				"help" => _("There must be ONLY ONE SSL product at any time, with only ONE renewal product."),
				"values" => $renew_id_popup,
				"display_replace" => $renew_id_replace
				),
			"name" => array(
				"type" => "text",
				"legend" => _("Name") ,
				"size" => "30"
				),
			"period" => array(
				"type" => "text",
				"help" => _("Period for the product in the format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, enter 0001-02-03. "),
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"setup_fee" => array(
				"type" => "text",
				"legend" => _("Setup fee") ,
				"size" => "4"
				),
			"affiliate_kickback" => array(
				"type" => "text",
				"help" => _("This is the amount of money that you will give back to the affiliate account responsible for the sale."),
				"legend" => _("Commission"). " " . $secpayconf_currency_symbol,
				"size" => "4"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"help" => _("If the private flag is set, then this product won't appear in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
			)
		);
	$out .= dtcDatagrid($dsc);
		// Build the product ID popup
		$qp = "SELECT id,name FROM ".$pro_mysql_custom_heb_types_table;
		$rp = mysql_query($qp)or die("Cannot query \"$qp\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$np = mysql_num_rows($rp);
		$type_id_popup = array();
		$type_id_replace = array();
	for($j=0;$j<$np;$j++){
		$ap = mysql_fetch_array($rp);
		$type_id_popup[] = $ap["id"];
		if(strlen($ap["name"]) > 20){
			$type_id_replace[] = $ap["id"]. ": " .substr($ap["name"],0,17)."...";
		}else{
			$type_id_replace[] = $ap["id"]. ": " .$ap["name"];
		}
	}
	// Build the product ID popup
		$qp = "SELECT id,name FROM $pro_mysql_product_table WHERE renew_prod_id='0' AND heb_type='custom' order by custom_heb_type";
		$rp = mysql_query($qp)or die("Cannot query \"$qp\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$np = mysql_num_rows($rp);
		$renew_id_popup = array();
		$renew_id_popup[] = 0;
		$renew_id_replace = array();
		$renew_id_replace[] = _("Not a renewal product");
	for($j=0;$j<$np;$j++){
		$ap = mysql_fetch_array($rp);
		$renew_id_popup[] = $ap["id"];
		if(strlen($ap["name"]) > 20){
			$renew_id_replace[] = $ap["id"]. ": " .substr($ap["name"],0,17)."...";
		}else{
			$renew_id_replace[] = $ap["id"]. ": " .$ap["name"];
		}
	}
		$dsc = array(
			"table_name" => $pro_mysql_product_table,
			"title" => _("Product list editor") . _(" (Custom)"),
			"action" => "hosting_product_list_custom",
			"forward" => array("rub"),
			"where_condition" => "heb_type='custom'",
			"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "yes",
				"legend" => "Id"
				),
			"renew_prod_id" => array(
				"type" => "popup",
				"help" => _("If you set the renewal ID, then this entry will be considered as a renewal product for the matching ID."),
				"legend" => _("Renewal-ID") ,
				"values" => $renew_id_popup,
				"display_replace" => $renew_id_replace
				),
			"name" => array(
				"type" => "text",
				"legend" => _("Name") ,
				"size" => "30"
				),
			"period" => array(
				"type" => "text",
				"help" => _("Period for the product in the format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, enter 0001-02-03. "),
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"setup_fee" => array(
				"type" => "text",
				"legend" => _("Setup fee") ,
				"size" => "4"
				),
			"affiliate_kickback" => array(
				"type" => "text",
				"help" => _("This is the amount of money that you will give back to the affiliate account responsible for the sale."),
				"legend" => _("Commission"). " " . $secpayconf_currency_symbol,
				"size" => "4"
				),
			"custom_heb_type" => array(
				"type" => "popup",
				"help" => _("The custom product types (set in custom heb types)."),
				"legend" => _("Custom product type") ,
				"values" => $type_id_popup,
				"display_replace" => $type_id_replace
				),
			"custom_heb_type_fld" => array(
				"type" => "custom_fld",
				"legend" => _("Custom fields"),
				"main_table" => $pro_mysql_custom_heb_types_fld_table,
				"second_table" => $pro_mysql_custom_heb_types_table,
				"third_table" => $pro_mysql_product_table,
				"main_join_clause" => $pro_mysql_custom_heb_types_fld_table.".custom_heb_type_id = ".$pro_mysql_custom_heb_types_table.".id",
				"second_join_clause" => $pro_mysql_custom_heb_types_table.".id = ".$pro_mysql_product_table.".custom_heb_type",
				"where_field" => $pro_mysql_product_table.".id", 
				"order_field" => "widgetorder"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"help" => _("If the private flag is set, then this product won't appear in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
			)
		);
	$out .= dtcDatagrid($dsc);

		$dsc = array(
			"table_name" => $pro_mysql_custom_heb_types_table,
			"title" => _("Product list editor") . _(" (Custom Heb Types)"),
			"action" => "hosting_list_custom_heb_types",
			"forward" => array("rub"),
			"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "yes",
				"legend" => "id"),
			"name" => array(
				"type" => "text",
				"legend" => "name"),
			"reqdomain" => array(
				"type" => "checkbox",
				"legend" => _("Requires Domain Name") ,
				"help" => _("If the reqdomain flag is set, then this product will prompt for a domain name in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"title" => _("Custom fields for custom product types"),
		"table_name" => $pro_mysql_custom_heb_types_fld_table,
		"action" => "custom_product_field_editor",
		"forward" => array("rub"),
		"order_by" => "custom_heb_type_id,widgetorder",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "Id"),
			"widgetorder" => array(
				"legend" => _("Display order"),
				"type" => "text",
				"size" => "4"),
			"varname" => array(
				"legend" => _("Variable name"),
				"type" => "text",
				"size" => "15"),
			"question" => array(
				"legend" => _("Question to the user"),
				"type" => "text",
				"size" => "30"),
			"widgettype" => array(
				"legend" => _("Widget type"),
				"type" => "popup",
				"values" => array( "text", "popup", "radio", "textarea")),
			"widgetvalues" => array(
				"legend" => _("Possible values"),
				"type" => "text",
				"size" => "20"),
			"widgetdisplay" => array(
				"legend" => _("Corresponding display"),
				"type" => "text",
				"size" => "30"),
			"custom_heb_type_id" => array(
				"type" => "popup",
				"help" => _("The custom product types (set in custom heb types)."),
				"legend" => _("Custom product type") ,
				"values" => $type_id_popup,
				"display_replace" => $type_id_replace)
			)
		);
	$out .= dtcDatagrid($dsc);
	$out .= _("On the above tables, the possible values are what is is going to be the internal value in the popup or radio buttons,
which is what is going to be recorded in the database. Values are separated by \"|\". The corresponding display is what will actually
be displayed to your users instead of the popup value.")."<br>";

	$out .= helpLink("PmWiki/HostingProductManager");
	return $out;
}

?>
