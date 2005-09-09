<?php

$theTopsIcons = '<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
<tr>
	<td width="14%" valign="bottom"><center><a href="'.$PHP_SELF.'?"><font face="Arial"><b>Home</b></font></a></center></td>
	<td width="14%" valign="bottom"><center><a href="'.$PHP_SELF.'?sousrub=dtcadmin"><font face="Arial"><b>DTC Admin Panel</b></font></a></center></td>
	<td width="14%" valign="bottom"><center><a href="'.$PHP_SELF.'?sousrub=dtc"><font face="Arial"><b>DTC Client Panel</b></font></a></center></td>
	<td width="14%" valign="bottom"><center><a href="'.$PHP_SELF.'?sousrub=dtcemail"><font face="Arial"><b>DTC Email Panel</b></font></a></center></td>
	<td width="14%" valign="bottom"><center><a href="'.$PHP_SELF.'?sousrub=squirrelmail"><font face="Arial"><b>SquirrelMail</b></font></a></center></td>
	<td width="15%" valign="bottom"><center><a href="'.$PHP_SELF.'?sousrub=phpmyadmin"><font face="Arial"><b>PhpMyAdmin</b></font></a></center></td>
	<td width="15%" valign="bottom"><center><a href="'.$PHP_SELF.'?sousrub=register"><font face="Arial"><b>New account</b></font></a></center></td>
</tr>
</table>';

$ZeContentWindowTitle = "General Server News And Infos|gplhost.gif";
$ZeContent = '<table width="100%" height="100%">
<tr><td width="100%" height="100%">
<center>

<b><font face="Arial"><center>
<a href="http://www.gplhost.com/?rub=softwares&sousrub=dtc">
<img src="dtc_logo.gif" border="0"></a><br><br>
<h1><u>Welcome to '.$_SERVER["HTTP_HOST"].'!</u></h1>
This is a place holder to show you how to create your client tool page.<br>
Please edit '.__FILE__.' to customise this page.<br><br>
</center></font><br><br><br></td></tr>

<tr><td width="100%" height="1"><center><font face="Arial" size="-2">
This website server is powered by Domain Technologie Control (DTC),
an open-source control panel<br>
<i>Most of code done by:
<a target="_blank" href="mailto:thomas [ at ] goirand.fr">Thomas GOIRAND</a>, under
<a target="_blank" href="http://www.gnu.org">LGPL</a>. Please visit <a
target="_blank" href="http://gplhost.com">GPLHost</a> and <a
target="_blank" href="http://gplhost.com/?rub=softwares&sousrub=dtc">DTC
home</a> for more infos.</i></font>
</center></td></tr></table>';

$sousrub = $_REQUEST["sousrub"];

if($sousrub == "register"){
	$ZeContentWindowTitle = "Register an account|gplhost.gif";
	$ZeContent = '<IFRAME border="0" src="http://'.$_SERVER["HTTP_HOST"].'/dtc/new_account.php?product_id='.$_REQUEST["product_id"].'" width="100%" height="100%" scrolling="auto" frameborder="1">
  [Your user agent does not support frames or is currently configured not to display frames. However, you may visit
  <A href="https://dtc.gplhost.com/dtc" target="_blank">the related document.</A>]
  </IFRAME>
';
}

//echo "<pre>";
//print_r($_SERVER);
//echo $_SERVER["HTTP_HOST"];
//echo "</pre>";

if($sousrub == "dtc"){
	$ZeContentWindowTitle = "DTC Client interface|dtc.gif";
	$ZeContent = '<IFRAME border="0" src="http://'.$_SERVER["HTTP_HOST"].'/dtc" width="100%" height="100%" scrolling="auto" frameborder="1">
  [Your user agent does not support frames or is currently configured not to display frames. However, you may visit
  <A href="http://dtc.gplhost.com/dtc" target="_blank">the related document.</A>]
  </IFRAME>
';
}

if($sousrub == "squirrelmail"){
	$ZeContentWindowTitle = "SquirrelMail|squirrel.gif";
	$ZeContent = '<IFRAME border="0" src="http://'.$_SERVER["HTTP_HOST"].'/squirrelmail/src/" width="100%" height="100%" scrolling="auto" frameborder="1">
  [Your user agent does not support frames or is currently configured not to display frames. However, you may visit
  <A href="http://dtc.gplhost.com/squirrelmail" target="_blank">the related document.</A>]
  </IFRAME>
';
}

if($sousrub == "dtcadmin"){
	$ZeContentWindowTitle = "DTC Client interface|dtc.gif";
	$ZeContent = '<IFRAME border="0" src="http://'.$_SERVER["HTTP_HOST"].'/dtcadmin" width="100%" height="100%" scrolling="auto" frameborder="1">
  [Your user agent does not support frames or is currently configured not to display frames. However, you may visit
  <A href="http://dtc.gplhost.com/dtc" target="_blank">the related document.</A>]
  </IFRAME>
';
}

if($sousrub == "dtcemail"){
	$ZeContentWindowTitle = "DTC Email interface|dtc.gif";
	$ZeContent = '<IFRAME border="0" src="http://'.$_SERVER["HTTP_HOST"].'/dtcemail" width="100%" height="100%" scrolling="auto" frameborder="1">
  [Your user agent does not support frames or is currently configured not to display frames. However, you may visit
  <A href="http://dtc.gplhost.com/dtcemail" target="_blank">the related document.</A>]
  </IFRAME>
';
}

if($sousrub == "phpmyadmin"){
	$ZeContentWindowTitle = "PhpMyAdmin|mysql.png";
	$ZeContent = '<IFRAME border="0" src="http://'.$_SERVER["HTTP_HOST"].'/phpmyadmin" width="100%" height="100%" scrolling="auto" frameborder="1">
  [Your user agent does not support frames or is currently configured not to display frames. However, you may visit
  <A href="http://dtc.gplhost.com/phpmyadmin" target="_blank">the related document.</A>]
  </IFRAME>
';
}

$insideContent = $ZeContent;

$content = '<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
<tr>
	<td width="100%" height="1">'.$theTopsIcons.'</td>
</tr><tr>
	<td width="100%" height="100%">'.$insideContent.'</td>
</tr></table>';

echo "<html>
<body leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">$content
</body>
</html>";

?>
