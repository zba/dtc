#!/bin/sh

PATH=/usr/local/bin:/usr/local/sbin:/sbin:/bin:/usr/bin:/usr/sbin:
DTC_ETC=$1

cd $DTC_ETC
if [ -e /var/qmail/bin/qmail-qstat ]; then
	QMAIL_QSTAT=/var/qmail/bin/qmail-qstat
elif [ -e /usr/sbin/qmail-qstat ]; then 
	QMAIL_QSTAT=/usr/sbin/qmail-qstat
else
	QMAIL_QSTAT=qmail-qstat
fi
active=`$QMAIL_QSTAT | head -n 1 | cut -f 2 -d':' | awk '{print $1}'`
deferred=`$QMAIL_QSTAT | tail -n 1 | cut -f 2 -d':' | awk '{print $1}'`
rrdtool update $DTC_ETC/mailqueues.rrd "N:$active:$deferred"
