<?
 	$dbhost = "localhost";
	$dbuser = "user";
	$dbpass = "pass";
	$dbname = "database";


if (!isset($conid))
{
	function dbconnect ()
	{
		global $dbhost, $dbuser, $dbpass, $dbname;
		$mysql = mysql_connect($dbhost, $dbuser, $dbpass);
		mysql_select_db($dbname);
		return $mysql;
	}

	function dbquery ($sql)
	{
		global $conid;
		$result = mysql_query($sql,$conid)or die("Cannot query \"$query\" !!!".mysql_error());;
		echo mysql_error();
		return $result;
	}

	function dbfetch ($result)
	{
		if ($row = mysql_fetch_array($result))
			return $row;
		else
			return false;
	}

	function dbrows ($result)
	{
		$num = mysql_num_rows($result);
		return $num;
	}

	function dbfree ($result)
	{
		mysql_free_result($result);
	}

	function dbclose ($conid)
	{
		global $conid;
		mysql_close($conid);
	}

$conid = dbconnect();

}

?>
