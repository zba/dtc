<?php

function helpLink($link){
	global $gfx_icn_path_help;
	if(isset($gfx_icn_path_help)){
		$helpimg_src = $gfx_icn_path_help;
	}else{
		$helpimg_src = "gfx/help.png";
	}
	$out = "<a target=\"_blank\" href=\"http://dtcsupport.gplhost.com/$link\"><img border=\"0\" src=\"$helpimg_src\"</a>";
	return $out;
}

function submitButtonStart(){
	return "<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\"
	onMouseOut=\"this.className='input_btn_container';\">
	 <div class=\"input_btn_left\"></div>
	  <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"";
}

function submitButtonEnd(){
	return "\"></div>
	 <div class=\"input_btn_right\"></div></div>";
}

function dtcFormTableAttrs(){
	$out = "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\">";
	return $out;
}

function dtcFormLineDraw($text,$control,$alternate_color=1,$help_text=""){
	global $gfx_form_entry_label_background;
	global $gfx_form_entry_label_control_background;
	global $gfx_form_entry_label_alt_background;
	global $gfx_form_entry_label_alt_control_background;

	if($alternate_color == 1){
		if(isset($gfx_form_entry_label_background)){
			$bgcolor = $gfx_form_entry_label_background;
		}else{
			$bgcolor = " bgcolor=\"#AAAAFF\" class=\"box_formtable_libelle\" ";
		}
		if(isset($gfx_form_entry_label_control_background)){
			$incolor = $gfx_form_entry_label_control_background;
		}else{
			$incolor = " bgcolor=\"#FFFFFF\" class=\"box_formtable_inputz\" ";
		}
	}else{
		if(isset($gfx_form_entry_label_alt_background)){
			$bgcolor = $gfx_form_entry_label_alt_background;
		}else{
			$bgcolor = " ";
		}
		if(isset($gfx_form_entry_label_alt_control_background)){
			$incolor = $gfx_form_entry_label_alt_control_background;
		}else{
			$incolor = " ";
		}
	}

	if($help_text != ""){
		$jshelp = " onmouseover=\"Tip('". addslashes($help_text) ."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" ";
	}else{
		$jshelp = "";
	}

	$out = "
  <tr>
    <th $bgcolor $jshelp style=\"text-align:right;white-space:nowrap;\">$text</th>
    <td $bgcolor>
      <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" height=\"100%\">
        <tr>
          <td $incolor style=\"white-space:nowrap;vertical-valign:bottom;\" height=\"100%\">$control</td>
        </tr>
      </table>
    </td>
  </tr>";
	return $out;
}

function dtcApplyButton(){
	global $gfx_icn_path_ok;
	if(isset($gfx_icn_path_ok)){
		$apply = "<div class=\"btn_p_container\" onMouseOver=\"this.className='btn_p_container-hover';\" onMouseOut=\"this.className='btn_p_container';\">
		<input type=\"image\" src=\"".$gfx_icn_path_ok."\"></div>";
//		<img src=\"".$gfx_icn_path_ok."\"></div>";
	}else{
		$apply = "<input type=\"image\" src=\"gfx/stock_apply_20.png\">";
	}
	return $apply;
}

function dtcDeleteButton(){
	global $gfx_icn_path_delete;
	global $conf_use_javascript;

	if(isset($gfx_icn_path_delete)){
                if ( $conf_use_javascript == "yes" ) {
                  $click_confirm = " onClick=\"return confirm('Are you sure to delete?')\"";
                } else { $click_confirm = NULL; }
		$delete = "<div class=\"btn_p_container\" onMouseOver=\"this.className='btn_p_container-hover';\" onMouseOut=\"this.className='btn_p_container';\"><input type=\"image\" src=\"".$gfx_icn_path_delete."\"".$click_confirm."></div>";
	}else{
		$delete = "<input type=\"image\" src=\"gfx/stock_trash_24.png\">";
	}
	return $delete;
}

function dtcAddButton(){
	global $gfx_icn_path_add;

	if(isset($gfx_icn_path_add)){
		$delete = "<div class=\"btn_p_container\" onMouseOver=\"this.className='btn_p_container-hover';\" onMouseOut=\"this.className='btn_p_container';\"><input type=\"image\" src=\"".$gfx_icn_path_add."\"></div>";
	}else{
		$delete = "<input type=\"image\" src=\"gfx/stock_add_24.png\">";
	}
	return $delete;
}

function dtcFromOkDraw($delete_form=""){
	$out = "<tr><td>&nbsp;</td><td>".dtcApplyButton()."$delete_form</td></tr>";
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
//   [update_check_callback] => Callback to accept or deny the update of a record.
//   [insert_check_callback] => callback to accept or deny the insert of a record
//   [where_condition] => "blabla='hop' AND titi='toto'" -> Condition of the display of the table
//   [order_by] => "fldname_1" -> Condition pour le order by
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
//			[cols] => 40 -> Number of cols
//			[rows] => 9 -> Number of rows
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
	global $txt_action;
	global $lang;
	global $action_error_txt;

	global $gfx_form_entry_label_background;
	global $conf_enforce_adm_encryption;

	$nbr_forwards = sizeof($dsc["forward"]);
	$keys_fw = array_keys($dsc["forward"]);

	// Process the "forward" parameter of the grid widget, so that $rub and $sousrub and so on are forwarded
	$fw = "";
	$fw_link = "?";
	for($i=0;$i<$nbr_forwards;$i++){
		if($dsc["forward"][$i] == "adm_pass"){
			$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$adm_pass."\">";
		}else{
			$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$_REQUEST[ $dsc["forward"][$i] ]."\">";
		}
		if($i != 0){
			$fw_link .= "&";
		}
		if($dsc["forward"][$i] == "adm_pass"){
			$fw_link .= $dsc["forward"][$i]."=$adm_pass";
		}else{
			$fw_link .= $dsc["forward"][$i]."=".$_REQUEST[ $dsc["forward"][$i] ];
		}
	}

	// Get a list of fields in sql and build a "field1, field2" string
	$nbr_fld = sizeof($dsc["cols"]);
	$flds = "";
	$keys = array_keys($dsc["cols"]);
	for($i=0;$i<$nbr_fld;$i++){
		if($i != 0){
			$flds .= ", ";
		}
		$flds .= $keys[$i];
	}

	// Start of performing actions (new, update, delete)
	$action_error_txt = "";

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
				case "password":
					if($added_one == "yes"){
						$vals .= ", ";
						$qflds .= ", ";
					}
					$qflds .= $keys[$i];
					if(isset($dsc["cols"][ $keys[$i] ]["happen_domain"])){
						$happen = $dsc["cols"][ $keys[$i] ]["happen_domain"];
					}else{
						$happen = "";
					}
					if( $dsc["cols"][ $keys[$i] ]["type"] == "password" && isset($dsc["cols"][ $keys[$i] ]["encrypt"]) && $dsc["cols"][ $keys[$i] ]["encrypt"] == "yes" && $conf_enforce_adm_encryption == "yes"){
						$vals .= "SHA1('".$_REQUEST[ $keys[$i] ].$happen."')";
					}else{
						$vals .= "'".$_REQUEST[ $keys[$i] ].$happen."'";
					}
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
					$added_one = "yes";
					break;
				case "custom_fld":
					//
					break;
				default:
					break;
				}
			}
			// Make sure we have the where_condition field filled with the correct value
			if( isset($dsc["where_condition"]) ){
				$exploded = explode("=",$dsc["where_condition"]);
				$qflds .= ", ".$exploded[0];
				$vals .= ", ".$exploded[1];
			}
			if( isset($dsc["insert_check_callback"]) ){
				if ( $dsc["insert_check_callback"]() ){
					$q = "INSERT INTO ".$dsc["table_name"]." ($qflds) VALUES($vals);";
				}else{
					$q = "";
				}
			}else{
				$q = "INSERT INTO ".$dsc["table_name"]." ($qflds) VALUES($vals);";
			}
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
				case "password":
					if($added_one == "yes"){
                                                $vals .= ", ";
                                        }
					if( isset($dsc["cols"][ $keys[$i] ]["encrypt"]) && $dsc["cols"][ $keys[$i] ]["encrypt"] == "yes" && $conf_enforce_adm_encryption){
	                                        $vals .= " ".$keys[$i]."=SHA1('".$_REQUEST[ $keys[$i] ]."') ";
					}else{
	                                        $vals .= " ".$keys[$i]."='".$_REQUEST[ $keys[$i] ]."' ";
					}
                                        $added_one = "yes";
					// if the crypt field is set, then we use this as the SQL field to populate the crypted password into
					if(isset($dsc["cols"][ $keys[$i] ]["cryptfield"])){
						if($added_one == "yes"){
							$vals .= ", ";
						}
						$vals .= " ".$dsc["cols"][ $keys[$i] ]["cryptfield"]."='".crypt($_REQUEST[ $keys[$i] ], dtc_makesalt())."' ";
					}
                                        break;
				case "custom_fld":
					if($added_one == "yes"){
						$vals .= ", ";
					}
					$arr_request = array();
					foreach ($_REQUEST[ $keys[$i] ] as $req_field => $req_value){
						$arr_request[] = $req_field.":".$req_value;
					}
					$txt_request = join("|",$arr_request);
					$vals .= " ".$keys[$i]."='".$txt_request."' ";
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
			if( isset($dsc["update_check_callback"]) ){
				if ( $dsc["update_check_callback"]() ){
					$q = "UPDATE ".$dsc["table_name"]." SET $vals WHERE $id_name='$id';";
				}else{
					$q = "";
				}
			}else{
				$q = "UPDATE ".$dsc["table_name"]." SET $vals WHERE $id_name='$id';";
			}
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
			$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
		}
	}

	$out = "<h3>".$dsc["title"]."</h3>";

	if( $action_error_txt != ""){
		$out .= "<font color=\"red\">" . $action_error_txt . "</font><br>";
	}

	// Display of all the titles of the table
	$out .= "<table class=\"dtcDatagrid_table_props\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">";
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
			if( isset($dsc["cols"][ $keys[$i] ]["help"]) ){
				$jshelp = " onmouseover=\"Tip('". addslashes($dsc["cols"][ $keys[$i] ]["help"]) ."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" ";
			}else{
				$jshelp = "";
			}
			if (!isset($dsc["cols"][ $keys[$i] ]["legend"])){
				$dsc["cols"][ $keys[$i] ]["legend"] = "";
			}
			$out .= "<td $jshelp class=\"dtcDatagrid_table_titles\">".$dsc["cols"][ $keys[$i] ]["legend"]."</td>";
		}
	}
	$out .= "<td class=\"dtcDatagrid_table_titles\" colspan=\"2\"><b>".$txt_action[$lang]."</b></td>";
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
		if( isset($dsc["print_where_condition"]) ){
			$where = " WHERE ".$dsc["print_where_condition"];
		}else{
			$where = "";
		}
	}
	if(isset($dsc["order_by"])){
		$order_by = " ORDER BY ".$dsc["order_by"]." ";
	}else{
		$order_by = "";
	}
	$q = "SELECT $sql_fld_list FROM ".$dsc["table_name"]." $where $order_by;";
	$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$out .= "<tr><form name=\"".$dsc["action"]."_edit_frm_$i\" action=\"?\">$fw<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_edit\">";
		if(($i % 2) == 1 && isset($gfx_form_entry_label_background)){
			$tdclass = "dtcDatagrid_table_flds_alt";
			$input_class = "dtcDatagrid_input_alt_color";
		}else{
			$tdclass = "dtcDatagrid_table_flds";
			$input_class = "dtcDatagrid_input_color";
		}
		// Get the primary key of the table raw before anything else.
		for($j=0;$j<$nbr_fld;$j++){
			if($dsc["cols"][ $keys[$j] ]["type"] == "id"){
				$id = $a[ $keys[$j] ];
			}
		}
		for($j=0;$j<$nbr_fld;$j++){
			$the_name = $keys[$j];
			$the_fld = $dsc["cols"][$the_name];
			if($the_fld["type"] ==  "double_forkey"){
				unset($dbl_forkey_link);
				$fk_found = 0;
				$the_type = $the_fld["forkey_type"];
				// Query the 1st indirection table
				$qrent = "SELECT ".$the_fld["searchkey_1st_ind"]." FROM ".$the_fld["table_1st_ind"]." WHERE ".$the_fld["fldwhere_1st_ind"]."='".$a[ $the_fld["fldwhere_1st_ind_orig"] ]."';";
				$rrent = mysql_query($qrent)or die("Cannot query $qrent in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
				$nrent = mysql_num_rows($rrent);
				if($nrent == 1){
					$arent = mysql_fetch_array($rrent);
					$cid = $arent[ $the_fld["searchkey_1st_ind"] ];
					$qcli = "SELECT ".$the_fld["display_flds_2nd_ind"]." FROM ".$the_fld["table_2nd_ind"]." WHERE ".$the_fld["fldwhere_1st_ind_orig"]."='$cid';";
					$rcli = mysql_query($qcli)or die("Cannot query $qcli in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
					$ncli = mysql_num_rows($rrent);
					if($ncli == 1){
						$acli = mysql_fetch_array($rcli);
						$db_value = $acli[ $the_fld["display_flds_2nd_ind"] ];
						$fk_found = 1;
						$fk_link_more = $cid;
						$dbl_forkey_link = $the_fld["link_start"].$cid;
					}else{
						$db_value = "Not found";
					}
				}else{
					$qnrent = "SELECT ".$the_fld["display_flds_back"]." FROM ".$the_fld["table_back"]." WHERE ".$the_fld["fldwhere_back"]."='".$a[ $the_fld["fldwhere_back_orig"] ]."';";
					$rnrent = mysql_query($qnrent)or die("Cannot query $qnrent in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
					$nnrent = mysql_num_rows($rnrent);
					if($nnrent == 1){
						$ancli = mysql_fetch_array($rnrent);
						$db_value = $ancli[ $the_fld["display_flds_back"] ];
						$fk_found = 1;
						$dbl_forkey_link = $the_fld["link_start"].$a[ $the_fld["fldwhere_back_orig"] ];
					}else{
						$db_value = "Not found";
					}
				}
			}else if($the_fld["type"] == "forkey"){
				$fk_found = 0;
				$the_type = $the_fld["forkey_type"];
				$forkey_table = $the_fld["table"];
				$forkey_other_table_fld = $the_fld["other_table_fld"];
				$forkey_other_table_key = $the_fld["other_table_key"];
				$forkey_this_table_field = $the_fld["this_table_field"];
				$qfk = "SELECT $forkey_other_table_fld AS dtcfkfld FROM $forkey_table WHERE $forkey_other_table_key='". $a[ $forkey_this_table_field ] ."';";
				$rfk = mysql_query($qfk)or die("Cannot query $qfk in ".__FILE__." line ".__LINE__." sql said: ".mysql_error()." when getting foreign key $forkey_fld value in table ".$the_fld["table"]);
				$nfk = mysql_num_rows($rfk);
				if($nfk == 1){
					// afk means array foreign key, not away from keyboard... :)
					$afk = mysql_fetch_array($rfk);
					$db_value = $afk["dtcfkfld"];
					$fk_found = 1;
				}else{
					if( isset($the_fld["bk_table"]) && isset($the_fld["bk_other_table_fld"]) && isset($the_fld["bk_other_table_key"]) && isset($the_fld["bk_this_table_field"]) ){
						$forkey_table = $the_fld["bk_table"];
						$forkey_other_table_fld = $the_fld["bk_other_table_fld"];
						$forkey_other_table_key = $the_fld["bk_other_table_key"];
						$forkey_this_table_field = $the_fld["bk_this_table_field"];
						$qfk = "SELECT $forkey_other_table_fld AS dtcfkfld FROM $forkey_table WHERE $forkey_other_table_key='". $a[ $forkey_this_table_field ] ."';";
						$rfk = mysql_query($qfk)or die("Cannot query $qfk in ".__FILE__." line ".__LINE__." sql said: ".mysql_error()." when getting foreign key $forkey_fld value in table ".$the_fld["table"]);
						$nfk = mysql_num_rows($rfk);
					}else{
						$nfk = 0;
					}
					if($nfk == 1){
						$afk = mysql_fetch_array($rfk);
						$db_value = $afk["dtcfkfld"];
						$fk_found = 2;
					}else{
						$db_value = "Foreign key N/A";
					}
				}
			}else{
				$the_type = $the_fld["type"];
				if($the_type != "hyperlink"){
					if (isset($a[ $the_name ])){
						$db_value = $a[ $the_name ];
					}else{
						$db_value = "";
					}
				}
			}
			switch($the_type){
			case "text":
				if( isset($the_fld["size"])){
					$size = " size=\"".$the_fld["size"]."\" ";
				}else{
					$size = "";
				}
				$out .= "<td class=\"$tdclass\">";
				$out .= "<input class=\"$input_class\" type=\"text\" $size name=\"".$the_name."\" value=\"".$db_value."\">";
				$out .= "</td>";
				break;
			case "password":
				if( isset($the_fld["size"])){
					$size = " size=\"".$the_fld["size"]."\" ";
				}else{
					$size = "";
				}
				$out .= "<td class=\"$tdclass\" style=\"white-space:nowrap;\">";
				$genpass = autoGeneratePassButton($dsc["action"]."_edit_frm_$i",$the_name);
				$out .= "<input class=\"$input_class\" autocomplete=\"off\" type=\"password\" $size name=\"".$the_name."\" value=\"".$db_value."\">$genpass";
				$out .= "</td>";
				break;
			case "radio":
				$out .= "<td class=\"$tdclass\">";
				$nbr_choices = sizeof( $the_fld["values"] );
				for($x=0;$x<$nbr_choices;$x++){
					if($db_value == $the_fld["values"][$x]){
						$selected = " checked ";
					}else{
						$selected = "";
					}
					$out .= " <input class=\"$input_class\" type=\"radio\" name=\"".$the_name."\" value=\"".$the_fld["values"][$x]."\" $selected> ";
					if( isset($the_fld["display_replace"][$x]) ){
						$out .= $the_fld["display_replace"][$x];
					}else{
						$out .= $the_fld["values"][$x];
					}
				}
				$out .= "</td>";
				break;
			case "checkbox":
				$out .= "<td class=\"$tdclass\">";
				if($db_value == $the_fld["values"][0]){
					$selected = " checked ";
				}else{
					$selected = "";
				}
				$out .= " <input class=\"$input_class\" type=\"checkbox\" name=\"".$the_name."\" value=\"".$the_fld["values"][0]."\" $selected> ";
				$out .= "</td>";
				break;
			case "textarea":
				if( isset($the_fld["cols"])){
					$cols = " cols=\"".$the_fld["cols"]."\" ";
				}else{
					$cols = "";
				}
				if( isset($the_fld["rows"])){
					$rows = " rows=\"".$the_fld["rows"]."\" ";
				}else{
					$rows = "";
				}
				$out .= "<td class=\"$tdclass\">";
				$out .= "<textarea $cols $rows name=\"".$the_name."\">".$db_value."</textarea>";
				$out .= "</td>";
				break;
			case "hyperlink":
				$out .= "<td class=\"$tdclass\">";
				$out .= "<a href=\"$fw_link&".$the_name."=".$id."\">";
				$out .= $the_fld["text"]."</a>";
				$out .= "</td>";
				break;
			case "info":
				$out .= "<td class=\"$tdclass\">";
				if($the_fld["type"] == "forkey" && isset($the_fld["link"]) && $fk_found == 1){
					$out .= "<a href=\"".$the_fld["link"]. $a[ $the_name ] ."\" style=\"white-space:nowrap;\">".$db_value."</a>";
				}else if($the_fld["type"] == "double_forkey" && $fk_found == 1){
					$out .= "<a href=\"".$dbl_forkey_link."\" target=\"_blank\" style=\"white-space:nowrap;\">".$db_value."</a>";
				}else{
					$out .= "<span style=\"white-space:nowrap;\">".$db_value."</span>";
				}
				$out .= "</td>";
				break;
			case "popup":
				$out .= "<td class=\"$tdclass\">";
				$out .= "<select class=\"$input_class\" name=\"".$the_name."\">";
				$nbr_values = sizeof($the_fld["values"]);
				for($x=0;$x<$nbr_values;$x++){
					if($the_fld["values"][$x] == $db_value){
						$selected = " selected ";
					}else{
						$selected = "";
					}
					if( isset($the_fld["display_replace"][$x]) ){
						$display_value_popup = $the_fld["display_replace"][$x];
					}else{
						$display_value_popup = $the_fld["values"][$x];
					}
					$out .= "<option value=\"".$the_fld["values"][$x]."\" $selected>".$display_value_popup."</option>";
				}
				$out .= "</select>";
				$out .= "</td>";
				break;
			case "id":
				$id_hidden = "<input type=\"hidden\" name=\"".$the_name."\" value=\"".$db_value."\">";
				if($dsc["cols"][ $the_name ]["display"] == "yes"){
					$out .= "<td class=\"$tdclass\">";
					$out .= $id_hidden;
					$out .= $db_value;
					$out .= "</td>";
				}else{
					$out .= $id_hidden;
				}
				break;
			case "custom_fld":
				$out .= "<td class=\"$tdclass\">";
				
				// Manage the output of custom fields.
				$customer_custom_fields = array();
				$explo_row = explode("|", htmlspecialchars(stripcslashes($db_value)));
				$n_custf = sizeof($explo_row);
				for($ix=0;$ix<$n_custf;$ix++){
					$one_fld = $explo_row[$ix];
					$pos_data = strpos($one_fld,":");
					if($pos_data === false){
						continue;
					}
					$varname = substr($one_fld,0,$pos_data);
					$value = substr($one_fld,$pos_data+1);
					$customer_custom_fields[ $varname ] = $value;
				}
				$qa = "SELECT ".$the_fld["main_table"].".* FROM ".$the_fld["main_table"]." INNER JOIN ".$the_fld["second_table"]." ON ".$the_fld["main_join_clause"]." INNER JOIN ".$the_fld["third_table"]." ON ".$the_fld["second_join_clause"]." WHERE ".$the_fld["where_field"]." = $id ORDER BY ".$the_fld["order_field"].";";
				//echo " | ".$qa." ";
				$ra = mysql_query($qa)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$na = mysql_num_rows($ra);
				$init_alt = 1;

				for($iz=0;$iz<$na;$iz++){
					$aa = mysql_fetch_array($ra);
					if(isset($customer_custom_fields[ $aa["varname"] ])){
						$val = $customer_custom_fields[ $aa["varname"] ];
					}else{
						$val = "";
					}
					switch($aa["widgettype"]){
					case "radio":
						$explo_popup = explode("|",$aa["widgetvalues"]);
						$explo_popup2 = explode("|",$aa["widgetdisplay"]);
						$n_val = sizeof($explo_popup);
						$widget = "";
						for($jz=0;$jz<$n_val;$jz++){
							if($val == $the_name[$explo_popup[$jz]]){
								$sel = " checked ";
							}else{
								$sel = " ";
							}
							$widget .= "<input type=\"radio\" name=\"".$the_name."[".$aa["varname"]."]\" value=\"".$explo_popup[$jz]."\" $sel > ".$explo_popup2[$jz];
						}
						break;
					case "popup":
						$explo_popup = explode("|",$aa["widgetvalues"]);
						$explo_popup2 = explode("|",$aa["widgetdisplay"]);
						$n_val = sizeof($explo_popup);
						$widget = "<select name=\"".$the_name."[".$aa["varname"]."]\">";
						for($jz=0;$jz<$n_val;$jz++){
							if($val == $the_name[$explo_popup[$jz]]){
								$sel = " selected ";
							}else{
								$sel = " ";
							}
							$widget .= "<option value=\"".$explo_popup[$jz]."\" $sel >".$explo_popup2[$jz]."</option>";
						}
						$widget .= "</select>";
						break;
					case "textarea":
						$widget = "<textarea name=\"".$the_name."[".$aa["varname"]."]\">".htmlspecialchars($val)."</textarea>";
						break;
					case "text":
					default:
						$widget = "<input size=\"40\" type=\"text\" name=\"".$the_name."[".$aa["varname"]."]\" value=\"".htmlspecialchars($val)."\">";
						break;
					}
					$out .= $aa["question"].": ".$widget; //,$init_alt);
				}

				$out .= "</td>";
				break;
			}
		}
		$out .= "<td class=\"$tdclass\">".dtcApplyButton()."</form></td>";
		if(isset($dsc["skip_deletion"]) && $dsc["skip_deletion"] == "yes"){
			$out .= "<td class=\"$tdclass\">&nbsp;</td></tr>";
		}else{
			$out .= "<td class=\"$tdclass\"><form action=\"?\">$fw$id_hidden
			<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_delete\">".dtcDeleteButton()."</form></td>";
			$out .= "</form></tr>";
		}
	}
	if(!isset($dsc["skip_creation"]) || $dsc["skip_deletion"] != "yes"){
		if(($i % 2) == 1 && isset($gfx_form_entry_label_background)){
			$tdclass = "dtcDatagrid_table_flds_alt";
			$input_class = "dtcDatagrid_input_alt_color";
		}else{
			$tdclass = "dtcDatagrid_table_flds";
			$input_class = "dtcDatagrid_input_color";
		}
		// Write the NEW stuff...
		$out .= "<tr><form name=\"".$dsc["action"]."_new_frm\" id=\"".$dsc["action"]."_new_frm\" action=\"?\">$fw<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_new\">";
		for($j=0;$j<$nbr_fld;$j++){
			$the_fld = $dsc["cols"][ $keys[$j] ];
			switch($the_fld["type"]){
			case "text":
				if( isset($dsc["cols"][ $keys[$j] ]["size"])){
					$size = " size=\"".$dsc["cols"][ $keys[$j] ]["size"]."\" ";
				}else{
					$size = "";
				}
				$out .= "<td class=\"$tdclass\">";
				$out .= "<input class=\"$input_class\" type=\"text\" $size name=\"".$keys[$j]."\" value=\"\">";
				$out .= "</td>";
				break;
			case "password":
				if( isset($dsc["cols"][ $keys[$j] ]["size"])){
					$size = " size=\"".$dsc["cols"][ $keys[$j] ]["size"]."\" ";
				}else{
					$size = "";
				}
				$out .= "<td class=\"$tdclass\" style=\"white-space:nowrap;\">";
				$genpass = autoGeneratePassButton($dsc["action"]."_new_frm",$keys[$j]);
				$out .= "<input class=\"$input_class\" autocomplete=\"off\" type=\"password\" $size name=\"".$keys[$j]."\" value=\"\">$genpass";
				$out .= "</td>";
				break;
			case "radio":
				$out .= "<td class=\"$tdclass\">";
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
					$out .= "<input class=\"$input_class\" type=\"radio\" name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected> ";
					if( isset($dsc["cols"][ $keys[$j] ]["display_replace"][$x]) ){
						$out .= $dsc["cols"][ $keys[$j] ]["display_replace"][$x];
					}else{
						$out .= $dsc["cols"][ $keys[$j] ]["values"][$x];
					}
				}
				$out .= "</td>";
				break;
			case "checkbox":
				$out .= "<td class=\"$tdclass\">";
				$out .= " <input class=\"$input_class\" type=\"checkbox\" name=\"".$keys[$j]."\" value=\"".$dsc["cols"][ $keys[$j] ]["values"][0]."\" selected> ";
				$out .= "</td>";
				break;
			case "textarea":
				break;
			case "hyperlink":
				$out .= "<td class=\"$tdclass\">&nbsp;</td>";
				break;
			case "popup":
				$out .= "<td class=\"$tdclass\">";
				$out .= "<select class=\"$input_class\" name=\"".$keys[$j]."\">";
				$nbr_values = sizeof($dsc["cols"][ $keys[$j] ]["values"]);
				for($x=0;$x<$nbr_values;$x++){
					if( isset($dsc["cols"][ $keys[$j] ]["display_replace"][$x]) ){
						$display_value_popup = $dsc["cols"][ $keys[$j] ]["display_replace"][$x];
					}else{
						$display_value_popup = $dsc["cols"][ $keys[$j] ]["values"][$x];
					}
					$out .= "<option value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\">".$display_value_popup."</option>";
				}
				$out .= "</select>";
				$out .= "</td>";
				break;
			case "id":
//				$id = $a[ $keys[$j] ];
				if($dsc["cols"][ $keys[$j] ]["display"] == "yes"){
					$out .= "<td class=\"tdclass\">";
					$out .= "<input type=\"hidden\" name=\"".$keys[$j]."\" value=\"\">";
//					$out .= $a[ $keys[$j] ];
					$out .= "</td>";
				}else{
					$out .= "<input type=\"hidden\" name=\"".$keys[$j]."\" value=\"\">";
				}
				break;
			}
		}
		$out .= "<td class=\"$tdclass\" colspan=\"2\">".dtcAddButton()."</form></td></tr>";
	}
	$out .= "</table>";
	return $out;
}

// Properties of the $dsc parameter for this function:
// title => $txt_subdom_list[$lang] -> Main title to display for this list
// new_item_title => $txt_subdom_create[$lang] -> Title to display when a new item is in edition
// new_item_link => $txt_subdom_new[$lang] -> Text for the link for new items
// edit_item_title => $txt_subdom_edit_one[$lang] -> Text to display when an existing item is in edition
// table_name => $pro_mysql_subdomain_table -> Name of the SQL table involved in this editor
// action => "subdomain_editor" -> Prefix of the hidden action of all forms and the form name prefix as well
// forward => array("adm_login","adm_pass","addrlink") -> List of variables to forward in hidden <input>
// id_fld => "id" -> Column to use as a autoincrement_id in the SQL
// list_fld_show => "subdomain_name" -> Column holding the variable to display in the list of items
// [max_item] => $max_subdomain, -> Max number of items to display in this list, if number is bigger, creation not allowed
// [num_item_txt] => $txt_number_of_active_subdomains[$lang] -> Text to display for the number of items
// [create_item_callback] => "subdomainCreateDirsCallBack", -> Name of the callback function called after a creation of an item
// [delete_item_callback] => "subdomainDeleteDirsCallBack" -> Name of the callback function called after a deletion of an item
// [where_list] => array("domain_name" => $domain["name"]) -> List of field => value to set in all the WHERE of all SQL (listing and submit)
// cols => "field1"
//         "field2"
//         ...
// Properties of each fields:
// "type" => "id" -> This is the auto_increment ID of the table
// "display" => "no" -> Display it or not in the list (currently only "no" is working, and is used only when type=id)
// "legend" => "id" -> Text to display in the left side of the field
//
// "type" => "text" -> The HTML control is a TEXT field
// "type" => "readonly" -> The HTML control is a READONLY TEXT field
// "type" => "password" -> The HTML control is a PASSWORD field with the random pass generation button
// Both types text, password and readonly understands the following (not mandatory) fields:
//   [disable_edit] => "yes" -> Can be used to disable the edition of a field in the edit mode (will still be editable in creation of items)
//   [hide_create] => "yes"  -> Can be used to hide a field in creation mode, it will only be displayed in edit mode 
//   [check] => "subdomain", -> Check against the format of the field content before allowing creation or edition
//                            Currently can have the follwing values: subdomain, subdomain_or_ip, domain_or_ip, dtc_login, dtc_pass
//   [can_be_empty] => "yes" -> Allow the field to be empty even with a check value
//   [empty_makes_sql_null] => "yes" -> Makes a SQL query with NULL as parametter wheneger a field is empty
//   [callback] -> this routine will be called to get further data it will need to display. The id (autoinc) will be passed to the routine and it is expected that an array will be returned: 
// 	array(
//		"value" => $value,
//		"happen" => $happen);

// "type" => "radio" -> The control is a radio button
// "type" => "checkbox" -> The control is a checkbox. Checkboxes have only 2 values (the possible ones in the db) and is not implemented yet
// "values" => array("yes","no") -> Values of the radio buttons (in display order)
//   [default] => "no" -> default value of the radio button for creation of an item

// "type" => "popup" -> The control is a popup
// "values" => array("yes","no") -> Values of the popup (in display order)
// [display_replace] => array(... -> Values to replace in the display (can be smaller than the values array)
// "max_value_positive" => INT -> Check against an INT max value for a field

// This function is to be used for the user panel, it has field content check & validations plus addslashes

function dtcListItemsEdit($dsc){
	global $adm_pass;

	$out = "<h3>".$dsc["title"]."</u></b></h3>";

	// Calculate the forwards parameters for links and forms
	$nbr_forwards = sizeof($dsc["forward"]);
	$keys_fw = array_keys($dsc["forward"]);

	$fw = "";
	$fw_link = "?";
	for($i=0;$i<$nbr_forwards;$i++){
		if($dsc["forward"][$i] == "adm_pass"){
			$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$adm_pass."\">";
		}else{
			$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$_REQUEST[ $dsc["forward"][$i] ]."\">";
		}
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
	if( isset($dsc["order_by"])){
		$order_by = " ORDER BY ". $dsc["order_by"];
	}else{
		$order_by = "";
	}
	$added_insert_names = "";
	$added_insert_values = "";
	if(isset($dsc["where_list"])){
		$nbr_where = sizeof($dsc["where_list"]);
		$where_keys = array_keys($dsc["where_list"]);
		for($i=0;$i<$nbr_where;$i++){
			if($i != 0){
				$added_insert_names .= ",";
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
			case "popup":
			case "radio":
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
			if( isset($dsc["cols"][ $keys[$i] ]["check"])){
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
				case "list_of_ip":
					if( isset($dsc["cols"][ $keys[$i] ]["can_be_empty"]) &&
						$dsc["cols"][ $keys[$i] ]["can_be_empty"] == "yes" &&
						$_REQUEST[ $keys[$i] ] == ""){
						break;
					}
					$mylist_of_ip = explode("|",$_REQUEST[ $keys[$i] ]);
					$number_of_ip = sizeof($mylist_of_ip);
					for($g=0;$g<$number_of_ip;$g++){
						if( !isIP($mylist_of_ip[$g]) ){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": invalid IP list<br>";
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
				case "ip6":
					if( !isIP6($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							if ( !isset($dsc["cols"][ $keys[$i] ]["empty_makes_default"])
										|| $dsc["cols"][ $keys[$i] ]["empty_makes_default"] != "yes"
										|| $_REQUEST[ $keys[$i] ] != "default"){
								$commit_flag = "no";
								$commit_err .= $keys[$i].": not an IPv6 address<br>";
							}
						}
					}
					break;
				case "page_url":
					if( !isPageURL($_REQUEST[ $keys[$i] ]) ){
						$commit_flag = "no";
						$commit_err .= $keys[$i]._(": not a page URL")."<br>";
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
				case "dtc_login_or_email":
					if( !isFtpLogin($_REQUEST[ $keys[$i] ]) && !isValidEmail($_REQUEST[ $keys[$i] ])){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct login format.<br>";
						}
					}
					break;
				case "mail_alias_group":
					$mail_alias_group_raw = trim($_REQUEST[ $keys[$i] ],"\r\n");
					$mail_alias_nocr = str_replace("\r", "", $mail_alias_group_raw);
					$mail_alias_array = preg_split("/\n/", $mail_alias_nocr);
					for($x=0;$x<count($mail_alias_array);$x++)
					{
						if ( ! isValidEmail($mail_alias_array[$x]) )
						{
							$commit_flag = "no";
							$commit_err .= $mail_alias_array[$x].": not a valid email format.<br>";
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
					if( FALSE != check_password($_REQUEST[ $keys[$i] ])){
						$commit_flag = "no";
						$commit_err .= $keys[$i].": "._("This password is one of the 500 most used passwords on the internet. Please choose a stronger one.");
					}
					break;
				case "email":
					if( !isValidEmail($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct email format<br>";
						}
					}
					break;
				case "number":
					if( !isRandomNum($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct number format<br>";
						}
					}
					break;
				case "max_value_2096":
					if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
								|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
								|| $_REQUEST[ $keys[$i] ] != ""){
						if( !isRandomNum($_REQUEST[ $keys[$i] ]) ){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": not a correct number format<br>";
						}
						if($_REQUEST[ $keys[$i] ] >= 2096){
							$commit_flag = "no";
							$commit_err .= $keys[$i].": is greater or equal than the max value 2096<br>";
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
		if(isset($dsc["check_unique"])){
			$nbr_unique_check = sizeof($dsc["check_unique"]);
			$where_clause = "";
			for($i=0;$i<$nbr_unique_check;$i++){
				if($i != 0){
					$where_clause .= " AND ";
				}
				if( isset ($dsc["cols"][ $dsc["check_unique"][$i] ]["happen_domain"]) ){
					$where_clause .= $dsc["check_unique"][$i] . "='".$_REQUEST[ $dsc["check_unique"][$i] ]  .  $dsc["cols"][ $dsc["check_unique"][$i] ]["happen_domain"]."' ";
				}else{
					$where_clause .= $dsc["check_unique"][$i] . "='".$_REQUEST[ $dsc["check_unique"][$i] ]."' ";
				}
			}
			if( !isset($dsc["check_unique_use_where_list"]) || $dsc["check_unique_use_where_list"] == "yes"){
				$nbr_where_list_fld = sizeof($dsc["where_list"]);
				$where_list_keys_fld = array_keys($dsc["where_list"]);
				for($i=0;$i<$nbr_where_list_fld;$i++){
					$where_clause .= " AND ".$where_list_keys_fld[$i]."='".$dsc["where_list"][ $where_list_keys_fld[$i] ]."'";
				}
			}
			$q = "SELECT * FROM ".$dsc["table_name"]." WHERE $where_clause ";
			$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n > 0){
				$commit_flag = "no";
				$commit_err = $dsc["check_unique_msg"];
			}
		}
		// Build the request
		$fld_names = "";
		$values = "";
		$added_one = "no";
		for($i=0;$i<$nbr_fld;$i++){
			switch($dsc["cols"][ $keys[$i] ]["type"]){
			case "password":
				if($added_one == "yes"){
					$fld_names .= ",";
					$values .= ",";
				}
				$fld_names .= $keys[$i];
				if( isset($dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"]) && $dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"] == "yes" && $_REQUEST[ $keys[$i] ] == ""){
					$values .= "NULL";
				}else if( isset($dsc["cols"][ $keys[$i] ]["empty_makes_default"]) && $dsc["cols"][ $keys[$i] ]["empty_makes_default"] == "yes" && $_REQUEST[ $keys[$i] ] == ""){
					$values .= "'default'";
				}else{
					if(isset($dsc["cols"][ $keys[$i] ]["happen_domain"])){
						$values .= "'".mysql_real_escape_string($_REQUEST[ $keys[$i] ]).$dsc["cols"][ $keys[$i] ]["happen_domain"]."'";
					}else{
						$values .= "'".mysql_real_escape_string($_REQUEST[ $keys[$i] ])."'";
					}
					// if the crypt field is set, then we use this as the SQL field to populate the crypted password into
					if(isset($dsc["cols"][ $keys[$i] ]["cryptfield"])){
						if($added_one == "yes"){
							$fld_names .= ",";
							$values .= ",";
						}
						$fld_names .= $dsc["cols"][ $keys[$i] ]["cryptfield"];
						$values .= "'".crypt($_REQUEST[ $keys[$i] ], dtc_makesalt())."'";
					}
				}
				$added_one = "yes";
				break;
			case "text":
			case "textarea":
				if($added_one == "yes"){
					$fld_names .= ",";
					$values .= ",";
				}
				$fld_names .= $keys[$i];
				if( isset($dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"]) && $dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"] == "yes" && $_REQUEST[ $keys[$i] ] == ""){
					$values .= "NULL";
				}else if( isset($dsc["cols"][ $keys[$i] ]["empty_makes_default"]) && $dsc["cols"][ $keys[$i] ]["empty_makes_default"] == "yes" && $_REQUEST[ $keys[$i] ] == ""){
					$values .= "'default'";
				}else{
					if(isset($dsc["cols"][ $keys[$i] ]["happen_domain"])){
						$values .= "'".mysql_real_escape_string($_REQUEST[ $keys[$i] ]).$dsc["cols"][ $keys[$i] ]["happen_domain"]."'";
					}else{
						$values .= "'".mysql_real_escape_string($_REQUEST[ $keys[$i] ])."'";
					}
				}
				$added_one = "yes";
				break;
			case "checkbox":
				if($added_one == "yes"){
					$fld_names .= ",";
					$values .= ",";
				}
				$added_one = "yes";
				$fld_names .= $keys[$i];
				if (isset($_REQUEST[ $keys[$i] ])) {
					$values .= "'".$dsc["cols"][ $keys[$i] ]["values"][0]."'";
				}else{
					$values .= "'".$dsc["cols"][ $keys[$i] ]["values"][1]."'";
				}
				break;
			case "popup":
			case "radio":
				if($added_one == "yes"){
					$fld_names .= ",";
					$values .= ",";
				}
				$fld_names .= $keys[$i];
				$values .= "'".mysql_real_escape_string($_REQUEST[ $keys[$i] ])."'";
				$added_one = "yes";
				break;
			}
		}
		if($commit_flag == "yes"){
			$q = "INSERT INTO ".$dsc["table_name"]." ($added_insert_names $fld_names) VALUES ($added_insert_values $values);";
			$success = "yes";
			$r = mysql_query($q)or $success = "no";
			if($success == "yes"){
				$insert_id = mysql_insert_id();
				if( isset($dsc["create_item_callback"]) ){
					$out .= $dsc["create_item_callback"]($insert_id);
				}
			}else{
				$out .= "<font color=\"red\">Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error()."</font>";
			}
		}else{
			$out .= "<font color=\"red\">"._("Could not commit the changes because of an error in field format: ")."<br>$commit_err</font><br>";
		}
	}else if( isset($_REQUEST["action"]) && $_REQUEST["action"] == $dsc["action"]."_save_item" ){
		// Todo: do the fields checkings
		$commit_flag = "yes";
		$commit_err = "";
		for($i=0;$i<$nbr_fld;$i++){
			switch($dsc["cols"][ $keys[$i] ]["type"]){
			case "checkbox":
				break;
			case "popup":
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
					$commit_err = "the variable ".$keys[$i]._(" is not one of the allowed values")."<br>";
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
							$commit_err .= $keys[$i]._(": not a subdomain")."<br>";
						}
					}
					break;
				case "list_of_ip":
					if( isset($dsc["cols"][ $keys[$i] ]["can_be_empty"]) &&
						$dsc["cols"][ $keys[$i] ]["can_be_empty"] == "yes" &&
						$_REQUEST[ $keys[$i] ] == ""){
						break;
					}
					$mylist_of_ip = explode("|",$_REQUEST[ $keys[$i] ]);
					$number_of_ip = sizeof($mylist_of_ip);
					for($g=0;$g<$number_of_ip;$g++){
						if( !isIP($mylist_of_ip[$g]) ){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": invalid IP list")."<br>";
						}
					}
					break;
				case "subdomain_or_ip":
					if( !checkSubdomainFormat($_REQUEST[ $keys[$i] ]) && !isIP($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a subdomain or IP addresse")."<br>";
						}
					}
					break;
				case "ip6":
					if( !isIP6($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							if ( !isset($dsc["cols"][ $keys[$i] ]["empty_makes_default"])
										|| $dsc["cols"][ $keys[$i] ]["empty_makes_default"] != "yes"
										|| $_REQUEST[ $keys[$i] ] != "default"){
								$commit_flag = "no";
								$commit_err .= $keys[$i]._(": not an IPv6 address")."<br>";
							}
						}
					}
					break;
				case "page_url":
					if( !isPageURL($_REQUEST[ $keys[$i] ]) ){
						$commit_flag = "no";
						$commit_err .= $keys[$i]._(": not a page URL")."<br>";
					}
					break;
				case "ip_addr":
					if( !isIP($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not an IP address")."<br>";
						}
					}
					break;
				case "domain_or_ip":
					if( !isIP($_REQUEST[ $keys[$i] ]) && !isHostname($_REQUEST[ $keys[$i] ])){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a domain or IP addresse")."<br>";
						}
					}
					break;
				case "dtc_login":
					if( !isFtpLogin($_REQUEST[ $keys[$i] ])){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a correct login format")."<br>";
						}
					}
					break;
				case "dtc_login_or_email":
					if( !isFtpLogin($_REQUEST[ $keys[$i] ])  && !isValidEmail($_REQUEST[ $keys[$i] ])){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a correct login format")."<br>";
						}
					}
					break;
				case "mail_alias_group":
					$mail_alias_group_raw = trim($_REQUEST[ $keys[$i] ],"\r\n");
					$mail_alias_nocr = str_replace("\r", "", $mail_alias_group_raw);
					$mail_alias_array = preg_split("/\n/", $mail_alias_nocr);
					for($x=0;$x<count($mail_alias_array);$x++)
					{
						if ( ! isValidEmail($mail_alias_array[$x]) )
						{
							$commit_flag = "no";
							$commit_err .= $mail_alias_array[$x]._(": not a valid email format")."<br>";
						}
					}
					break;
				case "dtc_pass":
					if( !isDTCPassword($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a correct password format")."<br>";
						}
					}
					if( FALSE != check_password($_REQUEST[ $keys[$i] ]) ){
						$commit_flag = "no";
						$commit_err .= $keys[$i].": "._("This password is one of the 500 most used passwords on the internet. Please choose a stronger one.");
					}
					break;
				case "email":
					if( !isValidEmail($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a correct email format")."<br>";
						}
					}
					break;
				case "number":
					if( !isRandomNum($_REQUEST[ $keys[$i] ]) ){
						if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
									|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
									|| $_REQUEST[ $keys[$i] ] != ""){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a correct number format")."<br>";
						}
					}
					break;
				case "max_value_2096":
					if( !isset($dsc["cols"][ $keys[$i] ]["can_be_empty"])
								|| $dsc["cols"][ $keys[$i] ]["can_be_empty"] != "yes"
								|| $_REQUEST[ $keys[$i] ] != ""){
						if( !isRandomNum($_REQUEST[ $keys[$i] ]) ){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": not a correct number format")."<br>";
						}
						if($_REQUEST[ $keys[$i] ] >= 2096){
							$commit_flag = "no";
							$commit_err .= $keys[$i]._(": is greater or equal than the max value 2096")."<br>";
						}
					}
					break;
				default:
					$commit_flag = "no";
					$commit_err .= $keys[$i]._(": unknown field checking type")." (".$dsc["cols"][ $keys[$i] ]["check"].").<br>";
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
				$id_fld_value = mysql_real_escape_string($_REQUEST[ $keys[$i] ]);
				break;
			case "readonly":
				break;
			case "text":
			case "textarea":
			case "password":
				if( !isset($dsc["cols"][ $keys[$i] ]["disable_edit"]) || $dsc["cols"][ $keys[$i] ]["disable_edit"] != "yes"){
					if($added_one == "yes"){
						$reqs .= ",";
					}
					if( isset($dsc["cols"][ $keys[$i] ]["happen_domain"])){
						$happen = $dsc["cols"][ $keys[$i] ]["happen_domain"];
					}else{
						$happen = "";
					}
					if( isset($dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"]) && $dsc["cols"][ $keys[$i] ]["empty_makes_sql_null"] == "yes" && $_REQUEST[ $keys[$i] ] == ""){
						$reqs .= $keys[$i]."=NULL";
					}else if( isset($dsc["cols"][ $keys[$i] ]["empty_makes_default"]) && $dsc["cols"][ $keys[$i] ]["empty_makes_default"] == "yes" && $_REQUEST[ $keys[$i] ] == ""){
						$reqs .= $keys[$i]."='default'";
					}else{
						$reqs .= $keys[$i]."='".mysql_real_escape_string($_REQUEST[ $keys[$i] ]).$happen."'";
						// if the crypt field is set, then we use this as the SQL field to populate the crypted password into
						if(isset($dsc["cols"][ $keys[$i] ]["cryptfield"])){
							if($added_one == "yes"){
								$reqs .= ", ";
							}
							$reqs .= " ".$dsc["cols"][ $keys[$i] ]["cryptfield"]."='".crypt($_REQUEST[ $keys[$i] ], dtc_makesalt())."' ";
						}
					}
					$added_one = "yes";
				}
				break;
			case "popup":
			case "radio":
				if($added_one == "yes"){
					$reqs .= ",";
				}
				$reqs .= $keys[$i]."='".mysql_real_escape_string($_REQUEST[ $keys[$i] ])."'";
				$added_one = "yes";
				break;
			case "checkbox":
				if($added_one == "yes"){
					$reqs .= ",";
				}
				if( isset($_REQUEST[ $keys[$i] ])){
					$reqs .= $keys[$i]."='".$dsc["cols"][ $keys[$i] ]["values"][0]."'";
				}else{
					$reqs .= $keys[$i]."='".$dsc["cols"][ $keys[$i] ]["values"][1]."'";
				}
				break;
			default:
				die($dsc["cols"][ $keys[$i] ]["type"].": Not implemented yet line ".__LINE__." file ".__FILE__);
				break;
			}
		}
		if($commit_flag != "yes"){
			$out .= "<font color=\"red\">Could not commit the changes because of an error in field format: [todo: error desc]<br>$commit_err</font>";
		}else if(!isset($id_fldname) || !isset($id_fld_value)){
			$out .= "<font color=\"red\">Could not commit the changes because the id is not set!</font>";
		}else{
			$q = "UPDATE ".$dsc["table_name"]." SET $reqs $where AND $id_fldname='$id_fld_value';";
			$r = mysql_query($q)or $out .= "<font color=\"red\">Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error()."</font>";
			if(isset($dsc["edit_item_callback"])){
				$dsc["edit_item_callback"]($id_fld_value);
			}
		}
	}else if( isset($_REQUEST["action"]) && $_REQUEST["action"] == $dsc["action"]."_delete_item" ){
		for($i=0;$i<$nbr_fld;$i++){
			if($dsc["cols"][ $keys[$i] ]["type"] == "id"){
				$id_fldname = $keys[$i];
				$id_fld_value = mysql_real_escape_string($_REQUEST[ $keys[$i] ]);
			}
		}
		if( isset($id_fldname) && isset($id_fld_value) ){
			if( isset($dsc["delete_item_callback"]) ){
				$dsc["delete_item_callback"]($id_fld_value);
			}
			$q = "DELETE FROM ".$dsc["table_name"]." $where AND $id_fldname='".$id_fld_value."';";
			$r = mysql_query($q)or $out .= "<font color=\"red\">Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error()."</font>";
		}else{
			$out .= "<font color=\"red\">Could not commit the deletion because the id field could not be found.</font>";
		}
	}

	// We have to query it again, in case an insert or a delete has occured!
	$q = "SELECT ".$dsc["id_fld"].",".$dsc["list_fld_show"]." FROM ".$dsc["table_name"]." $where $order_by;";
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
		$out .= "<h3>".$dsc["new_item_title"]."</h3><br>";
		if(isset($dsc["max_item"]) && $current_num_items >= $dsc["max_item"]){
			$out .= "<font color=\"red\">". _("Maximum number reached") ."!</font><br>";
		}else{
			$out .= "<form name=\"".$dsc["action"]."_new_item_frm\" action=\"?\">$fw
				<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_new_item\">".dtcFormTableAttrs();
			for($i=0;$i<$nbr_fld;$i++){
				if( isset($dsc["cols"][ $keys[$i] ]["help"])){
					$help = $dsc["cols"][ $keys[$i] ]["help"];
				}else{
					$help = "";
				}
				switch($dsc["cols"][ $keys[$i] ]["type"]){
				case "id":
					$out .= "<input type=\"hidden\" name=\"".$keys[$i]."\" value=\"\">";
					break;
				case "password":
					$genpass = autoGeneratePassButton($dsc["action"]."_new_item_frm",$keys[$i]);
					$ctrl = "<input type=\"password\" autocomplete=\"off\" name=\"".$keys[$i]."\" value=\"\">$genpass";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl,$i%2,$help);
					break;
				case "text":
				case "readonly":
					if (isset($dsc["cols"][ $keys[$i] ]["hide_create"]) && $dsc["cols"][ $keys[$i] ]["hide_create"]=="yes")
					{ break; }

					if( isset($dsc["cols"][ $keys[$i] ]["happen_domain"]) ){
						$happen = $dsc["cols"][ $keys[$i] ]["happen_domain"];
					}else{
						$happen = "";
					}
					if( isset($dsc["cols"][ $keys[$i] ]["happen"]) ){
						$happen .= $dsc["cols"][ $keys[$i] ]["happen"];
					}
					if( isset($dsc["cols"][ $keys[$i] ]["default"]) ){
						$ctrl_value = $dsc["cols"][ $keys[$i] ]["default"];
					}else{
						$ctrl_value = "";
					}
					if ($dsc["cols"][ $keys[$i] ]["type"]=="readonly")
					{
						$ctrl = "<input type=\"text\" name=\"".$keys[$i]."\" value=\"$ctrl_value\" READONLY>$happen";
					}
					else
					{
						$ctrl = "<input type=\"text\" name=\"".$keys[$i]."\" value=\"$ctrl_value\">$happen";
					}
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl,$i%2,$help);
					break;
				case "textarea":
					if( isset($dsc["cols"][ $keys[$i] ]["cols"]) ){
						$ctrl_cols = " cols=\"".$dsc["cols"][ $keys[$i] ]["cols"]."\" ";
					}else{
						$ctrl_cols = "";
					}
					if( isset($dsc["cols"][ $keys[$i] ]["rows"]) ){
						$ctrl_rows = " rows=\"".$dsc["cols"][ $keys[$i] ]["rows"]."\" ";
					}else{
						$ctrl_rows = "";
					}
					$ctrl = "<textarea $ctrl_cols $ctrl_rows name=\"".$keys[$i]."\"></textarea>";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl,$i%2,$help);
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
						if( isset($dsc["cols"][ $keys[$i] ]["display_replace"][$x]) ){
							$display_val = $dsc["cols"][ $keys[$i] ]["display_replace"][$x];
						}else{
							$display_val = $dsc["cols"][ $keys[$i] ]["values"][$x];
						}
						$ctrl .= "<input type=\"radio\" name=\"".$keys[$i]."\" value=\"".$dsc["cols"][ $keys[$i] ]["values"][$x]."\" $selected> ";
						$ctrl .= $display_val;
					}
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl,$i%2,$help);
					break;
				case "checkbox":
					if( !isset($dsc["cols"][ $keys[$i] ]["default"]) ){
						$checked = " checked ";
					}else{
						$checked = " ";
					}
					$ctrl = "<input type=\"checkbox\" name=\"".$keys[$i]."\" value=\"yes\" $checked>";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl,$i%2,$help);
					break;
				case "popup":
					$nbr_choices = sizeof( $dsc["cols"][ $keys[$i] ]["values"] );
					$ctrl = "<select name=\"".$keys[$i]."\">";
					for($x=0;$x<$nbr_choices;$x++){
						$selected = "";
						if( isset($dsc["cols"][ $keys[$i] ]["default"]) ){
							if($dsc["cols"][ $keys[$i] ]["values"][$x] == $dsc["cols"][ $keys[$i] ]["default"]){
								$selected = " selected ";
							}else{
								$selected = "";
							}
						}
						if( isset($dsc["cols"][ $keys[$i] ]["display_replace"][$x]) ){
							$display_val = $dsc["cols"][ $keys[$i] ]["display_replace"][$x];
						}else{
							$display_val = $dsc["cols"][ $keys[$i] ]["values"][$x];
						}
						$ctrl .= " <option value=\"".$dsc["cols"][ $keys[$i] ]["values"][$x]."\" $selected>$display_val</option>";
					}
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl,$i%2,$help);
					break;
				default:
					$ctrl = "Not implemented yet!!!";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$ctrl,$i%2,$help);
					break;
				}
			}
			$out .= dtcFromOkDraw();
			$out .= "</table></form>";
		}
	// Edition of existing items
	}else{
		$out .= "<a href=\"$fw_link&subaction=".$dsc["action"]."_new_item\">".$dsc["new_item_link"]."</a><br><br>";
		$out .= "<h3>".$dsc["edit_item_title"]."</h3><br>";
		$q = "SELECT * FROM ".$dsc["table_name"]." $where AND ".$dsc["id_fld"]."='".mysql_real_escape_string($_REQUEST["item"])."';";
		$r = mysql_query($q)or die("Cannot query $q in ".__FILE__." line ".__LINE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 1){
			$a = mysql_fetch_array($r);
			$out .= "<form name=\"".$dsc["action"]."_save_item_frm\" action=\"?\">$fw";
			$out .= "<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_save_item\">";
			$out .= "<input type=\"hidden\" name=\"subaction\" value=\"".$dsc["action"]."_edit_item\">";
			$out .= "<input type=\"hidden\" name=\"item\" value=\"".$a[ $dsc["id_fld"] ]."\">";
			$out .= dtcFormTableAttrs();
			for($j=0;$j<$nbr_fld;$j++){
				$the_fld = $dsc["cols"][ $keys[$j] ];
				if( isset($dsc["cols"][ $keys[$j] ]["help"])){
                                	$help = $dsc["cols"][ $keys[$j] ]["help"];
				}else{
					$help = "";
				}
				switch($the_fld["type"]){
				case "id":
					$out .= "<input type=\"hidden\" name=\"".$keys[$j]."\" value=\"".$a[ $keys[$j] ]."\">";
					$id_fldname = $keys[$j];
					$id_fld_value = $a[ $keys[$j] ];
					break;
				case "textarea":
					if( isset($dsc["cols"][ $keys[$j] ]["cols"]) ){
						$ctrl_cols = " cols=\"".$dsc["cols"][ $keys[$j] ]["cols"]."\" ";
					}else{
						$ctrl_cols = "";
					}
					if( isset($dsc["cols"][ $keys[$j] ]["rows"]) ){
						$ctrl_rows = " rows=\"".$dsc["cols"][ $keys[$j] ]["rows"]."\" ";
					}else{
						$ctrl_rows = "";
					}
					$ctrl = "<textarea $ctrl_cols $ctrl_rows name=\"".$keys[$j]."\">".stripslashes($a[ $keys[$j] ])."</textarea>";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl,$j%2,$help);
					break;
				case "password":
				case "text":
				case "readonly":
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
					if( isset($dsc["cols"][ $keys[$j] ]["happen_domain"]) && preg_match("/".$dsc["cols"][ $keys[$j] ]["happen_domain"]."\$/",$a[ $keys[$j] ])){
						$input_disp_value = substr($a[ $keys[$j] ],0,strlen($a[ $keys[$j] ]) - strlen($dsc["cols"][ $keys[$j] ]["happen_domain"]));
						$happen = $dsc["cols"][ $keys[$j] ]["happen_domain"];
					}else{
						if ($dsc["cols"][ $keys[$j] ]["type"]!="readonly")
						{
							$input_disp_value = $a[ $keys[$j] ];
						}
						$happen = "";
					}
					if( isset($dsc["cols"][ $keys[$j] ]["happen"]) ){
						$happen .= $dsc["cols"][ $keys[$j] ]["happen"];
					}
					if($the_fld["type"] == "password"){
						$genpass = autoGeneratePassButton($dsc["action"]."_save_item_frm",$keys[$j]);
						$input_disp_type = "password";
					}else{
						$genpass = "";
						$input_disp_type = "text";
					}
					// Do this only for readonly
					if ($dsc["cols"][ $keys[$j] ]["type"]=="readonly")
					{
						$disabled = " READONLY";
						isset($dsc["cols"][ $keys[$j] ]["default"]) ? $input_disp_value = $dsc["cols"][ $keys[$j] ]["default"] : $input_disp_value ='';
						isset($dsc["cols"][ $keys[$j] ]["happen"]) ? $happen = $dsc["cols"][ $keys[$j] ]["happen"] : $happen = '';;
					}
					if (isset($dsc["cols"][ $keys[$j] ]["callback"]))
					{
						$retArray=$dsc["cols"][ $keys[$j] ]["callback"]($id_fld_value);
						$input_disp_value = $retArray["value"];
						$happen = $retArray["happen"];
					}

					$ctrl = "<input type=\"$input_disp_type\" $size name=\"".$keys[$j]."\" value=\"".stripslashes($input_disp_value)."\" $disabled>$genpass$happen";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl,$j%2,$help);
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
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl,$j%2,$help);
					break;
				case "checkbox":
					if($dsc["cols"][ $keys[$j] ]["values"][0] == $a[ $keys[$j] ]){
						$selected = " checked ";
					}else{
						$selected = " ";
					}
					$ctrl = "<input type=\"checkbox\" name=\"".$keys[$j]."\" value=\"yes\" ".$selected.">";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl,$j%2,$help);
					break;
				case "popup":
					$nbr_choices = sizeof( $dsc["cols"][ $keys[$j] ]["values"] );
					$ctrl = "<select name=\"".$keys[$j]."\">";
					for($x=0;$x<$nbr_choices;$x++){
						if($dsc["cols"][ $keys[$j] ]["values"][$x] == $a[ $keys[$j] ]){
							$selected = " selected ";
						}else{
							$selected = "";
						}
						if( isset($dsc["cols"][ $keys[$j] ]["display_replace"][$x]) ){
							$display_val = $dsc["cols"][ $keys[$j] ]["display_replace"][$x];
						}else{
							$display_val = $dsc["cols"][ $keys[$j] ]["values"][$x];
						}
						$ctrl .= " <option value=\"".$dsc["cols"][ $keys[$j] ]["values"][$x]."\" $selected>$display_val</option>";
					}
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl,$j%2,$help);
					break;
				default:
					$ctrl = "Not implemented yet!!!";
					$out .= dtcFormLineDraw($dsc["cols"][ $keys[$j] ]["legend"],$ctrl,$j%2,$help);
					break;
				}
			}
			$delete_button = "<form action=\"?\">$fw
			<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."_delete_item"."\">
			<input type=\"hidden\" name=\"$id_fldname\" value=\"$id_fld_value\">
			".dtcDeleteButton()."</form>";

			$out .= "<tr><td>&nbsp;</td><td><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			<tr><td>".dtcApplyButton()."</form></td><td>$delete_button</td></tr></table></td></tr>";

			$out .= "</table>";
		}else{
			$out .= "No item by this number!";
		}
	}
	return $out;
}

?>
