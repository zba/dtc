<?php

function drawAdminTools_emailPanel($mailbox){

	global $adm_email_login;
	global $adm_email_pass;

//	print_r($mailbox);
	$url_start = "<a href=\"".$_SERVER["PHP_SELF"]."?adm_email_login=$adm_email_login&adm_email_pass=$adm_email_pass";
	$form_start = "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_email_login\" value=\"$adm_email_login\">
<input type=\"hidden\" name=\"adm_email_pass\" value=\"$adm_email_pass\">";

	$change_pass_form = "<br><b><u>Change your password:</b></u>".
$form_start."Type new password: <input type=\"hidden\" name=\"action\" value=\"dtcemail_change_pass\"><input type=\"password\" name=\"newpass1\" value=\"\"><br>
Repeate: <input type=\"password\" name=\"newpass2\" value=\"\">
<input type=\"submit\" name=\"submit\" value=\"Ok\"></form><br><br>";

	if($mailbox["data"]["localdeliver"] == "yes"){
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=no\"><font color=\"green\">yes</font></a>";
	}else{
		$deliverUrl = "$url_start&action=dtcemail_set_deliver_local&setval=yes\"><font color=\"red\">no</font></a>";
	}

	$redirect_form = "<br><b><u>Edit your mailbox redirections:</b></u><br><br>
Deliver mail localy: $deliverUrl".
$form_start."Redirection 1: <input type=\"hidden\" name=\"action\" value=\"dtcemail_edit_redirect\"><input type=\"text\" name=\"redirect1\" value=\"".$mailbox["data"]["redirect1"]."\"><br>
Redirection 2: <input type=\"text\" name=\"redirect2\" value=\"".$mailbox["data"]["redirect2"]."\">
<input type=\"submit\" name=\"submit\" value=\"Ok\"></form>";


	$out = "<br><h4>Email panel: customise your mailbox!</h4>
<table width=\"100%\" heigh=\"1\">
<tr>
	<td>".skin("frame",$change_pass_form,"")."</td>
	<td>".skin("frame",$redirect_form,"")."</td>
</tr></table>
<br>
<a href=\"".$_SERVER["PHP_SELF"]."?action=logout\">Logout</a>";
	return $out;
}

/////////////////////////////////////////
// One domain email collection edition //
/////////////////////////////////////////
function drawAdminTools_Emails($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $edit_mailbox;
	global $addrlink;

	global $lang;
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_mail_liste_of_your_box;
	global $txt_mail_new_mailbox;
	global $txt_mail_redirection1;
	global $txt_mail_redirection2;
	global $txt_mail_deliver_localy;
	global $txt_mail_edit;
	global $txt_mail_new_mailbox_link;
	global $txt_number_of_active_mailbox;
	global $txt_maximum_mailbox_reach;

	global $conf_hide_password;


	$nbr_email = sizeof($domain["emails"]);
	$max_email = $domain["max_email"];
	if($nbr_email >= $max_email){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = $txt_number_of_active_mailbox[$lang];
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_email ."</font> / <font size=\"-1\">" . $max_email . "</font><br><br>";

	$txt .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><b><u>".$txt_mail_liste_of_your_box[$lang]."</u><br>";
	$emails = $domain["emails"];
	$nbr_boites = sizeof($emails);
	for($i=0;$i<$nbr_boites;$i++){
		$email = $emails[$i];
		$id = $email["id"];
		if($id == $_REQUEST["edit_mailbox"]){
			$mailbox_name = $id;
			//print_r($email);
			$home = $email["home"];
			$passwd = $email["passwd"];
			$redir1 = $email["redirect1"];
			$redir2 = $email["redirect2"];
			$localdeliver = $email["localdeliver"];
			if($localdeliver == yes){
				$checkbox_state = " checked";
			}else{
				$checkbox_state = "";
			}
		}
		if($i != 0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails&edit_mailbox=$id\">$id</a>";
	}

	if($_REQUEST["edit_mailbox"] != "" && isset($_REQUEST["edit_mailbox"])){
		$txt .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails\">".$txt_mail_new_mailbox_link[$lang]."</a> ";
		$txt .= "<br><br><u>".$txt_mail_edit[$lang]."</u><br><br>";

		$txt .= "
<table border=\"1\"><tr><td align=\"right\">
<form action=\"?\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<input type=\"hidden\" name=\"edit_mailbox\" value=\"".$_REQUEST["edit_mailbox"]."\">
	".$txt_login_login[$lang]."</td><td><b>$mailbox_name</b>@$edit_domain
</td><td align=\"right\">
	".$txt_mail_redirection1[$lang]."</td><td><input type=\"text\" name=\"editmail_redirect1\" value=\"$redir1\">
</td></tr><tr><td align=\"right\">";
	if ($conf_hide_password == "yes")
	{
	$txt .= $txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"editmail_pass\" value=\"$passwd\">";
	} else {
	$txt .= $txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"editmail_pass\" value=\"$passwd\">";
	}
$txt .= "
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"editmail_redirect2\" value=\"$redir2\">
</td></tr><tr><td align=\"right\">
".$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"editmail_deliver_localy\" value=\"yes\"$checkbox_state></td>
<td>&nbsp;</td><td><input type=\"submit\" name=\"modifymailboxdata\" value=\"Ok\">&nbsp;
<input type=\"submit\" name=\"delemailaccount\" value=\"Del\">
</td></tr>
</table>
</form>
";
	}else{
		$txt .= "<br><br><u>".$txt_mail_new_mailbox[$lang]."</u><br>";

		if($nbr_email < $max_email){
			$txt .= "
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<table border=\"1\"><tr><td align=\"right\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newmail_login\" value=\"$mailbox_name\">
</td><td align=\"right\">
	".$txt_mail_redirection1[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect1\" value=\"\">
</td></tr><tr><td align=\"right\">";
	if ($conf_hide_password == "yes")
	{
		$txt .= $txt_login_pass[$lang]."</td><td><input type=\"password\" name=\"newmail_pass\" value=\"$passwd\">";
	} else {
		$txt .= $txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"newmail_pass\" value=\"$passwd\">";
	}
$txt .= "
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect2\" value=\"\">
</td></tr><tr><td align=\"right\">
".$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"newmail_deliver_localy\" value=\"yes\" checked></td>
<td></td>
<td><input type=\"submit\" name=\"addnewmailtodomain\" value=\"Ok\">
</td></tr>
</table>
</form>
";
		}else{
			$txt .= $txt_maximum_mailbox_reach[$lang]."<br>";
		}
	}
	$txt .= "</b></font></font>";
	return $txt;
}

?>
