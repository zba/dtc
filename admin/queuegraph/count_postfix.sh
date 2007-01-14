#!/bin/sh

PATH=/usr/local/rrdtool-1.0.48/bin:/usr/local/bin:/usr/local/sbin:/sbin:/bin:/usr/bin:/usr/sbin:
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



#set -x
qdir=`postconf -h queue_directory`
active=`find $qdir/incoming $qdir/active $qdir/maildrop -type f -print | wc -l | awk '{print $1}'`
deferred=`find $qdir/deferred -type f -print | wc -l | awk '{print $1}'`
#printf "active: %d\ndeferred: %d\n" $active $deferred
rrdtool update $DTC_ETC/mailqueues.rrd "N:$active:$deferred"
