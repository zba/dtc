#!/bin/sh

# Tarball uninstall sh script for DTC
# Written by Thomas GOIRAND <thomas [ at ] goirand.fr>
# under LGPL Licence

UNIX_TYPE=gentoo

echo "### DEAMON PATH CONFIGURATION ###"

PATH_HTTPD_CONF="/etc/apache2/httpd.conf"
PATH_NAMED_CONF="/etc/named.conf"
PATH_POSTFIX_ETC="/etc/postfix"
PATH_AWSTATS_ETC="/etc/awstats"
PATH_POSTFIX_CONF="${PATH_POSTFIX_ETC}/main.cf"
PATH_SASL_PASSWD2="/usr/sbin/saslpasswd2"
PATH_USERDB_DB="/usr/sbin/userdb"
PATH_MAILDROP_BIN="/usr/bin/maildrop"
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
