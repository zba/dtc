<?php

// Since version 0.26.3, DTC has a field for VPS & Dedicated bandwidth
// This short script iterates on all admins and reassign the correct
// bandwidth to all of them, depending on the assigned product

// Simply drop this file in the dtc/admin folder and run it with this command:
// php reassing_bandwidth_for_vps_and_dedicated.php

$panel_type="cronjob";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");
// Admin include files
require_once("genfiles/genfiles.php");

include("inc/submit_root_querys.php");
include("inc/nav.php");
include("inc/dtc_config_strings.php");
include("inc/dtc_config.php");
include("inc/draw_user_admin.php");

include("dtcrm/submit_to_sql.php");
include("dtcrm/main.php");
include("dtcrm/product_manager.php");

$q = "SELECT * FROM $pro_mysql_admin_table WHERE 1";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
	$a = mysql_fetch_array($r);
	// Check the admin's VPS
	$q2 = "SELECT * FROM $pro_mysql_vps_table WHERE owner='".$a["adm_login"]."'";
	$r2 = mysql_query($q2)or die("Cannot query ".$q2." line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	for($j=0;$j<$n2;$j++){
		$a2 = mysql_fetch_array($r2);
		$q3 = "SELECT * FROM $pro_mysql_product_table WHERE id='".$a2["product_id"]."';";
		$r3 = mysql_query($q3)or die("Cannot query ".$q3." line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		$n3 = mysql_num_rows($r3);
		if($n3 != 1){
			die("No product for the VPS of admin ".$a["adm_login"]);
		}
		$a3 = mysql_fetch_array($r3);
		$q4 = "UPDATE $pro_mysql_vps_table SET bandwidth_per_month_gb='".$a3["bandwidth"]."' WHERE id='".$a2["id"]."';";
		$r4 = mysql_query($q4)or die("Cannot query ".$q4." line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		echo $q4."\n";
	}

	// Check the admin's Dedicated
	$q2 = "SELECT * FROM $pro_mysql_dedicated_table WHERE owner='".$a["adm_login"]."'";
	$r2 = mysql_query($q2)or die("Cannot query ".$q2." line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	for($j=0;$j<$n2;$j++){
		$a2 = mysql_fetch_array($r2);
		$q3 = "SELECT * FROM $pro_mysql_product_table WHERE id='".$a2["product_id"]."';";
		$r3 = mysql_query($q3)or die("Cannot query ".$q3." line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		$n3 = mysql_num_rows($r3);
		if($n3 != 1){
			die("No product for the dedicated of admin ".$a["adm_login"]);
		}
		$a3 = mysql_fetch_array($r3);
		$q4 = "UPDATE $pro_mysql_dedicated_table SET bandwidth_per_month_gb='".$a3["bandwidth"]."' WHERE id='".$a2["id"]."';";
		$r4 = mysql_query($q4)or die("Cannot query ".$q4." line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
		echo $q4."\n";
	}
}

?>