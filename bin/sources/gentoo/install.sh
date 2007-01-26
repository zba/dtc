#/bin/sh

echo "Copying DTC's php scripts to /usr/share..."
PATH_DTC_SHARED="/usr/share/dtc"
if [ -e $PATH_DTC_SHARED ] ; then
	rm -rf $PATH_DTC_SHARED/admin $PATH_DTC_SHARED/client $PATH_DTC_SHARED/shared $PATH_DTC_SHARED/email $PATH_DTC_SHARED/doc
fi
mkdir -p $PATH_DTC_SHARED
cp -prf ./ $PATH_DTC_SHARED

. /usr/share/dtc/admin/install/gentoo_config
. /usr/share/dtc/admin/install/interactive_installer
. /usr/share/dtc/admin/install/functions

interactiveInstaller
DTCinstallPackage
DTCsetupDaemons
