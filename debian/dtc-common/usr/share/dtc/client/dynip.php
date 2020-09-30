<?

require("../shared/autoSQLconfig.php");
require("$dtcshared_path/vars/table_names.php");
require("$dtcshared_path/drawlib/dtc_functions.php");

// $pro_mysql_domain_table
// $pro_mysql_subdomain_table
// $pro_mysql_cronjob_table

$login = $_REQUEST["login"];
$pass = $_REQUEST["pass"];
if(isset($_REQUEST["ip"])){
	$ip = $_REQUEST["ip"];
}
$domain = $_REQUEST["domain"];

if(!isset($login) || $login == "" || !isset($pass) || $pass == ""){
	die("Incorrect params");
}
if(!isFtpLogin($login)){
        die("Requested login does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" sign.");
}
if(!isDTCPassword($pass)){
        die("Requested pass does not look like to be correct. It should be made only with letters, numbers, \".\" or \"-\" sign.");
}
if(!isHostname($domain)){
	die("Requested domain name does not looklike to be correct. Please check !");
}

$query = "SELECT * FROM $pro_mysql_subdomain_table WHERE login='$login' AND pass='$pass' AND domain_name='$domain';";
$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
$num_rows = mysql_num_rows($result);
if($num_rows != 1){
	die("Incorrect login, pass or domain name !");
}else{
	if(!isset($ip) || $ip == ""){
		$ip = $_SERVER["REMOTE_ADDR"];
	}else{
		if(!isIP($ip)){
			die("Incorrect IP format !");
		}
	}
	$row = mysql_fetch_array($result);
	if($ip != $row["ip"]){
		$edit_domain = $row["domain_name"];
		$domupdate_query = "UPDATE $pro_mysql_domain_table SET generate_flag='yes' WHERE name='$edit_domain' LIMIT 1;";
	        $domupdate_result = mysql_query ($domupdate_query)or die("Cannot execute query \"$domupdate_query\"");

		$query = "UPDATE $pro_mysql_subdomain_table SET ip='$ip' WHERE login='$login' AND pass='$pass';";
		mysql_query($query)or die("Cannot execute query \"$query\" !!!".mysql_error());

		$adm_query = "UPDATE $pro_mysql_cronjob_table SET gen_named='yes',reload_named='yes' WHERE 1;";
		mysql_query($adm_query)or die("Cannot execute query \"$adm_query\" !!!".mysql_error());;
		echo "Succes: updated to $ip\n";
	}else{
		echo "Succes: update not needed to $ip\n";
	}

}

?>
