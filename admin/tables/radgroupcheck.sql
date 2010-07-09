CREATE TABLE IF NOT EXISTS radgroupcheck(
  id int(11) unsigned NOT NULL auto_increment,
  GroupName varchar(64) NOT NULL default '',
  Attribute varchar(32) NOT NULL default '',
  op char(2) NOT NULL default '==',
  Value varchar(253) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY GroupName (GroupName(32))
)TYPE=MyISAM
