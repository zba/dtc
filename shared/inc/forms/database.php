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
