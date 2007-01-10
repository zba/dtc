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

echo ""
echo "Where is saslpasswd2 located ?"
echo -n "saslpasswd2 location []: "
read PATH_SASL_PASSWD2
if [ "$PATH_SASL_PASSWD2" = "" ];
then
	PATH_SASL_PASSWD2=""
fi

echo ""
echo "Where is userdb (courier) binary located ?"
echo -n "userdb location []: "
read PATH_USERDB_BIN
if [ "$PATH_USERDB_BIN" = "" ];
then
	PATH_USERDB_BIN=""
fi

echo ""
echo "Where is maildrop (courier) binary located ?"
echo -n "maildrop location []: "
read PATH_MAILDROP_BIN
if [ "$PATH_MAILDROP_BIN" = "" ];
then
	PATH_MAILDROP_BIN=""
fi

echo ""
echo "Where is located your courier config path (ie /etc/courier) ?"
echo "If Courier is not installed, just hit enter."
echo -n "courier config path [/etc/courier]: "
read PATH_COURIER_CONF_PATH
if [ "$PATH_COURIER_CONF_PATH" = "" ];
then
	PATH_COURIER_CONF_PATH="/etc/courier"
fi

echo ""
echo "Where is located your courier authlib path (ie /etc/authlib) ?"
echo "If Courier is not installed, just hit enter."
echo -n "courier config path [/etc/authlib]: "
read PATH_COURIER_AUTHD_CONF_PATH
if [ "$PATH_COURIER_AUTHD_CONF_PATH" = "" ];
then
	PATH_COURIER_AUTHD_CONF_PATH="/etc/authlib"
fi

echo ""
echo "Where is located your dovecot.conf ?"
echo "If Dovecot is not installed, just hit enter."
echo -n "Dovecot control path [/etc/dovecot.conf]: "
read PATH_DOVECOT_CONF
if [ "$PATH_DOVECOT_CONF" = "" ];
then
	PATH_DOVECOT_CONF="/etc/dovecot.conf"
fi

echo ""
echo "Where is located your proftpd.conf ?"
echo -n "Proftpd control path [/etc/proftpd.conf]: "
read PATH_PROFTPD_CONF
if [ "$PATH_PROFTPD_CONF" = "" ];
then
	PATH_PROFTPD_CONF="/etc/proftpd.conf"
fi

PATH_QMAIL_CTRL="/var/qmail/control"
PATH_PHP_CGI="/usr/bin/php"

# DTC's own path

PATH_DTC_SHARED="/usr/share/dtc"
PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"
PATH_DTC_ETC="/usr/share/dtc/etc"

echo -n "Copying DTC's php scripts to /usr/share..."
mkdir -p /usr/share/dtc
cp -rf usr /
cp -f install.sh /usr/sbin/dtc-install.sh
cp -f uninstall.sh /usr/sbin/dtc-uninstall.sh
chmod +x /usr/sbin/dtc-install.sh /usr/sbin/dtc-uninstall.sh

# Mac OS X does not have php4 Crypt CBC as default, like other >= 4.3 php distributions
if ! [ -e /usr/lib/php/Crypt ] ; then
	cp -r Crypt /usr/lib/php;
fi

echo "done!"

# This adds the mod_log_sql module if needed
if ! [ -e /usr/libexec/httpd/mod_log_sql.so ] ; then
	cp mod_log_sql.so /usr/libexec/httpd/
fi

# This might help if you want to use the mysql4 provided by fink
# Since standard Mac OS X 10.0.3.7 server that I have here uses mysql 3 within
# php and that mysql comes as default on Mac OS X Server, I leave the standard one
# otherwise you will have to recompile php with mysql4 support.
#conf_mysql_cli_path=/usr/local/mysql/bin/mysql



. /usr/share/dtc/admin/install/osx_config
. /usr/share/dtc/admin/install/interactive_installer
. /usr/share/dtc/admin/install/functions

interactiveInstaller
DTCinstallPackage
DTCsetupDaemons

