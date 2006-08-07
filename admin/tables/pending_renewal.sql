CREATE TABLE IF NOT EXISTS `vps` (
  `id` int(11) NOT NULL auto_increment,
  `adm_login` varchar(64) NOT NULL default '',
  `renew_date` date NOT NULL default '0000-00-00',
  `renew_time` time NOT NULL default '0000:00:00',
  `product_id` int(11) NOT NULL default '0',
  `renew_id` int(11) NOT NULL default '0',
  `heb_type` enum('shared', 'ssl', 'vps', 'server') NOT NULL default 'shared',
  `pay_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
