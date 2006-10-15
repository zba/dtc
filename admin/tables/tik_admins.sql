CREATE TABLE IF NOT EXISTS tik_admins (
  `id` int(11) NOT NULL auto_increment,
  `pseudo` varchar(64) NOT NULL default '',
  `realname` varchar(64) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `available` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
