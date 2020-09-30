#!/bin/sh -e


# Source debconf library.
. /usr/share/debconf/confmodule

db_go

# Check their answer.
db_get dtc/main_domainname
main_domainname=$RET
echo "Main Domain was set as $main_domainname"

if [ $# -lt 1 ]; then
echo "Usage: $0 <new Main Domain>"
exit
fi
echo "We want the Main Domain to be $1"

db_set dtc/main_domainname $1
# Check their answer.
db_get dtc/main_domainname
main_domainname=$RET
echo "Main Domain is now set as $main_domainname"
