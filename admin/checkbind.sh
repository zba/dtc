#!/bin/sh
conf_generated_file_path=$1
check=`cat /etc/group | cut -f 1 -d: | grep bind`
check=$?
if [ $check == "0" ]; then
        chown -R nobody:bind $conf_generated_file_path/zones
fi

