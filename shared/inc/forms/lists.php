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

	$txt = "";
//	$nbr_email = sizeof($domain["emails"]);
	if (isset($domain["mailinglists"])){
		$nbr_email = sizeof($domain["mailinglists"]);
	}else{
		$nbr_email = 0;
        }
	$max_email = $domain["max_lists"];
	if($nbr_email >= $max_email){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = _("Number of active mailing lists");
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\">". $nbr_email ."</font> / <font size=\"-1\">" . $max_email . "</font><br><br>";

	$txt .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><h3>". _("List of your mailing lists") ."</h3>";
	if (isset($domain["mailinglists"])){
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
		if(isset($_REQUEST["edit_mailbox"]) && $_REQUEST["edit_mailbox"] == $list_name){
			$txt .= "$list_name";
		}else{
			$txt .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails&edit_mailbox=$list_name&list_owner=$list_owner\">$list_name</a>";
		}
	}

	if(isset($_REQUEST["edit_mailbox"]) && $_REQUEST["edit_mailbox"] != ""){
		$txt .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails\">". _("new mailing list") ."</a> ";
		$txt .= "<br><br><h3>". _("Edit mailing list") ."</h3><br><br>";

		$list_name = $_REQUEST["edit_mailbox"];
		if (isset($_REQUEST["list_owner"])){
			$list_owner = $_REQUEST["list_owner"];
		} else if (isset($_REQUEST["editmail_owner"])){
			$list_owner = $_REQUEST["editmail_owner"];
		}

		$txt .= "
<table border=\"1\"><tr><td align=\"right\">
<form action=\"?\" method=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<input type=\"hidden\" name=\"edit_mailbox\" value=\"".$_REQUEST["edit_mailbox"]."\">
	<div onmouseover=\"return escape('". _("Name of the list.") ."')\">". _("List name:") ."</div></td>
	<td><b>$list_name</b>@$edit_domain</td></tr>
	<tr><td align=\"right\"><div onmouseover=\"return escape('". _("This is the main owner of the list.") ."')\">". _("List owner") ."</div></td>
	<td><input type=\"text\" name=\"editmail_owner\" value=\"$list_owner\"></td></tr>";
$txt .= list_options();
$txt .= "<tr><td>&nbsp;</td><td><input type=\"submit\" name=\"modifylistdata\" value=\"Ok\">&nbsp;
<input type=\"submit\" name=\"dellist\" value=\"Del\">
</td></tr>
</table>
</form>
";
		$admin_path = getAdminPath($adm_login);
		$list_path = $admin_path."/".$edit_domain."/lists/".$edit_domain."_".$_REQUEST["edit_mailbox"];
		$txt .= subscribers_list($list_path);
	}else{
		$txt .= "<br><br>". _("new mailing list");
		$txt .= "<br><br><h3>". _("New mailing list:") ."</h3><br>";

		if($nbr_email < $max_email){
			$txt .= "
<form action=\"?\" method=\"post\">
<table border=\"1\"><tr><td align=\"right\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<div onmouseover=\"return escape('"._("List name") ."')\">". _("List name:") ."</div></td>
	<td><input type=\"text\" name=\"newlist_name\" value=\"\"></td></tr>
	<tr><td align=\"right\"><div onmouseover=\"return escape('". _("This is the main owner of the list.") ."')\">". _("List owner:") ."</div></td>
	<td><input type=\"text\" name=\"newlist_owner\" value=\"\">";
$txt .= "</td></tr>
<tr><td>&nbsp;</td>
<td><input type=\"submit\" name=\"addnewlisttodomain\" value=\"Ok\"></td>
</tr>
</table>
</form>
";
		}else{
			$txt .= _("Maximum number of lists reached") ."<br>";
		}
	}
	$txt .= "</b></font></font>";
	
	return $txt;
}

function subscribers_list($list_path){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $edit_domain;

	$out = "<br><h3>". _("Subscriber list (click the address to unsubscribe):") ."</h3><br><br>";

	$path = $list_path."/subscribers.d";

	// Get all the subscribers in an array
	$subs = array();
	if (is_dir($path)){
		if ($dh = opendir($path)){
			while (($file = readdir($dh)) !== false){
				$fpath = $path ."/". $file;
				if(filetype($fpath) == "file"){
					$fcontent = file($fpath);
					$n = sizeof($fcontent);
					for($i=0;$i<$n;$i++){
						$subs[] = $fcontent[$i];
					}
				}
			}
		}
	}
	// Sort by alpha order
	sort($subs);
	// Display
	$n = sizeof($subs);
	for($i=0;$i<$n;$i++){
		if($i != 0){
			$out .= " - ";
		}
		$out .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails&edit_mailbox=".$_REQUEST["edit_mailbox"]."&action=unsubscribe_user&subscriber_email=".$subs[$i]."\">".$subs[$i]."</a>";
	}
	$out .= "<br><br><h3>". _("Subscribe a new user") .":</h3><br><br>";
	$out .= "<form action=\"?\" method=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<input type=\"hidden\" name=\"edit_mailbox\" value=\"".htmlspecialchars($_REQUEST["edit_mailbox"])."\">
	<input type=\"hidden\" name=\"action\" value=\"subscribe_new_user\">
	<input type=\"text\" size=\"40\" name=\"subscriber_email\" value=\"\">
	<input type=\"submit\" value=\"Ok\">
	</form>";
	return $out;
}

function getTunableHelp($tunable_name){
	$hlp = "<b>". $tunable_name.":</b> ";
	switch($tunable_name){
	case "subonlypost":
		$hlp .= _("When this flag is set, only people who are subscribed to the list, are allowed to post to it. The check is made against the &quot;From:&quot; header.") ;
	case "closedlist":
		$hlp .= _("Is the list is open or closed. If it\'s closed subscribtion and unsubscription via mail is disabled.") ;
		break;
	case "owner":
		$hlp .= _("The email addresses in this fields (1 per line) will get mails to listname-owner@listdomain.tld") ;
		break;
	case "moderated":
		$hlp .= _("If this flag is set, the email addresses in the field moderators will act as moderators for the list.") ;
		break;
	case "moderators":
		$hlp .= _("This is the list of moderators.") ;
		break;
	case "nosubconfirm":
		$hlp .= _("If this flag exists, no mail confirmation is needed to subscribe to the list. This should in principle never ever be used, but there is times on local lists etc. where this is useful. HANDLE WITH CARE!") ;
		break;
	case "prefix":
		$hlp .= _("The prefix for the Subject: line of mails to the list. This will alter the Subject: line, and add a prefix if it\'s not present elsewhere.") ;
		break;
	case "delheaders":
		$hlp .= _("In those fields is specified *ONE* headertoken to match per line. If the fields are like this:<br><br>Received:<br>Message-ID:<br><br>Then all occurences of these headers in incoming list mail will be deleted. From: and Return-Path: are deleted no matter what.") ;
		break;
	case "addtohdr":
		$hlp .= _("When this flag is present, a To: header including the recipients emailaddress will be added to outgoing mail. Recommended usage is to remove existing To: headers with delheaders (see above) first.") ;
		break;
	case "tocc":
		$hlp .= _("If this flag is set, the list address does not have to be in the To: or Cc: header of the email to the list (interesting for aliases addressing multiple lists).") ;
		break;
	case "customheaders":
		$hlp .= _("These headers are added to every mail coming through. This is the place you want to add Reply-To: header in case you want such.") ;
		break;
	case "footer":
		$hlp .= _("Fill this if you want every mail to have something like:<br>--<br>To unsubscribe send a mail to coollist+unsubscribe@lists.domain.net.") ;
		break;
	case "noarchive":
		$hlp .= _("If this flag exists, the mail won\'t be saved in the archive but simply deleted.") ;
		break;
	case "noget":
		$hlp .= _("If this file exists, then retrieving old posts with -get-N (for exemple mylist-get-12@my-domain.tld) is disabled") ;
		break;
	case "subonlyget":
		$hlp .= _("If this file exists, then retrieving old posts with -get-N is only possible for subscribers. The above mentioned \'noget\' have precedence.") ;
		break;
	case "digestinterval":
		$hlp .= _("This value specifies how many seconds will pass before the next digest is sent. Defaults to 604800 seconds, which is 7 days.") ;
		break;
	case "digestmaxmails":
		$hlp .= _("This file specifies how many mails can accumulate before digest sending is triggered. Defaults to 50 mails, meaning that if 50 mails arrive to the list before digestinterval have passed, the digest is delivered.") ;
		break;
	case "notifysub":
		$hlp .= _("If this flag is present, the owner(s) will get a mail with the address of someone sub/unsubscribing to a mailinglist.") ;
		break;
	case "nosubonlydenymails":
		$hlp .= _("Help missing for nosubonlydenymails") ;
		break;
	case "notoccdenymails":
		$hlp .= _("Reject mails that don\'t have the list address in the To: or Cc:.") ;
		break;
	case "noaccessdenymails":
		$hlp .= _("Help missing for noaccessdenymails") ;
		break;
	case "relayhost":
		$hlp .= _("Mail server used to send the messages.") ;
		break;
	case "memorymailsize":
		$hlp .= _("Here is specified in bytes how big a mail can be and still be prepared for sending in memory. It\'s greatly reducing the amount of write system calls to prepare it in memory before sending it, but can also lead to denial of service attacks. Default is 16k (16384 bytes).") ;
		break;
	case "verp":
		$hlp .= _("Enable VERP support.") ;
		break;
	case "bouncelife":
		$hlp .= _("Here is specified for how long time in seconds an address can bounce before it\'s unsubscribed. Defaults to 432000 seconds, which is 5 days.") ;
		break;
	case "maxverprecips":
		$hlp .= _("How many recipients pr. mail delivered to the smtp server. Defaults to 100.") ;
		break;
	case "delimiter":
		$hlp .= _("Do not change unless you really know what you are doing.") ;
		break;
	case "access":
		$hlp .= _("If this file exists, all headers of a post to the list is matched against the rules. The first rule to match wins. NOTE: the default action is to deny access (reject the mail) so take care if you write something here") ;
		break;
	case "webarchive":
		$hlp .= _("Enable webarchive.") ;
		break;
	case "rcfile":
		$hlp .= _("Insert here the template\'s code that you want use for your web archive. Read <a href=\'http://www.mhonarc.org/MHonArc/doc/resources/rcfile.html\' target=\'_blank\'>documentation</a> and see <a href=\'http://www.mhonarc.org/MHonArc/doc/app-rcfileexs.html\' target=\'_blank\'>examples</a>.") ;
		break;
	case "recreatewa":
		$hlp .= _("Recreate all messages of webarchive. Use this only if you have changed the webarchive\'s template. NOTE: this works only if you have &quot;web archive&quot; checked.") ;
		break;
	case "deletewa":
		$hlp .= _("Delete all messages of webarchive. NOTE: this works only if you have &quot;web archive&quot; not checked.") ;
		break;
	case "spammode":
		$hlp .= _("Hide email addresses to avoid spam.") ;
		break;
	default:
		break;
	}

	return $hlp;
}

function getListOptionsBoolean($ctrl_path,$tunable_name,$tunable_title){
	$option_file = $ctrl_path."/control/".$tunable_name;
	if (file_exists($option_file)){
		$check_option = " checked";
	}else{
		$check_option = "";
	}
	return "<tr>
                <td onmouseover=\"Tip('".getTunableHelp($tunable_name)."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" align=\"right\">".$tunable_title."</td>
                <td><input type=\"checkbox\" value=\"yes\" name=\"".$tunable_name."\"".$check_option."></td></tr>";
}

function getListOptionsValue($ctrl_path,$tunable_name,$tunable_title){
	$option_file = $ctrl_path."/control/".$tunable_name;
	if (!file_exists($option_file)){
		$value = "";
	}else{
		$a = file($option_file);
		$value = $a[0];
	}
	return "<tr>
			<td onmouseover=\"Tip('".getTunableHelp($tunable_name)."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" align=\"right\">".$tunable_title."</td>
			<td><input size=\"40\" type=\"text\" value=\"".htmlspecialchars($value)."\" name=\"".$tunable_name."\"></td></tr>";
}

function getListOptionsTextarea($ctrl_path,$tunable_name,$tunable_title){
	$option_file = $ctrl_path."/control/".$tunable_name;
	$value = "";
	if (file_exists($option_file)){
		$a = file($option_file);
		foreach ($a as $line_num => $line) {
			$value .= str_replace("\r","",str_replace("\n","",$line))."\n";
		}
	}
	return "<tr>
	<td onmouseover=\"Tip('".getTunableHelp($tunable_name)."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" valign=\"top\" align=\"right\">".$tunable_title."</td>
	<td><textarea rows=\"5\" cols=\"60\" name=\"".$tunable_name."\">".htmlspecialchars($value)."</textarea></td></tr>";
}


function getListOptionsList($ctrl_path,$tunable_name,$tunable_title){
	$option_file = $ctrl_path."/control/".$tunable_name;
	if (!file_exists($option_file)){
		$values = array();
	}else{
		$values = file($option_file);
	}
        //if owner i don't control the first line
//	if($tunable_name=="owner"){
//		$start=1;
//	}else{
		$start=0;
//	}

        $mouseover = "onmouseover=\"Tip('".getTunableHelp($tunable_name)."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\"";
	$out = "<tr>";
	
	for($i=$start;$i<sizeof($values);$i++){
		if ($i==$start){
			$out .= "<td $mouseover align=\"right\" valign=\"top\" rowspan=\"".(sizeof($values) - $start + 1)."\">".$tunable_title."</td>";
		}else{
			$out .= "<tr>";
		}
		$out .= "<td><input size=\"40\" type=\"text\" value=\"".htmlspecialchars($values[$i])."\" name=\"".$tunable_name."[]\"></td></tr>";
	}
	if($start >= sizeof($values)){
		$out .= "<td $mouseover align=\"right\">".$tunable_title."</td>";
	}else{
		$out .= "<tr>";
	}
	$out .="<td><input size=\"40\" type=\"text\" value=\"\" name=\"".$tunable_name."[]\"></td></tr>";
	return $out;
}

function getListOptionsWABoolean($tunable_name, $tunable_title){
	global $pro_mysql_list_table;
	global $edit_domain;
	$name = $_REQUEST["edit_mailbox"];
	$test_query = "SELECT webarchive FROM $pro_mysql_list_table	WHERE domain='$edit_domain' AND name='$name' LIMIT 1";
	$test_result = mysql_query ($test_query)or die("Cannot execute query \"$test_query\" line ".__LINE__." file ".__FILE__. " sql said ".mysql_error());
	$test = mysql_fetch_array($test_result);
	if ($test[0]== "yes"){
		$check_option = " checked";
	}else{
		$check_option = "";
	}
	return "<tr><td onmouseover=\"Tip('".getTunableHelp($tunable_name)."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" align=\"right\">". $tunable_title ."</td>
                <td><input type=\"checkbox\" value=\"yes\" name=\"".$tunable_name."\"".$check_option."></td></tr>";
}

function getListOptionsWATextarea($ctrl_path,$tunable_name,$tunable_title){
	$option_file = $ctrl_path."/".$tunable_name;
	$value = "";
	if (file_exists($option_file)){
		$a = file($option_file);
		foreach ($a as $line_num => $line) {
			$value .= $line."\n";
		}
	}
	return "<tr>
    <td onmouseover=\"Tip('".getTunableHelp($tunable_name)."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" valign=\"top\" align=\"right\">".$tunable_title."</td>
    <td><textarea rows=\"5\" cols=\"40\" name=\"".$tunable_name."\">".htmlspecialchars($value)."</textarea></td></tr>";
}

function getListOptionsWABooleanActions($tunable_name,$tunable_title){
	return "<tr><td onmouseover=\"Tip('".getTunableHelp($tunable_name)."',STICKY,true,CLICKCLOSE,true,FADEIN,600)\" align=\"right\">".$tunable_title."</td>
                <td><input type=\"checkbox\" value=\"yes\" name=\"".$tunable_name."\"></td></tr>";
}

//this function check options and checkbox
function list_options(){

	global $edit_domain;
	global $adm_login;
	global $conf_use_advanced_lists_tunables;

	$admin_path = getAdminPath($adm_login);
	$list_path = $admin_path."/".$edit_domain."/lists/".$edit_domain."_".$_REQUEST["edit_mailbox"];

	$output = "";
	$output .= "<tr><td colspan=\"2\"><b>". _("Rights") ."</b></td></tr>";
	$output .= getListOptionsBoolean($list_path,"subonlypost", _("Subscribers only post:") );
	$output .= getListOptionsBoolean($list_path,"closedlist", _("Closed list:") );
	$output .= getListOptionsList($list_path,"owner", _("Owner:") );
	$output .= getListOptionsBoolean($list_path,"moderated", _("Moderated:") );
	$output .= getListOptionsList($list_path,"moderators", _("Moderators:") );
	$output .= getListOptionsBoolean($list_path,"nosubconfirm", _("No subscribtion confirmation:") );

	$output .= "<tr><td colspan=\"2\"><b>". _("Header") ."</b></td></tr>";
	$output .= getListOptionsValue($list_path,"prefix", _("Subject prefix:") );
	$output .= getListOptionsList($list_path,"delheaders", _("Delete headers:") );
	$output .= getListOptionsBoolean($list_path,"addtohdr", _("Add To: header:") );
	$output .= getListOptionsBoolean($list_path,"tocc", _("To: or Cc: not mandatory:") );
	$output .= getListOptionsTextarea($list_path,"customheaders", _("Custom headers:") );
	$output .= getListOptionsTextarea($list_path,"footer", _("Added footer:") );

	$output .= "<tr><td colspan=\"2\"><b>". _("Archive") ."</b></td></tr>";
	$output .= getListOptionsBoolean($list_path,"noarchive", _("No archives:") );
	$output .= getListOptionsBoolean($list_path,"noget", _("No get-N function:") );
	$output .= getListOptionsBoolean($list_path,"subonlyget", _("get-N function only for subscribers:") );

	$output .= "<tr><td colspan=\"2\"><b>". _("Digest") ."</b></td></tr>";
	$output .= getListOptionsValue($list_path,"digestinterval", _("Digest interval:") );
	$output .= getListOptionsValue($list_path,"digestmaxmails", _("Digest max mails:") );

	$output .= "<tr><td colspan=\"2\"><b>". _("Notifications") ."</b></td></tr>";
	$output .= getListOptionsBoolean($list_path,"notifysub", _("Notify new subscribtions:") );
	$output .= getListOptionsBoolean($list_path,"nosubonlydenymails", _("Notify when post and not subscribed:") );
	$output .= getListOptionsBoolean($list_path,"notoccdenymails", _("Deny if no To: or Cc::") );
	$output .= getListOptionsBoolean($list_path,"noaccessdenymails", _("Notify when post and no access:") );

	$output .= "<tr><td colspan=\"2\"><b>". _("SMTP configuration") ."</b></td></tr>";
	$output .= getListOptionsValue($list_path,"memorymailsize", _("Max mail memory size:") );
	if($conf_use_advanced_lists_tunables == "yes"){
		$output .= getListOptionsValue($list_path,"relayhost", _("SMTP relay server:") );
		$output .= getListOptionsValue($list_path,"verp", _("VERP:") );
		$output .= getListOptionsValue($list_path,"maxverprecips", _("Max VERP recipients:") );
		$output .= getListOptionsValue($list_path,"delimiter", _("Delimiter:") );
		$output .= getListOptionsValue($list_path,"bouncelife", _("Bounce life:") );
		$output .= getListOptionsTextarea($list_path,"access", _("Access list:") );
	}

	$output .= "<tr><td colspan=\"2\"><b>". _("Web archive") ."</b></td></tr>";
	$output .= getListOptionsWABoolean("webarchive", _("Enable webarchive:") );
	$output .= getListOptionsWATextarea($list_path,"rcfile", _("Own template:") );
	$output .= getListOptionsWABooleanActions("recreatewa", _("Recreate:") );
	$output .= getListOptionsWABooleanActions("deletewa", _("Delete:") );
	$output .= getListOptionsWABoolean("spammode", _("Anti-spam mode:") );

	return $output;
}

?>
