# /etc/cron.d/dtc

*/10 * * * * root [ -d /usr/share/dtc/admin ] && cd /usr/share/dtc/admin && /usr/bin/php /usr/share/dtc/admin/cron.php >> /var/log/dtc.log
9    4 * * * dtc  [ -d /usr/share/dtc/admin ] && cd /usr/share/dtc/admin && nice -n+20 /usr/bin/php /usr/share/dtc/admin/accesslog.php
*    * * * * root [ -d /usr/share/dtc/admin ] && cd /usr/share/dtc/admin && nice -n+20 /usr/share/dtc/admin/rrdtool.sh >> /var/log/dtc.log
34   0 * * * root [ -d /usr/share/dtc/admin ] && cd /usr/share/dtc/admin && /usr/bin/php /usr/share/dtc/admin/reminders.php
