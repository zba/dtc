#!/bin/sh

# $Id: get_cpu_load.sh,v 1.1 2006/02/24 02:17:27 thomas Exp $

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

CPU=`cat /proc/loadavg | cut -f 1 -d' '`
CPU1=`echo ${CPU} | cut -f 1 -d'.'`
CPU2=`echo ${CPU} | cut -f 2 -d'.'`
LOADAVG=`echo ${CPU1}${CPU2}`
#echo $LOADAVG
rrdtool update $DTC_ETC/cpu.rrd "N:$LOADAVG"
