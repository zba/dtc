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
( sleep 1; echo ${PASSWORD}; sleep 1; echo ${PASSWORD}; ) | passwd

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

# Set debconf back to Noninteractive, otherwise phpmyadmin is annoying...
echo "debconf debconf/frontend select Noninteractive
debconf debconf/frontend seen true
debconf debconf/priority select medium
debconf debconf/priority seen true
debconf debconf-apt-progress/title string fake
debconf debconf-apt-progress/title seen true
debconf debconf-apt-progress/preparing string fake
debconf debconf-apt-progress/preparing seen true" >${SETSEL_FILE}
debconf-set-selections ${SETSEL_FILE}
apt-get --force-yes --assume-yes --reinstall install debconf

# Copy our selection_config_file template file, and tweak it with correct values
cp selection_config_file ${SETSEL_FILE}

sed -i "s/__PASSWORD__/${PASSWORD}/g" ${SETSEL_FILE}
sed -i "s/__DOMAIN_NAME__/${DOMAIN_NAME}/g" ${SETSEL_FILE}
sed -i "s/__IP__ADDRESS__/${IP_ADDR}/g" ${SETSEL_FILE}

# Set the values needed to setup DTC
debconf-set-selections ${SETSEL_FILE}

# Set the locales, otherwise postinst are printing a bunch of ugly warning messages
echo "en_US.UTF-8 UTF-8
en_US ISO-8859-1
en_US.ISO-8859-15 ISO-8859-15
" >/etc/locale.gen
export LANGUAGE="en_US.UTF-8"
export LANG="en_US.UTF-8"
export LC_ALL="en_US.UTF-8"
locale-gen
apt-get --force-yes --assume-yes install dtc-toaster

# Set debconf back to medium priority, using Dialog
echo "debconf debconf/frontend select Dialog
debconf debconf/frontend seen true
debconf debconf/priority select medium
debconf debconf/priority seen true
debconf debconf-apt-progress/title string fake
debconf debconf-apt-progress/title seen true
debconf debconf-apt-progress/preparing string fake
debconf debconf-apt-progress/preparing seen true" >${SETSEL_FILE}
debconf-set-selections ${SETSEL_FILE}
apt-get --force-yes --assume-yes --reinstall install debconf

# Finally start the dtc shell installer and we are done!
/usr/share/dtc/admin/install/install
