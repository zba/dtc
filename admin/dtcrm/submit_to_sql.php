<?php
if($_REQUEST["rub"] == "crm"){

//////////////////////////////////
// Client (new/edit) management //
//////////////////////////////////
//id=0&action=new_client&ed_familyname=&ed_christname=&ed_is_copany=yes&ed_company_name=&ed_addr1=&ed_addr2=&ed_addr3=
//&ed_city=&ed_zipcode=&ed_state=&ed_country=AF&ed_phone=&ed_fax=&ed_email=&ed_special_note=&ed_dollar=&
//ed_disk_quota_mb=&ed_gw_quota_per_month_gb=
if($_REQUEST["action"] == "new_client"){
	$q = "INSERT INTO $pro_mysql_client_table(
id,is_company,company_name,
familyname,christname,
addr1,addr2,addr3,
city,zipcode,state,
country,phone,fax,
email,special_note,dollar,
disk_quota_mb,bw_quota_per_month_gb
)VALUES(
'','".$_REQUEST["ed_is_company"]."','".$_REQUEST["ed_company_name"]."',
'".$_REQUEST["ed_familyname"]."','".$_REQUEST["ed_christname"]."',
'".$_REQUEST["ed_addr1"]."','".$_REQUEST["ed_addr2"]."','".$_REQUEST["ed_addr3"]."',
'".$_REQUEST["ed_city"]."','".$_REQUEST["ed_zipcode"]."','".$_REQUEST["ed_state"]."',
'".$_REQUEST["ed_country"]."','".$_REQUEST["ed_phone"]."','".$_REQUEST["ed_fax"]."',
'".$_REQUEST["ed_email"]."','".$_REQUEST["ed_special_note"]."','".$_REQUEST["ed_dollar"]."',
'".$_REQUEST["ed_disk_quota_mb"]."','".$_REQUEST["ed_bw_quota_per_month_gb"]."');";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !!!".mysql_error());
}

//id=0&action=new_client&ed_familyname=&ed_christname=&ed_is_copany=yes&ed_company_name=&ed_addr1=&ed_addr2=&ed_addr3=
//&ed_city=&ed_zipcode=&ed_state=&ed_country=AF&ed_phone=&ed_fax=&ed_email=&ed_special_note=&ed_dollar=&
//ed_disk_quota_mb=&ed_gw_quota_per_month_gb=
if($_REQUEST["action"] == "edit_client"){
	$q = "UPDATE $pro_mysql_client_table SET
is_company='".$_REQUEST["ed_is_company"]."',
company_name='".$_REQUEST["ed_company_name"]."',
familyname='".$_REQUEST["ed_familyname"]."',
christname='".$_REQUEST["ed_christname"]."',
addr1='".$_REQUEST["ed_addr1"]."',
addr2='".$_REQUEST["ed_addr2"]."',
addr3='".$_REQUEST["ed_addr3"]."',
city='".$_REQUEST["ed_city"]."',
zipcode='".$_REQUEST["ed_zipcode"]."',
state='".$_REQUEST["ed_state"]."',
country='".$_REQUEST["ed_country"]."',
phone='".$_REQUEST["ed_phone"]."',
fax='".$_REQUEST["ed_fax"]."',
email='".$_REQUEST["ed_email"]."',
special_note='".$_REQUEST["ed_special_note"]."',
dollar='".$_REQUEST["ed_dollar"]."',
disk_quota_mb='".$_REQUEST["ed_disk_quota_mb"]."',
bw_quota_per_month_gb='".$_REQUEST["ed_bw_quota_per_month_gb"]."'
WHERE id='".$_REQUEST["id"]."' LIMIT 1;";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !!!".mysql_error());
}

}
?>
