CREATE TABLE IF NOT EXISTS mailinglist(
  id int(9) NOT NULL auto_increment,
  domain varchar(255) NOT NULL default '',
  name varchar(64) NOT NULL default '',
  owner varchar(255) NOT NULL default '',
  spammode enum('yes', 'no') NOT NULL default 'no',
  webarchive enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (id)
)TYPE=MyISAM
