<?php

function helpLink($link){
	$out = "<a target=\"_blank\" href=\"http://dtcsupport.gplhost.com/pmwiki/$link\"><img border=\"0\" src=\"gfx/help.png\"</a>";
	return $out;
}

function dtcFormTableAttrs(){
	$out = "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\">";
	return $out;
}

function dtcFormLineDraw($text,$control){
	$bgcolor = "#AAAAFF";
	$incolor = "#FFFFFF";
	$out = "<tr><td bgcolor=\"$bgcolor\" style=\"text-align:right;white-space:nowrap;\">$text</td>
	<td bgcolor=\"$bgcolor\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"$incolor\">
<tr><td style=\"white-space:nowrap;vertical-valign:bottom;\">$control</td></tr></table></td></tr>";
	return $out;
}


// Properties for this grid_display stuff is an object of that kind:
// array(
//   table_name => "$pro_mysql_blabla_table" -> Name of the SQL table to edit
//   title => "The editor for this" -> Name of the title to display
//   action => "domain_prop_editor" -> name of the sql function forwarded in the form for doing the submit job (anything is ok, but don't have twice the same stuff)
//   forward => array(var1,var2...) -> names of the variables to forward
//   [skip_deletion] => "yes" -> Do not display the deletion option
//   [skip_creation] => "yes" -> Do not display the new item line
//   [where_condition] => "blabla='hop' AND titi='toto'" -> Condition of the display of the table
//   cols => array(
//		"feild0" => array(	-> Note this will be use as a WHERE blabla='titi' for the UPDATE statement
//			type => "id",
//			display => yes|no,
//			legend => "Text to display"),
//		"feild1" => array(
//			type => "text",
//			legend => "Text to display"),
//              "feild2" => array(
//			type => "radio", -> works the same way if "popup"
//			values => array("red","green","blue"),
//			legend => "Text to display"),
//                      [display_replace] => array("mega-cool) -> Used for popup only. List of display text for each of the "values". This list can be smaller than the "values" list, then it wont be replaced.
//		"feild3" => array(
//			type => "checkbox",
//			values => array("yes","no"),  -> First value means checked!
//              "feild4" => array(
//			type => "textarea",
//			legend => "Text to display"),
//              "feild5" => array(
//                      type => "hyperlink"
//                      text => "text to the link"
//              "feild6" => array(
//                      type => "info" -> used to only display a field that can not be edited
//                      legend => "Text to display"
//              ...    -> names of the sql feilds to edit
//   table_name => "name"

function dtcDatagrid($dsc){

	$out = "<b><u>".$dsc["title"]."</u></b>";

	$nbr_forwards = sizeof($dsc["forward"]);
	$keys_fw = array_keys($dsc["forward"]);

	$fw = "";
	$fw_link = $_SERVER["PHP_SELF"]."?";
	for($i=0;$i<$nbr_forwards;$i++){
		$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$_REQUEST[ $dsc["forward"][$i] ]."\">";
		if($i != 0){
			$fw_link .= "&";
		}
		$fw_link .= $dsc["forward"][$i]."=".$_REQUEST[ $dsc["forward"][$i] ];
	}

	$nbr_fld = sizeof($dsc["cols"]);
	$flds = "";
	$keys = array_keys($dsc["cols"]);
	for($i=0;$i<$nbr_fld;$i++){
		if($i != 0){
			$flds .= ", ";
		}
		$flds .= $keys[$i];
	}

	if(isset($_REQUEST["action"])){
		$added_one = "no";
		switch($_REQUEST["action"]){
		case $dsc["action"]."_new":
			$vals = "";
			$qflds = "";
			for($i=0;$i<$nbr_fld;$i++){
				switch($dsc["cols"][ $keys[$i] ]["type"]){
				case "text":
				case "radio":
				case "popup":
				case "textarea":
					if($added_one == "yes"){
						$vals .= ", ";
						$qflds .= ", ";
					}
					$qflds .= $keys[$i];
					$vals .= "'".$_REQUEST[ $keys[$i] ]."'";
					$added_one = "yes";
					break;
				case "checkbox":
					if($added_one == "yes"){
						$vals .= ", ";
						$qflds .= ", ";
					}
					if( isset($_REQUEST[ $keys[$i] ]) ){
						$index_val = 0;
					}else{
						$index_val = 1;
					}
					$qflds .= $keys[$i];
					$vals .= "'".$dsc["cols"][ $keys[$i] ]["values"][$index_val]."'";
					break;
				default:
					break;
				}
			}
			$q = "INSERT INTO ".$dsc["table_name"]." ($qflds) VALUES($vals);";
			break;
		case $dsc["action"]."_edit":
			$vals = "";
			for($i=0;$i<$nbr_fld;$i++){
				switch($dsc["cols"][ $keys[$i] ]["type"]){
				case "id":
					$id = $_REQUEST[ $keys[$i] ];
					$id_name = $keys[$i];
					break;
				case "info":
				case "hyperlink":
					break;
				case "checkbox":
					if( isset($_REQUEST[ $keys[$i] ]) ){
						$index_val = 0;
					}else{
						$index_val = 1;
					}
					if($added_one == "yes"){
						$vals .= ", ";
					}
					$vals .= " ".$keys[$i]."='".$dsc["cols"][ $keys[$i] ]["values"][$index_val]."' ";
					$added_one = "yes";
					break;
				default:
					if($added_one == "yes"){
						$vals .= ", ";
					}
					$vals .= " ".$keys[$i]."='".$_REQUEST[ $keys[$i] ]."' ";
					$added_one = "yes";
					break;
				}
			}
			$q = "UPDATE ".$dsc["table_name"]." SET $vals WHERE $id_name='$id';";
			break;
		case $dsc["action"]."_delete":
			for($i=0;$i<$nbr_fld;$i++){
				if($dsc["cols"][ $keys[$i] ]["type"] == "id"){
					$id = $_REQUEST[ $keys[$i] ];
					$id_name = $keys[$i];
				}
			}
			$q = "DELETE FROM ".$dsc["table_name"]." WHERE $id_name='$id';";
			break;
		default:
			$q = "";
			break;
		}
		if($q != ""){
			$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_query());
		}
	}

	// Display of all the titles of the table
	$out .= "<table class=\"dtcDatagrid_table_props\">";
	$out .= "<tr>";
	for($i=0;$i<$nbr_fld;$i++){
		if($dsc["cols"][ $keys[$i] ]["type"] == "id"){
			if($dsc["cols"][ $keys[$i] ]["display"] == "no"){
				$do_display = "no";
			}else{
				$do_display = "yes";
			}
		}else{
			$do_display = "yes";
		}
		if($do_display == "yes"){
			$out .= "<td class=\"dtcDatagrid_table_titles\"><b>".$dsc["cols"][ $keys[$i] ]["legend"]."</b></td>";
		}
	}
	$out .= "<td class=\"dtcDatagrid_table_titles\" colspan=\"2\"><b>Action</b></td>";
	$out .= "</tr>";

	// Display the existing entries of the table (edition and deletion)
	$added_one = "no";
	$sql_fld_list = "";
	for($i=0;$i<$nbr_fld;$i++){
		if($dsc["cols"][ $keys[$i] ]["type"] != "hyperlink"){
			if($added_one == "yes"){
				$sql_fld_list .= ", ";
			}
			$sql_fld_list .= $keys[$i];
			$added_one = "yes";
		}
	}

	// Display of all the values already in the table
	if(isset($dsc["where_condition"])){
		$where = " WHERE ".$dsc["where_condition"]." ";
	}else{
		$where = "";
	}
	$q = "SELECT $sql_fld_list FROM ".$dsc["table_name"]." $where;";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= "<tr><form action=\"".$_SERVER["PHP_SELF"]."\">$fw<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_edit\">";
		for($j=0;$j<$nbr_fld;$j++){
			$the_fld = $dsc["cols"][ $keys[$j] ];
			switch($the_fld["type"]){
			case "text":
				if( isset($dsc["cols"][ $keys[$j] ]["size"])){
					$size = " size=\"".$dsc["cols"][ $keys[$j] ]["size"]."\" ";
				}else{
					$size = "";
				}
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= "<input type=\"text\" $size name=\"".$keys[$j]."\" value=\"".$a[ $keys[$j] ]."\">";
				$out .= "</td>";
				break;
			case "radio":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$nbr_choices = sizeof( $dsc["cols"][ $keys[$j] ]["values"] );
				for($x=0;$x<$nbr_choices;$x++){
					if($a[ $keys[$j] ] == $dsc["cols"][ $keys[$j] ]["values"][$x]){
						$selected = " checked ";
					}else{
						$selected = "";
					}
					$out .= " <input type=\"radio\" name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected> ";
					$out .= $dsc["cols"][ $keys[$j] ]["values"][$x];
				}
				$out .= "</td>";
				break;
			case "checkbox":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				if($a[ $keys[$j] ] == $dsc["cols"][ $keys[$j] ]["values"][0]){
					$selected = " checked ";
				}else{
					$selected = "";
				}
				$out .= " <input type=\"checkbox\" name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][0]."\" $selected> ";
				$out .= "</td>";
				break;
			case "textaera":
				if( isset($dsc["cols"][ $keys[$j] ]["cols"])){
					$cols = " cols=\"".$dsc["cols"][ $keys[$j] ]["cols"]."\" ";
				}else{
					$cols = "";
				}
				if( isset($dsc["cols"][ $keys[$j] ]["raws"])){
					$raws = " cols=\"".$dsc["cols"][ $keys[$j] ]["raws"]."\" ";
				}else{
					$raws = "";
				}
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= "<textarea $cols $raws name=\"".$keys[$j]."\">".$a[ $keys[$j] ]."</textarea>";
				$out .= "</td>";
				break;
			case "hyperlink":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= "<a href=\"$fw_link&".$keys[$j]."=".$id."\">";
				$out .= $dsc["cols"][ $keys[$j] ]["text"]."</a>";
				$out .= "</td>";
				break;
			case "info":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= $a[ $keys[$j] ];
				$out .= "</td>";
				break;
			case "popup":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= "<select name=\"".$keys[$j]."\">";
				$nbr_values = sizeof($dsc["cols"][ $keys[$j] ]["values"]);
				for($x=0;$x<$nbr_values;$x++){
					if($dsc["cols"][ $keys[$j] ]["values"][$x] == $a[ $keys[$j] ]){
						$selected = " selected ";
					}else{
						$selected = "";
					}
					if( isset($dsc["cols"][ $keys[$j] ]["display_replace"][$x]) ){
						$display_value_popup = $dsc["cols"][ $keys[$j] ]["display_replace"][$x];
					}else{
						$display_value_popup = $dsc["cols"][ $keys[$j] ]["values"][$x];
					}
					$out .= "<option value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected>".$display_value_popup."</option>";
				}
				$out .= "<select>";
				$out .= "</td>";
				break;
			case "id":
				$id = $a[ $keys[$j] ];
				$id_hidden = "<input type=\"hidden\" name=\"".$keys[$j]."\" value=\"".$a[ $keys[$j] ]."\">";
				if($dsc["cols"][ $keys[$j] ]["display"] == "yes"){
					$out .= "<td class=\"dtcDatagrid_table_flds\">";
					$out .= $id_hidden;
					$out .= $a[ $keys[$j] ];
					$out .= "</td>";
				}else{
					$out .= $id_hidden;
				}
				break;
			}
		}
		$out .= "<td class=\"dtcDatagrid_table_flds\"><input type=\"image\" src=\"gfx/stock_apply_20.png\"></form></td>";
		if(isset($dsc["skip_deletion"]) && $dsc["skip_deletion"] == "yes"){
			$out .= "<td class=\"dtcDatagrid_table_flds\"></td></tr>";
		}else{
			$out .= "<td class=\"dtcDatagrid_table_flds\"><form action=\"".$_SERVER["PHP_SELF"]."\">$fw$id_hidden
			<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_delete\"><input type=\"image\" src=\"gfx/stock_trash_24.png\"></form></td>";
			$out .= "</form></tr>";
		}
	}
	if(!isset($dsc["skip_creation"]) || $dsc["skip_deletion"] != "yes"){
		// Write the NEW stuff...
		$out .= "<tr><form action=\"".$_SERVER["PHP_SELF"]."\">$fw<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_new\">";
		for($j=0;$j<$nbr_fld;$j++){
			$the_fld = $dsc["cols"][ $keys[$j] ];
			switch($the_fld["type"]){
			case "text":
				if( isset($dsc["cols"][ $keys[$j] ]["size"])){
					$size = " size=\"".$dsc["cols"][ $keys[$j] ]["size"]."\" ";
				}else{
					$size = "";
				}
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= "<input type=\"text\" $size name=\"".$keys[$j]."\" value=\"\">";
				$out .= "</td>";
				break;
			case "radio":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$nbr_choices = sizeof( $dsc["cols"][ $keys[$j] ]["values"] );
				for($x=0;$x<$nbr_choices;$x++){
					if($x == 0){
						$selected = " checked ";
					}else{
						$selected = "";
					}
					$out .= " <input type=\"radio\" $size name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected> ";
					$out .= $dsc["cols"][ $keys[$j] ]["values"][$x];
				}
				$out .= "</td>";
				break;
			case "checkbox":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= " <input type=\"checkbox\" name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][0]."\" selected> ";
				$out .= "</td>";
				break;
			case "textaera":
				break;
			case "hyperlink":
				$out .= "<td class=\"dtcDatagrid_table_flds\"></td>";
				break;
			case "popup":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= "<select name=\"".$keys[$j]."\">";
				$nbr_values = sizeof($dsc["cols"][ $keys[$j] ]["values"]);
				for($x=0;$x<$nbr_values;$x++){
					if( isset($dsc["cols"][ $keys[$j] ]["display_replace"][$x]) ){
						$display_value_popup = $dsc["cols"][ $keys[$j] ]["display_replace"][$x];
					}else{
						$display_value_popup = $dsc["cols"][ $keys[$j] ]["values"][$x];
					}
					$out .= "<option value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\">".$display_value_popup."</option>";
				}
				$out .= "<select>";
				$out .= "</td>";
				break;
			case "id":
				$id = $a[ $keys[$j] ];
				if($dsc["cols"][ $keys[$j] ]["display"] == "yes"){
					$out .= "<td class=\"dtcDatagrid_table_flds\">";
					$out .= "<input type=\"hidden\" name=\"".$keys[$j]."\" value=\"\">";
					$out .= $a[ $keys[$j] ];
					$out .= "</td>";
				}else{
					$out .= "<input type=\"hidden\" name=\"".$keys[$j]."\" value=\"\">";
				}
				break;
			}
		}
		$out .= "<td class=\"dtcDatagrid_table_flds\" colspan=\"2\"><input type=\"image\" src=\"gfx/stock_add_24.png\"></form></td></tr>";
	}
	$out .= "</table>";
	return $out;
}

?>
