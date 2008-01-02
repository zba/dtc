CREATE TABLE IF NOT EXISTS vps_server_lists (
  id int(11) NOT NULL auto_increment,
  hostname varchar(255) NOT NULL default '',
  list_name varchar(128) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY hostname (hostname,list_name)
) TYPE=MyISAM;
