<?php

function drawAdminTools_DomainInfo($admin,$eddomain){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $dtcshared_path;
	global $conf_administrative_site;
	global $pro_mysql_domain_table;
	global $pro_mysql_whois_table;
	global $renew_return;
	global $secpayconf_currency_letters;
	$out = "";

	$webname = $eddomain["name"];

	get_secpay_conf();

	// Domain registration API stuffs
	$out .= "<br><h3>". _("Registration:") ."</h3>";
	if($eddomain["whois"] == "away"){
		$out .= _("Your domain is not registered here.");
	}else{
		$q = "SELECT * FROM $pro_mysql_domain_table WHERE name='$webname';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			$out .= _("Cannot find your domain name registration information in the database.");
		}else{
			$a = mysql_fetch_array($r);
			// Domain renewals
			if( isset($_REQUEST["action"]) && $_REQUEST["action"] == "renew_domain"){
				$out .= dtcFormTableAttrs();
				$out .= dtcFormLineDraw( _("Money in your account: "), $admin["client"]["dollar"]." $secpayconf_currency_letters",1);
				$tld = find_domain_extension($webname);
				$out .= dtcFormLineDraw( _("Type of extension: "),$tld,0);
				$out .= dtcFormLineDraw( _("Renewal for how many years: "),$_REQUEST["num_years"],1);
				$price = find_domain_price($tld);
				if($price === FALSE){
					$out .= dtcFormLineDraw( "", "<font color=\"red\">"._("Price for the domain not found.")."</font>",0);
					$out .= "</table>";
				}else{
					$price = $_REQUEST["num_years"] * $price;
					$out .= dtcFormLineDraw( _("Total price: "),$price." $secpayconf_currency_letters",0);
					$remaining = $admin["client"]["dollar"] - $price;
					$out .= dtcFormLineDraw( _("Balance after transaction: "), $remaining." $secpayconf_currency_letters",1);
					if($remaining < 0){
						$out .= dtcFormLineDraw( "", "<font color=\"red\">"._("Insufficient balance for the transaction, please go to \"My account\" and add money.")."</font>",0);
						$out .= "</table>";
					}else{
						$out .= dtcFormLineDraw( "", "<form action=\"?\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$webname."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"action\" value=\"registry_renew_domain\">
<input type=\"hidden\" name=\"num_years\" value=\"".$_REQUEST["num_years"]."\">
".submitButtonStart()._("Renew domain").submitButtonEnd()."</form>",0);
						$out .= "</table>";
					}
				}
			}elseif(isset($_REQUEST["action"]) && $_REQUEST["action"] == "registry_renew_domain"){
				$out .= $renew_return["response_text"];
                        }else{
				$out .= dtcFormTableAttrs();

				// Domain auth code
				$authcode = registry_get_auth_code($webname);
				if($authcode === FALSE || $authcode["is_success"] != 1){
					$txt = _("Auth code retrieval failed.");
				}else{
					$txt = $authcode["response_text"];
				}
	                        $frm = "<form action=\"?\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"action\" value=\"renew_domain\">";
				$out .= dtcFormLineDraw( _("Registrar: ") . $frm , $a["registrar"],1);
				$out .= dtcFormLineDraw( _("Creation date: "), $a["creation_date"],0);
				$out .= dtcFormLineDraw( _("Last modification date: "), $a["modification_date"],1);
				$out .= dtcFormLineDraw( _("Expiration date: "), $a["expiration_date"],0);
				$out .= dtcFormLineDraw( _("Domain auth code: "), $txt,1);
				$out .= dtcFormLineDraw( "<select name=\"num_years\">
<option value=\"1\">1</option>
<option value=\"2\">2</option>
<option value=\"3\">3</option>
<option value=\"4\">4</option>
<option value=\"5\">5</option>
<option value=\"6\">6</option>
<option value=\"7\">7</option>
<option value=\"8\">8</option>
<option value=\"9\">9</option>
</select>"._("year(s)"), submitButtonStart()._("Renew domain").submitButtonEnd()."</form>",0);
	                        $out .= "</table>";
			}
			// Domain protection
			if( isset($_REQUEST["action"]) && $_REQUEST["action"] == "change_domain_protection"){
				switch($_REQUEST["protection"]){
				case "unlocked":
					$sel = "unlocked";
					break;
				case "transferprot":
					$sel = "transferprot";
					break;
				default:
				case "locked":
					$sel = "locked";
					break;
				}
				$ret = registry_set_domain_protection($webname,$sel);
				if($ret != FALSE && $ret["is_success"] == 1){
					$q = "UPDATE $pro_mysql_domain_table SET protection='$sel' WHERE name='$webname';";
					$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__."sql said: ".mysql_error());
					$a["protection"] = $sel;
				}
			}
                        $frm = "<form action=\"?\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"action\" value=\"change_domain_protection\">";
			$out .= dtcFormTableAttrs();
			$unlck_sel = "";
			$trans_sel = "";
			$lockd_sel = "";
			switch($a["protection"]){
			case "unlocked":
				$unlck_sel = " selected ";
				break;
			case "transferprot":
				$trans_sel = " selected ";
				break;
			default:
			case "locked":
				$lockd_sel = " selected ";
				break;
			}
			$out .= dtcFormLineDraw( _("Domain protection: ") . $frm , "<select name=\"protection\">
<option value=\"unlocked\" $unlck_sel>"._("Domain name unlocked")."</option>
<option value=\"transferprot\" $trans_sel>"._("Domain name transfer protected")."</option>
<option value=\"locked\" $lockd_sel>"._("Domain name protected")."</option>
</select>",1);
			$out .= dtcFormLineDraw( "", submitButtonStart()._("Set protection").submitButtonEnd(),0);
			$out .= "</form></table>";

		}
	}	// End of domain registration API code

	// Retrive domain config
	$quota = $eddomain["quota"];
	$max_email = $eddomain["max_email"];
	$max_ftp = $eddomain["max_ftp"];
	$max_subdomain = $eddomain["max_subdomain"];
	$domain_parking = $eddomain["domain_parking"];
	$domain_parking_type = $eddomain["domain_parking_type"];

	$adm_path = $admin["info"]["path"];

	// Retrive disk usage
//	$du_string = exec("du -sm $adm_path/$webname --exclude=access.log",$retval);
//	$du_state = explode("\t",$du_string);
//	$du = $du_state[0];

	// The upper version might be too slow and give a bad feeling to the user. This one should be a lot better:
	$du_stat = $eddomain["du_stat"];
	$du = $du_stat;

	// Retrive number of mailbox
	if(isset($eddomain["emails"]))	$email_nbr = sizeof($eddomain["emails"]);
	else	$email_nbr = 0;
	// Retrive number of ftp account
	if(isset($eddomain["ftps"]))	$ftp_nbr = sizeof($eddomain["ftps"]);
	else	$ftp_nbr = 0;
	// Retrive number of ftp account
	$subdomain_nbr = sizeof($eddomain["subdomains"]);

	$total_http_transfer = fetchHTTPInfo($webname);
	$total_ftp_transfer = fetchFTPInfo($webname);
	$total_pop_transfer = fetchPOPInfo($webname);
	$total_imap_transfer = fetchIMAPInfo($webname);
	$total_smtp_transfer = fetchSMTPInfo($webname);
	$total_transfer = smartByte($total_http_transfer + $total_ftp_transfer + $total_smtp_transfer + $total_pop_transfer + $total_imap_transfer);

	$out .= "<br><h3>". _("Your domain usage and quota:") ."</h3>
	". _("Total transfered bytes this month:") ." $total_transfer<br>
	". _("Your area disk usage:") ." ".smartByte($du)." / $quota MBytes<br>
	". _("Mailboxes:") ." $email_nbr / $max_email<br>
	". _("FTP accounts:") ." $ftp_nbr / $max_ftp<br>
	". _("Subdomains:") ." $subdomain_nbr / $max_subdomain<br><br>";

	$out .= "<h3>". _("Preview URL:") ."</h3>
	". _("Use") ." http(s)://".$conf_administrative_site."/www.".$_REQUEST["addrlink"]." ". _("aliasing") .":";

	if($eddomain["gen_unresolved_domain_alias"] == "yes"){
		$radio_yes = " checked";
		$radio_no = "";
	}else{
		$radio_no = " checked";
		$radio_yes = "";
	}

	$out .= "<form action=\"?\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"radio\" name=\"domain_gen_unresolv_alias\" value=\"yes\"$radio_yes>"._("Yes")."
<input type=\"radio\" name=\"domain_gen_unresolv_alias\" value=\"no\"$radio_no>"._("No")."
<input type=\"hidden\" name=\"change_unresolv_alias\" value=\"Ok\"><br>".submitButtonStart()._("Ok").submitButtonEnd()."</form><br><br>";

	$out .= "<h3>". _("Domain parking:") ."</h3>";
	$out .= _("This domain will be an alias of the following domain (domain parking):");
	$out .= "<form action=\"?\"><input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"addrlink\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"edit_domain\" value=\"".$_REQUEST["addrlink"]."\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"set_domain_parcking\" value=\"Ok\">
<select name=\"domain_parking_value\">
<option value=\"no-parking\">". _("No parking") ."</option>
";
	$q = "SELECT name FROM $pro_mysql_domain_table WHERE owner='$adm_login' AND domain_parking='no-parking' AND name NOT LIKE '".$_REQUEST["addrlink"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" line ".__LINE__." in file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	for($i=0;$i<$n;$i++){
		$a = mysql_fetch_array($r);
		if($domain_parking == $a["name"]){
			$checked = " selected ";
		}else{
			$checked = "";
		}
		$out .= "<option value=\"".$a["name"]."\"$checked>".$a["name"]."</option>";
	}

	$out .= "</select><br>";
	$redirect_selected = " ";
	$same_docroot_selected = " ";
	$serveralias_selected = " ";
	switch ($domain_parking_type) {
		case "redirect":
			$redirect_selected = " checked ";
			break;
		case "same_docroot":
			$same_docroot_selected = " checked ";
			break;
		case "serveralias":
			$serveralias_selected = " checked ";
			break;
	}
	$out .= "<input type=\"radio\" name=\"domain_parking_type\" value=\"redirect\" $redirect_selected>" ._("Redirection")." ";
	$out .= "<input type=\"radio\" name=\"domain_parking_type\" value=\"same_docroot\" $same_docroot_selected>" ._("Same DocumentRoot")." ";
	$out .= "<input type=\"radio\" name=\"domain_parking_type\" value=\"serveralias\" $serveralias_selected>" ._("ServerAlias")." ";
	$out .= "<br>".submitButtonStart()._("Ok").submitButtonEnd()."</form><br><br>";

	$out .= "<h3>". _("Domain configuration backup:") ."</h3>";
	$out .= "<a href=\"?adm_login=$adm_login&adm_pass=$adm_pass&action=export_domain&addrlink=".$_REQUEST["addrlink"]."\" target=\"_blank\">". _("Export this domain to a file") ."</a>";

	$out .= "<br><br>".helpLink("UserDoc/Domain-General-Config");
	return $out;
}

?>
