CREATE TABLE IF NOT EXISTS commande (
  id mediumint(9) NOT NULL auto_increment,
  id_client varchar(100) NOT NULL default '0',
  domain_name varchar(255) NOT NULL default '',
  quantity varchar(10) NOT NULL default '',
  price_devise enum('EUR','USD') NOT NULL default 'EUR',
  price varchar(255) NOT NULL default '',
  paiement_method enum('cb','cheque','wire','other','free') NOT NULL default 'cb',
  date date NOT NULL default '0000-00-00',
  expir date NOT NULL default '0000-00-00',
  valid varchar(16) NOT NULL default 'yes',
  product_id int(9) NOT NULL default '0',
  payment_id int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
