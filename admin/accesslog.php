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
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$fullpath = $a["path"]."/".$a["name"]."/subdomains/".$a["subdomain_name"]."/logs";
		$table_name = str_replace(".","_",$a["name"].'$'.$a["subdomain_name"].'$'."xfer");

		echo "Checking $table_name ... ";
		if(mysql_table_exists("apachelogs",$table_name)){
			mysql_select_db("apachelogs");
			$qus = "SHOW TABLE STATUS LIKE '".$table_name."'";
			$res = mysql_query($qus) or die("Cannot execute query \"$qus\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
			$ars = mysql_fetch_array($res);
			if($ars["Rows"] > 0){
				$query = "SELECT MIN(time_stamp) AS start FROM apachelogs.".$table_name."";
				$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
				$start = mysql_result($result,0,"start");
				if($start<$today_midnight){
					echo "stats to be done!\n";
				}
				while($start<$today_midnight){
					$year = date("Y",$start);
					$month = date("m",$start);
					$day = date("d",$start);
					$dump_folder = $fullpath."/".$year."/".$month;;
					$dump_filename = $dump_folder."/access_".$day.".log";

					$start_24h = $start + (60*60*24);
					$year_24h = date("Y",$start_24h);
					$month_24h = date("m",$start_24h);
					$day_24h = date("d",$start_24h);

					$end = mktime(0,0,0,$month_24h,$day_24h,$year_24h);

					echo "Querying SELECT * FROM apachelogs.".$table_name."...";
					$query_dump = "SELECT * FROM apachelogs.".$table_name." WHERE time_stamp>='".$start."' AND time_stamp<='".$end."' ORDER BY time_stamp";
					$result_dump = mysql_query($query_dump) or die("Cannot execute query \"$query_dump\" file ".__FILE__." line ".__LINE__.": ".mysql_error());
					$dump_num_rows = mysql_num_rows($result_dump);
					if($dump_num_rows>0){
						echo "$dump_num_rows records for $dump_filename...\n";
						if(!is_dir($dump_folder)){
							exec("mkdir -p $dump_folder");
						}
						$handle = fopen ($dump_filename, "w+");
						for($z=0;$z<$dump_num_rows;$z++){
							$rezar = mysql_fetch_array($result_dump);
							if(strstr($rezar["referer"],$a["name"])){
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
						fclose($handle);
						echo "Calculating webalizer stats for ".$day." ".$month." ".$year."\n";
						$webalizer_cmd = "nice -n+20 webalizer -n ".$a["subdomain_name"].".".$a["name"]." -o $fullpath $dump_filename";
						echo "$webalizer_cmd\n";
						exec ($webalizer_cmd);
					}else{
						echo "table empty\n";
					}
					$query_dump = "DELETE FROM apachelogs.".$table_name." WHERE time_stamp<='".$end."'";
					$result_dump = mysql_query($query_dump) or die("Cannot execute query \"$query_dump\" file ".__FILE__." line ".__LINE__.": ".mysql_error());

					$opt_table = "OPTIMIZE TABLE apachelogs.".$table_name." ;";
					mysql_query($opt_table) or die("Cannot execute query \"$opt_table\" line ".__LINE__." file ".__FILE__.": ".mysql_error());

					$qus = "SHOW TABLE STATUS LIKE '".$table_name."'";
					$res = mysql_query($qus) or die("Cannot execute query \"$qus\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
					$ars = mysql_fetch_array($res);
					if($ars["Rows"] == 0){
						$start = $today_midnight;
					}else{
						$query = "SELECT MIN(time_stamp) AS start FROM apachelogs.".$table_name."";
						$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
						$start = mysql_result($result,0,"start");
					}
				}
			}else{
				echo "No records!\n";
			}
			mysql_select_db($conf_mysql_db);
		}else{
			echo "no table!\n";
		}
	}
}

function make_log_archive (){
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
	$cur_year = date("Y");
	$cur_month = date("m");
	$last_year = $cur_year - 1;
	$before_last_year = $last_year - 1;
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$fullpath = $a["path"]."/".$a["name"]."/subdomains/".$a["subdomain_name"]."/logs";
		for($j=0;$j<12;$j++){
			$m = $cur_month + $j;
			if($m > 12){
				$m = $m - 12;
				$year = $cur_year;
			}else{
				$year = $last_year;
			}
			if(strlen($m) < 2)	$m = "0".$m;
			// If the month folder exists, do the archive...
			$monthlog_path = $fullpath."/".$year."/".$m;
			if(is_dir($monthlog_path)){
				echo "Archiving $monthlog_path\n";
				$flist = array();
				if(($dh = opendir($monthlog_path)) !== false){
					while (($file = readdir($dh)) !== false) {
						if(filetype($monthlog_path ."/". $file) == "file"){
							$flist[] = $monthlog_path ."/". $file;
						}
					}
				}
				$nbr_file = sizeof($flist);
				if($nbr_file > 0){
					sort($flist);
					$temp = tempnam($fullpath,"accesslog_");
					for($k=0;$k<$nbr_file;$k++){
						// echo $flist[$k]."\n";
						$cmd = "cat ".$flist[$k]." >>".$temp;
						exec($cmd);
						unlink($flist[$k]);
					}
					rmdir("$fullpath/$year/$m");
					$cmd = "nice -n+20 gzip $temp";
					exec($cmd);
					$cmd = "mv ".$temp.".gz ".$fullpath."/accesslog.".$a["subdomain_name"].".".$a["name"]."_".$year."_".$m.".gz";
					exec ($cmd);
				}
			}
		}
		// Search if there are old logs (older than one year) that we should remove
		for($j=0;$j<12;$j++){
			$m = $cur_month + $j;
			if($m > 12){
				$m = $m - 12;
				$year = $cur_year - 1;
			}else{
				$year = $last_year - 1;
			}
			if(strlen($m) < 2)      $m = "0".$m;
			$thefile = $fullpath."/accesslog.".$a["subdomain_name"].".".$a["name"]."_".$year."_".$m.".gz";
			if(file_exists($thefile)){
				unlink($thefile);
			}
		}
	}
}

make_stats();
make_log_archive();

?>
