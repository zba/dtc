#!/bin/bash
MAIN_DIR=$1
CHROOT_PATH=$2
if [ -z ""$MAIN_DIR ]; then
	MAIN_DIR=/var/www/sites
fi
if [ -z ""$CHROOT_PATH ]; then
	CHROOT_PATH=/var/www/chroot
fi
pushd $MAIN_DIR
for i in */*/subdomains/*/
do 
	echo "=> Updating chroot in $i..."
	cp -fulpRv $CHROOT_PATH/* $i 
	# remove un-needed directories
	rm -rf $i/etc/pam.d
	rm -rf $i/etc/security
	# chown back to all nobody (should some be root?)
	chown -R 65534:65534 $i
done
