<?
function fetchHTTPInfo($webname)
{
        global $pro_mysql_subdomain_table;
	global $conf_mysql_db;
// Was wrong. Correct way to check if apache vhost is generated is:
// ip!='default' (thomas notes)
        $query = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='".$webname."' AND subdomain_name!='default'";
        $result = mysql_query($query)or die("Cannot execute query \"$query\"");
        $num_rows = mysql_num_rows($result);
        $db_webname = trim(strtr($webname,".","_"));
        for($i=0;$i<$num_rows;$i++)
        {
                $subdomain_name = mysql_result($result,$i,"subdomain_name");
                $db_select_name = "access_".$subdomain_name."_".$db_webname;
                $q = "SELECT  SUM( bytes_sent ) AS amount FROM `".$db_select_name."` WHERE 1";
                mysql_select_db("apachelogs");
                $r_access = mysql_query($q) or die("Cannot execute query \"$q\" !!! ".mysql_error());
                $amount = $amount + mysql_result($r_access,0,"amount");
		mysql_select_db($conf_mysql_db);
        }
        
	if($amount>1073741824)
		$amount = round(($amount / 1073741824),3) ."Gbyte";
	if($amount>1048567)
		$amount = round(($amount / 1048567),3) ." Mbytes";
	if($amount>1024)
		$amount = round(($amount / 1024),3) ." kbytes";
	return($amount);
        //echo strtr($webname,".","_");
}
                                                                                                                                                    
function fetchFTPInfo($webname)
{
        global $pro_mysql_ftp_table;
        $query = "SELECT SUM(dl_bytes) AS dl_amount, SUM(ul_bytes) AS ul_amount FROM $pro_mysql_ftp_table WHERE hostname='".$webname."'";
        mysql_select_db("dtc");
        $result = mysql_query($query) or die("Cannot execute query \"$query\"");
        $num_rows = mysql_num_rows($result);
        $total_ftp_amount = mysql_result($result,0,"dl_amount") + mysql_result($result,0,"ul_amount");

	if($total_ftp_amount>1073741824)
		$total_ftp_amount = round(($total_ftp_amount / 1073741824),3) ."Gbyte";
	if($total_ftp_amount>1048567)
		$total_ftp_amount = round(($total_ftp_amount / 1048567),3) ." Mbytes";
	if($total_ftp_amount>1024)
		$total_ftp_amount = round(($total_ftp_amount / 1024),3) ." kbytes";
        return($total_ftp_amount);
}
                                                                                                                                                    
?>
