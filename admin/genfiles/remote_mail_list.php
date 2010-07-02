<?php

// db: add field dtc.backup: status enum('pending','done') default 'pending';
// $recipients = 1 (means email list)
// $recipients = 0 (means domain list)
function get_remote_mail($a,$recipients){
	global $conf_use_ssl;
	global $console;
	global $keep_mail_generate_flag;
	global $panel_type;
	
	$flag = false;
	$retry = 0;
	$rcpthosts_file = ""; //init variable here
	if ($recipients == 1){
		$url = $a["server_addr"].'/dtc/list_domains.php?action=list_mx_recipients&login='.$a["server_login"].'&pass='.$a["server_pass"];
	} else {
		$url = $a["server_addr"].'/dtc/list_domains.php?action=list_mx&login='.$a["server_login"].'&pass='.$a["server_pass"];
	}
	while($retry < 3 && $flag == false){
		$a_vers = explode(".",phpversion());
		if($a_vers[0] <= 4 && $a_vers[1] < 3){
			// Todo: use exec(lynx -source) because HTTPS will not work !
			$lines = "";
			if( $panel_type == "cronjob"){
				echo "\nUsing lynx -source on ".$a["server_addr"]." with login ".$a["server_login"]."...";
			}else{
				$console .= "<br>Using lynx -source on ".$a["server_addr"]." with login ".$a["server_login"]."...";
			}
			$result = exec("lynx -source \"$url\"",$lines,$return_val);
			$nline = sizeof($lines);
			if(
				(strstr($lines[0],"<dtc_backup_mx_domain_list>") &&
				strstr($lines[$nline-2],"</dtc_backup_mx_domain_list>")
				) ||
				( $recipients == 1 && strstr($lines[0],"<dtc_backup_mx_recipient_list>") &&
				strstr($lines[$nline-2],"</dtc_backup_mx_recipient_list>")
				)
			){
				for($j=1;$j<$nline-1;$j++){
					$rcpthosts_file .= $lines[$j]."\n";
				}
				$flag = true;
				if( $panel_type == "cronjob"){
					echo "success!\n";
				}else{
					$console .= "success!<br>";
				}
			}else{
				if( $panel_type == "cronjob"){
					echo "Failed!\n";
				}else{
					$console .= "Failed!<br>";
				}
			}

			
//			$rcpthosts_file .= "";
		}else{
			if( $panel_type == "cronjob"){
				echo "\nUsing php dtc_HTTPRequest class on ".$a["server_addr"]." with login ".$a["server_login"]."...";
			}else{
				$console .= "<br>Using php dtc_HTTPRequest class on ".$a["server_addr"]." with login ".$a["server_login"]."...";
			}
			$httprequest = new dtc_HTTPRequest("$url");
			$lines = $httprequest->DownloadToStringArray();
			if($lines != FALSE){
				$nline = sizeof($lines);
				if(
					(strstr($lines[0],"<dtc_backup_mx_domain_list>") &&
					strstr($lines[$nline-1],"</dtc_backup_mx_domain_list>")
					) ||
					( $recipients == 1 && strstr($lines[0],"<dtc_backup_mx_recipient_list>") &&
					strstr($lines[$nline-1],"</dtc_backup_mx_recipient_list>")
					)
				){
					for($j=1;$j<$nline-1;$j++){
						$rcpthosts_file .= $lines[$j]."\n";
					}
					$flag = true;
					if( $panel_type == "cronjob"){
						echo "success!\n";
					}else{
						$console .= "success!<br>";
					}
				}
			}
		}
		$retry ++;
		if($flag == false){
			if( $panel_type == "cronjob"){
				echo "failed: delaying in 3s!\n";
			}else{
				$console .= "failed: delaying in 3s!<br>";
			}
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
	return get_remote_mail_domains_internal(0);
}

function get_remote_mail_recipients(){
	return get_remote_mail_domains_internal(1);
}

// $recipients = 1 (means email list)
// $recipients = 0 (means domain list)
function get_remote_mail_domains_internal($recipients){
	global $panel_type;
	global $pro_mysql_backup_table;
	global $conf_generated_file_path;
	global $console;

	$domain_list = "";
	$recipient_list = "";

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
		$f_recipients = $conf_generated_file_path."/mail_recipients.".$u;
		if($a["status"] == "pending" || !file_exists($f)){
			if ($recipients == 1){
				if( $panel_type == "cronjob"){
					echo "Getting mail recipient list from ".$a["server_addr"]."/dtc/list_domains.php with login ".$a["server_login"]." and writting to disk.\n";
				}else{
					$console .= "Getting mail recipient list from ".$a["server_addr"]."/dtc/list_domains.php with login ".$a["server_login"]." and writting to disk.\n";
				}
			} else {
				if( $panel_type == "cronjob"){
					echo "Getting mail domain list from ".$a["server_addr"]."/dtc/list_domains.php with login ".$a["server_login"]." and writting to disk.\n";
				}else{
					$console .= "Getting mail domain list from ".$a["server_addr"]."/dtc/list_domains.php with login ".$a["server_login"]." and writting to disk.<br>";
				}
			}
			$remote_file = get_remote_mail($a, 0);
			if($remote_file != false){
				$fp = fopen($f,"w+");
				fwrite($fp,$remote_file);
				fclose($fp);

				// Check file is not zero lenght
				$fp = fopen($f,"r");
				fseek($fp,0,SEEK_END);
				$size = ftell($fp);
				fclose($fp);

				//now grab the recipients for these remote MX
				$remote_file_recipients = get_remote_mail($a, 1);

				if (! $remote_file_recipients){
					//since we couldn't get the remote file, we need to relay for all emails
					//TODO loop through each line, and prepend @
					$domain_list = explode ("\n", $remote_file);
					$remote_file_recipients = "";
					foreach($domain_list as $domain){
						if (isset($domain) && strlen($domain) > 0) {
							$remote_file_recipients .= "@" . $domain . "\n";
						}
					}
				}
				$fp = fopen($f_recipients,"w+");
				fwrite($fp,$remote_file_recipients);
				fclose($fp);

				// Check file is not zero lenght
				$fp = fopen($f_recipients,"r");
				fseek($fp,0,SEEK_END);
				$size = ftell($fp);
				fclose($fp);

				if ($size > 0){
					$domain_list .= $remote_file;
					$recipient_list .= $remote_file_recipients;
					$q2 = "UPDATE $pro_mysql_backup_table SET status='done' WHERE id='".$a["id"]."';";
					$r2 = mysql_query($q2)or die("Cannot query $q2 ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
					if( $panel_type == "cronjob"){
						echo "ok!\n";
					}else{
						$console .= "ok!<br>";
					}
					$flag = true;
				}else{
					if( $panel_type == "cronjob"){
						echo "wrong! File is empty!\n";
					}else{
						$console .= "wrong! File is empty!<br>";
					}
				}
			}else{
				if( $panel_type == "cronjob"){
					echo "failed!\n";
				}else{
					$console .= "failed!<br>";
				}
			}
		}
		if($flag == false){
			$f_domains = $f;
			if ($recipients == 1)
			{
				$f = $f_recipients;
			}
			if (file_exists($f) || $recipients == 1){
				if ($recipients == 1){
					if( $panel_type == "cronjob"){
						echo "Using mail recipient list from cache of ".$a["server_addr"]."...\n";
					}else{
						$console .= "Using mail recipient list from cache of ".$a["server_addr"]."...<br>";
					}
				} else {
					if( $panel_type == "cronjob"){
						echo "Using mail recipient list from cache of ".$a["server_addr"]."...\n";
					}else{
						$console .= "Using mail recipient list from cache of ".$a["server_addr"]."...<br>";
					}
				}
				//if our recipient file doesn't exist, but our domains one does
				if (!file_exists($f) && $recipients == 1 && file_exists($f_domains)) 
				{
					$f = $f_domains;
				}
				
				$fp = fopen($f,"r");
				fseek($fp,0,SEEK_END);
				$size = ftell($fp);
				if ($size > 0){
					fseek($fp,0);
					if ($recipients == 1)
					{
						if ($f == $f_domains)
						{
							//we need to generate a fake recipient file for now
							$domain_list .= fread($fp,$size);
							$domains_array = explode("\n", $domain_list);
							foreach($domains_array as $domain)
							{
								if (isset($domain) && strlen($domain) > 0){
									$recipient_list .= "@" . $domain . "\n";
								}
							}
						} else { 
							//we have a real recipient file to read here
							$recipient_list .= fread($fp,$size);
						}
					} else {
						$domain_list .= fread($fp,$size);
					}
				} else {
					$console .= "File [" . $f . "] is empty<br>\n";
				}
				fclose($fp);
			} else {
				if( $panel_type == "cronjob"){
					echo "Cache file not present, probably failed to read from remote host\n";
				}else{
					$console .= "Cache file not present, probably failed to read from remote host<br>";
				}
			}
		}
	}
	if ($recipients == 1)
	{
		return $recipient_list;
	} else {
		return $domain_list;
	}
}

?>
