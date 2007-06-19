<?php

function register_user(){
// adm_login=aaa&reqadm_pass=&reqadm_pass2=&domain_name=toto&familyname=nymous&
// firstname=ano&compname=testsoc&email=toto@toto.com&phone=PARIS&
// fax=state&address1=1&address2=2&address3=3&zipcode=75991&city=paris&
// state=state&country=US&Login=Register

	global $txt_err_not_registering;
	global $txt_product_id_not_valid;
	global $txt_user_login_incorrect;
	global $txt_username_invalid_root_or_debian;
	global $txt_password_format_incorrect;
	global $txt_passwords_do_not_match;
	global $txt_vps_location_not_selected;
	global $txt_email_does_not_look_like_correct;
	global $txt_required_field_family_name_missing;
	global $txt_required_field_first_name_missing;
	global $txt_could_not_find_vps_server_in_db;
	global $txt_country_code_seems_incorrect;
	global $txt_is_company_radio_button_wrong;
	global $txt_required_field_zipcode_missing;
	global $txt_required_field_city_missing;
	global $txt_selling_conditions_not_accepted;
	global $txt_username_already_taken_try_again;
	global $lang;

	global $pro_mysql_admin_table;
	global $pro_mysql_new_admin_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_server_table;
	global $conf_webmaster_email_addr;
	global $conf_selling_conditions_url;


	global $secpayconf_currency_letters;

	get_secpay_conf();

	// Check if all fields are blank, in wich case don't display error
	if((!isset($_REQUEST["reqadm_login"]) || $_REQUEST["reqadm_login"] == "")
	&& (!isset($_REQUEST["reqadm_pass"]) || $_REQUEST["reqadm_pass"] == "" )
	&& (!isset($_REQUEST["reqadm_pass2"]) || $_REQUEST["reqadm_pass2"] == "")
	&& (!isset($_REQUEST["domain_name"]) || $_REQUEST["domain_name"] == "")
	&& (!isset($_REQUEST["familyname"]) || $_REQUEST["familyname"] == "")
	&& (!isset($_REQUEST["firstname"]) || $_REQUEST["firstname"] == "")
	&& (!isset($_REQUEST["email"]) || $_REQUEST["email"] == "")
	&& (!isset($_REQUEST["phone"]) || $_REQUEST["phone"] == "")
	&& (!isset($_REQUEST["address1"]) || $_REQUEST["address1"] == "")
	&& (!isset($_REQUEST["zipcode"]) || $_REQUEST["zipcode"] == "")
	&& (!isset($_REQUEST["city"]) || $_REQUEST["city"] == "")
	&& (!isset($_REQUEST["firstname"]) || $_REQUEST["firstname"] == "")){
		$ret["err"] = 1;
		$ret["mesg"] = $txt_err_not_registering[$lang];
		return $ret;
	}

	if(isset($_REQUEST["product_id"])){
		$esc_product_id = addslashes($_REQUEST["product_id"]);
	}

	if(!isRandomNum($esc_product_id)){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_product_id_not_valid[$lang];
		return $ret;
	}
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='$esc_product_id';";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$ret["err"] = 2;
		$ret["mesg"] = "Product not found in database";
	}else{
		$db_product = mysql_fetch_array($r);
	}

	// Do field format checking and escaping for all fields
	if(!isFtpLogin($_REQUEST["reqadm_login"])){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_user_login_incorrect[$lang];
		return $ret;
	}
	if($_REQUEST["reqadm_login"] == "root" || $_REQUEST["reqadm_login"] == "debian-sys-maint"){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_username_invalid_root_or_debian[$lang];
		return $ret;
	}
	if(!isDTCPassword($_REQUEST["reqadm_pass"])){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_password_format_incorrect[$lang];
		return $ret;
	}
	if($_REQUEST["reqadm_pass"] != $_REQUEST["reqadm_pass2"]){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_passwords_do_not_match[$lang];
		return $ret;
	}
	// If shared or ssl hosting, we MUST do type checkings
	if($db_product["heb_type"] == "shared" || $db_product["heb_type"] == "ssl"){
		if(!isHostnameOrIP($_REQUEST["domain_name"])){
			$ret["err"] = 2;
			$ret["mesg"] = $txt_domain_name_does_not_look_like_correct[$lang];
			return $ret;
		}
	// If not a shared or ssl account, we don't care if it's umpty, but we take care of mysql insertion anyway
	}else{
		if($_REQUEST["domain_name"] != "" && (!isHostnameOrIP($_REQUEST["domain_name"]))){
			$ret["err"] = 2;
			$ret["mesg"] = $txt_domain_name_does_not_look_like_correct[$lang];
			return $ret;
		}
	}
	if($db_product["heb_type"] == "vps"){
		if($_REQUEST["vps_server_hostname"] == "-1"){
			$ret["err"] = 2;
			$ret["mesg"] = $txt_vps_location_not_selected[$lang];
			return $ret;
		}
		$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE hostname='".addslashes($_REQUEST["vps_server_hostname"])."';";
		$r = mysql_query($q)or die("Cannot query $q ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$ret["err"] = 2;
			$ret["mesg"] = $txt_could_not_find_vps_server_in_db[$lang];
			return $ret;
		}
	}
	if(!isValidEmail($_REQUEST["email"])){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_email_does_not_look_like_correct[$lang];
		return $ret;
	}

	if(!isset($_REQUEST["familyname"]) || $_REQUEST["familyname"]==""){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_required_field_family_name_missing[$lang];
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
		$ret["mesg"] = $txt_required_field_first_name_missing[$lang];
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

	if (!get_magic_quotes_gpc()){
		$esc_vat_num = addslashes($_REQUEST["vat_num"]);
	}else{
		$esc_vat_num = $_REQUEST["vat_num"];
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
		$ret["mesg"] = $txt_required_field_zipcode_missing[$lang];
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
		$ret["mesg"] = $txt_required_field_city_missing[$lang];
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

	if (!get_magic_quotes_gpc()){
		$esc_custom_notes = addslashes($_REQUEST["custom_notes"]);
	}else{
		$esc_custom_notes = $_REQUEST["custom_notes"];
	}

	if(!ereg("^([A-Z])([A-Z])\$",$_REQUEST["country"])){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_country_code_seems_incorrect[$lang];
		return $ret;
	}

	if($_REQUEST["iscomp"] == "yes"){
		$esc_comp = "yes";
	}else if($_REQUEST["iscomp"] == "no"){
		$esc_comp = "no";
	}else{
		$ret["err"] = 2;
		$ret["mesg"] = $txt_is_company_radio_button_wrong[$lang];
		return $ret;
	}

	if($conf_selling_conditions_url != "none" && (!isset($_REQUEST["condition"]) || $_REQUEST["condition"] != "yes")){
		$ret["err"] = 2;
		$ret["mesg"] = $txt_selling_conditions_not_accepted[$lang];
		return $ret;
	}

	$q = "SELECT adm_login FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["reqadm_login"]."';";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$ret["err"] = 3;
		$ret["mesg"] = $txt_username_already_taken_try_again[$lang];
		return $ret;
	}
	$q = "SELECT reqadm_login FROM $pro_mysql_new_admin_table WHERE reqadm_login='".$_REQUEST["reqadm_login"]."';";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n > 0){
		$ret["err"] = 3;
		$ret["mesg"] = $txt_username_already_taken_try_again[$lang];
		return $ret;
	}
	$vps_add1 = "";
	$vps_add2 = "";
	$vps_mail_add1 = "";
	if($db_product["heb_type"] == "vps"){
		if (!get_magic_quotes_gpc()){
			$esc_vps_os = addslashes($_REQUEST["vps_os"]);
		}else{
			$esc_vps_os = $_REQUEST["vps_os"];
		}
		$vps_add1 = ",vps_location,vps_os";
		$vps_add2 = ",'".$_REQUEST["vps_server_hostname"]."','$esc_vps_os'";
		$vps_mail_add1 = "VPS hostname: ".$_REQUEST["vps_server_hostname"];
	}

	// MaxMind: Rudd-O
	get_secpay_conf();
	global $secpayconf_maxmind_license_key;
	global $secpayconf_use_maxmind;
	if ($secpayconf_use_maxmind == "yes"){
		// This has been done in dtc/shared/dtc_lib.php
		// but could be removed from there... As you like!
		require_once("../shared/maxmind/HTTPBase.php");
		require_once("../shared/maxmind/CreditCardFraudDetection.php");
		$hash = array();
		$hash["i"] = $_SERVER["REMOTE_ADDR"];
		$hash["city"] = $_REQUEST["city"];
		$hash["postal"] = $_REQUEST["zipcode"];
		$hash["country"] = $_REQUEST["country"];
		$maildomain = split("@",$_REQUEST["email"],2);
		$hash["domain"] = $maildomain[1];
		$hash["custPhone"] = $_REQUEST["phone"];
		$hash["license_key"] = $secpayconf_maxmind_license_key;
		if (isset($_SERVER["X_HTTP_FORWARDED_FOR"]))
			$hash["forwardedIP"] = $_SERVER["X_HTTP_FORWARDED_FOR"];
		$hash["emailMD5"] = md5($_REQUEST["email"]);
		$hash["usernameMD5"] = md5($_REQUEST["reqadm_login"]);
		$hash["passwordMD5"] = md5($_REQUEST["reqadm_pass"]);
		trigger_error("MaxMind input: ".serialize($hash),E_USER_NOTICE);
		$ccfs = new CreditCardFraudDetection; $ccfs->isSecure = 1;
		$ccfs->input($hash); $ccfs->query(); $maxmind_output = $ccfs->output();
		trigger_error("MaxMind output: ".serialize($maxmind_output),E_USER_NOTICE);
	} else {
		$maxmind_output = "";
	}
	// end MaxMind

	$q = "INSERT INTO $pro_mysql_new_admin_table
(reqadm_login,
reqadm_pass,
domain_name,
family_name,
first_name,
comp_name,
vat_num,
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
country,
product_id,
custom_notes,
shopper_ip,
date,
time,
maxmind_output$vps_add1
)
VALUES('".$_REQUEST["reqadm_login"]."',
'".$_REQUEST["reqadm_pass"]."',
'".$_REQUEST["domain_name"]."',
'$esc_familyname',
'$esc_firstname',
'$esc_compname',
'$esc_vat_num',
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
'".$_REQUEST["country"]."',
'$esc_product_id',
'$esc_custom_notes',
'".$_SERVER["REMOTE_ADDR"]."',
'".date("Y-m-d")."',
'".date("H:i:s")."',
'".mysql_real_escape_string(serialize($maxmind_output))."'$vps_add2)";
	$r = mysql_query($q)or die("Cannot query  \"$q\" !!! Line: ".__LINE__." File: ".__FILE__." MySQL said: ".mysql_error());
	$id = mysql_insert_id();
	$ret["err"] = 0;
	$ret["mesg"] = "Query ok!";
	$ret["id"] = $id;

	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='$esc_product_id';";
	$r = mysql_query($q)or die("Cannot querry $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		echo "<font color=\"red\">Cannot find product id!</font>";
		$the_prod = $esc_product_id." (0 $secpayconf_currency_letters)";
	}else{
		$a = mysql_fetch_array($r);
		$the_prod = $a["name"]." (".$a["price_dollar"]." $secpayconf_currency_letters)";
	}

	$mail_content = "
Somebody tried to register an account. Here is the details of the new user:

login: ".$_REQUEST["reqadm_login"]."
pass: ".$_REQUEST["reqadm_pass"]."
domain: ".$_REQUEST["domain_name"]."
Company name: ".$_REQUEST["compname"]."
First name: ".$_REQUEST["firstname"]."
Family name: ".$_REQUEST["familyname"]."
Email: ".$_REQUEST["email"]."
Phone: $esc_phone
Fax: $esc_fax
Addr: ".$_REQUEST["address1"]." ".$_REQUEST["address2"]." ".$_REQUEST["address3"]."
Zipcode: $esc_zipcode
City: ".$_REQUEST["city"]."
State: ".$_REQUEST["state"]."
Contry: ".$_REQUEST["country"]."
Shopper ip: ".$_SERVER["REMOTE_ADDR"]."
Product id: $the_prod
Customer note: ".$_REQUEST["custom_notes"]."
$vps_mail_add1
";

	$headers = "From: DTC Robot <$conf_webmaster_email_addr>";
	mail($conf_webmaster_email_addr, "[DTC] Somebody tried to register an account", $mail_content, $headers);

	return $ret;
}

function registration_form(){
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_use_text_menu;
	global $txt_login_title;
        global $txt_client_info;
        global $txt_register_new_account;
        global $txt_go_to_login;
        global $txt_product;
        global $txt_login_info;
        global $txt_confirm_pass;
        global $txt_desired_domain_name;	
        global $conf_skin;
        global $txt_is_company;
        global $txt_yes;
        global $txt_no;
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
	global $txt_register_custom_message_title;
	global $txt_vat_number;
	global $txt_selling_conditions;
	global $txt_i_agree_to_the;
	global $txt_vps_operating_system;
	global $txt_vps_location;
	global $txt_client_addr_title;
	global $txt_vat_number;

	global $pro_mysql_product_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;

	global $conf_selling_conditions_url;
	global $secpayconf_currency_symbol;

	get_secpay_conf();

	if(isset($_REQUEST["product_id"]) && isRandomNum($_REQUEST["product_id"])){
		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["product_id"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("Product ID not found here line ".__LINE__." file ".__FILE__);
		}
		$a = mysql_fetch_array($r);
		$heb_type_condition = " heb_type='".$a["heb_type"]."' ";
		$heb_type = $a["heb_type"];
	}else{
		$heb_type_condition = " 1 ";
		$heb_type = "all";
	}

	$prod_popup = "";
	$p_jscript = " prod_popup_htype = new Array();";
	$q = "SELECT * FROM $pro_mysql_product_table WHERE $heb_type_condition AND renew_prod_id='0' AND private='no' ORDER BY id";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$prod_popup .= "<option value=\"-1\">Please select!</optioon>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$p_jscript .= " prod_popup_htype[".$a["id"]."] = '".$a["heb_type"]."';\n";
		if(isset($_REQUEST["product_id"]) && $a["id"] == $_REQUEST["product_id"]){
			$prod_popup .= "<option value=\"".$a["id"]."\" selected>".$a["name"]." / ".$a["price_dollar"]."$secpayconf_currency_symbol</option>\n";
		}else{
			$prod_popup .= "<option value=\"".$a["id"]."\">".$a["name"]." / ".$a["price_dollar"]."$secpayconf_currency_symbol</option>\n";
		}
	}
	$prod_popup = "<select onChange=\"hostingProductChanged();\" name=\"product_id\">".$prod_popup."</select>";

	$q = "SELECT $pro_mysql_vps_server_table.hostname,$pro_mysql_vps_server_table.location
	FROM $pro_mysql_vps_ip_table,$pro_mysql_vps_server_table
	WHERE $pro_mysql_vps_ip_table.vps_server_hostname=$pro_mysql_vps_server_table.hostname
	AND $pro_mysql_vps_ip_table.available='yes'
	GROUP BY $pro_mysql_vps_server_table.location;";
	$r = mysql_query($q)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$vps_location_popup = "<option value=\"-1\">Please select!</optioon>";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if(isset($_REQUEST["vps_server_hostname"]) && $_REQUEST["vps_server_hostname"] == $a["hostname"]){
			$selected = " selected ";
		}else{
			$selected = "";
		}
		$vps_location_popup .= "<option value=\"".$a["hostname"]."\" $selected>".$a["location"]."</optioon>";
	}

	if(isset($_REQUEST["reqadm_login"]))	$frm_login = $_REQUEST["reqadm_login"];
	else	$frm_login = "";

	if(isset($_REQUEST["domain_name"]))	$frm_domain_name = $_REQUEST["domain_name"];
	else	$frm_domain_name = "";

	if(isset($_REQUEST["firstname"]))	$frm_firstname = $_REQUEST["firstname"];
	else	$frm_firstname = "";

	if(isset($_REQUEST["familyname"]))	$frm_family_name = $_REQUEST["familyname"];
	else	$frm_family_name = "";

	if(isset($_REQUEST["compname"]))	$frm_compname = $_REQUEST["compname"];
	else	$frm_compname = "";

	if(isset($_REQUEST["vat_num"]))	$frm_vat_num = $_REQUEST["vat_num"];
	else	$frm_vat_num = "";

	if(isset($_REQUEST["email"]))	$frm_email = $_REQUEST["email"];
	else	$frm_email = "";

	if(isset($_REQUEST["phone"]))	$frm_phone = $_REQUEST["phone"];
	else	$frm_phone = "";

	if(isset($_REQUEST["fax"]))	$frm_fax = $_REQUEST["fax"];
	else	$frm_fax = "";

	if(isset($_REQUEST["address1"]))	$frm_addr1 = $_REQUEST["address1"];
	else	$frm_addr1 = "";

	if(isset($_REQUEST["address2"]))	$frm_addr2 = $_REQUEST["address2"];
	else	$frm_addr2 = "";

	if(isset($_REQUEST["address3"]))	$frm_addr3 = $_REQUEST["address3"];
	else	$frm_addr3 = "";

	if(isset($_REQUEST["zipcode"]))		$frm_zipcode = $_REQUEST["zipcode"];
	else	$frm_zipcode = "";

	if(isset($_REQUEST["city"]))	$frm_city = $_REQUEST["city"];
	else	$frm_city = "";

	if(isset($_REQUEST["state"]))	$frm_state = $_REQUEST["state"];
	else	$frm_state = "";

	if(isset($_REQUEST["country"]))	$frm_country = $_REQUEST["country"];
	else	$frm_country = "";

	if(isset($_REQUEST["custom_notes"]))	$frm_custom_notes = $_REQUEST["custom_notes"];
	else	$frm_custom_notes = "";

	if($heb_type == "all" || $heb_type == "shared" || $heb_type == "ssl"){
		$domname_hidden = " style=\"white-space:nowrap;\" ";
	}else{
		$domname_hidden = " style=\"display:none;visibility:hidden;white-space:nowrap;\" ";
	}

	if($heb_type == "all" || $heb_type == "vps"){
		$vps_hidden = " ";
	}else{
		$vps_hidden = " style=\"display:none;visibility:hidden;\" ";
	}

	$debian_selected = " ";
	$centos_selected = " ";
	$gentoo_selected = " ";
	$netbsd_selected = " ";
	if(isset($_REQUEST["vps_os"]) && $_REQUEST["vps_os"] == "debian")	$debian_selected = " selected ";
	if(isset($_REQUEST["vps_os"]) && $_REQUEST["vps_os"] == "centos")	$centos_selected = " selected ";
	if(isset($_REQUEST["vps_os"]) && $_REQUEST["vps_os"] == "gentoo")	$gentoo_selected = " selected ";
	if(isset($_REQUEST["vps_os"]) && $_REQUEST["vps_os"] == "netbsd")	$netbsd_selected = " selected ";

	$prod_popup = "<table>
<tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_product[$lang].": </td><td>".$prod_popup."</td>
</td><tr>
	<td style=\"white-space: nowrap;text-align: right;\"><div name=\"domname_text\" id=\"domname_text\" $domname_hidden>".$txt_desired_domain_name[$lang].":</div></td>
	<td><div name=\"domname_field\" id=\"domname_field\" $domname_hidden>www.<input type=\"text\" name=\"domain_name\" value=\"$frm_domain_name\"></div></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\"><div name=\"vps_popup_text\" id=\"vps_popup_text\" $vps_hidden>".$txt_vps_location[$lang]."</div></td>
	<td><div name=\"vps_popup_field\" id=\"vps_popup_field\" $vps_hidden><select name=\"vps_server_hostname\">$vps_location_popup</select></div></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\"><div name=\"vps_ospopup_text\" id=\"vps_ospopup_text\" $vps_hidden></div></td>
	<td><div name=\"vps_ospopup_field\" id=\"vps_ospopup_field\" $vps_hidden><select name=\"vps_os\">
		<option value=\"debian\" $debian_selected>Debian</option>
		<option value=\"centos\" $centos_selected>CentOS</option>
		<option value=\"gentoo\" $gentoo_selected>Gentoo</option>
		<option value=\"netbsd\" $netbsd_selected>NetBSD</option></select></div></td>
</tr></table>";

	$login_info = "<table>
<tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_login_login[$lang]."</td>
	<td><input type=\"text\" name=\"reqadm_login\" value=\"$frm_login\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_login_pass[$lang]."</td>
	<td><input type=\"password\" name=\"reqadm_pass\" value=\"\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">$txt_confirm_pass[$lang]:</td>
	<td><input type=\"password\" name=\"reqadm_pass2\" value=\"\"></td>
</tr></table>";
#	$login_skined = skin("frame",$login_info,"");
	$login_skined = $login_info;

	$compyes = "";
	$compno = "";
	if(isset($_REQUEST["iscomp"]) && $_REQUEST["iscomp"] == "yes"){
		$compyes = "checked";
	}else if(isset($_REQUEST["iscomp"]) && $_REQUEST["iscomp"] == "no"){
		$compno = " checked";
	}
	$client_info = "<table>
<tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_familyname[$lang]."</td>
	<td><input type=\"text\" name=\"familyname\" value=\"$frm_family_name\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_firstname[$lang]."</td>
	<td><input type=\"text\" name=\"firstname\" value=\"$frm_firstname\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">$txt_is_company[$lang]?</td>
	<td><input type=\"radio\" name=\"iscomp\" value=\"yes\"$compyes>$txt_yes[$lang]
<input type=\"radio\" name=\"iscomp\" value=\"no\"$compno>$txt_no[$lang]</td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_comp_name[$lang]."</td>
	<td><input type=\"text\" name=\"compname\" value=\"$frm_compname\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_vat_number[$lang]."</td>
	<td><input type=\"text\" name=\"vat_num\" value=\"$frm_vat_num\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_email[$lang]."</td>
	<td><input type=\"text\" name=\"email\" value=\"$frm_email\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_phone[$lang]."</td>
	<td><input type=\"text\" name=\"phone\" value=\"$frm_phone\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_fax[$lang]."</td>
	<td><input type=\"text\" name=\"fax\" value=\"$frm_fax\"></td>
</tr></table>";
//	$client_skined = skin("frame",$client_info,"");
	$client_skined = $client_info;

	$client_addr = "<table>
<tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_addr[$lang]."</td>
	<td><input type=\"text\" name=\"address1\" value=\"$frm_addr1\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_addr[$lang]." 2</td>
	<td><input type=\"text\" name=\"address2\" value=\"$frm_addr2\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_addr[$lang]." 3</td>
	<td><input type=\"text\" name=\"address3\" value=\"$frm_addr3\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_zipcode[$lang]."</td>
	<td><input type=\"text\" name=\"zipcode\" value=\"$frm_zipcode\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_city[$lang]."</td>
	<td><input type=\"text\" name=\"city\" value=\"$frm_city\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_state[$lang]."</td>
	<td><input type=\"text\" name=\"state\" value=\"$frm_state\"></td>
</tr><tr>
	<td style=\"white-space: nowrap;text-align: right;\">".$txt_draw_client_info_country[$lang]."</td>
	<td><select name=\"country\">".cc_code_popup($frm_country)."</select></td>
</tr></table>";
#	$addr_skined = skin("frame",$client_addr,"");
	$addr_skined = $client_addr;

	if($conf_selling_conditions_url != "none"){
		$conditions = "<input type=\"checkbox\" name=\"condition\" value=\"yes\"> ".$txt_i_agree_to_the[$lang]." <a href=\"$conf_selling_conditions_url\">".$txt_selling_conditions[$lang]."</a>";
	}else{
		$conditions = "";
	}

	$HTML_admin_edit_data = "<a href=\"/dtc\">$txt_go_to_login[$lang]</a>
<script language=\"javascript\">

$p_jscript

var DHTML = (document.getElementById || document.all || document.layers);
function getObj(name){
	if (document.getElementById){
		this.obj = document.getElementById(name);
		this.style = document.getElementById(name).style;
	}else if(document.all){
		this.obj = document.all[name];
		this.style = document.all[name].style;
	}else{
		this.obj = document.layers[name];
		this.style = document.layers[name];
	}
}

function hostingProductChanged(){
	if (!DHTML) return;
	if(document.newuser_form.product_id.value == -1){
		return;
	}
	hosting_type = prod_popup_htype[document.newuser_form.product_id.value];
	var a = new getObj('domname_field');
	var b = new getObj('domname_text');
	var c = new getObj('vps_popup_field');
	var d = new getObj('vps_popup_text');
	var e = new getObj('vps_ospopup_text');
	var f = new getObj('vps_ospopup_field');
	if(hosting_type == 'vps'){
		a.style.visibility = 'hidden';
		a.style.display = 'none';
		b.style.visibility = 'hidden';
		b.style.display = 'none';

		c.style.visibility = 'visible';
		c.style.display = 'block';
		d.style.visibility = 'visible';
		d.style.display = 'block';
		e.style.visibility = 'visible';
		e.style.display = 'block';
		f.style.visibility = 'visible';
		f.style.display = 'block';
	}else{
		a.style.visibility = 'visible';
		a.style.display = 'block';
		b.style.visibility = 'visible';
		b.style.display = 'block';

		c.style.visibility = 'hidden';
		c.style.display = 'none';
		d.style.visibility = 'hidden';
		d.style.display = 'none';
		e.style.visibility = 'hidden';
		e.style.display = 'none';
		f.style.visibility = 'hidden';
		f.style.display = 'none';
	}
}

</script>
<form name=\"newuser_form\" action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<input type=\"hidden\" name=\"action\" value=\"new_user_request\">
<table>
<tr>
	<td valign=\"top\"><h3>".$txt_product[$lang]."</h3>
	$prod_popup<br>
<h3>".$txt_login_info[$lang].":</h3> ".$login_skined."</td>
	<td width=\"4\" background=\"gfx/skin/frame/border_2.gif\"></td>
	<td valign=\"top\"><h3>".$txt_client_info[$lang]."</h3> $client_skined</td>
	<td width=\"4\" background=\"gfx/skin/frame/border_2.gif\"></td>
	<td valign=\"top\"><h3>".$txt_client_addr_title[$lang]."</h3> $addr_skined</td>
</tr></table>
$conditions
<table border=\"0\">
<tr>
	<td>".$txt_register_custom_message_title[$lang]."</td>
	<td><textarea name=\"custom_notes\" cols=\"50\" rows=\"5\">$frm_custom_notes</textarea></td>
	<td><input type=\"submit\" name=\"Login\" value=\"Register\"></td>
</tr>
</table>
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
