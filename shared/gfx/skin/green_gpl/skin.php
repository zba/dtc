<?php

$skinTable[] = array(
	"skinName" => 'green_gpl',
	"skinCss" => 'skin.css',
	"functionCode" => '
	$skinpath = "$skinGeneralPath/$skinpath";

	$border_color = " bgcolor=\"#77B386\" ";

	if($title == "" || !isset($title)){
		return "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
	<td $border_color width=\"9\" height=\"8\"><img width=\"9\" height=\"8\" src=\"$skinpath/no_title_corner_top_left.png\"></td>
	<td $border_color width=\"100%\" background=\"$skinpath/no_title_title_3_background.png\"></td>
	<td $border_color width=\"11\" height=\"8\"><img width=\"11\" height=\"8\" src=\"$skinpath/no_title_corner_top_right.png\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\" background=\"$skinpath/border_left.png\" height=\"100%\"><img src=\"$skinpath/border_left.png\" width=\"9\" border=\"0\"></td>
	<td width=\"100%\" background=\"$skinpath/background.png\" valign=\"top\" id=\"skinWinGreenGplContent2\"><blockquote>$content</blockquote></td>
	<td $border_color width=\"11\" background=\"$skinpath/border_right.png\"><img src=\"$skinpath/border_right.png\" width=\"11\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\" height=\"14\"><img width=\"9\" height=\"14\" src=\"$skinpath/corner_bottom_left.png\"></td>
	<td $border_color width=\"100%\" background=\"$skinpath/border_bottom.png\"><img src=\"$skinpath/border_bottom.png\" border=\"0\"></td>
	<td $border_color width=\"11\"><img width=\"11\" src=\"$skinpath/corner_bottom_right.png\" border=\"0\"></td>
</tr>
</table>
";
	}

	$winTitleArray = explode("|",$title);
	if(sizeof($winTitleArray) > 1){
		$zeIconPath = "icons/".$winTitleArray[1];
		$theIcon = "<img src=\"$skinpath/$zeIconPath\">";
		$title = $winTitleArray[0];
	}else{
		$theIcon = "<img src=\"$skinpath/title_1_icon.png\">";
	}

	return "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
	<td $border_color width=\"9\" height=\"51\"><img src=\"$skinpath/corner_top_left.png\"></td>
	<td $border_color width=\"32\" height=\"51\" background=\"$skinpath/title_1_icon.png\" valign=\"top\">$theIcon</td>
	<td $border_color width=\"28\" height=\"51\"><img src=\"$skinpath/title_2_transi.png\"></td>
	<td $border_color valign=\"center\" background=\"$skinpath/title_3_background.png\" id=\"skinWinGreenGpl\" style=\"white-space: nowrap\" nowrap><font size=\"-4\"><br></font><u>$title</u></td>
	<td $border_color width=\"100%\" background=\"$skinpath/title_3_background.png\"></td>
	<td $border_color width=\"27\" height=\"51\"><img src=\"$skinpath/title_4_end.png\" border=\"0\"></td>
	<td $border_color width=\"11\" height=\"51\"><img src=\"$skinpath/corner_top_right.png\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\" background=\"$skinpath/border_left.png\" height=\"100%\"><img width=\"9\" src=\"$skinpath/border_left.png\" border=\"0\"></td>
	<td colspan=\"5\" width=\"100%\" background=\"$skinpath/background.png\" valign=\"top\" id=\"skinWinGreenGplContent\">$content</td>
	<td $border_color width=\"11\" background=\"$skinpath/border_right.png\"><img width=\"11\" src=\"$skinpath/border_right.png\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\"><img width=\"9\" src=\"$skinpath/corner_bottom_left.png\"></td>
	<td colspan=\"5\" width=\"100%\" background=\"$skinpath/border_bottom.png\"><img src=\"$skinpath/border_bottom.png\" border=\"0\"></td>
	<td $border_color width=\"11\"><img width=\"11\" src=\"$skinpath/corner_bottom_right.png\" border=\"0\"></td>
</tr>
</table>
";
'
	);

?>
