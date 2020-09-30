<?php

///////////////////////////////////////////
// TMD : Tree Memory to Database Library //
// Written by Thomas GOIRAND             //
// Under GPL Licence                     //
// See LICENCE file for details          //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                                                             //
// Functions you can call :                                                                                                    //
// TMDselectTables($table_tree,$table_treecontent,array_name);                                                                 //
//									Select witch tables the lib will use for storing/retriving in database                     //
//                                                                                                                             //
// TMDgetTreeID($name);             Returns a tree ID giving a tree name, giving it's name as input. Return false if not found //
//                                                                                                                             //
// TMDnewTree($name);               Create a new tree, returns it's ID                                                         //
//                                                                                                                             //
// TMDtreeDelete($treeID);          Delete a tree in database                                                                  //
//                                                                                                                             //
// TMDtreeMemToDB($treeID,$tree);   Save a tree from memory to database                                                        //
//                                                                                                                             //
// TDMtreeDBToMem($tree_id);        Retrive a tree from database                                                               //
//                                                                                                                             //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// Internal vars stored by TMDselectTables()
// Thoses are the current tables selected

$TMDtbl_tree = "defaulttree";			// Tree names and root nodes table
$TMDtbl_content = "defaultcontent";	// Tree content (all the nodes of all trees)
$TMDfield_list = array();		// List of all field names that contain the tree data (eg non-tree info)
$TMDarray_name = "array";		// Name of the key that will handle all the tree descendants (one node childs)
								// Example: $the_node[$TMDarray_name] will be an array of childs

// ----------------------------------------------------------------------------------- //
// ----------------------                                       ---------------------- //
// ----------------------   UTILITY FUNCTIONS USED BY THE LIB   ---------------------- //
// ----------------------                                       ---------------------- //
// ----------------------------------------------------------------------------------- //
/**
 *
 * Fetch all rows of a table
 *
 * @param $query_string The SELECT query to give to mysql
 * @return A table of rows, each rows beeing an associative array
 *
 */
function TMDfetchAllRawsInArray($query_string){
	$result = mysql_query($query_string) or die("Cannot query : \"$query_string\" !".mysql_error());
	$num_rows = mysql_num_rows($result);
	for($i=0;$i<$num_rows;$i++){
		$table[] = mysql_fetch_array($result);
	}
	return $table;
}

/**
 *
 * Fetch an object from DB to memory
 *
 * @param id ID of the object that will be fetched in $TMDtbl_content
 * @return the object itself in an associative table form (mysql_fetch_array())
 *
 */
function TDMtreeFetchObject($id){
	global $TMDtbl_content;
	$table = fetchAllRawsInArray("SELECT * FROM $TMDtbl_content WHERE id='$id';");
	if(sizeof($table) != 1)	die("Cannot fetch object $ob_id !");
	return $table[0];
}

/**
 *
 * This function is used by this lib to know witch mysql fields are to be filled with the tree datas
 *
 * @return An array with all mysql variable names that contain users data (eg non-tree information)
 *
 */
function TMDgetFieldList(){
	global $TMDtbl_tree;
	global $TMDtbl_content;
	global $TMDarray_name;

	$query = "SELECT * FROM $TMDtbl_content";
	$result= mysql_query($query) or die("Cannot query $query<br>".mysql_error());
	$num_fields= mysql_num_fields($result);
	for($i=0;$i<$num_fields;$i++){
		$field_name= mysql_field_name($result,$i);
		if(	$field_name != "id" &&
			$field_name != "ob_head" && 
			$field_name != "ob_tail" && 
			$field_name != "ob_next" && 
			$field_name != "tree_id" && 
			$field_name != "name"){
			$out[] = $field_name;
		}
	}
	return $out;
}

// ---------------------------------------------------------------------------------- //
// ----------------------                                      ---------------------- //
// ----------------------    A P I    FUNCTIONS YOU CAN USE    ---------------------- //
// ----------------------                                      ---------------------- //
// ---------------------------------------------------------------------------------- //
/////////////////////////////////////////////////
// Select the tables used by further functions //
/////////////////////////////////////////////////
/**
 *
 * Call this function prior to call any others
 *
 * This function tells the TMD lib witch tables are used for storing/retriving informations from database
 *
 * @param $table_tree table used for tree identification (tree name and rootnode ID)
 * @param $table_treecontent table used for the tree content storage
 * @param $array_name name of the key used as a tree container for one node. All childs will be returned using this key name.
 * @return Nothing
 *
 */
function TMDselectTables($table_tree,$table_treecontent,$array_name){
	global $TMDtbl_tree;
	global $TMDtbl_content;
	global $TMDarray_name;
	global $TMDfield_list;

	$TMDtbl_tree = $table_tree;
	$TMDtbl_content = $table_treecontent;
	$TMDarray_name = $array_name;
	$TMDfield_list = TMDgetFieldList();
}

/**
 *
 * Find a tree id with it's name
 *
 * @param $name The tree name
 * @return false upon error, the tree_id otherwise
 *
 */
function TMDgetTreeID($name){
	global $TMDtbl_tree;

	$query = "SELECT id FROM $TMDtbl_tree WHERE name='$name';";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
	if(mysql_num_rows($result) != 1) return false;
	$tree = mysql_fetch_array($result);
	return $tree["id"];
}

/**
 *
 * Adds a new tree to the database
 *
 * @param $name The tree name
 * @return false upon error, the tree_id otherwise
 *
 */
function TMDnewTree($name){
	global $TMDtbl_tree;
	global $TMDtbl_content;

	if(TMDgetTreeID($name) != false) return false;

	$query = "INSERT INTO $TMDtbl_tree (id,name,root_object_id)VALUES('','$name','0')";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
	$tree_id = mysql_insert_id();

	$query = "INSERT INTO $TMDtbl_content (id,ob_head,ob_tail,ob_next,tree_id)
								VALUES('','0','0','0','$tree_id')";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
	$root_node_id = mysql_insert_id();

	$query = "UPDATE $TMDtbl_tree SET root_object_id='$root_node_id' WHERE id='$tree_id';";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
	return $tree_id;
}

/**
 *
 * Delete a tree in the database
 *
 * @param $treeID The tree ID in the database. Call TMDgetTreeID() if you don't know the tree ID.
 * @return nothing
 *
 */
function TMDtreeDelete($treeID){
	global $TMDtbl_tree;
	global $TMDtbl_content;
	$query = "DELETE FROM $TMDtbl_tree WHERE id='$treeID';";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());

	$query = "DELETE FROM $TMDtbl_content WHERE tree_id='$treeID';";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
}

/////////////////////////////
// Save a tree to Database //
/////////////////////////////
/**
 *
 * Internal recursive function for storing all the datas of a tree node. Do not call.
 *
 * @param $node_id The node to store in
 * @param $node_to_add The node to store itself, using memory representation (eg associative array)
 * @return nothing
 *
 */
function TDMtreeMemToDBUpdateDataFields($node_id,$node_to_add){
	global $TMDtbl_content;
	global $TMDfield_list;

	$nbr_fields = sizeof($TMDfield_list);
	for($i=0;$i<$nbr_fields;$i++){
		if($i != 0){
			$vars_update .= ',';
		}
		$vars_update .= $TMDfield_list[$i]."='". $node_to_add[$TMDfield_list[$i]] ."'";
	}
	$query = "UPDATE $TMDtbl_content SET $vars_update WHERE id='$node_id';";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
}

/**
 *
 * Internal function that recursively store a node and it's child in the database. Do not call
 *
 * @param $treeID The tree id to store in
 * @param $root_id The parent node ID
 * @param $nodes_to_add The node and it's child using memory representation (eg associative array)
 * @return nothing
 *
 */
function TMDtreeMemToDBRecursion($treeID,$root_id,$nodes_to_add){
	global $TMDtbl_tree;
	global $TMDtbl_content;
	global $TMDarray_name;

	$node_nbr = sizeof($nodes_to_add);

	if($node_nbr == 1){
		$cur_node = $nodes_to_add[0];

		$query = "INSERT INTO $TMDtbl_content (id,ob_head,ob_tail,ob_next,tree_id)
										VALUES ('','0','0','$root_id','$treeID');";
		$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
		$node_id = mysql_insert_id();

		$query = "UPDATE $TMDtbl_content SET ob_head='$node_id',ob_tail='$node_id' WHERE id='$root_id' AND tree_id='$treeID';";
		$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());

		TDMtreeMemToDBUpdateDataFields($node_id,$cur_node);

		if(is_array($cur_node[$TMDarray_name])){
			TMDtreeMemToDBRecursion($treeID,$node_id,$cur_node["$TMDarray_name"]);
		}

		return;
	}

	for($i=0;$i<$node_nbr;$i++){
		$cur_node = $nodes_to_add[$i];

		$query = "INSERT INTO $TMDtbl_content (id,ob_head,ob_tail,ob_next,tree_id)
										VALUES ('','0','0','0','$treeID');";
		$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
		$node_id = mysql_insert_id();

		if($i == 0){
			$query = "UPDATE $TMDtbl_content SET ob_head='$node_id' WHERE id='$root_id' AND tree_id='$treeID';";
			$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
		}

		if(isset($last_node_id)){
			$query = "UPDATE $TMDtbl_content SET ob_next='$node_id' WHERE id='$last_node_id' AND tree_id='$treeID';";
			$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
		}
		// If last node, then update the parent node
		if($i == $node_nbr-1){
			$query = "UPDATE $TMDtbl_content SET ob_next='$root_id' WHERE id='$node_id' AND tree_id='$treeID';";
			$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
			$query = "UPDATE $TMDtbl_content SET ob_tail='$node_id' WHERE id='$root_id' AND tree_id='$treeID';";
			$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
		}
		TDMtreeMemToDBUpdateDataFields($node_id,$cur_node);

		$last_node_id = $node_id;
		if(is_array($cur_node[$TMDarray_name])){
			TMDtreeMemToDBRecursion($treeID,$node_id,$cur_node[$TMDarray_name]);
		}
	}
	return;
}
/**
 *
 * Store a tree in database
 *
 * @param $treeID The tree ID to store in. This tree will be deleted and rewrited completely
 * @param $tree The node and it's child using memory representation (eg associative array)
 * @return Nothing
 *
 */
function TMDtreeMemToDB($treeID,$tree){
	global $TMDtbl_tree;
	global $TMDtbl_content;
	global $TMDarray_name;

	// Delete all rows of the current menu
	$query = "DELETE FROM $TMDtbl_content WHERE tree_id='$treeID';";
	mysql_query($query);

	// Insert the root node, and fetch it's ID
	$query = "INSERT INTO $TMDtbl_content (id,ob_head,ob_tail,ob_next,tree_id) VALUES
										('','0','0','0','$treeID');";
	$result = mysql_query($query) or die("Cannot query: \"$query\" !".mysql_error());
	$root_id = mysql_insert_id();

	// Modify the "tree" table to have the correct (newly created) root node ID
	$query = "UPDATE $TMDtbl_tree SET root_object_id='$root_id' WHERE id='$treeID';";
	$result = mysql_query($query) or die("Cannot query: \"$query\" !".mysql_error());

	// Now, add ALL nodes to the root node
	TMDtreeMemToDBRecursion($treeID,$root_id,$tree);
}

//////////////////////////////////////////////////
// Fetch a tree from the database to the memory //
//////////////////////////////////////////////////
/**
 *
 * Internal function that remove unwanted database tree information (like id,ob_head, etc...)
 *
 */
function TDMcleanObject($object){
	global $TMDfield_list;
	global $TMDarray_name;

	TMDgetFieldList();
	$keys = array_keys($object);
	$nbr_key = sizeof($keys);

	$new_obj = array();
	for($i=0;$i<$nbr_key;$i++){
		$flag = false;
		for($j=0;$j<sizeof($TMDfield_list);$j++){
			if($keys[$i] == $TMDfield_list[$j] || $keys[$i] == $TMDarray_name){
				$flag = true;
			}
			if($keys[$i] == "0")	$flag = false;
		}
		if($flag == false){
			continue;
		}
		$new_obj[$keys[$i]] = $object[$keys[$i]];
	}
	return $new_obj;
}

function TDMcleanChilds($tree){
	global $TMDarray_name;

	for($i=0;$i<sizeof($tree);$i++){
		$obj = &$tree[$i];
		if(is_array($obj[$TMDarray_name])){
//			echo "<br>Now cleaning $TMDarray_name<br>";
			$obj[$TMDarray_name] = TDMcleanChilds($obj[$TMDarray_name]);
		}
		$obj = TDMcleanObject($obj);
	}
	return $tree;
}
/**
 *
 * Internal function to retrive (recursively) one node's children from a database. Do not call.
 *
 * @param $father_id ID of the father node that handles the nodes to retrive
 * @return A associative array representing the node to retrive and all it's childs
 *
 */
function TDMtreeDBToMemRecurtion($object_id){
	global $TMDtbl_tree;
	global $TMDtbl_content;
	global $TMDarray_name;

	$query = "SELECT * FROM $TMDtbl_content WHERE id='$object_id';";
//	echo "<br>Staring one level with query : $query<br>";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
	$nbr_rows = mysql_num_rows($result);
	if($nbr_rows != 1)		return false;
	$ob = mysql_fetch_array($result);

	if($ob["ob_head"] == 0){
		return $ob;
	}

	$next_id = $ob["ob_head"];
	do{
		$cur_ob = TDMtreeDBToMemRecurtion($next_id);
		$next_id = $cur_ob["ob_next"];
		$childs[] = $cur_ob;
	}while($next_id != $object_id);

	$ob[$TMDarray_name] = $childs;
//	$ob = TDMcleanObject($ob);
	return $ob;
}

/**
 *
 * Retrive a tree from database giving it's node ID as parameter.
 *
 * @param $tree_id The tree to retrive
 * @return An associative array representing the tree
 *
 */
function TDMtreeDBToMem($tree_id){
	global $TMDtbl_tree;
	global $TMDtbl_content;
	global $TMDarray_name;

	$query = "SELECT root_object_id FROM $TMDtbl_tree WHERE id='$tree_id';";
	$result = mysql_query($query) or die("Cannot query $query<br>".mysql_error());
	$nbr_rows = mysql_num_rows($result);
	if($nbr_rows != 1)		return false;
	$row = mysql_fetch_array($result);
	$all_the_tree = TDMtreeDBToMemRecurtion($row["root_object_id"]);
	$tree = $all_the_tree["$TMDarray_name"];
	$tree = TDMcleanChilds($tree);
	return $tree;
}

?>
