CREATE TABLE IF NOT EXISTS mailinglist (
  id int(9) NOT NULL auto_increment,
  domain varchar(128) NOT NULL default '',
  name varchar(32) NOT NULL default '',
  owner varchar(255) NOT NULL default '',
  PRIMARY KEY (id)
) TYPE=MyISAM;
