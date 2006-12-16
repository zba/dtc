#!/bin/sh

echo "###############################################################"
echo "### Welcome to DTC config script for automatic installation ###"
echo "###############################################################"

echo ""
echo "Required packages before this script is run:"
echo " - Add DAG apt repository, as per http://dag.wieers.com/home-made/apt/FAQ.php#B (if you don't already have the dag yum repository)"
echo " - Add Dries repo (http://dries.ulyssis.org/rpm/clientconfig.html)"
echo " - Add NewRPMS repo (http://newrpms.sunsite.dk/docs/howto.txt)"
echo " - apt (yum install apt)"
echo " - remove sendmail (rpm -e sendmail sendmail-cf)"
echo " - postfix [rebuild the src rpm if encountering issues] (make sure inet_interfaces is correct) or qmail"
echo " - mysql-server (and started)"
echo " - courier-maildrop (download from http://tusker.sg/redhat/RPMS/i386/maildrop-2.0.1-2.i386.rpm or build your own)"
echo " - courier-authlib courier-imap and courier-pop (as per courier-maildrop above)"
echo " - php-mysql php-gd php-imap"
echo " - pear (or php-pear) (and pear install Crypt_CBC)"
echo " - bind (and started)"
echo " - mod_log_sql (from http://tusker.sg/redhat/RPMS/i386/mod_log_sql-1.100-2.dtc.i386.rpm) and enable the log_sql_module and log_sql_mysql_module module in /etc/httpd/conf.d/mod_log_sql.conf"
echo " - clamav (yum install clamav clamd)"
echo " - amavisd-new (yum install amavisd-new)"
echo " - sudo (yum install sudo)"
echo " - proftpd (Download the source rpm from http://dag.wieers.com/packages/proftpd/ and rpmbuild --rebuild <file> --with mysql) NOTE: The source rpm is older than the apt-get one, so don't upgrade proftpd..."
echo " - libnss-mysql (build yourself from source (rpmbuild -ta libnss-mysql-1.5.tar.gz)"
echo " - chrootuid (as per courier-maildrop above)"
echo " - mlmmj (built from src.rpm, ie http://ftp.dulug.duke.edu/pub/fedora/linux/extras/development/SRPMS/mlmmj-1.2.11-5.fc6.src.rpm)"
echo " - mhonarc (build from src.rpm, ie http://ftp.dulug.duke.edu/pub/fedora/linux/extras/development/SRPMS/mhonarc-2.6.16-2.fc6.src.rpm)"
echo " - optionally SqWebMail"
echo -n "Have you completed the above steps (yN)?"
read completed_steps
if [ ""$completed_steps = "y" -o ""$completed_steps = "Y" -o ""$completed_steps = "yes" ]; then
	echo "Starting interactive installer..."
else
	echo "Please come back later..."
	exit 1
fi 

. /usr/share/dtc/admin/install/redhat_config
. /usr/share/dtc/admin/install/interactive_installer
. /usr/share/dtc/admin/install/functions

interactiveInstaller
DTCinstallPackage
DTCsetupDaemons
