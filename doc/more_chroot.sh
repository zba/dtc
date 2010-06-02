#!/bin/sh

# This scripts populates your chroot with some more stuffs for your chroot
# so that it's usable with ssh

if [ -z "${1}" ] ; then
	DEST=/var/lib/dtc/chroot_template
else
	DEST=${1}
fi

# Copy all the terminfo stuufs
cp -rf /etc/terminfo ${DEST}/etc
cp -rf /usr/share/terminfo ${DEST}/usr/share
cp -rf /lib/terminfo ${DEST}/lib
cp -f /usr/bin/xterm ${DEST}/usr/bin
cp -f /usr/bin/lxterm ${DEST}/usr/bin
cp -f /usr/bin/resize ${DEST}/usr/bin
cp -f /usr/bin/uxterm ${DEST}/usr/bin
cp -f /usr/bin/vi ${DEST}/usr/bin

# Some basic apps
cp -f /usr/bin/whoami ${DEST}/usr/bin
cp -f /bin/cp ${DEST}/bin
cp -f /bin/mv ${DEST}/bin
cp -f /bin/gzip ${DEST}/bin
cp -f /usr/bin/wget ${DEST}/bin

# needed by wget
cp -rf /lib/libnss_dns.so.2 ${DEST}/lib

# nvi
cp -rf /etc/alternatives ${DEST}/etc
cp -f /usr/share/vi ${DEST}/usr/share
cp -f /usr/bin/nvi ${DEST}/usr/bin

# joe
cp -rf /etc/joe ${DEST}/etc
cp -f /usr/bin/jstar ${DEST}/usr/bin
cp -f /usr/bin/rjoe ${DEST}/usr/bin
cp -f /usr/bin/jpico ${DEST} /usr/bin
cp -f /usr/bin ${DEST}/usr/bin
cp -f /usr/bin/jmacs ${DEST}/usr/bin
