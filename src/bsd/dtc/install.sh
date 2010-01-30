#!/bin/sh

# This is the DTC's BSD interactive install configuration script
# called by the make install
# made by Thomas Goirand <thomas@goirand.fr> and Frederic Cambus


PREFIX=%%PREFIX%%
LOCALBASE=%%LOCALBASE%%
QMAIL_DIR=%%QMAIL_DIR%%

UID=`id|cut -d= -f2|cut -d\( -f1`

if [ $UID -ne 0 ]
then
	echo "##### WARNING #####"
	echo ""
	echo "In order to work correctly, the"
	echo "installation program should be run"
	echo "as root. Press ENTER to continue"
	echo "anyway or CTRL-C to abort process."
	read XX
fi

# DATABASE CONFIGURATION
PSMYSQL=`ps -axf|grep mysqld|grep -v grep|awk '{print $1}'`

if [ "$PSMYSQL" = "" ]
then
	echo "### MYSQL CONFIGURATION ###"
	echo ""
	echo "WARNING: Your MySQL Server MUST be running."
	echo "If not, please add mysql_enable=\"YES\" in your"
	echo "/etc/rc.conf and issue the following cmd:"
	echo "%%PREFIX%%/etc/rc.d/mysql-server start"
	echo ""
	echo "Press ENTER to continue, CTRL-C to abort install"
	read XX
fi

. %%WWWDIR%%/www/dtc/admin/install/bsd_config
. %%WWWDIR%%/www/dtc/admin/install/interactive_installer
. %%WWWDIR%%/www/dtc/admin/install/functions

enableBsdBind
copyBsdPhpIni
interactiveInstaller
DTCinstallPackage
DTCsetupDaemons
