<?php

//require("/usr/share/dtc/shared/strings.php");
require("$dtcshared_path/strings.php");
require_once "$dtcshared_path/inc/paiement.php";


function drawAdminTools_MyAccount($admin){
	global $PHP_SELF;
	global $adm_login;
	global $adm_pass;
	global $addrlink;

	global $cc_code_array;

	$frm_start = "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
";

	$out .= "<font color=\"red\">IN DEVELOPMENT: DO NOT USE</font><br>";

	$id_client = $admin["info"]["id_client"];

	$stats = fetchAdminStats($admin);

	if($id_client != 0 && $_REQUEST["action"] == "refund_myaccount"){
		$out .= "<b><u>Pay \$".$_REQUEST["refund_amount"]." on my account:</u></b><br>";
		$out .=" Please click on the button bellow to refund your account. Then,
when paiement is done, click the refresh button.";
		$out .= "<center><font size=\"+1\">\$".$_REQUEST["refund_amount"]."</font><br>".
		paynowButton(0,$_REQUEST["refund_amount"]);
		$out .= "<br><br>$frm_start<input type=\"submit\" value=\"Refresh and see my account\"></form></center>";
		return $out;
	}

	$out .= "<b><u>Transfère and disk usage:</u></b>";
	$out .= "<br>Transfer this month: ". smartByte($stats["total_transfer"]);
	if($id_client != 0){
		$bw_quota = $admin["info"]["bandwidth_per_month_mb"]*1024*1024;
		$out .= " / ".smartByte($bw_quota)."<br>";
		$out .= drawPercentBar($stats["total_transfer"],$bw_quota);
	}
	$out .= "Total disk usage: ".smartByte($stats["total_du"]);
	if($id_client != 0){
		$du_quota = $admin["info"]["quota"]*1024*1024;
		$out .= " / ".smartByte($du_quota)."<br>";
		$out .= drawPercentBar($stats["total_du"],$du_quota);
		$out .= "<br>$frm_start<input type=\"hidden\" name=\"action\" value=\"upgrade_myaccount\">
<input type=\"submit\" value=\"Upgrade my account\">
</form><br>";
	}

	if($id_client != 0){
		if($_REQUEST["action"] == "refund_myaccount"){
			$out .= "<b><u>Pay \$".$_REQUEST["refund_amount"]." on my account:</u></b><br>";
			$out .=" Please click on the button bellow to refund your account. Then,
when paiement is done, click the refresh button.";
			$out .= "<center><font size=\"+1\">\$".$_REQUEST["refund_amount"]."</font><br>".
			paynowButton(0,$_REQUEST["refund_amount"]);
			$out .= "<br><br>$frm_start<input type=\"submit\" value=\"Refresh and see my account\"></form></center>";
			return $out;
		}
		$client = $admin["client"];

		$out .=  "<b><u>Remaining money on my account:</u></b><br>
<table width=\"100%\" height=\"1\" cellpadding=\"4\" cellspacing=\"0\" border=\"0\">
<tr>
	<td><font size=\"+1\">\$".$client["dollar"]."</font></td>
	<td><font size=\"-1\">Refund my account:</font><br>
$frm_start<input type=\"hidden\" name=\"action\" value=\"refund_myaccount\">
\$<input size=\"8\" type=\"text\" name=\"refund_amount\" value=\"\">
<input type=\"submit\" value=\"Ok\">
</form></td></tr>
</table>
<hr width=\"90%\">
";

		$out .= "<center><b>Please tell us if the following is not correct:</b></center>";

		if($client["is_company"] == "yes"){
			$out .= "Company: ".$client["company_name"]."<br>";
		}
		$out .= "Firstname: " .$client["christname"]."<br>";
		$out .= "Familyname: " .$client["familyname"]."<br>";
		$out .= "Addresse 1: " .$client["addr1"]."<br>";
		$out .= "Addresse 2: " .$client["addr2"]."<br>";
		$out .= "Zipcode: " .$client["zipcode"]."<br>";
		$out .= "Sate: " .$client["state"]."<br>";
		$out .= "Country: " . $cc_code_array[ $client["country"] ] ."<br>";
		$out .= "Phone: " .$client["phone"]."<br>";
		$out .= "Fax: " .$client["fax"]."<br>";
		$out .= "Email: " .$client["email"]."<br>";
	}else{
		$out .= "You do not have a client account, so there
is no money in your account.";
	}
	return $out;

}


////////////////////////////////////////////////////////////////////////////
// Draw the form for configuring global admin account info (path, etc...) //
////////////////////////////////////////////////////////////////////////////
function drawEditAdmin($admin){
	global $lang;
	global $txt_password;
	global $txt_path;
	global $txt_id_client;
	global $txt_del_user;
	global $txt_del_user_confirm;
	global $txt_del_user_domain;
	global $txt_del_user_domain_confirm;
	global $txt_new_domain_for_user;

	global $adm_login;
	global $adm_pass;

	$info = $admin["info"];
	$data = $admin["data"];

	$adm_cur_pass = $info["adm_pass"];
	$adm_path = $info["path"];
	$adm_max_email = $info["max_email"];
	$adm_max_ftp = $info["max_ftp"];
	$adm_quota = $info["quota"];
	$adm_id_client = $info["id_client"];

	// Generate the user configuration form
	$user_data .= "
<form action=\"?\" methode=\"post\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<table>
	<tr><td align=\"right\">".$txt_password[$lang]."</td>
	<td><input type=\"text\" name=\"changed_pass\" value=\"$adm_cur_pass\"></td></tr>
	<tr><td align=\"right\">".$txt_path[$lang]."</td>
	<td><input type=\"text\" name=\"changed_path\" value=\"$adm_path\"></td></tr>
	<tr><td align=\"right\">".$txt_id_client[$lang]."</td>
	<td><input type=\"text\" name=\"changed_id_client\" value=\"$adm_id_client\"></td></tr>
	<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"updateuserinfo\" value=\"Ok\">
</td></tr></table></form>";

	// Generate the admin tool configuration module
	// Deletion of domains :
	$url = "".$_SERVER["PHP_SELF"]."?delete_admin_user=$adm_login";
	$confirmed_url = dtcJavascriptConfirmLink($txt_del_user_confirm[$lang],$url);
	$domain_conf = "<a href=\"$confirmed_url\"><b>".$txt_del_user[$lang]."</b></a><br><br>";
	$domain_conf .= "<b><u>".$txt_del_user_domain[$lang]."</u><br>";
	$nbr_domain = sizeof($data);
	for($i=0;$i<$nbr_domain;$i++){
		$dom = $data[$i]["name"];
		if($i != 0){
			$domain_conf .= " - ";
		}
		$url = "?adm_login=$adm_login&adm_pass=$adm_pass&deluserdomain=$dom";
		$js_url = dtcJavascriptConfirmLink($txt_del_user_domain_confirm[$lang],$url);
		$domain_conf .= "<a href=\"$js_url\">$dom</a>";
	}
	$domain_conf .= "</b><br><br>";

	// Creation of domains :
	$domain_conf .= "<b><u>".$txt_new_domain_for_user[$lang]."</u>";

	$domain_conf .= "<form action=\"?\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"text\" name=\"newdomain_name\" value=\"\">
	<input type=\"submit\" name=\"newdomain\" value=\"Ok\">
	</form>";

	$conf_user .= "<font size=\"-1\"><table><tr><td>$domain_conf</td><td background=\"gfx/cadre04/trait06.gif\">&nbsp;</td><td>$user_data</td></tr></table>";
	$conf_user .= "</b></font> ";

	return $conf_user;
}
/////////////////////////////////////////////////////////////////
// Generate a tool for configuring all domain of one sub-admin //
/////////////////////////////////////////////////////////////////
function drawDomainConfig($admin){
	global $lang;
	global $txt_domain_tbl_config_dom_name;
	global $txt_domain_tbl_config_quota;
	global $txt_domain_tbl_config_max_email;
	global $txt_domain_tbl_config_max_ftp;
	global $txt_domain_tbl_config_max_subdomain;
	global $txt_domain_tbl_config_ip;

	global $conf_site_addrs;
	$site_addrs = explode("|",$conf_site_addrs);

	global $adm_login;
	global $adm_pass;

	$domains = $admin["data"];

	$nbr_domain = sizeof($domains);

	$ret = "<table cellpadding=\"2\" cellspacing=\"0\" border=\"1\">
			<tr><td>".$txt_domain_tbl_config_dom_name[$lang]."</td><td>".$txt_domain_tbl_config_quota[$lang]."</td><td>".$txt_domain_tbl_config_max_email[$lang]."</td>
			<td>".$txt_domain_tbl_config_max_ftp[$lang]."</td><td>".$txt_domain_tbl_config_max_subdomain[$lang]."</td><td>Zone generation</td><td>".$txt_domain_tbl_config_ip[$lang]."</td><td>GO !</td></tr>";
	for($i=0;$i<$nbr_domain;$i++){
		$tobe_edited = $domains[$i];
		$webname = $tobe_edited["name"];
		$quota = $tobe_edited["quota"];
		$max_email = $tobe_edited["max_email"];
		$max_ftp = $tobe_edited["max_ftp"];
		$max_subdomain = $tobe_edited["max_subdomain"];
		$ip_addr = $tobe_edited["ip_addr"];
		if($tobe_edited["generate_flag"] == "yes"){
			$webalizer_gen_flag_txt = "<font color=\"#00FF00\">YES</font>";
			$what_to_switch = "no";
		}else{
			$webalizer_gen_flag_txt = "<font color=\"#FF0000\">NO</font>";
			$what_to_switch = "yes";
		}
		$ret .= "<form action=\"".$_SERVER["PHP_SELF"]."\">
				<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
				<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
				<input type=\"hidden\" name=\"user_domain_to_modify\" value=\"$webname\"><tr>";

		$popup_txt = "<select name=\"new_ip_addr\">";
		$nbr_site_ip = sizeof($site_addrs);
		for($j=0;$j<$nbr_site_ip;$j++){
			$curr_ip = $site_addrs[$j];
			if($curr_ip == $ip_addr){
				$popup_txt .= "<option value=\"$curr_ip\" selected>$curr_ip";
			}else{
				$popup_txt .= "<option value=\"$curr_ip\">$curr_ip";
			}
		}
		$popup_txt .= "</select>";

		$ret .= "<td>$webname</td>
				<td><input type=\"text\" name=\"new_quota\" value=\"$quota\" size=\"5\"></td>
				<td><input type=\"text\" name=\"new_max_email\" value=\"$max_email\" size=\"5\"></td>
				<td><input type=\"text\" name=\"new_max_ftp\" value=\"$max_ftp\" size=\"5\"></td>
				<td><input type=\"text\" name=\"new_max_subdomain\" value=\"$max_subdomain\" size=\"5\"></td>
				<td><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&action=switch_generate_flag&domain=$webname&switch_to=$what_to_switch\">$webalizer_gen_flag_txt</a></td>
				<td>$popup_txt</td>
				";

		$ret .= "<td><input type=\"submit\" name=\"modify_domain_config\" value=\"Ok\"></tr></form>";
	}
	$ret .= "</table>";
	return $ret;
}


function drawAdminTools_DomainDNS($admin,$eddomain){
	global $lang;

	global $txt_other_mx_servers;
	global $txt_primary_mx_server;
	global $txt_other_dns_ip;
	global $txt_primari_dns_ip;
	global $txt_comment_confirurate_your_domain_name;
	global $txt_confirurate_your_domain_name;

	global $adm_login;
	global $adm_pass;
	global $addrlink;

	// The domain DNS configuration
	$domain_dns_mx_conf_form = "<table><tr><td align=\"right\" nowrap><form action=\"".$_SERVER["PHP_SELF"]."\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$eddomain["name"]."\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
".$txt_primari_dns_ip[$lang]."</td><td><input type=\"text\" name=\"new_dns_1\" value=\"".$eddomain["primary_dns"]."\"></td></tr>
<tr><td align=\"right\" nowrap>".$txt_other_dns_ip[$lang]."</td><td>";
	if($eddomain["other_dns"] != "default"){
		$other_dns = explode("|",$eddomain["other_dns"]);
		$dns2 = $other_dns[0];
		$nbr_other_dns = sizeof($other_dns);
	}else{
		$dns2 = "default";
		$nbr_other_dns = 1;
	}
	$domain_dns_mx_conf_form .= "<input type=\"text\" name=\"new_dns_2\" value=\"$dns2\"></td></tr>";
	$domain_dns_mx_conf_form .= "<tr><td align=\"right\" nowrap>".$txt_other_dns_ip[$lang]."</td>";

	$new_dns_num = 3;
	for($z=1;$z<$nbr_other_dns;$z++){
		if($z != 1){
			$domain_dns_mx_conf_form .= "<tr><td></td>";
		}
		$domain_dns_mx_conf_form .= "<td><input type=\"text\" name=\"new_dns_$new_dns_num\" value=\"".$other_dns[$z]."\"></td></tr>";
		$new_dns_num += 1;
	}
	$domain_dns_mx_conf_form .= "<tr><td></td><td><input type=\"text\" name=\"new_dns_$new_dns_num\" value=\"\"><br><br></td></tr>";

	// The domain MX configuration
	if($eddomain["primary_dns"] == "default"){
		$domain_dns_mx_conf_form .= "<tr><td align=\"right\" nowrap>".$txt_primary_mx_server[$lang]."</td><td><input type=\"text\" name=\"new_mx_1\" value=\"".$eddomain["primary_mx"]."\"></td></tr>
<tr><td align=\"right\">".$txt_other_mx_servers[$lang]."</td><td>";
		if($eddomain["other_mx"] == "default"){
			$domain_dns_mx_conf_form .= "<input type=\"text\" name=\"new_mx_2\" value=\"\"></td></tr>";
		}else{
			$new_mx_num = 2;
			$other_mx = explode("|",$eddomain["other_mx"]);
			$nbr_other_mx = sizeof($other_mx);
			for($z=0;$z<$nbr_other_mx;$z++){
				if($z != 0)	$domain_dns_mx_conf_form .= "<tr><td></td><td>";
				$domain_dns_mx_conf_form .= "<input type=\"text\" name=\"new_mx_$new_mx_num\" value=\"".$other_mx[$z]."\"></td></tr>";
				$new_mx_num += 1;
			}
			$domain_dns_mx_conf_form .= "<tr><td></td><td><input type=\"text\" name=\"new_mx_$new_mx_num\" value=\"".$other_mx[$z]."\"></td></tr>";
		}
	}
	$domain_dns_mx_conf_form .= "<tr><td></td><td><input type=\"submit\" name=\"new_dns_and_mx_config\" value=\"Ok\"></form></td></tr></table>";

	return "<b><u>".$txt_confirurate_your_domain_name[$lang]."</b></u><br><br>
	$txt_comment_confirurate_your_domain_name[$lang]<br>
	$domain_dns_mx_conf_form";
}

function drawAdminTools_DomainInfo($admin,$eddomain){
	global $lang;
	global $txt_your_domain;
	global $txt_your_domain_quota;
	global $txt_your_domain_email;
	global $txt_your_domain_ftp;
	global $txt_your_domain_subdomain;

	global $txt_other_mx_servers;
	global $txt_primary_mx_server;
	global $txt_other_dns_ip;
	global $txt_primari_dns_ip;
	global $txt_comment_confirurate_your_domain_name;
	global $txt_confirurate_your_domain_name;


	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $dtcshared_path;

	// TODO : fetch the expiration in the database
//	$webname = $eddomain["name"];
//	$query = "SELECT * FROM $pro_mysql_command_table WHERE nom_domaine='$webname'";

	// Retrive domain config
	$quota = $eddomain["quota"];
	$max_email = $eddomain["max_email"];
	$max_ftp = $eddomain["max_ftp"];
	$max_subdomain = $eddomain["max_subdomain"];

	$adm_path = $admin["info"]["path"];
	$webname = $eddomain["name"];

	// Retrive disk usage
	$du_string = exec("du -sm $adm_path/$webname --exclude=access.log",$retval);
	$du_state = explode("\t",$du_string);
	$du = $du_state[0];

	// Retrive number of mailbox
	$email_nbr = sizeof($eddomain["emails"]);
	// Retrive number of ftp account
	$ftp_nbr = sizeof($eddomain["ftps"]);
	// Retrive number of ftp account
	$subdomain_nbr = sizeof($eddomain["subdomains"]);

	$total_http_transfer = fetchHTTPInfo($webname);
	$total_ftp_transfer = fetchFTPInfo($webname);
	$total_transfer = smartByte($total_http_transfer + $total_ftp_transfer);

	return "<b><u>".$txt_your_domain[$lang]."</u></b><br><br>
	<font size=\"-1\">
	Total transfer: $total_transfer<br>
	Quota disque : $du / $quota Mo<br>
	".$txt_your_domain_email[$lang]." $email_nbr / $max_email<br>
	".$txt_your_domain_ftp[$lang]." $ftp_nbr / $max_ftp<br>
	".$txt_your_domain_subdomain[$lang]." $subdomain_nbr /
	$max_subdomain</font><br><br>";

	if(file_exists($dtcshared_path."/dtcrm")){
		$out .= "<b><u>Domain registration info:</u></b><br><br>";
		if($eddomain["whois"] = "away"){
			$out .= "Domain has been registred using another registrar.<br>
			Click <a href=\"\">here</a> to order transfere";
		}else if($eddomain["whois"] == "linked"){
		}
	}
}

//////////////////////////////////////
// Database management for one user //
//////////////////////////////////////
// Todo : add a button for creating a MySql databe for one user
// and add credential to it !
function drawDataBase($database){
	global $lang;
	global $txt_draw_tatabase_your_list;
	global $conf_mysql_db;
	global $adm_login;
	global $adm_pass;

	global $conf_demo_version;

	$txt .= "<br><b><u>".$txt_draw_tatabase_your_list[$lang]."</u></b><br>";

	if($conf_demo_version == "no"){
		mysql_select_db("mysql")or die("Cannot select db mysql for account management !!!");
		$query = "SELECT Db FROM db WHERE User='$adm_login'";
		$result = mysql_query($query)or die("Cannot query \"$query\" !!!".mysql_error());
		$num_rows = mysql_num_rows($result);
		for($i=0;$i<$num_rows;$i++){
			$row = mysql_fetch_array($result);
			if($i != 0){
				$txt .= " - ";
			}
			$txt .= $row["Db"];
		}
		mysql_select_db($conf_mysql_db)or die("Cannot select db \"$conf_mysql_db\"	!!!");	

		$txt .= "<br><br><b><u>Change your MySQL password:</u></b><br>
		<form action=\"".$_SERVER["PHP_SELF"]."\">New password:<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
		<input type=\"text\" name=\"new_mysql_password\" value=\"\">
		<input type=\"submit\" name=\"change_mysql_password\" value=\"Ok\"></form>";
		return $txt;
	}else{
		$txt .= "No mysql account manager in demo version (because I don't have root access to the MySQL database).";
		return $txt;
	}
}

/////////////////////////////////////////////////////
// Draw the form for editing a domain's subdomains //
/////////////////////////////////////////////////////
function drawAdminTools_Subdomain($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $addrlink;

	global $lang;
	global $txt_subdom_list;
	global $txt_subdom_default_sub;
	global $txt_subdom_errase;
	global $txt_subdom_create;

	global $txt_number_of_active_subdomains;
	global $txt_subdom_limit_reach;

	global $edit_a_subdomain;

	global $dtcshared_path;
	global $pro_mysql_nameservers_table;

	$nbr_subdomain = sizeof($domain["subdomains"]);
	$max_subdomain = $domain["max_subdomain"];
	if($nbr_subdomain >= $max_subdomain){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = $txt_number_of_active_subdomains[$lang];
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_subdomain ."</font> / <font size=\"-1\">" . $max_subdomain . "</font><br><br>";


	$default_subdomain = $domain["default_subdomain"];
	$webname = $domain["name"];
	$subdomains = $domain["subdomains"];
	$nbr_subdomains = sizeof($subdomains);

	// Print a simple list of available sub-domains
	$txt .= "<font face=\"Verdana, Arial\"><font size=\"-1\"><b><u>".$txt_subdom_list[$lang]."</u><br>";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		$ip = $subdomains[$i]["ip"];
		if($sub == $_REQUEST["edit_a_subdomain"]){
			$ip_domain_to_edit = $ip;
			$login_to_edit = $subdomains[$i]["login"];
			$pass_to_edit = $subdomains[$i]["pass"];
		}
		if($i!=0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"http://$sub.$webname\" target=\"_blank\">$sub</a>";
	}
	$txt .= "<br>";

	// Let's start a form !
	$txt .= "<form action=\"?\" methode=\"post\">";
	$txt .= "<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">";
	$txt .= "<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">";
	$txt .= "<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">";
	$txt .= "<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">";
	$txt .= "<input type=\"hidden\" name=\"whatdoiedit\" value=\"subdomains\">";
	$txt .= "<table><tr><td align=\"right\">";

	// Popup for choosing default subdomain.
	$txt .= $txt_subdom_default_sub[$lang]."</td><td><select name=\"subdomaindefault_name\" tabindex=\"3\">";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($domain["default_subdomain"] == "$sub"){
			$txt .= "<option value=\"$sub\" selected>$sub</option>";
		}else{
			$txt .= "<option value=\"$sub\">$sub</option>";
		}
	}
	$txt .= "</select></td><td><input type=\"submit\" name=\"subdomaindefault\" value=\"Ok\"></td></tr>";

	// Print list of subdomains, allow creation of new ones, and destruction of existings.
	$txt .= "<tr><td align=\"right\">".$txt_subdom_errase[$lang]."</td><td><select name=\"delsubdomain_name\" tabindex=\"3\">";
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($domain["default_subdomain"] == "$sub"){
//			$txt .= "<option value=\"$sub\" selected>$sub</option>";
		}else{
			// Check that the subdomain is not used for a nameserver (in which case it cannot be deleted befor nameserver is deleted from registry)
			if(file_exists($dtcshared_path."/dtcrm")){
				$query = "SELECT * FROM $pro_mysql_nameservers_table WHERE domain_name='$webname' AND subdomain='$sub';";
				$result = mysql_query($query)or die("Cannot query: \"$query\" !!!".mysql_error());
				$num_rows = mysql_num_rows($result);
				if($num_rows < 1){
					$txt .= "<option value=\"$sub\">$sub</option>";
				}
			}else{
				$txt .= "<option value=\"$sub\">$sub</option>";
			}
		}
	}
	$txt .= "</select></td><td><input type=\"submit\" name=\"delsubdomain\" value=\"Ok\"></td></tr>";

	$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>Edit one of your subdomains:</u></b></font></td></tr>";
	$txt .= "<tr><td collspan=\"3\">";
	// List of subdomains to edit
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub = $subdomains[$i]["name"];
		if($i!=0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&edit_a_subdomain=$sub&edit_domain=$webname&addrlink=$addrlink\">$sub</a>";
	}
	$txt .= "<br><br><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&edit_domain=$webname&addrlink=$addrlink\">new subdomain</a>";
	$txt .= "</td></tr>";

	if(!isset($_REQUEST["edit_a_subdomain"]) || $_REQUEST["edit_a_subdomain"] == ""){
		$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>".$txt_subdom_create[$lang]."</u></b></font></td></tr>";

		// Allow creation of new sub-domains
		if($nbr_subdomain < $max_subdomain){
			$txt .= "<tr><td>Subdomain name:".$txt_subdom_create_name[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_name\" value=\"\"></td><td></td></tr>";
			$txt .= "<tr><td>IP du sous-domain (laissez vide sinon):".$txt_subdom_create_ip[$lang]."</td><td><input type=\"text\" name=\"newsubdomain_ip\" value=\"\"></td>";
			$txt .= "<tr><td>Dynamic ip update login:</td><td><input type=\"text\" name=\"newsubdomain_dynlogin\" value=\"\"></td></tr>";
			$txt .= "<tr><td>Dynamic ip update password:</td><td><input type=\"text\" name=\"newsubdomain_dynpass\" value=\"\"></td></tr>";
			$txt .= "<td><input type=\"submit\" name=\"newsubdomain\" value=\"Ok\"></td></tr>";
		}else{
			$txt .= "<td colspan=\"3\">".$txt_subdom_limit_reach[$lang]."</td>";
		}
	}else{
		// Edition of existing subdomains
		$txt .= "<tr><td collspan=\"3\"><font size=\"-1\"><b><u>Edit a subdomain:".$txt_subdom_edit[$lang]."</u></b></font></td></tr>";
		$txt .= "<tr><td>Subdomain name:".$txt_subdom_create_name[$lang]."</td><td>".$_REQUEST["edit_a_subdomain"]."</td><td></td></tr>";
		$txt .= "<tr><td>IP du sous-domain (laissez vide sinon):".$txt_subdom_create_ip[$lang]."</td><td><input type=\"hidden\" name=\"subdomain_name\" value=\"".$_REQUEST["edit_a_subdomain"]."\">
		<input type=\"hidden\" name=\"edit_a_subdomain\" value=\"".$_REQUEST["edit_a_subdomain"]."\"><input type=\"text\" name=\"newsubdomain_ip\" value=\"$ip_domain_to_edit\"></td>";
		$txt .= "<tr><td>Dynamic ip update login:</td><td><input type=\"text\" name=\"subdomain_dynlogin\" value=\"$login_to_edit\"></td></tr>";
		$txt .= "<tr><td>Dynamic ip update password:</td><td><input type=\"text\" name=\"subdomain_dynpass\" value=\"$pass_to_edit\"></td></tr>";
		$txt .= "<tr><td></td><td><input type=\"submit\" name=\"edit_one_subdomain\" value=\"Ok\"></td></tr>";
	}
	$txt .= "</table></form>";

	$txt .= "</b></font></font>";
	// Print the list of mail box, allow creation of new ones, editing of an account, and destruction of existings.
	return $txt;
}

/////////////////////////////////////////
// One domain email collection edition //
/////////////////////////////////////////
function drawAdminTools_Emails($domain){

	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $edit_mailbox;
	global $addrlink;

	global $lang;
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_mail_liste_of_your_box;
	global $txt_mail_new_mailbox;
	global $txt_mail_redirection1;
	global $txt_mail_redirection2;
	global $txt_mail_deliver_localy;
	global $txt_mail_edit;
	global $txt_mail_new_mailbox_link;
	global $txt_number_of_active_mailbox;
	global $txt_maximum_mailbox_reach;


	$nbr_email = sizeof($domain["emails"]);
	$max_email = $domain["max_email"];
	if($nbr_email >= $max_email){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = $txt_number_of_active_mailbox[$lang];
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_email ."</font> / <font size=\"-1\">" . $max_email . "</font><br><br>";

	$txt .= "<font face=\"Arial, Verdana\"><font size=\"-1\"><b><u>".$txt_mail_liste_of_your_box[$lang]."</u><br>";
	$emails = $domain["emails"];
	$nbr_boites = sizeof($emails);
	for($i=0;$i<$nbr_boites;$i++){
		$email = $emails[$i];
		$id = $email["id"];
		if($id == $_REQUEST["edit_mailbox"]){
			$mailbox_name = $id;
			//print_r($email);
			$home = $email["home"];
			$passwd = $email["passwd"];
			$redir1 = $email["redirect1"];
			$redir2 = $email["redirect2"];
			$localdeliver = $email["localdeliver"];
			if($localdeliver == yes){
				$checkbox_state = " checked";
			}else{
				$checkbox_state = "";
			}
		}
		if($i != 0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails&edit_mailbox=$id\">$id</a>";
	}

	if($_REQUEST["edit_mailbox"] != "" && isset($_REQUEST["edit_mailbox"])){
		$txt .= "<br><br><a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=mails\">".$txt_mail_new_mailbox_link[$lang]."</a> ";
		$txt .= "<br><br><u>".$txt_mail_edit[$lang]."</u><br><br>";

		$txt .= "
<table border=\"1\"><tr><td align=\"right\">
<form action=\"?\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	<input type=\"hidden\" name=\"edit_mailbox\" value=\"".$_REQUEST["edit_mailbox"]."\">
	".$txt_login_login[$lang]."</td><td><b>$mailbox_name</b>@$edit_domain
</td><td align=\"right\">
	".$txt_mail_redirection1[$lang]."</td><td><input type=\"text\" name=\"editmail_redirect1\" value=\"$redir1\">
</td></tr><tr><td align=\"right\">
	".$txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"editmail_pass\" value=\"$passwd\">
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"editmail_redirect2\" value=\"$redir2\">
</td></tr><tr><td align=\"right\">
".$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"editmail_deliver_localy\" value=\"yes\"$checkbox_state></td>
<td>&nbsp;</td><td><input type=\"submit\" name=\"modifymailboxdata\" value=\"Ok\">&nbsp;
<input type=\"submit\" name=\"delemailaccount\" value=\"Del\">
</td></tr>
</table>
</form>
";
	}else{
		$txt .= "<br><br><u>".$txt_mail_new_mailbox[$lang]."</u><br>";

		if($nbr_email < $max_email){
			$txt .= "
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
<table border=\"1\"><tr><td align=\"right\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"mails\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newmail_login\" value=\"$mailbox_name\">
</td><td align=\"right\">
	".$txt_mail_redirection1[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect1\" value=\"\">
</td></tr><tr><td align=\"right\">
	".$txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"newmail_pass\" value=\"$passwd\">
</td><td align=\"right\">
	".$txt_mail_redirection2[$lang]."</td><td><input type=\"text\" name=\"newmail_redirect2\" value=\"\">
</td></tr><tr><td align=\"right\">
".$txt_mail_deliver_localy[$lang]."</td><td><input type=\"checkbox\" name=\"newmail_deliver_localy\" value=\"yes\" checked></td>
<td></td>
<td><input type=\"submit\" name=\"addnewmailtodomain\" value=\"Ok\">
</td></tr>
</table>
</form>
";
		}else{
			$txt .= $txt_maximum_mailbox_reach[$lang]."<br>";
		}
	}
	$txt .= "</b></font></font>";
	return $txt;
}

////////////////////////////////////////////////////
// One domain name ftp account collection edition //
////////////////////////////////////////////////////
function drawAdminTools_Ftp($domain,$adm_path){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;

	global $edftp_account;
	global $addrlink;

	global $lang;
	global $txt_ftp_account_list;
	global $txt_ftp_new_account;
	global $txt_ftp_account_edit;
	global $txt_ftp_new_account_link;
	global $txt_login_login;
	global $txt_login_pass;
	global $txt_path;

	global $txt_number_of_active_ftp;
	global $txt_maxnumber_of_ftp_account_reached;

	$nbr_ftp = sizeof($domain["ftps"]);
	$max_ftp = $domain["max_ftp"];
	if($nbr_ftp >= $max_ftp){
		$max_color = "color=\"#440000\"";
	}
	$nbrtxt = $txt_number_of_active_ftp[$lang];
	$txt .= "<font size=\"-2\">$nbrtxt</font> <font size=\"-1\" $max_color>". $nbr_ftp ."</font> / <font size=\"-1\">" . $max_ftp . "</font><br><br>";

	$txt .= "<font face=\"Verdana, Arial\"><font size=\"-1\"><b><u>".$txt_ftp_account_list[$lang]."</u><br>";
	$ftps = $domain["ftps"];
	$nbr_account = sizeof($ftps);
	for($i=0;$i<$nbr_account;$i++){
		$ftp = $ftps[$i];
		$login = $ftp["login"];
		if($_REQUEST["edftp_account"] != "" && isset($_REQUEST["edftp_account"]) && $login == $_REQUEST["edftp_account"]){
			$pass = $ftp["passwd"];
			$ftpath = $ftp["path"];
		}
		if($i != 0){
			$txt .= " - ";
		}
		$txt .= "<a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=ftps&edftp_account=$login\">$login</a>";
	}

	if($_REQUEST["edftp_account"] != "" && isset($_REQUEST["edftp_account"]) && $ftpath == "$adm_path"){
		$is_selected = " selected";
	}else{
		$is_selected ="";
	}
	$path_popup .= "<option value=\"$adm_path\"$is_selected>/</option>";
	if($_REQUEST["edftp_account"] != "" && isset($_REQUEST["edftp_account"]) && $ftpath == "$adm_path/$edit_domain"){
		$is_selected = " selected";
	}else{
		$is_selected ="";
	}
	$path_popup .= "<option value=\"$adm_path/$edit_domain\"$is_selected>/$edit_domain/</option>";
	$nbr_subdomains = sizeof($domain["subdomains"]);
	for($i=0;$i<$nbr_subdomains;$i++){
		$sub_name = $domain["subdomains"][$i]["name"];
		if($_REQUEST["edftp_account"] != "" && isset($_REQUEST["edftp_account"]) && $ftpath == "$adm_path/$edit_domain/subdomains/$sub_name"){
			$is_selected = " selected";
		}else{
			$is_selected ="";
		}
		$path_popup .= "<option value=\"$adm_path/$edit_domain/subdomains/$sub_name\"$is_selected>/$edit_domain/subdomains/$sub_name/</option>";
	}

	if($_REQUEST["edftp_account"] != "" && isset($_REQUEST["edftp_account"]) && $_REQUEST["deleteftpaccount"] != "Delete"){
		$txt .= "<br><br><a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addrlink=$addrlink&edit_domain=$edit_domain&whatdoiedit=ftps\">".$txt_ftp_new_account_link[$lang]."</A><br>";
		$txt .= "
<br><u>".$txt_ftp_account_edit[$lang]."</u>
<table>
<tr><td align=\"right\">".$txt_login_login[$lang]."</td><td>".$_REQUEST["edftp_account"]."</td></tr>
<tr><td align=\"right\">
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"ftps\">
	<input type=\"hidden\" name=\"edftp_account\" value=\"".$_REQUEST["edftp_account"]."\">
	".$txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"edftp_pass\" value=\"$pass\">
</td></tr><tr><td align=\"right\">
	".$txt_path[$lang]."</td><td><select name=\"edftp_path\">$path_popup</select>
</td></tr><tr><td align=\"right\">
	<input type=\"submit\" name=\"deleteftpaccount\" value=\"Delete\"></td><td><input type=\"submit\" name=\"update_ftp_account\" value=\"Ok\">
</td></tr>
</table>
</form>
<br>
";
	}else{
		$txt .= "
<br><br><u>".$txt_ftp_new_account[$lang]."</u>";
		if($nbr_ftp < $max_ftp){
			$txt .= "
<table><tr><td align=\"right\">
<form action=\"".$_SERVER["PHP_SELF"]."\" methode=\"post\">
	<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
	<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
	<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
	<input type=\"hidden\" name=\"edit_domain\" value=\"$edit_domain\">
	<input type=\"hidden\" name=\"whatdoiedit\" value=\"ftps\">
	<input type=\"hidden\" name=\"edftp_account\" value=\"".$_REQUEST["edftp_account"]."\">
	".$txt_login_login[$lang]."</td><td><input type=\"text\" name=\"newftp_login\" value=\"\">
</td></tr><tr><td align=\"right\">
	".$txt_login_pass[$lang]."</td><td><input type=\"text\" name=\"newftp_pass\" value=\"\">
</td></tr><tr><td align=\"right\">
	".$txt_path[$lang]."</td><td><select name=\"newftp_path\">$path_popup</select>
</td></tr><tr><td align=\"right\">
	</td><td><input type=\"submit\" name=\"newftpaccount\" value=\"Ok\">
</td></tr>
</table>
";
		}else{
			$txt .= "<br>".$txt_maxnumber_of_ftp_account_reached[$lang];
		}
	}
	$txt .= "<br>$interface</b></font>";

	return $txt;
}

function drawAdminTools_AdminStats($admin){
	global $adm_login;
	global $conf_mysql_db;
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;

	$nowrap = " style=\"white-space:nowrap\" nowrap";

	$stats = fetchAdminStats($admin);
// ["domains"][]["name"]
//              ["du"]
//              ["ftp"]
//              ["http"]
//              ["total_transfer"]
// ["total_http"]
// ["total_ftp"]
// ["total_transfer"]
// ["total_du_domains"]
// ["db"][]["name"]
//         ["du"]
// ["total_db_du"]
// ["total_du"]
	$id_client = $admin["info"]["id_client"];

	$out .= "<u><b>Total transfered bytes this month:</b></u>";
	$out .= "<br>HTTP: ".smartByte($stats["total_http"]);
	$out .= "<br>FTP: ".smartByte($stats["total_ftp"]);
	$out .= "<br>Total: ". smartByte($stats["total_transfer"]);

	if($id_client != 0){
		$bw_quota = $admin["info"]["bandwidth_per_month_mb"]*1024*1024;
		$out .= " / ".smartByte($bw_quota)."<br>";
		$out .= drawPercentBar($stats["total_transfer"],$bw_quota);
	}
	$total_du = $du_amount + $dbdu_amount;
	$out .= "<br><u><b>Your area disk usage:</b></u>";
	$out .= "<br>Domain-name files: ".smartByte($stats["total_du_domains"]);
	$out .= "<br>Database files: ".smartByte($stats["total_db_du"]);
	$out .= "<br>Total disk usage: ".smartByte($stats["total_du"]);

	if($id_client != 0){
		$du_quota = $admin["info"]["quota"]*1024*1024;
		$out .= " / ".smartByte($du_quota)."<br>";
		$out .= drawPercentBar($stats["total_du"],$du_quota);
	}

	$out .= "<br><br><u><b>Databases disk usage:</b></u>";
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="0" cellspacing="1">';
	$out .= "<tr><td$nowrap><b>Database name</b></td><td$nowrap><b>Disk usage</b></tr>";
	for($i=0;$i<sizeof($stats["db"]);$i++){
		if($i % 2){
			$bgcolor = "$nowrap nowrap bgcolor=\"#000000\"";
		}else{
			$bgcolor = $nowrap;
		}
		$out .= "<tr>";
		$out .= "<td$bgcolor>".$stats["db"][$i]["name"]."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["db"][$i]["du"])."</td>";
		$out .= "</tr>";
	}
	$out .= '</table>';

	$out .= "<br><br><u><b>Domain name tranfic and disk usage:</b></u>";
	$out .= '<br><table border="1" width="100%" height="1" cellpadding="0" cellspacing="1">';
	$out .= "<tr><td><b>Domain Name</b></td><td$nowrap><b>Disk usage</b></td><td><b>FTP</b></td><td><b>HTTP</b></td><td$nowrap><b>Total trafic</b></td></tr>";
	for($ad=0;$ad<sizeof($stats["domains"]);$ad++){
		if($ad % 2){
			$bgcolor = "$nowrap nowrap bgcolor=\"#000000\"";
		}else{
			$bgcolor = $nowrap;
		}
		$out .= "<tr>";
		$out .= "<td$bgcolor>".$stats["domains"][$ad]["name"]."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["du"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["ftp"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["http"])."</td>";
		$out .= "<td$bgcolor>".smartByte($stats["domains"][$ad]["total_transfer"])."</td>";
		$out .= "</tr>";
	}
	$out .= '</table>';
	return $out;
}

function drawAdminTools_DomainStats($admin,$eddomain){
	global $pro_mysql_domain_table;
	global $pro_mysql_acc_http_table;
	global $pro_mysql_acc_ftp_table;	
	sum_http($eddomain["name"]);
	$query_http = "SELECT SUM(bytes_sent) AS transfer FROM $pro_mysql_acc_http_table WHERE domain='".$eddomain["name"]."'";
    $result_http = mysql_query($query_http)or die("Cannot execute query \"$query_http\"");
    $num_rows = mysql_num_rows($result_http);
    $http_amount = $http_amount + mysql_result($result_http,0,"transfer");
	sum_ftp($eddomain["name"]);
    $query_ftp = "SELECT SUM(transfer) AS transfer FROM $pro_mysql_acc_ftp_table WHERE sub_domain='".$eddomain["name"]."'";
    $result_ftp = mysql_query($query_ftp) or die("Cannot execute query \"$query\"");
    $num_rows = mysql_num_rows($result_ftp);
    $ftp_amount = $ftp_amount + mysql_result($result_ftp,0,"transfer");
	
	$out .= "<u><b>Total transfered bytes this month:</b></u><br>
HTTP: ".smartByte($http_amount);
	$out .= "<br>FTP:  ".smartByte($ftp_amount);
	$out .= "<br>Total: ". smartByte($http_amount + $ftp_amount);
	return $out;
}

function AdminTool_findDomainNum($name,$domains){
	$num_domains = sizeof($domains);
	for($i=0;$i<$num_domains;$i++){
		if($domains[$i]["name"] == $name){
			return $i;
		}
	}
	return -1;
}

function drawAdminTools($admin){
	global $adm_login;
	global $adm_pass;
	global $edit_domain;
	global $whatdoiedit;
	global $domain_conf_submenu;
	
	global $addrlink;
	global $lang;

	global $txt_draw_help_content;
	global $txt_left_menu_title;

	global $txt_title_mailbox_form;
	global $txt_title_subdomain_form;
	global $txt_title_ftp_form;
	global $txt_title_database_form;
	global $txt_title_help_form;
	global $txt_title_geninfo_form;

	global $dtcshared_path;

	$admin_data = $admin["data"];
	$admin_info = $admin["info"];

	$adm_cur_pass 	= $admin_info["adm_pass"];
	$adm_path 	= $admin_info["path"];

	unset($user_ZEmenu);
	$user_ZEmenu[] = array(
		"text" => "stats",
		"type" => "link",
		"link" => "stats");
	if(file_exists($dtcshared_path."/dtcrm")){
		$user_ZEmenu[] = array(
			"text" => "adddomain",
			"type" => "link",
			"link" => "adddomain");
		$user_ZEmenu[] = array(
			"text" => "nickhandles",
			"type" => "link",
			"link" => "nickhandles");
		$user_ZEmenu[] = array(
			"text" => "nameservers",
			"type" => "link",
			"link" => "nameservers");
	}
	$user_menu[] = array(
		"text" => "myaccount",
		"type" => "menu",
		"link" => "myaccount",
		"sub" => $user_ZEmenu);

	// Generate the admin tools
	$nbr_domain = sizeof($admin_data);
	$doms_txt .= "<b>";
	for($i=0;$i<$nbr_domain;$i++){

		$dom = $admin_data[$i]["name"];

		unset($domain_conf_submenu);

		if(file_exists($dtcshared_path."/dtcrm")){
			$domain_conf_submenu[] = array(
				"text" => "whois",
				"type" => "link",
				"link" => "whois");
		}

		$domain_conf_submenu[] = array(
			"text" => "stats",
			"type" => "link",
			"link" => "stats");

		$domain_conf_submenu[] = array(
			"text" => "dns",
			"type" => "link",
			"link" => "dns");

		if($admin_data[$i]["primary_dns"] == "default"){
			$domain_conf_submenu[] = array(
				"text" => "subdomains",
				"type" => "link",
				"link" => "subdomains");

			if($admin_data[$i]["primary_mx"] == "default"){
				$domain_conf_submenu[] = array(
					"text" => "mailboxs",
					"type" => "link",
					"link" => "mailboxs");
			}
			$domain_conf_submenu[] = array(
				"text" => "ftp-accounts",
				"type" => "link",
				"link" => "ftp-accounts");
		}

		$user_menu[] = array(
			"text" => "$dom",
			"type" => "menu",
			"link" => "$dom",
			"sub" => $domain_conf_submenu);
	}
	$user_menu[] = array(
		"text" => "database",
		"type" => "link",
		"link" => "database");
	$user_menu[] = array(
		"text" => "help",
		"type" => "link",
		"link" => "help");

	$mymenu = makeTreeMenu($user_menu,$addrlink,"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass","addrlink");
//	$mymenu = makeTreeMenu2($user_menu);


	$add_array = explode("/",$addrlink);

	if($addrlink != "" && isset($addrlink)){
		$num_domain = AdminTool_findDomainNum($edit_domain,$admin_data);
		$eddomain = $admin_data[$num_domain];

		if($add_array[1] == "mailboxs"){
			$web_editor .= drawAdminTools_Emails($eddomain);
			$title = $txt_title_mailbox_form[$lang].$edit_domain;
		}else if($add_array[1] == "dns"){
			$web_editor .= drawAdminTools_DomainDNS($admin,$eddomain);
			$title = "DNS config of: ".$edit_domain;
		}else if($add_array[1] == "stats"){
			if($add_array[0] == "myaccount"){
				$web_editor .= drawAdminTools_AdminStats($admin);
				$title = "My global statistics";
			}else{
				$web_editor .= drawAdminTools_DomainStats($admin,$eddomain);
				$title = "Statistics of domain: ".$edit_domain;
			}
		}else if($add_array[1] == "whois"){
			$web_editor .= drawAdminTools_Whois($admin,$eddomain);
			$title = "Whois editor of: ".$edit_domain;
		}else if($add_array[1] == "subdomains"){
			$web_editor .= drawAdminTools_Subdomain($eddomain);
			$title = $txt_title_subdomain_form[$lang].$edit_domain;
		}else if($add_array[1] == "ftp-accounts"){
			$web_editor .= drawAdminTools_Ftp($eddomain,$adm_path);
			$title = $txt_title_ftp_form[$lang].$edit_domain;
		}else if($add_array[1] == "nickhandles"){
			$web_editor .= drawAdminTools_NickHandles($admin);
			$title = "Internet Whois Nick-Handles management";
		}else if($add_array[1] == "adddomain"){
			$web_editor .= drawAdminTools_AddDomain($admin);
			$title = "Add a domain name to my account";
		}else if($add_array[1] == "nameservers"){
			$web_editor .= drawAdminTools_NameServers($admin);
			$title = "Manage my name servers";
		}else if($add_array[0] == "myaccount"){   
			$web_editor .= drawAdminTools_MyAccount($admin);
			$title = "My Account informations";
		}else if($add_array[0] == "database"){
			$web_editor .= drawDataBase($database);
			$title = $txt_title_database_form[$lang];
		}else if($add_array[0] == "help"){
			$web_editor .= $txt_draw_help_content[$lang];
			$title = $txt_title_help_form[$lang];
		}else{
			$web_editor .= drawAdminTools_DomainInfo($admin,$eddomain);
			$title = $txt_title_geninfo_form[$lang].$edit_domain;
		}
		$edition = skin("simple/green",$web_editor,$title);
	}

	$domain_list = skin("simple/green","<br>$mymenu",$txt_left_menu_title[$lang]);

	$out = "
<table width=\"100%\" height=\"100%\">
<tr><td valign=\"top\" width=\"220\" height=\"1\">
	<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>$domain_list</td></tr><tr><td>&nbsp</td></tr></table>
</td><td height=\"100%\">&nbsp;
</td><td align=\"left\" valign=\"top\" height=\"100%\">
	$edition
</td></tr>
</table>
";

	return $out;
}

?>
