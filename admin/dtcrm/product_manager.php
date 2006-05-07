<?php
	/**
	* @package DTC
	* @version  $Id: product_manager.php,v 1.9 2006/05/07 12:17:21 seeb Exp $
	* New arrays for translate menage_products
	* @see dtc/admin/inc/dtc_config_strings.php
	**/
function productManager(){
        global $pro_mysql_product_table;
        // modyfication by seeb 7-05-2006
        global $lang;
        global $txt_product_name;
        global $txt_product_price;
        global $txt_product_traffic;
        global $txt_product_disk;
        global $txt_product_mail;
        global $txt_product_action;
        global $txt_product_adddomain;
        global $txt_product_period;
        // end of modyfication ;)

        $q = "SELECT * FROM $pro_mysql_product_table";
        $r = mysql_query($q)or die("Cannot query \"$q\" !!! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
        $n = mysql_num_rows($r);
// modification by seeb 7th may 2006
        $out = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\"><tr>
        <td><b>".$txt_product_name[$lang]."</b></td>
        <td><b>".$txt_product_price[$lang]." \$</b></td>
        <td><b>".$txt_product_price[$lang]." &#8364;</b></td>
        <td><b>".$txt_product_disk[$lang]." MB</b></td>
        <td><b>".$txt_product_traffic[$lang]." MB</b></td>
        <td><b>".$txt_product_mail[$lang]."</b></td>
        <td><b>DB</b></td>
		<td><b>".$txt_product_period[$lang]."</b></td>
		<td><b>".$txt_product_adddomain[$lang]."</b></td>
		<td><b>".$txt_product_action[$lang]."</b></td>
		</tr>";
 // end modification
	for($i=0;$i<$n+1;$i++){
		if($i<$n){
			$a = mysql_fetch_array($r);
		}else{
			$a["id"] = "";
			$a["name"] = "";
			$a["price_dollar"] = "";
			$a["price_euro"] = "";
			$a["quota_disk"] = "";
			$a["bandwidth"] = "";
			$a["nbr_email"] = "";
			$a["nbr_database"] = "";
			$a["period"] = "";
			$a["allow_add_domain"] = "";
		}
		if($i%2){
			$bg_color="bgcolor=\"#000000\"";
		}else{
			$bg_color="";
		}
		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">";
		$out .= "<tr><td $bg_color><input type=\"hidden\" name=\"action\" value=\"edit_product\"><input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\"><input size=\"35\" type=\"text\" name=\"prodname\" value=\"".$a["name"]."\"><input type=\"hidden\" name=\"id\" value=\"".$a["id"]."\"></td>";
		$out .= "<td $bg_color><input size=\"4\" type=\"text\" name=\"price_dollar\" value=\"".$a["price_dollar"]."\"></td>";
		$out .= "<td $bg_color><input size=\"4\" type=\"text\" name=\"price_euro\" value=\"".$a["price_euro"]."\"></td>";
		$out .= "<td $bg_color><input size=\"6\" type=\"text\" name=\"quota_disk\" value=\"".$a["quota_disk"]."\"></td>";
		$out .= "<td $bg_color><input size=\"6\" type=\"text\" name=\"bandwidth\" value=\"".$a["bandwidth"]."\"></td>";
		$out .= "<td $bg_color><input size=\"2\" type=\"text\" name=\"nbr_email\" value=\"".$a["nbr_email"]."\"></td>";
		$out .= "<td $bg_color><input size=\"2\" type=\"text\" name=\"nbr_database\" value=\"".$a["nbr_database"]."\"></td>";
		$out .= "<td $bg_color><input size=\"8\" type=\"text\" name=\"period\" value=\"".$a["period"]."\"></td>";
		if($a["allow_add_domain"] == "yes"){
			$out .= "<td $bg_color><input type=\"checkbox\" name=\"allow_add_domain\" value=\"yes\" checked></td>";
		}else{
			$out .= "<td $bg_color><input type=\"checkbox\" name=\"allow_add_domain\" value=\"yes\"></td>";
		}

		if($i<$n){
			$out .= "<td $bg_color><input type=\"submit\" name=\"submit\" value=\"save\"> ";
			$out .= "<input type=\"submit\" name=\"submit\" value=\"del\"></td></form>";
		}else{
			$out .= "<td $bg_color><input type=\"submit\" name=\"submit\" value=\"create\"></td>";
		}
		$out .= "</tr>";
	}
	$out .= "</table>";
	return $out;
}

?>