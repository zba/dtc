<?php

$skinTable[] = array(
	"skinName" => 'darkblue',
	"skinCss" => 'skin.css',
	"functionCode" => '
	$skinpath = "$skinGeneralPath/$skinpath";

	return "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
	<td width=\"1\"><img src=\"$skinpath/corner_1.gif\"></td>
	<td width=\"100%\" background=\"$skinpath/border_1.gif\"></td>
	<td width=\"1\"><img src=\"$skinpath/corner_2.gif\" border=\"0\"></td>
</tr>
<tr>
	<td width=\"1\" background=\"$skinpath/border_4.gif\" height=\"100%\"><img src=\"$skinpath/border_4.gif\" border=\"0\"></td>
	<td width=\"100%\" background=\"$skinpath/background.gif\" valign=\"top\" id=\"skinPageTitle\">$content</td>
	<td width=\"1\" background=\"$skinpath/border_2.gif\"><img src=\"$skinpath/border_2.gif\" border=\"0\"></td>
</tr>
<tr>
	<td width=\"1\"><img src=\"$skinpath/corner_4.gif\"></td>
	<td width=\"100%\" background=\"$skinpath/border_3.gif\"><img src=\"$skinpath/border_3.gif\" border=\"0\"></td>
	<td width=\"1\"><img src=\"$skinpath/corner_3.gif\" border=\"0\"></td>
</tr>
</table>
";
'
	);

?>