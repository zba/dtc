<?php
////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
function skin_EmailPage_Default (){
	global $conf_skin;
	global $adm_email_login;

	global $adm_email_login;
	global $adm_email_pass;
	$anotherTopBanner = anotherTopBanner("DTC");

	$anotherLanguageSelection = anotherLanguageSelection();
	$lang_sel = skin($conf_skin,$anotherLanguageSelection, _("Language") );

	if($adm_email_login != "" && isset($adm_email_login) && $adm_email_pass != "" && isset($adm_email_pass)){
		$error = pass_check_email();
		// Fetch all the user informations, Print a nice error message if failure.
		if($error == false){
			$mesg = $admin["mesg"];
			$login_txt = "<font color=\"red\">" . _("Wrong login or password") . "!</font><br>";
			$login_txt .= login_emailpanel_form();
			$login_skined = skin($conf_skin,$login_txt, _("Email panel: ") . _("Login") );
			$mypage = layout_login_and_languages($login_skined,$lang_sel);
		}else{
			// Draw the html forms, login is successfull
			$admin = fetchMailboxInfos($adm_email_login,$adm_email_pass);
			$content = drawAdminTools_emailPanel($admin);
			$mypage = $content;
		}
	}else{
		$login_txt = login_emailpanel_form();
		$login_skined = skin($conf_skin,$login_txt, _("Email panel: ") . _("Login") );
		$mypage = layout_login_and_languages($login_skined,$lang_sel);
	}
	// Output the result !

	echo anotherPage("Email:","","",makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));
}


function skin_ClientPage_Default (){
	global $adm_pass;
	global $adm_login;
	global $conf_skin;

	////////////////////////////////////
	// Create the top banner and menu //
	////////////////////////////////////
	$anotherTopBanner = anotherTopBanner("DTC");

	$anotherLanguageSelection = anotherLanguageSelection();
	$lang_sel = skin($conf_skin,$anotherLanguageSelection, _("Language") );

	if($adm_login != "" && isset($adm_login) && $adm_pass != "" && isset($adm_pass)){
	        // Fetch all the user informations, Print a nice error message if failure.
	        $admin = fetchAdmin($adm_login,$adm_pass);
	        if(($error = $admin["err"]) != 0){
	                $mesg = $admin["mesg"];
	                $login_txt = _("Error") ." $error ". _("fetching admin: ") ."<font color=\"red\">$mesg</font><br>";
	                $login_txt .= login_form();
	                $login_skined = skin($conf_skin,$login_txt, _("Client panel:") ." ". _("Login") );
	                $mypage = layout_login_and_languages($login_skined,$lang_sel);
	        }else{
	                // Draw the html forms
	                $HTML_admin_edit_data = drawAdminTools($admin);
	                $mypage = $HTML_admin_edit_data;
	        }
	}else{
	        $login_txt = login_form();
	        $login_skined = skin($conf_skin,$login_txt, _("Client panel:") ." ". _("Login") );
	        $mypage = layout_login_and_languages($login_skined,$lang_sel);
	}
	// Output the result !
	if(!isset($anotherHilight)) $anotherHilight = "";

	echo anotherPage("Client:","",$anotherHilight,makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));
}

function skin_LayoutClientPage_Default ($menu_content,$main_content,$main_content_title){
	global $conf_skin;

	$domain_list = skin($conf_skin,$menu_content, _("Your domains") );
	if($main_content != ""){
		$main = skin($conf_skin,$main_content,$main_content_title);
	}else{
		$main = "";
	}
	return "
<table width=\"100%\" height=\"100%\">
<tr><td valign=\"top\" width=\"220\" height=\"1\">
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>$domain_list</td></tr><tr><td>&nbsp</td></tr></table>
</td><td height=\"100%\">&nbsp;
</td><td align=\"left\" valign=\"top\" height=\"100%\">
        $main
</td></tr>
</table>
";
}
	
function skin_LayoutAdminPage_Default (){
	global $rub;
	global $adm_login;
	global $adm_pass;
	global $conf_session_expir_minute;
	global $pro_mysql_config_table;
	global $conf_skin;
	global $top_commands;

	global $adm_random_pass;

	$anotherTopBanner = anotherTopBanner("DTC","yes");

	///////////////////////
	// Make All the page //
	///////////////////////
	switch($rub){
	case "crm": // CRM TOOL
		$rightFrameCells[] = skin($conf_skin,DTCRMeditClients(), _("Customer's address") );
			if(isset($_REQUEST["id"]) && $_REQUEST["id"] != "" && $_REQUEST["id"] != 0){
			$rightFrameCells[] = skin($conf_skin,DTCRMclientAdmins(), _("Customer's administrators") );
		}
		$rightFrame = makeVerticalFrame($rightFrameCells);
		$leftFrameCells[] = skin($conf_skin,DTCRMlistClients(), _("Customers list") );
		$leftFrame = makeVerticalFrame($leftFrameCells);
		$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
		break;
	case "renewal":
		$out = drawRenewalTables();
		$zemain_content = skin($conf_skin,$out, _("Customer renewals") );
		break;
	case "monitor": // Monitor button
		$out = drawAdminMonitor();
		$zemain_content = skin($conf_skin,$out, _("Customer's bandwidth consumption") );
		break;
	case "graph":
		$zemain_content = skin($conf_skin,drawRrdtoolGraphs (), _("Server statistic graphs") );
		break;
	case "generate": // Gen Config Files
		$mainFrameCells[] = skin($conf_skin,$top_commands, _("Starting configuration file generation") );
		$the_iframe = "<br><br>"._("Please note that you need to create at least one mailbox and one FTP account in order for the daemon status to work correctly.")."<br><IFRAME src=\"deamons_state.php\" width=\"100%\" height=\"135\"></iframe>";
		$mainFrameCells[] = skin($conf_skin,$the_iframe, _("Daemon Status") ); // fixed bug by seeb
		// The console
		$mainFrameCells[] = skinConsole();
		$zemain_content = makeVerticalFrame($mainFrameCells);
		break;
	case "config": // Global Config
		$chooser_menu = drawDTCConfigMenu();
		$leftFrameCells[] = skin($conf_skin,$chooser_menu,"Menu");
		$leftFrame = makeVerticalFrame($leftFrameCells);
		$rightFrameCells[] = skin($conf_skin,drawDTCConfigForm(), _("DTC Configuration") );
		$rightFrame = makeVerticalFrame($rightFrameCells);

		$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
		break;
	case "product":
		$bla = productManager();
		$zemain_content = skin($conf_skin,$bla, _("Hosting Product Manager") );
		break;
	case "user": // User Config
	case "domain_config":
	case "adminedit":
	default: // No rub selected
		// A virtual admin edition
		// We have to call it first, in case it will generate a random pass (edition of an admin with inclusion of user's panel)
		$rightFrameCells[] = userEditForms($adm_login,$adm_pass);
		$rightFrameCells[] = skinConsole();
		$rightFrame = makeVerticalFrame($rightFrameCells);

		// Our list of admins
		// If random password was not set before this, we have to calculate it now!
		if(isset($adm_random_pass)){
			$rand = $adm_pass;
		}else{
			$rand = getRandomValue();
			$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
			$q = "UPDATE $pro_mysql_config_table SET root_admin_random_pass='$rand', pass_expire='$expirationTIME';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		}
		$leftFrameCells[] = skin($conf_skin,adminList($rand), _("Admin List") );
		// Make the frame
		$leftFrame = makeVerticalFrame($leftFrameCells);

		$zemain_content = anotherLeftFrame($leftFrame,$rightFrame);
		break;
	}
	if(function_exists("skin_Navbar")){
		$dtc_main_menu = skin_Navbar();
	}else{
		$dtc_main_menu = skin_Navbar_Default();
	}

	$the_page[] = skin($conf_skin,$dtc_main_menu, _("Domain Technologie Control : root admin console") );

	$the_page[] = $zemain_content;
	$pageContent = makeVerticalFrame($the_page);
	$anotherFooter = anotherFooter("Footer content<br><br>");

	if(!isset($anotherHilight))	$anotherHilight = "";
	if(!isset($anotherMenu))	$anotherMenu = "";

	echo anotherPage("admin:","",$anotherHilight,makePreloads(),$anotherTopBanner,$anotherMenu,$pageContent,$anotherFooter);
}

function skin_Navbar_Default() {
	global $rub;

	global $adm_login;
	global $adm_pass;

	global $pro_mysql_admin_table;

	if(isset($adm_login) && isset($adm_pass)){
		$added_logpass = "&adm_login=$adm_login&adm_pass=$adm_pass";
	}else{
		$added_logpass = "";
	}

	$menu = "";
	// User management icon
	if(isset($_REQUEST["rub"]) && $_REQUEST["rub"] != "" && $_REQUEST["rub"] != "user" && $_REQUEST["rub"] != "domain_config" && $_REQUEST["rub"] != "adminedit"){
		$menu .= "<a href=\"?rub=user$added_logpass\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/admins.png\"><br>".
		 _("User Administration") ;
	if(isset($_REQUEST["rub"]) && $_REQUEST["rub"] == "" && $_REQUEST["rub"] != "user"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";

	// CRM Button
	if(!isset($rub) || $rub != "crm"){
		$query = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login' AND (adm_pass='$adm_pass' OR adm_pass=SHA1('$adm_pass'));";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) == 1){
			$row = mysql_fetch_array($result);
			$url_addon = "&id=".$row["id_client"];
		}else{
			$url_addon = "";
		}
		$menu .= "<a href=\"?rub=crm&admlist_type=Names".$url_addon."\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/crm.png\"><br>".
		 _("Customer Management") ;
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "crm"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";
	// Monitor button
	if(!isset($rub) || $rub != "monitor"){
		$menu .= "<a href=\"?rub=monitor\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/bw_icon.png\"><br>".
		 _("Bandwidth Monitor") ;
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "monitor"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";

	// The graph button
	if(!isset($rub) || $rub != "graph"){
		$menu .= "<a href=\"?rub=graph\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/bw_icon.png\"><br>".
		 _("Server Monitor") ;
	if(!isset($rub) || $rub != "graph"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";

	if(!isset($rub) || $rub != "renewal"){
		$menu .= "<a href=\"?rub=renewal\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/renewals.png\"><br>".
		 _("Renewal Management") ;

	if(!isset($rub) || $rub != "renewal"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";

	if(file_exists("dtcrm")){
		// Product manager
		if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "product"){
			$menu .= "<a href=\"?rub=product\">";
		}
		$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/product_manager.png\"><br>
	". _("Hosting Product Manager") ;
		if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "product"){
			$menu .= "</a>";
		}
		$html_array[] = $menu;
		$menu = "";
	}

	// Generate daemon files icon
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "generate"){
		$menu .= "<a href=\"?rub=generate\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/daemons.png\"><br>".
		 _("Configuration Generation") ;
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "generate"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$menu = "";

	// Main config panel icon
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "config"){
		$menu .= "<a href=\"?rub=config\">";
	}
	$menu .= "<img border=\"0\" alt=\"*\" src=\"gfx/menu/config_panel.png\"><br>".
		 _("DTC general configuration") ;
	if(!isset($_REQUEST["rub"]) || $_REQUEST["rub"] != "config"){
		$menu .= "</a>";
	}
	$html_array[] = $menu;
	$dtc_main_menu = make_table_valign_top($html_array,sizeof($html_array));
	return $dtc_main_menu;
}

function userEditForms($adm_login,$adm_pass){
	global $adm_random_pass;

	global $conf_skin;
	global $addrlink;
	global $rub;

	$ret["err"] = 0;
	$ret["mesg"] = "No error";

	if(isset($adm_login) && $adm_login != "" && isset($adm_pass) && $adm_pass != ""){

		// Fetch all the selected user informations, Print a nice error message if failure.
		$admin = fetchAdmin($adm_login,$adm_pass);
		if(isset($adm_random_pass)){
			$pass = $adm_random_pass;
		}else{
			$pass = $adm_pass;
		}

		if(($error = $admin["err"]) != 0){
			// now print out all the stuff from our HTTP headers
			//$input = array_merge($_GET,    $_POST,
                        //     $_COOKIE, $_SERVER,
                        //     $_ENV,    $_FILES,
                        //     isset($_SESSION) ? $_SESSION : array()); 
			//foreach ($input as $k => $v) { 
			//	echo "$k - $input[$k]\n";	
			//}
			echo("Error fetching admin : $error line ".__LINE__." file ".__FILE__);
			$ret["err"] = $admin["err"];
			$ret["mesg"] = $admin["mesg"];
			return $ret;
		}

		$iface_select = "<table height=\"1\" border=\"0\" width=\"100%\">";
		$iface_select .= "<tr><td width=\"33%\" valign=\"top\"><center>";
		if($rub != "user" && $rub != ""){
			$iface_select .= "<a href=\"?rub=user&adm_login=$adm_login&adm_pass=$pass\">";
		}
		$iface_select .= "<img src=\"gfx/menu/client-interface.png\" width=\"48\" height=\"48\" border=\"0\"><br>
". _("Client interface") ;
		if($rub != "user" && $rub != ""){
			$iface_select .= "</a>";
		}
		$iface_select .= "</center></td><td width=\"33%\" valign=\"top\"><center>";
		if($rub != "domain_config"){
			$iface_select .= "<a href=\"?rub=domain_config&adm_login=$adm_login&adm_pass=$pass\">";
		}
		$iface_select .= "<img src=\"gfx/menu/domain-config.png\" width=\"48\" height=\"48\" border=\"0\"><br>
". _("Domain config") ;
		if($rub != "domain_config"){
			$iface_select .= "</a>";
		}
		$iface_select .= "</center></td><td width=\"33%\" valign=\"top\"><center>";
		if($rub != "adminedit"){
			$iface_select .= "<a href=\"?rub=adminedit&adm_login=$adm_login&adm_pass=$pass\">";
		}
		$iface_select .= "<img src=\"gfx/menu/user-editor.png\" width=\"48\" height=\"48\" border=\"0\"><br>
". _("Admin editor") ;
		if($rub != "adminedit"){
			$iface_select .= "</a>";
		}
		$iface_select .= "</center></td></tr></table>";

		$iface_skined = skin($conf_skin,$iface_select, _("User administration") . " $adm_login" );

		//fix up the $adm_login in case it changed because of session vars:
		//in case users play silly bugger with the "GET" variables
		$adm_login = $admin["info"]["adm_login"];

		// Draw the html forms
		if(isset($rub) && $rub == "adminedit"){
			$HTML_admin_edit_info = drawEditAdmin($admin);
			$user_config = skin($conf_skin,$HTML_admin_edit_info, _("Configuration of the virtual administrator") ."<i>\"$adm_login\"</i>");
//			return $user_config;
		}else if(isset($rub) && $rub == "domain_config"){
			$HTML_admin_domain_config = drawDomainConfig($admin);
			$user_config = skin($conf_skin,$HTML_admin_domain_config, _("Configuration of domains for") ." <i>\"$adm_login\"</i>");
		}else{
			$HTML_admin_edit_data = drawAdminTools($admin);
			$user_config = skin($conf_skin,$HTML_admin_edit_data, _("Domains for") ." ".$adm_login);
//			return $user_tools;
		}


		// All thoses tools in a simple table
		return "<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
	<tr>
		<tr><td width=\"100%\">$iface_skined</td></tr>
		<tr><td width=\"100%\">$user_config</td></tr>
		<tr><td height=\"100%\">&nbsp;</td></tr>
	</tr>
</table>
";
	}else{
		// If no user is in edition, draw a tool for adding an admin
		$add_a_user = drawNewAdminForm();
		return skin($conf_skin, $add_a_user, _("Add a virtual administrator"));
	}
}

?>
