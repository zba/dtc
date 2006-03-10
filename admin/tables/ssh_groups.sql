CREATE TABLE IF NOT EXISTS `nss_groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `group_name` varchar(30) NOT NULL default '',
  `status` char(1) default 'A',
  `group_password` varchar(64) NOT NULL default 'x',
  `gid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`)
) TYPE=MyISAM;
