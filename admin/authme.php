<?php

if( !isset($_SERVER["PHP_AUTH_USER"]) || $_SERVER["PHP_AUTH_USER"] == ""){
	Header( "WWW-authenticate: basic realm=\"DTC Admin ".$_SERVER["HTTP_HOST"]."\"" );
	Header( "HTTP/1.0 401 Unauthorized" );
	echo _("Please login with username and password in order to access the DTC admin interface.");
	die();
}else{
	$q = "SELECT * FROM tik_admins WHERE pseudo='".mysql_escape_string($_SERVER['PHP_AUTH_USER'])."' AND tikadm_pass='".mysql_escape_string($_SERVER['PHP_AUTH_PW'])."';";
	$r = mysql_query($q)or die("Cannot query for auth line ".__LINE__." file ".__FILE__);
	$n = mysql_num_rows($r);
	if($n != 1){
		Header( "WWW-authenticate: basic realm=\"DTC Admin ".$_SERVER["HTTP_HOST"]."\"" );
		Header( "HTTP/1.0 401 Unauthorized" );
		echo _("Wrong login or password.");
		die();
	}
}


?>