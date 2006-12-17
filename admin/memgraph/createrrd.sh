#!/bin/sh

DTC_ETC=$1

# orig by Ralf Hildebrandt
# http://www.stahl.bau.tu-bs.de/~hildeb/postfix/queuegraph/
# modified by Damien Mascord for dtc

if [ -x /usr/bin/rrdtool ] ; then
	RRDTOOL=/usr/bin/rrdtool
else
	if [ -x /usr/local/bin/rrdtool ] ; then
		RRDTOOL=/usr/local/bin/rrdtool
        else
		exit 1
	fi
fi

$RRDTOOL create $DTC_ETC/memusage.rrd --step 60 \
	DS:totalmem:GAUGE:900:0:67108864 \
	DS:freemem:GAUGE:900:0:67108864 \
	DS:totalswap:GAUGE:900:0:67108864 \
	DS:freeswap:GAUGE:900:0:67108864 \
	RRA:AVERAGE:0.5:1:20160 \
	RRA:AVERAGE:0.5:30:2016 \
	RRA:AVERAGE:0.5:60:105120 \
	RRA:MAX:0.5:1:1440 \
	RRA:MAX:0.5:30:2016 \
	RRA:MAX:0.5:60:105120
