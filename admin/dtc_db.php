<?php
// Automatic database maintenance.
// Please, take care, this file is NOT using the MySQL syntax
// for the fields description, it's a way more restrictive.
// you have to write either:
// 	default NULL
// or as well:
// NOT NULL default 'something'
// but not for blob and text types.
// If you want to have the code that maintains DBs to be more
// permissive, then you should look into restor_db.php and change it.
// Happy hacking!
//	zigo

$dtc_database = array(
"version" => "1.0.0",
"tables" => array(
	"admin" => array(
		"vars" => array(
			"adm_login" => "varchar(64) NOT NULL default ''",
			"adm_pass" => "varchar(255) NOT NULL default ''",
			"path" => "varchar(255) NOT NULL default '/var/www/sites'",
			"max_email" => "int(12) NOT NULL default '3'",
			"max_ftp" => "int(12) NOT NULL default '3'",
			"max_ssh" => "int(12) NOT NULL default '3'",
			"quota" => "int(11) NOT NULL default '50'",
			"bandwidth_per_month_mb" => "int(11) NOT NULL default '100'",
			"id_client" => "int(9) NOT NULL default '0'",
			"expire" => "date NOT NULL default '0000-00-00'",
			"prod_id" => "int(11) NOT NULL default '0'",
			"pass_next_req" => "varchar(128) NOT NULL default '0'",
			"pass_expire" => "int(12) NOT NULL default '0'",
			"allow_add_domain" => "enum('yes','no','check') NOT NULL default 'check'",
			"max_domain" => "int(9) NOT NULL default '0'",
			"shared_hosting_security" => "enum('mod_php','sbox_copy','sbox_aufs') NOT NULL default 'mod_php'",
			"restricted_ftp_path" => "enum('yes','no') NOT NULL default 'no'",
			"allow_dns_and_mx_change" => "enum('yes','no') NOT NULL default 'yes'",
			"allow_mailing_list_edit" => "enum('yes','no') NOT NULL default 'yes'",
			"allow_subdomain_edit" => "enum('yes','no') NOT NULL default 'yes'",
			"allow_cronjob_edit" => "enum('yes','no') NOT NULL default 'yes'",
			"nbrdb" => "int(9) NOT NULL default '1'",
			"resseller_flag" => "enum('yes','no') NOT NULL default 'no'",
			"ssh_login_flag" => "enum('yes','no') NOT NULL default 'no'",
			"ftp_login_flag" => "enum('yes','no') NOT NULL default 'yes'",
			"pkg_install_flag" => "enum('yes','no') NOT NULL default 'yes'",
			"ob_head" => "varchar(64) NOT NULL default ''",
			"ob_tail" => "varchar(64) NOT NULL default ''",
			"ob_next" => "varchar(64) NOT NULL default ''",
			"last_used_lang" => "varchar(32) NOT NULL default 'en_US.UTF-8'",
			"recovery_token" => "varchar(64) NOT NULL default ''",
			"recovery_timestamp" => "int(12) NOT NULL default '0'",
			"max_ssh" => "int(12) NOT NULL default '3'"
		),
		"primary" => "(adm_login)",
		"index" => array(
			"path" => "(path)",
			"id_clientindex" => "(id_client)"
		)
	),
	"affiliate_payments" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"adm_login" => "varchar(64) NOT NULL default ''",
			"order_id" => "int(11) NOT NULL default '0'",
			"kickback" => "decimal(9,2) NOT NULL default '0.00'",
			"date_paid" => "date default NULL",
		),
		"primary" => "(id)"
	),
	"backup" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"server_addr" => "varchar(128) NOT NULL default ''",
			"server_login" => "varchar(128) NOT NULL default ''",
			"server_pass" => "varchar(128) NOT NULL default ''",
			"type" => "enum('grant_access','mail_backup','dns_backup','backup_ftp_to','trigger_changes','trigger_mx_changes') NOT NULL default 'grant_access'",
			"status" => "enum('pending','done') NOT NULL default 'pending'"
		),
		"primary" => "(id)"
	),
	"clients" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"is_company" => "enum('yes','no') NOT NULL default 'no'",
			"company_name" => "varchar(255) NOT NULL default ''",
			"vat_num" => "varchar(128) NOT NULL default ''",
			"familyname" => "varchar(64) NOT NULL default ''",
			"christname" => "varchar(64) NOT NULL default ''",
			"addr1" => "varchar(100) NOT NULL default ''",
			"addr2" => "varchar(100) NOT NULL default ''",
			"addr3" => "varchar(100) NOT NULL default ''",
			"city" => "varchar(64) NOT NULL default ''",
			"zipcode" => "varchar(32) NOT NULL default '0'",
			"state" => "varchar(32) NOT NULL default ''",
			"country" => "char(2) NOT NULL default ''",
			"phone" => "varchar(20) NOT NULL default '0'",
			"fax" => "varchar(20) NOT NULL default ''",
			"email" => "varchar(255) NOT NULL default ''",
			"special_note" => "blob",
			"dollar" => "decimal(9,2) NOT NULL default '0.00'",
			"disk_quota_mb" => "int(9) NOT NULL default '0'",
			"bw_quota_per_month_gb" => "int(9) NOT NULL default '0'",
			"expire" => "date NOT NULL default '0000-00-00'",
			"active" => "enum('yes','no') NOT NULL default 'yes'",
			"customfld" => "text"
		),
		"primary" => "(id)"
	),
	"completedorders" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"id_client" => "int(12) NOT NULL default '0'",
			"domain_name" => "varchar(255) NOT NULL default ''",
			"quantity" => "int(12) NOT NULL default '0'",
			"date" => "date NOT NULL default '0000-00-00'",
			"product_id" => "int(12) NOT NULL default '0'",
			"payment_id" => "int(12) NOT NULL default '0'",
			"download_pass" => "varchar(64) NOT NULL default 'none'",
			"country_code" => "varchar(255) NOT NULL default 'US'",
			"services" => "text",
			"last_expiry_date" => "text"
		),
		"primary" => "(id)"
	),
	"companies" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"name" => "varchar(128) NOT NULL default ''",
			"address" => "text",
			"country" => "varchar(128) NOT NULL default ''",
			"registration_number" => "varchar(128) NOT NULL default ''",
			"vat_number" => "varchar(128) NOT NULL default ''",
			"vat_rate" => "decimal(9,2) NOT NULL default '0.00'",
			"logo_path" => "varchar(255) NOT NULL default 'none'",
			"text_after" => "text",
			"footer" => "text",
			"in_europe" => "enum('yes','no') NOT NULL default 'no'"
		),
		"primary" => "(id)"
	),
	"config" => array(
		"vars" => array(
			"db_version" => "int(11) NOT NULL default '10002'",
			"use_mail_alias_group" => "enum('yes','no') NOT NULL default 'yes'",
			"unicrow" => "int(11) NOT NULL default '1'",
			"demo_version" => "enum('yes','no') NOT NULL default 'no'",
			"main_site_ip" => "varchar(16) NOT NULL default '127.0.0.1'",
			"site_addrs" => "text",
			"use_multiple_ip" => "enum('yes','no') NOT NULL default 'yes'",
			"use_cname_for_subdomains" => "enum('yes','no') NOT NULL default 'no'",
			"addr_mail_server" => "varchar(255) NOT NULL default 'mx.example.com'",
			"webmaster_email_addr" => "varchar(255) NOT NULL default 'postmaster@example.com'",
			"addr_primary_dns" => "varchar(255) NOT NULL default 'ns1.example.com'",
			"addr_secondary_dns" => "varchar(255) NOT NULL default 'ns2.example.com'",
			"ip_slavezone_dns_server" => "varchar(16) NOT NULL default '192.168.0.3'",
			"ip_allowed_dns_transfer" => "varchar(255) NOT NULL default '192.168.0.1'",
			"domainkey_publickey_filepath" => "varchar(255) NOT NULL default '/var/lib/dkimproxy/public.key'",
			"default_zones_ttl" => "int(11) NOT NULL default '7200'",
			"main_domain" => "varchar(128) NOT NULL default 'gplhost.com'",
			"404_subdomain" => "varchar(128) NOT NULL default '404'",
			"administrative_site" => "varchar(255) NOT NULL default 'dtc.example.com'",
			"administrative_ssl_port" => "varchar(16) NOT NULL default '443'",
			"site_root_host_path" => "varchar(255) NOT NULL default '/var/www'",
			"generated_file_path" => "varchar(255) NOT NULL default '/var/lib/dtc/etc'",
			"dtcshared_path" => "varchar(255) NOT NULL default '/usr/share/dtc/shared'",
			"dtcadmin_path" => "varchar(255) NOT NULL default '/usr/share/dtc/admin'",
			"dtcclient_path" => "varchar(255) NOT NULL default '/usr/share/dtc/client'",
			"dtcdoc_path" => "varchar(255) NOT NULL default '/usr/share/dtc/doc'",
			"dtcemail_path" => "varchar(255) NOT NULL default '/usr/share/dtc/email'",
			"htpasswd_path" => "varchar(255) NOT NULL default '/usr/sbin/htpasswd'",
			"qmail_newu_path" => "varchar(255) NOT NULL default '/var/qmail/bin/qmail-newu'",
			"qmail_rcpthost_path" => "varchar(255) NOT NULL default 'rcpthosts'",
			"qmail_virtualdomains_path" => "varchar(255) NOT NULL default 'virtualdomains'",
			"qmail_assign_path" => "varchar(255) NOT NULL default 'assign'",
			"qmail_poppasswd_path" => "varchar(255) NOT NULL default 'poppasswd'",
			"apache_vhost_path" => "varchar(255) NOT NULL default 'vhosts.conf'",
			"php_additional_library_path" => "varchar(255) NOT NULL default '/usr/local/lib/php/phplib'",
			"php_library_path" => "varchar(255) NOT NULL default '/usr/lib/php:/tmp:/var/lib/dtc/etc/dtc404'",
			"named_path" => "varchar(255) NOT NULL default 'named.conf'",
			"named_slavefile_path" => "varchar(255) NOT NULL default 'named.slavezones.conf'",
			"named_slavezonefiles_path" => "varchar(255) NOT NULL default 'slave_zones'",
			"named_zonefiles_path" => "varchar(255) NOT NULL default 'zones'",
			"send_passwords_in_emails" => "enum('yes','no') NOT NULL default 'no'",
			"enforce_adm_encryption" => "enum('yes','no') NOT NULL default 'yes'",
			"autogen_default_subdomains" => "enum('yes','no') NOT NULL default 'yes'",
			"autogen_subdomain_list" => "varchar(255) NOT NULL default 'pop|imap|mail|smtp|ftp'",
			"autogen_webmail_alias" => "enum('yes','no') NOT NULL default 'yes'",
			"autogen_webmail_type" => "enum('squirrelmail','roundcube') NOT NULL default 'squirrelmail'",
			"backup_script_path" => "varchar(255) NOT NULL default 'backup.bash'",
			"bakcup_path" => "varchar(255) NOT NULL default '/mnt/backup'",
			"webalizer_stats_script_path" => "varchar(255) NOT NULL default 'webalizer.bash'",
			"use_javascript" => "enum('yes','no') NOT NULL default 'yes'",
			"use_mail_alias_group" => "enum('yes','no') NOT NULL default 'yes'",
			"use_ssl" => "enum('yes','no') NOT NULL default 'no'",
			"use_shared_ssl" => "enum('yes','no') NOT NULL default 'no'",
			"force_use_https" => "enum('yes','no') NOT NULL default 'no'",
			"use_nated_vhost" => "enum('yes','no') NOT NULL default 'no'",
			"nated_vhost_ip" => "varchar(16) NOT NULL default '192.168.0.2'",
			"addr_backup_mail_server" => "varchar(255) NOT NULL default ''",
			"skin" => "varchar(128) NOT NULL default 'bwoup'",
			"mta_type" => "enum('qmail','postfix') NOT NULL default 'qmail'",
			"domain_based_ftp_logins" => "enum('yes','no') NOT NULL default 'yes'",
			"domain_based_ssh_logins" => "enum('yes','no') NOT NULL default 'yes'",
			"chroot_path" => "varchar(255) NOT NULL default '/var/www/chroot'",
			"hide_password" => "enum('yes','no') NOT NULL default 'yes'",
			"session_expir_minute" => "int(9) NOT NULL default '10'",
			"dns_type" => "enum('bind','djb') NOT NULL default 'bind'",
			"srs_user" => "varchar(128) NOT NULL default ''",
			"srs_live_key" => "varchar(255) NOT NULL default ''",
			"srs_test_key" => "varchar(255) NOT NULL default ''",
			"srs_enviro" => "enum('LIVE','TEST') NOT NULL default 'TEST'",
			"srs_crypt" => "enum('DES','BLOWFISH') NOT NULL default 'DES'",
			"use_registrar_api" => "enum('yes','no') NOT NULL default 'no'",
			"ftp_backup_host" => "varchar(255) NOT NULL default ''",
			"ftp_backup_login" => "varchar(255) NOT NULL default ''",
			"ftp_backup_pass" => "varchar(255) NOT NULL default ''",
			"ftp_backup_frequency" => "enum('day','week','month') NOT NULL default 'week'",
			"ftp_backup_activate" => "enum('yes','no') NOT NULL default 'no'",
			"ftp_backup_dest_folder" => "varchar(255) NOT NULL default '/'",
			"ftp_active_mode" => "enum('yes','no') NOT NULL default 'no'",
			"vps_renewal_before" => "varchar(64) NOT NULL default '5|10'",
			"vps_renewal_after" => "varchar(64) NOT NULL default '3|7'",
			"vps_renewal_lastwarning" => "varchar(64) NOT NULL default '12'",
			"vps_renewal_shutdown" => "varchar(64) NOT NULL default '15'",
			"shared_renewal_before" => "varchar(64) NOT NULL default '40|20|7'",
			"shared_renewal_after" => "varchar(64) NOT NULL default '15|7'",
			"shared_renewal_lastwarning" => "varchar(64) NOT NULL default '25'",
			"shared_renewal_shutdown" => "varchar(64) NOT NULL default '28'",
			"custom_renewal_before" => "varchar(64) NOT NULL default '5|10'",
			"custom_renewal_after" => "varchar(64) NOT NULL default '3|7'",
			"custom_renewal_lastwarning" => "varchar(64) NOT NULL default '12'",
			"custom_renewal_shutdown" => "varchar(64) NOT NULL default '15'",
			"webalizer_country_graph" => "enum('yes','no') NOT NULL default 'no'",
			"apache_version" => "varchar(16) NOT NULL default '1'",
			"dtc_system_uid" => "varchar(16) NOT NULL default 'CONF_DTC_SYSTEM_UID'",
			"dtc_system_username" => "varchar(64) NOT NULL default 'dtc'",
			"dtc_system_gid" => "varchar(16) NOT NULL default 'CONF_DTC_SYSTEM_GID'",
			"dtc_system_groupname" => "varchar(64) NOT NULL default 'nogroup'",
			"selling_conditions_url" => "varchar(255) NOT NULL default 'none'",
			"user_mysql_prepend_admin_name" => "enum('yes','no') NOT NULL default 'no'",
			"user_mysql_type" => "enum('localhost','distant') NOT NULL default 'localhost'",
			"user_mysql_host" => "varchar(255) NOT NULL default 'localhost'",
			"user_mysql_root_login" => "varchar(255) NOT NULL default 'none'",
			"user_mysql_root_pass" => "varchar(255) NOT NULL default 'none'",
			"user_mysql_client" => "varchar(255) NOT NULL default '%'",
			"recipient_delimiter" => "varchar(4) NOT NULL default '+'",
			"default_company_invoicing" => "int(12) NOT NULL default '0'",
			"this_server_country_code" => "varchar(4) NOT NULL default 'US'",
			"use_cyrus" => "enum('yes','no') NOT NULL default 'no'",
			"use_amavis" => "enum('yes','no') NOT NULL default 'yes'",
			"use_clamav" => "enum('yes','no') NOT NULL default 'yes'",
			"use_advanced_lists_tunables" => "enum('yes','no') NOT NULL default 'no'",
			"use_webalizer" => "enum('yes','no') NOT NULL default 'yes'",
			"use_awstats" => "enum('yes','no') NOT NULL default 'no'",
			"use_visitors" => "enum('yes','no') NOT NULL default 'no'",
			"message_subject_header" => "varchar(255) NOT NULL default '[DTC]'",
			"apache_directoryindex" => "varchar(255) NOT NULL default 'index.php index.cgi index.pl index.htm index.html index.php4'",
			"spam_keep_days"=> "int(9) NOT NULL default '20'",
			"named_soa_refresh" => "varchar(16) NOT NULL default '2H'",
			"named_soa_retry" => "varchar(16) NOT NULL default '60M'",
			"named_soa_expire" => "varchar(16) NOT NULL default '1W'",
			"named_soa_default_ttl" => "varchar(16) NOT NULL default '24H'",
			"webnic_server_url" => "varchar(256) NOT NULL default 'https://my.webnic.cc/jsp/'",
			"webnic_username" => "varchar(128) NOT NULL default ''",
			"webnic_password" => "varchar(128) NOT NULL default ''",
			"ovh_server_url" => "varchar(256) NOT NULL default 'https://www.ovh.com/soapi/soapi-re-1.8.wsdl'",
			"ovh_username" => "varchar(128) NOT NULL default ''",
			"ovh_password" => "varchar(128) NOT NULL default ''",
			"ovh_boolean" => "enum('false','true') NOT NULL default 'true'",
			"ovh_language" => "enum('fr','en','es','de','pl','it','pt','nl','cz','ie') NOT NULL default 'en'",
			"ovh_nicadmin" => "varchar(128) NOT NULL default ''",
			"ovh_nictech" => "varchar(128) NOT NULL default ''",
			"ovh_nicowner" => "varchar(128) NOT NULL default ''",
			"ovh_nicbilling" => "varchar(128) NOT NULL default ''",
			"internetbs_server_url" => "varchar(256) NOT NULL default 'https://testapi.internet.bs/'",
			"internetbs_username" => "varchar(128) NOT NULL default ''",
			"internetbs_password" => "varchar(128) NOT NULL default ''",
			"provide_own_domain_hosts" => "enum('yes','no') NOT NULL default 'no'",
			"nagios_host" => "varchar(255) NOT NULL default ''",
			"nagios_username" => "varchar(255) NOT NULL default ''",
			"nagios_config_file_path" => "varchar(255) NOT NULL default ''",
			"nagios_restart_command" => "varchar(255) NOT NULL default 'sudo /etc/init.d/nagios2 restart'",
			"affiliate_return_domain" => "varchar(255) NOT NULL default 'www.example.com'",
			"support_ticket_email" => "varchar(255) NOT NULL default 'support'",
			"support_ticket_domain" => "varchar(255) NOT NULL default 'default'",
			"support_ticket_fw_email" => "varchar(255) NOT NULL default 'supportfw'",
			"all_customers_list_email" => "varchar(255) NOT NULL default 'support'",
			"all_customers_list_domain" => "varchar(255) NOT NULL default 'default'",
			"panel_title" => "varchar(255) NOT NULL default 'default'",
			"panel_subtitle" => "varchar(255) NOT NULL default 'default'",
			"panel_logo" => "varchar(255) NOT NULL default 'default'",
			"this_server_default_tld" => "varchar(10) NOT NULL default '.com'",
			"panel_logolink" => "varchar(255) NOT NULL default 'default'",
			"invoice_scp_addr" => "varchar(255) NOT NULL default ''",
			"invoice_scp_when" => "enum('day','month') NOT NULL default 'day'"
		),
		"keys" => array(
			"unicrow" => "(unicrow)"
		),
		"max_rows" => 1
	),
	"cron_job" => array(
		"vars" => array(
			"unicrow" => "int(11) NOT NULL default '1'",
			"last_cronjob" => "timestamp NOT NULL default '0000-00-00 00:00:00'",
			"qmail_newu" => "enum('yes','no') NOT NULL default 'no'",
			"restart_qmail" => "enum('yes','no') NOT NULL default 'no'",
			"reload_named" => "enum('yes','no') NOT NULL default 'no'",
			"restart_apache" => "enum('yes','no') NOT NULL default 'no'",
			"gen_vhosts" => "enum('yes','no') NOT NULL default 'no'",
			"gen_named" => "enum('yes','no') NOT NULL default 'no'",
			"gen_reverse" => "enum('yes','no') NOT NULL default 'no'",
			"gen_fetchmail" => "enum('yes','no') NOT NULL default 'no'",
			"gen_qmail" => "enum('yes','no') NOT NULL default 'no'",
			"gen_webalizer" => "enum('yes','no') NOT NULL default 'no'",
			"gen_backup" => "enum('yes','no') NOT NULL default 'no'",
			"gen_ssh" => "enum('yes','no') NOT NULL default 'no'",
			"gen_nagios" => "enum('yes','no') NOT NULL default 'no'",
			"gen_user_cron" => "enum('yes','no') NOT NULL default 'no'",
			"lock_flag" => "enum('inprogress','finished') NOT NULL default 'finished'"
		),
		"keys" => array(
			"unicrow" => "(unicrow)"
		),
		"max_rows" => 1
	),
	"custom_fld" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"varname" => "varchar(255) NOT NULL default ''",
			"question" => "varchar(255) NOT NULL default ''",
			"widgettype" => "varchar(255) NOT NULL default ''",
			"widgetvalues" => "varchar(255) NOT NULL default ''",
			"widgetdisplay" => "varchar(255) NOT NULL default ''",
			"widgetorder" => "int(9) NOT NULL default '0'",
			"mandatory" => "enum('yes','no') NOT NULL default 'no'"
		),
		"primary" => "(id)"
	),
		"custom_heb_types" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"name" => "varchar(32) NOT NULL default ''",
			"reqdomain" => "enum('yes','no') NOT NULL default 'no'"
		),
		"primary" => "(id)",
		"keys" => array(
			"name" => "(name)"
		)
	),
	"custom_heb_types_fld" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"varname" => "varchar(255) NOT NULL default ''",
			"question" => "varchar(255) NOT NULL default ''",
			"widgettype" => "varchar(255) NOT NULL default ''",
			"widgetvalues" => "varchar(255) NOT NULL default ''",
			"widgetdisplay" => "varchar(255) NOT NULL default ''",
			"widgetorder" => "int(9) NOT NULL default '0'",
			"custom_heb_type_id" => "int(11) NOT NULL default '0'"
		),
		"primary" => "(id)"
	),
	"custom_product" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"owner" => "varchar(64) NOT NULL default ''",
			"domain" => "varchar(255) NOT NULL default ''",
			"start_date" => "date NOT NULL default '0000-00-00'",
			"expire_date" => "date NOT NULL default '0000-00-00'",
			"product_id" => "int(9) NOT NULL default '0'",
			"custom_heb_type" => "int(11) NOT NULL default '0'",
			"custom_heb_type_fld" => "text",
			"country_code" => "varchar(4) NOT NULL default 'US'"
		),
		"primary" => "(id)"
	),
	"dedicated" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"owner" => "varchar(64) NOT NULL default ''",
			"server_hostname" => "varchar(255) NOT NULL default ''",
			"start_date" => "date NOT NULL default '0000-00-00'",
			"expire_date" => "date NOT NULL default '0000-00-00'",
			"hddsize" => "int(9) NOT NULL default '1'",
			"ramsize" => "int(9) NOT NULL default '48'",
			"bandwidth_per_month_gb" => "int(9) NOT NULL default '10'",
			"product_id" => "int(9) NOT NULL default '0'",
			"operatingsystem" => "varchar(64) NOT NULL default 'debian'",
			"country_code" => "varchar(4) NOT NULL default 'US'"
		),
		"primary" => "(id)",
		"keys" => array(
			"server_hostname" => "(server_hostname)"
		)
	),
	"dedicated_ip" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"dedicated_server_hostname" => "varchar(255) NOT NULL default ''",
			"ip_addr" => "varchar(16) NOT NULL default ''",
			"available" => "enum('yes','no') NOT NULL default 'yes'",
			"rdns_addr" => "varchar(255) NOT NULL default 'gplhost.com'",
			"rdns_regen" => "enum('yes','no') NOT NULL default 'yes'",
			"ip_pool_id" => "int(11) NOT NULL default '0'"
		),
		"primary" => "(id)",
		"keys" => array(
				"ip_addr" => "(ip_addr)"
		)
	),
	"domain" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"name" => "varchar(128) NOT NULL default ''",
			"safe_mode" => "enum('yes','no') NOT NULL default 'yes'",
			"sbox_protect" => "enum('yes','no') NOT NULL default 'yes'",
			"owner" => "varchar(64) NOT NULL default ''",
			"default_subdomain" => "varchar(64) NOT NULL default 'www'",
			"default_sub_server_alias" => "enum('yes','no') NOT NULL default 'yes'",
			"generate_flag" => "enum('yes','no') NOT NULL default 'yes'",
			"quota" => "bigint(20) NOT NULL default '50'",
			"max_email" => "int(11) NOT NULL default '9'",
			"max_lists" => "int(11) NOT NULL default '3'",
			"max_ftp" => "int(11) NOT NULL default '3'",
			"max_ssh" => "int(11) NOT NULL default '3'",
			"max_subdomain" => "int(11) NOT NULL default '5'",
			"ip_addr" => "varchar(16) NOT NULL default '127.0.0.1'",
			"backup_ip_addr" => "varchar(16) NOT NULL default ''",
			"primary_mx" => "varchar(255) NOT NULL default 'default'",
			"other_mx" => "varchar(255) NOT NULL default 'default'",
			"whois" => "enum('here','away','linked') NOT NULL default 'away'",
			"hosting" => "enum('here','away') NOT NULL default 'here'",
			"du_stat" => "bigint(20) NOT NULL default '0'",
			"gen_unresolved_domain_alias" => "enum('yes','no') NOT NULL default 'no'",
			"txt_root_entry" => "varchar(128) NOT NULL default 'GPLHost:>_ Opensource hosting worldwide'",
			"txt_root_entry2" => "varchar(128) NOT NULL default 'This domain is hosted using Domain Technologie Control http://www.gplhost.com/software-dtc.html'",
			"catchall_email" => "varchar(128) NOT NULL default ''",
			"domain_parking" => "varchar(255) NOT NULL default 'no-parking'",
			"domain_parking_type" => "enum('redirect','same_docroot','serveralias') NOT NULL default 'redirect'",
			"registrar_password" => "varchar(255) NOT NULL default ''",
			"ttl" => "int(9) NOT NULL default '7200'",
			"stats_login" => "varchar(32) NOT NULL default ''",
  			"stats_pass" => "varchar(16) NOT NULL default ''",
  			"stats_subdomain" => "enum('yes','no') NOT NULL default 'no'",
  			"wildcard_dns" => "enum('yes','no') NOT NULL default 'no'",
			"primary_dns" => "varchar(255) NOT NULL default 'default'",
			"other_dns" => "varchar(255) NOT NULL default 'default'",
			"whois" => "enum('here','away','linked') NOT NULL default 'away'",
			"owner_id" => "int(16) NOT NULL default '0'",
			"admin_id" => "int(16) NOT NULL default '0'",
			"billing_id" => "int(16) NOT NULL default '0'",
			"teck_id" => "int(16) NOT NULL default '0'",
			"creation_date" => "date NOT NULL default '0000-00-00'",
			"modification_date" => "date NOT NULL default '0000-00-00'",
			"expiration_date" => "date NOT NULL default '0000-00-00'",
			"registrar" => "varchar(255) NOT NULL default 'webnic'",
			"protection" => "enum('unlocked','transferprot','locked') NOT NULL default 'unlocked'",
		),
		"primary" => "(id)",
		"keys" => array(
			"name" => "(name)"
		),
		"index" => array(
			"owner_index" => "(owner)"
		)
	),
	"email_accounting" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"domain_name" => "varchar(128) NOT NULL default ''",
			"smtp_trafic" => "int(14) NOT NULL default '0'",
			"pop_trafic" => "int(14) NOT NULL default '0'",
			"imap_trafic" => "int(14) NOT NULL default '0'",
			"month" => "int(2) NOT NULL default '0'",
			"year" => "int(4) NOT NULL default '0'"
		),
		"primary" => "(id)",
		"keys" => array(
			"domain_name" => "(domain_name,month,year)",
		)
	),
	"fetchmail" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"domain_user" => "varchar(64) NOT NULL default ''",
			"domain_name" => "varchar(128) NOT NULL default ''",
			"pop3_email" => "varchar(64) NOT NULL default ''",
			"pop3_server" => "varchar(128) NOT NULL default ''",
			"pop3_login" => "varchar(128) NOT NULL default ''",
			"pop3_pass" => "varchar(128) NOT NULL default ''",
			"checkit" => "enum('yes','no') NOT NULL default 'yes'",
			"autodel" => "enum('0','1','2','3','7','14','21') NOT NULL default '7'",
			"mailbox_type" => "enum('POP3','IMAP4','MSN','HOTMAIL','YAHOO','GMAIL') NOT NULL default 'POP3'"
		),
		"primary" => "(id)",
		"keys" => array(
			"domain_user" => "(domain_user,domain_name,pop3_server,pop3_login)",
			"pop3_email" => "(pop3_email)"
		)
	),
	"ftp_access" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"login" => "varchar(50) NOT NULL default ''",
			"uid" => "int(5) NOT NULL default 'CONF_DTC_SYSTEM_UID'",
			"gid" => "int(5) NOT NULL default 'CONF_DTC_SYSTEM_GID'",
			"password" => "varchar(50) NOT NULL default 'passwd'",
			"homedir" => "varchar(255) NOT NULL default ''",
			"count" => "int(11) NOT NULL default '0'",
			"fhost" => "varchar(50) default NULL",
			"faddr" => "varchar(15) default NULL",
			"ftime" => "timestamp NOT NULL default '0000-00-00 00:00:00'",
			"fcdir" => "varchar(150) default NULL",
			"fstor" => "int(11) NOT NULL default '0'",
			"fretr" => "int(11) NOT NULL default '0'",
			"bstor" => "int(11) NOT NULL default '0'",
			"bretr" => "int(11) NOT NULL default '0'",
			"creation" => "datetime default NULL",
			"ts" => "timestamp NOT NULL default '0000-00-00 00:00:00'",
			"frate" => "int(11) NOT NULL default '5'",
			"fcred" => "int(2) NOT NULL default '15'",
			"brate" => "int(11) NOT NULL default '5'",
			"bcred" => "int(2) NOT NULL default '1'",
			"flogs" => "int(11) NOT NULL default '0'",
			"size" => "int(11) NOT NULL default '0'",
			"shell" => "varchar(64) NOT NULL default '/bin/bash'",
			"hostname" => "varchar(64) NOT NULL default 'anotherlight.com'",
			"vhostip" => "varchar(16) NOT NULL default '0.0.0.0'",
			"login_count" => "int(11) NOT NULL default '0'",
			"last_login" => "datetime NOT NULL default '0000-00-00 00:00:00'",
			"dl_bytes" => "int(14) NOT NULL default '0'",
			"ul_bytes" => "int(14) NOT NULL default '0'",
			"dl_count" => "int(14) NOT NULL default '0'",
			"ul_count" => "int(14) NOT NULL default '0'"
		),
		"keys" => array(
			"login" => "(login)"
		),
		"primary" => "(id)",
		"index" => array(
			"hostname" => "(hostname)"
		)
	),
	"ftp_accounting" => array(
		"vars" => array(
			"id" => "int(14) NOT NULL auto_increment",
			"sub_domain" => "varchar(50) NOT NULL default ''",
			"transfer" => "int(14) NOT NULL default '0'",
			"last_run" => "int(14) NOT NULL default '0'",
			"month" => "int(4) NOT NULL default '0'",
			"year" => "int(4) NOT NULL default '0'",
			"hits" => "int(14) NOT NULL default '0'"
		),
		"primary" => "(id)",
		"keys" => array(
			"sub_domain" => "(sub_domain,month,year)"
		)
	),
	"ftp_logs" => array(
		"vars" => array(
			"ui" => "bigint(20) NOT NULL auto_increment",
			"username" => "text",
			"filename" => "text",
			"size" => "bigint(20) default NULL",
			"host" => "text",
			"ip" => "varchar(255) NOT NULL default ''",
			"command" => "text",
			"command_time" => "text",
			"local_time" => "datetime default NULL",
			"success" => "char(1) default NULL"
		),
		"primary" => "(ui)"
	),
	"groups" => array(
		"vars" => array(
			"gid" => "int(11) NOT NULL default 'CONF_DTC_SYSTEM_GID'",
			"groupname" => "varchar(255) NOT NULL default 'dtcgrp'",
			"members" => "varchar(255) NOT NULL default 'dtc'"
			),
		),
	"handle" => array(
		"vars" => array(
			"id" => "int(16) NOT NULL auto_increment",
			"name" => "varchar(32) NOT NULL default ''",
			"owner" => "varchar(64) NOT NULL default ''",
			"company" => "varchar(64) NOT NULL default ''",
			"firstname" => "varchar(64) NOT NULL default ''",
			"lastname" => "varchar(64) NOT NULL default ''",
			"addr1" => "varchar(100) NOT NULL default ''",
			"addr2" => "varchar(100) default NULL",
			"addr3" => "varchar(100) default NULL",
			"city" => "varchar(64) NOT NULL default ''",
			"state" => "varchar(32) NOT NULL default ''",
			"country" => "char(2) NOT NULL default 'us'",
			"zipcode" => "varchar(32) NOT NULL default ''",
			"language" => "char(2) NOT NULL default 'en'",
			"phone_num" => "varchar(20) NOT NULL default ''",
			"fax_num" => "varchar(20) NOT NULL default ''",
			"email" => "varchar(255) NOT NULL default ''",
                        "ovh_id" => "varchar(20) NOT NULL default ''",
                        "ovh_passwd" => "varchar(12) NOT NULL default ''"
		),
		"primary" => "(id)",
		"keys" => array(
			"name" => "(name,owner)"
		)
	),
	"http_accounting" => array(
		"vars" => array(
			"id" => "int(14) NOT NULL auto_increment",
			"vhost" => "varchar(50) NOT NULL default ''",
			"bytes_sent" => "bigint(14) NOT NULL default '0'",
			"bytes_receive" => "bigint(14) NOT NULL default '0'",
			"count_hosts" => "int(12) NOT NULL default '0'",
			"count_visits" => "int(12) NOT NULL default '0'",
			"count_status_200" => "int(12) NOT NULL default '0'",
			"count_status_404" => "int(12) NOT NULL default '0'",
			"count_impressions" => "int(18) NOT NULL default '0'",
			"last_run" => "int(14) NOT NULL default '0'",
			"month" => "int(4) NOT NULL default '0'",
			"year" => "int(4) NOT NULL default '0'",
			"domain" => "varchar(50) NOT NULL default ''",
		),
		"primary" => "(id)",
		"keys" => array(
			"vhost" => "(vhost,month,year,domain)"
		)
	),
	"invoicing" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"customer_country_code" => "char(2) NOT NULL default ''",
			"service_country_code" => "char(2) NOT NULL default ''",
			"company_id" => "int(12) NOT NULL default '0'"
		),
		"primary" => "(id)"
	),
	"ip_pool" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"location" => "varchar(255) NOT NULL default ''",
			"ip_addr" => "varchar(16) NOT NULL default ''",
			"netmask" => "varchar(16) NOT NULL default ''",
			"gateway" => "varchar(16) NOT NULL default ''",
			"broadcast" => "varchar(16) NOT NULL default ''",
			"dns" => "varchar(16) NOT NULL default ''",
			"zone_type" => "enum('support_ticket','ip_per_ip','ip_per_ip_cidr','one_zonefile','one_zonefile_with_minus','one_zonefile_with_name','one_zonefile_with_slash') NOT NULL default 'one_zonefile'",
			"custom_part" => "text"
		),
		"primary" => "(id)",
		"keys" => array(
			"ip_addr" => "(ip_addr)"
		)
	),
	"ip_port_service" => array(
                "vars" => array(
                        "id" => "int(11) NOT NULL auto_increment",
                        "ip" => "varchar(16) NOT NULL default ''",
                        "port" => "varchar(16) NOT NULL default ''",
                        "service" => "varchar(64) NOT NULL default ''"
		),
                "primary" => "(id)"
	),
	"mailalias" => array(
		"vars" => array(
			"autoinc" => "int(12) NOT NULL auto_increment",
			"id" => "varchar(32) NOT NULL default ''",
			"domain_parent" => "varchar(255) NOT NULL default ''",
			"delivery_group" => "blob",
			"active" => "int(11) NOT NULL default '1'",
			"start_date" => "date NOT NULL default '0000-00-00'",
			"expire_date" => "date NOT NULL default '0000-00-00'",
			"bounce_msg" => "text"
		),
		"primary" => "(autoinc)"
	),
	"mailinglist" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"domain" => "varchar(255) NOT NULL default ''",
			"name" => "varchar(64) NOT NULL default ''",
			"owner" => "varchar(255) NOT NULL default ''",
			"spammode" => "enum('yes','no') NOT NULL default 'no'",
			"webarchive" => "enum('yes','no') NOT NULL default 'no'"
		),
		"primary" => "(id)"
	),
	"nameservers" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"owner" => "varchar(64) NOT NULL default ''",
			"domain_name" => "varchar(128) NOT NULL default ''",
			"subdomain" => "varchar(128) NOT NULL default ''",
			"ip" => "varchar(16) NOT NULL default ''"
		),
		"primary" => "(id)",
		"keys" => array(
			"domain_name" => "(domain_name,subdomain)"
		)
	),
	"nas" => array(
		"vars" => array(
			"id" => "int(10) NOT NULL auto_increment",
			"nasname" => "varchar(128) NOT NULL default ''",
			"shortname" => "varchar(32) NOT NULL default ''",
			"type" => "varchar(30) NOT NULL default 'other'",
			"ports" => "int(5) default NULL",
			"secret" => "varchar(60) NOT NULL default 'secret'",
			"server" => "varchar(64) default NULL",
			"community" => "varchar(50) default NULL",
			"description" => "varchar(200) NOT NULL default 'RADIUS Client'"
		),
		"primary" => "(id)",
		"index" => array(
			"nasname" => "(nasname)"
		)
	),
	"new_admin" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"reqadm_login" => "varchar(64) NOT NULL default ''",
			"reqadm_pass" => "varchar(255) NOT NULL default ''",
			"domain_name" => "varchar(64) NOT NULL default ''",
			"family_name" => "varchar(64) NOT NULL default ''",
			"first_name" => "varchar(64) NOT NULL default ''",
			"comp_name" => "varchar(64) NOT NULL default ''",
			"iscomp" => "enum('yes','no') NOT NULL default 'yes'",
			"vat_num" => "varchar(128) NOT NULL default ''",
			"email" => "varchar(255) NOT NULL default ''",
			"phone" => "varchar(20) NOT NULL default ''",
			"fax" => "varchar(20) NOT NULL default ''",
			"addr1" => "varchar(100) NOT NULL default ''",
			"addr2" => "varchar(100) NOT NULL default ''",
			"addr3" => "varchar(100) NOT NULL default ''",
			"zipcode" => "varchar(32) NOT NULL default ''",
			"city" => "varchar(64) NOT NULL default ''",
			"state" => "varchar(32) NOT NULL default ''",
			"country" => "char(2) NOT NULL default 'us'",
			"paiement_id" => "int(9) NOT NULL default '0'",
			"product_id" => "int(9) NOT NULL default '0'",
			"custom_notes" => "text",
			"vps_location" => "varchar(255) NOT NULL default ''",
			"vps_os" => "varchar(255) NOT NULL default ''",
			"shopper_ip" => "varchar(16) NOT NULL default ''",
			"date" => "date NOT NULL default '0000-00-00'",
			"time" => "time NOT NULL default '00:00:00'",
			"maxmind_output" => "text",
			"last_used_lang" => "varchar(32) NOT NULL default 'en_US.UTF-8'",
			"add_service" => "enum('yes','no') NOT NULL default 'no'",
			"customfld" => "text",
			"archive" => "enum('yes','no') NOT NULL default 'yes'"
		),
		"primary" => "(id)"
	),
	"paiement" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"id_client" => "int(11) NOT NULL default '0'",
			"id_command" => "int(11) NOT NULL default '0'",
			"label" => "varchar(255) NOT NULL default '0'",
			"currency" => "varchar(64) NOT NULL default 'USD'",
			"refund_amount" => "decimal(9,2) NOT NULL default '0.00'",
			"paiement_cost" => "decimal(9,2) NOT NULL default '0.00'",
			"paiement_total" => "decimal(9,2) NOT NULL default '0.00'",
			"paiement_type" => "enum('online','cheque','wire','other','free') NOT NULL default 'online'",
			"secpay_site" => "enum('none','paypal','worldpay','enets','moneybokers','webmoney','dineromail') NOT NULL default 'none'",
			"secpay_custom_id" => "int(11) NOT NULL default '0'",
			"shopper_ip" => "varchar(16) NOT NULL default '0.0.0.0'",
			"date" => "date NOT NULL default '0000-00-00'",
			"time" => "time NOT NULL default '00:00:00'",
			"valid_date" => "date NOT NULL default '0000-00-00'",
			"valid_time" => "time NOT NULL default '00:00:00'",
			"valid" => "enum('yes','no','pending') NOT NULL default 'no'",
			"pending_reason" => "varchar(128) NOT NULL default ''",
			"new_account" => "enum('yes','no') NOT NULL default 'yes'",
			"product_id" => "int(11) NOT NULL default '0'",
			"vat_rate" => "decimal(9,2) NOT NULL default '0.00'",
			"vat_total" => "decimal(9,2) NOT NULL default '0.00'",
			"services" => "text",
			"hash_check_key" => "varchar(255) NOT NULL default ''",
		),
		"primary" => "(id)",
		"keys" => array(
			"id" => "(id)"
		)
	),
	"pending_queries" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"adm_login" => "varchar(64) NOT NULL default ''",
			"domain_name" => "varchar(128) NOT NULL default ''",
			"date" => "varchar(16) NOT NULL default '0000-00-00 00:00'"
		),
		"primary" => "(id)"
	),
	"pending_renewal" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"adm_login" => "varchar(64) NOT NULL default ''",
			"renew_date" => "date NOT NULL default '0000-00-00'",
			"renew_time" => "time NOT NULL default '00:00:00'",
			"product_id" => "int(11) NOT NULL default '0'",
			"renew_id" => "int(11) NOT NULL default '0'",
			"heb_type" => "enum('shared','ssl','vps','server','custom','ssl_renew','shared-upgrade','add-money','multiple-services') NOT NULL default 'shared'",
			"services" => "text",
			"pay_id" => "int(11) NOT NULL default '0'",
			"country_code" => "varchar(255) NOT NULL default 'US'"
		),
		"primary" => "(id)"
	),
	"pop_access" => array(
		"vars" => array(
			"autoinc" => "int(12) NOT NULL auto_increment",
			"id" => "varchar(32) NOT NULL default ''",
			"uid" => "int(11) NOT NULL default 'CONF_DTC_SYSTEM_UID'",
			"gid" => "int(11) NOT NULL default 'CONF_DTC_SYSTEM_GID'",
			"home" => "varchar(255) NOT NULL default ''",
			"shell" => "varchar(255) NOT NULL default ''",
			"mbox_host" => "varchar(120) NOT NULL default ''",
			"crypt" => "varchar(255) NOT NULL default ''",
			"passwd" => "varchar(255) NOT NULL default ''",
			"active" => "int(11) NOT NULL default '1'",
			"start_date" => "date NOT NULL default '0000-00-00'",
			"expire_date" => "date NOT NULL default '0000-00-00'",
			"quota_size" => "int(11) NOT NULL default '0'",
			"quota_files" => "int(11) NOT NULL default '0'",
			"quota_couriermaildrop" => "varchar(255) NOT NULL default '0S,0C'",
			"type" => "varchar(20) NOT NULL default 'default'",
			"memo" => "text",
			"du" => "bigint(20) NOT NULL default '0'",
			"another_perso" => "varchar(5) NOT NULL default 'no'",
			"redirect1" => "varchar(255) default NULL",
			"redirect2" => "varchar(255) default NULL",
			"localdeliver" => "varchar(10) NOT NULL default 'yes'",
			"pop3_login_count" => "int(9) NOT NULL default '0'",
			"pop3_transfered_bytes" => "int(14) NOT NULL default '0'",
			"imap_login_count" => "int(9) NOT NULL default '0'",
			"imap_transfered_bytes" => "int(14) NOT NULL default '0'",
			"last_login" => "int(14) NOT NULL default '0'",
			"iwall_protect" => "enum('yes','no') NOT NULL default 'no'",
			"bounce_msg" => "text",
			"spf_protect" => "enum('yes','no') NOT NULL default 'no'",
			"clamav_protect" => "enum('yes','no') NOT NULL default 'no'",
			"fullemail" => "varchar(255) NOT NULL default 'none'",
			"spam_mailbox_enable" => "enum('yes','no') NOT NULL default 'no'",
			"spam_mailbox" => "varchar(255) NOT NULL default 'SPAM'",
			"pass_next_req" => "varchar(128) NOT NULL default ''",
			"pass_expire" => "int(12) NOT NULL default '0'",
			"vacation_flag" => "enum('yes','no') NOT NULL default 'no'",
			"vacation_text" => "text"
		),
		"primary" => "(autoinc)",
		"keys" => array(
			"id" => "(id,mbox_host)"
		)
	),
	"product" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"price_dollar" => "varchar(9) NOT NULL default ''",
			"price_euro" => "varchar(9) NOT NULL default ''",
			"setup_fee" => "decimal(15,2) NOT NULL default '0.00'",
			"name" => "varchar(255) NOT NULL default ''",
			"quota_disk" => "int(9) NOT NULL default '0'",
			"memory_size" => "int(9) NOT NULL default '48'",
			"virt_type" => "enum('xen','vz','kvm','virtualbox') NOT NULL default 'xen'",
			"nbr_email" => "int(9) NOT NULL default '0'",
			"nbr_database" => "int(9) NOT NULL default '0'",
			"bandwidth" => "int(15) NOT NULL default '0'",
			"period" => "varchar(12) NOT NULL default '0001-00-00'",
			"allow_add_domain" => "enum('yes','no','check') NOT NULL default 'no'",
			"max_domain" => "int(9) NOT NULL default '0'",
			"restricted_ftp_path" => "enum('yes','no') NOT NULL default 'no'",
			"shared_hosting_security" => "enum('mod_php','sbox_copy','sbox_aufs') NOT NULL default 'mod_php'",
			"allow_dns_and_mx_change" => "enum('yes','no') NOT NULL default 'yes'",
			"allow_mailing_list_edit" => "enum('yes','no') NOT NULL default 'yes'",
			"allow_subdomain_edit" => "enum('yes','no') NOT NULL default 'yes'",
			"pkg_install_flag" => "enum('yes','no') NOT NULL default 'yes'",
			"ftp_login_flag" => "enum('yes','no') NOT NULL default 'yes'",
			"heb_type" => "enum('shared','ssl','vps','server','custom') NOT NULL default 'shared'",
			"renew_prod_id" => "int(11) NOT NULL default '0'",
			"affiliate_kickback" => "varchar(9) NOT NULL default ''",
			"private" => "enum('yes','no') NOT NULL default 'no'",
			"use_radius" => "enum('yes','no') NOT NULL default 'no'",
			"custom_heb_type" => "int(11) NOT NULL default '0'",
			"custom_heb_type_fld" => "text"
		),
		"primary" => "(id)",
		"keys" => array(
			"id" => "(id)"
		)
	),
	"radacct" => array(
		"vars" => array(
			"RadAcctId" => "bigint(21) NOT NULL auto_increment",
			"AcctSessionId" => "varchar(32) NOT NULL default ''",
			"AcctUniqueId" => "varchar(32) NOT NULL default ''",
			"UserName" => "varchar(64) NOT NULL default ''",
			"Realm" => "varchar(64) NOT NULL default ''",
			"NASIPAddress" => "varchar(15) NOT NULL default ''",
			"NASPortId" => "int(12) default NULL",
			"NASPortType" => "varchar(32) default NULL",
			"AcctStartTime" => "datetime NOT NULL default '0000-00-00 00:00:00'",
			"AcctStopTime" => "datetime NOT NULL default '0000-00-00 00:00:00'",
			"AcctSessionTime" => "int(12) default NULL",
			"AcctAuthentic" => "varchar(32) default NULL",
			"ConnectInfo_start" => "varchar(32) default NULL",
			"ConnectInfo_stop" => "varchar(32) default NULL",
			"AcctInputOctets" => "bigint(12) default NULL",
			"AcctOutputOctets" => "bigint(12) default NULL",
			"CalledStationId" => "varchar(50) NOT NULL default ''",
			"CallingStationId" => "varchar(50) NOT NULL default ''",
			"AcctTerminateCause" => "varchar(32) NOT NULL default ''",
			"ServiceType" => "varchar(32) default NULL",
			"FramedProtocol" => "varchar(32) default NULL",
			"FramedIPAddress" => "varchar(15) NOT NULL default ''",
			"AcctStartDelay" => "int(12) default NULL",
			"AcctStopDelay" => "int(12) default NULL",
			"XAscendSessionSrvKey" => "varchar(64) default NULL"
		),
		"primary" => "(RadAcctId)",
		"index" => array(
			"UserName" => "(UserName)",
			"FramedIPAddress" => "(FramedIPAddress)",
			"AcctSessionId" => "(AcctSessionId)",
			"AcctUniqueId" => "(AcctUniqueId)",
			"AcctStartTime" => "(AcctStartTime)",
			"AcctStopTime" => "(AcctStopTime)",
			"NASIPAddress" => "(NASIPAddress)"
		)
	),
	"radcheck" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"UserName" => "varchar(64) NOT NULL default ''",
			"Attribute" => "varchar(32) NOT NULL default ''",
			"op" => "char(2) NOT NULL default '=='",
			"Value" => "varchar(253) NOT NULL default ''"
		),
		"primary" => "(id)",
		"index" => array(
			"UserName" => "(UserName(32))"
		)
	),
	"radgroupcheck" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"GroupName" => "varchar(64) NOT NULL default ''",
			"Attribute" => "varchar(32) NOT NULL default ''",
			"op" => "char(2) NOT NULL default '=='",
			"Value" => "varchar(253) NOT NULL default ''"
		),
		"primary" => "(id)",
		"index" => array(
			"GroupName" => "(GroupName(32))"
		)
	),
	"radgroupreply" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"GroupName" => "varchar(64) NOT NULL default ''",
			"Attribute" => "varchar(32) NOT NULL default ''",
			"op" => "char(2) NOT NULL default '='",
			"Value" => "varchar(253) NOT NULL default ''",
			"prio" => "int(11) NOT NULL default '0'"
		),
		"primary" => "(id)",
		"index" => array(
			"GroupName" => "(GroupName(32))"
		)
	),
	"radpostauth" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"username" => "varchar(64) NOT NULL default ''",
			"pass" => "varchar(64) NOT NULL default ''",
			"reply" => "varchar(32) NOT NULL default ''",
			"authdate" => "timestamp NOT NULL default '0000-00-00 00:00:00'"
		),
		"primary" => "(id)"
	),
	"radreply" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"UserName" => "varchar(64) NOT NULL default ''",
			"Attribute" => "varchar(32) NOT NULL default ''",
			"op" => "char(2) NOT NULL default '='",
			"Value" => "varchar(253) NOT NULL default ''"
		),
		"primary" => "(id)",
		"index" => array(
			"UserName" => "(UserName(32))"
		)
	),
	"radgroup" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"GroupName" => "varchar(64) NOT NULL default ''",
			"Description" => "varchar(253) NOT NULL default ''"
		),
		"primary" => "(id)",
		"index" => array(
			"GroupName" => "(GroupName)"
		)
	),
	"radusergroup" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"UserName" => "varchar(64) NOT NULL default ''",
			"GroupName" => "varchar(64) NOT NULL default ''",
			"Password" => "varchar(253) NOT NULL default ''",
			"Dedicated_id" => "int(11) default NULL",
			"priority" => "int(11) NOT NULL default '1'"
		),
		"primary" => "(id)",
		"keys" => array(
			"Username" => "(UserName)",
			"Dedicated" => "(Dedicated_id)"
		),
		"index" => array(
			"GroupName" => "(GroupName)"
		)
	),
	"registrar_domains" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"tld" => "varchar(64) NOT NULL default '.com'",
			"registrar" => "varchar(128) NOT NULL default 'webnic'",
			"price" => "decimal(15,2) NOT NULL default '100.00'"
		),
		"primary" => "(id)",
		"keys" => array(
			"tld" => "(tld)"
		)
	),
	"scheduled_updates" => array(
		"vars" => array(
			"backup_id" => "int(9) NOT NULL default '0'",
			"timestamp" => "int(12) NOT NULL default '0'"
		),
	),
	"secpayconf" => array(
		"vars" => array(
			"unicrow" => "int(2) NOT NULL default '0'",
			"currency_symbol" => "varchar(16) NOT NULL default '$'",
			"currency_letters" => "varchar(16) NOT NULL default 'USD'",
			"use_paypal" => "enum('yes','no') NOT NULL default 'no'",
			"paypal_rate" => "float(6,2) NOT NULL default '0.00'",
			"paypal_flat" => "float(6,2) NOT NULL default '0.00'",
			"paypal_autovalidate" => "enum('yes','no') NOT NULL default 'yes'",
			"paypal_email" => "varchar(255) NOT NULL default 'palpay@gplhost.com'",
			"paypal_sandbox" => "enum('yes','no') NOT NULL default 'no'",
			"paypal_sandbox_email" => "varchar(255) NOT NULL default ''",
			"paypal_validate_with" => "enum('total','mc_gross') NOT NULL default 'total'",
			"use_paypal_recurring" => "enum('yes','no') NOT NULL default 'no'",

			"use_moneybookers" => "enum('yes','no') NOT NULL default 'no'",
			"moneybookers_rate" => "decimal(9,2) NOT NULL default '0.00'",
			"moneybookers_flat" => "decimal(9,2) NOT NULL default '0.00'",
			"moneybookers_autovalidate" => "enum('yes','no') NOT NULL default 'yes'",
			"moneybookers_email" => "varchar(128) NOT NULL default 'palpay@gplhost.com'",
			"moneybookers_sandbox" => "enum('yes','no') NOT NULL default 'no'",
			"moneybookers_sandbox_email" => "varchar(128) NOT NULL default ''",
			"moneybookers_validate_with" => "enum('total','mc_gross') NOT NULL default 'total'",
			"moneybookers_secret_word" => "varchar(128) NOT NULL default ''",

			"use_enets" => "enum('yes','no') NOT NULL default 'no'",
			"use_enets_test" => "enum('yes','no') NOT NULL default 'yes'",
			"enets_mid_id" => "varchar(255) NOT NULL default ''",
			"enets_test_mid_id" => "varchar(255) NOT NULL default ''",
			"enets_rate" => "decimal(9,2) NOT NULL default '0.00'",
			"use_maxmind" => "enum('yes','no') NOT NULL default 'no'",

			"maxmind_login" => "varchar(255) NOT NULL default ''",
			"maxmind_license_key" => "varchar(255) NOT NULL default ''",
			"maxmind_threshold" => "int(3) NOT NULL default '100'",

			"use_webmoney" => "enum('yes','no') NOT NULL default 'no'",
			"webmoney_license_key" => "varchar(255) NOT NULL default ''",
			"webmoney_wmz" => "varchar(255) NOT NULL default ''",

			"accept_cheques" => "enum('yes','no') NOT NULL default 'no'",
			"cheques_flat_fees" => "decimal(9,2) NOT NULL default '0.00'",
			"cheques_to_label" => "varchar(255) NOT NULL default ''",
			"cheques_send_address" => "text",

			"accept_wiretransfers" => "enum('yes','no') NOT NULL default 'no'",
			"wiretransfers_flat_fees" => "decimal(9,2) NOT NULL default '0.00'",
			"wiretransfers_bank_details" => "text",

			"use_dineromail" => "enum('yes','no') NOT NULL default 'no'",
			"dineromail_nrocuenta" => "varchar(20) NOT NULL default ''",
			"dineromail_tipospago" => "varchar(30) NOT NULL default '2,7,13,4,5,6,14,15,16,17,18'",
			"dineromail_cargocomision" => "decimal(9,2) NOT NULL default '0.00'",
			"dineromail_porcentajecomision" => "decimal(9,2) NOT NULL default '0.00'",
			"dineromail_logo_url" => "varchar(255) NOT NULL default ''"
		),
		"keys" => array(
			"unicrow" => "(unicrow)"
		)
	),
	"smtp_logs" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"newmsg_id" => "bigint(20) default NULL",
			"bounce_qp" => "int(11) default NULL",
			"bytes" => "int(11) NOT NULL default '0'",
			"sender_user" => "varchar(128) NOT NULL default ''",
			"sender_domain" => "varchar(128) NOT NULL default ''",
			"delivery_id" => "bigint(20) default NULL",
			"delivery_user" => "varchar(128) NOT NULL default ''",
			"delivery_domain" => "varchar(128) NOT NULL default ''",
			"delivery_success" => "enum('yes','no') NOT NULL default 'no'",
			"delivery_id_text" => "varchar(128) NOT NULL default ''",
			"time_stamp" => "int(11) NOT NULL default '0'",
			"msg_id_text" => "varchar(128) NOT NULL default ''"
		),
		"primary" => "(id)",
		"index" => array(
			"sender_domain" => "(sender_domain)",
			"delivery_domain" => "(delivery_domain)"
		),
		"keys" => array(
			"bounce_qp" => "(bounce_qp)",
			"newmsg_id" => "(newmsg_id)",
			"delivery_id_text" => "(delivery_id_text)"
		)
	),
	"spent_bank" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"acct_name" => "varchar(128) NOT NULL default ''",
			"id_company" => "int(11) NOT NULL default '0'",
			"acct_number" => "varchar(64) NOT NULL default '0'",
			"swift" => "varchar(128) NOT NULL default '0'",
			"sort_code" => "varchar(128) NOT NULL default ''",
			"bank_addr" => "varchar(255) NOT NULL default ''",
			"currency_type" => "varchar(10) NOT NULL default 'EUR'",
		),
		"primary" => "(id)"
	),
	"spent_moneyout" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"id_company_spending" => "int(11) NOT NULL default '0'",
			"id_provider" => "int(11) NOT NULL default '0'",
			"payment_total" => "decimal(9,2) NOT NULL default '0.00'",
			"payment_type" => "enum('none','credit_card','wire_transfer','paypal','check','cash') NOT NULL default 'none'",
			"label" => "varchar(128) NOT NULL default ''",
			"expenditure_type" => "int(11) NOT NULL default '0'",
			"invoice_date" => "date NOT NULL default '0000-00-00'",
			"paid_date" => "date NOT NULL default '0000-00-00'",
			"time" => "time NOT NULL default '00:00:00'",
			"vat_rate" => "decimal(9,2) NOT NULL default '0.00'",
			"vat_total" => "decimal(9,2) NOT NULL default '0.00'",
			"bank_acct_id" => "int(11) NOT NULL default '0'",
			"amount" => "decimal(9,2) NOT NULL default '0.00'",
			"currency_type" => "varchar(10) NOT NULL default 'EUR'",
		),
		"primary" => "(id)"
	),
	"spent_providers" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"quick_name" => "varchar(64) NOT NULL default '-'",
			"is_company" => "enum('yes','no') NOT NULL default 'no'",
			"company_name" => "varchar(64) NOT NULL default ''",
			"vat_num" => "varchar(128) NOT NULL default ''",
			"familyname" => "varchar(64) NOT NULL default ''",
			"christname" => "varchar(64) NOT NULL default ''",
			"addr1" => "varchar(100) NOT NULL default ''",
			"addr2" => "varchar(100) default NULL",
			"addr3" => "varchar(100) default NULL",
			"city" => "varchar(64) NOT NULL default ''",
			"zipcode" => "varchar(32) NOT NULL default '0'",
			"state" => "varchar(32) default NULL",
			"country" => "char(2) NOT NULL default ''",
			"phone" => "varchar(20) NOT NULL default '0'",
			"fax" => "varchar(20) default NULL",
			"email" => "varchar(255) NOT NULL default ''",
			"special_note" => "blob",
			"always_yes" => "enum('yes','no') NOT NULL default 'yes'"
		),
		"primary" => "(id)"
	),
	"spent_type" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"label" => "varchar(128) NOT NULL default ''"
		),
		"primary" => "(id)"
	),
	"ssh_access" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"login" => "varchar(50) NOT NULL default ''",
			"uid" => "int(5) NOT NULL default 'CONF_DTC_SYSTEM_UID'",
			"gid" => "int(5) NOT NULL default 'CONF_DTC_SYSTEM_GID'",
			"crypt" => "varchar(50) NOT NULL default ''",
			"password" => "varchar(50) NOT NULL default 'passwd'",
			"homedir" => "varchar(255) NOT NULL default ''",
			"count" => "int(11) NOT NULL default '0'",
			"fhost" => "varchar(50) default NULL",
			"faddr" => "varchar(15) default NULL",
			"ftime" => "timestamp NOT NULL default '0000-00-00 00:00:00'",
			"fcdir" => "varchar(150) default NULL",
			"fstor" => "int(11) NOT NULL default '0'",
			"fretr" => "int(11) NOT NULL default '0'",
			"bstor" => "int(11) NOT NULL default '0'",
			"bretr" => "int(11) NOT NULL default '0'",
			"creation" => "datetime default NULL",
			"ts" => "timestamp NOT NULL default '0000-00-00 00:00:00'",
			"frate" => "int(11) NOT NULL default '5'",
			"fcred" => "int(2) NOT NULL default '15'",
			"brate" => "int(11) NOT NULL default '5'",
			"bcred" => "int(2) NOT NULL default '1'",
			"flogs" => "int(11) NOT NULL default '0'",
			"size" => "int(11) NOT NULL default '0'",
			"shell" => "varchar(64) NOT NULL default '/usr/bin/dtc-chroot-shell'",
			"hostname" => "varchar(64) NOT NULL default 'anotherlight.com'",
			"vhostip" => "varchar(16) NOT NULL default '0.0.0.0'",
			"login_count" => "int(11) NOT NULL default '0'",
			"last_login" => "datetime NOT NULL default '0000-00-00 00:00:00'",
			"dl_bytes" => "int(14) NOT NULL default '0'",
			"ul_bytes" => "int(14) NOT NULL default '0'",
			"dl_count" => "int(14) NOT NULL default '0'",
			"ul_count" => "int(14) NOT NULL default '0'"
		),
		"keys" => array(
			"login" => "(login)"
		),
		"primary" => "(id)",
		"index" => array(
			"hostname" => "(hostname)"
		)
	),
	"ssh_groups" => array(
		"vars" => array(
			"group_id" => "int(11) NOT NULL auto_increment",
			"group_name" => "varchar(30) NOT NULL default ''",
			"status" => "char(1) NOT NULL default 'A'",
			"group_password" => "varchar(64) NOT NULL default 'x'",
			"gid" => "int(11) NOT NULL default '0'"
		),
		"primary" => "(group_id)",
		"keys" => array(
			"group_name_gid" => "(group_name,gid)",
			"group_gid" => "(gid)"
		)
	),
	"ssh_user_group" => array(
		"vars" => array(
			"user_id" => "int(11) NOT NULL default '0'",
			"group_id" => "int(11) NOT NULL default '0'"
			)
		),
	"subdomain" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"safe_mode" => "enum('yes','no') NOT NULL default 'yes'",
			"sbox_protect" => "enum('yes','no') NOT NULL default 'yes'",
			"shared_hosting_security" => "enum('mod_php','sbox_copy','sbox_aufs') NOT NULL default 'mod_php'",
			"shared_hosting_varwww_docroot" => "enum('yes','no') NOT NULL default 'no'",
			"domain_name" => "varchar(255) NOT NULL default ''",
			"subdomain_name" => "varchar(255) NOT NULL default ''",
			"path" => "varchar(64) NOT NULL default ''",
			"webalizer_generate" => "varchar(8) NOT NULL default 'no'",
			"ip" => "varchar(255) NOT NULL default 'default'",
			"ipv4_round_robin" => "varchar(255) NOT NULL default ''",
			"ip6" => "varchar(255) NOT NULL default ''",
			"register_globals" => "enum('yes','no') NOT NULL default 'no'",
			"login" => "varchar(16) default NULL",
			"pass" => "varchar(64) default NULL",
			"w3_alias" => "enum('yes','no') NOT NULL default 'no'",
			"associated_txt_record" => "varchar(256) NOT NULL default ''",
			"generate_vhost" => "enum('yes','no') NOT NULL default 'yes'",
			"ttl" => "int(11) NOT NULL default '7200'",
			"ssl_ip" => "varchar(16) NOT NULL default 'none'",
			"nameserver_for" => "varchar(64) default NULL",
			"srv_record" => "varchar(64) default NULL",
			"add_default_charset" => "varchar(32) NOT NULL default 'dtc-wont-add'",
			"customize_vhost" => "text",
			"php_memory_limit" => "int(11) NOT NULL default '64'",
			"php_max_execution_time" => "int(11) NOT NULL default '64'",
			"php_allow_url_fopen"=> "enum('yes','no') NOT NULL default 'no'",
			"php_post_max_size"=> "int(11) NOT NULL default '8'",
			"php_session_auto_start"=> "enum('yes','no') NOT NULL default 'no'",
			"php_upload_max_filesize"=> "int(11) NOT NULL default '2'",
			"use_shared_ssl" => "enum('yes','no') NOT NULL default 'no'",
			"redirect_url" => "varchar(512) NOT NULL default ''",
			"srv_record_protocol" => "enum('tcp','udp','sctp') NOT NULL default 'tcp'"
		),
		"primary" => "(id)",
		"keys" => array(
			"unic_subdomain" => "(domain_name,subdomain_name)",
			"login" => "(login)"
		),
		"index" => array(
			"domain_name_index" => "(domain_name)"
		)
	),
	"ssl_ips" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"ip_addr" => "varchar(16) NOT NULL default ''",
			"port" => "varchar(5) NOT NULL default ''",
			"adm_login" => "varchar(64) NOT NULL default ''",
			"available" => "enum('yes','no') NOT NULL default 'yes'",
			"expire" => "date NOT NULL default '0000-00-00'",
		),
		"primary" => "(id)",
		"keys" => array(
			"p_addr" => "(ip_addr)"
		),
	),
	"tik_admins" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"pseudo" => "varchar(64) NOT NULL default ''",
			"realname" => "varchar(64) NOT NULL default ''",
			"email" => "varchar(128) NOT NULL default ''",
			"available" => "enum('yes','no') NOT NULL default 'yes'",
			"tikadm_pass" => "varchar(255) NOT NULL default ''",
			"pass_next_req" => "varchar(128) NOT NULL default '0'",
			"pass_expire" => "int(12) NOT NULL default '0'"
		),
		"primary" => "(id)",
		"keys" => array(
			"pseudo" => "(pseudo)"
		)
	),
	"tik_attach" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"filename" => "varchar(255) NOT NULL default ''",
			"ctype_prim" => "varchar(64) NOT NULL default ''",
			"ctype_sec" => "varchar(64) NOT NULL default ''",
			"datahex" => "blob NOT NULL"
		),
		"primary" => "(id)"
	),
	"tik_cats" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"catname" => "varchar(64) NOT NULL default ''",
			"catdescript" => "varchar(255) NOT NULL default ''"
		),
		"primary" => "(id)"
	),
	"tik_queries" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"adm_login" => "varchar(64) NOT NULL default ''",
			"date" => "date NOT NULL default '0000-00-00'",
			"time" => "time NOT NULL default '00:00:00'",
			"in_reply_of_id" => "int(11) NOT NULL default '0'",
			"got_reply" => "enum('yes','no') NOT NULL default 'no'",
			"reply_id" => "int(11) NOT NULL default '0'",
			"admin_or_user" => "enum('admin','user') NOT NULL default 'user'",
			"subject" => "varchar(255) NOT NULL default ''",
			"text" => "text",
			"cat_id" => "int(11) NOT NULL default '0'",
			"initial_ticket" => "enum('yes','no') NOT NULL default 'yes'",
			"server_hostname" => "varchar(64) NOT NULL default ''",
			"request_close" => "enum('yes','no') NOT NULL default 'no'",
			"customer_email" => "varchar(255) NOT NULL default ''",
			"closed" => "enum('yes','no') NOT NULL default 'no'",
			"hash" => "varchar(32) NOT NULL default ''",
			"admin_name" => "varchar(256) NOT NULL default 'dtc'",
			"attach" => "text"
		),
		"primary" => "(id)",
		"index" => array(
			"in_reply" => "(in_reply_of_id)",
			"reply_id" => "(reply_id)",
			"got_reply2" => "(got_reply)"
		)
	),
	"user_cron" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"cron_name" => "varchar(128) NOT NULL default ''",
			"domain_name" => "varchar(128) NOT NULL default ''",
			"subdomain_name" => "varchar(255) NOT NULL default ''",
			"minute" => "varchar(4) NOT NULL default '0'",
			"hour" => "varchar(4) NOT NULL default '0'",
			"day_of_month" => "varchar(4) NOT NULL default '0'",
			"mon" => "varchar(4) NOT NULL default '0'",
			"dow" => "varchar(4) NOT NULL default '0'",
			"uri" => "varchar(128) NOT NULL default 'my_cron.php?param=value'"
		),
		"primary" => "(id)"
	),
	"usergroup" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"UserName" => "varchar(64) NOT NULL default ''",
			"GroupName" => "varchar(64) NOT NULL default ''"
		),
		"primary" => "(id)",
		"index" => array(
			"UserName" => "(UserName(32))"
		)
	),
	"vps" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"owner" => "varchar(64) NOT NULL default ''",
			"vps_server_hostname" => "varchar(255) NOT NULL default ''",
			"vps_xen_name" => "varchar(64) NOT NULL default ''",
			"start_date" => "date NOT NULL default '0000-00-00'",
			"expire_date" => "date NOT NULL default '0000-00-00'",
			"hddsize" => "int(9) NOT NULL default '1'",
			"ramsize" => "int(9) NOT NULL default '48'",
			"bandwidth_per_month_gb" => "int(9) NOT NULL default '1'",
			"product_id" => "int(9) NOT NULL default '0'",
			"operatingsystem" => "varchar(64) NOT NULL default 'debian'",
			"vncpassword" => "varchar(64) NOT NULL default 'none'",
			"howtoboot" => "varchar(256) NOT NULL default 'hdd'",
			"installed" => "enum('yes','no') NOT NULL default 'no'",
			"bsdkernel" => "enum('normal','install') NOT NULL default 'normal'",
			"monitoring_email" => "varchar(255) NOT NULL default ''",
			"monitor_ping" => "enum('yes','no') NOT NULL default 'no'",
			"monitor_ssh" => "enum('yes','no') NOT NULL default 'no'",
			"monitor_http" => "enum('yes','no') NOT NULL default 'no'",
			"monitor_smtp" => "enum('yes','no') NOT NULL default 'no'",
			"monitor_pop3" => "enum('yes','no') NOT NULL default 'no'",
			"monitor_imap4" => "enum('yes','no') NOT NULL default 'no'",
			"monitor_ftp" => "enum('yes','no') NOT NULL default 'no'"
		),
		"primary" => "(id)",
		"keys" => array(
			"vps_server_hostname" => "(vps_server_hostname,vps_xen_name)"
		),
		"index" => array(
			"ownerindex" => "(owner)"
		)
	),
	"vps_ip" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"vps_server_hostname" => "varchar(255) NOT NULL default ''",
			"vps_xen_name" => "varchar(64) NOT NULL default ''",
			"ip_addr" => "varchar(16) NOT NULL default ''",
			"rdns_addr" => "varchar(255) NOT NULL default 'gplhost.com'",
			"rdns_regen" => "enum('yes','no') NOT NULL default 'yes'",
			"ip_pool_id" => "int(11) NOT NULL default '0'",
			"available" => "enum('yes','no') NOT NULL default 'yes'"
		),
		"keys" => array(
			"ip_addr" => "(ip_addr)"
		),
		"primary" => "(id)"
	),
        "vps_server" => array(
                "vars" => array(
                        "id" => "int(11) NOT NULL auto_increment",
			"dom0_ips" => "varchar(255) NOT NULL default ''",
                        "hostname" => "varchar(255) NOT NULL default ''",
                        "location" => "varchar(64) NOT NULL default ''",
                        "soap_login" => "varchar(64) NOT NULL default ''",
                        "soap_pass" => "varchar(64) NOT NULL default ''",
                        "virt_type" => "enum('xen','vz','kvm','virtualbox') NOT NULL default 'xen'",
			"lvmenable" => "enum('yes','no') NOT NULL default 'yes'",
			"country_code" => "varchar(4) NOT NULL default 'US'"
		),
                "primary" => "(id)",
                "keys" => array(
			"hostname" => "(hostname)"
                )
	),
	"vps_server_lists" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"hostname" => "varchar(255) NOT NULL default ''",
			"list_name" => "varchar(128) NOT NULL default ''"
		),
		"primary" => "(id)",
		"keys" => array(
			"hostname" => "(hostname,list_name)"
		)
	),
	"vps_stats" => array(
		"vars" => array(
			"month" => "int(2) NOT NULL default '1'",
			"year" => "int(4) NOT NULL default '2000'",
			"vps_server_hostname" => "varchar(255) NOT NULL default ''",
			"vps_xen_name" => "varchar(64) NOT NULL default ''",
			"last_run" => "int(11) default NULL",
			"cputime_last" => "float default NULL",
			"cpu_usage" => "float default NULL",
			"network_in_last" => "bigint(22) default NULL",
			"network_out_last" => "bigint(22) default NULL",
			"network_in_count" => "bigint(22) default NULL",
			"network_out_count" => "bigint(22) default NULL",
			"diskio_last" => "bigint(22) default NULL",
			"diskio_count" => "bigint(22) default NULL",
			"swapio_last" => "bigint(22) default NULL",
			"swapio_count" => "bigint(22) default NULL"
		),
		"primary" => "(month,year,vps_server_hostname,vps_xen_name)"
	),
	"whitelist" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"pop_user" => "varchar(32) NOT NULL default ''",
			"mbox_host" => "varchar(128) NOT NULL default ''",
			"mail_from_user" => "varchar(128) default NULL",
			"mail_from_domain" => "varchar(128) default NULL",
			"mail_to" => "varchar(128) default NULL"
		),
		"primary" => "(id)",
		"keys" => array(
			"unicbox" => "(pop_user,mail_from_user,mail_from_domain,mbox_host)",
			"pop_user" => "(pop_user,mbox_host,mail_to)"
		)
	)
));
?>
