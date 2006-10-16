<?php

// action=new_ticket&subject=test+subject&server_hostname=test.vpsserver.com%3A01&issue_cat_id=network&ticketbody=I+can%27t+connect+to+my+VPS%21
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "new_ticket"){
	$q = "INSERT INTO $pro_mysql_tik_queries_table (id,adm_login,date,time,subject,text,cat_id,initial_ticket,server_hostname)
	VALUES ('','$adm_login','".date("Y-m-d")."','".date("H:i:s")."','".addslashes($_REQUEST["subject"])."','".addslashes($_REQUEST["ticketbody"])."','".addslashes($_REQUEST["issue_cat_id"])."','yes','".addslashes($_REQUEST["server_hostname"])."');";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
}

?>