Summary: Domain Technologie Control
Name: dtc-core
Version: 0.30.1
Release: 11.gplhost
License: Other
Group: System Environment/Daemons
URL: http://www.gplhost.com/software-dtc.html
BuildArch: noarch

Source: dtc-%{version}.tar.gz

BuildRoot:%{_tmppath}/%{name}-%{version}-%{release}-root

BuildRequires: symlinks make gettext
Requires: /bin/bash /bin/sh /usr/bin/env /usr/bin/perl perl(MIME::Parser) perl(MIME::Tools) perl(strict) httpd mod_ssl sudo php-cli php mysql mysql-server php-gd php-pear php-mysql bind bzip2 file gawk mod_log_sql openssh-clients cyrus-sasl-lib mailcap mlmmj net-tools openssl patch php-fpdf php-pear-Crypt-CBC rrdtool unzip zip pam_mysql nss_mysql sbox vixie-cron gettext chrootuid, which, anacron, cyrus-sasl-md5, cyrus-sasl-plain
# FIXME create multideps package, those should be pulled by it: amavisd-new clamav clamd spamassassin - these are not core dependencies but should be in psotfix-courier or whatever
# FIXME package-multideps like %package postfix-courier with the extra deps
# FIXME remember to check for ncftp to be nuked by thomas
Prereq: /usr/sbin/useradd

%description
Domain Technologie Control is a Web-based management panel for heavy-duty
Web hosting, network service and Xen virtual machine management.

%prep
%setup -n dtc

%build
echo "No build needed"

%install
%{__rm} -rf %{buildroot}
mkdir -p %{buildroot}
make DESTDIR=%{buildroot} UNIX_TYPE=redhat install-dtc-common
symlinks -crs %{buildroot}

%pre
if [ "$1" = "1" ] ; then  # first install
# Add the "dtc:dtcgrp" users/groups
	/usr/sbin/groupadd -r dtcgrp 2> /dev/null || :
	/usr/sbin/useradd -r -m -s /bin/bash -g dtcgrp dtc 2> /dev/null || :
fi

%postun
if [ "$1" = "0" ] ; then  # last uninstall
	/usr/sbin/userdel -r dtc
	/usr/sbin/groupdel dtcgrp
fi

%clean
%{__rm} -rf %{buildroot}

%files
%defattr(0644, root, root, 0755)
%{_datadir}/dtc/admin/*
%{_datadir}/dtc/client/*
%{_datadir}/dtc/email/*
%{_datadir}/dtc/shared/*
%{_datadir}/dtc/doc
%{_defaultdocdir}/dtc
%{_mandir}/man?/*
%config %{_sysconfdir}/cron.d/dtc
%config %{_sysconfdir}/logrotate.d/dtc
%config %{_sysconfdir}/logrotate.d/dtc-vhosts
%config %{_sysconfdir}/dtc
%config %{_localstatedir}/lib/dtc
%docdir %{_defaultdocdir}/dtc
# ROOT_ONLY
%attr(0750, root, root) %{_datadir}/dtc/admin/install/*
%attr(0750, root, root) %{_datadir}/dtc/admin/checkbind.sh
%attr(0750, root, root) %{_datadir}/dtc/admin/cron.php
%attr(0750, root, root) %{_datadir}/dtc/admin/reminders.php
# USER_ALSO
%attr(0755, root, root) %{_datadir}/dtc/admin/sa-wrapper
%attr(0755, root, root) %{_datadir}/dtc/admin/dtc-chroot-shell
%attr(0755, root, root) %{_datadir}/dtc/admin/accesslog.php
%attr(0755, root, root) %{_datadir}/dtc/admin/maint_apache.php
%attr(0755, root, root) %{_datadir}/dtc/admin/checkbind.sh
%attr(0755, root, root) %{_datadir}/dtc/admin/ip_change.sh
%attr(0755, root, root) %{_datadir}/dtc/admin/genfiles/change_debconf_domain.sh
%attr(0755, root, root) %{_datadir}/dtc/admin/genfiles/change_debconf_ip.sh
%attr(0755, root, root) %{_datadir}/dtc/admin/genfiles/gen_customer_ssl_cert.sh
%attr(0755, root, root) %{_datadir}/dtc/admin/*graph/*.sh
%attr(0755, root, root) %{_datadir}/dtc/admin/rrdtool.sh
%attr(0755, root, root) %{_datadir}/dtc/admin/updateChroot.sh


%changelog
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
