#!/bin/sh

MY_PATH=$1
COMMON_NAME=$2
SSL_PASSPHRASE=$3

if [ -x /usr/bin/openssl ] ; then
	OPENSSL=/usr/bin/openssl
else
	OPENSSL=openssl
fi

if [ ! -d ""$MY_PATH ] ; then
	echo "Cannot find directory $MY_PATH !"
	exit 1
fi

# TODO: find a way to detect the version with MKTEMP="mktemp -p /tmp"
MKTEMP="mktemp -t"


if [ "`uname -s`" = "FreeBSD" ]; then
	UNIX_TYPE=freebsd
elif [ -f /etc/debian_version ] ; then
	UNIX_TYPE=debian
else
	UNIX_TYPE=others
fi

if [ $UNIX_TYPE"" = "freebsd" -o $UNIX_TYPE"" = "osx" ] ; then
		TMP_FILE=`${MKTEMP} ""`
		gen_pass=`echo $TMP_FILE | cut -d'.' -f2`
		rm -f ${TMP_FILE}
		TMP_FILE=`${MKTEMP} ""`
		gen_pass=${gen_pass}`echo $TMP_FILE | cut -d'.' -f2`
		rm -f ${TMP_FILE}
else
	# This new one works as well with sh!
	gen_pass=`dd if=/dev/random bs=64 count=1 2>|/dev/null | md5sum | cut -d' ' -f1 | awk '{print substr($0,0,16)}'`
fi
if [ -z "$SSL_PASSPHRASE" ] ; then
	SSL_PASSPHRASE=$gen_pass
fi

if [ $UNIX_TYPE"" = "freebsd" -o $UNIX_TYPE"" = "osx" ] ; then
		TMP_FILE=`${MKTEMP} ""`
		gen_pass=`echo $TMP_FILE | cut -d'.' -f2`
		rm -f ${TMP_FILE}
		TMP_FILE=`${MKTEMP} ""`
		gen_pass=${gen_pass}`echo $TMP_FILE | cut -d'.' -f2`
		rm -f ${TMP_FILE}
else
	# This new one works as well with sh!
	gen_pass=`dd if=/dev/random bs=64 count=1 2>|/dev/null | md5sum | cut -d' ' -f1 | awk '{print substr($0,0,16)}'`
fi
CHALLENGE_PASS=$gen_pass

echo "Checking dirs"

if [ ! -e $MY_PATH/$COMMON_NAME.cert.new ] ; then
	if [ ! -e $MY_PATH/$COMMON_NAME.cert.key ] ; then
		OLDCWD=`pwd`
		cd $MY_PATH
		echo $pwd
		CERTPASS_TMP_FILE=`${MKTEMP} certfilepass.XXXXXX` || exit 1
		echo  $SSL_PASSPHRASE >$CERTPASS_TMP_FILE
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
		/bin/rm $CERTPASS_TMP_FILE
		cd $OLDCWD
	fi
fi
