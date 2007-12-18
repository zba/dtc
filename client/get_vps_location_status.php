<?php

$panel_type="client";
require_once("../shared/autoSQLconfig.php");

function getSpaceSlotsRemaining(){
	$space = array();

	$q = "SELECT location FROM vps_server GROUP BY location";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$q2 = "SELECT vps_ip.ip_addr FROM vps_ip,vps_server
			WHERE vps_server.location='".$a["location"]."'
			AND vps_server.hostname=vps_ip.vps_server_hostname
			AND vps_ip.available='yes';";
		$r2 = mysql_query($q2)or die("Cannot query ".$q2." line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		$space[] = array(
			"name" => $a["location"],
			"slots" => $n2);
	}
	return $space;
}

function displayAvailableSpace(){
	$out = "";

	$space = getSpaceSlotsRemaining();
	$n = sizeof($space);
	$full = "";
	$available = "";
	$j = 0;
	for($i=0;$i<$n;$i++){
		if($space[$i]["slots"] == 0){
			if($full != ""){
				$full .= " - ";
			}
			$full .= $space[$i]["name"];
		}else{
			if($j % 2){
				$available .= "<tr><td>".$space[$i]["name"]."</td><td><b>".$space[$i]["slots"]."</b></td></tr>";
			}else{
				$available .= "<tr><td bgcolor=\"#AAAAAA\">".$space[$i]["name"]."</td><td bgcolor=\"#AAAAAA\"><b>".$space[$i]["slots"]."</b></td></tr>";
			}
			$j++;
		}
	}
	$out .= "<b><u>Currently full:</b></u> $full<br>
<b><u>Currently with space available:</b></u>
<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
<tr><th>Location name</th><th>Number of slots available</th></tr>
$available
</table>";
	return $out;
}

echo "
<style>
a:link{color:#277193;text-decoration: none;}
a:visited{color:#277193;text-decoration: none;}
a:hover{color:#105278;text-decoration: underline;}
a:active{color:#105278;text-decoration: none;}

body, h2, h3, h4, h5, h6, td {
	font: 12px Helvetica, Arial, sans-serif;
	color: #000000;
}
</style>
".displayAvailableSpace()."<br><br>

<a target=\"_top\" href=\"https://".$_SERVER["HTTP_HOST"]."/dtc/new_account.php\">Register for a VPS hosting here</a>";

?>