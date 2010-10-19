<?php
// Email page content
function layoutEmailPanel($menu_title,$menu_content,$main_title,$main_content){
	return '<div id="container"><div class="contentslide"><div class="thtitle">'.$main_title.'</div><br />'.$main_content.'<br /></div></div><a class="trigger" href="#">'."Menu".'</a>
		<div class="panelside"><div class="box_wnb_nb_title_left"><div class="dtctitle">'.$menu_title.'</div></div>'.$menu_content.'<div style="clear:both;"></div></div>';
}
// Email page content

// mail page général layout
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
	switch($lang){
	case "fr":
		$iepower = "IePower_fr.js";
		break;
	case "en":
		$iepower = "IePower_en.js";
		break;
	default:
		$iepower = "IePower_en.js";
		break;
	}
	////////////////////////////////////
	// Create the top banner and menu //
	////////////////////////////////////
	$anotherTopBanner = anotherTopBanner("DTC");

	if($adm_email_login != "" && isset($adm_email_login) && $adm_email_pass != "" && isset($adm_email_pass)){
		$error = pass_check_email();
		// Fetch all the user informations, Print a nice error message if failure.
		if($error == false){
			$mesg = $admin["mesg"];
			$login_txt = "<div id=\"container\"><div class=\"highlight\"><font color=\"red\">Wrong login or password !</font></div>";
			$login_txt .= '<div class="contentlogpanel">'.login_emailpanel_form().'</div></div>';
			$mypage = skin($conf_skin,$login_txt, _("Email panel: ") . _("Login") );
		}else{
			// Draw the html forms, login is successfull
			$admin = fetchMailboxInfos($adm_email_login,$adm_email_pass);
			//$mypage = "<div id=\"container\"><div class=\"contentslide\">".drawAdminTools_emailPanel($admin)."</div></div>";
			$mypage = drawAdminTools_emailPanel($admin);
		}
	}else{
		$login_txt = '<div id="container"><div class="contentlogpanel">'.login_emailpanel_form().'</div></div>';
		$mypage = skin($conf_skin,$login_txt, _("Email panel: ") . _("Login") );
	}
	// Output the result !



	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\"ltr\" lang=\"en\" xml:lang=\"en\">
<head>
<title>DTC: Client: ".$_SERVER['SERVER_NAME']."</title>
$meta
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/skin.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/slide.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/style.css\" />
<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script>
<!--[if IE ]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/$iepower\"></script>
<![endif]-->
<!--[if lte IE 6]>
	<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/pngfix/supersleight-min.js\"></script>
<![endif]-->
<script src=\"gfx/skin/grayboard/js/slide.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.dropshadow.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.timers.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/mbTooltip.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/DD_roundies-min.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/gradualfader.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/easing.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds.js\"></script>
<!--[if gte IE 7]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds_IE.js\"></script>
<![endif]-->
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/general.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function(){
	$(\".trigger\").click(function(){
		$(\".panelside\").toggle(\"fast\");
		$(this).toggleClass(\"active\");
		return false;
	});
});
</script>
</head>
<body id=\"page\">
<div id=\"outerwrapper\">
	<div id=\"wrapper\">
		".makePreloads()."
		$confirm_javascript
		$java_script
		$skinCssString
		".anotherTopBanner("DTC","yes")."
		<div id=\"usernavbarreplacement\"></div>
		<div>".$mypage."</div>
		<div id=\"footer\">".anotherFooter("Footer content<br /><br />")."grayboard template for DTC made by <a href=\"http://www.labestiole.net\" title=\"la bestiole\" target=\"_blank\">cali</a></div>
    </div>
</div>
<script type=\"text/javascript\">gradualFader.init()</script>
</body>
</html>";
}

function skin_DisplayClientList($client_list){
	global $conf_use_javascript;

	$n = sizeof($client_list);
	$out = '<br /><br /><ul class="box_wnb_nb_items">';
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
	$out .= "</ul>";
	return $out;
}

function skin_AlternateTreeView($url_link,$text,$selected,$arbo,$entrylink,$do_rollover,$icon){
	global $addrlink;
	global $conf_use_javascript;

	$level = sizeof( explode("/",$addrlink));
	$arbo_tree = explode("/",$arbo);

	$onclick = 'onClick="self.location=\''.$url_link.'\';"';

	$end_tree = "";
//	echo "Called skin_AlternateTreeView arbo: $arbo text: arbo0: ".$arbo_tree[0]." text: $text entrylink: $entrylink<br />\n"; 
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
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/grayboard/gfx/treeview/box_wnb_tv_leaf_tree-branch.gif" /></a>';
		if($selected == 1){
			$class = "box_wnb_tv_leaf_leaf_select";
		}else{
			$class = "box_wnb_tv_leaf_leaf";
		}
		$mouseover_class = "box_wnb_tv_leaf_leaf-hover";
		$text = "&nbsp;&nbsp;$text";
		break;
	case "endtree":
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/grayboard/gfx/treeview/box_wnb_tv_leaf_tree-finalbranch.gif" /></a>';
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
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/grayboard/images/plus.png" /></a>';
		$class = "box_wnb_tv_leaf";
		$mouseover_class = "box_wnb_tv_leaf-hover";
		$text = "&nbsp;$text";
		break;
	case "minus":
		$icone_tree = '<a href="'.$url_link.'"><img align="absbottom" src="gfx/skin/grayboard/images/minus.png" /></a>';
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
	<td class=\"$class\" $mouseover_stuff>$ahref<div>".$text."</div>$aend</td>
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

	$out = '<br /><br /><ul class="box_wnb_nb_items">';

	
	$nbr = sizeof($dsc["admins"]);
	for($i=0;$i<$nbr;$i++){

			$dhtml = " ";
			$ahref= "<a href='?adm_login=".$dsc["admins"][$i]["adm_login"]."&adm_pass=".$dsc["admins"][$i]["adm_pass"]."&amp;rub=$rub' style='display:block'>";
			$aend = "</a>";

		if($dsc["admins"][$i]["adm_login"] == $adm_login){
			$out .= "<li $dhtml><div class=\"box_wnb_nb_item_select\">$ahref".$dsc["admins"][$i]["text"]."$aend</div></li>";
		}else{
			$out .= "<li $dhtml><div class=\"box_wnb_nb_item\" onMouseOver=\"this.className='box_wnb_nb_item-hover';\" onMouseOut=\"this.className='box_wnb_nb_item';\">$ahref".$dsc["admins"][$i]["text"]."$aend</div></li>";
		}
	}
	$out .= "</ul>";
	return $out;
}
// client page general layout
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
	global $lang;
	
	switch($lang){
	case "fr":
		$clmenu_expand = "Afficher";
		$clmenu_close = "Masquer";
		$iepower = "IePower_fr.js";
		break;
	case "en":
		$clmenu_expand = "Show";
		$clmenu_close = "Hide";
		$iepower = "IePower_en.js";
		break;
	default:
		$clmenu_expand = "Show";
		$clmenu_close = "Hide";
		$iepower = "IePower_en.js";
		break;
	}

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
			$login_txt = '<div id="container_user"><div class="highlight">' . _("Error") ." $error ". _("fetching admin: ") ."<font color=\"red\">$mesg</font><br /></div>";
			$login_txt .= '<div class="contentlogpanel">'.login_form().'</div></div>';
			$login_skined = skin($conf_skin,$login_txt, _("Client panel:") ." ". _("Login") );
			$mypage = layout_login_and_languages($login_skined,$lang_sel);
		}else{
			// Draw the html forms
			$HTML_admin_edit_data = '<div class="box_wnb_content_container">'.drawAdminTools($admin).'</div>';
			$mypage = $HTML_admin_edit_data;
		}
	}else{
		$login_txt = '<div id="container_user"><div class="contentlogpanel">'.login_form().'</div></div>';
                $mypage = skin($conf_skin,$login_txt, _("Client panel:") ." ". _("Login") );
        }
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\"ltr\" lang=\"en\" xml:lang=\"en\">
<head>
<title>DTC: Client: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/skin.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/slide.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/style.css\" />
<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script>
<!--[if IE ]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/$iepower\"></script>
<![endif]-->
<!--[if lte IE 6]>
	<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/pngfix/supersleight-min.js\"></script>
<![endif]-->
<script src=\"gfx/skin/grayboard/js/slide.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.dropshadow.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.timers.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/mbTooltip.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/DD_roundies-min.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/gradualfader.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/easing.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function() {
	$(\"a#controlbtn\").click(function(e) {
		e.preventDefault();
		var slidepx=$(\"div#linkblock\").width() + 10;
		if ( !$(\"div#ClientMenuContent\").is(':animated') ) { 
			if (parseInt($(\"div#ClientMenuContent\").css('marginLeft'), 10) < slidepx) {
				$(this).removeClass('close').html('".$clmenu_close."&nbsp;". _("Your domains") ."');
				margin = \"+=\" + slidepx;
			} else {
				$(this).addClass('close').html('".$clmenu_expand."&nbsp;". _("Your domains") ."');
				margin = \"-=\" + slidepx; 		
			}	
        		$(\"div#ClientMenuContent\").animate({ 
        			marginLeft: margin
      			}, {
                    	duration: 'slow',
                    	easing: 'easeOutBounce'
                	});
		} 
	}); 
});
</script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds.js\"></script>
<!--[if gte IE 7]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds_IE.js\"></script>
<![endif]-->
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/general.js\"></script>
</head>
<body id=\"page\">
	<div id=\"outerwrapper\">
		<div id=\"wrapper\">
			".makePreloads()."
			$confirm_javascript
			$java_script
			$skinCssString
			".anotherTopBanner("DTC","yes")."<div id=\"usernavbarreplacement\"></div>
			".$mypage."
			<div id=\"footer\">".anotherFooter("Footer content<br /><br />")."GrayBoard template for DTC made by <a href=\"http://solution.adquate.net\" title=\"AdQuate web site\" target=\"_blank\">cali</a></div>
		</div>
	</div>
<script type=\"text/javascript\">gradualFader.init()</script>
</body>
</html>";
}
// complete registering page skin
function skin_NewAccountPage ($form){
	global $conf_skin;
	global $page_metacontent;
	global $meta;
	global $confirm_javascript;
	global $java_script;
	global $skinCssString;
	global $lang;
	switch($lang){
	case "fr":
		$iepower = "IePower_fr.js";
		break;
	case "en":
		$iepower = "IePower_en.js";
		break;
	default:
		$iepower = "IePower_en.js";
		break;
	}


	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\"ltr\" lang=\"en\" xml:lang=\"en\">
<head>
<title>DTC: Client: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/skin.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/slide.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/style.css\" />
<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script>
<!--[if IE ]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/$iepower\"></script>
<![endif]-->
<!--[if lte IE 6]>
	<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/pngfix/supersleight-min.js\"></script>
<![endif]-->
<script src=\"gfx/skin/grayboard/js/slide.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.dropshadow.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.timers.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/mbTooltip.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/DD_roundies-min.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/gradualfader.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/easing.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/general.js\"></script>
<!--[if gte IE 7]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds_IE.js\"></script>
<![endif]-->
</head>
<body id=\"page\">
	<div id=\"outerwrapper\">
		
			".makePreloads()."
			$confirm_javascript
			$java_script
			$skinCssString
			".anotherTopBanner("DTC","yes")."			
			<div id=\"usernavbarreplacement\"></div>
			<div id=\"container\"><div class=\"contentslide\">".$form."</div></div>
			<div id=\"footer\">".anotherFooter("Footer content<br /><br />")."GrayBoard template for DTC made by <a href=\"http://solution.adquate.net\" title=\"AdQuate web site\" target=\"_blank\">cali</a></div>
		
	</div>
<script type=\"text/javascript\">gradualFader.init()</script>
</body>
</html>";
}
// admin DTC left menu
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
			$out .= '<li><div style="this.style.cursor=\'pointer\';" class="box_wnb_nb_item_select" onClick="document.location=\'?rub=config&sousrub='.$keys[$i].'\'"><a href="?rub=config&sousrub='.$keys[$i].'">'.$dsc[ $keys[$i] ]["text"].'</a></div></li>';
		}else{
			$out .= '<li><div style="this.style.cursor=\'pointer\';" onClick="document.location=\'?rub=config&sousrub='.$keys[$i].'\'" class="box_wnb_nb_item" onMouseOver="this.className=\'box_wnb_nb_item-hover\';" onMouseOut="this.className=\'box_wnb_nb_item\';"><a href="?rub=config&sousrub='.$keys[$i].'">'.$dsc[ $keys[$i] ]["text"].'</a></div></li>';
		}
        }
        $out .= "</ul>";
        return $out;
}
// client page content 
function skin_LayoutClientPage ($menu_content,$main_content,$main_content_title){
	global $conf_skin;
	global $lang;
	
	switch($lang){
	case "fr":
		$clmenu_expand = "Afficher";
		$clmenu_close = "Masquer";
		break;
	case "en":
		$clmenu_expand = "Show";
		$clmenu_close = "Hide";
		break;
	default:
		$clmenu_expand = "Show";
		$clmenu_close = "Hide";
		break;
	}

	if($main_content == ""){
		$main_content = "&nbsp;";
		$main_content_title = "&nbsp;";
	}
// client page content with picts
	return '
<div id="control"><a id="controlbtn" class="open" href="#" alt="'.$clmenu_expand.'/'.$clmenu_close.'&nbsp;">'.$clmenu_close.'&nbsp;'. _("Your domains") .'</a></div>
<div class="box_wnb_content_clientimport_box_wnb">
	<div id="ClientMenuContent">
		<div id="linkblock">
			<div class="box_wnb_nb">
				<div class="box_wnb_nb_title">
					<div class="box_wnb_nb_title_left">
						<div class="box_wnb_nb_title_right">
							<div class="box_wnb_nb_title_mid">'. _("Your domains") .'</div>
						</div>
					</div>
				</div>
				<div class="box_wnb_tv_container">
					<img src="gfx/skin/grayboard/gfx/spacer.gif" width="220" height="1" />
					'.$menu_content.'
				</div>
			</div>
		</div>
	</div>
	<div class="box_wnb_content">
		<div id="container_user"><div class="contentslide"><div class="thtitle">'.$main_content_title.'</div><br />'.$main_content.'<br /></div></div>
	</div>
</div>';
// client page content in text mode
	return "
<table width=\"100%\">
	<tr>
		<td width=\"220\" height=\"1\">
			<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
				<tr>
					<td>$domain_list</td>
				</tr>
				<tr>
					<td>&nbsp</td>
				</tr>
			</table>
		</td>
		<td height=\"100%\">&nbsp;</td>
		<td align=\"left\" height=\"100%\">$main</td>
	</tr>
</table>
";
}
// main admin navbar function
function skin_Navbar (){
	global $rub;
	global $lang;
	
	switch($lang){
	case "fr":
		$tp_expand = "D&eacute;plier";
		$tp_close = "Replier";
		break;
	case "en":
		$tp_expand = "Expand";
		$tp_close = "Close";
		break;
	default:
		$tp_expand = "Expand";
		$tp_close = "Close";
		break;
	}
	
	$out = '<div id="toppanel"><div id="paneltop"><div class="clearfix contenttop"><div class="left"><div id="navbar"><ul id="navbar_items">';

	if(!isset($rub) || $rub == "" || $rub == "user" || $rub == "domain_config" || $rub == "adminedit"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("Users administration") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/users.png" alt="" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=user" title="'. _("Users administration") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/users.png" alt=""class="gradualfader" /></a></li>';
	}
	if(isset($rub) && $rub == "crm"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("Customer management") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/clients.png" alt="" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=crm" title="'. _("Customer management") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/clients.png" alt=""class="gradualfader" /></a></li>';
	}
	if(isset($rub) && $rub == "monitor"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("Bandwidth monitor") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/bandwidth.png" alt="" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=monitor" title="'. _("Bandwidth monitor") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/bandwidth.png" alt=""class="gradualfader" /></a></li>';
	}
	if(isset($rub) && $rub == "graph"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("Server monitor") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/system-monitor.png" alt="" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=graph" title="'. _("Server monitor") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/system-monitor.png" alt=""class="gradualfader" /></a></li>';
	}
	if(isset($rub) && $rub == "renewal"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("Renewals management") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/renew.png" alt="" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=renewal" title="'. _("Renewals management") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/renew.png" alt=""class="gradualfader" /></a></li>';
	}
	if(isset($rub) && $rub == "product"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("Hosting product manager") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/packs.png" alt="" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=product" title="'. _("Hosting product manager") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/packs.png" alt=""class="gradualfader" /></a></li>';
	}
	if(isset($rub) && $rub == "generate"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("Configuration generation") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/conf_gen.png" alt="" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=generate" title="'. _("Configuration generation") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/conf_gen.png" alt=""class="gradualfader" /></a></li>';
	}
	if(isset($rub) && $rub == "config"){
		$out .= '<li><div class="navbar_item-select"><a href="#" title="'. _("DTC general configuration") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/dtc_admin.png" alt="'. _("DTC general configuration") .'" /></a></div></li>';
	}else{
		$out .= '<li><a href="?rub=config" title="'. _("DTC general configuration") .'"><img width="46" height="55" src="gfx/skin/grayboard/images/dtc_admin.png" alt="'. _("DTC general configuration") .'" class="gradualfader" /></a></li>';
	}
	$out .= '</ul></div></div></div></div>
	<div class="tab"><ul class="login"><li class="left">&nbsp;</li><li>Admin</li><li class="sep">|</li><li id="toggle"><a id="open" class="open" href="#">' .$tp_expand. '</a><a id="close" style="display: none;" class="close" href="#">' .$tp_close. '</a></li><li class="right">&nbsp;</li></ul></div></div>';
		
return $out;
}
//admin page content
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
	
	global $lang;
	
	switch($lang){
	case "fr":
		$clmenu_expand = "Afficher";
		$clmenu_close = "Masquer";
		$iepower = "IePower_fr.js";
		break;
	case "en":
		$clmenu_expand = "Show";
		$clmenu_close = "Hide";
		$iepower = "IePower_en.js";
		break;
	default:
		$clmenu_expand = "Show";
		$clmenu_close = "Hide";
		$iepower = "IePower_en.js";
		break;
	}

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
// main content
		$zemain_content = '<div id="container"><div class="contentslide"><div class="thtitle">'. _("Customer's address") .'</div><br />'.$client_editor.'<br /></div></div>
					<div class="panelside"><div class="box_wnb_nb_title_left"><div class="dtctitle">'. _("Customers list") .'</div></div>'.$admin_list.'<div style="clear:both;"></div></div>
					<a class="trigger" href="#">'. _("list") .'</a>';
		break;
//renewal page skin
	case "renewal":
		$out = drawRenewalTables();
		$zemain_content = '<div id="container"><div class="contentslide"><br />'.drawRenewalTables().'<br /></div></div>';
		break;
// server monitor skin
	case "monitor": // Monitor button
		$out = drawAdminMonitor();
		$zemain_content ='<div id="container"><div class="contentslide">'.drawAdminMonitor().'</div></div>';
		break;
// server RDDtool graphs skin
	case "graph":
		$zemain_content ='<div id="container"><div class="contentslide">'.drawRrdtoolGraphs ().'</div></div>';
		break;
// deamon gen skin
	case "generate": // Gen Config Files
		$mainFrameCells[] =''.$top_commands.'<br />';
		$the_iframe = "<IFRAME src=\"deamons_state.php\" width=\"100%\" height=\"135\"></iframe>";
		$mainFrameCells[] = ''.$the_iframe.'<br />';
		// The console
		$mainFrameCells[] = ''.skinConsole().'<br />';
		$zemain_content = '<div id="container"><div class="contentslide">'.makeVerticalFrame($mainFrameCells).'</div></div>';
		break;
// main config skin
	case "config": 
// Global Config
		$zemain_content = '<div id="container"><div class="contentslide"><div class="thtitle">'. _("DTC Configuration") .'</div><br />'.drawDTCConfigForm().'<br /></div></div>
					<div class="panelside"><div class="box_wnb_nb_title_left"><div class="dtctitle">'."Menu".'</div></div>'.drawDTCConfigMenu().'<div style="clear:both;"></div></div>
					<a class="trigger" href="#">'."Menu".'</a>';
		break;
// product setting page skin
	case "product":
		$bla = productManager();
		$zemain_content ='<div id="container"><div class="contentslide">'. $bla.'</div></div>';
		break;
	case "user": // User Config
	case "domain_config":
// admin home page skin
	case "adminedit":
	default: // No rub selected

		$bwoup_user_edit = bwoupUserEditForms($adm_login,$adm_pass);

		if(isset($adm_random_pass)){
			$rand = $adm_random_pass;
		}else{
			$rand = getRandomValue();
			$expirationTIME = mktime() + (60 * $conf_session_expir_minute);
			$q = "UPDATE $pro_mysql_tik_admins_table SET pass_next_req='$rand', pass_expire='$expirationTIME' WHERE pseudo='".$_SERVER["PHP_AUTH_USER"]."';";
			$r = mysql_query($q)or die("Cannot execute query \"$q\" !");
			$adm_random_pass = $rand;
		}
		$skinedConsole = '<div class="console"><div class="console_title">Console output :</div><div class="console_output"><pre>'.$_SERVER['SERVER_NAME'].':&gt;_'.$console.'<br /></pre></div></div>';

		$adm_list = adminList($rand).'<div class="voider"></div><br /><br /><div class="voider"></div>';

		$zemain_content = '<div id="container"><div class="contentslide"><div class="thtitle">'. _("User administration") .'</div><br />'.$bwoup_user_edit.$skinedConsole.'<br /></div></div>
					<div class="panelside"><div class="box_wnb_nb_title_left"><div class="dtctitle">'. _("Admins list") .'</div></div>'.$adm_list.'<div style="clear:both;"></div></div>
					<a class="trigger" href="#">Menu</a>';
		break;
	}
// admin page general layout
	$dtc_main_menu = skin_Navbar();
	$anotherFooter = anotherFooter("Footer content<br /><br />");

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" dir=\"ltr\" lang=\"en\" xml:lang=\"en\">
<head>
<title>DTC: Admin: ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/skin.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/slide.css\" />
<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"gfx/skin/grayboard/css/style.css\" />
<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script>
<!--[if IE ]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/$iepower\"></script>
<![endif]-->
<!--[if lte IE 6]>
	<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/pngfix/supersleight-min.js\"></script>
<![endif]-->
<script src=\"gfx/skin/grayboard/js/slide.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.dropshadow.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/jquery.timers.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/mbTooltip.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/DD_roundies-min.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/gradualfader.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/easing.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function(){
	$(\".trigger\").click(function(){
		$(\".panelside\").toggle(\"fast\");
		$(this).toggleClass(\"active\");
		return false;
	});
});
</script>
<script type=\"text/javascript\">
$(document).ready(function() {
	$(\"a#controlbtn\").click(function(e) {
		e.preventDefault();
		var slidepx=$(\"div#linkblock\").width() + 10;
		if ( !$(\"div#ClientMenuContent\").is(':animated') ) { 
			if (parseInt($(\"div#ClientMenuContent\").css('marginLeft'), 10) < slidepx) {
				$(this).removeClass('close').html('".$clmenu_close."&nbsp;". _("Your domains") ."');
				margin = \"+=\" + slidepx;
			} else {
				$(this).addClass('close').html('".$clmenu_expand."&nbsp;". _("Your domains") ."');
				margin = \"-=\" + slidepx; 		
			}	
        		$(\"div#ClientMenuContent\").animate({ 
        			marginLeft: margin
      			}, {
                    	duration: 'slow',
                    	easing: 'easeOutBounce'
                	});
		} 
	}); 
});
</script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds.js\"></script>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/general.js\"></script>
<!--[if gte IE 7]>
<script type=\"text/javascript\" src=\"gfx/skin/grayboard/js/css_adds_IE.js\"></script>
<![endif]-->
</head>
<body id=\"page\">
<div id=\"outerwrapper\">
	<div id=\"wrapper\">
		".makePreloads()."
		$confirm_javascript
		$java_script
		$skinCssString
		".anotherTopBanner("DTC","yes").$dtc_main_menu."
		".$zemain_content."
		<div id=\"footer\">".anotherFooter("Footer content<br /><br />")."GrayBoard template for DTC made by <a href=\"http://solution.adquate.net\" title=\"AdQuate web site\" target=\"_blank\">cali</a></div>
	</div>
</div>
<script type=\"text/javascript\">gradualFader.init()</script>
</body>
</html>";

}
// client admin tabs function
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
			echo("Error fetching admin : $error");
			$ret["err"] = $admin["err"];
			$ret["mesg"] = $admin["mesg"];
			return $ret;
		}

		$out = '<ul id="iconbar">';
		if($rub != "user" && $rub != ""){
			$out .= "<li><a href=\"?adm_login=$adm_login&adm_pass=$pass&amp;rub=user\">
					<img src=\"gfx/skin/grayboard/images/client.png\" alt=\"\" class=\"gradualfader\"  />
					<span>". _("Client interface") ."</span>
				</a></li>";
		}else{
			$out .= "<li><a href=\"?adm_login=$adm_login&adm_pass=$pass&amp;rub=user\">
					<img src=\"gfx/skin/grayboard/images/client.png\" alt=\"\" />
					<span>". _("Client interface") ."</span>
				</a></li>";
		}
		if($rub != "domain_config"){
			$out .= "<li><a href=\"?adm_login=$adm_login&adm_pass=$pass&amp;rub=domain_config\">
					<img src=\"gfx/skin/grayboard/images/domain.png\" alt=\"\" class=\"gradualfader\" />
					<span>". _("Domain config") ."</span>
				</a></li>";
		}else{
			$out .= "<li><a href=\"?adm_login=$adm_login&adm_pass=$pass&amp;rub=domain_config\">
					<img src=\"gfx/skin/grayboard/images/domain.png\" alt=\"\" />
					<span>". _("Domain config") ."</span>
				</a></li>";
		}
		if($rub != "adminedit"){
			$out .= "<li><a href=\"?adm_login=$adm_login&adm_pass=$pass&amp;rub=adminedit\">
					<img src=\"gfx/skin/grayboard/images/g_admin.png\" alt=\"\" class=\"gradualfader\" />
					<span>". _("Admin editor") ."</span>
				</a></li>";
		}else{
			$out .= "<li><a href=\"?adm_login=$adm_login&adm_pass=$pass&amp;rub=adminedit\">
					<img src=\"gfx/skin/grayboard/images/g_admin.png\" alt=\"\" />
					<span>". _("Admin editor") ."</span>
				</a></li>";
		}
		$out .= "</ul>";

		//fix up the $adm_login in case it changed because of session vars:
		//in case users play silly bugger with the "GET" variables
		$adm_login = $admin["info"]["adm_login"];

		// Draw the html forms
		if(isset($rub) && $rub == "adminedit"){
			$out .= '<div class="contentform">'.drawEditAdmin($admin).'</div>';
		}else if(isset($rub) && $rub == "domain_config"){
			$out .= '<div class="contentform">'.drawDomainConfig($admin).'</div>';
		}else{
			$out .= '<div class="contentform">'.drawAdminTools($admin).'</div>';
		}
		return $out;
	}else{
		// If no user is in edition, draw a tool for adding an admin
		return drawNewAdminForm();
	}
}

?>
