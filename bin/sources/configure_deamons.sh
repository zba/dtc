
# Multi OS (Unix system) install sh script for DTC
# Written by Thomas GOIRAND <thomas@goirand.fr>
# under LGPL Licence

# The configuration for all thoses variables must be written BEFORE this
# script. Do the start of the script for your operating system.
# I did mine for debian in debian/postinst

# Please note this script
# doeas not start with a :

#!/bin/sh

# because it's up to you to write it ! :)
# Do a "cat configure_deamons.sh >>your_os_setup_script"

# This script modify named, profptd, apache and qmail configuration
# files so that it uses the DTC genated files.

#
# First, copy our RENAME_ME_paiement_config.php to paiement_config.php
# so it works automaticaly even without Tucows API
#

# VERBOSE_INSTALL=yes

if ! [ -f $PATH_DTC_SHARED/shared/securepay/paiement_config.php ]
then
	cp -v $PATH_DTC_SHARED/shared/securepay/RENAME_ME_paiement_config.php $PATH_DTC_SHARED/shared/securepay/paiement_config.php
fi

#
# Include $PATH_DTC_ETC/vhosts.conf in $PATH_HTTPD_CONF
#

TMP_FILE=`mktemp -t DTC_install.httpd.conf.XXXXXX` || exit 1

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Modifying httpd.conf"
fi
# need to see if we can use the modules-config or apacheconfig tools
HTTPD_MODULES_CONFIG=/usr/sbin/apache-modconf

# if apacheconfig is a symlink (deprecated), then use modules-config
if [ -f $HTTPD_MODULES_CONFIG ]
then
	HTTPD_MODULES_CONFIG="$HTTPD_MODULES_CONFIG apache"
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
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo $HTTPD_MODULES_CONFIG enable ssl_module
			fi
			$HTTPD_MODULES_CONFIG enable ssl_module
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo " enabled by $HTTPD_MODULES_CONFIG"
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
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo $HTTPD_MODULES_CONFIG enable sql_log_module
			fi
			$HTTPD_MODULES_CONFIG enable sql_log_module
			$HTTPD_MODULES_CONFIG enable mod_log_sql # just in case
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo " enabled by $HTTPD_MODULES_CONFIG"
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
	if [ ""$UNIX_TYPE = "redhat" ] ;then
		echo "# Configured by DTC v0.12 : please do not touch this line !
Include $PATH_DTC_ETC/vhosts.conf" >>$PATH_HTTPD_CONF
	else
		echo "# Configured by DTC v0.12 : please do not touch this line !
Include $PATH_DTC_ETC/vhosts.conf
Listen 80
Listen 443" >>$PATH_HTTPD_CONF
	fi
	echo "LogSQLLoginInfo localhost "$conf_mysql_login" "$conf_mysql_pass"
LogSQLSocketFile /var/run/mysqld/mysqld.sock
LogSQLDatabase apachelogs
LogSQLCreateTables On
LogSQLTransferLogFormat IAbhRrSsU
Alias /dtc404/	$PATH_DTC_ETC/dtc404/
ErrorDocument 404 /dtc404/404.php
# ErrorDocument 404 http://www.$main_domain_name"/404.php"
# End of DTC configuration v0.12 : please don't touch this line !" >>$PATH_HTTPD_CONF
	if [ -f $TMP_FILE ]
	then
		rm -f $TMP_FILE
	fi
fi

# Create the ssl certificate if it does not exists (for distribs with /etc/apache only for the moment)
if [ -e "/etc/apache" ]; then
	if [ -e "/etc/apache/ssl" ]; then
		mkdir -p /etc/apache/ssl
	fi
fi

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

PATH_PAMD_SMTP=/etc/pam.d/smtp
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
	echo "auth required pam_mysql.so user="$conf_mysql_login" passwd="$conf_mysql_pass" db="$conf_mysql_db" table=pop_access usercolumn=id passwdcolumn=password crypt=0" >$PATH_PAMD_SMTP
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
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Including named.conf in $PATH_NAMED_CONF"
	fi
	if ! [ -f $PATH_NAMED_CONF.DTC.backup ]
	then
		cp -f $PATH_NAMED_CONF $PATH_NAMED_CONF.DTC.backup
	fi
	TMP_FILE=`mktemp -t DTC_install.named.conf.XXXXXX` || exit 1
	echo "// Configured by DTC v0.10 : please don't touch this line !" > $TMP_FILE
	echo "include \"$PATH_DTC_ETC/named.conf\";" >> $TMP_FILE
	touch $PATH_DTC_ETC/named.conf
	cat < $TMP_FILE >>$PATH_NAMED_CONF
	if [ -e $TMP_FILE ]; then
		rm -f $TMP_FILE
	fi
fi

# only try and do qmail stuff if we have qmail installed! (check the control directory)
if [ -e $PATH_QMAIL_CTRL ]
then
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
# Modify the postfix main.cf to include virtual delivery options
#

# Declare this makes the test when appenning the configuration for SASL
# works if you don't have SASL

SASLTMP_FILE="/thisfiledoesnotexists"
if [ -f $PATH_POSTFIX_CONF ]
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "===> Linking postfix control files to DTC generated files"
	fi
	if grep "Configured by DTC" "$PATH_POSTFIX_CONF" >/dev/null
	then
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Postfix main.cf has been configured before, not adding virtual mailbox options"
		fi
	else
		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo "Inserting DTC configuration inside $PATH_POSTFIX_CONF"
		fi
		TMP_FILE=`mktemp -t DTC_install.postfix_main.cf.XXXXXX` || exit 1
		echo "# Configured by DTC v0.12 : Please don't touch this line !" > $TMP_FILE
		echo "
# DTC virtual configuration
virtual_mailbox_domains = hash:$PATH_DTC_ETC/postfix_virtual_mailbox_domains
virtual_mailbox_base = /
virtual_mailbox_maps = hash:$PATH_DTC_ETC/postfix_vmailbox
virtual_minimum_uid = 100
virtual_uid_maps = static:65534
virtual_gid_maps = static:65534
virtual_alias_maps = hash:$PATH_DTC_ETC/postfix_virtual
relay_domains = $PATH_DTC_ETC/postfix_relay_domains
virtual_uid_maps = hash:$PATH_DTC_ETC/postfix_virtual_uid_mapping" >> $TMP_FILE

		if [ ""$VERBOSE_INSTALL = "yes" ] ;then
			echo " Attempting to determine if you have sasl2 installed..."
		fi
		if [ "$PATH_SASL_PASSWD2" = "" ]; then
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "No saslpasswd2 installed";
			fi
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
				touch /etc/sasldb2
				chown postfix:65534
				chmod 664 /var/spool/postfix/etc/sasldb2
				if [ ! -e $PATH_DTC_ETC/sasldb2 ]; then
					cp /etc/sasldb2 $PATH_DTC_ETC/sasldb2
				fi
			fi

			SASLTMP_FILE=`mktemp -t DTC_install.postfix_sasl.XXXXXX` || exit 1
			echo "# Configured by DTC v0.15 : Please don't touch this line !" > ""$SASLTMP_FILE
			echo "pwcheck_method: auxprop
mech_list: plain login digest-md5 cram-md5" >> $SASLTMP_FILE
			echo "# End of DTC configuration v0.15 : please don't touch this line !" >> $SASLTMP_FILE
			echo "smtpd_recipient_restrictions = permit_mynetworks, permit_sasl_authenticated, reject_unauth_destination


smtp_sasl_auth_enable = no
smtpd_sasl_security_options = noanonymous
smtpd_sasl_local_domain = /etc/mailname
smtpd_sasl_auth_enable = yes
smtpd_tls_auth_only = no
" >> $TMP_FILE
		else
			if [ ""$VERBOSE_INSTALL = "yes" ] ;then
				echo "No saslpasswd2 found"
			fi
		fi
		echo "# End of DTC configuration v0.12 : please don't touch this line !" >> $TMP_FILE

		# now to insert it at the end of the actual main.cf
		cat < $TMP_FILE >>$PATH_POSTFIX_CONF
		rm $TMP_FILE
		# append the configuration for SASL
		if [ -e $SASLTMP_FILE ]; then
			cat < $SASLTMP_FILE >> $PATH_POSTFIX_ETC/sasl/smtpd.conf
			rm $SASLTMP_FILE
		fi
	fi
fi

# This avoid hanging when (re)starting daemons under debian
if [ "$UNIX_TYPE" = "debian" ]
then
	db_stop
fi

#
# Install courier mysql authenticaion
#
if [ -f $PATH_COURIER_CONF_PATH/authdaemonrc ]
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
		TMP_FILE=`mktemp -t DTC_install.courier.conf.XXXXXX` || exit 1
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
MYSQL_USERNAME		$conf_mysql_login
MYSQL_PASSWORD		$conf_mysql_pass
MYSQL_USER_TABLE        pop_access
MYSQL_LOGIN_FIELD       id
MYSQL_CRYPT_PWFIELD     crypt
MYSQL_HOME_FIELD        home
MYSQL_UID_FIELD         uid
MYSQL_GID_FIELD         gid
MYSQL_DEFAULT_DOMAIN    $main_domain_name

# use the experimental query
MYSQL_SELECT_CLAUSE     SELECT concat(id, '@', mbox_host), crypt, passwd, uid, gid, home, '', quota_size, ''  FROM pop_access  WHERE (id = '\$(local_part)' AND mbox_host = '\$(domain)') OR (id = SUBSTRING_INDEX('\$(local_part)', '%', 1) AND mbox_host = SUBSTRING_INDEX('\$(local_part)', '%', -1))

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
			CERTPASS_TMP_FILE=`mktemp -t certfilepass.XXXXXX` || exit 1
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
		TMP_FILE=`mktemp -t DTC_install.dovecot.conf.XXXXXX` || exit 1
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
db_unix_socket = /var/run/mysqld/mysqld.sock
db = $conf_mysql_db
db_user = $conf_mysql_login
db_passwd = $conf_mysql_pass
db_client_flags = 0

default_pass_scheme = PLAIN
password_query = SELECT passwd FROM pop_access WHERE id = '%n' AND mbox_host = '%d'
user_query = SELECT home, uid, gid FROM pop_access WHERE id = '%n' AND mbox_host = '%d'
" > $PATH_DTC_ETC/dovecot-mysql.conf
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
	TMP_FILE=`mktemp -t DTC_install.proftp.conf.XXXXXX` || exit 1
	echo "# Configured by DTC v0.10 : Please don't touch this line !" > $TMP_FILE
# This directive is not used anymore in newer version of proftpd
#	echo "#UserReverseDNS	off" >> $TMP_FILE
	echo "IdentLookups	off" >> $TMP_FILE
	echo "DefaultRoot	~" >> $TMP_FILE
	echo "SQLAuthenticate	on" >> $TMP_FILE
	echo "SQLConnectInfo	"$conf_mysql_db"@"$conf_mysql_host" "$conf_mysql_login" "$conf_mysql_pass >> $TMP_FILE
	echo "SQLAuthTypes	Plaintext" >> $TMP_FILE
	echo "SQLUserInfo	ftp_access login password uid gid homedir shell" >> $TMP_FILE
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
# Install the cron php4 script in the /etc/crontab
#
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Installing cron script in /etc/crontab"
fi
if grep "Configured by DTC" /etc/crontab >/dev/null
then
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "/etc/crontab has been configured before : skinping include inssertion"
	fi
else
	if [ ""$VERBOSE_INSTALL = "yes" ] ;then
		echo "Inserting DTC cronjob in /etc/crontab"
	fi
	if ! [ -f /etc/crontab.DTC.backup ]
	then
		cp -f /etc/crontab /etc/crontab.DTC.backup
	fi
	TMP_FILE=`mktemp -t DTC_install.crontab.XXXXXX` || exit 1
	echo "# Configured by DTC v0.10 : Please don't touch this line !" > $TMP_FILE
	echo "00,10,20,30,40,50 * * * * root cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/cron.php >>/var/log/dtc.log" >> $TMP_FILE
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
	/usr/bin/htpasswd -cb "$conf_hosting_path"/.htpasswd "$conf_adm_login" $conf_adm_pass
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
	cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/cron.php 2>&1
	echo "done!"
fi

echo ""
echo "Browse to: \"http(s)://"$dtc_admin_subdomain"."$main_domain_name"/dtcadmin/\""
echo "with login/pass of the main domain admin."
echo "Remember to relaunch this installer if you"
echo "install some other mail servers, whatever"
echo "it is (qmail, postfix, courier, etc...)."
if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo ""
	echo "Visit DTC Home page"
fi
echo "http://www.gplhost.com/?rub=softwares&sousrub=dtc"

exit 0
