CREATE TABLE IF NOT EXISTS custom_fld(
  id int(9) unsigned NOT NULL auto_increment,
  varname varchar(255) NOT NULL default '',
  question varchar(255) NOT NULL default '',
  widgettype varchar(255) NOT NULL default '',
  widgetvalues varchar(255) NOT NULL default '',
  widgetdisplay varchar(255) NOT NULL default '',
  widgetorder int(9) NOT NULL default '0',
  mandatory enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (id)
)TYPE=MyISAM
