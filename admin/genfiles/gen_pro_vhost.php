<?php

$chk_dir_script="#!/bin/sh\n";

function vhost_chk_dir_sh($dir){
	global $chk_dir_script;
	$chk_dir_script .= "
if [ ! -d $dir ] ; then
	mkdir -p $dir
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

	if($conf_use_multiple_ip == "yes" && $conf_use_nated_vhost == "no"){
		$all_site_addrs = explode("|",$conf_site_addrs);
		$nbr_addrs = sizeof($all_site_addrs);
		for($i=0;$i<$nbr_addrs;$i++){
			$query2 = "SELECT * FROM $pro_mysql_domain_table WHERE ip_addr='".$all_site_addrs[$i]."' LIMIT 1;";
			$result2 = mysql_query ($query2)or die("Cannot execute query \"$query\"");
			$num_rows2 = mysql_num_rows($result2);
			if($num_rows2 > 0){
				$vhost_file .= "NameVirtualHost ".$all_site_addrs[$i]."\n";
			}
		}
	}else{
		if($conf_use_nated_vhost=="yes"){
			$vhost_file .= "NameVirtualHost ".$conf_nated_vhost_ip."\n";
		}else{
			$vhost_file .= "NameVirtualHost ".$conf_main_site_ip."\n";
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
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND ip='default' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
		if($num_rows2 < 1){
			die("No subdomain for domain $web_name !");
		}
		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];
			if($conf_administrative_site == "$web_subname.$web_name"){
				$log_tablename = str_replace(".","_",$web_name)."#".str_replace(".","_",$web_subname);
				if($conf_use_ssl == "yes"){
					$vhost_file .= "<VirtualHost ".$ip_to_write.":443>\n";
				}else{
					$vhost_file .= "<VirtualHost ".$ip_to_write.">\n";
				}
			  	$vhost_file .= "	ServerName $web_subname.$web_name\n";
				if($conf_use_ssl == "yes"){
					$vhost_file .= "	SSLEngine on
	SSLCertificateFile /etc/apache/ssl/new.cert.cert
	SSLCertificateKeyFile /etc/apache/ssl/new.cert.key\n";
				}
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/html");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/logs");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/cgi-bin");
				$vhost_file .= "	Alias /phpmyadmin /usr/share/phpmyadmin
	Alias /dtc /usr/share/dtc/client
	Alias /dtcadmin /usr/share/dtc/admin
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs
	php_admin_value sendmail_from webmaster@$web_name
	DocumentRoot $web_path/$web_name/subdomains/$web_subname/html
	ScriptAlias /cgi-bin $web_path/$web_name/subdomains/$web_subname/cgi-bin
#	CustomLog $web_path/$web_name/subdomains/$web_subname/logs/access.log combined
	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename#xfer
	DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>

";
			} else {
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/logs");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/html");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/cgi-bin");
				$log_tablename = str_replace(".","_",$web_name)."#".str_replace(".","_",$web_subname);
				if($subdomain["register_globals"] == "yes"){
					$vhost_more_conf .= "	php_admin_value register_globals 1\n";
				}
				if($web_subname == "$web_default_subdomain"){
					$vhost_more_conf .= "	ServerAlias $web_name\n";
				}
				$vhost_file .= "<VirtualHost $ip_to_write>
	ServerName $web_subname.$web_name
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs
	DocumentRoot $web_path/$web_name/subdomains/$web_subname/html/
$vhost_more_conf	php_admin_value safe_mode 1
	php_admin_value sendmail_from webmaster@$web_name
	<Location />
		php_admin_value open_basedir \"$web_path/$web_name/:$conf_php_library_path:$conf_php_additional_library_path:\"
	</Location>
	ScriptAlias /cgi-bin $web_path/$web_name/subdomains/$web_subname/cgi-bin
#	CustomLog $web_path/$web_name/subdomains/$web_subname/logs/access.log combined
	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename#xfer
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
