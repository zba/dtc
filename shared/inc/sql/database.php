<?php

///////////////////////////
// MySQL password change //
///////////////////////////
if(isset($_REQUEST["change_mysql_password"]) && $_REQUEST["change_mysql_password"] == "Ok"){
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("User or password is incorrect !");

	if(!isDTCPassword($_REQUEST["new_mysql_password"])){
		die("Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.");
	}

	mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
	$query = "UPDATE user SET Password=PASSWORD('".$_REQUEST["new_mysql_password"]."') WHERE User='$adm_login';";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	mysql_query("FLUSH PRIVILEGES");
	mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\" !!!");
}


?>
