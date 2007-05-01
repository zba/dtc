<?php
/**
 * @package DTC
 * @version $id:$
 * @return new admin menu
 */

function calculateAge($date,$time){
	$exp_date = explode("-",$date);
	$exp_time = explode(":",$time);
	$timestamp = mktime($exp_time[0],$exp_time[1],$exp_time[2],$exp_date[1],$exp_date[2],$exp_date[0]);
	$age_timestamp = mktime() - $timestamp;
	$age =  round($age_timestamp/(60*60*24)) ." j " .round(($age_timestamp/(60*60))%24) ." h " .round(($age_timestamp/60)%60) ." m " . $age_timestamp%60 . " s";
	return $age;
}

function mailUserTicketReply($adm_login,$subject,$body,$closed="no"){
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $conf_webmaster_email_addr;
	global $conf_administrative_site;

	$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		return "Admin not found!";
	}
	$a = mysql_fetch_array($r);
	$q = "SELECT * FROM $pro_mysql_client_table WHERE id='".$a["id_client"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		return "Admin not found!";
	}
	$a = mysql_fetch_array($r);
	$headers = "From: ".$conf_webmaster_email_addr;

	$content = "Subject: ".stripslashes($subject)."

Hello,

An administrator has replied to your support ticket.
Below is a copy of the reply sent by the administrator.

**********
$body
**********

Please DO NOT reply to this mail. In order to keep a history,
and enable us to reply faster and share the support work with
all the members of our team, use the control panel support
ticket tab to reply.

";

	if($closed == "yes"){
		$content .= "Note that the ticket is still open, meaning we are waiting
for your answer. So please login to the control panel at the
following URL:

http://$conf_administrative_site/dtc/

with your login $adm_login, then go in the support ticket tab
and type your reply.
";
	}else{
		$content .= "Note that the ticket has been closed, meaning that there is
no need for another reply. If you are still needing help, then
you must open a new support ticket. To do so, login to the
control panel at the following URL:

http://$conf_administrative_site/dtc/

with your login $adm_login, then go in the support
ticket tab and type your reply.
";
	}
	mail($a["email"],"[DTC] An administrator replied to your support ticket",$content,$headers);
}

function drawNewAdminForm(){
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_login_path;
	global $conf_site_root_host_path;
	global $lang;

	global $pro_mysql_new_admin_table;
	global $pro_mysql_pending_queries_table;
	global $pro_mysql_pay_table;
	global $pro_mysql_pending_renewal_table;
	global $pro_mysql_product_table;
	global $pro_mysql_vps_table;
	global $pro_mysql_tik_admins_table;
	global $pro_mysql_tik_queries_table;
	global $pro_mysql_tik_cats_table;
	global $pro_mysql_dedicated_table;

	global $txt_add_a_new_user;
	global $txt_userndomain_waiting_for_addition;
	global $txt_no_user_waiting;
	global $txt_no_domain_waiting;
	global $txt_login_title;
	global $txt_domain_tbl_config_dom_name;


	global $txt_dua_no_pending_renewals;
	global $txt_dua_product;
	global $txt_dua_payment_date;
	global $txt_dua_type;
	global $txt_action;
	global $txt_cannot_find_payment;

	global $txt_dua_cannot_find_vps_in_db;
	global $txt_dua_shared;
	global $txt_dua_ssl_token_purchase;
	global $txt_dua_ssl_token_renewal;
	global $txt_dua_cannot_find_server;
	global $txt_dua_server;
	global $txt_dua_no_pending_support_tickets;
	global $txt_dua_age;
	global $txt_dua_subject;
	global $txt_dua_cannot_find_ticket;

	global $txt_dua_cannot_find_ticket;
	global $txt_dua_subject2;
	global $txt_dua_type2;
	global $txt_dua_ticket_type_not_found;
	global $txt_dua_request_to_close_the_ticket;
	global $txt_yes;
	global $txt_no;
	global $txt_dua_back_to_pending_requests;
	global $txt_dua_domain_name_vps_server_hostname;
	global $txt_dua_name;	

	global $txt_dua_send_reply;
	global $txt_dua_send_reply_and_close_ticket;
	global $txt_dua_close_without_reply;
	global $txt_dua_cannot_find_product_in_db;

	global $txt_dua_del;
	global $txt_dua_add;
	global $txt_dua_view_details;
	global $txt_dua_bank_validated;

	global $secpayconf_currency_letters;

	get_secpay_conf();

	$out = "";
	if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "resolv_ticket"){
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE id='".$_REQUEST["tik_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			return $txt_dua_cannot_find_ticket[$lang];
		}
		$a = mysql_fetch_array($r);
		$out .= $txt_dua_subject2[$lang].stripslashes($a["subject"])."<br>";

		$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a["cat_id"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			$tmp = $txt_dua_ticket_type_not_found[$lang];
		}else{
			$a2 = mysql_fetch_array($r2);
			$tmp = $a2["catdescript"]."<br>";
		}
		$out .= $txt_dua_type2[$lang].$tmp;
		$out .= "First query date: ".$a["date"]." ".$a["time"]."<br>";
		$out .= "Server hostname related: ".$a["server_hostname"]."<br>";
		
		$out .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"0\">";
		$next_tikq = $_REQUEST["tik_id"];
		$close_request = "no";
		while($next_tikq != 0){
			$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='".$a["adm_login"]."' AND id='$next_tikq';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				$out .= $txt_dua_cannot_find_ticket[$lang];
				break;
			}
			$a = mysql_fetch_array($r);
			$last_tik = $next_tikq;
			$next_tikq = $a["reply_id"];
			if($a["admin_or_user"] == "user"){
				$bg = " bgcolor=\"#AAAAFF\" ";
			}else{
				$bg = " bgcolor=\"#FFFFAA\" ";
			}
			$out .= "<tr><td$bg valign=\"top\"><i>".$a["date"]." ".$a["time"]."</i></td><td$bg>".nl2br(stripslashes($a["text"]))."</td></tr>";
			if($a["request_close"] == "yes"){
				$close_request = "yes";
			}
		}
		$out .= "</table>";
		$out .= $txt_dua_request_to_close_the_ticket[$lang];
		if($close_request == "yes"){
			$out .= "<font color=\"#00FF00\">".$txt_yes[$lang]."</font><br>";
		}else{
			$out .= "<font color=\"#FF0000\">".$txt_no[$lang]."</font><br>";
		}
		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
		<input type=\"hidden\" name=\"subaction\" value=\"ticket_reply\">
		<textarea cols=\"60\" rows=\"10\" wrap=\"physical\" name=\"ticketbody\"></textarea><br>
		<input type=\"hidden\" name=\"tik_id\" value=\"".$_REQUEST["tik_id"]."\">
		<input type=\"hidden\" name=\"last_tik_id\" value=\"$last_tik\">
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"answer\" value=\"".$txt_dua_send_reply[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"answer_close\" value=\"".$txt_dua_send_reply_and_close_ticket[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"close\" value=\"".$txt_dua_close_without_reply[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		</form>";
		return $out;
	}
	if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "ticket_reply"){
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE id='".$_REQUEST["tik_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			return $txt_dua_cannot_find_ticket[$lang];
		}
		$a = mysql_fetch_array($r);
		if(isset($_REQUEST["answer"])){
			$closed = "no";
		}else{
			$closed = "yes";
		}
		if(isset($_REQUEST["answer"]) || isset($_REQUEST["answer_close"])){
			$q2 = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,in_reply_of_id,reply_id,admin_or_user,subject,text,cat_id,initial_ticket,server_hostname,closed)
			VALUES ('','".$a["adm_login"]."','".date("Y-m-d")."','".date("H:i:s")."','".$_REQUEST["last_tik_id"]."','0','admin','".$a["subject"]."','".addslashes($_REQUEST["ticketbody"])."','".$a["cat_id"]."','".$a["id"]."','".$a["server_hostname"]."','$closed');";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$ins_id = mysql_insert_id();
			$q2 = "UPDATE $pro_mysql_tik_queries_table SET reply_id='$ins_id' WHERE id='".$_REQUEST["last_tik_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$out .= "Ticket reply sent!<br>
				<form action=\"".$_SERVER["PHP_SELF"]."\">
				<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"submit\" value=\"".$txt_dua_back_to_pending_requests[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
				</form>";
			mailUserTicketReply($a["adm_login"],$a["subject"],$_REQUEST["ticketbody"],$closed);
		}
		if($closed == "yes"){
			$q2 = "UPDATE $pro_mysql_tik_queries_table SET closed='yes' WHERE id='".$_REQUEST["tik_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			mailUserTicketReply($a["adm_login"],"The ticket has been closed (without text reply)","yes");
		}
		return $out;
	}

	// Draw the form for making a new admin
	$add_a_user = "<h3>".$txt_add_a_new_user[$lang]."</h3>
<form name=\"addnewuser_frm\" action=\"?\" method=\"post\">
<input type=\"hidden\" name=\"newadminuser\" value=\"Ok\">
".dtcFormTableAttrs().
dtcFormLineDraw($txt_login_login[$lang],"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"newadmin_login\" value=\"\">").
dtcFormLineDraw($txt_login_pass[$lang],"<input class=\"dtcDatagrid_input_alt_color\" type=\"password\" name=\"newadmin_pass\" value=\"\">".autoGeneratePassButton("addnewuser_frm","newadmin_pass"),0).
dtcFormLineDraw($txt_login_path[$lang],"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"newadmin_path\" value=\"$conf_site_root_host_path\">").
dtcFromOkDraw()."
</form>
</table>
";

	// Draw the list of users awaiting for an account
	$waiting_new_users = "<h3>".$txt_userndomain_waiting_for_addition[$lang]."</h3>";
	$q = "SELECT * FROM $pro_mysql_new_admin_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>".$txt_no_user_waiting[$lang]."</b>";
	}else{
		$waiting_new_users .= "<table width=\"100%\"border=\"1\">
<tr><td>".$txt_dua_name[$lang]."</td><td>".$txt_login_title[$lang]."</td><td>".$txt_dua_domain_name_vps_server_hostname[$lang]."</td><td>".$txt_dua_product[$lang]."</td><td>".$txt_dua_bank_validated[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<tr><td style=\"white-space:nowrap\"><u>".$a["comp_name"].":</u><br>";
			$waiting_new_users .= $a["family_name"].", ".$a["first_name"]."</td>";
			$waiting_new_users .= "<td>".$a["reqadm_login"]."</td>";
			$prod_id = $a["product_id"];
			$q2 = "SELECT * FROM $pro_mysql_product_table WHERE id='$prod_id';";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$dom_name = $txt_dua_cannot_find_product_in_db[$lang];
				$prod_name = $txt_dua_cannot_find_product_in_db[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$prod_name = $a2["name"];
				if($a2["heb_type"] == "vps"){
					$dom_name = $a["vps_location"];
				}else{
					$dom_name = $a["domain_name"];
				}
			}
			$waiting_new_users .= "<td>$dom_name</td><td>$prod_name</td>";
			if($a["paiement_id"] == 0){
				$waiting_new_users .= "<td>No pay ID!</td>";
			}else{
				$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$a["paiement_id"]."';";
				$r2 = mysql_query($q)or die("Cannot select $q line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1)	echo "Numrows!=1 in $q line: ".__LINE__." file: ".__FILE__." : problems with sql tables !";
				$a2 = mysql_fetch_array($r2);
				if($a2["valid"] == "yes"){
					$waiting_new_users .= "<td><font color=\"green\">".$txt_yes[$lang]."</font></td>";
				}else{
					$waiting_new_users .= "<td><font color=\"red\">".$txt_no[$lang]."</font></td>";
				}
			}
			$waiting_new_users .= "<td style=\"white-space:nowrap\"><a target=\"_blank\" href=\"/dtcadmin/view_waitingusers.php?reqadm_login=".$a["reqadm_login"]."\">".$txt_dua_view_details[$lang]."</a> -
			<a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_user&reqadm_login=".$a["reqadm_login"]."\">".$txt_dua_add[$lang]."</a> -
			<a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_user&reqadm_login=".$a["reqadm_login"]."\">".$txt_dua_del[$lang]."</a></td>";
			$waiting_new_users .= "</tr>";
		}
		$waiting_new_users .= "</table>";
	}

	// Draw the list of domains awaiting to be add to users
	$q = "SELECT * FROM $pro_mysql_pending_queries_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<br><b>".$txt_no_domain_waiting[$lang]."</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
	<tr><td>".$txt_login_title[$lang]."</td><td>".$txt_domain_tbl_config_dom_name[$lang]."</td><td>Action</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<td>".$a["adm_login"]."</td>";
			$waiting_new_users .= "<td>".$a["domain_name"]."</td>";
			$waiting_new_users .= "<td><a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_domain_to_user&reqid=".$a["id"]."\">".$txt_dua_add[$lang]."</a>
- <a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_domain_to_user&reqid=".$a["id"]."\">".$txt_dua_del[$lang]."</a></td></tr>";
		}
		$waiting_new_users .= "</table>";
	}

	$q = "SELECT * FROM $pro_mysql_pending_renewal_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>".$txt_dua_no_pending_renewals[$lang]."</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
<tr><td>".$txt_login_title[$lang]."</td><td>".$txt_dua_product[$lang]."</td><td>".$txt_dua_payment_date[$lang]."</td><td>".$txt_dua_bank_validated[$lang]."</td><td>".$txt_dua_type[$lang]."</td><td>".$txt_action[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<tr><td>".$a["adm_login"]."</td>";
			$q2 = "SELECT name,price_dollar,period FROM $pro_mysql_product_table WHERE id='".$a["product_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$prod_name = "Cannot find product!";
			}else{
				$a2 = mysql_fetch_array($r2);
				$prod_name = $a2["name"]." (".$a2["price_dollar"]." $secpayconf_currency_letters: ".$a2["period"].")";
			}
			$waiting_new_users .= "<td>$prod_name</td>";
			$waiting_new_users .= "<td>".$a["renew_date"]." ".$a["renew_time"]."</td>";
			$q2 = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$a["pay_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$bank = $txt_dua_cannot_find_payment[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$bank = $a2["valid"];
			}
			$waiting_new_users .= "<td>$bank</td>";
			switch($a["heb_type"]){
			case "vps":
				$q2 = "SELECT * FROM $pro_mysql_vps_table WHERE id='".$a["renew_id"]."'";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
				if($n2 != 1){
					$heb_type = $txt_dua_cannot_find_vps_in_db[$lang];
				}else{
					$a2 = mysql_fetch_array($r2);
					$heb_type = "VPS: ".$a2["vps_xen_name"]."@".$a2["vps_server_hostname"];
				}
				break;
			case "shared":
			case "ssl":
				$heb_type = $txt_dua_shared[$lang];
				break;
			case "ssl":
				$heb_type = $txt_dua_ssl_token_purchase[$lang];
				break;
			case "ssl_renew":
				$heb_type = $txt_dua_ssl_token_renewal[$lang];
				break;
			case "server":
				$q2 = "SELECT * FROM $pro_mysql_dedicated_table WHERE id='".$a["renew_id"]."'";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
				if($n2 != 1){
					$tmp = $txt_dua_cannot_find_server[$lang];
				}else{
					$a2 = mysql_fetch_array($r2);
					$tmp = $a2["server_hostname"];
				}
				$heb_type = $txt_dua_server[$lang].$tmp;
				break;
			default:
				echo "Renew type ".$a["heb_type"]." not implemented line ".__LINE__." file ".__FILE__;
				break;
			}
			$waiting_new_users .= "<td>$heb_type</td>";
			$waiting_new_users .= "<td style=\"white-space:nowrap\"><a href=\"".$_SERVER["PHP_SELF"]."?action=validate_renewal&id=".$a["id"]."\">Validate</a> <a href=\"".$_SERVER["PHP_SELF"]."?action=delete_renewal&id=".$a["id"]."\">Del</a></td>";
			$waiting_new_users .= "</tr>";
		}
		$waiting_new_users .= "</table>";
	}
	// Ticket manager
	$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE closed='no' AND initial_ticket='yes';";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>".$txt_dua_no_pending_support_tickets[$lang]."</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
<tr><td>".$txt_login_title[$lang]."</td><td>".$txt_dua_age[$lang]."</td><td>".$txt_dua_type[$lang]."</td><td>".$txt_dua_subject[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<tr><td>".$a["adm_login"]."</td>";
			$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a["cat_id"]."'";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$cat = $txt_dua_ticket_type_not_found[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$cat = $a2["catname"];
			}
			$age = calculateAge($a["date"],$a["time"]);
			$waiting_new_users .= "<td style=\"white-space:nowrap;\">$age</td><td>$cat</td><td style=\"white-space:nowrap;\"><a href=\"".$_SERVER["PHP_SELF"]."?subaction=resolv_ticket&tik_id=".$a["id"]."\">".stripslashes($a["subject"])."</a></td></tr>";
		}
		$waiting_new_users .= "</table>";
	}
	return "<table>
<tr>
	<td valign=\"top\">".$add_a_user."</td>
	<td background=\"gfx/skin/frame/border_2.gif\">&nbsp;</td>
	<td valign=\"top\">".$waiting_new_users."</td>
</tr></table>";
}

function drawMySqlAccountManger(){
	global $lang;
	global $adm_login;
	global $adm_pass;
	global $conf_mysql_db;
	global $conf_demo_version;
	global $txt_mysqlmang_nouser_by_that_name;
	global $txt_mysqlmang_delete_a_db;
	global $txt_mysqlmang_add_a_db;
	global $txt_mysqlmang_db_name;
	global $txt_mysqlmang_not_in_demo;
	global $txt_delete_this_mysql_user_and_db;

	$out = "";

	// Retrive the infos from the database "mysql" that contains all the rights
	if($conf_demo_version == "no"){
		mysql_select_db("mysql")or die("Cannot select db mysql for account management  !!! Does your MySQL user/pass has the rights to it ?");
		$query = "SELECT * FROM user WHERE User='$adm_login';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows < 1){
			mysql_select_db($conf_mysql_db)or die("Cannot select DB $conf_mysql_db !!!");
			return $txt_mysqlmang_nouser_by_that_name[$lang];
		}else{
			$query = "SELECT Db FROM db WHERE User='$adm_login';";
			$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
			$num_rows = mysql_num_rows($result);
			for($i=0;$i<$num_rows;$i++){
				$row = mysql_fetch_array($result);
				$dblist[] = $row["Db"];
			}
			mysql_select_db($conf_mysql_db)or die("Cannot select DB $conf_mysql_db !!!");
			$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&action=delete_mysql_user\">".$txt_delete_this_mysql_user_and_db[$lang]."</a><br>
<b><u>".$txt_mysqlmang_delete_a_db[$lang]."</u></b><br>";
			for($i=0;$i<$num_rows;$i++){
				if($i != 0){
					$out .= " - ";
				}
				$out .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&action=delete_one_db&db_name=".$dblist[$i]."\">".$dblist[$i]."</a>";
			}
			$out .= "<br><br><b><u>".$txt_mysqlmang_add_a_db[$lang]."</u></b>
		<form action=\"".$_SERVER["PHP_SELF"]."\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
		".$txt_mysqlmang_db_name[$lang]."<input type=\"text\" name=\"new_mysql_database_name\" value=\"\">
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"new_mysql_database\" value=\"Ok\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		</form>";
			return $out;
		}
	}else{
		return $txt_mysqlmang_not_in_demo[$lang];
	}
}

function skinConsole(){
	global $HTTP_HOST;
	global $console;
	// added by seeb
	global $lang;
	global $txt_console_output;
	return "<table bgcolor=\"#000000\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" width=\"100%\" height=\"100%\">
<tr>
<td>
	<font color=\"#FFFFFF\">".$txt_console_output[$lang]."</font>
</td>
</tr>
<tr>
<td><pre>
<font color=\"#FFFFFF\">".$_SERVER["HTTP_HOST"].":&gt;<br>$console</font></pre>
</td>
</tr>
</table>
";
}

?>
