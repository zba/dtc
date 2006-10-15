<?php

function drawTickets($admin){
	global $lang;
	global $pro_mysql_tik_admins_table;
	global $pro_mysql_tik_queries_table;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	$out = "<font color=\"red\">This part is in active development: do not use yet</font><br>";

	if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "new_ticket"){
		print_r($admin["vps"]);
		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\" enctype=\"application/x-www-form-urlencoded\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">

Subject: <input name=\"subject\" type=\"text\" size=\"40\" maxlength=\"40\"><br>

What type of hosting are you having the issue with:<br>
<select name=\"hosting_type\">
<option value=\"shared\">Shared</option>
<option value=\"vps\">VPS</option>
<option value=\"dedicated\">Dedicated server</option>
</select><br>

What is your server hostname:<br>
<select name=\"server_hostname\">
<option value=\"node0103\">node0103</option>
</select><br>

Type of problem:<br>
<select name=\"issue_type\">
<option value=\"network\">Network connectivity to server</option>
<option value=\"crash\">Server does not respond or seems crashed</option>
<option value=\"password\">Access to the control panel or password issue</option>
<option value=\"webapp_install\">Installing web content or app</option>
<option value=\"emailerror\">Cannot send or receive email</option>
<option value=\"emailconfig\">Email configuration problem</option>
<option value=\"billing\">Renewal or billing issue</option>
<option value=\"dtchelp\">Need help on how works DTC</option>
<option value=\"dns\">DNS, whois or domain name registration</option>
<option value=\"other\">Other</option>
</select><br><br>

Full description of the trouble:<br>
<textarea name=\"ticketbody\" cols=\"60\" rows=\"10\" wrap=\"physical\"></textarea><br><br>

<input type=\"submit\" value=\"Send trouble ticket\">

</form>";
	}else{
		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"subaction\" value=\"new_ticket\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"submit\" value=\"Submit new support issue\">
</form>
";
	}
	return $out;
}

?>