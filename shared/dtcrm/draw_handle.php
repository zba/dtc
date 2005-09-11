<?php

function getContactsArrayFromID($owner_id,$billing_id,$admin_id){
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
	return $contacts;
}

function whoisHandleSelection($admin,$show_info="no",$owner=-1,$billing=-1,$admin=-1){
	global $lang;
	global $PHP_SELF; 
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $pro_mysql_handle_table;
	global $txt_dtcrm_create_new_handle;
	global $lang;

	$link_create = "<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=nickhandles\">".$txt_dtcrm_create_new_handle[$lang]."</a>";

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE owner='$adm_login';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	unset($rows);
	for($i=0;$i<$num_rows;$i++){
		$rows[] = mysql_fetch_array($result);
	}

	$out = "";
	$pop = "";
	$out .= "Who will own the domain name and will be the registrant (domain owner)?<br>
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
		$out .= "
Firstname: ". $infoz["firstname"] ."<br>
Lastname: ". $infoz["lastname"] ."<br>
Company: ". $infoz["company"] ."<br>
Address 1: ". $infoz["addr1"] ."<br>
Address 2: ". $infoz["addr2"] ."<br>
Address 3: ". $infoz["addr3"] ."<br>
Zipcode: ". $infoz["zipcode"] ."<br>
City: ". $infoz["city"] ."<br>
State: ". $infoz["state"] ."<br>
Country: ". $infoz["country"] ."<br>
Phone: ". $infoz["phone_num"] ."<br>
Fax: ". $infoz["fax_num"] ."<br>
Email: ". $infoz["email"] ."<br>";
	}
	$out .="
<br>";

	$out .= "
Who will be contacted for domain renewall (billing contact)?<br>
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
		$out .= "
Firstname: ". $infoz["firstname"] ."<br>
Lastname: ". $infoz["lastname"] ."<br>
Company: ". $infoz["company"] ."<br>
Address 1: ". $infoz["addr1"] ."<br>
Address 2: ". $infoz["addr2"] ."<br>
Address 3: ". $infoz["addr3"] ."<br>
Zipcode: ". $infoz["zipcode"] ."<br>
City: ". $infoz["city"] ."<br>
State: ". $infoz["state"] ."<br>
Country: ". $infoz["country"] ."<br>
Phone: ". $infoz["phone_num"] ."<br>
Fax: ". $infoz["fax_num"] ."<br>
Email: ". $infoz["email"] ."<br>";
	}
	$out .="
<br>";

	$out .= "
Who will have the rights for changing the whois information (admin contact)?<br>
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
		$out .= "
Firstname: ". $infoz["firstname"] ."<br>
Lastname: ". $infoz["lastname"] ."<br>
Company: ". $infoz["company"] ."<br>
Address 1: ". $infoz["addr1"] ."<br>
Address 2: ". $infoz["addr2"] ."<br>
Address 3: ". $infoz["addr3"] ."<br>
Zipcode: ". $infoz["zipcode"] ."<br>
City: ". $infoz["city"] ."<br>
State: ". $infoz["state"] ."<br>
Country: ". $infoz["country"] ."<br>
Phone: ". $infoz["phone_num"] ."<br>
Fax: ". $infoz["fax_num"] ."<br>
Email: ". $infoz["email"] ."<br>";
	}
	$out .="
<br>";
	return $out;
}


function drawAdminTools_NickHandles($admin){
	global $lang;
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $cc_code_popup;

	global $hdl_id;
	global $pro_mysql_handle_table;

	global $txt_dtcrm_indicate_required_field;
	global $txt_dtcrm_create_new_handle;
	global $txt_dtcrm_indicate_required_field;
	global $txt_dtcrm_name_for_this_handle;
	global $txt_dtcrm_company;
	global $txt_dtcrm_firstname;
	global $txt_dtcrm_lastname;
	global $txt_dtcrm_street_addr1;
	global $txt_dtcrm_street_addr2;
	global $txt_dtcrm_street_addr3;
	global $txt_dtcrm_optional;
	global $txt_dtcrm_if_applicable;
	global $txt_dtcrm_state;
	global $txt_dtcrm_city;
	global $txt_dtcrm_zipcode;
	global $txt_dtcrm_country;
	global $txt_dtcrm_phone_number;
	global $txt_dtcrm_phone_formating;
	global $txt_dtcrm_fax_number;
	global $txt_dtcrm_email;
	global $txt_dtcrm_must_be_valid_email;

	$out = "<b><u>List of your whois nick handles:</u></b><br>";

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE owner='$adm_login';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		if($i != 0){
			$out .= " - ";
		}
		$out .= "<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&hdl_id=".$row["id"]."\">".$row["name"]."</a>";
	}

	if(isset($_REQUEST["hdl_id"]) && $_REQUEST["hdl_id"] != ""){
		$hdl_id = $_REQUEST["hdl_id"];
		// Edit currently selected ID.
		$query = "SELECT * FROM $pro_mysql_handle_table WHERE id='$hdl_id' AND owner='$adm_login';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$hdl = mysql_fetch_array($result);
		$out .= "<br><br><a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">New handle</a><br>
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
		$hdlphone = $hdl["phone_num"];
		if(isset($hdl["fax_num"]))
			$hdlfax = $hdl["fax_num"];
		else
			$hdlfax = "";
		$hdlemail = $hdl["email"];
	}else{
		$out .= "<br><br><b><u>Create a new nick-handle:</u></b><br>";
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
		$hdlphone = "";
		$hdlfax = "";
		$hdlemail = "";
	}
	$rf = "<font color=\"red\">*</font>";	// Required field
	$out .= "($rf ".$txt_dtcrm_indicate_required_field[$lang].")<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">$hidden_inputs
<input type=\"hidden\" name=\"hdl_id\" value=\"$hdl_id\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<table width=\"100%\" height=\"1\"><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_name_for_this_handle[$lang]."</td>
	<td>$name_of_hdl</td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>".$txt_dtcrm_company[$lang]."</td>
	<td><input type=\"text\" name=\"company\" value=\"".$hdlcompany."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_firstname[$lang]."</td>
	<td><input type=\"text\" name=\"firstname\" value=\"".$hdlfirstname."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_lastname[$lang]."</td>
	<td><input type=\"text\" name=\"lastname\" value=\"".$hdllastname."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_street_addr1[$lang]."</td>
	<td><input type=\"text\" name=\"addr1\" value=\"".$hdladdr1."\">
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>".$txt_dtcrm_street_addr2[$lang]."<br><i>".$txt_dtcrm_optional[$lang]."</i></td>
	<td><input type=\"text\" name=\"addr2\" value=\"".$hdladdr2."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>".$txt_dtcrm_street_addr3[$lang]."<br><i>".$txt_dtcrm_optional[$lang]."</i></td>
	<td><input type=\"text\" name=\"addr3\" value=\"".$hdladdr3."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>".$txt_dtcrm_state[$lang]."<br><i>".$txt_dtcrm_if_applicable[$lang]."</i></td>
	<td><input type=\"text\" name=\"state\" value=\"".$hdlstate."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_city[$lang]."</td>
	<td><input type=\"text\" name=\"city\" value=\"".$hdlcity."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_zipcode[$lang]."</td>
	<td><input type=\"text\" name=\"zipcode\" value=\"".$hdlzipcode."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_country[$lang]."</td>
	<td><select name=\"country\">".cc_code_popup($hdlcountry)."</select></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_phone_number[$lang]."<br><i>".$txt_dtcrm_phone_formating[$lang]."</i></td>
	<td><input type=\"text\" name=\"phone_num\" value=\"".$hdlphone."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>".$txt_dtcrm_fax_number[$lang].":<br><i>".$txt_dtcrm_optional[$lang]."</i></td>
	<td><input type=\"text\" name=\"fax_num\" value=\"".$hdlfax."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>$rf".$txt_dtcrm_email[$lang]."<br><i>".$txt_dtcrm_must_be_valid_email[$lang]."</i></td>
	<td><input type=\"text\" name=\"email\" value=\"".$hdlemail."\"></td>
</tr><tr>
	<td></td><td><input type=\"submit\" value=\"Ok\"></td>
</tr></table>
</form>
";
	return $out;
}

?>
