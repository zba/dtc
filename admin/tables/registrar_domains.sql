CREATE TABLE IF NOT EXISTS registrar_domains (
  id int(12) NOT NULL auto_increment,
  tld varchar(64) NOT NULL default '.com',
  registrar varchar(128) NOT NULL default 'webnic',
  price decimal(15,2) NOT NULL default '100.00',
  UNIQUE tld (tld),
  PRIMARY KEY (id)
) TYPE=MyISAM
