CREATE TABLE IF NOT EXISTS `pending_renewal` (
  `id` int(11) NOT NULL auto_increment,
  `adm_login` varchar(64) NOT NULL default '',
  `renew_date` date NOT NULL default '0000-00-00',
  `renew_time` time NOT NULL default '0000:00:00',
  `product_id` int(11) NOT NULL default '0',
  `renew_id` int(11) NOT NULL default '0',
  `heb_type` enum('shared', 'ssl', 'vps', 'server','ssl_renew','shared-upgrade') NOT NULL default 'shared',
  `pay_id` int(11) NOT NULL default '0',
  `country_code` varchar(4) NOT NULL default 'US',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
