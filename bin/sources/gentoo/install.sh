#/bin/sh

# This is the DTC's tarball interactive install configuration script
# made by Thomas Goirand <thomas [ at ] goirand.fr>

UNIX_TYPE=gentoo

VERBOSE_INSTALL=yes

# Create our group and user
CONF_DTC_SYSTEM_USERNAME=dtc
CONF_DTC_SYSTEM_GROUPNAME=dtc
if getent group ${CONF_DTC_SYSTEM_GROUPNAME} >/dev/null ; then
        echo "Group ${CONF_DTC_SYSTEM_GROUPNAME} already exists: skiping creation!"
else
        groupadd ${CONF_DTC_SYSTEM_GROUPNAME}
fi
CONF_DTC_SYSTEM_GID=`getent group ${CONF_DTC_SYSTEM_GROUPNAME} | cut -d':' -f3`
if getent passwd ${CONF_DTC_SYSTEM_USERNAME} >/dev/null ; then
        echo "User ${CONF_DTC_SYSTEM_USERNAME} already exists: skiping creation!"
else
        useradd -m -s /bin/false -g ${CONF_DTC_SYSTEM_GROUPNAME} ${CONF_DTC_SYSTEM_USERNAME}
fi
CONF_DTC_SYSTEM_UID=`getent passwd ${CONF_DTC_SYSTEM_USERNAME} | cut -d':' -f3`

PATH_HTTPD_CONF="/etc/apache2/httpd.conf"
PATH_NAMED_CONF="/etc/bind/named.conf"
PATH_POSTFIX_ETC="/etc/postfix"
PATH_AWSTATS_ETC="/etc/awstats"
PATH_POSTFIX_CONF="${PATH_POSTFIX_ETC}/main.cf"
PATH_SASL_PASSWD2="/usr/sbin/saslpasswd2"
PATH_USERDB_BIN="/usr/sbin/userdb"
PATH_MAILDROP_BIN="/usr/bin/maildrop"
PATH_COURIER_CONF_PATH="/etc/courier"
PATH_COURIER_AUTHD_CONF_PATH="/etc/courier"
if [ ! -f $PATH_COURIER_AUTHD_CONF_PATH/authdaemonrc ]; then
        if [ -f /etc/authlib/authdaemonrc ]; then
                PATH_COURIER_AUTHD_CONF_PATH="/etc/authlib"
        fi
fi
PATH_DOVECOT_CONF="/etc/dovecot.conf"
PATH_PROFTPD_CONF="/etc/proftpd/proftpd.conf"
PATH_QMAIL_CTRL="/var/qmail/control"
PATH_PHP_CGI="/usr/bin/php"
PATH_DTC_SHARED="/usr/share/dtc"
PATH_DTC_ADMIN=$PATH_DTC_SHARED"/admin"
PATH_DTC_CLIENT=$PATH_DTC_SHARED"/client"
PATH_DTC_ETC="${PATH_DTC_SHARED}/etc"
PATH_AMAVISD_CONF=/etc/amavisd.conf
PATH_CLAMAV_CONF=/etc/clamd.conf
PATH_CRONTAB_CONF=/etc/crontab
PATH_CGIBIN=/usr/lib/cgi-bin

USER_ADD_CMD=useradd
USER_ADD_CMD=groupadd
USER_MOD_CMD=usermod
PASSWD_CMD=passwd

echo "Copying DTC's php scripts to /usr/share..."

if [ -e $PATH_DTC_SHARED ];
then
	rm -rf $PATH_DTC_SHARED/admin $PATH_DTC_SHARED/client $PATH_DTC_SHARED/shared $PATH_DTC_SHARED/email $PATH_DTC_SHARED/doc
fi
mkdir -p $PATH_DTC_SHARED
cp -prf ./ $PATH_DTC_SHARED

