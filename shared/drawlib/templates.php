<?php

// Properties for this grid_display stuff is an object of that kind:
// array(
//   action => "name" -> name of the sql function for doing the submit job
//   forward => array(var1,var2...) -> names of the variables to forward
//   cols => array(
//		"feild0" => array(
//			type => "id",
//			display => yes|no,
//			legend => "Text to display"),
//		"feild1" => array(
//			type => "text",
//			legend => "Text to display"),
//              "feild2" => array(
//			type => "radio",
//			values => array("red","green","blue"),
//			legend => "Text to display"),
//		"feild3" => array(
//			type => "checkbox",
//			values => array("yes","no"),  -> First value means checked!
//              "feild4" => array(
//			type => "textarea",
//			legend => "Text to display"),
//              "feild5" => array(
//                      type => "hyperlink"
//                      text => "text to the link"
//              ...    -> names of the sql feilds to edit
//   table_name => "name"

function dtcDatagrid($dsc){

	$out = "<b><u>".$dsc["title"]."</u></b>";

	$nbr_forwards = sizeof($dsc["forward"]);
	$keys_fw = array_keys($dsc["forward"]);

	$fw = "";
	$fw_link = $_SERVER["PHP_SELF"]."?";
//	echo "<pre>"; print_r($keys_fw); echo "</pre>";
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
				if($dsc["cols"][ $keys[$i] ]["type"] != "hyperlink" && $dsc["cols"][ $keys[$i] ]["type"] != "id"){
					if($added_one == "yes"){
						$vals .= ", ";
						$qflds .= ", ";
					}
					$qflds .= $keys[$i];
					$vals .= "'".$_REQUEST[ $keys[$i] ]."'";
					$added_one = "yes";
				}
			}
			$q = "INSERT INTO ".$dsc["table_name"]." ($qflds) VALUES($vals);";
			break;
		case $dsc["action"]."_edit":
			$vals = "";
			for($i=0;$i<$nbr_fld;$i++){
				if($dsc["cols"][ $keys[$i] ]["type"] == "id"){
					$id = $_REQUEST[ $keys[$i] ];
					$id_name = $keys[$i];
				}else if($dsc["cols"][ $keys[$i] ]["type"] == "hyperlink"){
				}else{
					if($added_one == "yes"){
						$vals .= ", ";
					}
					$vals .= " ".$keys[$i]."='".$_REQUEST[ $keys[$i] ]."' ";
					$added_one = "yes";
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
					$out .= " <input type=\"radio\" $size name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected> ";
					$out .= $dsc["cols"][ $keys[$j] ]["values"][$x];
				}
				$out .= "</td>";
				break;
			case "checkbox":
				break;
			case "textaera":
				break;
			case "hyperlink":
				$out .= "<td class=\"dtcDatagrid_table_flds\">";
				$out .= "<a href=\"$fw_link&".$keys[$j]."=".$id."\">";
				$out .= $dsc["cols"][ $keys[$j] ]["text"]."</a>";
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
		$out .= "<td class=\"dtcDatagrid_table_flds\"><input type=\"submit\" value=\"Save\"></form></td>";
		$out .= "<td class=\"dtcDatagrid_table_flds\"><form action=\"".$_SERVER["PHP_SELF"]."\">$fw$id_hidden
		<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_delete\"><input type=\"submit\" value=\"Delete\"></form></td>";
		$out .= "</form></tr>";
	}
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
			break;
		case "textaera":
			break;
		case "hyperlink":
			$out .= "<td class=\"dtcDatagrid_table_flds\"></td>";
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
	$out .= "<td class=\"dtcDatagrid_table_flds\" colspan=\"2\"><input type=\"submit\" value=\"New\"></form></td></tr>";
	$out .= "</table>";
	return $out;
}

?>
