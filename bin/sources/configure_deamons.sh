
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
# Include $PATH_DTC_ETC/vhosts.conf in $PATH_HTTPD_CONF
#

echo "===> Adding inclusion in httpd.conf"
if grep "Configured by DTC" $PATH_HTTPD_CONF
then
	echo "httpd.conf has been configured before : skiping include inssertion !"
else
	echo "Including DTC's vhosts.conf in $PATH_HTTPD_CONF"
	if ! [ -f $PATH_HTTPD_CONF.DTC.backup ]
	then
		cp -f "$PATH_HTTPD_CONF" "$PATH_HTTPD_CONF.DTC.backup"
	fi
	echo "# Configured by DTC v0.10 : please do not touch this line !" >/tmp/dtc_Temp_httpd
	echo "Include $PATH_DTC_ETC/vhosts.conf" >>/tmp/dtc_Temp_httpd
	touch $PATH_DTC_ETC/vhosts.conf
	cat </tmp/dtc_Temp_httpd >>"$PATH_HTTPD_CONF"
	rm /tmp/dtc_Temp_httpd
fi

#
# include $PATH_DTC_ETC/named.zones in $PATH_NAMED_CONF
#
echo "===> Adding inclusing to named.conf"
if grep "Configured by DTC" $PATH_NAMED_CONF
then
	echo "named.conf has been configured before : skiping include inssertion !"
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
	echo "SQLAuthenticate	on" >>/tmp/DTC_config_proftpd.conf
	echo "SQLConnectInfo	"$conf_mysql_db"@"$conf_mysql_host" "$conf_mysql_login" "$conf_mysql_pass >>/tmp/DTC_config_proftpd.conf
	echo "SQLAuthTypes	Plaintext" >>/tmp/DTC_config_proftpd.conf
	echo "SQLUserInfo	ftp_access login password uid gid homedir shell" >>/tmp/DTC_config_proftpd.conf
	echo "" >>/tmp/DTC_config_proftpd.conf
	echo "# End of DTC configuration v0.10 : please don't touch this line !" >>/tmp/DTC_config_proftpd.conf
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

echo "DTC has finished to install. Under normal circonstances, the DTC"
echo "cronjob will enable an administrator panel in less than 10 minutes"
echo "(you can watch /var/log/dtc.log and /var/log/syslog to see if it"
echo "works). Type \"cd /usr/share/dtc/admin ; php4 cron.php\" to do it"
echo "manualy. When configuration is done, you can point your favorite"
echo "browser to: http(s)://"$dtc_admin_subdomain"."$main_domain_name"/dtcadmin/"
echo "Please leave feedback in the dtc open support forum:"
echo "http://thomas.goirand.fr/d.t.c/phpBB2/"
