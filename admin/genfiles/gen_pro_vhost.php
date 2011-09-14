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
	chown $conf_dtc_system_username:$conf_dtc_system_groupname $dir
	echo \"Directory $dir was missing and has been created.\"
fi
";
}

$chk_certs_script = "#!/bin/sh

echo \"Checking certificates validity...\"
EXIT_VAL=0\n";
function check_certs_sh($cert_path,$common_name){
	global $chk_certs_script;
	$chk_certs_script .= "if openssl x509 -in $cert_path/$common_name.cert.cert -subject ; then
	echo \"$common_name checked\"
else
	echo \"$common_name is not a valid cert: will not start apache\"
	EXIT_VAL=1
fi\n";
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
		chown("$cert_path/$common_name".".cert.csr","$conf_dtc_system_username:$conf_dtc_system_groupname");
		chown("$cert_path/$common_name".".cert.key","$conf_dtc_system_username:$conf_dtc_system_groupname");
		chown("$cert_path/$common_name".".cert.cert","$conf_dtc_system_username:$conf_dtc_system_groupname");
		chown("$cert_path/privkey.pem","$conf_dtc_system_username:$conf_dtc_system_groupname");
	}
	if(		   file_exists("$cert_path/$common_name".".cert.csr")
			&& file_exists("$cert_path/privkey.pem")
			&& file_exists("$cert_path/$common_name".".cert.key")
			&& file_exists("$cert_path/$common_name".".cert.cert")){
		check_certs_sh($cert_path,$common_name);
		return "yes";
	}else{
		return "no";
	}
}

function test_valid_local_ip($address){
	global $console;
	global $panel_type;
	global $conf_nated_vhost_ip;

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

	if($conf_nated_vhost_ip != '*'){
		if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
			echo "socket_create() failed: reason: " . socket_strerror($sock) . "\n";
			return false;
		}
	}

	if (!($ret = socket_bind($sock, $address, $port))) {
		$error = socket_last_error();
		if ($error == 98 || $error == 48){
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

function get_defaultCharsetDirective($db_entry){
	if(!isset($db_entry) || $db_entry == "dtc-wont-add" || $db_entry == ""){
		return "";
	}else{
		if($db_entry == "Off"){
			return "\tAddDefaultCharset Off\n";
		}else{
			return "\tAddDefaultCharset ".$db_entry."\n";
		}
	}
}

function pro_vhost_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_ssl_ips_table;
	global $pro_mysql_product_table;

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
	global $conf_administrative_ssl_port;
	global $conf_use_ssl;

	global $conf_shared_renewal_shutdown;

	global $conf_use_nated_vhost;
	global $conf_nated_vhost_ip;

	global $console;
	global $chk_dir_script;
	global $chk_certs_script;

	global $conf_main_domain;
	global $conf_404_subdomain;

	global $conf_mysql_db;

	global $conf_apache_version;
	global $conf_apache_directoryindex;

	global $conf_autogen_webmail_alias;
	global $conf_autogen_webmail_type;
	
	global $conf_use_shared_ssl;
	$vhost_file = "";

	$aufs_list = "";

	$logrotate_file_chk = array();
	$logrotate_file = "# Do not edit this file, it's generated
# edit /etc/dtc/logrotate.template instead!
";

$vhost_file_start = "# WARNING ! This file is automatically edited by the dtc cron
# daemon: do not edit. All manual changes to hosts that are configured within
# the dtc panel will be removed with the next cron job. It's the same for all
# files in this folder exept the ssl, the 404 and the template folder.
#
# If you feel an option is missing, feel free to edit the script that generate
# this file in dtc/admin/genfiles/gen_pro_vhosts.php. Best is to send us your
# patch if you feel it's good enough to share.
#
# All non dtc hosts should be added in a SEPARATE file that you should include
# in your httpd.conf or apache.conf See your distribution manual to know where
# to find this file (somewhere in /etc/httpd or /etc/apache2 or even in
# /usr/local/etc/apache/httpd.conf ...).

# Loading the php5 module, as we had to disable the default config file which
# has in /etc/apache2/mods-enabled/php5.conf a SetHandler directive that is
# conflicting with the one we're using with SBOX
LoadModule php5_module /usr/lib/apache2/modules/libphp5.so

# Disabling TRACE (for security reasons)
RewriteEngine on
RewriteCond %{REQUEST_METHOD} ^(DELETE|TRACE|TRACK)
RewriteRule .* - [F]

";

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
		// make sure the vhost_chk_dir script has the 404 entries
                vhost_chk_dir_sh("$path_404/html");
                vhost_chk_dir_sh("$path_404/logs");
                vhost_chk_dir_sh("$path_404/cgi-bin");
	}

	if($conf_use_multiple_ip == "yes" && $conf_use_nated_vhost == "no"){
		$all_site_addrs = explode("|",$conf_site_addrs);
		$nbr_addrs = sizeof($all_site_addrs);
		for($i=0;$i<$nbr_addrs;$i++){
			// first write all config'ed IPs with the Listen
			if (test_valid_local_ip($all_site_addrs[$i]) && !preg_match("/Listen ".$all_site_addrs[$i].":80/", $vhost_file_listen))
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
				if ($conf_use_shared_ssl == "yes") {
					$vhost_file .= "NameVirtualHost ".$all_site_addrs[$i].":443\n";
				}
				if ($enable404feature == true){
					$vhost_file .= "<VirtualHost ".$all_site_addrs[$i].":80>
	ServerName $conf_404_subdomain.$conf_main_domain
	DocumentRoot $path_404/html
	<Directory $path_404/html>
		Allow from all
	</Directory>
	ScriptAlias /cgi-bin $path_404/cgi-bin
	ErrorLog $path_404/logs/error.log
	LogSQLTransferLogTable ".str_replace("-","A",str_replace(".","_",$conf_main_domain)).'$'.$conf_404_subdomain.'$'."xfer
	LogSQLScoreDomain $conf_main_domain
	LogSQLScoreSubdomain $conf_404_subdomain
	LogSQLScoreTable $conf_mysql_db.http_accounting
	DirectoryIndex $conf_apache_directoryindex
</VirtualHost>\n";
					$logrotate_file_chk[] = "$path_404/logs/error.log";
					//$logrotate_file .= "$path_404/logs/error.log ";
				}
			}
		}
	}else{
		$ip_for_404=$conf_main_site_ip;
		if($conf_use_nated_vhost=="yes"){
			$ip_for_404 = $conf_nated_vhost_ip;
			if (test_valid_local_ip($conf_nated_vhost_ip) && !preg_match("/Listen ".$conf_nated_vhost_ip.":80/", $vhost_file_listen))
			{
				$vhost_file_listen .= "Listen ".$conf_nated_vhost_ip.":80\n";
			} else {
				$vhost_file_listen .= "#Listen ".$conf_nated_vhost_ip.":80\n";
			}
			$vhost_file .= "NameVirtualHost ".$conf_nated_vhost_ip.":80\n";
		}else{
			if (test_valid_local_ip($conf_main_site_ip) && !preg_match("/Listen ".$conf_main_site_ip.":80/", $vhost_file_listen))
			{
				$vhost_file_listen .= "Listen ".$conf_main_site_ip.":80\n";
			} else {
				$vhost_file_listen .= "#Listen ".$conf_main_site_ip.":80\n";
			}
			$vhost_file .= "NameVirtualHost ".$conf_main_site_ip.":80\n";
			if ($conf_use_shared_ssl == "yes") {
				$vhost_file .= "NameVirtualHost ".$conf_main_site_ip.":443\n";
			}
		}
		if ($enable404feature == true){
			$vhost_file .= "<VirtualHost ".$ip_for_404.":80>
        ServerName $conf_404_subdomain.$conf_main_domain
        DocumentRoot $path_404/html
        ScriptAlias /cgi-bin $path_404/cgi-bin
        ErrorLog $path_404/logs/error.log
        LogSQLTransferLogTable ".str_replace("-","A",str_replace(".","_",$conf_main_domain)).'$'.$conf_404_subdomain.'$'."xfer
        LogSQLScoreDomain $conf_main_domain
        LogSQLScoreSubdomain $conf_404_subdomain
        LogSQLScoreTable $conf_mysql_db.http_accounting
        DirectoryIndex $conf_apache_directoryindex
</VirtualHost>\n";

		if ($conf_use_shared_ssl == "yes") {
			
		    $vhost_file .= "<VirtualHost ".$conf_main_site_ip.":443>
        ServerName $conf_404_subdomain.$conf_main_domain
        DocumentRoot $path_404/html
        ScriptAlias /cgi-bin $path_404/cgi-bin
        ErrorLog $path_404/logs/error.log
        LogSQLTransferLogTable ".str_replace("-","A",str_replace(".","_",$conf_main_domain)).'$'.$conf_404_subdomain.'$'."xfer
        LogSQLScoreDomain $conf_main_domain
        LogSQLScoreSubdomain $conf_404_subdomain
        LogSQLScoreTable $conf_mysql_db.http_accounting
        DirectoryIndex $conf_apache_directoryindex
	SSLEngine on
	SSLCertificateFile ".$conf_generated_file_path."/ssl/new.cert.cert
	SSLCertificateKeyFile ".$conf_generated_file_path."/ssl/new.cert.key
</VirtualHost>\n\n";	
		}

		$logrotate_file_chk[] = "$path_404/logs/error.log";
		// $logrotate_file .= "$path_404/logs/error.log ";
	    }
	
	}

	$vhost_file .= "<Directory $conf_dtcadmin_path>
	Options FollowSymLinks
	Order Deny,Allow
	Allow from all
</Directory>
<Directory $conf_dtcclient_path>
	Options FollowSymLinks
	Order Deny,Allow
	Allow from all
</Directory>
<Directory $conf_dtcemail_path>
	Options FollowSymLinks
	Order Deny,Allow
	Allow from all
</Directory>\n";

	if($conf_autogen_webmail_alias == "yes"){
		if($conf_autogen_webmail_type == "squirrelmail"){
			$vhost_file .= "RedirectPermanent /webmail https://$conf_administrative_site/squirrelmail\n";
		}else{
			$vhost_file .= "RedirectPermanent /webmail https://$conf_administrative_site/roundcube\n";
		}
	}

	#############################
	# mod_cband user generation #
	#############################
	$vhost_file .= "<IfModule mod_cband.c>\n";
	$q = "SELECT DISTINCT adm_login,$pro_mysql_product_table.bandwidth FROM $pro_mysql_domain_table,$pro_mysql_admin_table,$pro_mysql_product_table
WHERE $pro_mysql_domain_table.owner=$pro_mysql_admin_table.adm_login
AND $pro_mysql_product_table.id=$pro_mysql_admin_table.prod_id
AND $pro_mysql_admin_table.prod_id != '0'
AND $pro_mysql_admin_table.id_client != '0'";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$vhost_file .= "
<CBandUser ".$a["adm_login"].">
	CBandSpeed 10Mbps 10 30
	CBandRemoteSpeed 2Mbps 3 3
	CBandLimit ".$a["bandwidth"]."M
	CBandPeriod 4W
	CBandPeriodSlice 1W
	CBandExceededSpeed 32kbps 2 5
	CBandUserScoreboard /var/lib/dtc/etc/cband_scores/".$a["adm_login"]."
</CBandUser>
";
	}
	$vhost_file .= "</IfModule>\n";
	#################################
	# end mod_cband user generation #
	#################################

	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user");
		$web_name = $row["name"];
		if ($web_name == "")
		{
			print("No name specified for domain, skipping...");
			continue;
		}
		$web_owner = $row["owner"];
		$ip_addr = $row["ip_addr"];
		$domain_safe_mode = $row["safe_mode"];
		$domain_sbox_protect = $row["sbox_protect"];
		$domain_parking = $row["domain_parking"];
		$domain_parking_type = $row["domain_parking_type"];
		$domain_wildcard_dns = $row["wildcard_dns"];
		$domain_default_sub_server_alias = $row["default_sub_server_alias"];

		unset($backup_ip_addr);
		if (isset($row["backup_ip_addr"])){
			$backup_ip_addr = $row["backup_ip_addr"];
		}
		if (isset($backup_ip_addr) && ($backup_ip_addr == "NULL" || trim($backup_ip_addr) == "")){
			unset($backup_ip_addr);
		} 
		// need to check if we have a NameVirtualHost entry for this backup IP, to support multiple backup sites on one IP
		if (isset($backup_ip_addr)){
			if (test_valid_local_ip($backup_ip_addr) && !preg_match("/Listen ".$backup_ip_addr.":80/", $vhost_file_listen)){
				$vhost_file_listen .= "Listen ".$backup_ip_addr.":80\n";
			} else {
				$vhost_file_listen .= "#Listen ".$backup_ip_addr.":80\n";
			}
			if (!preg_match("/NameVirtualHost $backup_ip_addr/", $vhost_file)){
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
			echo("No user of that name ($web_owner)!\n");
			continue;
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
			$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND ip='default' AND subdomain_name!='$conf_404_subdomain' AND subdomain_name!='$web_default_subdomain' ORDER BY subdomain_name;";
		}else{
			$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$domain_to_get' AND ip='default' AND subdomain_name!='$web_default_subdomain' ORDER BY subdomain_name;";
		}
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);

		unset($temp_array_subs);
		$temp_array_subs = array();
		for($j=0;$j<$num_rows2;$j++){
			$temp_array_subs[] = mysql_fetch_array($result2) or die ("Cannot fetch user line ".__LINE__." file ".__FILE__);
		}

		// We get the default subdomain and we add it at the end of the array. The goal is to have the
		// wildcard subdomain be the last in the list of the vhosts.conf
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$domain_to_get' AND ip='default' AND subdomain_name='$web_default_subdomain';";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$my_num_rows = mysql_num_rows($result2);
		if($my_num_rows == 1){
			$temp_array_subs[] = mysql_fetch_array($result2) or die ("Cannot fetch user".__LINE__." file ".__FILE__);
			$num_rows2++;
		}

		for($j=0;$j<$num_rows2;$j++){
			$subdomain = $temp_array_subs[$j];
//			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];
			$shared_hosting_subdomain_security = $subdomain["shared_hosting_security"];
			if( $subdomain["customize_vhost"] == ""){
				$custom_directives = "";
			}else{
				$custom_directives = "
	# Start of custom directives
	".$subdomain["customize_vhost"]."
	# End of custom directives";
			}

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
					if (test_valid_local_ip($ip_to_write) && !preg_match("/Listen ".$ip_to_write.":".$conf_administrative_ssl_port."/", $vhost_file_listen))
					{
						$vhost_file_listen .= "Listen ".$ip_to_write.":".$conf_administrative_ssl_port."\n";
					} else {
						$vhost_file_listen .= "#Listen ".$ip_to_write.":".$conf_administrative_ssl_port."\n";
					}
					$vhost_file .= "<VirtualHost ".$ip_to_write.":".$conf_administrative_ssl_port.">\n";
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
						echo("No user of that name ($web_ownerX)!\n");
						continue;
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
							$safex = "php_admin_value safe_mode 0";
						}else{
							$safex = "php_admin_value safe_mode 1";
						}
						$vhost_file .= "\tAlias /$subdomx.$web_nameX $web_pathX/$web_nameX/subdomains/$subdomx/html
	<Location /$subdomx.$web_nameX>
		".$safex.$custom_directives."
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
					if (file_exists($conf_generated_file_path."/ssl/new.cert.ca")) {
						$vhost_file .= "	SSLCertificateChainFile ".$conf_generated_file_path."/ssl/new.cert.ca\n";
					}
				}
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/html");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/logs");
				vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/cgi-bin");
				if ( $conf_unix_type == "bsd" ) {
					$vhost_file .= "	Alias /phpmyadmin ".$conf_tools_prefix."/phpMyAdmin\n";
				}else{
					$vhost_file .= "	Alias /phpmyadmin ".$conf_tools_prefix."/phpmyadmin\n";
				}
				$vhost_file .= "	Alias /dtc $conf_dtcclient_path
	Alias /dtcdoc $conf_dtcdoc_path/html/en
	Alias /dtcemail $conf_dtcemail_path
	Alias /dtcadmin $conf_dtcadmin_path/
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs
	Alias /awstats-icon $conf_tools_prefix/awstats/icon
	Alias /squirrelmail ".$conf_tools_prefix."/squirrelmail
	Alias /roundcube /var/lib/roundcube
	Alias /extplorer /usr/share/extplorer
	php_admin_value sendmail_from webmaster@$web_name
	DocumentRoot $web_path/$web_name/subdomains/$web_subname/html
	<Directory $web_path/$web_name/subdomains/$web_subname/html>
		Allow from all
	</Directory>
	<Directory $web_path/$web_name/subdomains/$web_subname/logs>
		Allow from all
	</Directory>
# No ScriptAlias: we want to use system's /usr/lib/cgi-bin !!!
#	ScriptAlias /cgi-bin $web_path/$web_name/subdomains/$web_subname/cgi-bin
	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename\$xfer
	LogSQLScoreDomain $web_name
	LogSQLScoreSubdomain $web_subname
	LogSQLScoreTable $conf_mysql_db.http_accounting
	DirectoryIndex $conf_apache_directoryindex$custom_directives
	<IfModule mod_bwshare.c>
		BW_throttle_off 1
	</IfModule>
	<IfModule mod_security2.c>
		SecRuleEngine Off
	</IfModule>";
	if($conf_force_use_https == "yes" && $conf_use_ssl == "yes"){
		$vhost_file .= "
	RewriteEngine On
	RewriteCond %{HTTPS} off
	RewriteRule (.*) https://$conf_administrative_site$1 [R,L]";
	}
$vhost_file .= "
</VirtualHost>

";
			$logrotate_file_chk[] = "$web_path/$web_name/subdomains/$web_subname/logs/error.log";
			//$logrotate_file .= "$web_path/$web_name/subdomains/$web_subname/logs/error.log ";
} // - end of for loop

// ---------------------------------------------------
// --- Start of the conf of server users subdomain ---
// ---------------------------------------------------
			} else {
				// Generate a permanet redirect for all subdomains of target if using a domain parking
				if($domain_parking != "no-parking" && ($domain_parking_type == "redirect" || $conf_administrative_site == "$web_subname.$domain_to_get")){
					if($j == 0){
						$console .= "Making domain parking for $web_name\n";
						$vhost_file .= "<VirtualHost ".$ip_to_write.":80>
	ServerName $web_name
	Redirect permanent / http://$domain_parking/
</VirtualHost>\n\n";
					}
					$console .= "Making domain parking for $web_subname.$web_name\n";
					$vhost_file .= "<VirtualHost ".$ip_to_write.":80>
	ServerName $web_subname.$web_name
	Redirect permanent / http://$web_subname.$domain_parking/
</VirtualHost>\n\n";
                                } else if ($domain_parking != "no-parking" && $domain_parking_type == "serveralias") {
                                        // do nothing here, as serveralias parking will be injected throughout the generation of the main domain
				}else{
					vhost_chk_dir_sh("$web_path/$domain_to_get/subdomains/$web_subname/logs");
					vhost_chk_dir_sh("$web_path/$domain_to_get/subdomains/$web_subname/html");
					vhost_chk_dir_sh("$web_path/$domain_to_get/subdomains/$web_subname/cgi-bin");
					// We need to make it for both in case of a domain parking
					if($domain_to_get != $web_name){
						vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/logs");
						vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/html");
						vhost_chk_dir_sh("$web_path/$web_name/subdomains/$web_subname/cgi-bin");
					}
					if($webadmin["shared_hosting_security"] == 'sbox_aufs' || ($webadmin["shared_hosting_security"] == 'mod_php' && $subdomain["shared_hosting_security"] == 'sbox_aufs')){
						vhost_chk_dir_sh("$web_path/$web_name/subdomains.aufs");
						vhost_chk_dir_sh("$web_path/$web_name/subdomains.aufs/".$web_subname);
						$aufs_list .= "$web_path/$web_name/subdomains/".$web_subname."\n";
					}
					$iteration_table = array();
					$iteration_table[] = "normal";
					$ssl_cert_folder_path = "$web_path/$domain_to_get/subdomains/$web_subname/ssl";
					if($subdomain["ssl_ip"] != "none"){
						$ssl_returns = checkCertificate($ssl_cert_folder_path,$web_subname.".".$web_name);
						if($ssl_returns == "yes"){
							$iteration_table[] = "ssl";
							// Start of <krystian@ezpear.com> patch
							if($conf_use_nated_vhost=="yes"){
								$q="select port from $pro_mysql_ssl_ips_table where ip_addr='${subdomain["ssl_ip"]}' and available='no';";
								$r=mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
								$n = mysql_num_rows($r);
								if($n > 0){
									$row=mysql_fetch_array($r);
									$port=$row["port"];
									$ip_vhost=$ip_to_write;
									if(empty($port)){
										$port = "443";
									}
								}else{
									$port = "443";
									$ip_vhost = $subdomain["ssl_ip"];
								 }
							}else{
								$port = "443";
							}
							// End of <krystian@ezpear.com> patch
						}
					}

					// if we want to generate a backup IP (transitional)
					// need to loop through this one
					if (isset($backup_ip_addr)){
						$iteration_table[] = "backup";
					}

					$log_tablename = str_replace("-","A",str_replace(".","_",$web_name)).'$'.str_replace("-","A",str_replace(".","_",$web_subname));
					$vhost_more_conf = "";
					$php_more_conf = "";
					if($subdomain["register_globals"] == "yes"){
						$php_more_conf .= "	php_admin_value register_globals 1\n";
					}
					if($subdomain["php_memory_limit"] != ""){
						$php_more_conf .= "	php_admin_value memory_limit ".$subdomain["php_memory_limit"]."M\n";
					}
					if($subdomain["php_max_execution_time"] != ""){
						$php_more_conf .= "	php_admin_value max_execution_time ".$subdomain["php_max_execution_time"]."\n";
					}
					if($subdomain["php_session_auto_start"] == "yes"){
						$php_more_conf .= "	php_admin_flag session_autostart ".$subdomain["php_session_auto_start"]."\n";
					}
					if($subdomain["php_allow_url_fopen"] == "yes"){
						$php_more_conf .= "	php_admin_flag allow_url_fopen on\n";
					}
					if($subdomain["php_post_max_size"] != ""){
						$php_more_conf .= "	php_admin_value post_max_size ".$subdomain["php_post_max_size"]."M\n";
					}
					if($subdomain["php_upload_max_filesize"] != ""){
						$php_more_conf .= "	php_admin_value upload_max_filesize ".$subdomain["php_upload_max_filesize"]."M\n";
					}
					
					if($subdomain["use_shared_ssl"] == "yes" && $conf_use_shared_ssl == "yes"){
						$iteration_table[]="shared_ssl";
					}
					if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $subdomain["redirect_url"])) {
						$vhost_more_conf .= "Redirect / ".$subdomain["redirect_url"]."\n";
					}
					if($web_subname == "$web_default_subdomain"){
						if ($domain_parking == "no-parking"){
							// no domain parking
							$server_alias_domain = $domain_to_get;
						} else {
							// parking: same_docroot
							$server_alias_domain = $web_name;
							// parking: redirect doesn't happen in this else branch
						}
						if($domain_default_sub_server_alias == "yes"){
							$vhost_more_conf .= "	ServerAlias $server_alias_domain\n";
						}
						if($domain_wildcard_dns == "yes"){
							$vhost_more_conf .= "   ServerAlias *.$server_alias_domain\n";
						}
					}

					// ServerAlias for parked domains
					$q_serveralias = "select * from $pro_mysql_domain_table where domain_parking_type='serveralias' and domain_parking='$web_name'";
					$r_serveralias = mysql_query($q_serveralias) or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					while ($row_serveralias = mysql_fetch_array($r_serveralias)) {
						// default subdomain and wildcard subdomain settings are inherited from the main domain, not the parked domain
						// this is because in the gui these settings are not accessable for a parked domain
						if ($web_subname == "$web_default_subdomain") {
							$vhost_more_conf .= "        ServerAlias ${row_serveralias["name"]}\n";
						}
						$vhost_more_conf .= "        ServerAlias $web_subname.${row_serveralias["name"]}\n";
						if ($domain_wildcard_dns == "yes") {
							$vhost_more_conf .= "        ServerAlias *.${row_serveralias["name"]}\n";
						}
					}

					// Sbox and safe mode protection values
					if($domain_safe_mode == "no" && $subdomain["safe_mode"] == "no"){
						$safe_mode_value = "0";
					}else{
						$safe_mode_value = "1";
					}
					if($domain_sbox_protect == "no" && $subdomain["sbox_protect"] == "no"){
						$cgi_directive = "ScriptAlias /cgi-bin $web_path/$domain_to_get/subdomains/$web_subname/cgi-bin";
					}else{
						$cgi_directive = "";
//						$cgi_directive = "RewriteEngine on
//	RewriteRule ^/cgi-bin/(.*) /cgi-bin/sbox/$1 [PT]";
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
							//if($conf_use_nated_vhost=="no"){
							//	$vhost_file .= "Listen ".$ip_vhost.":$port\n";
							//}
							$vhost_file .= "Listen ".$subdomain["ssl_ip"].":$port\n";
							$vhost_file .= "<VirtualHost ".$subdomain["ssl_ip"].":$port>\n";
							$vhost_file .= "	SSLEngine on\n";
							$vhost_file .= "	SSLCertificateFile $ssl_cert_folder_path/".$web_subname.".".$domain_to_get.".cert.cert\n";
							$vhost_file .= "	SSLCertificateKeyFile $ssl_cert_folder_path/".$web_subname.".".$domain_to_get.".cert.key\n";
							if (file_exists("$ssl_cert_folder_path/".$web_subname.".".$domain_to_get.".cert.ca")) {
								$vhost_file .= "	SSLCertificateChainFile $ssl_cert_folder_path/".$web_subname.".".$domain_to_get.".cert.ca\n";
							}
							break;
						case "shared_ssl":
							$vhost_file .= "<VirtualHost ".$ip_to_write.":443>\n";
							$vhost_file .= "	SSLEngine on\n";
							$vhost_file .= "	SSLCertificateFile ".$conf_generated_file_path."/ssl/new.cert.cert\n";
							$vhost_file .= "	SSLCertificateKeyFile ".$conf_generated_file_path."/ssl/new.cert.key\n";
							if (file_exists($conf_generated_file_path."/ssl/new.cert.ca")) {
								$vhost_file .= "	SSLCertificateChainFile ".$conf_generated_file_path."/ssl/new.cert.ca\n";
							}
							break;
						}
						$vhost_file .= "	ServerName $web_subname.$web_name
	Alias /stats $web_path/$web_name/subdomains/$web_subname/logs
	Alias /awstats-icon /usr/share/awstats/icon\n";
						// Disable the site if expired
						if($site_expired == "yes"){
							$document_root = $conf_generated_file_path."/expired_site";
							$vhost_file .= "	DocumentRoot $document_root
	<Directory $document_root>
		Allow from all
	</Directory>\n";
						}else{
							if($webadmin["shared_hosting_security"] == 'sbox_aufs' || ($webadmin["shared_hosting_security"] == 'mod_php' && $subdomain["shared_hosting_security"] == 'sbox_aufs')){
								$document_root = "$web_path/$domain_to_get/subdomains.aufs/$web_subname/html";
							}else{
								$document_root = "$web_path/$domain_to_get/subdomains/$web_subname/html";
							}

							$vhost_file .= "	DocumentRoot $document_root
	<Directory $document_root>
		Allow from all
	</Directory>\n";
							if($webadmin["shared_hosting_security"] == 'mod_php' && $shared_hosting_subdomain_security == 'mod_php'){
								$vhost_file .= $vhost_more_conf.$php_more_conf."	php_admin_value safe_mode $safe_mode_value
	php_admin_value sendmail_from phpmailfunction$web_subname@$web_name
	php_admin_value sendmail_path \"/usr/sbin/sendmail -t -i -f phpmailfunction$web_subname@$domain_to_get\"
	php_value session.save_path $web_path/$domain_to_get/subdomains/$web_subname/tmp
	<Location />
		php_admin_value open_basedir \"$web_path:$conf_php_library_path:$conf_php_additional_library_path:\"
	</Location>
	$cgi_directive\n";
							}else{
								$vhost_file .= "$vhost_more_conf	ScriptAlias /cgi-bin /usr/lib/cgi-bin
	php_admin_flag engine off
	AddHandler php-cgi-wrapper .php
	Action php-cgi-wrapper /cgi-bin/sbox
	AddHandler python-cgi-wrapper .py
	Action python-cgi-wrapper /cgi-bin/sbox
	AddHandler ruby-cgi-wrapper .rb
	Action ruby-cgi-wrapper /cgi-bin/sbox
	AddHandler ruby-cgi-wrapper .pl
	Action ruby-cgi-wrapper /cgi-bin/sbox
	ErrorDocument 404 /sbox404/404.php
	ErrorDocument 400 /sbox404/406.php
	ErrorDocument 406 /sbox404/406.php
	ErrorDocument 500 /sbox404/406.php
	ErrorDocument 501 /sbox404/406.php
	Options +ExecCGI\n";
							}
							$vhost_file .= get_defaultCharsetDirective($subdomain["add_default_charset"]);
						}
						$vhost_file .= "	ErrorLog $web_path/$web_name/subdomains/$web_subname/logs/error.log
	LogSQLTransferLogTable $log_tablename\$xfer
	LogSQLScoreDomain $web_name
	LogSQLScoreSubdomain $web_subname
	LogSQLScoreTable $conf_mysql_db.http_accounting
	DirectoryIndex $conf_apache_directoryindex$custom_directives
	<IfModule mod_security.c>
		SecUploadDir $web_path/$domain_to_get/subdomains/$web_subname/tmp
	</IfModule>
	<IfModule mod_cband.c>
		CBandUser $web_owner
	</IfModule>
</VirtualHost>

";
						$logrotate_file_chk[] = "$web_path/$web_name/subdomains/$web_subname/logs/error.log";
						//$logrotate_file .= "$web_path/$web_name/subdomains/$web_subname/logs/error.log ";
						$num_generated_vhosts += $num_rows2;
					}
				}
			}
	        }
	}

	// Writting the vhosts.conf file
	$filep = fopen("$conf_generated_file_path/$conf_apache_vhost_path", "w+");
	if( $filep == NULL){
		die("Cannot open $conf_generated_file_path/$conf_apache_vhost_path file for writting");
	}
	fwrite($filep,$vhost_file_start);
	fwrite($filep,$vhost_file_listen);
	fwrite($filep,$vhost_file);
	fclose($filep);
	$console .= "$num_generated_vhosts vhosts generated !<br>";

	// Writting the vhost_check_dir script
	$filep = fopen("$conf_generated_file_path/vhost_check_dir","w+");
	if( $filep == NULL){
		echo("Cannot open $conf_generated_file_path/vhost_check_dir file for writting");
	}else{
		fwrite($filep,$chk_dir_script);
		fclose($filep);
	}
	$console .= "vhost_check_dir.sh script written !<br>";

	// Writing the vhost_check_ssl_cert script
	$chk_certs_script .= "exit \$EXIT_VAL";
	$filep = fopen("$conf_generated_file_path/vhost_check_ssl_cert","w+");
	if( $filep == NULL){
		echo("Cannot open $conf_generated_file_path/vhost_check_ssl_cert file for writting");
	}else{
		fwrite($filep,$chk_certs_script);
		fclose($filep);
		chmod("$conf_generated_file_path/vhost_check_ssl_cert",0700);
	}
	$console .= "vhost_check_ssl_cert script written !<br>";

	// Writing $aufs_list list
	$filep = fopen("$conf_generated_file_path/aufs_list","w+");
	if( $filep == NULL){
		echo("Cannot open $conf_generated_file_path/aufs_lis file for writting");
	}else{
		fwrite($filep,$aufs_list);
		fclose($filep);
	}
	$console .= "aufs_list written !<br>";

	// Writing the logrotate configuration file
	if($logrotate_file != ""){
		$fname = "";
		if( file_exists("/etc/dtc/logrotate.template") ){
			$fname = "/etc/dtc/logrotate.template";
		}else if( file_exists("/usr/local/etc/dtc/logrotate.template") ){
			$fname = "/usr/local/etc/dtc/logrotate.template";
		}
		if($fname != ""){
			$fp = fopen($fname,"r");
			if($fp != NULL){
				$logrotate_template = fread($fp,filesize($fname));
				fclose($fp);
			}else{
				$logrotate_template = "";
			}
		}else{
			$logrotate_template = "";
		}
		$logrotate_file_checked = array_unique($logrotate_file_chk);
		foreach($logrotate_file_checked as $logrotate_entry){ 
			if(!empty($logrotate_entry)) 
				$logrotate_file .= $logrotate_entry." ";
		}
		$logrotate_file .= " {
$logrotate_template

	sharedscripts
";
		if($conf_apache_version == "2"){
			$logrotate_file .= "
	postrotate
		if [ -f /var/run/apache2.pid ]; then
			/etc/init.d/apache2 restart > /dev/null
		fi
	endscript
}
";
		}else{
			$logrotate_file .= "
	postrotate
		if [ -f /var/run/apache.pid ]; then \
			if [ -x /usr/sbin/invoke-rc.d ]; then \
				invoke-rc.d apache reload > /dev/null; \
			else \
				if [ -x /etc/init.d/apache ]; then \
					/etc/init.d/apache reload > /dev/null; \
				elif [ -x /etc/init.d/httpd ]; then \
					/etc/init.d/httpd reload > /dev/null; \
				fi; \
			fi; \
		fi;
	endscript
}
";
		}
		$filep = fopen("$conf_generated_file_path/logrotate","w+");
		if( $filep == NULL){
			echo ("Cannot open $conf_generated_file_path/logrotate for writting");
		}else{
			fwrite($filep,$logrotate_file);
			fclose($filep);
		}
		$console .= "logrotate config file generated!<br>";
	}
	return true;
}

?>
