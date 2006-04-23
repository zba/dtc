#!/bin/sh
# This is the DTC's RPM interactive install configuration script
# made by Thomas Goirand <thomas [ at ] goirand.fr>

UNIX_TYPE=redhat

VERBOSE_INSTALL=yes

echo "###############################################################"
echo "### Welcome to DTC config script for automatic installation ###"
echo "###############################################################"

echo ""
echo "Required packages before this script is run:"
echo " - apt (yum install apt)"
echo " - postfix (make sure inet_interfaces is correct) or qmail"
echo " - mysql-server (and started)"
echo " - courier-maildrop (download from http://tusker.sg/redhat/RPMS/i386/maildrop-2.0.1-2.i386.rpm or build your own)"
echo " - php-mysql"
echo " - pear (or php-pear) (and pear install Crypt_CBC)"
echo " - bind (and started)"
echo " - Add DAG apt repository, as per http://dag.wieers.com/home-made/apt/FAQ.php#B (if you don't already have the dag yum repository)"
echo " - mod_log_sql (yum install mod_log_sql) and enable the log_sql_module and log_sql_mysql_module module in /etc/httpd/conf.d/mod_log_sql.conf"
echo " - clamav (yum install clamav)"
echo " - amavisd-new (yum install amavisd-new)"
echo " - proftpd (Download the source rpm from http://dag.wieers.com/packages/proftpd/ and rpmbuild --rebuild <file> --with mysql) NOTE: The source rpm is older than the apt-get one, so don't upgrade proftpd..."
echo " - courier-auth courier-imap and courier-pop (as per courier-maildrop above)"
echo " - optionally SqWebMail"
echo -n "Have you completed the above steps (yN)?"
read completed_steps
if [ ""$completed_steps = "y" -o ""$completed_steps = "Y" -o ""$completed_steps = "yes" ]; then
	echo "Starting interactive installer..."
else
	echo "Please come back later..."
	exit 1
fi 

PATH_HTTPD_CONF=/etc/httpd/conf/httpd.conf
PATH_CRONTAB_CONF=/etc/crontab
PATH_NAMED_CONF=/etc/named.conf
PATH_PROFTPD_CONF=/etc/proftpd.conf
PATH_DOVECOT_CONF=/etc/dovecot.conf
PATH_COURIER_CONF_PATH=/etc/courier
PATH_POSTFIX_CONF=/etc/postfix/main.cf
PATH_POSTFIX_ETC=/etc/postfix
PATH_AWSTATS_ETC=/etc/awstats
PATH_SASL_PASSWD2=/usr/sbin/saslpasswd2
PATH_USERDB_BIN=/usr/sbin/userdb
PATH_MAILDROP_BIN=/usr/bin/maildrop
PATH_QMAIL_CTRL=/var/qmail/control
PATH_PHP_CGI=/usr/bin/php
PATH_DTC_ETC=/usr/share/dtc/etc
PATH_DTC_SHARED=/usr/share/dtc
PATH_DTC_ADMIN=/usr/share/dtc/admin
PATH_DTC_CLIENT=/usr/share/dtc/client

if [ -e /var/lib/php/session ] ; then
	chgrp -R nobody /var/lib/php/session
fi

MKTEMP="mktemp -p /tmp"
MYSQL_DB_SOCKET_PATH="/var/lib/mysql/mysql.sock"
