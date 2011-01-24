<?php

function mailTicketToAllAdmins($subject,$body,$adm_login){
	global $pro_mysql_tik_admins_table;
	global $conf_webmaster_email_addr;
	global $send_email_header;

	global $conf_message_subject_header;

	if(isset($_REQUEST["server_hostname"])){
		$thehostname = "Server host name: ".$_REQUEST["server_hostname"];
	}else{
		$thehostname = "";
	}

	$q = "SELECT * FROM $pro_mysql_tik_admins_table WHERE available='yes';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$content = "A customer has submitted a support ticket.
Below is a copy of his message:

**********
Subject: ".stripslashes($subject)."
Admin login: $adm_login
$thehostname

".stripslashes($body)."
**********
";
		$headers = $send_email_header;
		$headers .= "X-DTC-Support-Ticket: Reply-From-Customer\n";
		$headers .= "From: ".$conf_webmaster_email_addr;
		mail($a["email"],"$conf_message_subject_header $adm_login has submitted a support ticket",$content,$headers);
	}
}

// action=new_ticket&subject=test+subject&server_hostname=test.vpsserver.com%3A01&issue_cat_id=network&ticketbody=I+can%27t+connect+to+my+VPS%21
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "new_ticket"){
	checkLoginPass($adm_login,$adm_pass);
	if( strlen($_REQUEST["subject"]) == 0){
		echo _("Subject line empty: cannot send ticket!");
	}else{
		$hash = createSupportHash();
		$q = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,subject,text,cat_id,initial_ticket,server_hostname,hash)
		VALUES ('','$adm_login','".date("Y-m-d")."','".date("H:i:s")."','".mysql_real_escape_string($_REQUEST["subject"])."','".mysql_real_escape_string($_REQUEST["ticketbody"])."','".mysql_real_escape_string($_REQUEST["issue_cat_id"])."','yes','".mysql_real_escape_string($_REQUEST["server_hostname"])."','$hash');";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		mailTicketToAllAdmins($_REQUEST["subject"],$_REQUEST["ticketbody"],$adm_login);
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_ticket_reply"){
	checkLoginPass($adm_login,$adm_pass);
	if(!isRandomNum($_REQUEST["last_tik_id"]) || !isRandomNum($_REQUEST["tik_id"])){
		echo _("last_tick_id or tik_id is not a number: hacking attempt!");
	}else{
		// Insert the new ticket
		$q = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,subject,text,cat_id,initial_ticket,server_hostname,in_reply_of_id,request_close)
		VALUES ('','$adm_login','".date("Y-m-d")."','".date("H:i:s")."','".mysql_real_escape_string($_REQUEST["subject"])."','".mysql_real_escape_string($_REQUEST["ticketbody"])."','".mysql_real_escape_string($_REQUEST["cat_id"])."','no','".mysql_real_escape_string($_REQUEST["server_hostname"])."','".mysql_real_escape_string($_REQUEST["last_tik_id"])."','".mysql_real_escape_string($_REQUEST["request_to_close"])."');";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		$ins_id = mysql_insert_id();
		// Update the chained list of tickets
		$q = "UPDATE $pro_mysql_tik_queries_table SET reply_id='$ins_id' WHERE id='".$_REQUEST["last_tik_id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		// Set the initial ticket as reopen in case it was closed
		$q = "UPDATE $pro_mysql_tik_queries_table SET closed='no' WHERE id='".$_REQUEST["tik_id"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		mailTicketToAllAdmins($_REQUEST["subject"],$_REQUEST["ticketbody"],$adm_login);
	}
}

?>