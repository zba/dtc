CREATE TABLE IF NOT EXISTS nas(
  id int(10) NOT NULL auto_increment,
  nasname varchar(128) NOT NULL default '',
  shortname varchar(32) NOT NULL default '',
  type varchar(30) NOT NULL default 'other',
  ports int(5) NULL,
  secret varchar(60) NOT NULL default 'secret',
  server varchar(64) default NULL,
  community varchar(50) default NULL,
  description varchar(200) NOT NULL default 'RADIUS Client',
  PRIMARY KEY  (id),
  KEY nasname (nasname)
)TYPE=MyISAM
