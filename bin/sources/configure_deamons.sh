
# Multi OS (Unix system) install sh script for DTC
# Written by Thomas GOIRAND <thomas@goirand.fr>
# Cyrus modifications by Cristian Livadaru <cristian@livadaru.net>
# under LGPL Licence

# The configuration for all thoses variables must be written BEFORE this
# script. Do the start of the script for your operating system.
# I did mine for debian in debian/postinst

# Please note this script
# doeas not start with a :

#!/bin/sh

# because it's up to you to write it ! :)
# Do a "cat configure_deamons.sh >>your_os_setup_script"

# This script modify all your daemons configuration
# files so that it uses the DTC genated files.

#
# First, copy our RENAME_ME_paiement_config.php to paiement_config.php
# so it works automaticaly even without Tucows API
#

#VERBOSE_INSTALL=yes

# We are just after the creation of the chroot tree, so it's time to copy it over
# our newly created vhosts dirs (in update mode)

if [ -z ""$MYSQL_DB_SOCKET_PATH ] ;then
	MYSQL_DB_SOCKET_PATH="/var/run/mysqld/mysqld.sock"
fi


# Copy newly created chroot tree to the 3 vhosts created with this installer (mx and ns don't have apache vhosts generated)
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo -n "===> Installing chroot file environment for www."$main_domain_name
fi
cp -fupR  $conf_chroot_path/* $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/"

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo -n " "$dtc_admin_subdomain"."$main_domain_name
fi
cp -fupR  $conf_chroot_path/* $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/404"

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo -n " 404."$main_domain_name
fi
cp -fupR  $conf_chroot_path/* $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/"$dtc_admin_subdomain

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo " chown -R nobody:65534 "$conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains"
fi
chown -R nobody:65534 $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains"

# if we have a sudo binary around, then use it to create our chroot shell
# check for some path defaults... 
if [ -z "$PATH_SUDO" ]; then
	PATH_SUDO=`which sudo`
fi
if [ -z "$PATH_CHROOT" ]; then
	PATH_CHROOT=`which chrootuid`
fi
if [ -z "$PATH_SHELLS_CONF" ]; then
	PATH_SHELLS_CONF=/etc/shells
fi
if [ -z "$PATH_SUDOERS_CONF" ]; then
	PATH_SUDOERS_CONF=/etc/sudoers
fi
if [ -n "$PATH_SUDO" ] ; then
	if [ ""$VERBOSE_INSTALL = "yes" ]; then
		echo "Creating chroot shell..."
	fi
        # create a chroot shell script
        CHROOT_SHELL=/bin/dtc-chroot-shell
        echo '#!/bin/sh' > $CHROOT_SHELL
	echo "# This shell script is used by DTC, please do not remove" >> $CHROOT_SHELL
        echo "$PATH_SUDO -H $PATH_CHROOT \$HOME \$USER" /bin/bash \"\$@\" >> $CHROOT_SHELL
        chmod 755 $CHROOT_SHELL
        # fix sudoers
	if grep "Configured by DTC" $PATH_SUDOERS_CONF >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "$PATH_SUDOERS_CONF has been configured before..."
		fi
	else
		if ! [ -f $PATH_SUDOERS_CONF.DTC.backup ]
		then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "===> Backuping "$PATH_SUDOERS_CONF
			fi
			cp -f "$PATH_SUDOERS_CONF" "$PATH_SUDOERS_CONF.DTC.backup"
		fi
		TMP_FILE=`${MKTEMP} DTC_install.sudoers.XXXXXX` || exit 1
		echo "# Configured by DTC 0.21 : please do not touch this line !" >> $TMP_FILE
		echo "nobody      ALL= NOPASSWD: $PATH_CHROOT *" >> $TMP_FILE
		echo "# End of DTC configuration : please don't touch this line !" >> $TMP_FILE
		cat <$TMP_FILE >>$PATH_SUDOERS_CONF
	fi
        # fix /etc/shells
	if grep "Configured by DTC" $PATH_SHELLS_CONF >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "$PATH_SHELLS_CONF has been configured before..."
		fi
	else
		if ! [ -f $PATH_SHELLS_CONF.DTC.backup ]
		then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "===> Backuping "$PATH_SHELLS_CONF
			fi
			cp -f "$PATH_SHELLS_CONF" "$PATH_SHELLS_CONF.DTC.backup"
		fi
		TMP_FILE=`${MKTEMP} DTC_install.shells.XXXXXX` || exit 1
		echo "# Configured by DTC 0.21 : please do not touch this line !" >> $TMP_FILE
		echo "/bin/dtc-chroot-shell" >> $TMP_FILE
		echo "# End of DTC configuration : please don't touch this line !" >> $TMP_FILE
		cat <$TMP_FILE >>$PATH_SHELLS_CONF
		rm $TMP_FILE
	fi
fi

if ! [ -f $PATH_DTC_SHARED/shared/securepay/paiement_config.php ] ; then
	cp -v $PATH_DTC_SHARED/shared/securepay/RENAME_ME_paiement_config.php $PATH_DTC_SHARED/shared/securepay/paiement_config.php
fi

#
# Include $PATH_DTC_ETC/vhosts.conf in $PATH_HTTPD_CONF
#

TMP_FILE=`${MKTEMP} DTC_install.httpd.conf.XXXXXX` || exit 1

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Modifying httpd.conf"
fi
# need to see if we can use the modules-config or apacheconfig tools
HTTPD_MODULES_CONFIG=/usr/sbin/apache-modconf

# if apacheconfig is a symlink (deprecated), then use modules-config
if [ -f $HTTPD_MODULES_CONFIG ]
then
	if [ ""$conf_apache_version = "2" ] ; then
		HTTPD_MODULES_CONFIG="$HTTPD_MODULES_CONFIG apache2"
	else
		HTTPD_MODULES_CONFIG="$HTTPD_MODULES_CONFIG apache"
	fi
else
	HTTPD_MODULES_CONFIG=""
fi

# check to see if our apacheconfig has been obseleted
if [ "$HTTPD_MODULES_CONFIG" = "" ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Not using modules-config tool"
	fi
else
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Using $HTTPD_MODULES_CONFIG to configure apache modules"
	fi
fi

if grep "Configured by DTC" $PATH_HTTPD_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "httpd.conf has been configured before : skiping include inssertion !"
	fi
else
	if ! [ -f $PATH_HTTPD_CONF.DTC.backup ]
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "===> Backuping "$PATH_HTTPD_CONF
		fi
		cp -f "$PATH_HTTPD_CONF" "$PATH_HTTPD_CONF.DTC.backup"
	fi

	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "=> Verifying User and Group directive"
	fi

	# Those 2 are for debian
	if grep "User www-data" $PATH_HTTPD_CONF >/dev/null 2>&1
	then
		echo "User www-data -> User nobody"
		sed "s/User www-data/User nobody/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	fi

	if grep "Group www-data" $PATH_HTTPD_CONF >/dev/null 2>&1
	then
		echo "Group www-data -> Group nogroup"
		sed "s/Group www-data/Group nogroup/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	fi

	# Those 2 are for BSD
	if grep "User www" $PATH_HTTPD_CONF >/dev/null 2>&1
	then
		echo "User www -> User nobody"
		sed "s/User www/User nobody/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	fi
	if grep "Group www" $PATH_HTTPD_CONF >/dev/null 2>&1
	then
		echo "Group www -> Group nobody"
		sed "s/Group www/Group nobody/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	fi
	# Those 2 are for RedHat
	if grep "User apache" $PATH_HTTPD_CONF >/dev/null 2>&1
	then
		echo "User apache -> User nobody"
		sed "s/User apache/User nobody/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	fi
	if grep "Group apache" $PATH_HTTPD_CONF >/dev/null 2>&1
	then
		echo "Group apache -> Group nobody"
		sed "s/Group apache/Group nobody/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	fi

	if [ "$UNIX_TYPE" = "debian" -o "$UNIX_TYPE" = "osx" ]
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "=> Checking apache modules"
			echo -n "Checking for php4..."
		fi
		# first of all, may as well try to use the provided modules-config or apacheconfig provided by debian...
		# else use the normal method to be cross platform compatible

		if [ "$HTTPD_MODULES_CONFIG" = "" ]
		then
			# need to support modules.conf version of apache debian package
			# default to normal HTTPD_CONF
			PATH_HTTPD_CONF_TEMP=$PATH_HTTPD_CONF
			if [ -f $PATH_HTTPD_MODULES_CONF ]
			then
				PATH_HTTPD_CONF_TEMP=$PATH_HTTPD_MODULES_CONF
			fi
			if grep -i "# LoadModule php4_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
			then
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo "found commented: activating php4 module!"
				fi
				sed "s/# LoadModule php4_module/LoadModule php4_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
				cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
			else
				if grep -i "LoadModule php4_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
				then
					if [ ""$VERBOSE_INSTALL = "yes" ] ;then
						echo " ok!"
					fi
				else
					if [ ""$VERBOSE_INSTALL = "yes" ] ;then
						echo "php4 missing! please install it or run apacheconfig!!!"
					fi
					exit 1
				fi
			fi
		else
			if [ ""$conf_apache_version = "2" ] ; then
				echo "Apache2 don't need module checkings..."
			else
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo $HTTPD_MODULES_CONFIG enable php4_module
				fi
				$HTTPD_MODULES_CONFIG enable php4_module
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo $HTTPD_MODULES_CONFIG enable mod_php4
				fi
				$HTTPD_MODULES_CONFIG enable mod_php4
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo " enabled by $HTTPD_MODULES_CONFIG"
				fi
			fi
		fi

		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo -n "Checking for ssl..."
		fi
		if [ "$HTTPD_MODULES_CONFIG" = "" ]
		then
			if grep -i "# LoadModule ssl_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
			then
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo "found commented: activating ssl module!"
				fi
				sed "s/# LoadModule ssl_module/LoadModule ssl_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
				cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
			else
				if grep -i "LoadModule ssl_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
				then
					if [ ""$VERBOSE_INSTALL = "yes" ] ;then
						echo " ok!"
					fi
				else
					if [ ""$VERBOSE_INSTALL = "yes" ] ;then
						echo "!!! Warning: ssl_module for apache not present !!!"
					fi
				fi
			fi
		else
			if [ ""$conf_apache_version = "2" ] ; then
				echo "Apache 2 don't need module checkings..."
			else
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo $HTTPD_MODULES_CONFIG enable ssl_module
				fi
				$HTTPD_MODULES_CONFIG enable ssl_module
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo " enabled by $HTTPD_MODULES_CONFIG"
				fi
			fi
		fi

		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo -n "Checking for sql_log..."
		fi
		if [ "$HTTPD_MODULES_CONFIG" = "" ]
		then
			if grep -i "# LoadModule sql_log_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
			then
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo "found commented: ativating sql_log module!"
				fi
				sed "s/# LoadModule sql_log_module/LoadModule sql_log_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
				cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
			else
				if grep -i "LoadModule log_sql_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
				then
					if [ ""$VERBOSE_INSTALL = "yes" ] ;then
						echo " ok!"
					fi
				else
					if grep -i "# LoadModule log_sql_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
					then
						if [ ""$VERBOSE_INSTALL = "yes" ] ;then
							echo "found commented: ativating sql_log module!"
						fi
						sed "s/# LoadModule log_sql_module/LoadModule log_sql_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
						cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
					else
						if grep -i "LoadModule sql_log_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
						then
							if [ ""$VERBOSE_INSTALL = "yes" ] ;then
								echo " ok!"
							fi
						else
							echo "!!! sql_log_module for apache not present !!!"
							echo "please install it or run apacheconfig"
							echo "or add the following type directive"
							echo "(matching your path) to httpd.conf:"
							echo "LoadModule sql_log_module /usr/lib/apache/1.3/mod_log_sql.so (debian)"
							echo "LoadModule log_sql_module /usr/local/libexec/apache/mod_log_sql.so (bsd)"
							exit 1
						fi
					fi
				fi
			fi
		else
			if [ ""$conf_apache_version = "2" ] ; then
				echo "Apache 2 don't need module checkings..."
			else
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo $HTTPD_MODULES_CONFIG enable log_sql_module
					echo $HTTPD_MODULES_CONFIG enable log_sql_mysql_module
				fi
	#			$HTTPD_MODULES_CONFIG enable log_sql_module
				$HTTPD_MODULES_CONFIG enable log_sql_module
				$HTTPD_MODULES_CONFIG enable log_sql_mysql_module
				$HTTPD_MODULES_CONFIG enable mod_log_sql # just in case
				if [ ""$VERBOSE_INSTALL = "yes" ] ;then
					echo " enabled by $HTTPD_MODULES_CONFIG"
				fi
			fi
		fi
	else
		echo ""
		echo "!!! WARNING !!! Tests for the folling apache modules"
		echo "has NOT been executed because this could crash"
		echo "the installer. Please verify you have the following"
		echo "apache modules configured and working:"
		echo "php4, ssl, rewrite, and sql_log"
		echo "Note also that current DTC wroks with SBOX and that it"
		echo "should be compiled and installed on your server to"
		echo "enable cgi-bin protected and chrooted environment."
		echo ""
	fi

	if [ ""$conf_apache_version = "2" ] ; then
		# Activate mod_rewrite
		if [ -f /etc/apache2/mods-available/rewrite.load ] ; then
			if [ -d /etc/apache2/mods-enabled ] ; then
				if ! [ -e /etc/apache2/mods-enabled/rewrite.load ] ; then
					ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
				fi
			fi
		fi
		# Activate mod_ssl
		if [ -f /etc/apache2/mods-available/ssl.load ] ; then
			if [ -d /etc/apache2/mods-enabled ] ; then
				if ! [ -e /etc/apache2/mods-enabled/ssl.load ] ; then
					ln -s ../mods-available/ssl.load /etc/apache2/mods-enabled/ssl.load
				fi
			fi
		fi
	fi

	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo -n "Checking for AllowOverride..."
	fi
	if grep "AllowOverride None" $PATH_HTTPD_CONF
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "AllowOverride None -> AllowOverride AuthConfig FileInfo Limit Indexes"
		fi
		sed "s/AllowOverride None/AllowOverride AuthConfig FileInfo Limit Indexes/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	else
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "ok!"
		fi
	fi
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "=> Adding DTC's directives to httpd.conf end"
	fi

	# It seems redhat has already the Listen directives...
	# detect whether we already have Listen directives, and comment them out	# and replace with Listen 127.0.0.1:80 and 127.0.0.1:443
	# the other IPs will be created in vhosts.conf

	if grep "^Listen" $PATH_HTTPD_CONF >/dev/null
	then
		perl -i -p -e 's/^Listen/#Listen/' $PATH_HTTPD_CONF	
	fi
	if grep "^BindAddress" $PATH_HTTPD_CONF >/dev/null
	then
		perl -i -p -e 's/^BindAddress/#BindAddress/' $PATH_HTTPD_CONF	
	fi
	# symlink the PidFile to our dtc location, so we can check it in our scripts
	apachepidfile=`grep ^PidFile $PATH_HTTPD_CONF | cut -f2 -d' '`
	echo "Symlinking $apachepidfile to $PATH_DTC_ETC/apache.pid ..."
	rm -f $PATH_DTC_ETC/apache.pid
	ln -s $apachepidfile $PATH_DTC_ETC/apache.pid
	if [ ! -f $apachepidfile ]; then
		echo "PidFile $apachepidfile didn't exist..."
		if ps -e | grep apache$ > /dev/null; then
			ps -e | grep apache$ | head -n 1 | cut -f1 -d' ' >> $apachepidfile	
		fi
	fi

	# annoyingly redhat has a different Listen for the ssl.conf
	# comment that out too
	if [ ""$UNIX_TYPE = "redhat" ] ;then
		perl -i -p -e 's/^Listen/#Listen/' /etc/httpd/conf.d/ssl.conf
	fi

	echo "# Configured by DTC v0.12 : please do not touch this line !
Include $PATH_DTC_ETC/vhosts.conf
Listen 127.0.0.1:80
Listen 127.0.0.1:443" >>$PATH_HTTPD_CONF

	echo "LogSQLLoginInfo localhost dtcdaemons "${MYSQL_DTCDAEMONS_PASS} >>$PATH_HTTPD_CONF
	if [ ""$UNIX_TYPE = "freebsd" ] ;then
		echo "LogSQLSocketFile /tmp/mysqld.sock" >>$PATH_HTTPD_CONF
	else
		echo "LogSQLSocketFile ${MYSQL_DB_SOCKET_PATH}" >>$PATH_HTTPD_CONF
	fi
	echo "LogSQLDatabase apachelogs
LogSQLCreateTables On
LogSQLTransferLogFormat IAbhRrSsU
Alias /dtc404/	$PATH_DTC_ETC/dtc404/
ErrorDocument 404 /dtc404/404.php
# End of DTC configuration v0.12 : please don't touch this line !" >>$PATH_HTTPD_CONF
	if [ -f $TMP_FILE ]
	then
		rm -f $TMP_FILE
	fi
fi

if [ -e /etc/apache2/ports.conf ] ; then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Founded ports.conf: will remove it's directive"
	fi
	if [ -e /etc/apache2/ports.conf.DTC_backup ] ; then
		echo -n "";
	else
		cp /etc/apache2/ports.conf /etc/apache2/ports.conf.DTC_backup
	fi
	echo "" >/etc/apache2/ports.conf
fi

# Remove all the directives for mod_log_sql that we setup already in the main httpd.conf
# Removes: "LogSQLLoginInfo", "LogSQLMassVirtualHosting" and "LogSQLTransferLogFormat"
MOD_SQL_CONF="/etc/apache2/modules.d/42_mod_log_sql.conf"
if [ -e ${MOD_SQL_CONF} ] ; then
	TMP_FILE=`${MKTEMP} DTC_configure_mod_log_sql.conf.XXXXXX` || exit 1
	grep -v "LogSQLLoginInfo" ${MOD_SQL_CONF} >${TMP_FILE}
	TMP_FILE2=`${MKTEMP} DTC_configure2_mod_log_sql.conf.XXXXXX` || exit 1
	grep -v "LogSQLMassVirtualHosting" ${TMP_FILE} >${TMP_FILE2}
	grep -v "LogSQLTransferLogFormat" ${TMP_FILE2} >${MOD_SQL_CONF}
	rm -f ${TMP_FILE} ${TMP_FILE2}
fi

# Create the ssl certificate if it does not exists (for distribs with /etc/apache only for the moment)
# Obsolet code: removed!
#if [ -e "/etc/apache" ]; then
#	if [ -e "/etc/apache/ssl" ]; then
#		mkdir -p /etc/apache/ssl
#	fi
#fi

# copy the template directory from shared to etc, so we can edit it without worry of being purged on each install
# only copy the directory, if it doesn't already exist in the etc path
if [ -e "$PATH_DTC_SHARED/shared/template" ]; then
	if [ ! -e "$PATH_DTC_ETC/template" ]; then
		cp -r $PATH_DTC_SHARED/shared/template $PATH_DTC_ETC
	fi
	chown -R nobody:65534 $PATH_DTC_ETC/template
	chmod -R 775 $PATH_DTC_ETC/template
fi

# copy the 404 index.php file if none is found.
if ! [ -e $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/404/html/index.php" ]; then
	if ! [ -e $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/404/html/index.html" ]; then
		if [ -e $PATH_DTC_SHARED/shared/404_template/index.php ]; then
			cp $PATH_DTC_SHARED/shared/404_template/index.php $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/404/html/"
		fi
	fi
fi

# copy the Error 404 document
if ! [ -e $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/html/404.php" ]; then
	if [ -e $PATH_DTC_SHARED/shared/404_template/404.php ]; then 
		cp $PATH_DTC_SHARED/shared/404_template/404.php $conf_hosting_path"/"$conf_adm_login"/"$main_domain_name"/subdomains/www/html/"
	fi
fi

# also copy it to the dtc404 directory
if ! [ -e $PATH_DTC_ETC/dtc404/404.php ]; then
	mkdir -p $PATH_DTC_ETC/dtc404/
	cp $PATH_DTC_SHARED/shared/404_template/404.php $PATH_DTC_ETC/dtc404/
fi

cyrus_auth_php="$PATH_DTC_SHARED/shared/cyrus.php"
echo "<?
\$CYRUS = array(
'HOST'  => 'localhost',
'PORT'  => 143,
'ADMIN' => 'cyrus',
'PASS'  => '${MYSQL_DTCDAEMONS_PASS}'
);
\$cyrus_used=1;
\$cyrus_default_quota=51200;
?>" > $cyrus_auth_php;

PATH_PAMD_SMTP=/etc/pam.d/smtp
PATH_PAMD_IMAP=/etc/pam.d/imap
PATH_PAMD_SIEVE=/etc/pam.d/sieve
PATH_PAMD_POP=/etc/pam.d/pop
if [ -e /etc/pam.d/ ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> Adding configuration inside "$PATH_PAMD_SMTP
	fi
	if [ -f $PATH_PAMD_SMTP ]
	then
		if ! [ -f $PATH_PAMD_SMTP.DTC.backup ]
		then
			cp -f $PATH_PAMD_SMTP $PATH_PAMD_SMTP.DTC.backup
		fi
	fi
	touch $PATH_PAMD_SMTP
	echo "auth required pam_mysql.so user=dtcdaemons passwd="${MYSQL_DTCDAEMONS_PASS}" db="$conf_mysql_db" table=pop_access usercolumn=id passwdcolumn=password crypt=0" >$PATH_PAMD_SMTP
	if [ ""$conf_use_cyrus = "true" ]; then
		echo "account sufficient pam_mysql.so user=dtcdaemons passwd="${MYSQL_DTCDAEMONS_PASS}" host=localhost db="$conf_mysql_db" table=pop_access usercolumn=fullemail passwdcolumn=crypt crypt=1

auth required pam_mysql.so user=dtcdaemons passwd="${MYSQL_DTCDAEMONS_PASS}" host=localhost db="$conf_mysql_db" table=pop_access usercolumn=fullemail passwdcolumn=crypt crypt=1" >$PATH_PAMD_SMTP
	
		if [ -f $PATH_PAMD_IMAP ]
		then
			if ! [ -f $PATH_PAMD_IMAP.DTC.backup ]
			then
				cp -f $PATH_PAMD_IMAP $PATH_PAMD_IMAP.DTC.backup
			fi
		fi
		cp -f $PATH_PAMD_SMTP $PATH_PAMD_IMAP

		if [ -f $PATH_PAMD_SIEVE ]
		then
			if ! [ -f $PATH_PAMD_SIEVE.DTC.backup ]
			then
				cp -f $PATH_PAMD_SIEVE $PATH_PAMD_SIEVE.DTC.backup
			fi
		fi
		cp -f $PATH_PAMD_SMTP $PATH_PAMD_SIEVE

		if [ -f $PATH_PAMD_POP ]
		then
			if ! [ -f $PATH_PAMD_POP.DTC.backup ]
			then
				cp -f $PATH_PAMD_POP $PATH_PAMD_POP.DTC.backup
			fi
		fi
		cp -f $PATH_PAMD_SMTP $PATH_PAMD_POP
	fi
#	if grep "Configured by DTC" $PATH_PAMD_SMTP
#		echo $PATH_PAMD_SMTP" has been configured before: skiping include insertion!"
#	else
#		echo "Including configuration in "$PATH_PAMD_SMTP
#	fi
fi

#
# include $PATH_DTC_ETC/named.zones in $PATH_NAMED_CONF
#
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Adding inclusion to named.conf"
fi
if grep "Configured by DTC" $PATH_NAMED_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "named.conf has been configured before : skiping include insertion !"
	fi
else
	if ! [ -f $PATH_NAMED_CONF.DTC.backup ]
	then
		cp -f $PATH_NAMED_CONF $PATH_NAMED_CONF.DTC.backup
	fi
	TMP_FILE=`${MKTEMP} DTC_install.named.conf.XXXXXX` || exit 1
	echo "// Configured by DTC v0.10 : please don't touch this line !" > $TMP_FILE
	echo "include \"$PATH_DTC_ETC/named.conf\";" >> $TMP_FILE
	touch $PATH_DTC_ETC/named.conf
	cat < $TMP_FILE >>$PATH_NAMED_CONF
	if [ -e $TMP_FILE ]; then
		rm -f $TMP_FILE
	fi
fi

# only try and do qmail stuff if we have qmail installed! (check the control directory)
if [ -e "$PATH_QMAIL_CTRL" ] ;then
	#
	# Install the qmail links in the /etc/qmail
	#
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> Linking qmail control files to DTC generated files"
	fi
	if ! [ -e $PATH_QMAIL_CTRL/rcpthosts.DTC.backup ]
	then
		cp -f $PATH_QMAIL_CTRL/rcpthosts $PATH_QMAIL_CTRL/rcpthosts.DTC.backup
	fi
	rm -f $PATH_QMAIL_CTRL/rcpthosts
	touch $PATH_DTC_ETC/rcpthosts
	ln -s $PATH_DTC_ETC/rcpthosts $PATH_QMAIL_CTRL/rcpthosts

	touch $PATH_QMAIL_CTRL/virtualdomains
	if ! [ -e $PATH_QMAIL_CTRL/virtualdomains.DTC.backup ]
	then
		cp -f $PATH_QMAIL_CTRL/virtualdomains $PATH_QMAIL_CTRL/virtualdomains.DTC.backup
	fi
	rm -f $PATH_QMAIL_CTRL/virtualdomains
	touch $PATH_DTC_ETC/virtualdomains
	ln -s $PATH_DTC_ETC/virtualdomains $PATH_QMAIL_CTRL/virtualdomains

	if ! [ -e /var/qmail/users/assign.DTC.backup ]
	then
		if [ -e /var/qmail/users/assign ]; then
			cp -f /var/qmail/users/assign /var/qmail/users/assign.DTC.backup
		fi
	fi
	rm -f /var/qmail/users/assign
	touch $PATH_DTC_ETC/assign
	if ! [ -e /var/qmail/users ]; then
		mkdir -p /var/qmail/users
	fi
	ln -s $PATH_DTC_ETC/assign /var/qmail/users/assign

	# Complete mistake ! Please forgive me !
	#
	#if ! [ -f $PATH_QMAIL_CTRL/locals.DTC.backup ]
	#then
	#	touch $PATH_QMAIL_CTRL/locals
	#        cp -f $PATH_QMAIL_CTRL/locals $PATH_QMAIL_CTRL/locals.DTC.backup
	#fi
	#rm -f $PATH_QMAIL_CTRL/locals
	#touch $PATH_DTC_ETC/rcpthosts
	#ln -s $PATH_DTC_ETC/rcpthosts $PATH_QMAIL_CTRL/locals

	touch /etc/poppasswd
	if ! [ -e /etc/poppasswd.DTC.backup ]
	then
		cp -f /etc/poppasswd /etc/poppasswd.DTC.backup
	fi
	rm -f /etc/poppasswd
	touch $PATH_DTC_ETC/poppasswd
	ln -s $PATH_DTC_ETC/poppasswd /etc/poppasswd
else
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Could not found qmail directory: skipping"
	fi
fi

#
# Make some changes to the amavisd-new configuration to allow clamav to work with it cleanly
#

# make sure the amavisd configuration has 'amavis' user and group

if [ -f "$PATH_AMAVISD_CONF" ]; then
        if [ ""$VERBOSE_INSTALL == "yes" ]; then
                echo "===> Checking user and group configuration for amavisd..."
        fi

        # make sure our users exist for amavis
        set +e
        # turn back on error handling, these users probably exist already
        $GROUP_ADD_CMD amavis > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "Group amavis already exists..."
                fi
        fi
        $USER_ADD_CMD -g amavis amavis > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "User amavis already exists..."
                fi
        fi
        $PASSWD_CMD -l amavis > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "Change password failed for amavis user"
                fi
        fi
        set -e

        if grep "Configured by DTC" "$PATH_AMAVISD_CONF" >/dev/null; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "$PATH_AMAVISD_CONF already configured..."
                fi
        else
                if [ ""$VERBOSE_INSTALL = "yes" ] ;then
                        echo "Inserting configuration into $PATH_AMAVISD_CONF"
                fi

                # strip the 1; from the end of the config file
                perl -i -p -e 's/^1;[^\n]*\n//' $PATH_AMAVISD_CONF

		# fix the clamd ctl file to point to /var/run/clamav/clamd.ctl
		perl -i -p -e 's/\"i\/.*?\/clamd.ctl\"/\"\/var\/run\/clamav\/clamd.ctl\"/' $PATH_AMAVISD_CONF

		mkdir -p /var/run/clamav/
		chown -R clamav:clamav /var/run/clamav

                TMP_FILE=`${MKTEMP} dtc_install.amavisd.conf.XXXXXX` || exit 1
                echo "# Configured by DTC $VERSION" >> $TMP_FILE
                echo "\$daemon_user  = 'amavis';" >> $TMP_FILE
                echo "\$daemon_group  = 'amavis';" >> $TMP_FILE
                echo "\$final_virus_destiny = D_DISCARD;" >> $TMP_FILE
                echo "\$final_spam_destiny = D_PASS;" >> $TMP_FILE
                echo "\$final_banned_destiny = D_PASS;" >> $TMP_FILE
                echo "\$final_bad_header_destiny = D_PASS;" >> $TMP_FILE
                echo "\$warnvirussender = 0;" >> $TMP_FILE
                echo "\$warnspamsender = 0;" >> $TMP_FILE
		echo " # kill level defaults " >> $TMP_FILE

		echo "\$sa_tag_level_deflt  = 2.0;" >> $TMP_FILE
		echo "\$sa_tag2_level_deflt = 6.3;" >> $TMP_FILE
		echo "\$sa_kill_level_deflt = \$sa_tag2_level_deflt;" >> $TMP_FILE
		echo "\$sa_dsn_cutoff_level = 50;" >> $TMP_FILE

		echo "\$sa_mail_body_size_limit = 150*1024;" >> $TMP_FILE
		echo "# The following line will read the local domains as generated by DTC, amavisd will need to be restarted for new domains..." >> $TMP_FILE
		echo "read_hash(\\%local_domains, '$PATH_DTC_ETC/local_domains');" >> $TMP_FILE

                echo "# End of DTC configuration $VERSION" >> $TMP_FILE
                echo "1;  # insure a defined return" >> $TMP_FILE

                # now to insert it at the end of the actual amavisd.conf
                cat < $TMP_FILE >>$PATH_AMAVISD_CONF
                rm ${TMP_FILE}
        fi
fi

if [ -f "$PATH_CLAMAV_CONF" ]; then
        if [ ""$VERBOSE_INSTALL == "yes" ]; then
                echo "===> Checking user and group configuration for clamav..."
        fi

        # make sure our users exist for amavis
        set +e
        # turn back on error handling, these users probably exist already
        $GROUP_ADD_CMD clamav > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "Group clamav already exists..."
                fi
        fi
        $USER_ADD_CMD -g clamav clamav > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "User clamav already exists..."
                fi
        fi
        $PASSWD_CMD -l clamav > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "Change password failed for clamav user"
                fi
        fi
	# now add amavisd to the clamav group and vice versa
	$USER_MOD_CMD -G clamav,amavis clamav > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "Change group failed for clamav user"
                fi
	fi
	$USER_MOD_CMD -G amavis,clamav amavis > /dev/null 2>&1
        if [ $? -ne 0 ]; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "Change group failed for amavis user"
                fi
	fi
	# need to add the following to the config file:
	# AllowSupplementaryGroups
	# LocalSocket /var/run/clamav/clamd.ctl
	
	# need to fix a problem with a previous version

	if grep "^1;" "$PATH_CLAMAV_CONF" > /dev/null; then
		perl -i -p -e 's/^1;[^\n]*\n//' $PATH_CLAMAV_CONF
	fi

	if grep "Configured by DTC" "$PATH_CLAMAV_CONF" >/dev/null; then
                if [ ""$VERBOSE_INSTALL == "yes" ]; then
                        echo "$PATH_CLAMAV_CONF already configured..."
                fi
        else
                if [ ""$VERBOSE_INSTALL = "yes" ] ;then
                        echo "Inserting configuration into $PATH_CLAMAV_CONF"
                fi

                TMP_FILE=`${MKTEMP} dtc_install.clamav.conf.XXXXXX` || exit 1
                echo "# Configured by DTC $VERSION" >> $TMP_FILE
		echo "AllowSupplementaryGroups" >> $TMP_FILE
		echo "LocalSocket /var/run/clamav/clamd.ctl" >> $TMP_FILE

                echo "# End of DTC configuration $VERSION" >> $TMP_FILE

                # now to insert it at the end of the actual clamav.conf
                cat < $TMP_FILE >>$PATH_CLAMAV_CONF
        fi
fi

#
# Modify the cyrus imapd.conf 
#

if [ -f "$PATH_CYRUS_CONF" ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> modifying cyrus config"
	fi
	if grep "Configured by DTC" "$PATH_CYRUS_CONF" >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Cyrus imapd.conf has been configured before"
		fi
	else
		if grep "unixhierarchysep: no" "$PATH_CYRUS_CONF" >/dev/null; then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Changing unixhierarchysep from no to yes"
			fi
			TMP_FILE=`${MKTEMP} DTC_install.imapd.conf.XXXXXX` || exit 1
			sed "s/unixhierarchysep: no/unixhierarchysep: yes/" "$PATH_CYRUS_CONF" >$TMP_FILE
			cat <$TMP_FILE >"$PATH_CYRUS_CONF"
			rm $TMP_FILE
		fi
		if grep "sasl_pwcheck_method: auxprop" "$PATH_CYRUS_CONF" >/dev/null; then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Changing sasl_pwcheck_method from auxprop to saslauthd"
			fi
			TMP_FILE=`${MKTEMP} DTC_install.imapd.conf.XXXXXX` || exit 1
			sed "s/sasl_pwcheck_method: auxprop/sasl_pwcheck_method: saslauthd/" "$PATH_CYRUS_CONF" >$TMP_FILE
			cat <$TMP_FILE >"$PATH_CYRUS_CONF"
			rm $TMP_FILE
		fi
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Inserting DTC configuration inside $PATH_CYRUS_CONF"
		fi

		TMP_FILE=`${MKTEMP} DTC_install.imapd.conf.XXXXXX` || exit 1
		echo "# Configured by DTC v0.20 : Please don't touch this line !" > $TMP_FILE
		echo "virtdomains: yes
quotawarn: 90
admins: cyrus
sasl_mech_list: PLAIN LOGIN" >> $TMP_FILE
		echo "# End of DTC configuration v0.20 : Please don't touch this line !" >> $TMP_FILE
	# now to insert it at the end of the actual imapd.conf
	cat < $TMP_FILE >>$PATH_CYRUS_CONF
	rm $TMP_FILE
	fi
    else
	echo "$PATH_CYRUS_CONF NOT FOUND"
fi

if [ -f "$PATH_SASL_START_CONF" ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> modifying saslauthd startup parameters"
	fi
	if grep "Configured by DTC" $PATH_SASL_START_CONF >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "$PATH_SASL_START_CONF has been configured before..."
		fi
	else
		TMP_FILE=`${MKTEMP} DTC_install.saslauthd.XXXXXX` || exit 1
		echo "# Configured by DTC v0.20 : Please don't touch this line !" > $TMP_FILE
		echo "START=yes
PARAMS=\"-r -c \"" >> $TMP_FILE
		echo "# End of DTC configuration v0.20 : Please don't touch this line !" >> $TMP_FILE
		# now to insert it at the end of the actual saslauthd startup file
		cat < $TMP_FILE >>$PATH_SASL_START_CONF
		rm $TMP_FILE
	fi
	if [ -f $PATH_SASL_STARTUP ]
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "modifying saslatuhd startup file"
		fi
		# create the direcotry for postfix to access SASL socket
		mkdir -p $PATH_SASL_SOCKET

		# get the md5sum of the file, if it's original do the change
		# else we have a problem and report it to do the change manualy
		sasl_start_md5sum=`md5sum $PATH_SASL_STARTUP|cut -d " " -f1`
		# ### CL find a better place for this !
		sasl_orginal_m5="6307086733ad29bbd57f81b6c38334a1";
		if [ ""$sasl_orginal_m5 = "$sasl_start_md5sum" ]
		then
			# ok file is original so we can "patch" it.
			patch $PATH_SASL_STARTUP <$PATH_DTC_ADMIN/patch_saslatuhd_startup
		else 
			echo "Can not modify the saslauthd startupfile"
			echo "Please edit $PATH_SASL_STARTUP by hand and add folowing after startup:"
			echo "rm -f /var/spool/postfix/var/run/saslauthd/mux
ln /var/run/saslauthd/mux /var/spool/postfix/var/run/saslauthd/mux"
			echo "for more informations contact DTC development and DTC forums"
		fi
	fi
else
	if [ ""$conf_use_cyrus = "true" ]; then
		echo "Big Problem: Cyrus install selected bo no saslauthd startup file";
		echo "Workaround: make saslauth start with -r -c -a pam";
	fi
fi
# 
# Modify the postfix main.cf to include virtual delivery options
#

# Declare this makes the test when appenning the configuration for SASL
# works if you don't have SASL

SASLTMP_FILE="/thisfiledoesnotexists"
if [ -f "$PATH_POSTFIX_CONF" ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> Linking postfix control files to DTC generated files"
	fi
	touch $PATH_DTC_ETC/postfix_virtual.db
	touch $PATH_DTC_ETC/postfix_aliases.db
	touch $PATH_DTC_ETC/postfix_relay_recipients.db
	touch $PATH_DTC_ETC/postfix_vmailbox.db
	touch $PATH_DTC_ETC/postfix_virtual_uid_mapping.db
	chown nobody:65534 $PATH_DTC_ETC/postfix_*.db
	if grep "Configured by DTC" "$PATH_POSTFIX_CONF" >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Postfix main.cf has been configured before, not adding virtual mailbox options"
		fi
	else

		if grep "recipient_delimiter = +" "$PATH_POSTFIX_ETC/main.cf" >/dev/null; then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Changing recipient delimiter from + to -"
			fi
			TMP_FILE=`${MKTEMP} DTC_install.main.cf.XXXXXX` || exit 1
			sed "s/recipient_delimiter = +/recipient_delimiter = -/" "$PATH_POSTFIX_ETC/main.cf" >$TMP_FILE
			cat <$TMP_FILE >"$PATH_POSTFIX_ETC/main.cf"
			rm $TMP_FILE
		fi

		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Inserting DTC configuration inside $PATH_POSTFIX_CONF"
		fi

		TMP_FILE=`${MKTEMP} DTC_install.postfix_main.cf.XXXXXX` || exit 1
		echo "# Configured by DTC v0.12 : Please don't touch this line !" > $TMP_FILE
		# CL: this is general config, for courier and cyrus
		echo "# DTC virtual configuration
# disable the following functionality by default (otherwise can't match subdomains correctly)
parent_domain_matches_subdomains=

# disable mailbox size limit by default (user can add to postfix_config_snippets)
mailbox_size_limit = 0

# stuff for amavis
content_filter=smtp-amavis:[127.0.0.1]:10024

virtual_mailbox_domains = hash:$PATH_DTC_ETC/postfix_virtual_mailbox_domains
" >> $TMP_FILE

if [ ""$conf_use_cyrus = "true" ]; then
	echo "virtual_transport = cyrus
mailbox_transport = cyrus
# local_recipient_maps = $alias_maps, ... ### CL ToDo! " >> $TMP_FILE
else
	# courier/postfix only!
	echo "virtual_mailbox_base = /
virtual_mailbox_maps = hash:$PATH_DTC_ETC/postfix_vmailbox
virtual_minimum_uid = 100
virtual_uid_maps = static:65534
virtual_gid_maps = static:65534
virtual_uid_maps = hash:$PATH_DTC_ETC/postfix_virtual_uid_mapping" >> $TMP_FILE
fi
# CL continue with global part
echo "virtual_alias_maps = hash:$PATH_DTC_ETC/postfix_virtual
alias_maps = hash:/etc/aliases, hash:$PATH_DTC_ETC/postfix_aliases
relay_domains = $PATH_DTC_ETC/postfix_relay_domains
relay_recipient_maps = hash:$PATH_DTC_ETC/postfix_relay_recipients " >> $TMP_FILE
		if [ -n $conf_dnsbl_list ]; then
			IFS=, 
			for i in $conf_dnsbl_list; do 
				dnsbl_list="$dnsbl_list reject_rbl_client $i,"
			done
			unset IFS
		fi
		
		if [ "$PATH_SASL_PASSWD2" = "" ]; then
			echo -n ""
		elif [ -f $PATH_SASL_PASSWD2 ]; then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Found sasl2passwd at $PATH_SASL_PASSWD2"
			fi

			mkdir -p $PATH_POSTFIX_ETC/sasl
			
			if [ -e $PATH_POSTFIX_ETC/sasl/smtpd.conf ]; then
				if ! [ -e $PATH_POSTFIX_ETC/sasl/smtpd.conf.dtcbackup ]; then 
					cp $PATH_POSTFIX_ETC/sasl/smtpd.conf $PATH_POSTFIX_ETC/sasl/smtpd.conf.dtcbackup
				fi
			fi

			# prepare some sasldb2 files, so that our script latter can fix them

			if [ -e /var/spool/postfix/etc ]; then
				touch /var/spool/postfix/etc/sasldb2
				chown postfix:65534 /var/spool/postfix/etc/sasldb2
				chmod 664 /var/spool/postfix/etc/sasldb2
				if [ ! -e $PATH_DTC_ETC/sasldb2 ]; then
					cp /var/spool/postfix/etc/sasldb2 $PATH_DTC_ETC/sasldb2
				fi
			else
				if [ -d /etc/sasl2 ]; then
					touch /etc/sasl2/sasldb2
					chown postfix:65534 /etc/sasl2/sasldb2
					chmod 664 /etc/sasl2/sasldb2
				else
					touch /etc/sasldb2
					chown postfix:65534 /etc/sasldb2
					chmod 664 /etc/sasldb2
				fi
				if [ ! -e $PATH_DTC_ETC/sasldb2 ]; then
					if [ -d /etc/sasl2 ]; then
						cp /etc/sasl2/sasldb2 $PATH_DTC_ETC/sasldb2
					else
						cp /etc/sasldb2 $PATH_DTC_ETC/sasldb2
					fi
				fi
			fi

			SASLTMP_FILE=`${MKTEMP} DTC_install.postfix_sasl.XXXXXX` || exit 1
			echo "# Configured by DTC v0.15 : Please don't touch this line !" > ""$SASLTMP_FILE
			# CL: for cyrus use saslauthd instead of auxprop!
			if [ ""$conf_use_cyrus = "true" ]; then
				echo "pwcheck_method: saslauthd
mech_list: login plain" >> $SASLTMP_FILE
			else
				echo "pwcheck_method: auxprop
mech_list: plain login digest-md5 cram-md5" >> $SASLTMP_FILE
			fi
			echo "# End of DTC configuration v0.15 : please don't touch this line !" >> $SASLTMP_FILE
			echo "smtpd_recipient_restrictions = permit_mynetworks, 
                               permit_sasl_authenticated,
			       $dnsbl_list
                               reject_unauth_destination" >> $TMP_FILE
echo "smtp_sasl_auth_enable = no
smtpd_sasl_security_options = noanonymous
smtpd_sasl_local_domain = /etc/mailname
smtpd_sasl_auth_enable = yes
smtpd_tls_auth_only = no
" >> $TMP_FILE
		else
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "No saslpasswd2 found"
			fi
			echo "smtpd_recipient_restrictions = permit_mynetworks,
                               $dnsbl_list
                               reject_unauth_destination" >> $TMP_FILE
		fi
		# this adds supports for "config" snippets to append to main.cf
		if [ -f $PATH_DTC_ETC/postfix_config_snippets ]; then
			cat $PATH_DTC_ETC/postfix_config_snippets >> $TMP_FILE
		else
			echo "# /usr/share/dtc/etc/postfix_config_snippets
# this file is appended to the postfix configure, in case you need to override some configure parameters in the postfix main.cf" > $PATH_DTC_ETC/postfix_config_snippets
		fi


		if grep "Configured by DTC 0.21" "$PATH_POSTFIX_ETC/master.cf" >/dev/null; then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Postfix master.cf has been configured before, not adding maildrop options"
			fi
		else
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "Inserting DTC configuration inside $PATH_POSTFIX_ETC/master.cf"
			fi

			TMP_FILE2=`${MKTEMP} DTC_install.postfix_master.cf.XXXXXX` || exit 1
			echo "# Configured by DTC v0.17 : Please don't touch this line !" > $TMP_FILE2
			# if we have maildrop, we should use it!
			if [ -n ""$PATH_USERDB_BIN -a -f "$PATH_USERDB_BIN" -a -n ""$PATH_MAILDROP_BIN -a -f "$PATH_MAILDROP_BIN" ]; then
				echo "maildrop  unix  -       n       n       -       -       pipe
    flags=DRhu user=nobody argv=$PATH_MAILDROP_BIN -d \${user}@\${nexthop} \${extension} \${recipient} \${user} \${nexthop}
" >> $TMP_FILE2
			fi
			# CL do we use cyrus? 
			if [ ""$conf_use_cyrus = "true" ]; then
				echo "cyrus     unix  -       n       n       -       -       pipe
  flags=R user=cyrus argv=/usr/sbin/cyrdeliver -e -m \${extension} \${recipient}"  >> $TMP_FILE2
			fi

			# Insert our amavis stuff inside the master.cf
			echo "# amavisd-new
smtp-amavis unix -      -       -       -       2  smtp
    -o smtp_data_done_timeout=1200
    -o smtp_send_xforward_command=yes
    -o disable_dns_lookups=yes
    -o max_use=20

127.0.0.1:10025 inet n  -       -       -       -  smtpd
    -o content_filter=
    -o local_recipient_maps=
    -o relay_recipient_maps=
    -o smtpd_restriction_classes=
    -o smtpd_client_restrictions=
    -o smtpd_helo_restrictions=
    -o smtpd_sender_restrictions=
    -o smtpd_recipient_restrictions=permit_mynetworks,reject
    -o mynetworks=127.0.0.0/8
    -o strict_rfc821_envelopes=yes
    -o smtpd_error_sleep_time=0
    -o smtpd_soft_error_limit=1001
    -o smtpd_hard_error_limit=1000
    -o smtpd_client_connection_count_limit=0
    -o smtpd_client_connection_rate_limit=0
    -o receive_override_options=no_header_body_checks,no_unknown_recipient_checks
" >> $TMP_FILE2
			echo "# End of DTC configuration v0.17 : please don't touch this line !" >> $TMP_FILE2
			cat < $TMP_FILE2 >>"$PATH_POSTFIX_ETC/master.cf"
			rm $TMP_FILE2
		fi
		# if we have maildrop, we should use it!
		if [ -n ""$PATH_USERDB_BIN -a -f "$PATH_USERDB_BIN" -a -n ""$PATH_MAILDROP_BIN -a -f "$PATH_MAILDROP_BIN" ]; then
			echo "virtual_transport = maildrop" >> $TMP_FILE
			echo "## Set to 1 because Maildrop only delivers one message at a time.
maildrop_destination_recipient_limit = 1" >> $TMP_FILE
		fi

		echo "# End of DTC configuration v0.12 : please don't touch this line !" >> $TMP_FILE

		# now to insert it at the end of the actual main.cf
		cat < $TMP_FILE >>$PATH_POSTFIX_CONF
		rm $TMP_FILE
		# over-write the configuration for SASL
		if [ -e $SASLTMP_FILE ]; then
			cat $SASLTMP_FILE > $PATH_POSTFIX_ETC/sasl/smtpd.conf
			rm $SASLTMP_FILE
		fi
	fi
fi

#
# prepare mlmmj environment to work with dtc
#
if [ -f "/usr/bin/mlmmj-make-ml" -o -f "/usr/bin/mlmmj-make-ml.sh" ] ; then
	mkdir -p /etc/mlmmj/lists
	chown -R root:65534 /etc/mlmmj/lists
	chmod -R g+w /etc/mlmmj/lists
fi
# create mlmmj spool directory if it doesn't exist yet
if [ ! -e /var/spool/mlmmj/ ]; then
	mkdir -p /var/spool/mlmmj
fi
if [ -e /var/spool/mlmmj/ ] ;then
	chown nobody:65534 /var/spool/mlmmj/
fi

# This avoid hanging when (re)starting daemons under debian
if [ "$UNIX_TYPE" = "debian" ]
then
	db_stop
fi

#
# Install courier mysql authenticaion
#
if [ -f "$PATH_COURIER_CONF_PATH/authdaemonrc" ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> Adding directives to Courier authdaemonrc"
	fi
	if grep "Configured by DTC" $PATH_COURIER_CONF_PATH/authdaemonrc >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "authdaemonrc has been configure before: skipping include insertion !"
		fi
	else
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Inserting DTC configuration inside "$PATH_COURIER_CONF_PATH/authdaemonrc
		fi
		if ! [ -f $PATH_COURIER_CONF_PATH.DTC.backup ]
		then
			cp -f $PATH_COURIER_CONF_PATH/authdaemonrc $PATH_COURIER_CONF_PATH.DTC.backup
		fi
		TMP_FILE=`${MKTEMP} DTC_install.courier.conf.XXXXXX` || exit 1
		echo "# Configured by DTC v0.12 : Please don't touch this line !" > $TMP_FILE
		echo "authmodulelist=\"authmysql authpam\"" >> $TMP_FILE
		echo "# End of DTC configuration v0.12 : please don't touch this line !" >> $TMP_FILE
		# now append this to the existing configuration file
		cat < $TMP_FILE >> $PATH_COURIER_CONF_PATH/authdaemonrc
		rm $TMP_FILE
		echo "
# DB details for dtc mysql DB
MYSQL_SERVER		$conf_mysql_host
MYSQL_PORT		3306
MYSQL_DATABASE		$conf_mysql_db
MYSQL_USERNAME		dtcdaemons
MYSQL_PASSWORD		${MYSQL_DTCDAEMONS_PASS}
MYSQL_USER_TABLE        pop_access
MYSQL_LOGIN_FIELD       id
MYSQL_CRYPT_PWFIELD     crypt
MYSQL_HOME_FIELD        home
MYSQL_UID_FIELD         uid
MYSQL_GID_FIELD         gid
MYSQL_DEFAULT_DOMAIN    $main_domain_name

# use the experimental query
MYSQL_SELECT_CLAUSE     SELECT concat(id, '@', mbox_host), crypt, passwd, uid, gid, home, '', quota_size, ''  FROM pop_access  WHERE (id = '\$(local_part)' AND mbox_host = '\$(domain)') OR (id = SUBSTRING_INDEX('\$(local_part)', '%', 1) AND mbox_host = SUBSTRING_INDEX('\$(local_part)', '%', -1))

MYSQL_CHPASS_CLAUSE     UPDATE pop_access SET passwd='\$(newpass)', crypt='\$(newpass_crypt)' WHERE (id = '\$(local_part)' AND mbox_host = '\$(domain)') OR (id = SUBSTRING_INDEX('\$(local_part)', '%', 1)  AND mbox_host = SUBSTRING_INDEX('\$(local_part)', '%', -1))
" > $PATH_COURIER_CONF_PATH/authmysqlrc
		if [ -x "/etc/init.d/courier-authdaemon" ] ; then
			if [ -x /usr/sbin/invoke-rc.d ]; then
				/usr/sbin/invoke-rc.d courier-authdaemon restart
			else
				/etc/init.d/courier-authdaemon restart
			fi
		fi
		if [ -x "/etc/init.d/courier-imap" ] ; then
			if [ -x /usr/sbin/invoke-rc.d ]; then
				/usr/sbin/invoke-rc.d courier-imap restart
			else
				/etc/init.d/courier-imap restart
			fi
		fi
		if [ -x "/etc/init.d/courier-pop" ] ; then
			if [ -x /usr/sbin/invoke-rc.d ]; then
				/usr/sbin/invoke-rc.d courier-pop restart
			else
				/etc/init.d/courier-pop restart
			fi
		fi
	fi	
fi

# Generate the OpenSSL test certificate if it does not exists
if [ ""$conf_gen_ssl_cert = "true" ]; then
	if [ ! -e $PATH_DTC_ETC"/ssl" ]; then
		mkdir -p $PATH_DTC_ETC"/ssl"
	fi
	cwd=`pwd`
	cd $PATH_DTC_ETC"/ssl"
	if [ ! -e "./"new.cert.csr ]; then
		if [ ! -e "./"new.cert.cert ]; then
			if [ ! -e "./"new.cert.key ]; then
				CERTPASS_TMP_FILE=`${MKTEMP} certfilepass.XXXXXX` || exit 1
				echo $conf_gen_ssl_cert"" >$CERTPASS_TMP_FILE
				( echo $conf_cert_countrycode;
				echo "the state";
				echo $conf_cert_locality;
				echo $conf_cert_organization;
				echo $conf_cert_unit;
				echo $dtc_admin_subdomain"."$main_domain_name;
				echo $conf_cert_email;
				echo $conf_cert_challenge_pass;
				echo $conf_cert_organization; ) | openssl req -passout file:$CERTPASS_TMP_FILE -new > new.cert.csr
				openssl rsa -passin file:$CERTPASS_TMP_FILE -in privkey.pem -out new.cert.key
				openssl x509 -in new.cert.csr -out new.cert.cert -req -signkey new.cert.key -days 3650
				rm $CERTPASS_TMP_FILE
				# Copy the certificates to make them available for qmail
				if [ -d /var/qmail/control ] ; then
					if ! [ -e /var/qmail/control/servercert.pem ] ; then
						cat $PATH_DTC_ETC/ssl/new.cert.key $PATH_DTC_ETC/ssl/new.cert.cert >/var/qmail/control/servercert.pem
						chown qmaild:qmail /var/qmail/control/servercert.pem
						chmod 400 /var/qmail/control/servercert.pem
					fi
				fi
			fi
		fi
	fi
	cd $cwd
fi

#
# Install dovecot mysql authenticaion
#
if [ -f $PATH_DOVECOT_CONF ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> Adding directives to dovecot.conf"
	fi
	if grep "Configured by DTC" $PATH_DOVECOT_CONF >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "dovecot.conf has been configure before: skipping include insertion !"
		fi
	else
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Inserting DTC configuration inside "$PATH_DOVECOT_CONF
		fi
		if ! [ -f $PATH_DOVECOT_CONF.DTC.backup ]
		then
			cp -f $PATH_DOVECOT_CONF $PATH_DOVECOT_CONF.DTC.backup
		fi
		TMP_FILE=`${MKTEMP} DTC_install.dovecot.conf.XXXXXX` || exit 1
		echo "# Configured by DTC v0.12 : Please don't touch this line !" > $TMP_FILE
		echo "auth_userdb = mysql $PATH_DTC_ETC/dovecot-mysql.conf" >> $TMP_FILE
		echo "auth_passdb = mysql $PATH_DTC_ETC/dovecot-mysql.conf" >> $TMP_FILE
		echo "# End of DTC configuration v0.12 : please don't touch this line !" >> $TMP_FILE 
		# now append this to the existing configuration file
		cat < $TMP_FILE >> $PATH_DOVECOT_CONF
		rm  $TMP_FILE
		echo "
# DB details for dtc mysql DB
db_host = $conf_mysql_host
db_port = 3306
db_unix_socket = $MYSQL_DB_SOCKET_PATH
db = $conf_mysql_db
db_user = dtcdaemons
db_passwd = ${MYSQL_DTCDAEMONS_PASS}
db_client_flags = 0

default_pass_scheme = PLAIN
password_query = SELECT passwd FROM pop_access WHERE id = '%n' AND mbox_host = '%d'
user_query = SELECT home, uid, gid FROM pop_access WHERE id = '%n' AND mbox_host = '%d'
" > $PATH_DTC_ETC/dovecot-mysql.conf
		# need to restart dovecot too
		if [ -x "/etc/init.d/dovecot" ] ; then
                        /etc/init.d/dovecot restart
                else
                        if [ -x /usr/sbin/invoke-rc.d ]; then
                                /usr/sbin/invoke-rc.d dovecot restart
                        fi
                fi
	fi	
fi

#
# Install pure-ftpd-mysql
#
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Adding directives to pure-ftpd-mysql"
fi
PURE_FTPD_ETC="/etc/pure-ftpd"
if [ -e $PURE_FTPD_ETC ] ;then
	if [ -e /etc/pure-ftpd/conf/ ] ;then
		echo "yes" >/etc/pure-ftpd/conf/ChrootEveryone
	fi
	if [ -e $PURE_FTPD_ETC/db/ ] ;then
		echo "# Configured by DTC v0.10 : Please don't touch this line !

MYSQLSocket /var/run/mysqld/mysqld.sock
MYSQLUser dtcdaemons
MYSQLPassword ${MYSQL_DTCDAEMONS_PASS}
MYSQLDatabase dtc
MYSQLCrypt cleartext
MYSQLGetPW      SELECT password FROM ftp_access WHERE login=\"\L\"
MYSQLGetUID     SELECT uid FROM ftp_access WHERE login=\"\L\"
MYSQLGetGID     SELECT gid FROM ftp_access WHERE login=\"\L\"
MYSQLGetDir     SELECT homedir FROM ftp_access WHERE login=\"\L\"

" >$PURE_FTPD_ETC/db/mysql.conf;
		if [ -x /usr/sbin/invoke-rc.d ]; then
			/usr/sbin/invoke-rc.d pure-ftpd-mysql restart
		else
			if [ -x /etc/init.d/pure-ftpd-mysql ] ;then
				/etc/init.d/pure-ftpd-mysql restart
			fi
		fi
	fi
fi

#
# Install proftpd.conf to access to the database
#
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Adding directives to proftpd.conf"
fi
if grep "Configured by DTC" $PATH_PROFTPD_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "proftpd.conf has been configured before : skiping include inssertion !"
	fi
else
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Inserting DTC configuration inside "$PATH_PROFTPD_CONF
	fi
	if ! [ -f $PATH_PROFTPD_CONF.DTC.backup ]
	then
		cp -f $PATH_PROFTPD_CONF $PATH_PROFTPD_CONF.DTC.backup
	fi

	# Generate the OpenSSL test certificate if it does not exists
	if [ ""$conf_gen_ssl_cert = "true" ]; then
		if [ ! -e $PATH_DTC_ETC"/ssl" ]; then
			mkdir -p $PATH_DTC_ETC"/ssl"
		fi
		if [ ! -e $PATH_DTC_ETC"/ssl/proftpd" ] ; then
			 mkdir -p $PATH_DTC_ETC"/ssl/proftpd"
		fi
		cwd=`pwd`
		cd $PATH_DTC_ETC"/ssl/proftpd"
		if [ ! -e "./"new.cert.csr ]; then
			if [ ! -e "./"new.cert.cert ]; then
				if [ ! -e "./"new.cert.key ]; then
				CERTPASS_TMP_FILE=`${MKTEMP} certfilepass.XXXXXX` || exit 1
				echo $conf_gen_ssl_cert"" >$CERTPASS_TMP_FILE
				( echo $conf_cert_countrycode;
				echo "the state";
				echo $conf_cert_locality;
				echo $conf_cert_organization;
				echo $conf_cert_unit;
				echo $dtc_admin_subdomain"."$main_domain_name;
				echo $conf_cert_email;
				echo $conf_cert_challenge_pass;
				echo $conf_cert_organization; ) | openssl req -passout file:$CERTPASS_TMP_FILE -new > new.cert.csr
				openssl rsa -passin file:$CERTPASS_TMP_FILE -in privkey.pem -out new.cert.key
				openssl x509 -in new.cert.csr -out new.cert.cert -req -signkey new.cert.key -days 3650
				rm $CERTPASS_TMP_FILE
				fi
			fi
		fi
		cd $cwd
	fi

	TMP_FILE=`${MKTEMP} DTC_install.proftp.conf.XXXXXX` || exit 1
	echo "# Configured by DTC v0.10 : Please don't touch this line !" > $TMP_FILE
# This directive is not used anymore in newer version of proftpd
#	echo "#UserReverseDNS	off" >> $TMP_FILE
	echo "IdentLookups	off" >> $TMP_FILE
	echo "DefaultRoot	~" >> $TMP_FILE
	echo "SQLAuthenticate	on" >> $TMP_FILE
	echo "SQLConnectInfo	"$conf_mysql_db"@"$conf_mysql_host" dtcdaemons "${MYSQL_DTCDAEMONS_PASS} >> $TMP_FILE
	echo "SQLAuthTypes	Plaintext" >> $TMP_FILE
	echo "SQLUserInfo	ftp_access login password uid gid homedir shell" >> $TMP_FILE
	if [ -e $PATH_DTC_ETC"/ssl/proftpd/new.cert.cert" ] ; then
		if [ -e $PATH_DTC_ETC"/ssl/proftpd/new.cert.key" ] ; then
			if [ ""$conf_gen_ssl_cert = "true" ]; then
				echo "# This is the TLS auth support. Thanks to Erwan Gurcuff (gort) for the tip!
<IfModule mod_tls.c>
	TLSEngine on
	TLSLog /var/log/proftpd-tls.log
	TLSProtocol TLSv1
	TLSRequired off
	TLSRSACertificateFile "$PATH_DTC_ETC"/ssl/proftpd/new.cert.cert
	TLSRSACertificateKeyFile "$PATH_DTC_ETC"/ssl/proftpd/new.cert.key
	TLSVerifyClient on
</IfModule>" >> $TMP_FILE
			fi
		fi
	fi
	echo "# // Transfer Log to Proftpd
SQLLog RETR,STOR transfer1
SQLNamedQuery transfer1 INSERT \"'%u', '%f', '%b', '%h', '%a', '%m', '%T',now(), 'c', NULL\" ftp_logs

# // Count Logins per User
SQLLog                PASS logincount
SQLNamedQuery         logincount UPDATE \"count=count+1 WHERE login='%u'\" ftp_access

# // Remember the last login time
SQLLog                PASS lastlogin
SQLNamedQuery         lastlogin UPDATE \"last_login=now() WHERE login='%u'\" ftp_access

# // Count the downloaded bytes
SQLLog RETR           dlbytescount
SQLNamedQuery         dlbytescount UPDATE \"dl_bytes=dl_bytes+%b WHERE login='%u'\" ftp_access

# // Count the downloaded files
SQLLog RETR           dlcount
SQLNamedQuery         dlcount UPDATE \"dl_count=dl_count+1 WHERE login='%u'\" ftp_access

# // Count the uploaded bytes
SQLLog STOR           ulbytescount
SQLNamedQuery         ulbytescount UPDATE \"ul_bytes=ul_bytes+%b WHERE login='%u'\" ftp_access

# // Count the uploaded files
SQLLog STOR           ulcount
SQLNamedQuery         ulcount UPDATE \"ul_count=ul_count+1 WHERE login='%u'\" ftp_access

# End of DTC configuration v0.10 : please don't touch this line !" >> $TMP_FILE
	cat < $TMP_FILE >>$PATH_PROFTPD_CONF
	rm $TMP_FILE
	# This restarts proftpd if under debian like system
	# work has to be done under other OS to restart the ftp daemon
	if [ -x "/etc/init.d/proftpd" ] ; then
		if [ -x /usr/sbin/invoke-rc.d ]; then
			/usr/sbin/invoke-rc.d proftpd restart
		else
			/etc/init.d/proftpd restart
		fi
	fi
fi

#
# Install and configuration of FreeRadius 1.0
#
if [ -e ""$FREERADIUS_ETC ] ;then 
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then  
		echo "DTC has found you are using Freeradius and it's now configuring it" 
	fi 

	FREERADIUS_CONF=$FREERADIUS_ETC/radiusd.conf  
	FREERADIUS_SQL_DOT_CONF=$FREERADIUS_ETC/sql.conf 

	# Backup of freeradius config files
	if [ -e $FREERADIUS_CONF ] ;then
		if ! [ -e $FREERADIUS_CONF".DTCbackup" ] ;then
			cp $FREERADIUS_CONF $FREERADIUS_CONF".DTCbackup"
		fi
	fi

	if [ -e $FREERADIUS_SQL_DOT_CONF ] ;then
		if ! [ -e $FREERADIUS_SQL_DOT_CONF".DTCbackup" ] ;then
			cp $FREERADIUS_SQL_DOT_CONF $FREERADIUS_SQL_DOT_CONF".DTCbackup"
		fi
	fi

	TMP_FILE=`${MKTEMP} DTC_install.radius.conf.XXXXXX` || exit 1
	TMP_FILE2=`${MKTEMP} DTC_install.radius.conf.XXXXXX` || exit 1
	TMP_FILE3=`${MKTEMP} DTC_install.radius.conf.XXXXXX` || exit 1
	TMP_FILE4=`${MKTEMP} DTC_install.radius.conf.XXXXXX` || exit 1
	TMP_FILE5=`${MKTEMP} DTC_install.radius.conf.XXXXXX` || exit 1
	TMP_FILE6=`${MKTEMP} DTC_install.radius.conf.XXXXXX` || exit 1

	if [ -e /var/log/radacct ] ;then
		chown -R nobody /var/log/radacct
	fi

	sed "s/#user = nobody/user = nobody/" $FREERADIUS_CONF >$TMP_FILE
	if grep "group = nobody" $TMP_FILE >/dev/null ;then
		sed "s/#group = nobody/group = nobody/" $TMP_FILE >$TMP_FILE2
	else
		sed "s/#group = nogroup/group = nogroup/" $TMP_FILE >$TMP_FILE2
	fi
	sed "s/log_auth = no/log_auth = yes/" $TMP_FILE2 >$TMP_FILE3
	sed "s/log_auth_badpass = no/log_auth_badpass = yes/" $TMP_FILE3 >$TMP_FILE4
	sed "s/log_auth_goodpass = no/log_auth_goodpass = yes/" $TMP_FILE4 >$TMP_FILE5
	sed "s/#	sql/       sql/" $TMP_FILE5 >$TMP_FILE6 

	cat <$TMP_FILE6 >$FREERADIUS_CONF

	rm $TMP_FILE $TMP_FILE2 $TMP_FILE3 $TMP_FILE4 $TMP_FILE5 $TMP_FILE6

	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	        echo "===> Adding directives to sql.conf"
	fi

	if grep "Configured by DTC" $FREERADIUS_SQL_DOT_CONF >/dev/null
	then
	        if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	                echo "sql.conf has been configured before : skiping include inssertion !"
	        fi
	else
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
                	echo "Inserting DTC configuration inside "$FREERADIUS_SQL_DOT_CONF
		fi

	        TMP_FILE1=`${MKTEMP} DTC_install.sql.conf.XXXXXX` || exit 1
	        TMP_FILE2=`${MKTEMP} DTC_install.sql.conf.XXXXXX` || exit 1
	        TMP_FILE3=`${MKTEMP} DTC_install.sql.conf.XXXXXX` || exit 1
	        TMP_FILE4=`${MKTEMP} DTC_install.sql.conf.XXXXXX` || exit 1
	        TMP_FILE5=`${MKTEMP} DTC_install.sql.conf.XXXXXX` || exit 1

		# Remove the default config
		grep -v "server =" $FREERADIUS_SQL_DOT_CONF >$TMP_FILE1
		grep -v "login =" $TMP_FILE1 >$TMP_FILE2
		grep -v "password =" $TMP_FILE2 >$TMP_FILE3
		grep -v "radius_db = " $TMP_FILE3 >$TMP_FILE4
		grep -v "}" $TMP_FILE4 >$TMP_FILE5

		# Install the DTC db config
	        TMP_FILE=`${MKTEMP} DTC_install.sql.conf.XXXXXX` || exit 1
	        echo "# Configured by DTC v0.10 : Please don't touch this line !
        # Connect info
        server = "$conf_mysql_host"
        login = "$conf_mysql_login"
        password = "$conf_mysql_pass"" >> $TMP_FILE5
#	        echo "SQLConnectInfo    "$conf_mysql_db"@"$conf_mysql_host" "$conf_mysql_login" "$conf_mysql_pass >> $TMP_FILE4
	        echo "        # Database table configuration
        radius_db = "$conf_mysql_db"
# End of DTC configuration v0.10 : please don't touch this line !
}
" >> $TMP_FILE5

		cat <$TMP_FILE5 >$FREERADIUS_SQL_DOT_CONF
		rm $TMP_FILE $TMP_FILE1 $TMP_FILE2 $TMP_FILE3 $TMP_FILE4 $TMP_FILE5
	fi
fi

#
# Generate default config file for awstats (if we have it installed)
#

if [ -f $PATH_AWSTATS_ETC/awstats.conf ]; then
	# now if we don't already have a dtc awstats config, create one based on the installed package config
	if ! [ -f $PATH_AWSTATS_ETC/awstats.dtc.conf ]; then
		# we will use the environment variables while calling awstats...
		# Parameter="__ENVNAME__"
		cp $PATH_AWSTATS_ETC/awstats.conf $PATH_AWSTATS_ETC/awstats.dtc.conf
		perl -i -p -e 's/^LogFile=\"[^\"]*\"/LogFile=\"__AWSTATS_LOG_FILE__\"/'  $PATH_AWSTATS_ETC/awstats.dtc.conf
		perl -i -p -e 's/^SiteDomain=\"[^\"]*\"/SiteDomain=\"__AWSTATS_FULL_DOMAIN__\"/' $PATH_AWSTATS_ETC/awstats.dtc.conf
		perl -i -p -e 's/^DirData=\"[^\"]*\"/DirData=\"__AWSTATS_DIR_DATA__\"/' $PATH_AWSTATS_ETC/awstats.dtc.conf
		perl -i -p -e 's/^CreateDirDataIfNotExists=0/CreateDirDataIfNotExists=1/' $PATH_AWSTATS_ETC/awstats.dtc.conf
	fi
fi

#
# create the rrd file for queuegraph.cgi
#
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
        echo "===> Setting up rrdtools and graphs"
fi
if [ ! -e $PATH_DTC_ETC/mailqueues.rrd ]; then
	$PATH_DTC_ADMIN/queuegraph/createrrd.sh $PATH_DTC_ETC
fi
if [ ! -e /usr/lib/cgi-bin/queuegraph.cgi ]; then
	ln -s $PATH_DTC_ADMIN/queuegraph.cgi /usr/lib/cgi-bin/queuegraph.cgi
fi
chown nobody:65534 /usr/lib/cgi-bin/queuegraph.cgi


# fix path for mailqueues.rrd
perl -i -p -e "s|/etc/postfix|$PATH_DTC_ETC|" $PATH_DTC_ADMIN/queuegraph.cgi

if [ -z "$conf_eth2monitor" ] ; then
	echo "No interface selected: skiping the netusage.rrd setup!!!"
else
	#
	# create the rrd file for netusegraph.cgi
	#
	if [ ! -e $PATH_DTC_ETC/netusage.rrd ]; then
		$PATH_DTC_ADMIN/netusegraph/createrrd.sh $PATH_DTC_ETC
	fi
	if [ ! -e /usr/lib/cgi-bin/netusegraph.cgi ]; then
		ln -s $PATH_DTC_ADMIN/netusegraph.cgi /usr/lib/cgi-bin/netusegraph.cgi
	fi

	# fix path for netusage.rrd
	perl -i -p -e "s|/etc/postfix|$PATH_DTC_ETC|" $PATH_DTC_ADMIN/netusegraph.cgi
	chown nobody:65534 /usr/lib/cgi-bin/netusegraph.cgi
fi

#
# create the rrd file for cpugraph.cgi
#
if [ ! -e $PATH_DTC_ETC/cpu.rrd ]; then
	$PATH_DTC_ADMIN/cpugraph/createrrd.sh $PATH_DTC_ETC
fi
if [ ! -e /usr/lib/cgi-bin/cpugraph.cgi ]; then
	ln -s $PATH_DTC_ADMIN/cpugraph.cgi /usr/lib/cgi-bin/cpugraph.cgi
fi
# fix path for cpugraph.cgi
perl -i -p -e "s|/etc/postfix|$PATH_DTC_ETC|" $PATH_DTC_ADMIN/cpugraph.cgi
chown nobody:65534 /usr/lib/cgi-bin/cpugraph.cgi


#
# Create the rrd file for memgraph.cgi
#
if [ ! -e $PATH_DTC_ETC/memusage.rrd ]; then
	$PATH_DTC_ADMIN/memgraph/createrrd.sh $PATH_DTC_ETC
fi
if [ ! -e /usr/lib/cgi-bin/memgraph.cgi ]; then
	ln -s $PATH_DTC_ADMIN/memgraph.cgi /usr/lib/cgi-bin/memgraph.cgi
fi
# fix path for memgraph.cgi
perl -i -p -e "s|/etc/postfix|$PATH_DTC_ETC|" $PATH_DTC_ADMIN/memgraph.cgi
chown nobody:65534 /usr/lib/cgi-bin/memgraph.cgi

#
# Modify the SSH default option to make sure the UsePAM and turn on Password auth
# 

# default to /etc/ssh/sshd_config if it's not set by the installer
if [ -z ""$PATH_SSH_CONF ]; then
	PATH_SSH_CONF=/etc/ssh/sshd_config
fi

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Modifying SSH config to allow chroot logins... "$PATH_SSH_CONF
fi

# first we want to comment out any previously set variables
# PasswordAuthentication 
# UsePAM

TMP_FILE=`${MKTEMP} DTC_install.sshd_conf.XXXXXX` || exit 1

if grep "^PasswordAuthentication" $PATH_SSH_CONF >/dev/null 2>&1
then
	sed -e "s/^PasswordAuthentication/#PasswordAuthentication/" $PATH_SSH_CONF > $TMP_FILE
	cat <$TMP_FILE >$PATH_SSH_CONF
fi

if grep "^UsePAM" $PATH_SSH_CONF >/dev/null 2>&1
then
	sed -e "s/^UsePAM/#UsePAM/" $PATH_SSH_CONF > $TMP_FILE
	cat <$TMP_FILE >$PATH_SSH_CONF
fi

# now that we have removed the conflicting entries, add it back with the DTC required switches

if grep "Configured by DTC" $PATH_SSH_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "$PATH_SSH_CONF has been configured before..."
	fi
else
	if ! [ -f $PATH_SSH_CONF.DTC.backup ]
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "===> Backuping "$PATH_SSH_CONF
		fi
		cp -f "$PATH_SSH_CONF" "$PATH_SSH_CONF.DTC.backup"
	fi
	echo "# Configured by DTC 0.21 : please do not touch this line !" > $TMP_FILE
	echo "UsePAM yes" >> $TMP_FILE
	echo "PasswordAuthentication yes" >> $TMP_FILE
	echo "# End of DTC configuration : please don't touch this line !" >> $TMP_FILE
	cat <$TMP_FILE >>$PATH_SSH_CONF
fi

rm $TMP_FILE

#
# Modify /etc/nsswitch.conf
#
TMP_FILE=`${MKTEMP} DTC_install.nsswitch.conf.XXXXXX` || exit 1

if [ -z "$PATH_NSSWITCH_CONF" ]; then
	PATH_NSSWITCH_CONF=/etc/nsswitch.conf
fi

if grep "Configured by DTC" $PATH_NSSWITCH_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "$PATH_NSSWITCH_CONF has been configured before..."
	fi
else
	if ! [ -f $PATH_NSSWITCH_CONF.DTC.backup ]
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "===> Backuping "$PATH_NSSWITCH_CONF
		fi
		cp -f "$PATH_NSSWITCH_CONF" "$PATH_NSSWITCH_CONF.DTC.backup"
	fi
	echo "# Configured by DTC 0.21 : please do not touch this line !" > $TMP_FILE
	echo "
passwd:         compat mysql
group:          compat mysql
shadow:         compat mysql
" >> $TMP_FILE
	echo "# End of DTC configuration : please don't touch this line !" >> $TMP_FILE
	cat <$TMP_FILE >>$PATH_NSSWITCH_CONF
fi

rm $TMP_FILE

#
# Modify /etc/nss-mysql.conf and /etc/nss-mysql-root.conf
# 

TMP_FILE=`${MKTEMP} DTC_install.nss-mysql.conf.XXXXXX` || exit 1

if [ -z "$PATH_NSS_CONF" ]; then
	PATH_NSS_CONF=/etc/nss-mysql.conf
fi

if [ -z "$PATH_NSS_ROOT_CONF" ]; then
	PATH_NSS_ROOT_CONF=/etc/nss-mysql-root.conf
fi

if grep "Configured by DTC" $PATH_NSS_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "$PATH_NSS_CONF has been configured before..."
	fi
else
	if ! [ -f $PATH_NSS_CONF.DTC.backup ]
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "===> Backuping "$PATH_NSS_CONF
		fi
		cp -f "$PATH_NSS_CONF" "$PATH_NSS_CONF.DTC.backup"
	fi
	echo "# Configured by DTC 0.21 : please do not touch this line !" > $TMP_FILE
	echo "
users.host = inet:localhost:3306;
users.database = dtc;
users.db_user = dtcdaemons;
users.db_password = ${MYSQL_DTCDAEMONS_PASS};
users.backup_host =;
users.backup_database =;
users.table = ssh_access;
users.where_clause =;
users.user_column = ssh_access.login;
users.password_column = ssh_access.crypt;
users.userid_column = ssh_access.uid;
users.uid_column = ssh_access.uid;
users.gid_column = ssh_access.gid;
users.realname_column = \"DTC User\";
users.homedir_column = ssh_access.homedir;
users.shell_column = ssh_access.shell;
groups.group_info_table = ssh_groups;
groups.where_clause =;
groups.group_name_column = ssh_groups.group_name;
groups.groupid_column = ssh_groups.group_id;
groups.gid_column = ssh_groups.gid;
groups.password_column = ssh_groups.group_password;
groups.members_table = ssh_user_group;
groups.member_userid_column = ssh_user_group.user_id;
groups.member_groupid_column = ssh_user_group.group_id;
" >> $TMP_FILE
	echo "# End of DTC configuration : please don't touch this line !" >> $TMP_FILE
	cat <$TMP_FILE >>$PATH_NSS_CONF
fi

# fix perm for the nss root configuration
chmod 400 $PATH_NSS_CONF

if grep "Configured by DTC" $PATH_NSS_ROOT_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "$PATH_NSS_ROOT_CONF has been configured before..."
	fi
else
	if ! [ -f $PATH_NSS_ROOT_CONF.DTC.backup ]
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "===> Backuping "$PATH_NSS_ROOT_CONF
		fi
		cp -f "$PATH_NSS_ROOT_CONF" "$PATH_NSS_ROOT_CONF.DTC.backup"
	fi
	echo "# Configured by DTC 0.21 : please do not touch this line !" > $TMP_FILE
	echo "
shadow.host = inet:localhost:3306;
shadow.database = dtc;
shadow.db_user = dtcdaemons;
shadow.db_password = ${MYSQL_DTCDAEMONS_PASS};
shadow.backup_host =; 
shadow.backup_database =; 
shadow.table = ssh_access;
shadow.where_clause =;
shadow.userid_column = ssh_access.uid;
shadow.user_column = ssh_access.login;
shadow.password_column = ssh_access.crypt;
shadow.lastchange_column = UNIX_TIMESTAMP()-10;
shadow.min_column = 1;
shadow.max_column = 2;
shadow.warn_column = 7;
shadow.inact_column = -1; # disabled
shadow.expire_column = -1; # disabled
" >> $TMP_FILE
	echo "# End of DTC configuration : please don't touch this line !" >> $TMP_FILE
	cat <$TMP_FILE >>$PATH_NSS_ROOT_CONF
fi

# fix perm for the nss root configuration
chmod 400 $PATH_NSS_ROOT_CONF

rm $TMP_FILE




#
# Install the cron php4 script in the $PATH_CRONTAB_CONF
#

# just in case we haven't specified PATH_CRONTAB_CONF, default to /etc/crontab
if [ -z ""$PATH_CRONTAB_CONF ]; then
	PATH_CRONTAB_CONF=/etc/crontab
fi

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Installing cron script in "$PATH_CRONTAB_CONF
fi
if grep "Configured by DTC " $PATH_CRONTAB_CONF >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "/etc/crontab has been configured before : skinping include inssertion"
	fi
else
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Inserting DTC cronjob in "$PATH_CRONTAB_CONF
	fi
	if ! [ -f $PATH_CRONTAB_CONF.DTC.backup ]
	then
		cp -f $PATH_CRONTAB_CONF $PATH_CRONTAB_CONF.DTC.backup
	fi
	TMP_FILE=`${MKTEMP} DTC_install.crontab.XXXXXX` || exit 1
	echo "# Configured by DTC v0.10 : Please don't touch this line !" > $TMP_FILE
	echo "00,10,20,30,40,50 * * * * root cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/cron.php >>/var/log/dtc.log" >> $TMP_FILE
	echo "* 4 * * * nobody cd $PATH_DTC_ADMIN; nice -n+20 $PATH_PHP_CGI $PATH_DTC_ADMIN/accesslog.php >/dev/null" >> $TMP_FILE
	if [ ""$conf_mta_type = "postfix" -o ""$conf_mta_type = "p" ]; then
		echo "* * * * * root cd $PATH_DTC_ADMIN; $PATH_DTC_ADMIN/queuegraph/count_postfix.sh $PATH_DTC_ETC >>/var/log/dtc.log" >> $TMP_FILE
	fi
	if [ ""$conf_mta_type = "qmail" -o ""$conf_mta_type = "q" ]; then
		echo "* * * * * root cd $PATH_DTC_ADMIN; nice -n+20 $PATH_DTC_ADMIN/queuegraph/count_qmail.sh $PATH_DTC_ETC >>/var/log/dtc.log" >> $TMP_FILE
	fi
	echo "* * * * * root cd $PATH_DTC_ADMIN; nice -n+20 $PATH_DTC_ADMIN/cpugraph/get_cpu_load.sh $PATH_DTC_ETC >>/var/log/dtc.log" >> $TMP_FILE
	echo "* * * * * root cd $PATH_DTC_ADMIN; nice -n+20 $PATH_DTC_ADMIN/netusegraph/get_net_usage.sh $PATH_DTC_ETC \"$conf_eth2monitor\" >>/var/log/dtc.log" >> $TMP_FILE
	echo "* * * * * root cd $PATH_DTC_ADMIN; nice -n+20 $PATH_DTC_ADMIN/memgraph/get_meminfo.sh $PATH_DTC_ETC >>/var/log/dtc.log" >> $TMP_FILE
	cat < $TMP_FILE >>/etc/crontab
	rm $TMP_FILE
fi

# add the default password to .htpasswd if it doesn't exist already
if [ -e $conf_hosting_path/.htpasswd ]; then 
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "OK, you have your "$conf_hosting_path"/.htpasswd setup already!"
	fi
else 
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Creating "$conf_hosting_path"/.htpasswd with username '$conf_adm_login' and password '$conf_adm_pass'"
	fi
	if [ -e "/usr/local/bin/htpasswd" ] ;then
		HTPASSWD="/usr/local/bin/htpasswd"
	else
		if [ -e "/usr/bin/htpasswd" ] ;then
			HTPASSWD="/usr/bin/htpasswd"
		else
			if [ -e "/usr/sbin/htpasswd" ] ;then
				HTPASSWD="/usr/sbin/htpasswd"
			else
				if [ -e "/usr/sbin/htpasswd2" ] ;then
					HTPASSWD="/usr/sbin/htpasswd2"
				else
					HTPASSWD="htpasswd"
				fi
			fi
		fi
	fi
	$HTPASSWD -cb "$conf_hosting_path"/.htpasswd "$conf_adm_login" $conf_adm_pass
fi

if [ -e $PATH_DTC_ADMIN/.htaccess ]; then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "OK, you have your "$PATH_DTC_ADMIN"/.htaccess setup already!"
	fi
else
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Creating "$PATH_DTC_ADMIN"/.htaccess file."
	fi
	echo "AuthName \"DTC root control panel login!\"
AuthType Basic
AuthUserFile "$conf_hosting_path"/.htpasswd
require valid-user" >$PATH_DTC_ADMIN/.htaccess
fi

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "***********************************************************"
	echo "*** Please wait while DTC configures all the daemons... ***"
	echo "***********************************************************"

	cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/cron.php
	echo "--- --- --- INSTALLATION FINISHED --- --- ---"
else
	cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/cron.php 2>&1 >/var/log/dtc.log
	echo "done!"
fi

echo ""
echo "Browse to: \"http://"$dtc_admin_subdomain"."$main_domain_name"/dtcadmin/\""
echo "    or to: \"https://"$dtc_admin_subdomain"."$main_domain_name"/dtcadmin/\""
echo "with login/pass of the main domain admin."
echo "Remember to relaunch this installer if you"
echo "install some other mail servers, whatever"
echo "it is (qmail, postfix, courier, etc...)."
echo "NOTE: please check sshd_config and then restart ssh"
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo ""
	echo "Visit DTC Home page"
fi
echo "http://www.gplhost.com/software-dtc.html"

exit 0
