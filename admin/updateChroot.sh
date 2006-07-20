#!/bin/bash
pushd /var/www/sites
CHROOT_PATH=/var/www/chroot
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
