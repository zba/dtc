#!/usr/bin/env php
<?php
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());

$script_start_time = time();
$panel_type="cronjob";

chdir(dirname(__FILE__));

require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

function delete_old_files_in_tmp(){
	global $conf_mysql_db;
	global $conf_webalizer_country_graph;
	global $pro_mysql_domain_table;
	global $dtcshared_path;
	global $conf_generated_file_path;

	global $conf_use_webalizer;
	global $conf_use_awstats;
	global $conf_use_visitors;

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
		$fullpath = $a["path"]."/".$a["name"]."/subdomains/".$a["subdomain_name"]."/tmp";
		// Delete files older than 7 days (atime adds one day to the +6...)
		$cmd = "find $fullpath -atime +6 -exec rm {} \;";
		if(file_exists($fullpath)) exec($cmd);
	}
}

function make_stats(){
	global $conf_mysql_db;
	global $conf_webalizer_country_graph;
	global $pro_mysql_domain_table;
	global $dtcshared_path;
	global $conf_generated_file_path;

	global $conf_use_webalizer;
	global $conf_use_awstats;
	global $conf_use_visitors;

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

		if(!file_exists("$fullpath/index.php")){
			copy("$conf_generated_file_path/dtc_stats_index.php","$fullpath/index.php");
		}

		$html_fullpath = $a["path"]."/".$a["name"]."/subdomains/".$a["subdomain_name"]."/html";
		$table_name = str_replace("-","A",str_replace(".","_",$a["name"].'$'.$a["subdomain_name"].'$'."xfer"));

		echo "Checking $table_name ... ";
		if(mysql_table_exists("apachelogs",$table_name)){
			mysql_select_db("apachelogs");
			$qus = "SHOW TABLE STATUS LIKE '".$table_name."'";
			$res = mysql_query($qus) or die("Cannot execute query \"$qus\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
			$ars = mysql_fetch_array($res);
			if($ars["Rows"] > 0){
				$query = "SELECT MIN(time_stamp) AS start FROM apachelogs.`".$table_name."`";
				$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
				$start = mysql_result($result,0,"start");
				if($start<$today_midnight){
					echo "stats to be done!";
				}
				while($start<$today_midnight){
					$year = date("Y",$start);
					$month = date("m",$start);
					$day = date("d",$start);
					$dump_folder = $fullpath."/".$year."/".$month;;
					$dump_filename = $dump_folder."/access_".$day.".log";

#					$start_24h = $start + (60*60*24);
#					$year_24h = date("Y",$start_24h);
#					$month_24h = date("m",$start_24h);
#					$day_24h = date("d",$start_24h);
#
#					$end = mktime(0,0,0,$month_24h,$day_24h,$year_24h);
					$secs = $start % (60*60*24);
					$end = $start - $secs + (60*60*24);

					echo "Querying SELECT * FROM apachelogs.".$table_name."...";
					$query_dump = "SELECT * FROM apachelogs.`".$table_name."` WHERE time_stamp>='".$start."' AND time_stamp<='".$end."' ORDER BY time_stamp";
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
							// in case we don't have a request_method logged
							if (!isset($rezar["request_method"]))
							{
								$rezar["request_method"]="GET";
							}
							// in case we don't have a HTTP proto logged
							if (!isset($rezar["request_protocol"]))
							{
								$rezar["request_protocol"]="HTTP/1.1";
							}
							if(strstr($rezar["referer"],$a["name"])){
								$rezar["referer"] = "self";
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
						if($conf_webalizer_country_graph != "yes"){
							$webalizer_cmd = "nice -n+20 webalizer -p -R 50 -Y -n ".$a["subdomain_name"].".".$a["name"]." -o $fullpath $dump_filename";
						}else{
							$webalizer_cmd = "nice -n+20 webalizer -p -R 50 -D ".$a["subdomain_name"].".".$a["name"].".dnscache -N 20 -n ".$a["subdomain_name"].".".$a["name"]." -o $fullpath $dump_filename";
						}
						echo "$webalizer_cmd\n";
						exec ($webalizer_cmd);

						if($conf_use_visitors == "yes"){
							echo "Calculating visitor stats...\n";
							$visitor_cmd = "nice -n+20 visitors -A -m 30 $dump_folder/access_*.log -o html > $fullpath/$year.$month.report.html";
							echo "$visitor_cmd\n";
							exec ($visitor_cmd);

							// copy the template file
							if (!file_exists("$fullpath/visitors.php")){
								copy("$dtcshared_path/visitors_template/visitors.php", "$fullpath/visitors.php");
							}
						}

						if($conf_use_awstats == "yes" && file_exists("/usr/lib/cgi-bin/awstats.pl")){
							if(!file_exists("$fullpath/awstats")){
								mkdir("$fullpath/awstats");
							}
							$fqdn = $a["subdomain_name"].".".$a["name"];
							if(file_exists("/usr/share/doc/awstats/examples/awstats_buildstaticpages.pl")){
								$aw_cmd = "export AWSTATS_FULL_DOMAIN=\"".$a["subdomain_name"].".".$a["name"]."\" ; export AWSTATS_DIR_DATA=\"$fullpath/awstats\" ; export AWSTATS_LOG_FILE=\"$dump_filename\" ; nice -n+20 /usr/share/doc/awstats/examples/awstats_buildstaticpages.pl -config=dtc -update -awstatsprog=/usr/lib/cgi-bin/awstats.pl -dir=$fullpath/awstats";
								exec($aw_cmd);
							}
						}
						// disable AWSTATS for now, it's too slow
						/*
						$stat_script = "#!/bin/sh
AWSTATS_LOG_FILE=$dump_filename
if [ -f \$AWSTATS_LOG_FILE ]; then
        AWSTATS_FULL_DOMAIN=".$a["subdomain_name"].".".$a["name"]."
	if [ ! -e $fullpath/awstats ]; then
		mkdir -p $fullpath/awstats
	fi
        AWSTATS_DIR_DATA=$fullpath/awstats
        export AWSTATS_LOG_FILE AWSTATS_FULL_DOMAIN AWSTATS_DIR_DATA
	echo \"\$AWSTATS_LOG_FILE \$AWSTATS_FULL_DOMAIN \$AWSTATS_DIR_DATA\" >> /tmp/awstats.log
        if [ -f /usr/share/doc/awstats/examples/awstats_buildstaticpages.pl ]; then
                nice -n+20 /usr/share/doc/awstats/examples/awstats_buildstaticpages.pl -config=dtc -update -awstatsprog=/usr/lib/cgi-bin/awstats.pl -dir=$fullpath/awstats
		nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nobody`
		# if we can't find the nobody group, try nogroup
		if [ -z \"\"\$nobodygroup ]; then
			nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nogroup`
		fi
		# if we can't find nogroup, then set to 65534
		if [ -z \"\"\$nobodygroup ]; then
			nobodygroup=65534
		fi

                chown nobody:\$nobodygroup $fullpath/awstats
		if [ ! -e $html_fullpath/awstats ]; then 
			ln -s $fullpath/awstats $html_fullpath/awstats
		fi
        fi
fi
";
						$filep = fopen("$fullpath/awstats.sh", "w+");
						if( $filep == NULL){
							die("Cannot open file for writing");
						}
						fwrite($filep,$stat_script);
						fclose($filep);

						echo "$fullpath/awstats.sh\n";
						chmod("$fullpath/awstats.sh",0750);
						exec ("$fullpath/awstats.sh");
						*/ 
					}else{
						echo "table empty\n";
					}
					// For sites with high traffic and stats to generate - prevent "MySQL server has gone away" error
					// Check if database still alive
					$is_db_alive = "SELECT FROM apachelogs.`".$table_name."` WHERE time_stamp='".$end."' LIMIT 0,1";
					// If not trying reconnect
					if (!mysql_query($is_db_alive)) {
						mysql_close();
						if(connect2base() == false){
							die("Cannot connect to database !!!");
						}
					}
															
					$query_dump = "DELETE FROM apachelogs.`".$table_name."` WHERE time_stamp<='".$end."'";
					$result_dump = mysql_query($query_dump) or die("Cannot execute query \"$query_dump\" file ".__FILE__." line ".__LINE__.": ".mysql_error());

					$opt_table = "OPTIMIZE TABLE apachelogs.`".$table_name."` ;";
					mysql_query($opt_table) or die("Cannot execute query \"$opt_table\" line ".__LINE__." file ".__FILE__.": ".mysql_error());

					$qus = "SHOW TABLE STATUS LIKE '".$table_name."'";
					$res = mysql_query($qus) or die("Cannot execute query \"$qus\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
					$ars = mysql_fetch_array($res);
					if($ars["Rows"] == 0){
						$start = $today_midnight;
					}else{
						$query = "SELECT MIN(time_stamp) AS start FROM apachelogs.`".$table_name."`";
						$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
						$start = mysql_result($result,0,"start");
					}
				}
				echo "[OK]\n";
			}else{
				echo "No records!\n";
			}
		}else{
			echo "no table!\n";
		}
		mysql_select_db($conf_mysql_db);
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
delete_old_files_in_tmp();

?>
