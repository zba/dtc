#!/bin/sh

# this chroot creation script will only work for debian testing
# please modify this for your particular distribution/OS
# Damien Mascord <tusker@tusker.org>

# Added support for Debian stable and integrate it in install
# script for all OS (path needs to be checked for BSD and RedHat...
# so this script is for the moment UNTESTED)
# Thomas GOIRAND <thomas [ at ] goirand.fr>

# first check to see if we have the correct command line settings
#if [ "$1" = "" ]
#then
#	echo "Usage: $0 <directory to create chroot in> <webuser> <webgroup>"
#	exit 1
#fi

# assign our variables
CHROOT_DIR=$conf_chroot_path
WEB_USER=nobody
if [ $UNIX_TYPE"" = "freebsd" ] ; then
	WEB_GROUP=nogroup
else
	WEB_GROUP=nogroup
fi

if [ ""$VERBOSE_INSTALL = "yes" ] ;then
	echo "===> Creating chroot tree in "$CHROOT_DIR
fi

# set our umask so things are created with the correct group perms
umask 022

# now onto the creation
mkdir -p $CHROOT_DIR
cd $CHROOT_DIR

# create directory structure
mkdir -p etc dev bin lib tmp var/tmp var/run sbin
mkdir -p usr/bin usr/lib usr/libexec usr/share usr/lib/zoneinfo

# make devices - adjust MAJOR/MINOR as appropriate ( see ls -l /dev/* )
if ! [ -e dev/null ]
then
	if [ $UNIX_TYPE"" = "freebsd" ];
	then
		mknod dev/null    c  2 2   # FreeBSD?
	else
		mknod dev/null    c  1 3   # Linux
	fi
fi

if [ $UNIX_TYPE"" = "freebsd" ] ; then
	if [ $kernel"" = "OpenBSD" ] ; then
		if ! [ -e dev/urandom ] ; then
			mknod dev/urandom c 45 2   # OpenBSD ?
		fi
	else
		if ! [ -e dev/random ] ; then
			mknod dev/random  c  2 3   # FreeBSD
		fi
		if ! [ -e dev/urandom ] ; then
			mknod dev/urandom  c  2 3
		fi
	fi
else
	if ! [ -e dev/random ] ; then
		mknod dev/random  c  1 8   # Linux
	fi
	if ! [ -e dev/urandom ] ; then
		mknod dev/urandom c  1 9   # Linux
	fi
fi

# some external programs may need these:

if [ $UNIX_TYPE"" = "freebsd" ] ; then
	if ! [ -e dev/stdin ] ; then
		mknod dev/stdin   c 22 0   # FreeBSD, OpenBSD
	fi
	if ! [ -e dev/stdout ] ; then
		mknod dev/stdout  c 22 1   # FreeBSD, OpenBSD
	fi
	if ! [ -e dev/stderr ] ; then
		mknod dev/stderr  c 22 2   # FreeBSD, OpenBSD
	fi
fi

# copy required binaries to $CHROOT_DIR/usr/bin and $CHROOT_DIR/bin
cp -pf /usr/bin/file /usr/bin/bzip2 usr/bin/

if [ $UNIX_TYPE"" = "freebsd" ] ; then
	cp -pf /usr/bin/cpio usr/bin
	cp -pf /usr/bin/gunzip /usr/bin/false /usr/bin/su bin/
else
	cp -pf /bin/gunzip /usr/bin/zip /bin/false /bin/su bin/
	cp -pf /bin/cpio usr/bin
fi

# copy required binaries to $CHROOT_DIR/bin
cp -pf /bin/sh /bin/echo /bin/ls /bin/pwd /bin/cat bin/

# copy ldconfig from sbin to $CHROOT_DIR/sbin
cp -pf /sbin/ldconfig sbin/

# copy needed /etc files to $CHROOT_DIR/etc
cp -pf /etc/protocols /etc/services /etc/hosts \
  /etc/group /etc/passwd /etc/resolv.conf etc/

if [ -e /etc/host.conf ] ; then
	cp -pf /etc/host.conf etc/
fi

if [ -e /etc/nsswitch.conf ] ; then
	cp -pf /etc/nsswitch.conf etc/
fi

if ! [ $UNIX_TYPE"" = "freebsd" ] ; then
	cp -pf /etc/localtime etc/
fi

# copy shared libraries to $CHROOT_DIR/lib
#   (check:  ldd /usr/bin/perl (or other binary) to see which ones are needed)
#
#FreeBSD: 
#for j in \
if [ $UNIX_TYPE"" = "freebsd" ] 
then
	cp -pf /usr/lib/libc.so* /usr/lib/libm.so* \
	  /usr/lib/libstdc\+\+.so* /usr/lib/libz.so.2 usr/lib/
	cp -pf /usr/libexec/ld-elf.so* usr/libexec/
else
	#Linux:
	cp -pf /lib/libdl.so.2 /lib/libm.so.6 /lib/libpthread.so.0 \
	  /lib/libc.so.6 /lib/libcrypt.so.1 /lib/ld-linux.so.2 \
	  /lib/libncurses.so.5 /usr/lib/libz.so.1 \
	  /lib/librt.so.1 \
	  /lib/libpam.so.0 /lib/libpam_misc.so.0 lib/
	if [ -e /usr/lib/libmagic.so.1 ]
	then
		cp -pf /usr/lib/libmagic.so.1 lib/
	fi
	#ln lib/ld-2.3.2.so lib/ld-linux.so.2
fi

# magic files needed by file(1). Different versions and installations
# expect magic files in different locations. Check the documentation.
# Some usual locations are:
if [ -e /usr/share/misc/file ]
then
	#cp -pf /usr/local/share/file/*  usr/local/share/file/
	mkdir -p usr/share/misc/file
	cp -pf /usr/share/misc/file/magic*   usr/share/misc/file
	#cp -pf /usr/share/magic         usr/share/
fi

# set protections
chmod 1770 tmp
chmod 1770 var/tmp
chmod 666 dev/null
chmod 644 dev/*random

#now need to copy over the perl binary and some modules
cp -pf /usr/bin/perl usr/bin/

# now create our ld.so cache
chroot $CHROOT_DIR ./sbin/ldconfig 
# just in case we have wiped our /etc/ld.so.cache (run locally)
/sbin/ldconfig
