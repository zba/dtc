<?php

require("$dtcshared_path/inc/forms/ticket_strings.php");

function drawTickets($admin){
	global $lang;
	global $pro_mysql_tik_admins_table;
	global $pro_mysql_tik_queries_table;
	global $pro_mysql_tik_cats_table;

	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $conf_administrative_site;

	global $lang;
	global $txt_ticket_subject;
	global $txt_ticket_submit_new_support_issue;
	global $txt_ticket_what_is_your_srv_hostname;
	global $txt_ticket_type_of_problem;
	global $txt_ticket_full_description_of_the_trouble;
	global $txt_ticket_send_trouble_ticket_button;
	global $txt_ticket_ticket_not_found;
	global $txt_ticket_type_type_not_found;
	global $txt_ticket_request_to_close_the_ticket;
	global $txt_ticket_old_tickets;
	global $txt_ticket_date;
	global $txt_ticket_time;
	global $txt_ticket_status;
	global $txt_ticket_type2;
	global $txt_ticket_hostname;
	global $txt_ticket_subject;
	global $txt_ticket_open;
	global $txt_ticket_closed;

	$out = "<br>";

	// New ticket form
	if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "new_ticket"){
		$popup_hostname = "";
		if(isset($admin["data"])){
			$popup_hostname .= "<option value=\"$conf_administrative_site\">$conf_administrative_site</option>";
		}
		if(isset($admin["vps"])){
			$nbr_vps = sizeof($admin["vps"]);
			for($i=0;$i<$nbr_vps;$i++){
				$vps_name = $admin["vps"][$i]["vps_server_hostname"].":".$admin["vps"][$i]["vps_xen_name"];
				$popup_hostname .= "<option value=\"$vps_name\">$vps_name</option>";
			}
		}

		$q = "SELECT * FROM $pro_mysql_tik_cats_table WHERE 1 ORDER BY id";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$popup_cats = "";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$popup_cats .= "<option value=\"".$a["id"]."\">".$a["catdescript"]."</option>";
		}

		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"new_ticket\">

".$txt_ticket_subject[$lang]." <input name=\"subject\" type=\"text\" size=\"40\" maxlength=\"40\"><br>

".$txt_ticket_what_is_your_srv_hostname[$lang]."<br>
<select name=\"server_hostname\">
$popup_hostname
</select><br>

".$txt_ticket_type_of_problem[$lang]."<br>
<select name=\"issue_cat_id\">
$popup_cats
</select><br><br>

".$txt_ticket_full_description_of_the_trouble[$lang]."<br>
<textarea name=\"ticketbody\" cols=\"60\" rows=\"10\" wrap=\"physical\"></textarea><br><br>

<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"".$txt_ticket_send_trouble_ticket_button[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
</form>";
	// View a ticket
	}else if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "view_ticket"){
		if( !isRandomNum($_REQUEST["tik_id"]) ){
			die("Selected ticket id is not valid!");
		}
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='$adm_login' AND id='".$_REQUEST["tik_id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$out .= $txt_ticket_ticket_not_found[$lang];
		}else{
			$a_t = mysql_fetch_array($r);
			$out .= $txt_ticket_subject[$lang]." ".stripslashes($a_t["subject"])."<br>";

			$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a_t["cat_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$out .= $txt_ticket_type_type_not_found[$lang];
			}else{
				$a2 = mysql_fetch_array($r2);
				$out .= $txt_ticket_type_of_problem[$lang]." ".$a2["catdescript"]."<br>";
			}
			$out .= "First query date: ".$a_t["date"]." ".$a_t["time"]."<br>";
			$out .= "Server hostname related: ".$a_t["server_hostname"]."<br>";

			if($a_t["closed"] == "yes"){
				$out .= "<font color=\"red\">Replying to an already closed ticket will reopen it.</font><br>";
			}
			
			$out .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"0\">";
			$next_tikq = $_REQUEST["tik_id"];
			while($next_tikq != 0){
				$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='$adm_login' AND id='$next_tikq';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1){
					$out .= $txt_ticket_ticket_not_found[$lang];
					break;
				}
				$a = mysql_fetch_array($r);
				$last_tik_id = $next_tikq;
				$next_tikq = $a["reply_id"];
				if($a["admin_or_user"] == "user"){
					$bg = " bgcolor=\"#AAAAFF\" ";
				}else{
					$bg = " bgcolor=\"#FFFFAA\" ";
				}
				$out .= "<tr><td$bg valign=\"top\"><i>".$a["date"]." ".$a["time"]."</i></td><td$bg>".nl2br(stripslashes($a["text"]))."</td></tr>";
			}
			$out .= "</table>";
			$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"subaction\" value=\"view_ticket\">
<input type=\"hidden\" name=\"action\" value=\"add_ticket_reply\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"tik_id\" value=\"".$_REQUEST["tik_id"]."\">
<input type=\"hidden\" name=\"last_tik_id\" value=\"$last_tik_id\">
<input type=\"hidden\" name=\"subject\" value=\"".$a_t["subject"]."\">
<input type=\"hidden\" name=\"cat_id\" value=\"".$a_t["cat_id"]."\">
<input type=\"hidden\" name=\"server_hostname\" value=\"".$a_t["server_hostname"]."\">
<textarea name=\"ticketbody\" cols=\"60\" rows=\"10\" wrap=\"physical\"></textarea><br>
".$txt_ticket_request_to_close_the_ticket[$lang]."<input type=\"radio\" name=\"request_to_close\" value=\"yes\" checked> "._("Yes")."
<input type=\"radio\" name=\"request_to_close\" value=\"no\"> "._("No")."<br>
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"".$txt_ticket_submit_new_support_issue[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
</form>
";
		}
	// The main screen
	}else{
		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"subaction\" value=\"new_ticket\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"".$txt_ticket_submit_new_support_issue[$lang]."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
</form>
";
		$out .= "<br><br><h3>".$txt_ticket_old_tickets[$lang]."</h3>";
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='$adm_login' AND in_reply_of_id='0' ORDER BY date,time DESC;";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
	<tr><td>".$txt_ticket_date[$lang]."</td><td>".$txt_ticket_time[$lang]."</td><td>".$txt_ticket_status[$lang]."</td><td>".$txt_ticket_type2[$lang]."</td><td>".$txt_ticket_hostname[$lang]."</td><td>".$txt_ticket_subject[$lang]."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$out .= "<tr><td>".$a["date"]."</td><td>".$a["time"]."</td>";
			if($a["closed"] == "yes"){
				$out .= "<td><font color=\"green\">".$txt_ticket_closed[$lang]."</font></td>";
			}else{
				$out .= "<td><font color=\"red\">".$txt_ticket_open[$lang]."</font></td>";
			}
			$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a["cat_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$out .= "<td>Cat not found!</td>";
			}else{
				$a2 = mysql_fetch_array($r2);
				$out .= "<td>".$a2["catname"]."</td>";
			}
			$out .= "<td>".$a["server_hostname"]."</td><td><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&subaction=view_ticket&tik_id=".$a["id"]."\">".stripslashes($a["subject"])."</a></td>";
			$out .= "</tr>";
		}
		$out .= "</table>";
	}
	return $out;
}

?>
