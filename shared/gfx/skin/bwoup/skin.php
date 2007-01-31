<?php

$skinTable[] = array(
	"skinName" => 'bwoup',
	"skinCss" => 'skin.css',
	"functionCode" => '
	$skinpath = "$skinGeneralPath/$skinpath";

	return "<table cellpadding=\"1\" width=\"100%\"><tr><td><div class=TEXwincontainer>
<div class=TEXwintitle>$title</div>
<div class=TEXwinbody>
$content
</div>
</div></td></tr></table>";
'
	);

?>