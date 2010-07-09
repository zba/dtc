CREATE TABLE IF NOT EXISTS affiliate_payments(
  id int(11) NOT NULL auto_increment,
  adm_login varchar(64) NOT NULL default '',
  order_id int(11) NOT NULL,
  kickback DECIMAL(10,5) NOT NULL,
  date_paid date NULL,
  PRIMARY KEY  (id)
)TYPE=MyISAM
