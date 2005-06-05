<?php

function newWhois($domain_name,$owner_id,$billing_id,$admin_id,$period,$ns_ar){
	global $pro_mysql_whois_table;
	global $pro_mysql_domain_table;

	$y = date("Y");
	$m = date("m");
	$d = date("d");

	$now = $y."-".$m."-".$d;
	$expir = ($period + $y)."-".$m."-".$d;

	for($i=2;$i<sizeof($ns_ar);$i++){
		$ns_field .= ",ns".$i." ";
		$ns_values .= ",'" . $ns_ar[$i] . "'";
	}

	$query = "INSERT INTO $pro_mysql_whois_table(
domain_name,owner_id,admin_id,billing_id,
creation_date,modification_date,expiration_date,registrar,
ns1,ns2". $ns_field .")VALUES('$domain_name','$owner_id','$billing_id','$admin_id',
'$now','$now','$expir','tucows',
'".$ns_ar[1]."','".$ns_ar[2]."'". $ns_values .");";
	$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());

	$query = "UPDATE $pro_mysql_domain_table SET whois='here' WHERE name='$domain_name';";
	$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
}

function drawAdminTools_Whois($admin,$eddomain){
	global $lang;
	global $PHP_SELF; 
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $pro_mysql_handle_table;
	global $pro_mysql_whois_table;

	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;

	global $txt_dtcrm_here;
	global $txt_dtcrm_your_domain_has_been_registred_elsewhere;
	global $txt_dtcrm_your_domain_name_whois_data;
	global $txt_dtcrm_secondary_dns;
	global $txt_dtcrm_primary_dns;
	global $txt_dtcrm_if_you_want_to_keep_your_current_registrar;
	global $txt_dtcrm_your_domain_has_been_registred_elsewhere;
	global $lang;

	$domain_name = $eddomain["name"];

	$out = "";
	if($eddomain["whois"] == "away"){
		if(isset($_REQUEST["dtcrm_action"]) && $_REQUEST["dtcrm_action"] == "transfer_domain"){
			$out .= drawNameTransfer($admin,$domain_name);
		}else{
			$out .= $txt_dtcrm_your_domain_has_been_registred_elsewhere[$lang]."
<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&add_domain_type=domregandhosting&dtcrm_action=transfer_domain\">".$txt_dtcrm_here[$lang]."</a>.<br><br>
".$txt_dtcrm_if_you_want_to_keep_your_current_registrar[$lang]."<br><br>
".$txt_dtcrm_primary_dns[$lang]."<b>$conf_addr_primary_dns</b><br>
".$txt_dtcrm_secondary_dns[$lang]."<b>$conf_addr_secondary_dns</b>
";
		}
	}else{
		if($_REQUEST["action"] == "update_whois_infoz"){
		        $owner_id = $_REQUEST["dtcrm_owner_hdl"];
		        $billing_id = $_REQUEST["dtcrm_billing_hdl"];
		        $admin_id = $_REQUEST["dtcrm_admin_hdl"];

		        $query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$owner_id' AND owner='$adm_login';";
			$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		        if(mysql_num_rows($result) != 1)        die("Handle ID not found !");
		        $contacts["owner"] = mysql_fetch_array($result)or die("Cannot fetch array !");

		        $query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$billing_id' AND owner='$adm_login';";
			$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		        if(mysql_num_rows($result) != 1)        die("Handle ID not found !");
		        $contacts["billing"] = mysql_fetch_array($result)or die("Cannot fetch array !");

		        $query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$admin_id' AND owner='$adm_login';";
		        $result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		        if(mysql_num_rows($result) != 1)        die("Handle ID not found !");
		        $contacts["admin"] = mysql_fetch_array($result)or die("Cannot fetch array !");

			$regz = registry_update_whois_infoz($adm_login,$adm_pass,$domain_name,$contacts);

			if($regz["is_success"] != 1){
				$out .= "<font color=\"red\"><b>Update of whois contact informations failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
			}else{
				$out .= "<font color=\"green\"><b>Update of whois contact informations succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>
";
				$query = "UPDATE $pro_mysql_whois_table SET owner_id='$owner_id',billing_id='$billing_id',admin_id='$admin_id' WHERE domain_name='$domain_name';";
				$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		        }
		}

		$query = "SELECT * FROM $pro_mysql_whois_table WHERE domain_name='".$eddomain["name"]."';";
		$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) != 1) die("Whois row not found !");
		$row = mysql_fetch_array($result);
		$out .= "<b><u>".$txt_dtcrm_your_domain_name_whois_data[$lang]."</u></b><br>
";

		$out .= "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"update_whois_infoz\">
";
		$out .= whoisHandleSelection($admin,"yes",$row["owner_id"],$row["billing_id"],$row["admin_id"]);
		$out .= "<input type=\"submit\" value=\"Ok\"></form>";
	}

	return $out;
}

?>
