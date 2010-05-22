<?php

function fetchmail_generate() {
	global $conf_generated_file_path;
	global $console;

	$filename=$conf_generated_file_path.'/fetchmailrc';
	$console.="Generating $filename : ";
	if (touch($filename)) {
		$console.="Done!\n";
	}else{
		$console.="Failed!";
		return false;
	}

	$result=mysql_query("SELECT * FROM fetchmail");
	$num=0;    
	$fetchline="";
	while ($row=mysql_fetch_assoc($result)) {
		/* Yes, only pop3 yet. Must specify it, auto is *sloooow* */
		$fetchline.="poll ${row['pop3_server']} proto POP3\n";
		$fetchline.="qvirtual \"MFY\"\n";
		$fetchline.="envelope \"Delivered-To\"\n";
		/* Unfortunately there is no such option in fetchmail to keep mails for X days, or something, so it must be done with
		other tools. I think its safer to keep messages on remote server by default */
		$fetchline.="user ${row['pop3_login']} with password ${row['pop3_pass']} to ${row['domain_user']}@${row['domain_name']}\n";
		$fetchline.="keep\n";

		$num++;
	}
	file_put_contents($filename,$fetchline);
	$console.="Number of fetchmailrc entries generated: ".$num."\n";
	updateUsingCron("gen_fetchmail='no'");
}
?>
