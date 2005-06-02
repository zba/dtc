<?php

$txt_default_lang = "en";

$txt_langname = array(
	"fr" => "iso-8859-15",
	"en" => "iso-8859-15",
	"hu" => "iso-8859-15",
	"it" => "iso-8859-15",
	"nl" => "iso-8859-15",
	"ru" => "koi8-r",
	"de" => "iso-8859-15",
	"zh" => "GB2312",
	"pl" => "iso-8859-2",
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
		if($_REQUEST["change_language"] == "es"){
		  $lang = "es";
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

?>
