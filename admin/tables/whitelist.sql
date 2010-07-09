CREATE TABLE IF NOT EXISTS whitelist(
  id int(9) NOT NULL auto_increment,
  pop_user varchar(32) NOT NULL default '',
  mbox_host varchar(128) NOT NULL default '',
  mail_from_user varchar(128) default NULL,
  mail_from_domain varchar(128) default NULL,
  mail_to varchar(128) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY unicbox (pop_user,mail_from_user,mail_from_domain,mbox_host),
  UNIQUE KEY pop_user (pop_user,mbox_host,mail_to)
)TYPE=MyISAM
