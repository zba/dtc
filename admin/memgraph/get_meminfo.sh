#!/bin/sh

# $Id: get_meminfo.sh,v 1.3 2007/01/14 09:49:33 thomas Exp $

# output the number of messages in the incoming, active, and deferred
# queues of postfix one per line suitable for use with snmpd/cricket
#
# mailqsize was originally written by Vivek Khera.  All I did was
# make it update an rrd.
# 2003/01/24 01:19:37  Mike Saunders <method at method DOT cx>
# I bundled this with a modified mailgraph
# 2003/04/14           Ralf Hildebrandz <ralf.hildebrandt at charite DOT de>
# Modified for DTC (and adapted for qmail)
# 2006/02/08	       Damien Mascord <tusker at tusker DOT org>

PATH=/usr/local/bin:/usr/local/sbin:/sbin:/bin:/usr/bin:/usr/sbin:
DTC_ETC=$1

cd $DTC_ETC

if [ -x /usr/bin/rrdtool ] ; then
	RRDTOOL=/usr/bin/rrdtool
else
	if [ -x /usr/local/bin/rrdtool ] ; then
		RRDTOOL=/usr/local/bin/rrdtool
	else
		exit 1
	fi
fi

if [ `uname -s` = FreeBSD ]
then
	MEMTOTAL=`sysctl hw.physmem | cut -d" " -f2|awk '{print $1 / 1024 }'`
	MEMFREE=` vmstat -H | tail -n1 | awk '{print $5}'`
	SWAPTOTAL=`swapctl -s|awk '{print $2 / 2 }'`
	SWAPFREE=`swapctl -s|awk '{print $3 / 2 }'`
	SWAPFREE=`expr $SWAPTOTAL - $SWAPFREE`
else
	MEMTOTAL=`grep MemTotal /proc/meminfo | gawk -F ' ' '{print $2}'`
	MEMFREE=`grep MemFree /proc/meminfo | gawk -F ' ' '{print $2}'`
	SWAPTOTAL=`grep SwapTotal /proc/meminfo | gawk -F ' ' '{print $2}'`
	SWAPFREE=`grep SwapFree /proc/meminfo | gawk -F ' ' '{print $2}'`
fi
$RRDTOOL update $DTC_ETC/memusage.rrd "N:${MEMTOTAL}:${MEMFREE}:${SWAPTOTAL}:${SWAPFREE}"
