#!/bin/sh

# This is the DTC's tarball interactive install configuration script
# made by Thomas Goirand <thomas [ at ] goirand.fr>

UNIX_TYPE=gentoo

VERBOSE_INSTALL=yes

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
if [ -z ""$conf_mysql_host ];
then
	conf_mysql_host="localhost"
fi

echo -n 'MySQL root login [root]: '
read conf_mysql_login
if [ -z ""$conf_mysql_login ];
then
	conf_mysql_login="root"
fi

echo -n 'MySQL root password []: '
read conf_mysql_pass

echo -n 'Choose a DB name for DTC [dtc]: '
read conf_mysql_db
if [ -z ""$conf_mysql_db ];
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

if [ -z ""$conf_mta_type ] ; then
	conf_mta_type="p"
fi

if [ "$conf_mta_type" = "p" ] ; then
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
echo -n "Domain name (example: super-mega-domain.co.tld): "
read main_domain_name
if [ -z ""$main_domain_name ];
then
	main_domain_name="super-mega-domain.co.tld"
fi

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

if [ -z ""$conf_ip_addr ];
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
	conf_use_nated_vhosts = "yes";
	echo "Nated vhosts will be used"
else
	conf_use_nated_vhosts = "no";
	echo "Nated vhosts won't be used"
fi

echo ""
echo " Please enter the LAN IP of your server if you said"
echo "yes to use nated vhosts. Ignore otherwise."
echo -n "IP address of your server if in the LAN [192.168.0.2]: "
read conf_nated_vhost_ip
if [ -z ""$conf_nated_vhosts_ip ]; then
	conf_nated_vhosts_ip = "192.168.0.2"
fi

echo ""
echo "Where will you keep your files for hosting ?"
echo -n "Hosting path [/var/www/sites]: "
read conf_hosting_path
if [ -z ""$conf_hosting_path ];
then
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
if [ -z ""$conf_chroot_path ];
then
	conf_chroot_path="/var/www/chroot"
fi


echo ""
echo "What admin login/pass you want for the administration of "$main_domain_name "?"
echo -n "Login [dtc]: "
read conf_adm_login
if [ -z ""$conf_adm_login ];
then
	conf_adm_login="dtc"
fi
echo -n "Password: "
read conf_adm_pass

PATH_HTTPD_CONF="/etc/apache2/httpd.conf"
PATH_NAMED_CONF="/etc/bind/named.conf"
PATH_POSTFIX_ETC="/etc/postfix"
PATH_AWSTATS_ETC="/etc/awstats"
PATH_POSTFIX_CONF="${PATH_POSTFIX_ETC}/main.cf"
PATH_SASL_PASSWD2="/usr/sbin/saslpasswd2"
PATH_COURIER_CONF_PATH="/etc/courier"
PATH_DOVECOT_CONF="/etc/dovecot.conf"
PATH_PROFTPD_CONF="/etc/proftpd/proftpd.conf"
PATH_QMAIL_CTRL="/var/qmail/control"
PATH_PHP_CGI="/usr/bin/php"
PATH_DTC_SHARED="/usr/share/dtc"
PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"
PATH_DTC_ETC="${PATH_DTC_SHARED}/etc"
PATH_CRONTAB_CONF=/etc/crontab

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

if [ ""$valid_infos =  'y' ];
then
	echo "Installation has started..."
else
	echo "Configuration not validated : exiting !"
	exit 1
fi

echo "Copying DTC's php scripts to /usr/share..."

cp -rf dtc /usr/share
