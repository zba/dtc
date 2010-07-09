CREATE TABLE IF NOT EXISTS fetchmail(
  id int(9) NOT NULL auto_increment,
  domain_user varchar(64) NOT NULL default '',
  domain_name varchar(128) NOT NULL default '',
  pop3_email varchar(64) NOT NULL default '',
  pop3_server varchar(128) NOT NULL default '',
  pop3_login varchar(128) NOT NULL default '',
  pop3_pass varchar(128) NOT NULL default '',
  checkit enum('yes','no') NOT NULL default 'yes',
  autodel enum('0','1','2','3','7','14','21') NOT NULL default '7',
  mailbox_type enum('POP3','IMAP4','MSN','HOTMAIL','YAHOO','GMAIL') NOT NULL default 'POP3',
  PRIMARY KEY  (id),
  UNIQUE KEY domain_user (domain_user,domain_name,pop3_server,pop3_login),
  UNIQUE KEY pop3_email (pop3_email)
)TYPE=MyISAM
