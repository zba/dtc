<?php

function checkLoginPassSubmitToSQL(){
	global $adm_login;
	global $adm_pass;
	global $pro_mysql_admin_table;

	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR adm_pass=SHA1('$adm_pass'));";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)      die("User or password is incorrect !");
}

/////////////////////
// Account manager //
/////////////////////
// https://dtc.gplhost.com/dtc/?adm_login=dianflon&adm_pass=dec0lease&addrlink=myaccount&action=refund&refund_amount=12
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "refund"){
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR adm_pass=SHA1('$adm_pass'));";
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

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "registry_renew_domain"){
	checkLoginPassAndDomain($adm_login,$adm_pass,$edit_domain);
	if(!isRandomNum($_REQUEST["num_years"]) || strlen($_REQUEST["num_years"]) != 1){
		echo _("Number of years is not a number between 1 and 9.");
	}else{
		$q = "SELECT id_client FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("ID client not found line ".__LINE__." file ".__FILE__);
		}
		$admin = mysql_fetch_array($r);
		$id_client = $admin["id_client"];
		$q = "SELECT * FROM $pro_mysql_client_table WHERE id='$id_client';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("Client record not found line ".__LINE__." file ".__FILE__);
		}
		$client = mysql_fetch_array($r);
		$tld = find_domain_extension($edit_domain);
		if($tld === FLASE){
			die("Domain TLD not found line ".__LINE__." file ".__FILE__);
		}
		$price = find_domain_price($tld);
		if($price === FALSE){
			die("TLD price not found line ".__LINE__." file ".__FILE__);
		}
		$price = $_REQUEST["num_years"] * $price;
		$remaining = $client["dollar"] - $price;
		if($remaining < 0){
			die("Not enough money on the account line ".__LINE__." file ".__FILE__);
		}
		$renew_return = registry_renew_domain($edit_domain,$_REQUEST["num_years"]);
		// If renew successful, remove some money from the account, and update expiration date of the domain
		if($renew_return["attributes"]["status"] == 0){
			$q = "UPDATE $pro_mysql_client_table SET dollar='$remaining' WHERE id='$id_client';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$q = "UPDATE $pro_mysql_domain_table SET expiration_date=DATE_ADD(expiration_date, INTERVAL ".$_REQUEST["num_years"]." YEAR) WHERE name='$edit_domain';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		}
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_one_domain" && isset($_REQUEST["confirm_delete"]) && $_REQUEST["confirm_delete"] == "yes"){
	if(!isHostnameOrIP($_REQUEST["to_delete_domain_name"])){
		die("Not a domain name");
	}
	checkLoginPassAndDomain($adm_login,$adm_pass,$_REQUEST["to_delete_domain_name"]);
	deleteUserDomain($adm_login,$adm_pass,$_REQUEST["to_delete_domain_name"],true);
	$adm_query = "UPDATE $pro_mysql_cronjob_table SET qmail_newu='yes',restart_qmail='yes',reload_named='yes',restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes',gen_ssh='yes' WHERE 1;";
	mysql_query($adm_query);
	triggerDomainListUpdate();
}

?>
