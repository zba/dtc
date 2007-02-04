<?php

$skinTable[] = array(
	"skinName" => 'bwoup',
	"skinCss" => 'skin.css',
	"functionCode" => '
	$skinpath = "$skinGeneralPath/$skinpath";

	return "
<table class=\"box_wnb\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
<tr>
<td class=\"box_wnb_nb\" valign=\"top\"><div class=\"box_wnb_nb_title\"><div class=\"box_wnb_nb_title_left\"><div class=\"box_wnb_nb_title_right\"><div class=\"box_wnb_nb_title_mid\">$title</div></div></div></div>
<div class=\"box_wnb_nb_content\">
$content
</div>
</td></table>";'
	);

?>