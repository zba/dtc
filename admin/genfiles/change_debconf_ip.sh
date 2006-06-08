#!/bin/sh -e


# Source debconf library.
. /usr/share/debconf/confmodule

db_go

# Check their answer.
db_get dtc/conf_ipaddr
conf_ipaddr=$RET
echo "IP was set as $conf_ipaddr"

if [ $# -lt 1 ]; then
echo "Usage: $0 <new IP>"
exit
fi
echo "We want the IP to be $1"

db_set dtc/conf_ipaddr $1
# Check their answer.
db_get dtc/conf_ipaddr
conf_ipaddr=$RET
echo "IP is now set as $conf_ipaddr"
