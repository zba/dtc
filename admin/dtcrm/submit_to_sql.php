<?php
if($_REQUEST["rub"] == "crm"){


if($_REQUEST["action"] == "new_client"){
	$q = "INSERT INTO $pro_mysql_client_table(
id,
is_company,
company_name,
familyname,
christname,
addr1,
addr2,
addr3,
city,
zipcode,
state,
country,
phone,
fax,
email,
special_note,
dolar decimal,
disk_quota_mb,
bw_quota_per_month_gb
)VALUES(
";
//&ed_familyname=&ed_christname=&ed_company_name=&ed_addr1=&ed_addr2=&ed_city=&ed_zipcode=&ed_state=&ed_country=&ed_phone=&ed_fax=&ed_email=



}

}
?>
