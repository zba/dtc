00,10,20,30,40,50 * * * * root cd /usr/share/dtc/admin; /usr/bin/php /usr/share/dtc/admin/cron.php >>/var/log/dtc.log
9 4 * * * nobody cd /usr/share/dtc/admin; nice -n+20 /usr/bin/php /usr/share/dtc/admin/accesslog.php
* * * * * root cd /usr/share/dtc/admin; nice -n+20 /usr/share/dtc/admin/cpugraph/get_cpu_load.sh /usr/share/dtc/etc >>/var/log/dtc.log
* * * * * root cd /usr/share/dtc/admin; nice -n+20 /usr/share/dtc/admin/netusegraph/get_net_usage.sh /usr/share/dtc/etc "eth0" >>/var/log/dtc.log
* * * * * root cd /usr/share/dtc/admin; nice -n+20 /usr/share/dtc/admin/memgraph/get_meminfo.sh /usr/share/dtc/etc >>/var/log/dtc.log
9 3 * * * root cd /usr/share/dtc/admin; /usr/bin/php /usr/share/dtc/admin/reminders.php
