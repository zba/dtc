#!/bin/sh

# This is the DTC's BSD interactive install configuration script
# called by the make install
# made by Thomas Goirand <thomas@goirand.fr> and Frederic Cambus


PREFIX=%%PREFIX%%
LOCALBASE=%%LOCALBASE%%
QMAIL_DIR=%%QMAIL_DIR%%

UNIX_TYPE=freebsd

VERBOSE_INSTALL=yes

echo "###############################################################"
echo "### Welcome to DTC config script for automatic installation ###"
echo "###############################################################"

# DATABASE CONFIGURATION
echo "### MYSQL CONFIGURATION ###"
echo ""
echo "WARNING: Your MySQL Server MUST be running."
echo "If not, please issue the following cmd:"
echo "/usr/local/etc/rc.d/mysql-server.sh start"
echo ""
echo "DTC needs to access to your mysql database"
echo "Please give your mysql account information"
echo "If you didn't setup a root password before,"
echo "DTC can do it of you (later on this script)."
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

echo "This is the password you want to be used after"
echo "this script is finished (not the old pass)."
echo -n 'MySQL root password []: '
read conf_mysql_pass

echo ""
echo "Do you want that DTC setup this password"
echo "for you ? (eg: UPDATE user SET Password=PASSWORD('XXX')...)"
echo -n 'Setup the mysql password [Ny]: '
read conf_mysql_change_root
if [ ""$conf_mysql_change_root = "y" ];
then
	echo "===> Changing MySQL Root password"
	echo "MySQL will now prompt your for the password to connect to"
	echo "the database. This is the OLD password that was there before"
	echo "you launched this script. If you didn't setup a root pass for"
	echo "mysqld, just hit ENTER to use empty pass."
	mysql -u$conf_mysql_login -p -h$conf_mysql_host -Dmysql --execute="UPDATE user SET Password=PASSWORD('"$conf_mysql_pass"') WHERE User='root'; FLUSH PRIVILEGES;";
else
	echo "Skinping MySQL password root change!"
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

echo -n 'Choose a DB name for DTC [dtc]: '
read conf_mysql_db
if [ "$conf_mysql_db" = "" ];
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


if [ "$dtc_admin_subdomain" = "" ];
then
	dtc_admin_subdomain="dtc"
fi

echo ""
echo "I need now your host information for apache !"
echo -n "What is your IP addresse ? [ 127.0.0.1 ]: "
read conf_ip_addr

if [ "$conf_ip_addr" = "" ];
then
	conf_ip_addr="127.0.0.1"
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
echo "the same logical device as the path for hosted"
echo "domains files."
echo -n "Chroot path [/var/www/chroot]: "
read conf_chroot_path
if [ "$conf_chroot_path" = "" ];
then
	conf_chroot_path="/var/www/chroot"
fi

echo ""
echo "What admin login/pass do you want for the administration of "$main_domain_name "?"
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
PATH_HTTPD_CONF="${LOCALBASE}/etc/apache/httpd.conf"
PATH_NAMED_CONF="/etc/namedb/named.conf"
# Copy default conf if no conf exists (BSD specific)
if [ ! -f ${LOCALBASE}/etc/proftpd.conf ];
then
	cp ${LOCALBASE}/etc/proftpd.conf.default ${LOCALBASE}/etc/proftpd.conf
fi
PATH_PROFTPD_CONF="${LOCALBASE}/etc/proftpd.conf"
PATH_DOVECOT_CONF="${LOCALBASE}/etc/dovecot.conf"
PATH_COURIER_CONF_PATH="${LOCALBASE}/etc/courier"
PATH_POSTFIX_CONF="${LOCALBASE}/etc/postfix/main.cf"
PATH_POSTFIX_ETC="${LOCALBASE}/etc/postfix"
PATH_SASL_PASSWD2="${LOCALBASE}/sbin/saslpasswd2"
PATH_QMAIL_CTRL="${QMAIL_DIR}/control"
PATH_PHP_CGI="${LOCALBASE}/bin/php"
FREERADIUS_ETC="${LOCALBASE}/etc/raddb"
PATH_DTC_SHARED="${PREFIX}/www/dtc"

PATH_DTC_ETC=$PATH_DTC_SHARED"/etc"
PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"


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
echo "Courier authdaemonrc: "$PATH_COURIER_CONF_PATH
echo "postfix/main.cf: "$PATH_POSTFIX_CONF
echo "qmail control: "$PATH_QMAIL_CTRL
echo "php4 cgi: "$PATH_PHP_CGI
echo "Freeradius sql.conf and rad.conf folder: "$FREERADIUS_ETC
echo "generated files: "$PATH_DTC_ETC
echo ""
echo ""
echo ""
echo -n 'Confirm and install DTC ? [Ny]:'
read valid_infos

if [ "$valid_infos" =  "y" ];
then
	echo "Installation has started..."
else
	echo "Configuration not validated : exiting !"
	exit 1
fi

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
	if [ "$flag" = "" ];
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

# Copy dist file if no php.ini is there yet...
if [ -e /usr/local/etc/php.ini-dist ] ; then
	if ! [ -e /usr/local/etc/php.ini ] ; then
		cp /usr/local/etc/php.ini-dist /usr/local/etc/php.ini
	fi
fi
# Check for pear in include path
if [ -f /usr/local/share/pear/PEAR.php ] ;then
	if [ -e /usr/local/etc/php.ini ] ; then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Checking include_path=/usr/local/share/pear in php.ini"
		fi
		if grep include_path /usr/local/etc/php.ini | grep /usr/local/share/pear > /dev/null
		then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Seems ok: skiping include_path insertion in php.ini"
			fi
		else
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Your php.ini doesn't has pear in it's inc path: changin!"
			fi
			echo "include_path = \".:/usr/local/share/pear\"" >>/usr/local/etc/php.ini
		fi
	fi
fi
