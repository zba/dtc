<?php

/* The affiliation setcookie snippet

This should work the following way.  Affiliate places a link to:
http://www.gplhost.com/dtc/affiliation.php?affiliate=customername&return=/hosting-vps.html

This redirects the customer to the sales page setting a cookie first

*/

// generic tlds (source: http://en.wikipedia.org/wiki/Generic_top-level_domain)
$G_TLD = array('biz','com','edu','gov','info','int','mil','name','net','org','aero','asia','cat','coop','jobs','mobi','museum','pro','tel','travel','arpa','root','berlin','bzh','cym','gal','geo','kid','kids','lat','mail','nyc','post','sco','web','xxx','nato','example','invalid','localhost','test','bitnet','csnet','ip','local','onion','uucp','co'   // note: not technically, but used in things like co.uk
);

// country tlds (source: http://en.wikipedia.org/wiki/Country_code_top-level_domain)
$C_TLD = array(
// active
'ac','ad','ae','af','ag','ai','al','am','an','ao','aq','ar','as','at','au','aw','ax','az',
'ba','bb','bd','be','bf','bg','bh','bi','bj','bm','bn','bo','br','bs','bt','bw','by','bz',
'ca','cc','cd','cf','cg','ch','ci','ck','cl','cm','cn','co','cr','cu','cv','cx','cy','cz',
'de','dj','dk','dm','do','dz','ec','ee','eg','er','es','et','eu','fi','fj','fk','fm','fo',
'fr','ga','gd','ge','gf','gg','gh','gi','gl','gm','gn','gp','gq','gr','gs','gt','gu','gw',
'gy','hk','hm','hn','hr','ht','hu','id','ie','il','im','in','io','iq','ir','is','it','je',
'jm','jo','jp','ke','kg','kh','ki','km','kn','kr','kw','ky','kz','la','lb','lc','li','lk',
'lr','ls','lt','lu','lv','ly','ma','mc','md','mg','mh','mk','ml','mm','mn','mo','mp','mq',
'mr','ms','mt','mu','mv','mw','mx','my','mz','na','nc','ne','nf','ng','ni','nl','no','np',
'nr','nu','nz','om','pa','pe','pf','pg','ph','pk','pl','pn','pr','ps','pt','pw','py','qa',
're','ro','ru','rw','sa','sb','sc','sd','se','sg','sh','si','sk','sl','sm','sn','sr','st',
'sv','sy','sz','tc','td','tf','tg','th','tj','tk','tl','tm','tn','to','tr','tt','tv','tw',
'tz','ua','ug','uk','us','uy','uz','va','vc','ve','vg','vi','vn','vu','wf','ws','ye','yu',
'za','zm','zw',
// inactive
'eh','kp','me','rs','um','bv','gb','pm','sj','so','yt','su','tp','bu','cs','dd','zr'
);

// Code to split the domain and find the first domain "after" the TLD

$serverdomain = $_SERVER["SERVER_NAME"];
$subs = array_reverse(explode(".",$serverdomain));
if ( count($subs) >= 2) { // FIXME watch out: this malfunctions with IP addresses
	$domain = ".{$subs[1]}.{$subs[0]}";
	if ( count($subs) >= 3 && (
			in_array($subs[1],$G_TLD) || in_array($subs[1],$C_TLD)
	        ) ) {
		$domain = ".{$subs[2]}.$domain";
	}
} else {
	$domain = $serverdomain;
}


// FIXME: need to include DTC validation functions so we can use isMailbox here instead
// relevant file: dtc/shared/drawlib/dtc_functions.php
$affiliate = $_REQUEST["affiliate"];
if (preg_match("/[^a-z0-9-_]/","",$affiliate)) die ( _("Affiliate can only have lowercase letters, numbers and - _") );

$returnurl = $_REQUEST["return"];
if ($returnurl) {
	if (preg_match("/[^\?&]/","",$returnurl)) die ( _("Return URL can't have query string parameters") );
	if (substr($returnurl,0,1) != "/") $returnurl = "/" . $returnurl;
} else {
	$returnurl = "/";
}

$panel_type="client";
require_once("../shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");


if ($conf_affiliate_return_domain) {
	$returnurl = "http://" . $conf_affiliate_return_domain . $returnurl;
} elseif ($_SERVER["SERVER_NAME"] == "dtc.node6503.gplhost.com") {
	$returnurl = "http://www.gplhost.com" . $returnurl;
} elseif ($_SERVER["SERVER_NAME"] == "dtc.gplhost.co.uk") {
	$returnurl = "http://www.gplhost.co.uk" . $returnurl;
}

setcookie("affiliate",$affiliate,time()+60*60*24*365,"/",$domain,false,false);
header("Location: $returnurl");
exit;

?>
