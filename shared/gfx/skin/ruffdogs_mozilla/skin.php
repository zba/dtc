<?php

$skinTable[] = array(
	"skinName" => 'ruffdogs_mozilla',
	"skinCss" => 'skin.css',
	"functionCode" => '
	$skinpath = "$skinGeneralPath/$skinpath";

	$border_color = " bgcolor=\"#C7D0D9\" ";

	return "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
	<td height=\"25\" $border_color valign=\"center\" background=\"$skinpath/title_1_icon.png\" id=\"skinRuffDogsMozilla\" style=\"white-space: nowrap\" nowrap><u>$title</u></td>
</tr>
<tr>
	<td width=\"100%\" background=\"$skinpath/background.png\" valign=\"top\" id=\"skinRuffDogsMozillaContent\">$content</td>
</tr>
<tr>
	<td height=\"25\" $border_color background=\"$skinpath/border_bottom.png\"></td>
</tr>
</table>
";
'
	);

?>