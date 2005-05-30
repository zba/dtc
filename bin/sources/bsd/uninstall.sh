#!/bin/sh

# Tarball uninstall sh script for DTC
# Written by Thomas GOIRAND <thomas@goirand.fr>
# under LGPL Licence

UNIX_TYPE=freebsd

PREFIX=%%PREFIX%%
LOCALBASE=%%LOCALBASE%%
QMAIL_DIR=%%QMAIL_DIR%%

echo "### DEAMON PATH CONFIGURATION ###"
PATH_HTTPD_CONF="${LOCALBASE}/etc/apache/httpd.conf"
PATH_NAMED_CONF="/etc/namedb/named.conf"
PATH_PROFTPD_CONF="${LOCALBASE}/etc/proftpd.conf"
PATH_DOVECOT_CONF="${LOCALBASE}/etc/dovecot.conf"
PATH_COURIER_CONF_PATH="${LOCALBASE}/etc/courier"
PATH_POSTFIX_CONF="${LOCALBASE}/etc/postfix/main.cf"
PATH_POSTFIX_ETC="${LOCALBASE}/etc/postfix"
PATH_SASL_PASSWD2="${LOCALBASE}/sbin/saslpasswd2"
PATH_AWSTATS_ETC=${LOCALBASE}/etc/awstats
PATH_QMAIL_CTRL="${QMAIL_DIR}/control"
PATH_CRONTAB_CONF="/etc/crontab"
