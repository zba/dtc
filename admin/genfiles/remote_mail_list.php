<?php

// db: add field dtc.backup: status enum('pending','done') default 'pending';

function get_remote_mail($a){
	global $conf_use_ssl;
	global $console;
	global $keep_mail_generate_flag;
	
	$flag = false;
	$retry = 0;
	$rcpthosts_file = ""; //init variable here
	$url = $a["server_addr"].'/dtc/list_domains.php?action=list_mx&login='.$a["server_login"].'&pass='.$a["server_pass"];
	while($retry < 3 && $flag == false){
		$a_vers = explode(".",phpversion());
		if(strncmp("https://",$a["server_addr"],strlen("https://")) == 0 && $a_vers[0] <= 4 && $a_vers[1] < 3){
			// Todo: use exec(lynx -source) because HTTPS will not work !
			$lines = "";
			$console .= "<br>Using lynx -source on ".$a["server_addr"]." with login ".$a["server_login"]."...";
			$result = exec("lynx -source \"$url\"",$lines,$return_val);
			$nline = sizeof($lines);
			if(strstr($lines[0],"<dtc_backup_mx_domain_list>") &&
				strstr($lines[$nline-1],"</dtc_backup_mx_domain_list>")){
				for($j=1;$j<$nline-1;$j++){
					$rcpthosts_file .= $lines[$j];
				}
				$flag = true;
				$console .= "success!<br>\n";
			}
//			$rcpthosts_file .= "";
		}else{
			$console .= "<br>Using php internal file() function on ".$a["server_addr"]." with login ".$a["server_login"]."...";
			$lines = file ($url);
			$nline = sizeof($lines);
			if(strstr($lines[0],"<dtc_backup_mx_domain_list>") &&
				strstr($lines[$nline-1],"</dtc_backup_mx_domain_list>")){
				for($j=1;$j<$nline-1;$j++){
					$rcpthosts_file .= $lines[$j];
				}
				$flag = true;
				$console .= "success!<br>\n";
			}
		}
		$retry ++;
		if($flag == false){
			$console .= "failed: delaying in 3s!<br>\n";
			sleep(3);
		}
	}
	if($flag == false){
		$keep_mail_generate_flag = "yes";
		return false;
	}
	else		return $rcpthosts_file;
}

function get_remote_mail_domains(){
	global $pro_mysql_backup_table;
	global $conf_generated_file_path;
	global $console;

	$domain_list = "";

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
			$console .= "Getting mail domain list from ".$a["server_addr"]."/dtc/domainlist.php with login ".$a["server_login"]." and writting to disk...";
			$remote_file = get_remote_mail($a);
			if($remote_file != false){
				$fp = fopen($f,"w+");
				fwrite($fp,$remote_file);
				fclose($fp);

				// Check file is not zero lenght
				$fp = fopen($f,"r");
				fseek($fp,0,SEEK_END);
				$size = ftell($fp);
				fclose($fp);

				if ($size > 0){
					$domain_list .= $remote_file;
					$q2 = "UPDATE $pro_mysql_backup_table SET status='done' WHERE id='".$a["id"]."';";
					$r2 = mysql_query($q2)or die("Cannot query $q2 ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
					$console .= "ok!<br>";
					$flag = true;
				}else{
					$console .= "wrong! File is empty!<br>";
				}
			}else{
				$console .= "failed!<br>";
			}
		}
		if($flag == false){
			if (file_exists($f))
                        {
			$console .= "Using mail domain list from cache of ".$a["server_addr"]."...<br>";
			$fp = fopen($f,"r");
			fseek($fp,0,SEEK_END);
			$size = ftell($fp);
			if ($size > 0)
			{
				fseek($fp,0);
				$domain_list .= fread($fp,$size);
			} else {
				$console .= "File [" . $f . "] is empty<br>";
			}
			fclose($fp);
			} else {
                                $console .= "Cache file not present, probably fa
iled to read from remote host";
			}
		}
	}
	return $domain_list;
}

?>
