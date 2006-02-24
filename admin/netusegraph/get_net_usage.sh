#!/bin/sh

# $Id: get_net_usage.sh,v 1.1 2006/02/24 04:32:10 thomas Exp $

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

BYTES_IN=`cat /proc/net/dev | grep eth0 | cut -f 2 -d':' | gawk -F ' ' '{print $1}'`
BYTES_OUT=`cat /proc/net/dev | grep eth0 | cut -f 2 -d':' | gawk -F ' ' '{print $9}'`
rrdtool update $DTC_ETC/netusage.rrd "N:${BYTES_IN}:${BYTES_OUT}"
