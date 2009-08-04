<?php

function productManager(){
        global $pro_mysql_product_table;
        global $secpayconf_currency_symbol;

        if(!isset($secpayconf_currency_symbol)){
        	get_secpay_conf();
        }

        $dsc = array(
        	"table_name" => $pro_mysql_product_table,
        	"title" => _("Product list editor") ." (shared)",
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
				"values" => array("check","no","yes")
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
        	"title" => _("Product list editor") ." (VPS)",
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
        	"title" => _("Product list editor") ." (Dedicated servers)",
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
        	"title" => _("Product list editor") ." (SSL IPs)",
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
				"values" => $renew_id_popup,
				"display_replace" => array("No-renew")
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
