#!/bin/sh

set -e

. /usr/share/debconf/confmodule

if [ $# -lt 1 ]; then
	echo "Usage: $0 <new rbl list>"
	echo "NOTE: You have to be root!"
	exit 1
fi

# Substitute in the values from the debconf db.
# There are obvious optimizations possible here.

db_get dtc/conf_dnsbl_list
FOO="$RET"
echo "Changing $FOO to $1"

db_set dtc/conf_dnsbl_list $1

exit 0
