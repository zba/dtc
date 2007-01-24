<?php

// This script is launched before restarting apache
// to check if a bastard has deleted his directories
// with ftp: apache would refuse to start otherwise.
$chk_dir_script="#!/bin/sh

echo \"Checking vhosts directories existance...\"\n";
function vhost_chk_dir_sh($dir){
	global $conf_dtc_system_username;
	global $conf_dtc_system_groupname;

	global $chk_dir_script;

/*	$chk_dir_script .= "
if [ ! -d $dir ] ; then
	mkdir -p $dir
	nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nobody`
	# if we can't find the nobody group, try nogroup
	if [ -z \"\"\$nobodygroup ]; then
		nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nogroup`
	fi
	# if we can't find nogroup, then set to 65534
	if [ -z \"\"\$nobodygroup ]; then
		nobodygroup=65534
	fi
	chown nobody:\$nobodygroup $dir
	echo \"Directory $dir was missing and has been created.\"
fi
";
*/
	$chk_dir_script .= "
if [ ! -d $dir ] ; then
	mkdir -p $dir
	chown $conf_dtc_system_username:$conf_dtc_system_groupname
	echo \"Directory $dir was missing and has been created.\"
fi
";
}

function checkCertificate($cert_path,$common_name){
	global $conf_dtc_system_username;
	global $conf_dtc_system_groupname;
	global $conf_dtcadmin_path;

	if(!is_dir($cert_path)){
		mkdir($cert_path);
	}
	if(		   !file_exists("$cert_path/$common_name".".cert.csr")
			&& !file_exists("$cert_path/privkey.pem")
			&& !file_exists("$cert_path/$common_name".".cert.key")
			&& !file_exists("$cert_path/$common_name".".cert.cert")){
		$cmd = "$conf_dtcadmin_path/genfiles/gen_customer_ssl_cert.sh $cert_path $common_name";
		$return_string = exec ($cmd, $output, $return_var);
	}
	if(		   file_exists("$cert_path/$common_name".".cert.csr")
			&& file_exists("$cert_path/privkey.pem")
			&& file_exists("$cert_path/$common_name".".cert.key")
			&& file_exists("$cert_path/$common_name".".cert.cert")){
		return "yes";
	}else{
		return "no";
	}
}

function test_valid_local_ip($address){
	global $console;
	global $panel_type;
	$port = 80;
	
        if (!function_exists('socket_create')) {
		if($panel_type=="cronjob"){
			echo("The socket_create function does not exist or is not enabled, please ensure you have a php_sockets.so or php_sockets.dll, or have the sockets compiled into PHP.  No IP checks can be done, so assuming all IPs configured are valid.\n");
		}
		return true;
        }

	// turn off error reporting for this function
	$console .= "Checking IP $address:";
	$old_error_reporting = error_reporting('E_NONE');

	if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
		echo "socket_create() failed: reason: " . socket_strerror($sock) . "\n";
		return false;
	}

	if (!($ret = socket_bind($sock, $address, $port))) {
		$error = socket_last_error();
		if ($error == 98){
			//echo "Address already in use!\n";
			$console .= " already in use -> success\n";
			return true;
		}
		else if ($error == 99)
		{
			//echo "IP not on server...\n";
			$console .= " IP not on server -> failed\n";
			return false;
		}
		else if ($error == 13)
		{
			if($panel_type=="admin"){
				$console .= " permission denied -> assuming succes\n";
				return true;
			}
			//echo "Permission denied...\n";
			$console .= " permission denied -> failed\n";
			return false;
		} else {
			//echo "$error\n";
			echo "socket_bind()[$address] failed: reason: " . socket_strerror($error) . "\n";
			$console .= " error ". socket_strerror($error) . " -> failed\n";
			return false;
		}
	} else {
		// bound ok! (nothing listening on this yet)
		return true;
	}

	// turn it back on to what it was
	error_reporting($old_error_reporting);
}

function pro_vhost_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $conf_db_version;
	global $conf_unix_type;

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

	global $conf_shared_renewal_shutdown;

	global $conf_use_nated_vhost;
	global $conf_nated_vhost_ip;

	global $console;
	global $chk_dir_script;

	global $conf_main_domain;
	global $conf_404_subdomain;

	$vhost_file = "";
	$vhost_file_listen = "";

	if($conf_unix_type == "gentoo"){
		$conf_tools_prefix = "/var/www/localhost/htdocs";
	}else if($conf_unix_type == "bsd"){
		$conf_tools_prefix = "/usr/local/www";
	}else{
		$conf_tools_prefix = "/usr/share";
	}

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
			// first write all config'ed IPs with the Listen
			if (test_valid_local_ip($all_site_addrs[$i]) && !ereg("Listen ".$all_site_addrs[$i].":80", $vhost_file_listen))
			{
				$vhost_file_listen .= "Listen ".$all_site_addrs[$i].":80\n";
			} else {
				$vhost_file_listen .= "#Listen ".$all_site_addrs[$i].":80\n";
			}
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
	LogSQLTransferLogTable ".str_replace("-","A",str_replace(".","_",$conf_main_domain)).'$'.$conf_404_subdomain.'$'."xfer
	LogSQLScoreDomain $conf_main_domain
	LogSQLScoreSubdomain $conf_404_subdomain
	LogSQLScoreTable dtc.http_accounting
	DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>\n";
				}
			}
		}
	}else{
		$ip_for_404=$conf_main_site_ip;
		if($conf_use_nated_vhost=="yes"){
			$ip_for_404 = $conf_nated_vhost_ip;
			if (test_valid_local_ip($conf_nated_vhost_ip) && !ereg("Listen ".$conf_nated_vhost_ip.":80", $vhost_file_listen))
			{
				$vhost_file_listen .= "Listen ".$conf_nated_vhost_ip.":80\n";
			} else {
				$vhost_file_listen .= "#Listen ".$conf_nated_vhost_ip.":80\n";
			}
			$vhost_file .= "NameVirtualHost ".$conf_nated_vhost_ip.":80\n";
		}else{
			if (test_valid_local_ip($conf_main_site_ip) && !ereg("Listen ".$conf_main_site_ip.":80", $vhost_file_listen))
			{
				$vhost_file_listen .= "Listen ".$conf_main_site_ip.":80\n";
			} else {
				$vhost_file_listen .= "#Listen ".$conf_main_site_ip.":80\n";
			}
			$vhost_file .= "NameVirtualHost ".$conf_main_site_ip.":80\n";
		}
		if ($enable404feature == true)
		{
			$vhost_file .= "<VirtualHost ".$ip_for_404.":80>
        ServerName $conf_404_subdomain.$conf_main_domain
        DocumentRoot $path_404/html
        ScriptAlias /cgi-bin $path_404/cgi-bin
        ErrorLog $path_404/logs/error.log
        LogSQLTransferLogTable ".str_replace("-","A",str_replace(".","_",$conf_main_domain)).'$'.$conf_404_subdomain.'$'."xfer
        LogSQLScoreDomain $conf_main_domain
        LogSQLScoreSubdomain $conf_404_subdomain
        LogSQLScoreTable dtc.http_accounting
        DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>\n";
		}
	}

	$vhost_file .= "<Directory $conf_dtcadmin_path>
	Options FollowSymLinks
</Directory>
<Directory $conf_dtcclient_path>
	Options FollowSymLinks
</Directory>
<Directory $conf_dtcemail_path>
	Options FollowSymLinks
</Directory>\n";
	if($conf_unix_type == "debian"){
		$vhost_file .= "ScriptAlias /cgi-bin /usr/lib/cgi-bin
<Directory /usr/lib/cgi-bin>
	Options FollowSymLinks
</Directory>\n";
	}

	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user");
		$web_name = $row["name"];
		$web_owner = $row["owner"];
		$ip_addr = $row["ip_addr"];
		$domain_safe_mode = $row["safe_mode"];
		$domain_sbox_protect = $row["sbox_protect"];
		$domain_parking = $row["domain_parking"];
		unset($backup_ip_addr);
		if (isset($row["backup_ip_addr"])){
			$backup_ip_addr = $row["backup_ip_addr"];
		}
		if (isset($backup_ip_addr) && ($backup_ip_addr == "NULL" || trim($backup_ip_addr) == "")){
			unset($backup_ip_addr);
		} 
		// need to check if we have a NameVirtualHost entry for this backup IP, to support multiple backup sites on one IP
		if (isset($backup_ip_addr)){
			if (test_valid_local_ip($backup_ip_addr) && !ereg("Listen ".$backup_ip_addr.":80", $vhost_file_listen)){
				$vhost_file_listen .= "Listen ".$backup_ip_addr.":80\n";
			} else {
				$vhost_file_listen .= "#Listen ".$backup_ip_addr.":80\n";
			}
			if (!ereg("NameVirtualHost $backup_ip_addr", $vhost_file)){
				$vhost_file .= "NameVirtualHost ".$backup_ip_addr.":80\n";
			}
		}
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
		$expire_stored = $webadmin["expire"];
		if($expire_stored == "0000-00-00"){
			$site_expired = "no";
		}else{
			$calc_expire_date = calculateExpirationDate($expire_stored,"0000-00-$conf_shared_renewal_shutdown");
			$calc_expire_date_array = explode("-",$calc_expire_date);
			$expire_timestamp = mktime(1,1,1,$calc_expire_date_array[1],$calc_expire_date_array[2],$calc_expire_date_array[0]);
			if($expire_timestamp < mktime()){
				$site_expired = "yes";
			}else{
				$site_expired = "no";
			}
		}

		if($domain_parking != "no-parking" && $web_name != $conf_main_domain){
			$domain_to_get = $domain_parking;
		}else{
			$domain_to_get = $web_name;
		}

		// Grab all subdomains
		if($web_name == $conf_main_domain){
			$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND ip='default' AND subdomain_name!='$conf_404_subdomain' ORDER BY subdomain_name;";
		}else{
			$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$domain_to_get' AND ip='default' ORDER BY subdomain_name;";
		}
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

//			$console .= "Working on $web_subname.$web_name\n";

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
				$gen_iterations++;
			}

			// if we want to generate a backup IP (transitional)
			// need to loop through this one
			if (isset($backup_ip_addr))
			{
				$gen_iterations++;
			}
			for ($k = 0; $k < $gen_iterations; $k++){
				$log_tablename = str_replace("-","A",str_replace(".","_",$web_name)).'$'.str_replace("-","A",str_replace(".","_",$web_subname));
				if($conf_use_ssl == "yes" && $k == 0){
					# add the directive for SSL here
					if (test_valid_local_ip($ip_to_write) && !ereg("Listen ".$ip_to_write.":443", $vhost_file_listen))
					{
						$vhost_file_listen .= "Listen ".$ip_to_write.":443\n";
					} else {
						$vhost_file_listen .= "#Listen ".$ip_to_write.":443\n";
					}
					$vhost_file .= "<VirtualHost ".$ip_to_write.":443>\n";
				} else if ($k == 1 && isset($backup_ip_addr) || ($conf_use_ssl != "yes" && $k == 0 && isset($backup_ip_addr))) {
					$vhost_file .= "<VirtualHost ".$backup_ip_addr.":80>\n";
				}else {
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
					$backup_ip_addrX = $rowX["backup_ip_addr"];
					$alias_user_query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$web_ownerX';";
					$alias_user_result = mysql_query($alias_user_query) or die("Cannot fetch user for Alias");
					$num_rows_alias_user = mysql_num_rows($alias_user_result);
					if ($num_rows_alias_user != 1) {
						die("No user of that name!");
					}
					$alias_path = mysql_fetch_array($alias_user_result) or die ("Cannot fetch user");
					$web_pathX = $alias_path["path"];
					// TG: Added open_basedir restriction (for obvious security reasons)
					$qsubdom = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_nameX' AND ip='default';";
					$rx = mysql_query ($qsubdom)or die("Cannot execute query \"$qsubdom\" line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
					$numx =  mysql_num_rows($rx);
					for($subx=0;$subx<$numx;$subx++){
						$ax = mysql_fetch_array($rx) or die ("Cannot fetch subdomain for Alias");
						$subdomx = $ax["subdomain_name"];
						$globalx = $ax["register_globals"];
						if($globalx == "yes"){
							$gblx = "php_admin_value register_globals 1";
						}else{
							$gblx = "php_admin_value register_globals 0";
						}
						if($rowX["safe_mode"] == "no" && $ax["safe_mode"] == "no"){
							$safex = "php_admin_value safe_mode 1";
						}else{
							$safex = "php_admin_value safe_mode 0";
						}
						$vhost_file .= "\tAlias /$subdomx.$web_nameX $web_pathX/$web_nameX/subdomains/$subdomx/html
	<Location /$subdomx.$web_nameX>
		$safex
		php_admin_value open_basedir \"$web_pathX/$web_nameX/:$conf_php_library_path:$conf_php_additional_library_path:\"
		$gblx
	</Location>\n";
					}
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
				$vhost_file .= "	Alias /phpmyadmin ".$conf_tools_prefix."/phpmyadmin
	Alias /dtc $conf_dtcclient_path
	Alias /dtcdoc $conf_dtcdoc_path/html/en
	Alias /dtcemail $conf_dtcemail_path
	Alias /dtcadmin $conf_dtcadmin_path
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs
	Alias /squirrelmail ".$conf_tools_prefix."/squirrelmail
	php_admin_value sendmail_from webmaster@$web_name
	DocumentRoot $web_path/$web_name/subdomains/$web_subname/html
# No ScriptAlias: we want to use system's /usr/lib/cgi-bin !!!
#	ScriptAlias /cgi-bin $web_path/$web_name/subdomains/$web_subname/cgi-bin
	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename\$xfer
	LogSQLScoreDomain $web_name
	LogSQLScoreSubdomain $web_subname
	LogSQLScoreTable dtc.http_accounting
	DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>

";
} // - end of for loop

// ---------------------------------------------------
// --- Start of the conf of server users subdomain ---
// ---------------------------------------------------
			} else {
				// Generate a permanet redirect for all subdomains of target if using a domain parking
				if($domain_parking != "no-parking"){
					$console .= "Making domain parking for $web_subname.$web_name\n";
					$vhost_file .= "<VirtualHost ".$ip_to_write.":80>
	ServerName $web_subname.$web_name
	Redirect permanent / http://$web_subname.$domain_parking/
</VirtualHost>\n\n";
				}else{
					vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/logs");
					vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/html");
					vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/cgi-bin");
					$iteration_table = array();
					$iteration_table[] = "normal";
					$ssl_cert_folder_path = "$web_path/$web_name/subdomains/$web_subname/ssl";
					if($subdomain["ssl_ip"] != none){
						$ssl_returns = checkCertificate($ssl_cert_folder_path,$web_subname.".".$web_name);
						if($ssl_returns == "yes"){
							$iteration_table[] = "ssl";
						}
					}

					// if we want to generate a backup IP (transitional)
					// need to loop through this one
					if (isset($backup_ip_addr)){
						$iteration_table[] = "backup";
					}

					$log_tablename = str_replace("-","A",str_replace(".","_",$web_name)).'$'.str_replace("-","A",str_replace(".","_",$web_subname));
					$vhost_more_conf = "";
					if($subdomain["register_globals"] == "yes"){
						$vhost_more_conf .= "	php_admin_value register_globals 1\n";
					}
					if($web_subname == "$web_default_subdomain"){
						$vhost_more_conf .= "	ServerAlias $web_name\n";
					}

					// Sbox and safe mode protection values
					if($domain_safe_mode == "no" && $subdomain["safe_mode"] == "no"){
						$safe_mode_value = "0";
					}else{
						$safe_mode_value = "1";
					}
					if($domain_sbox_protect == "no" && $subdomain["sbox_protect"] == "no"){
						$cgi_directive = "ScriptAlias /cgi-bin $web_path/$web_name/subdomains/$web_subname/cgi-bin";
					}else{
						$cgi_directive = "RewriteEngine on
	RewriteRule ^/cgi-bin/(.*) /cgi-bin/sbox/$1 [PT]";
					}
					$gen_iterations = sizeof($iteration_table);
					for ($k = 0; $k < $gen_iterations; $k++){
						switch($iteration_table[$k]){
						case "backup":
							$vhost_file .= "<VirtualHost ".$backup_ip_addr.":80>\n";
							break;
						case "normal":
							$vhost_file .= "<VirtualHost ".$ip_to_write.":80>\n";
							break;
						case "ssl":
							$vhost_file .= "Listen ".$subdomain["ssl_ip"].":443\n";
							$vhost_file .= "<VirtualHost ".$subdomain["ssl_ip"].":443>\n";
							$vhost_file .= "	SSLEngine on\n";
							$vhost_file .= "	SSLCertificateFile $ssl_cert_folder_path/".$web_subname.".".$web_name.".cert.cert\n";
							$vhost_file .= "	SSLCertificateKeyFile $ssl_cert_folder_path/".$web_subname.".".$web_name.".cert.key\n";
							break;
						}
						$vhost_file .= "	ServerName $web_subname.$web_name
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs\n";
						// Disable the site if expired
						if($site_expired == "yes"){
							$document_root = $conf_generated_file_path."/expired_site/";
							$vhost_file .= "	DocumentRoot $document_root\n";
						}else{
							$document_root = "$web_path/$web_name/subdomains/$web_subname/html/";
							$vhost_file .= "	DocumentRoot $document_root
$vhost_more_conf	php_admin_value safe_mode $safe_mode_value
	php_admin_value sendmail_from webmaster@$web_name
	php_value session.save_path $web_path/$web_name/subdomains/$web_subname/tmp
	<Location />
		php_admin_value open_basedir \"$web_path:$conf_php_library_path:$conf_php_additional_library_path:\"
	</Location>
	$cgi_directive\n";
						}
						$vhost_file .= "	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename\$xfer
	LogSQLScoreDomain $web_name
	LogSQLScoreSubdomain $web_subname
	LogSQLScoreTable dtc.http_accounting
	DirectoryIndex index.php index.cgi index.pl index.htm index.html index.php4
</VirtualHost>

";
						$num_generated_vhosts += $num_rows2;
					}
				}
			}
	        }
	}

	// Ecriture du fichier
	$filep = fopen("$conf_generated_file_path/$conf_apache_vhost_path", "w+");
	if( $filep == NULL){
		die("Cannot open $conf_generated_file_path/$conf_apache_vhost_path file for writting");
	}
	fwrite($filep,$vhost_file_listen);
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
