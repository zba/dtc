CREATE TABLE product (
  id int(11) NOT NULL auto_increment,
  price_dolar varchar(9) NOT NULL default '',
  price_euro varchar(9) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  quota_disk int(9) NOT NULL default '0',
  nbr_email int(9) NOT NULL default '0',
  nbr_database int(9) NOT NULL default '0',
  bandwidth int(9) NOT NULL default '0',
  period date NOT NULL default '0001-00-00',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM
