<?php

$pro_mysql_product_table = "product";

function DTCRMlistClients(){
	$id_client = $_REQUEST["id"];
	global $pro_mysql_client_table;

	$text .= "<div style=\"white-space: nowrap\" nowrap>
<a href=\"$PHP_SELF?rub=crm&id=0\">New customer</a>
</a><br><br>";

	$query = "SELECT * FROM $pro_mysql_client_table ORDER BY familyname";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		if($row["id"] != $_REQUEST["id"]){
			$text .= "<a href=\"$PHP_SELF?rub=crm&id=".$row["id"]."\">";
		}
		$text .= $row["familyname"].", ".$row["christname"]."";
		if($row["id"] != $_REQUEST["id"]){
			$text .= "</a>";
		}
		$text .= "<br>";
	}
	$text .= "</div>";
	return $text;
}

function DTCRMclientAdmins(){
	global $pro_mysql_client_table;
	global $pro_mysql_admin_table;

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='".$_REQUEST["id"]."'";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
        $n = mysql_num_rows($r);
	$text .= "<br><b><u>Delete an administrator account for this customer:</u></b><br>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($i > 0)
			$text .= " - ";
		$text .= "<a href=\"$PHP_SELF?rub=crm&id=".$_REQUEST["id"]."&action=remove_admin_from_client&adm_name=".$a["adm_login"]."\">".$a["adm_login"]."</a>";
	}

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='0'";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
        $n = mysql_num_rows($r);
	$text .= "<br><br><b><u>Add an existing administrator account for this customer:</u></b><br>";
	$text .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
<input type=\"hidden\" name=\"id\" value=\"".$_REQUEST["id"]."\">
<input type=\"hidden\" name=\"action\" value=\"add_admin_to_client\">
<select name=\"adm_name\">";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($i > 0)
			$text .= " - ";
		$text .= "<option value=\"".$a["adm_login"]."\">".$a["adm_login"]."</option>";
	}
	$text .= "</select><input type=\"submit\" value=\"Ok\"></form>";

	return $text;
}

function DTCRMeditClients(){
	global $pro_mysql_client_table;
	$cid = $_REQUEST["id"];	// current customer id
	if($cid == "")	return "Select a customer.";

	$iscomp_yes = "checked";
	$iscomp_no = "";
	if($cid != 0 && isset($cid) && $cid != ""){
		$query = "SELECT * FROM $pro_mysql_client_table WHERE id='".$_REQUEST["id"]."';";
	        $result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	        $num_rows = mysql_num_rows($result);
		if($num_rows != 1){
			return "<font color=\"red\">Error : no row by that client ID (".$_REQUEST["id"].") !!!</font>";
		}
		$row = mysql_fetch_array($result);
		$hidden_inputs = "<input type=\"hidden\" name=\"action\" value=\"edit_client\">";
		if($row["is_company"] == "no"){
			$iscomp_yes = "";
			$iscomp_no = "checked";
		}
	}else{
		$hidden_inputs = "<input type=\"hidden\" name=\"action\" value=\"new_client\">";
		unset($row);
	}
	$text = "<form action=\"$PHP_SELF\">
<table cellspacin=\"0\" cellpadding=\"0\">
<input type=\"hidden\" name=\"rub\" value=\"crm\">
<input type=\"hidden\" name=\"id\" value=\"$cid\">$hidden_inputs
<tr><td align=\"right\">Family name:</td><td><input size=\"40\" type=\"text\" name=\"ed_familyname\"value=\"".$row["familyname"]."\"></td></tr>
<tr><td align=\"right\">First name:</td><td><input size=\"40\" type=\"text\" name=\"ed_christname\" value=\"".$row["christname"]."\"></td></tr>
<tr><td align=\"right\">Is company:</td><td>
yes<input type=\"radio\" name=\"ed_is_company\" value=\"yes\" $iscomp_yes >
no<input type=\"radio\" name=\"ed_is_company\" value=\"no\" $iscomp_no >
<tr><td align=\"right\">Company name:</td><td><input size=\"40\" type=\"text\" name=\"ed_company_name\" value=\"".$row["company_name"]."\"></td></tr>
<tr><td align=\"right\">Addr1:</td><td><input size=\"40\" type=\"text\" name=\"ed_addr1\" value=\"".$row["addr1"]."\"></td></tr>
<tr><td align=\"right\">Addr2:</td><td><input size=\"40\" type=\"text\" name=\"ed_addr2\" value=\"".$row["addr2"]."\"></td></tr>
<tr><td align=\"right\">Addr3:</td><td><input size=\"40\" type=\"text\" name=\"ed_addr3\" value=\"".$row["addr3"]."\"></td></tr>
<tr><td align=\"right\">City:</td><td><input size=\"40\" type=\"text\" name=\"ed_city\" value=\"".$row["city"]."\"></td></tr>
<tr><td align=\"right\">Zicode:</td><td><input size=\"40\" type=\"text\" name=\"ed_zipcode\" value=\"".$row["zipcode"]."\"></td></tr>
<tr><td align=\"right\">State:</td><td><input size=\"40\" type=\"text\" name=\"ed_state\" value=\"".$row["state"]."\"></td></tr>
<tr><td align=\"right\">Country:</td><td><select name=\"ed_country\">".
cc_code_popup($row["country"])."</select></td></tr>
<tr><td align=\"right\">Phone:</td><td><input size=\"40\" type=\"text\" name=\"ed_phone\" value=\"".$row["phone"]."\"></td></tr>
<tr><td align=\"right\">Fax:</td><td><input size=\"40\" type=\"text\" name=\"ed_fax\" value=\"".$row["fax"]."\"></td></tr>
<tr><td align=\"right\">Email:</td><td><input size=\"40\" type=\"text\" name=\"ed_email\" value=\"".$row["email"]."\"></td></tr>
<tr><td align=\"right\">Notes:</td><td><textarea cols=\"40\" rows=\"5\" name=\"ed_special_note\">".$row["special_note"]."</textarea></td></tr>
<tr><td align=\"right\">Dollar:</td><td><input size=\"40\" type=\"text\" name=\"ed_dollar\" value=\"".$row["dollar"]."\"></td></tr>
<tr><td align=\"right\" style=\"white-space: nowrap\" nowrap>Disk quota (in MB):</td><td><input size=\"40\" type=\"text\" name=\"ed_disk_quota_mb\" value=\"".$row["disk_quota_mb"]."\"></td></tr>
<tr><td align=\"right\" style=\"white-space: nowrap\" nowrap>Allowed transfer per month (in GB):</td><td><input size=\"40\" type=\"text\" name=\"ed_bw_quota_per_month_gb\" value=\"".$row["bw_quota_per_month_gb"]."\"></td></tr>
<tr><td align=\"right\"></td><td><input type=\"submit\" value=\"Save\"></td></tr>
</table>
</form>";
	return $text;
}

function DTCRMshowClientCommands($cid){
	global $pro_mysql_client_table;
	global $pro_mysql_product_table;
	global $pro_mysql_command_table;

	$query = "SELECT * FROM $pro_mysql_command_table WHERE id_client='$cid'";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error);
	$num_rows = mysql_num_rows($result);
	$text .= "<br><table border=\"1\"><tr>
<td>What</td>
<td>Domain</td>
<td>Price</td>
<td>Quantity</td>
<td>Date</td>
<td>Expiration</td>
<td>Action</td></tr>";
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		$query2 = "SELECT * FROM $pro_mysql_product_table WHERE id='".$row["product_id"]."';";
		$result2 = mysql_query($query2)or die("Cannot query \"$query2\" !!!".mysql_error);
		$row2 = mysql_fetch_array($result2);
		if(($i % 2) == 0) $color = " bgcolor=\"#000000\" "; else $color = "";
		$text .= "<tr>
<td $color>".$row2["name"]."</td>
<td $color>".$row["domain_name"]."</td>
<td $color>".$row["price"]."</td>
<td $color>".$row["quantity"]."</td>
<td $color><input type=\"text\" size=\"10\" name=\"cmd_date\" value=\"".$row["date"]."\"></td>
<td $color><input type=\"text\" size=\"10\" name=\"cmd_expir\" value=\"".$row["expir"]."\"></td>
<td><input type=\"submit\" name=\"ed_command\" value=\"Save\"><input type=\"submit\" name=\"del_command\" value=\"Del\"></td>
	</tr>";
	}
	$text .= "</table>";

	$text .= "<br><br><form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"rub\" value=\"crm\">
<input type=\"hidden\" name=\"id\" value=\"$cid\">
<select name=\"add_new_command\">";
	$query = "SELECT * FROM $pro_mysql_product_table ORDER BY name";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error);
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		$text .= "<option value=\"".$row["id"]."\">".$row["name"]."</option>";
	}

	$text .= "</select><br>
Domain name:<input type=\"text\" name=\"add_newcmd_domain_name\" value=\"\"><input type=\"submit\" value=\"Add\">
</form>";

	return $text;
}

?>
