<?php

///////////////////////////
// MySQL password change //
///////////////////////////
// action=add_dbuser&dbuser=zigo2&db_pass=toto
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_dbuser"){
	checkLoginPass($adm_login,$adm_pass);
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= "Incorrect MySQL login form: please enter another login and try again.<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["db_pass"])){
		$submit_err .= "Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long in file ".__FILE__.".<br>\n";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0){
			$submit_err .= "A user by that name exists in the database, please select a new one.<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "INSERT INTO mysql.user (Host,User,Password,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Reload_priv,Shutdown_priv,Process_priv,File_priv,Grant_priv,References_priv,Index_priv,Alter_priv,dtcowner)
			VALUES ('localhost','".$_REQUEST["dbuser"]."',Password('".$_REQUEST["db_pass"]."'),'N','N','N','N','N','N','N','N','N','N','N','N','N','N','$adm_login');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}

// action=add_dbuser&newdb_name=blabla&dbuser=zigo
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_dbuser_db"){
	checkLoginPass($adm_login,$adm_pass);
	if(!isDatabase($_REQUEST["newdb_name"])){
		$submit_err .= "This is not a valid database name! Please pickup another one.<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT * FROM mysql.db WHERE Db='".$_REQUEST["newdb_name"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($r);
		if($num_rows > 0){
			$submit_err .= "A database by that name exists, please select a new one.<br>\n";
			$commit_flag = "no";
		}
	}
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= "Incorrect db login form: please enter another login and try again.<br>\n";
		$commit_flag = "no";
	}else{
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$submit_err .= "No user by that name exists in the database, please select a new one.<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "CREATE DATABASE ".$_REQUEST["newdb_name"].";";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "INSERT INTO mysql.db ( Host,Db,User,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Grant_priv,References_priv,Index_priv,Alter_priv)
		VALUES ('localhost','".$_REQUEST["newdb_name"]."','".$_REQUEST["dbuser"]."','Y','Y','Y','Y','Y','Y','N','Y','Y','Y');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "modify_dbuser_pass"){
	checkLoginPass($adm_login,$adm_pass);
	// action=modify_dbuser_pass&dbuser=zigo&db_pass=bla
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= "Incorrect MySQL login form: please enter another login and try again.<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["db_pass"])){
		$submit_err .= "Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.<br>\n";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."' AND dtcowner='$adm_login';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$submit_err .= "No user by that name for user $adm_login, please select one that you own.<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "UPDATE mysql.user SET Password=PASSWORD('".$_REQUEST["db_pass"]."') WHERE User='".$_REQUEST["dbuser"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "del_dbuser"){
	checkLoginPass($adm_login,$adm_pass);
	// action=del_dbuser&dbuser=zigo
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= "Incorrect MySQL login form: please enter another login and try again.<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT * FROM mysql.db WHERE User='".$_REQUEST["dbuser"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($r);
		if($num_rows > 0){
			$submit_err .= "That user owns some databases. Please remove them or change the owner of them first.<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "DELETE FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."' AND dtcowner='$adm_login';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_user_db"){
	checkLoginPass($adm_login,$adm_pass);
	// action=delete_user_db&dbname=clem
	if(!isDatabase($_REQUEST["dbname"])){
		$submit_err .= "Incorrect MySQL db name: please enter another and try again.<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT User FROM mysql.db WHERE Db='".$_REQUEST["dbname"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$submit_err .= "Cannot reselect MySQL db name: please enter another and try again.<br>\n";
			$commit_flag = "no";
		}else{
			$a = mysql_fetch_array($r);
			$q = "SELECT User FROM mysql.user WHERE User='".$a["User"]."' AND dtcowner='$adm_login';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$submit_err .= "MySql database ownership not valid: I will not let you delete this database because it doesn't seems to be owned by you.<br>\n";
				$commit_flag = "no";
			}
		}
	}
	if($commit_flag == "yes"){
		$q = "DELETE FROM mysql.db WHERE Db='".$_REQUEST["dbname"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "DROP DATABASE ".$_REQUEST["dbname"].";";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}

}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_db_owner"){
	checkLoginPass($adm_login,$adm_pass);
	// action=change_db_owner&dbname=clem&dbuser=zigo
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= "Incorrect MySQL login form: please enter another login and try again.<br>\n";
		$commit_flag = "no";
	}
	if(!isDatabase($_REQUEST["dbname"])){
		$submit_err .= "Incorrect MySQL db name: please enter another and try again.<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT User FROM mysql.db WHERE Db='".$_REQUEST["dbname"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$submit_err .= "Cannot reselect MySQL db name: please enter another and try again.<br>\n";
			$commit_flag = "no";
		}else{
			$a = mysql_fetch_array($r);
			$q = "SELECT User FROM mysql.user WHERE User='".$a["User"]."' AND dtcowner='$adm_login';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$submit_err .= "MySql database ownership not valid: I will not let you chane ownership of this database because it doesn't seems to be owned by you.<br>\n";
				$commit_flag = "no";
			}
		}
	}
	if($commit_flag == "yes"){
		$q = "UPDATE mysql.db SET User='".$_REQUEST["dbuser"]."' WHERE Db='".$_REQUEST["dbname"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}

if(isset($_REQUEST["change_mysql_password"]) && $_REQUEST["change_mysql_password"] == "Ok"){
	checkLoginPass($adm_login,$adm_pass);
	$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR (pass_next_req='$adm_pass' AND pass_expire > '".mktime()."'));";
	$result = mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows != 1)	die("User or password is incorrect !");

	if(!isDTCPassword($_REQUEST["new_mysql_password"])){
		die("Password are made only with standards chars and numbers (a-zA-Z0-9) and should be between 6 and 16 chars long.");
	}

	mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
	$query = "UPDATE user SET Password=PASSWORD('".$_REQUEST["new_mysql_password"]."') WHERE User='$adm_login';";
	mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
	mysql_query("FLUSH PRIVILEGES");
	mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\" !!!");
}


?>
