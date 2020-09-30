<?php
// HTTPRequest class adapted from http://sg.php.net/manual/en/function.fopen.php#58099
//#usage:
//$r = new HTTPRequest('http://www.php.net');
//echo $r->DownloadToString();

class dtc_HTTPRequest
{
    var $_fp;        // HTTP socket
    var $_url;        // full URL
    var $_host;        // HTTP host
    var $_protocol;    // protocol (HTTP/HTTPS)
    var $_uri;        // request URI
    var $_port;        // port
    
    // Timeout in seconds 
    var $_timeout = 5;
    
    // scan url
    function _scan_url()
    {
        $req = $this->_url;
        
        $pos = strpos($req, '://');
        $this->_protocol = strtolower(substr($req, 0, $pos));
        
        $req = substr($req, $pos+3);
        $pos = strpos($req, '/');
        if($pos === false)
            $pos = strlen($req);
        $host = substr($req, 0, $pos);
        
        if(strpos($host, ':') !== false)
        {
            list($this->_host, $this->_port) = explode(':', $host);
        }
        else 
        {
            $this->_host = $host;
            $this->_port = ($this->_protocol == 'https') ? 443 : 80;
        }
        
        $this->_uri = substr($req, $pos);
        if($this->_uri == '')
            $this->_uri = '/';
    }
    
    // constructor
    function dtc_HTTPRequest($url)
    {
        $this->_url = $url;
        $this->_scan_url();
    }
    
    // download URL to string Array
    function DownloadToStringArray()
    {
    	$crlf = "/[\r\n]+/";
	$fullresponse = $this->DownloadToString();
    	$array = preg_split($crlf, $fullresponse, -1, PREG_SPLIT_NO_EMPTY);
	return $array;
    }
    
    // download URL to string
    function DownloadToString()
    {
    	// store errors in case we need to handle them
        $errno;
        $errstr;
	$response ='';
        
        $crlf = "\r\n";
        
        // generate request
        $req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf 
            .    'Host: ' . $this->_host . $crlf 
            .    $crlf;
        
        
        // fetch from URL
        $this->_fp = fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port, $errno, $errstr, $this->_timeout);
        fwrite($this->_fp, $req);
        
        $a_vers = explode(".",phpversion());
				$use_stream = TRUE;
				if($a_vers[0] <= 4 && $a_vers[1] < 3)
				{
					// first < 4.3, then we need to use socket rather than stream
					$use_stream = FALSE;
				}
				
				if ($use_stream)
				{
					stream_set_blocking($this->_fp, TRUE); 
	        stream_set_timeout($this->_fp,$this->_timeout); 
				} 
				else 
				{
	        socket_set_blocking($this->_fp, TRUE); 
	        socket_set_timeout($this->_fp,$this->_timeout); 
        }
        
        // get the socket status
        $info;
        if ($use_stream)
        {
        	$info = stream_get_meta_data($this->_fp);
        } else {
        	$info = socket_get_status($this->_fp);
        }
       
        while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp) && (!$info['timed_out']))
        {
            $response .= fread($this->_fp, 4096);
            if ($use_stream)
            {
            	$info = stream_get_meta_data($this->_fp);
            } else {
            	$info = socket_get_status($this->_fp);
            }
            @ob_flush(); 
            flush();
        }
        fclose($this->_fp);
        
        // split header and body
        $pos = strpos($response, $crlf . $crlf);
        if($pos === false)
            return($response);
        $header = substr($response, 0, $pos);
        $body = substr($response, $pos + 2 * strlen($crlf));
        
        // parse headers
        $headers = array();
        $lines = explode($crlf, $header);
        foreach($lines as $line)
            if(($pos = strpos($line, ':')) !== false)
                $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
        
        // redirection?
        if(isset($headers['location']))
        {
            $http = new dtc_HTTPRequest($headers['location']);
            return($http->DownloadToString($http));
        }
        else 
        {
            return($body);
        }
    }
}
?>
