<?php

require_once("/usr/share/dtc/shared/autoSQLconfig.php");
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
$dtc_main_menu .= "<br><center>
<table width=\"66%\">
<tr><td width=\"33%\" align=\"center\">";
if($_REQUEST["rub"] != "" && isset($_REQUEST["rub"])){
	$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=user\">";
}
$dtc_main_menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/users.gif\">";
if($_REQUEST["rub"] == "" || !isset($_REQUEST["rub"])){
	$dtc_main_menu .= "</a>";
}
// CRM icon only if folder present.
if(file_exists("dtcrm")){
	$dtc_main_menu .= "</td>
	<td width=\"33%\" align=\"center\">";
	if($_REQUEST["rub"] != "crm"){
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) == 1){
			$row = mysql_fetch_array($result);
			$url_addon = "&id=".$row["id_client"];
		}
		$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=crm&admlist_type=Names".$url_addon."\">";
	}
	$dtc_main_menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/recycle.gif\">";
	if($_REQUEST["rub"] != "crm"){
		$dtc_main_menu .= "</a>";
	}
}
$dtc_main_menu .= "</td>
<td width=\"33%\" align=\"center\">";
if($_REQUEST["rub"] != "generate"){
	$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate\">";
}
$dtc_main_menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/recycle.gif\">";
if($_REQUEST["rub"] != "generate"){
	$dtc_main_menu .= "</a>";
}
$dtc_main_menu .= "</td>
<td width=\"33%\" align=\"center\">";
if($_REQUEST["rub"] != "config"){
	$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config\">";
}
$dtc_main_menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/config.gif\">";
if($_REQUEST["rub"] != "config"){
	$dtc_main_menu .= "</a>";
}
$dtc_main_menu .= "</td></tr>";

$dtc_main_menu .=
"<tr>
	<td align=\"center\" valign=\"top\">";

if($_REQUEST["rub"] != "" && isset($_REQUEST["rub"])){
	$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?\">";
}
$dtc_main_menu .= $txt_mainmenu_title_useradmin[$lang];
if($_REQUEST["rub"] == "" || !isset($_REQUEST["rub"])){
	$dtc_main_menu .= "</a>";
}
// CRM icon only if folder present.
if(file_exists("dtcrm")){
	$dtc_main_menu .= "</td>
	<td width=\"33%\" align=\"center\" valign=\"top\">";
	if($_REQUEST["rub"] != "crm"){
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND adm_pass='$adm_pass';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) == 1){
			$row = mysql_fetch_array($result);
			$url_addon = "&id=".$row["id_client"];
		}
		$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=crm&admlist_type=Names".$url_addon."\">";
	}
	$dtc_main_menu .= $txt_mainmenu_title_crm[$lang];
	if($_REQUEST["rub"] != "crm"){
		$dtc_main_menu .= "</a>";
	}
}
$dtc_main_menu .= "</td>
<td width=\"33%\" align=\"center\" valign=\"top\">";
if($_REQUEST["rub"] != "generate"){
	$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=generate\">";
}
$dtc_main_menu .= "$txt_mainmenu_title_deamonfile_generation[$lang]";
if($_REQUEST["rub"] != "generate"){
	$dtc_main_menu .= "</a>";
}
$dtc_main_menu .= "</td>
<td width=\"33%\" align=\"center\" valign=\"top\">";
if($_REQUEST["rub"] != "config"){
	$dtc_main_menu .= "<a href=\"".$_SERVER["PHP_SELF"]."?rub=config\">";
}
$dtc_main_menu .= $txt_mainmenu_title_dtc_config[$lang];
if($_REQUEST["rub"] != "config"){
	$dtc_main_menu .= "</a>";
}
$dtc_main_menu .= "</td>";
$dtc_main_menu .= "</table>
</center>";

$the_page[] = skin("simple/green2",$dtc_main_menu,"Menu");

switch($_REQUEST["rub"])
{
	case user: // User Config
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
	
	case crm: // CRM TOOL
		$rightFrameCells[] = skin("simple/green",DTCRMeditClients(),"Client address");
		if(isset($id_client) && $id_client != "")
			$rightFrameCells[] = skin("simple/green",DTCRMshowClientCommands($id_client),"Client commands");
		$rightFrame = makeVerticalFrame($rightFrameCells);
		$leftFrameCells[] = skin("simple/green",DTCRMlistClients(),"Client listing");
		$leftFrame = makeVerticalFrame($leftFrameCells);
		$the_page[] = anotherLeftFrame($leftFrame,$rightFrame);
	break;
	
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
}
/*
if($_REQUEST["rub"] == "generate"){
	$the_page[] = skin("simple/green",$top_commands,$txt_generate_buttons_title[$lang]);
	$the_iframe = "<br><IFRAME src=\"deamons_state.php\" width=\"100%\" height=\"95\">
</iframe>";
	$the_page[] = skin("simple/green",$the_iframe,"Deamons states");
	// The console
	$the_page[] = skinConsole();
}else if($_REQUEST["rub"] == "config"){
	if($install_new_config_values == "Ok"){
		saveDTCConfigInMysql();
		getConfig();
	}

	$configForm = drawDTCConfigForm();
	$the_page[] = skin("simple/green",$configForm,"DTC configuration");
}else if($_REQUEST["rub"] == "crm"){
	$rightFrameCells[] = skin("simple/green",DTCRMeditClients(),"Client address");
	if(isset($id_client) && $id_client != "")
		$rightFrameCells[] = skin("simple/green",DTCRMshowClientCommands($id_client),"Client commands");
	$rightFrame = makeVerticalFrame($rightFrameCells);
	$leftFrameCells[] = skin("simple/green",DTCRMlistClients(),"Client listing");
	$leftFrame = makeVerticalFrame($leftFrameCells);
	$the_page[] = anotherLeftFrame($leftFrame,$rightFrame);
}else{
	// Our list of admins
	$leftFrameCells[] = skin("simple/green","<br>$admins",$txt_virtual_admin_list[$lang]);

	// Make the frame
	$leftFrame = makeVerticalFrame($leftFrameCells);

	// A virtual admin edition
	$rightFrameCells[] = userEditForms($adm_login,$adm_pass);
	$rightFrameCells[] = skinConsole();

	$rightFrame = makeVerticalFrame($rightFrameCells);
	$the_page[] = anotherLeftFrame($leftFrame,$rightFrame);
}*/
$pageContent = makeVerticalFrame($the_page);
$anotherFooter = anotherFooter("Footer content<br><br>");

echo anotherPage("admin:".$txt_page_title[$lang],$txt_page_meta[$lang],$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$pageContent,$anotherFooter);

?>
