<?php

function remove_url_protocol($url){
	if(strstr($url,"http://")){
		return substr($url,7);
	}else if(strstr($url,"https://")){
		return substr($url,8);
	}else
		echo "ERROR: no protocol in distant mail server addr!";
	return false;
}

// db: add field dtc.backup: status enum('pending','done') default 'pending';

function get_remote_mail($a){
	$url = $a["server_addr"].'/dtc/list_domains.php?action=list_mx&login='.$a["server_login"].'&pass='.$a["server_pass"];
	while($retry < 3 && $flag == false){
		$lines = file ($url);
		$nline = sizeof($lines);
		if(strstr($lines[0],"<dtc_backup_mx_domain_list>") &&
			strstr($lines[$nline-1],"</dtc_backup_mx_domain_list>")){
			for($j=1;$j<$nline-1;$j++){
				$rcpthosts_file .= $lines[$j];
			}
			$flag = true;
		}
		$retry ++;
		if($flag == false)	sleep(5);
	}
	return $rcpthosts_file;
}

function get_remote_mail_domains(){
	global $pro_mysql_backup_table;
	global $conf_generated_file_path;
	global $console;

	// Get all domains from the servers for wich we act as backup MX
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='mail_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$retry = 0;
		$flag = false;
		$a = mysql_fetch_array($r);
		$u = remove_url_protocol($a["server_addr"]);
		if($u == false)	return false;
		$f = $conf_generated_file_path."/mail_domains.".$u;
		if($a["status"] == "pending" || !file_exists($f)){
			$console = "Getting mail domain list from ".$a["server_addr"]."/dtc/domainlist.php with login ".$a["server_login"]." and writting to disk...<br>";
			$remote_file = get_remote_mail($a);
			$fp = fopen($f,"w+");
			fwrite($fp,$remote_file);
			fclose($fp);
			$domain_list .= $remote_file;
		}else{
			$console = "Using mail domain list from cache of ".$a["server_addr"]."...<br>";
			$fp = fopen($f,"r");
			fseek($fp,0,SEEK_END);
			$size = ftell($fp);
			fseek($fp,0,SEEK_START);
			$domain_list .= fread($fp,$size);
			fclose($fp);
		}
	}
	return $domain_list;
}

?>
