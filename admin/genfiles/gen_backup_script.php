<?php

function backup_by_ftp(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $conf_generated_file_path;
	global $conf_backup_script_path;
	global $conf_bakcup_path;

	global $conf_mysql_login;
	global $conf_mysql_pass;

	global $conf_ftp_backup_host;
	global $conf_ftp_backup_login;
	global $conf_ftp_backup_pass;
	global $conf_ftp_backup_frequency;
	global $conf_ftp_backup_dest_folder;

	global $conf_nobody_group_id;

	global $conf_dtc_system_uid;
	global $conf_dtc_system_username;
	global $conf_dtc_system_gid;
	global $conf_dtc_system_groupname;

	global $conf_ftp_active_mode;
	global $conf_mysql_db;

	global $console;

	global $conf_user_mysql_host;

	global $conf_mysql_host;

	$num_generated_vhosts=0;
	$num_generated_db=0;

	$restor_net = "#!/bin/sh
date\n";

	$backup_net = "#!/bin/sh
date
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin
";
	
	//Set it to -F, we will go passive by default, active, if it turned on
	
	$ncftp_mode="-F";
	if ($conf_ftp_active_mode=="yes") {
	    $ncftp_mode="-E";
	}
	// Get the owner informations
	$q = "SELECT adm_login,path FROM $pro_mysql_admin_table WHERE 1;";
	$r = mysql_query ($q)or die("Cannot execute query \"$q\" !".mysql_error()." line ".__LINE__." file ".__FILE__);
	$nr = mysql_num_rows($r);
	for($i=0;$i<$nr;$i++){
		$ra = mysql_fetch_array($r);
		$owner = $ra["adm_login"];
		$path = $ra["path"];

		$restor_net .= "echo \"===> Restoring all files for user $owner:\"\n";
		$restor_net .= "mkdir -p $path\n";
//		$restor_net .= "chown nobody:$conf_nobody_group_id $path\n";
		$restor_net .= "chown  $conf_dtc_system_username:$conf_dtc_system_groupname $path\n";
		$restor_net .= "cd $path\n";

		$backup_net .= "echo \"===> Backuping all files for user $owner:\"\n";
		$backup_net .= "cd $path\n";
		$q2 = "SELECT name FROM $pro_mysql_domain_table WHERE owner='$owner';";
		$r2 = mysql_query ($q2)or die("Cannot execute query \"$q2\" !".mysql_error()." line ".__LINE__." file ".__FILE__);
		$nr2 = mysql_num_rows($r2);
		for($j=0;$j<$nr2;$j++){
			$ra2 = mysql_fetch_array($r2);
			$webname = $ra2["name"];
			$backup_net .= "echo -n \"$webname (\"\n";
			$backup_net .= "echo -n \"mail\"\n";
			$backup_net .= "tar -cf $owner.$webname.tar $webname/Mailboxs\n";
			$backup_net .= "echo -n \",lists\"\n";
			$backup_net .= "if [ -d $webname/lists ] ; then tar -rf $owner.$webname.tar $webname/lists ; else echo -n \"(dir not found)\"; fi\n";
			$q3 = "SELECT subdomain_name FROM $pro_mysql_subdomain_table WHERE domain_name='$webname';";
			$r3 = mysql_query ($q3)or die("Cannot execute query \"$q3\" !".mysql_error()." line ".__LINE__." file ".__FILE__);
			$nr3 = mysql_num_rows($r3);
			for($k=0;$k<$nr3;$k++){
				$ra3 = mysql_fetch_array($r3);
				$subdom_name = $ra3["subdomain_name"];
				$backup_net .= "echo -n \",$subdom_name\"\n";
				$backup_net .= "if [ -d $webname/subdomains/$subdom_name ] ; then tar -rf $owner.$webname.tar $webname/subdomains/$subdom_name ; else echo -n \"(dir not found)\"; fi\n";
			}
			$backup_net .= "echo -n \")\"\n";
			$backup_net .= "echo -n \" compressing\"\n";
			$backup_net .= "gzip -f $owner.$webname.tar\n";
			$backup_net .= "echo \" uploading\"\n";

			$restor_net .= "echo \"Getting domain file $owner.$webname.tar.gz\"\n";
			$restor_net .= "ncftpget -f $conf_generated_file_path/ncftpput_login.cfg $ncftp_mode . $conf_ftp_backup_dest_folder/$owner.$webname.tar.gz\n";
			$restor_net .= "echo \"Unpacking...\"\n";
			$restor_net .= "tar -xzf $owner.$webname.tar.gz\n";
			$restor_net .= "echo \"Chown... $webname\"\n";
//			$restor_net .= "chown -R nobody:$conf_nobody_group_id $webname\n";
			$restor_net .= "chown -R $conf_dtc_system_username:$conf_dtc_system_groupname $webname\n";
			$restor_net .= "rm -f $owner.$webname.tar.gz\n";

			$backup_net .= "ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder $owner.$webname.tar.gz\n";
			$backup_net .= "echo \" deleting archive\"\n";
			$backup_net .= "rm -f $owner.$webname.tar.gz\n";
			$num_generated_vhosts++;
		}
		$backup_net .= "echo \"===> Backuping all dabatases for user $owner:\"\n";
		mysql_select_db("mysql");
		$q3 = "SELECT db.Db FROM db,user WHERE user.dtcowner='$owner' AND db.User=user.User GROUP BY db.Db;";
		$r3 = mysql_query($q3)or die("Cannot query \"$q3\" ! Line:".__LINE__." File:".__FILE__);
		$n3 = mysql_num_rows($r3);
		for($k=0;$k<$n3;$k++){
			$a3 = mysql_fetch_array($r3)or die("Cannot fetch array line".__LINE__." file ".__FILE__);
			$dbfilename = $owner.".userdb.".$a3["Db"].".sql";
			$backup_net .= "echo -n \" Database ".$a3["Db"].": \"\n";
			$backup_net .= "echo -n \" dumping...\"\n";
			$backup_net .= "mysqldump -u$conf_mysql_login -p$conf_mysql_pass -c --add-drop-table --databases ".$a3["Db"]." >".$dbfilename."\n";
			$backup_net .= "echo -n \" compressing...\"\n";
			$backup_net .= "gzip $dbfilename\n";
			$backup_net .= "echo \" Done! Starting upload!\"\n";
			$backup_net .= "ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder ".$dbfilename.".gz\n";

			$restor_net .= "echo \"Getting file ".$dbfilename.".gz\"\n";
			$restor_net .= "ncftpget -f $conf_generated_file_path/ncftpput_login.cfg $ncftp_mode . $conf_ftp_backup_dest_folder/".$dbfilename.".gz\n";
			$restor_net .= "echo \"Ungziping...\"\n";
			$restor_net .= "gzip -d ".$dbfilename.".gz\n";
			$restor_net .= "echo \"Restoring SQL...\"\n";
			$restor_net .= "mysql -u$conf_mysql_login -p$conf_mysql_pass <".$dbfilename."\n";
			$restor_net .= "rm -f ".$dbfilename."\n";

			$backup_net .= "echo \" deleting archive\"\n";
			$backup_net .= "rm -f ".$dbfilename.".gz\n";
			$num_generated_db++;
		}
		mysql_select_db($conf_mysql_db);
	}
	$backup_net .= "echo -n \"===> Backuping database dtc: \"\n";
	$dbfilename = "dtcdb.sql";
	$backup_net .= "echo -n \" dumping...\"\n";
	$backup_net .= "mysqldump -h$conf_mysql_host -u$conf_mysql_login -p$conf_mysql_pass -c --add-drop-table --databases $conf_mysql_db >".$dbfilename."\n";
	$backup_net .= "echo -n \" compressing...\"\n";
	$backup_net .= "gzip $dbfilename\n";
	$backup_net .= "echo \" Done! Starting upload!\"\n";
	$backup_net .= "ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder ".$dbfilename.".gz\n";
	$backup_net .= "echo \" deleting archive\"\n";
	$backup_net .= "rm -f ".$dbfilename.".gz\n";

	$restor_net .= "echo \"Getting file ".$dbfilename.".gz\"\n";
	$restor_net .= "ncftpget -f $conf_generated_file_path/ncftpput_login.cfg $ncftp_mode . $conf_ftp_backup_dest_folder/".$dbfilename.".gz\n";
	$restor_net .= "echo \"Ungziping...\"\n";
	$restor_net .= "gzip -d ".$dbfilename.".gz\n";
	$restor_net .= "echo \"Restoring SQL...\"\n";
	$restor_net .= "mysql -h$conf_mysql_host -u$conf_mysql_login -p$conf_mysql_pass <".$dbfilename."\n";
	$restor_net .= "echo \" deleting archive\"\n";
	$restor_net .= "rm -f ".$dbfilename."\n";

	$backup_net .= "echo -n \"===> Backuping database mysql: \"\n";
	$dbfilename = "mysqldb.sql";
	$backup_net .= "echo -n \" dumping...\"\n";
	$backup_net .= "mysqldump -h$conf_user_mysql_host -u$conf_mysql_login -p$conf_mysql_pass -c --add-drop-table --databases mysql >".$dbfilename."\n";
	$backup_net .= "echo -n \" compressing...\"\n";
	$backup_net .= "gzip $dbfilename\n";
	$backup_net .= "echo \" Done! Starting upload!\"\n";
	$backup_net .= "ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder ".$dbfilename.".gz\n";
	$backup_net .= "echo \" deleting archive\"\n";
	$backup_net .= "rm -f ".$dbfilename.".gz\n";

	$restor_net .= "echo \"Getting file ".$dbfilename.".gz\"\n";
	$restor_net .= "ncftpget -f $conf_generated_file_path/ncftpput_login.cfg $ncftp_mode . $conf_ftp_backup_dest_folder/".$dbfilename.".gz\n";
	$restor_net .= "echo \"Ungziping...\"\n";
	$restor_net .= "gzip -d ".$dbfilename.".gz\n";
	$restor_net .= "echo \"Restoring SQL...\"\n";
	$restor_net .= "mysql -h$conf_user_mysql_host -u$conf_mysql_login -p$conf_mysql_pass <".$dbfilename."\n";
	$restor_net .= "echo \" deleting archive\"\n";
	$restor_net .= "rm -f ".$dbfilename."\n";

	$backup_net .= "ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder $conf_generated_file_path/net_restor.sh\n";
	$backup_net .= "if [ -e /etc/apache/httpd.conf ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/apache/httpd.conf\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/httpd/httpd.conf ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/httpd/httpd.conf\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /usr/local/etc/apache/httpd.conf ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /usr/local/etc/apache/httpd.conf\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /usr/local/etc/apache2/httpd.conf ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /usr/local/etc/apache2/httpd.conf\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /usr/local/etc/apache22/httpd.conf ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /usr/local/etc/apache22/httpd.conf\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/apache2/apache2.conf ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/apache2/apache2.conf\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/php4/apache/php.ini ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/php4/apache/php.ini\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/php4/apache2/php.ini ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/php4/apache2/php.ini\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/php5/apache/php.ini ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/php5/apache/php.ini\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/php5/apache2/php.ini ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/php5/apache2/php.ini\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/httpd/php.ini ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/httpd/php.ini\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /usr/local/etc/php.ini ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /usr/local/etc/php.ini\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /etc/crontab ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /etc/crontab\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e /var/spool/root/crontab ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder /var/spool/root/crontab\n";
	$backup_net .= "fi\n";
	$backup_net .= "if [ -e $conf_generated_file_path/ncftpput_login.cfg ] ; then\n";
	$backup_net .= "	ncftpput -f $conf_generated_file_path/ncftpput_login.cfg -V -T tmp. $ncftp_mode $conf_ftp_backup_dest_folder $conf_generated_file_path/ncftpput_login.cfg\n";
	$backup_net .= "fi\n";
	$restor_net .= "echo \"Rewriting dtcdaemons user password on mysql database.\"\n";
	$restor_net .= "dtcdaemons_pass=`cat $conf_generated_file_path/dtcdb_passwd`\n";
	$restor_net .= "mysql -h$conf_user_mysql_host -u$conf_mysql_login -p$conf_mysql_pass -D mysql -e \"update user set password=password('\$dtcdaemons_pass') where user='dtcdaemons';\"\n";
	$restor_net .= "echo \"Marking all scripts and DNS zones to be regenerated on next cron.\"\n";
	$restor_net .= "mysql -h$conf_user_mysql_host -u$conf_mysql_login -p$conf_mysql_pass -D dtc -e \"update domain set generate_flag='yes';\"\n";
	$restor_net .= "mysql -h$conf_user_mysql_host -u$conf_mysql_login -p$conf_mysql_pass -D dtc -e \"update dedicated_ip set rdns_regen='yes';\"\n";
	$restor_net .= "mysql -h$conf_user_mysql_host -u$conf_mysql_login -p$conf_mysql_pass -D dtc -e \"update vps_ip set rdns_regen='yes';\"\n";
	$restor_net .= "mysql -h$conf_user_mysql_host -u$conf_mysql_login -p$conf_mysql_pass -D dtc -e \"update cron_job set qmail_newu='yes',restart_qmail='yes',reload_named='yes',restart_apache='yes',gen_vhosts='yes',gen_named='yes',gen_reverse='yes',gen_fetchmail='yes',gen_qmail='yes',gen_webalizer='yes',gen_backup='yes',gen_ssh='yes',gen_nagios='yes',lock_flag='finished';\"\n";

	$backup_net .= "date\n";
	$restor_net .= "date\n";
	$filep = fopen("$conf_generated_file_path/net_backup.sh", "w+");
	if( $filep == NULL){
		echo("Cannot open file for writting");
	}
	fwrite($filep,$backup_net);
	fclose($filep);
	chmod("$conf_generated_file_path/net_backup.sh",0750);
	system("chown $conf_dtc_system_username \"$conf_generated_file_path/net_backup.sh\"");
	$console .= "Generated net_backup.sh script for $num_generated_vhosts domains and $num_generated_db db!<br>";

	$filep = fopen("$conf_generated_file_path/net_restor.sh", "w+");
	if( $filep == NULL){
		echo("Cannot open file for writting");
	}
	fwrite($filep,$restor_net);
	fclose($filep);
	chmod("$conf_generated_file_path/net_restor.sh",0750);
	system("chown $conf_dtc_system_username \"$conf_generated_file_path/net_restor.sh\"");
	$console .= "Generated net_restor.sh script for $num_generated_vhosts domains and $num_generated_db db!<br>";

	global $conf_ftp_backup_host;
	global $conf_ftp_backup_login;
	global $conf_ftp_backup_pass;
	global $conf_ftp_backup_frequency;

	$ftp_login_cfg = "host $conf_ftp_backup_host
user $conf_ftp_backup_login
pass $conf_ftp_backup_pass
";

	$filep = fopen("$conf_generated_file_path/ncftpput_login.cfg", "w+");
	if( $filep == NULL){
		echo("Cannot open file for writting");
	}
	fwrite($filep,$ftp_login_cfg);
	fclose($filep);
	system("chown $conf_dtc_system_username \"$conf_generated_file_path/ncftpput_login.cfg\"");
}

function backup_script_generate(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;

	global $conf_generated_file_path;
	global $conf_backup_script_path;
	global $conf_bakcup_path;

	global $console;

	$num_generated_vhosts=0;

	backup_by_ftp();
	
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

//	if($num_rows < 1){
//		die("No account to generate");
//	}

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
			echo("No user of that name: $web_owner!");
			continue;
		}
		$webadmin = mysql_fetch_array($result2) or die ("Cannot fetch user");
		$web_path = $webadmin["path"];

		// Grab all subdomains
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);
// Very bad idea: the script should never dies
//		if($num_rows2 < 1){
//			die("No subdomain for domain $web_name !");
//		}
		for($j=0;$j<$num_rows2;$j++){
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
			$web_subname = $subdomain["subdomain_name"];

			// Variable to use : $web_name $web_owner $web_subname
			$backup_script .= "
if [ -f today/$web_owner/$web_subname.$web_name.tar.gz ] ; then
	tar -uzf today/$web_owner/$web_subname.$web_name.tar.gz $web_path/$web_name/subdomains/$web_subname
else
	tar -czf today/$web_owner/$web_subname.$web_name.tar.gz $web_path/$web_name/subdomains/$web_subname
fi
";
		}
		$num_generated_vhosts += $num_rows2;
	}

	// Ecriture du fichier
	$filep = fopen("$conf_generated_file_path/$conf_backup_script_path", "w+");
	if( $filep == NULL){
		echo("Cannot open file for writting");
	}
	fwrite($filep,$backup_script);
	fclose($filep);
	chmod("$conf_generated_file_path/$conf_backup_script_path",0750);
	$console .= "Generated backup files for $num_generated_vhosts vhosts !<br>";
	return true;



}

?>
