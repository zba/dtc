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
			$num_of_slots_loc = "FULL";
		}else{
                	$num_of_slots_loc = $space[$i]["slots"];
		}
		if($j % 2){
			$available .= "<tr><td bgcolor=\"#F3F7F7\">".$space[$i]["name"]."</td><td bgcolor=\"#F3F7F7\"><b>".$num_of_slots_loc."</b></td></tr>";
		}else{
			$available .= "<tr><td bgcolor=\"#FFFFFF\">".$space[$i]["name"]."</td><td bgcolor=\"#FFFFFF\"><b>".$num_of_slots_loc."</b></td></tr>";
		}
		$j++;
	}
	$out .= "
<table cellspacing=\"1\" cellpadding=\"5\" border=\"0\" bgcolor=\"#c6d9d9\" width=\"100%\">
<tr><td align=\"center\" bgcolor=\"#8eb4b3\"><font color=\"#FFFFFF\">Location name</font></td>
<td align=\"center\" bgcolor=\"#8eb4b3\"><font color=\"#FFFFFF\">Slots available</font></td></tr>
$available
</table>";
	return $out;
}

echo "<html><head>
<style type=\"text/css\">
a:link{color:#277193;text-decoration: none;}
a:visited{color:#277193;text-decoration: none;}
a:hover{color:#105278;text-decoration: underline;}
a:active{color:#105278;text-decoration: none;}

body, h2, h3, h4, h5, h6, td {
	font-size: 11px;
	font-family: Verdana, Geneva, Helvetica, Arial, sans-serif;
	color: #366056;
}
</style>
</head>
<body>".displayAvailableSpace()."</body></html>
";

?>