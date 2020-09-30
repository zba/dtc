<?php
header("HTTP/1.0 404 Not Found");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Error 404: file not found</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta name="title" content="Error 404: file not found" />
<meta name="robots" content="NOINDEX, FOLLOW" />
<meta name="cache-control" content="no-cache" />
<style type="text/css">
* {margin: 0;	padding: 0;}
html{height:100%; margin:0;}
body{height:100%;margin:0;	font: 12px Arial, Helvetica, sans-serif; color: #000000;}
html>body #container {height: auto;	}
a { color: #105278; }
a:hover { color: #000000; }
h1{text-decoration:none; text-align:center;}
#container {position: relative;			min-height: 100%; height: 100%;	voice-family: "\"}\""; voice-family: inherit;height: auto;}
#wrapper {padding-bottom: 32px;}
.top{padding:25% 0 0 0 ; text-align:center;}
.message{padding:1% 5% 1% 5%; text-align:center;}
.footer{width:98%; text-align:center; position:absolute; bottom:0; padding:1%; }
.italics{font-style:italic;}
</style>
</head>
<body>
<div id="container">
    <div id="wrapper">
        <div class="top">
            <h1>404 Error: the requested document does not exist</h1>
            <?php echo "URL: http://".$_SERVER["SERVER_NAME"].htmlspecialchars($_SERVER["REQUEST_URI"]); ?>
</div>
        <div class="message">
            <p>
            You have reached this page because the requested
            document does not exist on this server. Please check that the URL
            is correct or inform the webmaster of the website of an incorrect
            link.<br /><br /><a href="<?php echo "http://".$_SERVER["SERVER_NAME"]?>">Go to
website home page</a>
</p>
</div>
<div class="footer">
	<p>This website server is powered by Domain Technologie Control (DTC), the open-source control panel.</p>
	<p><em>
		Get your DTC setup for <strong>free</strong> with one of our <strong><a target="_blank" href="http://gplhost.com/hosting-vps.html" title="Virtual private server hosting" >VPS servers</a></strong> and dedicated hosting services; visit the <a target="_blank" href="http://gplhost.com/software-dtc.html">DTC control panel</a> to learn more and download it for your server.
	</em></p>
</div>
</div>
</div>
</body>
</html>
