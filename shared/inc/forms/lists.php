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

function getListOptionsBoolean($ctrl_path,$tunable_name){
	$option_file = $ctrl_path."/".$tunable_name;
	if (file_exists($option_file)){
		$check_option = " checked";
	}else{
		$check_option = "";
	}
	$out = "<tr>
			<td>".$tunable_name."</td>
			<td><input type=\"checkbox\" value=\"yes\" name=\"".$tunable_name."\"".$check_option."></td>
		</tr>";
}

//this function check options and checkbox
function list_options(){

global $edit_domain;
global $adm_login;
$admin_path = getAdminPath($adm_login);
$list_path = $admin_path."/".$edit_domain."/lists/".$edit_domain."_".$_REQUEST["edit_mailbox"];

$output = getListOptionsBoolean($list_path,"closedlist");
$output .= getListOptionsBoolean($list_path,"moderated");
$output .= getListOptionsBoolean($list_path,"subonlypost");
$output .= getListOptionsBoolean($list_path,"notifysub");
$output .= getListOptionsBoolean($list_path,"nosubconfirm");
$output .= getListOptionsBoolean($list_path,"noarchive");
$output .= getListOptionsBoolean($list_path,"noget");
$output .= getListOptionsBoolean($list_path,"subonlyget");
$output .= getListOptionsBoolean($list_path,"tocc");
$output .= getListOptionsBoolean($list_path,"addtohdr");
$output .= getListOptionsBoolean($list_path,"notoccdenymails");
$output .= getListOptionsBoolean($list_path,"noaccessdenymails");
$output .= getListOptionsBoolean($list_path,"nosubonlydenymails");

//14 prefix file exist?
$txt_option = "";
$option_file = $list_path."prefix";
if (file_exists($option_file)){
	$f = file($option_file);
	$txt_option = $f[0];
	}
//add checkbox to form
$output .= "<tr><td>prefix</td><td><input type=\"text\" value=\"".$txt_option."\" name=\"prefix\" size=\"20\" maxlength=\"20\"></td></tr>";

return $output;
}

?>
