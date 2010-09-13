<?php

$MTM_items = array();		// The table of calculated menu entries
$MTM_recur_lvl=0;			// Curent level of recursion
$MTM_curadrTbl = array();	// A table for calculating curent menu entry address

function MTMcalculateLinkTo($entry){
	global $MTM_curadrTbl;
	global $MTM_recur_lvl;


	$MTM_curadrTbl[$MTM_recur_lvl] = $entry["arbo"];
	$entrylink = $entry["link"];
	$ret .= htmlentities($_SERVER["PHP_SELF"])."?mtm_addr=";
	for($i=0;$i<=$MTM_recur_lvl;$i++){
		if($i>0){
			$ret .= "/";
		}
		$ret .= $MTM_curadrTbl[$i];
	}
	return $ret;
}

function MTMimageLink($alink,$txt,$isSelected){
	global $MTM_recur_lvl;
	global $MTM_items;

	$alt = "";
	for($i=0;$i<=$MTM_recur_lvl;$i++){
		if($i>0){
			$signs .= "/";
		}
		$signs .= $MTM_items[$i];
		if($MTM_items[$i] == "hline"){
			$alt .= "|";
		}else if($MTM_items[$i] == "vline"){
			$alt .= "-";
		}else if($MTM_items[$i] == "endtree"){
			$alt .= "L";
		}else if($MTM_items[$i] == "tree"){
			$alt .= "+";
		}else if($MTM_items[$i] == "minus"){
			$alt .= "-";
		}
	}

	$img_src = "img2.php?link=$signs&text=$txt";

	if($isSelected)
		$img_source = "src=\"$img_src&color=1\"";
	else{
		$image_rolover = "$img_src&color=1";
		$image_source = "$img_src&color=0";
		$rolovered = addImageToPreloads($image_rolover);

		$img_source = " name=\"$rolovered\" src=\"$img_src&color=0\"
onMouseOver=\"$rolovered.src='$image_rolover'\"
onMouseOut=\"$rolovered.src='$image_source'\"";
	}

	return "$alink<img width=\"220\" height=\"20\" alt=\"$alt\" $img_source border=\"0\"></a><br>";
}

function MTMRecursiv($menu){
	global $MTM_items;
	global $MTM_recur_lvl;
	global $mtm_addr;

	// Get an array out of the current selected addresse
	$selected = explode("/",$mtm_addr);
	$selected_tree_level = sizeof($selected);

	// For each item of current level of the tree
	$nbr_menu_entry = sizeof($menu);
	for($i=0;$i<$nbr_menu_entry;$i++){
		$entry = $menu[$i];

		// Calculate the href link that the menu entry point to
		$linkTo = MTMcalculateLinkTo($entry);
		$alink = "<a href=\"$linkTo\">";
//		echo "$linkTo<br>";

		if(is_array($entry["sub"])){
			if($entry["arbo"] == $selected[$MTM_recur_lvl]){
				if($selected_tree_level-1 == $MTM_recur_lvl)
					$isEntrySelected = true;
				else
					$isEntrySelected = false;
				// Draw the minus sign
				$MTM_items[$MTM_recur_lvl] = "minus";
				$ret .= MTMimageLink($alink,$entry["text"],$isEntrySelected);
				$MTM_items[$MTM_recur_lvl] = "tree";

				// Do the recursion
				$MTM_items[$MTM_recur_lvl-1] = "vline";
				$MTM_recur_lvl += 1;
				$ret .= MTMRecursiv($entry["sub"]);
				$MTM_recur_lvl -= 1;
			}else{
				// Draw the plus sign
				if($MTM_recur_lvl > 0){
					if($i == $nbr_menu_entry-1){
						$MTM_items[$MTM_recur_lvl-1] = "endtree";
					}else{
						$MTM_items[$MTM_recur_lvl-1] = "tree";
					}
				}
				$MTM_items[$MTM_recur_lvl] = "plus";
				$ret .= MTMimageLink($alink,$entry["text"],false);
			}
		}else{
			if($MTM_recur_lvl == $selected_tree_level-1 && $selected[$MTM_recur_lvl] == $entry["arbo"])
				$isEntrySelected = true;
			else
				$isEntrySelected = false;
			if($MTM_recur_lvl > 0){
				if($i == $nbr_menu_entry-1){
					$mysign="endtree";
				}else{
					$mysign="tree";
				}
				$MTM_items[$MTM_recur_lvl] = "hline";
				$MTM_items[$MTM_recur_lvl-1] = "$mysign";
				if($MTM_recur_lvl > 1){
					$MTM_items[$MTM_recur_lvl-2] = "vline";
				}

				$ret .= MTMimageLink($alink,$entry["text"],$isEntrySelected);
			}else{
				$MTM_items[$MTM_recur_lvl] = "none";
				$ret .= MTMimageLink($alink,$entry["text"],$isEntrySelected);
			}
		}
	}
	return $ret;
}


function makeTreeMenu2($menu){
	
	global $MTM_items;
	global $mtm_addr;

	$MTM_items = array();
	$MTM_recur_lvl = 0;
	$MTM_curadrTbl = array();

	return MTMRecursiv($menu);
}

?>
