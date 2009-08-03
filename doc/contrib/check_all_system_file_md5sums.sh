#!/bin/sh

# This script goal is to check for the integrity of the system files
# in order to know if there was some corruption.
# That way, you can reinstall the target files if there were broken,
# or check if your system was trojaned (give the assemption that the
# sums in /var/lib/dpkg/info/*.md5sums are correct, which is likely
# NOT to be the case if your system has been hacked with a good root-kit,
# so this is not a very good hack-check!!!)

MD5=/usr/bin/md5sum

echo "Starting md5 checks"
for i in `ls /var/lib/dpkg/info/*.md5sums` ; do
	nbrline=`cat $i | wc -l`
	for j in `seq 1 $nbrline` ; do
		MD5_LINE=`cat $i | head -n $j | tail -n 1`
		FILE_MD5=`echo $MD5_LINE | awk '{print $1}'`
		FILE_NAME=`echo $MD5_LINE | awk '{print $2}'`
		CHECK=`./md5sum /$FILE_NAME | awk '{print $1}'`
		if [ "$CHECK" = "$FILE_MD5" ] ; then
			echo -n "."
		else
			echo "File $FILE_NAME has md5 not ok!"
			read whateveritsjustapause
		fi
	done
done
