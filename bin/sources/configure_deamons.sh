
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

echo "!!! WARNING !!!  Qmail or Postfix (or any supported mail daemons)"
echo "MUST be installed before in order to enable configuration by the"
echo "DTC installer !!!"
echo ""

if ! [ -f $PATH_DTC_SHARED/securepay/paiement_config.php ]
then
	cp -v $PATH_DTC_SHARED/shared/securepay/RENAME_ME_paiement_config.php $PATH_DTC_SHARED/shared/securepay/paiement_config.php
fi

#
# Include $PATH_DTC_ETC/vhosts.conf in $PATH_HTTPD_CONF
#

TMP_FILE=/tmp/DTC_install.httpd.conf

echo "===> Modifying httpd.conf"
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
	echo "Not using modules-config tool"
else
	echo "Using $HTTPD_MODULES_CONFIG to configure apache modules"
fi

if grep "Configured by DTC" $PATH_HTTPD_CONF
then
	echo "httpd.conf has been configured before : skiping include inssertion !"
else
	if ! [ -f $PATH_HTTPD_CONF.DTC.backup ]
	then
		echo "===> Backuping "$PATH_HTTPD_CONF
		cp -f "$PATH_HTTPD_CONF" "$PATH_HTTPD_CONF.DTC.backup"
	fi

	echo "=> Verifying User and Group directive"
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

	if [ "$UNIX_TYPE" = "debian" ]
	then
		echo "=> Checking apache modules"
		echo -n "Checking for php4..."

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
				echo "found commented: activating php4 module!"
				sed "s/# LoadModule php4_module/LoadModule php4_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
				cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
			else
				if grep -i "LoadModule php4_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
				then
					echo " ok!"
				else
					echo "php4 missing! please install it or run apacheconfig!!!"
					exit 1
				fi
			fi
		else
			echo $HTTPD_MODULES_CONFIG enable php4_module
			$HTTPD_MODULES_CONFIG enable php4_module
			echo $HTTPD_MODULES_CONFIG enable mod_php4
			$HTTPD_MODULES_CONFIG enable mod_php4
			echo " enabled by $HTTPD_MODULES_CONFIG"
		fi

		echo -n "Checking for ssl..."
		if [ "$HTTPD_MODULES_CONFIG" = "" ]
		then
			if grep -i "# LoadModule ssl_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
			then
				echo "found commented: activating ssl module!"
				sed "s/# LoadModule ssl_module/LoadModule ssl_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
				cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
			else
				if grep -i "LoadModule ssl_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
				then
					echo " ok!"
				else
					echo "!!! Warning: ssl_module for apache not present !!!"
				fi
			fi
		else
			echo $HTTPD_MODULES_CONFIG enable ssl_module
			$HTTPD_MODULES_CONFIG enable ssl_module
			echo " enabled by $HTTPD_MODULES_CONFIG"
		fi

		echo -n "Checking for sql_log..."
		if [ "$HTTPD_MODULES_CONFIG" = "" ]
		then
			if grep -i "# LoadModule sql_log_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
			then
				echo "found commented: ativating sql_log module!"
				sed "s/# LoadModule sql_log_module/LoadModule sql_log_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
				cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
			else
				if grep -i "LoadModule log_sql_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
				then
					echo " ok!"
				else
					if grep -i "# LoadModule log_sql_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
					then
						echo "found commented: ativating sql_log module!"
						sed "s/# LoadModule log_sql_module/LoadModule log_sql_module/" $PATH_HTTPD_CONF_TEMP >$TMP_FILE
						cat <$TMP_FILE >$PATH_HTTPD_CONF_TEMP
					else
						if grep -i "LoadModule sql_log_module" $PATH_HTTPD_CONF_TEMP >/dev/null 2>&1
						then
							echo " ok!"
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
			echo $HTTPD_MODULES_CONFIG enable sql_log_module
			$HTTPD_MODULES_CONFIG enable sql_log_module
			$HTTPD_MODULES_CONFIG enable mod_log_sql # just in case
			echo " enabled by $HTTPD_MODULES_CONFIG"
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

	echo -n "Checking for AllowOverride..."
	if grep "AllowOverride None" $PATH_HTTPD_CONF
	then
		echo "AllowOverride None -> AllowOverride AuthConfig FileInfo Limit Indexes"
		sed "s/AllowOverride None/AllowOverride AuthConfig FileInfo Limit Indexes/" $PATH_HTTPD_CONF >$TMP_FILE
		cat <$TMP_FILE >$PATH_HTTPD_CONF
	else
		echo "ok!"
	fi

	echo "=> Adding DTC's directives to httpd.conf end"
	echo "# Configured by DTC v0.12 : please do not touch this line !
Include $PATH_DTC_ETC/vhosts.conf
Listen 80
Listen 443

LogSQLLoginInfo localhost "$conf_mysql_login" "$conf_mysql_pass"
LogSQLSocketFile /var/run/mysqld/mysqld.sock
LogSQLDatabase apachelogs
LogSQLCreateTables On
LogSQLTransferLogFormat IAbhRrSsU
# End of DTC configuration v0.12 : please don't touch this line !" >>$PATH_HTTPD_CONF
	if [ -f $TMP_FILE ]
	then
		rm -f $TMP_FILE
	fi
fi

PATH_PAMD_SMTP=/etc/pam.d/smtp
if [ -e /etc/pam.d/ ]
then
	echo "===> Adding configuration inside "$PATH_PAMD_SMTP
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
echo "===> Adding inclusion to named.conf"
if grep "Configured by DTC" $PATH_NAMED_CONF
then
	echo "named.conf has been configured before : skiping include insertion !"
else
	echo "Including named.conf in $PATH_NAMED_CONF"
	if ! [ -f $PATH_NAMED_CONF.DTC.backup ]
	then
		cp -f $PATH_NAMED_CONF $PATH_NAMED_CONF.DTC.backup
	fi
	echo "// Configured by DTC v0.10 : please don't touch this line !" >/tmp/DTC_install_named.conf
	echo "include \"$PATH_DTC_ETC/named.conf\";" >>/tmp/DTC_install_named.conf
	touch $PATH_DTC_ETC/named.conf
	cat </tmp/DTC_install_named.conf >>$PATH_NAMED_CONF
fi

# only try and do qmail stuff if we have qmail installed! (check the control directory)
if [ -f $PATH_QMAIL_CTRL ]
then
	#
	# Install the qmail links in the /etc/qmail
	#
	echo "===> Linking qmail control files to DTC generated files"
	if ! [ -f $PATH_QMAIL_CTRL/rcpthosts.DTC.backup ]
	then
		cp -f $PATH_QMAIL_CTRL/rcpthosts $PATH_QMAIL_CTRL/rcpthosts.DTC.backup
	fi
	rm -f $PATH_QMAIL_CTRL/rcpthosts
	touch $PATH_DTC_ETC/rcpthosts
	ln -s $PATH_DTC_ETC/rcpthosts $PATH_QMAIL_CTRL/rcpthosts

	touch $PATH_QMAIL_CTRL/virtualdomains
	if ! [ -f $PATH_QMAIL_CTRL/virtualdomains.DTC.backup ]
	then
		cp -f $PATH_QMAIL_CTRL/virtualdomains $PATH_QMAIL_CTRL/virtualdomains.DTC.backup
	fi
	rm -f $PATH_QMAIL_CTRL/virtualdomains
	touch $PATH_DTC_ETC/virtualdomains
	ln -s $PATH_DTC_ETC/virtualdomains $PATH_QMAIL_CTRL/virtualdomains

	if ! [ -f /var/qmail/users/assign.DTC.backup ]
	then
		cp -f /var/qmail/users/assign /var/qmail/users/assign.DTC.backup
	fi
	rm -f /var/qmail/users/assign
	touch $PATH_DTC_ETC/assign
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
	if ! [ -f /etc/poppasswd.DTC.backup ]
	then
		cp -f /etc/poppasswd /etc/poppasswd.DTC.backup
	fi
	rm -f /etc/poppasswd
	touch $PATH_DTC_ETC/poppasswd
	ln -s $PATH_DTC_ETC/poppasswd /etc/poppasswd
fi

# 
# Modify the postfix main.cf to include virtual delivery options
#

if [ -f $PATH_POSTFIX_CONF ]
then
	echo "===> Linking postfix control files to DTC generated files"
	if grep "Configured by DTC" "$PATH_POSTFIX_CONF"
	then
		echo "Postfix main.cf has been configured before, not adding virtual mailbox options"
	else
		echo "Inserting DTC configuration inside $PATH_POSTFIX_CONF"
		echo "# Configured by DTC v0.12 : Please don't touch this line !" > /tmp/DTC_config_postfix_main.cf
		echo "
# DTC virtual configuration
virtual_mailbox_domains = hash:$PATH_DTC_ETC/postfix_virtual_mailbox_domains
virtual_mailbox_base = /
virtual_mailbox_maps = hash:$PATH_DTC_ETC/postfix_vmailbox
virtual_minimum_uid = 100
virtual_uid_maps = static:65534
virtual_gid_maps = static:65534
virtual_alias_maps = hash:$PATH_DTC_ETC/postfix_virtual
virtual_uid_maps = hash:$PATH_DTC_ETC/postfix_virtual_uid_mapping" >> /tmp/DTC_config_postfix_main.cf
		echo "# End of DTC configuration v0.12 : please don't touch this line !" >> /tmp/DTC_config_postfix_main.cf

		# now to insert it at the end of the actual main.cf
		cat </tmp/DTC_config_postfix_main.cf >>$PATH_POSTFIX_CONF
		rm /tmp/DTC_config_postfix_main.cf
	fi

fi


#
# Install courier mysql authenticaion
#
if [ -f $PATH_COURIER_CONF_PATH/authdaemonrc ]
then
	echo "===> Adding directives to Courier authdaemonrc"
	if grep "Configured by DTC" $PATH_COURIER_CONF_PATH/authdaemonrc
	then
		echo "authdaemonrc has been configure before: skipping include insertion !"
	else
		echo "Inserting DTC configuration inside "$PATH_COURIER_CONF_PATH/authdaemonrc
		if ! [ -f $PATH_COURIER_CONF_PATH.DTC.backup ]
		then
			cp -f $PATH_COURIER_CONF_PATH/authdaemonrc $PATH_COURIER_CONF_PATH.DTC.backup
		fi
		echo "# Configured by DTC v0.12 : Please don't touch this line !" >/tmp/DTC_config_courier.conf
		echo "authmodulelist=\"authmysql authpam\"" >>/tmp/DTC_config_courier.conf
		echo "# End of DTC configuration v0.12 : please don't touch this line !" >>/tmp/DTC_config_courier.conf
		# now append this to the existing configuration file
		cat </tmp/DTC_config_courier.conf >> $PATH_COURIER_CONF_PATH/authdaemonrc
		rm /tmp/DTC_config_courier.conf
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
MYSQL_SELECT_CLAUSE     SELECT concat(id, '@', mbox_host), crypt,  uid, gid, passwd, home, '', quota_size, ''  FROM pop_access  WHERE (id = '\$(local_part)' AND mbox_host = '\$(domain)') OR (id = SUBSTRING_INDEX('\$(local_part)', '%', 1) AND mbox_host = SUBSTRING_INDEX('\$(local_part)', '%', -1))

" > $PATH_COURIER_CONF_PATH/authmysqlrc
	fi	
fi

#
# Install dovecot mysql authenticaion
#
if [ -f $PATH_DOVECOT_CONF ]
then
	echo "===> Adding directives to dovecot.conf"
	if grep "Configured by DTC" $PATH_DOVECOT_CONF
	then
		echo "dovecot.conf has been configure before: skipping include insertion !"
	else
		echo "Inserting DTC configuration inside "$PATH_DOVECOT_CONF
		if ! [ -f $PATH_DOVECOT_CONF.DTC.backup ]
		then
			cp -f $PATH_DOVECOT_CONF $PATH_DOVECOT_CONF.DTC.backup
		fi
		echo "# Configured by DTC v0.12 : Please don't touch this line !" >/tmp/DTC_config_dovecot.conf
		echo "auth_userdb = mysql $PATH_DTC_ETC/dovecot-mysql.conf" >>/tmp/DTC_config_dovecot.conf
		echo "auth_passdb = mysql $PATH_DTC_ETC/dovecot-mysql.conf" >>/tmp/DTC_config_dovecot.conf
		echo "# End of DTC configuration v0.12 : please don't touch this line !" >>/tmp/DTC_config_dovecot.conf
		# now append this to the existing configuration file
		cat </tmp/DTC_config_dovecot.conf >> $PATH_DOVECOT_CONF
		rm /tmp/DTC_config_dovecot.conf
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
echo "===> Adding directives to proftpd.conf"
if grep "Configured by DTC" $PATH_PROFTPD_CONF
then
	echo "proftpd.conf has been configured before : skiping include inssertion !"
else
	echo "Inserting DTC configuration inside "$PATH_PROFTPD_CONF
	if ! [ -f $PATH_PROFTPD_CONF.DTC.backup ]
	then
		cp -f $PATH_PROFTPD_CONF $PATH_PROFTPD_CONF.DTC.backup
	fi
	echo "# Configured by DTC v0.10 : Please don't touch this line !" >/tmp/DTC_config_proftpd.conf
# This directive is not used anymore in newer version of proftpd
#	echo "#UserReverseDNS	off" >>/tmp/DTC_config_proftpd.conf
	echo "IdentLookups	off" >>/tmp/DTC_config_proftpd.conf
	echo "DefaultRoot	~" >>/tmp/DTC_config_proftpd.conf
	echo "SQLAuthenticate	on" >>/tmp/DTC_config_proftpd.conf
	echo "SQLConnectInfo	"$conf_mysql_db"@"$conf_mysql_host" "$conf_mysql_login" "$conf_mysql_pass >>/tmp/DTC_config_proftpd.conf
	echo "SQLAuthTypes	Plaintext" >>/tmp/DTC_config_proftpd.conf
	echo "SQLUserInfo	ftp_access login password uid gid homedir shell" >>/tmp/DTC_config_proftpd.conf
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

# End of DTC configuration v0.10 : please don't touch this line !" >>/tmp/DTC_config_proftpd.conf
	cat </tmp/DTC_config_proftpd.conf >>$PATH_PROFTPD_CONF
	rm /tmp/DTC_config_proftpd.conf
fi

#
# Install the cron php4 script in the /etc/crontab
#
echo "===> Installing cron script in /etc/crontab"
if grep "Configured by DTC" /etc/crontab
then
	echo "/etc/crontab has been configured before : skinping include inssertion"
else
	echo "Inserting DTC cronjob in /etc/crontab"
	if ! [ -f /etc/crontab.DTC.backup ]
	then
		cp -f /etc/crontab /etc/crontab.DTC.backup
	fi
	echo "# Configured by DTC v0.10 : Please don't touch this line !" >/tmp/DTC_config_crontab
	echo "00,10,20,30,40,50 * * * * root cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/cron.php >>/var/log/dtc.log" >>/tmp/DTC_config_crontab
	cat </tmp/DTC_config_crontab >>/etc/crontab
	rm /tmp/DTC_config_crontab
fi

# This avoid hanging when (re)starting daemons under debian
if [ "$UNIX_TYPE" = "debian" ]
then
	db_stop
fi

# add the default password to .htpasswd if it doesn't exist already
if [ -e $conf_hosting_path/.htpasswd ]; then 
	echo "OK, you have your "$conf_hosting_path"/.htpasswd setup already!"
else 
	echo "Creating "$conf_hosting_path"/.htpasswd with username '$conf_adm_login' and password '$conf_adm_pass'"
	/usr/bin/htpasswd -cb "$conf_hosting_path"/.htpasswd "$conf_adm_login" $conf_adm_pass
fi

if [ -e $PATH_DTC_ADMIN/.htaccess ]; then
	echo "OK, you have your "$PATH_DTC_ADMIN"/.htaccess setup already!"
else
	echo "Creating "$PATH_DTC_ADMIN"/.htaccess file."
	echo "AuthName \"DTC root control panel login!\"
AuthType Basic
AuthUserFile "$conf_hosting_path"/.htpasswd
require valid-user" >$PATH_DTC_ADMIN/.htaccess
fi

echo "***********************************************************"
echo "*** Please wait while DTC configures all the daemons... ***"
echo "***********************************************************"
cd $PATH_DTC_ADMIN; $PATH_PHP_CGI $PATH_DTC_ADMIN/cron.php

echo "--- --- --- INSTALLATION FINISHED --- --- ---"
echo "DTC has finished to install. You can point your favorite"
echo "browser to: http(s)://"$dtc_admin_subdomain"."$main_domain_name"/dtcadmin/"
echo "Note that if you install some other mail servers, whatever"
echo "it is (qmail, postfix, courier, dovecot, etc...), you have"
echo "to re-run DTC's installer script so it can configurate it."
echo "Dont forget to edit the forwarders part of your bind"
echo "configuration if not done already !"
echo ""
echo "Please visit DTC home:"
echo "http://www.gplhost.com/?rub=softwares&sousrub=dtc"

exit 0
