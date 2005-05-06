<?php

//////////////////////////////////////
// Database management for one user //
//////////////////////////////////////
// Todo : add a button for creating a MySql databe for one user
// and add credential to it !
function drawDataBase($database){
	global $lang;
	global $txt_draw_tatabase_your_list;
	global $conf_mysql_db;
	global $adm_login;
	global $adm_pass;

	global $txt_draw_database_chpass;
	global $txt_password;

	global $conf_demo_version;

	$txt = "<br><b><u>Your users:</u></b>";
	$q = "SELECT * FROM mysql.user WHERE dtcowner='$adm_login';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	$txt .= "<table><tr><td>User</td><td>Password</td><td>Action</td></tr>";
	$hidden = "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$txt .= "<tr><td><form action=\"".$_SERVER["PHP_SELF"]."\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"modify_dbuser_pass\">
		<input type=\"hidden\" name=\"dbuser\" value=\"".$a["User"]."\">
		".$a["User"]."</td>
		<td><input type=\"text\" name=\"db_pass\" value=\"\"></td>
		<td><input type=\"submit\" value=\"Save\"></form>
		<form action=\"".$_SERVER["PHP_SELF"]."\">$hidden
		<input type=\"hidden\" name=\"action\" value=\"del_dbuser\">
		<input type=\"hidden\" name=\"dbuser\" value=\"".$a["User"]."\">
		<input type=\"submit\" value=\"Delete\"></form></td></tr>";
	}
	$txt .= "<tr><td><form action=\"".$_SERVER["PHP_SELF"]."\">$hidden
	<input type=\"hidden\" name=\"action\" value=\"add_dbuser\">
	<input type=\"text\" name=\"dbuser\" value=\"\"></td>
	<td><input type=\"text\" name=\"db_pass\" value=\"\"></td>
	<td><input type=\"submit\" value=\"New\"></form></td></tr>";
	$txt .= "</table>";

	$txt .= "<br><b><u>".$txt_draw_tatabase_your_list[$lang]."</u></b><br>";

	if($conf_demo_version == "no"){
		mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
		$query = "SELECT Db FROM db WHERE User='$adm_login'";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		for($i=0;$i<$num_rows;$i++){
			$row = mysql_fetch_array($result);
			if($i != 0){
				$txt .= " - ";
			}
			$txt .= $row["Db"];
		}
		mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\"	!!!");	

		$txt .= "<br><br><b><u>".$txt_draw_database_chpass[$lang]."</u></b><br>
		<form action=\"".$_SERVER["PHP_SELF"]."\">".$txt_password[$lang]."<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		<input type=\"text\" name=\"new_mysql_password\" value=\"\">
		<input type=\"submit\" name=\"change_mysql_password\" value=\"Ok\"></form>";
		return $txt;
	}else{
		$txt .= "No mysql account manager in demo version (because I don't have root access to the MySQL database).";
		return $txt;
	}
}

?>
