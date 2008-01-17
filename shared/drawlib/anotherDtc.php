<?php
/**
 * @package DTC
 * @version $Id: anotherDtc.php,v 1.38 2007/02/05 06:46:39 thomas Exp $
 * @abstract language chose link/images
 * 
 * Added swedish to project languages
 * if posible translate to swedish.
 * $Log: anotherDtc.php,v $
 * Revision 1.38  2007/02/05 06:46:39  thomas
 * Better drawings on IE too. Work still in progress...
 *
 * Revision 1.37  2006/08/07 20:53:00  thomas
 * Added VPS renewal. Still have to validate payment by hand, but working
 * well appart from that.
 *
 * Revision 1.36  2006/05/21 12:52:41  seeb
 * Change dispaly bug
 *
 * Revision 1.35  2006/05/21 01:01:26  seeb
 * $txt_documentation added
 *
 * Revision 1.34  2006/05/21 00:20:00  seeb
 * *** empty log message ***
 *
 * Revision 1.33  2006/05/20 23:47:40  seeb
 * Adding swedish
 *
 */
function anotherLanguageSelection(){
	global $lang;
	global $rub;

	// Language selection box
	$out = "
<div align=\"right\">
<table cellpadding=\"0\" cellspacing=\"4\">
<tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=fr&rub=$rub\">FR
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/fr.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=en&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/en.gif\">&nbsp;EN</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=nl&rub=$rub\">NL
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/nl.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=ru&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/ru.gif\">&nbsp;RU</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=pt&rub=$rub\">PT
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/pt.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=hu&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/hu.gif\">&nbsp;HU</a></td>
</tr><tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=es&rub=$rub\">ES
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/es.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=de&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/de.gif\">&nbsp;DE</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=it&rub=$rub\">IT
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/it.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=zh&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/zh.gif\">&nbsp;ZH</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"".$_SERVER["PHP_SELF"]."?change_language=pl&rub=$rub\">PL
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/pl.png\"></a></td>
<td><a href=\"".$_SERVER["PHP_SELF"]."?change_language=se&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/se.png\">&nbsp;SE</a></td>
</tr></table>
</div>
";
	return $out;
}

$confirm_javascript="
<script language=\"JavaScript\" type=\"text/javascript\">

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
target=\"_blank\" href=\"http://www.gplhost.com/software-dtc.html\">DTC home</a> for more infos.</i></font>
</center>
";

	return $sponsors_inside;
}

function anotherTopBanner($inside,$drawLanguageSelect="no"){
	global $conf_dtc_version;
	global $conf_dtc_release;
	global $conf_unix_type;
	
	global $conf_skin;
	global $dtcshared_path;
	
	$nowrap = " style=\"white-space:nowrap\" valign=\"top\" nowrap ";
	if($drawLanguageSelect=="yes"){
		$zeLanguage = "
	<td valign=\"top\">&nbsp;</td>
	<td $nowrap width=\"1\">".anotherLanguageSelection()."</td>";
		$links = "<a target=\"_blank\" href=\"/dtcdoc/\">". _("Documentation") ."</a> <a target=\"_blank\" href=\"/phpmyadmin/\">PhpMyAdmin</a>";
	}else{
		$links = "";
		$zeLanguage = "";
	}


	$pagetop_filename = $dtcshared_path.'/gfx/skin/'.$conf_skin.'/pagetop.html';
	if(file_exists($pagetop_filename)){
		$fp = fopen($pagetop_filename,"r");
		$inside = fread($fp,filesize($pagetop_filename));
		fclose($fp);
		$inside = str_replace("__DTC_SUBTITLE__", _("Take control of your domain name") ,$inside);
		$inside = str_replace("__DTC_LANGUAGES_LINKS__",$zeLanguage,$inside);
		$inside = str_replace("__DTC_VERSION__","V$conf_dtc_version R$conf_dtc_release - $conf_unix_type",$inside);
		$inside = str_replace("__DTC_LINK__",$links,$inside);
		return $inside;
	}else{
		return "
<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"1\">
<tr>
	<td $nowrap><center><a href=\"http://www.gplhost.com/software-dtc.html\"><img border=\"0\" alt=\"Domain Teck Control\" src=\"gfx/dtc_logo_small.gif\"></a><br>
<font size=\"-2\" face=\"Arial\">V$conf_dtc_version R$conf_dtc_release - $conf_unix_type</font></center></td>
	<td $nowrap><center><b><font size=\"+1\" face=\"Verdana\">Domain Technologie Control</font></b><br>
<font size=\"-1\"><i>". _("Take control of your domain name") ."</i></font><br>$links</center></td>
	<td $nowrap width=\"100%\">&nbsp;</td>".$zeLanguage."
</tr>
</table>
";
	}

	$inside = "<style>
.logo {
	background-image:url(gfx/pagetop/logofond.gif);
	background-repeat:repeat-x;
	font-family:Arial;
	font-weight:bold;
	color:#868686;
	height:59px;
}
.logotitle {
	font-size:16px;
	padding-top:20px;
}
.logosub {
	font-size:9px;
	padding-top:4px;
}
A .logosub {
	font-size:9px;
	padding-top:4px;
}
</style>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"1\"><tr>
	<td valign=\"top\" width=\"100%\">
	<img src=gfx/pagetop/logostart.gif align=left hspace=0><div class=logo>
	<img src=gfx/pagetop/logoend.gif align=right hspace=0>
	<div class=logotitle>Domain Technologie Control - <font style=\"font-size:12px\">Prenez le controle de votre nom de domaine</font></div>
	<div class=logosub>V$conf_dtc_version R$conf_dtc_release - $conf_unix_type&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$links</div></td>
	<td>&nbsp;$zeLanguage</td>
</tr></table>
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
