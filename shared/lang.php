<?php

$txt_default_lang = "en";

$txt_langname = array(
	"fr" => "iso-8859-1",
	"en" => "iso-8859-1",
	"nl" => "iso-8859-15",
	"ru" => "koi8-r");

session_register("lang");
$lang = $_SESSION["lang"];
if($_REQUEST["change_language"] == "en"){
	$lang = "en";
}
if($_REQUEST["change_language"] == "nl"){
	$lang = "nl";
}
if($_REQUEST["change_language"] == "fr"){
	$lang = "fr";
}
if($_REQUEST["change_language"] == "ru"){
	$lang = "ru";
}

$_SESSION["lang"] = $lang;

// Get the default language variable. Multilanguage example taken from OMail.
if (!$lang){
	// if no language defined yet (cookie or session):
	// try to findout users language by checking it's HTTP_ACCEPT_LANGUAGE variable
	if ($HTTP_ACCEPT_LANGUAGE) {
		$all_languages = strtok("$HTTP_ACCEPT_LANGUAGE",";");
		$langaccept = explode(",", $all_languages);
		for ($i = 0; $i < sizeof($langaccept); $i++) { 
			$tmplang = trim($langaccept[$i]);
			$tmplang2 = substr($tmplang,0,2);
			if ($txt_langname[$tmplang] && !$lang) {   // if the whole string matchs ("de-CH", or "en", etc)
				$lang = $tmplang;
			} elseif ($txt_langname[$tmplang2] && !$lang) { // then try only the 2 first chars ("de", "fr"...)
				$lang = $tmplang2; 
			}
		}
	}
	if (!$lang) {
		// didn't catch any valid lang : we use the default settings
		$lang = $txt_default_lang; 
    }
}

$charset = $txt_langname[$lang];
$page_metacontent = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
header("Content-type: text/html; charset=$charset");



?>