#!/bin/sh
# generate sasldb stuff, gen_sasl.sh <domain> <user> <password> <mailname>
domain_full_name=$1
id=$2
passwdtemp=$3
mailname=$4

# Try to find SASLpasswd2 binary
if [ -e /usr/sbin/saslpasswd2 ] ; then
	SASLPWD2=/usr/sbin/saslpasswd2
fi
if [ -e /usr/local/sbin/saslpasswd2 ] ; then
	SASLPWD2=/usr/local/sbin/saslpasswd2
fi

nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nobody`
# if we can't find the nobody group, try nogroup
if [ -z ""$nobodygroup ]; then
	nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nogroup`
fi
# if we can't find nogroup, then set to 65534
if [ -z ""$nobodygroup ]; then
	nobodygroup=65534
fi

# If found, use it to generate accounts
if ! [ -z ""$SASLPWD2 ] ; then
	echo $passwdtemp | $SASLPWD2 -c -p -f ../etc/sasldb2 -u $mailname $id\@$domain_full_name
	chmod 664 ../etc/sasldb2 
	if [ -e /var/spool/postfix/etc ]; then
		echo "OK, in /var/spool" >> /tmp/sasl.tmp
		cat ../etc/sasldb2 > /var/spool/postfix/etc/sasldb2
		chmod 664 /var/spool/postfix/etc/sasldb2

		chown postfix:$nobodygroup /var/spool/postfix/etc/sasldb2
	else 
		echo "OK, in /etc/" >> /tmp/sasl.tmp
		if [ -d /etc/sasl2 ] ; then
			chmod 664 /etc/sasl2/sasldb2
			chown postfix:$nobodygroup /etc/sasl2/sasldb2
			cat ../etc/sasldb2 > /etc/sasl2/sasldb2
		else
			chmod 664 /etc/sasldb2
			chown postfix:$nobodygroup /etc/sasldb2
			cat ../etc/sasldb2 > /etc/sasldb2
		fi
	fi

	ls ../etc/sasldb2 >> /tmp/sasl.tmp
fi
