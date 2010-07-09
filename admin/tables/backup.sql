CREATE TABLE IF NOT EXISTS backup(
  id int(9) NOT NULL auto_increment,
  server_addr varchar(128) NOT NULL default '',
  server_login varchar(128) NOT NULL default '',
  server_pass varchar(128) NOT NULL default '',
  type enum('grant_access','mail_backup','dns_backup','backup_ftp_to','trigger_changes','trigger_mx_changes') NOT NULL default 'grant_access',
  status enum('pending','done') NOT NULL default 'pending',
  PRIMARY KEY  (id)
)TYPE=MyISAM
