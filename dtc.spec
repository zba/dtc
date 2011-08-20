Name: dtc-core
Version: __VERSION__
Release: 0.1.20101009
License: LGPL
Group: System Environment/Daemons
URL: http://www.gplhost.com/software-dtc.html
BuildArch: noarch
Source: dtc-core-%{version}.tar.gz
BuildRoot:%{_tmppath}/%{name}-%{version}-%{release}-root
BuildRequires: symlinks make gettext

Requires: libgcc php-mbstring /usr/sbin/chroot gzip /usr/bin/perl perl(MIME::Parser) perl(MIME::Tools) php-pear-XML-Serializer php-pear-Net-IPv4 perl(strict) cpio httpd mod_ssl sudo php-cli php mysql php-gd php-pear php-mysql bzip2 file gawk mod_log_sql openssh-clients cyrus-sasl-lib mailcap net-tools openssl patch php-fpdf php-pear-Crypt-CBC php-pear-Mail-Mime rrdtool unzip zip pam_mysql nss_mysql sbox vixie-cron gettext chrootuid, which, anacron, cyrus-sasl-md5, cyrus-sasl-plain
# FIXME remember to check for ncftp to be nuked by thomas
Prereq: /usr/sbin/useradd
Summary: web control panel for admin and accounting hosting services (common files)
Group: System Environment/Daemons
%description
Domain Technologie Control (DTC) is a control panel aiming at commercial
hosting. Using a web GUI for the administration and accounting all hosting
services, DTC can delegate the task of creating subdomains, email, ssh,
database, mailing lists, and FTP accounts to users for the domain names they
own.
DTC manages a MySQL database containing all the hosting informations,
and configure your server's services and apllication for doing virtual hosting
(DTC is compabible with a huge list of applications). It also connects to
dtc-xen to manage and monitor the usage of Virtual Private Servers (VPS), it
does the billing in general (including billing of dedicated servers), has
integrated support tickets and more.
This package contains the common files.

%package -n dtc-postfix-courier
Summary: web control panel for admin and accounting hosting services (more depends)
Group: System Environment/Daemons
Requires: squirrelmail, maildrop, dtc-core, awstats, courier-authlib-userdb, courier-authlib-mysql, courier-imap, dkimproxy, mysql-server, bind, mlmmj, pure-ftpd, webalizer, amavisd-new, postfix, spamassassin, clamav, clamav-db, clamd, fetchmail, perl-Net-Whois, phpmyadmin, php-mcrypt, dtc-dos-firewall
%description -n dtc-postfix-courier
Domain Technologie Control (DTC) is a control panel aiming at commercial
hosting. Using a web GUI for the administration and accounting all hosting
services, DTC can delegate the task of creating subdomains, email, ssh,
database, mailing lists, and FTP accounts to users for the domain names they
own.
DTC manages a MySQL database containing all the hosting informations,
and configure your server's services and apllication for doing virtual hosting
(DTC is compabible with a huge list of applications). It also connects to
dtc-xen to manage and monitor the usage of Virtual Private Servers (VPS), it
does the billing in general (including billing of dedicated servers), has
integrated support tickets and more.
This package contains more dependencies to have the maximum setup.

%package -n dtc-dos-firewall
Summary: a small anti-DoS firewall script for your web, ftp and mail servers
Group: System Environment/Daemons
Requires: iptables
%description -n dtc-dos-firewall
If running in a production environment, you might want to have a basic
firewall running on your server to avoid having DoS attack. This is not the
state-of-the-art, but just another attempt to make things a bit more smooth.

%package -n dtc-stats-daemon
Summary: DTC-Xen VM statistics for the DTC web control panel
Group: System Environment/Daemons
Requires: dtc-core
%description -n dtc-stats-daemon
Domain Technologie Control (DTC) is a control panel aiming at commercial
hosting. This small daemon will query all the dtc-xen servers that you have
configured in DTC and fetch the statistics from them: I/O stats, network and
CPU. This information is then stored in DTC for your customer accounting.

%prep
%setup

%build
echo "No build needed"

%install

set -e

%{__rm} -rf %{buildroot}
mkdir -p %{buildroot}
make install-dtc-common DESTDIR=%{buildroot} UNIX_TYPE=redhat MANUAL_DIR=%{_mandir} DTC_APP_DIR=%{_datadir} \
	DTC_GEN_DIR=%{_localstatedir}/lib CONFIG_DIR=%{_sysconfdir} DTC_DOC_DIR=%{_defaultdocdir} BIN_DIR=%{_bindir}

make install-dtc-stats-daemon DESTDIR=%{buildroot} UNIX_TYPE=redhat MANUAL_DIR=%{_mandir} DTC_APP_DIR=%{_datadir} \
	DTC_GEN_DIR=%{_localstatedir}/lib CONFIG_DIR=%{_sysconfdir} DTC_DOC_DIR=%{_defaultdocdir} BIN_DIR=%{_bindir} INIT_DIR=%{_initrddir}

make install-dtc-dos-firewall DESTDIR=%{buildroot} UNIX_TYPE=redhat MANUAL_DIR=%{_mandir} DTC_APP_DIR=%{_datadir} \
	DTC_GEN_DIR=%{_localstatedir}/lib CONFIG_DIR=%{_sysconfdir} DTC_DOC_DIR=%{_defaultdocdir} BIN_DIR=%{_bindir} INIT_DIR=%{_initrddir}

symlinks -crs %{buildroot}

%pre
if [ "$1" = "1" ] ; then  # first install
# Add the "dtc:dtcgrp" users/groups
	/usr/sbin/groupadd -r dtcgrp 2> /dev/null || :
	/usr/sbin/useradd -r -m -s /bin/bash -g dtcgrp dtc 2> /dev/null || :
fi

%clean
%{__rm} -rf %{buildroot} 2>&1 >/dev/null

%files
%defattr(-, root, root, -)
%{_datadir}/dtc/admin/*
%{_datadir}/dtc/client/*
%{_datadir}/dtc/email/*
%{_datadir}/dtc/shared/*
%{_bindir}/dtc-chroot*
%{_datadir}/dtc/doc
%doc %{_defaultdocdir}/dtc/*
%{_mandir}/man?/*
%config %{_localstatedir}/lib/dtc
%docdir %{_defaultdocdir}/dtc
%config %{_sysconfdir}/cron.d/dtc
%config %{_sysconfdir}/logrotate.d/dtc
%config %{_sysconfdir}/logrotate.d/dtc-vhosts
%config %{_sysconfdir}/dtc/logrotate.template
%config(noreplace) %{_sysconfdir}/dtc/reminders_msg
%config(noreplace) %{_sysconfdir}/dtc/registration_msg
%config(noreplace) %{_sysconfdir}/dtc/tickets
%config(noreplace) %{_sysconfdir}/dtc/*.txt
%config(noreplace) %{_sysconfdir}/dtc/chroot_allowed_path

%files -n dtc-postfix-courier
%files -n dtc-stats-daemon
%config %{_initrddir}/dtc-stats-daemon
%config %{_sysconfdir}/logrotate.d/dtc-stats-daemon
%{_sbindir}/dtc-stats-daemon
%files -n dtc-dos-firewall
%config(noreplace) %{_sysconfdir}/dtc/dtc-dos-firewall.conf
%config %{_initrddir}/dtc-dos-firewall

%post -n dtc-stats-daemon
mkdir %{_var}/lib/dtc/dtc-xenservers-rrds

%changelog
* Mon Sep 28 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.9-0.1.20090928
- New upstream release.
- Added dependency to libgcc so ssh accounts are working.

* Mon Aug 31 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.4-0.1.20090831
- Source RPM is now called dtc-core, and there's no dtc package anymore.

* Sat Aug 08 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.4-0.1.20090808
- Fixed the sasldb2 link
- Fixed the restart of dkimproxy in the cron.php

* Thu Aug 06 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.4-0.1.20090806
- Fixed the setup of pure-ftpd
- Added inet_interfaces = all in postfix main.cf

* Tue Aug 04 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.4-0.1.20090804
- Fixed the dtc-stats-daemon last issues

* Sun Aug 02 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.4-0.1.20090802
- Fixed the dtc-stats-daemon init.d script and daemon

* Sat Aug 01 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.4-0.1.20090801
- CentOS beta version

* Thu Jul 30 2009 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.30.3-0.4.20090730
- Restored defattr in files
- Made it so the postinst phase is silent
- dtc-stats-daemon goes into /usr/sbin

* Thu Jul 30 2009 Thomas Goirand (zigo) <thomas@goirand.fr> 0.30.3-0.2.20090730
- Pre-release

* Tue Dec 16 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.29.15-12.gplhost
- Updated package to 0.29.15

* Tue Feb 19 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.28.2-11.gplhost
- UNIX_TYPE=redhat in make install
- Updated package to 0.28.2

* Fri Jan 18 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.8-10.gplhost
- Fix postin postunin scripts

* Fri Jan 18 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.8-9.gplhost
- Create DTC username and group upon install / nuke upon delete

* Thu Jan 17 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.8-8.gplhost
- Made more *Graph scripts executable
- Try 2 on the sysconfdir (now on localstatedir/lib) etc/

* Wed Jan 16 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.6-7.gplhost
- Fixed RRD creation scripts permissions, and other scripts made executable
- Fixed DTC configuration directory /var/lib/dtc/etc, now included in the manifest

* Wed Jan 09 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.6-6.gplhost
- Make postinstall scripts executable

* Wed Jan 09 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.6-5.gplhost
- Fixed permissions and owners in some files

* Tue Jan 08 2008 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.5-4.gplhost
- Removed optional dependencies on clamd and spamassassin

* Thu Nov 22 2007 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.3-3.gplhost
- Generate the latest stable package automatically

* Mon Nov 20 2007 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.1.gplhost
- Added logrotate.d and cron.d files
- Added cron dependency
- Dropped ncftp dependency in anticipation of FTP scripting work

* Wed Nov 14 2007 Manuel Amador (Rudd-O) <rudd-o@rudd-o.com> 0.27.1-1.gplhost
- First construction for CentOS 5

* Thu Mar 03 2005 Jim Perrin <jperrin@gmail.com> - 1.100-1
- Initial Package (derived from mod_log_sql 1.100 specfile)
