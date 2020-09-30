<?php

/*******************************************************************
 * This will generate the appropriate files for maildrop operation *
 * Will do the following:              *****************************
 * 1) check to see if the /etc/courier/userdb exists
 * 2) fix perms for userdb
 * 3) create the userdb entry
 *    # userdb "<hostname>/<user>@<hostname>" set home=<path just before
Maildir> mail=<path just before Maildir> uid=<uid> gid=<uid>
 * 4) update the db using makeuserdb
 *    # makeuserdb 
 **********************************************/

function mail_account_generate_maildrop(){
	global $pro_mysql_domain_table;
	global $pro_mysql_admin_table;
	global $pro_mysql_subdomain_table;
	global $pro_mysql_pop_table;
	global $conf_nobody_user_id;
	global $conf_nobody_group_id;
	global $conf_nobody_user_name;

	global $conf_dtc_system_uid;
	global $conf_dtc_system_username;
	global $conf_dtc_system_gid;
	global $conf_dtc_system_groupname;

	global $console;

	global $conf_generated_file_path;
	global $conf_addr_mail_server;

	global $panel_type;

	if( file_exists("/etc/courier/userdb") ){
		$path_userdb="/etc/courier/userdb";
	}elseif( file_exists("/usr/local/etc/userdb") ){
		$path_userdb="/usr/local/etc/userdb";
	}elseif( file_exists("/usr/local/etc/authlib/userdb") ){
		$path_userdb="/usr/local/etc/authlib/userdb";
	}elseif( file_exists("/usr/local/etc/courier/userdb") ){
		$path_userdb="/usr/local/etc/courier/userdb";
	}else{
		$path_userdb="/etc/authlib/userdb";
	}

	if( file_exists("/usr/bin/maildirmake") ){
		$path_maildirmake="/usr/bin/maildirmake";
	}elseif( file_exists("/usr/local/bin/maildirmake") ){
		$path_maildirmake="/usr/local/bin/maildirmake";
	}else{
		$path_maildirmake="maildirmake";
	}

	if($panel_type == "cronjob"){
		echo "Making maildrop userdb file\n";
	}

	if( ! is_file($path_userdb) ){
		if($panel_type == "cronjob"){
			echo "Maildrop $path_userdb is not a file: cannot generate!\n";
		}else{
			$console .= "Maildrop $path_userdb is not a file: cannot generate!";
		}
		return false;
	}

	// This is a rewrite of this function that should be faster and better.
	$q2 = "SELECT name,domain_parking FROM $pro_mysql_domain_table";
	$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	$userdb_file = "";
	for($j=0;$j<$n2;$j++){
		$a2 = mysql_fetch_array($r2);
		$name = $a2["name"];

		// This handles domain parking delivery
		if($a2["domain_parking"] != "no-parking"){
			$query_dom_name = $a2["domain_parking"];
		}else{
			$query_dom_name = $name;
		}

		$q = "SELECT $pro_mysql_admin_table.path,$pro_mysql_domain_table.name,$pro_mysql_pop_table.id,$pro_mysql_pop_table.uid,$pro_mysql_pop_table.gid,$pro_mysql_pop_table.quota_size,$pro_mysql_pop_table.quota_couriermaildrop
		FROM $pro_mysql_admin_table,$pro_mysql_pop_table,$pro_mysql_domain_table
		WHERE $pro_mysql_admin_table.adm_login=$pro_mysql_domain_table.owner
		AND $pro_mysql_domain_table.name=$pro_mysql_pop_table.mbox_host
		AND $pro_mysql_domain_table.name='$query_dom_name'
		ORDER BY $pro_mysql_pop_table.id";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$boxpath = $a["path"]."/".$a["name"]."/Mailboxs/".$a["id"];
			$userdb_file .= $a["id"]."@".$a2["name"]."\t".'home='.$boxpath.'|mail='.$boxpath."|uid=".$a["uid"].'|gid='.$a["gid"].'|quota='.$a["quota_couriermaildrop"]."\n";
			$quota_maildrop=$a["quota_couriermaildrop"];

			$PATH = getenv('PATH');
			putenv("PATH=/usr/lib/courier-imap/bin:$PATH");
			system("/bin/mkdir -p $boxpath/Maildir");
			system("$path_maildirmake $boxpath/Maildir >/dev/null 2>&1");
			if($quota_maildrop!="0S,0C"){
				system("$path_maildirmake -q $quota_maildrop $boxpath/Maildir >/dev/null 2>&1");
			}
			putenv("PATH=$PATH");
			if($quota_maildrop=="0S,0C"){
				if(file_exists("$boxpath/Maildir/maildirsize")){
					system("rm $boxpath/Maildir/maildirsize");
				}
			}else{
				if($panel_type == "cronjob" && file_exists("$boxpath/Maildir/maildirsize")){
					chown("$boxpath/Maildir/maildirsize",$conf_dtc_system_username);
				}
			}

		}

	}

	// Write the file
	if(!is_writable($path_userdb)){
		$console .= "$path_userdb is not writable: please fix!";
		return;
	}
	$fp = fopen($path_userdb,"w+");
	if(!$fp){
		$console .= "Could not open $path_userdb in line ".__LINE__." file ".__FILE__;
		return;
	}
	if(fwrite($fp,$userdb_file) === FALSE){
		$console .= "Could not write $path_userdb in line ".__LINE__." file ".__FILE__;
		return;
	}
	$chmod_return = chmod ( $path_userdb, 0600 );
	fclose($fp);

	// Create the binary database
	if( file_exists("/usr/local/sbin/makeuserdb") ){
		system("/usr/local/sbin/makeuserdb -f " . $path_userdb);
	}else{
		system("/usr/sbin/makeuserdb -f " . $path_userdb);
	}
	return;
}

?>
