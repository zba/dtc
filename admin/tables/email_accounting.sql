CREATE TABLE IF NOT EXISTS email_accounting(
  id int(11) NOT NULL auto_increment,
  domain_name varchar(128) NOT NULL default '',
  smtp_trafic int(14) unsigned NOT NULL default '0',
  pop_trafic int(14) unsigned NOT NULL default '0',
  imap_trafic int(14) unsigned NOT NULL default '0',
  month int(2) NOT NULL default '0',
  year int(4) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY domain_name (domain_name,month,year)
)TYPE=MyISAM
