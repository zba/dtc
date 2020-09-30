# dtc
Domain Technologie Control

The documentation for this project is compiled and installed during installation.

If you need to view it for assistance during or before installation it can be found at the link below.

https://github.com/timothygwebb/dtc/blob/master/doc/html/en/2.html

How to contribute.
Clone the repository or fork it to /usr/share

cd /usr/share

git clone https://github.com/timothygwebb/dtc.git

Install required dependences before package build.

Dependencies:

Debian Users:

Install dependencies as follows apt-get install or my preferred method aptitude install

default-mysql-client default-mysql-server galera-3 altermime amavisd-new clamav clamav-daemon clamav-freshclam courier-authlib expect libberkeleydb-perl 
libclamav9 libconvert-binhex-perl libconvert-tnef-perl libconvert-uulib-perl libcourier-unicode4 libdbd-mysql-perl libio-multiplex-perl libjemalloc1 
libmime-tools-perl libnet-cidr-perl libnet-server-perl libtfm1 libunix-syslog-perl maildrop mariadb-client-10.1 mariadb-client-core-10.1 
mariadb-server-10.1 mariadb-server-core-10.1 mysql-server pax ripole tcl-expect composer jsonlint php-composer-ca-bundle php-composer-semver 
php-composer-spdx-licenses php-composer-xdebug-handler php-json-schema php-symfony-console php-symfony-filesystem php-symfony-finder 
php-symfony-polyfill-php80 php-symfony-process 

apt-get update or aptitude update

apt-get upgrade or aptitude upgrade

Checkout -b your branch

make changes.

git add/rm

git commit

git push

Then Debian Users:

dpkg-buildpackage in /usr/share/dtc directory after clone.

Other Users:
******************************************************************
*Please select one of the following targets:                     *
*install-dtc-stats-daemon, install-dtc-common, bsd-ports-packages*
*install-dtc-dos-firewall or make debian-packages                *
*Note that debian users should NOT use make debian-packages      *
*directly, but dpkg-buildpackage that will call it.              *
******************************************************************

The above process will build the following packages.
dpkg-deb: building package 'dtc-postfix-dovecot' in '../dtc-postfix-dovecot_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-cyrus' in '../dtc-cyrus_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-stats-daemon' in '../dtc-stats-daemon_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-common' in '../dtc-common_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-autodeploy' in '../dtc-autodeploy_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-core' in '../dtc-core_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-toaster' in '../dtc-toaster_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-postfix-courier' in '../dtc-postfix-courier_0.34.5-1_all.deb'.
dpkg-deb: building package 'dtc-dos-firewall' in '../dtc-dos-firewall_0.34.5-1_all.deb'.

The packages will be in the directory above your chosen directory. 

In this case it would be /usr/share

cd to /usr/share and execute the following.

dpkg -i dtc-common_0.34.5-1_all.deb

then execute the following

How to install.
/usr/share/dtc/admin/install/install

How to uninstall
/usr/share/dtc/admin/install/uninstall.

