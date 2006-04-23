#!/bin/sh

# This is the DTC's BSD interactive install configuration script
# called by the make install
# made by Thomas Goirand <thomas@goirand.fr> and Frederic Cambus


PREFIX=%%PREFIX%%
LOCALBASE=%%LOCALBASE%%
QMAIL_DIR=%%QMAIL_DIR%%

UNIX_TYPE=freebsd

VERBOSE_INSTALL=yes

# DATABASE CONFIGURATION
echo "### MYSQL CONFIGURATION ###"
echo ""
echo "WARNING: Your MySQL Server MUST be running."
echo "If not, please issue the following cmd:"
echo "/usr/local/etc/rc.d/mysql-server.sh start"
echo ""

# Deamon path configuration
echo "### DEAMON PATH CONFIGURATION ###"
if [ -f "${LOCALBASE}/etc/apache/httpd.conf" ] ;then
	PATH_HTTPD_CONF="${LOCALBASE}/etc/apache/httpd.conf"
elif [ -f "${LOCALBASE}/etc/apache2/httpd.conf" ] ;then
	PATH_HTTPD_CONF="${LOCALBASE}/etc/apache2/httpd.conf"
else
	echo "Could not found your httpd.conf: exiting."
	exit 1
fi
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
PATH_MAILDROP_BIN="${LOCALBASE}/bin/maildrop"
PATH_USERDB_BIN="${LOCALBASE}/sbin/userdb"
PATH_QMAIL_CTRL="${QMAIL_DIR}/control"
PATH_PHP_CGI="${LOCALBASE}/bin/php"
FREERADIUS_ETC="${LOCALBASE}/etc/raddb"
PATH_DTC_SHARED="${PREFIX}/www/dtc"
PATH_CRONTAB_CONF=/etc/crontab
PATH_AWSTATS_ETC=${LOCALBASE}/etc/awstats
MYSQL_DB_SOCKET_PATH="/tmp/mysqld.sock"

PATH_DTC_ETC=$PATH_DTC_SHARED"/etc"
PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"

USER_ADD_CMD=useradd
USER_ADD_CMD=groupadd
USER_MOD_CMD=usermod
PASSWD_CMD=passwd

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

# Here starts the interactive_installer.sh script
