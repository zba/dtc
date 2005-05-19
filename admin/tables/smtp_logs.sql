CREATE TABLE  IF NOT EXISTS smtp_logs (
  id int(11) NOT NULL auto_increment,
  newmsg_id bigint(20) default NULL,
  bounce_qp int(11) default NULL,
  bytes int(11) NOT NULL default '0',
  sender_user varchar(128) NOT NULL default '',
  sender_domain varchar(128) NOT NULL default '',
  delivery_id bigint(20) default NULL,
  delivery_user varchar(128) NOT NULL default '',
  delivery_domain varchar(128) NOT NULL default '',
  delivery_success enum('yes','no') NOT NULL default 'no',
  delivery_id_text varchar(255) NOT NULL default '',
  time_stamp int(14) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY bounce_qp (bounce_qp),
  UNIQUE KEY newmsg_id (newmsg_id),
  KEY sender_domain (sender_domain),
  KEY delivery_domain (delivery_domain)
) TYPE=MyISAM;
