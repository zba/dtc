CREATE TABLE IF NOT EXISTS invoicing(
  id int(12) NOT NULL auto_increment,
  customer_country_code char(2) NOT NULL default '',
  service_country_code char(2) NOT NULL default '',
  company_id int(12) NOT NULL default '0',
  PRIMARY KEY  (id)
)TYPE=MyISAM
