<?php
// this is the file that generates the SSH account details
function ssh_account_generate()
{
	// need to genereate ./etc/shadow ./etc/passwd and ./etc/group for account in chroot
	global $pro_mysql_domain_table;
        global $pro_mysql_admin_table;
        global $pro_mysql_subdomain_table;

        global $conf_db_version;
        global $conf_unix_type;

        global $conf_apache_vhost_path;
        global $conf_generated_file_path;
        global $conf_dtcshared_path;
        global $conf_dtcadmin_path;
        global $conf_dtcclient_path;
        global $conf_dtcdoc_path;
        global $conf_dtcemail_path;
        global $conf_main_site_ip;
        global $conf_use_multiple_ip;
        global $conf_site_addrs;
        global $conf_php_library_path;
        global $conf_php_additional_library_path;
        global $conf_administrative_site;

	$num_generated_vhosts=0;
        $query = "SELECT * FROM $pro_mysql_domain_table WHERE 1 ORDER BY name;";
        $result = mysql_query ($query)or die("Cannot execute query \"$query\"");
        $num_rows = mysql_num_rows($result);

        if($num_rows < 1){
                die("No account to generate : database has to contain AT LEAST one domain name");
        }

	for($i=0;$i<$num_rows;$i++){
                $row = mysql_fetch_array($result) or die ("Cannot fetch user");
		$web_name = $row["name"];
                $web_owner = $row["owner"];
		echo "SSH: $web_name";
		$query2 = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$web_name' AND ip='default'
ORDER BY subdomain_name;";
		$result2 = mysql_query ($query2)or die("Cannot execute query \"$query2\"");
		$num_rows2 = mysql_num_rows($result2);

		for ($j=0; $j < $num_rows2; $j++)
		{
			$subdomain = mysql_fetch_array($result2) or die ("Cannot fetch user");
                        $web_subname = $subdomain["subdomain_name"];
			// now for each of these subdomains, we need to edit the etc/shadow etc and add the users

			echo "SSH: $web_subname";
		}
	}
}
?>
