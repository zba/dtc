#!/bin/sh

# this chroot creation script will only work for debian testing
# please modify this for your particular distribution/OS
# Damien Mascord <tusker@tusker.org>

# Added support for Debian stable and integrate it in install
# script for all OS (path needs to be checked for BSD and RedHat...
# so this script is for the moment UNTESTED)
# Thomas GOIRAND <thomas@goirand.fr>

# first check to see if we have the correct command line settings
#if [ "$1" = "" ]
#then
#	echo "Usage: $0 <directory to create chroot in> <webuser> <webgroup>"
#	exit 1
#fi

# assign our variables
CHROOT_DIR=conf_chroot_path
WEB_USER=nobody
WEB_GROUP=nogroup

# set our umask so things are created with the correct group perms
umask 022

# now onto the creation
cd $CHROOT_DIR

# create directory structure
mkdir -p etc dev bin lib tmp var/tmp var/run sbin
mkdir -p usr/bin usr/lib usr/libexec usr/share usr/lib/zoneinfo

# make devices - adjust MAJOR/MINOR as appropriate ( see ls -l /dev/* )
# mknod dev/null    c  2 2   # FreeBSD?
mknod dev/null    c  1 3   # Linux

mknod dev/random  c  1 8   # Linux
mknod dev/urandom c  1 9   # Linux
#mknod dev/urandom c 45 2   # OpenBSD ?
#mknod dev/random  c  2 3   # FreeBSD
#ln -s dev/random dev/urandom # FreeBSD

# some external programs may need these:
#mknod dev/stdin   c 22 0   # FreeBSD, OpenBSD
#mknod dev/stdout  c 22 1   # FreeBSD, OpenBSD
#mknod dev/stderr  c 22 2   # FreeBSD, OpenBSD

# copy required binaries to $CHROOT_DIR/usr/bin
cp -pv /usr/bin/file /usr/bin/bzip2 /bin/cpio usr/bin/

# copy required binaries to $CHROOT_DIR/bin
cp -pv /bin/bash /bin/sh /bin/echo /bin/false /bin/gzip \
  /bin/gunzip /bin/ls /bin/pwd /bin/cat /bin/su bin/

# copy ldconfig from sbin to $CHROOT_DIR/sbin
cp -pv /sbin/ldconfig sbin/

# copy needed /etc files to $CHROOT_DIR/etc
cp -pv /etc/protocols /etc/services /etc/hosts \
  /etc/group /etc/passwd /etc/resolv.conf /etc/localtime \
  /etc/nsswitch.conf /etc/host.conf etc/

# copy shared libraries to $CHROOT_DIR/lib
#   (check:  ldd /usr/bin/perl (or other binary) to see which ones are needed)
#
#FreeBSD: 
#for j in \
#  /usr/lib/libc.so.5 /usr/lib/libm.so.2 /usr/lib/libstdc++.so.4 \
#  /usr/lib/libz.so.2 \
#  /usr/local/lib/libsavi.so.3 /usr/local/lib/libclamav.so*
#do cp -p $j usr/lib/; done
#cp -p /usr/libexec/ld-elf.so.1 usr/libexec/

#Linux:
# /usr/lib/libmagic.so.1 
cp -pv /lib/libdl.so.2 /lib/libm.so.6 /lib/libpthread.so.0 \
  /lib/libc.so.6 /lib/libcrypt.so.1 /lib/ld-linux.so.2 \
  /lib/libncurses.so.5 /usr/lib/libmagic.so.1 /usr/lib/libz.so.1 \
  /lib/librt.so.1 /lib/libacl.so.1 /lib/libpthread.so.0 \
  /lib/libattr.so.1 /lib/libpam.so.0 /lib/libpam_misc.so.0 lib/
#ln lib/ld-2.3.2.so lib/ld-linux.so.2

# magic files needed by file(1). Different versions and installations
# expect magic files in different locations. Check the documentation.
# Some usual locations are:
#cp -p /usr/local/share/file/*  usr/local/share/file/
mkdir -p usr/share/misc/file
cp -p /usr/share/misc/file/magic*   usr/share/misc/file
#cp -p /usr/share/magic         usr/share/

# set protections
chmod 1770 tmp
chmod 1770 var/tmp
chmod 666 dev/null
chmod 644 dev/*random

#now need to copy over the perl binary and some modules
cp -p /usr/bin/perl usr/bin/

# now create our ld.so cache
chroot $CHROOT_DIR ./sbin/ldconfig 
# just in case we have wiped our /etc/ld.so.cache (run locally)
/sbin/ldconfig
