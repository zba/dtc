#!/bin/sh

nice -n+20 cpugraph/get_cpu_load.sh     /var/lib/dtc/etc 2>&1 >>/var/log/dtc.log
nice -n+20 netusegraph/get_net_usage.sh /var/lib/dtc/etc "eth0" 2>&1 >>/var/log/dtc.log
nice -n+20 memgraph/get_meminfo.sh      /var/lib/dtc/etc 2>&1 >>/var/log/dtc.log

nice -n+20 queuegraph/count_postfix.sh /var/lib/dtc/etc 2>&1 >>/var/log/dtc.log
