<?php

if($panel_type!="email"){
	require("$dtcshared_path/inc/sql/dns.php");
	require("$dtcshared_path/inc/sql/database.php");
	require("$dtcshared_path/inc/sql/domain_info.php");
	require("$dtcshared_path/inc/sql/subdomain.php");
	require("$dtcshared_path/inc/sql/ftp.php");
}
require("$dtcshared_path/inc/sql/email.php");

?>
