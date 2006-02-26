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
if [ ""$completed_steps = "" -o ""$completed_steps = "n" ]; then
	echo "Please come back later..."
	exit 1
fi 

# DATABASE CONFIGURATION
echo "### MYSQL CONFIGURATION ###"
echo ""
echo "DTC needs to access to your mysql database"
echo "Please give your mysql account information"
echo -n 'MySQL hostname [localhost]: '
read conf_mysql_host
if [ ""$conf_mysql_host = "" ] ; then
	conf_mysql_host="localhost"
fi

echo -n 'MySQL root login [root]: '
read conf_mysql_login
if [ ""$conf_mysql_login = "" ] ; then
	conf_mysql_login="root"
fi

echo -n 'MySQL root password []: '
read conf_mysql_pass

echo ""
echo "Do you want that DTC setup this password"
echo "for you ? (eg: UPDATE user SET Password=PASSWORD('XXX')...)"
echo -n 'Setup the mysql password [Ny]: '
read conf_mysql_change_root

if [ ""$conf_mysql_change_root = "y" ]; then
	echo "===> Changing MySQL Root password"
	echo "MySQL will now prompt your for the password to connect to"
	echo "the database. This is the OLD password that was there before"
	echo "you launched this script. If you didn't setup a root pass for"
	echo "mysqld, just hit ENTER to use empty pass."
	mysql -u$conf_mysql_login -p -h$conf_mysql_host -Dmysql --execute="UPDATE user SET Password=PASSWORD('"$conf_mysql_pas"') WHERE User='root'; FLUSH PRIVILEGES;";
else
	echo "Skinping MySQL password root change!"
fi


echo -n 'Choose a DB name for DTC [dtc]: '
read conf_mysql_db
if [ ""$conf_mysql_db = "" ] ; then
	conf_mysql_db="dtc"
fi

echo ""
echo "What MTA (Mail Tranport Agent, the one that"
echo "will route and deliver your incoming mail) do"
echo "you wish to use with DTC ? Type q for qmail"
echo "or type p for postfix."
echo -n 'MTA type (Qmail or Postfix) [Q/p]: '
read conf_mta_type
if [ ""$conf_mta_type = "p" ];
then
	conf_mta_type=postfix
	echo "Postfix will be used"
else
	conf_mta_type=qmail
	echo "Qmail will be used"
fi

# Host configuration
cur_ip_addr=`ifconfig | head -n 2 | tail -n 1 | cut -f2 -d":" | cut -f1 -d" "`
echo "### YOUR SERVER CONFIGURATION ###"
echo ""
echo "Please enter the main domain name you will use."
echo "DTC will install the root admin panel on that host."
echo -n "Domain name (example: toto.com): "
read main_domain_name

echo ""
echo "DTC will install a root admin panel on a subdomain"
echo "of the domain you just provided. The default subdomain"
echo "is dtc, which leeds you to http://dtc."$main_domain_name"/"
echo "You can enter another subdomain name if you want."
echo -n 'Subdomain for DTC admin panel [dtc]: '
read dtc_admin_subdomain


if [ ""$dtc_admin_subdomain = "" ] ; then
	dtc_admin_subdomain="dtc"
fi

echo ""
echo "I need now you host information for apache !"
echo -n "What is your IP addresse ? ["$cur_ip_addr"]: "
read conf_ip_addr

if [ ""$conf_ip_addr = "" ];
then
	conf_ip_addr=$cur_ip_addr
fi

echo ""
echo " Do you want that DTC generates apache file to use"
echo "a LAN IP address that your server is using?"
echo "If your server is in the LAN behind a firewall"
echo "that does NAT and port redirections of the public IP(s)"
echo "address(es) to your server, then you must say YES"
echo "here, otherwise (if your server is connected directly"
echo "to the internet with a public static IP) leave it to NO."
echo -n "Use NATed vhosts ? [N/y]: "
read conf_use_nated_vhosts

if [ ""$conf_use_nated_vhosts = "y" -o ""$conf_use_nated_vhosts = "Y" -o ""$conf_use_nated_vhosts = "yes" ]; then
	conf_use_nated_vhosts="yes";
else
	conf_use_nated_vhosts="no";
fi

echo ""
echo " Please enter the LAN IP of your server if you said"
echo "yes to use nated vhosts. Ignore otherwise."
echo -n "IP address of your server if in the LAN [192.168.0.2]: "
read conf_nated_vhost_ip
if [ ""$conf_nated_vhosts_ip = "" ]; then
	conf_nated_vhosts_ip="192.168.0.2"
fi


echo ""
echo "Where will you keep your files for hosting ?"
echo -n "Hosting path [/var/www/sites]: "
read conf_hosting_path
if [ ""$conf_hosting_path = "" ] ; then
	conf_hosting_path="/var/www/sites"
fi

echo ""
echo "Path where to build the chroot environment."
echo "Where do you want DTC to build the cgi-bin chroot"
echo "environment? Please note that DTC will do hardlinks"
echo "to that directory, so the chroot path should be in"
echo "the same logical device as the path for hosted"
echo "domains files."
echo -n "Chroot path [/var/www/chroot]: "
read conf_chroot_path
if [ ""$conf_chroot_path = "" ] ; then
	conf_chroot_path="/var/www/chroot"
fi

echo ""
echo "What admin login/pass you want for the administration of "$main_domain_name "?"
echo -n "Login [dtc]: "
read conf_adm_login
if [ ""$conf_adm_login = "" ] ; then
	conf_adm_login="dtc"
fi
echo -n "Password: "
read conf_adm_pass

if [ -z "$conf_eth2monitor" ] ; then
	NBRLINES=`grep -v "lo:" /proc/net/dev | wc -l`
	NBRIFACE=$((${NBRLINES} - 2 ))
	CNT=${NBRIFACE}
	ALL_IFACES=''
	while [ ${CNT} -gt 0 ] ; do
		ALL_IFACES=${ALL_IFACES}' '`grep -v "lo:" /proc/net/dev | tail -n ${CNT} | cut -f 1 -d':' | gawk -F ' ' '{print $1}' | head -n 1`
		CNT=$((${CNT} - 1 ))
	done
	echo ""
	echo "DTC will setup an RRDTools graphing system for you, please"
	echo "enter all the interfaces you wish to see in the total traffic."
	echo -n 'Enter the iface you wish to monitor ['$ALL_IFACES']: '
	read conf_eth2monitor
	if [ -z "$conf_eth2monitor" ];
	then
		conf_eth2monitor=$ALL_IFACES
	fi
fi

# Deamon path configuration

echo "### DEAMON PATH CONFIGURATION ###"

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

echo ""
echo ""
echo ""
echo ""
echo ""
echo "### Last confirmation before installation !!! ###"
echo ""
echo "Here are the given informations:"
echo ""
echo "MySQL host: "$conf_mysql_host
echo "MySQL login: "$conf_mysql_login
echo "MySQL pass: "$conf_mysql_pass
echo "MySQL db: "$conf_mysql_db
echo "Addresse of dtc panel: http://"$dtc_admin_subdomain"."$main_domain_name"/"
echo "IP addr: "$conf_ip_addr
echo "Hosting path: "$conf_hosting_path
echo "DTC login: "$conf_adm_login
echo "DTC pass: "$conf_adm_pass
echo "httpd.conf: "$PATH_HTTPD_CONF
echo "named.conf: "$PATH_NAMED_CONF
echo "proftpd.conf: "$PATH_PROFTPD_CONF
echo "dovecot.conf: "$PATH_DOVECOT_CONF
echo "Courier config path: "$PATH_COURIER_CONF_PATH
echo "postfix/main.cf: "$PATH_POSTFIX_CONF
echo "qmail control: "$PATH_QMAIL_CTRL
echo "php4 cgi: "$PATH_PHP_CGI
echo "generated files: "$PATH_DTC_ETC
echo ""
echo -n 'Confirm and install DTC ? [Ny]:'
read valid_infos

if [ ""$valid_infos = "y" ] ; then
	echo "Installation has started..."
else
	echo "Configuration not validated : exiting !"
	exit
fi

if [ -e /var/lib/php/session ] ; then
	chgrp -R nobody /var/lib/php/session
fi

MKTEMP="mktemp -p /tmp"

MYSQL_DB_SOCKET_PATH="/var/lib/mysql/mysql.sock"

