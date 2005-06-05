<?php

////////////////////////////////////////////////////
// One domain name ftp account collection edition //
////////////////////////////////////////////////////
function drawAdminTools_MailingLists($domain){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $edit_mailbox;
	global $addrlink;

	global $lang;
	global $txt_list_name;
	global $txt_list_owner;
	global $txt_list_liste_of_your_ml;
	global $txt_list_new_list;
	global $txt_mail_edit;
	global $txt_mail_new_mailbox_link;
	global $txt_number_of_active_lists;
	global $txt_maximum_mailbox_reach;


	$txt = "";
	$nbr_email = sizeof($domain["emails"]);
	$max_email = $domain["max_email"];
	if($nbr_email >= $max_email){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = $txt_number_of_active_lists[$lang];
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\">". $nbr_email ."</font> / <font size=\"-1\">" . $max_email . "</font><br><br>";

	$txt .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><b><u>".$txt_list_liste_of_your_ml[$lang]."</u><br>";
	$emails = $domain["emails"];
	$nbr_boites = sizeof($emails);
	for($i=0;$i<$nbr_boites;$i++){
		$email = $emails[$i];
		$id = $email["id"];
		if(isset($_REQUEST["edit_mailbox"]) && $id == $_REQUEST["edit_mailbox"]){
			$list_name = $id;
			$home = $email["home"];
			$list_owner = $email["passwd"];
			$redir1 = $email["redirect1"];
			$redir2 = $email["redirect2"];
			$localdeliver = $email["localdeliver"];
			if($localdeliver == "yes"){
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

	if(isset($_REQUEST["edit_mailbox"]) && $_REQUEST["edit_mailbox"] != ""){
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
	".$txt_list_name[$lang]."</td><td><b>$list_name</b>@$edit_domain
</td></tr><tr><td align=\"right\">";
	$txt .= $txt_list_owner[$lang]."</td><td><input type=\"text\" name=\"editmail_pass\" value=\"$list_owner\">";
$txt .= "
</td></tr><tr>
<td>&nbsp;</td><td><input type=\"submit\" name=\"modifylistdata\" value=\"Ok\">&nbsp;
<input type=\"submit\" name=\"dellist\" value=\"Del\">
</td></tr>
</table>
</form>
";
	}else{
		$txt .= "<br><br><u>".$txt_list_new_list[$lang]."</u><br>";

		if($nbr_email < $max_email){
			$txt .= "
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<table border=\"1\"><tr><td align=\"right\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	".$txt_list_name[$lang]."</td><td><input type=\"text\" name=\"newlist_name\" value=\"$list_name\">
</td></tr><tr><td align=\"right\">";
			$txt .= $txt_list_owner[$lang]."</td><td><input type=\"text\" name=\"newlist_owner\" value=\"$list_owner\">";
$txt .= "
</td></tr><tr>
<td></td>
<td><input type=\"submit\" name=\"addnewlisttodomain\" value=\"Ok\">
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
