<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");

$recover_txt = "<a href=\"/dtc/\">"._("Client panel")."</a> -
<a href=\"/dtcemail\">". _("Email panel") ."</a> -
<a href=\"new_account.php\">". _("Register a new account") ."</a> -
". _("Recover password")."<br>";


if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "recover_lost_pass"){
	if( isset($_REQUEST["adm_lost_login"]) && isDTCLogin($_REQUEST["adm_lost_login"])){
		$q = "SELECT * FROM $pro_mysql_admin_table,$pro_mysql_client_table WHERE $pro_mysql_admin_table.adm_login='".$_REQUEST["adm_lost_login"]."' AND $pro_mysql_client_table.id = $pro_mysql_admin_table.id_client;";
		$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$recover_txt .= _("Could not find such login into our database!");
		}else{
			$a = mysql_fetch_array($r);
			// Create the email message, add header and footer
			$message = _("Dear customer, You or somebody else tried the recovery password procedure. Here is your password: %%%RECOVERED_PASSWORD%%%");
			$msg = str_replace("%%%RECOVERED_PASSWORD%%%",$a["adm_pass"],$message);
			$msg = headAndTailEmailMessage($msg);

			// Send the email
			$headers = "From: ".$conf_webmaster_email_addr;
			mail($a["email"],"$conf_message_subject_header" . _("Your account password"),$msg,$headers);

			$recover_txt .= "<br><br>" . _("An email with your password has been sent to your address.") . "<br><br>";
		}
	}else if( isset($_REQUEST["adm_lost_email"]) && isValidEmail($_REQUEST["adm_lost_email"])){
		$q = "SELECT * FROM $pro_mysql_admin_table,$pro_mysql_client_table WHERE $pro_mysql_client_table.email='".$_REQUEST["adm_lost_email"]."';";
		$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$recover_txt .= "<br><br>" . _("The following logins have been found to be related to this email address. Click on any of them to send your password to the email address:")."<br><br>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$login = $a["adm_login"];
			if($i != 0){
				$recover_txt .= " - ";
			}
			$recover_txt .= "<a href=\"".$_SERVER["PHP_SELF"]."?action=recover_lost_pass&adm_lost_login=$login\">$login</a>";
		}
	}
}else{
	$recover_l_txt = dtcFormTableAttrs();
	$recover_l_txt .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"action\" value=\"recover_lost_pass\">";
	$recover_l_txt .= dtcFormLineDraw( _("Login:") ,"<input type=\"text\" name=\"adm_lost_login\">",0);
	$recover_l_txt .= dtcFromOkDraw()."</table></form>";

	$recover_r_txt = dtcFormTableAttrs();
	$recover_r_txt .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"action\" value=\"recover_lost_pass\">";
	$recover_r_txt .= dtcFormLineDraw( _("Email:") ,"<input type=\"text\" name=\"adm_lost_email\">",0);
	$recover_r_txt .= dtcFromOkDraw()."</table></form>";

	$recover_txt .= '<table cellpadding="8" border="0"><tr><td>'.$recover_l_txt."</td><td>".$recover_r_txt."</td></table>";
}

$mypage = skin($conf_skin,$recover_txt, _("Client panel:") ." ". _("Recover password") );

if(function_exists("skin_NewAccountPage")){
	skin_NewAccountPage($mypage);
}else{
	echo anotherPage("Client:","","",makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));
}

?>
