CREATE TABLE IF NOT EXISTS secpayconf (
  unicrow int(2) NOT NULL default '0',
  use_paypal enum('yes','no') NOT NULL default 'no',
  paypal_rate float(6,2) NOT NULL default '0.00',
  paypal_flat float(6,2) NOT NULL default '0.00',
  paypal_autovalidate enum('yes','no') NOT NULL default 'yes',
  paypal_email varchar(128) NOT NULL default 'palpay@gplhost.com',
  paypal_sandbox enum('yes','no') NOT NULL default 'no',
  paypal_sandbox_email varchar(128) NOT NULL default ''
  UNIQUE KEY unicrow (unicrow)
) TYPE=MyISAM
