# dtc
Domain Technologie Control

How to contribute.
Clone the repository or fork it.
cd /usr/share
git clone https://github.com/zba/dtc.git

Checkout -b your branch

make changes.

git add/rm

git commit

git push

How to build from source
Install required dependences before package build.
Dependencies:
autopoint{a} debhelper dh-autoreconf{a} dh-strip-nondeterminism{a} dwz{a} intltool-debian{a} libarchive-zip-perl{a} libdebhelper-perl{a} 
  libfile-stripnondeterminism-perl{a} libsub-override-perl{a} po-debconf 
Debian Users:
dpkg-buildpackage in /usr/share/dtc directory after clone.

Other Users:
******************************************************************
*Please select one of the following targets:                     *
*install-dtc-stats-daemon, install-dtc-common, bsd-ports-packages*
*install-dtc-dos-firewall or make debian-packages                *
*Note that debian users should NOT use make debian-packages      *
*directly, but dpkg-buildpackage that will call it.              *
******************************************************************


How to install.
/usr/share/dtc/admin/install/install

How to uninstall
/usr/share/dtc/admin/install/uninstall.

