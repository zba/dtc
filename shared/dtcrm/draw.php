<?php

require_once "$dtcshared_path/dtcrm/registry_calls.php";

$registration_added_price = 3;

require_once "$dtcshared_path/dtcrm/draw_register_forms.php";
require_once "$dtcshared_path/dtcrm/draw_handle.php";
require_once "$dtcshared_path/dtcrm/draw_whois.php";
require_once "$dtcshared_path/dtcrm/draw_nameservers.php";
require_once "$dtcshared_path/dtcrm/draw_transferdomain.php";
require_once "$dtcshared_path/dtcrm/draw_adddomain.php";

function draw_UpgradeAccount($admin){
	global $adm_pass;
	global $adm_login;
	global $addrlink;

	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_product_table;

	$nowrap = 'style="white-space:nowrap"';

	$frm_start = "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"upgrade_myaccount\">
";
	$client = $admin["client"];
	$out .= "<b><u>Upgrade my account:</u></b><br>";
	if($admin["info"]["prod_id"] != 0){
		$out .= "<i><u>Past account refundal</u></i><br>";
		$out .= "Your last command expire on the: ".$admin["info"]["expire"].".<br>";
		$out .= "Today is the: ".date("Y-m-d")."<br>";
		$today = mktime (0,0,0,date("m"),date("d"),date("Y"));
		$ar = explode("-",$admin["info"]["expire"]);
		$expire = mktime (0,0,0,$ar[1],0,$ar[0]);
		$remaining_seconds = $expire - $today;
		$days_remaining = $remaining_seconds / (60*60*24);

		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$admin["info"]["prod_id"]."';";
		$r = mysql_query($q)or die("Cannot querry: \"$q\" !!!".mysql_error()." line ".__LINE__." in file ".__FILE__);
		$prod = mysql_fetch_array($r);
		$ar = explode("-",$prod["period"]);
		$prod_period = mktime (0,0,0,$ar[1],0,1970+$ar[0]);
		$prod_days =  $prod_period / (60*60*24);
		$price_per_days = $prod["price_dollar"] / $prod_days;

		$refundal = floor($days_remaining * $price_per_days);

		$out .= "Your past account was: \$".$prod["price_dollar"]." for ".smartDate($prod["period"])."<br>";
		$out .= "Refundal ($days_remaining days) for upgrading will be: \$$refundal<br><br>";
	}else{
		$out .= "You currently don't have a validated account. Please contact customer support.";
		return $out;
	}
	$out .= "<i><u>Step 1: choose your upgrade</u></i><br>";
	if($_REQUEST["prod_id"] == "" || !isset($_REQUEST["prod_id"])){
		$out .= "Your current account is ".smartByte($admin["info"]["quota"]*1024*1024)." disk storage
and ".smartByte($admin["info"]["bandwidth_per_month_mb"]*1024*1024)." of data transfer each month.<br><br>
To what capacity would you like to upgrade to?<br>";
		$q = "SELECT * FROM $pro_mysql_product_table WHERE quota_disk > '".$admin["info"]["quota"]."' OR bandwidth > '".$admin["info"]["bandwidth_per_month_mb"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "$frm_start";
		$out .= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" height=\"1\">";
		$out .= "<tr><td></td><td>Product</td><td>Storage</td><td>Bandwidth/month</td>
			<td>Price</td><td>Periode</td></tr>";
		for($i=0;$i<$n;$i++){
			$ro = mysql_fetch_array($r);
			if($i % 2){
				$color = " bgcolor=\"#000000\" ";
			}else{
				$color = "";
			}
			$out .= '<tr><td><input type="radio" name="prod_id" value="'.$ro["id"].'"></td>';
			$out .= "<td $color $nowrap >".$ro["name"].'</td>';
			$out .= "<td $color $nowrap >".smartByte($ro["quota_disk"]*1024*1024).'</td>';
			$out .= "<td $color $nowrap >".smartByte($ro["bandwidth"]*1024*1024).'</td>';
			$out .= "<td $color $nowrap >".$ro["price_dollar"].'</td>';

			$out .= "<td $color $nowrap >".smartDate($ro["period"]).'</td></tr>';
		}
		$out .= '</table><center><input type="submit" value="Calculate price"></center></form>';
		return $out;
	}
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$_REQUEST["prod_id"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)	die("Product not found !!!");
	$ro = mysql_fetch_array($r);

	$frm_start .= '<input type="hidden" name="prod_id" value="'.$ro["id"].'">';
	$out .= "You have selected: ".$ro["name"];
	$out .= " (Storage: ".smartByte($ro["quota_disk"]*1024*1024);
	$out .= ", Transfer: ".smartByte($ro["bandwidth"]*1024*1024).'), ';
	$out .= '$'.$ro["price_dollar"].' each '.smartDate($ro["period"]);

	$out .=  "<br><br><i><u>Step 2: proceed to upgrade</u></i><br>";
	$remaining = $admin["client"]["dollar"];

	$ze_price = $ro["price_dollar"];
	$heber_price = $ze_price - $refundal;

	$out .= "Remaining on your account: \$" . $remaining . "<br>
New account price: \$". $ze_price . "<br>
Past account refundal: \$". $refundal . "<br>
Total price: \$". $heber_price . "<br>";
	if($heber_price > $remaining){
		$to_pay = $heber_price - $remaining;

		$payButton = paynowButton($product_id,$to_pay);

		$out .= "<br>You currently don't have enough funds on your account. You will be
redirected to our paiement system. Please click on the button bellow
to pay, and then click refresh button.<br><br>
<br><br>
$form_start<input type=\"submit\" value=\"Paiement done, let met checkout\">
</form>";
		return $out;
	}

	$after_upgrade_remaining = $remaining - $heber_price;
	$out .= "After upgrade, you will have: \$$after_upgrade_remaining<br><br>";

	// Check for confirmation
	if($_REQUEST["toreg_confirm_register"] != "yes"){
		$out .= "
You have enough funds on your account to proceed account upgrade. Press
the confirm button and your order will be proceeded.<br><br>
$form_start
<input type=\"hidden\" name=\"toreg_confirm_register\" value=\"yes\">
<input type=\"submit\" value=\"Proceed to account upgrade\">
</form>";
		return $out;
	}




	return $out;
}

?>
