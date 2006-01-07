<?php

$script_start_time = time();
$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");


function make_stats(){
	global $conf_mysql_db;
	global $pro_mysql_domain_table;
	global $dtcshared_path;

	$today_midnight = mktime(0,0,0);

	$q = "SELECT admin.adm_login,admin.path,subdomain.subdomain_name,domain.name
	FROM admin,domain,subdomain
	WHERE domain.owner=admin.adm_login
	AND subdomain.domain_name=domain.name
	AND subdomain.ip='default'
	AND subdomain.generate_vhost='yes'
	ORDER BY admin.adm_login,domain.name,subdomain.subdomain_name";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	mysql_select_db("apachelogs");
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$fullpath = $a["path"]."/".$a["name"]."/subdomains/".$a["subdomain_name"]."/logs";
		$table_name = $a["name"].'$'.$a["subdomain_name"].'$'."xfer";
		
		$query = "SELECT MIN(time_stamp) AS start FROM ".table_name."";
		$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
		$start = mysql_result($result,0,"start");
		while($start<$today_midnight){
			$query_dump = "SELECT * FROM ".$db_select_name." WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end." ORDER BY time_stamp";
			$result_dump = mysql_query($query_dump) or die("Cannot execute query \"$query_dump\" file ".__FILE__." line ".__LINE__.": ".mysql_error());
			$dump_num_rows = mysql_num_rows($result_dump);
			if($dump_num_rows>0){
				echo "\nDumping logs for ".$table_name."_".$month."_".$year."\n";
				$handle = fopen ($dump_file_name, "w");
				for($z=0;$z<$dump_num_rows;$z++){
					$rezar = mysql_fetch_array($result_dump);
					if(strstr($rezar["referer"],$domain)){
						$rezar["referer"] == "self";
					}
					$content = $rezar["remote_host"]." - - ".
						date("[d/M/Y:H:i:s O] ",$rezar["time_stamp"]).
						'"'.$rezar["request_method"]." ".$rezar["request_uri"]." ".$rezar["request_protocol"].'" '.
						$rezar["status"]." ".$rezar["bytes_sent"].
						' "'.$rezar["referer"].'" "'.$rezar["agent"].'"'."\n";
					if (!fwrite($handle, $content)){
						echo "WARNING: Cannot write logfile, maybe disk full...\n";
					}
				}
			}
			$query = "SELECT MIN(time_stamp) AS start FROM ".table_name."";
			$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
			$start = mysql_result($result,0,"start");
		}
	}
	mysql_select_db($conf_mysql_db);
}

make_stats();

?>
