CREATE TABLE IF NOT EXISTS subdomain (
  id int(12) NOT NULL auto_increment,
  domain_name varchar(64) NOT NULL default '',
  subdomain_name varchar(64) NOT NULL default '',
  path varchar(64) NOT NULL default '',
  webalizer_generate varchar(8) NOT NULL default 'no',
  ip varchar(16) NOT NULL default 'default',
  register_globals enum('yes','no') NOT NULL default 'no',
  login varchar(16) default NULL,
  pass varchar(64) default NULL,
  w3_alias enum('yes','no') NOT NULL default 'no',
  associated_txt_record varchar(128) NOT NULL default '',
  generate_vhost enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (id),
  UNIQUE KEY unic_subdomain (domain_name,subdomain_name)
) TYPE=MyISAM
