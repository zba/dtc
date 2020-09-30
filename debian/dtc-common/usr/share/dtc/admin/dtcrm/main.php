<?php

function DTCRMlistClients(){
	if(isset($_REQUEST["id"]))
		$id_client = $_REQUEST["id"];
	global $pro_mysql_client_table;
	global $pro_mysql_admin_table;

	// The popup value is stored in the session, let's manage it
	if(isset($_REQUEST["clientlist_type"]) && $_REQUEST["clientlist_type"] != ""){
		$_SESSION["cur_clientlist_type"] = $_REQUEST["clientlist_type"];
		$clientlist_type = $_REQUEST["clientlist_type"];
	}else{
		if(isset($_SESSION["cur_clientlist_type"]) && $_SESSION["cur_clientlist_type"] != ""){
			$clientlist_type = $_SESSION["cur_clientlist_type"];
		}else{
			$clientlist_type = "hide-no-admins";
			$_SESSION["cur_clientlist_type"] = "hide-no-admins";
		}
	}

	$text = "<div style=\"white-space: nowrap\" nowrap>
<a href=\"?rub=crm&id=0\">". _("New customer") ."</a>
</a><br><br>";
	if(!isset($_REQUEST["clientsearch_txt"])){
		$_REQUEST["clientsearch_txt"] = '';
	}
	if($clientlist_type == "search" && preg_match("/^[a-zA-Z0-9\_\-\@\.\ ].*$/", $_REQUEST["clientsearch_txt"])){
		$query = "SELECT * FROM $pro_mysql_client_table WHERE UCASE(company_name) LIKE UCASE('%".mysql_real_escape_string($_REQUEST["clientsearch_txt"])."%') OR UCASE(familyname) LIKE UCASE('%".mysql_real_escape_string($_REQUEST["clientsearch_txt"])."%') OR UCASE(christname) LIKE UCASE('%".mysql_real_escape_string($_REQUEST["clientsearch_txt"])."%') OR UCASE(email) LIKE UCASE('%".mysql_real_escape_string($_REQUEST["clientsearch_txt"])."%') OR UCASE(phone) LIKE UCASE('%".mysql_real_escape_string($_REQUEST["clientsearch_txt"])."%') OR UCASE(special_note) LIKE UCASE('%".mysql_real_escape_string($_REQUEST["clientsearch_txt"])."%') OR UCASE(customfld) LIKE UCASE('%".mysql_real_escape_string($_REQUEST["clientsearch_txt"])."%') ORDER BY familyname";
	}else{
		$query = "SELECT * FROM $pro_mysql_client_table ORDER BY familyname";
	}

	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	$client_list = array();
	if(isset($id_client) && $_REQUEST["id"] == 0){
		$selected = "yes";
	}else{
		$selected = "no";
	}
	$client_list[] = array(
		"text" => _("New customer") ,
		"link" => "?rub=crm&id=0",
		"selected" => $selected);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		if($clientlist_type == "hide-no-admins"){
			$qa = "SELECT adm_login FROM $pro_mysql_admin_table WHERE id_client='".$row["id"]."';";
			$ra = mysql_query($qa)or die("Cannot query $qa line ".__LINE__." file ".__FILE__." sql said: ".mysql_error()); 
			$rn = mysql_num_rows($ra);
			if($rn == 0){
				$do_display = "no";
			}else{
				$do_display = "yes";
			}
		}else{
			$do_display = "yes";
		}
		if($do_display == "yes"){
			if(!isset($id_client) || $row["id"] != $_REQUEST["id"]){
				$text .= "<a href=\"?rub=crm&id=".$row["id"]."\">";
				$selected = "no";
			}else{
				$selected = "yes";
			}
			$url = "?rub=crm&id=".$row["id"];
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
	}
	$text .= "</div>";
	if(function_exists("skin_DisplayClientList")){
		$out = skin_DisplayClientList($client_list);
	}else{
		$out = $text;
	}

	// A popup to select to print all customers or not display the one without admins
	if($clientlist_type == "hide-no-admins"){
		$selectedlist_hide_no_admin = " selected";
		$selectedlist_show_all = "";
		$selectedlist_search = "";
	}elseif($clientlist_type == "show-all"){
 		$selectedlist_hide_no_admin = "";
 		$selectedlist_show_all = " selected";
		$selectedlist_search = "";
	}else{ // search
		$selectedlist_hide_no_admin = "";
		$selectedlist_show_all = "";
		$selectedlist_search = " selected";
	}
       $list_prefs = "<div class=\"box_wnb_nb_content\">
<div style=\"white-space: nowrap\" nowrap><form action=\"?\"><font size=\"-2\">". _("Show:")  ."<br>
<input type=\"hidden\" name=\"rub\" value=\"crm\">
<select class=\"box_wnb_nb_input\" name=\"clientlist_type\">
<option value=\"hide-no-admins\"$selectedlist_hide_no_admin>". _("Hide client without admin") ."
<option value=\"show-all\"$selectedlist_show_all>"._("Show all")."
<option value=\"search\"$selectedlist_search>"._("Search")."</option>
</select><br /><br />". _("Search string:") ."<br />
<input class=\"box_wnb_nb_input\" type=\"text\" name=\"clientsearch_txt\">
<div class=\"box_wnb_nb_input_btn_container\" onMouseOver=\"this.className='box_wnb_nb_input_btn_container-hover';\" onMouseOut=\"this.className='box_wnb_nb_input_btn_container';\">
 <div class=\"box_wnb_nb_input_btn_left\"></div>
 <div class=\"box_wnb_nb_input_btn_mid\"><input class=\"box_wnb_nb_input_btn\" type=\"submit\" value=\""._("Ok")."\"></div>
 <div class=\"box_wnb_nb_input_btn_right\"></div>
</div></form><br></div>
<div class=\"voider\"></div>
</div>
";

	return $list_prefs . $out;
}

function DTCRMclientAdmins(){
	global $pro_mysql_client_table;
	global $pro_mysql_admin_table;

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='".$_REQUEST["id"]."'";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
        $n = mysql_num_rows($r);
	$text = "<br><h3>". _("Remove an administrator for this customer:") ."</h3><br>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($i > 0)
			$text .= " - ";
		$text .= "<a href=\"?rub=crm&id=".$_REQUEST["id"]."&action=remove_admin_from_client&adm_name=".$a["adm_login"]."\">".$a["adm_login"]."</a>";
	}

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='0'";
	$r = mysql_query($q)or die("Cannot execute query: \"$q\" line ".__LINE__." in file ".__FILE__.", mysql said: ".mysql_error());
        $n = mysql_num_rows($r);
	$text .= "<br><br><h3>". _("Add an administrator to this customer:") ."</h3><br>";
	$text .= "<form action=\"?\">
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
	global $pro_mysql_custom_fld_table;

	if(isset($_REQUEST["id"])){
		$cid = $_REQUEST["id"];	// current customer id
	}else{
		return _("Select a customer.") ;
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
	$text = "<form action=\"?\">
<input type=\"hidden\" name=\"rub\" value=\"crm\">
<input type=\"hidden\" name=\"id\" value=\"$cid\">$hidden_inputs
";
	$text .= dtcFormTableAttrs();
	$text .= dtcFormLineDraw( _("Last Name: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_familyname\"value=\"".htmlspecialchars(stripcslashes($row["familyname"]))."\">");
	$text .= dtcFormLineDraw( _("First Name: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_christname\" value=\"".htmlspecialchars(stripcslashes($row["christname"]))."\">",0);
	$text .= dtcFormLineDraw( _("Company Account: ") ,"<input type=\"radio\" name=\"ed_is_company\" value=\"yes\" $iscomp_yes > "._("Yes")."
<input type=\"radio\" name=\"ed_is_company\" value=\"no\" $iscomp_no > "._("No"));
	$text .= dtcFormLineDraw( _("Company Name: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_company_name\" value=\"".htmlspecialchars(stripcslashes($row["company_name"]))."\">",0);
	$text .= dtcFormLineDraw( _("VAT Number: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_vat_num\" value=\"".htmlspecialchars(stripcslashes($row["vat_num"]))."\">");
	$text .= dtcFormLineDraw( _("Address (line1): ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_addr1\" value=\"".htmlspecialchars(stripcslashes($row["addr1"]))."\">",0);
	$text .= dtcFormLineDraw( _("Address (line2): ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_addr2\" value=\"".htmlspecialchars(stripcslashes($row["addr2"]))."\">");
	$text .= dtcFormLineDraw( _("Address (line3): ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_addr3\" value=\"".htmlspecialchars(stripcslashes($row["addr3"]))."\">",0);
	$text .= dtcFormLineDraw( _("City: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_city\" value=\"".htmlspecialchars(stripcslashes($row["city"]))."\">");
	$text .= dtcFormLineDraw( _("Zipcode: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_zipcode\" value=\"".htmlspecialchars(stripcslashes($row["zipcode"]))."\">",0);
	$text .= dtcFormLineDraw( _("State: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_state\" value=\"".htmlspecialchars(stripcslashes($row["state"]))."\">");
	$text .= dtcFormLineDraw( _("Country: ") ,"<select class=\"dtcDatagrid_input_alt_color\" name=\"ed_country\">".
cc_code_popup($row["country"])."</select>",0);
	$text .= dtcFormLineDraw( _("Phone Number: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_phone\" value=\"".htmlspecialchars(stripcslashes($row["phone"]))."\">");
	$text .= dtcFormLineDraw( _("Fax: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_fax\" value=\"".htmlspecialchars(stripcslashes($row["fax"]))."\">",0);
	$text .= dtcFormLineDraw( _("Email: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_email\" value=\"".$row["email"]."\">");
	$text .= dtcFormLineDraw( _("Notes: ") ,"<textarea cols=\"60\" rows=\"7\" class=\"dtcDatagrid_input_alt_color\" cols=\"40\" rows=\"5\" name=\"ed_special_note\">".htmlspecialchars(stripcslashes($specnot))."</textarea>",0);
	$text .= dtcFormLineDraw( _("Money remaining: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_dollar\" value=\"".$row["dollar"]."\">");
	$text .= dtcFormLineDraw( _("Quota (MB): ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"ed_disk_quota_mb\" value=\"".$row["disk_quota_mb"]."\">",0);
	$text .= dtcFormLineDraw( _("Allowed data transfer (GB): ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"ed_bw_quota_per_month_gb\" value=\"".$row["bw_quota_per_month_gb"]."\">");

	// Manage to print the answers of the custom fields;
	// We first, out of the custom_fld field, get an array with the custom field varname as key
	// and the customer's answer as data.
	$customer_custom_fields = array();
	if( isset($row["customfld"]) ){
		$explo_row = explode("|", htmlspecialchars(stripcslashes($row["customfld"])));
		$n_custf = sizeof($explo_row);
		for($i=0;$i<$n_custf;$i++){
			$one_fld = $explo_row[$i];
			$pos_data = strpos($one_fld,":");
			if($pos_data === false){
				continue;
			}
			$varname = substr($one_fld,0,$pos_data);
			$value = substr($one_fld,$pos_data+1);
			$customer_custom_fields[ $varname ] = $value;
		}
	}

	$q = "SELECT * FROM $pro_mysql_custom_fld_table WHERE 1 ORDER BY widgetorder;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$init_alt = 1;

	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($init_alt == 1){
			$init_alt = 0;
			$css_class = "dtcDatagrid_input_alt_color";
		}else{
			$init_alt = 1;
			$css_class = "dtcDatagrid_input_color";
		}
		if(isset($customer_custom_fields[ $a["varname"] ])){
			$val = $customer_custom_fields[ $a["varname"] ];
		}else{
			$val = "";
		}
		switch($a["widgettype"]){
		case "radio":
			$explo_popup = explode("|",$a["widgetvalues"]);
			$explo_popup2 = explode("|",$a["widgetdisplay"]);
			$n_val = sizeof($explo_popup);
			$widget = "";
			for($j=0;$j<$n_val;$j++){
				if($val == $explo_popup[$j]){
					$sel = " checked ";
				}else{
					$sel = " ";
				}
				$widget .= "<input type=\"radio\" name=\"".$a["varname"]."\" value=\"".$explo_popup[$j]."\" $sel > ".$explo_popup2[$j];
			}
			break;
		case "popup":
			$explo_popup = explode("|",$a["widgetvalues"]);
			$explo_popup2 = explode("|",$a["widgetdisplay"]);
			$n_val = sizeof($explo_popup);
			$widget = "<select name=\"".$a["varname"]."\">";
			for($j=0;$j<$n_val;$j++){
				if($val == $explo_popup[$j]){
					$sel = " selected ";
				}else{
					$sel = " ";
				}
				$widget .= "<option value=\"".$explo_popup[$j]."\" $sel >".$explo_popup2[$j]."</option>";
			}
			$widget .= "</select>";
			break;
		case "textarea":
			$widget = "<textarea cols=\"60\" rows=\"7\" class=\"$css_class\" name=\"".$a["varname"]."\">".htmlspecialchars($val)."</textarea>";
			break;
		case "text":
		default:
			$widget = "<input size=\"60\" class=\"$css_class\" size=\"40\" type=\"text\" name=\"".htmlspecialchars($a["varname"])."\" value=\"".$val."\">";
			break;
		}
		$text .= dtcFormLineDraw($a["question"],$widget,$init_alt);
	}
	$text .= "
<tr><td align=\"right\"></td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"Save\"></div>
 <div class=\"input_btn_right\"></div>
</div></form>
<form><form action=\"?\">
<input type=\"hidden\" name=\"rub\" value=\"crm\">
<input type=\"hidden\" name=\"delete_id\" value=\"$cid\">
<input type=\"hidden\" name=\"action\" value=\"delete_customer_id\">
<input type=\"hidden\" name=\"del\" value=\"Del\">
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\""._("Delete client")."\"></div>
 <div class=\"input_btn_right\"></div>
</div></td></tr>
</table>
</form>";
	return $text;
}

?>
