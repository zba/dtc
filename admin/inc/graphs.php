<?php
	
function drawRrdtoolGraphs (){
	$out = "<center><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"920\">
<tr><td><h3>". _("Network Traffic Statistics") ."</h3></td><tr>
<tr><td><IFRAME src=\"netusegraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>". _("CPU Load Average") ."</h3></td><tr>
<tr><td><IFRAME src=\"cpugraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>". _("Memory and Swap Usage ") ."</h3></td><tr>
<tr><td><IFRAME src=\"memgraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>". _("Mail Queue graph") ."</h3></td><tr>
<tr><td><IFRAME src=\"mailgraph.php\" width=\"100%\" height=\"318\"></iframe></td></tr>";
	if( file_exists("/usr/sbin/mailgraph")){
		$out .= "<tr><td><h3>". _("Mail Queue statistics") ."</h3></td><tr>";
		$out .= "<tr><td><IFRAME src=\"/cgi-bin/mailgraph.cgi\" width=\"100%\" height=\"388\"></iframe></td></tr>";
	}
	$out .= "</table></center>";
	return $out;
}

?>
