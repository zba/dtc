CREATE TABLE `xfer_stat` (
  `username` tinytext,
  `filename` text,
  `size` bigint(20) default NULL,
  `host` tinytext,
  `ip` tinytext,
  `command` tinytext,
  `command_time` tinytext,
  `local_time` datetime default NULL,
  `success` char(1) default NULL,
  `ui` bigint(20) NOT NULL auto_increment,
  PRIMARY KEY  (`ui`)
) TYPE=MyISAM;