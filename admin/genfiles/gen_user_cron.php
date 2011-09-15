<?php

function user_cron_generate() {
	global $conf_generated_file_path;
	global $pro_mysql_user_cron_table;
	global $console;

	$filename=$conf_generated_file_path.'/dtc-user-cron';
	$console.="Generating $filename : ";

	$q = "SELECT * FROM $pro_mysql_user_cron_table;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$f = "# /etc/cron.d/dtc-user/cron\n\n";

	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if(substr($a["minute"],0,2) == "is"){
			$minute = substr($a["minute"],2);
		}else{
			$minute = $a["minute"];
		}
		if(substr($a["hour"],0,2) == "is"){
			$hour = substr($a["hour"],2);
		}else{
			$hour = $a["hour"];
		}
		$f .= $minute." ".$hour." ".$a["day_of_month"]." * ".$a["dow"].
			" dtc wget -O - '".escapeshellarg("http://".$a["subdomain_name"].".".$a["domain_name"].$a["uri"])."' 2>&1 >/dev/null\n";
	}
	if (touch($filename)) {
		$console.="Done!\n";
	}else{
		$console.="Failed!\n";
		return false;
	}
	file_put_contents($filename,$f);
	$console.="Number of cron jobs entries generated: ".$n."\n";
	updateUsingCron("gen_user_cron='no'");
}
?>
