<?php

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
}

$DONOT_USE_ROTATING_PASS=yes;

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC","yes");
$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

///////////////////////
// Make All the page //
///////////////////////

// User management icon
if($_REQUEST["rub"] != "" && isset($_REQUEST["rub"]) && $_REQUEST["rub"] != "user"){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=user\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/users.gif\"><br>".
	$txt_mainmenu_title_useradmin[$lang];
if($_REQUEST["rub"] == "" || !isset($_REQUEST["rub"]) && $_REQUEST["rub"] != "user"){
	$menu .= "</a>";
}
$html_array[] = $menu;
$menu = "";

// CRM icons are present only if folder is present:
// this helps simplification if user does not need it.
if(file_exists("dtcrm")){
	// CRM Button
	if($_REQUEST["rub"] != "crm"){
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) == 1){
			$row = mysql_fetch_array($result);
			$url_addon = "&id=".$row["id_client"];
		}
		$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=crm&admlist_type=Names".$url_addon."\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/crm.png\"><br>".
		$txt_mainmenu_title_client_management[$lang];
	if($_REQUEST["rub"] != "crm"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";
	// Monitor button
	if($_REQUEST["rub"] != "monitor"){
		$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=monitor\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/bw_icon.png\"><br>".
		$txt_mainmenu_title_bandwidth_monitor[$lang];
	if($_REQUEST["rub"] != "monitor"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";
}

// Generate daemon files icon
if($_REQUEST["rub"] != "generate"){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/recycle.gif\"><br>".
	$txt_mainmenu_title_deamonfile_generation[$lang];
if($_REQUEST["rub"] != "generate"){
	$menu .= "</a>";
}
$html_array[] = $menu;
$menu = "";

// Main config panel icon
if($_REQUEST["rub"] != "config"){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/config.gif\"><br>".
	$txt_mainmenu_title_dtc_config[$lang];
if($_REQUEST["rub"] != "config"){
	$menu .= "</a>";
}
$html_array[] = $menu;
$dtc_main_menu = make_table($html_array,sizeof($html_array));
$the_page[] = skin($conf_skin,$dtc_main_menu,$txt_root_adm_title[$lang]);

switch($_REQUEST["rub"]){
case crm: // CRM TOOL
	$rightFrameCells[] = skin($conf_skin,DTCRMeditClients(),$txt_client_addr_title[$lang]);
	if(isset($_REQUEST["id"]) && $_REQUEST["id"] != "" && $_REQUEST["id"] != 0){
		$rightFrameCells[] = skin($conf_skin,DTCRMclientAdmins(),$txt_client_admins_title[$lang]);
		$rightFrameCells[] = skin($conf_skin,DTCRMshowClientCommands($id_client),$txt_client_commands_title[$lang]);
	}
	$rightFrame = makeVerticalFrame($rightFrameCells);
	$leftFrameCells[] = skin($conf_skin,DTCRMlistClients(),$txt_client_list_title[$lang]);
	$leftFrame = makeVerticalFrame($leftFrameCells);
	$the_page[] = anotherLeftFrame($leftFrame,$rightFrame);
	break;

case monitor: // Monitor button

	// For each clients
	$q = "SELECT * FROM $pro_mysql_client_table WHERE 1 ORDER BY familyname,christname";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
	$nr = mysql_num_rows($r);
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="1" cellspacing="1">';
	$out .=
"<tr><td><b>User</b></td><td><b>".$txt_transfer[$lang]."</b></td><td><b>".$txt_bw_quota[$lang]."</b></td><td><b>".$txt_graf[$lang]."</b></td><td><b>Transfer per month</b></td><td><b>".$txt_disk_usage[$lang]."</b></td><td><b>".$txt_domain_tbl_config_quota[$lang]."</b></td><td><b>".$txt_graf[$lang]."</b></td></tr>";
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
		$out .= "<tr><td$back><u>".$ar["company_name"].":</u> ".$ar["familyname"].", ".$ar["christname"]."</td>";
		$out .= "<td$back>".smartByte($transfer)."</td><td$back>".smartByte($ar["bw_quota_per_month_gb"]*1024*1024*1024)."</td><td$back>".drawPercentBar($transfer,$ar["bw_quota_per_month_gb"]*1024*1024*1024,"no")."</td>";
		$out .= "<td$back><img width=\"120\" height=\"48\" src=\"bw_per_month.php?cid=".$ar["id"]."\"></td>";
		$out .= "<td$back>".smartByte($du)."</td>";
		$out .= "<td$back>".smartByte($ar["disk_quota_mb"]*1024*1024)."</td>";
		$out .= "<td$back>".drawPercentBar($du,$ar["disk_quota_mb"]*1024*1024,"no")."</td></tr>";
		$total_box_transfer += $transfer;
//fetchAdminStats($admin)
	}
	$out .= "</table>";
	$out .= $txt_server_total_bp[$lang].smartByte($total_box_transfer);
	$module = skin($conf_skin,$out,$txt_customer_bw_consumption[$lang]);
	$the_page[] = $module;
	break;
	
case generate: // Gen Config Files
	$the_page[] = skin($conf_skin,$top_commands,$txt_generate_buttons_title[$lang]);
	$the_iframe = "<br><IFRAME src=\"deamons_state.php\" width=\"100%\" height=\"95\"></iframe>";
	$the_page[] = skin($conf_skin,$the_iframe,"Deamons states");
	// The console
	$the_page[] = skinConsole();
	break;
	
case config: // Global Config
	if($_REQUEST["install_new_config_values"] == "Ok"){
		saveDTCConfigInMysql();
		getConfig();
	}

	$chooser_menu = drawDTCConfigMenu();
	$leftFrameCells[] = skin($conf_skin,$chooser_menu,"Menu");
	$leftFrame = makeVerticalFrame($leftFrameCells);

	$rightFrameCells[] = skin($conf_skin,drawDTCConfigForm(),"DTC configuration");
	$rightFrame = makeVerticalFrame($rightFrameCells);

	$the_page[] = anotherLeftFrame($leftFrame,$rightFrame);
//	$the_page[] = skin($conf_skin,$configForm,"DTC configuration");
	break;
	
case user: // User Config
default: // No rub selected
	// Our list of admins
	$leftFrameCells[] = skin($conf_skin,"<br>$admins",$txt_virtual_admin_list[$lang]);
	// Make the frame
	$leftFrame = makeVerticalFrame($leftFrameCells);

	// A virtual admin edition
	$rightFrameCells[] = userEditForms($adm_login,$adm_pass);
	$rightFrameCells[] = skinConsole();

	$rightFrame = makeVerticalFrame($rightFrameCells);
	$the_page[] = anotherLeftFrame($leftFrame,$rightFrame);
	break;
}

$pageContent = makeVerticalFrame($the_page);
$anotherFooter = anotherFooter("Footer content<br><br>");

echo anotherPage("admin:".$txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$pageContent,$anotherFooter);

?>
