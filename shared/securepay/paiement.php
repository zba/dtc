<?php

require("$dtcshared_path/securepay/paiement_config.php");
if($conf_use_worldpay == "yes")	include("$dtcshared_path/securepay/gateways/worldpay.php");
if($conf_use_paypal == "yes")	include("$dtcshared_path/securepay/gateways/paypal.php");
require("$dtcshared_path/securepay/pay_functions.php");

function get_secpay_conf(){
	global $conf_mysql_db;
	global $pro_mysql_secpayconf_table;

        $query = "SELECT * FROM $pro_mysql_secpayconf_table WHERE 1 LIMIT 1;";
        $result = mysql_query($query)or die("Cannot query $query !!! line: ".__LINE__." file: ".__FULE__." sql said: ".mysql_error());
        $num_rows = mysql_num_rows($result);
        if($num_rows != 1)      die("No config values in table !!!");
        $row = mysql_fetch_array($result);

	$fields = mysql_list_fields($conf_mysql_db, $pro_mysql_secpayconf_table);
	$columns = mysql_num_fields($fields);

	for($i=0;$i<$columns;$i++){
		$field_name = mysql_field_name($fields, $i);
		$toto = "secpayconf_".$field_name;
		global $$toto;
		$$toto = $row["$field_name"];
        }
}
?>
