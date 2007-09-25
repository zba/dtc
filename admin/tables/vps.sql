CREATE TABLE IF NOT EXISTS `vps` (
  `id` int(11) NOT NULL auto_increment,
  `owner` varchar(64) NOT NULL default '',
  `vps_server_hostname` varchar(255) NOT NULL default '',
  `vps_xen_name` varchar(64) NOT NULL default '',
  `start_date` date NOT NULL default '0000-00-00',
  `expire_date` date NOT NULL default '0000-00-00',
  `hddsize` int(9) NOT NULL default '1',
  `ramsize` int(9) NOT NULL default '48',
  `bandwidth_per_month_gb` int(9) NOT NULL default '1',
  `product_id` int(9) NOT NULL default '0',
  `operatingsystem` varchar(64) NOT NULL default 'debian',
  `installed` enum('yes','no') NOT NULL default 'no',
  `bsdkernel` enum('normal','install') NOT NULL default 'normal',
  PRIMARY KEY  (id),
  UNIQUE KEY `vps_server_hostname` (vps_server_hostname,vps_xen_name)
) TYPE=MyISAM;
