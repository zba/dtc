CREATE TABLE IF NOT EXISTS `ssl_ips` (
  `id` int(12) NOT NULL auto_increment,
  `ip_addr` varchar(16) NOT NULL default '',
  `adm_login` varchar(64) NOT NULL default '',
  `available` enum('yes','no') NOT NULL default 'yes',
  `expire` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY 	p_addr (`ip_addr`)
) TYPE=MyISAM;