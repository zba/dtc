<?php

function register_user(){
// adm_login=aaa&reqadm_pass=&reqadm_pass2=&domain_name=toto&familyname=nymous&
// firstname=ano&compname=testsoc&email=toto@toto.com&phone=PARIS&
// fax=state&address1=1&address2=2&address3=3&zipcode=75991&city=paris&
// state=state&country=US&Login=Register

	global $pro_mysql_admin_table;
	global $pro_mysql_new_admin_table;

	// Check if all fields are blank, in wich case don't display error
	if(($_REQUEST["reqadm_login"] == "" || !isset($_REQUEST["reqadm_login"]))
	&& ($_REQUEST["reqadm_pass"] == "" || !isset($_REQUEST["reqadm_pass"]))
	&& ($_REQUEST["reqadm_pass2"] == "" || !isset($_REQUEST["reqadm_pass2"]))
	&& ($_REQUEST["domain_name"] == "" || !isset($_REQUEST["domain_name"]))
	&& ($_REQUEST["familyname"] == "" || !isset($_REQUEST["familyname"]))
	&& ($_REQUEST["firstname"] == "" || !isset($_REQUEST["firstname"]))
	&& ($_REQUEST["email"] == "" || !isset($_REQUEST["email"]))
	&& ($_REQUEST["phone"] == "" || !isset($_REQUEST["phone"]))
	&& ($_REQUEST["address1"] == "" || !isset($_REQUEST["address1"]))
	&& ($_REQUEST["zipcode"] == "" || !isset($_REQUEST["zipcode"]))
	&& ($_REQUEST["city"] == "" || !isset($_REQUEST["city"]))
	&& ($_REQUEST["firstname"] == "" || !isset($_REQUEST["firstname"]))){
		$ret["err"] = 1;
		$ret["mesg"] = "Not registering";
		return $ret;
	}

	// Do field format checking and escaping for all fields
	if(!isFtpLogin($_REQUEST["reqadm_login"])){
		$ret["err"] = 2;
		$ret["mesg"] = "User login format incorrect. Please use letters and numbers only and from 4 to 16 chars.";
		return $ret;
	}
	if(!isDTCPassword($_REQUEST["reqadm_pass"])){
		$ret["err"] = 2;
		$ret["mesg"] = "Password format incorrect. Please use letters and numbers only and from 4 to 16 chars.";
		return $ret;
	}
	if($_REQUEST["reqadm_pass"] != $_REQUEST["reqadm_pass2"]){
		$ret["err"] = 2;
		$ret["mesg"] = "Passwords 1 and 2 does not match!";
		return $ret;
	}
	if(!isHostnameOrIP($_REQUEST["domain_name"])){
		$ret["err"] = 2;
		$ret["mesg"] = "Domain name does not look like to be a correct hostname.";
		return $ret;
	}
	if(!isValidEmail($_REQUEST["email"])){
		$ret["err"] = 2;
		$ret["mesg"] = "Email does not look like to be a correct address format.";
		return $ret;
	}

	if(!isset($_REQUEST["familyname"]) || $_REQUEST["familyname"]==""){
		$ret["err"] = 2;
		$ret["mesg"] = "Required field family name missing.";
		return $ret;
	}else{
		if (!get_magic_quotes_gpc()){
			$esc_familyname = addslashes($_REQUEST["familyname"]);
		}else{
			$esc_familyname = $_REQUEST["familyname"];
		}
	}

	if(!isset($_REQUEST["firstname"]) || $_REQUEST["firstname"]==""){
		$ret["err"] = 2;
		$ret["mesg"] = "Required field first name missing.";
		return $ret;
	}else{
		if (!get_magic_quotes_gpc()){
			$esc_firstname = addslashes($_REQUEST["firstname"]);
		}else{
			$esc_firstname = $_REQUEST["firstname"];
		}
	}

	if(!isset($_REQUEST["phone"]) || $_REQUEST["phone"]==""){
		$ret["err"] = 2;
		$ret["mesg"] = "Required field phone missing.";
		return $ret;
	}else{
		if (!get_magic_quotes_gpc()){
			$esc_phone = addslashes($_REQUEST["phone"]);
		}else{
			$esc_phone = $_REQUEST["phone"];
		}
	}

	if (!get_magic_quotes_gpc()){
		$esc_fax = addslashes($_REQUEST["fax"]);
	}else{
		$esc_fax = $_REQUEST["fax"];
	}

	if (!get_magic_quotes_gpc()){
		$esc_compname = addslashes($_REQUEST["compname"]);
	}else{
		$esc_compname = $_REQUEST["compname"];
	}

	if(!isset($_REQUEST["address1"]) || $_REQUEST["address1"]==""){
		$ret["err"] = 2;
		$ret["mesg"] = "Required field address missing.";
		return $ret;
	}else{
		if (!get_magic_quotes_gpc()){
			$esc_address1 = addslashes($_REQUEST["address1"]);
		}else{
			$esc_address1 = $_REQUEST["address1"];
		}
	}

	if (!get_magic_quotes_gpc()){
		$esc_address2 = addslashes($_REQUEST["address2"]);
	}else{
		$esc_address2 = $_REQUEST["address2"];
	}

	if (!get_magic_quotes_gpc()){
		$esc_address3 = addslashes($_REQUEST["address3"]);
	}else{
		$esc_address3 = $_REQUEST["address3"];
	}

	if(!isset($_REQUEST["zipcode"]) || $_REQUEST["zipcode"]==""){
		$ret["err"] = 2;
		$ret["mesg"] = "Required field zipcode missing.";
		return $ret;
	}else{
		if (!get_magic_quotes_gpc()){
			$esc_zipcode = addslashes($_REQUEST["zipcode"]);
		}else{
			$esc_zipcode = $_REQUEST["zipcode"];
		}
	}

	if(!isset($_REQUEST["city"]) || $_REQUEST["city"]==""){
		$ret["err"] = 2;
		$ret["mesg"] = "Required field city missing.";
		return $ret;
	}else{
		if (!get_magic_quotes_gpc()){
			$esc_city = addslashes($_REQUEST["city"]);
		}else{
			$esc_city = $_REQUEST["city"];
		}
	}

	if (!get_magic_quotes_gpc()){
		$esc_state = addslashes($_REQUEST["state"]);
	}else{
		$esc_state = $_REQUEST["state"];
	}

	if(!ereg("^([A-Z])([A-Z])\$",$_REQUEST["country"])){
		$ret["err"] = 2;
		$ret["mesg"] = "Country code seems incorrect.";
		return $ret;
	}

	if($_REQUEST["iscomp"] == "yes"){
		$esc_comp = "yes";
	}else if($_REQUEST["iscomp"] == "no"){
		$esc_comp = "no";
	}else{
		$ret["err"] = 2;
		$ret["mesg"] = "Is company radio button is wrong!";
		return $ret;
	}

	$q = "SELECT adm_login FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["reqadm_login"]."';";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$ret["err"] = 3;
		$ret["mesg"] = "Username already taken! Try again.";
		return $ret;
	}
	$q = "SELECT reqadm_login FROM $pro_mysql_new_admin_table WHERE reqadm_login='".$_REQUEST["reqadm_login"]."';";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$ret["err"] = 3;
		$ret["mesg"] = "Username already tried to be taken! Try again.";
		return $ret;
	}
	$q = "INSERT INTO $pro_mysql_new_admin_table
(reqadm_login,
reqadm_pass,
domain_name,
family_name,
first_name,
comp_name,
iscomp,
email,
phone,
fax,
addr1,
addr2,
addr3,
zipcode,
city,
state,
country
)
VALUES('".$_REQUEST["reqadm_login"]."',
'".$_REQUEST["reqadm_pass"]."',
'".$_REQUEST["domain_name"]."',
'$esc_familyname',
'$esc_firstname',
'$esc_compname',
'$esc_comp',
'".$_REQUEST["email"]."',
'$esc_phone',
'$esc_fax',
'$esc_address1',
'$esc_address2',
'$esc_address3',
'$esc_zipcode',
'$esc_city',
'$esc_state',
'".$_REQUEST["country"]."')";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$ret["err"] = 0;
	$ret["mesg"] = "Query ok!";
	return $ret;
}

function registration_form(){
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_use_text_menu;
	global $txt_login_title;

	global $conf_skin;

	global $lang;
	global $txt_draw_client_info_familyname;
	global $txt_draw_client_info_firstname;
	global $txt_draw_client_info_comp_name;
	global $txt_draw_client_info_addr;
	global $txt_draw_client_info_email;
	global $txt_draw_client_info_fax;
	global $txt_draw_client_info_phone;
	global $txt_draw_client_info_zipcode;
	global $txt_draw_client_info_city;
	global $txt_draw_client_info_state;
	global $txt_draw_client_info_country;

	$login_info = "<table>
<tr>
	<td align=\"right\">".$txt_login_login[$lang]."</td>
	<td><input type=\"text\" name=\"reqadm_login\" value=\"".$_REQUEST["reqadm_login"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_login_pass[$lang]."</td>
	<td><input type=\"password\" name=\"reqadm_pass\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">Confirm pass:</td>
	<td><input type=\"password\" name=\"reqadm_pass2\" value=\"\"></td>
</tr><tr>
	<td align=\"right\">Desired domain name:</td>
	<td><input type=\"text\" name=\"domain_name\" value=\"".$_REQUEST["domain_name"]."\"></td>
</tr></table>";
	$login_skined = skin("frame",$login_info,"");

	if($_REQUEST["iscomp"] == "yes"){
		$compyes = "checked";
	}else if($_REQUEST["iscomp"] == "no"){
		$compno = " checked";
	}
	$client_info = "<table>
<tr>
	<td align=\"right\">".$txt_draw_client_info_familyname[$lang]."</td>
	<td><input type=\"text\" name=\"familyname\" value=\"".$_REQUEST["familyname"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_firstname[$lang]."</td>
	<td><input type=\"text\" name=\"firstname\" value=\"".$_REQUEST["firstname"]."\"></td>
</tr><tr>
	<td align=\"right\">Is company</td>
	<td><input type=\"radio\" name=\"iscomp\" value=\"yes\"$compyes>Yes
<input type=\"radio\" name=\"iscomp\" value=\"no\"$compno>No</td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_comp_name[$lang]."</td>
	<td><input type=\"text\" name=\"compname\" value=\"".$_REQUEST["compname"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_email[$lang]."</td>
	<td><input type=\"text\" name=\"email\" value=\"".$_REQUEST["email"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_phone[$lang]."</td>
	<td><input type=\"text\" name=\"phone\" value=\"".$_REQUEST["phone"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_fax[$lang]."</td>
	<td><input type=\"text\" name=\"fax\" value=\"".$_REQUEST["fax"]."\"></td>
</tr></table>";
	$client_skined = skin("frame",$client_info,"");

	$client_addr = "<table>
<tr>
	<td align=\"right\">".$txt_draw_client_info_addr[$lang]."</td>
	<td><input type=\"text\" name=\"address1\" value=\"".$_REQUEST["address1"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_addr[$lang]." 2</td>
	<td><input type=\"text\" name=\"address2\" value=\"".$_REQUEST["address2"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_addr[$lang]." 3</td>
	<td><input type=\"text\" name=\"address3\" value=\"".$_REQUEST["address3"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_zipcode[$lang]."</td>
	<td><input type=\"text\" name=\"zipcode\" value=\"".$_REQUEST["zipcode"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_city[$lang]."</td>
	<td><input type=\"text\" name=\"city\" value=\"".$_REQUEST["city"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_state[$lang]."</td>
	<td><input type=\"text\" name=\"state\" value=\"".$_REQUEST["state"]."\"></td>
</tr><tr>
	<td align=\"right\">".$txt_draw_client_info_country[$lang]."</td>
	<td><select name=\"country\">".cc_code_popup($_REQUEST["country"])."</select></td>
</tr></table>";
	$addr_skined = skin("frame",$client_addr,"");

	$HTML_admin_edit_data = "<a href=\"/dtc\">Go to login</a>
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<input type=\"hidden\" name=\"action\" value=\"new_user_request\">
<table>
<tr>
	<td>Login info:$login_skined</td>
	<td>Client info:$client_skined</td>
	<td>Client info:$addr_skined</td>
</tr></table>
<center><input type=\"submit\" name=\"Login\" value=\"Register\"></center>
</form>";

//	return $login_skined;
	return $HTML_admin_edit_data;
}

function layout_login_and_languages($login_skined,$language_selection_skined){
	return "
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">
<tr>
	<td width=\"1\" height=\"1\">$language_selection_skined</td>
	<td width=\"100%\"></td>
</tr><tr>
	<td colspan=\"2\">$login_skined</td>
</tr></table>";
}

?>