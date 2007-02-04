<?

function layoutAdminPanel($title,$meta,$java_script,$onloads,$banner,$menu,$content,$footer){
	global $page_metacontent;
	global $confirm_javascript;
	global $skinCssString;
	global $body_tag;

	return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
<head>
<title>DTC: $title ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
</head>

$onloads
$confirm_javascript
$java_script
$skinCssString

$body_tag

$banner
$menu
<div id=\"content\">$content</div>
$footer
</html>";

}

function skin_Navbar (){
	global $rub;
	global $txt_mainmenu_title_useradmin;
	global $txt_mainmenu_title_client_management;
	global $txt_mainmenu_title_bandwidth_monitor;
	global $txt_mainmenu_title_server_monitor;
	global $txt_mainmenu_title_renewals;
	global $txt_product_manager;
	global $txt_mainmenu_title_deamonfile_generation;
	global $txt_mainmenu_title_dtc_config;
	global $lang;

	$out = '<div id="navbar"><div id="navbar_left"></div><ul id="navbar_items">';

	if(!isset($rub) || $rub == "" || $rub == "user" || $rub == "domain_config" || $rub == "adminedit"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_user.gif"><br />'.$txt_mainmenu_title_useradmin[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=user"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_user.gif"><br />'.$txt_mainmenu_title_useradmin[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "crm"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_customer.gif"><br />'.$txt_mainmenu_title_client_management[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=crm"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_customer.gif"><br />'.$txt_mainmenu_title_client_management[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "monitor"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_bandwith.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=monitor"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_bandwith.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "graph"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif"><br />'.$txt_mainmenu_title_server_monitor[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=graph"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif"><br />'.$txt_mainmenu_title_server_monitor[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "renewal"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_renewal.gif"><br />'.$txt_mainmenu_title_renewals[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=renewal"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_renewal.gif"><br />'.$txt_mainmenu_title_renewals[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "product"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_product.gif"><br />'.$txt_product_manager[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=product"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_product.gif"><br />'.$txt_product_manager[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "generate"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_daemon.gif"><br />'.$txt_mainmenu_title_deamonfile_generation[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=generate"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_daemon.gif"><br />'.$txt_mainmenu_title_deamonfile_generation[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "config"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_config.gif"><br />'.$txt_mainmenu_title_dtc_config[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=config"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_config.gif"><br />'.$txt_mainmenu_title_dtc_config[$lang].'</a></li>';
	}

	$out .= '</ul><div id="navbar_right"></div></div>';
	return $out;
}

function skin_LayoutAdminPage (){
	global $rub;
	global $adm_login;
	global $adm_pass;
	global $conf_session_expir_minute;
	global $pro_mysql_config_table;
	global $conf_skin;
	global $lang;
	global $txt_virtual_admin_list;
	global $txt_root_adm_title;
	global $txt_dtc_configuration;
	global $top_commands;
	global $txt_generate_buttons_title;
	global $txt_iframe_ds;
	global $txt_product_manager;
	global $txt_customer_bw_consumption;
	global $txt_client_addr_title;
	global $txt_client_list_title;
	global $txt_client_admins_title;
	global $txt_client_commands_title;

	global $page_metacontent;
	global $meta;


	global $confirm_javascript;
	global $java_script;
	global $skinCssString;
	global $console;

	///////////////////////
	// Make All the page //
	///////////////////////
	switch($rub){
	case "crm": // CRM TOOL
		$rightFrameCells[] = skin($conf_skin,DTCRMeditClients(),$txt_client_addr_title[$lang]);
			if(isset($_REQUEST["id"]) && $_REQUEST["id"] != "" && $_REQUEST["id"] != 0){
			$rightFrameCells[] = skin($conf_skin,DTCRMclientAdmins(),$txt_client_admins_title[$lang]);
			$rightFrameCells[] = skin($conf_skin,DTCRMshowClientCommands($_REQUEST["id"]),$txt_client_commands_title[$lang]);
		}
		$rightFrame = makeVerticalFrame($rightFrameCells);
		$leftFrameCells[] = skin($conf_skin,DTCRMlistClients(),$txt_client_list_title[$lang]);
		$leftFrame = makeVerticalFrame($leftFrameCells);
		$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
		break;
	case "renewal":
		$out = drawRenewalTables();
		$zemain_content = skin($conf_skin,$out,"Customer Renewals");
		break;
	case "monitor": // Monitor button
		$out = drawAdminMonitor();
		$zemain_content = skin($conf_skin,$out,$txt_customer_bw_consumption[$lang]);
		break;
	case "graph":
		$zemain_content = skin($conf_skin,drawRrdtoolGraphs (),"Server statistic graphs");
		break;
	case "generate": // Gen Config Files
		$mainFrameCells[] = skin($conf_skin,$top_commands,$txt_generate_buttons_title[$lang]);
		$the_iframe = "<br><IFRAME src=\"deamons_state.php\" width=\"100%\" height=\"135\"></iframe>";
		$mainFrameCells[] = skin($conf_skin,$the_iframe,$txt_iframe_ds[$lang]); // fixed bug by seeb
		// The console
		$mainFrameCells[] = skinConsole();
		$zemain_content = makeVerticalFrame($mainFrameCells);
		break;
	case "config": // Global Config
		$chooser_menu = drawDTCConfigMenu();
		$leftFrameCells[] = skin($conf_skin,$chooser_menu,"Menu");
		$leftFrame = makeVerticalFrame($leftFrameCells);
		$rightFrameCells[] = skin($conf_skin,drawDTCConfigForm(),$txt_dtc_configuration[$lang]);
		$rightFrame = makeVerticalFrame($rightFrameCells);

		$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
		break;
	case "product":
		$bla = productManager();
		$zemain_content = skin($conf_skin,$bla,$txt_product_manager[$lang]);
		break;
	case "user": // User Config
	case "domain_config":
	case "adminedit":
	default: // No rub selected

		$bwoup_user_edit = bwoupUserEditForms($adm_login,$adm_pass);

		if(isset($adm_random_pass)){
			$rand = $adm_random_pass;
		}else{
			$rand = getRandomValue();
			$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
			$q = "UPDATE $pro_mysql_config_table SET root_admin_random_pass='$rand', pass_expire='$expirationTIME';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		}
		$zemain_content = '<div id="content">

	<table class="box_wnb" border="0" cellpadding="0" cellspacing="0">
    <tr>
	<td class="box_wnb_nb" valign="top">

		<div class="box_wnb_nb_title"><div class="box_wnb_nb_title_left"><div class="box_wnb_nb_title_right"><div class="box_wnb_nb_title_mid">
Admin list</div></div></div>
<div class="box_wnb_nb_content">'.adminList($rand).'
			<div class="voider"></div>

		  <br /><br />

		</div>
		<div class="voider"></div>
	  		
	</td>
	<td class="box_wnb_content" valign="top">
	<div class="box_wnb_content_container">

	  <h2>User administration</h2>
'.$bwoup_user_edit.'
	<table cellpadding="0" cellspacing="0" class="console">
	  <tr>
		<td class="console_title">Console output :</td>
	  </tr>
	  <tr>		
		<td class="console_output"><pre>'.$_SERVER['SERVER_NAME'].':&gt;'.$console.'<br></pre></td>

	  </tr>
	  </table>


	</div>
	</td>
	</tr>
	<tr>
	<td class="box_wnb_nb_bottom"></td>
	<td class="box_wnb_content_bottom" valign="top"></td>

	</tr>
	</table></div>';
		break;


		// Our list of admins
		// If random password was not set before this, we have to calculate it now!
		if(isset($adm_random_pass)){
			$rand = $adm_random_pass;
		}else{
			$rand = getRandomValue();
			$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
			$q = "UPDATE $pro_mysql_config_table SET root_admin_random_pass='$rand', pass_expire='$expirationTIME';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		}

		$leftFrameCells[] = skin($conf_skin,adminList($rand),$txt_virtual_admin_list[$lang]);
		$leftFrame = makeVerticalFrame($leftFrameCells);

		// A virtual admin edition
		// We have to call it first, in case it will generate a random pass (edition of an admin with inclusion of user's panel)
		$rightFrameCells[] = userEditForms($adm_login,$adm_pass);
		$rightFrameCells[] = skinConsole();
		$rightFrame = makeVerticalFrame($rightFrameCells);


		$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
		break;
	}

	$dtc_main_menu = skin_Navbar();
	$the_page[] = skin($conf_skin,$dtc_main_menu,$txt_root_adm_title[$lang]);
	$the_page[] = $zemain_content;
	$pageContent = makeVerticalFrame($the_page);
	$anotherFooter = anotherFooter("Footer content<br><br>");

	if(!isset($anotherHilight))	$anotherHilight = "";
	if(!isset($anotherMenu))	$anotherMenu = "";

//	anotherPage($title,$meta,$java_script,$onloads,$banner,$menu,$content,$footer);
//	echo anotherPage("admin:","",$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$pageContent,$anotherFooter);
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
<head>
<title>DTC: Admin: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
</head>

".makePreloads()."
$confirm_javascript
$java_script
$skinCssString

<body id=\"page\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
".anotherTopBanner("DTC","yes").$dtc_main_menu."
<div id=\"content\">".$zemain_content."</div>
<div id=\"footer\">".anotherFooter("Footer content<br><br>")."</div>
</html>";

}

function bwoupUserEditForms($adm_login,$adm_pass){
	global $txt_general_virtual_admin_edition;
	global $txt_domains_configuration_title;
	global $txt_add_user_title;

	global $txt_client_interface;
	global $txt_domain_config;
	global $txt_admin_editor;
	global $lang;
	// added by seeb
	global $txt_user_administration;
	global $txt_user_administration_domains_for;

	global $adm_random_pass;

	// end added
	global $conf_skin;
	global $lang;
	global $addrlink;
	global $rub;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	if(isset($adm_login) && $adm_login != "" && isset($adm_pass) && $adm_pass != ""){
		$out = '<ul class="box_wnb_content_nb">';
		if($rub != "user" && $rub != ""){
			$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=user\">client interface</a></li>";
		}else{
			$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=user\">client interface</a></li>";
		}
		$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
		if($rub != "domain_config"){
			$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=domain_config\">".$txt_domain_config[$lang]."</a></li>";
		}else{
			$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=domain_config\">".$txt_domain_config[$lang]."</a></li>";
		}
		$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
		if($rub != "adminedit"){
			$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=adminedit\">".$txt_domain_config[$lang]."</a></li>";
		}else{
			$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&rub=adminedit\">".$txt_domain_config[$lang]."</a></li>";
		}
		$out .= "</ul>";

		// Fetch all the selected user informations, Print a nice error message if failure.
		$admin = fetchAdmin($adm_login,$adm_pass);
		if(isset($adm_random_pass)){
			$pass = $adm_random_pass;
		}else{
			$pass = $adm_pass;
		}
		if(($error = $admin["err"]) != 0){
			echo("Error fetching admin : $error");
			$ret["err"] = $admin["err"];
			$ret["mesg"] = $admin["mesg"];
			return $ret;
		}

		//fix up the $adm_login in case it changed because of session vars:
		//in case users play silly bugger with the "GET" variables
		$adm_login = $admin["info"]["adm_login"];

		// Draw the html forms
		if(isset($rub) && $rub == "adminedit"){
			$out .= drawEditAdmin($admin);
		}else if(isset($rub) && $rub == "domain_config"){
			$out .= drawDomainConfig($admin);
		}else{
			$out .= drawAdminTools($admin);
		}
		return $out;
	}else{
		// If no user is in edition, draw a tool for adding an admin
		return drawNewAdminForm();
	}
}

?>