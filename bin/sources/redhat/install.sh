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
echo " - remove sendmail (rpm -e sendmail sendmail-cf)"
echo " - postfix [rebuild the src rpm if encountering issues] (make sure inet_interfaces is correct) or qmail"
echo " - mysql-server (and started)"
echo " - courier-maildrop (download from http://tusker.sg/redhat/RPMS/i386/maildrop-2.0.1-2.i386.rpm or build your own)"
echo " - courier-authlib courier-imap and courier-pop (as per courier-maildrop above)"
echo " - php-mysql php-gd php-imap"
echo " - pear (or php-pear) (and pear install Crypt_CBC)"
echo " - bind (and started)"
echo " - Add DAG apt repository, as per http://dag.wieers.com/home-made/apt/FAQ.php#B (if you don't already have the dag yum repository)"
echo " - mod_log_sql (from http://tusker.sg/redhat/RPMS/i386/mod_log_sql-1.100-2.dtc.i386.rpm) and enable the log_sql_module and log_sql_mysql_module module in /etc/httpd/conf.d/mod_log_sql.conf"
echo " - clamav (yum install clamav clamd)"
echo " - amavisd-new (yum install amavisd-new)"
echo " - proftpd (Download the source rpm from http://dag.wieers.com/packages/proftpd/ and rpmbuild --rebuild <file> --with mysql) NOTE: The source rpm is older than the apt-get one, so don't upgrade proftpd..."
echo " - libnss-mysql (build yourself from source (rpmbuild -ta libnss-mysql-1.5.tar.gz)"
echo " - chrootuid (as per courier-maildrop above)"
echo " - mlmmj (built from src.rpm, ie http://ftp.dulug.duke.edu/pub/fedora/linux/extras/development/SRPMS/mlmmj-1.2.11-5.fc6.src.rpm)"
echo " - mhonarc (build from src.rpm, ie http://ftp.dulug.duke.edu/pub/fedora/linux/extras/development/SRPMS/mhonarc-2.6.16-2.fc6.src.rpm)"
echo " - optionally SqWebMail"
echo -n "Have you completed the above steps (yN)?"
read completed_steps
if [ ""$completed_steps = "y" -o ""$completed_steps = "Y" -o ""$completed_steps = "yes" ]; then
	echo "Starting interactive installer..."
else
	echo "Please come back later..."
	exit 1
fi 

PATH_NAMED_CONF=/etc/named.conf
PATH_QMAIL_CTRL=/var/qmail/control
PATH_PHP_CGI=/usr/bin/php
PATH_PHP_INI_APACHE=/etc/php.ini
PATH_PROFTPD_CONF=/etc/proftpd.conf
PATH_DOVECOT_CONF=/etc/dovecot.conf
PATH_CRONTAB_CONF=/etc/crontab
PATH_COURIER_CONF_PATH=/etc/courier
PATH_COURIER_AUTHD_CONF_PATH=/etc/courier
if [ ! -f $PATH_COURIER_AUTHD_CONF_PATH/authdaemonrc ]; then
        if [ -f /etc/authlib/authdaemonrc ]; then
                PATH_COURIER_AUTHD_CONF_PATH="/etc/authlib"
        fi
fi
PATH_CYRUS_CONF=/etc/imapd.conf
PATH_POSTFIX_CONF=/etc/postfix/main.cf
PATH_POSTFIX_ETC=/etc/postfix
PATH_SASL_START_CONF=/etc/init.d/saslauthd
PATH_SASL_STARTUP=/etc/init.d/saslauthd
PATH_SASL_SOCKET=/var/spool/postfix/var/run/saslauthd/
PATH_SASL_PASSWD2=/usr/sbin/saslpasswd2
PATH_USERDB_BIN=/usr/sbin/userdb
PATH_MAILDROP_BIN=/usr/bin/maildrop
PATH_HTTPD_CONF=/etc/httpd/conf/httpd.conf
PATH_AWSTATS_ETC=/etc/awstats
PATH_AMAVISD_CONF=/etc/amavisd.conf
PATH_CLAMAV_CONF=/etc/clamd.conf
PATH_DTC_ETC=/usr/share/dtc/etc
PATH_DTC_SHARED=/usr/share/dtc
PATH_DTC_ADMIN=/usr/share/dtc/admin
PATH_DTC_CLIENT=/usr/share/dtc/client
PATH_CGIBIN=/var/www/cgi-bin

if [ -e /var/lib/php/session ] ; then
	chgrp -R nobody /var/lib/php/session
fi

MKTEMP="mktemp -p /tmp"
MYSQL_DB_SOCKET_PATH="/var/lib/mysql/mysql.sock"
