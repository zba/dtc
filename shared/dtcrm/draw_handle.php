<?php

function getContactsArrayFromID($owner_id,$billing_id,$admin_id,$tech_id){
	global $pro_mysql_handle_table;
	$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$owner_id';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
	if(mysql_num_rows($result) != 1)	die("Handle ID not found !");
	$contacts["owner"] = mysql_fetch_array($result)or die("Cannot fetch array !");

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$billing_id';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
	if(mysql_num_rows($result) != 1)	die("Handle ID not found !");
	$contacts["billing"] = mysql_fetch_array($result)or die("Cannot fetch array !");

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$admin_id';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
	if(mysql_num_rows($result) != 1)	die("Handle ID not found !");
	$contacts["admin"] = mysql_fetch_array($result)or die("Cannot fetch array !");

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$tech_id';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!! ".mysql_error());
	if(mysql_num_rows($result) != 1)        die("Handle ID not found !");
	$contacts["teck"] = mysql_fetch_array($result)or die("Cannot fetch array !");
	return $contacts;
}

function whoisHandleSelection($admin,$show_info="no",$owner=-1,$billing=-1,$admin=-1,$teck=-1){
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $pro_mysql_handle_table;

	$link_create = "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=myaccount/nickhandles\">". _("Create a new handle") ."</a>";

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE owner='$adm_login';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	unset($rows);
	for($i=0;$i<$num_rows;$i++){
		$rows[] = mysql_fetch_array($result);
	}

	$out = "";
	$pop = "";
	$out .= "<b>"._("Domain owner / registrant:")."</b> "._("Who is the domain name owner registrant?") ."<br>
<select name=\"dtcrm_owner_hdl\">";
	for($i=0;$i<$num_rows;$i++){
		$id = $rows[$i]["id"];
		if($id == $owner){
			$sel = " selected ";
			$infoz = $rows[$i];
		}else{
			$sel = "";
		}
		$out .= "<option value=\"$id\"$sel>".$rows[$i]["name"]."</option>";
		$pop .= "<option value=\"$id\">".$rows[$i]["name"]."</option>";
	}
	$out .= "</select>$link_create<br>";
	if($show_info == "yes"){
		if(!isset($infoz)){
			$out .= "<font color=\"red\">"._("Whois handle information not found. Please select a new nick handle.")."</font>";
		}else{
			$out .= _("First Name: ") . $infoz["firstname"] ."<br>".
_("Last Name: ") . $infoz["lastname"] ."<br>".
_("Company Name: ") . $infoz["company"] ."<br>".
_("Address (line1): ") . $infoz["addr1"] ."<br>".
_("Address (line2): ") . $infoz["addr2"] ."<br>".
_("Address (line3): ") . $infoz["addr3"] ."<br>".
_("Zipcode: ") . $infoz["zipcode"] ."<br>".
_("City: ") . $infoz["city"] ."<br>".
_("State: ") . $infoz["state"] ."<br>".
_("Country: ") . $infoz["country"] ."<br>".
_("Language: ") . $infoz["language"] ."<br>".
_("Phone Number: ") . $infoz["phone_num"] ."<br>".
_("Fax: ") . $infoz["fax_num"] ."<br>".
_("Email: ") . $infoz["email"] ."<br>";
		}
	}
	$out .="
<br>";
	unset($infoz);

	$out .= "<b>"._("Admin contact:")."</b> "._("Who will have the rights for changing the whois contact information?") ."<br>
<select name=\"dtcrm_admin_hdl\">";
	for($i=0;$i<$num_rows;$i++){
		$id = $rows[$i]["id"];
		if($id == $admin){
			$sel = " selected ";
			$infoz = $rows[$i];
		}else{
			$sel = "";
		}
		$out .= "<option value=\"$id\"$sel>".$rows[$i]["name"]."</option>";
		$pop .= "<option value=\"$id\">".$rows[$i]["name"]."</option>";
	}
	$out .= "</select>$link_create<br>";
	if($show_info == "yes"){
		if(!isset($infoz)){
			$out .= "<font color=\"red\">"._("Whois handle information not found. Please select a new nick handle.")."</font>";
		}else{
			$out .= _("First Name: ") . $infoz["firstname"] ."<br>".
_("Last Name: ") . $infoz["lastname"] ."<br>".
_("Company Name: ") . $infoz["company"] ."<br>".
_("Address (line1): ") . $infoz["addr1"] ."<br>".
_("Address (line2): ") . $infoz["addr2"] ."<br>".
_("Address (line3): ") . $infoz["addr3"] ."<br>".
_("Zipcode: ") . $infoz["zipcode"] ."<br>".
_("City: ") . $infoz["city"] ."<br>".
_("State: ") . $infoz["state"] ."<br>".
_("Country: ") . $infoz["country"] ."<br>".
_("Language: ") . $infoz["language"] ."<br>".
_("Phone Number: ") . $infoz["phone_num"] ."<br>".
_("Fax: ") . $infoz["fax_num"] ."<br>".
_("Email: ") . $infoz["email"] ."<br>";
		}
	}
	$out .="
<br>";
	unset($infoz);

	$out .= "<b>"._("Billing contact:")."</b> "._("Who will be contacted for domain renewal?") ."<br>
<select name=\"dtcrm_billing_hdl\">";
	for($i=0;$i<$num_rows;$i++){
		$id = $rows[$i]["id"];
		if($id == $billing){
			$sel = " selected ";
			$infoz = $rows[$i];
		}else{
			$sel = "";
		}
		$out .= "<option value=\"$id\"$sel>".$rows[$i]["name"]."</option>";
		$pop .= "<option value=\"$id\">".$rows[$i]["name"]."</option>";
	}
	$out .= "</select>$link_create<br>";
	if($show_info == "yes"){
		if(!isset($infoz)){
			$out .= "<font color=\"red\">"._("Whois handle information not found. Please select a new nick handle.")."</font>";
		}else{
			$out .= _("First Name: ") . $infoz["firstname"] ."<br>".
_("Last Name: ") . $infoz["lastname"] ."<br>".
_("Company Name: ") . $infoz["company"] ."<br>".
_("Address (line1): ") . $infoz["addr1"] ."<br>".
_("Address (line2): ") . $infoz["addr2"] ."<br>".
_("Address (line3): ") . $infoz["addr3"] ."<br>".
_("Zipcode: ") . $infoz["zipcode"] ."<br>".
_("City: ") . $infoz["city"] ."<br>".
_("State: ") . $infoz["state"] ."<br>".
_("Country: ") . $infoz["country"] ."<br>".
_("Language: ") . $infoz["language"] ."<br>".
_("Phone Number: ") . $infoz["phone_num"] ."<br>".
_("Fax: ") . $infoz["fax_num"] ."<br>".
_("Email: ") . $infoz["email"] ."<br>";
		}
	}
	$out .="
<br>";
	unset($infoz);

	$out .= "<b>"._("Technical contact:")."</b> "._("Who will be contacted for technical changes?") ."<br>
<select name=\"dtcrm_teck_hdl\">";
	for($i=0;$i<$num_rows;$i++){
		$id = $rows[$i]["id"];
		if($id == $billing){
			$sel = " selected ";
			$infoz = $rows[$i];
		}else{
			$sel = "";
		}
		$out .= "<option value=\"$id\"$sel>".$rows[$i]["name"]."</option>";
		$pop .= "<option value=\"$id\">".$rows[$i]["name"]."</option>";
	}
	$out .= "</select>$link_create<br>";
	if($show_info == "yes"){
		if(!isset($infoz)){
			$out .= "<font color=\"red\">"._("Whois handle information not found. Please select a new nick handle.")."</font>";
		}else{
			$out .= _("First Name: ") . $infoz["firstname"] ."<br>".
_("Last Name: ") . $infoz["lastname"] ."<br>".
_("Company Name: ") . $infoz["company"] ."<br>".
_("Address (line1): ") . $infoz["addr1"] ."<br>".
_("Address (line2): ") . $infoz["addr2"] ."<br>".
_("Address (line3): ") . $infoz["addr3"] ."<br>".
_("Zipcode: ") . $infoz["zipcode"] ."<br>".
_("City: ") . $infoz["city"] ."<br>".
_("State: ") . $infoz["state"] ."<br>".
_("Country: ") . $infoz["country"] ."<br>".
_("Language: ") . $infoz["language"] ."<br>".
_("Phone Number: ") . $infoz["phone_num"] ."<br>".
_("Fax: ") . $infoz["fax_num"] ."<br>".
_("Email: ") . $infoz["email"] ."<br>";
		}
	}

	$out .="
<br>";
	return $out;
}

function nickHandleCreateCallback($id){
	global $pro_mysql_handle_table;
	$q = "SELECT * FROM $pro_mysql_handle_table WHERE id='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created nick handle line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	return registry_create_nick_handle($a);
}

function nickHandleDeleteCallback($id){
	global $pro_mysql_handle_table;
	$q = "SELECT * FROM $pro_mysql_handle_table WHERE id='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created nick handle line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	return registry_delete_nick_handle($a);
}

function nickHandleEditCallback($id){
	global $pro_mysql_handle_table;
	$q = "SELECT * FROM $pro_mysql_handle_table WHERE id='$id';";
	$r = mysql_query($q)or die ("Cannot query $q line: ".__LINE__." file ".__FILE__." sql said:" .mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Cannot find created nick handle line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	return registry_edit_nick_handle($a);
}

function drawAdminTools_NickHandles($admin){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $cc_code_array;

	global $cc_code_popup;

	global $hdl_id;
	global $pro_mysql_handle_table;

	$dsc = array(
		"title" => _("List of your whois nick handles:"),
		"new_item_title" => _("New nick handle"),
		"new_item_link" => _("New nick handle"),
		"edit_item_title" => _("Edit a nick handle:"),
		"table_name" => $pro_mysql_handle_table,
		"action" => "nick_handle_list_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "name",
		"where_list" => array(
			"owner" => $adm_login),
		"order_by" => "name",

		// Functions to call if some registries need to record nick handles
		"create_item_callback" => "nickHandleCreateCallback",
		"delete_item_callback" => "nickHandleDeleteCallback",
		"edit_item_callback" => "nickHandleEditCallback",

		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => _("Login:") ),
			"name" => array(
				"legend" => _("Nick Handle Name: "),
				"help" => _("This text is used only in the DTC control panel, and will not appear in the whois database."),
				"type" => "text",
				"size" => "20",
				),
			"company" => array(
				"legend" => _("Company Name: "),
				"type" => "text",
				"size" => "20",
				),
			"firstname" => array(
				"legend" => _("First Name: "),
				"type" => "text",
				"size" => "20",
				),
			"lastname" => array(
				"legend" => _("Last Name: "),
				"type" => "text",
				"size" => "20",
				),
			"addr1" => array(
				"legend" => _("Address (line1): "),
				"type" => "text",
				"size" => "20",
				),
			"addr2" => array(
				"legend" => _("Address (line2): "),
				"type" => "text",
				"size" => "20",
				),
			"addr3" => array(
				"legend" => _("Address (line3): "),
				"type" => "text",
				"size" => "20",
				),
			"city" => array(
				"legend" => _("City: "),
				"type" => "text",
				"size" => "20",
				),
			"state" => array(
				"legend" => _("State: "),
				"type" => "text",
				"size" => "20",
				),
			"country" => array(
				"legend" => _("Country: "),
				"type" => "popup",
				"values" => array_keys($cc_code_array),
				"display_replace" => array_values($cc_code_array)
				),
			"language" => array(
				"legend" => _("Language: "),
				"type" => "text",
				"size" => "2",
				),
			"zipcode" => array(
				"legend" => _("Zipcode: "),
				"type" => "text",
				"size" => "20",
				),
			"phone_num" => array(
				"legend" => _("Phone Number: "),
				"type" => "text",
				"size" => "20",
				),
			"fax_num" => array(
				"legend" => _("Fax: "),
				"type" => "text",
				"size" => "20",
				),
			"email" => array(
				"legend" => _("Email: "),
				"type" => "text",
				"size" => "20",
				)
			)
		);
	$list_items = dtcListItemsEdit($dsc);
	return $list_items;

	$out = "<b><u>List of your whois nick handles:</u></b><br>";

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE owner='$adm_login';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		if($i != 0){
			$out .= " - ";
		}
		$out .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&hdl_id=".$row["id"]."\">".$row["name"]."</a>";
	}

	if(isset($_REQUEST["hdl_id"]) && $_REQUEST["hdl_id"] != ""){
		$hdl_id = $_REQUEST["hdl_id"];
		// Edit currently selected ID.
		$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$hdl_id' AND owner='$adm_login';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$hdl = mysql_fetch_array($result);
		$out .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">New handle</a><br>
		<b><u>Edit existing nick-handle ".$hdl["name"].":</u></b><br>";
		$hidden_inputs = "<input type=\"hidden\" name=\"action\" value=\"update_nickhandle\">";
		$name_of_hdl = "<input type=\"hidden\" name=\"name\" value=\"".$hdl["name"]."\">".$hdl["name"];
		$hdlcompany = $hdl["company"];
		$hdlfirstname = $hdl["firstname"];
		$hdllastname = $hdl["lastname"];
		$hdladdr1 = $hdl["addr1"];
		if(isset($hdl["addr2"]))
			$hdladdr2 = $hdl["addr2"];
		else
			$hdladdr2 = "";
		if(isset($hdl["addr3"]))
			$hdladdr3 = $hdl["addr3"];
		else
			$hdladdr3 = "";
		if(isset($hdl["state"]))
			$hdlstate = $hdl["state"];
		else
			$hdlstate = "";
		$hdlcity = $hdl["city"];
		$hdlzipcode = $hdl["zipcode"];
		$hdlcountry = $hdl["country"];
		if(isset($hdl["language"]))
			$hdllanguage = $hdl["language"];
		else
			$hdllanguage = "";
		$hdlphone = $hdl["phone_num"];
		if(isset($hdl["fax_num"]))
			$hdlfax = $hdl["fax_num"];
		else
			$hdlfax = "";
		$hdlemail = $hdl["email"];
	}else{
		$out .= "<br><br><b><u>". _("Create a new nick-handle:") ."</u></b><br>";
		$hidden_inputs = "<input type=\"hidden\" name=\"action\" value=\"create_nickhandle\">";
		$name_of_hdl = "<input type=\"text\" name=\"name\" value=\"\">";
		$hdlcompany = "";
		$hdlfirstname = "";
		$hdllastname = "";
		$hdladdr1 = "";
		$hdladdr2 = "";
		$hdladdr3 = "";
		$hdlstate = "";
		$hdlcity = "";
		$hdlzipcode = "";
		$hdlcountry = "";
		$hdllanguage = "";
		$hdlphone = "";
		$hdlfax = "";
		$hdlemail = "";
	}
	$rf = "<font color=\"red\">*</font>";	// Required field
	$out .= "($rf ". _("marked fields are required") .")<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">$hidden_inputs
<input type=\"hidden\" name=\"hdl_id\" value=\"$hdl_id\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<table width=\"100%\" height=\"1\"><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("Name for this handle: ") ."</td>
	<td>$name_of_hdl</td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>". _("Company Name: ") ."</td>
	<td><input type=\"text\" name=\"company\" value=\"".$hdlcompany."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("First Name: ") ."</td>
	<td><input type=\"text\" name=\"firstname\" value=\"".$hdlfirstname."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("Last Name: ") ."</td>
	<td><input type=\"text\" name=\"lastname\" value=\"".$hdllastname."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("Address (line1): ") ."</td>
	<td><input type=\"text\" name=\"addr1\" value=\"".$hdladdr1."\">
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>". _("Address (line2): ") ."<br><i>". _("optional") ."</i></td>
	<td><input type=\"text\" name=\"addr2\" value=\"".$hdladdr2."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>". _("Address (line3): ") ."<br><i>". _("optional") ."</i></td>
	<td><input type=\"text\" name=\"addr3\" value=\"".$hdladdr3."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>". _("State: ") ."<br><i>". _("if applicable") ."</i></td>
	<td><input type=\"text\" name=\"state\" value=\"".$hdlstate."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("City: ") ."</td>
	<td><input type=\"text\" name=\"city\" value=\"".$hdlcity."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("Zipcode: ") ."</td>
	<td><input type=\"text\" name=\"zipcode\" value=\"".$hdlzipcode."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("Country: ") ."</td>
	<td><select name=\"country\">".cc_code_popup($hdlcountry)."</select></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>". _("Language: ") .":<br><i>". _("optional, but required for .eu f.ex.") ."</i></td>
	<td><input type=\"text\" name=\"language\" value=\"".$hdllanguage."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("Phone Number: ") ."<br><i>". _("+1.XXXXXXXXXX format") ."</i></td>
	<td><input type=\"text\" name=\"phone_num\" value=\"".$hdlphone."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>". _("Fax: ") .":<br><i>". _("optional") ."</i></td>
	<td><input type=\"text\" name=\"fax_num\" value=\"".$hdlfax."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf". _("Email: ") ."<br><i>". _("This MUST be a valid address") ."</i></td>
	<td><input type=\"text\" name=\"email\" value=\"".$hdlemail."\"></td>
</tr><tr>
	<td></td><td><input type=\"submit\" value=\"Ok\"></td>
</tr></table>
</form>
";
	return $out;
}

?>
