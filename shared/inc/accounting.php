<?
function mysql_table_exists($dbname,$tableName)
{
$tables = array();
$tablesResult = mysql_list_tables($dbname);
while ($row = mysql_fetch_row($tablesResult)) $tables[] = $row[0];
return(in_array($tableName, $tables));
}

function fetchHTTPInfo($webname)
{
		global $pro_mysql_acc_http_table;
        $query = "SELECT SUM(bytes_sent) AS transfer FROM $pro_mysql_acc_http_table WHERE domain='".$webname."'";
        $result = mysql_query($query)or die("Cannot execute query \"$query\"");
        $num_rows = mysql_num_rows($result);
       	$amount = mysql_result($result,0,"transfer");
	   
		if($amount>1073741824)
			$amount = round(($amount / 1073741824),3) ."Gbyte";
		if($amount>1048567)
			$amount = round(($amount / 1048567),3) ." Mbytes";
		if($amount>1024)
			$amount = round(($amount / 1024),3) ." kbytes";
		return($amount);
}
                                                                                                                                                    
function fetchFTPInfo($webname)
{
        global $pro_mysql_acc_ftp_table;
        $query = "SELECT SUM(transfer) AS transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$webname."'";
        $result = mysql_query($query) or die("Cannot execute query \"$query\"");
        $total_ftp_amount = mysql_result($result,0,"transfer");

		if($total_ftp_amount>1073741824)
			$total_ftp_amount = round(($total_ftp_amount / 1073741824),3) ."Gbyte";
		if($total_ftp_amount>1048567)
			$total_ftp_amount = round(($total_ftp_amount / 1048567),3) ." Mbytes";
		if($total_ftp_amount>1024)
			$total_ftp_amount = round(($total_ftp_amount / 1024),3) ." kbytes";
        return($total_ftp_amount);
}

error_reporting(0);

function sum_http($webname)
{
	global $pro_mysql_subdomain_table;
	global $conf_mysql_db;
	global $pro_mysql_acc_http_table;
	$query = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='".$webname."' AND ip='default'";
    $result = mysql_query($query)or die("Cannot execute query \"$query\"");
    $num_rows = mysql_num_rows($result);
    $db_webname = trim(strtr($webname,".","_"));

	for($i=0;$i<$num_rows;$i++)
    {
       $subdomain_name = mysql_result($result,$i,"subdomain_name");
       $db_select_name = "access_".$subdomain_name."_".$db_webname;
	   $last_run = "SELECT MAX(last_run) AS last FROM $pro_mysql_acc_http_table WHERE vhost='".$subdomain_name."'";
	   $result_l = mysql_query($last_run) or die("Cannot execute query \"$last_run\"");
	   for($l=0;$l<mysql_num_rows($result_l);$l++)
	   {
	   		$last_run_st = mysql_result($result_l,0,"last");
			if($last_run_st==NULL || $last_run_st==0)
	   		{
				echo "Create first entry for ".$subdomain_name."<br />";
				$query_insert = "INSERT INTO $pro_mysql_acc_http_table (vhost,domain,last_run,month,year) VALUES ('".$subdomain_name."','".$webname."','".$last_run_st."', ".date("m",time()).",".date("Y",time()).")";
				mysql_query($query_insert)or die("Cannot execute query \"$query_insert\"");
				$last_run_st=0;
	   		}
			elseif(strcmp(date("m.Y",$last_run_st),date("m.Y",time())))
			{
				echo "Create first entry for this month for ".$subdomain_name."<br />";
				$query_insert = "INSERT INTO $pro_mysql_acc_http_table (vhost,domain,last_run,month,year) VALUES ('".$subdomain_name."','".$webname."','".time()."',".date("m",time()).",".date("Y",time()).")";
				mysql_query($query_insert)or die("Cannot execute query \"$query_insert\"");
			}
				if(mysql_table_exists("apachelogs",$db_select_name))
				{
					// Get bytes_sent
					$q_bytes = "SELECT  SUM( bytes_sent ) AS amount FROM `".$db_select_name."` WHERE time_stamp>=".$last_run_st;
					mysql_select_db("apachelogs");
                	$r_bytes = mysql_query($q_bytes) or die("Cannot execute query \"$q_visits\" !!! ".mysql_error());
				    $bytes_sent = mysql_result($r_bytes,0,"amount");

					// Get bytes_sent
					$q_visits = "SELECT DISTINCT id FROM `".$db_select_name."` WHERE time_stamp>=".$last_run_st;
					mysql_select_db("apachelogs");
                	$r_visits = mysql_query($q_visits) or die("Cannot execute query \"$q_visits\" !!! ".mysql_error());
				    $visits = mysql_num_rows($r_visits);

					// Get bytes_sent
					$q_hosts = "SELECT remote_host FROM `".$db_select_name."` WHERE time_stamp>=".$last_run_st." GROUP BY remote_host";
					mysql_select_db("apachelogs");
                	$r_hosts = mysql_query($q_hosts) or die("Cannot execute query \"$q_hosts\" !!! ".mysql_error());
				    $hosts = mysql_num_rows($r_hosts);

					// Get impressions
					$q_imp = "SELECT id FROM `".$db_select_name."` WHERE time_stamp>=".$last_run_st;
					mysql_select_db("apachelogs");
                	$r_imp = mysql_query($q_imp) or die("Cannot execute query \"$q_imp\" !!! ".mysql_error());
				    $imp = mysql_num_rows($r_imp);

					mysql_select_db($conf_mysql_db);

					$query_insert_bytes = "UPDATE $pro_mysql_acc_http_table set
					bytes_sent=bytes_sent+".$bytes_sent."+0,
					count_visits=count_visits+".$visits."+0,
					count_hosts=count_hosts+".$hosts."+0,
					count_impressions=count_impressions+".$imp."+0,
					last_run=".time()." WHERE vhost='".$subdomain_name."' AND last_run=".$last_run_st;
					mysql_query($query_insert_bytes)or die("Cannot execute query \"$query_insert_bytes\"".mysql_error());
				}
			}
	}

}

function sum_ftp($webname)
{
	global $pro_mysql_ftp_table;
	global $conf_mysql_db;
	global $pro_mysql_acc_ftp_table;

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

sum_http("hl-s.de");
sum_ftp("hl-s.de");
?>
