<?php

//////////////////////////////////////
// Database management for one user //
//////////////////////////////////////
// Todo : add a button for creating a MySql databe for one user
// and add credential to it !
function drawDataBase($database){
	global $conf_mysql_db;
	global $adm_login;
	global $adm_pass;

	global $conf_user_mysql_type;
	global $conf_user_mysql_host;
	global $conf_user_mysql_root_login;
	global $conf_user_mysql_root_pass;

	global $conf_demo_version;
	global $conf_user_mysql_prepend_admin_name;

	global $pro_mysql_admin_table;

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Cannot find user !");
	$admin_param = mysql_fetch_array($r);

	if($conf_user_mysql_type=="distant"){
		$newid = mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass)or die("Cannot connect to user SQL host");
	}

	$out = "<br><h3>". _("Your users") ."</h3>";
	if($conf_user_mysql_prepend_admin_name == "yes"){
		$out .= "<i>" . _("Your username will be prepended to the database username.") . "</i><br>";
	}
	$q = "SELECT DISTINCT User FROM mysql.user WHERE dtcowner='$adm_login' ORDER BY User;";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	$num_users = $n;
	$out .= "<table><tr><td>". _("User") ."</td><td>". _("Password:") ."</td><td>". _("Action") ."</td><td></td></tr>";
	$hidden = "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">";
	$dblist_user = "";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= "<tr><td><form action=\"?\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"modify_dbuser_pass\">
		<input type=\"hidden\" name=\"dbuser\" value=\"".$a["User"]."\">
		".$a["User"]."</td>
		<td><input type=\"text\" name=\"db_pass\" value=\"\"></td>
		<td><input type=\"submit\" value=\"". _("Save") ."\"></form></td>
		<td><form action=\"?\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"del_dbuser\">
		<input type=\"hidden\" name=\"dbuser\" value=\"".$a["User"]."\">
		<input type=\"submit\" value=\"". _("Delete") ."\"></form></td></tr>";
		if(!isset($dblist_clause)){
			$dblist_clause = "User='".$a["User"]."'";
		}else{
			$dblist_clause .= " OR User='".$a["User"]."'";
		}
		$dblist_user[] = $a["User"];
	}
	$out .= "<tr><td><form action=\"?\">$hidden
	<input type=\"hidden\" name=\"action\" value=\"add_dbuser\">
	<input type=\"text\" name=\"dbuser\" value=\"\"></td>
	<td><input type=\"text\" name=\"db_pass\" value=\"\"></td>
	<td><input type=\"submit\" value=\"". _("Create") ."\"></form></td><td></td></tr>";
	$out .= "</table>";

	$out .= "<br><h3>". _("List of your databases:") ."</h3><br>";
	if($conf_user_mysql_prepend_admin_name == "yes"){
		$out .= "<i>" . _("Your username will be prepended to the database name.") . "</i><br>";
	}
	if($conf_demo_version == "no" && $num_users > 0){
		mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
		$query = "SELECT DISTINCT Db,User FROM db WHERE $dblist_clause;";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		$dblist = "<table cellpadding=\"2\" cellspacing=\"2\">";
		$dblist .= "<tr><td>". _("Database name") ."</td><td>". _("User") ."</td><td>". _("Action") ."</td><td></td></tr>";
		for($i=0;$i<$num_rows;$i++){
			$row = mysql_fetch_array($result);
			if($i != 0){
//				$out .= " - ";
			}
			$dblist_user_popup = "";
			for($j=0;$j<$num_users;$j++){
				if($row["User"] == $dblist_user[$j]){
					$dblist_user_popup .= "<option value=\"".$dblist_user[$j]."\" selected>".$dblist_user[$j]."</option>";
				}else{
					$dblist_user_popup .= "<option value=\"".$dblist_user[$j]."\">".$dblist_user[$j]."</option>";
				}
			}
			$dblist .= "<tr><td>".$row["Db"]."</td>";
			$dblist .= "<td><form action=\"?\">$hidden
			<input type=\"hidden\" name=\"action\" value=\"change_db_owner\">
			<input type=\"hidden\" name=\"dbname\" value=\"".$row["Db"]."\">
			<select name=\"dbuser\">$dblist_user_popup</select></td>";
			$dblist .= "<td><input type=\"submit\" value=\"". _("Save") ."\"></form></td>
			<td><form action=\"?\">$hidden
			<input type=\"hidden\" name=\"action\" value=\"delete_user_db\">
			<input type=\"hidden\" name=\"dbname\" value=\"".$row["Db"]."\">
			<input type=\"submit\" value=\"". _("Delete") ."\"></form></td></tr>";
//			$out .= $row["Db"];
		}
		if($num_rows < $admin_param["nbrdb"]){
			$dblist_user_popup = "";
			for($j=0;$j<$num_users;$j++){
				$dblist_user_popup .= "<option value=\"".$dblist_user[$j]."\">".$dblist_user[$j]."</option>";
			}

			$dblist .= "<tr><td><form action=\"?\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"add_dbuser_db\">
		<input type=\"text\" name=\"newdb_name\"></td>
				<td><select name=\"dbuser\">$dblist_user_popup</select></td>
				<td><input type=\"submit\" value=\"". _("Create") ."\"></form></td><td></td></tr>";
		}
		$dblist .= "</table>";
		$out .= $dblist;
		$out .= "<br>". _("Total database number:") ." $num_rows/".$admin_param["nbrdb"]."<br>";

		if($conf_user_mysql_type=="distant"){
			mysql_close($newid)or die("Cannot disconnect to user database");
			connect2base();
		}
		mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\" !!!");

		return $out;
	}else{
		$out .= _("Please create a MySQL user in order to be able to create a database.");
		return $out;
	}
}

?>
