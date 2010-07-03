<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="admin";
require_once("$dtcshared_path/dtc_lib.php");

require_once("authme.php");

include("inc/submit_root_querys.php");
include("inc/nav.php");
include("inc/dtc_config.php");
include("inc/draw_user_admin.php");

if(file_exists("dtcrm")){
	include("dtcrm/submit_to_sql.php");
	include("dtcrm/main.php");
	include("dtcrm/product_manager.php");
	
}

get_secpay_conf();

$DONOT_USE_ROTATING_PASS="yes";

////////////////////////////////////
// Create the top banner and menu //
////////////////////////////////////
$anotherTopBanner = anotherTopBanner("DTC","yes");
$anotherMenu = "";

$q = "SELECT * FROM $pro_mysql_new_admin_table WHERE id='".$_REQUEST["reqadm_id"]."'";
$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	$text = "$q: User not found!!!";
}else{
	$a = mysql_fetch_array($r);
	$p = array();
	$q3 = "SELECT * FROM $pro_mysql_pay_table WHERE id='";
	if (isset($b["paiement_id"])){
		$q3 .= $b["paiement_id"];
	}else{
		$q3 .= $a["paiement_id"];
	}
	$q3 .= "';";
	$r3 = mysql_query($q3)or die("Cannot query \"$q3\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n3 = mysql_num_rows($r3);
	if($n3 != 1){
		$has_payement = 0;
	}else{
		$has_payement = 1;
		$a3 = mysql_fetch_array($r3);
	}
	if (isset($_POST['reqadm_id'])){
		foreach ($a as $ids_a => $val_a){
			if (isset($_POST[$ids_a])){
				$b[$ids_a] = $_POST[$ids_a];
			}
			if($_POST["iscomp"] == "no"){
				$iscomp_yes = "";
				$iscomp_no = "checked";
			}else{
				$iscomp_yes = "checked";
				$iscomp_no = "";
			}
		}
		foreach ($a3 as $ids_p => $val_p){
			if (isset($_POST[$ids_p])){
				$p[$ids_p] = $_POST[$ids_p];
			}
		}
		
		// save the data.
		if ($b != $a){
			$c = array();
			foreach ($b as $id_b => $val_b){
				$c[] = $id_b." = '".mysql_real_escape_string($val_b)."'";
			}
			$q1 = "UPDATE $pro_mysql_new_admin_table SET ".join(",",$c)." WHERE id='".$_POST["reqadm_id"]."'";
			$r1= mysql_query($q1)or die("Cannot query \"$q1\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
		}
		if ($has_payement == 1 and $p != $a3){
			$d = array();
			foreach ($p as $id_c => $val_c){
				$d[] = $id_c." = '".mysql_real_escape_string($val_c)."'";
			}
			$q2 = "UPDATE $pro_mysql_pay_table SET ".join(",",$d)." WHERE id='".$_POST["paiement_id"]."'";
			$r2= mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
		}
	}else{
		$b = $a;
		$p = $a3;
		if($a["iscomp"] == "no"){
			$iscomp_yes = "";
			$iscomp_no = "checked";
		}else{
			$iscomp_yes = "checked";
			$iscomp_no = "";
		}
		
	}

	global $pro_mysql_product_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;
	
	$prod_popup = "";
	$prod_ids = array();
	$qpr = "SELECT id, name, price_dollar FROM $pro_mysql_product_table WHERE renew_prod_id='0' AND private='no' ORDER BY id";
	$rpr = mysql_query($qpr)or die("Cannot execute query \"$qpr\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$npr = mysql_num_rows($rpr);
	for($ipr=0;$ipr<$npr;$ipr++){
		$apr = mysql_fetch_array($rpr);
		if (isset($_POST["product_id"])) {
			$prod_id_sel = $_POST["product_id"];
		}else{
			$prod_id_sel = $a["product_id"];
		}
		if ($apr["id"] == $prod_id_sel){
			$selected = " selected ";
		}else{
			$selected = "";
		}
		$prod_popup .= "<option value=\"".$apr["id"]."\" $selected>".$apr["name"]." / ".$apr["price_dollar"]."$secpayconf_currency_symbol</option>\n";
		$prod_ids[] = $apr["id"];
	}
	// in case the product was erased after the client filled the form
	if (!in_array($a["product_id"], $prod_ids)) {
		$prod_popup .= "<option value=\"".$a["product_id"]."\" selected>".$a["product_id"]."</option>\n";
	}
	$prod_popup = "<select name=\"product_id\">".$prod_popup."</select>";

	$vps_ids = array();
	$vps_location_popup = "";
	$qv = "SELECT $pro_mysql_vps_server_table.hostname,$pro_mysql_vps_server_table.location
	FROM $pro_mysql_vps_ip_table,$pro_mysql_vps_server_table
	WHERE $pro_mysql_vps_ip_table.vps_server_hostname=$pro_mysql_vps_server_table.hostname
	AND $pro_mysql_vps_ip_table.available='yes'
	GROUP BY $pro_mysql_vps_server_table.location;";
	$rv = mysql_query($qv)or die("Cannot execute query \"$q\" ! line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
	$nv = mysql_num_rows($rv);
	for($iv=0;$iv<$nv;$iv++){
		$av = mysql_fetch_array($rv);
		if(isset($_REQUEST["vps_server_hostname"]) && $_REQUEST["vps_server_hostname"] == $av["hostname"]){
			$selected = " selected ";
		}else{
			$selected = "";
		}
		$vps_location_popup .= "<option value=\"".$av["hostname"]."\" $selected>".$av["location"]."</optioon>";
		$vps_ids[] = $apr["id"];
	}
	// when there is no vps, for example in a web hosting plan
	if (!in_array($a["vps_location"], $vps_ids)) {
		$vps_location_popup .= "<option value=\"".$a["vps_location"]."\" selected>".$a["vps_location"]."</option>\n";
	}
	$vps_location_popup = "<select name=\"vps_location\">".$vps_location_popup."</select>";
	
	$text = "<form action=\"".$_SERVER["PHP_SELF"]."\" method = \"POST\">
<input type=\"hidden\" name=\"reqadm_id\" value=\"".$_REQUEST["reqadm_id"]."\">
";
	$text .= dtcFormTableAttrs();
	$text .= dtcFormLineDraw( _("Product: ") ,$prod_popup);
	$text .= dtcFormLineDraw( _("VPS location: ") ,$vps_location_popup,0);
	$text .= dtcFormLineDraw( _("VPS OS: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"vps_os\" value=\"".stripcslashes($b["vps_os"])."\">");
	$text .= dtcFormLineDraw( _("Login: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"reqadm_login\" value=\"".stripcslashes($b["reqadm_login"])."\">",0);
	$text .= dtcFormLineDraw( _("Password: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"reqadm_pass\" value=\"".stripcslashes($b["reqadm_pass"])."\">");
	$text .= dtcFormLineDraw( _("Domain Name: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"domain_name\" value=\"".$b["domain_name"]."\">",0);
	$text .= dtcFormLineDraw( _("Familly name: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"family_name\"value=\"".stripcslashes($b["family_name"])."\">");
	$text .= dtcFormLineDraw( _("First name: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"first_name\" value=\"".stripcslashes($b["first_name"])."\">",0);
	$text .= dtcFormLineDraw( _("Is it a company: ") ,"<input type=\"radio\" name=\"iscomp\" value=\"yes\" $iscomp_yes > "._("Yes")."
<input type=\"radio\" name=\"iscomp\" value=\"no\" $iscomp_no > "._("No"));
	$text .= dtcFormLineDraw( _("Company name: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"comp_name\" value=\"".stripcslashes($b["comp_name"])."\">",0);
	$text .= dtcFormLineDraw( _("VAT number: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"vat_num\" value=\"".stripcslashes($b["vat_num"])."\">");
	$text .= dtcFormLineDraw( _("Address (line1): ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"addr1\" value=\"".stripcslashes($b["addr1"])."\">",0);
	$text .= dtcFormLineDraw( _("Address (line2): ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"addr2\" value=\"".stripcslashes($b["addr2"])."\">");
	$text .= dtcFormLineDraw( _("Address (line3): ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"addr3\" value=\"".stripcslashes($b["addr3"])."\">",0);
	$text .= dtcFormLineDraw( _("City: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"city\" value=\"".stripcslashes($b["city"])."\">");
	$text .= dtcFormLineDraw( _("Zipcode: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"zipcode\" value=\"".stripcslashes($b["zipcode"])."\">",0);
	$text .= dtcFormLineDraw( _("State: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"state\" value=\"".stripcslashes($b["state"])."\">");
	$text .= dtcFormLineDraw( _("Country: ") ,"<select class=\"dtcDatagrid_input_alt_color\" name=\"country\">".
cc_code_popup($b["country"])."</select>",0);
	$text .= dtcFormLineDraw( _("Phone number: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"phone\" value=\"".stripcslashes($b["phone"])."\">");
	$text .= dtcFormLineDraw( _("Fax: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"fax\" value=\"".stripcslashes($b["fax"])."\">",0);
	$text .= dtcFormLineDraw( _("Email: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"email\" value=\"".$b["email"]."\">");
	$text .= dtcFormLineDraw( _("Notes: ") ,"<textarea class=\"dtcDatagrid_input_alt_color\" cols=\"40\" rows=\"5\" name=\"custom_notes\">".stripcslashes($b["custom_notes"])."</textarea>",0);
	$text .= "<input type=\"hidden\" name=\"paiement_id\" value=\"".$b["paiement_id"]."\">";
	if ($has_payement == 1) {
		$text .= "<input type=\"hidden\" name=\"secpay_site\" value=\"".$p["secpay_site"]."\">";
		$text .= dtcFormLineDraw( _("Payment site: ") ,$p["secpay_site"]);
		$text .= dtcFormLineDraw( _("Refund amount: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"refund_amount\" value=\"".$p["refund_amount"]."\">",0);
		$text .= dtcFormLineDraw( _("Paiement cost: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"paiement_cost\" value=\"".$p["paiement_cost"]."\">");
		$text .= dtcFormLineDraw( _("Paiement total: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"paiement_total\" value=\"".$p["paiement_total"]."\">",0);
		$text .= dtcFormLineDraw( _("Vat rate: ") ,"<input class=\"dtcDatagrid_input_color\" size=\"40\" type=\"text\" name=\"vat_rate\" value=\"".$p["vat_rate"]."\">");
		$text .= dtcFormLineDraw( _("Vat total: ") ,"<input class=\"dtcDatagrid_input_alt_color\" size=\"40\" type=\"text\" name=\"vat_total\" value=\"".$p["vat_total"]."\">",0);
	}else{
		$text .= dtcFormLineDraw( _("Payment site: ") ,_("Paiement not found!"));
	}
	$text .= dtcFormLineDraw( _("Shopper IP: ") ,$b["shopper_ip"]);
	$text .= "<input type=\"hidden\" name=\"shopper_ip\" value=\"".$b["shopper_ip"]."\">";
	$text .= "
<tr><td align=\"right\"></td><td><div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\""._("Save")."\"></div>
 <div class=\"input_btn_right\"></div>
</div></form>";
}

$the_page[] = skin($conf_skin, $text, "User details:");

$pageContent = makeVerticalFrame($the_page);
$anotherFooter = anotherFooter("Footer content<br><br>");

echo anotherPage("admin:","","",makePreloads(),$anotherTopBanner,$anotherMenu,$pageContent,$anotherFooter);

?>
