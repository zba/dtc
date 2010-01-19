<?php

function newWhois($domain_name,$owner_id,$billing_id,$admin_id,$teck_id,$period,$ns_ar,$registrar){
	global $pro_mysql_whois_table;
	global $pro_mysql_domain_table;

	$y = date("Y");
	$m = date("m");
	$d = date("d");

	$now = $y."-".$m."-".$d;
	$expir = ($period + $y)."-".$m."-".$d;

	$ns_field = "";
	$ns_values = "";
	for($i=2;$i<sizeof($ns_ar);$i++){
		$ind = $i+1;
		$ns_field .= ",ns". $ind ." ";
		$ns_values .= ",'" . $ns_ar[$i] . "'";
	      
	}

	$query = "INSERT INTO $pro_mysql_whois_table(
domain_name,owner_id,admin_id,billing_id,teck_id,
creation_date,modification_date,expiration_date,registrar,
ns1,ns2". $ns_field .")VALUES('$domain_name','$owner_id','$billing_id','$admin_id','$teck_id',
'$now','$now','$expir','$registrar',
'".$ns_ar[0]."','".$ns_ar[1]."'". $ns_values .");";
	$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());

	$query = "UPDATE $pro_mysql_domain_table SET whois='here' WHERE name='$domain_name';";
	$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
}

function drawAdminTools_Whois($admin,$eddomain){
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $pro_mysql_handle_table;
	global $pro_mysql_whois_table;

	global $conf_addr_primary_dns;
	global $conf_addr_secondary_dns;

	$domain_name = $eddomain["name"];

	$out = "";
	if($eddomain["whois"] == "away"){
		if(isset($_REQUEST["dtcrm_action"]) && $_REQUEST["dtcrm_action"] == "transfer_domain"){
			$out .= drawNameTransfer($admin,$domain_name);
		}else{
			$out .= _("Your domain name has been registred elsewhere (i.e. not on this site). To order its transfer, please click ") .
"<a href=\"". $_SERVER["PHP_SELF"] ."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&add_domain_type=domregandhosting&dtcrm_action=transfer_domain\">". _("here") ."</a>.<br><br>
". _("If you want to keep your current registrar, you have to make the whois point to these DNS:") ."<br><br>
". _("Primary DNS:") ."<b>$conf_addr_primary_dns</b><br>
". _("Secondary DNS:") ."<b>$conf_addr_secondary_dns</b>
";
		}
	}else{
		if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "update_whois_infoz"){
		        $owner_id = $_REQUEST["dtcrm_owner_hdl"];
		        $billing_id = $_REQUEST["dtcrm_billing_hdl"];
		        $admin_id = $_REQUEST["dtcrm_admin_hdl"];
		        $teck_id = $_REQUEST["dtcrm_teck_hdl"];

			if( !isRandomNum($owner_id) || !isRandomNum($billing_id) || !isRandomNum($admin_id) || !isRandomNum($teck_id) ){
				die("Admin contact is not a number: exiting!");
			}

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

		        $query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$teck_id' AND owner='$adm_login';";
		        $result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		        if(mysql_num_rows($result) != 1)        die("Handle ID not found !");
		        $contacts["teck"] = mysql_fetch_array($result)or die("Cannot fetch array !");

			$regz = registry_update_whois_info($adm_login,$adm_pass,$domain_name,$contacts);

			if($regz["is_success"] != 1){
				$out .= "<font color=\"red\"><b>". _("Update of whois contact informations failed"). "</b></font><br>
".("Server said:")." <i>" . $regz["response_text"] . "</i><br>";
			}else{
				$out .= "<font color=\"green\"><b>". _("Update of whois contact informations succesfull")."</b></font><br>
"._("Server said:")." <i>" . $regz["response_text"] . "</i><br>
";
				$query = "UPDATE $pro_mysql_whois_table SET owner_id='$owner_id',billing_id='$billing_id',admin_id='$admin_id',teck_id='$teck_id' WHERE domain_name='$domain_name';";
				$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		        }
		}

		$query = "SELECT * FROM $pro_mysql_whois_table WHERE domain_name='".$eddomain["name"]."';";
		$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) != 1) die("Whois row not found !");
		$row = mysql_fetch_array($result);
		$out .= "<b><u>". _("Your domain name whois data:") ."</u></b><br>
";

		$out .= "<form action=\"". $_SERVER["PHP_SELF"] ."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"update_whois_infoz\">
";
		$out .= whoisHandleSelection($admin,"yes",$row["owner_id"],$row["billing_id"],$row["admin_id"],$row["teck_id"]);
		$out .= submitButtonStart(). _("Update whois") .submitButtonEnd()."</form>";
	}

	$out .= "<br><br>" . _("The current whois for this domain is as follow:") . "<br>";
	$ret = registry_get_whois($domain_name);
//	print_r($ret);
	$out .= nl2br($ret["response_text"]);

	return $out;
}

?>
