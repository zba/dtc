<?php

function drawAdminTools_NameServers($admin){
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $pro_mysql_subdomain_table;
	global $pro_mysql_nameservers_table;
	global $pro_mysql_domain_table;

	$out = "";

	if(isset($_REQUEST["subdomain"]))	$subdomain = $_REQUEST["subdomain"];
	if(isset($_REQUEST["domain_name"]))	$domain_name = $_REQUEST["domain_name"];
	if(isset($_REQUEST["ip"]))		$ip = $_REQUEST["ip"];

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "new_nameserver"){
		checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name);
		$regz = registry_add_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip);
		if($regz["is_success"] == 1){
			$out .= "<font color=\"green\"><b>". _("Registration of your name server was succesful") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i><br>";
			$query = "SELECT * FROM $pro_mysql_subdomain_table WHERE domain_name='$domain_name' AND subdomain_name='$subdomain';";
			$result = mysql_query($query)or die("Cannot query \"query\" !!!".mysql_error());
			$num_rows = mysql_num_rows($result);
			if($num_rows == 0){
				$query = "INSERT INTO $pro_mysql_subdomain_table (id,
domain_name,subdomain_name,webalizer_generate,ip)VALUES('','$domain_name','$subdomain','no','$ip');";
			}else if($num_rows == 1){
				$query = "UPDATE $pro_mysql_subdomain_table SET ip='$ip'
					WHERE domain_name='$domain_name' AND subdomain_name='$subdomain' LIMIT 1;";
			}else{
				die("Subdomain table problem: twice the same subdomain !");
			}
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
			$query = "INSERT INTO $pro_mysql_nameservers_table(id,
owner,domain_name,subdomain,ip)VALUES(
'','$adm_login','$domain_name','$subdomain','$ip');";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		}else{
			$out .= "<font color=\"red\"><b>". _("Registration of your name server failed") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i><br>";
		}
	}
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit_nameserver"){
		checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name);
		$regz = registry_modify_nameserver($adm_login,$adm_pass,$subdomain,$domain_name,$ip);
		if($regz["is_success"] == 1){
			 $out .= "<font color=\"green\"><b>". _("Addition of name server was succesfull") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i><br>";
			$query = "UPDATE $pro_mysql_subdomain_table SET ip='$ip'
				WHERE domain_name='$domain_name' AND subdomain_name='$subdomain' LIMIT 1;";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
			$query = "UPDATE $pro_mysql_nameservers_table SET ip='$ip'
				WHERE domain_name='$domain_name' AND subdomain='$subdomain' LIMIT 1;";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		}else{
			$out .= "<font color=\"red\"><b>". _("Addition of name server failed") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i><br>";
		}
	}
 
	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete_nameserver"){
		checkLoginPassAndDomain($adm_login,$adm_pass,$domain_name);
		$regz = registry_delete_nameserver($adm_login,$adm_pass,$subdomain,$domain_name);
		if($regz["is_success"] == 1){
			$out .= "<font color=\"green\"><b>". _("Deletion of name server was succesful") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i><br>";
			$query="DELETE FROM $pro_mysql_nameservers_table
				WHERE domain_name='$domain_name' AND subdomain='$subdomain' LIMIT 1";
			mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		}else{
			$out .= "<font color=\"red\"><b>". _("Deletion of name server failed") ."</b></font><br>
". _("Server said: ") ."<i>" . $regz["response_text"] . "</i><br>";
		}
	}

	$out .= "<b><u>". _("List of your registred name-servers:") ."</u></b><br>";

	$query = "SELECT * FROM $pro_mysql_nameservers_table WHERE owner='$adm_login';";
	$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
        $num_rows = mysql_num_rows($result);
        for($i=0;$i<$num_rows;$i++){
		$row = mysql_fetch_array($result);
		if($i > 0){
			$out .= " - ";
		}
		$out .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_id=" .
			$row["id"] ."\">" . $row["subdomain"] . "." . $row["domain_name"] . "</a>";
	}

	if(isset($_REQUEST["edit_id"]) && $_REQUEST["edit_id"] != ""){
		$query = "SELECT * FROM $pro_mysql_nameservers_table WHERE id='". $_REQUEST["edit_id"] ."' AND owner='$adm_login';";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		if(mysql_num_rows($result) != 1) die("Nameserver not found !!!");
		$row = mysql_fetch_array($result);
		$out .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink\">New name server</a><br>
<b><u>". _("Edit a name server:") ."</u></b><br>
<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\"> 
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">   
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">   
<input type=\"hidden\" name=\"action\" value=\"edit_nameserver\">
<input type=\"hidden\" name=\"domain_name\" value=\"". $row["domain_name"] ."\">
<input type=\"hidden\" name=\"subdomain\" value=\"". $row["subdomain"] ."\">
". _("Name server hostname: ") .$row["subdomain"] .".". $row["domain_name"] ."<br>
<input type=\"hidden\" name=\"edit_id\" value=\"". $_REQUEST["edit_id"] ."\">
". _("IP address of name server:") ."<input type=\"text\" name=\"ip\" value=\"". $row["ip"] ."\">
<input type=\"submit\" value=\"Ok\">
</form>
<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\"> 
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">    
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">    
<input type=\"hidden\" name=\"action\" value=\"delete_nameserver\">
<input type=\"hidden\" name=\"domain_name\" value=\"". $row["domain_name"] ."\">
<input type=\"hidden\" name=\"subdomain\" value=\"". $row["subdomain"] ."\">
<input type=\"hidden\" name=\"delete_id\" value=\"". $_REQUEST["edit_id"] ."\">
<input type=\"submit\" value=\"". _("Delete name server") ."\">
</form>
";
	}else{
		$out .= "<br><br><b><u>". _("Register a new name server:") ."</u></b><br>
		". _("What subzone do you want to use (example: ns1):") ."
<form action=\"?\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">   
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">   
<input type=\"hidden\" name=\"action\" value=\"new_nameserver\">
<input type=\"text\" name=\"subdomain\" value=\"\"><br>";
		$query = "SELECT * FROM $pro_mysql_domain_table WHERE owner='$adm_login';";
		$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		$out .= _("Select one of your domain names to use as an additional name server with the domain registry:") ."<br>
		<select name=\"domain_name\">";
		for($i=0;$i<$num_rows;$i++){
			$row = mysql_fetch_array($result);
			$out .= "<option value=\"" . $row["name"] . "\">" . $row["name"] . "</option>";
		}
		$out .= "</select><br>
			". _("IP address of name server:") ."
			<input type=\"text\" name=\"ip\" value=\"\"><br>";
		$out .= "<input type=\"submit\" value=\"Ok\">
</form>
<br>";
	}

	return $out;
}

?>
