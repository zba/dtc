<?php

function make_registration_tld_popup(){
	global $pro_mysql_registrar_domains_table;

	$q = "SELECT tld FROM $pro_mysql_registrar_domains_table WHERE 1;";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$reg_tld_popup = "";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$reg_tld_popup .= "<option value=\"".$a["tld"]."\">".$a["tld"]."</option>";
	}
	$form_enter_domain_name = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr><td><input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">www.<input type=\"text\" name=\"toreg_domain\" value=\"\"></td>
<td><select name=\"toreg_extention\">$reg_tld_popup</select></td>
<td>".submitButtonStart(). _("Ok") .submitButtonEnd()."</td></tr></table>
";
	return $form_enter_domain_name;
}

$form_enter_dns_infos_ip = "Please enter now the DNS server ip or hostname. If you want to configurate your domain here,
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
ip:<input size=\"14\" type=\"text\" name=\"toreg_dns6_ip\" value=\"\">";

$form_enter_dns_infos = "Please enter now the DNS server ip or hostname. If you want to configurate your domain here,
leave it with value \"default\".<br>
DNS1 host:<input size=\"16\" type=\"text\" name=\"toreg_dns1\" value=\"default\"><br>
DNS2 host:<input size=\"16\" type=\"text\" name=\"toreg_dns2\" value=\"default\"><br>
<i>Optional:</i><br>
DNS3 host:<input size=\"16\" type=\"text\" name=\"toreg_dns3\" value=\"\"><br>
DNS4 host:<input size=\"16\" type=\"text\" name=\"toreg_dns4\" value=\"\"><br>
DNS5 host:<input size=\"16\" type=\"text\" name=\"toreg_dns5\" value=\"\"><br>
DNS6 host:<input size=\"16\" type=\"text\" name=\"toreg_dns6\" value=\"\">";

if(isset($_REQUEST["dtcrm_owner_hdl"])) $dtcrm_owner_hdl=$_REQUEST["dtcrm_owner_hdl"];
else	$dtcrm_owner_hdl="";
if(isset($_REQUEST["dtcrm_admin_hdl"])) $dtcrm_admin_hdl=$_REQUEST["dtcrm_admin_hdl"];
else	$dtcrm_admin_hdl="";
if(isset($_REQUEST["dtcrm_billing_hdl"])) $dtcrm_billing_hdl=$_REQUEST["dtcrm_billing_hdl"];
else	$dtcrm_billing_hdl="";
if(isset($_REQUEST["dtcrm_teck_hdl"])) $dtcrm_teck_hdl=$_REQUEST["dtcrm_teck_hdl"];
else	$dtcrm_teck_hdl="";
if(isset($_REQUEST["toreg_dns1"])) $toreg_dns1=$_REQUEST["toreg_dns1"];else	$toreg_dns1="";
if(isset($_REQUEST["toreg_dns2"])) $toreg_dns2=$_REQUEST["toreg_dns2"];else	$toreg_dns2="";
if(isset($_REQUEST["toreg_dns3"])) $toreg_dns3=$_REQUEST["toreg_dns3"];else	$toreg_dns3="";
if(isset($_REQUEST["toreg_dns4"])) $toreg_dns4=$_REQUEST["toreg_dns4"];else	$toreg_dns4="";
if(isset($_REQUEST["toreg_dns5"])) $toreg_dns5=$_REQUEST["toreg_dns5"];else	$toreg_dns5="";
if(isset($_REQUEST["toreg_dns6"])) $toreg_dns6=$_REQUEST["toreg_dns6"];else	$toreg_dns6="";

if(isset($_REQUEST["toreg_dns1_ip"])) $toreg_dns1_ip=$_REQUEST["toreg_dns1_ip"];else	$toreg_dns1_ip="";
if(isset($_REQUEST["toreg_dns2_ip"])) $toreg_dns2_ip=$_REQUEST["toreg_dns2_ip"];else	$toreg_dns2_ip="";
if(isset($_REQUEST["toreg_dns3_ip"])) $toreg_dns3_ip=$_REQUEST["toreg_dns3_ip"];else	$toreg_dns3_ip="";
if(isset($_REQUEST["toreg_dns4_ip"])) $toreg_dns4_ip=$_REQUEST["toreg_dns4_ip"];else	$toreg_dns4_ip="";
if(isset($_REQUEST["toreg_dns5_ip"])) $toreg_dns5_ip=$_REQUEST["toreg_dns5_ip"];else	$toreg_dns5_ip="";
if(isset($_REQUEST["toreg_dns6_ip"])) $toreg_dns6_ip=$_REQUEST["toreg_dns6_ip"];else	$toreg_dns6_ip="";

$whois_forwareded_params_ip = "
<input type=\"hidden\" name=\"dtcrm_owner_hdl\" value=\"$dtcrm_owner_hdl\">
<input type=\"hidden\" name=\"dtcrm_admin_hdl\" value=\"$dtcrm_admin_hdl\">
<input type=\"hidden\" name=\"dtcrm_billing_hdl\" value=\"$dtcrm_billing_hdl\">
<input type=\"hidden\" name=\"dtcrm_teck_hdl\" value=\"$dtcrm_teck_hdl\">
<input type=\"hidden\" name=\"toreg_dns1\" value=\"$toreg_dns1\">
<input type=\"hidden\" name=\"toreg_dns2\" value=\"$toreg_dns2\">
<input type=\"hidden\" name=\"toreg_dns3\" value=\"$toreg_dns3\">
<input type=\"hidden\" name=\"toreg_dns4\" value=\"$toreg_dns4\">
<input type=\"hidden\" name=\"toreg_dns5\" value=\"$toreg_dns5\">
<input type=\"hidden\" name=\"toreg_dns6\" value=\"$toreg_dns6\">

<input type=\"hidden\" name=\"toreg_dns1_ip\" value=\"$toreg_dns1_ip\">
<input type=\"hidden\" name=\"toreg_dns2_ip\" value=\"$toreg_dns2_ip\">
<input type=\"hidden\" name=\"toreg_dns3_ip\" value=\"$toreg_dns3_ip\">
<input type=\"hidden\" name=\"toreg_dns4_ip\" value=\"$toreg_dns4_ip\">
<input type=\"hidden\" name=\"toreg_dns5_ip\" value=\"$toreg_dns5_ip\">
<input type=\"hidden\" name=\"toreg_dns6_ip\" value=\"$toreg_dns6_ip\">
";

$whois_forwareded_params = "
<input type=\"hidden\" name=\"dtcrm_owner_hdl\" value=\"$dtcrm_owner_hdl\">
<input type=\"hidden\" name=\"dtcrm_admin_hdl\" value=\"$dtcrm_admin_hdl\">
<input type=\"hidden\" name=\"dtcrm_billing_hdl\" value=\"$dtcrm_billing_hdl\">
<input type=\"hidden\" name=\"dtcrm_teck_hdl\" value=\"$dtcrm_teck_hdl\">
<input type=\"hidden\" name=\"toreg_dns1\" value=\"$toreg_dns1\">
<input type=\"hidden\" name=\"toreg_dns2\" value=\"$toreg_dns2\">
<input type=\"hidden\" name=\"toreg_dns3\" value=\"$toreg_dns3\">
<input type=\"hidden\" name=\"toreg_dns4\" value=\"$toreg_dns4\">
<input type=\"hidden\" name=\"toreg_dns5\" value=\"$toreg_dns5\">
<input type=\"hidden\" name=\"toreg_dns6\" value=\"$toreg_dns6\">
";

$period_popup = "<select name=\"toreg_period\">
<option value=\"1\">1 "._("year")."</value>
<option value=\"2\">2 "._("years")."</value>
<option value=\"3\">3 "._("years")."</value>
<option value=\"4\">4 "._("years")."</value>
<option value=\"5\">5 "._("years")."</value>
<option value=\"6\">6 "._("years")."</value>
<option value=\"7\">7 "._("years")."</value>
<option value=\"8\">8 "._("years")."</value>
<option value=\"9\">9 "._("years")."</value>
</select>";

if(isset($_REQUEST["authcode"])) $authcode=$_REQUEST["authcode"];else     $authcode="";
$form_enter_auth_code = "
<br />".("An authorisation code must be requested from the existing registrar of this domain")."
<br />".("Auth Code")." :<input size=\"24\" type=\"text\" name=\"authcode\" value=\"$authcode\">
";

?>
