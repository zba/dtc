<?php

$skinTable[] = array(
	"skinName" => 'paperboard',
	"skinCss" => 'skin.css',
	"functionCode" => '
	$skinpath = "$skinGeneralPath/$skinpath";

	return "<div class=\"box\">
	  <div class=\"box_topright\"><div class=\"box_bottomright\"><div class=\"box_bottomleft\"><div class=\"box_topleft\">
	  <div class=\"box_content\">


	  <h2>$title</h2>
	  $content
	</div></div></div></div>
</div></div>
";'
	);

?>