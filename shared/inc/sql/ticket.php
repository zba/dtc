<?php

function mailTicketToAllAdmins($subject,$body){
	global $pro_mysql_tik_admins_table;
	global $conf_webmaster_email_addr;

	$q = "SELECT * FROM $pro_mysql_tik_admins_table WHERE available='yes';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$content = "A customer has submitted a support ticket.
Below is a copy of his message:

**********
Subject: ".stripslashes($subject)."

".stripslashes($body)."
**********
";
		$headers = "From: ".$conf_webmaster_email_addr;
		mail($a["email"],"[DTC] A customer has submitted a support ticket",$content,$headers);
	}
}

// action=new_ticket&subject=test+subject&server_hostname=test.vpsserver.com%3A01&issue_cat_id=network&ticketbody=I+can%27t+connect+to+my+VPS%21
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "new_ticket"){
	if( strlen($_REQUEST["subject"]) == 0){
		echo "Subject line empty: cannot send ticket!";
	}else{
		$q = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,subject,text,cat_id,initial_ticket,server_hostname)
		VALUES ('','$adm_login','".date("Y-m-d")."','".date("H:i:s")."','".addslashes($_REQUEST["subject"])."','".addslashes($_REQUEST["ticketbody"])."','".addslashes($_REQUEST["issue_cat_id"])."','yes','".addslashes($_REQUEST["server_hostname"])."');";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
		mailTicketToAllAdmins($_REQUEST["subject"],$_REQUEST["ticketbody"]);
	}
}

if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_ticket_reply"){
	$q = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,subject,text,cat_id,initial_ticket,server_hostname,in_reply_of_id,request_close)
	VALUES ('','$adm_login','".date("Y-m-d")."','".date("H:i:s")."','".addslashes($_REQUEST["subject"])."','".addslashes($_REQUEST["ticketbody"])."','".addslashes($_REQUEST["cat_id"])."','no','".addslashes($_REQUEST["server_hostname"])."','".addslashes($_REQUEST["last_tik_id"])."','".addslashes($_REQUEST["request_to_close"])."');";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	$ins_id = mysql_insert_id();
	$q = "UPDATE $pro_mysql_tik_queries_table SET reply_id='$ins_id' WHERE id='".$_REQUEST["last_tik_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
	mailTicketToAllAdmins($_REQUEST["subject"],$_REQUEST["ticketbody"]);
}

?>
