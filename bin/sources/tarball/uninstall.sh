#!/bin/sh

# Tarball uninstall sh script for DTC
# Written by Thomas GOIRAND <thomas@goirand.fr>
# under LGPL Licence

UNIX_TYPE=generic

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
echo "Where is located your proftpd.conf ?"
echo -n "proftpd control path [/etc/proftpd.conf]: "
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
