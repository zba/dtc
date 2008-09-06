<?php

if(isset($_REQUEST["action"]) && ($_REQUEST["action"] == "set_dedicated_ip_rdns")){
	checkLoginPass($adm_login,$adm_pass);
	if(!isIP($_REQUEST["ip_addr"])){
		$submit_err = _("This is not a correct IP line ") .__LINE__. _(" file ") .__FILE__;
	}else{
		if(!isHostnameOrIP($_REQUEST["rdns"])){
			$submit_err = _("This is not a correct hostname or IP line ") .__LINE__. _(" file ") .__FILE__;
		}else{
			$q = "SELECT $pro_mysql_dedicated_ips_table.ip_addr
			FROM $pro_mysql_dedicated_ips_table,$pro_mysql_dedicated_table
			WHERE $pro_mysql_dedicated_ips_table.ip_addr='".$_REQUEST["ip_addr"]."'
			AND $pro_mysql_dedicated_ips_table.dedicated_server_hostname=$pro_mysql_dedicated_table.server_hostname
			AND $pro_mysql_dedicated_table.owner='".$adm_login."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				$submit_err = _("This IP doesn't bellong to you, you can't change it's RDNS entry!");
			}else{
				$q = "UPDATE $pro_mysql_dedicated_ips_table SET rdns_addr='".$_REQUEST["rdns"]."',rdns_regen='yes' WHERE ip_addr='".$_REQUEST["ip_addr"]."';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				updateUsingCron("gen_named='yes',reload_named='yes'");
			}
		}
	}
}

?>