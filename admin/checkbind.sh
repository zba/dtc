#!/bin/sh
conf_generated_file_path=$1
bindgroup=`cat /etc/group | cut -f 1 -d: | grep named`

if [ ""$bindgroup = "" ] ; then
	bindgroup=`cat /etc/group | cut -f 1 -d: | grep bind`
fi

if [ ""$bindgroup != "" ]; then
	echo "Changing "$bindgroup $conf_generated_file_path"/zones permissions to nobody:"$bindgroup
        chown -R nobody:""$bindgroup $conf_generated_file_path/zones
else
	echo "Didn't find any groups for named, it must be running as root"
fi
