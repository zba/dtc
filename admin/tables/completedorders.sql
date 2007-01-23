CREATE TABLE IF NOT EXISTS completedorders (
  id int(12) NOT NULL auto_increment,
  id_client int(12) NOT NULL default '0',
  domain_name varchar(255) NOT NULL default '',
  quantity int(12) NOT NULL default '0',
  date date NOT NULL default '0000-00-00',
  product_id int(12) NOT NULL default '0',
  payment_id int(12) NOT NULL default '0',
  download_pass varchar(64) NOT NULL default 'none',
  country_code varchar(4) NOT NULL default 'US',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
