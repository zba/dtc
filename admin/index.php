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

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC","yes");
$anotherMenu = makeHoriMenu($txt_top_menu_entrys[$lang],2);

///////////////////////
// Make All the page //
///////////////////////

// User management icon
if($_REQUEST["rub"] != "" && isset($_REQUEST["rub"])){
	$menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=user\">";
}
$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/users.gif\"><br>".
	$txt_mainmenu_title_useradmin[$lang];
if($_REQUEST["rub"] == "" || !isset($_REQUEST["rub"])){
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
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/recycle.gif\"><br>".
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
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/recycle.gif\"><br>".
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
$the_page[] = skin("simple/green2",$dtc_main_menu,"Menu");

switch($_REQUEST["rub"]){
case crm: // CRM TOOL
	$rightFrameCells[] = skin("simple/green",DTCRMeditClients(),"Client address");
	if(isset($id_client) && $id_client != "")
		$rightFrameCells[] = skin("simple/green",DTCRMshowClientCommands($id_client),"Client commands");
	$rightFrame = makeVerticalFrame($rightFrameCells);
	$leftFrameCells[] = skin("simple/green",DTCRMlistClients(),"Client listing");
	$leftFrame = makeVerticalFrame($leftFrameCells);
	$the_page[] = anotherLeftFrame($leftFrame,$rightFrame);
	break;

case monitor: // Monitor button

	// For each clients
	$q = "SELECT * FROM $pro_mysql_client_table WHERE 1 ORDER BY familyname,christname";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
	$nr = mysql_num_rows($r);
	for($i=0;$i<$nr;$i++){
		$ar = mysql_fetch_array($r);
		$out .= "User: ".$ar["christname"].", ".$ar["familyname"]."<br>";
		$transfer = 0;

		// For each of it's admins
		$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE id_client='".$ar["id"]."';";
		$r2 = mysql_query($q2)or die("Cannot query: \"$q2\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
		$nr2 = mysql_num_rows($r);
		for($j=0;$j<$nr2;$j++){
			$ar2 = mysql_fetch_array($r2);

			// For each of it's domains
			$q3 = "SELECT name FROM $pro_mysql_domain_table WHERE owner='".$ar2["adm_login"]."';";
			$r3 = mysql_query($q3)or die("Cannot query: \"$q3\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
			$nr3 = mysql_num_rows($r3);
			for($k=0;$k<$nr3;$k++){
				$ar3 = mysql_fetch_array($r3);

				// FTP
				$q4 = "SELECT transfer FROM $pro_mysql_acc_ftp_table WHERE
				sub_domain='".$ar3["name"]."' AND month='".date("n")."' AND year='".date("Y")."';";
				$r4 = mysql_query($q4)or die("Cannot query: \"$q4\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
				$nr4 = mysql_num_rows($r4);
				if($nr4 != 1){
					$sum = 0;
				}else{
					$ar4 = mysql_fetch_array($r4);
					$sum = $ar4["transfer"];
				}
				// HTTP
				$q4 = "SELECT SUM(bytes_sent) AS amount FROM $pro_mysql_acc_http_table WHERE
				domain='".$ar3["name"]."' AND month='".date("n")."' AND year='".date("Y")."';";
				$r4 = mysql_query($q4)or die("Cannot query: \"$q4\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
				$nr4 = mysql_num_rows($r4);
				if($nr4 == 1){
					$ar4 = mysql_fetch_array($r4);
					$sum += $ar4["amount"];
				}

				// EMAIL
				$q4 = "SELECT smtp_trafic,pop_trafic FROM $pro_mysql_acc_email_table WHERE
				month='".date("n")."' AND year='".date("Y")."';";
				$r4 = mysql_query($q4)or die("Cannot query: \"$q4\" !".mysql_error()." line ".__LINE__." in file ".__FILE__);
				$nr4 = mysql_num_rows($r4);
				if($nr4 == 1){
					$ar4 = mysql_fetch_array($r4);
					$sum += $ar4["smtp_trafic"] + $ar4["pop_trafic"];
				}
				$transfer += $sum;
			}
		}
		$out .= "User: ".$ar["christname"].", ".$ar["familyname"]."<br>";
		$out .= "Transfer: ".smartByte($transfer)."<br>";
//fetchAdminStats($admin)
	}
	$module = skin("simple/green",$out,"Client address");
	$the_page[] = $module;
	break;
	
case generate: // Gen Config Files
	$the_page[] = skin("simple/green",$top_commands,$txt_generate_buttons_title[$lang]);
	$the_iframe = "<br><IFRAME src=\"deamons_state.php\" width=\"100%\" height=\"95\"></iframe>";
	$the_page[] = skin("simple/green",$the_iframe,"Deamons states");
	// The console
	$the_page[] = skinConsole();
	break;
	
case config: // Global Config
	if($_REQUEST["install_new_config_values"] == "Ok"){
		saveDTCConfigInMysql();
		getConfig();
	}

	$configForm = drawDTCConfigForm();
	$the_page[] = skin("simple/green",$configForm,"DTC configuration");
	break;
	
case user: // User Config
default: // No rub selected
	// Our list of admins
	$leftFrameCells[] = skin("simple/green","<br>$admins",$txt_virtual_admin_list[$lang]);
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
