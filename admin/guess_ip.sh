#!/bin/sh

set -e

IF=`route | grep default |awk -- '{ print $8 }'`
guessed_ip_addr=`ifconfig ${IF} | grep 'inet addr' | sed 's/.\+inet addr:\([0-9.]\+\).\+/\\1/'`
# Seems there can be BOTH addr and adr...
if [ -z "${guessed_ip_addr}" ] ; then
	guessed_ip_addr=`ifconfig ${IF} | grep 'inet adr' | sed 's/.\+inet adr:\([0-9.]\+\).\+/\\1/'`
fi
echo $guessed_ip_addr
