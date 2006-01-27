<?php

require("$dtcshared_path/inc/forms/lists_strings.php");

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
	global $txt_lists_hlp_main_owner;
	global $txt_lists_hlp_list_name;

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
<form action=\"?\" method=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<input type=\"hidden\" name=\"edit_mailbox\" value=\"".$_REQUEST["edit_mailbox"]."\">
	<div onmouseover=\"return escape('".$txt_lists_hlp_list_name[$lang]."')\">".$txt_list_name[$lang]."</div></td>
	<td><b>$list_name</b>@$edit_domain</td></tr>
	<tr><td align=\"right\"><div onmouseover=\"return escape('".$txt_lists_hlp_main_owner[$lang]."')\">".$txt_list_owner[$lang]."</div></td>
	<td><input type=\"text\" name=\"editmail_owner\" value=\"$list_owner\"></td></tr>";
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
<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
<table border=\"1\"><tr><td align=\"right\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<div onmouseover=\"return escape('".$txt_lists_hlp_list_name[$lang]."')\">".$txt_list_name[$lang]."</div></td>
	<td><input type=\"text\" name=\"newlist_name\" value=\"\"></td></tr>
	<tr><td align=\"right\"><div onmouseover=\"return escape('".$txt_lists_hlp_main_owner[$lang]."')\">".$txt_list_owner[$lang]."</div></td>
	<td><input type=\"text\" name=\"newlist_owner\" value=\"\">";
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
	
	$txt .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"gfx/wz_tooltip.js\"></script>";
	return $txt;
}

function getTunableHelp($tunable_name){
  global $lang;

  $varname = "txt_lists_hlp_".$tunable_name;

  global $$varname;
  if(isset($$varname)){
    $out = $$varname;
    return "<b>".$tunable_name.": </b>".$out[$lang];
  }else{
    return "<b>".$tunable_name."</b>";
  }
}

function getTunableTitle($tunable_name){
  global $lang;
  $varname = "txt_lists_title_".$tunable_name;
  
  global $$varname;
  if(isset($$varname)){
    $out = $$varname;
    return $out[$lang];
  }else{
    return "";
  }
}

function getListOptionsBoolean($ctrl_path,$tunable_name){
	$option_file = $ctrl_path."/control/".$tunable_name;
	if (file_exists($option_file)){
		$check_option = " checked";
	}else{
		$check_option = "";
	}
	return "<tr>
                <td onmouseover=\"this.T_STICKY=true;return escape('".getTunableHelp($tunable_name)."')\" align=\"right\">".getTunableTitle($tunable_name)."</td>
                <td><input type=\"checkbox\" value=\"yes\" name=\"".$tunable_name."\"".$check_option."></td></tr>";
}

function getListOptionsValue($ctrl_path,$tunable_name){
	$option_file = $ctrl_path."/control/".$tunable_name;
	if (!file_exists($option_file)){
		$value = "";
	}else{
		$a = file($option_file);
		$value = $a[0];
	}
	return "<tr>
			<td onmouseover=\"this.T_STICKY=true;return escape('".getTunableHelp($tunable_name)."')\" align=\"right\">".getTunableTitle($tunable_name)."</td>
			<td><input size=\"40\" type=\"text\" value=\"".$value."\" name=\"".$tunable_name."\"></td></tr>";
}

function getListOptionsTextarea($ctrl_path,$tunable_name){
  $option_file = $ctrl_path."/control/".$tunable_name;
  $value = "";
  if (file_exists($option_file)){
    $a = file($option_file);
    foreach ($a as $line_num => $line) {
      $value .= $line."\n";
    }
  }
  return "<tr>
    <td onmouseover=\"this.T_STICKY=true;return escape('".getTunableHelp($tunable_name)."')\" valign=\"top\" align=\"right\">".getTunableTitle($tunable_name)."</td>
    <td><textarea rows=\"5\" cols=\"40\" name=\"".$tunable_name."\">".$value."</textarea></td></tr>";
}


function getListOptionsList($ctrl_path,$tunable_name){
	$option_file = $ctrl_path."/control/".$tunable_name;
	if (!file_exists($option_file)){
		$values = array();
	}else{
		$values = file($option_file);
	}
        //if owner i don't control the first line
	if($tunable_name=="owner"){
          $start=1;
        }else{
          $start=0;
        }

        $mouseover = "onmouseover=\"this.T_STICKY=true;return escape('".getTunableHelp($tunable_name)."')\"";
	$out = "<tr>";
	
	for($i=$start;$i<sizeof($values);$i++){
		if ($i==$start){
		  $out .= "<td $mouseover align=\"right\" valign=\"top\" rowspan=\"".(sizeof($values) - $start + 1)."\">".getTunableTitle($tunable_name)."</td>";
		  }else{
		  $out .= "<tr>";
		  }
	$out .= "<td><input size=\"40\" type=\"text\" value=\"".$values[$i]."\" name=\"".$tunable_name."[]\"></td></tr>";
	}
	if($start >= sizeof($values)){
	$out .= "<td $mouseover align=\"right\">".getTunableTitle($tunable_name)."</td>";
	}else{
	$out .= "<tr>";
	}
	$out .="<td><input size=\"40\" type=\"text\" value=\"\" name=\"".$tunable_name."[]\"></td></tr>";
	return $out;
}

function getListOptionsWABoolean($tunable_name){
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
	return "<tr><td onmouseover=\"this.T_STICKY=true;return escape('".getTunableHelp($tunable_name)."')\" align=\"right\">".getTunableTitle($tunable_name)."</td>
                <td><input type=\"checkbox\" value=\"yes\" name=\"".$tunable_name."\"".$check_option."></td></tr>";
}

function getListOptionsWATextarea($ctrl_path,$tunable_name){
  $option_file = $ctrl_path."/".$tunable_name;
  $value = "";
  if (file_exists($option_file)){
    $a = file($option_file);
    foreach ($a as $line_num => $line) {
      $value .= $line."\n";
    }
  }
  return "<tr>
    <td onmouseover=\"this.T_STICKY=true;return escape('".getTunableHelp($tunable_name)."')\" valign=\"top\" align=\"right\">".getTunableTitle($tunable_name)."</td>
    <td><textarea rows=\"5\" cols=\"40\" name=\"".$tunable_name."\">".$value."</textarea></td></tr>";
}

function getListOptionsWABooleanActions($tunable_name){
	return "<tr><td onmouseover=\"this.T_STICKY=true;return escape('".getTunableHelp($tunable_name)."')\" align=\"right\">".getTunableTitle($tunable_name)."</td>
                <td><input type=\"checkbox\" value=\"yes\" name=\"".$tunable_name."\"></td></tr>";
}

//this function check options and checkbox
function list_options(){

global $edit_domain;
global $adm_login;
global $txt_lists_main_title_rights;
global $txt_lists_main_title_header;
global $txt_lists_main_title_archive;
global $txt_lists_main_title_digest;
global $txt_lists_main_title_notification;
global $txt_lists_main_title_smtp_config;
global $txt_lists_main_title_subunsub;
global $txt_lists_main_title_webarchive;
global $txt_lists_title_sub;
global $txt_lists_title_unsub;
global $txt_lists_hlp_sub;
global $txt_lists_hlp_unsub;
global $lang;
$admin_path = getAdminPath($adm_login);
$list_path = $admin_path."/".$edit_domain."/lists/".$edit_domain."_".$_REQUEST["edit_mailbox"];

$output = "";
$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_rights[$lang]."</b></td></tr>";
$output .= getListOptionsBoolean($list_path,"subonlypost");
$output .= getListOptionsBoolean($list_path,"closedlist");
$output .= getListOptionsList($list_path,"owner");
$output .= getListOptionsBoolean($list_path,"moderated");
$output .= getListOptionsList($list_path,"moderators");
$output .= getListOptionsBoolean($list_path,"nosubconfirm");

$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_header[$lang]."</b></td></tr>";
$output .= getListOptionsValue($list_path,"prefix");
$output .= getListOptionsList($list_path,"delheaders");
$output .= getListOptionsBoolean($list_path,"addtohdr");
$output .= getListOptionsBoolean($list_path,"tocc");
$output .= getListOptionsTextarea($list_path,"customheaders");
$output .= getListOptionsTextarea($list_path,"footer");

$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_archive[$lang]."</b></td></tr>";
$output .= getListOptionsBoolean($list_path,"noarchive");
$output .= getListOptionsBoolean($list_path,"noget");
$output .= getListOptionsBoolean($list_path,"subonlyget");

$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_digest[$lang]."</b></td></tr>";
$output .= getListOptionsValue($list_path,"digestinterval");
$output .= getListOptionsValue($list_path,"digestmaxmails");

$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_notification[$lang]."</b></td></tr>";
$output .= getListOptionsBoolean($list_path,"notifysub");
$output .= getListOptionsBoolean($list_path,"nosubonlydenymails");
$output .= getListOptionsBoolean($list_path,"notoccdenymails");
$output .= getListOptionsBoolean($list_path,"noaccessdenymails");

$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_smtp_config[$lang]."</b></td></tr>";
$output .= getListOptionsValue($list_path,"memorymailsize");
$output .= getListOptionsValue($list_path,"relayhost");
$output .= getListOptionsValue($list_path,"verp");
$output .= getListOptionsValue($list_path,"maxverprecips");
$output .= getListOptionsValue($list_path,"delimiter");
$output .= getListOptionsTextarea($list_path,"access");

$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_subunsub[$lang]."</b></td></tr>";
$output .= "<tr><td onmouseover=\"this.T_STICKY=true;return escape('".$txt_lists_hlp_sub[$lang]."')\" valign=\"top\" align=\"right\">".$txt_lists_title_sub[$lang]."</td>
    <td><input size=\"40\" type=\"text\" value=\"\" name=\"sub\"></td></tr>";
$output .= "<tr><td onmouseover=\"this.T_STICKY=true;return escape('".$txt_lists_hlp_unsub[$lang]."')\" valign=\"top\" align=\"right\">".$txt_lists_title_unsub[$lang]."</td>
    <td><input size=\"40\" type=\"text\" value=\"\" name=\"unsub\"></td></tr>";

$output .= "<tr><td colspan=\"2\"><b>".$txt_lists_main_title_webarchive[$lang]."</b></td></tr>";
$output .= getListOptionsWABoolean("webarchive");
$output .= getListOptionsWATextarea($list_path,"rcfile");
$output .= getListOptionsWABooleanActions("recreatewa");
$output .= getListOptionsWABooleanActions("deletewa");

return $output;
}

?>
