<?php 

function dtc_db_auth() {
	if( !file_exists("/usr/share/dtc/shared/mysql_config.php") ){
		return false;
	}
	require_once("/usr/share/dtc/shared/mysql_config.php");

	$ressource_id = mysql_connect("$conf_mysql_host", "$conf_mysql_login", "$conf_mysql_pass");
	if($ressource_id == false){
		return false;
	}
	mysql_select_db($conf_mysql_db)or die("Could not connect to the DTC db!");
	$q = "SELECT * FROM admin";
	$r = mysql_query($q)or die("Could not query the DTC db!");
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$GLOBALS["users"][] = array($a["adm_login"],md5($a["adm_pass"]),$a["path"],"http://localhost",1,"",1,1);
	}
}

	/** ensure this file is being included by a parent file */
	defined( "_VALID_MOS" ) or die( "Direct Access to this location is not allowed." );
	$GLOBALS["users"]=array(); 
	dtc_db_auth();
?>