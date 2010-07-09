CREATE TABLE IF NOT EXISTS usergroup(
  id int(11) unsigned NOT NULL auto_increment,
  UserName varchar(64) NOT NULL default '',
  GroupName varchar(64) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY UserName (UserName(32))
)TYPE=MyISAM
