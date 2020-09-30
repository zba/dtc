#!/bin/sh
set -e
. /usr/share/debconf/confmodule

if [ $# -lt 1 ]; then
	echo "Usage: $0 <new ip address>"
	echo "NOTE: You have to be root!"
	exit 1
fi

# Substitute in the values from the debconf db.
# There are obvious optimizations possible here.
db_get dtc/conf_ipaddr
FOO="$RET"
echo "Changing $FOO to $1"
db_set dtc/conf_ipaddr $1
