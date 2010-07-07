CREATE TABLE IF NOT EXISTS custom_fld (
  `id` int(9) unsigned NOT NULL auto_increment,
  `varname` varchar(255) collate latin1_bin NOT NULL,
  `question` varchar(255) collate latin1_bin NOT NULL,
  `widgettype` varchar(255) collate latin1_bin NOT NULL,
  `widgetvalues` varchar(255) collate latin1_bin NOT NULL,
  `widgetdisplay` varchar(255) collate latin1_bin NOT NULL,
  `widgetorder` int(9) NOT NULL,
  `mandatory` enum('yes','no') collate latin1_bin NOT NULL default 'no',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
