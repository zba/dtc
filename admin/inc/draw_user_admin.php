<?php

function drawNewAdminForm(){
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_login_path;
	global $conf_site_root_host_path;
	global $lang;

	global $pro_mysql_new_admin_table;
	global $pro_mysql_pending_queries_table;

	$add_a_user .= "<h4>Add a new user:</h4>
<form action=\"?\" methode=\"post\">
<table>
<tr><td align=\"right\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newadmin_login\" value=\"\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"newadmin_pass\" value=\"\"><br>
</td></tr><tr><td align=\"right\">
	".$txt_login_path[$lang]."</td><td><input type=\"text\" name=\"newadmin_path\" value=\"$conf_site_root_host_path\"><br>
</td></tr><tr><td align=\"right\">
	&nbsp;</td><td><input type=\"submit\" name=\"newadminuser\" value=\"Ok\">
</td></tr>
</form>
</table>
";

	$waiting_new_users = "<h4>User and domain waiting for addition:</h4>";
	$q = "SELECT * FROM $pro_mysql_new_admin_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>No user waiting!</b>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
	<tr><td>Name</td><td>Login</td><td>Domain name</td><td>Bank</td><td>Action</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<tr><td><u>".$a["comp_name"].":</u> ";
			$waiting_new_users .= $a["family_name"].", ".$a["first_name"]."</td>";
			$waiting_new_users .= "<td>".$a["reqadm_login"]."</td>";
			$waiting_new_users .= "<td>".$a["domain_name"]."</td>";
			if($a["paiement_id"] == 0){
				$waiting_new_users .= "<td>No pay ID!</td>";
			}else{
				$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$a["paiement_id"]."';";
				$r = mysql_query($q)or die("Cannot select $q line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1)	die("Numrows!=1 in $q line: ".__LINE__." file: ".__FILE__);
				$a = mysql_fetch_array($r);
				if($a["valid"] == "yes"){
					$waiting_new_users .= "<td><font color=\"green\">YES</font></td>";
				}else{
					$waiting_new_users .= "<td><font color=\"red\">NO</font></td>";
				}
			}
			$waiting_new_users .= "<td><a target=\"_blank\" href=\"/dtcadmin/view_waitingusers.php?reqadm_login=".$a["reqadm_login"]."\">View details</a> - <a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_user&reqadm_login=".$a["reqadm_login"]."\">Add</a> - <a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_user&reqadm_login=".$a["reqadm_login"]."\">Del</a></td>";
			$waiting_new_users .= "</tr>";
		}
		$waiting_new_users .= "</table>";
	}
	$q = "SELECT * FROM $pro_mysql_pending_queries_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<br><b>No domain waiting!</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
	<tr><td>Login</td><td>Domain name</td><td>Action</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<td>".$a["adm_login"]."</td>";
			$waiting_new_users .= "<td>".$a["domain_name"]."</td>";
			$waiting_new_users .= "<td><a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_domain_to_user&reqid=".$a["id"]."\">Add</a> - <a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_domain_to_user&reqid=".$a["id"]."\">Del</a></td></tr>";
		}
		$waiting_new_users .= "</table>";
	}
	return "<table>
<tr>
	<td valign=\"top\">".
skin("frame",$add_a_user,"")."</td>
	<td valign=\"top\">".
skin("frame",$waiting_new_users,"")."</td>
</tr></table>";
}

function drawMySqlAccountManger(){
	global $lang;
	global $adm_login;
	global $adm_pass;
	global $conf_mysql_db;
	global $conf_demo_version;
	global $txt_mysqlmang_nouser_by_that_name;
	global $txt_mysqlmang_delete_a_db;
	global $txt_mysqlmang_add_a_db;
	global $txt_mysqlmang_db_name;
	global $txt_mysqlmang_not_in_demo;
	global $txt_delete_this_mysql_user_and_db;

	// Retrive the infos from the database "mysql" that contains all the rights
	if($conf_demo_version == "no"){
		mysql_select_db("mysql")or die("Cannot select db mysql for account management  !!! Does your MySQL user/pass has the rights to it ?");
		$query = "SELECT * FROM user WHERE User='$adm_login';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			mysql_select_db($conf_mysql_db)or die("Cannot select DB $conf_mysql_db !!!");
			return $txt_mysqlmang_nouser_by_that_name[$lang];
		}else{
			$query = "SELECT Db FROM db WHERE User='$adm_login';";
			$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
			$num_rows = mysql_num_rows($result);
			for($i=0;$i<$num_rows;$i++){
				$row = mysql_fetch_array($result);
				$dblist[] = $row["Db"];
			}
			mysql_select_db($conf_mysql_db)or die("Cannot select DB $conf_mysql_db !!!");
			$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&action=delete_mysql_user\">".$txt_delete_this_mysql_user_and_db[$lang]."</a><br>
<b><u>".$txt_mysqlmang_delete_a_db[$lang]."</u></b><br>";
			for($i=0;$i<$num_rows;$i++){
				if($i != 0){
					$out .= " - ";
				}
				$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&action=delete_one_db&db_name=".$dblist[$i]."\">".$dblist[$i]."</a>";
			}
			$out .= "<br><br><b><u>".$txt_mysqlmang_add_a_db[$lang]."</u></b>
		<form action=\"".$_SERVER["PHP_SELF"]."\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		".$txt_mysqlmang_db_name[$lang]."<input type=\"text\" name=\"new_mysql_database_name\" value=\"\">
		<input type=\"submit\" name=\"new_mysql_database\" value=\"Ok\">
		</form>";
			return $out;
		}
	}else{
		return $txt_mysqlmang_not_in_demo[$lang];
	}
}

function userEditForms($adm_login,$adm_pass){
	global $txt_general_virtual_admin_edition;
	global $txt_domains_configuration_title;
	global $txt_add_user_title;
	global $conf_skin;
	global $lang;
	if($adm_login != "" && isset($adm_login) && $adm_pass != "" && isset($adm_pass)){
		// Fetch all the selected user informations, Print a nice error message if failure.
		$admin = fetchAdmin($adm_login,$adm_pass);
		if(($error = $admin["err"]) != 0){
			die("Error fetching admin : $error");
		}

		// Draw the html forms
		$HTML_admin_edit_info .= drawEditAdmin($admin);
		$HTML_admin_mysql_config .= drawMySqlAccountManger();
		$HTML_admin_domain_config .= drawDomainConfig($admin);
		$HTML_admin_edit_data .= drawAdminTools($admin);

		// Output and skin the result !
		$user_config = skin($conf_skin,$HTML_admin_edit_info,$txt_general_virtual_admin_edition[$lang]);
		$user_mysql_config = skin($conf_skin,$HTML_admin_mysql_config,"MySQL");
		$user_domain_config = skin($conf_skin,$HTML_admin_domain_config,$txt_domains_configuration_title[$lang]);
		$user_tools = skin($conf_skin,$HTML_admin_edit_data,"Domains for $adm_login");

		// All thoses tools in a simple table
		return "<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">
	<tr>
		<tr><td width=\"100%\">$user_config
		</td></tr><tr><td width=\"100%\">$user_mysql_config
		</td></tr><tr><td width=\"100%\">$user_domain_config
		</td></tr><tr><td width=\"100%\">$user_tools
		</td></tr><tr><td height=\"100%\">&nbsp;
		</td></tr>
	</tr>
</table>
";
	}else{
		// If no user is in edition, draw a tool for adding an admin
		$add_a_user = drawNewAdminForm();
		return skin($conf_skin,$add_a_user,$txt_add_user_title[$lang]);
	}
}

function skinConsole(){
	global $HTTP_HOST;
	global $console;
	return "<table bgcolor=\"#000000\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" width=\"100%\" height=\"100%\">
<tr>
<td>
	<font color=\"#FFFFFF\">Console output</font>
</td>
</tr>
<tr>
<td><pre>
<font color=\"#FFFFFF\">".$_SERVER["HTTP_HOST"].":&gt;<br>$console</font></pre>
</td>
</tr>
</table>
";
}

?>
