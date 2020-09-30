<?php

function mysql_table_exists($dbname,$tableName){
	$tables = array();
	$tablesResult = mysql_query("SHOW TABLES FROM " . $dbname);
	while ($row = mysql_fetch_row($tablesResult)) $tables[] = $row[0];
	return(in_array($tableName, $tables));
}

function fetchHTTPInfo($webname){
	global $pro_mysql_acc_http_table;
	$query = "SELECT SUM(bytes_sent) AS transfer FROM $pro_mysql_acc_http_table WHERE domain='".$webname."'
	AND month='".date("m")."' AND year='".date("Y")."';";
	$result = mysql_query($query)or die("Cannot execute query \"$query\"".mysql_error());
	$num_rows = mysql_num_rows($result);
	$amount = mysql_result($result,0,"transfer");

	return $amount;
}

function fetchFTPInfo($webname){
	global $pro_mysql_acc_ftp_table;
	$query = "SELECT SUM(transfer) AS transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$webname."'
	AND month='".date("n")."' AND year='".date("Y")."';";
	$result = mysql_query($query) or die("Cannot execute query \"$query\"");
	$total_ftp_amount = mysql_result($result,0,"transfer");

	return $total_ftp_amount;
}

function fetchSMTPInfo($webname){
	global $pro_mysql_acc_email_table;
	$q = "SELECT smtp_trafic AS transfer FROM $pro_mysql_acc_email_table WHERE domain_name='".$webname."'
	AND month='".date("n")."' AND year='".date("Y")."';";
	$r = mysql_query($q) or die("Cannot execute query \"$q\" !".
	mysql_error()." line ".__LINE__." file ".__FILE__);
	if(mysql_num_rows($r) < 1)
		$total_smtp_amount = 0;
	else
		$total_smtp_amount = mysql_result($r,0,"transfer");

	return $total_smtp_amount;
}

function fetchIMAPInfo($webname){
	global $pro_mysql_acc_email_table;
	$q = "SELECT imap_trafic AS transfer FROM $pro_mysql_acc_email_table WHERE domain_name='".$webname."'
	AND month='".date("n")."' AND year='".date("Y")."';";
	$r = mysql_query($q) or die("Cannot execute query \"$q\" !".
	mysql_error()." line ".__LINE__." file ".__FILE__);
	if(mysql_num_rows($r) < 1)
		$total_imap_amount = 0;
	else
		$total_imap_amount = mysql_result($r,0,"transfer");

	return $total_imap_amount;
}

function fetchPOPInfo($webname){
	global $pro_mysql_acc_email_table;
	$q = "SELECT pop_trafic AS transfer FROM $pro_mysql_acc_email_table WHERE domain_name='".$webname."'
	AND month='".date("n")."' AND year='".date("Y")."';";
	$r = mysql_query($q) or die("Cannot execute query \"$q\" !".
	mysql_error()." line ".__LINE__." file ".__FILE__);
	if(mysql_num_rows($r) < 1)
		$total_pop_amount = 0;
	else
		$total_pop_amount = mysql_result($r,0,"transfer");

	return $total_pop_amount;
}

/*
function sum_email($webname){
}

function sum_http($webname){
	global $pro_mysql_subdomain_table;
	global $pro_mysql_acc_http_table;
	global $conf_mysql_db;

	$current_year = date("Y",time());
	$current_month = date("n",time());

	mysql_select_db($conf_mysql_db);
	$query = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='".$webname."' AND ip='default'";
	$result = mysql_query($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);
	$selected_month_start = mktime(0,0,0,$current_month,1,$current_year);
	$selected_month_end = mktime(0,0,0,($current_month+1),1,$current_year)-1;
	for($i=0;$i<$num_rows;$i++){
		$subdomain_name = mysql_result($result,$i,"subdomain_name");
		$db_webname=strtr($webname,'.','_');
		$db_select_name = $db_webname.'$'.$subdomain_name.'$'."xfer";

		if(mysql_table_exists("apachelogs",$db_select_name)){

	This old portion of code is now replaced by
	realtime using mod_log_sql2 + patch


			mysql_select_db("apachelogs");
			// Get bytes_sent
			$q_bytes = "SELECT SUM( bytes_sent ) AS amount FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
			$r_bytes = mysql_query($q_bytes) or die("Cannot execute query \"$q_visits\" !!! ".mysql_error());
			$bytes_sent = mysql_result($r_bytes,0,"amount");
			//$bytes_sent = 0;
			// Get visits
			$q_visits = "SELECT DISTINCT id FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
			$r_visits = mysql_query($q_visits) or die("Cannot execute query \"$q_visits\" !!! ".mysql_error());
			$visits = mysql_num_rows($r_visits);
//			$visits = 0;

			// Get hosts
			$q_hosts = "SELECT remote_host FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end." GROUP BY remote_host";
			$r_hosts = mysql_query($q_hosts) or die("Cannot execute query \"$q_hosts\" !!! ".mysql_error());
			$hosts = mysql_num_rows($r_hosts);
//			$hosts = 0;

			// Get impressions
			$q = "SELECT id FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end.";";
			$r_imp = mysql_query($q) or die("Cannot execute query \"$q\" !!! ".mysql_error());
			$imp = mysql_num_rows($r_imp);
//			$imp = 0;

			mysql_select_db($conf_mysql_db);

			$query_dub = "SELECT * FROM $pro_mysql_acc_http_table WHERE
			MONTH=".$current_month." AND year=".$current_year." AND vhost='".$subdomain_name."' AND domain='".$webname."'";

			if(mysql_num_rows(mysql_query($query_dub))==1){
				$query_insert_bytes = "UPDATE $pro_mysql_acc_http_table set
				bytes_sent='".$bytes_sent."',
				count_visits='".$visits."',
				count_hosts='".$hosts."',
				count_impressions='".$imp."'
				WHERE MONTH=".$current_month." AND year=".$current_year." AND vhost='".$subdomain_name."' AND domain='".$webname."'";
				mysql_query($query_insert_bytes)or die("Cannot execute query \"$query_insert_bytes\"".mysql_error());
			}else{
				mysql_select_db($conf_mysql_db);
				$query_insert_bytes = "INSERT INTO $pro_mysql_acc_http_table (vhost,bytes_sent,count_hosts,count_visits,count_impressions,domain,MONTH,year)
				VALUES ('".$subdomain_name."','".$bytes_sent."','".$hosts."','".$visits."','".$imp."','".$webname."','".$current_month."','".$current_year."')";
				mysql_query($query_insert_bytes)or die("Cannot execute query \"$query_insert_bytes\"".mysql_error());
				//dump_access_log($subdomain_name,$webname,$db_select_name,$current_month,$current_year);
			}
			mysql_select_db($conf_mysql_db);

			dump_access_log($subdomain_name,$webname,$db_select_name,$current_month,$current_year);
		}
	}
	mysql_select_db($conf_mysql_db);
}

function dump_access_log($vhost,$domain,$db_select_name,$current_month,$current_year){
	global $conf_mysql_db;
	global $pro_mysql_domain_table;
	global $dtcshared_path;
	mysql_select_db("apachelogs");
	$query = "SELECT MAX(time_stamp) AS end FROM `".$db_select_name."`";
	$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
	$end = mysql_result($result,0,"end");

	$query = "SELECT MIN(time_stamp) AS start FROM `".$db_select_name."`";
	$result = mysql_query($query) or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
	$start = mysql_result($result,0,"start");

	if($start!=0 && $end!=0){
		$year_start = date("Y",$start);
		$year_end = date("Y",$end);
		$start_month = date("m",$start);

		for($year=$year_start;$year<=$year_end;$year++){
			for($month=$start_month;$month<=12;$month++){
				mysql_select_db($conf_mysql_db);
				$query_admin = "SELECT * FROM $pro_mysql_domain_table WHERE name='$domain'";
				$result_admin = mysql_query($query_admin) or die("Cannot execute query \"$query_admin\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
				$adm_login = mysql_result($result_admin,0,"owner");

				$admin_path = getAdminPath($adm_login);

				mysql_select_db("apachelogs");

				$dump_path = $admin_path."/".$domain."/subdomains/".$vhost."/logs/";
				$dump_file_name = $dump_path."access_log.".$vhost.$domain."_".$year."_".$month;
				if($year == $current_year && $month == $current_month){
					
				}
				if(!file_exists($dump_file_name.".bz2") && ($year!=$current_year || $month!=$current_month)){
					$selected_month_start = mktime(0,0,0,$month,1,$year);
					$selected_month_end = (mktime(0,0,0,($month+1),1,$year))-1;
//					$query_dump = "SELECT remote_host,time_stamp,request_uri,status,bytes_sent,referer,agent FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
					$query_dump = "SELECT * FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end." ORDER BY time_stamp";
					$result_dump = mysql_query($query_dump) or die("Cannot execute query \"$query_dump\" file ".__FILE__." line ".__LINE__.": ".mysql_error());
					$dump_num_rows = mysql_num_rows($result_dump);
					if($dump_num_rows>0){
						echo "\nDumping logs for ".$db_select_name."_".$month."_".$year."\n";
						$handle = fopen ($dump_file_name, "w");
						for($z=0;$z<$dump_num_rows;$z++){
							$rezar = mysql_fetch_array($result_dump);
							if(strstr($rezar["referer"],$domain))	$rezar["referer"] == "self";
//							$content = $rezar["remote_host"]." - - ".
//							date("[d/M/Y:H:i:s] ",$rezar["time_stamp"]).
//							'"'.$rezar["request_uri"].'" '.$rezar["status"].
//							" ".$rezar["bytes_sent"].
//							' "'.$rezar["referer"].'" "'.$rezar["agent"].'"'."\n";
// obso (luke) patch
$content = $rezar["remote_host"]." - - ".
date("[d/M/Y:H:i:s O] ",$rezar["time_stamp"]).
'"'.$rezar["request_method"]." ".$rezar["request_uri"]." ".$rezar["request_protocol"].'" '.
$rezar["status"]." ".$rezar["bytes_sent"].
' "'.$rezar["referer"].'" "'.$rezar["agent"].'"'."\n";

							if (!fwrite($handle, $content))
        						echo "WARNING: Cannot write logfile, maybe disk full...\n";
						}
						fclose($handle);
						echo "Calculating webalizer stats for ".$month."_".$year."\n";
						$webalizer_cmd = "webalizer -n $vhost.$domain -o $dump_path $dump_file_name";
						echo "$webalizer_cmd\n";
						exec ($webalizer_cmd);
						$tar_cmd = "bzip2 ".$dump_file_name;
						echo $tar_cmd."\n";
						exec ($tar_cmd);
						check_sum($db_select_name,$selected_month_start,$selected_month_end,$domain,$vhost);
					}
					//check_sum($db_select_name,$selected_month_start,$selected_month_end,$domain,$vhost);
					$query_del = "DELETE FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
					mysql_select_db("apachelogs");
					mysql_query($query_del) or die("Cannot execute query \"$query_del\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
					$opt_table = "OPTIMIZE TABLE `".$db_select_name.";";
					mysql_query($opt_table) or die("Cannot execute query \"$opt_table\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
				}
			}
			$start_month = 1;
		}
	}
	mysql_select_db($conf_mysql_db);
}


function check_sum($db_select_name,$selected_month_start,$selected_month_end,$webname,$subdomain_name){
	global $pro_mysql_subdomain_table;
	global $pro_mysql_acc_http_table;
	global $conf_mysql_db;
	mysql_select_db("apachelogs");

	$current_month = date("n",$selected_month_start);
	$current_year = date("Y",$selected_month_start);

	// Get bytes_sent
	$q_bytes = "SELECT SUM( bytes_sent ) AS amount FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
	$r_bytes = mysql_query($q_bytes) or die("Cannot execute query \"$q_visits\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
	$bytes_sent = mysql_result($r_bytes,0,"amount");
	$visits = 0;

	// Get hosts
	$q_hosts = "SELECT remote_host FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end." GROUP BY remote_host";
        $r_hosts = mysql_query($q_hosts) or die("Cannot execute query \"$q_hosts\" !!! ".mysql_error());
	$hosts = mysql_num_rows($r_hosts);

	// Get impressions
	$q_imp = "SELECT id FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;;
	$r_imp = mysql_query($q_imp) or die("Cannot execute query \"$q_imp\" !!! ".mysql_error());
	$imp = mysql_num_rows($r_imp);

	mysql_select_db($conf_mysql_db);

	$query_dub = "SELECT * FROM $pro_mysql_acc_http_table WHERE
	MONTH=".$current_month." AND year=".$current_year." AND vhost='".$subdomain_name."' AND domain='".$webname."'";

	if(mysql_num_rows(mysql_query($query_dub))==1){
		$query_insert_bytes = "UPDATE $pro_mysql_acc_http_table set
		bytes_sent=".$bytes_sent.",
		count_visits=".$visits.",
		count_hosts=".$hosts.",
		count_impressions=".$imp."
		WHERE MONTH=".$current_month." AND year=".$current_year." AND vhost='".$subdomain_name."' AND domain='".$webname."'";
		mysql_query($query_insert_bytes)or die("Cannot execute query \"$query_insert_bytes\"".mysql_error());
	}else{
		mysql_select_db($conf_mysql_db);
		$query_insert_bytes = "INSERT INTO $pro_mysql_acc_http_table (vhost,bytes_sent,count_hosts,count_visits,count_impressions,domain,MONTH,year)
		VALUES ('".$subdomain_name."','".$bytes_sent."','".$hosts."','".$visits."','".$imp."','".$webname."','".$current_month."','".$current_year."')";
		mysql_query($query_insert_bytes)or die("Cannot execute query \"$query_insert_bytes\"".mysql_error());
	}
}
*/

function sum_ftp($webname){
	global $pro_mysql_ftp_table;
	global $conf_mysql_db;
	global $pro_mysql_acc_ftp_table;
//	mysql_select_db($conf_mysql_db);
////////////////////////////////////////////////////////////////////////////////////
// Sirhexalo: when you see this text, please erase it !!! and commit the file !!! //
// Zigo partly rewriten (or cleaned-up, as you like :), because it was
// accouting a single user stuff to all domains of the admin. Removed the
// use of the last_run stuff... Please re-check my code.
// Thomas Goirand comment: if was there but there was some code before,
// accessing to db dtc. Then that would have been either buggy
// or not usefull. As long as it didn't bug, I suppose it's not
// usefull so removed.
////////////////////////////////////////////////////////////////////////////////////
//	mysql_select_db("dtc");

	// Get all ftp users of the domain and sum they're transfer
	$total_ftp_amount = 0;
	$query = "SELECT SUM(dl_bytes) AS dl_amount, SUM(ul_bytes) AS ul_amount FROM $pro_mysql_ftp_table WHERE hostname='".$webname."'";
	$result = mysql_query($query) or die("Cannot execute query \"$query\" !".mysql_error());
	$num_rows = mysql_num_rows($result);
	$total_ftp_amount = mysql_result($result,0,"dl_amount") + mysql_result($result,0,"ul_amount");
	$query = "UPDATE $pro_mysql_ftp_table set ul_bytes=0, dl_bytes=0 WHERE hostname='".$webname."'";
	mysql_query($query) or die("Cannot execute query \"$query\"".mysql_error());

	$q = "SELECT last_run,transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$webname."' AND month='".date("m",time())."' AND year='".date("Y",time())."'";
	$result_l = mysql_query($q) or die("Cannot execute query \"$q\"");
	$numrow_acctable = mysql_num_rows($result_l);
	if($numrow_acctable < 1){
		$q2 = "INSERT INTO $pro_mysql_acc_ftp_table (sub_domain,transfer,last_run,month,year) VALUES ('".$webname."','$total_ftp_amount','".time()."', ".date("m",time()).",".date("Y",time()).")";
	}else{
		$q2 = "UPDATE $pro_mysql_acc_ftp_table set transfer=transfer+".$total_ftp_amount."+0, last_run=".time()." WHERE sub_domain='".$webname."' AND month='".date("m",time())."' AND year='".date("Y",time())."';";
	}
	mysql_query($q2) or die("Cannot execute query \"$q2\" !".mysql_error());
}

?>
