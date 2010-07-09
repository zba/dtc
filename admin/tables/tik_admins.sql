CREATE TABLE IF NOT EXISTS tik_admins(
  id int(11) NOT NULL auto_increment,
  pseudo varchar(64) NOT NULL default '',
  realname varchar(64) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  available enum('yes','no') NOT NULL default 'yes',
  tikadm_pass varchar(255) NOT NULL default '',
  pass_next_req varchar(128) NOT NULL default '0',
  pass_expire int(12) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY pseudo (pseudo)
)TYPE=MyISAM
