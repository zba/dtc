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
	nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nobody`
	# if we can't find the nobody group, try nogroup
	if [ -z ""$nobodygroup ]; then
		nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nogroup`
	fi
	# if we can't find nogroup, then set to 65534
	if [ -z ""$nobodygroup ]; then
		nobodygroup=65534
	fi
	chown -R nobody:$nobodygroup $i
done
