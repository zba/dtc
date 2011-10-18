<?php

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
	require("$dtcshared_path/inc/forms/invoices.php");
	require("$dtcshared_path/inc/forms/domain_stats.php");
	require("$dtcshared_path/inc/forms/dns.php");
	require("$dtcshared_path/inc/forms/subdomain.php");
	require("$dtcshared_path/inc/forms/user_cronjobs.php");
	require("$dtcshared_path/inc/forms/lists.php");
	require("$dtcshared_path/inc/forms/ticket.php");
	//udns.us add
	require("$dtcshared_path/inc/forms/tools.php");
	//udns.us /add

	require("$dtcshared_path/inc/forms/vps.php");
	require("$dtcshared_path/inc/forms/vps_monitoring.php");
	require("$dtcshared_path/inc/forms/vps_graphs.php");
	require("$dtcshared_path/inc/forms/vps_dom0graphs.php");
	require("$dtcshared_path/inc/forms/vps_installation.php");
	require("$dtcshared_path/inc/forms/dedicated.php");
	require("$dtcshared_path/inc/forms/multiple_renew.php");
	require("$dtcshared_path/inc/forms/customservices.php");
}
require("$dtcshared_path/inc/forms/email.php");
require("$dtcshared_path/inc/forms/aliases.php");

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
	global $pro_mysql_admin_table;
	global $conf_enforce_adm_encryption;

	$pass_submit_err = "";

	if(isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_adm_pass"){
	        $commit_flag = "yes"; //Init the commit_flag
		if(!isDTCPassword($_REQUEST["new_pass1"]) || !isDTCPassword($_REQUEST["new_pass2"])){
			$pass_submit_err .= _("This is not a valid password.")."<br>\n";
			$commit_flag = "no";
		}
		if($_REQUEST["new_pass1"] != $_REQUEST["new_pass2"]){
			$pass_submit_err .= _("Passwords do not match.")."<br>\n";
			$commit_flag = "no";
		}
		if($commit_flag == "yes"){
			if($conf_enforce_adm_encryption == "yes"){
				$new_password_encrypted = "SHA1('".$_REQUEST["new_pass1"]."')";
			}else{
				$new_password_encrypted = "'".$_REQUEST["new_pass1"]."'";
			}
			$q = "UPDATE $pro_mysql_admin_table SET adm_pass=$new_password_encrypted WHERE adm_login='$adm_login';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said ".mysql_error());
			$pass_submit_err .= _("Your administrator password has been changed.")."<br>\n";
		}
	}

	$out = "<h3>". _("Change your password:") ."</h3><br>
$pass_submit_err
<form action=\"?\" method=\"post\">
".dtcFormTableAttrs()."
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"addrlink\" value=\"$addrlink\">
<input type=\"hidden\" name=\"action\" value=\"change_adm_pass\">
".dtcFormLineDraw( _("New password:") ,"<input type=\"password\" name=\"new_pass1\" value=\"\">").
dtcFormLineDraw( _("Retype new password:") ,"<input type=\"password\" name=\"new_pass2\" value=\"\">",1).
dtcFromOkDraw()."</form></table>";
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

	global $dtcshared_path;

	global $dtc_use_text_menu;

	global $conf_skin;
	global $conf_use_registrar_api;
	global $conf_use_mail_alias_group;

	global $vps_node;
	global $vps_name;
	global $dedicated_server_hostname;
	global $custom_id;
	global $server_subscreen;
	global $vps_subcommand;

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

	if(isset($admin["dedicated"])){
		$admin_dedicated = $admin["dedicated"];
		$nbr_dedicated = sizeof($admin_dedicated);
	}else{
		$nbr_dedicated = 0;
	}

	if(isset($admin["custom"])){
		$admin_custom = $admin["custom"];
		$nbr_custom = sizeof($admin_custom);
	}else{
		$nbr_custom = 0;
	}

	$admin_info = $admin["info"];

	$adm_cur_pass 	= $admin_info["adm_pass"];
	$adm_path 	= $admin_info["path"];
	$resseller_flag = $admin_info["resseller_flag"];
	$ssh_login_flag = $admin_info["ssh_login_flag"];
	$ftp_login_flag = $admin_info["ftp_login_flag"];
	$pkg_install_flag = $admin_info["pkg_install_flag"];
	$allow_dns_and_mx_change = $admin_info["allow_dns_and_mx_change"];
	$allow_mailing_list_edit = $admin_info["allow_mailing_list_edit"];
	$allow_subdomain_edit = $admin_info["allow_subdomain_edit"];
	$allow_cronjob_edit = $admin_info["allow_cronjob_edit"];

	unset($user_ZEmenu);
	if($nbr_domain > 0){
		$user_ZEmenu[] = array(
 			"text" => _("Statistics") ,
			"icon" => "box_wnb_nb_picto-statistics.gif",
			"type" => "link",
			"link" => "stats");
	}
	$user_ZEmenu[] = array(
		"text" => _("Past payments") ,
		"icon" => "box_wnb_nb_picto-pastpayments.gif",
		"type" => "link",
		"link" => "invoices");
	$user_ZEmenu[] = array(
		"text" => _("Add a domain or service") ,
		"icon" => "box_wnb_nb_picto-addadomainname.gif",
		"type" => "link",
		"link" => "adddomain");
	if(($nbr_vps + $nbr_dedicated) > 1){
		$user_ZEmenu[] = array(
			"text" => _("Multiple renew") ,
			"icon" => "box_wnb_nb_picto-addadomainname.gif",
			"type" => "link",
			"link" => "multiple-renew");
	}
	if($conf_use_registrar_api == "yes" && $nbr_domain > 0){
		$user_ZEmenu[] = array(
			"text" => _("DNS NIC handles") ,
			"icon" => "box_wnb_nb_picto-nickhandles.gif",
			"type" => "link",
			"link" => "nickhandles");
		$user_ZEmenu[] = array(
			"text" => _("Name servers"),
			"icon" => "box_wnb_nb_picto-nameservers.gif",
			"type" => "link",
			"link" => "nameservers");
	}
	$user_menu[] = array(
		"text" => _("My account") ,
		"icon" => "box_wnb_nb_picto-general.gif",
		"type" => "menu",
		"link" => "myaccount",
		"sub" => $user_ZEmenu);

	// Draw all vps
	for($i=0;$i<$nbr_vps;$i++){
		$vps_submenu = array();
		$vps_submenu[] = array(
				"text" => _("Monitoring"),
				"icon" => "box_wnb_nb_picto-statistics.gif",
				"type" => "link",
				"link" => "monitor"
			);
		$vps_submenu[] = array(
				"text" => _("My VPS usage"),
				"icon" => "box_wnb_nb_picto-statistics.gif",
				"type" => "link",
				"link" => "rrdgraphs"
			);
		$vps_submenu[] = array(
				"text" => _("My VPS vs. others"),
				"icon" => "box_wnb_nb_picto-statistics.gif",
				"type" => "link",
				"link" => "dom0graphs"
			);
		$vps_submenu[] = array(
				"text" => _("Installation"),
				"icon" => "box_wnb_nb_picto-packageinstaller.gif",
				"type" => "link",
				"link" => "installation"
			);
		$user_menu[] = array(
			"text" => $admin_vps[$i]["vps_server_hostname"].":".$admin_vps[$i]["vps_xen_name"],
			"icon" => "box_wnb_nb_picto-vpsservers.gif",
			"type" => "menu",
			"link" => "vps:".$admin_vps[$i]["vps_server_hostname"].":".$admin_vps[$i]["vps_xen_name"],
			"sub" => $vps_submenu);
	}

	// Draw all the dedicated servers
	for($i=0;$i<$nbr_dedicated;$i++){
		$user_menu[] = array(
			"text" => $admin_dedicated[$i]["server_hostname"],
			"icon" => "box_wnb_nb_picto-dedicatedservers.gif",
			"type" => "link",
			"link" => "server:".$admin_dedicated[$i]["server_hostname"]);
	}

	//Draw all custom products
	for($i=0;$i<$nbr_custom;$i++){
		$user_menu[] = array(
			"text" => $admin_custom[$i]["id"],
			"icon" => "box_wnb_nb_picto-dedicatedservers.gif",
			"type" => "link",
			"link" => "custom:".$admin_custom[$i]["id"]);
	}

	// Generate the admin tools
	$doms_txt .= "<b>";
	unset($selected_domain);
	$not_selected_domains = array();
	for($i=0;$i<$nbr_domain;$i++){

		$dom = $admin_data[$i]["name"];
		$domain_parking = $admin_data[$i]["domain_parking"];

		unset($domain_conf_submenu);

		if($conf_use_registrar_api == "yes"){
			$domain_conf_submenu[] = array(
				"text" => _("Whois") ,
				"icon" => "box_wnb_nb_picto-whois.gif",
				"type" => "link",
				"link" => "whois");
		}

		$domain_conf_submenu[] = array(
			"text" => _("Statistics") ,
			"icon" => "box_wnb_nb_picto-statistics.gif",
			"type" => "link",
			"link" => "stats");

		if($allow_dns_and_mx_change == "yes"){
			$domain_conf_submenu[] = array(
				"text" => _("DNS and MX") ,
				"icon" => "box_wnb_nb_picto-mxnsservers.gif",
				"type" => "link",
				"link" => "dns");
		}

		if($admin_data[$i]["primary_dns"] == "default"){
		  if($domain_parking == "no-parking"){
			if($allow_subdomain_edit == "yes"){
				$domain_conf_submenu[] = array(
					"text" => _("Sub-domains"),
					"icon" => "box_wnb_nb_picto-subdomains.gif",
					"type" => "link",
					"link" => "subdomains");
			}
			if($allow_cronjob_edit == "yes"){
				$domain_conf_submenu[] = array(
					"text" => _("Cron jobs"),
					"icon" => "box_wnb_nb_picto-subdomains.gif",
					"type" => "link",
					"link" => "cronjobs");
			}
                        if($ftp_login_flag == "yes"){
				$domain_conf_submenu[] = array(
					"text" => _("FTP accounts") ,
					"icon" => "box_wnb_nb_picto-ftpaccounts.gif",
					"type" => "link",
					"link" => "ftp-accounts");
			}
			if($ssh_login_flag == "yes"){
				$domain_conf_submenu[] = array(
					"text" => _("SSH accounts") ,
					"icon" => "box_wnb_nb_picto-sshaccounts.gif",
					"type" => "link",
					"link" => "ssh-accounts");
                        }
			if($pkg_install_flag == "yes"){
				$domain_conf_submenu[] = array(
					"text" => _("Package installer") ,
					"icon" => "box_wnb_nb_picto-packageinstaller.gif",
					"type" => "link",
					"link" => "package-installer");
			}
                  }
		}
		if($admin_data[$i]["primary_mx"] == "default" && $domain_parking == "no-parking"){
			$domain_conf_submenu[] = array(
				"text" => _("Mailboxes"),
				"icon" => "box_wnb_nb_picto-mailboxes.gif",
				"type" => "link",
				"link" => "mailboxs");
		}
		if($admin_data[$i]["primary_mx"] == "default" && $domain_parking == "no-parking" && $conf_use_mail_alias_group == "yes" && $allow_mailing_list_edit == "yes"){
			$domain_conf_submenu[] = array(
				"text" => _("Mail Groups"),
				"icon" => "box_wnb_nb_picto-mailgroups.gif",
				"type" => "link",
				"link" => "mailaliases");
		}
		if($admin_data[$i]["primary_mx"] == "default" && $domain_parking == "no-parking" && $allow_mailing_list_edit == "yes"){
			$domain_conf_submenu[] = array(
				"text" => _("Mailing lists"),
				"icon" => "box_wnb_nb_picto-mailinglists.gif",
				"type" => "link",
				"link" => "mailing-lists");
		}
//udns.us add
// This is to be re-written from scratch, has many holes.
//		if($admin_data[$i]["primary_mx"] == "default" && $domain_parking == "no-parking"){
//			$domain_conf_submenu[] = array(
//				"text" => $txt_domain_tools[$lang],
//				"type" => "link",
//				"link" => "tools");
//		}
//udns.us /add
		if($add_array[0] == $dom){
		  $selected_domain = array(
			"text" => "$dom",
			"icon" => "box_wnb_nb_picto-servers.gif",
			"type" => "menu",
			"link" => "$dom",
			"sub" => $domain_conf_submenu);
		}else{
		  $not_selected_domains[] = array(
			"text" => "$dom",
			"icon" => "box_wnb_nb_picto-servers.gif",
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

	if($nbr_domain > 0){
		$user_menu[] = array(
			"text" => _("Databases") ,
			"icon" => "box_wnb_nb_picto-database.gif",
			"type" => "link",
			"link" => "database");
		if( file_exists("/usr/share/extplorer/index.php") ){
			$user_menu[] = array(
				"text" => _("File manager"),
				"icon" => "box_wnb_nb_picto-database.gif",
				"type" => "link",
				"link" => "filemang");
		}
        }
        if($resseller_flag == "yes"){
        	$user_menu[] = array(
	        	"text" => _("Sub-accounts (reseller)") ,
	        	"icon" => "box_wnb_nb_picto-resellers.gif",
	        	"type" => "link",
	        	"link" => "reseller");
        }
	$user_menu[] = array(
		"text" => _("Password") ,
		"icon" => "box_wnb_nb_picto-passwords.gif",
		"type" => "link",
		"link" => "password");
	$user_menu[] = array(
		"text" => _("Support tickets") ,
		"icon" => "box_wnb_nb_picto-supporttickets.gif",
		"type" => "link",
		"link" => "ticket");
	$user_menu[] = array(
		"text" => _("Help") ,
		"icon" => "box_wnb_nb_picto-help.gif",
		"type" => "link",
		"link" => "help");

	$mymenu = makeTreeMenu($user_menu,$addrlink,"?adm_login=$adm_login&adm_pass=$adm_pass","addrlink");
	$mymenu .= "<div align=\"center\" class=\"box_wnb_nb_content\"><a href=\"?\">". _("Logout") ."</a>";
	if($dtc_use_text_menu == "no"){
		$mymenu .= " - <a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addlink=$addrlink&use_text_menu=yes\">". _("Use text menu") ."</a>";
	}else{
		$mymenu .= " - <a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&addlink=$addrlink&use_text_menu=no\">". _("Use image menu")."</a>";
	}
	$mymenu .= "</div>";



	$web_editor = "";

	if(isset($addrlink) && $addrlink != ""){
		if (isset($admin_data)){
			$num_domain = AdminTool_findDomainNum($edit_domain,$admin_data);
			$eddomain = @$admin_data[$num_domain];
		}else{
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
			$web_editor .= "<img src=\"gfx/toolstitles/virtual-server.png\" align=\"left\"><font size=\"+2\"><b><u>VPS $vps_name:$vps_node</u></b><br></font>";
			if($vps_founded){
				switch($vps_subcommand){
				case "monitor":
					$web_editor .= drawAdminTools_VPSMonitor($admin,$admin["vps"][$vps_order_number]);
					break;
				case "rrdgraphs":
					$web_editor .= drawAdminTools_VPSRRDGraphs($admin,$admin["vps"][$vps_order_number]);
					break;
				case "dom0graphs":
					$web_editor .= drawAdminTools_dm0RRDGraphs($admin,$admin["vps"][$vps_order_number]);
					break;
				case "installation":
					$web_editor .= drawAdminTools_VPSInstallation($admin,$admin["vps"][$vps_order_number]);
					break;
				default:
					$web_editor .= drawAdminTools_VPS($admin,$admin["vps"][$vps_order_number]);
					break;
				}
			}else{
				$web_editor .= "VPS not found!";
			}
			$title = _("Virtual Private Server") . " $vps_name " . _("running on") . " $vps_node";
		}else if(substr($addrlink,0,7) == "server:"){
			$web_editor .= drawAdminTools_Dedicated($admin,$dedicated_server_hostname);
			$title = _("Dedicated server") .": $dedicated_server_hostname";
		}else if(substr($addrlink,0,7) == "custom:"){
			$web_editor .= drawAdminTools_Custom($admin,$custom_id);
			$title = _("Dedicated server") .": $dedicated_server_hostname";
		}else if(@$add_array[1] == "mailboxs"){
                        $web_editor .= "<img src=\"gfx/toolstitles/mailboxs.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Mailboxes:") ."</u></b><br></font>";
                        $web_editor .= drawAdminTools_Emails($eddomain);
                  	$title = _("Mailboxes of ") .$edit_domain;
		}else if(@$add_array[1] == "mailaliases"){
                        $web_editor .= "<img src=\"gfx/toolstitles/mailaliasgroup.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Mail Groups:") ."</u></b><br></font>";
                        $web_editor .= drawAdminTools_Aliases($eddomain);
                  	$title = _("Mail groups of ").$edit_domain;
		}else if(@$add_array[1] == "mailing-lists"){
                        $web_editor .= "<img src=\"gfx/toolstitles/mailing-lists.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Mailing lists:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_MailingLists($eddomain);
			$title = _("Edit mailing lists of domain:") .$edit_domain;
		//udns.us add
		}else if(@$add_array[1] == "tools"){
			$web_editor .= "<img src=\"gfx/toolstitles/tools.png\" align=\"left\"><font size=\"+2\"><b><u>Tools:</u></b><br></font>";
			$web_editor .= drawAdminTools_Tools($eddomain);
			// To be translated:
			$title = _("Domain Tools");
			//udns.us /add
		}else if(@$add_array[1] == "dns"){
                        $web_editor .= "<img src=\"gfx/toolstitles/nameservers.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Name servers:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_DomainDNS($admin,$eddomain);
			$title = _("DNS config of:") ." ".$edit_domain;
		}else if(@$add_array[1] == "invoices"){
			$web_editor .= "<img src=\"gfx/toolstitles/stats.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Invoices") .":</u></b><br></font>";
			$web_editor .= drawAdminTools_Invoices($admin);
			$title = _("Invoices");
		}else if(@$add_array[1] == "stats"){
			if($add_array[0] == "myaccount"){
				$web_editor .= "<img src=\"gfx/toolstitles/stats.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Statistics:") ."</u></b><br></font>";
				$web_editor .= drawAdminTools_AdminStats($admin);
				$title = _("My account global statistics");
			}else{
				$web_editor .= "<img src=\"gfx/toolstitles/stats.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Statistics:") ."</u></b><br></font>";
				$web_editor .= drawAdminTools_DomainStats($admin,$eddomain);
				$title = _("Statistics of domain:")." ".$edit_domain;
			}
		}else if(@$add_array[1] == "whois"){
                        $web_editor .= "<img src=\"gfx/toolstitles/nickhandles.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Whois:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_Whois($admin,$eddomain);
			$title = _("Whois of:") ." ".$edit_domain;
		}else if(@$add_array[1] == "subdomains"){
                        $web_editor .= "<img src=\"gfx/toolstitles/subdomains.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Subdomains:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_Subdomain($admin,$eddomain);
			$title = _("Subdomains of ") .$edit_domain;
		}else if(@$add_array[1] == "cronjobs"){
			$web_editor .= "<img src=\"gfx/toolstitles/subdomains.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Cron jobs:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_User_CronJob($admin,$eddomain);
			$title = _("Cron jobs of ") .$edit_domain;
		}else if(@$add_array[1] == "ftp-accounts"){
                        $web_editor .= "<img src=\"gfx/toolstitles/ftp-accounts.png\" align=\"left\"><font size=\"+2\"><b><u>". _("FTP accounts") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_Ftp($eddomain,$adm_path);
			$title = _("FTP accounts of ") .$edit_domain;
		}else if(@$add_array[1] == "ssh-accounts"){
                        $web_editor .= "<img src=\"gfx/toolstitles/ssh-accounts.png\" align=\"left\"><font size=\"+2\"><b><u>". _("SSH accounts:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_SSH($eddomain,$adm_path);
			$title = _("SSH accounts of ") .$edit_domain;
		}else if(@$add_array[1] == "package-installer"){
                        $web_editor .= "<img src=\"gfx/toolstitles/package-installer.png\" align=\"left\"><font size=\"+2\"><b><u>"._("Package installer:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_PackageInstaller($eddomain,$adm_path);			
			$title = _("Package") .": ".$edit_domain;
		}else if(@$add_array[1] == "nickhandles"){
                        $web_editor .= "<img src=\"gfx/toolstitles/nickhandles.png\" align=\"left\"><font size=\"+2\"><b><u>". _("DNS NIC handles") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_NickHandles($admin);
			$title = _("Internet Whois NIC Handle management:") ;
		}else if(@$add_array[1] == "adddomain"){
                        $web_editor .= "<img src=\"gfx/toolstitles/adddomain.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Add a domain or service:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_AddDomain($admin);
			$title = _("Add a domain name to my account") ;
		}else if(@$add_array[1] == "multiple-renew"){
                        $web_editor .= "<img src=\"gfx/toolstitles/adddomain.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Renewals:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_MultipleRenew($admin);
			$title = _("Renew multiple services at once") ;
		}else if(@$add_array[1] == "nameservers"){
                        $web_editor .= "<img src=\"gfx/toolstitles/nameservers.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Name servers:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_NameServers($admin);
			$title = _("Manage my name servers:");
		}else if($add_array[0] == "myaccount"){   
			$web_editor .= "<img src=\"gfx/toolstitles/my-account.png\" align=\"left\"><font size=\"+2\"><b><u>" . _("Statistics:") ."</u></b><br></font>";
			$web_editor .= drawAdminTools_MyAccount($admin);
			$title = _("My Account information:");
		}else if($add_array[0] == "database"){
                        $web_editor .= "<img src=\"gfx/toolstitles/databases.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Databases:") ."</u></b><br></font>";
			$web_editor .= drawDataBase("");
			$title = _("Your databases");
		}else if($add_array[0] == "filemang"){
			$web_editor .= "<img src=\"gfx/toolstitles/databases.png\" align=\"left\"><font size=\"+2\"><b><u>". _("File manager:") ."</u></b><br></font>";
			$web_editor .= "<table width=\"100%\" height=\"100%\" border=\"0\"><tr>
					<td width=\"1\" height=\"100%\"><img width=\"1\" height=\"600\" src=\"gfx/skin/bwoup/gfx/spacer.gif\"></td>
					<td width=\"100%\" height=\"100%\"><iframe width=\"100%\" height=\"100%\" src=\"/extplorer/\"></iframe></td>
				</tr></table>";
			$title = _("eXtplorer web file manager");
		}else if($add_array[0] == "reseller"){
                        $web_editor .= "<img src=\"gfx/toolstitles/reseller.png\" align=\"left\"><font size=\"+2\"><b><u>"._("Sub-accounts (reseller):") ."</u></b><br></font>";
			$web_editor .= drawReseller($admin);	
			$title = _("Reseller child accounts");
		}else if($add_array[0] == "password"){
                        $web_editor .= "<img src=\"gfx/toolstitles/password.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Password:") ."</u></b><br></font>";
			$web_editor .= drawPasswordChange();
			$title = _("Password");
		}else if($add_array[0] == "ticket"){
                        $web_editor .= "<img src=\"gfx/toolstitles/ticket.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Support tickets:") . "</u></b><br></font>";
			$web_editor .= drawTickets($admin);
			$title = _("Support ticket system");
		}else if($add_array[0] == "help"){
                        $web_editor .= "<img src=\"gfx/toolstitles/help.png\" align=\"left\"><font size=\"+2\"><b><u>". _("Help:") ."</u></b><br></font>";
			$web_editor .= _("<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>ONLINE DTC HELP</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. What is DTC</u></font><br><br>
DTC is a tool we made especialy for you. With it, you can take the control of your domain administration : you can manage all your
subdomains, email boxes, ftp or ssh accounts, and manage your VPS. DTC also does billing and invoices, plus many other things.<br><br>

All this tool and all the software it uses and configure have been release under the <a href=\"http://www.gnu.org/\">GPL</a>
(GNU Public Licence), which means that you can have a copy of this interface source code, modify it and use it as you wish, as long as
you redistribute all thoses changes. We (at GPLHost) believe in the Free Software effort, and we hope this participation will encourage
other developpements. We consider that because we use only open-source software for our hosting service, it is normal to redistribute
our developpements as well.<br><br>

If you want to know more about DTC, you can visit this URL: <a href=\"http://www.gplhost.com/software-dtc.html\">DTC home page</a>.

<font size=\"+1\"><u>2. Emails</u></font><br>
<u>2.1. What will it do ?</u><br><br>
You can add, delete or modify a mailbox with this tool.<br><br>

<u>2.2. Redirection and local delivery</u><br><br>
Each mailbox can be redirected to one or more email addresses. This means that when a message is recieved, it is forwared to one
or two email adresse(s). The &quote;deliver locally&quote; checkbox tells wether or not all message for this mailbox will be written on
our hard disk, so thenafter you will be able to read your messages using a mail client, connecting to our server. Don't forget to
checkup your mails often if you have trafic, because the mailbox are included in the quota<br><br>

<u>2.3. Delay when adding / deleting accounts</u><br><br>
When you add or delete a mail account, don't expect it to work immediatly: you will have to wait until the next cron job to start, so the
mail, ssh or web server reloads it's database.<br><br>

<u>2.4. Other tools for email</u><br><br>
DTC doesn't only have email accounts. It also handles mailing lists using MLMMJ and mail alias groups to be able to have one email
redirected to many other addresses.

<font size=\"+1\"><u>3. Subdomains</u></font><br>
<u>3.1. What will it do ?</u><br><br>
This part of the interface is for configurating your somain's sites, which means that you will be able to populate your web site with url
of the form:
<pre>
http://anything.u.want.mydomain.com
</pre>

<u>3.2. What is the default subdomain ?</u><br><br>
Whe someone trys to contact your web site with an URL without a subdomain, he is redirected to the subdomain you said it was the default.
In other words, if you tell that:
<pre>
www
</pre>
is the default subdomain, someone that trys to connect using an url starting with:
<pre>
http://mydomain.com
</pre>
will be redirected to:
<pre>
http://www.mydomain.com
</pre>

In fact, the URL is kept, and no URL redirection in a HTML page has been created, but simply, a website with that URL has been configurated
to the same location of the &quote;www&quote; subdomain, so it accesses the same html (or php) files, and shares the same log file.<br><br>

<u>3.3. Subdomains generated by default</u><br><br>
Because you might expect to have these subdomains for other services than web already working by default, the following subdomains will
resolve to the same IP address as your web site, even if you didn't add them:
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
<li>mail</li>
</ul>

<u>3.4. Delay when adding / deleting subdomain</u><br><br>
Like for mail, any action here will take up to 10 minutes to take effect.<br><br>

<u>3.6. Trafic statistics for your subdomains</u><br><br>
Because all your trafic is loged, we calculate the overall last 12 month statistics using <a href=\"http://www.mrunix.net/webalizer/\">
webalizer</a>. The statistics are calculated each days (this is when there is less trafic), and can be reach under the \"/stats\" directory
on each subdomains. That means that if you have registerd :
<pre>
http://www.mydomain.com
</pre>
all statistics will be generated under :
<pre>
http://www.mydomain.com/stats/
</pre>

If you wish, you can protect this address with a login and password.

<font size=\"+1\"><u>4. FTP accounts</u></font><br>
<u>4.1. What will it do ?</u><br><br>
To have your page working and running, you have to upload them. But because you may not be only one to work on your web site, you may want to
have more that one FTP account for accessing your web site. DTC will be the tool for managing thoses accounts and passwords.<br><br>

<u>4.2. Delay when adding / deleting FTP accounts</u><br><br>
Because DTC uses ProFTP or pure-ftpd with a special module for handling accounts in a MySql database, all changes to your FTP accounts take
effect in realtime.<br><br>

</div>
</font>") .
"<pre><a href=\"http://www.gplhost.com\">
   _____       _____________   (c) 2oo3.2oo8     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
     Open source | hosting worldwide  /_____/                         |HOST.
</a></pre>";
			$title = _("Online DTC help");
		}else{
                        $web_editor .= "<img src=\"gfx/toolstitles/domains.png\" align=\"left\"><font size=\"+2\"><b><u>$addrlink:</u></b><br></font>";
			$web_editor .= drawAdminTools_DomainInfo($admin,$eddomain);
			$title = _("General Information ") .$edit_domain;
		}
		$edition = skin($conf_skin,$web_editor,$title);
	}else{
		$edition = "";
		$title = "";
	}
	if( function_exists("skin_LayoutClientPage") ){
		return skin_LayoutClientPage ($mymenu,$web_editor,$title);
	}else{
		return skin_LayoutClientPage_Default ($mymenu,$web_editor,$title);
	}
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
