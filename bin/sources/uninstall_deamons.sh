
# Multi OS (Unix system) uninstall sh script for DTC
# Written by Thomas GOIRAND <thomas@goirand.fr>
# under LGPL Licence

# The configuration for all thoses variables must be written BEFORE this
# script. Do the start of the script for your operating system.
# I did mine for debian in debian/postinst

# Please note this script
# doeas not start with a :

#!/bin/sh

# because it's up to you to write it ! :)
# Do a "cat uninstall_deamons.sh >> your_OS_vars_setup_script.sh"

#
# uninstall named.conf
#

if grep "Configured by DTC" $PATH_NAMED_CONF
then
	echo "===> Uninstalling inclusion from named.conf"
	grep -v "Configured by DTC" $PATH_NAMED_CONF >/tmp/dtc_uninstall.named.conf
	grep -v "include \"$PATH_DTC_ETC/named.conf\"" /tmp/dtc_uninstall.named.conf >/tmp/dtc_uninstall2.named.conf
	cp -f $PATH_NAMED_CONF $PATH_NAMED_CONF.DTC.removed
	mv /tmp/dtc_uninstall2.named.conf $PATH_NAMED_CONF
	rm -f /tmp/dtc_uninstall.named.conf
fi

#
# uninstall httpd.conf
#
if grep "Configured by DTC" $PATH_HTTPD_CONF
then
	echo "===> Uninstalling inclusion from httpd.conf"
	if grep "Configured by DTC v0.10" $PATH_HTTPD_CONF >/dev/null 2>&1
	then
		grep -v "Configured by DTC" $PATH_HTTPD_CONF | grep -v "Include $PATH_DTC_ETC/vhosts.conf" >/tmp/dtc_uninstall.httpd.conf
		cp -f $PATH_HTTPD_CONF $PATH_HTTPD_CONF.DTC.removed
		mv /tmp/dtc_uninstall.httpd.conf $PATH_HTTPD_CONF
	else
		start_line=`grep -n "Configured by DTC" $PATH_HTTPD_CONF | cut -d":" -f1`
		end_line=`grep -n "End of DTC configuration" $PATH_HTTPD_CONF| cut -d":" -f1`
		nbr_line=`cat $PATH_HTTPD_CONF | wc -l`
		cat $PATH_HTTPD_CONF | head -n $(($start_line - 1 )) >/tmp/DTC_uninstall.httpd.conf
		cat $PATH_HTTPD_CONF | tail -n $(($nbr_line - $end_line )) >>/tmp/DTC_uninstall.httpd.conf
		cat </tmp/DTC_uninstall.httpd.conf >$PATH_HTTPD_CONF
	fi
fi

#
# uninstall courier config details
#

echo "===> Uninstalling inclusion from courier authdaemonrc"
if grep "Configured by DTC" $PATH_COURIER_CONF_PATH/authdaemonrc
then
	start_line=`grep -n "Configured by DTC" $PATH_COURIER_CONF_PATH/authdaemonrc | cut -d":" -f1`
	end_line=`grep -n "End of DTC configuration" $PATH_COURIER_CONF_PATH/authdaemonrc| cut -d":" -f1`
	nbr_line=`cat $PATH_COURIER_CONF_PATH/authdaemonrc | wc -l`
	cat $PATH_COURIER_CONF_PATH/authdaemonrc | head -n $(($start_line - 1 )) >/tmp/DTC_uninstall.courier.conf
	cat $PATH_COURIER_CONF_PATH/authdaemonrc | tail -n $(($nbr_line - $end_line )) >>/tmp/DTC_uninstall.courier.conf
	cp -f $PATH_COURIER_CONF_PATH/authdaemonrc $PATH_COURIER_CONF_PATH/authdaemonrc.DTC.removed
	mv /tmp/DTC_uninstall.courier.conf $PATH_COURIER_CONF_PATH/authdaemonrc
fi
#
# uninstall dovecot.conf
#

echo "===> Uninstalling inclusion from dovecot.conf"
if grep "Configured by DTC" $PATH_DOVECOT_CONF
then
	start_line=`grep -n "Configured by DTC" $PATH_DOVECOT_CONF | cut -d":" -f1`
	end_line=`grep -n "End of DTC configuration" $PATH_DOVECOT_CONF| cut -d":" -f1`
	nbr_line=`cat $PATH_DOVECOT_CONF | wc -l`
	cat $PATH_DOVECOT_CONF | head -n $(($start_line - 1 )) >/tmp/DTC_uninstall.dovecot.conf
	cat $PATH_DOVECOT_CONF | tail -n $(($nbr_line - $end_line )) >>/tmp/DTC_uninstall.dovecot.conf
	cp -f $PATH_DOVECOT_CONF $PATH_DOVECOT_CONF.DTC.removed
	mv /tmp/DTC_uninstall.dovecot.conf $PATH_DOVECOT_CONF
fi
#
# uninstall proftpd.conf
#

echo "===> Uninstalling inclusion from proftpd.conf"
if grep "Configured by DTC" $PATH_PROFTPD_CONF
then
	start_line=`grep -n "Configured by DTC" $PATH_PROFTPD_CONF | cut -d":" -f1`
	end_line=`grep -n "End of DTC configuration" $PATH_PROFTPD_CONF| cut -d":" -f1`
	nbr_line=`cat $PATH_PROFTPD_CONF | wc -l`
	cat $PATH_PROFTPD_CONF | head -n $(($start_line - 1 )) >/tmp/DTC_uninstall.profptd.conf
	cat $PATH_PROFTPD_CONF | tail -n $(($nbr_line - $end_line )) >>/tmp/DTC_uninstall.profptd.conf
	cp -f $PATH_PROFTPD_CONF $PATH_PROFTPD_CONF.DTC.removed
	mv /tmp/DTC_uninstall.profptd.conf $PATH_PROFTPD_CONF
fi

#
# uninstall postfix/main.cf
#

echo "===> Uninstalling inclusion from proftpd.conf"
if grep "Configured by DTC" $PATH_POSTFIX_CONF
then
	start_line=`grep -n "Configured by DTC" $PATH_POSTFIX_CONF | cut -d":" -f1`
	end_line=`grep -n "End of DTC configuration" $PATH_POSTFIX_CONF| cut -d":" -f1`
	nbr_line=`cat $PATH_POSTFIX_CONF | wc -l`
	cat $PATH_POSTFIX_CONF | head -n $(($start_line - 1 )) >/tmp/DTC_uninstall.postfix.conf
	cat $PATH_POSTFIX_CONF | tail -n $(($nbr_line - $end_line )) >>/tmp/DTC_uninstall.postfix.conf
	cp -f $PATH_POSTFIX_CONF $PATH_POSTFIX_CONF.DTC.removed
	mv /tmp/DTC_uninstall.postfix.conf $PATH_POSTFIX_CONF
fi

#
# Uninstall qmail
#

echo "===> Uninstalling from qmail"
if [ -e /var/qmail ]
then
	if ! [ -f /var/qmail/control/rcpthosts.DTC.backup ] ; then
		cp -f /var/qmail/control/rcpthosts.DTC.backup /var/qmail/control/rcpthosts
	fi

	if ! [ -f /var/qmail/control/virtualdomains.DTC.backup ] ; then
		cp -f /var/qmail/control/virtualdomains.DTC.backup /var/qmail/control/virtualdomains
	fi

	if ! [ -f /var/qmail/control/users/assign.DTC.backup ] ; then
		cp -f /var/qmail/control/users/assign.DTC.backup /var/qmail/control/users/assign
	fi

	if ! [ -f /etc/poppasswd.DTC.backup ] ; then
		cp -f /etc/poppasswd.DTC.backup /etc/poppasswd
	fi
fi
