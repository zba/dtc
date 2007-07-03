CREATE TABLE IF NOT EXISTS `mailalias` (
  `autoinc` int(12) NOT NULL auto_increment,
  `id` varchar(32) NOT NULL default '',
  `domain_parent` varchar(50) collate latin1_bin NOT NULL,
  `delivery_group` blob NOT NULL,
  `active` int(11) NOT NULL default '1',
  `start_date` date NOT NULL default '0000-00-00',
  `expire_date` date NOT NULL default '0000-00-00',
  `bounce_msg` text collate latin1_bin NOT NULL,
  PRIMARY KEY  (`autoinc`)
) TYPE=MyISAM ;
