#!/bin/sh

# This is the DTC's tarball interactive install configuration script
# made by Thomas Goirand <thomas@goirand.fr>

UNIX_TYPE=generic

echo "###############################################################"
echo "### Welcome to DTC config script for automatic installation ###"
echo "###############################################################"

# DATABASE CONFIGURATION
echo "### MYSQL CONFIGURATION ###"
echo ""
echo "DTC needs to access to your mysql database"
echo "Please give your mysql account information"
echo -n 'MySQL hostname [localhost]: '
read conf_mysql_host
if [ "$conf_mysql_host" = "" ];
then
	conf_mysql_host="localhost"
fi

echo -n 'MySQL root login [root]: '
read conf_mysql_login
if [ "$conf_mysql_login" = "" ];
then
	conf_mysql_login="root"
fi

echo -n 'MySQL root password []: '
read conf_mysql_pass

echo -n 'Choose a DB name for DTC [dtc]: '
read conf_mysql_db
if [ "$conf_mysql_db" = "" ];
then
	conf_mysql_db="dtc"
fi

echo ""
echo "What MTA (Mail Tranport Agent, the one that"
echo "will route and deliver your incoming mail) do"
echo "you wish to use with DTC ? Type q for qmail"
echo "or type p for postfix."
echo -n 'MTA type (Qmail or Postfix) [Q/p]: '
read conf_mta_type
if [ "$conf_mta_type" = "p" ];
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


if [ "$dtc_admin_subdomain" = "" ];
then
	dtc_admin_subdomain="dtc"
fi

echo ""
echo "I need now you host information for apache !"
echo -n "What is your IP addresse ? ["$cur_ip_addr"]: "
read conf_ip_addr

if [ "$conf_ip_addr" = "" ];
then
	conf_ip_addr=$cur_ip_addr
fi

echo ""
echo "Where will you keep your files for hosting ?"
echo -n "Hosting path [/var/www/sites]: "
read conf_hosting_path
if [ "$conf_hosting_path" = "" ];
then
	conf_hosting_path="/var/www/sites"
fi

echo ""
echo "Path where to build the chroot environment."
echo "Where do you want DTC to build the cgi-bin chroot"
echo "environment? Please note that DTC will do hardlinks"
echo "to that directory, so the chroot path should be in"
echo "the same physical device as the path for hosted"
echo "domains files."
echo -n "Chroot path [/var/www/chroot]: "
read conf_chroot_path
if [ "$conf_chroot_path" = "" ];
then
	conf_chroot_path="/var/www/chroot"
fi


echo ""
echo "What admin login/pass you want for the administration of "$main_domain_name "?"
echo -n "Login [dtc]: "
read conf_adm_login
if [ "$conf_adm_login" = "" ];
then
	conf_adm_login="dtc"
fi
echo -n "Password: "
read conf_adm_pass

# Deamon path configuration

echo "### DEAMON PATH CONFIGURATION ###"

echo ""
echo "Where is located you httpd.conf ?"
echo -n "httpd.conf path [/usr/local/apache/httpd.conf]: "
read PATH_HTTPD_CONF
if [ "$PATH_HTTPD_CONF" = "" ];
then
	PATH_HTTPD_CONF="/usr/local/apache/httpd.conf"
fi

echo ""
echo "Where is located your named.conf ?"
echo -n "named.conf path [/etc/named.conf]: "
read PATH_NAMED_CONF
if [ "$PATH_NAMED_CONF" = "" ];
then
	PATH_NAMED_CONF="/etc/named.conf"
fi

echo ""
echo "Where is located your postfix/main.cf ?"
echo -n "postfix control path [/etc/postfix/main.cf]: "
read PATH_POSTFIX_CONF
if [ "$PATH_POSTFIX_CONF" = "" ];
then
	PATH_POSTFIX_CONF="/etc/postfix/main.cf"
fi

echo ""
echo "Where is located your courier config path (ie /etc/courier) ?"
echo -n "courier config path [/etc/courier]: "
read PATH_COURIER_CONF_PATH
if [ "$PATH_COURIER_CONF_PATH" = "" ];
then
	PATH_COURIER_CONF_PATH="/etc/courier"
fi

echo ""
echo "Where is located your courier config path ?"
echo -n "courier config path [/etc/courier]: "
read PATH_COURIER_CONF_PATH
if [ "$PATH_COURIER_CONF_PATH" = "" ];
then
	PATH_COURIER_CONF_PATH="/etc/courier"
fi

echo ""
echo "Where is located your dovecot.conf ?"
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

echo ""
echo "Where is located your qmail control directory ?"
echo -n "Qmail control path [/var/qmail/control]: "
read PATH_QMAIL_CTRL
if [ "$PATH_QMAIL_CTRL" = "" ];
then
	PATH_QMAIL_CTRL="/var/qmail/control"
fi

echo ""
echo "Where is located your php4 cgi parser ?"
echo -n "php4 cgi path [/usr/local/bin/php4]: "
read PATH_PHP_CGI
if [ "$PATH_PHP_CGI" = "" ];
then
	PATH_PHP_CGI="/usr/local/bin/php4"
fi

# DTC's own path

echo "### DTC PATH ###"
echo ""
echo "Where do you want DTC be installed ?"
echo -n "DTC shared install folder [/usr/share/dtc]: "
read PATH_DTC_SHARED
if [ "$PATH_DTC_SHARED" = "" ];
then
	PATH_DTC_SHARED="/usr/share/dtc"
fi

PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"

echo ""
echo "Where do you want DTC to store it's generated configuration files ?"
echo -n "DTC generated files [/usr/share/dtc/etc]: "
read PATH_DTC_ETC
if [ "$PATH_DTC_ETC" = "" ];
then
	PATH_DTC_ETC="/usr/share/dtc/etc"
fi


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
echo "DTC shared folder:"$PATH_DTC_SHARED
echo "generated files: "$PATH_DTC_ETC
echo ""
echo -n 'Confirm and install DTC ? [Ny]:'
read valid_infos

if [ "$valid_infos" =  'y' ];
then
	echo "Installation has started..."
else
	echo "Configuration not validated : exiting !"
	exit 1
fi

echo "Copying DTC's php scripts to /usr/share..."

cp -rf dtc /usr/share



