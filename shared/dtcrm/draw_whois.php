<?php

function newWhois($domain_name,$owner_id,$billing_id,$admin_id,$period,$ns_ar){
	global $pro_mysql_whois_table;

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
}

function drawNameTransfer($given_fqdn="none"){
	global $form_enter_domain_name;
	$toreg_domain = $_REQUEST["toreg_domain"];
	$toreg_extention = $_REQUEST["toreg_extention"];
	$form_start = "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"dtcrm_action\" value=\"transfer_domain\">
";
//	registry_check_transfer($domain)
	$out .= "<b><u>Transfer ".$eddomain["name"]." from
another registrar to GPLHost:</u></b><br><br>
<i><u>Step1: check if domain is transferable</u></i>
";
	if($given_fqdn != "none" && !isset($toreg_extention)){
		$c = strrpos($given_fqdn);
		$toreg_extention = substr($given_fqdn,$c);
		$toreg_domain = substr($given_fqdn,1,strlen($given_fqdn)-$c);
	}
	if(isset($domain_extention) && $domain_extention != "" &&
	isset($domain_name) && $domain_name != ""){
		if($domain_extention != ".com" || $domain_extention != ".net" ||
		$domain_extention != ".org" || $domain_extention != ".biz" ||
		$domain_extention != ".name" || $domain_extention != ".info")
			die("Domain extention registraion not open for $domain_extention");
		$out .= $form_start;
		$out .= whoisHandleSelection($admin);
	}else{
		$out .= "$form_start$form_enter_domain_name";
	}
	$out .= "<input type=\"submit\" value=\"Ok\">
</form>";
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

	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";
	if($eddomain["whois"] == "away"){
		if($_REQUEST["dtcrm_action"] == "transfer_domain"){
			$out .= drawNameTransfer();
		}else{
			$out .= "Your domain name has been registred elsewhere (eg
not on this site). To order for it's transfer and management, please click
<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&dtcrm_action=transfer_domain\">here</a>.<br><br>
If you want to keep your current registrar, you have to make the whois point
to thoses DNS:<br><br>
Primary DNS: <b>$conf_addr_primary_dns</b><br>
Secondary DNS: <b>$conf_addr_secondary_dns</b>
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

			$domain_name = $eddomain["name"];
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
		$out .= "<b><u>Your domain name whois data:</u></b><br>
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
