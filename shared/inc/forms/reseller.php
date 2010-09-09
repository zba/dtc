<?php

//////////////////////////////////////
// Database management for one user //
//////////////////////////////////////
// Todo : add a button for creating a MySql databe for one user
// and add credential to it !
function drawReseller($admin){
	global $lang;
	global $txt_draw_tatabase_your_list;
	global $conf_mysql_db;
	global $adm_login;
	global $adm_pass;
	global $pro_mysql_admin_table;
	
	global $conf_demo_version;

	$hidden = "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">";

	$out = "<b><u>". _("Your child accounts:") ."</u></b>";
	$out .= "<table>
	<tr><td>". _("Login") ."</td><td>". _("Password") ."</td><td>". _("Action") ."</td></tr>";
	if($admin["info"]["ob_head"] != ""){
		$next_adm = $admin["info"]["ob_head"];
		while($next_adm != $adm_login){
			$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$next_adm';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1)	die("Could not fetch one of the child accounts !!!");
			$a = mysql_fetch_array($r);
			$next_adm = $a["ob_next"];
			$out .= "<tr><td><form action=\"?\">$hidden
			<input type=\"hidden\" name=\"action\" value=\"change_child_account_values\">
			<input type=\"hidden\" name=\"account_name\" value=\"".$a["adm_login"]."\">".$a["adm_login"]."</td>
			<td><input type=\"text\" name=\"new_adm_pass\" value=\"\"></td><td><input type=\"submit\" value=\"." . _("Save") . "\"></form>
			<form action=\"?\">$hidden
			<input type=\"hidden\" name=\"action\" value=\"delete_child_account\">
			<input type=\"hidden\" name=\"account_name\" value=\"".$a["adm_login"]."\"><input type=\"submit\" value=\"". _("Del") . "\"></form></td></tr>";
		}
//		TMDselectTables("treeindex",$pro_mysql_admin_table,"childrens");
	}
	$out .= "<tr><td><form action=\"?\">
	$hidden<input type=\"hidden\" name=\"action\" value=\"add_child_account\">
	<input type=\"text\" name=\"new_adm_login\" value=\"\"></td>
	<td><input type=\"text\" name=\"new_adm_pass\" value=\"\"></td>
	<td><input type=\"submit\" value=\"" . _("Create") . "\"></form></td></tr></table>";
	return $out;
}

?>
