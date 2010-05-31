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

	mysql_select_db("dtc");

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
		if (strlen($web_owner) == 0)
		{
			continue;
		}
		// Get the owner informations
		$query2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$web_owner';";
		$result2 = mysql_query ($query2);
		if (!$result2) {
			echo "Failed to execute query: \"$query2\"";
			continue;
		}
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 != 1){
			echo "No user of that name $web_owner !";
			continue;
		}
		$webadmin = mysql_fetch_array($result2) or die ("Cannot fetch user");
		$web_path = $webadmin["path"];

		// Grab all subdomains
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND webalizer_generate='yes' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);

		$console .= "Found $num_rows2 domains to generate...\n";

		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];
			$db_webname=strtr($web_name,'.','_');
			$db_subname=strtr($web_subname,'.','_');
			$db_select_name = $db_webname."#".$db_subname."#xfer";
			
			$console.= "Generating for $web_subname $db_webname $db_select_name ...\n";

			// Variable to use : $web_name $web_owner $web_subname
			$stat_script .= "
AWSTATS_LOG_FILE=$web_path/$web_name/subdomains/$web_subname/logs/access.log
if [ -f \$AWSTATS_LOG_FILE ]; then
	AWSTATS_FULL_DOMAIN=$web_subname.$web_name
	AWSTATS_DIR_DATA=$web_path/$web_name/subdomains/$web_subname/awstats
	export AWSTATS_LOG_FILE AWSTATS_FULL_DOMAIN AWSTATS_DIR_DATA
	if [ -f /usr/share/doc/awstats/examples/awstats_buildstaticpages.pl ]; then 
		nice 15 /usr/share/doc/awstats/examples/awstats_buildstaticpages.pl -config=dtc -update -awstatsprog=/usr/lib/cgi-bin/awstats.pl -dir=$web_path/$web_name/subdomains/$web_subname/awstats
		nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nobody`
		# if we can't find the nobody group, try nogroup
		if [ -z \"\"\$nobodygroup ]; then
			nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nogroup`
		fi
		# if we can't find nogroup, then set to 65534
		if [ -z \"\"\$nobodygroup ]; then
			nobodygroup=65534
		fi

		chown nobody:$nobodygroup $web_path/$web_name/subdomains/$web_subname/awstats
		ln -s $web_path/$web_name/subdomains/$web_subname/awstats $web_path/$web_name/subdomains/$web_subname/html/awstats
	fi
fi
";
		}
		$num_generated_vhosts += $num_rows2;
	}

	// Ecriture du fichier
	$filep = fopen("$conf_generated_file_path/awstats_gen.sh", "w+");
	if( $filep == NULL){
		die("Cannot open file for writting");
	}
	fwrite($filep,$stat_script);
	fclose($filep);
	chmod("$conf_generated_file_path/awstats_gen.sh",0750);
	$console .= "Generated statistic script files for $num_generated_vhosts vhosts !<br>";
	return true;
}

?>
