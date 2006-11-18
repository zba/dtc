CHROOT_DIR=$conf_chroot_path
WEB_USER=${CONF_DTC_SYSTEM_USERNAME}
WEB_GROUP=${CONF_DTC_SYSTEM_GROUPNAME}

if [ $CHROOT_DIR"" = "" ] ; then
	CHROOT_DIR=/var/www/chroot
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
if ! [ ""$conf_omit_dev_mknod = "yes" ] ; then
	if ! [ -e dev/null ] ; then
		if [ $UNIX_TYPE"" = "freebsd" -o $UNIX_TYPE"" = "osx" ] ; then
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
fi

# copy required binaries to $CHROOT_DIR/usr/bin and $CHROOT_DIR/bin
if [ -e /bin/bzip2 ] ; then
	cp -pf /bin/bzip2 usr/bin/
fi
if [ -e /usr/bin/bzip2 ] ; then
	cp -pf /usr/bin/bzip2 usr/bin/
fi
if [ -e /usr/bin/file ] ; then
	cp -pf /usr/bin/file usr/bin/
fi

if [ $UNIX_TYPE"" = "freebsd" ] ; then
	cp -pf /usr/bin/cpio usr/bin
	cp -pf /bin/rm /bin/mv /usr/bin/gunzip /usr/bin/tar /usr/bin/false bin/
elif [ $UNIX_TYPE"" = "osx" ] ; then
	cp -pf /usr/bin/cpio usr/bin
	cp -pf /usr/bin/rm /usr/bin/mv /usr/bin/gunzip /usr/bin/tar /usr/bin/false bin/
else
	cp -pf /bin/rm /bin/mv /bin/gunzip bin/
	cp -pf /bin/cpio usr/bin
fi
# copy zip and unzip if they are present
if [ -e /usr/bin/zip ] ; then
	cp -pf /usr/bin/zip bin/
fi
if [ -e /usr/bin/unzip ] ; then
	cp -pf /usr/bin/unzip bin/
fi

if [ -e /bin/bash ] ; then
	cp -pf /bin/bash bin
fi
if [ -e /usr/bin/bash ] ; then
	cp -pf /usr/bin/bash bin
fi
if [ -e /usr/bin/tar ]; then
	cp -pf /usr/bin/tar bin
fi
if [ -e /bin/tar ]; then
	cp -pf /bin/tar bin
fi
if [ -e /usr/bin/false ]; then
	cp -pf /usr/bin/false bin
fi
if [ -e /bin/false ]; then
	cp -pf /bin/false bin
fi

if [ -e /usr/bin/sftp ]; then
	cp -pf /usr/bin/sftp bin/
fi

# the sftp-server binary can be in /usr/lib or /lib, so check both places
if [ -e /usr/lib/sftp-server ]; then
	cp -pf /usr/lib/sftp-server usr/lib/
fi

if [ -e /lib/sftp-server ]; then
	cp -pf /lib/sftp-server lib/
fi

if [ -e /usr/bin/scp ]; then
	cp -pf /usr/bin/scp bin/
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
set +e
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
grep ${CONF_DTC_SYSTEM_GROUPNAME} /etc/group >> etc/group
grep ${CONF_DTC_SYSTEM_USERNAME} /etc/group >> etc/group
set -e

# fix entry for nobody in /etc/passwd
echo "${CONF_DTC_SYSTEM_USERNAME}:x:${CONF_DTC_SYSTEM_UID}:${CONF_DTC_SYSTEM_GID}:${CONF_DTC_SYSTEM_USERNAME}:/html:/bin/bash" >> etc/passwd

# create shadow account line for nobody
echo "${CONF_DTC_SYSTEM_USERNAME}::12719:0:99999:7:::" > etc/shadow
chown ${CONF_DTC_SYSTEM_USERNAME}:${CONF_DTC_SYSTEM_GROUPNAME} etc/shadow

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
		FOUNDED_ARCH=`uname -m`
		if [ ""$FOUNDED_ARCH = "x86_64" ] ; then
			if [ ! -e lib64 ]; then
				ln -s lib lib64
			fi
		fi
		if [ -e /lib/ld-linux.so.2 ] ; then
			cp -pf /lib/ld-linux.so.2 lib/
		fi
		if [ -e /lib/ld-linux-x86-64.so.2 ] ; then
			cp -pf /lib/ld-linux-x86-64.so.2 lib/
		fi
		cp -pf /lib/libdl.so.2 /lib/libm.so.6 /lib/libpthread.so.0 \
		  /lib/libc.so.6 /lib/libcrypt.so.1 \
		  /lib/librt.so.1 \
		  /lib/libnss_compat.so.2 /lib/libnsl.so.1 /lib/libnss_files.so.2 \
		  /lib/libpam.so.0 /lib/libpam_misc.so.0 lib/

		if [ -e /lib/libncurses.so.5 ]; then
		  cp  /lib/libncurses.so.5 lib/
		fi

		if [ -e /usr/lib/libncurses.so.5 ]; then
		  cp /usr/lib/libncurses.so.5 lib/
		fi

		if [ -e /lib/libacl.so.1 ]; then
		  	cp /lib/libacl.so.1 lib/
		fi

		if [ -e /lib/libattr.so.1 ]; then
			cp /lib/libattr.so.1 lib/
		fi

		if [ -e /lib/libcap.so.1 ]; then
			cp /lib/libcap.so.1 lib/
		fi

		if [ -e /lib/libbz2.so.1.0 ]; then
			cp /lib/libbz2.so.1.0 lib/
		fi 
		if [ -e /usr/lib/libbz2.so.1.0 ]; then
			cp /usr/lib/libbz2.so.1.0 lib/
		fi
		if [ -e /usr/lib/libmagic.so.1 ]
		then
			cp -pf /usr/lib/libmagic.so.1 lib/
		fi
		if [ $UNIX_TYPE"" = "gentoo" ] ; then
			cp -pf /lib/libz.so.1 lib/
		else
			cp -pf /usr/lib/libz.so.1 lib/
		fi

		# libs for sftp and scp

		if [ -e /lib/libresolv.so.2 ]; then
			cp -pf /lib/libresolv.so.2 lib/
		fi

		if [ -e /usr/lib/libcrypto.so.0.9.7 ]; then
			cp -pf /usr/lib/libcrypto.so.0.9.7 lib/
		fi

		if [ -e /usr/lib/libcrypto.so.0.9.8 ]; then
			cp -pf /usr/lib/libcrypto.so.0.9.8 lib/
		fi

		if [ -e /lib/libutil.so.1 ]; then
			cp -pf /lib/libutil.so.1 lib/
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

# now we have come this far, make sure our chroot includes enough libs for this environment
LDD=`which ldd`
if [ -n "$LDD" ]; then

for i in bin/*;
do
        for j in `$LDD $i | cut -f 1 -d' '`;
        do
		if [ -e $j ]; then
			cp -pf $j lib/
		fi

                if [ -e /lib/$j ]; then
                        cp -pf /lib/$j lib/
                fi

                if [ -e /usr/lib/$j ]; then
                        cp -pf /usr/lib/$j lib/
                fi

                if [ -e /usr/local/lib/$j ]; then
                        cp -pf /usr/local/lib/$j lib/
                fi
        done
done


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
if ! [ ""$conf_omit_dev_mknod = "yes" ] ; then
	chmod 666 dev/null
	chmod 644 dev/*random
fi

#now need to copy over the perl binary and some modules
cp -pf /usr/bin/perl usr/bin/

if [ $UNIX_TYPE"" = "freebsd" ] ;then
        # now create our ld.so cache
        cp /libexec/ld-elf.so.1 $CHROOT_DIR/libexec
        chroot $CHROOT_DIR ./sbin/ldconfig
        # just in case we have wiped our /etc/ld.so.cache (run locally)
        /sbin/ldconfig
else
	if ! [ $UNIX_TYPE"" = "osx" ] ;then
		# now create our ld.so cache
		mkdir -p $CHROOT_DIR/etc
		touch $CHROOT_DIR/etc/ld.so.cache
		touch $CHROOT_DIR/etc/ld.so.conf
		chroot $CHROOT_DIR ./sbin/ldconfig 
		# just in case we have wiped our /etc/ld.so.cache (run locally)
		/sbin/ldconfig
	fi
fi

