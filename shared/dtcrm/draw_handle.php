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

	$link_create = "<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=nickhandles\">Create new handle</a>";

	$query = "SELECT * FROM $pro_mysql_handle_table WHERE owner='$adm_login';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	unset($rows);
	for($i=0;$i<$num_rows;$i++){
		$rows[] = mysql_fetch_array($result);
	}

	$out .= "
Who will own the domain name and will be the registrant (domain owner)?<br>
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

	$out = "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>
<b><u>List of your whois nick handles:</u></b><br>";

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
		$hdladdr2 = $hdl["addr2"];
		$hdladdr3 = $hdl["addr3"];
		$hdlstate = $hdl["state"];
		$hdlcity = $hdl["city"];
		$hdlzipcode = $hdl["zipcode"];
		$hdlcountry = $hdl["country"];
		$hdlphone = $hdl["phone_num"];
		$hdlfax = $hdl["fax_num"];
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
	$out .= "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">$hidden_inputs
<input type=\"hidden\" name=\"hdl_id\" value=\"$hdl_id\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<table width=\"100%\" height=\"1\"><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Name for this handle$rf:</td>
	<td>$name_of_hdl</td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Company:</td>
	<td><input type=\"text\" name=\"company\" value=\"".$hdlcompany."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Firstname$rf:</td>
	<td><input type=\"text\" name=\"firstname\" value=\"".$hdlfirstname."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Lastname$rf:</td>
	<td><input type=\"text\" name=\"lastname\" value=\"".$hdllastname."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Street address$rf:</td>
	<td><input type=\"text\" name=\"addr1\" value=\"".$hdladdr1."\">
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Street address 2:<br><i>Optionnal</i></td>
	<td><input type=\"text\" name=\"addr2\" value=\"".$hdladdr2."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Street address 3:<br><i>Optionnal</i></td>
	<td><input type=\"text\" name=\"addr3\" value=\"".$hdladdr3."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>State:<br><i>If applicable</i></td>
	<td><input type=\"text\" name=\"state\" value=\"".$hdlstate."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>City:</td>
	<td><input type=\"text\" name=\"city\" value=\"".$hdlcity."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Zipcode$rf:</td>
	<td><input type=\"text\" name=\"zipcode\" value=\"".$hdlzipcode."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Country$rf:</td>
	<td><select name=\"country\">".cc_code_popup($hdlcountry)."</select></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Phone number$rf:</td>
	<td><input type=\"text\" name=\"phone_num\" value=\"".$hdlphone."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Fax number:<br><i>Optional</i></td>
	<td><input type=\"text\" name=\"fax_num\" value=\"".$hdlfax."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>email$rf:<br><i>MUST be a valid email</i></td>
	<td><input type=\"text\" name=\"email\" value=\"".$hdlemail."\"></td>
</tr><tr>
	<td></td><td><input type=\"submit\" value=\"Ok\"></td>
</tr></table>
</form>
";
	return $out;
}

?>
