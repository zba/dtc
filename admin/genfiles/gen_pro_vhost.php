<?php

// This script is launched before restarting apache
// to check if a bastard has deleted his directories
// with ftp: apache would refuse to start otherwise.
$chk_dir_script="#!/bin/sh\n";
function vhost_chk_dir_sh($dir){
	global $chk_dir_script;
	$chk_dir_script .= "
if [ ! -d $dir ] ; then
	mkdir -p $dir
	chown nobody:65534 $dir
	echo \"Directory $dir was missing and has been created.\"
fi
";
}

function pro_vhost_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $conf_db_version;

	global $conf_apache_vhost_path;
	global $conf_generated_file_path;
	global $conf_dtcshared_path;
	global $conf_dtcadmin_path;
	global $conf_dtcclient_path;
	global $conf_dtcdoc_path;
	global $conf_dtcemail_path;
	global $conf_main_site_ip;
	global $conf_use_multiple_ip;
	global $conf_site_addrs;
	global $conf_php_library_path;
	global $conf_php_additional_library_path;
	global $conf_administrative_site;
	global $conf_use_ssl;

	global $conf_use_nated_vhost;
	global $conf_nated_vhost_ip;

	global $console;
	global $chk_dir_script;

	global $conf_main_domain;
	global $conf_404_subdomain;

	$vhost_file = "";
	$chk_dir_script = "#!/bin/sh\n";

	// DB version check
	if(($conf_db_version < 10000 || !isset($conf_db_version)) ||
		!isset($conf_use_ssl) || !isset($conf_use_nated_vhost)){
		$vhost_file .= "# WARNING !!! DATABASE SCHEMA IS COMMING FROM AN HOLD DTC VERSION : PLEASE UPGRADE YOUR TABLES TO NEW VERSION !!!\n";
	}

	$num_generated_vhosts=0;
	$query = "SELECT * FROM $pro_mysql_domain_table WHERE 1 ORDER BY name;";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){
		die("No account to generate : database has to contain AT LEAST one domain name");
	}

	$query2 = "SELECT $pro_mysql_admin_table.path
FROM $pro_mysql_domain_table,$pro_mysql_admin_table
WHERE $pro_mysql_domain_table.name='$conf_main_domain'
AND $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner;";
	$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"!");
	$enable404feature = true;
	//echo "Query $query2 resulted in ".mysql_num_rows($result2)."\n";
	if(mysql_num_rows($result2) != 1){
		$enable404feature=false;
	}
	//don't die here... 	we will try and do things to work around this bug
	//die("Cannot find main domain admin path!!!");

	if ($enable404feature == true)
	{
		$a = mysql_fetch_array($result2);
		$path_404 = $a["path"]."/$conf_main_domain/subdomains/$conf_404_subdomain";
	}

	if($conf_use_multiple_ip == "yes" && $conf_use_nated_vhost == "no"){
		$all_site_addrs = explode("|",$conf_site_addrs);
		$nbr_addrs = sizeof($all_site_addrs);
		for($i=0;$i<$nbr_addrs;$i++){
			$query2 = "SELECT * FROM $pro_mysql_domain_table WHERE ip_addr='".$all_site_addrs[$i]."' LIMIT 1;";
			$result2 = mysql_query ($query2)or die("Cannot execute query \"$query\"");
			$num_rows2 = mysql_num_rows($result2);
			if($num_rows2 > 0){
				$vhost_file .= "NameVirtualHost ".$all_site_addrs[$i].":80\n";
				if ($enable404feature == true)
				{
				$vhost_file .= "<VirtualHost ".$all_site_addrs[$i].":80>
	ServerName $conf_404_subdomain.$conf_main_domain
	DocumentRoot $path_404/html
	ScriptAlias /cgi-bin $path_404/cgi-bin
	ErrorLog $path_404/logs/error.log
	LogSQLTransferLogTable ".str_replace(".","_",$conf_main_domain).'$'.$conf_404_subdomain.'$'."xfer
	DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>\n";
				}
			}
		}
	}else{
		if($conf_use_nated_vhost=="yes"){
			$vhost_file .= "NameVirtualHost ".$conf_nated_vhost_ip.":80\n";
		}else{
			$vhost_file .= "NameVirtualHost ".$conf_main_site_ip.":80\n";
		}
	}

	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user");
		$web_name = $row["name"];
		$web_owner = $row["owner"];
		$ip_addr = $row["ip_addr"];
		if($conf_use_multiple_ip == "yes"){
			$ip_to_write = $ip_addr;
		}else{
			$ip_to_write = $conf_main_site_ip;
		}
		if($conf_use_nated_vhost == "yes"){
			$ip_to_write = $conf_nated_vhost_ip;
		}
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
		if($web_name == $conf_main_domain)
			$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND ip='default' AND subdomain_name!='$conf_404_subdomain' ORDER BY subdomain_name;";
		else
			$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND ip='default' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
// This is a bad idea to die in this case
// because it actualy happen if you redirect www ip to something else.
//		if($num_rows2 < 1){
//			die("No subdomain for domain $web_name !");
//		}
		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];

			// if we explicitly don't want to generate a vhost entry for this subdomain
			if (isset($subdomain["generate_vhost"]) && $subdomain["generate_vhost"] == "no")
			{
				continue;
			}

// ------------------------------------------------
// --- Start of the conf of the panel subdomain ---
// ------------------------------------------------
			if($conf_administrative_site == "$web_subname.$web_name"){
			// generate SSL and non SSL if we have enabled SSL
			$gen_iterations = 1;
			if ($conf_use_ssl == "yes"){
				$gen_iterations = 2;
			}
			for ($k = 0; $k < $gen_iterations; $k++){
				$log_tablename = str_replace(".","_",$web_name).'$'.str_replace(".","_",$web_subname);
				if($conf_use_ssl == "yes" && $k == 0){
					$vhost_file .= "<VirtualHost ".$ip_to_write.":443>\n";
				}else{
					$vhost_file .= "<VirtualHost ".$ip_to_write.":80>\n";
				}

				// Added by Luke
				// Needed to create an Alias in httpd.conf for non-resolvable domains
				// This does http://dtc.your-domain.com/unresolved-domain.com
				// TG: added a flag to say yes/no to that alias for each domains
				$alias_domain_query = "SELECT * FROM $pro_mysql_domain_table WHERE gen_unresolved_domain_alias='yes' ORDER BY name;";
				$result_alias = mysql_query ($alias_domain_query)or die("Cannot execute query \"$query\" line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
				$num_rows_alias = mysql_num_rows($result_alias);
				for($x=0;$x<$num_rows_alias;$x++) {
					$rowX = mysql_fetch_array($result_alias) or die ("Cannot fetch domain for Alias");
					$web_nameX = $rowX["name"];
					$web_ownerX = $rowX["owner"];
					$ip_addrX = $rowX["ip_addr"];
					$alias_user_query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$web_ownerX';";
					$alias_user_result = mysql_query($alias_user_query) or die("Cannot fetch user for Alias");
					$num_rows_alias_user = mysql_num_rows($alias_user_result);
					if ($num_rows_alias_user != 1) {
						die("No user of that name!");
					}
					$alias_path = mysql_fetch_array($alias_user_result) or die ("Cannot fetch user");
					$web_pathX = $alias_path["path"];
					// TG: Added open_basedir restriction (for obvious security reasons)
					$vhost_file .= "\tAlias /$web_nameX $web_pathX/$web_nameX/subdomains/www/html
	<Location /$web_nameX>
		php_admin_value open_basedir \"$web_pathX/$web_nameX/:$conf_php_library_path:$conf_php_additional_library_path:\"
	</Location>\n";
				}
				// End of Luke's patch

				$vhost_file .= "	ServerName $web_subname.$web_name\n";
				if($conf_use_ssl == "yes" && $k == 0){
					$vhost_file .= "	SSLEngine on
	SSLCertificateFile ".$conf_generated_file_path."/ssl/new.cert.cert
	SSLCertificateKeyFile ".$conf_generated_file_path."/ssl/new.cert.key\n";
				}
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/html");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/logs");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/cgi-bin");
				$vhost_file .= "	Alias /phpmyadmin /usr/share/phpmyadmin
	Alias /dtc $conf_dtcclient_path
	Alias /dtcdoc $conf_dtcdoc_path/html/en
	Alias /dtcemail $conf_dtcemail_path
	Alias /dtcadmin $conf_dtcadmin_path
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs
	Alias /squirrelmail /usr/share/squirrelmail
	php_admin_value sendmail_from webmaster@$web_name
	DocumentRoot $web_path/$web_name/subdomains/$web_subname/html
# This is old fashion no protection CGI
	ScriptAlias /cgi-bin $web_path/$web_name/subdomains/$web_subname/cgi-bin
# This is new style using SBOX engine
#	RewriteEngine on
#	RewriteRule ^/cgi-bin/(.*) /cgi-bin/sbox/$1 [PT]
	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename\$xfer
	DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>

";
} // - end of for loop

// ---------------------------------------------------
// --- Start of the conf of server users subdomain ---
// ---------------------------------------------------
			} else {
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/logs");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/html");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/cgi-bin");
				$log_tablename = str_replace(".","_",$web_name).'$'.str_replace(".","_",$web_subname);
				$vhost_more_conf = "";
				if($subdomain["register_globals"] == "yes"){
					$vhost_more_conf .= "	php_admin_value register_globals 1\n";
				}
				if($web_subname == "$web_default_subdomain"){
					$vhost_more_conf .= "	ServerAlias $web_name\n";
				}
				$vhost_file .= "<VirtualHost $ip_to_write:80>
	ServerName $web_subname.$web_name
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs
	DocumentRoot $web_path/$web_name/subdomains/$web_subname/html/
$vhost_more_conf	php_admin_value safe_mode 1
	php_admin_value sendmail_from webmaster@$web_name
	php_value session.save_path $web_path/$web_name/subdomains/$web_subname/tmp
	<Location />
		php_admin_value open_basedir \"$web_path:$conf_php_library_path:$conf_php_additional_library_path:\"
	</Location>
# This is old fashion no protection CGI
#	ScriptAlias /cgi-bin $web_path/$web_name/subdomains/$web_subname/cgi-bin
# This is new style using SBOX engine
	RewriteEngine on
	RewriteRule ^/cgi-bin/(.*) /cgi-bin/sbox/$1 [PT]

#	CustomLog $web_path/$web_name/subdomains/$web_subname/logs/access.log combined
	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename\$xfer
	DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>

";
			}
        }
		$num_generated_vhosts += $num_rows2;
	}

	// Ecriture du fichier
	$filep = fopen("$conf_generated_file_path/$conf_apache_vhost_path", "w+");
	if( $filep == NULL){
		die("Cannot open $conf_generated_file_path/$conf_apache_vhost_path file for writting");
	}
	fwrite($filep,$vhost_file);
	fclose($filep);
	$console .= "$num_generated_vhosts vhosts generated !<br>";

	$filep = fopen("$conf_generated_file_path/vhost_check_dir","w+");
	if( $filep == NULL){
		die("Cannot open $conf_generated_file_path/vhost_check_dir.sh file for writting");
	}
	fwrite($filep,$chk_dir_script);
	fclose($filep);
	$console .= "vhost_check_dir.sh script written !<br>";

	return true;
}

?>
