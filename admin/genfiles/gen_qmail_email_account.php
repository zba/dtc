<?php

/**********************************************
 * Generate the following files:              *
 * /var/qmail/control/rcpthosts               *
 * domain.com                                 *
 *                                            *
 * /var/qmail/control/virtualdomains          *
 * domain.com:domain-com                      *
 *                                            *
 * /etc/poppasswd                             *
 * pop_login:crypted_password:real_login:path *
 *                                            *
 * /var/qmail/users/assign                    *
 * =domain-com-joe:nobody:888:888:path:::     *
 **********************************************/

function mail_account_generate_qmail(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_backup_table;

	global $console;

	global $conf_generated_file_path;
	global $conf_qmail_rcpthost_path;
	global $conf_qmail_virtualdomains_path;
	global $conf_qmail_assign_path;
	global $conf_qmail_poppasswd_path;

	$rcpthosts_file = "";
	$virtualdomains_file = "";
	$poppasswd_file = "";
	$assign_file = "";
	$more_rcpt = "";

	$query = "SELECT * FROM $pro_mysql_admin_table ORDER BY adm_login;";
	$result = mysql_query ($query)or die("Cannot execute query : \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){
		die("No account to generate");
	}

	for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result) or die ("Cannot fetch user-admin");
		$user_admin_name = $row["adm_login"];
		$user_admin_pass = $row["adm_pass"];
		$admin = fetchAdmin($user_admin_name,$user_admin_pass);
		if(($error = $admin["err"]) != 0){
			die("Error fetching admin : $error");
		}
		$info = $admin["info"];
		$data = $admin["data"];


		$nbr_domain = sizeof($data);
		for($j=0;$j<$nbr_domain;$j++){
			$domain = $data[$j];
			$domain_full_name = $domain["name"];
			$domain_qmail_name = strtr($domain_full_name, ".", "-");
			$rcpthosts_file .= "$domain_full_name\n";
			$virtualdomains_file .= "$domain_full_name:$domain_qmail_name\n";
			$more_rcpt .= "$domain_full_name\n";

			$emails = $domain["emails"];
			$nbr_boites = sizeof($emails);
			for($k=0;$k<$nbr_boites;$k++){
				$email = $emails[$k];
				$id = $email["id"];
				$qmail_id = strtr($id,".",":");
				$home = $email["home"];
				$passwdtemp = $email["passwd"];
				$passwd = crypt($passwdtemp);
				$poppasswd_file .= "$id@$domain_full_name:$passwd:nobody:$home\n";
				$assign_file .= "=$domain_qmail_name-$id:nobody:65534:65534:$home:::\n";
				//$console .= "=$domain_qmail_name-$id:nobody:65534:65534:$home:::\n";
			}
		}
	}

	// Get all domains from the servers for wich we act as backup MX
	$q = "SELECT * FROM $pro_mysql_backup_table WHERE type='mail_backup';";
	$r = mysql_query($q)or die("Cannot query $q ! line ".__FILE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$lines = file ('http://'.$a["server_addr"].'/list_domains.php?action=list_mx&login='.$a["server_login"].'&pass='.$a["server_pass"]);
		$nline = sizeof($lines);
		if(strstr($lines[0],"<dtc_backup_mx_domain_list>") &&
			strstr($lines[$nline-1],"</dtc_backup_mx_domain_list>")){
			for($j=1;$j<$nline-1;$j++){
				$rcpthosts_file .= $lines[$j];
			}
		}
	}

	$assign_file .= ".\n";
	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_rcpthost_path", "w");
	fwrite ($fp,$rcpthosts_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_virtualdomains_path", "w");
	fwrite ($fp,$virtualdomains_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_poppasswd_path", "w");
	fwrite ($fp,$poppasswd_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/$conf_qmail_assign_path", "w");
	fwrite ($fp,$assign_file);
	fclose($fp);

	$fp = fopen ( "$conf_generated_file_path/morercpthosts", "w");
	fwrite ($fp,$more_rcpt);
	fclose($fp);
}

?>
