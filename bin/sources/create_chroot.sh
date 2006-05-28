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

if [ $CHROOT_DIR"" = "" ] ; then
	CHROOT_DIR=/var/www/chroot
fi

if [ $UNIX_TYPE"" = "freebsd" ] ; then
	WEB_GROUP=nobody
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
	if [ $UNIX_TYPE"" = "freebsd" -o $UNIX_TYPE"" = "osx" ];
	then
		mknod dev/null    c  2 2   # FreeBSD?
	else
		mknod dev/null    c  1 3   # Linux
	fi
fi

if [ $UNIX_TYPE"" = "freebsd"  -o $UNIX_TYPE"" = "osx" ] ; then
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
if [ $UNIX_TYPE"" = "freebsd"  -o $UNIX_TYPE"" = "osx" ] ; then
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
if [ $UNIX_TYPE"" = "gentoo" ] ; then
	cp -pf /bin/bzip2 usr/bin/
else
	cp -pf /usr/bin/bzip2 usr/bin/
fi

cp -pf /usr/bin/file usr/bin/

if [ $UNIX_TYPE"" = "freebsd" -o $UNIX_TYPE"" = "osx" ] ; then
	cp -pf /usr/bin/cpio usr/bin
	cp -pf /usr/bin/rm /usr/bin/mv /usr/bin/gunzip /usr/bin/tar /usr/bin/false bin/
else
	cp -pf /bin/rm /bin/mv /bin/gunzip /usr/bin/tar /usr/bin/zip /usr/bin/false bin/
	cp -pf /bin/cpio usr/bin
fi

if [ -e /bin/bash ] ; then
	cp -pf /bin/bash bin
fi
if [ -e /usr/bin/bash ] ; then
	cp -pf /usr/bin/bash bin
fi

# copy more required binaries to $CHROOT_DIR/bin
cp -pf /bin/sh /bin/echo /bin/ls /bin/pwd /bin/cat bin/

# copy ldconfig from sbin to $CHROOT_DIR/sbin
if ! [ $UNIX_TYPE"" = "osx" ] ; then
	cp -pf /sbin/ldconfig sbin/
fi

# copy needed /etc files to $CHROOT_DIR/etc
cp -pf /etc/protocols /etc/services /etc/hosts \
  /etc/resolv.conf etc/

# generate /etc/passwd and /etc/group
# ignore errors
if [ $UNIX_TYPE"" = "debian" ] ; then
	set +e
fi
grep daemon /etc/passwd > etc/passwd
grep bin /etc/passwd >> etc/passwd
grep sys /etc/passwd >> etc/passwd
grep man /etc/passwd >> etc/passwd
grep lp /etc/passwd >> etc/passwd
grep mail /etc/passwd >> etc/passwd
grep news /etc/passwd >> etc/passwd
grep uucp /etc/passwd >> etc/passwd
grep www-data /etc/passwd >> etc/passwd
# generate this one manually: grep nobody /etc/passwd >> etc/passwd
grep daemon /etc/group > etc/group
grep bin /etc/group >> etc/group
grep sys /etc/group >> etc/group
grep man /etc/group >> etc/group
grep lp /etc/group >> etc/group
grep mail /etc/group >> etc/group
grep news /etc/group >> etc/group
grep uucp /etc/group >> etc/group
grep www-data /etc/group >> etc/group
grep nogroup /etc/group >> etc/group
grep $WEB_USER /etc/group >> etc/group
if [ $UNIX_TYPE"" = "debian" ] ; then
	set -e
fi

# fix entry for nobody in /etc/passwd
echo "$WEB_USER:x:65534:65534:$WEB_USER:/html:/bin/bash" >> etc/passwd

# create shadow account line for nobody
echo "$WEB_USER::12719:0:99999:7:::" > etc/shadow
chown $WEB_USER:$WEB_GROUP etc/shadow

if [ -e /etc/host.conf ] ; then
	cp -pf /etc/host.conf etc/
fi

if [ -e /etc/ld.conf ] ; then
	cp -pf /etc/ld.conf etc/
fi

if [ -e /etc/nsswitch.conf ] ; then
	cp -pf /etc/nsswitch.conf etc/
fi

if [ -e /etc/localtime ] ; then
	cp -pf /etc/localtime etc/
fi

# copy shared libraries to $CHROOT_DIR/lib
#   (check:  ldd /usr/bin/perl (or other binary) to see which ones are needed)
#
#FreeBSD: 
#for j in \
if [ $UNIX_TYPE"" = "freebsd"  ] ; then
	cp -pf /usr/lib/libc.so* /usr/lib/libm.so* \
	  /usr/lib/libstdc\+\+.so* usr/lib/
	if [ -e /usr/compat/linux/usr/lib/libz.so.1 ] ; then
		cp /usr/compat/linux/usr/lib/libz.so.1 usr/lib
	fi
else
	if [ $UNIX_TYPE"" = "osx"  ] ; then
		cp -pf /usr/lib/dyld /usr/lib/libSystem.B.dylib \
		  /usr/lib/libc.dylib /usr/lib/libdl.dylib \
		  /usr/lib/libncurses.5.dylib /usr/lib/libpam.dylib \
		  /usr/lib/libpthread.dylib usr/lib/
		cp -pf /usr/lib/dylib1.o /usr/lib/libSystem.dylib \
		  /usr/lib/libcrypto.dylib /usr/lib/libm.dylib \
		  /usr/lib/libncurses.dylib /usr/lib/libpam_misc.dylib \
		  /usr/lib/libz.dylib usr/lib
		mkdir usr/lib/system
		cp -pf /usr/lib/system/libmathCommon.A.dylib usr/lib/system
	else
		cp -pf /lib/libdl.so.2 /lib/libm.so.6 /lib/libpthread.so.0 \
		  /lib/libc.so.6 /lib/libcrypt.so.1 /lib/ld-linux.so.2 \
		  /lib/libncurses.so.5 \
		  /lib/libbz2.so.1.0 \
		  /lib/librt.so.1 \
		  /lib/libacl.so.1 \
		  /lib/libattr.so.1 \
		  /lib/libnss_compat.so.2 /lib/libnsl.so.1 /lib/libnss_files.so.2 /lib/libcap.so.1 \
		  /lib/libpam.so.0 /lib/libpam_misc.so.0 lib/
		if [ -e /usr/lib/libmagic.so.1 ]
		then
			cp -pf /usr/lib/libmagic.so.1 lib/
		fi
		if [ $UNIX_TYPE"" = "gentoo" ] ; then
			cp -pf /lib/libz.so.1 lib/
		else
			cp -pf /usr/lib/libz.so.1 lib/
		fi
	fi
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

# No need anymore - fix up pam.d into jail
# if [ -e /etc/pam.d ]; then
# 	mkdir -p ./etc/pam.d/
# 	cp /etc/pam.d/* ./etc/pam.d/
# fi
# No need anymore - copy PAM-Modules to jail
#if [ -e /lib/security ]; then
	#cp -r /lib/security ./lib/
#fi
#if [ -e /etc/security ]; then 
#	cp -r /etc/security ./etc/
#fi
if [ -e /etc/login.defs ]; then
	cp /etc/login.defs ./etc/
fi

# if we have a sudo binary around, then use it to create our chroot shell
SUDO=`which sudo`
if [ -n "$SUDO" ] ; then
	# create a chroot shell script
	echo "Creating chroot shell script..."
	SHELL=/bin/dtc-chroot-shell
	echo '#!/bin/sh' > $SHELL
	echo "`which sudo` -H `which chroot` \$HOME /bin/su - \$USER" \"\$@\" >> $SHELL
	chmod 755 $SHELL
	# fix sudoers
	# fix /etc/shells
fi

# set protections
chmod 1770 tmp
chmod 1770 var/tmp
chmod 666 dev/null
chmod 644 dev/*random

#now need to copy over the perl binary and some modules
cp -pf /usr/bin/perl usr/bin/

if ! [ $UNIX_TYPE"" = "osx" ] ;then
	# now create our ld.so cache
	chroot $CHROOT_DIR ./sbin/ldconfig 
	# just in case we have wiped our /etc/ld.so.cache (run locally)
	/sbin/ldconfig
fi

