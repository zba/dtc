#!/bin/sh

# This is the DTC's tarball interactive install configuration script
# made by Thomas Goirand <thomas [ at ] goirand.fr>

UNIX_TYPE=osx

VERBOSE_INSTALL="yes"

# Deamon path configuration
PATH_HTTPD_CONF="/etc/httpd/httpd.conf"
PATH_NAMED_CONF="/etc/named.conf"
PATH_CRONTAB_CONF="/etc/crontab"
PATH_POSTFIX_ETC="/etc/postfix"
PATH_AWSTATS_ETC="/etc/awstats"
PATH_CGIBIN="/usr/lib/cgi-bin"

PATH_POSTFIX_CONF="${PATH_POSTFIX_ETC}/main.cf"
PATH_SASL_PASSWD2=""
PATH_USERDB_BIN=""
PATH_MAILDROP_BIN=""
PATH_COURIER_CONF_PATH=""
PATH_COURIER_AUTHD_CONF_PATH=""
PAH_COURIER_AUTHD_CONF_PATH="/etc/authlib"
PATH_DOVECOT_CONF=""
PATH_PROFTPD_CONF=""
PATH_QMAIL_CTRL="/var/qmail/control"
PATH_PHP_CGI="/usr/bin/php"
PATH_DTC_SHARED="/usr/share/dtc"
PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"
PATH_DTC_ETC="/usr/share/dtc/etc"

mkdir -p /var/lib/dtc
DTC_SAVED_INSTALL_CONFIG=/var/lib/dtc/saved_install_config

echo "Copying dtc files in the /usr/share folder"
mkdir -p /usr/share/dtc
cp -rf usr /
cp -f install.sh /usr/sbin/dtc-install.sh
cp -f uninstall.sh /usr/sbin/dtc-uninstall
chmod +x /usr/sbin/dtc-install.sh /usr/sbin/dtc-uninstall.sh

# Mac OS X does not have php4 Crypt CBC as default, like other >= 4.3 php distributions
if ! [ -e /usr/lib/php/Crypt ] ; then
	cp -r Crypt /usr/lib/php;
fi

echo "done!"

# This adds the mod_log_sql module if needed
if ! [ -e /usr/libexec/httpd/mod_log_sql.so ] ; then
	cp mod_log_sql.so /usr/libexec/httpd/
	cp mod_log_sql_mysql.so /usr/libexec/httpd/
fi

# Under osx, it doesn't seem to exist, so we create it!
mkdir -p /usr/lib/cgi-bin

# This might help if you want to use the mysql4 provided by fink
# Since standard Mac OS X 10.0.3.7 server that I have here uses mysql 3 within
# php and that mysql comes as default on Mac OS X Server, I leave the standard one
# otherwise you will have to recompile php with mysql4 support.
#conf_mysql_cli_path=/usr/local/mysql/bin/mysql
if [ -x /usr/bin/mysql ] ; then
	conf_mysql_cli_path=/usr/bin/mysql
fi
if [ -x /usr/local/mysql/bin/mysql ] ; then
	conf_mysql_cli_path=/usr/local/mysql/bin/mysql
fi
if [ -z ""$conf_mysql_cli_path ] ; then
	echo "Cannot find the mysql cli binary in /usr/bin/mysql or in /usr/local/mysql/bin/mysql: exiting!"
	exit 1
fi

. /usr/share/dtc/admin/install/osx_config
. /usr/share/dtc/admin/install/interactive_installer
. /usr/share/dtc/admin/install/functions

interactiveInstaller
DTCinstallPackage
DTCsetupDaemons

