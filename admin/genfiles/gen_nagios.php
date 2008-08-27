<?php

function nagios_generate(){
	global $pro_mysql_vps_table, $pro_mysql_vps_ip_table;

	$text = "
		define timeperiod{
			timeperiod_name dtc-allday
			alias           All day long
			sunday          00:00-24:00
			monday          00:00-24:00
			tuesday         00:00-24:00
			wednesday       00:00-24:00
			thursday        00:00-24:00
			friday          00:00-24:00
			saturday        00:00-24:00
		}

		define command{
			command_name    dtc-host-notify-by-email
			command_line    /usr/bin/printf \"%b\" \"***** Nagios *****\\n\\nNotification Type: \$NOTIFICATIONTYPE$\\nHost: \$HOSTALIAS\$\\nState: \$HOSTSTATE\$ for \$HOSTDURATION\$\\nAddress: \$HOSTADDRESS\$\\nInfo:\\n\\n\$HOSTOUTPUT\$\\n\\nDate/Time: \$LONGDATETIME\$\\n\\nACK by: \$HOSTACKAUTHOR\$\\nComment: \$HOSTACKCOMMENT\$\\n\" | mail -s \"** \$NOTIFICATIONTYPE\$ alert \$NOTIFICATIONNUMBER\$ - \$HOSTALIAS\$ host is \$HOSTSTATE\$ **\" \$CONTACTEMAIL\$
		}
		
		define command{
			command_name    dtc-notify-by-email
			command_line    /usr/bin/printf \"%b\" \"***** Nagios  *****\\n\\nNotification Type: \$NOTIFICATIONTYPE\$\\n\\nService: \$SERVICEDESC\$\\nHost: \$HOSTALIAS\$\\nState: \$SERVICESTATE\$ for \$SERVICEDURATION\$\\nAddress: \$HOSTADDRESS\$\\n\\nInfo:\\n\\n\$SERVICEOUTPUT\$\\n\\nDate/Time: \$LONGDATETIME\$\\n\\nACK by: \$SERVICEACKAUTHOR\$\\nComment: \$SERVICEACKCOMMENT\$\\n\" | mail -s \"** \$NOTIFICATIONTYPE\$ alert \$NOTIFICATIONNUMBER\$ - \$HOSTALIAS\$/\$SERVICEDESC\$ is \$SERVICESTATE\$ **\" \$CONTACTEMAIL\$                                                                                          
		}

		define service{
			name                            dtc-monitored-service
			active_checks_enabled           1       
			passive_checks_enabled          1
			parallelize_check               1
			obsess_over_service             1
			check_freshness                 0
			notifications_enabled           1
			event_handler_enabled           1
			flap_detection_enabled          1
			failure_prediction_enabled      1
			process_perf_data               1
			retain_status_information       1
			retain_nonstatus_information    1
			notification_interval           0
			is_volatile                     0
			check_period                    dtc-allday
			normal_check_interval           5
			retry_check_interval            1
			max_check_attempts              7
			notification_period             dtc-allday
			notification_options            w,u,c,r
			contact_groups                  everyone
			register                        0
		}

		define host {
			name                            dtc-monitored-host
			notifications_enabled           1
			event_handler_enabled           1
			flap_detection_enabled          1
			failure_prediction_enabled      1
			process_perf_data               1
			retain_status_information       1
			retain_nonstatus_information    1
			check_command                   dtc-check-host-alive
			max_check_attempts              7
			notification_interval           120
			notification_period             dtc-allday
			notification_options            d,r
			register                        0
		}

	";

	$q = "SELECT * FROM $pro_mysql_vps_table INNER JOIN $pro_mysql_vps_ip_table ON $pro_mysql_vps_table.vps_xen_name = $pro_mysql_vps_ip_table.vps_xen_name AND $pro_mysql_vps_table.vps_server_hostname = $pro_mysql_vps_ip_table.vps_server_hostname;";

	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

	$monitoring_emails = array();

	$ping_machines = array();
	$ssh_machines = array();
	$http_machines = array();
	$smtp_machines = array();
	$pop3_machines = array();
	$imap4_machines = array();
	$ftp_machines = array();

	while ($line = mysql_fetch_array($r,MYSQL_ASSOC)) {

		if ( !defined($line['monitoring_email']) || ! $line['monitoring_email'] ) continue;

		if (	$line['monitor_ping'] != 'yes' &&
			$line['monitor_ssh'] != 'yes' &&
			$line['monitor_http'] != 'yes' &&
			$line['monitor_smtp'] != 'yes' &&
			$line['monitor_pop3'] != 'yes' &&
			$line['monitor_imap4'] != 'yes' &&
			$line['monitor_ftp'] != 'yes' ) continue;

		if ( ! in_array($line['monitoring_email'],$monitoring_emails) ) {
			$text .= "
				define contact {
					contact_name {$line['monitoring_email']}
					alias {$line['monitoring_email']}
					email {$line['monitoring_email']}
					service_notification_period     dtc-allday
					host_notification_period        dtc-allday
					service_notification_options    w,c,r
					host_notification_options       d,r
					service_notification_commands   dtc-notify-by-email
					host_notification_commands      dtc-host-notify-by-email
				}
			";
			$monitoring_emails[] = $line['monitoring_email'];
		}

		$text .= "
			define host {
				use dtc-monitored-host
				host_name {$line['ip_addr']}
				alias {$line['vps_xen_name']}.{$line['vps_server_hostname']}
				address {$line['ip_addr']}
				contacts {$line['monitoring_email']}
			}
		";

		if ($line['monitor_ping'] != 'yes') $ping_machines[] = $line['ip_addr'];
		if ($line['monitor_ssh'] != 'yes') $ssh_machines[] = $line['ip_addr'];
		if ($line['monitor_http'] != 'yes') $http_machines[] = $line['ip_addr'];
		if ($line['monitor_smtp'] != 'yes') $smtp_machines[] = $line['ip_addr'];
		if ($line['monitor_pop3'] != 'yes') $pop3_machines[] = $line['ip_addr'];
		if ($line['monitor_imap4'] != 'yes') $imap4_machines[] = $line['ip_addr'];
		if ($line['monitor_ftp'] != 'yes') $ftp_machines[] = $line['ip_addr'];

	}

	if ($ping_machines) $text .= "
			define service {
				service_description             Ping (DTC-configured)
				check_command                 check_ping!500.0,20%!1000.0,60%
				use                             dtc-monitored-service
				host_name ".implode(",",$ping_machines)."
			}
		";
	if ($http_machines) $text .= "
			define service {
			service_description             HTTP (DTC-configured)
			check_command                   check_http
				use                             dtc-monitored-service
				host_name ".implode(",",$http_machines)."
			}
		";
	if ($ssh_machines) $text .= "
			define service {
				service_description             SSH (DTC-configured)
				check_command                   check_ssh
				use                             dtc-monitored-service
				host_name ".implode(",",$ssh_machines)."
			}
		";
	if ($smtp_machines) $text .= "
			define service {
				service_description             SMTP (DTC-configured)
				check_command                   check_smtp
				use                             dtc-monitored-service
				host_name ".implode(",",$smtp_machines)."
			}
		";
	if ($pop3_machines) $text .= "
			define service {
				service_description             POP3 (DTC-configured)
				check_command                   check_pop
				use                             dtc-monitored-service
				host_name ".implode(",",$pop3_machines)."
			}
		";
	if ($imap4_machines) $text .= "
			define service {
				service_description             IMAP (DTC-configured)
				check_command                   check_imap
				use                             dtc-monitored-service
				host_name ".implode(",",$imap4_machines)."
			}
		";
	if ($ftp_machines) $text .= "
			define service {
				service_description             FTP (DTC-configured)
				check_command                   check_ftp
				use                             dtc-monitored-service
				host_name ".implode(",",$ftp_machines)."
			}
		";

	return $text;
}

?>