
# Multi OS (Unix system) DATABASE setup sh script for DTC
# Written by Thomas GOIRAND <thomas@goirand.fr>
# under LGPL Licence

# The configuration for all thoses variables must be written BEFORE this
# script. Do the start of the script for your operating system.
# I did mine for debian in debian/postinst

# Please note this script
# doeas not start with a :

#!/bin/sh

# because it's up to you to write it ! :)
# Do a "cat setup_mysql_db.sh >>your_os_setup_script"

# This is the setup script for
# MYSQL database initialisation procedure
# Written by Thomas Goirand <thomas@goirand.fr>


# Uses the following variables :
# "MySQL host: "$conf_mysql_host
# "MySQL login: "$conf_mysql_login
# "MySQL pass: "$conf_mysql_pass
# "MySQL db: "$conf_mysql_db
# "vhost: http://"$dtc_admin_subdomain"."$main_domain_name"/"
# "IP addr: "$conf_ip_addr
# "DTC login: "$conf_adm_login
# "DTC pass: "$conf_adm_pass
# "Hosting path: "$conf_hosting_path
# $PATH_DTC_ETC & $PATH_DTC_SHARED

echo "==> Creating directory for hosting "$main_domain_name
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/"$dtc_admin_subdomain"/www/html"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/"$dtc_admin_subdomain"/www/logs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/"$dtc_admin_subdomain"/www/cgi-bin"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/html"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/logs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/cgi-bin"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/Mailboxs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/mysql"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/dtc/html"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/dtc/logs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/dtc/cgi-bin"

chown -R nobody:nogroup $conf_hosting_path

echo "==> DTC is now creating it's database:"

create_tables=$PATH_DTC_SHARED"/admin/tables"
curdir=`pwd`

echo "If not exists, create DTC's database name: "$conf_mysql_db
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host --execute="CREATE DATABASE IF NOT EXISTS "$conf_mysql_db
cd $create_tables
echo -n "DTC is now creating table if not exists: "
for i in $( ls *.sql );
do
	table_name=`echo $i | cut -f1 -d"."`
	echo -n $table_name" "
	table_create=`cat $i`
#	mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="DROP TABLE IF EXISTS "$table_name
	mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db <$i
done
echo "done."

echo "Inserting values in mysql for hosting "$main_domain_name
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO groups (members) VALUES ('zigo')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO admin (adm_login,adm_pass,path) VALUES ('"$conf_adm_login"','"$conf_adm_pass"','"$conf_hosting_path"/"$conf_adm_login"')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO domain (name,owner,default_subdomain,generate_flag,ip_addr) VALUES ('"$main_domain_name"','"$conf_adm_login"','www','yes','"$conf_ip_addr"')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain (domain_name,subdomain_name,path) VALUES ('"$main_domain_name"','www','www')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain(domain_name,subdomain_name,ip) VALUES ('"$main_domain_name"','ns1','$conf_ip_addr')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain(domain_name,subdomain_name,ip) VALUES ('"$main_domain_name"','mx','$conf_ip_addr')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain (domain_name,subdomain_name,path) VALUES ('"$main_domain_name"','"$dtc_admin_subdomain"','www')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO config (unicrow,demo_version,main_site_ip,site_addrs,addr_mail_server,webmaster_email_addr,addr_primary_dns,administrative_site,site_root_host_path,generated_file_path,dtcshared_path,dtcadmin_path,dtcclient_path) VALUES('1','no','"$conf_ip_addr"','"$conf_ip_addr"','mx."$main_domain_name"','webmaster@"$main_domain_name"','ns1."$main_domain_name"','"$dtc_admin_subdomain"."$main_domain_name"','"$conf_hosting_path"','"$PATH_DTC_ETC"','"$PATH_DTC_SHARED"','"$PATH_DTC_ADMIN"','"$PATH_DTC_CLIENT"')"
mysql -u$conf_mysql_login -p$conf_mysql_pass -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO cron_job (unicrow,reload_named,restart_apache,gen_vhosts,gen_named) VALUES ('1','yes','yes','yes','yes')"

echo "<?php" > $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_host=\""$conf_mysql_host"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_login=\""$conf_mysql_login"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_pass=\""$conf_mysql_pass"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_db=\""$conf_mysql_db"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_conf_ok=\"yes\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "?>" >> $PATH_DTC_SHARED"/shared/mysql_config.php"

cd $curdir
