CREATE TABLE IF NOT EXISTS paiement (
  id int(11) NOT NULL auto_increment,
  id_client int(11) NOT NULL default '0',
  id_command int(11) NOT NULL default '0',

  label varchar(255) NOT NULL default '0',
  currency enum('EUR','USD') NOT NULL default 'USD',
  refund_amount decimal(9,2) NOT NULL default '0',

  paiement_cost decimal(9,2) NOT NULL default '0',
  paiement_total decimal(9,2) NOT NULL default '0',
  paiement_method enum('online','cheque','wire','other','free') NOT NULL default 'online',
  secure_paiement_site enum('none','paypal','worldpay') NOT NULL default 'none',
  secure_custom_id int(11) NOT NULL default '0',
  shopper_ip varchar(16) NOT NULL default '0.0.0.0';

  date date NOT NULL default '0000-00-00',
  valid enum('yes','no') NOT NULL default 'no',

  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
