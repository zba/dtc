#!/bin/sh

# This is the DTC's BSD interactive install configuration script
# called by the make install
# made by Thomas Goirand <thomas@goirand.fr>

PREFIX=$1
WRKSRC=$2

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
if [ $conf_mysql_host == ""];
then
	conf_mysql_host="localhost"
fi

echo -n 'MySQL root login [root]: '
read conf_mysql_login
if [ $conf_mysql_login == ""];
then
	conf_mysql_login="root"
fi

echo -n 'MySQL root password []: '
read conf_mysql_pass

echo -n 'Choose a DB name for DTC [dtc]: '
read conf_mysql_db
if [ $conf_mysql_db == ""];
then
	conf_mysql_db="dtc"
fi

# Host configuration
# cur_ip_addr=`ifconfig | head -n 2 | tail -n 1 | cut -f2 -d":" | cut -f1 -d" "`
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


if [ $dtc_admin_subdomain == ""];
then
	dtc_admin_subdomain="dtc"
fi

echo ""
echo "I need now your host information for apache !"
echo -n "What is your IP addresse ? [ 127.0.0.1 ]: "
read conf_ip_addr

if [ $conf_ip_addr == ""];
then
	conf_ip_addr="127.0.0.1"
fi

echo ""
echo "Where will you keep your files for hosting ?"
echo -n "Hosting path [/var/www/sites]: "
read conf_hosting_path
if [ $conf_hosting_path == ""];
then
	conf_hosting_path="/var/www/sites"
fi

echo ""
echo "What admin login/pass do you want for the administration of "$main_domain_name "?"
echo -n "Login [dtc]: "
read conf_adm_login
if [ $conf_adm_login == ""];
then
	conf_adm_login="dtc"
fi
echo -n "Password: "
read conf_adm_pass

# Deamon path configuration

echo "### DEAMON PATH CONFIGURATION ###"
PATH_HTTPD_CONF="${PREFIX}/usr/local/etc/apache/httpd.conf"
PATH_NAMED_CONF="/etc/namedb/named.conf"
# Copy default conf if no conf exists (BSD specific)
if [ ! -f ${PREFIX}/etc/proftpd.conf ];
then
	cp ${PREFIX}/etc/proftpd.conf.default ${PREFIX}/etc/proftpd.conf
fi
PATH_PROFTPD_CONF="${PREFIX}/etc/proftpd.conf"
PATH_QMAIL_CTRL="/var/qmail/control"
PATH_PHP_CGI="${PREFIX}/bin/php"
PATH_DTC_ETC="${PREFIX}/share/dtc/etc"
PATH_DTC_SHARED="${PREFIX}/share/dtc"

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
echo "qmail control: "$PATH_QMAIL_CTRL
echo "php4 cgi: "$PATH_PHP_CGI
echo "generated files: "$PATH_DTC_ETC
echo ""
echo -n 'Confirm and install DTC ? [Ny]:'
read valid_infos

if [ $valid_infos =  "y" ];
then
	echo "Installation has started..."
else
	echo "Configuration not validated : exiting !"
	exit
fi

echo "Copying DTC's php scripts to ${PREFIX}/share..."

cp -Rf dtc ${PREFIX}/share

echo "===> Checking BSD type"
kernel=`uname -a | awk '{print $1}'`;
echo "Kernel: $kernel"

if [ $kernel = "FreeBSD" ] || [ $kernel = "NetBSD" ];
then
	named=`grep  named_enable /etc/rc.conf`
	nonamed=`cat /etc/rc.conf | grep named  | awk '{print $1}' | grep NO`
	if [ "$named" = "" ] || [ "$nonamed" != "" ]; 
	then
		echo "===> FreeBSD or NetBSD: Backing up /etc/rc.conf and insterting named_enamble=YES"
		cp /etc/rc.conf /etc/rc.conf.old
		echo "/etc/rc.conf /etc/rc.conf.old saved"
		cat /etc/rc.conf | grep -v "named" >> /etc/rc.tmp
		echo 'named_enable="YES"              # Run named, the DNS server (or NO).' >> /etc/rc.tmp
		echo 'named_program="/usr/sbin/named" # path to named, if you want a different one.' >> /etc/rc.tmp
		echo '#named_flags="-u bind -g bind"  # Flags for named' >> /etc/rc.tmp
		mv /etc/rc.tmp /etc/rc.conf
		echo "named /etc/rc.conf injected"
	else
		echo "===> /etc/rc.conf is already configured: leaving..."
	fi
fi

if [ $kernel = "OpenBSD" ];
then
	flag=`grep named_flags=\"-c /etc/rc.conf`	
	echo "conf $flag"
	if [ "$flag" == "" ];
	then
		echo "===> OpenBSD: Backing up /etc/rc.conf and insterting named_flags=\"-c /etc/named.conf\""
		echo "/etc/rc.conf no named"
		cp /etc/rc.conf /etc/rc.conf.old
		echo "/etc/rc.conf /etc/rc.conf.old saved"
		cat /etc/rc.conf | grep -v "named_flags=NO" >> /etc/rc.tmp
		echo 'named_flags="-c /etc/named.conf"' >> /etc/rc.tmp
		mv /etc/rc.tmp /etc/rc.conf
		echo "named /etc/rc.conf injected"
		if [ ! -f /etc/named.conf ];
		then
			echo "no /etc/named.conf"
			if ! [ ! -f /var/named/etc/named.conf ];
			then
				cp /var/named/etc/named.conf /etc/named.conf 
				echo "/var/named/etc/named.conf /etc/named.conf copied" 
			else
				mv	/etc/rc.conf.old /etc/rc.conf
				echo 	"/etc/rc.conf.old /etc/rc.conf replaced"
				echo	"set named at your own configuration in /etc/rc.conf and in your named.conf"
			fi
			echo "conf named.conf done"
		fi
		echo "conf /etc/rc.conf done"
	fi
	echo "conf done"
fi

