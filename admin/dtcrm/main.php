<?php

$pro_mysql_product_table = "product";

function DTCRMlistClients(){
	global $lang;
	global $txt_new_customer_link;
	if(isset($_REQUEST["id"]))
		$id_client = $_REQUEST["id"];
	global $pro_mysql_client_table;

	$text = "<div style=\"white-space: nowrap\" nowrap>
<a href=\"".$_SERVER["PHP_SELF"]."?rub=crm&id=0\">".$txt_new_customer_link[$lang]."</a>
</a><br><br>";

	$query = "SELECT * FROM $pro_mysql_client_table ORDER BY familyname";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	$client_list = array();
	if(isset($id_client) && $_REQUEST["id"] == 0){
		$selected = "yes";
	}else{
		$selected = "no";
	}
	$client_list[] = array(
		"text" => $txt_new_customer_link[$lang],
		"link" => $_SERVER["PHP_SELF"]."?rub=crm&id=0",
		"selected" => $selected);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		if(!isset($id_client) || $row["id"] != $_REQUEST["id"]){
			$text .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=crm&id=".$row["id"]."\">";
			$url = $_SERVER["PHP_SELF"]."?rub=crm&id=".$row["id"];
			$selected = "no";
		}else{
			$url = $_SERVER["PHP_SELF"]."?rub=crm&id=".$row["id"];
			$selected = "yes";
		}
		if($row["is_company"] == "yes"){
			if(strlen($row["company_name"]) > 15){
				$company = substr($row["company_name"],0,14)."...: ";
			}else{
				$company = $row["company_name"].": ";
			}
		}else{
			$company = "";
		}
		$text .= $company.$row["familyname"].", ".$row["christname"]."";
		if(!isset($id_client) || $row["id"] != $_REQUEST["id"]){
			$text .= "</a>";
		}
		$text .= "<br>";
		$client_list[] = array(
			"text" => $company.$row["familyname"].", ".$row["christname"],
			"link" => $url,
			"selected" => $selected);
	}
	$text .= "</div>";
	if(function_exists("skin_DisplayClientList")){
		return skin_DisplayClientList($client_list);
	}else{
		return $text;
	}
}

function DTCRMclientAdmins(){
	global $pro_mysql_client_table;
	global $pro_mysql_admin_table;

	global $lang;
	global $txt_remove_admin_from_client;
	global $txt_add_admin_to_client;

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='".$_REQUEST["id"]."'";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
        $n = mysql_num_rows($r);
	$text = "<br><h3>".$txt_remove_admin_from_client[$lang]."</h3><br>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($i > 0)
			$text .= " - ";
		$text .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=crm&id=".$_REQUEST["id"]."&action=remove_admin_from_client&adm_name=".$a["adm_login"]."\">".$a["adm_login"]."</a>";
	}

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='0'";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
        $n = mysql_num_rows($r);
	$text .= "<br><br><h3>".$txt_add_admin_to_client[$lang]."</h3><br>";
	$text .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td><input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
<input type=\"hidden\" name=\"id\" value=\"".$_REQUEST["id"]."\">
<input type=\"hidden\" name=\"action\" value=\"add_admin_to_client\">
<select name=\"adm_name\">";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($i > 0)
			$text .= " - ";
		$text .= "<option value=\"".$a["adm_login"]."\">".$a["adm_login"]."</option>";
	}
	$text .= "</select></td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr></table></form>";

	return $text;
}

function DTCRMeditClients(){
	global $pro_mysql_client_table;

	global $lang;
	global $txt_draw_client_info_familyname;
	global $txt_draw_client_info_firstname;
	global $txt_draw_client_info_comp_name;
	global $txt_domain_tbl_config_quotaMB;
	global $txt_draw_client_info_addr;
	global $txt_draw_client_info_zipcode;
	global $txt_draw_client_info_country;
	global $txt_draw_client_info_city;
	global $txt_draw_client_info_state;
	global $txt_draw_client_info_phone;
	global $txt_draw_client_info_fax;
	global $txt_draw_client_info_email;
	global $txt_vat_number;
	global $txt_is_company;
	global $txt_allowed_data_transferGB;	
	global $txt_notes;
	global $txt_money_remaining;
	global $txt_select_a_new;

	if(isset($_REQUEST["id"])){
		$cid = $_REQUEST["id"];	// current customer id
	}else{
		return $txt_select_a_new[$lang];
	}

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
		$row["familyname"] = "";
		$row["christname"] = "";
		$row["company_name"] = "";
		$row["vat_num"] = "";
		$row["addr1"] = "";
		$row["addr2"] = "";
		$row["addr3"] = "";
		$row["city"] = "";
		$row["zipcode"] = "";
		$row["state"] = "";
		$row["country"] = "us";
		$row["phone"] = "+";
		$row["fax"] = "";
		$row["email"] = "";
		$row["special_note"] = "";
		$row["dollar"] = "";
		$row["disk_quota_mb"] = "80";
		$row["bw_quota_per_month_gb"] = "1";
	}
	if(isset($row["special_note"])){
		$specnot = $row["special_note"];
	}else{
		$specnot = "";
	}
	$text = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"rub\" value=\"crm\">
<input type=\"hidden\" name=\"id\" value=\"$cid\">$hidden_inputs
";
	$text .= dtcFormTableAttrs();
	$text .= dtcFormLineDraw($txt_draw_client_info_familyname[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_familyname\"value=\"".$row["familyname"]."\">");
	$text .= dtcFormLineDraw($txt_draw_client_info_firstname[$lang],"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_christname\" value=\"".$row["christname"]."\">",0);
	$text .= dtcFormLineDraw($txt_is_company[$lang],"<input type=\"radio\" name=\"ed_is_company\" value=\"yes\" $iscomp_yes > "._("Yes")."
<input type=\"radio\" name=\"ed_is_company\" value=\"no\" $iscomp_no > "._("No"));
	$text .= dtcFormLineDraw($txt_draw_client_info_comp_name[$lang],"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_company_name\" value=\"".$row["company_name"]."\">",0);
	$text .= dtcFormLineDraw($txt_vat_number[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_vat_num\" value=\"".$row["vat_num"]."\">");
	$text .= dtcFormLineDraw($txt_draw_client_info_addr[$lang],"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_addr1\" value=\"".$row["addr1"]."\">",0);
	$text .= dtcFormLineDraw("2:","<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_addr2\" value=\"".$row["addr2"]."\">");
	$text .= dtcFormLineDraw("3:","<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_addr3\" value=\"".$row["addr3"]."\">",0);
	$text .= dtcFormLineDraw($txt_draw_client_info_city[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_city\" value=\"".$row["city"]."\">");
	$text .= dtcFormLineDraw($txt_draw_client_info_zipcode[$lang],"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_zipcode\" value=\"".$row["zipcode"]."\">",0);
	$text .= dtcFormLineDraw($txt_draw_client_info_state[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_state\" value=\"".$row["state"]."\">");
	$text .= dtcFormLineDraw($txt_draw_client_info_country[$lang],"<select class=\"dtcDatagrid_input_alt_color\" name=\"ed_country\">".
cc_code_popup($row["country"])."</select>",0);
	$text .= dtcFormLineDraw($txt_draw_client_info_phone[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_phone\" value=\"".$row["phone"]."\">");
	$text .= dtcFormLineDraw($txt_draw_client_info_fax[$lang],"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_fax\" value=\"".$row["fax"]."\">",0);
	$text .= dtcFormLineDraw($txt_draw_client_info_email[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_email\" value=\"".$row["email"]."\">");
	$text .= dtcFormLineDraw($txt_notes[$lang],"<textarea class=\"dtcDatagrid_input_alt_color\" cols=\"40\" rows=\"5\" name=\"ed_special_note\">".$specnot."</textarea>",0);
	$text .= dtcFormLineDraw($txt_money_remaining[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_dollar\" value=\"".$row["dollar"]."\">");
	$text .= dtcFormLineDraw($txt_domain_tbl_config_quotaMB[$lang],"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_disk_quota_mb\" value=\"".$row["disk_quota_mb"]."\">",0);
	$text .= dtcFormLineDraw($txt_allowed_data_transferGB[$lang],"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_bw_quota_per_month_gb\" value=\"".$row["bw_quota_per_month_gb"]."\">");

	$text .= "
<tr><td align=\"right\"></td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"Save\"></div>
 <div class=\"input_btn_right\"></div>
</div>
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"del\" value=\"Del\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr>
</table>
</form>";
	return $text;
}

function DTCRMshowClientCommands($cid){
	global $pro_mysql_client_table;
	global $pro_mysql_product_table;
	global $pro_mysql_command_table;

	global $lang;
	global $txt_what;
	global $txt_price;
	global $txt_quantity;
	global $txt_date;
	global $txt_expiration_date;
	global $txt_action;
	global $txt_domain_tbl_config_dom_name;

	$query = "SELECT * FROM $pro_mysql_command_table WHERE id_client='$cid'";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error);
	$num_rows = mysql_num_rows($result);
	$text = "<br><table border=\"1\"><tr>
<td>".$txt_what[$lang]."</td>
<td>".$txt_domain_tbl_config_dom_name[$lang]."</td>
<td>".$txt_price[$lang]."</td>
<td>".$txt_quantity[$lang]."</td>
<td>".$txt_date[$lang]."</td>
<td>".$txt_expiration_date[$lang]."</td>
<td>".$txt_action[$lang]."</td></tr>";
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		$query2 = "SELECT * FROM $pro_mysql_product_table WHERE id='".$row["product_id"]."';";
		$result2 = mysql_query($query2)or die("Cannot query \"$query2\" !!!".mysql_error);
		$row2 = mysql_fetch_array($result2);
		if(($i % 2) == 0) $color = " bgcolor=\"#777777\" "; else $color = "";
		$text .= "<tr>
<td $color>".$row2["name"]."</td>
<td $color>".$row["domain_name"]."</td>
<td $color><form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"cmd_id\" value=\"".$row["id"]."\">
<input type=\"hidden\" name=\"id\" value=\"".$_REQUEST["id"]."\">
<input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
<input type=\"text\" size=\"5\" name=\"price\" value=\"".$row["price"]."\"></td>
<td $color><input type=\"text\" size=\"5\" name=\"quantity\" value=\"".$row["quantity"]."\"></td>
<td $color><input type=\"hidden\" name=\"action\" value=\"modify_client_cmd\">
<input type=\"text\" size=\"10\" name=\"cmd_date\" value=\"".$row["date"]."\"></td>
<td $color><input type=\"text\" size=\"10\" name=\"cmd_expir\" value=\"".$row["expir"]."\"></td>
<td $color><input type=\"submit\" name=\"ed_command\" value=\"Save\"><input type=\"submit\" name=\"del_command\" value=\"Del\"></td></form>
	</tr>";
	}
	$text .= "</table>";

	$text .= "<br><br><form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"rub\" value=\"crm\">
<input type=\"hidden\" name=\"id\" value=\"$cid\">
<input type=\"hidden\" name=\"action\" value=\"add_cmd_to_client\">
<select name=\"add_new_command\">";
	$query = "SELECT * FROM $pro_mysql_product_table ORDER BY name";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error);
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		$text .= "<option value=\"".$row["id"]."\">".$row["name"]."</option>";
	}
	$text .= "</select><br>
".$txt_domain_tbl_config_dom_name[$lang]."<input type=\"text\" name=\"add_newcmd_domain_name\" value=\"\"><input type=\"submit\" value=\"Add\">
</form>";

	return $text;
}

?>
