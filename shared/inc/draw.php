<?php

if($panel_type !="email"){
	require("$dtcshared_path/inc/forms/my_account.php");
	require("$dtcshared_path/inc/forms/root_admin.php");
	// require("$dtcshared_path/inc/forms/root_dns.php");
	require("$dtcshared_path/inc/forms/domain_info.php");
	require("$dtcshared_path/inc/forms/database.php");
	require("$dtcshared_path/inc/forms/ftp.php");
        require("$dtcshared_path/inc/forms/packager.php");
	require("$dtcshared_path/inc/forms/admin_stats.php");
	require("$dtcshared_path/inc/forms/domain_stats.php");
	require("$dtcshared_path/inc/forms/dns.php");
	require("$dtcshared_path/inc/forms/subdomain.php");
	require("$dtcshared_path/inc/forms/lists.php");
}
require("$dtcshared_path/inc/forms/email.php");

function AdminTool_findDomainNum($name,$domains){
	$num_domains = sizeof($domains);
	for($i=0;$i<$num_domains;$i++){
		if($domains[$i]["name"] == $name){
			return $i;
		}
	}
	return -1;
}

function drawPasswordChange(){
  global $adm_login;
  global $adm_pass;
  global $addrlink;

  $out = "<b><u>Change your password:</u></b><br>
<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"change_adm_pass\">
New password: <input type=\"password\" name=\"new_pass1\" value=\"\">
Retype new password: <input type=\"password\" name=\"new_pass2\" value=\"\">
<input type=\"submit\" value=\"Ok\">
</form>";
  return $out;
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
	global $txt_my_account_global_stats_title;

	global $dtcshared_path;

	global $dtc_use_text_menu;

	global $conf_skin;

        $doms_txt = "";

	if(isset($admin["data"])){
		$admin_data = $admin["data"];
		$nbr_domain = sizeof($admin_data);
	}else{
		$nbr_domain = 0;
	}
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
			if($admin_data[$i]["primary_mx"] == "default"){
				$domain_conf_submenu[] = array(
					"text" => "mailing-lists",
					"type" => "link",
					"link" => "mailing-lists");
			}
			$domain_conf_submenu[] = array(
				"text" => "ftp-accounts",
				"type" => "link",
				"link" => "ftp-accounts");
			$domain_conf_submenu[] = array(
				"text" => "package-installer",
				"type" => "link",
				"link" => "package-installer");
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
		"text" => "password",
		"type" => "link",
		"link" => "password");
	$user_menu[] = array(
		"text" => "help",
		"type" => "link",
		"link" => "help");

	$mymenu = makeTreeMenu($user_menu,$addrlink,"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass","addrlink");
//	$mymenu = makeTreeMenu2($user_menu);


	$add_array = explode("/",$addrlink);

	$web_editor = "";

	if(isset($addrlink) && $addrlink != ""){
		$num_domain = AdminTool_findDomainNum($edit_domain,$admin_data);
		$eddomain = @$admin_data[$num_domain];

		if(@$add_array[1] == "mailboxs"){
			$web_editor .= drawAdminTools_Emails($eddomain);
			$title = $txt_title_mailbox_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "mailing-lists"){
			$web_editor .= drawAdminTools_MailingLists($eddomain);
			$title = $txt_title_maillinglist_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "dns"){
			$web_editor .= drawAdminTools_DomainDNS($admin,$eddomain);
			$title = "DNS config of: ".$edit_domain;
		}else if(@$add_array[1] == "stats"){
			if($add_array[0] == "myaccount"){
				$web_editor .= drawAdminTools_AdminStats($admin);
				$title = $txt_my_account_global_stats_title[$lang];
			}else{
				$web_editor .= drawAdminTools_DomainStats($admin,$eddomain);
				$title = "Statistics of domain: ".$edit_domain;
			}
		}else if(@$add_array[1] == "whois"){
			$web_editor .= drawAdminTools_Whois($admin,$eddomain);
			$title = "Whois editor of: ".$edit_domain;
		}else if(@$add_array[1] == "subdomains"){
			$web_editor .= drawAdminTools_Subdomain($eddomain);
			$title = $txt_title_subdomain_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "ftp-accounts"){
			$web_editor .= drawAdminTools_Ftp($eddomain,$adm_path);
			$title = $txt_title_ftp_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "package-installer"){
			$web_editor .= drawAdminTools_PackageInstaller($eddomain,$adm_path);
			$title = "Package: ".$edit_domain;
		}else if(@$add_array[1] == "nickhandles"){
			$web_editor .= drawAdminTools_NickHandles($admin);
			$title = "Internet Whois Nick-Handles management";
		}else if(@$add_array[1] == "adddomain"){
			$web_editor .= drawAdminTools_AddDomain($admin);
			$title = "Add a domain name to my account";
		}else if(@$add_array[1] == "nameservers"){
			$web_editor .= drawAdminTools_NameServers($admin);
			$title = "Manage my name servers";
		}else if($add_array[0] == "myaccount"){   
			$web_editor .= drawAdminTools_MyAccount($admin);
			$title = "My Account informations";
		}else if($add_array[0] == "database"){
			$web_editor .= drawDataBase("");
			$title = $txt_title_database_form[$lang];
		}else if($add_array[0] == "password"){
			$web_editor .= drawPasswordChange();
			$title = "Password";
		}else if($add_array[0] == "help"){
			$web_editor .= $txt_draw_help_content[$lang];
			$title = $txt_title_help_form[$lang];
		}else{
			$web_editor .= drawAdminTools_DomainInfo($admin,$eddomain);
			$title = $txt_title_geninfo_form[$lang].$edit_domain;
		}
		$edition = skin($conf_skin,$web_editor,$title);
	}else{
	  $edition = "";
	}

	$mymenu .= "<div align=\"center\"><a href=\"".$_SERVER["PHP_SELF"]."?\">Logout</a>";
	if($dtc_use_text_menu == "no"){
		$mymenu .= " - <a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addlink=$addrlink&use_text_menu=yes\">Use text</a>";
	}else{
		$mymenu .= " - <a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addlink=$addrlink&use_text_menu=no\">Use images</a>";
	}
	$mymenu .= "</div>";

	$domain_list = skin($conf_skin,"<br>$mymenu",$txt_left_menu_title[$lang]);

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

// require("$dtcshared_path/strings.php");
// To maintainers: please remember the following structure is fetched from
// database by the "fetch.php" source code using the function:
// fetchAdmin($adm_login,$adm_pass). It's secured, because called with login AND password...
// so it can be called from both admin and client pannel.
// That is fetched at EACH call of the index page of the client interface,
// and when an virtual admin is selected on the user management panel of the
// dtc.yourdomain.com/dtcadmin/, so please do not fetch informations TWICE !
//     $admin["info"]["adm_login"]
//                   ["adm_pass"]
//                   ["path"]
//                   ["max_email"]
//                   ["max_ftp"]
//                   ["quota"];
//                   ["bandwidth_per_month_mb"]
//                   ["prod_id"]
//                   ["expire"]
//                   ["id_client"]
//           [data][0-n]["default_subdomain"]
//                      ["name"]
//                      ["quota"]
//                      ["max_email"]
//                      ["max_ftp"]
//                      ["max_subdomain"]
//                      ["ip_addr"]
//                      ["subdomains"][0-n]["name"]
//                                         ["path"]
//                                         ["ip"]
//                                         ["login"]
//                                         ["pass"]
//                                         ["w3_alias"]
//                                         ["register_globals"]
//                                         ["webalizer_generate"]
//                      ["emails"][0-n]["id"]
//                                     ["home"]
//                                     ["crypt"]
//                                     ["passwd"]
//                                     ["shell"]
//                      ["ftps"]["login"]
//                              ["passwd"]
//                              ["path"]

// The fetchAdminStats($admin) return the followin structure:
// ["domains"][0-n]["name"]
//                 ["du"]
//                 ["ftp"]
//                 ["http"]
//                 ["smtp"]
//                 ["pop"]
//                 ["imap"]
//                 ["total_transfer"]
// ["total_http"]
// ["total_ftp"]
// ["total_email"]
// ["total_transfer"]
// ["total_du_domains"]
// ["db"][0-n]["name"]
//            ["du"]
// ["total_db_du"]
// ["total_du"]



?>
