<?php

$txt_select_lang_title = array(
	"fr" => "Langue",
	"en" => "Language",
	"nl" => "Taal",
	"ru" => "Language",
	"de" => "Language");

function anotherLanguageSelection(){
	global $lang;
	global $rub;

	// Language selection box
	$out = "
<div align=\"right\">
<table cellpadding=\"0\" cellspacing=\"4\">
<tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=fr&rub=$rub\">FRANCAIS
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/fr.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=en&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/en.gif\">&nbsp;ENGLISH</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=nl&rub=$rub\">DUTCH
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/nl.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=ru&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/ru.gif\">&nbsp;RUSSIAN</a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=hu&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/hu.gif\">&nbsp;HUNGARIAN</a></td>
</tr><tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=es&rub=$rub\">ESPANOL
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/es.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=de&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/de.gif\">&nbsp;DEUTSCH</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=it&rub=$rub\">ITALIANO
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/it.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=zh&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/zh.gif\">&nbsp;CHINESE</a></td>
<td valign=\"center\" nowrap>
	&nbsp;</td>
</tr></table>
</div>
";
	return $out;
}

$confirm_javascript="
<script language=\"JavaScript\">

function dtc_js_confirm(text,url){
	if(window.confirm(text)){
		document.location = url;
	}
}

</script>
";
function dtcJavascriptConfirmLink($text,$url){
	global $conf_use_javascript;

	if($conf_use_javascript == "no"){
		return $url;
	}else{
		return "javascript:dtc_js_confirm('$text','$url')";
	}
}


function anotherFooter($content){
	global $lang;

	$sponsors_inside = "<center><font face=\"Arial\" size=\"-2\">
<i>Most of code done by:
<a target=\"_blank\" href=\"mailto:thomas [ at ] goirand.fr\">Thomas GOIRAND</a>, under
<a target=\"_blank\" href=\"http://www.gnu.org/copyleft/lesser.txt\">LGPL</a>. Please visit <a
target=\"_blank\" href=\"http://www.gplhost.com\">GPLHost</a> and <a
target=\"_blank\" href=\"http://www.gplhost.com/?rub=softwares&sousrub=dtc\">DTC home</a> for more infos.</i></font>
</center>
";

	return $sponsors_inside;
}

function anotherTopBanner($inside,$drawLanguageSelect="no"){
	global $conf_dtc_version;
	global $conf_dtc_release;
	global $conf_unix_type;

	global $txt_pagetop_zesubtitle;
	global $lang;

	$nowrap = " style=\"white-space:nowrap\" valign=\"top\"";
	if($drawLanguageSelect=="yes"){
		$zeLanguage = "
	<td valign=\"top\">&nbsp;</td>
	<td $nowrap width=\"1\">".anotherLanguageSelection()."</td>";
		$links = "<br>
<font size=\"-2\" face=\"Arial\"><a target=\"_blank\" href=\"/dtcdoc/\">Documentation</a>
<a target=\"_blank\" href=\"/phpmyadmin/\">PhpMyAdmin</a></font>";
	}else{
		$links = "";
		$zeLanguage = "";
	}

	$inside = "
<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"1\">
<tr>
	<td $nowrap><center><a href=\"http://www.gplhost.com/?rub=softwares&sousrub=dtc\"><img border=\"0\" alt=\"Domain Teck Control\" src=\"gfx/dtc_logo_small.gif\"></a><br>
<font size=\"-2\" face=\"Arial\">V$conf_dtc_version R$conf_dtc_release - $conf_unix_type</font></center></td>
	<td $nowrap><center><b><font size=\"+1\" face=\"Verdana\">Domain Technologie Control</font></b><br>
<font size=\"-1\"><i>".$txt_pagetop_zesubtitle[$lang]."</i></font>$links</center></td>
	<td $nowrap width=\"100%\">&nbsp;</td>".$zeLanguage."
</tr>
</table>
";
	return $inside;
}

function anotherPage($title,$meta,$java_script,$onloads,$banner,$menu,$content,$footer){
	global $page_metacontent;
	global $conf_skin;
	global $dtcshared_path;

	global $skinCssString;
	global $confirm_javascript;

	if(file_exists($dtcshared_path.'/gfx/skin/'.$conf_skin.'/bgcolor.php')){
		include($dtcshared_path.'/gfx/skin/'.$conf_skin.'/bgcolor.php');
	}else{
		$body_tag = "<link rel=\"stylesheet\" href=\"gfx/dtc.css\" type=\"text/css\">
<body bgcolor=\"#74748A\" text=\"#FFFFFF\" leftmargin=\"0\"
topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">";
	}

return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
<html>
<head>
<title>DTC: $title ".$_SERVER['SERVER_NAME']."</title>
$page_metacontent
$meta
</head>

$onloads
$confirm_javascript
$java_script
$skinCssString

$body_tag

<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
<tr><td width=\"100%\" height=\"1\">$banner</td></tr>
<tr><td width=\"100%\" height=\"1\">$menu</td></tr>
<tr><td width=\"100%\" height=\"1\" valign=\"top\">$content</td></tr>
<tr><td width=\"100%\" height=\"100%\">&nbsp;<br><br></td></tr>
<tr><td width=\"100%\" height=\"1\">$footer</td></tr>
</table>
</html>";
}

function anotherLeftFrame($left,$right){
	return "
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"100%\">
<tr><td valign=\"top\" width=\"1\">
	$left
</td><td valign=\"top\">
	$right
</td></tr>
</table>
";
}


?>
