<?php

$skinTable[] = array(
	"skinName" => 'iglobal',
	"skinCss" => 'skin.css',
	"functionCode" => '
	$skinpath = "$skinGeneralPath/$skinpath";

	$border_color = " bgcolor=\"#DDDDDD\" ";

//	if($title == "" || !isset($title)){
		return "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
	<td $border_color width=\"9\" height=\"11\"><img width=\"9\" height=\"11\" src=\"$skinpath/corner_top_left.gif\"></td>
	<td $border_color width=\"100%\" background=\"$skinpath/border_top.gif\"></td>
	<td $border_color width=\"12\" height=\"11\"><img width=\"12\" height=\"11\" src=\"$skinpath/corner_top_right.gif\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\" background=\"$skinpath/border_left.gif\" height=\"100%\"><img src=\"$skinpath/border_left.gif\" width=\"9\" border=\"0\"></td>
	<td width=\"100%\" background=\"$skinpath/background.gif\" valign=\"top\" id=\"skinWinIGLOBALContent2\">$content</td>
	<td $border_color width=\"12\" background=\"$skinpath/border_right.gif\"><img src=\"$skinpath/border_right.gif\" width=\"12\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\" height=\"18\"><img width=\"9\" height=\"18\" src=\"$skinpath/corner_bottom_left.gif\"></td>
	<td $border_color width=\"100%\" background=\"$skinpath/border_bottom.gif\"><img src=\"$skinpath/border_bottom.gif\" border=\"0\"></td>
	<td $border_color width=\"12\"><img width=\"12\" src=\"$skinpath/corner_bottom_right.gif\" border=\"0\"></td>
</tr>
</table>
";
//	}

/*	$winTitleArray = explode("|",$title);
	if(sizeof($winTitleArray) > 1){
		$zeIconPath = "icons/".$winTitleArray[1];
		$theIcon = "<img src=\"$skinpath/$zeIconPath\">";
		$title = $winTitleArray[0];
	}else{
		$theIcon = "<img src=\"$skinpath/title_1_icon.gif\">";
	}

	return "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
	<td $border_color width=\"9\" height=\"51\"><img src=\"$skinpath/corner_top_left.gif\"></td>
	<td $border_color width=\"32\" height=\"51\" background=\"$skinpath/title_1_icon.gif\" valign=\"top\">$theIcon</td>
	<td $border_color width=\"28\" height=\"51\"><img src=\"$skinpath/title_2_transi.gif\"></td>
	<td $border_color valign=\"center\" background=\"$skinpath/title_3_background.gif\" id=\"skinWinIGLOBAL\" style=\"white-space: nowrap\" nowrap><font size=\"-4\"><br></font><u>$title</u></td>
	<td $border_color width=\"100%\" background=\"$skinpath/title_3_background.gif\"></td>
	<td $border_color width=\"27\" height=\"51\"><img src=\"$skinpath/title_4_end.gif\" border=\"0\"></td>
	<td $border_color width=\"11\" height=\"51\"><img src=\"$skinpath/corner_top_right.gif\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\" background=\"$skinpath/border_left.gif\" height=\"100%\"><img width=\"9\" src=\"$skinpath/border_left.gif\" border=\"0\"></td>
	<td colspan=\"5\" width=\"100%\" background=\"$skinpath/background.gif\" valign=\"top\" id=\"skinWinIGLOBALContent\">$content</td>
	<td $border_color width=\"11\" background=\"$skinpath/border_right.gif\"><img width=\"11\" src=\"$skinpath/border_right.gif\" border=\"0\"></td>
</tr>
<tr>
	<td $border_color width=\"9\"><img width=\"9\" src=\"$skinpath/corner_bottom_left.gif\"></td>
	<td colspan=\"5\" width=\"100%\" background=\"$skinpath/border_bottom.gif\"><img src=\"$skinpath/border_bottom.gif\" border=\"0\"></td>
	<td $border_color width=\"11\"><img width=\"11\" src=\"$skinpath/corner_bottom_right.gif\" border=\"0\"></td>
</tr>
</table>
"; */
'
	);

?>
