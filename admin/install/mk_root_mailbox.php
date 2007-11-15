#!/usr/bin/env php

<?php

        $base_path = dirname(__FILE__);

	if ($_SERVER["argc"] < 1) {
		print "Syntax Error: ".$_SERVER["argv"][0]." <root domain>\n";
		exit;
	} 

	$root_domain = $_SERVER["argv"][1];

	require($base_path.'/../../shared/cyradm.php');
	require($base_path.'/../../shared/cyrus.php');

	# login to cyradm
	$cyr_conn = new cyradm;
	$error=$cyr_conn -> imap_login();
	if ($error!=0){
		die ("imap_login Error $error");
	}
	$result=$cyr_conn->createmb("user/root" . "@".$root_domain);
?>
