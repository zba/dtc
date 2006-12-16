#!/bin/sh

# This is the DTC's BSD interactive install configuration script
# called by the make install
# made by Thomas Goirand <thomas@goirand.fr> and Frederic Cambus


PREFIX=/usr
LOCALBASE=/usr/local
QMAIL_DIR=/var/qmail

# DATABASE CONFIGURATION
echo "### MYSQL CONFIGURATION ###"
echo ""
echo "WARNING: Your MySQL Server MUST be running."
echo "If not, please issue the following cmd:"
echo "/usr/local/etc/rc.d/mysql-server.sh start"
echo ""

#/bin/sh

echo "Copying DTC's php scripts to /usr/share..."
PATH_DTC_SHARED="/usr/local/www/dtc"
if [ -e $PATH_DTC_SHARED ] ; then
	rm -rf $PATH_DTC_SHARED/admin $PATH_DTC_SHARED/client $PATH_DTC_SHARED/shared $PATH_DTC_SHARED/email $PATH_DTC_SHARED/doc
fi
mkdir -p $PATH_DTC_SHARED
cp -prf ./ $PATH_DTC_SHARED

. ${LOCALBASE}/www/dtc/admin/install/bsd_config
. ${LOCALBASE}/www/dtc/admin/install/interactive_installer
. ${LOCALBASE}/www/dtc/admin/install/functions

enableBsdBind
copyBsdPhpIni
interactiveInstaller
DTCinstallPackage
DTCsetupDaemons
