CREATE TABLE IF NOT EXISTS spent_moneyout (
  id int(11) NOT NULL auto_increment,
  id_company_spending int(11) NOT NULL default '0',
  id_provider int(11) NOT NULL default '0',
  payment_total decimal(9,2) NOT NULL default '0',
  payment_type enum('none','credit_card','wire_transfer','paypal','check','cash') NOT NULL default 'none',
  label varchar(128) NOT NULL default '',
  expenditure_type int(11) NOT NULL default '0',
  invoice_date date NOT NULL default '0000-00-00',
  paid_date date NOT NULL default '0000-00-00',
  time time NOT NULL default '00:00:00',
  vat_rate decimal(9,2) NOT NULL default '0.00',
  vat_total decimal(9,2) NOT NULL default '0',
  bank_acct_id int(11) NOT NULL default '0',
  amount decimal(9,2) NOT NULL default '0.00',
  currency_type varchar(10) NOT NULL default 'EUR',
  PRIMARY KEY (id)
) TYPE=MyISAM;
