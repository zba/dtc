<?php
///////////////////////////////////////////
// Commercial information about a client //
// (admin, or user, call it whatever...) //
///////////////////////////////////////////
function drawClientInfo($admin){
	global $PHP_SELF;
	global $adm_pass;
	global $adm_login;
	global $lang;
	global $txt_draw_client_info_comp_name;
	global $txt_draw_client_info_addr;
	global $txt_draw_client_info_zipcode;
	global $txt_draw_client_info_country;
	global $txt_draw_client_info_city;
	global $txt_draw_client_info_phone;
	global $txt_draw_client_info_fax;
	global $txt_draw_client_info_email;

	$client = $admin["client"];
	if($client == "NULL"){
		return "No information fetched";
	}else{
		// Fetch all the admin info in more easy variables !
		$company_name = $client["company_name"];
		$lastname = $client["lastname"];
		$firstname = $client["firstname"];
		$addr = $client["addr"];
		$city = $client["city"];
		$zipcode = $client["zipcode"];
		$country = $client["country"];
		$tel = $client["tel"];
		$fax = $client["fax"];
		$email = $client["email"];
		$special_note = $client["special_note"];
		$id_client = $client["id_client"];

		// Draw the client general info (name, addr, etc...)
		$client_info_txt = "<u><b>$firstname $lastname :</b></u><br>
<font size=\"-1\">
".$txt_draw_client_info_comp_name[$lang]."<b>$company_name</b><br>
".$txt_draw_client_info_addr[$lang]."<b>$addr</b><br>
".$txt_draw_client_info_zipcode[$lang]."<b>$zipcode</b><br>
".$txt_draw_client_info_city[$lang]."<b>$city</b><br>
".$txt_draw_client_info_country[$lang]."<b>$country</b><br>
".$txt_draw_client_info_phone[$lang]."<b>$tel</b> ".$txt_draw_client_info_fax[$lang]." <b>$fax</b><br>
".$txt_draw_client_info_email[$lang]."<b><a href=\"mailto:$email\">$email</a></b>
</font>";

		// Draw the client command table
		$commands = fetchCommands($id_client);

		if($commands["err"] == 0){
			$cmds = $commands["data"];
			$num_cmd = sizeof($cmds);
			$cmds_txt .= "<u>Commands list:</u>
<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\">
<tr><td>
	Type
</td><td>
	Domain
</td><td>
	Quota
</td><td>
	Price FF
</td><td>
	Price e
</td><td>
	Payement
</td><td>
	date
</td><td>
	Periode
</td><td>
	Ref commande
</td></tr>
";
			for($i=0;$i<$num_cmd;$i++){
				$cmd = $cmds[$i];
				$type_commande = $cmd["type_commande"];
				$nom_domaine = $cmd["nom_domaine"];
				$packMo = $cmd["packMo"];
				$prixff_commande = $cmd["prixff_commande"];
				$prixE_commande = $cmd["prixE_commande"];
				$moyen_paiement = $cmd["moyen_paiement"];
				$date = $cmd["date"];
				$duree_contrat = $cmd["duree_contrat"];
				$ref_command = $cmd["ref_commande"];
				if($i%2){
					$tbl_bg_color = "#000088";
				}else{
					$tbl_bg_color = "";
				}

				$cmds_txt .= "
<tr><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$type_commande</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$nom_domaine</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$packMo</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$prixff_commande&nbsp;FF</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$prixE_commande&nbsp;eur</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$moyen_paiement</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$date</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$duree_contrat</font>
</td><td bgcolor=\"$tbl_bg_color\">
	<font size=\"-1\">$ref_command</font>
</td></tr>";
			}
			$cmds_txt .= "</table>";
		}

		$notes_client_txt = "<form action=\"$PHP_SELF\">
<input type=\"hidden\" name=\"adm_login\" value=\"$adm_login\">
<input type=\"hidden\" name=\"adm_pass\" value=\"$adm_pass\">
<input type=\"hidden\" name=\"id_client\" value=\"$id_client\">
<TEXTAREA name=\"new_note_text\" rows=\"7\" cols=\"40\">
$special_note
</TEXTAREA>
<input type=\"submit\" name=\"modify_client_note\" value=\"Ok\">
</form>
";

		return "<table><tr><td valign=\"top\">$client_info_txt</td><td>$notes_client_txt</td></Table>
$cmds_txt
</font>";
	}
}

?>