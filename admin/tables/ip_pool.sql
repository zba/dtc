CREATE TABLE IF NOT EXISTS ip_pool (
  `id` int(11) NOT NULL auto_increment,
  `location` varchar(255) NOT NULL default '',
  `ip_addr` varchar(16) NOT NULL default '',
  `netmask` varchar(16) NOT NULL default '',
  `gateway` varchar(16) NOT NULL default '',
  `broadcast` varchar(16) NOT NULL default '',
  `dns` varchar(16) NOT NULL default '',
  `zone_type` enum('support_ticket','ip_per_ip','ip_per_ip_cidr','one_zonefile') default 'one_zonefile',
  `custom_part` text NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY ip_addr (ip_addr)
) TYPE=MyISAM;
