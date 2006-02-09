#!/bin/sh

# $Id: count_postfix.sh,v 1.1 2006/02/09 00:37:59 tusker Exp $

# output the number of messages in the incoming, active, and deferred
# queues of postfix one per line suitable for use with snmpd/cricket
#
# mailqsize was originally written by Vivek Khera.  All I did was
# make it update an rrd.
# 2003/01/24 01:19:37  Mike Saunders <method at method DOT cx>
# I bundled this with a modified mailgraph
# 2003/04/14           Ralf Hildebrandz <ralf.hildebrandt at charite DOT de>
# Modified for DTC
# 2006/02/08	       Damien Mascord <tusker at tusker DOT org>

PATH=/usr/local/rrdtool-1.0.48/bin:/usr/local/bin:/usr/local/sbin:/sbin:/bin:/usr/bin:/usr/sbin:

#set -x
qdir=`postconf -h queue_directory`
active=`find $qdir/incoming $qdir/active $qdir/maildrop -type f -print | wc -l | awk '{print $1}'`
deferred=`find $qdir/deferred -type f -print | wc -l | awk '{print $1}'`
#printf "active: %d\ndeferred: %d\n" $active $deferred
rrdtool update mailqueues.rrd "N:$active:$deferred"
