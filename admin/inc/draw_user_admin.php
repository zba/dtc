<?php

function deleteTicketThread($delete_me){
	global $pro_mysql_tik_queries_table;

	$a = array();
	$a["in_reply_of_id"] = $delete_me;
	while($a["in_reply_of_id"] != 0){
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE id='".$a["in_reply_of_id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("Cannot find ticket ".$a["in_reply_of_id"]." when willing to search head thread line ".__LINE__." file ".__FILE__);
		}
		$a = mysql_fetch_array($r);
	}
	$head = $a["id"];
	$next = $a["reply_id"];
	$q = "DELETE FROM $pro_mysql_tik_queries_table WHERE id='$head';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	while($next != 0){
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE id='$next';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("Cannot find ticket ".$a["in_reply_of_id"]." when willing to delete thread line ".__LINE__." file ".__FILE__);
		}
		$a = mysql_fetch_array($r);
		$head = $next;
		$next = $a["reply_id"];
		$q = "DELETE FROM $pro_mysql_tik_queries_table WHERE id='$head';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	}
}

function calculateAge($date,$time){
	$exp_date = explode("-",$date);
	$exp_time = explode(":",$time);
	$timestamp = mktime($exp_time[0],$exp_time[1],$exp_time[2],$exp_date[1],$exp_date[2],$exp_date[0]);
	$age_timestamp = mktime() - $timestamp;
	$age =  round($age_timestamp/(60*60*24)) ." j " .round(($age_timestamp/(60*60))%24) ." h " .round(($age_timestamp/60)%60) ." m " . $age_timestamp%60 . " s";
	return $age;
}

function numOfDays($date,$time="00:00:00"){
	$exp_date = explode("-",$date);
	$exp_time = explode(":",$time);
	$timestamp = mktime($exp_time[0],$exp_time[1],$exp_time[2],$exp_date[1],$exp_date[2],$exp_date[0]);
	$age_timestamp = mktime() - $timestamp;
	$age =  round($age_timestamp/(60*60*24));
	return $age;
}

function mailUserTicketReply($adm_email,$hash,$subject,$body,$closed="no",$adm_login=""){
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $pro_mysql_tik_admins_table;
	global $conf_webmaster_email_addr;
	global $conf_administrative_site;

	global $conf_message_subject_header;
	global $conf_main_domain;

	global $conf_support_ticket_email;
	global $conf_support_ticket_domain;
	global $conf_recipient_delimiter;
	global $conf_main_domain;

	global $send_email_header;
	global $pro_mysql_tik_admins_table;

	if($conf_support_ticket_domain == "default"){
		$support_domain = $conf_main_domain;
	} else {
		$support_domain = $conf_support_ticket_domain;
	}

	$support_email = $conf_support_ticket_email.$conf_recipient_delimiter.$hash."@".$support_domain;
	$headers = $send_email_header;
	$headers .= "From: $conf_support_ticket_email@$support_domain <$support_email>";
	$header_admin_reply = readCustomizedMessage("tickets/header_admin_reply",$adm_login);
	$content = "Subject: ".stripslashes($subject)."

$header_admin_reply
**********
$body
**********
";

	if($closed == "no"){
		$text_filename = "tickets/footer_admin_reply_no_close";
	}else{
		$text_filename = "tickets/footer_admin_reply_close";
	}
	$footer_admin_reply = readCustomizedMessage($text_filename,$adm_login);
	$footer_admin_reply = str_replace("%%%DTC_CLIENT_URL%%%","http://$conf_administrative_site/dtc/",$footer_admin_reply);
	$footer_admin_reply = str_replace("%%%SUPPORT_EMAIL_ADDRESS%%%",$support_email,$footer_admin_reply);

	$content .= $footer_admin_reply;

	$tocustomer_subject = readCustomizedMessage("tickets/subject_admin_reply",$adm_login);

	$q = "SELECT * FROM $pro_mysql_tik_admins_table WHERE pseudo='".$_SERVER["PHP_AUTH_USER"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Ticket admin not found line ".__LINE__." file ".__FILE__);
	}
	$a = mysql_fetch_array($r);
	$realname = $a["realname"];
	mail($adm_email,$conf_message_subject_header." ".$realname." ".$tocustomer_subject,$content,$headers);

	// Mail the ticket reply to all administrators
	$adm_content = "Subject: ".stripslashes($subject)."

Hello,

An administrator has replied to a support ticket. Below is a copy of
his reply to the customer:

**********
$body
**********

The administrator decided that the issue is:

";
	if($closed == "no"){
		$adm_content .= "OPEN TO FURTHER DISCUSSION\n";
	}else{
		$adm_content .= "CLOSED\n";
	}

	// Use email if login is empty (case of an admin email not in the DB)
	if ( $adm_login == "" ){
		$subject_line_adm_name = $adm_email;
	}else{
		$subject_line_adm_name = $adm_login;
	}

	$q = "SELECT * FROM $pro_mysql_tik_admins_table WHERE available='yes';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		mail($a["email"],"$conf_message_subject_header ".$_SERVER["PHP_AUTH_USER"]." replied to the support ticket of ".$subject_line_adm_name,$adm_content,$headers);
	}
}

function drawNewAdminForm(){
	global $conf_site_root_host_path;
	global $lang;

	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
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

	global $secpayconf_currency_letters;
	global $secpayconf_use_maxmind;

	get_secpay_conf();

	$out = "";
	// Resolve support ticket stuff
	if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "resolv_ticket"){
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE id='".$_REQUEST["tik_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			return _("Cannot find ticket!");
		}
		$a = mysql_fetch_array($r);
		$out .= _("Subject: ") .htmlspecialchars(stripslashes($a["subject"]))."<br>";

		$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a["cat_id"]."';";
		$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n2 = mysql_num_rows($r2);
		if($n2 != 1){
			$tmp = _("Type not found!");
		}else{
			$a2 = mysql_fetch_array($r2);
			$tmp = $a2["catdescript"]."<br>";
		}
		$out .= _("Type:") .$tmp;
		$out .= _("First query date: ") .$a["date"]." ".$a["time"]."<br>";
		$out .= _("Server hostname related: ") .$a["server_hostname"]."<br>";
		$out .= _("Admin login: ") .$a["adm_login"]."<br><br>";
		
		$out .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"0\">";
		$next_tikq = $_REQUEST["tik_id"];
		$close_request = "no";
		while($next_tikq != 0){
			$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='".$a["adm_login"]."' AND id='$next_tikq';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				$out .= _("Cannot find ticket!");
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
			if($a["admin_or_user"] == "admin"){
                        	$replied_by = "<br>"._("Replied by:")." ".$a["admin_name"];
			}else{
				$replied_by = "";
			}
			$out .= "<tr><td$bg valign=\"top\"><i>".$a["date"]." ".$a["time"]."</i>".$replied_by."</td><td$bg>".nl2br(htmlspecialchars(stripslashes($a["text"])))."</td></tr>";
			if($a["request_close"] == "yes"){
				$close_request = "yes";
			}
		}
		$out .= "</table>";
		$out .= _("Request to close the ticket: ");
		if($close_request == "yes"){
			$out .= "<font color=\"#00FF00\">"._("Yes")."</font><br>";
		}else{
			$out .= "<font color=\"#FF0000\">"._("No")."</font><br>";
		}
		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
		<input type=\"hidden\" name=\"subaction\" value=\"ticket_reply\">
		<textarea cols=\"100\" rows=\"10\" wrap=\"physical\" name=\"ticketbody\"></textarea><br>
		<input type=\"hidden\" name=\"tik_id\" value=\"".$_REQUEST["tik_id"]."\">
		<input type=\"hidden\" name=\"server_hostname\" value=\"".$a["server_hostname"]."\">
		<input type=\"hidden\" name=\"last_tik_id\" value=\"$last_tik\">
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"answer\" value=\"". _("Send reply") ."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"answer_close\" value=\"". _("Send reply and close ticket") ."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"close\" value=\"". _("Close without reply") ."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" name=\"delete_thread\" value=\"". _("Delete thread silently") ."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
		</form>";
		return $out;
	}
	// Reply to support ticket stuff
	if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "ticket_reply"){
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE id='".$_REQUEST["tik_id"]."';";
		$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			return _("Cannot find ticket!");
		}
		$a = mysql_fetch_array($r);
		if(isset($_REQUEST["answer"])){
			$closed = "no";
		}else{
			$closed = "yes";
		}
		$adm_login = $a["adm_login"];
		if( strlen($adm_login) != 0){
			$q = "SELECT * FROM $pro_mysql_admin_table WHERE adm_login='$adm_login';";
			$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				return "Admin not found!";
			}
			$admin = mysql_fetch_array($r);
			$q = "SELECT * FROM $pro_mysql_client_table WHERE id='".$admin["id_client"]."';";
			$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				return "Admin not found!";
			}
			$client = mysql_fetch_array($r);
		}else{
			$adm_login = "";
		}
		if( isset($_REQUEST["delete_thread"]) ){
			deleteTicketThread($_REQUEST["tik_id"]);
			$closed = "no";
		}
		if(isset($_REQUEST["answer"]) || isset($_REQUEST["answer_close"])){
			$qps = "SELECT * FROM $pro_mysql_tik_admins_table WHERE pseudo='".$_SERVER["PHP_AUTH_USER"]."';";
			$rps = mysql_query($qps)or die("Cannot query $qps line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$nps = mysql_num_rows($rps);
			if($nps != 1){
				die("Ticket admin not found line ".__LINE__." file ".__FILE__);
			}
			$aps = mysql_fetch_array($rps);
			$pseudo = $aps["pseudo"];

			$q2 = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,in_reply_of_id,reply_id,admin_or_user,subject,text,cat_id,initial_ticket,server_hostname,closed,admin_name)
			VALUES ('','".$a["adm_login"]."','".date("Y-m-d")."','".date("H:i:s")."','".$_REQUEST["last_tik_id"]."','0','admin','".mysql_real_escape_string($a["subject"])."','".mysql_real_escape_string($_REQUEST["ticketbody"])."','".$a["cat_id"]."','no','".$a["server_hostname"]."','$closed','$pseudo');";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$ins_id = mysql_insert_id();
			$q2 = "UPDATE $pro_mysql_tik_queries_table SET reply_id='$ins_id' WHERE id='".$_REQUEST["last_tik_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$out .= "Ticket reply sent!<br>";
			if( strlen($adm_login) != 0){
				mailUserTicketReply($client["email"],$a["hash"],$a["subject"],$_REQUEST["ticketbody"],$closed,$adm_login);
			}
			if( strlen($a["customer_email"]) != 0){
				mailUserTicketReply($a["customer_email"],$a["hash"],$a["subject"],$_REQUEST["ticketbody"],$closed,$adm_login);
			}
		}
		if($closed == "yes"){
			$q2 = "UPDATE $pro_mysql_tik_queries_table SET closed='yes' WHERE id='".$_REQUEST["tik_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		}
		if( isset($_REQUEST["close"]) ){
			if( strlen($adm_login) != 0){
				mailUserTicketReply($client["email"],$a["hash"],"The ticket has been closed (without text reply)","The ticket has been closed (without text reply)",$closed,$adm_login);
			}
			if( strlen($a["customer_email"]) != 0){
				mailUserTicketReply($a["customer_email"],$a["hash"],"The ticket has been closed (without text reply)","The ticket has been closed (without text reply)",$closed,$adm_login);
			}
		}
	}

	// Draw the form for making a new admin
	$add_a_user = "<h3>". _("Add a new user") ."</h3>
<form name=\"addnewuser_frm\" action=\"?\" method=\"post\">
<input type=\"hidden\" name=\"newadminuser\" value=\"Ok\">
".dtcFormTableAttrs().
dtcFormLineDraw(_("Login:"),"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"newadmin_login\" value=\"\">").
dtcFormLineDraw(_("Password:"),"<input class=\"dtcDatagrid_input_alt_color\" type=\"password\" name=\"newadmin_pass\" value=\"\">".autoGeneratePassButton("addnewuser_frm","newadmin_pass"),0).
dtcFormLineDraw(_("Path:"),"<input class=\"dtcDatagrid_input_color\" type=\"text\" name=\"newadmin_path\" value=\"$conf_site_root_host_path\">").
dtcFromOkDraw()."
</form>
</table>
";

	if($secpayconf_use_maxmind == "yes"){
		$maxmindsays_th = "<td>" . _("MaxMind says") . "</td>";
	}else{
		$maxmindsays_th = "";
	}
	// Draw the list of users awaiting for an account
	$waiting_new_users = "<h3>". _("User and domain waiting for addition:") ."</h3>";
	$q = "SELECT * FROM $pro_mysql_new_admin_table ORDER BY date,time";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>". _("No user waiting!") ."</b>";
	}else{
		$waiting_new_users .= "<table width=\"100%\"border=\"1\">
<tr><td>". _("Name") ."</td><td>". _("Login") ."</td><td>". _("Domain name / VPS server hostname") ."</td><td>". _("Product") ."</td><td>". _("Date") . "</td><td>". _("Bank validated") ."</td>$maxmindsays_th<td>". _("Action") ."</td></tr>";
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
				$dom_name = _("Cannot find product in db!");
				$prod_name = _("Cannot find product in db!");
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
			$waiting_new_users .= "<td>".$a["date"]." ".$a["time"]."<br>".calculateAge($a["date"],$a["time"])."</td>";
			if($a["paiement_id"] == 0){
				$waiting_new_users .= "<td>". _("No pay ID!") ."</td>";
			}else{
				$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$a["paiement_id"]."';";
				$r2 = mysql_query($q)or die("Cannot select $q line: ".__LINE__." file: ".__FILE__." sql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1)	echo "Numrows!=1 in $q line: ".__LINE__." file: ".__FILE__." : problems with sql tables !";
				$a2 = mysql_fetch_array($r2);
				if($a2["valid"] == "yes"){
					$waiting_new_users .= "<td><font color=\"green\">"._("Yes")."</font></td>";
				}elseif($a2["valid"] == "pending"){
					$waiting_new_users .= "<td><font color=\"#FF8800\">". _("Pending") .": ". $a2["pending_reason"] ."</font></td>";
				}else{
					$waiting_new_users .= "<td><font color=\"red\">"._("No")."</font></td>";
				}
			}
			if($secpayconf_use_maxmind == "yes"){
				$waiting_new_users .= "<td><pre style='width: 200px; height: 100px; overflow: scroll;'>".htmlspecialchars(
					print_r(unserialize($a["maxmind_output"]),true))."</pre></td>";
			}
			$waiting_new_users .= "<td style=\"white-space:nowrap\"><a target=\"_blank\" href=\"/dtcadmin/view_waitingusers.php?reqadm_id=".$a["id"]."\">". _("View details") ."</a><br/>
			<a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_user&reqadm_id=".$a["id"]."\">". _("Add") ."</a><br/>
			<a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_user&reqadm_id=".$a["id"]."\">". _("Delete") ."</a></td>";
			$waiting_new_users .= "</tr>";
		}
		$waiting_new_users .= "</table>";
	}

	// Draw the list of domains awaiting to be add to users
	$q = "SELECT * FROM $pro_mysql_pending_queries_table";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<br><b>". _("No domain waiting!") ."</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
	<tr><td>". _("Login") ."</td><td>". _("Domain name") ."</td><td>". _("Action") ."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<td>".$a["adm_login"]."</td>";
			$waiting_new_users .= "<td>".$a["domain_name"]."</td>";
			$waiting_new_users .= "<td><a href=\"".$_SERVER["PHP_SELF"]."?action=valid_waiting_domain_to_user&reqid=".$a["id"]."\">". _("Add") ."</a>
- <a href=\"".$_SERVER["PHP_SELF"]."?action=delete_waiting_domain_to_user&reqid=".$a["id"]."\">". _("Delete") ."</a></td></tr>";
		}
		$waiting_new_users .= "</table>";
	}

	// Draw the list of pending renewals
	$q = "SELECT * FROM $pro_mysql_pending_renewal_table ORDER BY renew_date,renew_time";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>". _("No pending renewals!") ."</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
<tr><td>". _("Login")."</td><td>". _("Product") ."</td><td>". _("Payment date") ."</td><td>". _("Bank validated") ."</td><td>". _("Type") ."</td><td>". _("Action") ."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$waiting_new_users .= "<tr><td>".$a["adm_login"]."</td>";
			$q2 = "SELECT name,price_dollar,period FROM $pro_mysql_product_table WHERE id='".$a["product_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$prod_name = _("Cannot find product!");
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
				$bank = _("Cannot find payment!");
			}else{
				$a2 = mysql_fetch_array($r2);
				switch($a2["valid"]){
				case "yes":
					$bank = "<font color=\"green\">"._("Yes")."</font>";
					break;
				default:
				case "no":
					$bank = "<font color=\"red\">"._("No")."</font>";
					break;
				case "pending":
					$bank = "<font color=\"#FF8800\">". _("Pending") .": ".$a2["pending_reason"]."</font>";
					break;
				}
			}
			$waiting_new_users .= "<td>$bank</td>";
			switch($a["heb_type"]){
			case "vps":
				$q2 = "SELECT * FROM $pro_mysql_vps_table WHERE id='".$a["renew_id"]."'";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
				if($n2 != 1){
					$heb_type = _("VPS: Cannot find VPS in db!");
				}else{
					$a2 = mysql_fetch_array($r2);
					$heb_type = "VPS: ".$a2["vps_xen_name"]."@".$a2["vps_server_hostname"];
				}
				break;
			case "shared":
			case "ssl":
				$heb_type = _("Shared");
				break;
			case "ssl":
				$heb_type = _("SSL Token purchase");
				break;
			case "ssl_renew":
				$heb_type = _("SSL Token renewal");
				break;
			case "server":
				$q2 = "SELECT * FROM $pro_mysql_dedicated_table WHERE id='".$a["renew_id"]."'";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
				if($n2 != 1){
					$tmp = _("Cannot find server in db!");
				}else{
					$a2 = mysql_fetch_array($r2);
					$tmp = $a2["server_hostname"];
				}
				$heb_type = _("Server:") .$tmp;
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
	// Ticket manager: draw all open tickets
	$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE closed='no' AND initial_ticket='yes' ORDER BY `date`,`time`;";
	$r = mysql_query($q)or die("Cannot query \"$q\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n < 1){
		$waiting_new_users .= "<b>". _("No pending support tickets!") ."</b><br>";
	}else{
		$waiting_new_users .= "<table border=\"1\">
<tr><td>". _("Login") ."</td><td>". _("Age") ."</td><td>". _("Type") ."</td><td>". _("Subject") ."</td><td>". _("Last message from"). "</td><td>" ._("Last message age"). "</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			if( strlen($a["customer_email"]) != 0){
				$who = $a["customer_email"];
				if( strlen($a["adm_login"]) != 0){
					$who .= " / ".$a["adm_login"];
				}
			}else{
				$who = $a["adm_login"];
			}
			$waiting_new_users .= "<tr><td>$who</td>";
			$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a["cat_id"]."'";
			$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$cat = _("Type not found!");
			}else{
				$a2 = mysql_fetch_array($r2);
				$cat = $a2["catname"];
			}
			$age = calculateAge($a["date"],$a["time"]);
			$waiting_new_users .= "<td style=\"white-space:nowrap;\">$age</td><td>$cat</td><td style=\"white-space:nowrap;\"><a href=\"".$_SERVER["PHP_SELF"]."?subaction=resolv_ticket&tik_id=".$a["id"]."\">".htmlspecialchars(stripslashes($a["subject"]))."</a></td>";
			$next_reply_id = $a["reply_id"];
			$last_reply_text = "<font color=\"green\">". _("Admin"). "</font>";
			$last_message_date = $a["date"];
			$last_message_time = $a["time"];
			$loop_num = 0;
			$last_guy_replied = "user";
			while($next_reply_id != 0 && $loop_num < 49){
				$loop_num++;
				$q2 = "SELECT * FROM $pro_mysql_tik_queries_table WHERE id='$next_reply_id';";
				$r2 = mysql_query($q2)or die("Cannot query \"$q2\" ! Line: ".__LINE__." in file: ".__FILE__." mysql said: ".mysql_error());
				$n2 = mysql_num_rows($r2);
				if($n2 != 1){
					echo "Warning: couldn't find tik query $next_reply_id in last reply detection!";
					break;
				}
				$a3 = mysql_fetch_array($r2);
				$last_message_date = $a3["date"];
				$last_message_time = $a3["time"];
				if($a3["admin_or_user"] == "user"){
					$last_guy_replied = "user";
				}else{
					$last_guy_replied = "admin";
				}
				$next_reply_id = $a3["reply_id"];
				if($loop_num >= 49){
					echo "Warning: loop_num exeeded 50, not displaying last ticket reply from line".__LINE__." file ".__FILE__;
				}
			}
			if($last_guy_replied == "user"){
				$last_reply_text = "<font color=\"red\">". _("User") ."</font>";
			}
			$waiting_new_users .= "<td>$last_reply_text</td>";
			$age2 = calculateAge($last_message_date,$last_message_time);
			$waiting_new_users .= "<td>".$age2."</td>";
			$waiting_new_users .= "</tr>";
		}
		$waiting_new_users .= "</table>";
	}
	return "<table>
<tr>
	<td valign=\"top\">".$waiting_new_users."</td>
	</tr><tr>
	<td valign=\"top\">".$add_a_user."</td>
</tr></table>";
}

function skinConsole(){
	global $HTTP_HOST;
	global $console;
	// added by seeb

	return "<table bgcolor=\"#000000\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" width=\"100%\" height=\"100%\">
<tr>
<td>
	<font color=\"#FFFFFF\">". _("Console output") ."</font>
</td>
</tr>
<tr>
<td><pre>
<font color=\"#FFFFFF\">".$_SERVER["HTTP_HOST"].":&gt;<br><span id=\"console_content\" class=\"console_content\">$console</span></font></pre>
</td>
</tr>
</table>
";
}

?>
