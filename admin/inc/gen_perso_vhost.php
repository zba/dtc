<?php

// Generation du fichier de vhost apache correspondant aux acounts de MySql-Qmail

function perso_vhost_generate(){
	global $perso_mysql_host;
	global $perso_mysql_login;
	global $perso_mysql_pass;
	global $perso_mysql_db;
	global $perso_mysql_table;
	global $perso_apache_vhost_path;
	global $site_ip;
	global $console;
	// Recuperations des infos

	$query = "SELECT login,homedir,hostname,vhost_ip FROM $perso_mysql_table WHERE another_perso='yes' AND hostname='anotherlight.com' ORDER BY hostname,login";
	$result = mysql_query ($query)or die("Cannot execute query \"$query\"");
	$num_rows = mysql_num_rows($result);

	if($num_rows < 1){
		die("No account to generate");
	}

	$vhost_file = "";
	for($i=0;$i<$num_rows;$i++){
	        $row = mysql_fetch_array($result) or die ("Cannot fetch user");
	        $qm_id = $row["login"];
	        $qm_home = $row["homedir"];
	        $qm_mbox_host = $row["hostname"];
			$qm_vhost_ip = $row["vhost_ip"];

	        // Insirer ici une virification de la syntaxe pour le sous-dommain

	        $home_letter = sscanf ($qm_id,"%c");

        $vhost_file .= "<VirtualHost $qm_vhost_ip>
        ServerName $qm_id.anotherlight.com
        DocumentRoot $qm_home
        DirectoryIndex index.htm index.html index.php4 index.php
</VirtualHost>
";
	}
	$console .= "$num_rows vhost genere !<br>";

	// Ecriture du fichier

	$filep = fopen("$perso_apache_vhost_path", "w+");
	if( $filep == NULL){
		die("Cannot open file for writting");
	}
	fwrite($filep,$vhost_file);
	fclose($filep);
	return true;
}

?>
