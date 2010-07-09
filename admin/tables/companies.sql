CREATE TABLE IF NOT EXISTS companies(
  id int(12) NOT NULL auto_increment,
  name varchar(128) NOT NULL default '',
  address text NOT NULL default '',
  country varchar(128) NOT NULL default '',
  registration_number varchar(128) NOT NULL default '',
  vat_number varchar(128) NOT NULL default '',
  vat_rate decimal(9,2) NOT NULL default '0.00',
  logo_path varchar(255) NOT NULL default 'none',
  text_after text NOT NULL default '',
  footer text NOT NULL default '',
  in_europe enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (id)
)TYPE=MyISAM
