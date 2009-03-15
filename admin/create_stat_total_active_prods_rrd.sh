#!/bin/sh

DTC_ETC=$1

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

$RRDTOOL create $DTC_ETC/stat_total_active_prods.rrd --step 86400 \
	DS:shared:GAUGE:172800:0:U \
	DS:vps:GAUGE:172800:0:U \
	DS:dedicated:GAUGE:172800:0:U \
	RRA:AVERAGE:0.5:1:365 \
	RRA:AVERAGE:0.5:365:3650
