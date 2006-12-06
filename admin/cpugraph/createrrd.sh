#!/bin/sh

DTC_ETC=$1

# orig by Ralf Hildebrandt
# http://www.stahl.bau.tu-bs.de/~hildeb/postfix/queuegraph/
# modified by Damien Mascord for dtc

RRDTOOL=rrdtool

$RRDTOOL create $DTC_ETC/cpu.rrd --step 60 \
	DS:loadaverage:GAUGE:900:0:U \
	RRA:AVERAGE:0.5:1:20160 \
	RRA:AVERAGE:0.5:30:2016 \
	RRA:AVERAGE:0.5:60:105120 \
	RRA:MAX:0.5:1:1440 \
	RRA:MAX:0.5:30:2016 \
	RRA:MAX:0.5:60:105120
