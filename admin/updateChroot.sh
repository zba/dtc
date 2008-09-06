#!/bin/bash
MAIN_DIR=$1
CHROOT_PATH=$2
if [ -z ""$MAIN_DIR ]; then
	MAIN_DIR=/var/www/sites
fi
if [ -z ""$CHROOT_PATH ]; then
	CHROOT_PATH=/var/lib/dtc/chroot_template
fi
pushd $MAIN_DIR
for i in */*/subdomains/*/
do 
	echo "=> Updating chroot in $i..."
	if [ "`uname -s`" == "FreeBSD" ] ; then		# same for OSX?
		cp -fpRv $CHROOT_PATH/* $i
	else
		cp -fupRv $CHROOT_PATH/* $i
	fi
	# remove un-needed directories
	rm -rf $i/etc/pam.d
	rm -rf $i/etc/security
	# if we can't find nogroup, then set to 65534
	chown -R dtc:dtcgrp $i
done
