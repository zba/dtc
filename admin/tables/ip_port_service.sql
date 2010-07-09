CREATE TABLE IF NOT EXISTS ip_port_service(
  id int(11) NOT NULL auto_increment,
  ip varchar(16) NOT NULL ,
  port varchar(16) NOT NULL ,
  service varchar(64) NOT NULL ,
  PRIMARY KEY  (id)
)TYPE=MyISAM
