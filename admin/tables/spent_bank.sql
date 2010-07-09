CREATE TABLE IF NOT EXISTS spent_bank(
  id int(11) NOT NULL auto_increment,
  acct_name varchar(128) NOT NULL default '',
  id_company int(11) NOT NULL default '0',
  acct_number varchar(64) NOT NULL default '0',
  swift varchar(128) NOT NULL default '0',
  sort_code varchar(128) NOT NULL default '',
  bank_addr varchar(255) NOT NULL default '',
  currency_type varchar(10) NOT NULL default 'EUR',
  PRIMARY KEY  (id)
)TYPE=MyISAM
