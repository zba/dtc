#!/bin/sh

PATH=$1
COMMON_NAME=$2
SSL_PASSPHRASE=$3

if [ -x /usr/bin/openssl ] ; then
	OPENSSL=/usr/bin/openssl
else
	OPENSSL=openssl
fi

if [ ! -d ""$PATH ] ; then
	echo "Cannot find directory $PATH !"
	exit 1
fi

# TODO: find a way to detect the version with MKTEMP="mktemp -p /tmp"
if [ -z "$MKTEMP" ] ; then
	MKTEMP="mktemp -t"
fi

if [ -z "$SSL_PASSPHRASE" ] ; then
	SSL_PASSPHRASE=$RANDOM$RANDOM
fi

CHALLENGE_PASS=$RANDOM$RANDOM

if [ ! -e $PATH/$COMMON_NAME.cert.new ] ; then
	if [ ! -e $PATH/$COMMON_NAME.cert.key ] ; then	
		pushd $PATH
		CERTPASS_TMP_FILE=`${MKTEMP} certfilepass.XXXXXX` || exit 1
		echo  >$CERTPASS_TMP_FILE
		( echo "US";
		echo "the state";
		echo "the locality";
		echo "GPLHost DTC Panel";
		echo "No-unit";
		echo "$COMMON_NAME";
		echo "fake@example-domain.com";
		echo $CHALLENGE_PASS;
		echo "Orga1"; ) | $OPENSSL req -passout file:$CERTPASS_TMP_FILE -new > $COMMON_NAME.cert.csr
		$OPENSSL rsa -passin file:$CERTPASS_TMP_FILE -in privkey.pem -out $COMMON_NAME.cert.key
		$OPENSSL x509 -in $COMMON_NAME.cert.csr -out $COMMON_NAME.cert.cert -req -signkey $COMMON_NAME.cert.key -days 3650
		rm $CERTPASS_TMP_FILE
		popd
	fi
fi
