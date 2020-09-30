#!/bin/sh

PATH=/usr/local/bin:/usr/local/sbin:/sbin:/bin:/usr/bin:/usr/sbin:
DTC_ETC=$1
IFACELIST=$2

BYTES_IN=0
BYTES_OUT=0

if [ -x /usr/bin/rrdtool ] ; then
	RRDTOOL=/usr/bin/rrdtool
else
	if [ -x /usr/local/bin/rrdtool ] ; then
		RRDTOOL=/usr/local/bin/rrdtool
	else
		exit 1
	fi
fi

for IFACE_NAME in $IFACELIST ; do
#	echo $IFACE_NAME
	if [ `uname -s` = "FreeBSD" ] ; then
		IFACE_TXT=`netstat -Wnib | grep ${IFACE_NAME} | grep Link | head -1 | awk '{ print $8,$11}'`
		BYTES_IN=$(($BYTES_IN + 0`echo ${IFACE_TXT} | awk -F ' ' '{print $1}'`))
		BYTES_OUT=$(($BYTES_OUT + 0`echo ${IFACE_TXT} | awk -F ' ' '{print $2}'`))
	else
		IFACE_TXT=`cat /proc/net/dev | grep ${IFACE_NAME} | cut -f 2 -d':'`
		BYTES_IN=$(($BYTES_IN + `echo ${IFACE_TXT} | gawk -F ' ' '{print $1}'`))
		BYTES_OUT=$(($BYTES_OUT + `echo ${IFACE_TXT} | gawk -F ' ' '{print $9}'`))
	fi
done
rrdtool update $DTC_ETC/netusage.rrd "N:${BYTES_IN}:${BYTES_OUT}"
