<?php

function productManager(){
	global $pro_mysql_product_table;

	$q = "SELECT * FROM $pro_mysql_product_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$out = "<table width=\"100%\"><tr>
	<td><b>Name</b></td><td><b>Price \$</b></td><td><b>Quota disk</b></td>
<td><b>Bandwidth (MB)</b></td><td><b>Period</b></td><td><b>Action</b></td>
</tr>";
	for($i=0;$i<$n+1;$i++){
		if($i<$n){
			$a = mysql_fetch_array($r);
		}else{
			unset($a);
		}
		$out .= "<tr><td><input size=\"30\" type=\"text\" name=\"prodname\" value=\"".$a["name"]."\"></td>";
		$out .= "<td><input size=\"6\" type=\"text\" name=\"price_dollar\" value=\"".$a["price_dollar"]."\"></td>";
		$out .= "<td><input size=\"8\" type=\"text\" name=\"quota_disk\" value=\"".$a["quota_disk"]."\"></td>";
		$out .= "<td><input size=\"8\" type=\"text\" name=\"bandwidth\" value=\"".$a["bandwidth"]."\"></td>";
		$out .= "<td><input size=\"10\" type=\"text\" name=\"period\" value=\"".$a["period"]."\"></td>";
		if($i<$n){
			$out .= "<td><input type=\"submit\" name=\"submit\" value=\"save\"> ";
			$out .= "<input type=\"submit\" name=\"submit\" value=\"del\"></td>";
		}else{
			$out .= "<td><input type=\"submit\" name=\"submit\" value=\"create\"></td>";
		}
		$out .= "</tr>";
	}
	$out .= "</table>";
	return $out;
}

?>