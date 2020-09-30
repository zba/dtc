<?php

$theTopsIcons = '
<div class="nav">
	<a href="?">Home</a>
	<a href="?sousrub=dtcadmin">DTC Admin Panel</a>
	<a href="?sousrub=dtc">DTC Client Panel</a>
	<a href="?sousrub=dtcemail">DTC Email Panel</a>
	<a href="?sousrub=squirrelmail">SquirrelMail</a>
	<a href="?sousrub=phpmyadmin">PhpMyAdmin</a>
	<a href="?sousrub=register">New account</a>
</div>	';

$ZeContentWindowTitle = "General Server News And Infos|gplhost.gif";
$ZeContent = '<div class="top">
            <a href="http://www.gplhost.com/software-dtc.html"><img src="dtclogo.png" alt="DTC web control panel" border="0" /></a>
</div>
        <div class="message">
            <h1>
            Welcome to '.$_SERVER["HTTP_HOST"].'!
            </h1>
            <p>
            This is a place holder to show you how to create your
            client tool page.
            <br />
            Please edit
             '.__FILE__.'
            to customize this page.
            </p>
</div>
        <div class="footer">
	<p>This website server is powered by Domain Technologie Control (DTC), the open-source control panel.</p>
	<p><em>
		Get your DTC setup for <strong>free</strong> with one of our <strong><a target="_blank" href="http://gplhost.com/hosting-vps.html" title="Virtual private server hosting" >VPS servers</a></strong> and dedicated hosting services; visit the <a target="_blank" href="http://gplhost.com/software-dtc.html">DTC control panel</a> to learn more and download it for your server.
	</em></p>
        </div> ';

if( isset($_REQUEST['sousrub']) && preg_match("/[a-zA-Z0-9]/", $_REQUEST["sousrub"])){
	$sousrub = $_REQUEST["sousrub"];
}else{
	$sousrub = "";
}

if($sousrub == "register"){
	$ZeContentWindowTitle = "Register an account|gplhost.gif";
	$ZeContent = '
	
<div id="FRAMEDIV_ID" style="width:100%;height:100%;display:none;">
<iframe id="FRAME_ID" allowtransparency="true" overflow="visible" frameborder="0" src="//'.$_SERVER["HTTP_HOST"].'/dtc/new_account.php?product_id=';
	if(isset($_REQUEST["product_id"]) && preg_match("/[a-zA-Z0-9]/", $_REQUEST["product_id"])){
		$ZeContent .= $_REQUEST["product_id"];
	}
$_REQUEST["product_id"];
	$heb_types = array('shared', 'ssl', 'vps', 'server');
	if(isset($_REQUEST["heb_type"]) && preg_match("/[a-zA-Z0-9\_]/", $_REQUEST["heb_type"])){
		if(in_array(strtolower($_REQUEST["heb_type"]),$heb_types) or preg_match("/custom_[0-9]{1,11}/", $_REQUEST["heb_type"])){
			$ZeContent .= '&heb_type='.strtolower($_REQUEST["heb_type"]);
		    }
		}
	$ZeContent .= '" width="100%">  </iframe>
      </div>

';
}

//echo "<pre>";
//print_r($_SERVER);
//echo $_SERVER["HTTP_HOST"];
//echo "</pre>";

if($sousrub == "dtc"){
	$ZeContentWindowTitle = "DTC Client interface|dtc.gif";
	$ZeContent = '
<div id="FRAMEDIV_ID" style="width:100%;height:100%;display:none;">
<iframe id="FRAME_ID" allowtransparency="true" overflow="visible" frameborder="0" src="//'.$_SERVER["HTTP_HOST"].'/dtc/" width="100%">  </iframe>
      </div>
';
}

if($sousrub == "squirrelmail"){
	$ZeContentWindowTitle = "SquirrelMail|squirrel.gif";
	$ZeContent = '
	<div id="FRAMEDIV_ID" style="width:100%;height:100%;display:none;">
<iframe id="FRAME_ID" allowtransparency="true" overflow="visible" frameborder="0" src="//'.$_SERVER["HTTP_HOST"].'/squirrelmail/src/" width="100%">  </iframe>
      </div>
';
}

if($sousrub == "dtcadmin"){
	$ZeContentWindowTitle = "DTC Client interface|dtc.gif";
	$ZeContent = '<div id="FRAMEDIV_ID" style="width:100%;height:100%;display:none;">
<iframe id="FRAME_ID" allowtransparency="true" overflow="visible" frameborder="0" src="//'.$_SERVER["HTTP_HOST"].'/dtcadmin/" width="100%">  </iframe>
      </div>
';
}

if($sousrub == "dtcemail"){
	$ZeContentWindowTitle = "DTC Email interface|dtc.gif";
	$ZeContent = '<div id="FRAMEDIV_ID" style="width:100%;height:100%;display:none;">
<iframe id="FRAME_ID" allowtransparency="true" overflow="visible" frameborder="0" src="//'.$_SERVER["HTTP_HOST"].'/dtcemail/" width="100%">  </iframe>
      </div>
';
}

if($sousrub == "phpmyadmin"){
	$ZeContentWindowTitle = "PhpMyAdmin|mysql.png";
	$ZeContent = '<div id="FRAMEDIV_ID" style="width:100%;height:100%;display:none;">
<iframe id="FRAME_ID" allowtransparency="true" overflow="visible" frameborder="0" src="//'.$_SERVER["HTTP_HOST"].'/phpmyadmin/" width="100%">  </iframe>
      </div>
';
}

$insideContent = $ZeContent;

$content = $theTopsIcons . $insideContent;

echo "

<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"
xml:lang=\"en\"
lang=\"en\">
<head>
<title>
Domain Technologie Control (DTC)
</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=us-ascii\" />
<meta name=\"title\" content=\"Domain Technologie Control (DTC)\" />
<style type=\"text/css\">
* {margin: 0;	padding: 0;}
html{height:100%; margin:0;}
body{height:100%;margin:0;	font: 12px Arial, Helvetica, sans-serif; color: #000000;}
html>body #container {height: auto;	}
a { color: #105278; }
a:hover { color: #000000; }
h1{text-decoration:none; text-align:center;}
#container {position: relative;min-height: 100%; height: 100%;	voice-family: inherit;height: auto;}
#wrapper {padding-bottom: 32px;}
.nav{text-align:center; padding:1%; }
.nav a:link,.nav a:visited,.nav a:hover, .nav a:active  {margin:2% 2% 2% 2%; }
.top{padding:15% 0 0 0 ; text-align:center;}
.message{padding:1% 5% 1% 5%; text-align:center;}
.footer{width:98%; text-align:center; position:absolute; bottom:0; padding:1%; }
.italics{font-style:italic;}
</style>
<script>
function setFrmHeight(){
frheight=(document.documentElement.offsetHeight);
document.getElementById(\"FRAME_ID\").style.height=(frheight+\"px\");
document.getElementById(\"FRAMEDIV_ID\").style.display=\"block\";
}
</script>
</head>
<body onload=\"setFrmHeight();\" onresize=\"setFrmHeight();\">
<div id=\"container\">
    <div id=\"wrapper\">
$content
    
	
	</div>
</div>
</body>
</html>";

?>
