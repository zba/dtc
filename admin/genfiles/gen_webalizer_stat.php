<?php

require("genfiles/gen_awstats.php");

function stat_script_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $conf_generated_file_path;
	global $conf_webalizer_stats_script_path;

	global $console;

	$num_generated_vhosts=0;
	
	// Initialy delete last week backup
	$stat_script = "#!/bin/bash
#
# This is \"Domain Technologies Control\"'s webalizer stat script
# to be installed in crontab. Do not edit : use web interface
# to generate it !!! :)

# Now create all webalizer stats
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
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND webalizer_generate='yes' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);

		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];

			// Variable to use : $web_name $web_owner $web_subname
			$stat_script .= "
cd $web_path/$web_name/subdomains/$web_subname/logs;
webalizer -o ./ -n $web_subname.$web_name -D $web_subname.$web_name.dnscache -N 4 access.log;
";
		}
		$num_generated_vhosts += $num_rows2;
	}

	// Ecriture du fichier
	$filep = fopen("$conf_generated_file_path/$conf_webalizer_stats_script_path", "w+");
	if( $filep == NULL){
		die("Cannot open file for writting");
	}
	fwrite($filep,$stat_script);
	fclose($filep);
	chmod("$conf_generated_file_path/$conf_webalizer_stats_script_path",0750);
	$console .= "Generated statistic script files for $num_generated_vhosts vhosts !<br>";
	// put a hack in here for now for awstats
	awstat_script_generate();
	// ok, awstats done
	return true;
}

?>
