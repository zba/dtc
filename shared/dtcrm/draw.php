<?php

require_once "$dtcshared_path/dtcrm/registry_calls.php";
// require_once "$dtcshared_path/dtcrm/cc_code_popup.php";

$registration_added_price = 1;

$pro_mysql_handle_table = "handle";

function drawTransfer(){

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
}

function drawAdminTools_AddDomain($admin){
	global $lang;
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $pro_mysql_handle_table;

	$form_check_domain = "
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
<input type=\"hidden\" name=\"add_regORtrans\" value=\"register\">
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
www.<input type=\"text\" name=\"toreg_domain\" value=\"\">
<select name=\"toreg_extention\">
<option value=\".com\" selected>.com</option>
<option value=\".net\">.net</option>
<option value=\".org\">.org</option>
<option value=\".biz\">.biz</option>
<option value=\".info\">.info</option>
<option value=\".name\">.name</option>
</select><input type=\"submit\" value=\"Ok\">
</form>
";

	$period_popup = "<select name=\"toreg_period\">
<option value=\"1\">1 years</value>
<option value=\"2\">2 years</value>
<option value=\"3\">3 years</value>
<option value=\"4\">4 years</value>
<option value=\"5\">5 years</value>
<option value=\"6\">6 years</value>
<option value=\"7\">7 years</value>
<option value=\"8\">8 years</value>
<option value=\"9\">9 years</value>
<option value=\"10\">10 years</value>
</select>";

	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";

	if($_REQUEST["add_domain_type"] == "domregandhosting" ||
	$_REQUEST["add_domain_type"] == "domreg"){
		if($_REQUEST["add_regORtrans"] == "register"){
			if($_REQUEST["toreg_domain"] == "" || !isset($_REQUEST["toreg_domain"]) ||
			$_REQUEST["toreg_extention"] == "" || !isset($_REQUEST["toreg_extention"])){
				$out .= "<b><u>Register a domain name</u></b><br>
<i><u>Step 1: Verify availability</u></i><br><br>
Enter the domain name you want to register:<br>
$form_check_domain";
			}else{
				$fqdn = $_REQUEST["toreg_domain"] . $_REQUEST["toreg_extention"];
				$domlookup = registry_check_availability($fqdn);
				if($domlookup["is_success"] != 1){
					die("Could not connect to domain registration server: please try again later !!!");
				}
				if($domlookup["attributes"]["status"] == "available"){
					// DOMAIN IS AVAILABLE, PROCEED DO REGISTRATION
					$out = "<b><u>Register a domain name</u></b><br>
<i><u>Step 1: Verify availability</u></i><br><br>
Domain name <b>$fqdn</b> is available for registration.<br><br>

<i><u>Step 2: Enter whois informations</u></i><br><br>
";


					if(isset($_REQUEST["dtcrm_owner_hdl"]) && $_REQUEST["dtcrm_owner_hdl"] != "" &&
						isset($_REQUEST["dtcrm_admin_hdl"]) && $_REQUEST["dtcrm_admin_hdl"] != "" &&
						isset($_REQUEST["dtcrm_billing_hdl"]) && $_REQUEST["dtcrm_billing_hdl"] != "" &&
						isset($_REQUEST["toreg_dns1"]) && $_REQUEST["toreg_dns1"] != "" &&
						isset($_REQUEST["toreg_dns2"]) && $_REQUEST["toreg_dns2"] != "" &&
						$_REQUEST["toreg_period"] >= 1 && $_REQUEST["toreg_period"] <= 10){
						$out .= "Registration for <b>" . $_REQUEST["toreg_period"] . " years</b><br>";
						$out .= "DNS1: " . $_REQUEST["toreg_dns1"] . "<br>";
						$out .= "DNS2: " . $_REQUEST["toreg_dns2"] . "<br><br>";
						$price = registry_get_domain_price($fqdn,$_REQUEST["toreg_period"]);
						$fqdn_price = $price["attributes"]["price"];
						$fqdn_price += $_REQUEST["toreg_period"] * 2;
						$out .= "<i><u>Step 3: Proceed to registration</u></i>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">   
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">     
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">     
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
<input type=\"hidden\" name=\"add_regORtrans\" value=\"register\">
<input type=\"hidden\" name=\"toreg_domain\" value=\"".$_REQUEST["toreg_domain"]."\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"".$_REQUEST["toreg_extention"]."\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
<input type=\"hidden\" name=\"dtcrm_owner_hdl\" value=\"".$_REQUEST["dtcrm_owner_hdl"]."\">
<input type=\"hidden\" name=\"dtcrm_admin_hdl\" value=\"".$_REQUEST["dtcrm_admin_hdl"]."\">
<input type=\"hidden\" name=\"dtcrm_billing_hdl\" value=\"".$_REQUEST["dtcrm_billing_hdl"]."\">
<input type=\"hidden\" name=\"toreg_dns1\" value=\"".$_REQUEST["toreg_dns1"]."\">
<input type=\"hidden\" name=\"toreg_dns2\" value=\"".$_REQUEST["toreg_dns2"]."\">
<input type=\"hidden\" name=\"toreg_dns3\" value=\"".$_REQUEST["toreg_dns3"]."\">
<input type=\"hidden\" name=\"toreg_dns4\" value=\"".$_REQUEST["toreg_dns4"]."\">
<input type=\"hidden\" name=\"toreg_dns5\" value=\"".$_REQUEST["togeg_dns5"]."\">
<input type=\"hidden\" name=\"toreg_dns6\" value=\"".$_REQUEST["toreg_dns6"]."\">

<input type=\"hidden\" name=\"toreg_dns1_ip\" value=\"".$_REQUEST["toreg_dns1_ip"]."\">
<input type=\"hidden\" name=\"toreg_dns2_ip\" value=\"".$_REQUEST["toreg_dns2_ip"]."\">
<input type=\"hidden\" name=\"toreg_dns3_ip\" value=\"".$_REQUEST["toreg_dns3_ip"]."\">
<input type=\"hidden\" name=\"toreg_dns4_ip\" value=\"".$_REQUEST["toreg_dns4_ip"]."\">
<input type=\"hidden\" name=\"toreg_dns5_ip\" value=\"".$_REQUEST["togeg_dns5_ip"]."\">
<input type=\"hidden\" name=\"toreg_dns6_ip\" value=\"".$_REQUEST["toreg_dns6_ip"]."\">
<input type=\"hidden\" name=\"toreg_period\" value=\"".$_REQUEST["toreg_period"]."\">
";

						if($admin["info"]["id_client"] != 0){
							//print_r($admin["client"]);
							$remaining = $admin["client"]["dolar"];
						}else{
							$out .= "You don't have a client ID. Please contact us.<br>";
							$remaining = 0;
						}
						$out .= "
Remaining on your account: \$" . $remaining . "<br>
Total price: \$". $fqdn_price . "<br><br>";
						if($fqdn_price > $remaining){
							$out .= "
You currently don't have enough funds on your account. You will be
redirected to our paiement system.<br><br>
<input type=\"submit\" value=\"Proceed to paiement\">
</form>";
						}else{
							if($_REQUEST["toreg_confirm_register"] == "yes"){

///////////////////////////////////////
// START OF DOMAIN NAME REGISTRATION //
	$owner_id = $_REQUEST["dtcrm_owner_hdl"];
	$billing_id = $_REQUEST["dtcrm_billing_hdl"];
	$admin_id = $_REQUEST["dtcrm_admin_hdl"];

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

	$regz = registry_register_domain($adm_login,$adm_pass,$fqdn,$period,$contacts,$dns_servers);

	if($regz["is_success"] == 0){
		$out .= "<font color=\"red\"><b>Registration failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
	}else{
		$out .= "<font color=\"green\"><b>Registration succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>

Click <a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">here</a>
to refresh the menu or add another domain name.
";
		addDomainToUser($adm_login,$adm_pass,$fqdn);
		unset($ns_ar);
		$ns_ar[] = $_REQUEST["toreg_dns1"];
		$ns_ar[] = $_REQUEST["toreg_dns2"];
		if(isset($_REQUEST["toreg_dns3"]) && $_REQUEST["toreg_dns3"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns3"];
		if(isset($_REQUEST["toreg_dns4"]) && $_REQUEST["toreg_dns4"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns4"];
		if(isset($_REQUEST["toreg_dns5"]) && $_REQUEST["toreg_dns5"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns5"];
		if(isset($_REQUEST["toreg_dns6"]) && $_REQUEST["toreg_dns6"] != "")
			$ns_ar[] = $_REQUEST["toreg_dns6"];
		newWhois($fqdn,$owner_id,$billing_id,$admin_id,$period,$ns_ar);
	}
//	print_r($regz);
// END OF DOMAIN NAME REGISTRATION //
/////////////////////////////////////

								$out .= "
";
							}else{
								$out .= "
You have enough funds on your account to proceed registration. Press
the confirm button and your order will be proceeded.<br>
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
<input type=\"submit\" value=\"Proceed to registration\">
</form>
";
							}
						}
// https://dtc.gplhost.com/dtc/?adm_login=zigo&adm_pass=lazaro&addrlink=adddomain&action=dtcrm_add_domain&add_regORtrans=register&toreg_domain=yuglux24&toreg_extention=.com&add_domain_type=domreg&dtcrm_owner_hdl=1&dtcrm_billing_hdl=1&dtcrm_admin_hdl=1&toreg_dns1=default&toreg_dns2=default&toreg_dns3=&toreg_dns4=&toreg_dns5=&toreg_dns6=
// https://dtc.gplhost.com/dtc/?adm_login=zigo&adm_pass=lazaro&addrlink=adddomain&action=dtcrm_add_domain&add_regORtrans=register&toreg_domain=yuglux24&toreg_extention=.com&add_domain_type=domreg&dtcrm_owner_hdl=1&dtcrm_admin_hdl=1&dtcrm_billing_hdl=1&toreg_dns1=&toreg_dns2=&toreg_dns3=&toreg_dns4=&toreg_dns5=&toreg_dns6=
					}else{
						$out .= "
Please select the 3 contact handles you want to use for registering that
domain name.<br><br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">  
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">  
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
<input type=\"hidden\" name=\"add_regORtrans\" value=\"register\">
<input type=\"hidden\" name=\"toreg_domain\" value=\"".$_REQUEST["toreg_domain"]."\">
<input type=\"hidden\" name=\"toreg_extention\" value=\"".$_REQUEST["toreg_extention"]."\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
";
						$out .= whoisHandleSelection($admin);
						$out .= "
<br>
Please enter now the DNS server ip or hostname. If you want to configurate your domain here,
leave it with value \"default\".<br>
DNS1 host:<input size=\"16\" type=\"text\" name=\"toreg_dns1\" value=\"default\">
ip:<input size=\"14\" type=\"text\" name=\"toreg_dns1_ip\" value=\"\"><br>
DNS2 host:<input size=\"16\" type=\"text\" name=\"toreg_dns2\" value=\"default\">
ip:<input size=\"14\" type=\"text\" name=\"toreg_dns2_ip\" value=\"\"><br>
<i>Optional:</i><br>
DNS3 host:<input size=\"16\" type=\"text\" name=\"toreg_dns3\" value=\"\">
ip:<input size=\"14\" type=\"text\" name=\"toreg_dns3_ip\" value=\"\"><br>
DNS4 host:<input size=\"16\" type=\"text\" name=\"toreg_dns4\" value=\"\">
ip:<input size=\"14\" type=\"text\" name=\"toreg_dns4_ip\" value=\"\"><br>
DNS5 host:<input size=\"16\" type=\"text\" name=\"toreg_dns5\" value=\"\">
ip:<input size=\"14\" type=\"text\" name=\"toreg_dns5_ip\" value=\"\"><br>
DNS6 host:<input size=\"16\" type=\"text\" name=\"toreg_dns6\" value=\"\">
ip:<input size=\"14\" type=\"text\" name=\"toreg_dns6_ip\" value=\"\"><br><br>
Select how long you want to register this domain name:<br>
$period_popup<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
					}
				}else{
					$out = "<b><u>Register a domain name</u></b><br>
<i><u>Step 1: Verify availability</u></i><br><br>
Sorry, the domain name <b>$fqdn</b> is
NOT available for registration. Registration server returned:<br><font color=\"red\">" . $srs_result["response_text"] . "</font>
<br><br>
Have another try:<br>
$form_check_domain";
				}
			}
		}else if($_REQUEST["add_regORtrans"] == "transfer"){
		}else{
			$out .= "<b><u>Do you want to transfer an existing domain or register a new domain?</u></b><br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"add_domain_type\" value=\"".$_REQUEST["add_domain_type"]."\">
<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
<input type=\"radio\" name=\"add_regORtrans\" value=\"register\" checked>Register a new domain<br>
<input type=\"radio\" name=\"add_regORtrans\" value=\"transfer\">Transfer an existing domain from another registrar<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
		}
	}else if($_REQUEST["add_domain_type"] == "hosting"){
	}else{
		$out .= "<b><u>What do you want to do:</u></b><br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"radio\" name=\"add_domain_type\" value=\"domregandhosting\" checked>Hosting + free registration/transfer<br>
<input type=\"radio\" name=\"add_domain_type\" value=\"domreg\">Domain registration<br>
<input type=\"radio\" name=\"add_domain_type\" value=\"hosting\">Hosting only<br>
<input type=\"submit\" value=\"Ok\">
</form>
";
	}
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

	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";
	if($eddomain["whois"] == "away"){
		if($_REQUEST["action"] == "transfer_domain"){
			$out .= "<b><u>Transfer ".$eddomain["name"]." from
another registrar to GPLHost:</u></b><br><br>

<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"dtcrm_action\" value=\"transfer_domain_2\">";
			$out .= whoisHandleSelection($admin);
			$out .= "<input type=\"submit\" value=\"Ok\">
</form>";
		}else{
			$out .= "Your domain name has been registred elsewhere (eg
not on this site). To order for it's transfer and management, please click
<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&action=transfer_domain\">here</a>.<br><br>
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

			$domain_name = $eddomain["name"];
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
	}else{
		$out .= "<br><br><b><u>Create a new nick-handle:</u></b><br>";
		$hidden_inputs = "<input type=\"hidden\" name=\"action\" value=\"create_nickhandle\">";
		$name_of_hdl = "<input type=\"text\" name=\"name\" value=\"".$hdl["name"]."\">";
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
	<td><input type=\"text\" name=\"company\" value=\"".$hdl["company"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Firstname$rf:</td>
	<td><input type=\"text\" name=\"firstname\" value=\"".$hdl["firstname"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Lastname$rf:</td>
	<td><input type=\"text\" name=\"lastname\" value=\"".$hdl["lastname"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Street address$rf:</td>
	<td><input type=\"text\" name=\"addr1\" value=\"".$hdl["addr1"]."\">
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Street address 2:<br><i>Optionnal</i></td>
	<td><input type=\"text\" name=\"addr2\" value=\"".$hdl["addr2"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Street address 3:<br><i>Optionnal</i></td>
	<td><input type=\"text\" name=\"addr3\" value=\"".$hdl["addr3"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>State:<br><i>If applicable</i></td>
	<td><input type=\"text\" name=\"state\" value=\"".$hdl["state"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>City:</td>
	<td><input type=\"text\" name=\"city\" value=\"".$hdl["city"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Zipcode$rf:</td>
	<td><input type=\"text\" name=\"zipcode\" value=\"".$hdl["zipcode"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Country$rf:</td>
	<td><select name=\"country\">".cc_code_popup($hdl["country"])."</select></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Phone number$rf:</td>
	<td><input type=\"text\" name=\"phone_num\" value=\"".$hdl["phone_num"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>Fax number:<br><i>Optional</i></td>
	<td><input type=\"text\" name=\"fax_num\" value=\"".$hdl["fax_num"]."\"></td>
</tr><tr>
	<td align=\"right\" width=\"1\" style=\"white-space: nowrap\" nowrap>email$rf:<br><i>MUST be a valid email</i></td>
	<td><input type=\"text\" name=\"email\" value=\"".$hdl["email"]."\"></td>
</tr><tr>
	<td></td><td><input type=\"submit\" value=\"Ok\"></td>
</tr></table>
</form>
";
	return $out;
}

function drawAdminTools_MyAccount($admin){
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $cc_code_array;

	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";

	$id_client = $admin["info"]["id_client"];

	if($id_client != 0){
		$client = $admin["client"];
		$out .=  "<b><u>Remaining money on my account:</u></b><br>
<font size=\"+2\">\$".$client["dolar"]."</font><br><br>
Refund my account with the following ammount:<br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"refund\">
<input type=\"text\" name=\"refund_amount\" value=\"\">
<input type=\"submit\" value=\"Ok\">
</form>
<hr width=\"90%\">
";

		$out .= "<center><b>Please tell us if the following is not correct:</b></center>";

		if($client["is_company"] == "yes"){
			$out .= "Company: ".$client["company_name"]."<br>";
		}
		$out .= "Firstname: " .$client["christname"]."<br>";
		$out .= "Familyname: " .$client["familyname"]."<br>";
		$out .= "Addresse 1: " .$client["addr1"]."<br>";
		$out .= "Addresse 2: " .$client["addr2"]."<br>";
		$out .= "Zipcode: " .$client["zipcode"]."<br>";
		$out .= "Sate: " .$client["state"]."<br>";
		$out .= "Country: " . $cc_code_array[ $client["country"] ] ."<br>";
		$out .= "Phone: " .$client["phone"]."<br>";
		$out .= "Fax: " .$client["fax"]."<br>";
		$out .= "Email: " .$client["email"]."<br>";
	}
	return $out;

}

function drawAdminTools_NameServers($admin){
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $pro_mysql_subdomain_table;
	global $pro_mysql_nameservers_table;
	global $pro_mysql_domain_table;


	$subdomain = $_REQUEST["subdomain"];
	$domain_name = $_REQUEST["domain_name"];
	$ip = $_REQUEST["ip"];

	if($_REQUEST["action"] == "new_nameserver"){
		checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name);
		$regz = registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip);
		if($regz["is_success"] == 1){
			$out .= "<font color=\"green\"><b>Registration of your name server succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
			$query = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$domain_name' AND subdomain_name='$subdomain';";
			$result = mysql_query($query)or die("Cannot query \"query\" !!!".mysql_error());
			$num_rows = mysql_num_rows($result);
			if($num_rows == 0){
				$query = "INSERT INTO $pro_mysql_subdomain_table (id,
domain_name,subdomain_name,webalizer_generate,ip)VALUES('','$domain_name','$subdomain','no','$ip');";
			}else if($num_rows == 1){
				$query = "UPDATE $pro_mysql_subdomain_table SET ip='$ip'
					WHERE domain_name='$domain_name' AND subdomain_name='$subdomain' LIMIT 1;";
			}else{
				die("Subdomain table problem: twice the same subdomain !");
			}
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
			$query = "INSERT INTO $pro_mysql_nameservers_table(id,
owner,domain_name,subdomain,ip)VALUES(
'','$adm_login','$domain_name','$subdomain','$ip');";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		}else{
			$out .= "<font color=\"red\"><b>Registration of your name server failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
		}
	}
	if($_REQUEST["action"] == "edit_nameserver"){
		checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name);
		$regz = registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip);
		if($regz["is_success"] == 1){
			 $out .= "<font color=\"green\"><b>Edition of name server succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
			$query = "UPDATE $pro_mysql_subdomain_table SET ip='$ip'
				WHERE domain_name='$domain_name' AND subdomain_name='$subdomain' LIMIT 1;";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
			$query = "UPDATE $pro_mysql_nameservers_table SET ip='$ip'
				WHERE domain_name='$domain_name' AND subdomain='$subdomain' LIMIT 1;";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		}else{
			$out .= "<font color=\"red\"><b>Edition name server failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
		}
	}
 
	if($_REQUEST["action"] == "delete_nameserver"){
		checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name);
		$regz = registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name);
		if($regz["is_success"] == 1){
			$out .= "<font color=\"green\"><b>Deletion of name server succesfull</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
			$query="DELETE FROM $pro_mysql_nameservers_table
				WHERE domain_name='$domain_name' AND subdomain='$subdomain' LIMIT 1";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		}else{
			$out .= "<font color=\"red\"><b>Deletion name server failed</b></font><br>
Server said: <i>" . $regz["response_text"] . "</i><br>";
		}
	}



	$out .= "<b><u>List of your registred name-servers:</u></b><br>";

	$query = "SELECT * FROM $pro_mysql_nameservers_table WHERE owner='$adm_login';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
        $num_rows = mysql_num_rows($result);
        for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		if($i > 0){
			$out .= " - ";
		}
		$out .= "<a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_id=" .
			$row["id"] ."\">" . $row["subdomain"] . "." . $row["domain_name"] . "</a>";
	}

	if($_REQUEST["edit_id"] != "" && isset($_REQUEST["edit_id"])){
		$query = "SELECT * FROM $pro_mysql_nameservers_table WHERE id='". $_REQUEST["edit_id"] ."' AND owner='$adm_login';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) != 1) die("Nameserver not found !!!");
		$row = mysql_fetch_array($result);
		$out .= "<br><br><a href=\"$PHP_SELF?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">New name server</a><br>
<b><u>Edit name server:</u></b><br>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\"> 
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">   
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">   
<input type=\"hidden\" name=\"action\" value=\"edit_nameserver\">
<input type=\"hidden\" name=\"domain_name\" value=\"". $row["domain_name"] ."\">
<input type=\"hidden\" name=\"subdomain\" value=\"". $row["subdomain"] ."\">
Name server hostname: ". $row["subdomain"] .".". $row["domain_name"] ."<br>
<input type=\"hidden\" name=\"edit_id\" value=\"". $_REQUEST["edit_id"] ."\">
Name server IP:<input type=\"text\" name=\"ip\" value=\"". $row["ip"] ."\">
<input type=\"submit\" value=\"Ok\">
</form>
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\"> 
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">    
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">    
<input type=\"hidden\" name=\"action\" value=\"delete_nameserver\">
<input type=\"hidden\" name=\"domain_name\" value=\"". $row["domain_name"] ."\">
<input type=\"hidden\" name=\"subdomain\" value=\"". $row["subdomain"] ."\">
<input type=\"hidden\" name=\"delete_id\" value=\"". $_REQUEST["edit_id"] ."\">
<input type=\"submit\" value=\"Delete name server\">
</form>
";
	}else{
		$out .= "<br><br><b><u>Register new name server:</u></b><br>
		What subzone do you want to use (exemple: \"ns1\"):
<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">   
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">   
<input type=\"hidden\" name=\"action\" value=\"new_nameserver\">
<input type=\"text\" name=\"subdomain\" value=\"\"><br>";
		$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
		$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		$out .= "Select one your domain-name for adding a name server to the registries:<br>
		<select name=\"domain_name\">";
		for($i=0;$i<$num_rows;$i++){
			$row = mysql_fetch_array($result);
			$out .= "<option value=\"" . $row["name"] . "\">" . $row["name"] . "</option>";
		}
		$out .= "</select><br>
			IP address of that name server:
			<input type=\"text\" name=\"ip\" value=\"\"><br>";
		$out .= "<input type=\"submit\" value=\"Ok\">
</form>
<br>";
	}

	return $out;
}

?>
