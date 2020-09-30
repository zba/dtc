#!/bin/sh

PATH=/usr/local/bin:/usr/local/sbin:/sbin:/bin:/usr/bin:/usr/sbin:
DTC_ETC=$1

cd $DTC_ETC

if [ -x /usr/bin/rrdtool ] ; then
	RRDTOOL=/usr/bin/rrdtool
else
	if [ -x /usr/local/bin/rrdtool ] ; then
		RRDTOOL=/usr/local/bin/rrdtool
	else
		if [ -x /opt/local/bin/rrdtool ] ; then
			RRDTOOL=/opt/local/bin/rrdtool
		else
			echo "Could not find the rrdtool binary in $0"
			exit 1
		fi
	fi
fi


CPU=`uptime | rev | cut -d : -f1 | rev| cut -d "," -f1 | tr -d " "`
CPU1=`echo ${CPU} | cut -f 1 -d'.'`
CPU2=`echo ${CPU} | cut -f 2 -d'.'`
LOADAVG=`echo ${CPU1}${CPU2}`
#echo $LOADAVG
$RRDTOOL update $DTC_ETC/cpu.rrd "N:$LOADAVG"
