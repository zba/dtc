#!/bin/sh

# Tarball uninstall sh script for DTC
# Written by Thomas GOIRAND <thomas@goirand.fr>
# under LGPL Licence

echo "### DEAMON PATH CONFIGURATION ###"
PATH_HTTPD_CONF="${PKG_PREFIX}/etc/apache/httpd.conf"
PATH_NAMED_CONF="/etc/namedb/named.conf"
PATH_PROFTPD_CONF="${PKG_PREFIX}/etc/proftpd.conf"
PATH_QMAIL_CTRL="/var/qmail/control"
