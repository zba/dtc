<?php

// This script contains upgrades of your dtc DATABSE in case
// you are upgrading from previous DTC version

$sql_updates[] = array(
	"dbversion" => "10000",
	"sql" => "
ALTER TABLE cron_job
		ADD unicrow INT DEFAULT '1' NOT NULL,
		UNIQUE (unicrow);
ALTER TABLE config
		ADD use_ssl ENUM('yes','no') DEFAULT 'no' NOT NULL,
		ADD unicrow INT DEFAULT '1' NOT NULL,
		ADD UNIQUE (unicrow),
		ADD db_version int(11) NOT NULL default '10000',
		ADD use_nated_vhost enum('yes','no') NOT NULL default 'no',
		ADD nated_vhost_ip varchar(16) NOT NULL default '192.168.0.2';");

$sql_updates[] = array(
	"dbversion" => "10001",
	"sql" => "
DROP TABLE clients;

CREATE TABLE IF NOT EXISTS clients (
  id int(9) NOT NULL auto_increment,
  is_company enum('yes','no') NOT NULL default 'no',
  company_name varchar(64) default NULL,
  familyname varchar(64) NOT NULL default '',
  christname varchar(64) NOT NULL default '',
  addr1 varchar(100) NOT NULL default '',
  addr2 varchar(100) default NULL,
  addr3 varchar(64) default NULL,
  city varchar(64) NOT NULL default '',
  zipcode varchar(32) NOT NULL default '0',
  state varchar(32) default NULL,
  country char(2) NOT NULL default '',
  phone varchar(20) NOT NULL default '0',
  fax varchar(20) default NULL,
  email varchar(255) NOT NULL default '',
  special_note blob,
  dollar decimal(9,2) NOT NULL default '0.00',
  disk_quota_mb int(9) NOT NULL default '0',
  bw_quota_per_month_gb int(9) NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

DROP TABLE commande;

CREATE TABLE IF NOT EXISTS commande (
  id mediumint(9) NOT NULL auto_increment,
  id_client varchar(100) NOT NULL default '0',
  domain_name varchar(255) NOT NULL default '',
  quantity varchar(10) NOT NULL default '',
  price_devise enum('EUR','USD') NOT NULL default 'EUR',
  price varchar(255) NOT NULL default '',
  paiement_method enum('cb','cheque','wire','other','free') NOT NULL default 'cb',
  date date NOT NULL default '0000-00-00',
  expir date NOT NULL default '0000-00-00',
  valid varchar(16) NOT NULL default 'yes',
  product_id int(9) NOT NULL default '0',
  payment_id int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

ALTER TABLE ftp_access
	ADD login_count INT( 11 ) NOT NULL ,
	ADD last_login DATETIME NOT NULL ,
	ADD dl_bytes INT( 14 ) NOT NULL ,
	ADD ul_bytes INT( 14 ) NOT NULL ,
	ADD dl_count INT( 14 ) NOT NULL ,
	ADD ul_count INT( 14 ) NOT NULL ;

ALTER TABLE domain
	ADD whois enum('here','away','linked') NOT NULL default 'away',
	ADD hosting enum('here','away') NOT NULL default 'here',
	ADD du_stat bigint(20) NOT NULL default '0' ;

ALTER TABLE subdomain
	ADD register_global enum('yes','no') NOT NULL default 'no',
	ADD login varchar(16) default NULL,
	ADD pass varchar(64) default NULL,
	ADD w3_alias enum('yes','no') NOT NULL default 'non' ;

ALTER TABLE admin
	ADD bandwidth_per_month_mb INT ( 11 ) NOT NULL default '100',
	ADD expire DATE DEFAULT '0000-00-00' NOT NULL ;

ALTER TABLE cron_job
	ADD lock_flag enum('finished','inprogress') NOT NULL default 'finished';

ALTER TABLE pop_access
	ADD pop3_login_count int(9) NOT NULL default '0',
	ADD pop3_transfered_bytes int(14) NOT NULL default '0',
	ADD last_login int(14) NOT NULL default '0';

UPDATE config SET dbversion='10001' WHERE 1;

");

/*
$sql_updates[] = array(
	"dbversion" => "10002",
	"sql" => "
UPDATE config SET dbversion='10002' WHERE 1;
");
*/

?>
