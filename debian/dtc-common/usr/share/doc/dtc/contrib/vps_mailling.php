<?php

// Sometimes you want to send a mail to all the user of a VPS server
// and it's quite borring to do it one by one. This small script helps
// you to subscribe all the users of a particular VPS server to a mailing
// list, so you can send email to them. We use to create the list with
// nodeXXXXusers@control-panel-domain.com as the name of the list.
// We suggest you to check the follwing parameters in the mailing list
// when creating it:
// "Closed list" so nobody can register to it
// "Moderated" so your users can't write to it
// "Moderator" enter your own email address in it
// "Subject prefix" so it's a nicer subject
// "Delete headers" enter To: in it (see below)
// "Add To: header" so your users wont see the list but
//           their own address in the To: field (so they
//           wont even see it's a MLMMJ list).

// Instead of $mail_list_server_name, I'll write: $mlsn

// Then simply start with "php vps_mailling.php"

$panel_type="admin";
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

// ************** CONFIGURATION START ************************** //
// Name of the server holding your dtc install, can be whatever
// $mlsn = $conf_administrative_site;
// $mlsn = "nodeXXXX.your-domain.com";
$mlsn = $conf_main_domain;

// Select the name of your list here to subscribe users with this script
// can be any name of existing mailing list
// $list_name = "nodeXXXXusers";
$list_name = "my-list-name";

// Before starting this script, modify the parameter below:
// $hosting_path = "/var/www/sites";
$hosting_path = $conf_site_root_host_path;

// Here is the condition we need to pass to sql to select users
// $select_where_condition = "vps_server_hostname='nodeXXXX.your-domain.com'";
$select_where_condition = "vps_server_hostname='nodeXXXX.your-domain.com'";

// With above fields, it builds this:
// Maybe you think that this version is more easy (use one of them only)
$list_full_path = "$hosting_path/$mlsn/lists/".$mlsn."_".$list_name."/";
// ************** CONFIGURATION END **************************** //

$DONOT_USE_ROTATING_PASS="yes";
$out = "";
$q = "SELECT $pro_mysql_client_table.email FROM $pro_mysql_client_table,$pro_mysql_vps_table,$pro_mysql_admin_table
WHERE $select_where_condition 
AND $pro_mysql_client_table.id=$pro_mysql_admin_table.id_client
AND $pro_mysql_admin_table.adm_login=$pro_mysql_vps_table.owner";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
for($i=0;$i<$n;$i++){
        $a = mysql_fetch_array($r);
        $cmdout = array();
        $cmdret = exec("/usr/bin/mlmmj-sub -L $list_full_path -a ".$a["email"],$cmdout,$retval);
        echo $a["email"]."\n";
}
echo "Number of users: $n";

?>
