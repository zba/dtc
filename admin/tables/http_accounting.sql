#
# Table structure for table `http_accounting`
#
CREATE TABLE IF NOT EXISTS http_accounting (
  id int(14) NOT NULL auto_increment,
  vhost varchar(50) NOT NULL default '',
  bytes_sent bigint(14) unsigned NOT NULL default '0',
  bytes_receive bigint(14) unsigned NOT NULL default '0',
  count_hosts int(12) NOT NULL default '0',
  count_visits int(12) NOT NULL default '0',
  count_status_200 int(12) NOT NULL default '0',
  count_status_404 int(12) NOT NULL default '0',
  count_impressions int(18) NOT NULL default '0',
  last_run int(14) NOT NULL default '0',
  month int(4) NOT NULL default '0',
  year int(4) NOT NULL default '0',
  domain varchar(50) NOT NULL default '',
  PRIMARY KEY (id),
  KEY month (month,year,vhost)
) TYPE=MyISAM;
