<?php
// Automatic database array generation for DTC
// Generation date: 2005-02(Feb)-19 Saturday 03:20
$dtc_database = array(
"version" => "1.0.0",
"tables" => array(
	"admin" => array(
		"vars" => array(
			"adm_login" => "varchar(64) NOT NULL ",
			"adm_pass" => "varchar(16) NOT NULL ",
			"path" => "varchar(128) NOT NULL default '/web/disk4' ",
			"max_email" => "int(12) NOT NULL default '3' ",
			"max_ftp" => "int(12) NOT NULL default '3' ",
			"quota" => "int(11) NOT NULL default '50' ",
			"bandwidth_per_month_mb" => "int(11) NOT NULL default '100' ",
			"expire" => "date NOT NULL default '0000-00-00' ",
			"id_client" => "int(9) NOT NULL default '0' ",
			"pass_next_req" => "varchar(128) NOT NULL default '0' ",
			"pass_expire" => "int(12) NOT NULL default '0' ",
			"allow_add_domain" => "enum('yes','no','check') NOT NULL default 'check' ",
			"prod_id" => "int(11) NOT NULL default '0' "
			),
		"keys" => array(
			"PRIMARY" => "(adm_login)",
			"adm_login" => "(adm_login)",
			"path" => "(path)",
			"adm_login_2" => "(adm_login)"
			)
		),
	"backup" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"server_addr" => "varchar(128) NOT NULL ",
			"server_login" => "varchar(128) NOT NULL ",
			"server_pass" => "varchar(128) NOT NULL ",
			"type" => "enum('grant_access','mail_backup','dns_backup','trigger_changes','backup_ftp_to') NOT NULL default 'grant_access' ",
			"status" => "enum('pending','done') NOT NULL default 'pending' "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"id" => "(id)",
			"id_2" => "(id)"
			)
		),
	"clients" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"is_company" => "enum('yes','no') NOT NULL default 'no' ",
			"company_name" => "varchar(64) NULL ",
			"familyname" => "varchar(64) NOT NULL ",
			"christname" => "varchar(64) NOT NULL ",
			"addr1" => "varchar(100) NOT NULL ",
			"addr2" => "varchar(100) NULL ",
			"addr3" => "varchar(100) NULL ",
			"city" => "varchar(64) NOT NULL ",
			"zipcode" => "varchar(32) NOT NULL default '0' ",
			"state" => "varchar(32) NULL ",
			"country" => "char(2) NOT NULL ",
			"phone" => "varchar(20) NOT NULL default '0' ",
			"fax" => "varchar(20) NULL ",
			"email" => "varchar(255) NOT NULL ",
			"special_note" => "blob NULL ",
			"dollar" => "decimal(9,2) NOT NULL default '0.00' ",
			"disk_quota_mb" => "int(9) NOT NULL default '0' ",
			"bw_quota_per_month_gb" => "int(9) NOT NULL default '0' ",
			"expire" => "date NOT NULL default '0000-00-00' "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"id" => "(id)"
			)
		),
	"commande" => array(
		"vars" => array(
			"id" => "mediumint(9) NOT NULL auto_increment",
			"id_client" => "varchar(100) NOT NULL default '0' ",
			"domain_name" => "varchar(255) NOT NULL ",
			"quantity" => "varchar(10) NOT NULL ",
			"price_devise" => "enum('EUR','USD') NOT NULL default 'EUR' ",
			"price" => "varchar(255) NOT NULL ",
			"paiement_method" => "enum('cb','cheque','wire','other','free') NOT NULL default 'cb' ",
			"date" => "date NOT NULL default '0000-00-00' ",
			"expir" => "date NOT NULL default '0000-00-00' ",
			"valid" => "varchar(16) NOT NULL default 'yes' ",
			"product_id" => "int(9) NOT NULL default '0' ",
			"payment_id" => "int(11) NOT NULL default '0' "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"id_2" => "(id)",
			"id" => "(id)"
			)
		),
	"config" => array(
		"vars" => array(
			"db_version" => "int(11) NOT NULL default '10002' ",
			"unicrow" => "int(11) NOT NULL default '1' ",
			"demo_version" => "enum('yes','no') NOT NULL default 'no' ",
			"main_site_ip" => "varchar(16) NOT NULL default '127.0.0.1' ",
			"site_addrs" => "varchar(255) NOT NULL default '127.0.0.1|192.168.0.1' ",
			"use_multiple_ip" => "enum('yes','no') NOT NULL default 'yes' ",
			"addr_mail_server" => "varchar(255) NOT NULL default 'mx.example.com' ",
			"addr_backup_mail_server" => "varchar(255) NOT NULL ",
			"webmaster_email_addr" => "varchar(255) NOT NULL default 'postmaster@example.com' ",
			"addr_primary_dns" => "varchar(255) NOT NULL default 'ns1.example.com' ",
			"addr_secondary_dns" => "varchar(255) NOT NULL default 'ns2.example.com' ",
			"ip_slavezone_dns_server" => "varchar(16) NOT NULL default '192.168.0.3' ",
			"main_domain" => "varchar(128) NOT NULL default 'gplhost.com' ",
			"404_subdomain" => "varchar(128) NOT NULL default '404' ",
			"administrative_site" => "varchar(255) NOT NULL default 'dtc.example.com' ",
			"site_root_host_path" => "varchar(255) NOT NULL default '/var/www' ",
			"generated_file_path" => "varchar(255) NOT NULL default '/usr/share/dtc/etc' ",
			"dtcshared_path" => "varchar(255) NOT NULL default '/usr/share/dtc/shared' ",
			"dtcadmin_path" => "varchar(255) NOT NULL default '/usr/share/dtc/admin' ",
			"dtcclient_path" => "varchar(255) NOT NULL default '/usr/share/dtc/client' ",
			"dtcdoc_path" => "varchar(255) NOT NULL default '/usr/share/dtc/doc' ",
			"dtcemail_path" => "varchar(128) NOT NULL default '/usr/share/dtc/email' ",
			"qmail_rcpthost_path" => "varchar(255) NOT NULL default 'rcpthosts' ",
			"qmail_virtualdomains_path" => "varchar(255) NOT NULL default 'virtualdomains' ",
			"qmail_assign_path" => "varchar(255) NOT NULL default 'assign' ",
			"qmail_poppasswd_path" => "varchar(255) NOT NULL default 'poppasswd' ",
			"apache_vhost_path" => "varchar(255) NOT NULL default 'vhosts.conf' ",
			"php_additional_library_path" => "varchar(255) NOT NULL default '/usr/local/lib/php/phplib/:/usr/share/dtc/shared/' ",
			"php_library_path" => "varchar(255) NOT NULL default '/usr/lib/php/:/tmp/' ",
			"dns_type" => "enum('bind','djb') NOT NULL default 'bind' ",
			"named_path" => "varchar(255) NOT NULL default 'named.conf' ",
			"named_slavefile_path" => "varchar(255) NOT NULL default 'named.slavezones.conf' ",
			"named_slavezonefiles_path" => "varchar(255) NOT NULL default 'slave_zones' ",
			"named_zonefiles_path" => "varchar(255) NOT NULL default 'zones' ",
			"backup_script_path" => "varchar(255) NOT NULL default 'backup.bash' ",
			"bakcup_path" => "varchar(255) NOT NULL default '/mnt/backup' ",
			"webalizer_stats_script_path" => "varchar(255) NOT NULL default 'webalizer.bash' ",
			"use_javascript" => "enum('yes','no') NOT NULL default 'yes' ",
			"use_ssl" => "enum('yes','no') NOT NULL default 'no' ",
			"use_nated_vhost" => "enum('yes','no') NOT NULL default 'no' ",
			"nated_vhost_ip" => "varchar(16) NOT NULL default '192.168.0.2' ",
			"skin" => "varchar(128) NOT NULL default 'green' ",
			"mta_type" => "enum('qmail','postfix') NOT NULL default 'qmail' ",
			"domain_based_ftp_logins" => "enum('yes','no') NOT NULL default 'yes' ",
			"chroot_path" => "varchar(255) NOT NULL default '/var/www/chroot' ",
			"hide_password" => "enum('yes','no') NOT NULL default 'no' ",
			"session_expir_minute" => "int(9) NOT NULL default '10' ",
			"unicrow2" => "int(11) NOT NULL default '1' "
			),
		"keys" => array(
			"unicrow2" => "(unicrow2)",
			"unicrow" => "(unicrow)"
			)
		),
	"cron_job" => array(
		"vars" => array(
			"unicrow" => "int(11) NOT NULL default '1' ",
			"last_cronjob" => "timestamp(14) NULL ",
			"qmail_newu" => "enum('yes','no') NOT NULL default 'no' ",
			"restart_qmail" => "enum('yes','no') NOT NULL default 'no' ",
			"reload_named" => "enum('yes','no') NOT NULL default 'no' ",
			"restart_apache" => "enum('yes','no') NOT NULL default 'no' ",
			"gen_vhosts" => "enum('yes','no') NOT NULL default 'no' ",
			"gen_named" => "enum('yes','no') NOT NULL default 'no' ",
			"gen_qmail" => "enum('yes','no') NOT NULL default 'no' ",
			"gen_webalizer" => "enum('yes','no') NOT NULL default 'no' ",
			"gen_backup" => "enum('yes','no') NOT NULL default 'no' ",
			"lock_flag" => "enum('inprogress','finished') NOT NULL default 'finished' "
			),
		"keys" => array(
			"unicrow" => "(unicrow)"
			)
		),
	"domain" => array(
		"vars" => array(
			"name" => "varchar(64) NOT NULL ",
			"owner" => "varchar(64) NOT NULL ",
			"default_subdomain" => "varchar(64) NULL default 'www' ",
			"generate_flag" => "enum('yes','no') NOT NULL default 'yes' ",
			"quota" => "bigint(20) NOT NULL default '50' ",
			"max_email" => "int(11) NOT NULL default '9' ",
			"max_ftp" => "int(11) NOT NULL default '3' ",
			"max_subdomain" => "int(11) NOT NULL default '5' ",
			"ip_addr" => "varchar(16) NOT NULL default '213.215.47.212' ",
			"primary_dns" => "varchar(255) NOT NULL default 'default' ",
			"other_dns" => "varchar(255) NOT NULL default 'default' ",
			"primary_mx" => "varchar(255) NOT NULL default 'default' ",
			"other_mx" => "varchar(255) NOT NULL default 'default' ",
			"whois" => "enum('here','away','linked') NOT NULL default 'away' ",
			"hosting" => "enum('here','away') NOT NULL default 'here' ",
			"du_stat" => "bigint(20) NOT NULL default '0' ",
			"gen_unresolved_domain_alias" => "enum('yes','no') NOT NULL default 'no' ",
			"txt_root_entry" => "varchar(128) NOT NULL default 'GPLHost:>_ Opensource hosting worldwide' ",
			"txt_root_entry2" => "varchar(128) NOT NULL default 'This domain is hosted using Domain Technologie Control http://www.gplhost.com/?rub=softwares&sousrub=dtc' "
			),
		"keys" => array(
			"owner_index" => "(owner)",
			"name" => "(name)"
			)
		),
	"email_accounting" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"domain_name" => "varchar(128) NOT NULL ",
			"smtp_trafic" => "int(14) unsigned NOT NULL default '0' ",
			"pop_trafic" => "int(14) unsigned NOT NULL default '0' ",
			"month" => "int(2) NOT NULL default '0' ",
			"year" => "int(4) NOT NULL default '0' ",
			"imap_trafic" => "int(14) unsigned NOT NULL default '0' "
			),
		"keys" => array(
			"overall_index" => "(domain_name,month,year)",
			"id" => "(id)",
			"domain_name" => "(domain_name,month,year)"
			)
		),
	"fetchmail" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"domain_user" => "varchar(64) NOT NULL ",
			"domain_name" => "varchar(128) NOT NULL ",
			"pop3_email" => "varchar(64) NOT NULL ",
			"mailbox_type" => "enum('POP3','IMAP4','MSN','HOTMAIL','YAHOO','GMAIL') NOT NULL default 'POP3' ",
			"pop3_server" => "varchar(128) NOT NULL ",
			"pop3_login" => "varchar(128) NOT NULL ",
			"pop3_pass" => "varchar(128) NOT NULL ",
			"checkit" => "enum('yes','no') NOT NULL default 'yes' ",
			"autodel" => "enum('0','1','2','3','7','14','21') NOT NULL default '7' "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"domain_user" => "(domain_user,domain_name,pop3_server,pop3_login)",
			"pop3_email" => "(pop3_email)"
			)
		),
	"ftp_access" => array(
		"vars" => array(
			"login" => "varchar(50) NOT NULL ",
			"uid" => "int(5) NOT NULL default '65534' ",
			"gid" => "int(5) NOT NULL default '65534' ",
			"password" => "varchar(50) NOT NULL default 'passwd' ",
			"homedir" => "varchar(70) NOT NULL ",
			"count" => "int(11) NULL default '0' ",
			"fhost" => "varchar(50) NULL ",
			"faddr" => "varchar(15) NULL ",
			"ftime" => "timestamp(14) NULL ",
			"fcdir" => "varchar(150) NULL ",
			"fstor" => "int(11) NULL default '0' ",
			"fretr" => "int(11) NULL default '0' ",
			"bstor" => "int(11) NULL default '0' ",
			"bretr" => "int(11) NULL default '0' ",
			"creation" => "datetime NULL ",
			"ts" => "timestamp(14) NULL ",
			"frate" => "int(11) NULL default '5' ",
			"fcred" => "int(2) NULL default '15' ",
			"brate" => "int(11) NULL default '5' ",
			"bcred" => "int(2) NULL default '1' ",
			"flogs" => "int(11) NULL default '0' ",
			"size" => "int(11) NOT NULL default '0' ",
			"shell" => "varchar(64) NOT NULL default '/bin/bash' ",
			"hostname" => "varchar(64) NOT NULL default 'anotherlight.com' ",
			"login_count" => "int(11) NOT NULL default '0' ",
			"last_login" => "datetime NOT NULL default '0000-00-00 00:00:00' ",
			"dl_bytes" => "int(14) NOT NULL default '0' ",
			"ul_bytes" => "int(14) NOT NULL default '0' ",
			"dl_count" => "int(14) NOT NULL default '0' ",
			"ul_count" => "int(14) NOT NULL default '0' ",
			"vhostip" => "varchar(16) NOT NULL default '0.0.0.0' "
			),
		"keys" => array(
			"login" => "(login)",
			"hostname" => "(hostname)"
			)
		),
	"ftp_accounting" => array(
		"vars" => array(
			"id" => "int(14) NOT NULL auto_increment",
			"sub_domain" => "varchar(50) NOT NULL ",
			"transfer" => "int(14) unsigned NOT NULL default '0' ",
			"last_run" => "int(14) NOT NULL default '0' ",
			"month" => "int(4) NOT NULL default '0' ",
			"year" => "int(4) NOT NULL default '0' "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"sub_domain" => "(sub_domain,month,year)"
			)
		),
	"ftp_logs" => array(
		"vars" => array(
			"username" => "tinytext NULL ",
			"filename" => "text NULL ",
			"size" => "bigint(20) NULL ",
			"host" => "tinytext NULL ",
			"ip" => "tinytext NULL ",
			"command" => "tinytext NULL ",
			"command_time" => "tinytext NULL ",
			"local_time" => "datetime NULL ",
			"success" => "char(1) NULL ",
			"ui" => "bigint(20) NOT NULL auto_increment"
			),
		"keys" => array(
			"PRIMARY" => "(ui)"
			)
		),
	"groups" => array(
		"vars" => array(
			"gid" => "int(11) NOT NULL default '65534' ",
			"groupname" => "varchar(255) NOT NULL default 'nogroup' ",
			"members" => "varchar(255) NOT NULL default 'zigo' "
			),
		"keys" => array(
			)
		),
	"handle" => array(
		"vars" => array(
			"id" => "int(16) NOT NULL auto_increment",
			"name" => "varchar(32) NOT NULL ",
			"owner" => "varchar(64) NOT NULL ",
			"company" => "varchar(64) NULL ",
			"firstname" => "varchar(64) NOT NULL ",
			"lastname" => "varchar(64) NOT NULL ",
			"addr1" => "varchar(100) NOT NULL ",
			"addr2" => "varchar(100) NULL ",
			"addr3" => "varchar(100) NULL ",
			"city" => "varchar(64) NOT NULL ",
			"state" => "varchar(32) NULL ",
			"country" => "char(2) NOT NULL ",
			"zipcode" => "varchar(32) NOT NULL ",
			"phone_num" => "varchar(20) NOT NULL ",
			"fax_num" => "varchar(20) NULL ",
			"email" => "varchar(255) NOT NULL "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"name" => "(name,owner)"
			)
		),
	"http_accounting" => array(
		"vars" => array(
			"id" => "int(14) NOT NULL auto_increment",
			"vhost" => "varchar(50) NOT NULL ",
			"bytes_sent" => "int(14) NOT NULL default '0' ",
			"count_hosts" => "int(12) NOT NULL default '0' ",
			"count_visits" => "int(12) NOT NULL default '0' ",
			"count_status_200" => "int(12) NOT NULL default '0' ",
			"count_status_404" => "int(12) NOT NULL default '0' ",
			"count_impressions" => "int(18) NOT NULL default '0' ",
			"last_run" => "int(14) NOT NULL default '0' ",
			"month" => "int(4) NOT NULL default '0' ",
			"year" => "int(4) NOT NULL default '0' ",
			"domain" => "varchar(50) NOT NULL "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"vhost" => "(vhost,month,year,domain)",
			"month" => "(month,year,vhost)"
			)
		),
	"nameservers" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"owner" => "varchar(64) NOT NULL ",
			"domain_name" => "varchar(128) NOT NULL ",
			"subdomain" => "varchar(128) NOT NULL ",
			"ip" => "varchar(16) NOT NULL "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"domain_name" => "(domain_name,subdomain)"
			)
		),
	"new_admin" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"reqadm_login" => "varchar(64) NOT NULL ",
			"reqadm_pass" => "varchar(16) NOT NULL ",
			"domain_name" => "varchar(64) NOT NULL ",
			"family_name" => "varchar(64) NOT NULL ",
			"first_name" => "varchar(64) NOT NULL ",
			"comp_name" => "varchar(64) NOT NULL ",
			"iscomp" => "enum('yes','no') NOT NULL default 'yes' ",
			"email" => "varchar(255) NOT NULL ",
			"phone" => "varchar(20) NOT NULL ",
			"fax" => "varchar(20) NOT NULL ",
			"addr1" => "varchar(100) NOT NULL ",
			"addr2" => "varchar(100) NOT NULL ",
			"addr3" => "varchar(100) NOT NULL ",
			"zipcode" => "varchar(32) NOT NULL ",
			"city" => "varchar(64) NOT NULL ",
			"state" => "varchar(32) NOT NULL ",
			"country" => "char(2) NOT NULL ",
			"paiement_id" => "int(9) NOT NULL default '0' ",
			"product_id" => "int(9) NOT NULL default '0' "
			),
		"keys" => array(
			"PRIMARY" => "(id)"
			)
		),
	"paiement" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"id_client" => "int(11) NOT NULL default '0' ",
			"id_command" => "int(11) NOT NULL default '0' ",
			"label" => "varchar(255) NOT NULL default '0' ",
			"currency" => "enum('EUR','USD') NOT NULL default 'USD' ",
			"refund_amount" => "decimal(9,2) NOT NULL default '0.00' ",
			"paiement_cost" => "decimal(9,2) NOT NULL default '0.00' ",
			"paiement_total" => "decimal(9,2) NOT NULL default '0.00' ",
			"paiement_type" => "enum('online','cheque','wire','other','free') NOT NULL default 'online' ",
			"secpay_site" => "enum('none','paypal','worldpay') NOT NULL default 'none' ",
			"secpay_custom_id" => "int(11) NOT NULL default '0' ",
			"shopper_ip" => "varchar(16) NOT NULL default '0.0.0.0' ",
			"date" => "date NOT NULL default '0000-00-00' ",
			"time" => "time NOT NULL default '00:00:00' ",
			"valid_date" => "varchar(10) NOT NULL default '0000-00-00' ",
			"valid_time" => "varchar(8) NOT NULL default '00:00:00' ",
			"valid" => "enum('yes','no') NOT NULL default 'no' ",
			"new_account" => "enum('yes','no') NOT NULL default 'yes' "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"id" => "(id)"
			)
		),
	"pending_queries" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"adm_login" => "varchar(64) NOT NULL ",
			"domain_name" => "varchar(128) NOT NULL ",
			"date" => "varchar(16) NOT NULL default '0000-00-00 00:00' "
			),
		"keys" => array(
			"PRIMARY" => "(id)"
			)
		),
	"pop_access" => array(
		"vars" => array(
			"id" => "varchar(32) NOT NULL ",
			"uid" => "int(11) NOT NULL default '65534' ",
			"gid" => "int(11) NOT NULL default '65534' ",
			"home" => "varchar(255) NOT NULL ",
			"shell" => "varchar(255) NOT NULL ",
			"mbox_host" => "varchar(120) NOT NULL ",
			"crypt" => "varchar(50) NOT NULL ",
			"passwd" => "varchar(50) NOT NULL ",
			"active" => "int(11) NOT NULL default '1' ",
			"start_date" => "date NOT NULL default '0000-00-00' ",
			"expire_date" => "date NOT NULL default '0000-00-00' ",
			"quota_size" => "int(11) NOT NULL default '0' ",
			"type" => "varchar(20) NOT NULL default 'default' ",
			"memo" => "text NULL ",
			"du" => "bigint(20) NOT NULL default '0' ",
			"another_perso" => "varchar(5) NOT NULL default 'no' ",
			"redirect1" => "varchar(255) NULL ",
			"redirect2" => "varchar(255) NULL ",
			"localdeliver" => "varchar(10) NOT NULL default 'yes' ",
			"pop3_login_count" => "int(9) NOT NULL default '0' ",
			"pop3_transfered_bytes" => "int(14) NOT NULL default '0' ",
			"last_login" => "int(14) NOT NULL default '0' ",
			"imap_login_count" => "int(9) NOT NULL default '0' ",
			"imap_transfered_bytes" => "int(14) NOT NULL default '0' ",
			"iwall_protect" => "enum('yes','no') NOT NULL default 'no' ",
			"bounce_msg" => "text NOT NULL ",
			"spf_protect" => "enum('yes','no') NOT NULL default 'no' ",
			"clamav_protect" => "enum('yes','no') NOT NULL default 'no' ",
			"pass_next_req" => "varchar(128) NOT NULL ",
			"pass_expire" => "int(12) NOT NULL default '0' "
			),
		"keys" => array(
			"PRIMARY" => "(id,mbox_host)"
			)
		),
	"product" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"price_dollar" => "float(9,2) NOT NULL default '0.00' ",
			"price_euro" => "float(9,2) NOT NULL default '0.00' ",
			"name" => "varchar(255) NOT NULL ",
			"quota_disk" => "int(9) NOT NULL default '0' ",
			"nbr_email" => "int(9) NOT NULL default '0' ",
			"nbr_database" => "int(9) NOT NULL default '0' ",
			"bandwidth" => "int(9) NOT NULL default '0' ",
			"period" => "date NOT NULL default '0001-00-00' ",
			"allow_add_domain" => "enum('yes','no') NOT NULL default 'no' "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"id" => "(id)"
			)
		),
	"secpayconf" => array(
		"vars" => array(
			"unicrow" => "int(2) NOT NULL default '0' ",
			"use_paypal" => "enum('yes','no') NOT NULL default 'no' ",
			"paypal_rate" => "float(6,2) NOT NULL default '0.00' ",
			"paypal_flat" => "float(6,2) NOT NULL default '0.00' ",
			"paypal_autovalidate" => "enum('yes','no') NOT NULL default 'yes' ",
			"paypal_email" => "varchar(128) NOT NULL default 'palpay@gplhost.com' "
			),
		"keys" => array(
			"unicrow" => "(unicrow)"
			)
		),
	"smtp_logs" => array(
		"vars" => array(
			"id" => "int(11) NOT NULL auto_increment",
			"newmsg_id" => "bigint(20) NULL ",
			"bounce_qp" => "int(11) NULL ",
			"bytes" => "int(11) NOT NULL default '0' ",
			"sender_user" => "varchar(128) NOT NULL ",
			"sender_domain" => "varchar(128) NOT NULL ",
			"delivery_id" => "bigint(20) NULL ",
			"delivery_user" => "varchar(128) NOT NULL ",
			"delivery_domain" => "varchar(128) NOT NULL ",
			"delivery_success" => "enum('yes','no') NOT NULL default 'no' ",
			"time_stamp" => "int(14) NOT NULL default '0' ",
			"msg_id_text" => "varchar(128) NOT NULL ",
			"delivery_id_text" => "varchar(128) NOT NULL "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"delivery_id_text" => "(delivery_id_text)",
			"newmsg_id" => "(newmsg_id)",
			"bounce_qp" => "(bounce_qp)",
			"delivery_id_text_2" => "(delivery_id_text)",
			"sender_domain" => "(sender_domain)",
			"delivery_domain" => "(delivery_domain)"
			)
		),
	"subdomain" => array(
		"vars" => array(
			"id" => "int(12) NOT NULL auto_increment",
			"domain_name" => "varchar(64) NOT NULL ",
			"subdomain_name" => "varchar(64) NOT NULL ",
			"path" => "varchar(64) NOT NULL ",
			"webalizer_generate" => "varchar(8) NOT NULL default 'no' ",
			"ip" => "varchar(16) NOT NULL default 'default' ",
			"register_globals" => "enum('yes','no') NOT NULL default 'no' ",
			"login" => "varchar(16) NULL ",
			"pass" => "varchar(64) NULL ",
			"w3_alias" => "enum('yes','no') NOT NULL default 'no' ",
			"associated_txt_record" => "varchar(128) NOT NULL "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"unic_subdomain" => "(domain_name,subdomain_name)",
			"login" => "(login)"
			)
		),
	"whitelist" => array(
		"vars" => array(
			"id" => "int(9) NOT NULL auto_increment",
			"pop_user" => "varchar(32) NOT NULL ",
			"mbox_host" => "varchar(128) NOT NULL ",
			"mail_from_user" => "varchar(128) NULL ",
			"mail_from_domain" => "varchar(128) NULL ",
			"mail_to" => "varchar(128) NULL "
			),
		"keys" => array(
			"PRIMARY" => "(id)",
			"unicbox" => "(pop_user,mail_from_user,mail_from_domain,mbox_host)",
			"pop_user" => "(pop_user,mbox_host,mail_to)"
			)
		),
	"whois" => array(
		"vars" => array(
			"domain_name" => "varchar(128) NOT NULL ",
			"owner_id" => "int(16) NOT NULL default '0' ",
			"admin_id" => "int(16) NOT NULL default '0' ",
			"billing_id" => "int(16) NOT NULL default '0' ",
			"creation_date" => "date NOT NULL default '0000-00-00' ",
			"modification_date" => "date NOT NULL default '0000-00-00' ",
			"expiration_date" => "date NOT NULL default '0000-00-00' ",
			"registrar" => "enum('tucows','namebay') NOT NULL default 'tucows' ",
			"ns1" => "varchar(64) NOT NULL default 'ns1.example.com' ",
			"ns2" => "varchar(64) NOT NULL default 'ns2.example.com' ",
			"ns3" => "varchar(64) NULL ",
			"ns4" => "varchar(64) NULL ",
			"ns5" => "varchar(64) NULL ",
			"ns6" => "varchar(64) NULL "
			),
		"keys" => array(
			"PRIMARY" => "(domain_name)"
			)
		)
	)
);
?>
