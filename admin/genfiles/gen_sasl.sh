#!/bin/sh
# generate sasldb stuff, gen_sasl.sh <domain> <user> <password>
domain_full_name=$1
id=$2
passwdtemp=$3

if [ -e /var/spool/postfix/etc/ ]; then
	echo $passwdtemp | /usr/sbin/saslpasswd2 -c -f /var/spool/postfix/etc/sasldb2 -u $domain_full_name $id
	chmod 644 /var/spool/postfix/etc/sasldb2 
else 
	echo $passwdtemp | /usr/sbin/saslpasswd2 -c -u $domain_full_name $id
	chmod 644 /etc/sasldb2
fi 
