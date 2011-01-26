<?php

///////////////////////////
// MySQL password change //
///////////////////////////
// action=add_dbuser&dbuser=zigo2&db_pass=toto
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_dbuser"){
	checkLoginPass($adm_login,$adm_pass);
	if($conf_user_mysql_type=="distant"){
		$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user SQL host");
	}
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= _("Incorrect MySQL login format: please enter another login and try again.") ."<br>\n";
		$commit_flag = "no";
	}
	if($_REQUEST["dbuser"] == "root" || $_REQUEST["dbuser"] == "debian-sys-maint"){
		$submit_err .= _("Incorrect MySQL login format: please don't enter root or debian-sys-maint") ."<br>\n";
		$commit_flag = "no";
	}
	if($conf_user_mysql_prepend_admin_name == "yes"){
		$dbuser = $adm_login . "-" . $_REQUEST["dbuser"];
	}else{
		$dbuser = $_REQUEST["dbuser"];
	}
	if(!isDTCPassword($_REQUEST["db_pass"])){
		$submit_err .= _("Password must consist of only letters and numbers (a-zA-Z0-9) and should be between 6 and 16 characters long.") ."<br>\n";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$query = "SELECT * FROM mysql.user WHERE User='".$dbuser."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0){
			$submit_err .= _("A user with that name exists in the database, please select a new one.") ."<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "INSERT INTO mysql.user (Host,User,Password,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Reload_priv,Shutdown_priv,Process_priv,File_priv,Grant_priv,References_priv,Index_priv,Alter_priv,dtcowner)
			VALUES ('%','".$dbuser."',Password('".$_REQUEST["db_pass"]."'),'N','N','N','N','N','N','N','N','N','N','N','N','N','N','$adm_login');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "INSERT INTO mysql.user (Host,User,Password,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Reload_priv,Shutdown_priv,Process_priv,File_priv,Grant_priv,References_priv,Index_priv,Alter_priv,dtcowner)
			VALUES ('localhost','". $dbuser ."',Password('".$_REQUEST["db_pass"]."'),'N','N','N','N','N','N','N','N','N','N','N','N','N','N','$adm_login');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	if($conf_user_mysql_type=="distant"){
		mysql_close($newid) or die( _("Cannot close user database") );
		connect2base();
	}
}

// action=add_dbuser&newdb_name=blabla&dbuser=zigo
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_dbuser_db"){
	checkLoginPass($adm_login,$adm_pass);
	if($conf_user_mysql_type=="distant"){
		$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user SQL host");
	}
	// Check the db name format and that it's one one of the forbidden dbs
	if(!isDatabase($_REQUEST["newdb_name"])
				|| $_REQUEST["newdb_name"] == "mysql"
				|| $_REQUEST["newdb_name"] == "information_schema"
				|| $_REQUEST["newdb_name"] == "apachelogs"
				|| $_REQUEST["newdb_name"] == "dtc"
				|| $_REQUEST["newdb_name"] == $conf_mysql_db){
		$submit_err .= _("This is not a valid database name! Please choose another one.") ."<br>\n";
		$commit_flag = "no";
	}else{
		if($conf_user_mysql_prepend_admin_name == "yes"){
			$newdb_name = $adm_login . "_" . $_REQUEST["newdb_name"];
		}else{
			$newdb_name = $_REQUEST["newdb_name"];
		}
		$q = "SELECT * FROM mysql.db WHERE Db='". $newdb_name ."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($r);
		if($num_rows > 0){
			$submit_err .= _("A database by that name exists, please choose another name.") ."<br>\n";
			$commit_flag = "no";
		}
	}
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= _("Incorrect db login format: please enter another login and try again.") ."<br>\n";
		$commit_flag = "no";
	}else{
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$submit_err .= _("No user by that name exists in the database, or you don't own this db user. Please select a new one.") ."<br>\n";
			$commit_flag = "no";
		}
	}
	if($_REQUEST["newdb_name"] == $conf_mysql_db || $_REQUEST["newdb_name"] == "mysql" || $_REQUEST["newdb_name"] == "information_schema" || $_REQUEST["newdb_name"] == "apachelogs"){
		$submit_err .= _("Forbidden database name")." ".$_REQUEST["newdb_name"]."<br>";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$q = "CREATE DATABASE IF NOT EXISTS `". $newdb_name ."`;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "INSERT INTO mysql.db ( Host,Db,User,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Grant_priv,References_priv,Index_priv,Alter_priv,Lock_tables_priv,Create_tmp_table_priv,Create_view_priv,Show_view_priv)
		VALUES ('%','". $newdb_name ."','".$_REQUEST["dbuser"]."','Y','Y','Y','Y','Y','Y','N','Y','Y','Y','Y','Y','Y','Y');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "INSERT INTO mysql.db ( Host,Db,User,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Grant_priv,References_priv,Index_priv,Alter_priv,Lock_tables_priv,Create_tmp_table_priv,Create_view_priv,Show_view_priv)
		VALUES ('localhost','". $newdb_name ."','".$_REQUEST["dbuser"]."','Y','Y','Y','Y','Y','Y','N','Y','Y','Y','Y','Y','Y','Y');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	if($conf_user_mysql_type=="distant"){
		mysql_close($newid) or die("Cannot disconnect to user database");
		connect2base();
	}
	updateUsingCron("gen_backup='yes'");
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "modify_dbuser_pass"){
	checkLoginPass($adm_login,$adm_pass);
	if($conf_user_mysql_type=="distant"){
		$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user SQL host");
	}
	// action=modify_dbuser_pass&dbuser=zigo&db_pass=bla
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= _("Incorrect MySQL db format: please enter another login and try again.")."<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["db_pass"])){
		$submit_err .= _("Incorrect MySQL password format: please enter another login and try again.")."<br>\n";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."' AND dtcowner='$adm_login';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$submit_err .= _("A MySQL user by that name already exists. Please choose another one.")."<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "UPDATE mysql.user SET Password=PASSWORD('".$_REQUEST["db_pass"]."') WHERE User='".$_REQUEST["dbuser"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	if($conf_user_mysql_type=="distant"){
		mysql_close($newid) or die("Cannot disconnect to user database");
		connect2base();
	}
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "del_dbuser"){
	checkLoginPass($adm_login,$adm_pass);
	if($conf_user_mysql_type=="distant"){
		$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user SQL host");
	}
	// action=del_dbuser&dbuser=zigo
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= _("Incorrect db login form")."<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT * FROM mysql.db WHERE User='".$_REQUEST["dbuser"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($r);
		if($num_rows > 0){
			$submit_err .= _("That user still owns some databases. Please remove them or change the owner first.") ."<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "DELETE FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."' AND dtcowner='$adm_login';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	if($conf_user_mysql_type=="distant"){
		mysql_close($newid) or die("Cannot disconnect to user database");
		connect2base();
	}
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_user_db"){
	checkLoginPass($adm_login,$adm_pass);
	if($conf_user_mysql_type=="distant"){
		$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user SQL host");
	}
	// action=delete_user_db&dbname=clem
	if(!isDatabase($_REQUEST["dbname"])){
		$submit_err .= _("Incorrect MySQL db name format: please enter another and try again.") ."<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT User FROM mysql.db WHERE Db='".$_REQUEST["dbname"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$submit_err .= _("Cannot reselect MySQL db name: please enter another and try again.") ."<br>\n";
			$commit_flag = "no";
		}else{
			$a = mysql_fetch_array($r);
			$q = "SELECT User FROM mysql.user WHERE User='".$a["User"]."' AND dtcowner='$adm_login';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$submit_err .= _("MySql database ownership not valid: I will not let you delete this database because it doesn't seem to be owned by you.")."<br>\n";
				$commit_flag = "no";
			}
		}
	}
	if($commit_flag == "yes"){
		$q = "DELETE FROM mysql.db WHERE Db='".$_REQUEST["dbname"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "DROP DATABASE IF EXISTS `".$_REQUEST["dbname"]."`;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
	if($conf_user_mysql_type=="distant"){
		mysql_close($newid) or die("Cannot disconnect to user database");
		connect2base();
	}
	updateUsingCron("gen_backup='yes'");
}
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_db_owner"){
	checkLoginPass($adm_login,$adm_pass);
	if($conf_user_mysql_type=="distant"){
		$newid=mysql_connect($conf_user_mysql_host,$conf_user_mysql_root_login,$conf_user_mysql_root_pass) or die("Cannot connect to user SQL host");
	}
	// action=change_db_owner&dbname=clem&dbuser=zigo
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= _("Incorrect MySQL login format: please enter another login and try again.")."<br>\n";
		$commit_flag = "no";
	}
	if(!isDatabase($_REQUEST["dbname"])){
		$submit_err .= _("Incorrect MySQL db name format: please enter another and try again.") ."<br>\n";
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
				$submit_err .= _("MySql database ownership not valid: I will not let you change owner of this database because it doesn't seems to be owned by you.")."<br>\n";
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
	if($conf_user_mysql_type=="distant"){
		mysql_close($newid) or die("Cannot disconnect to user database");
		connect2base();
	}
}

?>
