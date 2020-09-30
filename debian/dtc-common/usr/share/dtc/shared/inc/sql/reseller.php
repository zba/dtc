<?php

/////////////////////////////
// Ftp accounts management //
/////////////////////////////
// new_adm_login=test&new_adm_pass=test
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_child_account"){
	checkLoginPass($adm_login,$adm_pass);
	if(!isDTCPassword($_REQUEST["new_adm_pass"])){
		$submit_err .= "Incorrect FTP password: from 6 to 16 chars, a-z A-Z 0-9<br>\n";
		$commit_flag = "no";
	}
	if(!isFtpLogin($_REQUEST["new_adm_login"])){
		$submit_err .= "Incorrect DTC login: a-z A-Z 0-9<br>\n";
		$commit_flag = "no";
	}

	if($commit_flag == "yes"){
		$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["new_adm_login"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 0){
			$submit_err .= "There is already an admin with that name. Please pickup another name!<br>\n";
			$commit_flag = "no";
		}
	}

	checkLoginPass($adm_login,$adm_pass);
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Cannot find user $adm_login line ".__LINE__." file ".__FILE__);
	$a = mysql_fetch_array($r);
	if($commit_flag == "yes"){
		// Create the admin's path
		$new_adm_path = $conf_site_root_host_path."/".$_REQUEST["new_adm_login"];
		if($conf_demo_version == "no"){
			$oldumask = umask(0);
			if(!file_exists($new_adm_path)){
				mkdir("$new_adm_path", 0750);
				$console .= "mkdir $new_adm_path;<br>";
			}
			umask($oldumask);
		}

		// Insert the new admin
		$q = "INSERT INTO $pro_mysql_admin_table (adm_login, adm_pass, path, ob_next, ob_head, ob_tail)
		VALUES ('".$_REQUEST["new_adm_login"]."','".$_REQUEST["new_adm_pass"]."', '$new_adm_path','".$a["adm_login"]."','','');";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo "<!-- $q -->";
		// If this admin had no child account
		echo "<!-- ob_head: ".$a["ob_head"]."<br>"."ob_tail: ".$a["ob_tail"]."<br> -->";
		if($a["ob_head"] == "" && $a["ob_tail"] == ""){
			// Simply update the main account ob_head and tail
			$q = "UPDATE $pro_mysql_admin_table SET ob_head='".$_REQUEST["new_adm_login"]."',ob_tail='".$_REQUEST["new_adm_login"]."' WHERE adm_login='$adm_login';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo "<!-- $q -->";
		// If this admin has children
		}else{
			// Update the last child's ob_next to point to the added admin
			$q = "UPDATE $pro_mysql_admin_table SET ob_next='".$_REQUEST["new_adm_login"]."' WHERE adm_login='".$a["ob_tail"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo "<!-- $q -->";

			// Simply update the main account ob_tail, ob_head is good already
			$q = "UPDATE $pro_mysql_admin_table SET ob_tail='".$_REQUEST["new_adm_login"]."' WHERE adm_login='$adm_login';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		echo "<!-- $q -->";
		}
	}
}

function recursiveDeleteAdmin($adm_login){
	global $pro_mysql_admin_table;

	echo "<!-- recursiveDeleteAdmin($adm_login) -->";
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$a = mysql_fetch_array($r);
	// Admin has children
	if($a["ob_head"] != "" || $a["ob_tail"] != ""){
		if($a["ob_head"] == "" || $a["ob_tail"] == ""){
			die("ob_head and ob_tail don't patch: sub-admin tree is broken line ".__LINE__." file ".__FILE__.", please contact system administrator!");
		}
		$next = $a["ob_head"];
		// Get a list of all children of the admin
		while($next != $adm_login){
			$child_list[] = $next;
			$q = "SELECT ob_next FROM $pro_mysql_admin_table WHERE adm_login='$next';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$a = mysql_fetch_array($r);
			$next = $a["ob_next"];
		}
		// Then delete them all calling recurtion
		$nbr_child = sizeof($child_list);
		for($i=0;$i<$nbr_child;$i++){
			recursiveDeleteAdmin($child_list[$i]);
		}
	}
	// Now do the real delete
	DTCdeleteAdmin($adm_login);
}

// Check if $adm_login_to_delete is a direct child of $adm_login_father
// and delete $adm_login_to_delete and it's children
function deleteAdminFromFather($adm_login_to_delete,$adm_login_father){
	global $pro_mysql_admin_table;
	global $submit_err;
	global $commit_flag;

	echo "<!-- deleteAdminFromFather $adm_login_to_delete, $adm_login_father -->";

	// Search for child to delete: we have to be SURE the admin
	// is one of our direct child accounts!!! If it is, then call the recursive deletion of the user
	// and update SQL links
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login_father';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$a = mysql_fetch_array($r);
	if($a["ob_head"] == "" || $a["ob_tail"] == ""){
		$submit_err .= "You don't have any child account: nothing to delete!<br>\n";
		$commit_flag = "no";
	}else{
		// There is only one child, we should just delete the account recurcively and remove the tree
		if($a["ob_head"] == $a["ob_tail"] && $a["ob_head"] != "" && $a["ob_head"] == $adm_login_to_delete){
			echo "<!-- reached -->";
			$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["ob_head"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			if(mysql_num_rows($r) != 1){
				$submit_err .= "User not found in db: sub-admin tree is broken line ".__LINE__." file ".__FILE__.", please contact system administrator!";
				$commit_flag = "no";
			}else{
				$q = "UPDATE $pro_mysql_admin_table SET ob_head='',ob_tail='' WHERE adm_login='$adm_login_father';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
				echo "<!-- $q -->";
				recursiveDeleteAdmin($adm_login_to_delete);
			}
		// There is more than one child
		}else{
			// Account to delete is first child
			if($a["ob_head"] == $adm_login_to_delete){
				$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$a["ob_head"]."';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
				if(mysql_num_rows($r) != 1){
					$submit_err .= "User not found in db: sub-admin tree is broken line ".__LINE__." file ".__FILE__.", please contact system administrator!";
					$commit_flag = "no";
				}else{
					$a = mysql_fetch_array($r);
					$q = "UPDATE $pro_mysql_admin_table SET ob_head='".$a["ob_next"]."' WHERE adm_login='$adm_login_father';";
					echo "<!-- $q -->";
					//recursiveDeleteAdmin($adm_login_to_delete);
				}
				recursiveDeleteAdmin($adm_login_to_delete);
			// Account to delete is last child or
			// Account to delete is not first child and not last child, but in the chained list
			// we should search for it first
			}else{
				$next = $a["ob_head"];
				$ob_previous = $next;
				$founded = "no";
				$deep_limit = 9999;
				while($next != $adm_login_father && $deep_limit > 0 && $founded == "no" && $commit_flag == "yes"){
					$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$next';";
					$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
					if(mysql_num_rows($r) != 1){
						$submit_err .= "User not found in db: sub-admin tree is broken line ".__LINE__." file ".__FILE__.", please contact system administrator!";
						$commit_flag = "no";
					}else{
						$a2 = mysql_fetch_array($r);
						if($a2["ob_next"] == $adm_login_to_delete){
							$founded = "yes";
							$next = $a2["ob_next"];
							break;
						}else{
							if($a2["ob_next"] == ""){
								$submit_err .= "User not found in db: sub-admin tree is broken, the subadmin shoulnd't have it's ob_next empty line ".__LINE__." file ".__FILE__." please contact system administrator!";
								$commit_flag = "no";
								break;
							}else{
								$ob_previous = $next;
								$next = $a2["ob_next"];
								$deep_limit--;
							}
						}
					}
				}
				if($deep_limit == 0){
					$submit_err .= "User not found in db: sub-admin tree is broken, deep search limit reached line ".__LINE__." file ".__FILE__." please contact system administrator!";
					$commit_flag = "no";
				}else{
					if( $founded == "yes" && $commit_flag == "yes" ){
						// Account is the last child
						if($a["ob_tail"] == $adm_login_to_delete){
							$q = "UPDATE $pro_mysql_admin_table SET ob_head='$ob_previous' WHERE adm_login='$adm_login_father';";
							echo "<!-- $q -->";
							//$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
							$q = "UPDATE $pro_mysql_admin_table SET ob_next='$adm_login_father' WHERE adm_login='$ob_previous';";
							echo "<!-- $q -->";
							//$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
						// Account is one of the childs, but not first or last child
						}else{
							$q = "UPDATE $pro_mysql_admin_table SET ob_next='$next' WHERE adm_login='$ob_previous';";
							//$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
							echo "<!-- $q -->";
						}
						recursiveDeleteAdmin($adm_login_to_delete);
					}
				}
			}
		}
	}
}

function rootConsolDeleteAdmin($adm_login){
	global $pro_mysql_admin_table;

	echo "<!-- rootConsolDeleteAdmin($adm_login) -->";
	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Cannot found admin line ".__LINE__." file ".__FILE__);
	$a = mysql_fetch_array($r);
	// The admin has a father admin and it should be removed from it's chain!
	if($a["ob_next"] != ""){
		// Search for the father admin
		$cur = $a;
		$max_loop = 1024;
		do{
			if($cur["ob_next"] == "")	die("Object should have ob_next line ".__LINE__." file ".__FILE__);
			$q2 = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='".$cur["ob_next"]."'";
			$r2 = mysql_query($q2)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1)	die("Cannot found admin line ".__LINE__." file ".__FILE__);
			$last_adm_login = $cur["adm_login"];
			$cur = mysql_fetch_array($r2);
			$max_loop--;
		}while($cur["ob_tail"] != $last_adm_login && $max_loop != 0);
		if($max_loop == 0){
			die("Max loop reached line ".__LINE__." file ".__FILE__);
		}
		$adm_login_father = $cur["adm_login"];
		deleteAdminFromFather($adm_login,$adm_login_father);
	}else{
		recursiveDeleteAdmin($adm_login);
	}
}

// adm_login=zigo&addrlink=resseller&adm_pass=513411410&action=delete_child_account&account_name=bbbb
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_child_account"){
	checkLoginPass($adm_login,$adm_pass);


	if(!isFtpLogin($_REQUEST["account_name"])){
		$submit_err .= "Incorrect DTC login: a-z A-Z 0-9<br>\n";
		$commit_flag = "no";
	}else{
		deleteAdminFromFather($_REQUEST["account_name"],$adm_login);
	}
}

?>
