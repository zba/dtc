CREATE TABLE IF NOT EXISTS nameservers (
  id int(9) NOT NULL auto_increment,
  owner varchar(64) NOT NULL default '',
  domain_name varchar(128) NOT NULL default '',
  subdomain varchar(128) NOT NULL default '',
  ip varchar(16) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY domain_name (domain_name,subdomain)
) TYPE=MyISAM;
