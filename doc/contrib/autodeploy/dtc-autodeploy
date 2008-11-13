#!/bin/sh
# Given the fact that your VPS is well configured
# with a correct hostname ans IP, this script will
# setup DTC in ONCE, by just giving a password that
# will be set for the root: shell account, MySQL,
# DTC and phpmyadmin.
# This will only run on Debian based VPS, it should
# run well with Etch and Lenny, but it's not tested
# if using Ubuntu.

set -e

# Check number of params and print usage.
if ! [ $# = 1 ] ; then
	echo "Usage: dtc-autodeploy <password-to-set>"
	exit 1
fi

# Get the password to set using the command line...
PASSWORD=$1

apt-get update
apt-get --assume-yes install ssh

# Use shadow password and set the root pass of the ssh
shadowconfig on
( sleep 1; echo ${PASSWORD}; sleep 1; echo {PASSWORD}; ) | passwd

# Set the apt to NOT install the recommends, to make it a smaller footprint
echo "APT{
Install-Recommends "false";
}" >/etc/apt/apt.conf

# Find the hostname and default interface and IP of the VPS
DOMAIN_NAME=`hostname --domain`
DEFAULT_IF=`/sbin/route | grep default |awk -- '{ print $8 }'`
IP_ADDR=`ifconfig ${DEFAULT_IF} | grep 'inet addr' | sed 's/.\+inet addr:\([0-9.]\+\).\+/\1/'`

# Set the values in debconf
MKTEMP="mktemp -t"

SETSEL_FILE=`${MKTEMP} DTC_AUTODEPLOY.XXXXXX` || exit 1
cp selection_config_file >${TMP_FILE}

sed -i "s/__PASSWORD__/${PASSWORD}/g" ${SETSEL_FILE}
sed -i "s/__DOMAIN_NAME__/${DOMAIN_NAME}/g" ${SETSEL_FILE}
sed -i "s/__IP__ADDRESS__/${IP_ADDR}/g" ${SETSEL_FILE}

debconf-set-selections ${SETSEL_FILE}
apt-get --assume-yes install dtc-toaster
/usr/share/dtc/admin/install/install
