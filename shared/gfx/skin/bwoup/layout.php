<?php

function layoutEmailPanel($menu_title,$menu_content,$main_title,$main_content){
	return '
  <table class="box_wnb_content_clientimport_box_wnb" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="box_wnb_nb" valign="top">
	<div class="box_wnb_nb_title"><div class="box_wnb_nb_title_left"><div class="box_wnb_nb_title_right"><div class="box_wnb_nb_title_mid">'.$menu_title.'</div></div></div></div>
	<div class="box_wnb_tv_container">
	<img src="gfx/skin/bwoup/gfx/spacer.gif" width="220" height="1">
	'.$menu_content.'
	</div>
      </td>
      <td class="box_wnb_content" valign="top">
	<div class="box_wnb_content_container">
	  <h2>'.$main_title.'</h2>
	  '.$main_content.'
	</div>
      </td>
    </tr>
    <tr>
      <td class="box_wnb_nb_bottom"></td>
      <td class="box_wnb_content_bottom"></td>
    </tr>
  </table>
';
}

function skin_EmailPage(){
	global $conf_skin;
	global $adm_email_login;

	global $page_metacontent;
	global $meta;
	global $confirm_javascript;
	global $java_script;
	global $skinCssString;
	
	global $adm_email_login;
	global $adm_email_pass;
	////////////////////////////////////
	// Create the top banner and menu //
	////////////////////////////////////
	$anotherTopBanner = anotherTopBanner("DTC");

	if($adm_email_login != "" && isset($adm_email_login) && $adm_email_pass != "" && isset($adm_email_pass)){
		$error = pass_check_email();
		// Fetch all the user informations, Print a nice error message if failure.
		if($error == false){
			$login_txt = "<font color=\"red\">" . _("Wrong login or password") . " !</font><br>";
			$login_txt .= login_emailpanel_form();
			$mypage = skin($conf_skin,$login_txt, _("Email panel: ") . _("Login") );
		}else{
			// Draw the html forms, login is successfull
			$admin = fetchMailboxInfos($adm_email_login,$adm_email_pass);
			$mypage = drawAdminTools_emailPanel($admin);
		}
	}else{
		$login_txt = login_emailpanel_form();
		$mypage = skin($conf_skin,$login_txt, _("Email panel: ") . _("Login") );
	}
	// Output the result !

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html>
<head>
<title>DTC: Client: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
</head>
<body id=\"page\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
	  <div id=\"outerwrapper\">
    <div id=\"wrapper\">

".makePreloads()."
$confirm_javascript
$java_script
<link rel=\"stylesheet\" href=\"gfx/skin/bwoup/skin.css\" type=\"text/css\">
$skinCssString

".anotherTopBanner("DTC","yes")."<div id=\"usernavbarreplacement\"></div>
<div id=\"content\"><div class=\"box_wnb_content_container\">".$mypage."</div></div>
<div id=\"footer\">".anotherFooter("Footer content<br><br>")."</div>
    </div>
</div>
</body>
</html>";
}

function skin_DisplayClientList($client_list){
	global $conf_use_javascript;

	$n = sizeof($client_list);
	$out = '<br><br><div class=\"box_wnb_nb_content\"><ul class="box_wnb_nb_items">';
	for($i=0;$i<$n;$i++){
		if($conf_use_javascript == "yes"){
			$dhtml = " onclick=\"document.location='".$client_list[$i]["link"]."'\" ";
		}else{
			$dhtml = " ";
		}
		$ahref= "<a href=\"".$client_list[$i]["link"]."\">";
		$aend = "</a>";
		if($client_list[$i]["selected"] == "yes"){
			$out .= "<li $dhtml><div class=\"box_wnb_nb_item_select\">$ahref".$client_list[$i]["text"]."$aend</div></li>";
		}else{
			$out .= "<li $dhtml><div class=\"box_wnb_nb_item\" onMouseOver=\"this.className='box_wnb_nb_item-hover';\" onMouseOut=\"this.className='box_wnb_nb_item';\">$ahref".$client_list[$i]["text"]."$aend</div></li>";
		}
	}
	$out .= "</ul></div>";
	return $out;
}

function skin_AlternateTreeView($url_link,$text,$selected,$arbo,$entrylink,$do_rollover,$icon){
	global $addrlink;
	global $conf_use_javascript;

	$level = sizeof( explode("/",$addrlink));
	$arbo_tree = explode("/",$arbo);

	$onclick = 'onClick="self.location=\''.$url_link.'\';"';

	$end_tree = "";
//	echo "Called skin_AlternateTreeView arbo: $arbo text: arbo0: ".$arbo_tree[0]." text: $text entrylink: $entrylink<br>\n"; 
	switch($arbo_tree[0]){
	default:
	case "none":
		$icone_tree = "";
		if($selected == 1){
			$class = "box_wnb_tv_leaf_select";
		}else{
			$class = "box_wnb_tv_leaf";
		}
		$mouseover_class = "box_wnb_tv_leaf-hover";
		$text = "&nbsp;$text";
		break;
	case "tree":
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/bwoup/gfx/treeview/box_wnb_tv_leaf_tree-branch.gif" /></a>';
		if($selected == 1){
			$class = "box_wnb_tv_leaf_leaf_select";
		}else{
			$class = "box_wnb_tv_leaf_leaf";
		}
		$mouseover_class = "box_wnb_tv_leaf_leaf-hover";
		$text = "&nbsp;&nbsp;$text";
		break;
	case "endtree":
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/bwoup/gfx/treeview/box_wnb_tv_leaf_tree-finalbranch.gif" /></a>';
		if($selected == 1){
			$class = "box_wnb_tv_leaf_leaf_select";
		}else{
			$class = "box_wnb_tv_leaf_leaf";
		}
		$end_tree = '<tr><td class="box_wnb_tv_leaf_openbottom" colspan="2"></td></tr>';
		$mouseover_class = "box_wnb_tv_leaf_leaf-hover";
		$text = "&nbsp;&nbsp;$text";
		break;
	case "plus":
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/bwoup/gfx/treeview/box_wnb_tv_tree_plus.gif" /></a>';
		$class = "box_wnb_tv_leaf";
		$mouseover_class = "box_wnb_tv_leaf-hover";
		$text = "&nbsp;$text";
		break;
	case "minus":
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/bwoup/gfx/treeview/box_wnb_tv_tree_minus.gif" /></a>';
		if($selected == 1){
			if($level > 1){
				$class = "box_wnb_tv_leaf_open";
			}else{
				$class = "box_wnb_tv_leaf_select";
			}
		}else{
			$class = "box_wnb_tv_open";
		}
		$mouseover_class = "box_wnb_tv_leaf-hover";
		$text = "&nbsp;$text";
		break;
	}
	
	$mouseover_stuff= "onMouseOver=\"this.className='$mouseover_class';\" onMouseOut=\"this.className='$class';\"";
	$onclick = " onClick=\"document.location='$url_link'\" ";

	$ahref = "<a href=\"$url_link\">";
	$aend = "</a>";

	$out = "
<tr>
	<td class=\"box_wnb_tv_tree\">".$icone_tree."</td>
	<td class=\"$class\" $mouseover_stuff>$ahref<div><img align=\"absbottom\" src=\"gfx/skin/bwoup/gfx/treeview/$icon\" width=\"16\" height\"16\" border=\"0\">".$text."</div>$aend</td>
</tr>$end_tree";
	return $out;
}

function skin_AliternateTreeViewContainer($tree_view){
	return '<div class="box_wnb_tv_container">
<table class="box_wnb_tv" border="0" cellpadding="0" cellspacing="0">'.$tree_view.'</table>';
}

function skin_displayAdminList($dsc){
	global $adm_login;
	global $adm_pass;
	global $rub;

	global $conf_use_javascript;

	$out = '<br><br><div class="box_wnb_nb_content"><ul class="box_wnb_nb_items">';

	
	$nbr = sizeof($dsc["admins"]);
	for($i=0;$i<$nbr;$i++){

			$dhtml = " ";
			$ahref= "<a href='?adm_login=".$dsc["admins"][$i]["adm_login"]."&adm_pass=".$dsc["admins"][$i]["adm_pass"]."&rub=$rub' style='display:block'>";
			$aend = "</a>";

		if($dsc["admins"][$i]["adm_login"] == $adm_login){
			$out .= "<li $dhtml><div class=\"box_wnb_nb_item_select\">$ahref".$dsc["admins"][$i]["text"]."$aend</div></li>";
		}else{
			$out .= "<li $dhtml><div class=\"box_wnb_nb_item\" onMouseOver=\"this.className='box_wnb_nb_item-hover';\" onMouseOut=\"this.className='box_wnb_nb_item';\">$ahref".$dsc["admins"][$i]["text"]."$aend</div></li>";
		}
	}
	$out .= "</ul></div>";
	return $out;
}

function skin_ClientPage (){
	global $adm_pass;
	global $adm_login;
	global $conf_skin;

	global $page_metacontent;
	global $meta;
	global $confirm_javascript;
	global $java_script;
	global $skinCssString;
	global $console;

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
			$HTML_admin_edit_data = '<div class="box_wnb_content_container">'.drawAdminTools($admin).'</div>';
			$mypage = $HTML_admin_edit_data;
		}
	}else{
		$login_txt = login_form();
                $mypage = skin($conf_skin,$login_txt, _("Client panel:") ." ". _("Login") );
        }
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html>
<head>
<title>DTC: Client: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
</head>
<body id=\"page\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
	  <div id=\"outerwrapper\">
    <div id=\"wrapper\">

".makePreloads()."
$confirm_javascript
$java_script
<link rel=\"stylesheet\" href=\"gfx/skin/bwoup/skin.css\" type=\"text/css\">
$skinCssString

".anotherTopBanner("DTC","yes")."<div id=\"usernavbarreplacement\"></div>
<div id=\"content\"><div class=\"box_wnb_content_container\">".$mypage."</div></div>
<div id=\"footer\">".anotherFooter("Footer content<br><br>")."</div>
    </div>
</div>
</body>
</html>";
}

function skin_NewAccountPage ($form){
	global $conf_skin;
	global $page_metacontent;
	global $meta;
	global $confirm_javascript;
	global $java_script;
	global $skinCssString;

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html>
<head>
<title>DTC: Client: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
</head>
<body id=\"page\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
	  <div id=\"outerwrapper\">
    <div id=\"wrapper\">

".makePreloads()."
$confirm_javascript
$java_script
<link rel=\"stylesheet\" href=\"gfx/skin/bwoup/skin.css\" type=\"text/css\">
$skinCssString

".anotherTopBanner("DTC","yes")."<div id=\"usernavbarreplacement\"></div>
<div id=\"content\"><div class=\"box_wnb_content_container\">".$form."</div></div>
<div id=\"footer\">".anotherFooter("Footer content<br><br>")."</div>
    </div>
</div>
</body>
</html>";
}

function skin_DTCConfigMenu ($dsc){
	if(!isset($_REQUEST["sousrub"])){
		$sousrub = "general";
        }else{
          $sousrub = $_REQUEST["sousrub"];
        }

		
		
        $out = '<ul class="box_wnb_nb_items">';

        $keys = array_keys($dsc);
        $nbr_entry = sizeof($dsc);
        for($i=0;$i<$nbr_entry;$i++){
        	if($keys[$i] == $sousrub){
			$out .= '<li><div style="this.style.cursor=\'pointer\';" class="box_wnb_nb_item_select" onClick="document.location=\'?rub=config&sousrub='.$keys[$i].'\'"><a href="?rub=config&sousrub='.$keys[$i].'"><img width="16" height="16" src="gfx/skin/bwoup/gfx/config-icon/'.$dsc[ $keys[$i] ]["icon"].'" align="top"> '.$dsc[ $keys[$i] ]["text"].'</a></div></li>';
		}else{
			$out .= '<li><div style="this.style.cursor=\'pointer\';" onClick="document.location=\'?rub=config&sousrub='.$keys[$i].'\'" class="box_wnb_nb_item" onMouseOver="this.className=\'box_wnb_nb_item-hover\';" onMouseOut="this.className=\'box_wnb_nb_item\';"><a href="?rub=config&sousrub='.$keys[$i].'"><img width="16" height="16" src="gfx/skin/bwoup/gfx/config-icon/'.$dsc[ $keys[$i] ]["icon"].'" align="top"> '.$dsc[ $keys[$i] ]["text"].'</a></div></li>';
		}
        }
        $out .= "</ul>";
        return $out;
}

function skin_LayoutClientPage ($menu_content,$main_content,$main_content_title){
	global $conf_skin;

	if($main_content == ""){
		$main_content = "&nbsp;";
		$main_content_title = "&nbsp;";
	}

	return '
  <table class="box_wnb_content_clientimport_box_wnb" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="box_wnb_nb" valign="top">
	<div class="box_wnb_nb_title"><div class="box_wnb_nb_title_left"><div class="box_wnb_nb_title_right"><div class="box_wnb_nb_title_mid">'. _("Your domains") .'</div></div></div></div>
	<div class="box_wnb_tv_container">
	<img src="gfx/skin/bwoup/gfx/spacer.gif" width="280" height="1">
	'.$menu_content.'
	</div>
      </td>
      <td class="box_wnb_content" valign="top">
	<div class="box_wnb_content_container">
	  <h2>'.$main_content_title.'</h2>
	  '.$main_content.'
	</div>
      </td>
    </tr>
    <tr>
      <td class="box_wnb_nb_bottom"></td>
      <td class="box_wnb_content_bottom"></td>
    </tr>
  </table>
';
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

function skin_Navbar (){
	global $rub;

	$out = '<div id="navbar"><div id="navbar_left"></div><ul id="navbar_items">';

	if(!isset($rub) || $rub == "" || $rub == "user" || $rub == "domain_config" || $rub == "adminedit"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_user.gif" alt="'. _("User Administration") .'"><br />'. _("User Administration") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=user"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_user.gif" alt="'. _("User Administration") .'"><br />'. _("User Administration") .'</a></li>';
	}
	if(isset($rub) && $rub == "crm"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_customer.gif" alt="'. _("Customer Management") .'"><br />'. _("Customer Management") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=crm"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_customer.gif" alt="'. _("Customer Management") .'"><br />'. _("Customer Management") .'</a></li>';
	}
	if(isset($rub) && $rub == "monitor"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_bandwith.gif" alt="'. _("Bandwidth Monitor") .'"><br />'. _("Bandwidth Monitor") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=monitor"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_bandwith.gif" alt="'. _("Bandwidth Monitor") .'"><br />'. _("Bandwidth Monitor") .'</a></li>';
	}
	if(isset($rub) && $rub == "graph"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif" alt="'. _("Server Monitor") .'"><br />'. _("Server Monitor") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=graph"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif" alt="'. _("Server Monitor") .'"><br />'. _("Server Monitor") .'</a></li>';
	}
	if(isset($rub) && $rub == "renewal"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_renewal.gif" alt="'. _("Renewal Management") .'"><br />'. _("Renewal Management") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=renewal"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_renewal.gif" alt="'. _("Renewal Management") .'"><br />'. _("Renewal Management") .'</a></li>';
	}
	if(isset($rub) && $rub == "product"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_product.gif" alt="'. _("Hosting Product manager") .'"><br />'. _("Hosting Product Manager") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=product"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_product.gif" alt="'. _("Hosting Product Manager") .'"><br />'. _("Hosting Product Manager") .'</a></li>';
	}
	if(isset($rub) && $rub == "generate"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_daemon.gif" alt="'. _("Configuration Generation") .'"><br />'. _("Configuration Generation") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=generate"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_daemon.gif" alt="'. _("Configuration Generation") .'"><br />'. _("Configuration Generation") .'</a></li>';
	}
	if(isset($rub) && $rub == "config"){
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_config.gif" alt="'. _("DTC General Configuration") .'"><br />'. _("DTC General Configuration") .'</div></li>';
	}else{
		$out .= '<li><a href="?rub=config"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_config.gif" alt="'. _("DTC General Configuration") .'"><br />'. _("DTC General Configuration") .'</a></li>';
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
	global $pro_mysql_tik_admins_table;
	global $conf_skin;

	global $top_commands;

	global $adm_random_pass;

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
		$admin_list = DTCRMlistClients();
		$client_editor = DTCRMeditClients();
		if(isset($_REQUEST["id"]) && $_REQUEST["id"] != "" && $_REQUEST["id"] != 0){
			$client_editor .= DTCRMclientAdmins();
		}
		$zemain_content = '
<table class="box_wnb" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="box_wnb_nb" valign="top">
    	<div class="box_wnb_nb_title"><div class="box_wnb_nb_title_left"><div class="box_wnb_nb_title_right"><div class="box_wnb_nb_title_mid">'. _("Customers list") .'</div></div></div></div>
    	'.$admin_list.'
    </td>
    <td class="box_wnb_content" valign="top">
      <div class="box_wnb_content_container">
      <h2>'. _("Customer's address") .'</h2>
      '.$client_editor.'
      </div>
    </td>
  </tr>
  <tr>
    <td class="box_wnb_nb_bottom"></td>
    <td class="box_wnb_content_bottom" valign="top"></td>
  </tr>
</table>';
		break;
	case "renewal":
		$out = drawRenewalTables();
		$zemain_content = skin($conf_skin,$out, _("Customer renewals"));
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
		$zemain_content = '
<table class="box_wnb" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="box_wnb_nb" valign="top">
    	<div class="box_wnb_nb_title"><div class="box_wnb_nb_title_left"><div class="box_wnb_nb_title_right"><div class="box_wnb_nb_title_mid">'._("Menu").'</div></div></div></div>
    	'.drawDTCConfigMenu().'
    </td>
    <td class="box_wnb_content" valign="top">
    	 <div class="box_wnb_content_container">
	  <h2>'. _("DTC Configuration") .'</h2>
'.drawDTCConfigForm().'
	</div>
    </td>
  </tr>
  <tr>
    <td class="box_wnb_nb_bottom"></td>
    <td class="box_wnb_content_bottom" valign="top"></td>
  </tr>
</table>';
		break;
	case "product":
		$bla = productManager();
		$zemain_content = skin($conf_skin,$bla, _("Hosting Product Manager") );
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
			$adm_random_pass = $rand;
			$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
			$q = "UPDATE $pro_mysql_tik_admins_table SET pass_next_req='$rand', pass_expire='$expirationTIME' WHERE pseudo='".$_SERVER["PHP_AUTH_USER"]."';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
		}
		$skinedConsole = '<table cellpadding="0" cellspacing="0" class="console">
	  <tr><td class="console_title">'. _("Console output") .' :</td>
	  </tr><tr>		<td class="console_output"><pre>'.$_SERVER['SERVER_NAME'].':&gt;_'.$console.'<br><span id="console_content" class="console_content"></span></pre></td></tr></table>';

	  $adm_list = adminList($rand).'
	  <div class="voider"></div>
	  <br /><br />
		<div class="voider"></div>';

		$zemain_content = '
<table class="box_wnb" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="box_wnb_nb" valign="top">
    	<div class="box_wnb_nb_title"><div class="box_wnb_nb_title_left"><div class="box_wnb_nb_title_right"><div class="box_wnb_nb_title_mid">'. _("Admin List") .'</div></div></div></div>
    	'.$adm_list.'
    </td>
    <td class="box_wnb_content" valign="top">
      <div class="box_wnb_content_container">
      <h2>'. _("User administration") ." $adm_login".'</h2>
      '.$bwoup_user_edit
      .$skinedConsole.'
      </div>
    </td>
  </tr>
  <tr>
    <td class="box_wnb_nb_bottom"></td>
    <td class="box_wnb_content_bottom" valign="top"></td>
  </tr>
</table>';
		break;
	}

	$dtc_main_menu = skin_Navbar();
	$anotherFooter = anotherFooter("Footer content<br><br>");

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html>
<head>
<title>DTC: Admin: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
</head>
<body id=\"page\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
	  <div id=\"outerwrapper\">
    <div id=\"wrapper\">

".makePreloads()."
$confirm_javascript
$java_script
<link rel=\"stylesheet\" href=\"gfx/skin/bwoup/skin.css\" type=\"text/css\">
$skinCssString

".anotherTopBanner("DTC","yes").$dtc_main_menu."
<div id=\"content\">".$zemain_content."</div>
<div id=\"footer\">".anotherFooter("Footer content<br><br>")."</div>
    </div>
</div>
</body>
</html>";

}

function bwoupUserEditForms($adm_login,$adm_pass){
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
			echo(_("Error fetching admin")." : $error");
			$ret["err"] = $admin["err"];
			$ret["mesg"] = $admin["mesg"];
			return $ret;
		}

		$out = '<ul class="box_wnb_content_nb">';
		if($rub != "user" && $rub != ""){
			$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?adm_login=$adm_login&adm_pass=$pass&rub=user\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\"  align=\"absmiddle\" border=\"0\"> ". _("Client interface") ."</a></li>";
		}else{
			$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?adm_login=$adm_login&adm_pass=$pass&rub=user\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("Client interface") ."</a></li>";
		}
		$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
		if($rub != "domain_config"){
			$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?adm_login=$adm_login&adm_pass=$pass&rub=domain_config\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_domainconfig.gif\" align=\"absmiddle\" border=\"0\">". _("Domain config") ."</a></li>";
		}else{
			$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?adm_login=$adm_login&adm_pass=$pass&rub=domain_config\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_domainconfig.gif\" align=\"absmiddle\" border=\"0\">". _("Domain config") ."</a></li>";
		}
		$out .= '<li class="box_wnb_content_nb_item_vsep"></li>';
		if($rub != "adminedit"){
			$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?adm_login=$adm_login&adm_pass=$pass&rub=adminedit\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_admineditor.gif\" align=\"absmiddle\" border=\"0\">". _("Admin editor") ."</a></li>";
		}else{
			$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?adm_login=$adm_login&adm_pass=$pass&rub=adminedit\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_admineditor.gif\" align=\"absmiddle\" border=\"0\">". _("Admin editor") ."</a></li>";
		}
		$out .= "</ul>";

		//fix up the $adm_login in case it changed because of session vars:
		//in case users play silly bugger with the "GET" variables
		$adm_login = $admin["info"]["adm_login"];

		// Draw the html forms
		if(isset($rub) && $rub == "adminedit"){
			$out .= drawEditAdmin($admin);
		}else if(isset($rub) && $rub == "domain_config"){
			$out .= drawDomainConfig($admin);
		}else{
			$out .= '<table class="box_wnb_content_clientimport"><tr><td>'.drawAdminTools($admin).'</td></tr></table>';
		}
		return $out;
	}else{
		// If no user is in edition, draw a tool for adding an admin
		return drawNewAdminForm();
	}
}

?>
