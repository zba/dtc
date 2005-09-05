<?php

include_once "webnic_settings.php";

// $post_url is usually something like: https://pay.web.cc/new/cgi-bin/pn_reg.cgi
// $source is the webnic username
// $post_params_hash is a hashtable of the POST parameters

// returns 99 if there are no $source values present in the request
// returns error code from URL
// returns actual content from URL

function webnic_submit($post_url, $source, $post_params_hash)
{
	$strContent = "";	
	if (isset($source))
	{
		$strContent.="source=$source";
	} 
	else if (isset($post_params_hash["source"]))
	{
		$strContent.="source=" . $post_params_hash["source"];
	} 
	else 
	{
		return "99\nNo source (Webnic.cc partner username specified\n";			
	}

	foreach(array_keys($post_params_hash) as $key)
	{
		if ($key != "source")
		{
			$strContent.= "&$key=". $post_params_hash[$key];
		}
	}

        $postfield=$strContent;
	if ($debug == 1)
	{
		echo $postfield . "\n";
		echo "Post URL: $post_url\n";
	}
        $ch = curl_init();
        // $url2="https://pay.web.cc/new/cgi-bin/pn_whois.cgi";
        curl_setopt($ch, CURLOPT_URL,$post_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
        curl_exec ($ch);
        $strReturn= ob_get_contents ( );
        curl_close ($ch);
        return $strReturn;
}

?>
