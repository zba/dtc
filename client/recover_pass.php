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
	if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "do_recovery_type_pass" && isset($_REQUEST["token"]) && $_REQUEST["token"] != ""){
		if( !isDTCLogin($_REQUEST["adm_lost_login"])){
			$recover_txt = _("Login format incorrect");
		}else if(!isRandomNum($_REQUEST["token"])){
			$recover_txt = _("The recovery password token doesn't seem correct");
		}else{
			$recover_txt = dtcFormTableAttrs()."<form action=\"?\">
<input type=\"hidden\" name=\"action\" value=\"recover_lost_pass\">
<input type=\"hidden\" name=\"subaction\" value=\"do_recovery_validate_new_pass\">
<input type=\"hidden\" name=\"adm_lost_login\" value=\"".$_REQUEST["adm_lost_login"]."\">
<input type=\"hidden\" name=\"token\" value=\"".$_REQUEST["token"]."\">
";
			$recover_l_txt .= dtcFormLineDraw( _("New password:") ,"<input type=\"password\" name=\"adm_new_pass1\">",0);
			$recover_l_txt .= dtcFormLineDraw( _("New password (confirm):") ,"<input type=\"password\" name=\"adm_new_pass2\">",1);
			$recover_l_txt .= dtcFromOkDraw()."</table></form>";
		}
	}else if(isset($_REQUEST["subaction"]) && $_REQUEST["subaction"] == "do_recovery_validate_new_pass"){
		if( !isDTCLogin($_REQUEST["adm_lost_login"])){
			$recover_txt = _("Login format incorrect");
		}else if(!isRandomNum($_REQUEST["token"])){
			$recover_txt = _("The recovery password token doesn't seem correct");
		}else if(!isDTCPassword($_REQUEST["adm_new_pass1"])){
			$recover_txt = _("Your new password doesn't seem to be in a valid format. Please use only letters and numbers, at and least 4 characters");
		}else if($_REQUEST["adm_new_pass1"] != $_REQUEST["adm_new_pass1"]){
			$recover_txt = _("Password 1 doesn't match password 2.");
		}else if(check_password($_REQUEST["adm_new_pass1"]) === FALSE){
			$q = "SELECT recovery_token,recovery_timestamp FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["adm_lost_login"]."' AND recovery_token='".$_REQUEST["token"]."';";
			$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			if($n != 1){
				$recover_txt = _("Cannot find the recovery token in the database");
			}else{
				$a = mysql_fetch_array($r);
				if(mktime() > $a["recovery_timestamp"]){
					$recover_txt = _("The recovery password procedure has timed out: you wont be able to reset your password.");
				}else{
					if($conf_enforce_adm_encryption == "yes"){
						$new_encrypt_dtcadm_pass = "SHA1('".$_REQUEST["adm_new_pass1"]."')";
					}else{
						$new_encrypt_dtcadm_pass = "'".$_REQUEST["adm_new_pass1"]."'";
					}
					$q = "UPDATE $pro_mysql_admin_table SET adm_pass=$new_encrypt_dtcadm_pass WHERE adm_login='".$_REQUEST["adm_lost_login"]."';";
					$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
					$recover_txt = _("Your account has been updated with the new password");
				}
			}
		}else{
			$recover_txt = _("The new password you choosed is one of the most used on the internet, so we wont accept it.");
		}
	}else if( isset($_REQUEST["adm_lost_login"]) && isDTCLogin($_REQUEST["adm_lost_login"])){
		$q = "SELECT * FROM $pro_mysql_admin_table,$pro_mysql_client_table WHERE $pro_mysql_admin_table.adm_login='".$_REQUEST["adm_lost_login"]."' AND $pro_mysql_client_table.id = $pro_mysql_admin_table.id_client;";
		$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$recover_txt .= _("Could not find login.");
		}else{
			$a = mysql_fetch_array($r);
			$token = getRandomValue().getRandomValue();
			$timestamp_expire = mktime() + (60*60);	// The timestamp expires in 1 hour from now
			$q = "UPDATE $pro_mysql_admin_table SET recovery_token='$token',recovery_timestamp='$timestamp_expire' WHERE adm_login='".$_REQUEST["adm_lost_login"]."';";
			$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

			// Create the email message, add header and footer
			$message = _("Dear customer,

You recently requested that your login information be reset. If you didn't do
such a password recovery request, simply ignore this message. Otherwise, to
recover your password, please visit the following web address:");
			$message .= "
https://$conf_administrative_site/dtc/recover_pass.php?action=recover_lost_pass&adm_lost_login=".$_REQUEST["adm_lost_login"]."&subaction=do_recovery_type_pass&token=$token
";
			$msg = headAndTailEmailMessage($msg);

			// Send the email
			$headers = $send_email_header;
			$headers .= "From: ".$conf_webmaster_email_addr;
			mail($a["email"],"$conf_message_subject_header" . _("Account password recovery for ").$conf_administrative_site,$msg,$headers);

			$recover_txt .= "<br><br>" . _("An email with the password recovery procedure has been sent to your address.") . "<br><br>";
		}
	}else if( isset($_REQUEST["adm_lost_email"]) && isValidEmail($_REQUEST["adm_lost_email"])){
		$q = "SELECT * FROM $pro_mysql_admin_table,$pro_mysql_client_table WHERE $pro_mysql_client_table.email='".$_REQUEST["adm_lost_email"]."' AND $pro_mysql_admin_table.id_client=$pro_mysql_client_table.id;";
		$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		$recover_txt .= "<br><br>" . _("The following logins have been found to be related to this email address. Click on any of them to send your password recovery to the email address:")."<br><br>";
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$login = $a["adm_login"];
			if($i != 0){
				$recover_txt .= " - ";
			}
			$recover_txt .= "<a href=\"?action=recover_lost_pass&adm_lost_login=$login\">$login</a>";
		}
	}
}else{
	$recover_l_txt = dtcFormTableAttrs();
	$recover_l_txt .= "<form action=\"?\">
<input type=\"hidden\" name=\"action\" value=\"recover_lost_pass\">";
	$recover_l_txt .= dtcFormLineDraw( _("Login:") ,"<input type=\"text\" name=\"adm_lost_login\">",0);
	$recover_l_txt .= dtcFromOkDraw()."</table></form>";

	$recover_r_txt = dtcFormTableAttrs();
	$recover_r_txt .= "<form action=\"?\">
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
