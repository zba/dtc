<?php

function skin_DTCConfigMenu_Default ($dsc){
	if(!isset($_REQUEST["sousrub"])){
		$sousrub = "general";
        }else{
          $sousrub = $_REQUEST["sousrub"];
        }

        $out = "<table border=\"0\">";

        $keys = array_keys($dsc);
        $nbr_entry = sizeof($dsc);
        for($i=0;$i<$nbr_entry;$i++){
        	if($keys[$i] == $sousrub){
			$out .= "<tr><td style=\"white-space:nowrap\"><img border=\"0\" width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/".$dsc[ $keys[$i] ]["icon"]."\">".$dsc[ $keys[$i] ]["text"]."</td></tr>";
		}else{
			$out .= "<tr><td style=\"white-space:nowrap\"><a href=\"?rub=config&sousrub=".$keys[$i]."\"><img border=\"0\" width=\"16\" height=\"16\" src=\"gfx/skin/bwoup/gfx/config-icon/".$dsc[ $keys[$i] ]["icon"]."\">".$dsc[ $keys[$i] ]["text"]."</a></td></tr>";
		}
        }
        $out .= "</table>";
        return $out;
}


function drawDTCConfigMenu(){
	$dsc = array(
		"general" => array(
			"text" => gettext("General"),
			"icon" => "box_wnb_nb_picto-general.gif"),
		"ticket" => array(
			"text" => gettext("Support and auth"),
			"icon" => "box_wnb_nb_picto-supportickets.gif"),
		"ip" => array(
			"text" => gettext("IP Addresses and Network"),
			"icon" => "box_wnb_nb_picto-ipaddresses.gif"),
		"sslip" => array(
			"text" => gettext("SSL IP Addresses"),
			"icon" => "box_wnb_nb_picto-sslip.gif"),
		"ip_pool" => array(
			"text" => gettext("IP Address Pools"),
			"icon" => "box_wnb_nb_picto-sslip.gif"),
		"nagios" => array(
			"text" => _("Nagios Config"),
			"icon" => "box_wnb_nb_picto-sslip.gif"),
		"dedicatedip" => array(
			"text" => _("Dedicated Server IPs"),
			"icon" => "box_wnb_nb_picto-dedicatedservers.gif"),
		"zonefile" => array(
			"text" => gettext("Named Zonefiles"),
			"icon" => "box_wnb_nb_picto-namedzonefiles.gif"),
		"backup" => array(
			"text" => gettext("MX and NS Backup Servers"),
			"icon" => "box_wnb_nb_picto-mxnsservers.gif"),
		"ftpbackup" => array(
			"text" => gettext("FTP Backup"),
			"icon" => "box_wnb_nb_picto-ftpbackup.gif"),
		"vps" => array(
			"text" => gettext("VPS Servers"),
			"icon" => "box_wnb_nb_picto-vpsservers.gif"),
		"radius" => array(
			"text" => "Radius",
			"icon" => "box_wnb_nb_picto-sslip.gif"),
		"path" => array(
			"text" => gettext("Paths"),
			"icon" => "box_wnb_nb_picto-paths.gif"),
		"payconf" => array(
			"text" => gettext("Payment Gateways"),
			"icon" => "box_wnb_nb_picto-payementgateway.gif"),
		"renewals" => array(
			"text" => _("Renewals"),
			"icon" => "box_wnb_nb_picto-renewals.gif"),
		"registryapi" => array(
			"text" => _("Domain Name Registration"),
			"icon" => "box_wnb_nb_picto-domainnamereg.gif"),
		"companies" => array(
			"text" => _("Companies"),
			"icon" => "box_wnb_nb_picto-companies.gif"),
		"invoicing" => array(
			"text" => _("Invoicing"),
			"icon" => "box_wnb_nb_picto-invoicing.gif")
	);
	if(function_exists("skin_DTCConfigMenu")){
		return skin_DTCConfigMenu ($dsc);
	}else{
		return skin_DTCConfigMenu_Default ($dsc);
	}
}

function configEditorTemplate ($dsc,$conftype="config"){
	global $pro_mysql_config_table;
	global $pro_mysql_secpayconf_table;
	$out = "";

	if($conftype == "config"){
		$sql_table = $pro_mysql_config_table;
		$prefix = "conf_";
	}else{
		$sql_table = $pro_mysql_secpayconf_table;
		$prefix = "secpayconf_";
	}

	$keys = array_keys($dsc["cols"]);
	$n = sizeof($keys);

	// Do the sql stuff here!
	if( isset($_REQUEST["action"]) && $_REQUEST["action"] == $dsc["action"] ){
		$vals = "";
		for($i=0;$i<$n;$i++){
			if($i != 0){
				$vals .= ", ";
			}
			if( !isset($_REQUEST[ $keys[$i] ] ) ){
				$_REQUEST[ $keys[$i] ] = "";
			}
			if( !is_array( $_REQUEST[ $keys[$i] ] ) ){
				$my_value = $_REQUEST[ $keys[$i] ];
			}else{
				$my_value = join( ",",$_REQUEST[ $keys[$i] ] );
			}
			$vals .= $keys[$i]."='".$my_value."'";
		}
		$q = "UPDATE $sql_table SET $vals WHERE 1;";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said: ".mysql_error());
	}

	if(isset($dsc["edit_callback"])){
		$dsc["edit_callback"]();
	}

	if($conftype == "config"){
		getConfig();
	}else{
		get_secpay_conf();
	}

	$out .= "<h3>".$dsc["title"]."</h3>";
	if( isset($dsc["desc"]) ){
		$out .= $dsc["desc"]."<br><br>";
	}

	$nbr_forwards = sizeof($dsc["forward"]);
	$fw = "";
	for($i=0;$i<$nbr_forwards;$i++){
		$fw .= "<input type=\"hidden\" name=\"".$dsc["forward"][$i]."\" value=\"".$_REQUEST[ $dsc["forward"][$i] ]."\">";
	}

	$out .= dtcFormTableAttrs();
	$out .= "<form action=\"?\">
<input type=\"hidden\" name=\"action\" value=\"".$dsc["action"]."\">$fw";
	for($i=0;$i<$n;$i++){
		$fld = $prefix.$keys[$i];
		global $$fld;
		if($i%2 == 1){
			$input_class = "dtcDatagrid_input_alt_color";
		}else{
			$input_class = "dtcDatagrid_input_color";
		}
		switch($dsc["cols"][ $keys[$i] ]["type"]){
		case "radio":
			$nb_choices = sizeof($dsc["cols"][ $keys[$i] ]["values"]);
			$control = "";
			for($j=0;$j<$nb_choices;$j++){
				if($$fld == $dsc["cols"][ $keys[$i] ]["values"][$j]){
					$selected = " checked ";
				}else{
					$selected = "";
				}
				if( isset($dsc["cols"][ $keys[$i] ]["display_replace"][$j]) ){
					$text = $dsc["cols"][ $keys[$i] ]["display_replace"][$j];
				}else{
					$text = $dsc["cols"][ $keys[$i] ]["values"][$j];
				}
				$control .= "<input type=\"radio\" name=\"".$keys[$i]."\" value=\"".$dsc["cols"][ $keys[$i] ]["values"][$j]."\" $selected> $text";
			}
			break;
		case "popup":
			$nb_choices = sizeof($dsc["cols"][ $keys[$i] ]["values"]);
			$control = "";
			for($j=0;$j<$nb_choices;$j++){
				if($$fld == $dsc["cols"][ $keys[$i] ]["values"][$j]){
					$selected = " selected ";
				}else{
					$selected = "";
				}
				if( isset($dsc["cols"][ $keys[$i] ]["display_replace"][$j]) ){
					$text = $dsc["cols"][ $keys[$i] ]["display_replace"][$j];
				}else{
					$text = $dsc["cols"][ $keys[$i] ]["values"][$j];
				}
				$control .= "<option value=\"".$dsc["cols"][ $keys[$i] ]["values"][$j]."\" $selected>$text</option>";
			}
			$control = "<select class=\"$input_class\" name=\"".$keys[$i]."\">".$control."</select>";
			break;
		case "checkboxcomma":
			$nb_choices = sizeof($dsc["cols"][ $keys[$i] ]["values"]);
			$control = "";
			$arr_values = preg_split("/,/",$$fld);
			$cntchk = 0;
			for($j=0;$j<$nb_choices;$j++){
				if (in_array($dsc["cols"][ $keys[$i] ]["values"][$j], $arr_values)){
					$selected = " checked ";
				}else{
					$selected = "";
				}
				if( isset($dsc["cols"][ $keys[$i] ]["display_replace"][$j]) ){
					$text = $dsc["cols"][ $keys[$i] ]["display_replace"][$j];
				}else{
					$text = $dsc["cols"][ $keys[$i] ]["values"][$j];
				}
				$control .= "<input type=\"checkbox\" name=\"".$keys[$i]."[]\" value=\"".$dsc["cols"][ $keys[$i] ]["values"][$j]."\" $selected> $text\n";
				if ($cntchk > 3){
					$control .= "<br />";
					$cntchk = 0;
				}
				$cntchk++;
			}
			break;
		case "textarea":
			if( isset($dsc["cols"][ $keys[$i] ]["cols"]) ){
				$cols = " cols=\"".$dsc["cols"][ $keys[$i] ]["cols"]."\" ";
			}else{
				$cols = "";
			}
			if( isset($dsc["cols"][ $keys[$i] ]["rows"]) ){
				$rows = " rows=\"".$dsc["cols"][ $keys[$i] ]["rows"]."\" ";
			}else{
				$rows = "";
			}
			$control = "<textarea class=\"$input_class\" $cols $rows name=\"".$keys[$i]."\">".$$fld."</textarea>";
			break;
		case "text":
		default:
			if( isset($dsc["cols"][ $keys[$i] ]["size"]) ){
				$size = " size=\"".$dsc["cols"][ $keys[$i] ]["size"]."\" ";
			}else{
				$size = "";
			}
			$control = "<input class=\"$input_class\" $size type=\"text\" name=\"".$keys[$i]."\" value=\"".$$fld."\">";
			break;
		}
		$out .= dtcFormLineDraw($dsc["cols"][ $keys[$i] ]["legend"],$control,!($i%2));
	}
	$out .= dtcFromOkDraw()."</form></table>";
	return $out;
}

function drawRenewalsConfig(){

	global $conf_dtcadmin_path;
	$out = "";

	$dsc = array(
		"title" => _("VPS renewal email reminder configuration"),
		"action" => "vps_renewal_period",
		"forward" => array("rub","sousrub"),
		"desc" => _("These numbers represent the days before and after expiration.
Warnings before and after expiration can be listed separated by |,
while single warnings accept one value. The message templates
are stored in /etc/dtc, and if not present in: ").$conf_dtcadmin_path."/reminders_msg/
<br />For custom product types the file format is for example custom_1_expired_already.txt 
for custom product type with id 1 for the expired reminder",
		"cols" => array(
			"vps_renewal_before" => array(
				"legend" => _("Warning before expiration for VPS: "),
				"type" => "text",
				"size" => "16"),
			"vps_renewal_after" => array(
				"legend" => _("Warning after expiration for VPS: "),
                                "type" => "text",
                                "size" => "16"),
			"vps_renewal_lastwarning" => array(
				"legend" => _("Last warning for VPS: "),
                                "type" => "text",
                                "size" => "16"),
			"vps_renewal_shutdown" => array(
				"legend" => _("Shutdown warning for VPS: "),
                                "type" => "text",
                                "size" => "16"),
			"shared_renewal_before" => array(
				"legend" => _("Warning before expiration for Shared Hosting: "),
				"type" => "text",
				"size" => "16"),
			"shared_renewal_after" => array(
				"legend" => _("Warning after expiration for Shared Hosting: "),
                                "type" => "text",
                                "size" => "16"),
			"shared_renewal_lastwarning" => array(
				"legend" => _("Last warning for Shared Hosting: "),
                                "type" => "text",
                                "size" => "16"),
			"shared_renewal_shutdown" => array(
				"legend" => _("Shutdown warning for Shared Hosting: "),
                                "type" => "text",
                                "size" => "16"),
            "custom_renewal_before" => array(
				"legend" => _("Warning before expiration for Custom Products: "),
				"type" => "text",
				"size" => "16"),
			"custom_renewal_after" => array(
				"legend" => _("Warning after expiration for Custom Products: "),
                                "type" => "text",
                                "size" => "16"),
			"custom_renewal_lastwarning" => array(
				"legend" => _("Last warning for Custom Products: "),
                                "type" => "text",
                                "size" => "16"),
			"custom_renewal_shutdown" => array(
				"legend" => _("Shutdown warning for Custom Products: "),
                                "type" => "text",
                                "size" => "16"),
			"message_subject_header" => array(
				"legend" => _("Reminders and registration messages subject header:"),
                                "type" => "text",
                                "size" => "16")
			)
		);
	return configEditorTemplate ($dsc);
}

function drawNagiosConfig(){

	global $conf_dtcadmin_path;
	$out = "";

	$dsc = array(
		"title" => _("Nagios Monitoring"),
		"action" => "vps_renewal_period",
		"forward" => array("rub","sousrub"),
		"desc" => _("DTC lets you auto-configure a Nagios monitoring server, enabling your VPS customers know when their services are working. This works by generating the Nagios configuration file and copying it using SCP to the remote Nagios server, then using SSH to restart the remote Nagios service.<br><br>
Unfortunately, this requires some manual intervention to set up SSH keys: you must manually add a user to your Nagios server, and give it sudo access to restart the Nagios service. Then you must set up public key authentication for that user, add the Nagios server's SSH public key to the DTC user's SSH keyring, and create an empty config file which must be writable by the user in the Nagios server."),
		"cols" => array(
			"nagios_host" => array(
				"legend" => _("Nagios host name: "),
				"type" => "text",
				"size" => "16"),
			"nagios_username" => array(
				"legend" => _("User name on the Nagios machine: "),
                                "type" => "text",
                                "size" => "16"),
			"nagios_config_file_path" => array(
				"legend" => _("Path to the Nagios configuration file to be created: "),
                                "type" => "text",
                                "size" => "16"),
			"nagios_restart_command" => array(
				"legend" => _("Command line that, run in the Nagios server, will reload its configuration: "),
                                "type" => "text",
                                "size" => "16")
			)
		);
	return configEditorTemplate ($dsc);
}

function drawDedicatedIPConfig(){
	global $pro_mysql_dedicated_ips_table;
	global $pro_mysql_dedicated_table;
	global $pro_mysql_ip_pool_table;
	$out = "";

	$q = "SELECT server_hostname FROM $pro_mysql_dedicated_table ORDER BY server_hostname";
	$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$popup_vals = array();
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$popup_vals[] = $a["server_hostname"];
	}

	$q = "SELECT * FROM $pro_mysql_ip_pool_table";
	$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n == 0){
		$out .= "<font color=\"red\">". _("Warning: no IP pool in the database") ."</font>";
	}
	unset($my_pool_values);
	unset($my_pool_text);
	$my_pool_values = array();
	$my_pool_text = array();
	$my_pool_values[] = 0;
	$my_pool_text[] = _("Not set");
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$my_pool_values[] = $a["id"];
		$my_pool_text[] = $a["location"] . " " . $a["ip_addr"] . " " . $a["netmask"];
	}


	$dsc = array(
		"table_name" => $pro_mysql_dedicated_ips_table,
		"title" => _("Dedicated Server IP Address Pool Editor"),
		"action" => "dedicated_ip_list",
		"order_by" => "dedicated_server_hostname,ip_addr",
		"forward" => array("rub","sousrub"),
		"update_check_callback" => "checkIPAssigned",
		"insert_check_callback" => "checkIPAssigned",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"ip_addr" => array(
				"type" => "text",
				"legend" => _("IPs address")),
			"ip_pool_id" => array(
				"type" => "popup",
				"legend" => _("IP Pool"),
				"values" => $my_pool_values,
				"display_replace" => $my_pool_text),
			"dedicated_server_hostname" => array(
				"type" => "popup",
				"values" => $popup_vals,
				"legend" => _("Dedicated Server Hostname")),
			"rdns_addr" => array(
				"type" => "text",
				"size" => "30",
				"legend" => _("RDNS Hostname"))
			)
	);
	$out .= dtcDatagrid($dsc);
	return $out;
}

function drawIPPoolConfig(){
	global $pro_mysql_ip_pool_table;
	global $pro_mysql_dedicated_ips_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_cronjob_table;

	global $rub;
	global $sousrub;

	$out = "";
	$dsc = array(
		"table_name" => $pro_mysql_ip_pool_table,
		"title" => _("IP address pool editor: "),
		"action" => "ip_pool_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"editpool" => array(
				"type" => "hyperlink",
				"legend" => _("Customize"),
				"text" => _("Customize") ),
			"show_ip_pool_report" => array(
				"type" => "hyperlink",
				"legend" => _("Show"),
				"text" => _("Show") ),
			"location" => array(
				"type" => "text",
				"legend" => _("Location")),
			"ip_addr" => array(
				"type" => "text",
				"legend" => _("Network")),
			"netmask" => array(
				"type" => "text",
				"size" => "12",
				"legend" => _("Netmask")),
			"broadcast" => array(
				"type" => "text",
				"size" => "12",
				"legend" => _("Broadcast")),
			"gateway" => array(
				"type" => "text",
				"size" => "12",
				"legend" => _("Gateway")),
			"dns" => array(
				"type" => "text",
				"size" => "12",
				"legend" => _("DNS")),
			"zone_type" => array(
				"type" => "popup",
				"legend" => _("Type of zone generation"),
				"values" => array("support_ticket", "ip_per_ip", "ip_per_ip_cidr", "one_zonefile","one_zonefile_with_minus","one_zonefile_with_name","one_zonefile_with_slash"),
				"display_replace" => array(_("Support ticket"),_("One zonefile per IP"),_("One zonefile per IP with net/cidr"),_("One zonefile per pool"),_("One zonefile per pool with net-cidr"),_("One zonefiele per pool with name"),_("One zonefile per pool with net/cidr")))
			)
		);
	$out .= dtcDatagrid($dsc);
	$out .= "<br><br>";

	if(isset($_REQUEST["show_ip_pool_report"])){
		$out .= drawPool($_REQUEST["show_ip_pool_report"]);
	}

	if(isset($_REQUEST["editpool"])){
		if(isset($_REQUEST["action"]) && $_REQUEST["action"] = "edit_custom_rdns_text"){
			$q = "UPDATE $pro_mysql_ip_pool_table SET custom_part='".$_REQUEST["custom_part"]."' WHERE id='".$_REQUEST["editpool"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
			$q = "UPDATE $pro_mysql_dedicated_ips_table SET rdns_regen='yes' WHERE ip_pool_id='".$_REQUEST["editpool"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
			$q = "UPDATE $pro_mysql_vps_ip_table SET rdns_regen='yes' WHERE ip_pool_id='".$_REQUEST["editpool"]."';";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
			$q = "UPDATE $pro_mysql_cronjob_table SET reload_named='yes',gen_named='yes' WHERE 1;";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
		}
		$q = "SELECT * FROM $pro_mysql_ip_pool_table WHERE id='".$_REQUEST["editpool"]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$out .= "<font color=\"red\">"._("Error: no IP pool by that ID.")."</font>";
		}else{
			$a = mysql_fetch_array($r);
			$out .= "<h3>"._("Custom RDNS entries for the IP pool")." " .$a["location"] . " (" . $a["ip_addr"] . " / " . $a["netmask"] . "):</h3>";
			$out .= _("The following will be appened at the end of the reverse zone file.");
			$out .= "<form action=\"?\">
<input type=\"hidden\" name=\"rub\" value=\"$rub\">
<input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
<input type=\"hidden\" name=\"editpool\" value=\"".$_REQUEST["editpool"]."\">
<input type=\"hidden\" name=\"action\" value=\"edit_custom_rdns_text\">
<textarea name=\"custom_part\" cols=\"80\" rows=\"20\">".$a["custom_part"]."</textarea><br>
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
<div class=\"input_btn_left\"></div>
<div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\""._("Save")."\"></div>
<div class=\"input_btn_right\"></div></div></form>
";
		}
	}
	return $out;
}

function drawSSLIPConfig(){
	global $pro_mysql_ssl_ips_table;
	global $rub;
	global $sousrub;

	$out = "";
	$dsc = array(
		"table_name" => $pro_mysql_ssl_ips_table,
		"title" => _("SSL dedicated IP addresses: "),
		"action" => "ssl_ip_list",
		"forward" => array("rub","sousrub"),
		"update_check_callback" => "checkIPAssigned",
		"insert_check_callback" => "checkIPAssigned",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"ip_addr" => array(
				"type" => "text",
				"legend" => _("IPs addrs")),
			"port" => array(
				"type" => "text",
				"legend" => _("NAT Port")),
			"adm_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"expire" => array(
				"type" => "text",
				"size" => "14",
				"legend" => _("Expire")),
			"available" => array(
				"type" => "radio",
				"legend" => _("Available"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")))
			)
		);
	$out .= dtcDatagrid($dsc);
	$out .= "<i>"._("Take care not to add the control panel SSL IP itself if you don't want to have conflicts (and prevent apache from restarting)!")."</i><br>";
	return $out;
}

function drawTicketConfig(){
	global $pro_mysql_tik_admins_table;
	global $pro_mysql_tik_cats_table;
	global $pro_mysql_domain_table;
	global $rub;
	global $sousrub;
	global $conf_all_customers_list_domain;
	global $conf_all_customers_list_email;
	global $conf_enforce_adm_encryption;
	global $pro_mysql_list_table;
	global $pro_mysql_domain_table;
	global $pro_mysql_client_table;

	$out = "";

	$domains = array();
	$domains[] = "default";
	$q = "SELECT name FROM $pro_mysql_domain_table;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$domains[] = $a["name"];
	}

	$all_lists = array();
	$domains[] = _("no list selected");
	$q = "SELECT * FROM $pro_mysql_list_table WHERE domain='$conf_all_customers_list_domain';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." mysql said ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$all_lists[] = $a["name"];
	}


	$dsc = array(
		"title" => _("Mailing list for sending email to all customers"),
		"action" => "tik_global_param_list",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"all_customers_list_email" => array(
				"legend" => _("All customers list address: "),
				"type" => "popup",
				"values" => $all_lists
				),
			"all_customers_list_domain" => array(
				"legend" => "@",
				"type" => "popup",
				"values" => $domains
				)
			)
		);
	$out .= configEditorTemplate ($dsc);

	if( isset($_REQUEST["action"]) && $_REQUEST["action"] == "resubscript_all_users"){
		$q = "SELECT owner FROM $pro_mysql_domain_table WHERE name='$conf_all_customers_list_domain';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("No domain by this name: $conf_all_customers_list_domain line ".__LINE__." file ".__FILE__);
		}
		$a = mysql_fetch_array($r);
		$admpath = getAdminPath($a["owner"]);
		$subs_path = $admpath . "/$conf_all_customers_list_domain/lists/" . $conf_all_customers_list_domain . "_" . $conf_all_customers_list_email . "/subscribers.d";
		if( !is_dir($subs_path)){
			$out .= "<font color=\"red\">" . _("Could not find the folder"). " " . $subs_path . " " . _("when resubscribing all users.") . "</font>";
		}else{
			// First we list all files in the subscriber.d folder
			$file_list = array();
			if ($dh = opendir($subs_path)) {
				while (($file = readdir($dh)) !== false) {
					$fullpath = $subs_path . "/" . $file;
					if(filetype($fullpath) != "dir"){
						$file_list[] = $fullpath;
					}
				}
			}
			// Then we delete them all
			$n = sizeof($file_list);
			for($i=0;$i<$n;$i++){
				unlink($file_list[$i]);
			}
			// Then we get a list of all the users and we subscribe them
			$old_file = "";
			$addr_list = "";
			$q = "SELECT DISTINCT email FROM $pro_mysql_client_table ORDER BY email";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				$fname = strtolower( substr($a["email"],0,1) );
				if($fname == $old_file || $old_file == ""){
					$addr_list .= $a["email"]."\n";
				}else{
					$fullpath = $subs_path."/".$old_file;
					$fp = fopen($fullpath,"w+");
					fwrite($fp,$addr_list);
					fclose($fp);
					$addr_list = $a["email"]."\n";
				}
				$old_file = $fname;
			}
			if($n > 0){
				$fullpath = $subs_path."/".substr($a["email"],0,1);
				$fp = fopen($fullpath,"w+");
				fwrite($fp,$addr_list);
				fclose($fp);
			}
		}
	}
	$out .= "<form action=\"?\">
<input type=\"hidden\" name=\"rub\" value=\"$rub\">
<input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
<input type=\"hidden\" name=\"action\" value=\"resubscript_all_users\">
<div class=\"input_btn_container\" onMouseOver=\"this.className='input_btn_container-hover';\" onMouseOut=\"this.className='input_btn_container';\">
<div class=\"input_btn_left\"></div>
<div class=\"input_btn_mid\"><input class=\"input_btn\" type=\"submit\" value=\""._("Resubscribe all users")."\"></div>
<div class=\"input_btn_right\"></div></div></form><br><br>
"._("Note that you should always click on the above button to resubscribe all of your users before sending an email to the list")."<br>
";


	$out .= "<h3>"._("Support ticket configuration")."</h3>";

	$dsc = array(
		"title" => _("Ticket global parameters"),
		"action" => "tik_global_param",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"support_ticket_email" => array(
				"legend" => _("Support ticket email address: "),
				"type" => "text",
				"size" => "32"),
			"support_ticket_fw_email" => array(
				"legend" => _("Support ticket forward email address:"),
				"type" => "text",
				"size" => "32"),
			"support_ticket_domain" => array(
				"legend" => "@",
				"type" => "popup",
				"values" => $domains
				)
			)
		);
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_tik_admins_table,
		"title" => _("List of administrators allowed to login and receiving support messages:"),
		"action" => "tik_admins",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"pseudo" => array(
				"type" => "text",
				"legend" => _("Nickname")),
			"realname" => array(
				"type" => "text",
				"legend" => _("Real Name")),
			"email" => array(
				"type" => "text",
				"legend" => _("Email Address")),
			"tikadm_pass" => array(
				"type" => "password",
				"encrypt" => "$conf_enforce_adm_encryption",
				"legend" => _("Password")),
			"available" => array(
				"type" => "radio",
				"legend" => _("Available (will receive tickets)"),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")))
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_tik_cats_table,
		"title" => _("Ticket categories:"),
		"action" => "tik_cats",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"catname" => array(
				"type" => "text",
				"legend" => _("Category")),
			"catdescript" => array(
				"type" => "text",
				"size" => "50",
				"legend" => _("Category description"))));
	$out .= dtcDatagrid($dsc);
	return $out;
}

function drawFTPBacupConfig(){
	global $lang;

	$dsc = array(
		"title" => _("FTP Backup Configuration"),
		"action" => "ftp_backup_configuration",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"ftp_backup_activate" => array(
				"legend" => _("Activate FTP Backups:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"ftp_backup_host" => array(
				"legend" => _("Hostname:"),
				"type" => "text",
				"size" => "30"),
			"ftp_backup_login" => array(
				"legend" => _("FTP login:"),
				"type" => "text",
				"size" => "30"),
			"ftp_backup_pass" => array(
				"legend" => _("FTP password:"),
				"type" => "text",
				"size" => "30"),
			"ftp_backup_frequency" => array(
				"legend" => _("Backup frequency:"),
				"type" => "popup",
				"values" => array("day","week","month"),
				"display_replace" => array(_("Daily"),_("Weekly"),_("Monthly"))),
			"ftp_backup_dest_folder" => array(
				"legend" => _("Destination folder:"),
				"type" => "text",
				"size" => "30"),
			"ftp_active_mode" => array(
				"legend" => _("Use active FTP connection to backup server:"),
				"type"=>"radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")))));
	return configEditorTemplate ($dsc);
}

// This function is a callback when editing or adding a dom0 or a VPS IP,
// so we can make sure that there is not twice the same IP in the database
function checkIPAssigned(){
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;
	global $pro_mysql_dedicated_ips_table;
	global $pro_mysql_ssl_ips_table;
	global $action_error_txt;


	// We first force an IP to be in.
	if($_REQUEST["action"] != "ssl_ip_list_edit" && $_REQUEST["action"] != "ssl_ip_list_new" &&
	                                $_REQUEST["action"] != "dedicated_ip_list_edit" && $_REQUEST["action"] != "dedicated_ip_list_new" &&
					$_REQUEST["action"] != "vps_server_ip_list_edit" && $_REQUEST["action"] != "vps_server_ip_list_new" &&
					$_REQUEST["dom0_ips"] == ""){
		$action_error_txt = _("Please enter an IP address for your dom0.");
		return false;
	}
	// Then we check all IPs separated by | one by one
	$ret_val = true;
	if($_REQUEST["action"] == "vps_server_ip_list_edit" || $_REQUEST["action"] == "vps_server_ip_list_new" ||
				$_REQUEST["action"] == "ssl_ip_list_edit" || $_REQUEST["action"] == "ssl_ip_list_new" ||
				$_REQUEST["action"] == "dedicated_ip_list_edit" || $_REQUEST["action"] == "dedicated_ip_list_new"){
		$all_dom0_ips = array();
		$all_dom0_ips[] = $_REQUEST["ip_addr"];
		$nbr_ips = 1;
	}else{
		$all_dom0_ips = explode("|",$_REQUEST["dom0_ips"]);
		$nbr_ips = sizeof($all_dom0_ips);
	}
	for($i=0;$i<$nbr_ips;$i++){
		$test_ip = $all_dom0_ips[$i];

		// First check against all dom0 IPs

		// This request will filter a lot the IPs, but will return many IP separated by |
		// we will have to test them one by one in a loop
		// If we are editing a record for the dom0, we don't want to check this record
		if($_REQUEST["action"] == "vps_server_list_edit"){
			$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE dom0_ips LIKE '%$test_ip%' AND id != '". $_REQUEST["id"] ."';";
		}else{
			$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE dom0_ips LIKE '%$test_ip%';";
		}
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 0){
			for($j=0;$j<$n;$j++){
				$a = mysql_fetch_array($r);
				// Checking all IPs of the SQL record
				$q_all_dom0_ips = explode("|",$a["dom0_ips"]);
				$q_nbr_ips = sizeof($q_all_dom0_ips);
				for($k=0;$k<$q_nbr_ips;$k++){
					if($q_all_dom0_ips[$k] == $test_ip){
						$action_error_txt = _("The IP address $test_ip is already assigned to a dom0: cannot assign to this one.");
						$ret_val = false;
					}
				}
			}
		}

		// Then check against all VPS
		// If we are editing a VPS IP, we don't want to check this record
		if($_REQUEST["action"] == "vps_server_ip_list_edit"){
			$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_addr='$test_ip' AND id != '". $_REQUEST["id"] ."';";
		}else{
			$q = "SELECT * FROM $pro_mysql_vps_ip_table WHERE ip_addr='$test_ip';";
		}
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 0){
			$a = mysql_fetch_array($r);
			$action_error_txt = _("The IP address $test_ip is already assigned to a VPS: cannot assign this one!");
			$action_error_txt .= "<br>xen" . $a["vps_xen_name"] . " " . $a["vps_server_hostname"];
			$ret_val = false;
		}

		// Check against the SSLIP list
		if($_REQUEST["action"] == "dedicated_ip_list_edit"){
			$q = "SELECT * FROM $pro_mysql_dedicated_ips_table WHERE ip_addr='$test_ip' AND id != '". $_REQUEST["id"] ."';";
		}else{
			$q = "SELECT * FROM $pro_mysql_dedicated_ips_table WHERE ip_addr='$test_ip';";
		}
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 0){
			$a = mysql_fetch_array($r);
			$action_error_txt = _("The IP address $test_ip is already assigned to a dedicated server: cannot assign this one!");
			$action_error_txt .= "<br>". _("Server hostname: ") . $a["dedicated_server_hostname"];
			$ret_val = false;
		}


		// Check against the dedicated IP list
		if($_REQUEST["action"] == "ssl_ip_list_edit"){
			$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE ip_addr='$test_ip' AND id != '". $_REQUEST["id"] ."';";
		}else{
			$q = "SELECT * FROM $pro_mysql_ssl_ips_table WHERE ip_addr='$test_ip';";
		}
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__file__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 0){
			echo $action_error_txt = _("The IP address $test_ip is already assigned to an SSL site: cannot assign this one!");
			$ret_val = false;
		}

	}
	return $ret_val;
}

function drawVPSServerConfig(){
	global $pro_mysql_vps_table;
	global $pro_mysql_vps_ip_table;
	global $pro_mysql_vps_server_table;
	global $pro_mysql_list_table;
	global $pro_mysql_vps_server_lists_table;
	global $pro_mysql_ip_pool_table;
	global $rub;
	global $sousrub;
	global $cc_code_array;

	global $conf_main_domain;

	$out = "<h3>"._("VPS Servers and IP addresses")."</h3>";
	$dsc = array(
		"table_name" => $pro_mysql_vps_server_table,
		"title" => _("VPS Server list:"),
		"action" => "vps_server_list",
		"forward" => array("rub","sousrub"),
		"order_by" => "hostname",
		"update_check_callback" => "checkIPAssigned",
		"insert_check_callback" => "checkIPAssigned",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"edithost" => array(
				"type" => "hyperlink",
				"legend" => _("IPs addresses"),
				"text" => _("Edit IPs")),
			"dom0_ips" => array(
				"type" => "text",
				"size" => "8",
				"legend" => _("dom0 IP addresses")),
			"hostname" => array(
				"type" => "text",
				"size" => "8",
				"legend" => _("Hostname")),
			"location" => array(
				"type" => "text",
				"size" => "8",
				"legend" => _("Location")),
			"soap_login" => array(
				"type" => "text",
				"size" => "7",
				"legend" => _("SOAP login")),
			"soap_pass" => array(
				"type" => "password",
				"size" => "7",
				"legend" => _("SOAP password")),
			"country_code" => array(
				"type" => "popup",
				"legend" => _("Country"),
				"values" => array_keys($cc_code_array),
				"display_replace" => array_values($cc_code_array)),
			"lvmenable" => array(
				"type" => "radio",
				"legend" => _("Use LVM backend"),
				"display_replace" => array(_("Yes"),_("No")),
				"values" => array("yes","no"))));
	$vps_server_list = dtcDatagrid($dsc);
	$out .= $vps_server_list;

	if(isset($_REQUEST["edithost"])){
		$q = "SELECT * FROM $pro_mysql_ip_pool_table";
		$r = mysql_query($q) or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 0){
			$out .= "<font color=\"red\">". _("Warning: no IP pools in the database.") ."</font>";
		}
		unset($my_pool_values);
		unset($my_pool_text);
		$my_pool_values = array();
		$my_pool_text = array();
		$my_pool_values[] = 0;
		$my_pool_text[] = _("Not set");
		for($i=0;$i<$n;$i++){
			$a = mysql_fetch_array($r);
			$my_pool_values[] = $a["id"];
			$my_pool_text[] = $a["location"] . " " . $a["ip_addr"] . " " . $a["netmask"];
		}
		$q = "SELECT * FROM $pro_mysql_vps_server_table WHERE id='".$_REQUEST["edithost"]."';";
		$r = mysql_query($q);
		$a = mysql_fetch_array($r);
		$dsc = array(
			"table_name" => $pro_mysql_vps_ip_table,
			"title" => _("VPS IPs for ").$a["hostname"].":",
			"where_condition" => "vps_server_hostname='".$a["hostname"]."'",
			"order_by" => "vps_xen_name",
			"update_check_callback" => "checkIPAssigned",
			"insert_check_callback" => "checkIPAssigned",
			"action" => "vps_server_ip_list",
			"forward" => array("rub","sousrub","edithost"),
			"cols" => array(
				"id" => array(
					"type" => "id",
					"display" => "no",
					"legend" => "id"),
				"vps_xen_name" => array(
					"type" => "text",
					"help" => _("Names of your VPS should always be in the form 01, 02, 03 ... (including the leading zero)."),
					"legend" => _("VPS xen number")),
				"ip_addr" => array(
					"type" => "text",
					"legend" => _("IPs addrs")),
				"ip_pool_id" => array(
					"type" => "popup",
					"legend" => _("IP Pool"),
					"values" => $my_pool_values,
					"display_replace" => $my_pool_text),
				"rdns_addr" => array(
					"type" => "text",
					"size" => "30",
					"legend" => _("RDNS hostname")),
				"available" => array(
					"type" => "radio",
					"legend" => _("Available"),
					"display_replace" => array(_("Yes"),_("No")),
					"values" => array("yes","no")))
			);
		$out .= dtcDatagrid($dsc);
		$out .= "<br><br>";
		$q = "SELECT name FROM $pro_mysql_list_table WHERE domain='$conf_main_domain';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n == 0){
			$out .= _("Create a mailing list @").$conf_main_domain._(" if you want to write to all users of this VPS server.");
		}else{
			$out .= "<h3>" . _("Owners of the VPS of") . " <i>".$a["hostname"]."</i> " . _("are subscribed automatically to the following mailing list:") . "</h3>";
			$q2 = "SELECT * FROM $pro_mysql_vps_server_lists_table WHERE hostname='".$a["hostname"]."' ORDER BY list_name;";
			$r2 = mysql_query($q2)or die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n2 = mysql_num_rows($r2);
			$out .= _("Click on the list name to remove the list from the server:<br><br>");
			$conditions = "";
			for($i=0;$i<$n2;$i++){
				$a2 = mysql_fetch_array($r2);
				if($i != 0){
					$out .= " - ";
				}
				$conditions .= " AND name!='".$a2["list_name"]."'";
				$out .= "<a href=\"?action=vps_server_list_remove&rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&edithost=".$_REQUEST["edithost"]."&list_name=".$a2["list_name"]."\">".$a2["list_name"]."</a>";
			}
			$out .= "<br><br>";
			$q = "SELECT * FROM $pro_mysql_list_table WHERE domain='$conf_main_domain' $conditions ORDER BY name;";
			$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			$n = mysql_num_rows($r);
			$out .= _("Click on the list name to add the list to the server:")."<br><br>";
			for($i=0;$i<$n;$i++){
				$a = mysql_fetch_array($r);
				if($i != 0){
					$out .= " - ";
				}
				$out .= "<a href=\"?action=vps_server_list_add&rub=".$_REQUEST["rub"]."&sousrub=".$_REQUEST["sousrub"]."&edithost=".$_REQUEST["edithost"]."&name=".$a["name"]."\">".$a["name"]."</a>";
			}
		}
	}
	return $out;
}

function drawRegistrySelection(){
	global $pro_mysql_registry_table;
	$out = "<h3>". _("Registry selection") ."</h3>";
	$out .= "";
	$out .= "<form action=\"?\">
<input type=\"hidden\" name=\"rub\" value=\"".$_REQUEST["rub"]."\">
<input type=\"hidden\" name=\"sousrub\" value=\"".$_REQUEST["sousrub"]."\">
<input type=\"hidden\" name=\"action\" value=\"add_mx_trigger_backup\">
<input type=\"hidden\" name=\"install_new_config_values\" value=\"Ok\">
<tr><td><input size=\"40\" type=\"text\" name=\"server_addr\" value=\"http://dtc.\"></td>";
	$out .= "<td><input type=\"text\" name=\"server_login\" value=\"\"></td>";
	$out .= "<td><input type=\"text\" name=\"server_pass\" value=\"\"></td>";
	$out .= "<td><input type=\"submit\" name=\"add\" value=\"add\"></td></tr></form>\n";
	return $out;
}

function generalDaemonCallback(){
        global $pro_mysql_domain_table;
        global $pro_mysql_cronjob_table;
        #$q = "UPDATE $pro_mysql_domain_table SET generate_flag='yes';";
        #$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $q = "UPDATE $pro_mysql_cronjob_table SET restart_apache='yes', gen_vhosts='yes';";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

function drawGeneralConfig(){
	global $cc_code_array;
	global $dtcshared_path;
	global $conf_skin;
	global $allTLD;
	global $conf_skin;
	global $dtcshared_path;
	$dir = $dtcshared_path."/gfx/skin/";
	$out = "";

	$dsc = array(
		"title" => _("General"),
		"action" => "general_config_editor",
		"forward" => array("rub"),
		"cols" => array(
			"use_javascript" => array(
				"legend" => _("Use javascript: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
                        "send_passwords_in_emails" => array(
                                "legend" => _("Send passwords in registration emails: "),
                                "type" => "radio",
                                "values" => array("yes","no"),
                                "display_replace" => array(_("Yes"),_("No"))),
			"use_ssl" => array(
				"legend" => _("Use SSL: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"enforce_adm_encryption" => array(
				"legend" => _("Enforce admin passwords encryption: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_shared_ssl" => array(
				"legend" => _("(<a href=\"http://dtcsupport.gplhost.com/PmWiki/Name-based-shared-SSL-vhosts\">Shared SSL warnings</a>) Allow use of name based shared SSL vhosts: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"force_use_https" => array(
				"legend" => _("Force HTTPS use for contol panel: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"domain_based_ftp_logins" => array(
				"legend" => _("Use @domain.com ftp logins: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"domain_based_ssh_logins" => array(
				"legend" => _("Use @domain.com ssh logins: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"session_expir_minute" => array(
				"legend" => _("User session expire time (min): "),
				"type" => "text",
				"size" => "10"),
			"this_server_country_code" => array(
				"type" => "popup",
				"legend" => _("This server location: "),
				"values" => array_keys($cc_code_array),
				"display_replace" => array_values($cc_code_array)),
			"this_server_default_tld" => array(
				"type" => "popup",
				"legend" => _("Registration default TLD: "),
				"values" => array_values($allTLD)),
			"selling_conditions_url" => array(
				"legend" => _("Terms and Conditions URL: "),
				"type" => "text",
				"size" => "40")));
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"title" => _("Daemon"),
		"action" => "general_config_daemon",
		"forward" => array("rub"),
                "edit_callback" => "generalDaemonCallback",
		"cols" => array (
			"mta_type" => array(
				"legend" => "MTA <a href=\"http://www.wikipedia.org/wiki/Mail_transfer_agent\" target=\"_blank\">*</a> : ",
				"type" => "radio",
				"values" => array("qmail","postfix")),
			"use_cyrus" => array(
				"type" => "radio",
				"legend" => _("Use cyrus: "),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_advanced_lists_tunables" => array(
				"type" => "radio",
				"legend" => _("Show advanced mailing list options: "),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_webalizer" => array(
				"type" => "radio",
				"legend" => _("Use webalizer: "),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"webalizer_country_graph" => array(
				"legend" => _("Webalizer country graph: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_awstats" => array(
				"type" => "radio",
				"legend" => "(<a href=\"http://dtcsupport.gplhost.com/PmWiki/Why-not-using-awstats\">"._("AWStats harmful")."?</a>) " . _("Use awstats: "),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_visitors" => array(
				"type" => "radio",
				"legend" => _("Use visitors: "),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"user_mysql_prepend_admin_name" => array(
				"type" => "radio",
				"legend" => _("Prepend admin name to db names: "),
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"user_mysql_type" => array(
				"legend" => _("Location of user's database: "),
				"type" => "radio",
				"values" => array("localhost","distant"),
				"display_replace" => array(_("Same as for DTC"),_("Another location"))),
			"user_mysql_host" => array(
				"legend" => _("User MySQL host: "),
				"type" => "text"),
			"user_mysql_root_login" => array(
				"legend" => _("User MySQL root login: "),
				"type" => "text"),
			"user_mysql_root_pass" => array(
				"legend" => _("User MySQL root password: "),
				"type" => "text"),
			"autogen_webmail_alias" => array(
				"legend" => _("Generate a global /webmail alias: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"autogen_webmail_type" => array(
				"legend" => _("Type of webmail for the /webmail alias: "),
				"type" => "radio",
				"display_replace" => array("Roundcube","Squirrelmail"),
				"values" => array("roundcube","squirrelmail")),
			"apache_directoryindex" => array(
				"legend" => _("Apache DirectoryIndex Config: "),
				"size" => "50",
				"type" => "text"),
			"spam_keep_days" => array(
				"legend" => _("Keep spam email for n days"),
				"type"	=> "text")));
	$out .= configEditorTemplate ($dsc);

	// Open a known directory, and proceed to read its contents
	$skin_list = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(is_dir($dtcshared_path."/gfx/skin/".$file) && $file != "." && $file != ".."){
					$skin_list[] = $file;
				}
			}
			closedir($dh);
		}
	}
	$dsc = array(
		"title" => _("DTC Skin chooser"),
		"action" => "general_config_skin_chooser",
		"forward" => array("rub"),
		"cols" => array(
			"panel_title" => array(
				"type" => "text",
				"legend" => _("Panel title: "),
				"size" => "30"),
			"panel_subtitle" => array(
				"type" => "text",
				"legend" => _("Panel subtitle: "),
				"size" => "30"),
			"panel_logo" => array(
				"type" => "text",
				"legend" => _("Panel logo path (relative to ").$dtcshared_path."/gfx/skin/".$conf_skin."/gfx"._(") :"),
				"size" => "30"),
			"panel_logolink" => array(
				"type" => "text",
				"legend" => _("Panel logo link: "),
				"size" => "30"),
			"skin" => array(
				"legend" => _("Select the type of skin:"),
				"type" => "popup",
				"values" => $skin_list)));
	$out .= configEditorTemplate ($dsc);

	return $out;
}

function drawNetworkConfig(){
	$dsc = array(
		"title" => _("Main software configuration of DTC"),
		"action" => "general_config_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"main_site_ip" => array(
				"legend" => _("Main ip address of the server: "),
				"type" => "text",
				"size" => "20"),
			"site_addrs" => array(
				"legend" => _("Host IP addresses (separated by \"|\"): "),
				"type" => "text",
				"size" => "50"),
			"use_multiple_ip" => array(
				"legend" => _("Use multiple IPs: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_nated_vhost" => array(
				"legend" => _("Generate all apache vhosts on local network ip (NAT): "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"nated_vhost_ip" => array(
				"legend" => _("Local network area ip address of the vhost using NAT: "),
				"type" => "text",
				"size" => "20"),
			"administrative_site" => array(
				"legend" => _("Full hostname of DTC admin panel: "),
				"type" => "text",
				"size" => "50"),
			"administrative_ssl_port" => array(
				"legend" => _("SSL Port: "),
				"type" => "text",
				"size" => "10")));
	return configEditorTemplate ($dsc);
}

function namedEditionCallback(){
	global $pro_mysql_domain_table;
	global $pro_mysql_cronjob_table;
	$q = "UPDATE $pro_mysql_domain_table SET generate_flag='yes';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$q = "UPDATE $pro_mysql_cronjob_table SET reload_named='yes', gen_named='yes';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
}

function drawNamedConfig(){
	$dsc = array(
		"title" => _("Named zonefiles"),
		"action" => "named_zonefile_config_editor",
		"forward" => array("rub","sousrub"),
		"edit_callback" => "namedEditionCallback",
		"cols" => array(
			"addr_mail_server" => array(
				"legend" => _("Address of your main MX server:"),
				"type" => "text",
				"size" => "40"),
			"addr_backup_mail_server" => array(
				"legend" => _("List your backup MX servers separated by &quot;|&quot; (pipe)<br>
(leave blank if you don't have backup MX server):"),
				"type" => "text",
				"size" => "50"),
			"webmaster_email_addr" => array(
				"legend" => _("Email address of your webmaster:"),
				"type" => "text",
				"size" => "40"),
			"addr_primary_dns" => array(
				"legend" => _("Primary DNS server address:"),
				"type" => "text",
				"size" => "40"),
			"addr_secondary_dns" => array(
				"legend" => _("Secondary DNS servers address<br>
(separated by &quot;|&quot; (pipe) if more than one):"),
				"type" => "text",
				"size" => "40"),
			"ip_slavezone_dns_server" => array(
				"legend" => _("IP of the master DNS to be written in the named.slavezones.conf:"),
				"type" => "text",
				"size" => "20"),
/*			"use_cname_for_subdomains" => array(
				"legend" => _("Use CNAME instead of A record for subdomains:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))), */
			"autogen_default_subdomains" => array(
				"legend" => _("Auto-generate default subdomains (mail, pop, imap, smtp, ftp, list):"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"autogen_subdomain_list" => array(
				"legend" => _("Auto-generate subdomain list (separated by |):"),
				"type" => "text",
				"size" => "50"),
			"ip_allowed_dns_transfer" => array(
				"legend" => _("List the DNS server IPs allowed to perform zone<br>
transfers separated by &quot;|&quot; (pipe)<br>
(leave blank if you don't have backup DNS server):"),
				"type" => "text",
				"size" => "50"),
			"default_zones_ttl" => array(
				"legend" => _("Defaut TTL for your zonefiles:"),
				"type" => "text",
				"size" => "10"),
			"named_soa_refresh" => array(
				"legend" => _("SOA refresh value: "),
				"type" => "text",
				"size" => "16"),
			"named_soa_retry" => array(
				"legend" => _("SOA retry value: "),
				"type" => "text",
				"size" => "16"),
			"named_soa_expire"  => array(
				"legend" => _("SOA expire value: "),
				"type" => "text",
				"size" => "16"),
			"named_soa_default_ttl" => array(
				"legend" => _("SOA default ttl value: "),
				"type" => "text",
				"size" => "16"),
			"domainkey_publickey_filepath" => array(
				"legend" => _("Full path to location of the public.key file for DomainKey support:"),
				"type" => "text",
				"size" => "50")));

	return configEditorTemplate ($dsc);
}

function drawBackupConfig(){
        global $pro_mysql_backup_table;

	$out = "";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("List of servers that may fetch a secondary zone configuration from this server:"),
		"action" => "backup_grant_access_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='grant_access'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "14",
				"legend" => _("IP address")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$out .= "<hr width=\"100%\">";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Notify the following servers when a domain is added or removed :"),
		"action" => "trigger_dns_update_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='trigger_changes'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Notify the following servers when an email is added or removed :"),
		"action" => "trigger_mx_update_editor",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='trigger_mx_changes'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$out .= "<hr width=\"100%\">";

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Act as backup mail server for the following servers:"),
		"action" => "act_as_backup_mail",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='mail_backup'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);

	$dsc = array(
		"table_name" => $pro_mysql_backup_table,
		"title" => _("Act as backup DNS server for the following servers:"),
		"action" => "act_as_backup_dns",
		"forward" => array("rub","sousrub"),
		"where_condition" => "type='dns_backup'",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"server_addr" => array(
				"type" => "text",
				"size" => "35",
				"legend" => _("URL")),
			"server_login" => array(
				"type" => "text",
				"legend" => _("Login")),
			"server_pass" => array(
				"type" => "password",
				"legend" => _("Password"))
			)
		);
	$out .= dtcDatagrid($dsc);
	return $out;
}

function drawRegistryApiConfig(){

	global $pro_mysql_config_table;
	global $pro_mysql_registrar_domains_table;
	global $allTLD;
	global $registry_api_modules;

	$out = "";

	// Load all registrar names
	$all_registrar = array();
	$nbr_registrar = sizeof($registry_api_modules);
	for($i=0;$i<$nbr_registrar;$i++){
		$all_registrar[] = $registry_api_modules[$i]["name"];
	}

	// Allow activation of the registrar API (not everyone is using it...)
	$dsc = array(
		"title" => _("Domain name registry API configuration"),
		"action" => "domain_registry_config_yesno_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_registrar_api" => array(
				"legend" => _("Use registrar API:"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")))
		)
	);
	$out .= configEditorTemplate ($dsc);

	// Display a grid so the root admin can configure what registrar will be used for what TLD
	$dsc = array(
		"title" => _("Registrar selection and final customer pricing"),
		"action" => "tld_vs_registrar_selection",
		"forward" => array("rub","sousrub"),
		"table_name" => $pro_mysql_registrar_domains_table,
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id",
				),
			"tld" => array(
				"type" => "popup",
				"legend" => _("TLD"),
				"values" => $allTLD,
				),
			"price" => array(
				"type" => "text",
				"legend" => _("Price"),
				),
			"registrar" => array(
				"type" => "popup",
				"legend" => _("Registrar"),
				"values" => $all_registrar
				)
		)
	);
	$out .= dtcDatagrid($dsc);

	// Display the configurator for each registrar (configuration is in the main.php of each module)
	for($i=0;$i<$nbr_registrar;$i++){
		$dsc = $registry_api_modules[$i]["configure_descriptor"];
		$out .= configEditorTemplate ($dsc);
	}
	return $out;
}

function drawDTCpayConfig(){
	global $pro_mysql_secpayconf_table;
	global $pro_mysql_custom_fld_table;

	$out = "";

	$dsc = array(
		"title" => _("Web host subdomain registration"),
		"action" => "own_domain_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"provide_own_domain_hosts" => array(
				"legend" => _("Allow registration of subdomain of the main domain: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))) ));
	$out .= configEditorTemplate($dsc);

	$dsc = array(
		"title" => _("Affiliation"),
		"action" => "affiliate_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"affiliate_return_domain" => array(
				"legend" => _("Domain name for affiliation return URL:"),
				"type" => "text"
				)));
	$out .= configEditorTemplate($dsc);

	$dsc = array(
		"title" => _("Secure payment configuration"),
		"action" => "payment_gateway_currency_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"currency_symbol" => array(
				"legend" => _("Currency symbol: "),
				"type" => "text",
				"size" => "6"),
			"currency_letters" => array(
				"legend" => _("Currency letters: "),
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => _("Every days or months, scp all invoices"),
		"action" => "invoice_scp_addr_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"invoice_scp_when" => array(
				"legend" => _("How often should scp occure: "),
				"type" => "radio",
				"values" => array( "day", "month"),
				"display_replace" => array(_("Every day"),_("Every month"))),
			"invoice_scp_addr" => array(
				"legend" => _("scp address: "),
				"type" => "text",
				"size" => "40")));  
	$out .= configEditorTemplate($dsc);

	$dsc = array(
		"title" => _("Custom registration fields"),
		"table_name" => $pro_mysql_custom_fld_table,
		"action" => "custom_registration_field_editor",
		"forward" => array("rub","sousrub"),
		"order_by" => "widgetorder",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "Id"),
			"widgetorder" => array(
				"legend" => _("Display order"),
				"type" => "text",
				"size" => "4"),
			"varname" => array(
				"legend" => _("Variable name"),
				"type" => "text",
				"size" => "15"),
			"question" => array(
				"legend" => _("Question to the user"),
				"type" => "text",
				"size" => "30"),
			"widgettype" => array(
				"legend" => _("Widget type"),
				"type" => "popup",
				"values" => array( "text", "popup", "radio", "textarea")),
			"widgetvalues" => array(
				"legend" => _("Possible values"),
				"type" => "text",
				"size" => "20"),
			"widgetdisplay" => array(
				"legend" => _("Corresponding display"),
				"type" => "text",
				"size" => "30"),
			"mandatory" => array(
				"legend" => _("Mandatory field"),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No")),
				"default" => "no")
			)
		);
	$out .= dtcDatagrid($dsc);
	$out .= _("On the above tables, the possible values are what is is going to be the internal value in the popup or radio buttons,
which is what is going to be recorded in the database. Values are separated by \"|\". The corresponding display is what will actually
be displayed to your users instead of the popup value.")."<br>";

	$dsc = array(
		"title" => _("Cheques and wire transfers:"),
		"action" => "cheques_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"accept_cheques" => array (
				"legend" => _("Accept cheques: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"cheques_flat_fees" => array (
				"legend" => _("Flat fee for accepting cheques: "),
				"size" => "6",
				"type" => "text"),
			"cheques_to_label" => array (
				"legend" => _("Cheques Payable To: "),
				"size" => "30",
				"type" => "text"),
			"cheques_send_address" => array (
				"legend" => _("Instructions to send cheques: "),
				"cols" => "60",
				"rows" => "7",
				"type" => "textarea"),
			"accept_wiretransfers" => array (
				"legend" => _("Accept wire transfers to bank: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"wiretransfers_flat_fees" => array (
				"legend" => _("Flat fee for accepting wire transfers: "),
				"size" => "6",
				"type" => "text"),
			"wiretransfers_bank_details" => array (
				"legend" => _("Bank account details: "),
				"cols" => "60",
				"rows" => "12",
				"type" => "textarea")
			)
		);
	$out .= configEditorTemplate ($dsc,"secpay");

//  -- zion --
	$dsc = array(
		"title" => "WebMoney:",
		"action" => "webmoney_gateway_webmoney_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_webmoney" => array(
				"legend" => _("Use WebMoney: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"webmoney_license_key" => array(
				"legend" => _("WebMoney secret key: "),
				"type" => "text",
				"size" => "44"),
			"webmoney_wmz" => array(
				"legend" => _("WMZ: "),
				"type" => "text",
				"size" => "44") ));
	$out .= configEditorTemplate ($dsc,"secpay");
// -- zion --

	$dsc = array(
		"title" => "PayPal:",
		"action" => "payment_gateway_paypal_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_paypal" => array(
				"legend" => _("Use paypal: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"paypal_autovalidate" => array(
				"legend" => _("Validate new account if paid: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"paypal_email" => array(
				"legend" => _("PayPal business account email: "),
				"type" => "text",
				"size" => "30"),
			"paypal_rate" => array(
				"legend" => _("PayPal fee rate: "),
				"type" => "text",
				"size" => "6"),
			"paypal_flat" => array(
				"legend" => _("PayPal flat fee: "),
				"type" => "text",
				"size" => "6"),
			"paypal_validate_with" => array(
				"legend" => _("Validate payment if amount is greater or equal to: "),
				"type" => "radio",
				"values" => array("total","mc_gross"),
				"display_replace" => array(_("Grand total"),_("Gross amount"))),
			"paypal_sandbox" => array(
				"legend" => _("Use the sandbox test server: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_paypal_recurring" => array(
				"legend" => _("Use paypal recuring: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"paypal_sandbox_email" => array(
				"legend" => _("PayPal test account email (sandbox): "),
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => _("MoneyBookers (currently in development):"),
		"action" => "payment_gateway_moneybookers_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_moneybookers" => array(
				"legend" => _("Use MoneyBookers: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"moneybookers_autovalidate" => array(
				"legend" => _("Validate new account if paid: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"moneybookers_email" => array(
				"legend" => _("MoneyBookers business account email: "),
				"type" => "text",
				"size" => "30"),
			"moneybookers_secret_word" => array(
				"legend" => _("MoneyBookers secret word: "),
				"type" => "text",
				"size" => "10"),
			"moneybookers_rate" => array(
				"legend" => _("MoneyBookers fee rate: "),
				"type" => "text",
				"size" => "6"),
			"moneybookers_flat" => array(
				"legend" => _("MoneyBookers flat fee: "),
				"type" => "text",
				"size" => "6"),
			"moneybookers_validate_with" => array(
				"legend" => _("Validate payment if amount is greater or equal to: "),
				"type" => "radio",
				"values" => array("total","mc_gross"),
				"display_replace" => array(_("Grand total"),_("Gross amount"))),
			"moneybookers_sandbox" => array(
				"legend" => _("Use the sandbox test server: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"moneybookers_sandbox_email" => array(
				"legend" => _("MoneyBookers test account email (sandbox): "),
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => "eNETS:",
		"action" => "payment_gateway_enets_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_enets" => array(
				"legend" => _("Use eNETS: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"use_enets_test" => array(
				"legend" => _("eNETS server: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Test server"),_("Production server"))),
			"enets_mid_id" => array(
				"legend" => _("eNETS merchant ID: "),
				"type" => "text",
				"size" => "6"),
			"enets_test_mid_id" => array(
				"legend" => _("eNETS test merchant ID: "),
				"type" => "text",
				"size" => "6"),
			"enets_rate" => array(
				"legend" => _("eNETS rate: "),
				"type" => "text",
				"size" => "6")));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => _("Maxmind API:"),
		"action" => "maxmind_api_conf_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_maxmind" => array(
				"legend" => _("Use Maxmind API: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"maxmind_login" => array(
				"legend" => _("Maxmind login: "),
				"type" => "text",
				"size" => "30"),
			"maxmind_license_key" => array(
				"legend" => _("Maxmind license key: "),
				"type" => "text",
				"size" => "30"),
			"maxmind_threshold" => array(
				"legend" => _("Maxmind fraud threshold: "),
				"type" => "number",
				"size" => "3"),

			));
	$out .= configEditorTemplate ($dsc,"secpay");

	$dsc = array(
		"title" => _("Dineromail:"),
		"action" => "dineromail_gateway_dineromail_edit",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"use_dineromail" => array(
				"legend" => _("Use dineromail: "),
				"type" => "radio",
				"values" => array("yes","no"),
				"display_replace" => array(_("Yes"),_("No"))),
			"dineromail_nrocuenta" => array(
				"legend" => _("Dineromail Account: "),
				"type" => "text",
				"size" => "20"),
			"dineromail_tipospago" => array(
				"legend" => _("Payments Accepted: "),
				"type" => "checkboxcomma",
				"values" => array("2","7","13","4","5","6","14","15","16","17","18"),
				"display_replace" => array(_("Barcode"),_("DineroMail account funds"),_("Wiretransfer"),_("Credit card in one payment"),
					_("Credit card in 3 installments"),_("Credit card in 6 installments"),_("Credit card in 9 installments"),
					_("Credit card in 12 installments"),_("Credit card in 18 installments"),_("Credit card in 24 installments"),_("Credit card in Z plan"))),
			"dineromail_cargocomision" => array(
				"legend" => _("Fixed charge fee: "),
				"type" => "text",
				"size" => "6"),
			"dineromail_porcentajecomision" => array(
				"legend" => _("Percentage fee: "),
				"type" => "text",
				"size" => "6"),
			"dineromail_logo_url" => array(
				"legend" => _("Display payment image url (leave blank for default): "),
				"type" => "text",
				"size" => "6")
				));
	$out .= configEditorTemplate ($dsc,"secpay");

	return $out;
}

function drawDTCradiusConfig(){
	global $conf_dtcshared_path;
	global $pro_mysql_nas_table;
	global $pro_mysql_radgroup_table;
	global $pro_mysql_radgroupcheck_table;
	global $pro_mysql_radgroupreply_table;
	global $pro_mysql_raduser_table;
	global $pro_mysql_radcheck_table;
	global $pro_mysql_radreply_table;
	global $pro_mysql_product_table;
	global $pro_mysql_dedicated_table;
	global $lang;

	$out = "WARNING!!! All parameteres (specially Attributes) set here have NO validation at all, so be careful.<BR>Freeradius will have unexpected behavoiurs if you do mistakes or errors here, and may be it can't start.";
	$dsc = array(
		"title" => _("NAS config"),
		"table_name" => $pro_mysql_nas_table,
		"action" => "nas_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "nasname",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"nasname" => array(
				"type" => "text",
				"disable_edit" => "yes",
				"legend" => _("Name")),
			"shortname" => array(
				"type" => "text",
				"legend" => _("Short name")),
			"type" => array(
				"type" => "popup",
				"legend" => _("Type"),
				"values" => array("cisco","computone","livingston","max40xx","multitech","netserver","pathras","patton","portslave","tc","usrhiper","other")),
			"ports" => array(
				"type" => "text",
				"legend" => _("Port number"),
				"check" => "number"),
			"secret" => array(
				"type" => "password",
				"legend" => _("Password"),
				"check" => "dtc_pass"),
			"server" => array(
				"type" => "text",
				"legend" => _("Virtual Radius Server")),
			"community" => array(
				"type" => "text",
				"legend" => _("SNMP community")),
			"description" => array(
				"type" => "text",
				"legend" => _("Description"))));
	$out .= dtcDatagrid($dsc);
	$out .= "<BR><BR>";

	$dsc = array(
		"title" => _("Group config"),
		"table_name" => $pro_mysql_radgroup_table,
		"action" => "group_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "GroupName",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"GroupName" => array(
				"type" => "text",
				"disable_edit" => "yes",
				"legend" => _("Name")),
			"Description" => array(
				"type" => "text",
				"legend" => _("Description"))));
	$out .= dtcDatagrid($dsc);
	$out .= "<BR><BR>";

        $q = "SELECT * FROM $pro_mysql_radgroup_table WHERE 1;";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $n = mysql_num_rows($r);
        $group_names = array(_("Please select"));
        $group_ids = array(0);
        for($i=0;$i<$n;$i++){
                $a = mysql_fetch_array($r);
                $group_names[] = $a["GroupName"];
//		echo $a["GroupName"];
                $group_ids[] = $a["id"];
        }

	$dsc = array(
		"title" => _("Group Attributes checking config"),
		"table_name" => $pro_mysql_radgroupcheck_table,
		"action" => "group_attribute_check_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "GroupName",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"GroupName" => array(
				"type" => "popup",
				"disable_edit" => "yes",
				"legend" => _("Group"),
				"values" => $group_ids,
				"display_replace" => $group_names),
			"Attribute" => array(
				"type" => "text",
				"legend" => _("Attribute")),
			"op" => array(
				"type" => "popup",
				"legend" => _("Operation"),
				"values" => array("==",":=","+=","!=",">=","<=",">","<","=~","!~","=*","!*")),
			"Value" => array(
				"type" => "text",
				"legend" => _("Value"))));
	$out .= dtcDatagrid($dsc);
	$out .= "<BR><BR>";


	$dsc = array(
		"title" => _("Group Reply Attributes config"),
		"table_name" => $pro_mysql_radgroupreply_table,
		"action" => "group_attribute_reply_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "GroupName",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"GroupName" => array(
				"type" => "popup",
				"disable_edit" => "yes",
				"legend" => _("Group"),
				"values" => $group_ids,
				"display_replace" => $group_names),
			"Attribute" => array(
				"type" => "text",
				"legend" => _("Attribute")),
			"op" => array(
				"type" => "popup",
				"legend" => _("Operation"),
				"values" => array("=",":=","+=")),
			"Value" => array(
				"type" => "text",
				"legend" => _("Value")),
			"prio" => array(
				"type" => "text",
				"legend" => _("Priority"),
				"size" => 5)));
	$out .= dtcDatagrid($dsc);
	$out .= "<BR><BR>";

        $q = "SELECT * FROM $pro_mysql_raduser_table WHERE 1;";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $n = mysql_num_rows($r);
        $user_names = array(_("Please select"));
        $user_ids = array(0);
        for($i=0;$i<$n;$i++){
                $a = mysql_fetch_array($r);
                $user_names[] = $a["UserName"];
//		echo $a["UserName"];
                $user_ids[] = $a["id"];
        }

        $q = "SELECT server_hostname,$pro_mysql_dedicated_table.id FROM $pro_mysql_dedicated_table,$pro_mysql_product_table WHERE $pro_mysql_dedicated_table.product_id=$pro_mysql_product_table.id and use_radius='yes';";
        $r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
        $n = mysql_num_rows($r);
        $dedicated_names = array(_("None"));
        $dedicated_ids = array(0);
        for($i=0;$i<$n;$i++){
                $a = mysql_fetch_array($r);
                $dedicated_names[] = $a["server_hostname"];
//		echo $a["server_hostname"];
                $dedicated_ids[] = $a["id"];
        }

	$dsc = array(
		"title" => _("Radius Users"),
		"table_name" => $pro_mysql_raduser_table,
		"action" => "raduser_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "UserName",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"UserName" => array(
				"type" => "text",
				"disable_edit" => "yes",
				"legend" => _("Username")),
			"Password" => array(
				"type" => "password",
				"legend" => _("Password")),
			"GroupName" => array(
				"type" => "popup",
				"legend" => _("Group"),
				"values" => $group_ids,
				"display_replace" => $group_names),
			"Dedicated_id" => array(
				"type" => "popup",
				"legend" => _("Associated Dedicated Service"),
				"values" => $dedicated_ids,
				"display_replace" => $dedicated_names)));
	$out .= dtcDatagrid($dsc);
	$out .= "<BR><BR>";

	$dsc = array(
		"title" => _("User Attributes checking config"),
		"table_name" => $pro_mysql_radcheck_table,
		"action" => "user_attribute_check_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "UserName",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"UserName" => array(
				"type" => "popup",
				"disable_edit" => "yes",
				"legend" => _("Username"),
				"values" => $user_names),
			"Attribute" => array(
				"type" => "text",
				"legend" => _("Attribute")),
			"op" => array(
				"type" => "popup",
				"legend" => _("Operation"),
				"values" => array("==",":=","+=","!=",">=","<=",">","<","<>","=~","!~","=*","!*")),
			"Value" => array(
				"type" => "text",
				"legend" => _("Value"))));
	$out .= dtcDatagrid($dsc);
	$out .= "<BR><BR>";


	$dsc = array(
		"title" => _("User Reply Attributes config"),
		"table_name" => $pro_mysql_radreply_table,
		"action" => "user_attribute_reply_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "UserName",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"UserName" => array(
				"type" => "popup",
				"disable_edit" => "yes",
				"legend" => _("Username"),
				"values" => $user_names),
			"Attribute" => array(
				"type" => "text",
				"legend" => _("Attribute")),
			"op" => array(
				"type" => "popup",
				"legend" => _("Operation"),
				"values" => array("=",":=","+=")),
			"Value" => array(
				"type" => "text",
				"legend" => _("Value"))));
	$out .= dtcDatagrid($dsc);
	$out .= "<BR><BR>";

	return $out;
}

function drawDTCpathConfig(){
	global $conf_dtcshared_path;
	global $conf_site_root_host_path;
	global $conf_generated_file_path;

	global $conf_qmail_rcpthost_path;
	global $conf_qmail_virtualdomains_path;
	global $conf_qmail_assign_path;
	global $conf_qmail_poppasswd_path;

	global $conf_apache_vhost_path;
	global $conf_php_additional_library_path;
	global $conf_php_library_path;

	global $conf_named_path;
	global $conf_named_slavefile_path;
	global $conf_named_slavezonefiles_path;
	global $conf_named_zonefiles_path;

	global $conf_backup_script_path;
	global $conf_bakcup_path;
	global $conf_webalizer_stats_script_path;

	global $conf_chroot_path;

	global $conf_htpasswd_path;

	global $lang;

	$out = "";

	$dsc = array(
		"title" => _("Paths"),
		"action" => "main_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"dtcshared_path" => array(
				"legend" => _("DTC shared directory path:"),
				"type" => "text"),
			"site_root_host_path" => array(
				"legend" => _("Default new account directory:"),
				"type" => "text"),
			"chroot_path" => array(
				"legend" => _("Path of the cgi-wrapper disk template (chroot for SBOX):"),
				"type" => "text"),
			"generated_file_path" => array(
				"legend" => _("Base path to DTC-generated daemon configuration files.<br>
All of the DTC daemon config files defined on this page will be saved here:"),
				"type" => "text"),
			"htpasswd_path" => array(
				"legend" => _("Apache htpasswd path"),
				"type" => "text")));
	$out .= configEditorTemplate ($dsc);


	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_mail.gif\">"._("Qmail path").":",
		"action" => "qmail_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"qmail_rcpthost_path" => array(
				"legend" => "rcpthosts: ",
				"type" => "text",
				"size" => "26"),
			"qmail_virtualdomains_path" => array(
				"legend" => "virtualdomains: ",
				"type" => "text",
				"size" => "26"),
			"qmail_assign_path" => array(
				"legend" => "assign: ",
				"type" => "text",
				"size" => "26"),
			"qmail_poppasswd_path" => array(
				"legend" => "poppasswd: ",
				"type" => "text",
				"size" => "26")));
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_web.gif\">" . _("Apache file names"),
		"action" => "apache_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"apache_vhost_path" => array(
				"legend" => _("Virtual host config-file:"),
				"type" => "text",
				"size" => "20"),
			"php_library_path" => array(
				"legend" => _("PHP libraries open_basedir<br>
(separated by \":\", reset on each dtc install):"),
				"type" => "text",
				"size" => "60"),
			"php_additional_library_path" => array(
				"legend" => _("PHP open_basedir additional libraries path<br>
(survives reinstallation):"),
				"type" => "text",
				"size" => "60")));
	$out .= configEditorTemplate ($dsc);

	$dsc = array(
		"title" => "<img src=\"gfx/dtc/generate_named.gif\"> " . _("Named file names"),
		"action" => "named_path_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"named_path" => array(
				"legend" => _("Named main file:"),
				"type" => "text",
				"size" => "30"),
			"named_slavefile_path" => array(
				"legend" => _("Named slave file:"),
				"type" => "text",
				"size" => "30"),
			"named_zonefiles_path" => array(
				"legend" => _("Named main zonefiles folder:"),
				"type" => "text",
				"size" => "30")));
	$out .= configEditorTemplate ($dsc);

	return $out;
}

function drawCompaniesConfig(){
	global $pro_mysql_companies_table;
	global $cc_code_array;
	global $conf_generated_file_path;

	$out = "";

	$country_codes = array_keys($cc_code_array);
	$country_fullnames = array_values($cc_code_array);
	$dsc = array(
		"title" => _("List of your companies:"),
		"new_item_title" => _("Add a new company:"),
		"new_item_link" => _("Add a new company"),
		"edit_item_title" => _("Edit a company:"),
		"table_name" => $pro_mysql_companies_table,
		"action" => "hosting_company_editor",
		"forward" => array("rub","sousrub"),
		"id_fld" => "id",
		"list_fld_show" => "name",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"name" => array(
				"type" => "text",
				"size" => "30",
				"legend" => _("Company Name:")),
			"address" => array(
				"type" => "textarea",
				"cols" => "60",
				"rows" => "5",
				"legend" => _("Address:")),
			"country" => array(
				"type" => "popup",
				"legend" => _("Country:"),
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"registration_number" => array(
				"type" => "text",
				"size" => "30",
				"legend" => _("Registration Number:")),
			"vat_number" => array(
				"type" => "text",
				"size" => "30",
				"legend" => _("VAT Number:")),
			"vat_rate" => array(
				"type" => "text",
				"size" => "10",
				"legend" => _("VAT Rate:")),
			"logo_path" => array(
				"type" => "text",
                                "size" => "30",
                                "legend" => _("Logo path relative to") . "<br>$conf_generated_file_path/invoice_pics/:"),
			"text_after" => array(
				"type" => "textarea",
				"cols" => "60",
				"rows" => "5",
				"legend" => _("Invoice free text:")),
			"footer" => array(
				"type" => "textarea",
				"cols" => "60",
				"rows" => "5",
				"legend" => _("Invoice Footer:") )));
	$out .= dtcListItemsEdit($dsc);
	return $out;
}

function drawInvoicingConfig(){
	global $pro_mysql_companies_table;
	global $pro_mysql_invoicing_table;
	global $cc_code_array;

	$out = "";

	$q = "SELECT * FROM $pro_mysql_companies_table WHERE 1;";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	$comp_names = array(_("Please select"));
	$comp_ids = array(0);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		$comp_names[] = $a["name"];
		$comp_ids[] = $a["id"];
	}

	$dsc = array(
		"title" => _("Default company invoicing:"),
		"action" => "default_company_invoicing_editor",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"default_company_invoicing" => array(
				"legend" => _("Default company invoicing:"),
				"type" => "popup",
				"values" => $comp_ids,
				"display_replace" => $comp_names)));
	$out .= configEditorTemplate ($dsc);

	$country_codes = array_keys($cc_code_array);
	$country_fullnames = array_values($cc_code_array);
	$country_codes = array_reverse($country_codes);
	$country_fullnames = array_reverse($country_fullnames);
	$country_codes[] = "00";
	$country_fullnames[] = _("none");
	$country_codes = array_reverse($country_codes);
	$country_fullnames = array_reverse($country_fullnames);

	$dsc = array(
		"table_name" => $pro_mysql_invoicing_table,
		"title" => _("Customer and Service country vs. Company:"),
		"action" => "cust_and_serv_country_vs_comp",
		"forward" => array("rub","sousrub"),
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => "id"),
			"service_country_code" => array(
				"type" => "popup",
				"legend" => _("Service Country"),
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"customer_country_code" => array(
				"type" => "popup",
				"legend" => _("Customer Country"),
				"values" => $country_codes,
				"display_replace" => $country_fullnames),
			"company_id" => array(
				"type" => "popup",
				"legend" => _("Company Name"),
				"values" => $comp_ids,
				"display_replace" => $comp_names)
			)
		);
	$out .= dtcDatagrid($dsc);

	return $out;
}


function drawDTCConfigForm(){
	if(!isset($_REQUEST["sousrub"])){
		$sousrub = "general";
	}else{
		$sousrub = $_REQUEST["sousrub"];
	}

	switch($sousrub){
	default:
	case "general":
		return drawGeneralConfig();
	case "ip":
		return drawNetworkConfig();
	case "sslip":
		return drawSSLIPConfig();
	case "ip_pool":
		return drawIPPoolConfig();
	case "nagios":
		return drawNagiosConfig();
	case "dedicatedip":
		return drawDedicatedIPConfig();
	case "zonefile":
		return drawNamedConfig();
        case "backup":
                return drawBackupConfig();
        case "registryapi":
		return drawRegistryApiConfig();
        case "payconf":
                return drawDTCpayConfig();
	case "companies":
		return drawCompaniesConfig();
	case "invoicing":
		return drawInvoicingConfig();
        case "radius":
                return drawDTCradiusConfig();
        case "ftpbackup":
		return drawFTPBacupConfig();
	case "path":
		return drawDTCpathConfig();
	case "ticket":
		return drawTicketConfig();
        case "renewals":
		return drawRenewalsConfig();
        case "vps":
		return drawVPSServerConfig();
	}
}

?>
