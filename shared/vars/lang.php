<?php
/**
 * @package DTC
 * @version $Id: lang.php,v 1.18 2007/01/12 16:32:01 thomas Exp $
 * @abstract language change
 * $Log: lang.php,v $
 * Revision 1.18  2007/01/12 16:32:01  thomas
 * Added more protections for some global variables (maybe an XSS flow, not
 * sure about it, but it's better to correct it, just in case...).
 *
 * Revision 1.17  2006/05/21 00:34:26  seeb
 * Adding swedish
 *
 */

$txt_default_lang = "en";

$txt_langname = array(
	"fr" => "iso-8859-15",
	"en" => "iso-8859-15",
	"hu" => "iso-8859-2",
	"it" => "iso-8859-15",
	"nl" => "iso-8859-15",
	"ru" => "UTF-8",
	"de" => "iso-8859-15",
	"zh" => "GB2312",
	"pl" => "iso-8859-2",
	"se" => "iso-8859-15",
	"pt" => "iso-8859-15",
	"es" => "iso-8859-15");

//check to see if we are running in the shell or web
if($panel_type!="cronjob"){
	session_register("lang");
	// If something like phpbb that has $lang on the same domain, this should
	// avoid problems. This is a lack in php (IMHO)
	if(isset($_SESSION["lang"]) && !is_string($_SESSION["lang"])){
		unset($lang);
	}
	if(isset($_SESSION["lang"])){
	  $lang = $_SESSION["lang"];
        }
	if(isset($_REQUEST["change_language"])){
		if(!ereg("^([a-z0-9]+)([.a-z0-9-]*)([a-z0-9]+)\$",$_REQUEST["change_language"])){
			die("Lang parameter not correct!!!");
		}
	        if($_REQUEST["change_language"] == "fr"){
	                $lang = "fr";
	        }
	        if($_REQUEST["change_language"] == "en"){
                  $lang = "en";
                }
                if($_REQUEST["change_language"] == "hu"){
		  $lang = "hu";
                }
                if($_REQUEST["change_language"] == "it"){
		  $lang = "it";
                }
                if($_REQUEST["change_language"] == "nl"){
		  $lang = "nl";
                }
                if($_REQUEST["change_language"] == "ru"){
                  $lang = "ru";
                }
                if($_REQUEST["change_language"] == "de"){
		  $lang = "de";
		}
		if($_REQUEST["change_language"] == "zh"){
		  $lang = "zh";
		}
		if($_REQUEST["change_language"] == "pl"){
		  $lang = "pl";
		}
		if($_REQUEST["change_language"] == "se"){
		  $lang = "se";
		}
		if($_REQUEST["change_language"] == "es"){
		  $lang = "es";
		}
		if($_REQUEST["change_language"] == "pt"){
		  $lang = "pt";
		}
	}
	if(isset($lang)){
	  $_SESSION["lang"] = $lang;
        }
} else {
	//for cron, we will just use english
	$lang = "en";
}



// Get the default language variable. Multilanguage example taken from OMail.
if (!isset($lang)){
	// if no language defined yet (cookie or session):
	// try to findout users language by checking it's HTTP_ACCEPT_LANGUAGE variable
	if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && $_SERVER["HTTP_ACCEPT_LANGUAGE"]) {
		$all_languages = strtok($_SERVER["HTTP_ACCEPT_LANGUAGE"],";");
		$langaccept = explode(",", $all_languages);
		for ($i = 0; $i < sizeof($langaccept); $i++) { 
			$tmplang = trim($langaccept[$i]);
			$tmplang2 = substr($tmplang,0,2);
			if (!isset($lang) && isset($txt_langname[$tmplang]) && $txt_langname[$tmplang]) {   // if the whole string matchs ("de-CH", or "en", etc)
				$lang = $tmplang;
			}elseif (!isset($lang) && isset($txt_langname[$tmplang2])) { // then try only the 2 first chars ("de", "fr"...)
				$lang = $tmplang2;
			}
		}
	}

	if (!isset($lang)) {
		// didn't catch any valid lang : we use the default settings
		$lang = $txt_default_lang; 
        }
        $_SESSION["lang"] = $lang;
}
$charset = $txt_langname[$lang];
$page_metacontent = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
header("Content-type: text/html; charset=$charset");

switch($lang){
case "fr_FR":
case "fr":
	$gettext_lang = "fr_FR";
	break;
case "hu_HU":
case "hu":
	$gettext_lang = "hu_HU";
	break;
case "it_IT":
case "it":
	$gettext_lang = "it_IT";
	break;
case "nl_NL":
case "nl":
	$gettext_lang = "nl_NL";
	break;
case "ru_RU.KOI8-R":
case "ru_RU":
case "ru":
	$gettext_lang = "ru_RU.KOI8-R";
	break;
case "de_DE":
case "de":
	$gettext_lang = "de_DE";
	break;
case "zh_CN":
case "zh":
	$gettext_lang = "zh_CN";
	break;
case "pl_PL":
case "pl":
	$gettext_lang = "pl_PL";
	break;
case "se_NO":
case "se":
	$gettext_lang = "se_NO";
	break;
case "pt_PT":
case "pt":
	$gettext_lang = "pt_PT";
	break;
case "es_ES":
case "es":
	$gettext_lang = "es_ES";
	break;
default:
	$gettext_lang = "en_US";
	break;
}
if(FALSE === putenv("LC_ALL=$gettext_lang")){
	echo "Failed to putenv LC_ALL=$gettext_lang<br>";
}
if(FALSE === putenv("LANG=$gettext_lang")){
	echo "Failed to putenv LANG=$gettext_lang<br>";
}
if(FALSE === setlocale(LC_ALL, $gettext_lang)){
	echo "Failed to setlocale(LC_ALL,$gettext_lang)<br>";
}

//echo "gettext() lang: $gettext_lang<br>";
$pathname = bindtextdomain("messages", "$dtcshared_path/vars/locale"); 
//echo "Pathname = $pathname<br>";
$message_domain = textdomain("messages");
//echo "Message domain = $message_domain";

?>
