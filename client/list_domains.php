<?php


require_once("../shared/autoSQLconfig.php");
$panel_type="cronjob";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");

$q = "SELECT * FROM $pro_mysql_backup_table WHERE
server_login='".$_REQUEST["login"]."'
AND server_pass='".$_REQUEST["pass"]."'
AND server_addr='".$_SERVER["REMOTE_ADDR"]."';";
$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1)	die("Access not granted!\n");

switch($_REQUEST["action"]){
case "list_dns":
	$out .= "// Start of DTC generated slave zone file for backuping $conf_administrative_site\n";
	$q = "SELECT * FROM $pro_mysql_domain_table WHERE other_dns='default';";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= 'zone "'.$a["name"].'" {
	type slave;
	masters { ';
		if($a["primary_dns"] == "default"){
			$out .= $conf_main_site_ip;
		}else{
			$out .= $a["primary_dns"];
		}
		$out .= '; };
	file "'.$conf_generated_file_path.'/zones/'.$a["name"].'";
	};
';
	}
	$out .= "// End of DTC generated slave zone file for backuping $conf_administrative_site\n";
	break;
case "list_mx":
	$q = "SELECT * FROM $pro_mysql_domain_table WHERE other_mx='default';";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$out .= "<dtc_backup_mx_domain_list>\n";
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= $a["name"]."\n";
	}
	$out .= "</dtc_backup_mx_domain_list>\n";
	break;
case "update_request":
	$q = "UPDATE $pro_mysql_cronjob_table SET gen_qmail='yes',restart_qmail='yes',gen_named='yes',restart_named='yes' WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	$q = "UPDATE $pro_mysql_backup_table SET status='pending' WHERE type='mail_backup' OR type='dns_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	$out .= "Successfuly recieved trigger!";
	break;
default:
	break;
}

echo $out;
?>
