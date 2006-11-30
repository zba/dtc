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
	<td bgcolor=\"$bgcolor\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"$incolor\" width=\"100%\">
<tr><td style=\"white-space:nowrap;vertical-valign:bottom;\">$control</td></tr></table></td></tr>";
	return $out;
}

function dtcFromOkDraw($delete_form=""){
	$out = "<tr><td>&nbsp;</td><td><input type=\"image\" src=\"gfx/stock_apply_20.png\">$delete_form</td></tr>";
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
//		"field0" => array(	-> Note this will be use as a WHERE blabla='titi' for the UPDATE statement
//			type => "id",
//			display => yes|no,
//			legend => "Text to display"),
//		"field1" => array(
//			type => "text",
//			legend => "Text to display"),
//              "field2" => array(
//			type => "radio", -> works the same way if "popup"
//			values => array("red","green","blue"),
//			legend => "Text to display"),
//                      [display_replace] => array("mega-cool) -> Used for popup only. List of display text for each of the "values". This list can be smaller than the "values" list, then it wont be replaced.
//		"field3" => array(
//			type => "checkbox",
//			values => array("yes","no"),  -> First value means checked!
//              "field4" => array(
//			type => "textarea",
//			legend => "Text to display"),
//              "field5" => array(
//                      type => "hyperlink"
//                      text => "text to the link"
//              "field6" => array(
//                      type => "info" -> used to only display a field that can not be edited
//                      legend => "Text to display"
//              ...    -> names of the sql feilds to edit
//   table_name => "name"

// Note: this function has no field validation checks, do not use for the user panel, only for the root one !!!
function dtcDatagrid($dsc){
	global $adm_pass;

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
		if($dsc["forward"][$i] == "adm_pass"){
			$fw_link .= $dsc["forward"][$i]."=$adm_pass";
		}else{
			$fw_link .= $dsc["forward"][$i]."=".$_REQUEST[ $dsc["forward"][$i] ];
		}
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
					if( isset($dsc["cols"][ $keys[$j] ]["default"]) ){
						if($dsc["cols"][ $keys[$j] ]["values"][$x] == $dsc["cols"][ $keys[$j] ]["default"]){
							$selected = " checked ";
						}else{
							$selected = "";
						}
					}else{
						if($x == 0){
							$selected = " checked ";
						}else{
							$selected = "";
						}
					}
					$out .= "<input type=\"radio\" name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected> ";
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

// This function is to be used for the user panel, it has field content check & validations plus addslashes
function dtcListItemsEdit($dsc){
	global $adm_pass;

	$out = "<b><u>".$dsc["title"]."</u></b><br><br>";

	// Calculate the forwards parameters for links and forms
	$nbr_forwards = sizeof($dsc["forward"]);
	$keys_fw = array_keys($dsc["forward"]);

	$fw = "";
	$fw_link = $_SERVER["PHP_SELF"]."?";
	for($i=0;$i<$nbr_forwards;$i++){
		$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$_REQUEST[ $dsc["forward"][$i] ]."\">";
		if($i != 0){
			$fw_link .= "&";
		}
		if($dsc["forward"][$i] == "adm_pass"){
			$fw_link .= $dsc["forward"][$i]."=$adm_pass";
		}else{
			$fw_link .= $dsc["forward"][$i]."=".$_REQUEST[ $dsc["forward"][$i] ];
		}
	}

	// Condition to add to each queries
	$where = "WHERE 1";
	$added_insert_names = "";
	$added_insert_values = "";
	if(isset($dsc["where_list"])){
		$nbr_where = sizeof($dsc["where_list"]);
		$where_keys = array_keys($dsc["where_list"]);
		for($i=0;$i<$nbr_where;$i++){
			if($i != 0){
				$added_insert .= ",";
				$added_insert_values .= ",";
			}
			$added_insert_names .= $where_keys[$i];
			$added_insert_values .= "'".$dsc["where_list"][ $where_keys[$i] ]."'";
			$where .= " AND ".$where_keys[$i]."='".$dsc["where_list"][ $where_keys[$i] ]."'";
		}
		// As there will be other fields, we need that one
		$added_insert_names .= ",";
		$added_insert_values .= ",";
	}
	// Number of fields that we are about to manage here and theire names
	$nbr_fld = sizeof($dsc["cols"]);
	$keys = array_keys($dsc["cols"]);

	// We need the current number of items now to check against the max number for addition
	$q = "SELECT ".$dsc["id_fld"].",".$dsc["list_fld_show"]." FROM ".$dsc["table_name"]." $where;";
	$r_item_list = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
	$current_num_items = mysql_num_rows($r_item_list);

	// SQL submit stuffs
	if( isset($_REQUEST["action"]) && $_REQUEST["action"] == $dsc["action"]."_new_item" ){
		// Todo: do the fields checkings
		$commit_flag = "yes";
		$commit_err = "";
		for($i=0;$i<$nbr_fld;$i++){
			switch($dsc["cols"][ $keys[$i] ]["type"]){
			case "radio":
			case "checkbox":
				$nbr_choices = sizeof($dsc["cols"][ $keys[$i] ]["values"]);
				$is_one_of_them = "no";
				for($j=0;$j<$nbr_choices;$j++){
					if($dsc["cols"][ $keys[$i] ]["values"][$j] == $_REQUEST[ $keys[$i] ]){
						$is_one_of_them = "yes";
					}
				}
				if($is_one_of_them == "no"){
					$commit_flag = "no";
					$commit_err = "the variable ".$keys[$i]." is not one of the allowed values<br>";
				}
				break;
			default:
				break;
			}
			if( isset($dsc["cols"][ $keys[$i] ]["check"]) && (!isset($dsc["cols"][ $keys[$i] ]["disable_edit"]) || $dsc["cols"][ $keys[$i] ]["disable_edit"] != "yes") ){
				switch($dsc["cols"][ $keys[$i] ]["check"]){
				case "subdomain":
					if( !checkSubdomainFormat($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a subdomain<br>";
						}
					}
					break;
				case "subdomain_or_ip":
					if( !checkSubdomainFormat($_REQUEST[ $keys[$i] ]) && !isIP($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a subdomain or IP addresse<br>";
						}
					}
					break;
				case "ip_addr":
					if( !isIP($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not an IP address<br>";
						}
					}
					break;
				case "domain_or_ip":
					if( !isIP($_REQUEST[ $keys[$i] ]) && !isHostname($_REQUEST[ $keys[$i] ])){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a domain or IP addresse<br>";
						}
					}
					break;
				case "dtc_login":
					if( !isFtpLogin($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct login format.<br>";
						}
					}
					break;
				case "dtc_pass":
					if( !isDTCPassword($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct password format<br>";
						}
					}
					break;
				default:
					$commit_flag = "no";
					$commit_err .= $keys[$i].": unknown field checking type (".$dsc["cols"][ $keys[$i] ]["check"].").<br>";
					break;
				}
			}
		}
		if(isset($dsc["max_item"]) && $current_num_items >= $dsc["max_item"]){
			$commit_flag = "no";
			$commit_err = "Max number of items reached!";
		}
		// Build the request
		$fld_names = "";
		$values = "";
		$added_one = "no";
		for($i=0;$i<$nbr_fld;$i++){
			switch($dsc["cols"][ $keys[$i] ]["type"]){
			case "text":
				if($added_one == "yes"){
					$fld_names .= ",";
					$values .= ",";
				}
				$fld_names .= $keys[$i];
				if( isset($dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"]) && $dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"] == "yes" && $_REQUEST[ $keys[$i] ] == ""){
					$values .= "NULL";
				}else{
					$values .= "'".addslashes($_REQUEST[ $keys[$i] ])."'";
				}
				$added_one = "yes";
				break;
			}
		}
		if($commit_flag == "yes"){
			$q = "INSERT INTO ".$dsc["table_name"]." ($added_insert_names $fld_names) VALUES ($added_insert_values $values);";
			$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
			if( isset($dsc["create_item_callback"]) ){
				$dsc["create_item_callback"]();
			}
		}else{
			$out .= "<font color=\"red\">Could not commit the changes because of an error in field format: <br>$commit_err</font>";
		}
	}else if( isset($_REQUEST["action"]) && $_REQUEST["action"] == $dsc["action"]."_save_item" ){
		// Todo: do the fields checkings
		$commit_flag = "yes";
		$commit_err = "";
		for($i=0;$i<$nbr_fld;$i++){
			switch($dsc["cols"][ $keys[$i] ]["type"]){
			case "radio":
			case "checkbox":
				$nbr_choices = sizeof($dsc["cols"][ $keys[$i] ]["values"]);
				$is_one_of_them = "no";
				for($j=0;$j<$nbr_choices;$j++){
					if($dsc["cols"][ $keys[$i] ]["values"][$j] == $_REQUEST[ $keys[$i] ]){
						$is_one_of_them = "yes";
					}
				}
				if($is_one_of_them == "no"){
					$commit_flag = "no";
					$commit_err = "the variable ".$keys[$i]." is not one of the allowed values<br>";
				}
				break;
			default:
				break;
			}
			if( isset($dsc["cols"][ $keys[$i] ]["check"]) && (!isset($dsc["cols"][ $keys[$i] ]["disable_edit"]) || $dsc["cols"][ $keys[$i] ]["disable_edit"] != "yes") ){
				switch($dsc["cols"][ $keys[$i] ]["check"]){
				case "subdomain":
					if( !checkSubdomainFormat($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a subdomain<br>";
						}
					}
					break;
				case "subdomain_or_ip":
					if( !checkSubdomainFormat($_REQUEST[ $keys[$i] ]) && !isIP($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a subdomain or IP addresse<br>";
						}
					}
					break;
				case "ip_addr":
					if( !isIP($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not an IP address<br>";
						}
					}
					break;
				case "domain_or_ip":
					if( !isIP($_REQUEST[ $keys[$i] ]) && !isHostname($_REQUEST[ $keys[$i] ])){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a domain or IP addresse<br>";
						}
					}
					break;
				case "dtc_login":
					if( !isFtpLogin($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct login format.<br>";
						}
					}
					break;
				case "dtc_pass":
					if( !isDTCPassword($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct password format<br>";
						}
					}
					break;
				default:
					$commit_flag = "no";
					$commit_err .= $keys[$i].": unknown field checking type (".$dsc["cols"][ $keys[$i] ]["check"].").<br>";
					break;
				}
			}
		}
		// Build the request
		$added_one = "no";
		$reqs = "";
		for($i=0;$i<$nbr_fld;$i++){
			switch($dsc["cols"][ $keys[$i] ]["type"]){
			case "id":
				$id_fldname = $keys[$i];
				$id_fld_value = addslashes($_REQUEST[ $keys[$i] ]);
				break;
			case "text":
				if( !isset($dsc["cols"][ $keys[$i] ]["disable_edit"]) || $dsc["cols"][ $keys[$i] ]["disable_edit"] != "yes"){
					if($added_one == "yes"){
						$reqs .= ",";
					}
					$reqs .= $keys[$i]."='".addslashes($_REQUEST[ $keys[$i] ])."'";
					$added_one = "yes";
				}
				break;
			case "radio":
				if($added_one == "yes"){
					$reqs .= ",";
				}
				$reqs .= $keys[$i]."='".addslashes($_REQUEST[ $keys[$i] ])."'";
				$added_one = "yes";
				break;
			case "checkbox":
			default:
				die("Not implemented yet line ".__LINE__." file ".__FILE__);
				break;
			}
		}
		if($commit_flag != "yes"){
			$out .= "<font color=\"red\">Could not commit the changes because of an error in field format: [todo: error desc]<br>$commit_err</font>";
		}else if(!isset($id_fldname) || !isset($id_fld_value)){
			$out .= "<font color=\"red\">Could not commit the changes because the id is not set!</font>";
		}else{
			$q = "UPDATE ".$dsc["table_name"]." SET $reqs $where AND $id_fldname='$id_fld_value';";
			$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
		}
	}else if( isset($_REQUEST["action"]) && $_REQUEST["action"] == $dsc["action"]."_delete_item" ){
		for($i=0;$i<$nbr_fld;$i++){
			if($dsc["cols"][ $keys[$i] ]["type"] == "id"){
				$id_fldname = $keys[$i];
				$id_fld_value = addslashes($_REQUEST[ $keys[$i] ]);
			}
		}
		if( isset($id_fldname) && isset($id_fld_value) ){
			if( isset($dsc["delete_item_callback"]) ){
				$dsc["delete_item_callback"]();
			}
			$q = "DELETE FROM ".$dsc["table_name"]." $where AND $id_fldname='".$id_fld_value."';";
			$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
		}else{
			$out .= "<font color=\"red\">Could not commit the deletion because the id field could not be found.</font>";
		}
	}

	// We have to query it again, in case an insert or a delete has occured!
	$q = "SELECT ".$dsc["id_fld"].",".$dsc["list_fld_show"]." FROM ".$dsc["table_name"]." $where;";
	$r_item_list = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
	$current_num_items = mysql_num_rows($r_item_list);

	if(isset($dsc["max_item"])){
		if($current_num_items >= $dsc["max_item"]){
			$out .= "<font color=\"red\">";
		}
		$out .= $dsc["num_item_txt"].$current_num_items."/".$dsc["max_item"];
		if($current_num_items >= $dsc["max_item"]){
			$out .= "</font>";
		}
		$out .= "<br><br>";
	}

	// First display a list of items
	for($i=0;$i<$current_num_items;$i++){
		$a = mysql_fetch_array($r_item_list);
		if($i!=0){
			$out .= " - ";
		}
		if( isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == $dsc["action"]."_edit_item" && $_REQUEST["item"] == $a[ $dsc["id_fld"] ]){
			$out .= $a[ $dsc["list_fld_show"] ];
		}else{
			$out .= "<a href=\"$fw_link&subaction=".$dsc["action"]."_edit_item&item=".$a[ $dsc["id_fld"] ]."\">".$a[ $dsc["list_fld_show"] ]."</a>";
		}
	}
	$out .= "<br><br>";

	// Creation of new items
	if( !isset($_REQUEST["subaction"]) || $_REQUEST["subaction"] != $dsc["action"]."_edit_item"){
		$out .= $dsc["new_item_link"]."<br><br>";
		$out .= "<u><b>".$dsc["new_item_title"]."</u></b><br>";
		if(isset($dsc["max_item"]) && $current_num_items >= $dsc["max_item"]){
			$out .= "<font color=\"red\">Maximum number reached!</font><br>";
		}else{
			$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">$fw
				<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_new_item\">".dtcFormTableAttrs();
			for($i=0;$i<$nbr_fld;$i++){
				switch($dsc["cols"][ $keys[$i] ]["type"]){
				case "id":
					$out .= "<input type=\"hidden\" name=\"".$keys[$i]."\" value=\"\">";
					break;
				case "text":
					$ctrl = "<input type=\"text\" name=\"".$keys[$i]."\" value=\"\">";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl);
					break;
				case "radio":
					$nbr_choices = sizeof( $dsc["cols"][ $keys[$i] ]["values"] );
					$ctrl = "";
					for($x=0;$x<$nbr_choices;$x++){
						if( isset($dsc["cols"][ $keys[$i] ]["default"]) ){
							if($dsc["cols"][ $keys[$i] ]["values"][$x] == $dsc["cols"][ $keys[$i] ]["default"]){
								$selected = " checked ";
							}else{
								$selected = "";
							}
						}else{
							if($x == 0){
								$selected = " checked ";
							}else{
								$selected = "";
							}
						}
						$ctrl .= " <input type=\"radio\" name=\"".$keys[$i]."\" value=\"".$dsc["cols"][ $keys[$i] ]["values"][$x]."\" $selected> ";
						$ctrl .= $dsc["cols"][ $keys[$i] ]["values"][$x];
					}
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl);
					break;
				default:
					$ctrl = "bla";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl);
					break;
				}
			}
			$out .= dtcFromOkDraw();
			$out .= "</table></form>";
		}
	// Edition of existing items
	}else{
		$out .= "<a href=\"$fw_link&subaction=".$dsc["action"]."_new_item\">New item</a><br><br>";
		$out .= "<b><u>".$dsc["edit_item_title"]."</u></b><br>";
		$q = "SELECT * FROM ".$dsc["table_name"]." $where AND ".$dsc["id_fld"]."='".addslashes($_REQUEST["item"])."';";
		$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 1){
			$a = mysql_fetch_array($r);
			$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">$fw";
			$out .= "<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_save_item\">";
			$out .= "<input type=\"hidden\" name=\"subaction\" value=\"".$dsc["action"]."_edit_item\">";
			$out .= "<input type=\"hidden\" name=\"item\" value=\"".$a[ $dsc["id_fld"] ]."\">";
			$out .= dtcFormTableAttrs();
			for($j=0;$j<$nbr_fld;$j++){
				$the_fld = $dsc["cols"][ $keys[$j] ];
				switch($the_fld["type"]){
				case "id":
					$out .= "<input type=\"hidden\" name=\"".$keys[$j]."\" value=\"".$a[ $keys[$j] ]."\">";
					$id_fldname = $keys[$j];
					$id_fld_value = $a[ $keys[$j] ];
					break;
				default:
				case "text":
					if( isset($dsc["cols"][ $keys[$j] ]["disable_edit"]) && $dsc["cols"][ $keys[$j] ]["disable_edit"] == "yes"){
						$disabled = " disabled ";
					}else{
						$disabled = " ";
					}
					if( isset($dsc["cols"][ $keys[$j] ]["size"])){
						$size = " size=\"".$dsc["cols"][ $keys[$j] ]["size"]."\" ";
					}else{
						$size = "";
					}
					$ctrl = "<input type=\"text\" $size name=\"".$keys[$j]."\" value=\"".$a[ $keys[$j] ]."\" $disabled>";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl);
					break;
				case "radio":
					$nbr_choices = sizeof( $dsc["cols"][ $keys[$j] ]["values"] );
					$ctrl = "";
					for($x=0;$x<$nbr_choices;$x++){
						if($dsc["cols"][ $keys[$j] ]["values"][$x] == $a[ $keys[$j] ]){
							$selected = " checked ";
						}else{
							$selected = "";
						}
						$ctrl .= " <input type=\"radio\" name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected> ";
						$ctrl .= $dsc["cols"][ $keys[$j] ]["values"][$x];
					}
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl);
					break;
				}
			}
			$delete_button = "<form action=\"".$_SERVER["PHP_SELF"]."\">$fw
			<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_delete_item"."\">
			<input type=\"hidden\" name=\"$id_fldname\" value=\"$id_fld_value\">
			<input type=\"image\" src=\"gfx/stock_trash_24.png\"></form>";

			$out .= "<tr><td>&nbsp;</td><td><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			<tr><td><input type=\"image\" src=\"gfx/stock_apply_20.png\"></form></td><td>$delete_button</td></tr></table></td></tr>";

			$out .= "</table>";
		}else{
			$out .= "No item by this number!";
		}
	}
	return $out;
}

?>