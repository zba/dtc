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

	$out = "<font color=\"red\">This part is still in development: do not use</font><br>";

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

Subject: <input name=\"subject\" type=\"text\" size=\"40\" maxlength=\"40\"><br>

What is your server hostname:<br>
<select name=\"server_hostname\">
$popup_hostname
</select><br>

Type of problem:<br>
<select name=\"issue_cat_id\">
$popup_cats
</select><br><br>

Full description of the trouble:<br>
<textarea name=\"ticketbody\" cols=\"60\" rows=\"10\" wrap=\"physical\"></textarea><br><br>

<input type=\"submit\" value=\"Send trouble ticket\">

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
			$out .= "Ticket not found!!!";
		}else{
			$a = mysql_fetch_array($r);
			$out .= "Subject: ".$a["subject"]."<br>";

			$q2 = "SELECT * FROM $pro_mysql_tik_cats_table WHERE id='".$a["cat_id"]."';";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			if($n2 != 1){
				$out .= "Type: type not found!";
			}else{
				$a2 = mysql_fetch_array($r2);
				$out .= "Type: ".$a2["catdescript"]."<br>";
			}
			$out .= "First query date: ".$a["date"]." ".$a["time"]."<br>";
			$out .= "Server hostname related: ".$a["server_hostname"]."<br>";
			
			$out .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"0\">";
			$next_tikq = $_REQUEST["tik_id"];
			while($next_tikq != 0){
				$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='$adm_login' AND id='$next_tikq';";
				$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$n = mysql_num_rows($r);
				if($n != 1){
					$out .= "Ticket not found!!!";
					break;
				}
				$a = mysql_fetch_array($r);
				$next_tikq = $a["reply_id"];
				if($a["admin_or_user"] == "user"){
					$bg = " bgcolor=\"#AAAAFF\" ";
				}else{
					$bg = " bgcolor=\"#FFFFAA\" ";
				}
				$out .= "<tr><td$bg><i>".$a["date"]." ".$a["time"]."</i></td><td$bg>".$a["text"]."</td></tr>";
			}
			$out .= "</table>";
		}
	// The main screen
	}else{
		$out .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"subaction\" value=\"new_ticket\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"submit\" value=\"Submit new support issue\">
</form>
";
		$out .= "<b><u>Old tickets:</u></b>";
		$q = "SELECT * FROM $pro_mysql_tik_queries_table WHERE adm_login='$adm_login' AND in_reply_of_id='0' ORDER BY date,time DESC;";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$out .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
		<tr><td>Date</td><td>Time</td><td>Status</td><td>Type</td><td>Hostname</td><td>Subject</td></tr>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$out .= "<tr><td>".$a["date"]."</td><td>".$a["time"]."</td>";
			if($a["closed"] == "yes"){
				$out .= "<td><font color=\"green\">Closed</font></td>";
			}else{
				$out .= "<td><font color=\"red\">Open</font></td>";
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
			$out .= "<td>".$a["server_hostname"]."</td><td><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&subaction=view_ticket&tik_id=".$a["id"]."\">".$a["subject"]."</a></td>";
			$out .= "</tr>";
		}
		$out .= "</table>";
	}
	return $out;
}

?>