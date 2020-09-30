<?php
	
function drawRrdtoolGraphs (){
	global $rub;
	global $pro_mysql_vps_server_table;

	if(!isset($_REQUEST["sousrub"]) || $_REQUEST["sousrub"] == ""){
		$sousrub = "localserver";
	}else{
		$sousrub = $_REQUEST["sousrub"];
	}

	$out = '<ul class="box_wnb_content_nb">';
	if( $sousrub == "localserver"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=localserver\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("Local host") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=localserver\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("Local host") ."</a></li>";
	}
	if( $sousrub == "vpsservers"){
		$out .= "<li class=\"box_wnb_content_nb_item_select\"><a href=\"?rub=$rub&sousrub=vpsservers\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("VPS servers") ."</a></li>";
	}else{
		$out .= "<li class=\"box_wnb_content_nb_item\"><a href=\"?rub=$rub&sousrub=vpsservers\"><img width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/tabs/p_clientinterface.gif\" align=\"absmiddle\" border=\"0\"> ". _("VPS server") ."</a></li>";
	}
// gfx/skin/bwoup/gfx/config-icon/box_wnb_nb_picto-vpsservers.gif
	$out .= "</ul>";

	if( $sousrub == "localserver"){
		$out .= "<center><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"920\">
<tr><td><h3>". _("Network Traffic Statistics") ."</h3></td><tr>
<tr><td><IFRAME src=\"netusegraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>". _("CPU Load Average") ."</h3></td><tr>
<tr><td><IFRAME src=\"cpugraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>". _("Memory and Swap Usage ") ."</h3></td><tr>
<tr><td><IFRAME src=\"memgraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>". _("Mail Queue Graph") ."</h3></td><tr>
<tr><td><IFRAME src=\"mailgraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>";
		if( file_exists("/usr/sbin/mailgraph")){
			$out .= "<tr><td><h3>". _("Mail Queue Statistics") ."</h3></td><tr>";
			$out .= "<tr><td><IFRAME src=\"/cgi-bin/mailgraph.cgi\" width=\"100%\" height=\"388\"></iframe></td></tr>";
		}
		$out .= "</table></center>";
	}
	if( $sousrub == "vpsservers"){
		if(!isset($_REQUEST["period"]) || $_REQUEST["period"] == ""){
			$period = "day";
		}else{
			$period = $_REQUEST["period"];
		}
		$out .= "<br>";
		if($period == "hour"){
			$out .= _("Hour graph");
		}else{
			$out .= "<a href=\"?rub=$rub&sousrub=vpsservers&period=hour\">"._("Hour graph")."</a>";
		}
		$out .= " - ";
		if($period == "day"){
			$out .= _("Day graph");
		}else{
			$out .= "<a href=\"?rub=$rub&sousrub=vpsservers&period=day\">"._("Day graph")."</a>";
		}
		$out .= " - ";
		if($period == "week"){
			$out .= _("Week graph");
		}else{
			$out .= "<a href=\"?rub=$rub&sousrub=vpsservers&period=week\">"._("Week graph")."</a>";
		}
		$out .= " - ";
		if($period == "month"){
			$out .= _("Month graph");
		}else{
			$out .= "<a href=\"?rub=$rub&sousrub=vpsservers&period=month\">"._("Month graph")."</a>";
		}
		$out .= " - ";
		if($period == "year"){
			$out .= _("Year graph");
		}else{
			$out .= "<a href=\"?rub=$rub&sousrub=vpsservers&period=year\">"._("Year graph")."</a>";
		}
		$out .= "<br><br>";
		$q = "SELECT hostname FROM $pro_mysql_vps_server_table ORDER BY hostname";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 0){
			$out .= _("No VPS server configured");
		}else{
			$out .= "<center><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"920\"><tr>";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$hostname = $a["hostname"];
				$out .= "<tr><td width=\"33%\">"._("CPU")." $hostname<br><img src=\"vm-cpu-all.php?vps_server_hostname=$hostname&graph=$period\"></td>
<td width=\"33%\">"._("HDD activity")." $hostname<br><img src=\"vm-io-all.php?vps_server_hostname=$hostname&graph=$period\"></td>
<td width=\"33%\">"._("Network usage")." $hostname<br><img src=\"vm-net-all.php?vps_server_hostname=$hostname&graph=$period\"></td></tr>";
			}
			$out .= "</table></center>";
		}
	}
	return $out;
}

?>
