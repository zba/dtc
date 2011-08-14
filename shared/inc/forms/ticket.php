<?php

function drawTickets($admin){
	global $lang;
	global $pro_mysql_tik_admins_table;
	global $pro_mysql_tik_queries_table;
	global $pro_mysql_tik_cats_table;

	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $conf_administrative_site;

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
		if(isset($admin["dedicated"])){
			$nbr_dedicated = sizeof($admin["dedicated"]);
			for($i=0;$i<$nbr_dedicated;$i++){
				$dedi_name = $admin["dedicated"][$i]["server_hostname"];
				$popup_hostname .= "<option value=\"$dedi_name\">$dedi_name</option>";
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

		$out .= "<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"new_ticket\">

". _("Subject") ." <input name=\"subject\" type=\"text\" size=\"40\" maxlength=\"40\"><br>

". _("What is your server hostname:") ."<br>
<select name=\"server_hostname\">
$popup_hostname
</select><br>

". _("Type of problem:") ."<br>
<select name=\"issue_cat_id\">
$popup_cats
</select><br><br>

". _("Full description of the trouble:") ."<br>
<textarea name=\"ticketbody\" cols=\"60\" rows=\"10\" wrap=\"physical\"></textarea><br><br>

<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"". _("Send trouble ticket") ."\"></div>
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
			$out .= _("Ticket not found.") ;
		}else{
			$a_t = mysql_fetch_array($r);
			$out .= _("Subject:") ." ".htmlspecialchars(stripslashes($a_t["subject"]))."<br>";

			$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a_t["cat_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$out .= _("Type: type not found.") ;
			}else{
				$a2 = mysql_fetch_array($r2);
				$out .= _("Type of problem:") ." ".$a2["catdescript"]."<br>";
			}
			$out .= _("First query date") . ": ".$a_t["date"]." ".$a_t["time"]."<br>";
			$out .= _("Server hostname related") . ": ".$a_t["server_hostname"]."<br>";

			if($a_t["closed"] == "yes"){
				$out .= "<font color=\"red\">". _("Replying to an already closed ticket will reopen it."). "</font><br>";
			}
			
			$out .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"0\">";
			$next_tikq = $_REQUEST["tik_id"];
			while($next_tikq != 0){
				$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='$adm_login' AND id='$next_tikq';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1){
					$out .= _("Ticket not found.") ;
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
				if($a["admin_or_user"] == "admin"){
					$qr = "SELECT * FROM $pro_mysql_tik_admins_table WHERE pseudo='".$a["admin_name"]."';";
					$rr = mysql_query($qr)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
					$nr = mysql_num_rows($rr);
					if($nr == 1){
						$ar = mysql_fetch_array($rr);
						$realname = $ar["realname"];
					}else{
						$realname = _("Not found!");
					}
					$replied_by = "<br>"._("Replied to By:")." ".$realname;
				}else{
					$replied_by = "";
				}
				$out .= "<tr><td$bg valign=\"top\"><i>".$a["date"]." ".$a["time"]."</i>".$replied_by."</td><td$bg>".nl2br(htmlspecialchars(stripslashes($a["text"])))."</td></tr>";
			}
			$out .= "</table>";
			$out .= "<form action=\"?\" method=\"post\">
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
". _("Request to close the issue:") ."<input type=\"radio\" name=\"request_to_close\" value=\"yes\" checked> "._("Yes")."
<input type=\"radio\" name=\"request_to_close\" value=\"no\"> "._("No")."<br>
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"". _("Submit new support issue") ."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
</form>
";
		}
	// The main screen
	}else{
		$out .= "<form action=\"?\" method=\"post\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"subaction\" value=\"new_ticket\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
 <div class=\"input_btn_left\"></div>
 <div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\"". _("Submit new support issue") ."\"></div>
 <div class=\"input_btn_right\"></div>
</div>
</form>
";
		$out .= "<br><br><h3>". _("Old tickets:") ."</h3>";
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='$adm_login' AND in_reply_of_id='0' ORDER BY date,time DESC;";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "<font color=\"red\">". _("Replying to an already closed ticket will reopen it."). "</font><br>";
		$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
	<tr><td>". _("Date") ."</td><td>". _("Time") ."</td><td>". _("Status") ."</td><td>". _("Type") ."</td><td>". _("Hostname") ."</td><td>". _("Subject") ."</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$out .= "<tr><td>".$a["date"]."</td><td>".$a["time"]."</td>";
			if($a["closed"] == "yes"){
				$out .= "<td><font color=\"green\">". _("Closed") ."</font></td>";
			}else{
				$out .= "<td><font color=\"red\">". _("Open") ."</font></td>";
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
			$out .= "<td>".$a["server_hostname"]."</td><td><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&subaction=view_ticket&tik_id=".$a["id"]."\">".stripslashes($a["subject"])."</a></td>";
			$out .= "</tr>";
		}
		$out .= "</table>";
	}
	return $out;
}

?>
