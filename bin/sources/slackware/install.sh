#!/bin/sh

# This is the modification of DTC's BSD interactive install configuration script
# called by the make install
# made by Thomas Goirand <thomas@goirand.fr> and Frederic Cambus
# ver 0.0.1

LOCALBASE=/usr/share

# DATABASE CONFIGURATION
echo "### MYSQL CONFIGURATION ###"
echo ""
echo "WARNING: Your MySQL Server MUST be running."
echo "If not, please issue the following cmd:"
echo "/etc/rc.d/rc.mysql start"
echo ""

#/bin/sh

echo "Copying DTC's php scripts to /usr/share..."
PATH_DTC_SHARED="/usr/share/dtc"
if [ -e $PATH_DTC_SHARED ] ; then
	rm -rf $PATH_DTC_SHARED/admin $PATH_DTC_SHARED/client $PATH_DTC_SHARED/shared $PATH_DTC_SHARED/email $PATH_DTC_SHARED/doc
fi
mkdir -p $PATH_DTC_SHARED
cp -prf ./ $PATH_DTC_SHARED

. ${LOCALBASE}/dtc/admin/install/slack_config
. ${LOCALBASE}/dtc/admin/install/interactive_installer
. ${LOCALBASE}/dtc/admin/install/functions


interactiveInstaller
DTCinstallPackage
DTCsetupDaemons
