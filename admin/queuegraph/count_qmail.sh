#!/bin/sh

# $Id: count_qmail.sh,v 1.1 2006/02/09 00:37:59 tusker Exp $

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

if [ -e /var/qmail/bin/qmail-qstat ]; then
QMAIL_QSTAT=/var/qmail/bin/qmail-qstat
elif [ -e /usr/sbin/qmail-qstat ]; then 
QMAIL_QSTAT=/usr/sbin/qmail-qstat
else
QMAIL_QSTAT=qmail-qstat
fi
QMAIL_OUTPUT=(`$QMAIL_QSTAT | cut -f 2 -d':'`)
active=${QMAIL_OUTPUT[0]}
deferred=${QMAIL_OUTPUT[1]}
rrdtool update mailqueues.rrd "N:$active:$deferred"
