CREATE TABLE IF NOT EXISTS ftp_logs(
  ui bigint(20) NOT NULL auto_increment,
  username tinytext,
  filename text,
  size bigint(20) default NULL,
  host tinytext,
  ip tinytext NULL,
  command tinytext NULL,
  command_time tinytext,
  local_time datetime default NULL,
  success char(1) default NULL,
  PRIMARY KEY  (ui)
)TYPE=MyISAM
