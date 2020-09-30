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
	"fr" => "UTF-8",
	"en" => "UTF-8",
	"hu" => "UTF-8",
	"it" => "UTF-8",
	"nl" => "UTF-8",
	"ru" => "UTF-8",
	"de" => "UTF-8",
	"zh" => "UTF-8",
	"zh_TW" => "UTF-8",
	"pl" => "UTF-8",
	"sv" => "UTF-8",
	"sr" => "UTF-8",
	"pt" => "UTF-8",
	"pt_BR" => "UTF-8",
	"es" => "UTF-8",
	"fi" => "UTF-8",
	"lv" => "UTF-8",
	"cs" => "UTF-8");

//check to see if we are running in the shell or web
if($panel_type!="cronjob"){
	@session_start();
	if(isset($lang)) $_SESSION["lang"]=$lang;
	// If something like phpbb that has $lang on the same domain, this should
	// avoid problems. This is a lack in php (IMHO)
	if(isset($_SESSION["lang"]) && !is_string($_SESSION["lang"])){
		unset($lang);
	}
	if(isset($_SESSION["lang"])){
	  $lang = $_SESSION["lang"];
        }
	if(isset($_REQUEST["change_language"])){
		// This is the case of xx_YY
		if( strlen($_REQUEST["change_language"]) == 5){
			$exploded_lang = explode("_",$_REQUEST["change_language"]);
			if( !preg_match("/^([a-z0-9]+)([.a-z0-9-]*)([a-z0-9]+)\$/",$exploded_lang[0]) ){
				die("Lang parameter not correct line ".__LINE__." file ".__FILE__);
			}
			if( !preg_match("/^([A-Z0-9]+)([.A-Z0-9-]*)([A-Z0-9]+)\$/",$exploded_lang[1]) ){
				die("Lang parameter not correct line ".__LINE__." file ".__FILE__);
			}
		// This is the case of xx only and no _YY
		}else if( strlen($_REQUEST["change_language"]) == 2){
			if(!preg_match("/^([a-z0-9]+)([.a-z0-9-]*)([a-z0-9]+)\$/",$_REQUEST["change_language"])){
				die("Lang parameter not correct line ".__LINE__." file ".__FILE__);
			}
		}else{
			die("Lang parameter not correct line ".__LINE__." file ".__FILE__);
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
		if($_REQUEST["change_language"] == "zh_TW"){
		  $lang = "zh_TW";
		}
		if($_REQUEST["change_language"] == "pl"){
		  $lang = "pl";
		}
		if($_REQUEST["change_language"] == "sv"){
		  $lang = "sv";
		}
		if($_REQUEST["change_language"] == "es"){
		  $lang = "es";
		}
		if($_REQUEST["change_language"] == "sr"){
		  $lang = "sr";
		}
		if($_REQUEST["change_language"] == "pt"){
		  $lang = "pt";
		}
		if($_REQUEST["change_language"] == "pt_BR"){
		  $lang = "pt_BR";
		}
		if($_REQUEST["change_language"] == "fi"){
		  $lang = "fi";
		}
		if($_REQUEST["change_language"] == "lv"){
		  $lang = "lv";
		}
		if($_REQUEST["change_language"] == "cs"){
		  $lang = "cs";
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
if($panel_type != "cronjob"){
	header("Content-type: text/html; charset=$charset");
}

switch($lang){
case "fr_FR":
case "fr":
	$gettext_lang = "fr_FR.UTF-8";
	break;
case "hu_HU":
case "hu":
	$gettext_lang = "hu_HU.UTF-8";
	break;
case "it_IT":
case "it":
	$gettext_lang = "it_IT.UTF-8";
	break;
case "nl_NL":
case "nl":
	$gettext_lang = "nl_NL.UTF-8";
	break;
case "ru_RU.UTF-8":
case "ru_RU":
case "ru":
	$gettext_lang = "ru_RU.UTF-8";
	break;
case "de_DE":
case "de":
	$gettext_lang = "de_DE.UTF-8";
	break;
case "zh_CN":
case "zh":
	$gettext_lang = "zh_CN.UTF-8";
	break;
case "zh_TW":
	$gettext_lang = "zh_TW.UTF-8";
	break;
case "pl_PL":
case "pl":
	$gettext_lang = "pl_PL.UTF-8";
	break;
case "sv_SE":
case "sv":
	$gettext_lang = "sv_SE.UTF-8";
	break;
case "sr_RS":
case "sr":
	$gettext_lang = "sr_RS.UTF-8";
	break;
case "pt_PT":
case "pt":
	$gettext_lang = "pt_PT.UTF-8";
	break;
case "pt_BR":
	$gettext_lang = "pt_BR.UTF-8";
	break;
case "es_ES":
case "es":
	$gettext_lang = "es_ES.UTF-8";
	break;
case "fi_FI":
case "fi":
	$gettext_lang = "fi_FI.UTF-8";
	break;
case "lv_LV":
case "lv":
	$gettext_lang = "lv_LV.UTF-8";
	break;
case "cs_CZ":
case "cs":
	$gettext_lang = "cs_CZ.UTF-8";
	break;
default:
	$gettext_lang = "en_US.UTF-8";
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
