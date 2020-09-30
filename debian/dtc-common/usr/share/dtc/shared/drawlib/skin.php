<?php

$globpadd = 0;
$globspace = 0;

////////////////////////////////////////////////////////////////
// Layout elements of the input parameter in a vertical table //
////////////////////////////////////////////////////////////////
function makeVerticalFrame($cells){
	global $globspace;
	global $globpadd;
	$num_cells = sizeof($cells);
	$ret = "
<table boder=\"0\" cellspacing=\"$globspace\" cellpadding=\"$globpadd\" width=\"100%\" height=\"100%\">
";
	for($i=0;$i<$num_cells;$i++){
		$oneCell = $cells[$i];
		$ret .= "<tr><td height=\"1\">$oneCell</td></tr>";
	}
	$ret .= "<tr><td height=\"100%\">&nbsp;</td></tr></table>";
	return $ret;
}

function make_table($html_array,$num_colone){
	global $globspace;
	global $globpadd;
	$num_ligne = sizeof($html_array) / $num_colone;
	$width = 100 / $num_colone;
	$height = 100 / $num_ligne;
	$out = "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"$globpadd\">
";
	for($i=0;$i<$num_ligne;$i++){
		$out .= "
	<tr>";
		for($j=0;$j<$num_colone;$j++){
			$in = $html_array[$i*$num_colone+$j];
			$out.= "<td width=\"$width%\" height=\"$height%\" valign=\"bottom\">
	<center>$in</center></td>
";
		}
		$out .= "
	</tr>";
	}
	$out .= "
</table>
";
	return $out;
}

function make_table_valign_top($html_array,$num_colone){
	global $globspace;
	global $globpadd;
	$num_ligne = sizeof($html_array) / $num_colone;
	$width = 100 / $num_colone;
	$height = 100 / $num_ligne;
	$out = "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"$globpadd\">
";
	for($i=0;$i<$num_ligne;$i++){
		$out .= "
	<tr>";
		for($j=0;$j<$num_colone;$j++){
			$in = $html_array[$i*$num_colone+$j];
			$out.= "<td width=\"$width%\" height=\"$height%\" valign=\"top\">
	<center>$in</center></td>
";
		}
		$out .= "
	</tr>";
	}
	$out .= "
</table>
";
	return $out;
}

////////////////////////////////////////////////////////////////////////////////////
// Layout a image and a html content with a link (optional, could be equal to "") //
////////////////////////////////////////////////////////////////////////////////////
// image_position have to be : "left", "right", "top", or "bottom"
function make_inside($image,$text,$img_pos,$link){
	global $globspace;
	global $globpadd;
	if($link != ""){
		$ref1 = "<a href=\"$link\">";
		$ref2 = "</a>";
	}else{
		$ref1 = "";
		$ref2 = "";
	}

	$out = "
<table width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"$globspace\" cellpadding=\"$globpadd\">
	<tr>
";

	if($img_pos == "left"){
		$out .= "
		<tr><td>
			$ref1<img border=\"0\" $image>$ref2
		</td><td width=\"100%\">
			<div align=\"justify\">$text</div>
		</td></tr>
";	}else if($img_pos == "right"){
		$out .= "
		<tr><td width=\"100%\">
			<div align=\"justify\">$text</div>
		</td><td>
			$ref1<img border=\"0\" $image>$ref2
		</td></tr>
";	}else if($img_pos == "top"){
		$out .= "
		<tr><td>
			<center>
			$ref1<img border=\"0\" $image>$ref2
			</center>
		</td></tr><tr><td height=\"100%\">
			<div align=\"justify\">$text</div>
		</td></tr>
";	}else if($img_pos == "bottom"){
		$out .= "
		<tr><td height=\"100%\">
			<div align=\"justify\">$text</div>
		</td></tr><tr><td>
			<center>
			$ref1<img border=\"0\" $image>$ref2
			</center>
		</td></tr>
";	}

	$out .= "
	</tr>
</table>
";
	return $out;
}

///////////////////////////////////////////////////////////////////////////////////////////////
// Add a "preload image" in the header javascripts, and return the name of the created image //
// for adding the onmouseover() and onmouseout() javascript stuff                            //
///////////////////////////////////////////////////////////////////////////////////////////////
$preloads = array();
$nbrPreloadedImage = 0;
$imagePrefix = "rolloverImg";
function addImageToPreloads($imagePath){
	global $preloads;
	global $nbrPreloadedImage;
	global $imagePrefix;

	$preloads[] = $imagePath;
	$nbrPreloadedImage++;
	return "$imagePrefix$nbrPreloadedImage";
}
//////////////////////////////////////////////
// Make the javascript for preloaded images //
//////////////////////////////////////////////
function makePreloads(){
	global $preloads;
	global $nbrPreloadedImage;
	global $imagePrefix;
	$java_script = "<script language=\"JavaScript\" type=\"text/javascript\">
<!-- Begin\n";
	for($i=0;$i<$nbrPreloadedImage;$i++){
		$imgNum = $i+1;
		$src = $preloads[$i];
		$java_script .= "
$imagePrefix$imgNum = new Image();
$imagePrefix$imgNum.src = \"$src\";
";
	}
	$java_script .= "\n// End --></script>\n";
	return $java_script;
}
/////////////////////////////////////////////////////////////////////////////
// Make a rollover image out of two path and add it to rollover collection //
/////////////////////////////////////////////////////////////////////////////
function makeImgRollover($normal,$rollover,$alt){
	if($rollover != ""){
		$imgName = addImageToPreloads($rollover);
		$rolloverScript = " name=\"$imgName\" onmouseover=\"$imgName.src='$rollover'\" onmouseout=\"$imgName.src='$normal'\" ";
	}
	return "<img src=\"$normal\" $rolloverScript border=\"0\" alt=\"$alt\">";
}
/////////////////////////////
// Make an horizontal menu //
/////////////////////////////
function makeHoriMenu($entrys,$num_selected){
	$ret = "
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"/gfx/menu/body/fond.gif\" width=\"100%\">
<tr>
";
	$num_entrys = sizeof($entrys);
	for($i=0;$i<$num_entrys;$i++){
		if($i>0){
			$ret .= "<td><div align=\"center\">
						<img src=\"/gfx/menu/body/inter.gif\">
					</div></td>";
		}
		$curEntry = $entrys[$i];
		$link = $curEntry["link"];
		$img = $curEntry["image"];
		$alt = $curEntry["alt"];
		$rollover = $curEntry["rollover"];
		$type = $curEntry["type"];
		if($i == $num_selected){
			$ret .= "<td><div align=\"center\">
					<a href=\"$link\">
					<img    src=\"$rollover\"
	                		border=\"0\" alt=\"$alt\"></a>
				</div></td>\n";
		}else{
			$rolledImg = makeImgRollover($img,$rollover,$alt);
			$ret .= "<td><div align=\"center\">
					<a href=\"$link\">
					$rolledImg
				</div></td>\n";
		}
	}
$ret .= "</tr>
</table>";
	return $ret;
}
///////////////////////////////////
// Make a standard vertical menu //
///////////////////////////////////
function makeVertiMenu($entrys_array,$gfx_path){

	global $lang;
	$nbr_entrys = sizeof($entrys_array);
	$out = "
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
";
	for($i=0;$i<$nbr_entrys;$i++){
		$menu_entry = $entrys_array[$i];
		$image = $menu_entry["image"];
		if($menu_entry["type"] == "title"){
			$out .= "<tr><td>
					<img src=\"$gfx_path/$image\" width=\"134\" height=\"22\">
				</td></tr>";
		}else if($menu_entry["type"] == "link"){
			$rollover = $menu_entry["rollover"];
			$link = $menu_entry["link"];
			$alt = $menu_entry["alt"];
			if(false == strstr(htmlentities($_SERVER["PHP_SELF"]),$link)){
				$rolledImg = makeImgRollover("$gfx_path/$image","$gfx_path/$rollover",$alt);
				$imgToDraw = "<a href=\"$link\">$rolledImg</a>";
			}else{
				$imgToDraw =  "<img border=\"0\" src=\"$gfx_path/$rollover\" width=\"134\" height=\"22\">";
			}
			$out .= "<tr><td>
				$imgToDraw
				</td></tr>";
		}else if($menu_entry["type"] == "space"){
			$out .= "<tr><td>&nbsp;</td></tr>";
		}
	}
	$out .= "</table>";
	return $out;
}
//////////////////////////////////////////////////
// DROP DOWN WITH IE STYLE TREE MENU GENERATION //
//////////////////////////////////////////////////
$ietype_menu_img_nbr=0;
$ietype_menu_recurs_level=0;
$treesign_array=array();
$treeAddrsArray = array();

$alt_signs = "";

// Calculate a string reprensentative of the
// tree cells to draw. Exemple of ret values : none/vline/endtree/hline
function makeTreeGfxUrl($array,$nbr){
	global $alt_signs;

	$ret = "";

	$alt_signs = "";
	for($i=0;$i<=$nbr;$i++){
		if($i > 0){
			$ret .= "/";
		}
		$plop = $array[$i];
		$ret .= $plop;
		if($plop == "none"){
			$alt_signs .= "";
		}else if($plop == "vline"){
			$alt_signs .= "-";
		}else if($plop == "tree"){
			$alt_signs .= "|";
		}else if($plop == "endtree"){
			$alt_signs .= "`";
		}else if($plop == "hline"){
			$alt_signs .= "-";
		}else if($plop == "minus"){
			$alt_signs .= "-";
		}else if($plop == "plus"){
			$alt_signs .= "+";
		}
	}
	return $ret;
}

// Calculate current entry full adresse
function calculateCurEntryAddr($entry){
	global $ietype_menu_recurs_level;
	global $treeAddrsArray;

	$ret = "";

	$entrylink = $entry["link"];
	$treeAddrsArray[$ietype_menu_recurs_level] = $entrylink;
	for($i=0;$i<=$ietype_menu_recurs_level;$i++){
		if($i>0){
			$ret .= "/";
		}
		$ret .= $treeAddrsArray[$i];
	}
	return $ret;
}

function getCacheImageURL($text,$color,$arbo,$addrlink){
	global $lang;
	$cache = str_replace("/","_",$lang."_".$text.$color.$arbo) . ".png";
	if(file_exists("../shared/imgcache/$cache")){
		$url = "imgcache/$cache";
	}else{
		$url = "inc/img.php?text=$text&color=$color&link=$arbo&addrlink=$addrlink&lang=$lang";
	}
	return $url;
}

function makeIetypeMenu($menu,$curent_addr,$self_link,$link_name){
	global $ietype_menu_img_nbr;
	global $ietype_menu_recurs_level;
	global $link;
	global $treesign_array;

	global $alt_signs;
	global $dtc_use_text_menu;

	$ret = "";

	// Get an array out of the current selected addresse
	$selected = explode("/",$curent_addr);


	// For each item of current level of the tree
	$nbr_menu_entry = sizeof($menu);
	for($i=0;$i<$nbr_menu_entry;$i++){
		$entry = $menu[$i];
		$text = $entry["text"];
		if(isset($entry["icon"])){
			$icon = $entry["icon"];
		}else{
			$icon = "";
		}

		// Calculate current addresse
		$entrylink = calculateCurEntryAddr($entry);

		// Calculate the href link that the menu entry point to
		$url_link = "$self_link&$link_name=$entrylink";
		$alink = "<a href=\"$url_link\">";

		// Is it a drop down entry with plus/minus lign ?
		if($entry["type"] == "menu"){
			if($entry["link"] == $selected[$ietype_menu_recurs_level]){
				$treesign_array[$ietype_menu_recurs_level] = "minus";
				$arbo = makeTreeGfxUrl($treesign_array,$ietype_menu_recurs_level);
				$image_source = getCacheImageURL($text,1,$arbo,$entrylink);
				if($dtc_use_text_menu == "no"){
					if(function_exists("skin_AlternateTreeView")){
						$ret .= skin_AlternateTreeView($url_link,$text,1,$arbo,$entrylink,0,$icon);
					}else{
						$ret .= "$alink<img width=\"220\" height=\"32\" border=\"0\" alt=\"-".$entry["text"]."\" src=\"$image_source\"></a><br>";
					}
				}else{
					$ret .= $alink." -".$entry["text"]."</a><br>";
				}

				$treesign_array[$ietype_menu_recurs_level] = "tree";

				// Recurse inside the menu because it is selected

				$ietype_menu_recurs_level += 1;
				$ret .= makeIetypeMenu($entry["sub"],$curent_addr,$self_link,$link_name);
				$ietype_menu_recurs_level -= 1;
			}else{
				// Menu is not selected, so just draw it normaly
				$treesign_array[$ietype_menu_recurs_level] = "plus";
				$arbo = makeTreeGfxUrl($treesign_array,$ietype_menu_recurs_level);
				$image_source = getCacheImageURL($text,0,$arbo,$entrylink);
				if($dtc_use_text_menu == "no"){
					if(function_exists("skin_AlternateTreeView")){
						$ret .= skin_AlternateTreeView($url_link,$text,0,$arbo,$entrylink,1,$icon);
					}else{
						$ret .= "$alink<img width=\"220\" height=\"32\" border=\"0\" name=\"$rolovered\"
src=\"$image_source\" alt=\"$alt_signs".$entry["text"]."\" \"></a><br>";
					}
				}else{
					$ret .= "$alink".$alt_signs.$entry["text"]."</a><br>";
				}
			}
		}else if($entry["type"] == "link"){
			// Calculate the sign to put at the left of the entry (plus, minus, or none)
			if($ietype_menu_recurs_level > 0){
				if($i == $nbr_menu_entry-1){
					$mysign="endtree";
				}else{
					$mysign="tree";
				}
			}else if($entry["type"] == "link"){
				$mysign="";
			}
			if($ietype_menu_recurs_level > 0){
				$treesign_array[$ietype_menu_recurs_level] = "hline";
				$treesign_array[$ietype_menu_recurs_level-1] = "$mysign";
				if($ietype_menu_recurs_level > 1){
					$treesign_array[$ietype_menu_recurs_level-2] = "vline";
				}
			}else{
				$treesign_array[$ietype_menu_recurs_level] = "none";
			}
			$arbo = makeTreeGfxUrl($treesign_array,$ietype_menu_recurs_level);
			if($entry["link"] == @$selected[$ietype_menu_recurs_level]){
				$image_source = getCacheImageURL($text,1,$arbo,$entrylink);
				if($dtc_use_text_menu == "no"){
					if(function_exists("skin_AlternateTreeView")){
						$ret .= skin_AlternateTreeView($url_link,$text,1,$arbo,$entrylink,0,$icon);
					}else{
						$ret .= "$alink<img width=\"220\" height=\"32\" border=\"0\" alt=\"$alt_signs".$entry["text"]."\" src=\"$image_source\"></a><br>";
					}
				}else{
					$ret .= "$alink".$alt_signs.$entry["text"]."</a><br>";
				}
			}else{
				$image_source = getCacheImageURL($text,0,$arbo,$entrylink);
				if($dtc_use_text_menu == "no"){
					if(function_exists("skin_AlternateTreeView")){
						$ret .= skin_AlternateTreeView($url_link,$text,0,$arbo,$entrylink,1,$icon);
					}else{
						$ret .= "$alink<img width=\"220\" height=\"32\" border=\"0\" name=\"$rolovered\"
src=\"$image_source\" alt=\"$alt_signs".$entry["text"]."\" \"></a><br>";
					}
				}else{
					$ret .= "$alink".$alt_signs.$entry["text"]."</a><br>";
				}
			}
			if($mysign=="endtree"){
				$treesign_array[$ietype_menu_recurs_level] = "none";
			}
		}
	}

	return $ret;
}

function makeTreeMenu($menu,$selected,$self_link,$link_name){
	global $ietype_menu_img_nbr;
	global $ietype_menu_recurs_level;
	global $treesign_array;
	global $treeAddrsArray;

	global $dtc_use_text_menu;

	$ret = "";

	$ietype_menu_img_nbr=0;
	$ietype_menu_recurs_level=0;
	$treesign_array=array();
	$treeAddrsArray = array();

	if($dtc_use_text_menu == "yes"){
		$ret .= "<pre><b><font size=\"+1\">";
	}
	if(function_exists("skin_AliternateTreeViewContainer")){
		$ret .= skin_AliternateTreeViewContainer(makeIetypeMenu($menu,$selected,$self_link,$link_name));
	}else{
		$ret .= makeIetypeMenu($menu,$selected,$self_link,$link_name);
	}
	if($dtc_use_text_menu == "yes"){
		$ret .= "</font></b></pre>";
	}
	return $ret;
}


?>
