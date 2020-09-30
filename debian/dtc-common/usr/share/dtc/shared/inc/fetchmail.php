<?php

function gethostbynameFalse($nametotest){
   $ipaddress = $nametotest;
   $ipaddress = gethostbyname($nametotest);
   if ($ipaddress == $nametotest) {
       return false;
   }else {
       return $ipaddress;
   }
}

function checkMailbox($user,$host,$email,$mailbox_type,$server,$login,$password){
	global $errTxt;
	// Check function parameters...
	switch($mailbox_type){
	case "MSN":
	case "HOTMAIL":{
		if($mailbox_type == "MSN"){
			$domain = "msn.com";
		}else{
			$domain = "hotmail.com";
		}
		echo $login;
		if(!isMailPassword($login)){
			if(false != strstr('@',$login)){
				$errTxt = "Mail login format not correct, you shouldn't happen ".'@'."$domain to login)";
			}else{
				$errTxt = "Mail login format not correct!";
			}
			return false;
		}
		if(!isMailPassword($password)){
			$errTxt = "Mail password format not correct!";
			return false;
		}
		// This one need field checking, otherwise a user can execute a system wide command under nobody:nogroup user...
		$cmd = "gotmail -u $login".'@'."$domain -d $domain -p $password -s localhost -f $user".'@'."$host --nodownload";
		echo $cmd;
		$out = exec($cmd,$output,$ret_val);
		$cmd_console = "";
		for($i=0;$i<sizeof($output);$i++){
			echo $output[$i]."<br>";
			$cmd_console .= $output[$i]."<br>";
		}
		if($ret_val != 0){
			$errTxt = "Could not get mail from $domain: $cmd_console";
			return false;
		}
		}break;
	case "POP3":{
		// Check email format
		if(!isValidEmail($email)){
			$errTxt = "Give email is not valid $email";
			return false;
		}
		// echo "Checking POP3<br>";
		if(($server_ip = gethostbynameFalse($server)) == false){
			$errTxt = "Cannot resolv your pop3 server, maybe you entered a wrong address: $server";
			return false;
		}
		// echo "Opening socket<br>";
		$soc = fsockopen($server_ip,110,$erno,$errstring,10);
		if($soc == false){
			$errTxt = "Could not connect to pop3 server (timed out): $server";
			return false;
		}
		// echo "Checking ok after connect<br>";
		$popline = fgets($soc,1024);
		if(!strstr($popline,"+OK")){
			$errTxt = "Server did not send OK after connect, maybe wrong server or server is down: $popline";
			return false;
		}
		// echo "Sending login<br>";
		if(!fwrite($soc,"USER $login\n")){
			$errTxt = "Could not write USER $login to server";
			return false;
		}
		// echo "Checking ok after login<br>";
		$popline = fgets($soc,1024);
		if(!strstr($popline,"+OK")){
			$errTxt = "Server did not send OK after USER, maybe login is wrong: $popline";
			return false;
		}
		//echo "Sending pass<br>";
		if(!fwrite($soc,"PASS $password\n")){
			$errTxt = "Could not write to pop3 server for password";
			return false;
		}
		//echo "Checking ok after pass<br>";
		$popline = fgets($soc,1024);
		if(!strstr($popline,"+OK")){
			$errTxt = "Server didn't accept your login/pass: $popline";
			return false;
		}
		//echo "Closing socket<br>";
		fclose($soc);
		}break;
	default:{
		$errTxt = "Mailbox type not supported (yet?)!";
		return false;
		}break;
	}
	$errTxt = "Successfully checked and added mail account.";
	return true;
}



?>