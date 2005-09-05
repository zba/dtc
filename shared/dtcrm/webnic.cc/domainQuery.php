<?php

require_once "webnic_submit.php";

// INPUT:
// $domain - domain to query on Webnic systems (similar to whois, but not universal)
// $source - webnic username

// OUTPUT:
// Large string output

// return codes:
// ok  	message
// 0 	Domain is available to register
// 1 	Domain was already taken
// 2 	error message returned by registry
// 3 	Invalid partner
// 4 	No domain specify
// 5 	Domain may contain only alpha-numeric characters or the dash '-' symbol (no leading or trailing dashes)
// 6 	Domain must be between three and forty-six characters in length
// 7 	Invalid domain extension
// 8 	https connection error

function domainQuery($source, $domain)
{
	$post_param_hash["domain"]=$domain;
	$post_param_hash["source"]=$source;
	$url = "https://pay.web.cc/new/cgi-bin/pn_whois.cgi";
	
	return webnic_submit($url, $source, $post_param_hash);
}
?>
