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

function skinCustomRootNavBar (){
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
		$out .= '<li><div class="navbar_item-select"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=graph"><img width="46" height="55" src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</a></li>';
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

/*	$out .= '<li><div class="navbar_item-select"><img src="gfx/navbar_p_user.gif"><br />user administration</div></li>
	<li><a href="#"><img src="gfx/navbar_p_customer.gif"><br />customer relation management</a></li>
	<li><a href="#"><img src="gfx/navbar_p_bandwith.gif"><br />bandwith monitor</a></li>

	<li><a href="#"><img src="gfx/navbar_p_server.gif"><br />server monitor</a></li>
	<li><a href="#"><img src="gfx/navbar_p_renewal.gif"><br />renewals</a></li>
	<li><a href="#"><img src="gfx/navbar_p_product.gif"><br />hosting product manager</a></li>
	<li><a href="#"><img src="gfx/navbar_p_daemon.gif"><br />daemons configuration files generation</a></li>
	<li><a href="#"><img src="gfx/navbar_p_config.gif"><br />DTC general configuration</a></li>';*/
	$out .= '</ul><div id="navbar_right"></div></div>';
	return $out;
}

?>