<?php

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

if(file_exists("dtcrm")){
	include("dtcrm/submit_to_sql.php");
	include("dtcrm/main.php");
	include("dtcrm/product_manager.php");
}

$DONOT_USE_ROTATING_PASS="yes";

$out = "";

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC","yes");
//$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

///////////////////////
// Make All the page //
///////////////////////
switch($rub){
case "crm": // CRM TOOL
	$rightFrameCells[] = skin($conf_skin,DTCRMeditClients(),$txt_client_addr_title[$lang]);
	if(isset($_REQUEST["id"]) && $_REQUEST["id"] != "" && $_REQUEST["id"] != 0){
		$rightFrameCells[] = skin($conf_skin,DTCRMclientAdmins(),$txt_client_admins_title[$lang]);
		$rightFrameCells[] = skin($conf_skin,DTCRMshowClientCommands($id_client),$txt_client_commands_title[$lang]);
	}
	$rightFrame = makeVerticalFrame($rightFrameCells);
	$leftFrameCells[] = skin($conf_skin,DTCRMlistClients(),$txt_client_list_title[$lang]);
	$leftFrame = makeVerticalFrame($leftFrameCells);
	$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
	break;

case "monitor": // Monitor button

	// For each clients
	$q = "SELECT * FROM $pro_mysql_client_table WHERE 1 ORDER BY familyname,christname";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
	$nr = mysql_num_rows($r);
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="1" cellspacing="1">';
	$out .=
"<tr><td><b>User</b></td><td><b>".$txt_transfer[$lang]." / ".$txt_bw_quota[$lang]."</b></td><td><b>Transfer per month</b></td><td><b>".$txt_disk_usage[$lang]." / ".$txt_domain_tbl_config_quota[$lang]."</b></td></tr>";
	$total_box_transfer = 0;
	for($i=0;$i<$nr;$i++){
		$ar = mysql_fetch_array($r);
		$transfer = 0;
		$du = 0;
		// For each of it's admins
		$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='".$ar["id"]."';";
		$r2 = mysql_query($q2)or die("Cannot query: \"$q2\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
		$nr2 = mysql_num_rows($r2);
		for($j=0;$j<$nr2;$j++){
			$ar2 = mysql_fetch_array($r2);

			$admin = fetchAdmin($ar2["adm_login"],$ar2["adm_pass"]);
			$admin_stats = fetchAdminStats($admin);
			$transfer += $admin_stats["total_transfer"];
			$du += $admin_stats["total_du"];
		}
		if($i % 2){
			$back = " bgcolor=\"#000000\" style=\"white-space:nowrap;color:#FFFFFF\" nowrap";
		}else{
			$back = " style=\"white-space:nowrap;\" nowrap";
		}
		$out .= "<tr><td$back><u>".$ar["company_name"].":</u><br>
".$ar["familyname"].", ".$ar["christname"]."</td>";
		$out .= "<td$back>".drawPercentBar($transfer,$ar["bw_quota_per_month_gb"]*1024*1024*1024,"no")."<br>
".smartByte($transfer)." / ".smartByte($ar["bw_quota_per_month_gb"]*1024*1024*1024)."</td>";
		$out .= "<td$back><img width=\"120\" height=\"48\" src=\"bw_per_month.php?cid=".$ar["id"]."\"></td>";
		$out .= "<td$back>".drawPercentBar($du,$ar["disk_quota_mb"]*1024*1024,"no")."<br>
".smartByte($du)." / ".smartByte($ar["disk_quota_mb"]*1024*1024)."</td>";
		$total_box_transfer += $transfer;
//fetchAdminStats($admin)
	}
	$out .= "</table>";
	$out .= $txt_server_total_bp[$lang].smartByte($total_box_transfer);
	$module = skin($conf_skin,$out,$txt_customer_bw_consumption[$lang]);
	$zemain_content = $module;
	break;
	
case "generate": // Gen Config Files
	$mainFrameCells[] = skin($conf_skin,$top_commands,$txt_generate_buttons_title[$lang]);
	$the_iframe = "<br><IFRAME src=\"deamons_state.php\" width=\"100%\" height=\"135\"></iframe>";
	$mainFrameCells[] = skin($conf_skin,$the_iframe,"Deamons states");
	// The console
	$mainFrameCells[] = skinConsole();
	$zemain_content = makeVerticalFrame($mainFrameCells);
	break;
	
case "config": // Global Config
	if(isset($_REQUEST["install_new_config_values"]) && $_REQUEST["install_new_config_values"] == "Ok"){
		saveDTCConfigInMysql();
		getConfig();
	}

	$chooser_menu = drawDTCConfigMenu();
	$leftFrameCells[] = skin($conf_skin,$chooser_menu,"Menu");
	$leftFrame = makeVerticalFrame($leftFrameCells);

	$rightFrameCells[] = skin($conf_skin,drawDTCConfigForm(),"DTC configuration");
	$rightFrame = makeVerticalFrame($rightFrameCells);

	$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
	break;

case "product":
	$bla = productManager();
	$zemain_content = skin($conf_skin,$bla,"Product manager");
	break;
case "adminedit":
	// A virtual admin edition
	// We have to call it first, in case it will generate a random pass (edition of an admin with inclusion of user's panel)
	$rightFrameCells[] = userEditForms($adm_login,$adm_pass);
	$rightFrameCells[] = skinConsole();
	$rightFrame = makeVerticalFrame($rightFrameCells);

	// Our list of admins
	// If random password was not set before this, we have to calculate it now!
	if(isset($adm_random_pass)){
		$leftFrameCells[] = skin($conf_skin,adminList($adm_pass),$txt_virtual_admin_list[$lang]);
	}else{
		$rand = getRandomValue();
		$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
		$q = "UPDATE $pro_mysql_config_table SET root_admin_random_pass='$rand', pass_expire='$expirationTIME';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		$leftFrameCells[] = skin($conf_skin,adminList($rand),$txt_virtual_admin_list[$lang]);
	}
	// Make the frame
	$leftFrame = makeVerticalFrame($leftFrameCells);

	$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
	break;
case "user": // User Config
default: // No rub selected
	// A virtual admin edition
	// We have to call it first, in case it will generate a random pass (edition of an admin with inclusion of user's panel)
	$rightFrameCells[] = userEditForms($adm_login,$adm_pass);
	$rightFrameCells[] = skinConsole();
	$rightFrame = makeVerticalFrame($rightFrameCells);

	// Our list of admins
	// If random password was not set before this, we have to calculate it now!
	if(isset($adm_random_pass)){
		$leftFrameCells[] = skin($conf_skin,adminList($adm_pass),$txt_virtual_admin_list[$lang]);
	}else{
		$rand = getRandomValue();
		$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
		$q = "UPDATE $pro_mysql_config_table SET root_admin_random_pass='$rand', pass_expire='$expirationTIME';";
		$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		$leftFrameCells[] = skin($conf_skin,adminList($rand),$txt_virtual_admin_list[$lang]);
	}
	// Make the frame
	$leftFrame = makeVerticalFrame($leftFrameCells);

	$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
	break;
}

if(isset($adm_login) && isset($adm_pass)){
	$added_logpass = "&adm_login=$adm_login&adm_pass=$adm_pass";
}else{
	$added_logpass = "";
}

$menu = "";
// User management icon
if(isset($_REQUEST["rub"]) && $_REQUEST["rub"] != "" && $_REQUEST["rub"] != "user"){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=user$added_logpass\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/admins.png\"><br>".
	$txt_mainmenu_title_useradmin[$lang];
if(isset($_REQUEST["rub"]) && $_REQUEST["rub"] == "" && $_REQUEST["rub"] != "user"){
	$menu .= "</a>";
}
$html_array[] = $menu;
$menu = "";

// Generate edit admins icon
if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "adminedit"){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=adminedit$added_logpass\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/user-editor.png\"><br>"."Admin editor";
if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "adminedit"){
	$menu .= "</a>";
}
$html_array[] = $menu;
$menu = "";

// CRM icons are present only if folder is present:
// this helps simplification if user does not need it.
if(file_exists("dtcrm")){
	// CRM Button
	if(!isset($rub) || $rub != "crm"){
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) == 1){
			$row = mysql_fetch_array($result);
			$url_addon = "&id=".$row["id_client"];
		}else{
			$url_addon = "";
		}
		$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=crm&admlist_type=Names".$url_addon."\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/crm.png\"><br>".
		$txt_mainmenu_title_client_management[$lang];
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "crm"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";
	// Monitor button
	if(!isset($rub) || $rub != "monitor"){
		$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=monitor\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/bw_icon.png\"><br>".
		$txt_mainmenu_title_bandwidth_monitor[$lang];
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "monitor"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";
	// Product manager
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "product"){
		$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=product\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/product_manager.png\"><br>
		Product manager";
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "product"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";
}

// Generate daemon files icon
if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "generate"){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/daemons.png\"><br>".
	$txt_mainmenu_title_deamonfile_generation[$lang];
if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "generate"){
	$menu .= "</a>";
}
$html_array[] = $menu;
$menu = "";

// Main config panel icon
if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "config"){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/config_panel.png\"><br>".
	$txt_mainmenu_title_dtc_config[$lang];
if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "config"){
	$menu .= "</a>";
}
$html_array[] = $menu;
$dtc_main_menu = make_table($html_array,sizeof($html_array));
$the_page[] = skin($conf_skin,$dtc_main_menu,$txt_root_adm_title[$lang]);

$the_page[] = $zemain_content;
$pageContent = makeVerticalFrame($the_page);
$anotherFooter = anotherFooter("Footer content<br><br>");

if(!isset($anotherHilight))	$anotherHilight = "";
if(!isset($anotherMenu))	$anotherMenu = "";

echo anotherPage("admin:","",$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$pageContent,$anotherFooter);

?>
