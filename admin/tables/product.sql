CREATE TABLE IF NOT EXISTS product (
  id int(11) NOT NULL auto_increment,
  price_dollar varchar(9) NOT NULL default '',
  price_euro varchar(9) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  quota_disk int(9) NOT NULL default '0',
  memory_size int(9) NOT NULL default '48',
  nbr_email int(9) NOT NULL default '0',
  nbr_database int(9) NOT NULL default '0',
  bandwidth int(9) NOT NULL default '0',
  period date NOT NULL default '0001-00-00',
  allow_add_domain enum('yes','no','check') NOT NULL default 'no',
  heb_type enum('shared','ssl','vps','server') NOT NULL default 'shared',
  renew_prod_id int(11) NOT NULL default '0',
  private enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM
