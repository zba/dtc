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
	if (isset($domain["mailinglists"]))
	{
	 $nbr_email += sizeof($domain["mailinglists"]);
	}
	$max_email = $domain["max_email"];
	if($nbr_email >= $max_email){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = $txt_number_of_active_lists[$lang];
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\">". $nbr_email ."</font> / <font size=\"-1\">" . $max_email . "</font><br><br>";

	$txt .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><b><u>".$txt_list_liste_of_your_ml[$lang]."</u><br>";
	if (isset($domain["mailinglists"]))
	{
		$lists = $domain["mailinglists"];
	}
	$nbr_boites = 0;
	if (isset($lists)){
		$nbr_boites = sizeof($lists);
	}
	for($i=0;$i<$nbr_boites;$i++){
		$list = $lists[$i];
		$id = $list["id"];
		$list_name = $list["name"];
		$list_owner = $list["owner"];
		if($i != 0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails&edit_mailbox=$list_name&list_owner=$list_owner\">$list_name</a>";
	}

	if(isset($_REQUEST["edit_mailbox"]) && $_REQUEST["edit_mailbox"] != ""){
		$txt .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails\">".$txt_mail_new_mailbox_link[$lang]."</a> ";
		$txt .= "<br><br><u>".$txt_mail_edit[$lang]."</u><br><br>";

		$list_name = $_REQUEST["edit_mailbox"];
		if (isset($_REQUEST["list_owner"])){
			$list_owner = $_REQUEST["list_owner"];
		} else if (isset($_REQUEST["editmail_owner"])){
			$list_owner = $_REQUEST["editmail_owner"];
		}

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
	$txt .= $txt_list_owner[$lang]."</td><td><input type=\"text\" name=\"editmail_owner\" value=\"$list_owner\">";
$txt .= "</td></tr>";
$txt .= list_options();
$txt .= "<tr><td>&nbsp;</td><td><input type=\"submit\" name=\"modifylistdata\" value=\"Ok\">&nbsp;
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
	".$txt_list_name[$lang]."</td><td><input type=\"text\" name=\"newlist_name\" value=\"\">
</td></tr><tr><td align=\"right\">";
			$txt .= $txt_list_owner[$lang]."</td><td><input type=\"text\" name=\"newlist_owner\" value=\"\">";
$txt .= "</td></tr>
<tr><td>&nbsp;</td>
<td><input type=\"submit\" name=\"addnewlisttodomain\" value=\"Ok\"></td>
</tr>
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

//this function check options and checkbox
function list_options(){

global $edit_domain;
global $adm_login;
$admin_path = getAdminPath($adm_login);
$list_path = $admin_path."/".$edit_domain."/lists/".$edit_domain."_".$_REQUEST["edit_mailbox"]."/";

//1 closedlist file exist?
$check_option = "";
$option_file = $list_path."closedlist";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output = "<tr><td>closedlist</td><td><input type=\"checkbox\" value=\"yes\" name=\"closedlist\"".$check_option."></td></tr>";

//2 moderated file exist?
$check_option = "";
$option_file = $list_path."moderated";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>moderated</td><td><input type=\"checkbox\" value=\"yes\" name=\"moderated\"".$check_option."></td></tr>";

//3 subonlypost file exist?
$check_option = "";
$option_file = $list_path."subonlypost";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>subonlypost</td><td><input type=\"checkbox\" value=\"yes\" name=\"subonlypost\"".$check_option."></td></tr>";

//4 notifysub file exist?
$check_option = "";
$option_file = $list_path."notifysub";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>notifysub</td><td><input type=\"checkbox\" value=\"yes\" name=\"notifysub\"".$check_option."></td></tr>";

//5 nosubconfirm file exist?
$check_option = "";
$option_file = $list_path."nosubconfirm";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>nosubconfirm</td><td><input type=\"checkbox\" value=\"yes\" name=\"nosubconfirm\"".$check_option."></td></tr>";

//6 noarchive file exist?
$check_option = "";
$option_file = $list_path."noarchive";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>noarchive</td><td><input type=\"checkbox\" value=\"yes\" name=\"noarchive\"".$check_option."></td></tr>";

//7 noget file exist?
$check_option = "";
$option_file = $list_path."noget";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>noget</td><td><input type=\"checkbox\" value=\"yes\" name=\"noget\"".$check_option."></td></tr>";

//8 subonlyget file exist?
$check_option = "";
$option_file = $list_path."subonlyget";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>subonlyget</td><td><input type=\"checkbox\" value=\"yes\" name=\"subonlyget\"".$check_option."></td></tr>";

//9 tocc file exist?
$check_option = "";
$option_file = $list_path."tocc";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>tocc</td><td><input type=\"checkbox\" value=\"yes\" name=\"tocc\"".$check_option."></td></tr>";

//10 addtohdr file exist?
$check_option = "";
$option_file = $list_path."addtohdr";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>addtohdr</td><td><input type=\"checkbox\" value=\"yes\" name=\"addtohdr\"".$check_option."></td></tr>";

//11 notoccdenymails file exist?
$check_option = "";
$option_file = $list_path."notoccdenymails";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>notoccdenymails</td><td><input type=\"checkbox\" value=\"yes\" name=\"notoccdenymails\"".$check_option."></td></tr>";

//12 noaccessdenymails file exist?
$check_option = "";
$option_file = $list_path."noaccessdenymails";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>noaccessdenymails</td><td><input type=\"checkbox\" value=\"yes\" name=\"noaccessdenymails\"".$check_option."></td></tr>";

//13 nosubonlydenymails file exist?
$check_option = "";
$option_file = $list_path."nosubonlydenymails";
if (file_exists($option_file)){
	$check_option = " checked";
	}
//add checkbox to form
$output .= "<tr><td>nosubonlydenymails</td><td><input type=\"checkbox\" value=\"yes\" name=\"nosubonlydenymails\"".$check_option."></td></tr>";

return $output;
}

?>
