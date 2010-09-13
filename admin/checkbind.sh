#!/bin/sh
conf_generated_file_path=$1

bindgroup=`cat /etc/group | cut -f 1 -d: | grep named`
binduser=`cat /etc/passwd | cut -f 1 -d: | grep named`

# That part is from old < 0.25 version, removing it...
#nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nobody`
# if we can't find the nobody group, try nogroup
#if [ -z ""$nobodygroup ]; then
#	nobodygroup=`cat /etc/group | cut -f 1 -d: | grep ^nogroup`
#fi
# if we can't find nogroup, then set to 65534
#if [ -z ""$nobodygroup ]; then
#	nobodygroup=65534
#fi

if [ -z "$bindgroup" ] ; then
	bindgroup=`cat /etc/group | cut -f 1 -d: | grep bind`
fi

if [ -z "$binduser" ] ; then
        binduser=`cat /etc/passwd | cut -f 1 -d: | grep bind`
fi

if [ -n "$bindgroup" ]; then
	echo "Changing $conf_generated_file_path/zones permissions to 770 $bindgroup:$nobodygroup"
        chown -R dtc:$bindgroup $conf_generated_file_path/zones
	chmod -R 0660 $conf_generated_file_path/zones
	chmod 0770 $conf_generated_file_path/zones
	# make sure the slave_zones are owned by $binduser so that refreshes work
        chown -R $binduser:$bindgroup $conf_generated_file_path/slave_zones
	# the directory can be owned by dtc
        chown dtc:$bindgroup $conf_generated_file_path/slave_zones
	chmod -R 0660 $conf_generated_file_path/slave_zones
	chmod 0770 $conf_generated_file_path/slave_zones
	chown dtc:$bindgroup $conf_generated_file_path/named.conf
	chmod 0660 $conf_generated_file_path/named.conf
	chown dtc:$bindgroup $conf_generated_file_path/named.slavezones.conf
	chmod 0660 $conf_generated_file_path/named.slavezones.conf
	if [ -e $conf_generated_file_path/reverse_zones ] ; then
		chown -R dtc:$bindgroup $conf_generated_file_path/reverse_zones
		chmod -R 0660 $conf_generated_file_path/reverse_zones
		chmod 0770 $conf_generated_file_path/reverse_zones
	fi
	if [ -e $conf_generated_file_path/slave_reverse_zones ] ; then
		# make sure the slave_reverse_zones are owned by $binduser so that refreshes work
		chown -R $binduser:$bindgroup $conf_generated_file_path/slave_reverse_zones
		# the directory can be owned by dtc
		chown dtc:$bindgroup $conf_generated_file_path/slave_reverse_zones
		chmod -R 0660 $conf_generated_file_path/slave_reverse_zones
		chmod 0770 $conf_generated_file_path/slave_reverse_zones
	fi
	if [ -e $conf_generated_file_path/named.conf.reverse ] ; then
		chown dtc:$bindgroup $conf_generated_file_path/named.conf.reverse
		chmod 0660 $conf_generated_file_path/named.conf.reverse
	fi
	if [ -e $conf_generated_file_path/named.conf.slave.reverse ] ; then
		chown dtc:$bindgroup $conf_generated_file_path/named.conf.slave.reverse
		chmod 0660 $conf_generated_file_path/named.conf.slave.reverse
	fi
	# why do we change the slave_reverse_zones path again here?
	if [ -e $conf_generated_file_path/slave_reverse_zones ] ; then
		chown dtc:$bindgroup $conf_generated_file_path/slave_reverse_zones
		chmod +x $conf_generated_file_path/slave_reverse_zones
	fi
else
	echo "Didn't find named groups, it must be running as root: keeping permissions"
fi
