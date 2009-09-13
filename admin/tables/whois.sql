CREATE TABLE IF NOT EXISTS whois (
  domain_name varchar(128) NOT NULL default '',
  owner_id int(16) NOT NULL default '0',
  admin_id int(16) NOT NULL default '0',
  billing_id int(16) NOT NULL default '0',
  teck_id int(16) NOT NULL default '0',
  creation_date date NOT NULL default '0000-00-00',
  modification_date date NOT NULL default '0000-00-00',
  expiration_date date NOT NULL default '0000-00-00',
  registrar enum('tucows','namebay') NOT NULL default 'tucows',
  ns1 varchar(64) NOT NULL default 'ns1.example.com',
  ns2 varchar(64) NOT NULL default 'ns2.example.com',
  ns3 varchar(64) default NULL,
  ns4 varchar(64) default NULL,
  ns5 varchar(64) default NULL,
  ns6 varchar(64) default NULL,
  PRIMARY KEY  (domain_name)
) TYPE=MyISAM;
