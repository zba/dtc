CREATE TABLE IF NOT EXISTS dedicated_ip (
  `id` int(11) NOT NULL auto_increment,
  `dedicated_server_hostname` varchar(255) NOT NULL default '',
  `ip_addr` varchar(16) NOT NULL default '',
  `available` enum('yes','no') NOT NULL default 'yes',
  `rdns_addr` varchar(255) NOT NULL default 'gplhost.com',
  `rdns_regen` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (id),
  UNIQUE KEY ip_addr (ip_addr)
) TYPE=MyISAM;
