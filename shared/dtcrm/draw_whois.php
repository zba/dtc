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

	$query = "UPDATE $pro_mysql_domain_table SET whois='here' WHERE name='$domain_name';";
	$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
}

function drawNameTransfer($admin,$given_fqdn="none"){
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $form_enter_domain_name;
	global $form_enter_dns_infos;
	global $whois_forwareded_params;

	$toreg_domain = $_REQUEST["toreg_domain"];
	$toreg_extention = $_REQUEST["toreg_extention"];
	if($given_fqdn != "none" && !isset($toreg_extention)){
		$c = strrpos($given_fqdn,".");
		$toreg_extention = substr($given_fqdn,$c);
		$toreg_domain = substr($given_fqdn,0,$c);
	}
//echo $toreg_domain."<br>";
//echo $toreg_extention."<br>";

// $_REQUEST["add_domain_type"]

	$form_start = "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"dtcrm_action\" value=\"transfer_domain\">
<input type=\"hidden\" name=\"add_regortrans\" value=\"transfer\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
";
//	registry_check_transfer($domain)
	$out .= "<b><u>Transfer ".$eddomain["name"]." from
another registrar to GPLHost:</u></b><br>
<i><u>Step1: check if domain is transferable</u></i>";

	if(!isset($toreg_extention) || $toreg_extention == "" ||
	!isset($toreg_domain) || $toreg_domain == "" ||
	($toreg_extention != ".com" && $toreg_extention != ".net" &&
	$toreg_extention != ".org" && $toreg_extention != ".biz" &&
	$toreg_extention != ".name" && $toreg_extention != ".info")		){
		$out .= "$form_start<br>
Please enter the domain name you wish to transfer:
$form_enter_domain_name";
		return $out;
	}

	$form_start .= "<input type=\"hidden\" name=\"toreg_domain\" value=\"$toreg_domain\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"$toreg_extention\">";

	$regz = registry_check_transfer($toreg_domain.$toreg_extention);
	if($regz["is_success"] != 1){
		die("<font color=\"red\">TRANSFER CHECK FAILED: registry server didn't reply
successfuly.</font>");
	}
	$regz["attributes"]["transferrable"] = 1;
	if($regz["attributes"]["transferrable"] != 1){
		$out .= "<font color=\"red\">TRANSFER CHECK FAILED</font><br>
Server said: ".$regz["attributes"]["reason"]."<br>
$form_start<input type=\"submit\" value=\"Go back\"></form>";
		return $out;
	}
	$out .= "<br><font color=\"green\">TRANSFER CHECK SUCCESSFULL</font><br>
Server said: ".$regz["attributes"]["reason"]."<br><br>
<i><u>Step2: select contacts for domain transfer</u></i><br>
";
	if(	!isset($_REQUEST["dtcrm_owner_hdl"]) || $_REQUEST["dtcrm_owner_hdl"] == "" ||
		!isset($_REQUEST["dtcrm_admin_hdl"]) || $_REQUEST["dtcrm_admin_hdl"] == "" ||
		!isset($_REQUEST["dtcrm_billing_hdl"]) || $_REQUEST["dtcrm_billing_hdl"] == "" ||
		!isset($_REQUEST["toreg_dns1"]) || $_REQUEST["toreg_dns1"] == "" ||
		!isset($_REQUEST["toreg_dns2"]) || $_REQUEST["toreg_dns2"] == ""){
		$out .= $form_start . whoisHandleSelection($admin);
		$out .= $form_enter_dns_infos;
		$out .= "<input type=\"submit\" value=\"Proceed to transfer\"></form>";
		return $out;
	}
	$form_start .= $whois_forwareded_params;
		
	$out .= "DNS1: ".$_REQUEST["toreg_dns1"]."<br>";
	$out .= "DNS2: ".$_REQUEST["toreg_dns2"]."<br><br>";

	$fqdn = $toreg_domain . $toreg_extention;
	$price = registry_get_domain_price($fqdn,$_REQUEST["toreg_period"]);
	$fqdn_price = $price["attributes"]["price"];
	$fqdn_price += $_REQUEST["toreg_period"] * 2;

	if($admin["info"]["id_client"] != 0){
		$remaining = $admin["client"]["dollar"];
	}else{
		$out .= "You don't have a client ID. Please contact us.<br>";
		$remaining = 0;
		return $out;
	}

	$out .= "<i><u>Step3: Proceed for transfer</u></i><br>";
	$out .= "
Remaining on your account: \$" . $remaining . "<br>
Total price: \$". $fqdn_price . "<br><br>";

	if($fqdn_price > $remaining){
		$out .= "
You currently don't have enough funds on your account. You will be
redirected to our paiement system.<br><br>
$form_start<input type=\"submit\" value=\"Proceed to paiement\">
</form>";
		return $out;
	}
	$out .= "$form_start
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
<input type=\"submit\" value=\"Proceed transfer\">
</form>
";
	return $out;
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

	$domain_name = $eddomain["name"];

	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";
	if($eddomain["whois"] == "away"){
		if($_REQUEST["dtcrm_action"] == "transfer_domain"){
			$out .= drawNameTransfer($admin,$domain_name);
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
