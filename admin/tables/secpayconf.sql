CREATE TABLE IF NOT EXISTS secpayconf (
  unicrow int(2) NOT NULL default '0',
  currency_symbol varchar(16) NOT NULL default '$',
  currency_letters varchar(16) NOT NULL default 'USD',

  use_paypal enum('yes','no') NOT NULL default 'no',
  paypal_rate float(6,2) NOT NULL default '0.00',
  paypal_flat float(6,2) NOT NULL default '0.00',
  paypal_autovalidate enum('yes','no') NOT NULL default 'yes',
  paypal_email varchar(128) NOT NULL default 'palpay@gplhost.com',
  paypal_sandbox enum('yes','no') NOT NULL default 'no',
  paypal_sandbox_email varchar(128) NOT NULL default '',
  paypal_validate_with enum('total','mc_gross') NOT NULL default 'total',
  use_paypal_recurring enum('yes','no') NOT NULL default 'no',

  use_moneybookers enum('yes','no') NOT NULL default 'no',
  moneybookers_rate float(6,2) NOT NULL default '0.00',
  moneybookers_flat float(6,2) NOT NULL default '0.00',
  moneybookers_autovalidate enum('yes','no') NOT NULL default 'yes',
  moneybookers_email varchar(128) NOT NULL default 'palpay@gplhost.com',
  moneybookers_sandbox enum('yes','no') NOT NULL default 'no',
  moneybookers_sandbox_email varchar(128) NOT NULL default '',
  moneybookers_validate_with enum('total','mc_gross') NOT NULL default 'total',
  moneybookers_secret_word varchar(128) NOT NULL default '',

  use_enets enum('yes','no') NOT NULL default 'no',
  use_enets_test enum('yes','no') NOT NULL default 'yes',
  enets_mid_id varchar(255) NOT NULL default '',
  enets_test_mid_id varchar(255) NOT NULL default '',
  enets_rate float(6,2) NOT NULL default '0.00',

  use_maxmind enum('yes','no') NOT NULL default 'no',
  maxmind_login varchar(255) NOT NULL default '',
  maxmind_license_key varchar(255) NOT NULL default '',

  use_webmoney enum('yes','no') NOT NULL default 'no',
  webmoney_license_key varchar(255) NOT NULL default '',
  webmoney_wmz varchar(255) NOT NULL default '',

  accept_cheques enum('yes','no') NOT NULL default 'no',
  cheques_flat_fees float(6,2) NOT NULL default '0.00',
  cheques_to_label varchar(255) NOT NULL default '',
  cheques_send_address text NOT NULL default '',
  accept_wiretransfers enum('yes','no') NOT NULL default 'no',
  wiretransfers_flat_fees float(6,2) NOT NULL default '0.00',
  wiretransfers_bank_details text NOT NULL default '',

  UNIQUE KEY unicrow (unicrow)
) TYPE=MyISAM
