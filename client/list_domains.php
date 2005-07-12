<?php


require_once("../shared/autoSQLconfig.php");
$panel_type="cronjob";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");

$q = "SELECT * FROM $pro_mysql_backup_table WHERE
server_login='".addslashes($_REQUEST["login"])."'
AND server_pass='".addslashes($_REQUEST["pass"])."'
AND server_addr='".$_SERVER["REMOTE_ADDR"]."';";
$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1)	die("Access not granted!\n");

$out = ""; //init $out variable

switch($_REQUEST["action"]){
case "list_dns":
	$out .= "// Start of DTC generated slave zone file for backuping $conf_administrative_site\n";
	$q = "SELECT * FROM $pro_mysql_domain_table WHERE other_dns='default' AND primary_dns='default';";
	$out .= "// $q";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$out .= "// $n domain(s) installed\n";
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
	file "'.$conf_generated_file_path.'/' . $conf_named_slavezonefiles_path . '/'.$a["name"].'";
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
	$q = "UPDATE $pro_mysql_cronjob_table SET gen_qmail='yes',restart_qmail='yes',gen_named='yes',reload_named='yes' WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	$q = "UPDATE $pro_mysql_backup_table SET status='pending' WHERE type='mail_backup' OR type='dns_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());

	$out .= "Successfuly recieved trigger!";
	break;
case "list_mx_recipients":
	$q = "SELECT * FROM $pro_mysql_domain_table WHERE other_mx='default';";
	$r = mysql_query($q)or die("Cannot query $q ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$out .= "<dtc_backup_mx_recipient_list>\n";
	for($i=0;$i<$n;$i++)
	{
		$a = mysql_fetch_array($r);
		$domain = $a["name"];
		$catchall_email = $a["catchall_email"];
		$q_email = "SELECT fullemail FROM `pop_access` WHERE mbox_host='$domain';";
		$r_email = mysql_query($q_email)or die("Cannot query $q_email ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n_email = mysql_num_rows($r_email);
		for ($j=0; $j < $n_email; $j++) 
		{
			$a_email = mysql_fetch_array($r_email);
			$out .= $a_email["fullemail"] . "\n";
		}
		//now for the catch_all email if the user has set it
		if (isset($catchall_email) && strlen($catchall_email) > 0) 
		{
			$out .= "@" . $domain . "\n";
		}
		//add the mailing lists as well
		$q_mailinglist = "SELECT name FROM `mailinglist` WHERE domain='$domain';";
		$r_mailinglist =  mysql_query($q_mailinglist)or die("Cannot query $q_mailinglist ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
		$n_mailinglist = mysql_num_rows($r_mailinglist);
		for ($j=0; $j < $n_mailinglist; $j++)
		{
			$a_mailinglist = mysql_fetch_array($r_mailinglist);
			if (isset($domain) && isset($a_mailinglist["name"]))
			{
				$out .= $a_mailinglist["name"] . "@" . $domain . "\n";
			}
		}
		//now make sure we have abuse@ and postmaster@
		$out .= "abuse@" . $domain . "\n";
		$out .= "postmaster@" . $domain . "\n";
	}

	$out .= "</dtc_backup_mx_recipient_list>\n";
	break;
default:
	break;
}

echo $out;
?>
