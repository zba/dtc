#
# Table structure for table `ftp_accounting`
#

CREATE TABLE `ftp_accounting` (
  `id` int(14) NOT NULL auto_increment,
  `sub_domain` varchar(50) NOT NULL default '',
  `transfer` int(14) unsigned NOT NULL default '0',
  `last_run` int(14) NOT NULL default '0',
  `month` int(4) NOT NULL default '0',
  `year` int(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM
