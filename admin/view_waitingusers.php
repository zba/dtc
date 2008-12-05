<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="admin";
require_once("$dtcshared_path/dtc_lib.php");
// Admin include files
require_once("genfiles/genfiles.php");

include("inc/submit_root_querys.php");
include("inc/nav.php");
include("inc/dtc_config.php");
include("inc/draw_user_admin.php");

if(file_exists("dtcrm")){
	include("dtcrm/submit_to_sql.php");
	include("dtcrm/main.php");
	include("dtcrm/product_manager.php");
}

get_secpay_conf();

$DONOT_USE_ROTATING_PASS="yes";

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC","yes");
$anotherMenu = "";

$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE id='".$_REQUEST["reqadm_id"]."'";
$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	$newu_infos = "$q: User not found!!!";
}else{
	$a = $a = mysql_fetch_array($r);
	$newu_infos = "";
	$newu_infos .= "<h4><u>".$a["comp_name"].":</u> ";
	$newu_infos .= $a["family_name"].", ".$a["first_name"]."</h4>";
	$newu_infos .= "<b>Login:</b> ".$a["reqadm_login"]."<br>";
	$newu_infos .= "<b>Password:</b> ".$a["reqadm_pass"]."<br>";
	$newu_infos .= "<b>Domain name:</b> ".$a["domain_name"]."<br>";
	$newu_infos .= "<b>Email:</b> ".$a["email"]."<br>";
	$newu_infos .= "<b>Phone:</b> ".$a["phone"]."<br>";
	$newu_infos .= "<b>Fax:</b> ".$a["fax"]."<br>";
	$newu_infos .= "<b>Address:</b> ".$a["addr1"]." ".$a["addr2"]." ".$a["addr3"]."<br>";
	$newu_infos .= "<b>Zipcode:</b> ".$a["zipcode"]."<br>";
	$newu_infos .= "<b>State:</b> ".$a["state"]."<br>";
	$newu_infos .= "<b>City:</b> ".$a["city"]."<br>";
	$newu_infos .= "<b>Country:</b> ".$cc_code_array[$a["country"]]."<br>";
	$newu_infos .= "<b>Customer notes:</b> ".nl2br($a["custom_notes"])."<br>";
	$newu_infos .= "";
	$q2 = "SELECT * FROM product WHERE id='".$a["product_id"]."';";
	$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n2 = mysql_num_rows($r2);
	if($n2 != 1){
		$newu_infos .= "<font color=\"red\">Product not found!</font><br>";
	}else{
		$a2 = mysql_fetch_array($r2);
		$newu_infos .= "<b>Product:</b> ".$a2["name"]." (".$a2["price_dollar"]." $secpayconf_currency_letters)<br>";
	}
	$q3 = "SELECT * FROM paiement WHERE id='".$a["paiement_id"]."';";
	$r3 = mysql_query($q3)or die("Cannot query \"$q3\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n3 = mysql_num_rows($r3);
	if($n3 != 1){
		$newu_infos .= "<font color=\"red\">Paiement not found!</font><br>";
	}else{
		$a3 = mysql_fetch_array($r3);
		$newu_infos .= "<b>Payment site:</b> ".$a3["secpay_site"]."<br>";
	}
}

$the_page[] = skin($conf_skin,$newu_infos,"User details:");

$pageContent = makeVerticalFrame($the_page);
$anotherFooter = anotherFooter("Footer content<br><br>");

echo anotherPage("admin:","","",makePreloads(),$anotherTopBanner,$anotherMenu,$pageContent,$anotherFooter);

?>
