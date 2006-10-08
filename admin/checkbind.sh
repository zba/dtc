#!/bin/sh
conf_generated_file_path=$1

bindgroup=`cat /etc/group | cut -f 1 -d: | grep named`
nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nobody`
# if we can't find the nobody group, try nogroup
if [ -z ""$nobodygroup ]; then
nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nogroup`
fi
# if we can't find nogroup, then set to 65534
if [ -z ""$nobodygroup ]; then
nobodygroup=65534
fi

if [ -z "$bindgroup" ] ; then
	bindgroup=`cat /etc/group | cut -f 1 -d: | grep bind`
fi

if [ -n "$bindgroup" ]; then
	echo "Changing $conf_generated_file_path/zones permissions to 770 $bindgroup:$nobodygroup"
        chown -R $bindgroup:$nobodygroup $conf_generated_file_path/zones
	chmod -R 0770 $conf_generated_file_path/zones
        chown -R $bindgroup:$nobodygroup $conf_generated_file_path/slave_zones
	chmod -R 0770 $conf_generated_file_path/slave_zones
else
	echo "Didn't find named groups, it must be running as root: keeping permissions"
fi
