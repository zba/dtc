You will find here all alter tables needed before upgrading DTC.
This is temporary before we have an automated table upgrade system.

Listed here are changed needed to be passed thru one by one to your
database. Do it begining from older version, continuing to the newest,
one by one. Donot alter tables if you don't have them already, DTC
will create them when installing/upgrading.

Issue the following command to alter:
mysql -uroot -D[YOUR_DTC_DB_NAME] -p

and then type the alters one by one.

Upgrading from pre-0.11.1-0:
--------------------------
ALTER TABLE cron_job ADD unicrow INT DEFAULT '1' NOT NULL;
ALTER TABLE cron_job ADD UNIQUE (unicrow);
ALTER TABLE config ADD use_ssl ENUM('yes','no') DEFAULT 'no' NOT NULL;
ALTER TABLE config ADD unicrow INT DEFAULT '1' NOT NULL;
ALTER TABLE config ADD UNIQUE (unicrow);
ALTER TABLE config ADD db_version int(11) NOT NULL default '10000';
ALTER TABLE config ADD use_nated_vhost enum('yes','no') NOT NULL default 'no';
ALTER TABLE config ADD nated_vhost_ip varchar(16) NOT NULL default '192.168.0.2';

Upgrading from pre-0.11.1-31:
-----------------------------
DROP TABLE clients;
DROP TABLE commande;

ALTER TABLE ftp_access ADD login_count INT( 11 ) NOT NULL ,
ADD ast_login DATETIME NOT NULL ,
ADD dl_bytes INT( 14 ) NOT NULL ,
ADD ul_bytes INT( 14 ) NOT NULL ,
ADD dl_count INT( 14 ) NOT NULL ,
ADD ul_count INT( 14 ) NOT NULL ;

ALTER TABLE domain
ADD whois enum('here','away','linked') NOT NULL default 'away',
ADD hosting enum('here','away') NOT NULL default 'here' ;

ALTER TABLE subdomain
login varchar(16) default NULL,
pass varchar(64) default NULL ;
