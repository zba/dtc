<?php

$form_enter_domain_name = "<input type=\"hidden\" name=\"action\" value=\"dtcrm_add_domain\">
www.<input type=\"text\" name=\"toreg_domain\" value=\"\">
<select name=\"toreg_extention\">
<option value=\".com\" selected>.com</option>
<option value=\".net\">.net</option>
<option value=\".org\">.org</option>
<option value=\".biz\">.biz</option>
<option value=\".info\">.info</option>
<option value=\".name\">.name</option>
</select><input type=\"submit\" value=\"Ok\">
";

$form_enter_dns_infos = "Please enter now the DNS server ip or hostname. If you want to configurate your domain here,
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

$whois_forwareded_params = "
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

?>
