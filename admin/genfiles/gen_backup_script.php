<?php

function backup_script_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $conf_generated_file_path;
	global $conf_backup_script_path;
	global $conf_bakcup_path;

	global $console;

	$num_generated_vhosts=0;
	
	// Initialy delete last week backup
	$backup_script = "#!/bin/bash
#
# This is \"Domain Technologies Control\"'s backup script
# to be installed in crontab. Do not edit : use web interface
# to generate it !!! :)
# More information about dtc : http://thomas.goirand.fr
# The best hosting service ever : http://www.anotherlight.com

cd $conf_bakcup_path;

# Preserve last backup

if [ -d lastweek ] ; then
	if [ -d today ] ; then
		rm -rf lastweek;
		mv today lastweek;
		mkdir today;
	fi
else
	if [ -d today ] ; then
		mv today lastweek;
		mkdir today;
	fi
fi

# Now create or update each subdomains inside they're owner's directory
";

	// Select all domains
	$query = "SELECT * FROM $pro_mysql_domain_table ORDER BY name;";
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
			$backup_script .= "

### $web_owner/$web_name ###
if [ ! -d today/$web_owner ] ; then
	mkdir today/$web_owner;
fi
";		
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
		if($num_rows2 < 1){
			die("No subdomain for domain $web_name !");
		}
		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];

			// Variable to use : $web_name $web_owner $web_subname
			$backup_script .= "
if [ -f today/$web_owner/$web_subname.$web_name.tar.gz ] ; then
	tar -uzf today/$web_owner/$web_subname.$web_name.tar.gz $web_path/$web_name/subdomains/$web_subname/html $web_path/$web_name/subdomains/$web_subname/cgi-bin
else
	tar -czf today/$web_owner/$web_subname.$web_name.tar.gz $web_path/$web_name/subdomains/$web_subname/html $web_path/$web_name/subdomains/$web_subname/cgi-bin
fi
";
		}
		$num_generated_vhosts += $num_rows2;
	}

	// Ecriture du fichier
	$filep = fopen("$conf_generated_file_path/$conf_backup_script_path", "w+");
	if( $filep == NULL){
		die("Cannot open file for writting");
	}
	fwrite($filep,$backup_script);
	fclose($filep);
	chmod("$conf_generated_file_path/$conf_backup_script_path",0750);
	$console .= "Generated backup files for $num_generated_vhosts vhosts !<br>";
	return true;



}

?>
