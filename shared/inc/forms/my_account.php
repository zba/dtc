<?php

function drawAdminTools_MyAccount($admin){
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $pro_mysql_pay_table;
	global $dtcshared_path;

	global $cc_code_array;

	global $lang;

	global $txt_please_tell_if_info_not_ok;
	global $txt_refund_my_account;
	global $txt_remaining_money;
	global $txt_renew_my_account_button;
	global $txt_upgrade_my_account_button;
	global $txt_expiration_date;
	global $txt_allowed_data_transfer;
	global $txt_storage_space;
	global $txt_your_hosting_account;
	global $txt_total_disk_usage;
	global $txt_transfer_this_month;
	global $txt_transfer_du;
	global $txt_draw_client_info_familyname;
	global $txt_draw_client_info_firstname;
	global $txt_draw_client_info_comp_name;
	global $txt_draw_client_info_addr;
	global $txt_draw_client_info_zipcode;
	global $txt_draw_client_info_country;
	global $txt_draw_client_info_city;
	global $txt_draw_client_info_state;
	global $txt_draw_client_info_phone;
	global $txt_draw_client_info_fax;
	global $txt_draw_client_info_email;

	$frm_start = "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
";

	$out = "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";

	$id_client = $admin["info"]["id_client"];

	$stats = fetchAdminStats($admin);

	if(isset($_REQUEST["action"]) && $id_client != 0 && $_REQUEST["action"] == "upgrade_myaccount"){
		return draw_UpgradeAccount($admin);
	}

	if(isset($_REQUEST["action"]) && $id_client != 0 && $_REQUEST["action"] == "refund_myaccount"){
		$q = "INSERT INTO $pro_mysql_pay_table (id,id_client,id_command,label,currency,refund_amount,date,time)VALUES('','$id_client','0','Refund my account','USD','".$_REQUEST["refund_amount"]."','".date("Y-m-j")."','".date("H:i:s")."');";
		$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error()." in file ".__FILE__." line ".__LINE__);
		$payid = mysql_insert_id();
		$out .= "<b><u>Pay \$".$_REQUEST["refund_amount"]." on my account:</u></b><br>";
		$out .=" Please click on the button bellow to refund your account. Then,
when paiement is done, click the refresh button.";
		$out .= "<center><font size=\"+1\">\$".$_REQUEST["refund_amount"]."</font><br>".
		paynowButton($payid,$_REQUEST["refund_amount"]);
		$out .= "<br><br>$frm_start<input type=\"submit\" value=\"Refresh and see my account\"></form></center>";
		return $out;
	}

	$out .= "<b><u>".$txt_transfer_du[$lang]."</u></b>";
	// Draw overall this month usage
	$overall = "<br>".$txt_transfer_this_month[$lang].smartByte($stats["total_transfer"]);
	if($id_client != 0){
		$bw_quota = $admin["info"]["bandwidth_per_month_mb"]*1024*1024;
		$overall .= " / ".smartByte($bw_quota)."<br>";
		$overall .= drawPercentBar($stats["total_transfer"],$bw_quota);
	}
	$overall .= $txt_total_disk_usage[$lang].smartByte($stats["total_du"]);
	if($id_client != 0){
		$du_quota = $admin["info"]["quota"]*1024*1024;
		$overall .= " / ".smartByte($du_quota)."<br>";
		$overall .= drawPercentBar($stats["total_du"],$du_quota);

		$client = $admin["client"];
	}

	if($id_client != 0){
		$out .= '<table><td>'.$overall.'</td><td><img src="bw_per_month.php?cid='.$id_client.'&adm_login='.$adm_login.'"></td></tr></table>';
	}else{
		$out .= $overall;
	}

	if($id_client != 0){
		$out .= "<br><b><u>".$txt_your_hosting_account[$lang]."</u></b>";
		$out .= "<table width=\"100%\" height=\"1\" cellpadding=\"4\" cellspacing=\"0\" border=\"1\">
<tr>
	<td><b>".$txt_storage_space[$lang]."</b></td><td><b>".$txt_allowed_data_transfer[$lang]."</b></td><td><b>".$txt_expiration_date[$lang]."</b></td>
</tr>
<tr>
	<td>".smartByte($du_quota)."</td><td>".smartByte($bw_quota)."</td><td>".$admin["info"]["expire"]."</td>
</tr>
</table>";
		if(file_exists($dtcshared_path."/dtcrm")){
			$out .= "<br><center>$frm_start<input type=\"hidden\" name=\"action\" value=\"upgrade_myaccount\">
<input type=\"submit\" value=\"".$txt_upgrade_my_account_button[$lang]."\">
</form>";
			$out .= "$frm_start<input type=\"hidden\" name=\"action\" value=\"renew_myaccount\">
<input type=\"submit\" value=\"".$txt_renew_my_account_button[$lang]."\">
</form></center><br>";
		}

		$out .=  "<b><u>".$txt_remaining_money[$lang]."</u></b><br>
<table width=\"100%\" height=\"1\" cellpadding=\"4\" cellspacing=\"0\" border=\"0\">
<tr>
	<td><font size=\"+1\">\$".$client["dollar"]."</font></td>
	<td><font size=\"-1\">".$txt_refund_my_account[$lang]."</font><br>
$frm_start<input type=\"hidden\" name=\"action\" value=\"refund_myaccount\">
\$<input size=\"8\" type=\"text\" name=\"refund_amount\" value=\"\">
<input type=\"submit\" value=\"Ok\">
</form></td></tr>
</table>
<hr width=\"90%\">
";

		$out .= "<center><b>".$txt_please_tell_if_info_not_ok[$lang]."</b></center>";

		if($client["is_company"] == "yes"){
			$out .= $txt_draw_client_info_comp_name[$lang].$client["company_name"]."<br>";
		}

		$out .= $txt_draw_client_info_firstname[$lang].$client["christname"]."<br>";
		$out .= $txt_draw_client_info_familyname[$lang].$client["familyname"]."<br>";
		$out .= $txt_draw_client_info_addr[$lang].$client["addr1"]."<br>";
		$out .= $client["addr2"]."<br>";
		$out .= $txt_draw_client_info_zipcode[$lang].$client["zipcode"]."<br>";
		$out .= $txt_draw_client_info_city[$lang].$client["city"]."<br>";
		$out .= $txt_draw_client_info_state[$lang].$client["state"]."<br>";
		$out .= $txt_draw_client_info_country[$lang].$cc_code_array[ $client["country"] ] ."<br>";
		$out .= $txt_draw_client_info_phone[$lang].$client["phone"]."<br>";
		$out .= $txt_draw_client_info_fax[$lang].$client["fax"]."<br>";
		$out .= $txt_draw_client_info_email[$lang].$client["email"]."<br>";
	}else{
		$out .= "You do not have a client account, so there
is no money in your account.";
	}
	return $out;

}


?>
