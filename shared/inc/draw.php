<?php
/**
 * @package DTC
 * @version $Id: draw.php,v 1.79 2006/06/29 09:31:56 thomas Exp $
 * 
 */
if($panel_type !="email"){
	require("$dtcshared_path/inc/forms/my_account.php");
	require("$dtcshared_path/inc/forms/root_admin.php");
	// require("$dtcshared_path/inc/forms/root_dns.php");
	require("$dtcshared_path/inc/forms/domain_info.php");
	require("$dtcshared_path/inc/forms/database.php");
	require("$dtcshared_path/inc/forms/reseller.php");
	require("$dtcshared_path/inc/forms/ftp.php");
	require("$dtcshared_path/inc/forms/ssh.php");
        require("$dtcshared_path/inc/forms/packager.php");
	require("$dtcshared_path/inc/forms/admin_stats.php");
	require("$dtcshared_path/inc/forms/domain_stats.php");
	require("$dtcshared_path/inc/forms/dns.php");
	require("$dtcshared_path/inc/forms/subdomain.php");
	require("$dtcshared_path/inc/forms/lists.php");
	require("$dtcshared_path/inc/forms/vps.php");
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

  global $txt_change_your_password;
  global $txt_type_new_password;
  global $txt_retype_new_password;
  global $lang;

  $out = "<b><u>".$txt_change_your_password[$lang]."</u></b><br>
<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"change_adm_pass\">
".$txt_type_new_password[$lang]." <input type=\"password\" name=\"new_pass1\" value=\"\">
".$txt_retype_new_password[$lang]." <input type=\"password\" name=\"new_pass2\" value=\"\">
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
// seeb ;)
	global $txt_logout;
	global $txt_use_images_menu;
	global $txt_use_text_menu;
	
	
	global $txt_title_mailbox_form;
	global $txt_title_subdomain_form;
	global $txt_title_ftp_form;
	global $txt_title_ssh_form;
	global $txt_title_database_form;
	global $txt_title_help_form;
	global $txt_title_geninfo_form;
	global $txt_my_account_global_stats_title;

	global $txt_cmenu_myaccount;
	global $txt_cmenu_myaccount_stats;
	global $txt_cmenu_add_domain;
	global $txt_cmenu_nickhandles;
	global $txt_cmenu_nameservers;
	global $txt_cmenu_whois;
	global $txt_cmenu_dns;
	global $txt_cmenu_subdomains;
	global $txt_cmenu_ftpaccounts;
	global $txt_cmenu_sshaccounts;
	global $txt_cmenu_packageinstaller;
	global $txt_cmenu_mailboxs;
	global $txt_cmenu_mailinglists;
	global $txt_cmenu_database;
	global $txt_cmenu_reseller;
	global $txt_cmenu_password;
	global $txt_cmenu_help;

	global $dtcshared_path;

	global $dtc_use_text_menu;

	global $conf_skin;
	global $conf_use_registrar_api;

	global $vps_node;
	global $vps_name;
	
// nowe
	global $txt_password;
	global $txt_edit_mailing_lists_of_domain;
	global $txt_DNS_config_of;
	global $txt_Statistics_of_domain;
	global $txt_Whois_editor_of;
	global $txt_Package;
	global $txt_Internet_Whois_Nick_Handles_management;
	global $txt_Add_a_domain_name_to_my_account;
	global $txt_Manage_my_name_servers;
	global $txt_My_Account_information;
	global $txt_resseller_child_accounts;
	
	$add_array = explode("/",$addrlink);
        $doms_txt = "";

	if(isset($admin["data"])){
		$admin_data = $admin["data"];
		$nbr_domain = sizeof($admin_data);
	}else{
		$nbr_domain = 0;
	}

	if(isset($admin["vps"])){
	  $admin_vps = $admin["vps"];
	  $nbr_vps = sizeof($admin_vps);
	}else{
	  $nbr_vps = 0;
	}

	$admin_info = $admin["info"];

	$adm_cur_pass 	= $admin_info["adm_pass"];
	$adm_path 	= $admin_info["path"];
	$resseller_flag = $admin_info["resseller_flag"];
	$ssh_login_flag = $admin_info["ssh_login_flag"];

	unset($user_ZEmenu);
	$user_ZEmenu[] = array(
		"text" => $txt_cmenu_myaccount_stats[$lang],
		"type" => "link",
		"link" => "stats");
	if(file_exists($dtcshared_path."/dtcrm")){
		$user_ZEmenu[] = array(
			"text" => $txt_cmenu_add_domain[$lang],
			"type" => "link",
			"link" => "adddomain");
        }
        if(file_exists($dtcshared_path."/dtcrm") && $conf_use_registrar_api == "yes"){
		$user_ZEmenu[] = array(
			"text" => $txt_cmenu_nickhandles[$lang],
			"type" => "link",
			"link" => "nickhandles");
		$user_ZEmenu[] = array(
			"text" => $txt_cmenu_nameservers[$lang],
			"type" => "link",
			"link" => "nameservers");
	}
	$user_menu[] = array(
		"text" => $txt_cmenu_myaccount[$lang],
		"type" => "menu",
		"link" => "myaccount",
		"sub" => $user_ZEmenu);

        // Draw all vps
        for($i=0;$i<$nbr_vps;$i++){
          $user_menu[] = array(
            "text" => $admin_vps[$i]["vps_server_hostname"].":".$admin_vps[$i]["vps_xen_name"],
            "type" => "link",
            "link" => "vps:".$admin_vps[$i]["vps_server_hostname"].":".$admin_vps[$i]["vps_xen_name"]);
        }

	// Generate the admin tools
	$doms_txt .= "<b>";
	unset($selected_domain);
	$not_selected_domains = array();
	for($i=0;$i<$nbr_domain;$i++){

		$dom = $admin_data[$i]["name"];
		$domain_parking = $admin_data[$i]["domain_parking"];

		unset($domain_conf_submenu);

		if(file_exists($dtcshared_path."/dtcrm") && $conf_use_registrar_api == "yes"){
			$domain_conf_submenu[] = array(
				"text" => $txt_cmenu_whois[$lang],
				"type" => "link",
				"link" => "whois");
		}

		$domain_conf_submenu[] = array(
			"text" => $txt_cmenu_myaccount_stats[$lang],
			"type" => "link",
			"link" => "stats");

		$domain_conf_submenu[] = array(
			"text" => $txt_cmenu_dns[$lang],
			"type" => "link",
			"link" => "dns");

		if($admin_data[$i]["primary_dns"] == "default"){
		  if($domain_parking == "no-parking"){
			$domain_conf_submenu[] = array(
				"text" => $txt_cmenu_subdomains[$lang],
				"type" => "link",
				"link" => "subdomains");
			$domain_conf_submenu[] = array(
				"text" => $txt_cmenu_ftpaccounts[$lang],
				"type" => "link",
				"link" => "ftp-accounts");
                        if($ssh_login_flag == "yes"){
			  $domain_conf_submenu[] = array(
				"text" => $txt_cmenu_sshaccounts[$lang],
				"type" => "link",
				"link" => "ssh-accounts");
                        }
			$domain_conf_submenu[] = array(
				"text" => $txt_cmenu_packageinstaller[$lang],
				"type" => "link",
				"link" => "package-installer");
                  }
		}
		if($admin_data[$i]["primary_mx"] == "default" && $domain_parking == "no-parking"){
			$domain_conf_submenu[] = array(
				"text" => $txt_cmenu_mailboxs[$lang],
				"type" => "link",
				"link" => "mailboxs");
		}
		if($admin_data[$i]["primary_mx"] == "default" && $domain_parking == "no-parking"){
			$domain_conf_submenu[] = array(
				"text" => $txt_cmenu_mailinglists[$lang],
				"type" => "link",
				"link" => "mailing-lists");
		}
		if($add_array[0] == $dom){
		  $selected_domain = array(
			"text" => "$dom",
			"type" => "menu",
			"link" => "$dom",
			"sub" => $domain_conf_submenu);
		}else{
		  $not_selected_domains[] = array(
			"text" => "$dom",
			"type" => "menu",
			"link" => "$dom",
			"sub" => $domain_conf_submenu);
                }
	}
	if(isset($selected_domain)){
	  $user_menu[] = $selected_domain;
	}

	$nbr_remaining = sizeof($not_selected_domains);
	for($i=0;$i<$nbr_remaining;$i++){
	  $user_menu[] = $not_selected_domains[$i];
	}

	$user_menu[] = array(
		"text" => $txt_cmenu_database[$lang],
		"type" => "link",
		"link" => "database");
        if($resseller_flag == "yes"){
        	$user_menu[] = array(
	        	"text" => $txt_cmenu_reseller[$lang],
	        	"type" => "link",
	        	"link" => "reseller");
        }
	$user_menu[] = array(
		"text" => $txt_cmenu_password[$lang],
		"type" => "link",
		"link" => "password");
	$user_menu[] = array(
		"text" => $txt_cmenu_help[$lang],
		"type" => "link",
		"link" => "help");

	$mymenu = makeTreeMenu($user_menu,$addrlink,"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass","addrlink");
//	$mymenu = makeTreeMenu2($user_menu);



	$web_editor = "";

	if(isset($addrlink) && $addrlink != ""){
		if (isset($admin_data)){
			$num_domain = AdminTool_findDomainNum($edit_domain,$admin_data);
			$eddomain = @$admin_data[$num_domain];
		} else {
			$num_domain = 0;
		}
		if(isset($vps_node)){
		  $vps_founded = 0;
		  for($i=0;$i<$nbr_vps;$i++){
		    if($vps_name == $admin_vps[$i]["vps_xen_name"] && $vps_node == $admin_vps[$i]["vps_server_hostname"]){
		      $vps_order_number = $i;
		      $vps_founded = 1;
		    }
		  }
		  $web_editor .= "<img src=\"inc/virtual-server.png\" align=\"left\"><font size=\"+2\"><b><u>VPS $vps_name:$vps_node</u></b><br></font>";
                  if($vps_founded){
		    $web_editor .= drawAdminTools_VPS($admin,$admin["vps"][$vps_order_number]);
                  }else{
                    $web_editor .= "VPS not found!";
                  }
		$title = "Virtual Private Server $vps_name running on $vps_node";
		}else if(@$add_array[1] == "mailboxs"){
                        $web_editor .= "<img src=\"inc/mailboxs.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_mailboxs[$lang]:</u></b><br></font>";
                        $web_editor .= drawAdminTools_Emails($eddomain);
                  	$title = $txt_title_mailbox_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "mailing-lists"){
                        $web_editor .= "<img src=\"inc/mailing-lists.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_mailinglists[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_MailingLists($eddomain);
			$title = $txt_edit_mailing_lists_of_domain[$lang].":".$edit_domain;
		}else if(@$add_array[1] == "dns"){
                        $web_editor .= "<img src=\"inc/nameservers.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_nameservers[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_DomainDNS($admin,$eddomain);
			$title = $txt_DNS_config_of[$lang].": ".$edit_domain;
		}else if(@$add_array[1] == "stats"){
			if($add_array[0] == "myaccount"){
			  $web_editor .= "<img src=\"inc/stats.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_myaccount_stats[$lang]:</u></b><br></font>";
				$web_editor .= drawAdminTools_AdminStats($admin);
				$title = $txt_my_account_global_stats_title[$lang];
			}else{
			  $web_editor .= "<img src=\"inc/stats.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_myaccount_stats[$lang]:</u></b><br></font>";
				$web_editor .= drawAdminTools_DomainStats($admin,$eddomain);
				$title = $txt_Statistics_of_domain[$lang].":".$edit_domain;
			}
		}else if(@$add_array[1] == "whois"){
                        $web_editor .= "<img src=\"inc/nickhandles.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_whois[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_Whois($admin,$eddomain);
			$title = $txt_Whois_editor_of[$lang].": ".$edit_domain;
		}else if(@$add_array[1] == "subdomains"){
                        $web_editor .= "<img src=\"inc/subdomains.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_subdomains[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_Subdomain($eddomain);
			$title = $txt_title_subdomain_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "ftp-accounts"){
                        $web_editor .= "<img src=\"inc/ftp-accounts.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_ftpaccounts[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_Ftp($eddomain,$adm_path);
			$title = $txt_title_ftp_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "ssh-accounts"){
                        $web_editor .= "<img src=\"inc/ssh-accounts.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_sshaccounts[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_SSH($eddomain,$adm_path);
			$title = $txt_title_ssh_form[$lang].$edit_domain;
		}else if(@$add_array[1] == "package-installer"){
                        $web_editor .= "<img src=\"inc/package-installer.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_packageinstaller[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_PackageInstaller($eddomain,$adm_path);			
			$title = $txt_Package[$lang].": ".$edit_domain;
		}else if(@$add_array[1] == "nickhandles"){
                        $web_editor .= "<img src=\"inc/nickhandles.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_nickhandles[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_NickHandles($admin);
			$title = $txt_Internet_Whois_Nick_Handles_management[$lang];
		}else if(@$add_array[1] == "adddomain"){
                        $web_editor .= "<img src=\"inc/adddomain.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_add_domain[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_AddDomain($admin);
			$title = $txt_Add_a_domain_name_to_my_account[$lang];
		}else if(@$add_array[1] == "nameservers"){
                        $web_editor .= "<img src=\"inc/nameservers.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_nameservers[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_NameServers($admin);
			$title = $txt_Manage_my_name_servers[$lang];
		}else if($add_array[0] == "myaccount"){   
			$web_editor .= "<img src=\"inc/my-account.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_myaccount[$lang]:</u></b><br></font>";
			$web_editor .= drawAdminTools_MyAccount($admin);
			$title = $txt_My_Account_information[$lang];
		}else if($add_array[0] == "database"){
                        $web_editor .= "<img src=\"inc/databases.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_database[$lang]:</u></b><br></font>";
			$web_editor .= drawDataBase("");
			$title = $txt_title_database_form[$lang];
		}else if($add_array[0] == "reseller"){
                        $web_editor .= "<img src=\"inc/reseller.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_reseller[$lang]:</u></b><br></font>";
			$web_editor .= drawReseller($admin);	
			$title = $txt_resseller_child_accounts[$lang];
		}else if($add_array[0] == "password"){
                        $web_editor .= "<img src=\"inc/password.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_password[$lang]:</u></b><br></font>";
			$web_editor .= drawPasswordChange();
			$title = $txt_password[$lang];
		}else if($add_array[0] == "help"){
                        $web_editor .= "<img src=\"inc/help.png\" align=\"left\"><font size=\"+2\"><b><u>$txt_cmenu_help[$lang]:</u></b><br></font>";
			$web_editor .= $txt_draw_help_content[$lang];
			$title = $txt_title_help_form[$lang];
		}else{
                        $web_editor .= "<img src=\"inc/domains.png\" align=\"left\"><font size=\"+2\"><b><u>$addrlink:</u></b><br></font>";
			$web_editor .= drawAdminTools_DomainInfo($admin,$eddomain);
			$title = $txt_title_geninfo_form[$lang].$edit_domain;
		}
		$edition = skin($conf_skin,$web_editor,$title);
	}else{
	  $edition = "";
	}

	$mymenu .= "<div align=\"center\"><a href=\"".$_SERVER["PHP_SELF"]."?\">".$txt_logout[$lang]."</a>";
	if($dtc_use_text_menu == "no"){
		$mymenu .= " - <a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addlink=$addrlink&use_text_menu=yes\">".$txt_use_text_menu[$lang]."</a>";
	}else{
		$mymenu .= " - <a href=\"".$_SERVER["PHP_SELF"]."?adm_login=$adm_login&adm_pass=$adm_pass&addlink=$addrlink&use_text_menu=no\">".$txt_use_images_menu[$lang]."</a>";
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
//                   ["max_ssh"]
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
//                      ["max_ssh"]
//                      ["max_subdomain"]
//                      ["ip_addr"]
//                      ["backup_ip_addr"]
//                      ["subdomains"][0-n]["name"]
//                                         ["path"]
//                                         ["ip"]
//                                         ["login"]
//                                         ["pass"]
//                                         ["w3_alias"]
//                                         ["register_globals"]
//                                         ["webalizer_generate"]
//                                         ["generate_vhost"]
//                      ["emails"][0-n]["id"]
//                                     ["home"]
//                                     ["crypt"]
//                                     ["passwd"]
//                                     ["shell"]
//                      ["ftps"]["login"]
//                              ["passwd"]
//                              ["path"]
//                      ["sshs"]["login"]
//                              ["passwd"]
//                              ["path"]

// The fetchAdminStats($admin) return the followin structure:
// ["domains"][0-n]["name"]
//                 ["du"]
//                 ["ftp"]
//                 ["ssh"]
//                 ["http"]
//                 ["smtp"]
//                 ["pop"]
//                 ["imap"]
//                 ["total_transfer"]
// ["total_http"]
// ["total_ftp"]
// ["total_ssh"]
// ["total_email"]
// ["total_transfer"]
// ["total_du_domains"]
// ["db"][0-n]["name"]
//            ["du"]
// ["total_db_du"]
// ["total_du"]



?>
