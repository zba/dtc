
# Multi OS (Unix system) DATABASE setup sh script for DTC
# Written by Thomas GOIRAND <thomas [ at ] goirand.fr>
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

if [ -z "$MKTEMP" ] ; then
	MKTEMP="mktemp -t"
fi

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "DTC is configuring your services: please wait..."
	echo "DTC installer is in VERBOSE mode"
else
	echo -n "DTC is configuring your services: please wait..."
fi

# Create hosting directories for main site
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "==> Creating directory for hosting "$main_domain_name
fi
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/"$dtc_admin_subdomain"/html"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/"$dtc_admin_subdomain"/logs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/"$dtc_admin_subdomain"/cgi-bin"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/html"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/logs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/cgi-bin"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/404/html"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/404/logs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/404/cgi-bin"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/Mailboxs"
mkdir -p $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/mysql"
ADMIN_HOME=$conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/"$dtc_admin_subdomain"/html"
if ! [ -e $ADMIN_HOME"/index.php" ] ;then
	cp $PATH_DTC_SHARED"/shared/default_admin_site.php" $ADMIN_HOME"/index.php"
	if ! [ -e $ADMIN_HOME"/dtc_logo.gif" ] ;then
		cp $PATH_DTC_SHARED"/shared/template/dtc_logo.gif" $ADMIN_HOME
	fi
	if ! [ -e $ADMIN_HOME"/favicon.ico" ] ;then
		cp $PATH_DTC_SHARED"/shared/template/favicon.ico" $ADMIN_HOME
	fi
fi

# Copy a template site to the new main site
MAINSITE_HOME=$conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/html"
if ! [ -e $MAINSITE_HOME"/index.php" ] ;then
	if ! [ -e $MAINSITE_HOME"/index.html" ] ;then
		cp $PATH_DTC_SHARED"/shared/template/index.php" $MAINSITE_HOME
		if ! [ -e $MAINSITE_HOME"/dtc_logo.gif" ] ;then
			cp $PATH_DTC_SHARED"/shared/template/dtc_logo.gif" $MAINSITE_HOME
		fi
	fi
fi

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo chown -R nobody:65534 $conf_hosting_path
fi
chown -R nobody:65534 $conf_hosting_path

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "==> DTC is now creating it's database:"
fi
# Added for MacOS X support with mysql not in the path...
if [ ""$conf_mysql_cli_path = "" ] ;then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "mysql_cli_path is not set"
	fi
	conf_mysql_cli_path="mysql";
fi
if [ ""$conf_mysqlshow_cli_path = "" ] ;then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "mysqlshow_cli_path is not set"
	fi
	conf_mysqlshow_cli_path="mysqlshow";
fi
if [ "$conf_mysql_pass" = "" ];
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Setting up mysql cli "$conf_mysql_cli_path" without password"
	fi
        MYSQL=""$conf_mysql_cli_path
	MYSQLSOW=$conf_mysqlshow_cli_path
else
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Setting up mysql cli with password"
	fi
#	MYSQL=""$conf_mysql_cli_path "-p"$conf_mysql_pass
	MYSQL=$conf_mysql_cli_path" -p${conf_mysql_pass}"
	MYSQLSHOW=$conf_mysqlshow_cli_path" -p${conf_mysql_pass}"
fi


create_tables=$PATH_DTC_SHARED"/admin/tables"
curdir=`pwd`

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "If not exists, create DTC's database name: "$conf_mysql_db
fi
$MYSQL -u$conf_mysql_login -h$conf_mysql_host --execute="CREATE DATABASE IF NOT EXISTS "$conf_mysql_db
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "Creating apachelogs database: apachelogs"
fi
$MYSQL -u$conf_mysql_login -h$conf_mysql_host --execute="CREATE DATABASE IF NOT EXISTS apachelogs"

cd $create_tables
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo -n "DTC is now creating table if not exists: "
fi
for i in $( ls *.sql );
do
	table_name=`echo $i | cut -f1 -d"."`
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo -n $table_name" "
	fi
	table_create=`cat $i`
	$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db <$i
done
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "done."
fi
#echo $PATH_PHP_CGI $PATH_DTC_ADMIN/restor_db.php -u $conf_mysql_login -h $conf_mysql_host -d $conf_mysql_db $conf_mysql_pass
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/restor_db.php -u $conf_mysql_login -h $conf_mysql_host -d $conf_mysql_db "$conf_mysql_pass"
else
	cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/restor_db.php -u $conf_mysql_login -h $conf_mysql_host -d $conf_mysql_db "$conf_mysql_pass" >/dev/null
fi
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "Inserting values in mysql for hosting "$main_domain_name
fi
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO groups (members) VALUES ('zigo')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO admin (adm_login,adm_pass,path) VALUES ('"$conf_adm_login"','"$conf_adm_pass"','"$conf_hosting_path"/"$conf_adm_login"')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO domain (name,owner,default_subdomain,generate_flag,ip_addr) VALUES ('"$main_domain_name"','"$conf_adm_login"','www','yes','"$conf_ip_addr"')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain (domain_name,subdomain_name,path) VALUES ('"$main_domain_name"','www','www')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain (domain_name,subdomain_name,path) VALUES ('"$main_domain_name"','404','404')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain(domain_name,subdomain_name,ip) VALUES ('"$main_domain_name"','ns1','$conf_ip_addr')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain(domain_name,subdomain_name,ip) VALUES ('"$main_domain_name"','mx','$conf_ip_addr')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO subdomain (domain_name,subdomain_name,path) VALUES ('"$main_domain_name"','"$dtc_admin_subdomain"','www')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO config (unicrow,demo_version,main_site_ip,site_addrs,addr_mail_server,webmaster_email_addr,addr_primary_dns,administrative_site,site_root_host_path,generated_file_path,dtcshared_path,dtcadmin_path,dtcclient_path,mta_type,main_domain,404_subdomain) VALUES('1','no','"$conf_ip_addr"','"$conf_ip_addr"','mx."$main_domain_name"','webmaster@"$main_domain_name"','ns1."$main_domain_name"','"$dtc_admin_subdomain"."$main_domain_name"','"$conf_hosting_path"','"$PATH_DTC_ETC"','"$PATH_DTC_SHARED"','"$PATH_DTC_ADMIN"','"$PATH_DTC_CLIENT"','"$conf_mta_type"','"$main_domain_name"','404')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO cron_job (unicrow,reload_named,restart_apache,gen_vhosts,gen_named) VALUES ('1','yes','yes','yes','yes')"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO secpayconf (unicrow,use_paypal,paypal_rate,paypal_flat,paypal_autovalidate,paypal_email) VALUES ('1','yes','3.21','0.50','no','webmaster@"$main_domain_name"')"

# Regenerate the "main" domain on each installs...
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE domain SET generate_flag='yes' WHERE name='"$main_domain_name"'"

# This one is in case of reinstalltion, so the installer has prority to old values
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE config SET main_site_ip='"$conf_ip_addr"',administrative_site='"$dtc_admin_subdomain"."$main_domain_name"',site_root_host_path='"$conf_hosting_path"',generated_file_path='"$PATH_DTC_ETC"',mta_type='"$conf_mta_type"',main_domain='"$main_domain_name"',404_subdomain='404' WHERE 1"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE cron_job SET qmail_newu='yes',restart_qmail='yes',gen_qmail='yes',reload_named='yes',restart_apache='yes',gen_vhosts='yes',gen_named='yes' WHERE 1"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE config SET php_library_path='/usr/lib/php:/tmp:/usr/share/pear:$PATH_DTC_ETC/dtc404' WHERE 1"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE domain SET  ip_addr='"$conf_ip_addr"', generate_flag='yes' WHERE name='"$main_domain_name"'"

# Here are some DB maintainance for old DTC versions
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER TABLE subdomain CHANGE ip ip VARCHAR(255) DEFAULT 'default' NOT NULL"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER TABLE ftp_access CHANGE homedir homedir VARCHAR(255) DEFAULT '' NOT NULL"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER TABLE pop_access CHANGE crypt crypt VARCHAR(255) DEFAULT '' NOT NULL"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER TABLE pop_access CHANGE passwd passwd VARCHAR(255) DEFAULT '' NOT NULL"
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE pop_access SET crypt=ENCRYPT(passwd,CONCAT(\"\$1\$\",SUBSTRING(crypt,4,8)))"

# Add dtc userspace info to mysql db if it's not there
TMP_FILE=`${MKTEMP} dtc_downer_grep.XXXXXXXX`  || exit 1
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -Dmysql --execute="DESCRIBE user dtcowner" >${TMP_FILE}
if ! grep dtcowner ${TMP_FILE} 2>&1 >/dev/null ;then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Adding dtcowner column to mysql.user"
	fi
	$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE mysql.user ADD dtcowner varchar (255) DEFAULT 'none' NOT NULL"
fi
if [ -e ${TMP_FILE} ] ;then
	rm ${TMP_FILE}
fi

# 2005/05/05 Remove bad keys preventing good accounting set in old dtc versions
# This needs to be fixed with no error. Any idea???
#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE smtp_logs DROP INDEX sender_domain"
#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE smtp_logs DROP INDEX delivery_domain"
#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE smtp_logs DROP INDEX delivery_id_text"
#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE smtp_logs DROP INDEX delivery_id_text_2"
#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE http_accounting DROP INDEX month"
#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE email_accounting DROP INDEX sender_domain"
#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE email_accounting DROP INDEX delivery_domain"

# Add a fullemail field to the pop table if not exists.
TMP_FILE=`${MKTEMP} dtc_pop_access_grep.XXXXXXXX`  || exit 1
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="DESCRIBE pop_access fullemail" >${TMP_FILE}
if ! grep fullemail ${TMP_FILE} 2>&1 >/dev/null ;then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Adding fullemail column to dtc.pop_access and updating id@mbox_host field."
	fi
	if $MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="ALTER IGNORE TABLE pop_access ADD fullemail varchar (255) DEFAULT 'none' NOT NULL" ; then
		echo "plop !"
	fi
	$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE pop_access SET fullemail = concat( id,  \"@\", mbox_host )"
fi
if [ -e ${TMP_FILE} ] ;then
	rm ${TMP_FILE}
fi

# Add a dtc user to the mysql db, generate a password randomly if no password is there already
# Using a file to remember password...
PATH_DB_PWD_FILE=${PATH_DTC_ETC}/dtcdb_passwd
if ! [ -e ""${PATH_DB_PWD_FILE} ] ;then
	MYSQL_DTCDAEMONS_PASS=`echo ${RANDOM}${RANDOM}`
	echo ${MYSQL_DTCDAEMONS_PASS} >${PATH_DB_PWD_FILE}
else
	MYSQL_DTCDAEMONS_PASS=`cat <${PATH_DB_PWD_FILE}`
fi
if [ -z "${MYSQL_DTCDAEMONS_PASS}" ] ;then
	MYSQL_DTCDAEMONS_PASS=${RANDOM}${RANDOM}
	echo ${MYSQL_DTC_PASS} >${PATH_DB_PWD_FILE}
fi

chmod 600 ${PATH_DB_PWD_FILE}

# Inserting the user
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.user (Host, User, Password, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', 'dtcdaemons', PASSWORD('"${MYSQL_DTCDAEMONS_PASS}"'), 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N')"

# Update the password in case of (bad) reinstallation case
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE mysql.user SET Password=PASSWORD('"${MYSQL_DTCDAEMONS_PASS}"') WHERE User='dtcdaemons'"

# grant Select,Insert,Update,Delete,References,Index to ftp_access
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'ftp_access', '', NOW(NULL), 'Select,Insert,Update,Delete,References,Index', 'Select')"

# grant Select,Insert,Update,Delete,References,Index to ftp_access
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'groups', '', NOW(NULL), 'Select,Insert,Update,Delete,References,Index', 'Select')"

# grant Select,Insert,Update,Delete,References,Index to ftp_logs
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'ftp_logs', '', NOW(NULL), 'Select,Insert,Update,Delete,References,Index', '')"

# grant Select,Insert,Update,Delete,References,Index to ftp_accounting
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'ftp_accounting', '', NOW(NULL), 'Select,Insert,Update,Delete,References,Index', '')"

# grant all to apachelogs
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Grant_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', 'apachelogs', 'dtcdaemons', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'N', 'Y', 'Y', 'Y')"

# grant select to pop_access 
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'pop_access', '', NOW(NULL), 'Select,Update', 'Select,Update')"
# update in case of old installations
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE IGNORE mysql.tables_priv SET Timestamp = NOW(NULL) , Table_priv = 'Select,Update', Column_priv = 'Select,Update' WHERE Host = 'localhost' AND Db = 'dtc' AND User = 'dtcdaemons' AND Table_name = 'pop_access' LIMIT 1 "

#$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="GRANT SELECT , UPDATE ( crypt , passwd ) ON dtc.pop_access TO 'dtcdaemons'@'localhost'"

# grant Select,Insert,Update,Delete,References,Index to smtp_logs
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'smtp_logs', '', NOW(NULL), 'Select,Insert,Update,Delete,References,Index', '')"

# grant select to whitelist
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'whitelist', '', NOW(NULL), 'Select', 'Select')"

# grant select to fetchmail
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="INSERT IGNORE INTO mysql.tables_priv (Host, Db, User, Table_name, Grantor, Timestamp, Table_priv, Column_priv) VALUES ('localhost', 'dtc', 'dtcdaemons', 'fetchmail', '', NOW(NULL), 'Select', 'Select')"

$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="FLUSH PRIVILEGES"

# Setup good values depending on Unix distribution
$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE config SET dtcadmin_path='${PATH_DTC_ADMIN}', dtcclient_path='${PATH_DTC_CLIENT}', dtcdoc_path='${PATH_DTC_SHARED}/doc', dtcemail_path='${PATH_DTC_SHARED}/email' WHERE 1"

# Add the config for nated vhosts if needed
if [ ""$conf_use_nated_vhosts = "yes" ] ;then
	$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE config SET use_nated_vhost='yes'"
	$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE config SET nated_vhost_ip='"${nated_vhost_ip}"'"
else
	$MYSQL -u$conf_mysql_login -h$conf_mysql_host -D$conf_mysql_db --execute="UPDATE config SET use_nated_vhost='no'"
fi

# The panel needs root access (it does database management)
echo "<?php" > $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_host=\""$conf_mysql_host"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_login=\""$conf_mysql_login"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_pass=\""$conf_mysql_pass"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_db=\""$conf_mysql_db"\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "\$conf_mysql_conf_ok=\"yes\";" >> $PATH_DTC_SHARED"/shared/mysql_config.php"
echo "?>" >> $PATH_DTC_SHARED"/shared/mysql_config.php"

cd $curdir
