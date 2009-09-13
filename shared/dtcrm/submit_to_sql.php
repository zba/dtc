<?php

function checkLoginPassSubmitToSQL(){
	global $adm_login;
	global $adm_pass;
	global $pro_mysql_admin_table;

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)      die("User or password is incorrect !");
}

/////////////////////
// Account manager //
/////////////////////
// https://dtc.gplhost.com/dtc/?adm_login=dianflon&adm_pass=dec0lease&addrlink=myaccount&action=refund&refund_amount=12
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "refund"){
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)      die("User or password is incorrect !");
	$row = mysql_fetch_array($result);
	$id_client = $row["id_client"];
	if($id_client != 0){
		$query = "SELECT * FROM $pro_mysql_client_table WHERE id='$id_client';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows != 1)	die("Client id not found in client table !");
		$row = mysql_fetch_array($result);
		$funds = $row["dolar"];
		$funds += $_REQUEST["refund_amount"];
		$query = "UPDATE $pro_mysql_client_table SET dolar='$funds' WHERE id='$id_client';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	}else{
		die("You don't have a client ID !!!");
	}
}

?>
