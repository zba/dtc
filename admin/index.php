<?php
	
$panel_type="admin";
require_once("../shared/autoSQLconfig.php");
require_once("$dtcshared_path/dtc_lib.php");
// Admin include files
require_once("genfiles/genfiles.php");

require_once("authme.php");

include("inc/submit_root_querys.php");
include("inc/nav.php");
include("inc/dtc_config.php");
include("inc/draw_user_admin.php");

if(file_exists("dtcrm")){
	include("dtcrm/submit_to_sql.php");
	include("dtcrm/main.php");
	include("dtcrm/product_manager.php");
	include("inc/renewals.php");
	include("inc/graphs.php");
	include("inc/monitor.php");
	if( isset($_REQUEST["show_ip_pool_report"]) ){
		include("inc/ip_usage_report.php");
	}
}

$DONOT_USE_ROTATING_PASS="yes";

if(function_exists("skin_LayoutAdminPage")){
	skin_LayoutAdminPage();
}else{
	skin_LayoutAdminPage_Default();
}

?>
