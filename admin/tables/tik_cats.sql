CREATE TABLE IF NOT EXISTS tik_cats(
  id int(11) NOT NULL auto_increment,
  catname varchar(64) NOT NULL default '',
  catdescript varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
)TYPE=MyISAM
