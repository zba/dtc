CREATE TABLE IF NOT EXISTS vps_server (
  id int(11) NOT NULL auto_increment,
  hostname varchar(255) NOT NULL default '',
  location varchar(64) NOT NULL default '',
  soap_login varchar(64) NOT NULL default '',
  soap_pass varchar(64) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY hostname (hostname)
) TYPE=MyISAM;
