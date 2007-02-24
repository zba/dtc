<?php

require("$dtcshared_path/inc/sql/database_strings.php");

///////////////////////////
// MySQL password change //
///////////////////////////
// action=add_dbuser&dbuser=zigo2&db_pass=toto
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_dbuser"){
	checkLoginPass($adm_login,$adm_pass);
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= $txt_dbsql_incorrect_mysql_login_form_please_try_again[$lang]."<br>\n";
		$commit_flag = "no";
	}
	if($_REQUEST["dbuser"] == "root" || $_REQUEST["dbuser"] == "debian-sys-maint"){
		$submit_err .= $txt_dbsql_incorrect_mysql_login_root_or_debiansysmaint[$lang]."<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["db_pass"])){
		$submit_err .= $txt_dbsql_password_are_made_only_with_standards_chars_and_numbers_and_size[$lang]."<br>\n";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0){
			$submit_err .= $txt_dbsql_a_user_by_that_name_exists_in_the_db[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "INSERT INTO mysql.user (Host,User,Password,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Reload_priv,Shutdown_priv,Process_priv,File_priv,Grant_priv,References_priv,Index_priv,Alter_priv,dtcowner)
			VALUES ('%','".$_REQUEST["dbuser"]."',Password('".$_REQUEST["db_pass"]."'),'N','N','N','N','N','N','N','N','N','N','N','N','N','N','$adm_login');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}

// action=add_dbuser&newdb_name=blabla&dbuser=zigo
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_dbuser_db"){
	checkLoginPass($adm_login,$adm_pass);
	if(!isDatabase($_REQUEST["newdb_name"])){
		$submit_err .= $txt_dbsql_this_is_not_a_valid_db_name[$lang]."<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT * FROM mysql.db WHERE Db='".$_REQUEST["newdb_name"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($r);
		if($num_rows > 0){
			$submit_err .= $txt_dbsql_a_db_by_that_name_exists[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= $txt_dbsql_incorrect_db_login_form[$lang]."<br>\n";
		$commit_flag = "no";
	}else{
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$submit_err .= $txt_dbsql_no_db_user_by_that_name_exists_in_the_db[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}
	if($commit_flag == "yes"){
		$q = "CREATE DATABASE ".$_REQUEST["newdb_name"].";";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "INSERT INTO mysql.db ( Host,Db,User,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,Grant_priv,References_priv,Index_priv,Alter_priv,Lock_tables_priv)
		VALUES ('%','".$_REQUEST["newdb_name"]."','".$_REQUEST["dbuser"]."','Y','Y','Y','Y','Y','Y','N','Y','Y','Y','Y');";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$q = "FLUSH PRIVILEGES;";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "modify_dbuser_pass"){
	checkLoginPass($adm_login,$adm_pass);
	// action=modify_dbuser_pass&dbuser=zigo&db_pass=bla
	if(!isFtpLogin($_REQUEST["dbuser"])){
		$submit_err .= $txt_dbsql_incorrect_db_login_form[$lang]."<br>\n";
		$commit_flag = "no";
	}
	if(!isDTCPassword($_REQUEST["db_pass"])){
		$submit_err .= $txt_dbsql_password_are_made_only_with_standards_chars_and_numbers_and_size[$lang]."<br>\n";
		$commit_flag = "no";
	}
	if($commit_flag == "yes"){
		$query = "SELECT * FROM mysql.user WHERE User='".$_REQUEST["dbuser"]."' AND dtcowner='$adm_login';";
		$result = mysql_query($query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			$submit_err .= $txt_dbsql_no_db_user_by_that_name_exists_in_the_db[$lang]."<br>\n";
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
		$submit_err .= $txt_dbsql_incorrect_db_login_form[$lang]."<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT * FROM mysql.db WHERE User='".$_REQUEST["dbuser"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$num_rows = mysql_num_rows($r);
		if($num_rows > 0){
			$submit_err .= $txt_dbsql_that_user_owns_some_db_please_remove_them[$lang]."<br>\n";
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
		$submit_err .= $txt_dbsql_incorrect_mysql_db_name[$lang]."<br>\n";
		$commit_flag = "no";
	}else{
		$q = "SELECT User FROM mysql.db WHERE Db='".$_REQUEST["dbname"]."';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n < 1){
			$submit_err .= $txt_dbsql_cannot_reselect_mysql_db_name[$lang]."<br>\n";
			$commit_flag = "no";
		}else{
			$a = mysql_fetch_array($r);
			$q = "SELECT User FROM mysql.user WHERE User='".$a["User"]."' AND dtcowner='$adm_login';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$n = mysql_num_rows($r);
			if($n < 1){
				$submit_err .= $txt_dbsql_mysql_db_ownership_not_valid_i_wont_del_because_you_dont_own_it[$lang]."<br>\n";
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
		$submit_err .= $txt_dbsql_incorrect_db_login_form[$lang]."<br>\n";
		$commit_flag = "no";
	}
	if(!isDatabase($_REQUEST["dbname"])){
		$submit_err .= $txt_dbsql_incorrect_mysql_db_name[$lang]."<br>\n";
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
				$submit_err .= $txt_dbsql_mysql_db_ownership_not_valid_i_wont_change_owner_because_you_dont_own_it[$lang]."<br>\n";
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
	if($num_rows != 1){
		$submit_err .= $txt_dbsql_user_or_password_incorrect[$lang]."<br>\n";
		$commit_flag = "no";
	}

	if($commit_flag == "yes"){
		if(!isDTCPassword($_REQUEST["new_mysql_password"])){
			$submit_err .= $txt_dbsql_password_are_made_only_with_standards_chars_and_numbers_and_size[$lang]."<br>\n";
			$commit_flag = "no";
		}
	}

	if($commit_flag == "yes"){
		mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
		$query = "UPDATE user SET Password=PASSWORD('".$_REQUEST["new_mysql_password"]."') WHERE User='$adm_login';";
		mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());
		mysql_query("FLUSH PRIVILEGES");
		mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\" !!!");
	}
}

?>
