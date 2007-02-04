<?php
	
function drawRrdtoolGraphs (){
	global $lang;
	global $txt_iframe_nts;
	global $txt_iframe_cpu;
	global $txt_iframe_msu;
	global $txt_iframe_mqg;

	$out = "<center><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"940\">
<tr><td><h3>".$txt_iframe_nts[$lang]."</h3></td><tr>
<tr><td><IFRAME src=\"/cgi-bin/netusegraph.cgi\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>".$txt_iframe_cpu[$lang]."</h3></td><tr>
<tr><td><IFRAME src=\"/cgi-bin/cpugraph.cgi\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>".$txt_iframe_msu[$lang]."</h3></td><tr>
<tr><td><IFRAME src=\"/cgi-bin/memgraph.cgi\" width=\"100%\" height=\"318\"></iframe></td></tr>
<tr><td><h3>".$txt_iframe_mqg[$lang]."</h3></td><tr>
<tr><td><IFRAME src=\"/cgi-bin/queuegraph.cgi\" width=\"100%\" height=\"318\"></iframe></td></tr>
</table></center>
";
	return $out;
/*	$the_iframe = "<IFRAME src=\"/cgi-bin/netusegraph.cgi\" width=\"100%\" height=\"318\"></iframe>";
	$mainFrameCells[] = skin($conf_skin,$the_iframe,$txt_iframe_nts[$lang]);
	$the_iframe = "<IFRAME src=\"/cgi-bin/cpugraph.cgi\" width=\"100%\" height=\"318\"></iframe>";
	$mainFrameCells[] = skin($conf_skin,$the_iframe,$txt_iframe_cpu[$lang]);
	$the_iframe = "<IFRAME src=\"/cgi-bin/memgraph.cgi\" width=\"100%\" height=\"318\"></iframe>";
	$mainFrameCells[] = skin($conf_skin,$the_iframe,$txt_iframe_msu[$lang]);
	$the_iframe = "<IFRAME src=\"/cgi-bin/queuegraph.cgi\" width=\"100%\" height=\"318\"></iframe>";

	$out .= "</table>";
	return $out;*/
}

?>
