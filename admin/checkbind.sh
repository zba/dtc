#!/bin/sh
conf_generated_file_path=$1
bindgroup=`cat /etc/group | cut -f 1 -d: | grep named`

if [ ""$bindgroup = "" ] ; then
	bindgroup=`cat /etc/group | cut -f 1 -d: | grep bind`
fi

if [ ""$bindgroup != "" ]; then
	echo "Changing "$bindgroup $conf_generated_file_path"/zones permissions to nobody:"$bindgroup
        chown -R $bindgroup:""65534 $conf_generated_file_path/zones
	chmod -R 0770 $conf_generated_file_path/zones
        chown -R $bindgroup:""65534 $conf_generated_file_path/slave_zones
	chmod -R 0770 $conf_generated_file_path/slave_zones
else
	echo "Didn't find any groups for named, it must be running as root"
fi
