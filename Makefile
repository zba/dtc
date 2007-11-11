# Makefile for dtc-common

# Example call parameters:
# make install-dtc-common DESTDIR=/tmp/test_dtc \
# DTC_APP_DIR=/usr/share DTC_GEN_DIR=/var/lib \
# CONFIG_DIR=/etc DTC_DOC_DIR=/usr/share/doc \
# MANUAL_DIR=/usr/share/man

ifndef $(DTC_APP_DIR)
	DTC_APP_DIR=/usr/share
endif
ifndef $(DTC_GEN_DIR)
	DTC_GEN_DIR=/var/lib
endif
ifndef $(CONFIG_DIR)
	CONFIG_DIR=/etc
endif
ifndef $(DTC_DOC_DIR)
	DTC_DOC_DIR=/usr/share/doc
endif
ifndef $(MANUAL_DIR)
	MANUAL_DIR=/usr/share/man
endif
ifndef $(BIN_DIR)
	BIN_DIR=/bin
endif

# /usr/share
APP_INST_DIR = $(DESTDIR)$(DTC_APP_DIR)/dtc
# /var/lib
GENFILES_DIRECTORY = $(DESTDIR)$(DTC_GEN_DIR)/dtc
# /etc
ETC_DIRECTORY = $(DESTDIR)$(CONFIG_DIR)/dtc
# /usr/share/doc
DOC_DIR = $(DESTDIR)$(DTC_DOC_DIR)/dtc
# /usr/share/man
MAN_DIR = $(DESTDIR)$(MANUAL_DIR)
# /usr/bin
BINARY_DIR = $(DESTDIR)$(BIN_DIR)

INSTALL = install

default:
	@echo ""
	@echo "*** Error: there is no default target in this Makefile! ***"
	@echo "Please select one of the following targets:"
	@echo "installstatsdaemon, install-dtc-common"
	@echo "and don't forget to set the following variables:"
	@echo "DESTDIR="$(DESTDIR)
	@echo "DTC_APP_DIR="$(DTC_APP_DIR)
	@echo "DTC_GEN_DIR="$(DTC_GEN_DIR)
	@echo "CONFIG_DIR="$(CONFIG_DIR)
	@echo "DTC_DOC_DIR="$(DTC_DOC_DIR)
	@echo "MANUAL_DIR="$(MANUAL_DIR)
	@echo "BIN_DIR="$(BIN_DIR)
	@echo ""
	@exit 1

installstatsdaemon:
	install -D -m 0644 admin/dtc-stats-daemon.php $(APP_INST_DIR)/admin

install-dtc-common:
	### admin dir ###
	# PHP scripts files served by web server
	install -D -m 0644 admin/favicon.ico			$(APP_INST_DIR)/admin/favicon.ico
	install -D -m 0644 admin/404.php			$(APP_INST_DIR)/admin/404.php
	install -D -m 0644 admin/awstats.dtc.conf		$(APP_INST_DIR)/admin/awstats.dtc.conf
	install -D -m 0644 admin/bw_per_month.php		$(APP_INST_DIR)/admin/bw_per_month.php
	install -D -m 0644 admin/index.php			$(APP_INST_DIR)/admin/index.php
	install -D -m 0644 admin/cpugraph.php			$(APP_INST_DIR)/admin/cpugraph.php
	install -D -m 0644 admin/mailgraph.php			$(APP_INST_DIR)/admin/mailgraph.php
	install -D -m 0644 admin/deamons_state.php		$(APP_INST_DIR)/admin/deamons_state.php
	install -D -m 0644 admin/deamons_state_strings.php	$(APP_INST_DIR)/admin/deamons_state_strings.php
	install -D -m 0644 admin/view_waitingusers.php		$(APP_INST_DIR)/admin/view_waitingusers.php
	install -D -m 0644 admin/memgraph.php			$(APP_INST_DIR)/admin/memgraph.php
	install -D -m 0644 admin/netusegraph.php		$(APP_INST_DIR)/admin/netusegraph.php
	install -D -m 0644 admin/vps_stats_cpu.php		$(APP_INST_DIR)/admin/vps_stats_cpu.php
	install -D -m 0644 admin/vps_stats_hdd.php		$(APP_INST_DIR)/admin/vps_stats_hdd.php
	install -D -m 0644 admin/vps_stats_network.php		$(APP_INST_DIR)/admin/vps_stats_network.php
	install -D -m 0644 admin/vps_stats_swap.php		$(APP_INST_DIR)/admin/vps_stats_swap.php
	install -D -m 0644 admin/patch_saslatuhd_startup	$(APP_INST_DIR)/admin/patch_saslatuhd_startup

	# Management scripts that are executed
	install -D -m 0764 admin/cron.php		$(APP_INST_DIR)/admin/cron.php
	install -D -m 0764 admin/accesslog.php		$(APP_INST_DIR)/admin/accesslog.php
	install -D -m 0764 admin/maint_apache.php	$(APP_INST_DIR)/admin/maint_apache.php
	install -D -m 0764 admin/reminders.php		$(APP_INST_DIR)/admin/reminders.php

	install -D -m 0764 admin/rrdtool.sh		$(APP_INST_DIR)/admin/rrdtool.sh
	install -D -m 0764 admin/checkbind.sh		$(APP_INST_DIR)/admin/checkbind.sh
	install -D -m 0764 admin/updateChroot.sh	$(APP_INST_DIR)/admin/updateChroot.sh
	install -D -m 0764 admin/ip_change.sh		$(APP_INST_DIR)/admin/ip_change.sh
	install -D -m 0764 admin/sa-wrapper		$(APP_INST_DIR)/admin/sa-wrapper
	install -D -m 0764 admin/dtc-chroot-shell	$(BINARY_DIR)/dtc-chroot-shell

	# sh scripts for rrdtool stuffs
	install -D -m 0764 admin/queuegraph/count_postfix.sh	$(APP_INST_DIR)/admin/queuegraph/count_postfix.sh
	install -D -m 0764 admin/queuegraph/count_qmail.sh	$(APP_INST_DIR)/admin/queuegraph/count_qmail.sh
	install -D -m 0764 admin/queuegraph/createrrd.sh	$(APP_INST_DIR)/admin/queuegraph/createrrd.sh
	install -D -m 0764 admin/cpugraph/createrrd.sh		$(APP_INST_DIR)/admin/cpugraph/createrrd.sh
	install -D -m 0764 admin/cpugraph/get_cpu_load.sh	$(APP_INST_DIR)/admin/cpugraph/get_cpu_load.sh
	install -D -m 0764 admin/memgraph/createrrd.sh		$(APP_INST_DIR)/admin/memgraph/createrrd.sh
	install -D -m 0764 admin/memgraph/get_meminfo.sh	$(APP_INST_DIR)/admin/memgraph/get_meminfo.sh
	install -D -m 0764 admin/netusegraph/createrrd.sh	$(APP_INST_DIR)/admin/netusegraph/createrrd.sh
	install -D -m 0764 admin/netusegraph/get_net_usage.sh	$(APP_INST_DIR)/admin/netusegraph/get_net_usage.sh

	# genfiles PHP and SH scripts
	install -D -m 0640 admin/genfiles/gen_awstats.php			$(APP_INST_DIR)/admin/genfiles/gen_awstats.php
	install -D -m 0640 admin/genfiles/gen_postfix_email_account.php		$(APP_INST_DIR)/admin/genfiles/gen_postfix_email_account.php
	install -D -m 0640 admin/genfiles/gen_perso_vhost.php			$(APP_INST_DIR)/admin/genfiles/gen_perso_vhost.php
	install -D -m 0640 admin/genfiles/gen_postfix_email_account.php		$(APP_INST_DIR)/admin/genfiles/gen_postfix_email_account.php
	install -D -m 0640 admin/genfiles/gen_backup_script.php			$(APP_INST_DIR)/admin/genfiles/gen_backup_script.php
	install -D -m 0640 admin/genfiles/gen_pro_vhost.php			$(APP_INST_DIR)/admin/genfiles/gen_pro_vhost.php
	install -D -m 0640 admin/genfiles/gen_qmail_email_account.php		$(APP_INST_DIR)/admin/genfiles/gen_qmail_email_account.php
	install -D -m 0640 admin/genfiles/gen_email_account.php			$(APP_INST_DIR)/admin/genfiles/gen_email_account.php
	install -D -m 0640 admin/genfiles/genfiles.php				$(APP_INST_DIR)/admin/genfiles/genfiles.php
	install -D -m 0640 admin/genfiles/gen_ssh_account.php			$(APP_INST_DIR)/admin/genfiles/gen_ssh_account.php
	install -D -m 0640 admin/genfiles/gen_maildrop_userdb.php		$(APP_INST_DIR)/admin/genfiles/gen_maildrop_userdb.php
	install -D -m 0640 admin/genfiles/gen_webalizer_stat.php		$(APP_INST_DIR)/admin/genfiles/gen_webalizer_stat.php
	install -D -m 0640 admin/genfiles/gen_named_files_alt-wildcard.php	$(APP_INST_DIR)/admin/genfiles/gen_named_files_alt-wildcard.php
	install -D -m 0640 admin/genfiles/remote_mail_list.php			$(APP_INST_DIR)/admin/genfiles/remote_mail_list.php
	install -D -m 0640 admin/genfiles/gen_named_files.php			$(APP_INST_DIR)/admin/genfiles/gen_named_files.php
	install -D -m 0640 admin/genfiles/mailfilter_vacation_template		$(APP_INST_DIR)/admin/genfiles/mailfilter_vacation_template
	install -D -m 0640 admin/genfiles/gen_pro_vhost_alt-wildcard.php	$(APP_INST_DIR)/admin/genfiles/gen_pro_vhost_alt-wildcard.php
	install -D -m 0744 admin/genfiles/change_debconf_domain.sh		$(APP_INST_DIR)/admin/genfiles/change_debconf_domain.sh
	install -D -m 0744 admin/genfiles/change_debconf_ip.sh			$(APP_INST_DIR)/admin/genfiles/change_debconf_ip.sh
	install -D -m 0744 admin/genfiles/gen_customer_ssl_cert.sh		$(APP_INST_DIR)/admin/genfiles/gen_customer_ssl_cert.sh 

	# inc PHP scripts
	install -D -m 0640 admin/inc/img_alt_skin.php			$(APP_INST_DIR)/admin/inc/img_alt_skin.php
	install -D -m 0640 admin/inc/img.php				$(APP_INST_DIR)/admin/inc/img.php
	install -D -m 0640 admin/inc/renewals.php			$(APP_INST_DIR)/admin/inc/renewals.php
	install -D -m 0640 admin/inc/renewals_strings.php		$(APP_INST_DIR)/admin/inc/renewals_strings.php
	install -D -m 0640 admin/inc/draw_user_admin.php		$(APP_INST_DIR)/admin/inc/draw_user_admin.php
	install -D -m 0640 admin/inc/draw_user_admin_strings.php	$(APP_INST_DIR)/admin/inc/draw_user_admin_strings.php
	install -D -m 0640 admin/inc/dtc_config.php			$(APP_INST_DIR)/admin/inc/dtc_config.php
	install -D -m 0640 admin/inc/dtc_config_strings.php		$(APP_INST_DIR)/admin/inc/dtc_config_strings.php
	install -D -m 0640 admin/inc/monitor.php			$(APP_INST_DIR)/admin/inc/monitor.php
	install -D -m 0640 admin/inc/submit_root_querys.php		$(APP_INST_DIR)/admin/inc/submit_root_querys.php
	install -D -m 0640 admin/inc/graphs.php				$(APP_INST_DIR)/admin/inc/graphs.php
	install -D -m 0640 admin/inc/nav.php				$(APP_INST_DIR)/admin/inc/nav.php
	install -D -m 0640 admin/inc/img_alt.php			$(APP_INST_DIR)/admin/inc/img_alt.php
	# inc png files
	install -D -m 0640 admin/inc/adddomain.png	$(APP_INST_DIR)/admin/inc/adddomain.png
	install -D -m 0640 admin/inc/package-installer.png	$(APP_INST_DIR)/admin/inc/package-installer.png
	install -D -m 0640 admin/inc/databases.png	$(APP_INST_DIR)/admin/inc/databases.png
	install -D -m 0640 admin/inc/imglong.png	$(APP_INST_DIR)/admin/inc/imglong.png
	install -D -m 0640 admin/inc/password.png	$(APP_INST_DIR)/admin/inc/password.png
	install -D -m 0640 admin/inc/dedic-server.png	$(APP_INST_DIR)/admin/inc/dedic-server.png
	install -D -m 0640 admin/inc/domains.png	$(APP_INST_DIR)/admin/inc/domains.png
	install -D -m 0640 admin/inc/imgshort.png	$(APP_INST_DIR)/admin/inc/imgshort.png
	install -D -m 0640 admin/inc/mailaliasgroup.png	$(APP_INST_DIR)/admin/inc/mailaliasgroup.png
	install -D -m 0640 admin/inc/reseller.png	$(APP_INST_DIR)/admin/inc/reseller.png
	install -D -m 0640 admin/inc/mailboxs.png	$(APP_INST_DIR)/admin/inc/mailboxs.png
	install -D -m 0640 admin/inc/ssh-accounts.png	$(APP_INST_DIR)/admin/inc/ssh-accounts.png
	install -D -m 0640 admin/inc/mailing-lists.png	$(APP_INST_DIR)/admin/inc/mailing-lists.png
	install -D -m 0640 admin/inc/stats.png		$(APP_INST_DIR)/admin/inc/stats.png
	install -D -m 0640 admin/inc/subdomains.png	$(APP_INST_DIR)/admin/inc/subdomains.png
	install -D -m 0640 admin/inc/folder.png		$(APP_INST_DIR)/admin/inc/folder.png
	install -D -m 0640 admin/inc/ftp-accounts.png	$(APP_INST_DIR)/admin/inc/ftp-accounts.png
	install -D -m 0640 admin/inc/my-account.png	$(APP_INST_DIR)/admin/inc/my-account.png
	install -D -m 0640 admin/inc/ticket.png		$(APP_INST_DIR)/admin/inc/ticket.png
	install -D -m 0640 admin/inc/nameservers.png	$(APP_INST_DIR)/admin/inc/nameservers.png
	install -D -m 0640 admin/inc/tools.png		$(APP_INST_DIR)/admin/inc/tools.png
	install -D -m 0640 admin/inc/help.png		$(APP_INST_DIR)/admin/inc/help.png
	install -D -m 0640 admin/inc/virtual-server.png	$(APP_INST_DIR)/admin/inc/virtual-server.png
	install -D -m 0640 admin/inc/nickhandles.png	$(APP_INST_DIR)/admin/inc/nickhandles.png

	# install sh scripts
	install -D -m 0640 admin/install/mk_root_mailbox.php	$(APP_INST_DIR)/admin/install/mk_root_mailbox.php
	install -D -m 0744 admin/install/bsd_config		$(APP_INST_DIR)/admin/install/bsd_config
	install -D -m 0744 admin/install/gentoo_config		$(APP_INST_DIR)/admin/install/gentoo_config

	install -D -m 0744 admin/install/slack_config		$(APP_INST_DIR)/admin/install/slack_config
	install -D -m 0744 admin/install/debian_config		$(APP_INST_DIR)/admin/install/debian_config
	install -D -m 0744 admin/install/install		$(APP_INST_DIR)/admin/install/install
	install -D -m 0744 admin/install/osx_config		$(APP_INST_DIR)/admin/install/osx_config
	install -D -m 0744 admin/install/uninstall		$(APP_INST_DIR)/admin/install/uninstall
	install -D -m 0744 admin/install/functions		$(APP_INST_DIR)/admin/install/functions
	install -D -m 0744 admin/install/interactive_installer	$(APP_INST_DIR)/admin/install/interactive_installer
	install -D -m 0744 admin/install/redhat_config		$(APP_INST_DIR)/admin/install/redhat_config

	# The SQL table scripts
	install -D -m 0644 admin/tables/admin.sql		$(APP_INST_DIR)/admin/tables/admin.sql
	install -D -m 0644 admin/tables/backup.sql		$(APP_INST_DIR)/admin/tables/backup.sql
	install -D -m 0644 admin/tables/clients.sql		$(APP_INST_DIR)/admin/tables/clients.sql
	install -D -m 0644 admin/tables/commande.sql		$(APP_INST_DIR)/admin/tables/commande.sql
	install -D -m 0644 admin/tables/companies.sql		$(APP_INST_DIR)/admin/tables/companies.sql
	install -D -m 0644 admin/tables/completedorders.sql	$(APP_INST_DIR)/admin/tables/completedorders.sql
	install -D -m 0644 admin/tables/config.sql		$(APP_INST_DIR)/admin/tables/config.sql
	install -D -m 0644 admin/tables/cron_job.sql		$(APP_INST_DIR)/admin/tables/cron_job.sql
	install -D -m 0644 admin/tables/dedicated.sql		$(APP_INST_DIR)/admin/tables/dedicated.sql
	install -D -m 0644 admin/tables/domain.sql		$(APP_INST_DIR)/admin/tables/domain.sql
	install -D -m 0644 admin/tables/email_accouting.sql	$(APP_INST_DIR)/admin/tables/email_accouting.sql
	install -D -m 0644 admin/tables/fetchmail.sql		$(APP_INST_DIR)/admin/tables/fetchmail.sql
	install -D -m 0644 admin/tables/freeradius.sql		$(APP_INST_DIR)/admin/tables/freeradius.sql
	install -D -m 0644 admin/tables/ftp_access.sql		$(APP_INST_DIR)/admin/tables/ftp_access.sql
	install -D -m 0644 admin/tables/ftp_accounting.sql	$(APP_INST_DIR)/admin/tables/ftp_accounting.sql
	install -D -m 0644 admin/tables/ftp_logs.sql		$(APP_INST_DIR)/admin/tables/ftp_logs.sql
	install -D -m 0644 admin/tables/groups.sql		$(APP_INST_DIR)/admin/tables/groups.sql
	install -D -m 0644 admin/tables/handle.sql		$(APP_INST_DIR)/admin/tables/handle.sql
	install -D -m 0644 admin/tables/http_accounting.sql	$(APP_INST_DIR)/admin/tables/http_accounting.sql
	install -D -m 0644 admin/tables/invoicing.sql		$(APP_INST_DIR)/admin/tables/invoicing.sql
	install -D -m 0644 admin/tables/ip_port_service.sql	$(APP_INST_DIR)/admin/tables/ip_port_service.sql
	install -D -m 0644 admin/tables/mailaliasgroup.sql	$(APP_INST_DIR)/admin/tables/mailaliasgroup.sql
	install -D -m 0644 admin/tables/mailinglist.sql		$(APP_INST_DIR)/admin/tables/mailinglist.sql
	install -D -m 0644 admin/tables/nameservers.sql		$(APP_INST_DIR)/admin/tables/nameservers.sql
	install -D -m 0644 admin/tables/new_admin.sql		$(APP_INST_DIR)/admin/tables/new_admin.sql
	install -D -m 0644 admin/tables/paiement.sql		$(APP_INST_DIR)/admin/tables/paiement.sql
	install -D -m 0644 admin/tables/pending_queries.sql	$(APP_INST_DIR)/admin/tables/pending_queries.sql
	install -D -m 0644 admin/tables/pending_renewal.sql	$(APP_INST_DIR)/admin/tables/pending_renewal.sql
	install -D -m 0644 admin/tables/pop_access.sql		$(APP_INST_DIR)/admin/tables/pop_access.sql
	install -D -m 0644 admin/tables/product.sql		$(APP_INST_DIR)/admin/tables/product.sql
	install -D -m 0644 admin/tables/scheduled_updates.sql	$(APP_INST_DIR)/admin/tables/scheduled_updates.sql
	install -D -m 0644 admin/tables/secpayconf.sql		$(APP_INST_DIR)/admin/tables/secpayconf.sql
	install -D -m 0644 admin/tables/smtp_logs.sql		$(APP_INST_DIR)/admin/tables/smtp_logs.sql
	install -D -m 0644 admin/tables/ssh_access.sql		$(APP_INST_DIR)/admin/tables/ssh_access.sql
	install -D -m 0644 admin/tables/ssh_groups.sql		$(APP_INST_DIR)/admin/tables/ssh_groups.sql
	install -D -m 0644 admin/tables/ssh_user_group.sql	$(APP_INST_DIR)/admin/tables/ssh_user_group.sql
	install -D -m 0644 admin/tables/ssl_ips.sql		$(APP_INST_DIR)/admin/tables/ssl_ips.sql
	install -D -m 0644 admin/tables/subdomain.sql		$(APP_INST_DIR)/admin/tables/subdomain.sql
	install -D -m 0644 admin/tables/tik_admins.sql		$(APP_INST_DIR)/admin/tables/tik_admins.sql
	install -D -m 0644 admin/tables/tik_cats.sql		$(APP_INST_DIR)/admin/tables/tik_cats.sql
	install -D -m 0644 admin/tables/tik_queries.sql		$(APP_INST_DIR)/admin/tables/tik_queries.sql
	install -D -m 0644 admin/tables/vps_ip.sql		$(APP_INST_DIR)/admin/tables/vps_ip.sql
	install -D -m 0644 admin/tables/vps_server.sql		$(APP_INST_DIR)/admin/tables/vps_server.sql
	install -D -m 0644 admin/tables/vps.sql			$(APP_INST_DIR)/admin/tables/vps.sql
	install -D -m 0644 admin/tables/vps_stats.sql		$(APP_INST_DIR)/admin/tables/vps_stats.sql
	install -D -m 0644 admin/tables/whitelist.sql		$(APP_INST_DIR)/admin/tables/whitelist.sql
	install -D -m 0644 admin/tables/whois.sql		$(APP_INST_DIR)/admin/tables/whois.sql

	# The database upgrade scripts
	install -D -m 0644 bin/sources/dtc_db.php	$(APP_INST_DIR)/admin/dtc_db.php
	install -D -m 0644 bin/sources/restor_db.php	$(APP_INST_DIR)/admin/restor_db.php

	# dtcrm php scripts
	install -D -m 0644 admin/dtcrm/main.php				$(APP_INST_DIR)/admin/dtcrm/main.php
	install -D -m 0644 admin/dtcrm/product_manager.php		$(APP_INST_DIR)/admin/dtcrm/product_manager.php
	install -D -m 0644 admin/dtcrm/product_manager_strings.php	$(APP_INST_DIR)/admin/dtcrm/product_manager_strings.php
	install -D -m 0644 admin/dtcrm/submit_to_sql.php		$(APP_INST_DIR)/admin/dtcrm/submit_to_sql.php

	### client files ###
	install -D -m 0644 client/bw_per_month.php		$(APP_INST_DIR)/client/bw_per_month.php
	install -D -m 0644 client/dynip.php			$(APP_INST_DIR)/client/dynip.php
	install -D -m 0644 client/enets-notify.php		$(APP_INST_DIR)/client/enets-notify.php
	install -D -m 0644 client/index.php			$(APP_INST_DIR)/client/index.php
	install -D -m 0644 client/invoice.php			$(APP_INST_DIR)/client/invoice.php
	install -D -m 0644 client/list_domains.php		$(APP_INST_DIR)/client/list_domains.php
	install -D -m 0644 client/login.php			$(APP_INST_DIR)/client/login.php
	install -D -m 0644 client/new_account_form.php		$(APP_INST_DIR)/client/new_account_form.php
	install -D -m 0644 client/new_account.php		$(APP_INST_DIR)/client/new_account.php
	install -D -m 0644 client/new_account_renewal.php	$(APP_INST_DIR)/client/new_account_renewal.php
	install -D -m 0644 client/paypal.php			$(APP_INST_DIR)/client/paypal.php
	install -D -m 0644 client/secpaycallback_worldpay.php	$(APP_INST_DIR)/client/secpaycallback_worldpay.php
	install -D -m 0644 client/vps_stats_cpu.php		$(APP_INST_DIR)/client/vps_stats_cpu.php
	install -D -m 0644 client/vps_stats_hdd.php		$(APP_INST_DIR)/client/vps_stats_hdd.php
	install -D -m 0644 client/vps_stats_network.php		$(APP_INST_DIR)/client/vps_stats_network.php
	install -D -m 0644 client/vps_stats_swap.php		$(APP_INST_DIR)/client/vps_stats_swap.php
	install -D -m 0644 client/enets_pay_icon.gif		$(APP_INST_DIR)/client/enets_pay_icon.gif
	install -D -m 0644 client/favicon.ico			$(APP_INST_DIR)/client/favicon.ico

	# inc png files
	install -D -m 0640 admin/inc/adddomain.png		$(APP_INST_DIR)/client/inc/adddomain.png
	install -D -m 0640 admin/inc/package-installer.png	$(APP_INST_DIR)/client/inc/package-installer.png
	install -D -m 0640 admin/inc/databases.png		$(APP_INST_DIR)/client/inc/databases.png
	install -D -m 0640 client/inc/database.png		$(APP_INST_DIR)/client/inc/database.png
	install -D -m 0640 client/inc/dns.png			$(APP_INST_DIR)/client/inc/dns.png
	install -D -m 0640 client/inc/domain.png		$(APP_INST_DIR)/client/inc/domain.png
	install -D -m 0640 client/inc/domains.png		$(APP_INST_DIR)/client/inc/domains.png
	install -D -m 0640 client/inc/floppy.png		$(APP_INST_DIR)/client/inc/floppy.png
	install -D -m 0640 client/inc/folder.png		$(APP_INST_DIR)/client/inc/folder.png
	install -D -m 0640 client/inc/home.png			$(APP_INST_DIR)/client/inc/home.png
	install -D -m 0640 admin/inc/imglong.png		$(APP_INST_DIR)/client/inc/imglong.png
	install -D -m 0640 client/inc/imgshort.png		$(APP_INST_DIR)/client/inc/imgshort.png
	install -D -m 0640 client/inc/mail.png			$(APP_INST_DIR)/client/inc/mail.png
	install -D -m 0640 client/inc/man.png			$(APP_INST_DIR)/client/inc/man.png
	install -D -m 0640 client/inc/stat.png			$(APP_INST_DIR)/client/inc/stat.png
	install -D -m 0640 client/inc/tools.png			$(APP_INST_DIR)/client/inc/tools.png
	install -D -m 0640 admin/inc/password.png		$(APP_INST_DIR)/client/inc/password.png
	install -D -m 0640 admin/inc/dedic-server.png		$(APP_INST_DIR)/client/inc/dedic-server.png
	install -D -m 0640 admin/inc/domains.png		$(APP_INST_DIR)/client/inc/domains.png
	install -D -m 0640 admin/inc/imgshort.png		$(APP_INST_DIR)/client/inc/imgshort.png
	install -D -m 0640 admin/inc/mailaliasgroup.png		$(APP_INST_DIR)/client/inc/mailaliasgroup.png
	install -D -m 0640 admin/inc/reseller.png		$(APP_INST_DIR)/client/inc/reseller.png
	install -D -m 0640 admin/inc/mailboxs.png		$(APP_INST_DIR)/client/inc/mailboxs.png
	install -D -m 0640 admin/inc/ssh-accounts.png		$(APP_INST_DIR)/client/inc/ssh-accounts.png
	install -D -m 0640 admin/inc/mailing-lists.png		$(APP_INST_DIR)/client/inc/mailing-lists.png
	install -D -m 0640 admin/inc/stats.png			$(APP_INST_DIR)/client/inc/stats.png
	install -D -m 0640 admin/inc/subdomains.png		$(APP_INST_DIR)/client/inc/subdomains.png
	install -D -m 0640 admin/inc/folder.png			$(APP_INST_DIR)/client/inc/folder.png
	install -D -m 0640 admin/inc/ftp-accounts.png		$(APP_INST_DIR)/client/inc/ftp-accounts.png
	install -D -m 0640 admin/inc/my-account.png		$(APP_INST_DIR)/client/inc/my-account.png
	install -D -m 0640 admin/inc/ticket.png			$(APP_INST_DIR)/client/inc/ticket.png
	install -D -m 0640 admin/inc/nameservers.png		$(APP_INST_DIR)/client/inc/nameservers.png
	install -D -m 0640 admin/inc/tools.png			$(APP_INST_DIR)/client/inc/tools.png
	install -D -m 0640 admin/inc/help.png			$(APP_INST_DIR)/client/inc/help.png
	install -D -m 0640 admin/inc/virtual-server.png		$(APP_INST_DIR)/client/inc/virtual-server.png
	install -D -m 0640 admin/inc/nickhandles.png		$(APP_INST_DIR)/client/inc/nickhandles.png

	install -D -m 0640 client/inc/img_alt.php	$(APP_INST_DIR)/client/inc/img_alt.php
	install -D -m 0640 client/inc/img_alt_skin.php	$(APP_INST_DIR)/client/inc/img_alt_skin.php
	install -D -m 0640 client/inc/img.php		$(APP_INST_DIR)/client/inc/img.php

	### email panel ###
	install -D -m 0640 email/api.php			$(APP_INST_DIR)/email/api.php
	install -D -m 0640 email/index.php			$(APP_INST_DIR)/email/index.php
	install -D -m 0640 email/login.php			$(APP_INST_DIR)/email/login.php
	install -D -m 0640 email/submit_to_sql_dtcemail.php	$(APP_INST_DIR)/email/submit_to_sql_dtcemail.php
	install -D -m 0640 admin/inc/img_alt.php		$(APP_INST_DIR)/email/inc/img_alt.php
	install -D -m 0640 admin/inc/img_alt_skin.php		$(APP_INST_DIR)/email/inc/img_alt_skin.php
	install -D -m 0640 admin/inc/img.php			$(APP_INST_DIR)/email/inc/img.php
	install -D -m 0640 email/inc/domain.png			$(APP_INST_DIR)/email/inc/domain.png
	install -D -m 0640 email/inc/domains.png		$(APP_INST_DIR)/email/inc/domains.png

	### the shared folder ###
	install -D -m 0640 doc/dtc-chroot-shell.8		$(MAN_DIR)/man8/dtc-chroot-shell.8
	install -D -m 0640 shared/autoSQLconfig.php		$(APP_INST_DIR)/shared/autoSQLconfig.php
	install -D -m 0640 shared/cyradm.php			$(APP_INST_DIR)/shared/cyradm.php
	install -D -m 0640 shared/default_admin_site.php	$(APP_INST_DIR)/shared/default_admin_site.php
	install -D -m 0640 shared/dtc_lib.php			$(APP_INST_DIR)/shared/dtc_lib.php
	install -D -m 0640 shared/dtc_stats_index.php		$(APP_INST_DIR)/shared/dtc_stats_index.php

	install -D -m 0640 shared/404_template/404.php		$(APP_INST_DIR)/shared/404_template/404.php
	install -D -m 0640 shared/404_template/expired.php	$(APP_INST_DIR)/shared/404_template/expired.php
	install -D -m 0640 shared/404_template/index.php	$(APP_INST_DIR)/shared/404_template/index.php

	install -D -m 0640 shared/drawlib/anotherDtc.php	$(APP_INST_DIR)/shared/drawlib/anotherDtc.php
	install -D -m 0640 shared/drawlib/cc_code_popup.php	$(APP_INST_DIR)/shared/drawlib/cc_code_popup.php
	install -D -m 0640 shared/drawlib/dtc_functions.php	$(APP_INST_DIR)/shared/drawlib/dtc_functions.php
	install -D -m 0640 shared/drawlib/skinLib.php		$(APP_INST_DIR)/shared/drawlib/skinLib.php
	install -D -m 0640 shared/drawlib/skin.php		$(APP_INST_DIR)/shared/drawlib/skin.php
	install -D -m 0640 shared/drawlib/templates.php		$(APP_INST_DIR)/shared/drawlib/templates.php
	install -D -m 0640 shared/drawlib/tree_menu.php		$(APP_INST_DIR)/shared/drawlib/tree_menu.php

	install -D -m 0640 shared/dtcrm/draw_adddomain.php		$(APP_INST_DIR)/shared/dtcrm/draw_adddomain.php
	install -D -m 0640 shared/dtcrm/draw_handle.php			$(APP_INST_DIR)/shared/dtcrm/draw_handle.php
	install -D -m 0640 shared/dtcrm/draw_nameservers.php		$(APP_INST_DIR)/shared/dtcrm/draw_nameservers.php
	install -D -m 0640 shared/dtcrm/draw.php			$(APP_INST_DIR)/shared/dtcrm/draw.php
	install -D -m 0640 shared/dtcrm/draw_register_forms.php		$(APP_INST_DIR)/shared/dtcrm/draw_register_forms.php
	install -D -m 0640 shared/dtcrm/draw_transferdomain.php		$(APP_INST_DIR)/shared/dtcrm/draw_transferdomain.php
	install -D -m 0640 shared/dtcrm/draw_whois.php			$(APP_INST_DIR)/shared/dtcrm/draw_whois.php
	install -D -m 0640 shared/dtcrm/opensrs.php			$(APP_INST_DIR)/shared/dtcrm/opensrs.php
	install -D -m 0640 shared/dtcrm/registry_calls.php		$(APP_INST_DIR)/shared/dtcrm/registry_calls.php
	install -D -m 0640 shared/dtcrm/RENAME_ME_srs_config.php	$(APP_INST_DIR)/shared/dtcrm/RENAME_ME_srs_config.php
	install -D -m 0640 shared/dtcrm/srs_base.php			$(APP_INST_DIR)/shared/dtcrm/srs_base.php
	install -D -m 0640 shared/dtcrm/srs_nameserver.php		$(APP_INST_DIR)/shared/dtcrm/srs_nameserver.php
	install -D -m 0640 shared/dtcrm/srs_registernames.php		$(APP_INST_DIR)/shared/dtcrm/srs_registernames.php
	install -D -m 0640 shared/dtcrm/strings.php			$(APP_INST_DIR)/shared/dtcrm/strings.php
	install -D -m 0640 shared/dtcrm/submit_to_sql.php		$(APP_INST_DIR)/shared/dtcrm/submit_to_sql.php
	install -D -m 0640 shared/dtcrm/todo				$(APP_INST_DIR)/shared/dtcrm/todo

	install -D -m 0640 shared/dtcrm/srs/CHANGELOG		$(APP_INST_DIR)/shared/dtcrm/srs/CHANGELOG
	install -D -m 0640 shared/dtcrm/srs/country_codes.php	$(APP_INST_DIR)/shared/dtcrm/srs/country_codes.php
	install -D -m 0640 shared/dtcrm/srs/openSRS_base.php	$(APP_INST_DIR)/shared/dtcrm/srs/openSRS_base.php
	install -D -m 0640 shared/dtcrm/srs/openSRS.php		$(APP_INST_DIR)/shared/dtcrm/srs/openSRS.php
	install -D -m 0640 shared/dtcrm/srs/ops.dtd		$(APP_INST_DIR)/shared/dtcrm/srs/ops.dtd
	install -D -m 0640 shared/dtcrm/srs/OPS.php		$(APP_INST_DIR)/shared/dtcrm/srs/OPS.php
	install -D -m 0640 shared/dtcrm/srs/readme		$(APP_INST_DIR)/shared/dtcrm/srs/readme
	install -D -m 0640 shared/dtcrm/srs/test.php		$(APP_INST_DIR)/shared/dtcrm/srs/test.php
	install -D -m 0640 shared/dtcrm/srs/test.xml		$(APP_INST_DIR)/shared/dtcrm/srs/test.xml
	install -D -m 0640 shared/dtcrm/srs/todo		$(APP_INST_DIR)/shared/dtcrm/srs/todo

	install -D -m 0640 shared/dtcrm/webnic.cc/domainQuery.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc/domainQuery.php
	install -D -m 0640 shared/dtcrm/webnic.cc/domainRegistration.php	$(APP_INST_DIR)/shared/dtcrm/webnic.cc/domainRegistration.php
	install -D -m 0640 shared/dtcrm/webnic.cc/test.php			$(APP_INST_DIR)/shared/dtcrm/webnic.cc/test.php
	install -D -m 0640 shared/dtcrm/webnic.cc/webnic_base.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc/webnic_base.php
	install -D -m 0640 shared/dtcrm/webnic.cc/webnic_settings.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc/webnic_settings.php
	install -D -m 0640 shared/dtcrm/webnic.cc/webnic_submit.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc/webnic_submit.php

	# Copy all the graphics...
	cp -rf shared/gfx	$(APP_INST_DIR)/shared
	[ -h $(APP_INST_DIR)/admin/gfx ] || ln -s ../shared/gfx	$(APP_INST_DIR)/admin/gfx
	[ -h $(APP_INST_DIR)/client/gfx ] || ln -s ../shared/gfx	$(APP_INST_DIR)/client/gfx
	[ -h $(APP_INST_DIR)/email/gfx ] || ln -s ../shared/gfx	$(APP_INST_DIR)/email/gfx

	install -D -m 0640 shared/inc/accounting.php		$(APP_INST_DIR)/shared/inc/accounting.php
	install -D -m 0640 shared/inc/dbconect.php		$(APP_INST_DIR)/shared/inc/dbconect.php
	install -D -m 0640 shared/inc/delete_user.php		$(APP_INST_DIR)/shared/inc/delete_user.php
	install -D -m 0640 shared/inc/domain_export.php		$(APP_INST_DIR)/shared/inc/domain_export.php
	install -D -m 0640 shared/inc/draw.php			$(APP_INST_DIR)/shared/inc/draw.php
	install -D -m 0640 shared/inc/fetchmail.php		$(APP_INST_DIR)/shared/inc/fetchmail.php
	install -D -m 0640 shared/inc/fetch.php			$(APP_INST_DIR)/shared/inc/fetch.php
	install -D -m 0640 shared/inc/nusoap.php		$(APP_INST_DIR)/shared/inc/nusoap.php
	install -D -m 0640 shared/inc/skin.class.php		$(APP_INST_DIR)/shared/inc/skin.class.php
	install -D -m 0640 shared/inc/submit_to_sql.php		$(APP_INST_DIR)/shared/inc/submit_to_sql.php
	install -D -m 0640 shared/inc/tree_mem_to_db.php	$(APP_INST_DIR)/shared/inc/tree_mem_to_db.php
	install -D -m 0640 shared/inc/vps.php			$(APP_INST_DIR)/shared/inc/vps.php
	install -D -m 0640 shared/inc/forms/admin_stats.php		$(APP_INST_DIR)/shared/inc/forms/admin_stats.php
	install -D -m 0640 shared/inc/forms/aliases.php			$(APP_INST_DIR)/shared/inc/forms/aliases.php
	install -D -m 0640 shared/inc/forms/database.php		$(APP_INST_DIR)/shared/inc/forms/database.php
	install -D -m 0640 shared/inc/forms/dedicated.php		$(APP_INST_DIR)/shared/inc/forms/dedicated.php
	install -D -m 0640 shared/inc/forms/dedicated_strings.php	$(APP_INST_DIR)/shared/inc/forms/dedicated_strings.php
	install -D -m 0640 shared/inc/forms/dns.php			$(APP_INST_DIR)/shared/inc/forms/dns.php
	install -D -m 0640 shared/inc/forms/domain_info.php		$(APP_INST_DIR)/shared/inc/forms/domain_info.php
	install -D -m 0640 shared/inc/forms/domain_stats.php		$(APP_INST_DIR)/shared/inc/forms/domain_stats.php
	install -D -m 0640 shared/inc/forms/email.php			$(APP_INST_DIR)/shared/inc/forms/email.php
	install -D -m 0640 shared/inc/forms/ftp.php			$(APP_INST_DIR)/shared/inc/forms/ftp.php
	install -D -m 0640 shared/inc/forms/invoices.php		$(APP_INST_DIR)/shared/inc/forms/invoices.php
	install -D -m 0640 shared/inc/forms/lists.php			$(APP_INST_DIR)/shared/inc/forms/lists.php
	install -D -m 0640 shared/inc/forms/lists_strings.php		$(APP_INST_DIR)/shared/inc/forms/lists_strings.php
	install -D -m 0640 shared/inc/forms/my_account.php		$(APP_INST_DIR)/shared/inc/forms/my_account.php
	install -D -m 0640 shared/inc/forms/packager.php		$(APP_INST_DIR)/shared/inc/forms/packager.php
	install -D -m 0640 shared/inc/forms/reseller.php		$(APP_INST_DIR)/shared/inc/forms/reseller.php
	install -D -m 0640 shared/inc/forms/root_admin.php		$(APP_INST_DIR)/shared/inc/forms/root_admin.php
	install -D -m 0640 shared/inc/forms/root_admin_strings.php	$(APP_INST_DIR)/shared/inc/forms/root_admin_strings.php
	install -D -m 0640 shared/inc/forms/ssh.php			$(APP_INST_DIR)/shared/inc/forms/ssh.php
	install -D -m 0640 shared/inc/forms/subdomain.php		$(APP_INST_DIR)/shared/inc/forms/subdomain.php
	install -D -m 0640 shared/inc/forms/ticket.php			$(APP_INST_DIR)/shared/inc/forms/ticket.php
	install -D -m 0640 shared/inc/forms/ticket_strings.php		$(APP_INST_DIR)/shared/inc/forms/ticket_strings.php
	install -D -m 0640 shared/inc/forms/tools.php			$(APP_INST_DIR)/shared/inc/forms/tools.php
	install -D -m 0640 shared/inc/forms/vps.php			$(APP_INST_DIR)/shared/inc/forms/vps.php
	install -D -m 0640 shared/inc/forms/vps_strings.php		$(APP_INST_DIR)/shared/inc/forms/vps_strings.php
	install -D -m 0640 shared/inc/sql/database.php			$(APP_INST_DIR)/shared/inc/sql/database.php
	install -D -m 0640 shared/inc/sql/database_strings.php		$(APP_INST_DIR)/shared/inc/sql/database_strings.php
	install -D -m 0640 shared/inc/sql/dns.php			$(APP_INST_DIR)/shared/inc/sql/dns.php
	install -D -m 0640 shared/inc/sql/domain_info.php		$(APP_INST_DIR)/shared/inc/sql/domain_info.php
	install -D -m 0640 shared/inc/sql/domain_info_strings.php	$(APP_INST_DIR)/shared/inc/sql/domain_info_strings.php
	install -D -m 0640 shared/inc/sql/domain_stats.php		$(APP_INST_DIR)/shared/inc/sql/domain_stats.php
	install -D -m 0640 shared/inc/sql/email.php			$(APP_INST_DIR)/shared/inc/sql/email.php
	install -D -m 0640 shared/inc/sql/email_strings.php		$(APP_INST_DIR)/shared/inc/sql/email_strings.php
	install -D -m 0640 shared/inc/sql/ftp.php			$(APP_INST_DIR)/shared/inc/sql/ftp.php
	install -D -m 0640 shared/inc/sql/ftp_strings.php		$(APP_INST_DIR)/shared/inc/sql/ftp_strings.php
	install -D -m 0640 shared/inc/sql/lists.php			$(APP_INST_DIR)/shared/inc/sql/lists.php
	install -D -m 0640 shared/inc/sql/reseller.php			$(APP_INST_DIR)/shared/inc/sql/reseller.php
	install -D -m 0640 shared/inc/sql/ssh.php			$(APP_INST_DIR)/shared/inc/sql/ssh.php
	install -D -m 0640 shared/inc/sql/ssh_strings.php		$(APP_INST_DIR)/shared/inc/sql/ssh_strings.php
	install -D -m 0640 shared/inc/sql/subdomain.php			$(APP_INST_DIR)/shared/inc/sql/subdomain.php
	install -D -m 0640 shared/inc/sql/subdomain_strings.php		$(APP_INST_DIR)/shared/inc/sql/subdomain_strings.php
	install -D -m 0640 shared/inc/sql/ticket.php			$(APP_INST_DIR)/shared/inc/sql/ticket.php
	install -D -m 0640 shared/inc/sql/vps.php			$(APP_INST_DIR)/shared/inc/sql/vps.php
	install -D -m 0640 shared/inc/sql/vps_strings.php		$(APP_INST_DIR)/shared/inc/sql/vps_strings.php

	install -D -m 0640 shared/maxmind/Changes			$(APP_INST_DIR)/shared/maxmind/Changes
	install -D -m 0640 shared/maxmind/copyright			$(APP_INST_DIR)/shared/maxmind/copyright
	install -D -m 0640 shared/maxmind/CreditCardFraudDetection.php	$(APP_INST_DIR)/shared/maxmind/CreditCardFraudDetection.php
	install -D -m 0640 shared/maxmind/HTTPBase.php			$(APP_INST_DIR)/shared/maxmind/HTTPBase.php
	install -D -m 0640 shared/maxmind/LocationVerification.php	$(APP_INST_DIR)/shared/maxmind/LocationVerification.php
	install -D -m 0640 shared/maxmind/README			$(APP_INST_DIR)/shared/maxmind/README
	install -D -m 0640 shared/maxmind/TelephoneVerification.php	$(APP_INST_DIR)/shared/maxmind/TelephoneVerification.php

	install -D -m 0640 shared/securepay/paiement_config.php		$(APP_INST_DIR)/shared/securepay/paiement_config.php
	install -D -m 0640 shared/securepay/paiement.php		$(APP_INST_DIR)/shared/securepay/paiement.php
	install -D -m 0640 shared/securepay/pay_functions.php		$(APP_INST_DIR)/shared/securepay/pay_functions.php
	install -D -m 0640 shared/securepay/gateways/enets.php		$(APP_INST_DIR)/shared/securepay/gateways/enets.php
	install -D -m 0640 shared/securepay/gateways/paypal.php		$(APP_INST_DIR)/shared/securepay/gateways/paypal.php
	install -D -m 0640 shared/securepay/gateways/worldpay.php	$(APP_INST_DIR)/shared/securepay/gateways/worldpay.php

	install -D -m 0640 shared/template/dtc_logo.gif		$(APP_INST_DIR)/shared/template/dtc_logo.gif
	install -D -m 0640 shared/template/dtclogo.png		$(APP_INST_DIR)/shared/template/dtclogo.png
	install -D -m 0640 shared/template/favicon.ico		$(APP_INST_DIR)/shared/template/favicon.ico
	install -D -m 0640 shared/template/index.php		$(APP_INST_DIR)/shared/template/index.php
	install -D -m 0640 shared/template/logo_dtc.gif		$(APP_INST_DIR)/shared/template/logo_dtc.gif

	install -D -m 0640 shared/vars/clear_lang_array.php	$(APP_INST_DIR)/shared/vars/clear_lang_array.php
	install -D -m 0640 shared/vars/error_strings.php	$(APP_INST_DIR)/shared/vars/error_strings.php
	install -D -m 0640 shared/vars/global_vars.php		$(APP_INST_DIR)/shared/vars/global_vars.php
	install -D -m 0640 shared/vars/lang.php			$(APP_INST_DIR)/shared/vars/lang.php
	install -D -m 0640 shared/vars/strings.php		$(APP_INST_DIR)/shared/vars/strings.php
	install -D -m 0640 shared/vars/table_names.php		$(APP_INST_DIR)/shared/vars/table_names.php

	install -D -m 0640 shared/visitors_template/visitors.php	$(APP_INST_DIR)/shared/visitors_template/visitors.php

	mkdir -p $(APP_INST_DIR)/shared/imgcache
	[ -h $(APP_INST_DIR)/admin/imgcache ] || ln -s ../shared/imgcache $(APP_INST_DIR)/admin/imgcache
	[ -h $(APP_INST_DIR)/client/imgcache ] || ln -s ../shared/imgcache $(APP_INST_DIR)/client/imgcache
	[ -h $(APP_INST_DIR)/email/imgcache ] || ln -s ../shared/imgcache $(APP_INST_DIR)/email/imgcache

	# Create the variables directory
	mkdir -p $(GENFILES_DIRECTORY)/etc/zones
	chmod 755 $(GENFILES_DIRECTORY)/etc/zones
	mkdir -p $(GENFILES_DIRECTORY)/etc/slave_zones
	chmod 755 $(GENFILES_DIRECTORY)/etc/slave_zones

	# Create the configuration folder
	mkdir -p $(ETC_DIRECTORY)
	cp -rf admin/reminders_msg $(ETC_DIRECTORY)
	cp shared/messages_header.txt $(ETC_DIRECTORY)
	install -D -m 0640 shared/registration_msg/dedicated_open.txt	$(ETC_DIRECTORY)/registration_msg/dedicated_open.txt
	install -D -m 0640 shared/registration_msg/shared_open.txt	$(ETC_DIRECTORY)/registration_msg/shared_open.txt
	install -D -m 0640 shared/registration_msg/vps_open.txt		$(ETC_DIRECTORY)/registration_msg/vps_open.txt
	install -D -m 0644 admin/signature.txt 				$(ETC_DIRECTORY)/signature.txt
	install -D -m 0644 etc/logrotate.template			$(ETC_DIRECTORY)/logrotate.template

	# Doc dir
	mkdir -p $(DOC_DIR)
	[ -h $(APP_INST_DIR)/doc ] || ln -s $(DOC_DIR) $(APP_INST_DIR)/doc
	cp -rf doc/* $(DOC_DIR)
