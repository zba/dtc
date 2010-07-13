CREATE TABLE IF NOT EXISTS cron_job(
  unicrow int(11) NOT NULL default '1',
  last_cronjob timestamp(14) NOT NULL default '0000-00-00',
  qmail_newu enum('yes','no') NOT NULL default 'no',
  restart_qmail enum('yes','no') NOT NULL default 'no',
  reload_named enum('yes','no') NOT NULL default 'no',
  restart_apache enum('yes','no') NOT NULL default 'no',
  gen_vhosts enum('yes','no') NOT NULL default 'no',
  gen_named enum('yes','no') NOT NULL default 'no',
  gen_reverse enum('yes','no') NOT NULL default 'no',
  gen_fetchmail enum('yes','no') NOT NULL default 'no',
  gen_qmail enum('yes','no') NOT NULL default 'no',
  gen_webalizer enum('yes','no') NOT NULL default 'no',
  gen_backup enum('yes','no') NOT NULL default 'no',
  gen_ssh enum('yes','no') NOT NULL default 'no',
  gen_nagios enum('yes','no') NOT NULL default 'no',
  lock_flag enum('inprogress','finished') NOT NULL default 'finished',
  UNIQUE KEY unicrow (unicrow)
)MAX_ROWS = 1 TYPE=MyISAM
