<?php

function awstat_script_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $conf_generated_file_path;
	global $conf_awstats_stats_script_path;

	global $console;

	if ( $conf_awstats_stats_script_path == null || $conf_awstats_stats_script_path == "")
	{
		$conf_awstats_stats_script_path = "awstats_gen.sh";
	}

	$num_generated_vhosts=0;
	
	// Initialy delete last week backup
	$stat_script = "#!/bin/bash
#
# This is \"Domain Technologies Control\"'s awstats stat script
# to be installed in crontab. Do not edit : use web interface
# to generate it !!! :)

# Now create all awstats stats
";

	// Select all domains
	$query = "SELECT * FROM $pro_mysql_domain_table WHERE 1 ORDER BY name;";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){
		die("No account to generate");
	}

	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user");
		$web_name = $row["name"];
		$web_owner = $row["owner"];
		$web_default_subdomain = $row["default_subdomain"];

		// Get the owner informations
		$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$web_owner';";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 != 1){
			die("No user of that name !");
		}
		$webadmin = mysql_fetch_array($result2) or die ("Cannot fetch user");
		$web_path = $webadmin["path"];

		// Grab all subdomains
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);

		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];
			$db_webname=strtr($web_name,'.','_');
			$db_subname=strtr($web_subname,'.','_');
			$db_select_name = $db_webname."#".$db_subname."#xfer";

			if(mysql_table_exists("apachelogs",$db_select_name)){
				generateTempAccessLog($web_name, $web_subname, $web_subname.'_'.$web_name.'.tempaccess.log');
				// Variable to use : $web_name $web_owner $web_subname
				$stat_script .= "
AWSTATS_LOG_FILE=$web_path/$web_name/subdomains/$web_subname/logs/$web_subname" . "_" . $web_name . ".tempaccess.log" . "
if [ -f $AWSTATS_LOG_FILE ]; then
	AWSTATS_FULL_DOMAIN=$web_subname.$web_name
	AWSTATS_DIR_DATA=$web_path/$web_name/subdomains/$web_subname/awstats
	export AWSTATS_LOG_FILE AWSTATS_FULL_DOMAIN AWSTATS_DIR_DATA
	if [ -f /usr/share/doc/awstats/examples/awstats_buildstaticpages.pl ]; then 
		nice 15 /usr/share/doc/awstats/examples/awstats_buildstaticpages.pl -config=dtc -update -awstatsprog=/usr/lib/cgi-bin/awstats.pl -dir=$web_path/$web_name/subdomains/$web_subname/awstats
		chown nobody:65534 $web_path/$web_name/subdomains/$web_subname/awstats
		ln -s $web_path/$web_name/subdomains/$web_subname/awstats $web_path/$web_name/subdomains/$web_subname/html/awstats
	fi
fi
";
			}
		}
		$num_generated_vhosts += $num_rows2;
	}

	// Ecriture du fichier
	$filep = fopen("$conf_generated_file_path/$conf_awstats_stats_script_path", "w+");
	if( $filep == NULL){
		die("Cannot open file for writting");
	}
	fwrite($filep,$stat_script);
	fclose($filep);
	chmod("$conf_generated_file_path/$conf_awstats_stats_script_path",0750);
	$console .= "Generated statistic script files for $num_generated_vhosts vhosts !<br>";
	return true;
}

function generateTempAccessLog($web_name, $web_subname, $output_filename)
{
	global $conf_mysql_db;
	global $pro_mysql_domain_table;

	//make sure we have our config DB selected...
	mysql_select_db($conf_mysql_db);	

	$domain = $web_name;
	$vhost = $web_subname;

	//strip the dots
	$db_webname=strtr($web_name,'.','_');
	$db_subname=strtr($web_subname,'.','_');
	$db_select_name = $db_webname."#".$db_subname."#xfer";

	//get the admin details	
	$query_admin = "SELECT * FROM $pro_mysql_domain_table WHERE name='$domain'";
	$result_admin = mysql_query($query_admin) or die("Cannot execute query \"$query_admin\" line ".__LINE__." file ".__FILE__.": ".mysql_error());
	$adm_login = mysql_result($result_admin,0,"owner");

	// get the admin path
	$admin_path = getAdminPath($adm_login);

	//select our logs DB
	mysql_select_db("apachelogs");

	$dump_path = $admin_path."/".$domain."/subdomains/".$vhost."/logs/";
	//$dump_file_name = $dump_path.$db_select_name."_".$year."_".$month;
	$dump_file_name = $dump_path.$output_filename;

	$tomorrow = mktime(0,0,0, date("m") , date("d")+1, date("Y")); 
	$yesterday = mktime(0,0,0, date("m") , date("d")-1, date("Y")); 

	$selected_time_start = $yesterday;
	$selected_time_end = $tomorrow;

	$query_dump = "SELECT * FROM `".$db_select_name."` WHERE time_stamp>=".$selected_time_start." AND time_stamp<=".$selected_time_end." ORDER BY time_stamp";
	echo "\n$query_dump\n"; 

	echo "\n Trying to get dump for $domain $vhost \n";
	$result_dump = mysql_query($query_dump) or die("Cannot execute query \"$query_dump\" file ".__FILE__." line ".__LINE__.": ".mysql_error());
	$dump_num_rows = mysql_num_rows($result_dump);
	if($dump_num_rows>0){
		echo "\nDumping logs for ".$db_select_name."\n";//_".$month."_".$year."\n";
		$handle = fopen ($dump_file_name, "w");
		for($z=0;$z<$dump_num_rows;$z++){
			$rezar = mysql_fetch_array($result_dump);
			if(strstr($rezar["referer"],$domain))   $rezar["referer"] == "self";
			$content = $rezar["remote_host"]." - - ".
			date("[d/M/Y:H:i:s O] ",$rezar["time_stamp"]).
			'"'.$rezar["request_method"]." ".$rezar["request_uri"]." ".$rezar["request_protocol"].'" '.
			$rezar["status"]." ".$rezar["bytes_sent"].
			' "'.$rezar["referer"].'" "'.$rezar["agent"].'"'."\n";

			if (!fwrite($handle, $content))
				echo "WARNING: Cannot write logfile, maybe disk full...\n";

		}
		fclose($handle);
		//echo "Calculating awstats stats from yesterday...";
		//$webalizer_cmd = "webalizer -n $vhost.$domain -o $dump_path $dump_file_name";
		//echo "$webalizer_cmd\n";
		//exec ($webalizer_cmd);
		//$tar_cmd = "bzip2 ".$dump_file_name;
		//echo $tar_cmd."\n";
		//exec ($tar_cmd);
	}
	//select back our standard DB
        mysql_select_db($conf_mysql_db);
}

?>
