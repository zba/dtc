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

////////////////////////////
// Nick-handle management //
////////////////////////////
// adm_login=zigo&adm_pass=XXX&action=create_nickhandle
// &hdl_id=&addrlink=nickhandles&
// name=a&company=aa&firstname=b&lastname=c&addr1=d&addr2=e&addr3=f&state=g&country=h&zipcode=i&phone_num=j&fax_num=k&email=l
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "create_nickhandle"){
	checkLoginPassSubmitToSQL();
		
	if($_REQUEST["name"] == "" ||
		$_REQUEST["addr1"] == "" ||
		$_REQUEST["city"] == "" ||
		$_REQUEST["state"] == "" ||
		$_REQUEST["country"] == "" ||
		$_REQUEST["zipcode"] == "" ||
		$_REQUEST["phone_num"] == "" ||
		$_REQUEST["email"] == "" ||
		!isset($_REQUEST["name"]) ||
		!isset($_REQUEST["addr1"]) ||
		!isset($_REQUEST["city"]) ||
		!isset($_REQUEST["state"]) ||
		!isset($_REQUEST["country"]) ||
		!isset($_REQUEST["zipcode"]) ||
		!isset($_REQUEST["phone_num"]) ||
		!isset($_REQUEST["email"])){
			echo $_REQUEST["city"]."<br>";
			echo $_REQUEST["state"]."<br>";
			die("One of the required field is umpty !!!");
	}

	$query =" INSERT INTO $pro_mysql_handle_table(
id,name,owner,
company,firstname,lastname,
addr1,addr2,addr3,city,
state,country,zipcode,
phone_num,fax_num,email) VALUES (
'','".$_REQUEST["name"]."','$adm_login',
'".$_REQUEST["company"]."','".$_REQUEST["firstname"]."','".$_REQUEST["lastname"]."',
'".$_REQUEST["addr1"]."','".$_REQUEST["addr2"]."','".$_REQUEST["addr3"]."','".$_REQUEST["city"]."',
'".$_REQUEST["state"]."','".$_REQUEST["country"]."','".$_REQUEST["zipcode"]."',
'".$_REQUEST["phone_num"]."','".$_REQUEST["fax_num"]."','".$_REQUEST["email"]."');";
	//echo $query;
	mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "update_nickhandle"){
	checkLoginPassSubmitToSQL();
		
	if($_REQUEST["name"] == "" ||
		$_REQUEST["addr1"] == "" ||
		$_REQUEST["city"] == "" ||
		$_REQUEST["state"] == "" ||
		$_REQUEST["country"] == "" ||
		$_REQUEST["zipcode"] == "" ||
		$_REQUEST["phone_num"] == "" ||
		$_REQUEST["email"] == "" ||
		$_REQUEST["hdl_id"] == "" ||
		!isset($_REQUEST["name"]) ||
		!isset($_REQUEST["addr1"]) ||
		!isset($_REQUEST["city"]) ||
		!isset($_REQUEST["state"]) ||
		!isset($_REQUEST["country"]) ||
		!isset($_REQUEST["zipcode"]) ||
		!isset($_REQUEST["phone_num"]) ||
		!isset($_REQUEST["email"]) ||
		!isset($_REQUEST["hdl_id"])){
			die("One of the required field is umpty !!!");
	}

	$query =" UPDATE $pro_mysql_handle_table SET
name='".$_REQUEST["name"]."',
company='".$_REQUEST["company"]."',
firstname='".$_REQUEST["firstname"]."',
lastname='".$_REQUEST["lastname"]."',
addr1='".$_REQUEST["addr1"]."',
addr2='".$_REQUEST["addr2"]."',
addr3='".$_REQUEST["addr3"]."',
city='".$_REQUEST["city"]."',
state='".$_REQUEST["state"]."',
country='".$_REQUEST["country"]."',
zipcode='".$_REQUEST["zipcode"]."',
phone_num='".$_REQUEST["phone_num"]."',
fax_num='".$_REQUEST["fax_num"]."',
email='".$_REQUEST["email"]."'
WHERE id='".$_REQUEST["hdl_id"]."' AND owner='$adm_login' LIMIT 1;";
	//echo $query;
	mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
}

?>
