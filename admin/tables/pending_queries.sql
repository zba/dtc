CREATE TABLE IF NOT EXISTS pending_queries (
  id int(9) NOT NULL auto_increment,
  adm_login varchar(64) NOT NULL default '',
  domain_name varchar(128) NOT NULL default '',
  date varchar(16) NOT NULL default '0000-00-00 00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
