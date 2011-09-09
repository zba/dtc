<?php

require_once("../shared/autoSQLconfig.php");
$panel_type="client";
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");



function recover_enter_login_or_email(){
	$recover_l_txt = dtcFormTableAttrs();
	$recover_l_txt .= "<form action=\"?\">
<input type=\"hidden\" name=\"subaction\" value=\"do_send_recovery_token\">";
	$recover_l_txt .= dtcFormLineDraw( _("Login:") ,"<table border=\"0\"><tr><td><input type=\"text\" name=\"adm_lost_login\">
						<td>".submitButtonStart()._("Recover login").submitButtonEnd()."</td></tr></table>",0);
	$recover_l_txt .= "</table></form>";

	// or an email
	$recover_r_txt = dtcFormTableAttrs();
	$recover_r_txt .= "<form action=\"?\">
<input type=\"hidden\" name=\"subaction\" value=\"select_login_out_of_email\">";
	$recover_r_txt .= dtcFormLineDraw( _("Email:") ,"<table border=\"0\"><tr><td><input type=\"text\" name=\"adm_lost_email\"></td>
						<td>".submitButtonStart()._("Search email").submitButtonEnd()."</td></tr></table>",0);
	$recover_r_txt .= "</table></form>";

	return "<br><br>"._("Enter a login or email address to recover a password:").'<br>
<br>
<table cellpadding="8" border="0"><tr><td>'.$recover_l_txt."</td><td>"._("Or")."</td><td>".$recover_r_txt."</td></table>";
}

function select_login_out_of_email(){
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;

	if(!isValidEmail($_REQUEST["adm_lost_email"])){
		return _("Invalid email address.");
	}
	$recover_txt = "";
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
		$recover_txt .= "<a href=\"?subaction=do_send_recovery_token&adm_lost_login=$login\">$login</a>";
	}
	return $recover_txt;
}

function send_password_recover_token(){
	global $pro_mysql_admin_table;
	global $pro_mysql_client_table;
	global $conf_administrative_site;
	global $conf_webmaster_email_addr;
	global $conf_message_subject_header;
	global $send_email_header;

	if(!isDTCLogin($_REQUEST["adm_lost_login"])){
		return _("Wrong parameter");
	}

	$recover_txt = "";
	$q = "SELECT * FROM $pro_mysql_admin_table,$pro_mysql_client_table WHERE $pro_mysql_admin_table.adm_login='".$_REQUEST["adm_lost_login"]."' AND $pro_mysql_client_table.id = $pro_mysql_admin_table.id_client;";
	$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		$recover_txt .= _("Could not find login.");
	}else{
		$a = mysql_fetch_array($r);
		$my_token = "tok".getRandomValue().getRandomValue();
		$timestamp_expire = mktime() + (60*60);	// The timestamp expires in 1 hour from now
		$q = "UPDATE $pro_mysql_admin_table SET recovery_token='$my_token',recovery_timestamp='$timestamp_expire' WHERE adm_login='".$_REQUEST["adm_lost_login"]."';";
		$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());

		// Create the email message, add header and footer
		$message = "
"._("Dear customer,

You recently requested that your login information be reset. If you didn't do
such a password recovery request, simply ignore this message. Otherwise, to
recover your password, please visit the following web address:");
		$message .= "
https://$conf_administrative_site/dtc/recover_pass.php?adm_lost_login=3D".$_REQUEST["adm_lost_login"]."&subaction=3Ddo_recovery_type_pass&token=3D".$my_token."
";
		$msg = headAndTailEmailMessage($message);

		// Send the email
		$headers = $send_email_header;
		$headers .= "From: ".$conf_webmaster_email_addr;
		mail($a["email"],$conf_message_subject_header . " " . _("Account password recovery for ").$conf_administrative_site,$msg,$headers);

		$recover_txt .= "<br><br>" . _("An email with the password recovery procedure has been sent to your address.") . "<br><br>";
	}
	return $recover_txt;
}

function do_recovery_new_pass_form(){
	if( !isDTCLogin($_REQUEST["adm_lost_login"])){
		return _("Login format incorrect");
	}
	$recover_txt = dtcFormTableAttrs()."<form action=\"?\">
<input type=\"hidden\" name=\"subaction\" value=\"do_recovery_validate_new_pass\">
<input type=\"hidden\" name=\"adm_lost_login\" value=\"".$_REQUEST["adm_lost_login"]."\">
<input type=\"hidden\" name=\"token\" value=\"".$_REQUEST["token"]."\">
";
	$recover_txt .= dtcFormLineDraw( _("New password:") ,"<input type=\"password\" name=\"adm_new_pass1\">",0);
	$recover_txt .= dtcFormLineDraw( _("New password (confirm):") ,"<input type=\"password\" name=\"adm_new_pass2\">",1);
	$recover_txt .= dtcFromOkDraw()."</table></form>";
	return $recover_txt;
}

function do_recovery_validate_recovery(){
	global $pro_mysql_admin_table;
	global $conf_enforce_adm_encryption;
	if( !isDTCLogin($_REQUEST["adm_lost_login"])){
		return _("Login format incorrect");
	}
	if(!isDTCPassword($_REQUEST["adm_new_pass1"]) || !isDTCPassword($_REQUEST["adm_new_pass2"])){
		return _("Your new password doesn't seem to be in a valid format. Please use only letters and numbers, at and least 4 characters");
	}
	if($_REQUEST["adm_new_pass1"] != $_REQUEST["adm_new_pass1"]){
		return _("Password 1 doesn't match password 2.");
	}
	if(check_password($_REQUEST["adm_new_pass1"]) !== FALSE){
		return _("The new password you choosed is one of the most used on the internet, so we wont accept it.");
	}
	$q = "SELECT recovery_token,recovery_timestamp FROM $pro_mysql_admin_table WHERE adm_login='".$_REQUEST["adm_lost_login"]."' AND recovery_token='".$_REQUEST["token"]."';";
	$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		return _("Cannot find the recovery token in the database");
	}else{
		$a = mysql_fetch_array($r);
		if(mktime() > $a["recovery_timestamp"]){
			return _("The recovery password procedure has timed out: you wont be able to reset your password.");
		}else{
			if($conf_enforce_adm_encryption == "yes"){
				$new_encrypt_dtcadm_pass = "SHA1('".$_REQUEST["adm_new_pass1"]."')";
			}else{
				$new_encrypt_dtcadm_pass = "'".$_REQUEST["adm_new_pass1"]."'";
			}
			$q = "UPDATE $pro_mysql_admin_table SET adm_pass=$new_encrypt_dtcadm_pass WHERE adm_login='".$_REQUEST["adm_lost_login"]."';";
			$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			return _("Your account has been updated with the new password");
		}
	}
}

function check_token(){
	if( !isset($_REQUEST["token"]) || substr($_REQUEST["token"],0,3) != "tok" || !isRandomNum(substr($_REQUEST["token"],3))){
		return FALSE;
	}
	return substr($_REQUEST["token"],3);
}

function recover_password(){
	$given_token = check_token();
	if($given_token === FALSE){
		if( !isset($_REQUEST["subaction"])){
			// Setp 1: enter a login
			return recover_enter_login_or_email();
		}else if($_REQUEST["subaction"] == "select_login_out_of_email"){
			// Step 2: select a login out of email addresses
			return select_login_out_of_email();
		}else if($_REQUEST["subaction"] == "do_send_recovery_token"){
			// Step 3: send password recovery token
			return send_password_recover_token();
		}else{
			return _("Recovery password parameter error: ")."Token but not select_login_out_of_email";
		}
	}else{
		if(!isset($_REQUEST["subaction"])){
			return _("Recovery password parameter error: ")."No subaction";
		}
		switch($_REQUEST["subaction"]){
		case "do_recovery_type_pass":
			// Step 4: type a new password
			return do_recovery_new_pass_form();
			break;
		case "do_recovery_validate_new_pass":
			// Step 5: validate new password
			return do_recovery_validate_recovery();
			break;
		default:
			return _("Recovery password parameter error:")."Subaction not understood";
			break;
		}
	}
}
$recover_txt = "<a href=\"/dtc/\">"._("Client panel")."</a> -
<a href=\"/dtcemail\">". _("Email panel") ."</a> -
<a href=\"new_account.php\">". _("Register a new account") ."</a> -
". _("Recover password")."<br>";
$recover_txt .= recover_password();
$mypage = skin($conf_skin,$recover_txt, _("Client panel:") ." ". _("Recover password") );

if(function_exists("skin_NewAccountPage")){
	skin_NewAccountPage($mypage);
}else{
	echo anotherPage("Client:","","",makePreloads(),$anotherTopBanner,"",$mypage,anotherFooter(""));
}

?>