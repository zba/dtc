# Makefile for dtc-common

# /usr/share/dtc
APP_INST_DIR = $(DESTDIR)/$(DTC_APP_DIRECTORY)/dtc
# /var/lib/dtc/etc
GENFILES_DIRECTORY = $(DESTDIR)/$(DTC_GEN_DIR)
# /etc/dtc
ETC_DIRECTORY = $(DESTDIR)/etc/dtc
# /usr/share/doc/dtc
DOC_DIR = $(DESTDIR)/$(DTC_DOC_DIR)
# /usr/share/man
MAN_DIR = $(DESTDIR)/usr/share/man

INSTALL = install

installstatsdaemon:
	install -D -m 0644 admin/dtc-stats-daemon.php $(APP_INST_DIR)/admin

install:
	### admin dir ###
	# PHP scripts files served by web server
	install -D -m 0644 admin/favicon.ico			$(APP_INST_DIR)/admin
	install -D -m 0644 admin/404.php			$(APP_INST_DIR)/admin
	install -D -m 0644 admin/awstats.dtc.conf		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/bw_per_month.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/index.php			$(APP_INST_DIR)/admin
	install -D -m 0644 admin/cpugraph.php			$(APP_INST_DIR)/admin
	install -D -m 0644 admin/mailgraph.php			$(APP_INST_DIR)/admin
	install -D -m 0644 admin/deamons_state.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/view_waitingusers.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/memgraph.php			$(APP_INST_DIR)/admin
	install -D -m 0644 admin/netusegraph.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/vps_stats_cpu.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/vps_stats_hdd.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/vps_stats_network.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/vps_stats_swap.php		$(APP_INST_DIR)/admin
	install -D -m 0644 admin/patch_saslatuhd_startup	$(APP_INST_DIR)/admin

	# Management scripts that are executed
	install -D -m 0764 admin/cron.php		$(APP_INST_DIR)/admin
	install -D -m 0764 admin/accesslog.php		$(APP_INST_DIR)/admin
	install -D -m 0764 admin/maint_apache.php	$(APP_INST_DIR)/admin
	install -D -m 0764 admin/reminders.php		$(APP_INST_DIR)/admin

	install -D -m 0764 admin/rrdtool.sh		$(APP_INST_DIR)/admin
	install -D -m 0764 admin/checkbind.sh		$(APP_INST_DIR)/admin
	install -D -m 0764 admin/updateChroot.sh	$(APP_INST_DIR)/admin
	install -D -m 0764 admin/ip_change.sh		$(APP_INST_DIR)/admin
	install -D -m 0764 admin/dtc-chroot-shell	$(APP_INST_DIR)/admin
	install -D -m 0764 admin/sa-wrapper		$(APP_INST_DIR)/admin

	# sh scripts for rrdtool stuffs
	install -D -m 0764 admin/queuegraph/count_postfix.sh	$(APP_INST_DIR)/admin/queuegraph
	install -D -m 0764 admin/queuegraph/count_qmail.sh	$(APP_INST_DIR)/admin/queuegraph
	install -D -m 0764 admin/queuegraph/createrrd.sh	$(APP_INST_DIR)/admin/queuegraph
	install -D -m 0764 admin/cpugraph/createrrd.sh		$(APP_INST_DIR)/admin/cpugraph
	install -D -m 0764 admin/cpugraph/get_cpu_load.sh	$(APP_INST_DIR)/admin/cpugraph
	install -D -m 0764 admin/memgraph/createrrd.sh		$(APP_INST_DIR)/admin/memgraph
	install -D -m 0764 admin/memgraph/get_meminfo.sh	$(APP_INST_DIR)/admin/memgraph
	install -D -m 0764 admin/netusegraph/createrrd.sh	$(APP_INST_DIR)/admin/netusegraph
	install -D -m 0764 admin/netusegraph/get_net_usage.sh	$(APP_INST_DIR)/admin/netusegraph

	# genfiles PHP and SH scripts
	install -D -m 0640 admin/genfiles/gen_awstats.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_postfix_email_account.php		$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_perso_vhost.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_postfix_email_account.php		$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_backup_script.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_pro_vhost.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_qmail_email_account.php		$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_email_account.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/genfiles.php				$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_ssh_account.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_maildrop_userdb.php		$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_webalizer_stat.php		$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_named_files_alt-wildcard.php	$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/remote_mail_list.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_named_files.php			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/mailfilter_vacation_template		$(APP_INST_DIR)/admin/genfiles
	install -D -m 0640 admin/genfiles/gen_pro_vhost_alt-wildcard.php	$(APP_INST_DIR)/admin/genfiles
	install -D -m 0744 admin/genfiles/change_debconf_domain.sh		$(APP_INST_DIR)/admin/genfiles
	install -D -m 0744 admin/genfiles/change_debconf_ip.sh			$(APP_INST_DIR)/admin/genfiles
	install -D -m 0744 admin/genfiles/gen_customer_ssl_cert.sh		$(APP_INST_DIR)/admin/genfiles

	# inc PHP scripts
	install -D -m 0640 admin/inc/img_alt_skin.php			$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/img.php				$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/renewals.php			$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/renewals_strings.php		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/draw_user_admin.php		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/draw_user_admin_strings.php	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/dtc_config.php			$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/dtc_config_strings.php		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/monitor.php			$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/submit_root_querys.php		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/graphs.php				$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/nav.php				$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/img_alt.php			$(APP_INST_DIR)/admin/inc
	# inc png files
	install -D -m 0640 admin/inc/adddomain.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/package-installer.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/databases.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/imglong.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/password.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/dedic-server.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/domains.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/imgshort.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/mailaliasgroup.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/reseller.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/mailboxs.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/ssh-accounts.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/mailing-lists.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/stats.png		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/subdomains.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/folder.png		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/ftp-accounts.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/my-account.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/ticket.png		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/nameservers.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/tools.png		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/help.png		$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/virtual-server.png	$(APP_INST_DIR)/admin/inc
	install -D -m 0640 admin/inc/nickhandles.png	$(APP_INST_DIR)/admin/inc

	# install sh scripts
	install -D -m 0640 admin/install/mk_root_mailbox.php	$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/bsd_config		$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/gentoo_config		$(APP_INST_DIR)/admin/install

	install -D -m 0744 admin/install/slack_config		$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/debian_config		$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/install		$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/osx_config		$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/uninstall		$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/functions		$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/interactive_installer	$(APP_INST_DIR)/admin/install
	install -D -m 0744 admin/install/redhat_config		$(APP_INST_DIR)/admin/install

	# The SQL table scripts
	install -D -m 0644 admin/tables/admin.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/backup.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/clients.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/commande.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/companies.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/completedorders.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/config.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/cron_job.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/dedicated.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/domain.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/email_accouting.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/fetchmail.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/freeradius.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ftp_access.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ftp_accounting.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ftp_logs.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/groups.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/handle.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/http_accounting.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/invoicing.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ip_port_service.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/mailaliasgroup.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/mailinglist.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/nameservers.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/new_admin.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/paiement.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/pending_queries.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/pending_renewal.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/pop_access.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/product.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/scheduled_updates.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/secpayconf.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/smtp_logs.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ssh_access.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ssh_groups.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ssh_user_group.sql	$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/ssl_ips.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/subdomain.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/tik_admins.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/tik_cats.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/tik_queries.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/vps_ip.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/vps_server.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/vps.sql			$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/vps_stats.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/whitelist.sql		$(APP_INST_DIR)/admin/install
	install -D -m 0644 admin/tables/whois.sql		$(APP_INST_DIR)/admin/install

	# The database upgrade scripts
	install -D -m 0644 bin/sources/dtc_db.php	$(APP_INST_DIR)/admin
	install -D -m 0644 bin/sources/restor_db.php	$(APP_INST_DIR)/admin

	# dtcrm php scripts
	install -D -m 0644 admin/dtcrm/main.php				$(APP_INST_DIR)/admin/dtcrm
	install -D -m 0644 admin/dtcrm/product_manager.php		$(APP_INST_DIR)/admin/dtcrm
	install -D -m 0644 admin/dtcrm/product_manager_strings.php	$(APP_INST_DIR)/admin/dtcrm
	install -D -m 0644 admin/dtcrm/submit_to_sql.php		$(APP_INST_DIR)/admin/dtcrm

	### client files ###
	install -D -m 0644 client/bw_per_month.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/dynip.php			$(APP_INST_DIR)/client
	install -D -m 0644 client/enets-notify.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/img2.php			$(APP_INST_DIR)/client
	install -D -m 0644 client/index.php			$(APP_INST_DIR)/client
	install -D -m 0644 client/invoice.php			$(APP_INST_DIR)/client
	install -D -m 0644 client/list_domains.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/login.php			$(APP_INST_DIR)/client
	install -D -m 0644 client/new_account_form.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/new_account.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/new_account_renewal.php	$(APP_INST_DIR)/client
	install -D -m 0644 client/paypal.php			$(APP_INST_DIR)/client
	install -D -m 0644 client/secpaycallback_worldpay.php	$(APP_INST_DIR)/client
	install -D -m 0644 client/vps_stats_cpu.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/vps_stats_hdd.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/vps_stats_network.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/vps_stats_swap.php		$(APP_INST_DIR)/client
	install -D -m 0644 client/enets_pay_icon.gif		$(APP_INST_DIR)/client
	install -D -m 0644 client/favicon.ico			$(APP_INST_DIR)/client

	# inc png files
	install -D -m 0640 admin/inc/adddomain.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/package-installer.png	$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/databases.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/imglong.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/password.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/dedic-server.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/domains.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/imgshort.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/mailaliasgroup.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/reseller.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/mailboxs.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/ssh-accounts.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/mailing-lists.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/stats.png			$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/subdomains.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/folder.png			$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/ftp-accounts.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/my-account.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/ticket.png			$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/nameservers.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/tools.png			$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/help.png			$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/virtual-server.png		$(APP_INST_DIR)/client/inc
	install -D -m 0640 admin/inc/nickhandles.png		$(APP_INST_DIR)/client/inc

	install -D -m 0640 client/inc/img_alt.php	$(APP_INST_DIR)/client/inc
	install -D -m 0640 client/inc/img_alt_skin.php	$(APP_INST_DIR)/client/inc
	install -D -m 0640 client/inc/img.php		$(APP_INST_DIR)/client/inc

	### email panel ###
	install -D -m 0640 email/api.php			$(APP_INST_DIR)/email
	install -D -m 0640 email/index.php			$(APP_INST_DIR)/email
	install -D -m 0640 email/login.php			$(APP_INST_DIR)/email
	install -D -m 0640 email/submit_to_sql_dtcemail.php	$(APP_INST_DIR)/email
	install -D -m 0640 admin/inc/img_alt.php		$(APP_INST_DIR)/email/inc
	install -D -m 0640 admin/inc/img_alt_skin.php		$(APP_INST_DIR)/email/inc
	install -D -m 0640 admin/inc/img.php			$(APP_INST_DIR)/email/inc
	install -D -m 0640 email/inc/domain.png			$(APP_INST_DIR)/email/inc
	install -D -m 0640 email/inc/domains.png		$(APP_INST_DIR)/email/inc

	### the shared folder ###
	mkdir -p $(APP_INST_DIR)/imgcache
	ln -s ../shared/imgcache $(APP_INST_DIR)/admin/imgcache
	ln -s ../shared/imgcache $(APP_INST_DIR)/client/imgcache
	ln -s ../shared/imgcache $(APP_INST_DIR)/email/imgcache
	ln -s ../shared/gfx $(APP_INST_DIR)/admin/gfx
	ln -s ../shared/gfx $(APP_INST_DIR)/client/gfx
	ln -s ../shared/gfx $(APP_INST_DIR)/email/gfx
	install -D -m 0640 doc/dtc-chroot-shell.8 $(MAN_DIR)/man8
	install -D -m 0640 shared/autoSQLconfig.php		$(APP_INST_DIR)/shared
	install -D -m 0640 shared/cyradm.php			$(APP_INST_DIR)/shared
	install -D -m 0640 shared/default_admin_site.php	$(APP_INST_DIR)/shared
	install -D -m 0640 shared/dtc_lib.php			$(APP_INST_DIR)/shared
	install -D -m 0640 shared/dtc_stats_index.php		$(APP_INST_DIR)/shared

	install -D -m 0640 shared/404_template/404.php		$(APP_INST_DIR)/shared/404_template
	install -D -m 0640 shared/404_template/expired.php	$(APP_INST_DIR)/shared/404_template
	install -D -m 0640 shared/404_template/index.php	$(APP_INST_DIR)/shared/404_template

	install -D -m 0640 shared/drawlib/anotherDtc.php	$(APP_INST_DIR)/shared/drawlib
	install -D -m 0640 shared/drawlib/cc_code_popup.php	$(APP_INST_DIR)/shared/drawlib
	install -D -m 0640 shared/drawlib/dtc_functions.php	$(APP_INST_DIR)/shared/drawlib
	install -D -m 0640 shared/drawlib/skinLib.php		$(APP_INST_DIR)/shared/drawlib
	install -D -m 0640 shared/drawlib/skin.php		$(APP_INST_DIR)/shared/drawlib
	install -D -m 0640 shared/drawlib/templates.php		$(APP_INST_DIR)/shared/drawlib
	install -D -m 0640 shared/drawlib/tree_menu.php		$(APP_INST_DIR)/shared/drawlib

	install -D -m 0640 shared/dtcrm/ $(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/draw_adddomain.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/draw_handle.php			$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/draw_nameservers.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/draw.php			$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/draw_register_forms.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/draw_transferdomain.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/draw_whois.php			$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/opensrs.php			$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/registry_calls.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/RENAME_ME_srs_config.php	$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/srs_base.php			$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/srs_nameserver.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/srs_registernames.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/strings.php			$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/submit_to_sql.php		$(APP_INST_DIR)/shared/dtcrm
	install -D -m 0640 shared/dtcrm/todo				$(APP_INST_DIR)/shared/dtcrm

	install -D -m 0640 shared/dtcrm/srs/CHANGELOG		$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/country_codes.php	$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/openSRS_base.php	$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/openSRS.php		$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/ops.dtd		$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/OPS.php		$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/readme		$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/test.php		$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/test.xml		$(APP_INST_DIR)/shared/dtcrm/srs
	install -D -m 0640 shared/dtcrm/srs/todo		$(APP_INST_DIR)/shared/dtcrm/srs

	install -D -m 0640 shared/dtcrm/webnic.cc/domainQuery.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc
	install -D -m 0640 shared/dtcrm/webnic.cc/domainRegistration.php	$(APP_INST_DIR)/shared/dtcrm/webnic.cc
	install -D -m 0640 shared/dtcrm/webnic.cc/test.php			$(APP_INST_DIR)/shared/dtcrm/webnic.cc
	install -D -m 0640 shared/dtcrm/webnic.cc/webnic_base.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc
	install -D -m 0640 shared/dtcrm/webnic.cc/webnic_settings.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc
	install -D -m 0640 shared/dtcrm/webnic.cc/webnic_submit.php		$(APP_INST_DIR)/shared/dtcrm/webnic.cc

	# Copy all the graphics...
	cp -rf shared/gfx	$(APP_INST_DIR)/shared
	ln -s ../shared/gfx	$(APP_INST_DIR)/admin/gfx
	ln -s ../shared/gfx	$(APP_INST_DIR)/client/gfx
	ln -s ../shared/gfx	$(APP_INST_DIR)/email/gfx

	install -D -m 0640 shared/inc/accounting.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/dbconect.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/delete_user.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/domain_export.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/draw.php			$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/fetchmail.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/fetch.php			$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/nusoap.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/skin.class.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/submit_to_sql.php		$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/tree_mem_to_db.php	$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/vps.php			$(APP_INST_DIR)/shared/inc
	install -D -m 0640 shared/inc/forms/admin_stats.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/aliases.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/database.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/dedicated.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/dedicated_strings.php	$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/dns.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/domain_info.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/domain_stats.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/email.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/ftp.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/invoices.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/lists.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/lists_strings.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/my_account.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/packager.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/reseller.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/root_admin.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/root_admin_strings.php	$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/ssh.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/subdomain.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/ticket.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/ticket_strings.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/tools.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/vps.php			$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/forms/vps_strings.php		$(APP_INST_DIR)/shared/inc/forms
	install -D -m 0640 shared/inc/sql/database.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/database_strings.php		$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/dns.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/domain_info.php		$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/domain_info_strings.php	$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/domain_stats.php		$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/email.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/email_strings.php		$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/ftp.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/ftp_strings.php		$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/lists.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/reseller.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/ssh.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/ssh_strings.php		$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/subdomain.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/subdomain_strings.php		$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/ticket.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/vps.php			$(APP_INST_DIR)/shared/inc/sql
	install -D -m 0640 shared/inc/sql/vps_strings.php		$(APP_INST_DIR)/shared/inc/sql

	install -D -m 0640 shared/maxmind/Changes			$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/copyright			$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/CreditCardFraudDetection.php	$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/Example_locv.php		$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/Example.php			$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/Example_telv.php		$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/HTTPBase.php			$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/LocationVerification.php	$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/README			$(APP_INST_DIR)/shared/maxmind
	install -D -m 0640 shared/maxmind/TelephoneVerification.php	$(APP_INST_DIR)/shared/maxmind

	install -D -m 0640 shared/securepay/paiement_config.php		$(APP_INST_DIR)/shared/securepay
	install -D -m 0640 shared/securepay/paiement.php		$(APP_INST_DIR)/shared/securepay
	install -D -m 0640 shared/securepay/pay_functions.php		$(APP_INST_DIR)/shared/securepay
	install -D -m 0640 shared/securepay/gateways/enets.php		$(APP_INST_DIR)/shared/securepay/gateways
	install -D -m 0640 shared/securepay/gateways/paypal.php		$(APP_INST_DIR)/shared/securepay/gateways
	install -D -m 0640 shared/securepay/gateways/worldpay.php	$(APP_INST_DIR)/shared/securepay/gateways

	install -D -m 0640 shared/template/dtc_logo.gif		$(APP_INST_DIR)/shared/template
	install -D -m 0640 shared/template/dtclogo.png		$(APP_INST_DIR)/shared/template
	install -D -m 0640 shared/template/favicon.ico		$(APP_INST_DIR)/shared/template
	install -D -m 0640 shared/template/index.php		$(APP_INST_DIR)/shared/template
	install -D -m 0640 shared/template/logo_dtc.gif		$(APP_INST_DIR)/shared/template

	install -D -m 0640 shared/vars/clear_lang_array.php	$(APP_INST_DIR)/shared/vars
	install -D -m 0640 shared/vars/error_strings.php	$(APP_INST_DIR)/shared/vars
	install -D -m 0640 shared/vars/global_vars.php		$(APP_INST_DIR)/shared/vars
	install -D -m 0640 shared/vars/lang.php			$(APP_INST_DIR)/shared/vars
	install -D -m 0640 shared/vars/strings.php		$(APP_INST_DIR)/shared/vars
	install -D -m 0640 shared/vars/table_names.php		$(APP_INST_DIR)/shared/vars

	install -D -m 0640 shared/visitors_template/visitors.php	$(APP_INST_DIR)/shared/visitors_template

	# Create the variables directory
	mkdir -p $(GENFILES_DIRECTORY)/etc/zones
	chmod 755 $(GENFILES_DIRECTORY)/etc/zones
	mkdir -p $(GENFILES_DIRECTORY)/etc/slave_zones
	chmod 755 $(GENFILES_DIRECTORY)/etc/slave_zones

	# Create the configuration folder
	mkdir -p $(ETC_DIRECTORY)
	cp -rfv admin/reminders_msg $(ETC_DIRECTORY)
	cp shared/messages_header.txt $(ETC_DIRECTORY)
	install -D -m 0644 admin/signature.txt $(ETC_DIRECTORY)
	install -D -m 0644 etc/logrotate.template $(ETC_DIRECTORY)

	# Doc dir
	mkdir -p $(DOC_DIR)
	ln -s $(DOC_DIR) $(APP_INST_DIR)/doc
	cp -rfv doc/* $(APP_INST_DIR)/doc
