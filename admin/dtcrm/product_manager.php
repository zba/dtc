<?php

function productManager(){
        global $pro_mysql_product_table;

	// Build the product ID popup
        $qp = "SELECT id FROM $pro_mysql_product_table WHERE renew_prod_id='0'";
        $rp = mysql_query($qp)or die("Cannot query \"$qp\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
        $np = mysql_num_rows($rp);
        $renew_id_popup = array();
        $renew_id_popup[] = 0;
	for($j=0;$j<$np;$j++){
		$ap = mysql_fetch_array($rp);
		$renew_id_popup[] = $ap["id"];
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
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"quota_disk" => array(
				"type" => "text",
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
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
        		)
        	);
	$out = dtcDatagrid($dsc);

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
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"quota_disk" => array(
				"type" => "text",
				"legend" => _("Disk") ,
				"size" => "4"
				),
			"memory_size" => array(
				"type" => "text",
				"legend" => _("RAM") ,
				"size" => "4"
				),
			"bandwidth" => array(
				"type" => "text",
				"legend" => _("Traffic") ,
				"size" => "5"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
        		)
        	);
	$out .= dtcDatagrid($dsc);

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
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"quota_disk" => array(
				"type" => "text",
				"legend" => _("Disk") ,
				"size" => "4"
				),
			"memory_size" => array(
				"type" => "text",
				"legend" => "RAM",
				"size" => "4"
				),
			"bandwidth" => array(
				"type" => "text",
				"legend" => _("Traffic") ,
				"size" => "5"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Period") ,
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
				"legend" => _("Period") ,
				"size" => "10"
				),
			"price_dollar" => array(
				"type" => "text",
				"legend" => _("Price") ,
				"size" => "4"
				),
			"private" => array(
				"type" => "checkbox",
				"legend" => _("Private") ,
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
