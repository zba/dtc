# Redhat RPM uninstall sh script for DTC
# Written by Thomas GOIRAND <thomas [ at ] goirand.fr>
# under LGPL Licence

UNIX_TYPE=redhat

VERBOSE_INSTALL=yes

echo "### DEAMON PATH CONFIGURATION ###"
PATH_HTTPD_CONF=/etc/httpd/conf/httpd.conf
PATH_NAMED_CONF="/etc/named.conf"
PATH_PROFTPD_CONF="/etc/proftpd.conf"
PATH_DOVECOT_CONF="/etc/dovecot.conf"
PATH_COURIER_CONF_PATH="/etc/courier"
PATH_POSTFIX_CONF="/etc/postfix/main.cf"
PATH_POSTFIX_ETC="/etc/postfix"
PATH_QMAIL_CTRL="/var/qmail/control"
PATH_CRONTAB_CONF="/etc/crontab"
PATH_DTC_SHARED=/usr/share/dtc
FREERADIUS_ETC="/etc/raddb"

PATH_DTC_ETC=$PATH_DTC_SHARED"/etc"
PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"

#PATH_DTC_ETC

MKTEMP="mktemp -p /tmp"

