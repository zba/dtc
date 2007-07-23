CREATE TABLE IF NOT EXISTS vps_stats (
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  vps_server_hostname varchar(255) NOT NULL,
  vps_xen_name varchar(64) NOT NULL,
  last_run int(11) default NULL,
  cputime_last float default NULL,
  cpu_usage float default NULL,
  network_in_last bigint(22) default NULL,
  network_out_last bigint(22) default NULL,
  network_in_count bigint(22) default NULL,
  network_out_count bigint(22) default NULL,
  diskio_last bigint(22) default NULL,
  diskio_count bigint(22) default NULL,
  swapio_last bigint(22) default NULL,
  swapio_count bigint(22) default NULL,
  PRIMARY KEY (`month`,`year`,vps_server_hostname,vps_xen_name)
)TYPE=MyISAM;
