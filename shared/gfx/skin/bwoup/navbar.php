<?

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

	if(!isset($rub) || $rub == "user" || $rub == ""){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_user.gif"><br />'.$txt_mainmenu_title_useradmin[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=user"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_user.gif"><br />'.$txt_mainmenu_title_useradmin[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "crm"){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_customer.gif"><br />'.$txt_mainmenu_title_client_management[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=crm"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_customer.gif"><br />'.$txt_mainmenu_title_client_management[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "monitor"){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_bandwith.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=monitor"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_bandwith.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "graph"){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=graph"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_server.gif"><br />'.$txt_mainmenu_title_bandwidth_monitor[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "renewal"){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_renewal.gif"><br />'.$txt_mainmenu_title_renewals[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=renewal"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_renewal.gif"><br />'.$txt_mainmenu_title_renewals[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "product"){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_product.gif"><br />'.$txt_product_manager[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=product"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_product.gif"><br />'.$txt_product_manager[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "generate"){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_daemon.gif"><br />'.$txt_mainmenu_title_deamonfile_generation[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=generate"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_daemon.gif"><br />'.$txt_mainmenu_title_deamonfile_generation[$lang].'</a></li>';
	}
	if(isset($rub) && $rub == "config"){
		$out .= '<li><div class="navbar_item-select"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_config.gif"><br />'.$txt_mainmenu_title_dtc_config[$lang].'</div></li>';
	}else{
		$out .= '<li><a href="?rub=config"><img src="gfx/skin/bwoup/gfx/navbar/navbar_p_config.gif"><br />'.$txt_mainmenu_title_dtc_config[$lang].'</a></li>';
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