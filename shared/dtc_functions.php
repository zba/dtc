<?php

////////////////////////////////////////////////////////////////////////////////////
// Verify that someone is not trying to modify another account (nasty hacker !!!) //
// Fetch the admin real path stored in the database
//
////////////////////////////////////////////////////////////////////////////////////
function checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name){
	global $pro_mysql_admin_table;
	global $pro_mysql_domain_table;
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)      die("User or password is incorrect !");

	$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login' AND name='$domain_name';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("Cannot update DNS or MX the user does not own the domain name !");
}

?>
