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
PATH_QMAIL_CTRL="${QMAIL_DIR}/control"
