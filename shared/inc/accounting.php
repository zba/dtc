<?
function mysql_table_exists($dbname,$tableName){
	$tables = array();
	$tablesResult = mysql_list_tables($dbname);
	while ($row = mysql_fetch_row($tablesResult)) $tables[] = $row[0];
	return(in_array($tableName, $tables));
}

function fetchHTTPInfo($webname){
	global $pro_mysql_acc_http_table;
	$query = "SELECT SUM(bytes_sent) AS transfer FROM $pro_mysql_acc_http_table WHERE domain='".$webname."'";
	$result = mysql_query($query)or die("Cannot execute query \"$query\"".mysql_error());
	$num_rows = mysql_num_rows($result);
	$amount = mysql_result($result,0,"transfer");

	return $amount;
}
                                                                                                                                                    
function fetchFTPInfo($webname){
	global $pro_mysql_acc_ftp_table;
	$query = "SELECT SUM(transfer) AS transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$webname."'";
	$result = mysql_query($query) or die("Cannot execute query \"$query\"");
	$total_ftp_amount = mysql_result($result,0,"transfer");

	return $total_ftp_amount;
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
	for($i=0;$i<$num_rows;$i++)
	{
		$subdomain_name = mysql_result($result,$i,"subdomain_name");
		$db_webname=strtr($webname,'.','_');
		$db_select_name = $db_webname."#".$subdomain_name."#xfer";

		if(mysql_table_exists("apachelogs",$db_select_name))
		{
		mysql_select_db("apachelogs");

		// Get bytes_sent
		$q_bytes = "SELECT SUM( bytes_sent ) AS amount FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
        $r_bytes = mysql_query($q_bytes) or die("Cannot execute query \"$q_visits\" !!! ".mysql_error());
		$bytes_sent = mysql_result($r_bytes,0,"amount");
		//$bytes_sent = 0;
		// Get visits
		/*$q_visits = "SELECT DISTINCT id FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
        $r_visits = mysql_query($q_visits) or die("Cannot execute query \"$q_visits\" !!! ".mysql_error());
		$visits = mysql_num_rows($r_visits);*/
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

		if(mysql_num_rows(mysql_query($query_dub))==1)
		{
			$query_insert_bytes = "UPDATE $pro_mysql_acc_http_table set
			bytes_sent=bytes_sent+".$bytes_sent."+0,
			count_visits=count_visits+".$visits."+0,
			count_hosts=count_hosts+".$hosts."+0,
			count_impressions=count_impressions+".$imp."+0
			WHERE MONTH=".$current_month." AND year=".$current_year." AND vhost='".$subdomain_name."' AND domain='".$webname."'";
			mysql_query($query_insert_bytes)or die("Cannot execute query \"$query_insert_bytes\"".mysql_error());
		}
		else
		{

			$query_insert_bytes = "INSERT INTO $pro_mysql_acc_http_table (vhost,bytes_sent,count_hosts,count_visits,count_impressions,domain,MONTH,year)
			VALUES ('".$subdomain_name."','".$bytes_sent."','".$hosts."','".$visits."','".$imp."','".$webname."','".$current_month."','".$current_year."')";
			mysql_query($query_insert_bytes)or die("Cannot execute query \"$query_insert_bytes\"".mysql_error());
		}
		//dump_access_log($subdomain_name,$webname,$db_select_name,$current_month,$current_year);
		}
	}
	mysql_select_db($conf_mysql_db);
}

function dump_access_log($vhost,$domain,$db_select_name,$current_month,$current_year)
{
	mysql_select_db("apachelogs");
	$query = "SELECT MAX(time_stamp) AS end FROM `".$db_select_name."`";
    $result = mysql_query($query) or die("Cannot execute query \"$query\" !!! ".mysql_error());
	$end = mysql_result($result,0,"end");

	$query = "SELECT MIN(time_stamp) AS start FROM `".$db_select_name."`";
    $result = mysql_query($query) or die("Cannot execute query \"$query\" !!! ".mysql_error());
	$start = mysql_result($result,0,"start");

	if($start!=0 && $end!=0)
	{
		$year_start = date("Y",$start);
		$year_end = date("Y",$end);

		for($year=$year_start;$year<=$year_end;$year++)
		{
			for($month=1;$month<=12;$month++)
			{
				$dump_file_name = "/var/www/sites/dtc/".$domain."/subdomains/".$vhost."/logs/".$db_select_name."_".$month."_".$year;
				if(!file_exists($dump_file_name.".tar.gz")
				)
				// && $month!=$current_month && $year!=$current_year
				{
					$selected_month_start = mktime(0,0,0,$month,1,$year);
					$selected_month_end = (mktime(0,0,0,($month+1),1,$year))-1;
					$query_dump = "SELECT * FROM `".$db_select_name."` WHERE time_stamp>=".$selected_month_start." AND time_stamp<=".$selected_month_end;
        			$result_dump = mysql_query($query_dump) or die("Cannot execute query \"$query_dump\" !!! ".mysql_error());
					if(mysql_num_rows($result_dump)>0)
					{
						$handle = fopen ($dump_file_name, "w");
						for($z=0;$z<mysql_num_rows($result_dump);$z++)
						{
							$content = "'".mysql_result($result_dump,$z,"id")."','".mysql_result($result_dump,$z,"agent")."','".mysql_result($result_dump,$z,"bytes_sent")."','".mysql_result($result_dump,$z,"child_pid")."','".mysql_result($result_dump,$z,"cookie")."','".mysql_result($result_dump,$z,"request_file")."','".mysql_result($result_dump,$z,"referer")."','".mysql_result($result_dump,$z,"remote_host")."','".mysql_result($result_dump,$z,"remote_logname")."','".mysql_result($result_dump,$z,"remote_user")."','".mysql_result($result_dump,$z,"request_duration")."','".mysql_result($result_dump,$z,"request_line")."','".mysql_result($result_dump,$z,"request_method")."','".mysql_result($result_dump,$z,"request_protocol")."','".mysql_result($result_dump,$z,"request_time")."','".mysql_result($result_dump,$z,"request_uri")."','".mysql_result($result_dump,$z,"request_args")."','".mysql_result($result_dump,$z,"request_args")."','".mysql_result($result_dump,$z,"ssl_cipher")."','".mysql_result($result_dump,$z,"ssl_keysize")."','".mysql_result($result_dump,$z,"ssl_maxkeysize")."','".mysql_result($result_dump,$z,"status")."','".mysql_result($result_dump,$z,"time_stamp")."','".mysql_result($result_dump,$z,"virtual_host")."'\n";
							if (!fwrite($handle, $content))
							{
        						print "Cannot write to file ($filename)";
						    }
						}
						fclose($handle);
						exec ("tar cfj ".$dump_file_name.".tar.bz2 " .$dump_file_name."");
						//echo "tar cfj ".$dump_file_name.".tar.bz2 " .$dump_file_name;
						//echo "tar cfj ".$dump_file_name." ".$dump_file_name.".tar.bz2 ";
						unlink($dump_file_name);
						//echo "creating dump for ".$db_select_name."_".$month."_".$year. "<br>";
					}
				}
			}
		}
	}



}

function sum_ftp($webname)
{
	global $pro_mysql_ftp_table;
	global $conf_mysql_db;
	global $pro_mysql_acc_ftp_table;
	mysql_select_db($conf_mysql_db);
	$last_run = "SELECT MAX(last_run) AS last FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$webname."'";
	$result_l = mysql_query($last_run) or die("Cannot execute query \"$last_run\"");
 	$last_run_st = mysql_result($result_l,0,"last");
	if($last_run_st==NULL || $last_run_st==0)
	{
		echo "Create first entry for ".$webname."<br />";
		$query_insert = "INSERT INTO $pro_mysql_acc_ftp_table (sub_domain,last_run,month,year) VALUES ('".$webname."','".$last_run_st."', ".date("m",time()).",".date("Y",time()).")";
		mysql_query($query_insert)or die("Cannot execute query \"$query_insert\"");
		$last_run_st=0;
	}
		elseif(strcmp(date("m.Y",$last_run_st),date("m.Y",time())))
	{
		echo "Create first entry for this month for ".$webname."<br />";
		$query_insert = "INSERT INTO $pro_mysql_acc_ftp_table (sub_domain,last_run,month,year) VALUES ('".$webname."','".time()."',".date("m",time()).",".date("Y",time()).")";
		mysql_query($query_insert)or die("Cannot execute query \"$query_insert\"");
	}
	$query = "SELECT SUM(dl_bytes) AS dl_amount, SUM(ul_bytes) AS ul_amount FROM $pro_mysql_ftp_table WHERE hostname='".$webname."'";
    mysql_select_db("dtc");
    $result = mysql_query($query) or die("Cannot execute query \"$query\"");
    $num_rows = mysql_num_rows($result);
    $total_ftp_amount = mysql_result($result,0,"dl_amount") + mysql_result($result,0,"ul_amount");

	$query = "UPDATE $pro_mysql_ftp_table set ul_bytes=0, dl_bytes=0 WHERE hostname='".$webname."'";
	mysql_query($query) or die("Cannot execute query \"$query\"".mysql_error());
	
	
	$query_insert = "UPDATE $pro_mysql_acc_ftp_table set transfer=transfer+".$total_ftp_amount."+0, last_run=".time()." WHERE last_run=".$last_run_st;
	mysql_query($query_insert) or die("Cannot execute query \"$query_insert\"");
}
?>
