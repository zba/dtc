ALTER TABLE cron_job ADD unicrow INT DEFAULT '1' NOT NULL;
ALTER TABLE cron_job ADD UNIQUE (unicrow);
ALTER TABLE config ADD use_ssl ENUM('yes','no') DEFAULT 'no' NOT NULL;
ALTER TABLE config ADD unicrow INT DEFAULT '1' NOT NULL;
ALTER TABLE config ADD UNIQUE (unicrow);
ALTER TABLE config ADD db_version int(11) NOT NULL default '10000';
ALTER TABLE config ADD use_nated_vhost enum('yes','no') NOT NULL default 'no';
ALTER TABLE config ADD nated_vhost_ip varchar(16) NOT NULL default '192.168.0.2';
