CREATE TABLE IF NOT EXISTS admin (
  adm_login varchar(64) NOT NULL default '',
  adm_pass varchar(16) NOT NULL default '',
  path varchar(128) NOT NULL default '/web/disk4',
  max_email int(12) NOT NULL default '3',
  max_ftp int(12) NOT NULL default '3',
  quota int(11) NOT NULL default '50',
  bandwidth_per_month_mb INT ( 11 ) NOT NULL default '100',
  expire date NOT NULL default '0000-00-00',
  id_client int(9) NOT NULL default '0',
  PRIMARY KEY  (adm_login),
  pass_next_req varchar(128) NOT NULL default '0',
  pass_expire int(12) NOT NULL default '0',
  UNIQUE KEY adm_login (adm_login),
  UNIQUE KEY path (path)
)TYPE=MyISAM
