#!/bin/sh
# generate sasldb stuff, gen_sasl.sh <domain> <user> <password>
domain_full_name=$1
id=$2
passwdtemp=$3

echo $passwdtemp | /usr/sbin/saslpasswd2 -c -p -f ../etc/sasldb2 -u $domain_full_name $id
chmod 644 ../dtc/etc/sasldb2 
