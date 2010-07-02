<?php

function productManager(){
        global $pro_mysql_product_table;
        global $secpayconf_currency_symbol;

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
				"help" => _("Period for the product with format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, write 0001-02-03. "),
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
				"help" => _("This is the amount of money that you will give back to the affiliate account that made the sell possible."),
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
				"legend" => _("Max email") ,
				"size" => "2"
				),
			"nbr_database" => array(
				"type" => "text",
				"legend" => _("Max database") ,
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
				"help" => _("If set to yes, the the admin can add a domain, if set to check, then it will go through moderation, set to no, no domain addition to account is possible by the admin."),
				"values" => array("check","no","yes"),
				"display_replace" => array(_("check"),_("no"),_("yes"))
				),
			"max_domain" => array(
				"type" => "text",
				"legend" => _("Max domain"),
				"help" => _("Maximum number of domain a customer can add by himself on his shared account. Setting a value of zero will mean no limit."),
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
				"help" => _("If set to no, users will only be able to create FTP accounts with a path in the html folder of each vhosts."),
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
				"help" => _("If set to no, users wont be able to add/remove/edit subdomains."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "yes"
				),
			"pkg_install_flag" => array(
				"type" => "checkbox",
				"legend" => _("Subdomains"),
				"help" => _("If set to no, users wont be able to use the package installer."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "yes"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"help" => _("If the private flag is set, then this product wont appear in the registration form."),
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
				"help" => _("Period for the product with format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, write 0001-02-03. "),
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
				"help" => _("This is the amount of money that you will give back to the affiliate account that made the sell possible."),
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
				"help" => _("If the private flag is set, then this product wont appear in the registration form."),
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
				"help" => _("Period for the product with format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, write 0001-02-03. "),
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
				"help" => _("This is the amount of money that you will give back to the affiliate account that made the sell possible."),
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
				"help" => _("If the private flag is set, then this product wont appear in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
        		)
        	);
	$out .= dtcDatagrid($dsc);

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
				"display_replace" => array(_("No-renew"))
				),
			"name" => array(
				"type" => "text",
				"legend" => _("Name") ,
				"size" => "30"
				),
			"period" => array(
				"type" => "text",
				"help" => _("Period for the product with format YYYY-MM-DD. For example, if you want a product that will last 1 year, 2 months, and 3 days, write 0001-02-03. "),
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
				"help" => _("This is the amount of money that you will give back to the affiliate account that made the sell possible."),
				"legend" => _("Commission"). " " . $secpayconf_currency_symbol,
				"size" => "4"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"help" => _("If the private flag is set, then this product wont appear in the registration form."),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
        		)
        	);
	$out .= dtcDatagrid($dsc);

	$out .= helpLink("PmWiki/HostingProductManager");
	return $out;
}

?>
