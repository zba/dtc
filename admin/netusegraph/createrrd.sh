#!/bin/sh

DTC_ETC=$1

# orig by Ralf Hildebrandt
# http://www.stahl.bau.tu-bs.de/~hildeb/postfix/queuegraph/
# modified by Damien Mascord for dtc

RRDTOOL=/usr/bin/rrdtool

$RRDTOOL create $DTC_ETC/netusage.rrd --step 60 \
	DS:bytesin:COUNTER:900:0:U \
	DS:bytesout:COUNTER:900:0:U \
	RRA:AVERAGE:0.5:1:20160 \
	RRA:AVERAGE:0.5:30:2016 \
	RRA:AVERAGE:0.5:60:105120
