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
<br>
<table cellpadding=\"4\" cellspacing=\"0\">
<tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=fr&rub=".$_REQUEST["rub"]."\">Francais
	<img alt=\"*\" border=\"0\" src=\"gfx/language/fr.gif\"></a>
</td><td valign=\"center\">
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=en&rub=".$_REQUEST["rub"]."\">
	<img alt=\"*\" border=\"0\" src=\"gfx/language/en.gif\">English</a></td>
</tr><tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=nl&rub=".$_REQUEST["rub"]."\">Dutch
	<img alt=\"*\" border=\"0\" src=\"gfx/language/nl.gif\"></a>
</td><td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=ru&rub=".$_REQUEST["rub"]."\">
	<img alt=\"*\" border=\"0\" src=\"gfx/language/ru.gif\">Russian</a></td>
</tr><tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=nl&rub=".$_REQUEST["rub"]."\">-</a>
</td><td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=de&rub=".$_REQUEST["rub"]."\">
	<img alt=\"*\" border=\"0\" src=\"gfx/language/de.gif\">Deutsch</a></td>
</tr></table>
</div>
";
	return $out;
//	return skin("simple/green2",$out,"");
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

	$txt_footer_topline = array(
		"fr" => "Societe Anotherlight Multimedia services d'hebergement professionnel. ╘ 2002-2003",
		"ru" => "Anotherlight Multimedia : система профессионального веб-хостинга. ╘ 2002-2003",
		"en" => "Anotherlight Multimedia : professional web hosting service. ╘ 2002-2003");

	$txt_footer_bottomline = array(
		"fr" => "Solutions - Hebergement de sites web - Hebergement de serveurs dediИ -
HИbergement streaming - Connexion LS - Connexion Turbodsl",
		"ru" => "Решения - Веб-хостинг - Размещение выделенных серверов - Хостинг широковещательных сервисов -
LS соединения - Соединения Turbodsl",
		"nl" => "Solutions - Web sites hosting - Dedicated server hosting - Streaming services hosting -
LS connection - TurboDSL connection",
		"en" => "Solutions - Web sites hosting - Dedicated server hosting - Streaming services hosting -
LS connection - TurboDSL connection");

	$sponsors_inside = "<center><font face=\"Arial\" size=\"-2\">
<i>Programmation :
<a target=\"_blank\" href=\"http://thomas.goirand.fr/?rub=gpl\">Thomas GOIRAND</a>, under
<a target=\"_blank\" href=\"http://www.gnu.org\">LGPL</a></i></font>
</center>
";

	return $sponsors_inside;

	return "
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"
height=\"1\" width=\"100%\">
<tr><td height=\"24\" background=\"gfx/menu/body/fond.gif\" align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"-2\" color=\"#FFCC00\">
	<b>".$txt_footer_topline[$lang]."</b></font></td></tr>
<tr><td align=\"center\">
$sponsors_inside
</td></tr>

<tr><td height=\"24\" background=\"gfx/menu/body/fond.gif\" align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"-2\" color=\"#FFCC00\">
	<b>".$txt_footer_bottomline[$lang]."</b></font></td></tr>
<tr><td>
&nbsp;
</td></tr>
</table>
";
}

function anotherTopBanner($inside,$drawLanguageSelect="no"){

	if($drawLanguageSelect=="yes"){
		$zeLanguage = "<td>".anotherLanguageSelection()."</td>";
	}

	$inside = "
<table cellpadding=\"8\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"100%\"
<tr><td>
	<img src=\"gfx/dtc_logo.gif\">
</td><td>
	<h1><b><font face=\"Verdana\">Domain Technologie Control</font></b></h1>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<i>Take the control of your domain name</i>
</td>".$zeLanguage."</tr>
</table>
";
	return $inside;

/*	return"

<table boder=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"1\" bgcolor=\"#000066\">
<tr><td width=\"1\">
	<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
			codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\"
			width=\"103\" height=\"64\">
		<param name=movie value=\"gfx/flash/114.swf\">
		<param name=quality value=high>
		<param name=\"BGCOLOR\" value=\"#000066\">
		<embed	src=\"gfx/flash/114.swf\" quality=high
				pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\"
		  		type=\"application/x-shockwave-flash\" width=\"103\" height=\"64\" bgcolor=\"#000066\">
          </embed> 
        </object>
</td><td width=\"100%\"><div align=\"center\">
	$inside
</div></td><td width=\"1\">
	<img src=\"gfx/motif02.gif\">
</td></tr>
</table>
";*/
}

function anotherPage($title,$meta,$java_script,$onloads,$banner,$menu,$content,$footer){
	global $page_metacontent;

	global $skinCssString;
	global $confirm_javascript;
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
<link rel=\"stylesheet\" href=\"gfx/dtc.css\" type=\"text/css\">
<body bgcolor=\"#74748A\" text=\"#FFFFFF\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
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
