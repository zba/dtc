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
	<a href=\"?change_language=fr&rub=$rub\">FR
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/fr.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"?change_language=en&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/en.gif\">&nbsp;EN</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"?change_language=nl&rub=$rub\">NL
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/nl.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"?change_language=ru&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/ru.gif\">&nbsp;RU</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"?change_language=pt&rub=$rub\">PT
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/pt.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"?change_language=hu&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/hu.gif\">&nbsp;HU</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"?change_language=pt_BR&rub=$rub\">BR
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/pt_br.gif\"></a></td>
</tr><tr><td align=\"right\" valign=\"center\" nowrap>
	<a href=\"?change_language=es&rub=$rub\">ES
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/es.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"?change_language=de&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/de.gif\">&nbsp;DE</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"?change_language=it&rub=$rub\">IT
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/it.gif\"></a></td>
<td valign=\"center\" nowrap>
	<a href=\"?change_language=zh&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/zh.gif\">&nbsp;ZH</a></td>
<td valign=\"center\" nowrap>
	<a href=\"?change_language=zh_TW&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/tw.gif\">&nbsp;TW</a></td>
<td align=\"right\" valign=\"center\" nowrap>
	<a href=\"?change_language=pl&rub=$rub\">PL
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/pl.png\"></a></td>
<td><a href=\"?change_language=fi&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/fi.gif\">&nbsp;FI</a></td>
<td><a href=\"?change_language=lv&rub=$rub\">
	<img width=\"16\" height=\"11\" alt=\"*\" border=\"0\" src=\"gfx/language/lv.gif\">&nbsp;LV</a></td>
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
	global $adm_login;
	global $adm_email_login;
	global $panel_type;	

	global $conf_panel_title;
	global $conf_panel_subtitle;
	global $conf_panel_logo;
	global $conf_panel_logolink;
	
	global $conf_skin;
	global $dtcshared_path;
	
	$nowrap = " style=\"white-space:nowrap\" valign=\"top\" nowrap ";
	if($drawLanguageSelect=="yes"){
		$zeLanguage = "
	<td valign=\"top\">&nbsp;</td>
	<td $nowrap width=\"1\">".anotherLanguageSelection()."</td>";
		$links = "<a target=\"_blank\" href=\"/dtcdoc/\">". _("Documentation") ."</a> <a target=\"_blank\" href=\"/phpmyadmin/\">". _("PhpMyAdmin") . "</a>";
	}else{
		$links = "";
		$zeLanguage = "";
	}


	if($panel_type == "admin"){
		$display_user = $_SERVER["PHP_AUTH_USER"];
	}else if($panel_type == "client"){
		$display_user = $adm_login;
	}else if($panel_type == "email"){
		$display_user = $adm_email_login;
	}else{
		$display_user = "";
	}

	$pagetop_filename = $dtcshared_path.'/gfx/skin/'.$conf_skin.'/pagetop.html';
	if(file_exists($pagetop_filename)){
		$fp = fopen($pagetop_filename,"r");
		$inside = fread($fp,filesize($pagetop_filename));
		fclose($fp);
		// Enable possible customization of the title and logos
		if($conf_panel_title == "default"){
			$inside = str_replace("__DTC_TITLE__", _("Domain Technologie Control") ,$inside);
		}else{
			$inside = str_replace("__DTC_TITLE__", $conf_panel_title ,$inside);
		}
		if($conf_panel_subtitle == "default"){
			$inside = str_replace("__DTC_SUBTITLE__", _("Take control of your domain name") ,$inside);
		}else{
			$inside = str_replace("__DTC_SUBTITLE__", $conf_panel_subtitle ,$inside);
		}
		if($conf_panel_logo == "default"){
			// Prefer a default logo in PNG format
			$panel_logo = "gfx/skin/".$conf_skin."/gfx/logo_dtc.png";
			if (!file_exists($dtcshared_path."/".$panel_logo)) {
				// fall-back to GIF format
				$panel_logo = "gfx/skin/".$conf_skin."/gfx/logo_dtc.gif";
			}
			$inside = str_replace("__DTC_LOGO__", $panel_logo ,$inside);
		}else{
			$inside = str_replace("__DTC_LOGO__", "gfx/skin/".$conf_skin."/gfx/". $conf_panel_logo ,$inside);
		}
		if($conf_panel_logolink == "default"){
			$inside = str_replace("__DTC_LOGOLINK__", "http://www.gplhost.com/software-dtc.html", $inside);
		}else{
			$inside = str_replace("__DTC_LOGOLINK__", $conf_panel_logolink, $inside);
		}
		$inside = str_replace("__DTC_LANGUAGES_LINKS__",$zeLanguage,$inside);
		$inside = str_replace("__DTC_VERSION__","V$conf_dtc_version R$conf_dtc_release - $conf_unix_type",$inside);
		$inside = str_replace("__DTC_LINK__",$links,$inside);
		$inside = str_replace("__AUTH_USER__",_("Logged in as:") . " " . $display_user,$inside);
		$inside = str_replace("__DOCUMENTATION__",_("Documentation"),$inside);
		$inside = str_replace("__PHPMYADMIN__",_("PhpMyAdmin"),$inside);
		$inside .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"gfx/wz_tooltip.js\"></script>";
		return $inside;
	}else{
		return "
<script language=\"JavaScript\" type=\"text/javascript\" src=\"gfx/wz_tooltip.js\"></script>
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
