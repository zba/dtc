<?php

function drawNewAdminForm(){
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_login_path;
	global $conf_site_root_host_path;
	global $lang;

	global $pro_mysql_new_admin_table;
	global $pro_mysql_pending_queries_table;
	global $pro_mysql_pay_table;

	global $txt_add_a_new_user;
	global $txt_userndomain_waiting_for_addition;
	global $txt_no_user_waiting;
	global $txt_no_domain_waiting;
	global $txt_login_title;
	global $txt_domain_tbl_config_dom_name;

	// Draw the form for making a new admin
	$add_a_user = "<h4>".$txt_add_a_new_user[$lang]."</h4>
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

	// Draw the list of users awaiting for an account
	$waiting_new_users = "<h4>".$txt_userndomain_waiting_for_addition[$lang]."</h4>";
	$q = "SELECT * FROM $pro_mysql_new_admin_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>".$txt_no_user_waiting[$lang]."</b>";
	}else{
		$waiting_new_users .= "<table width=\"100%\"border=\"1\">
	<tr><td>Name</td><td>".$txt_login_title[$lang]."</td><td>Domain name</td><td>Bank</td><td>Action</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<tr><td style=\"white-space:nowrap\"><u>".$a["comp_name"].":</u><br>";
			$waiting_new_users .= $a["family_name"].", ".$a["first_name"]."</td>";
			$waiting_new_users .= "<td>".$a["reqadm_login"]."</td>";
			$waiting_new_users .= "<td>".$a["domain_name"]."</td>";
			if($a["paiement_id"] == 0){
				$waiting_new_users .= "<td>No pay ID!</td>";
			}else{
				$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$a["paiement_id"]."';";
				$r2 = mysql_query($q)or die("Cannot select $q line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1)	echo "Numrows!=1 in $q line: ".__LINE__." file: ".__FILE__." : problems with sql tables !";
				$a2 = mysql_fetch_array($r2);
				if($a2["valid"] == "yes"){
					$waiting_new_users .= "<td><font color=\"green\">YES</font></td>";
				}else{
					$waiting_new_users .= "<td><font color=\"red\">NO</font></td>";
				}
			}
			$waiting_new_users .= "<td style=\"white-space:nowrap\"><a target=\"_blank\" href=\"/dtcadmin/view_waitingusers.php?reqadm_login=".$a["reqadm_login"]."\">View details</a> - <a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_user&reqadm_login=".$a["reqadm_login"]."\">Add</a> - <a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_user&reqadm_login=".$a["reqadm_login"]."\">Del</a></td>";
			$waiting_new_users .= "</tr>";
		}
		$waiting_new_users .= "</table>";
	}

	// Draw the list of domains awaiting to be add to users
	$q = "SELECT * FROM $pro_mysql_pending_queries_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<br><b>".$txt_no_domain_waiting[$lang]."</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
	<tr><td>".$txt_login_title[$lang]."</td><td>".$txt_domain_tbl_config_dom_name[$lang]."</td><td>Action</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<td>".$a["adm_login"]."</td>";
			$waiting_new_users .= "<td>".$a["domain_name"]."</td>";
			$waiting_new_users .= "<td><a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_domain_to_user&reqid=".$a["id"]."\">Add</a>
- <a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_domain_to_user&reqid=".$a["id"]."\">Del</a></td></tr>";
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

	$out = "";

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
	global $addrlink;
	global $rub;

	if($adm_login != "" && isset($adm_login) && $adm_pass != "" && isset($adm_pass)){
		// Fetch all the selected user informations, Print a nice error message if failure.
		$admin = fetchAdmin($adm_login,$adm_pass);
		if(($error = $admin["err"]) != 0){
			// now print out all the stuff from our HTTP headers
			//$input = array_merge($_GET,    $_POST,
                        //     $_COOKIE, $_SERVER,
                        //     $_ENV,    $_FILES,
                        //     isset($_SESSION) ? $_SESSION : array()); 
			//foreach ($input as $k => $v) { 
			//	echo "$k - $input[$k]\n";	
			//}
			die("Error fetching admin : $error");
		}

		//fix up the $adm_login in case it changed because of session vars:
		//in case users play silly bugger with the "GET" variables
		$adm_login = $admin["info"]["adm_login"];

		// Draw the html forms
		if(isset($rub) && $rub == "adminedit"){
			$HTML_admin_edit_info = drawEditAdmin($admin);
			$user_config = skin($conf_skin,$HTML_admin_edit_info,$txt_general_virtual_admin_edition[$lang]);
//			return $user_config;
		}else if(isset($rub) && $rub == "domain_config"){
			$HTML_admin_domain_config = drawDomainConfig($admin);
			$user_config = skin($conf_skin,$HTML_admin_domain_config,$txt_domains_configuration_title[$lang]);
		}else{
			$HTML_admin_edit_data = drawAdminTools($admin);
			$user_config = skin($conf_skin,$HTML_admin_edit_data,"Domains for $adm_login");
//			return $user_tools;
		}

		$iface_select = "<table height=\"1\" border=\"0\" width=\"100%\">";
		$iface_select .= "<tr><td width=\"33%\" valign=\"top\"><center>";
		if($rub != "user" && $rub != ""){
			$iface_select .= "<a href=\"?rub=user&adm_login=$adm_login&adm_pass=$adm_pass\">";
		}
		$iface_select .= "<img src=\"gfx/menu/client-interface.png\" width=\"48\" height=\"48\" border=\"0\"><br>
Client interface";
		if($rub != "user" && $rub != ""){
			$iface_select .= "</a>";
		}
		$iface_select .= "</center></td><td width=\"33%\" valign=\"top\"><center>";
		if($rub != "domain_config"){
			$iface_select .= "<a href=\"?rub=domain_config&adm_login=$adm_login&adm_pass=$adm_pass\">";
		}
		$iface_select .= "<img src=\"gfx/menu/domain-config.png\" width=\"48\" height=\"48\" border=\"0\"><br>
Domain config";
		if($rub != "domain_config"){
			$iface_select .= "</a>";
		}
		$iface_select .= "</center></td><td width=\"33%\" valign=\"top\"><center>";
		if($rub != "adminedit"){
			$iface_select .= "<a href=\"?rub=adminedit&adm_login=$adm_login&adm_pass=$adm_pass\">";
		}
		$iface_select .= "<img src=\"gfx/menu/user-editor.png\" width=\"48\" height=\"48\" border=\"0\"><br>
Admin editor";
		if($rub != "adminedit"){
			$iface_select .= "</a>";
		}
		$iface_select .= "</center></td></tr></table>";

		$iface_skined = skin($conf_skin,$iface_select,"User administration");

		// All thoses tools in a simple table
		return "<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
	<tr>
		<tr><td width=\"100%\">$iface_skined</td></tr>
		<tr><td width=\"100%\">$user_config</td></tr>
		<tr><td height=\"100%\">&nbsp;</td></tr>
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
