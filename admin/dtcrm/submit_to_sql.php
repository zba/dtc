<?php

// action=edit_product&rub=product
// &prodname=Domain+name+registration
// &id=1
// &price_dollar=11.50&price_euro=10.50
// &quota_disk=1&bandwidth=20&nbr_email=1&nbr_database=0
// &allow_add_domain=yes&period=0001-00-00&submit=save
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit_product"){
	if(isset($_REQUEST["allow_add_domain"]) && $_REQUEST["allow_add_domain"] == "yes"){
		$yesval = "yes";
	}else if(isset($_REQUEST["allow_add_domain"]) && $_REQUEST["allow_add_domain"] == "check"){
		$yesval = "check";
	}else{
		$yesval = "no";
	}
	if($_REQUEST["submit"] == "save"){
		$q = "UPDATE $pro_mysql_product_table
SET price_dollar='".$_REQUEST["price_dollar"]."',
price_euro='".$_REQUEST["price_euro"]."',
name='".$_REQUEST["prodname"]."',
quota_disk='".$_REQUEST["quota_disk"]."',
memory_size='".$_REQUEST["memory_size"]."',
nbr_email='".$_REQUEST["nbr_email"]."',
nbr_database='".$_REQUEST["nbr_database"]."',
bandwidth='".$_REQUEST["bandwidth"]."',
period='".$_REQUEST["period"]."',
allow_add_domain='$yesval',
heb_type='".$_REQUEST["heb_type"]."',
renew_prod_id='".$_REQUEST["renew_prod_id"]."'
WHERE id='".$_REQUEST["id"]."' LIMIT 1;";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		
	}else if($_REQUEST["submit"] == "del"){
		$q = "DELETE FROM $pro_mysql_product_table WHERE id='".$_REQUEST["id"]."' LIMIT 1;";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	}else if($_REQUEST["submit"] == "create"){
		$q = "INSERT INTO $pro_mysql_product_table
(id,price_dollar,price_euro,name,quota_disk,memory_size,nbr_email,nbr_database,
bandwidth,period,allow_add_domain,heb_type,renew_prod_id) VALUES('','".$_REQUEST["price_dollar"]."','".$_REQUEST["price_euro"]."','".$_REQUEST["prodname"]."',
'".$_REQUEST["quota_disk"]."','".$_REQUEST["memory_size"]."','".$_REQUEST["nbr_email"]."','".$_REQUEST["nbr_database"]."',
'".$_REQUEST["bandwidth"]."','".$_REQUEST["period"]."','$yesval','".$_REQUEST["heb_type"]."','".$_REQUEST["renew_prod_id"]."');";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	}
}



if(isset($_REQUEST["rub"]) && $_REQUEST["rub"] == "crm"){
//////////////////////////////////
// Client (new/edit) management //
//////////////////////////////////
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "modify_client_cmd"){
	if($_REQUEST["ed_command"] == "Save"){
// cmd_id=1&id=1&rub=crm&price=25&quantity=1&action=modify_client_cmd&
// cmd_date=2004-06-10&cmd_expir=2005-06-10&ed_command=Save
		$q = "UPDATE $pro_mysql_command_table SET quantity='".$_REQUEST["quantity"]."',price='".$_REQUEST["price"]."',date='".$_REQUEST["cmd_date"]."',expir='".$_REQUEST["cmd_expir"]."' WHERE id='".$_REQUEST["cmd_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	}
	if($_REQUEST["del_command"] == "Del"){
		$q = "DELETE FROM $pro_mysql_command_table WHERE id='".$_REQUEST["cmd_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	}
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_admin_to_client"){
	$q = "UPDATE $pro_mysql_admin_table SET id_client='".$_REQUEST["id"]."' WHERE adm_login='".$_REQUEST["adm_name"]."';";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "remove_admin_from_client"){
	$q = "UPDATE $pro_mysql_admin_table SET id_client='0' WHERE adm_login='".$_REQUEST["adm_name"]."';";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
}
//id=0&action=new_client&ed_familyname=&ed_christname=&ed_is_copany=yes&ed_company_name=&ed_addr1=&ed_addr2=&ed_addr3=
//&ed_city=&ed_zipcode=&ed_state=&ed_country=AF&ed_phone=&ed_fax=&ed_email=&ed_special_note=&ed_dollar=&
//ed_disk_quota_mb=&ed_gw_quota_per_month_gb=
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "new_client"){
	$q = "INSERT INTO $pro_mysql_client_table(
id,is_company,company_name,vat_num,
familyname,christname,
addr1,addr2,addr3,
city,zipcode,state,
country,phone,fax,
email,special_note,dollar,
disk_quota_mb,bw_quota_per_month_gb
)VALUES(
'','".$_REQUEST["ed_is_company"]."','".mysql_real_escape_string($_REQUEST["ed_company_name"])."','".mysql_real_escape_string($_REQUEST["ed_vat_num"])."',
'".mysql_real_escape_string($_REQUEST["ed_familyname"])."','".mysql_real_escape_string($_REQUEST["ed_christname"])."',
'".mysql_real_escape_string($_REQUEST["ed_addr1"])."','".mysql_real_escape_string($_REQUEST["ed_addr2"])."','".mysql_real_escape_string($_REQUEST["ed_addr3"])."',
'".mysql_real_escape_string($_REQUEST["ed_city"])."','".mysql_real_escape_string($_REQUEST["ed_zipcode"])."','".mysql_real_escape_string($_REQUEST["ed_state"])."',
'".mysql_real_escape_string($_REQUEST["ed_country"])."','".mysql_real_escape_string($_REQUEST["ed_phone"])."','".mysql_real_escape_string($_REQUEST["ed_fax"])."',
'".mysql_real_escape_string($_REQUEST["ed_email"])."','".mysql_real_escape_string($_REQUEST["ed_special_note"])."','".mysql_real_escape_string($_REQUEST["ed_dollar"])."',
'".mysql_real_escape_string($_REQUEST["ed_disk_quota_mb"])."','".mysql_real_escape_string($_REQUEST["ed_bw_quota_per_month_gb"])."');";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
}

//id=0&action=new_client&ed_familyname=&ed_christname=&ed_is_copany=yes&ed_company_name=&ed_addr1=&ed_addr2=&ed_addr3=
//&ed_city=&ed_zipcode=&ed_state=&ed_country=AF&ed_phone=&ed_fax=&ed_email=&ed_special_note=&ed_dollar=&
//ed_disk_quota_mb=&ed_gw_quota_per_month_gb=
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_customer_id"){
	$q = "DELETE FROM $pro_mysql_client_table WHERE id='".$_REQUEST["delete_id"]."' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	$q = "UPDATE $pro_mysql_admin_table SET id_client='0' WHERE id_client='".$_REQUEST["delete_id"]."';";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit_client"){
	if(isset($_REQUEST["del"]) && $_REQUEST["del"] == "Del"){
		$q = "DELETE FROM $pro_mysql_client_table WHERE id='".$_REQUEST["id"]."' LIMIT 1;";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
		$q = "UPDATE $pro_mysql_admin_table SET id_client='0' WHERE id_client='".$_REQUEST["id"]."' LIMIT 1;";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	}else{
		$q = "UPDATE $pro_mysql_client_table SET
is_company='".mysql_real_escape_string($_REQUEST["ed_is_company"])."',
company_name='".mysql_real_escape_string($_REQUEST["ed_company_name"])."',
vat_num='".mysql_real_escape_string($_REQUEST["ed_vat_num"])."',
familyname='".mysql_real_escape_string($_REQUEST["ed_familyname"])."',
christname='".mysql_real_escape_string($_REQUEST["ed_christname"])."',
addr1='".mysql_real_escape_string($_REQUEST["ed_addr1"])."',
addr2='".mysql_real_escape_string($_REQUEST["ed_addr2"])."',
addr3='".mysql_real_escape_string($_REQUEST["ed_addr3"])."',
city='".mysql_real_escape_string($_REQUEST["ed_city"])."',
zipcode='".mysql_real_escape_string($_REQUEST["ed_zipcode"])."',
state='".mysql_real_escape_string($_REQUEST["ed_state"])."',
country='".mysql_real_escape_string($_REQUEST["ed_country"])."',
phone='".mysql_real_escape_string($_REQUEST["ed_phone"])."',
fax='".mysql_real_escape_string($_REQUEST["ed_fax"])."',
email='".$_REQUEST["ed_email"]."',
special_note='".mysql_real_escape_string($_REQUEST["ed_special_note"])."',
dollar='".$_REQUEST["ed_dollar"]."',
disk_quota_mb='".$_REQUEST["ed_disk_quota_mb"]."',
bw_quota_per_month_gb='".$_REQUEST["ed_bw_quota_per_month_gb"]."'
WHERE id='".$_REQUEST["id"]."' LIMIT 1;";
		$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	}
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_cmd_to_client"){
	get_secpay_conf();

	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["add_new_command"]."';";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Product ID not found!!!");
	$a = mysql_fetch_array($r);
	$exp = explode("-",$a["period"]);
	$d = 60*60*24;
	$m = $d*365/12;
	$y = $d*365;
	$exp_date = date("Y-m-d",time() + $y*$exp[0] + $m*$exp[1] + $d*$exp[2] );
	$q = "INSERT INTO $pro_mysql_command_table (id,
id_client,domain_name,quantity,price_devise,price,paiement_method,date,expir,product_id
)VALUES('','".$_REQUEST["id"]."','".$_REQUEST["add_newcmd_domain_name"]."','1','$secpayconf_currency_letters','".$a["price_dollar"]."','free','".date("Y-m-d")."','$exp_date','".$_REQUEST["add_new_command"]."');";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
}

}
?>
