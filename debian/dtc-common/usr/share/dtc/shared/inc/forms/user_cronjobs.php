<?php

function userCronCreateCallback($id){
	updateUsingCron("gen_user_cron='yes'");
	return;
}
function userCronDeleteCallback(){
	updateUsingCron("gen_user_cron='yes'");
	return;
}
function userCronEditCallback() {
	updateUsingCron("gen_user_cron='yes'");
	return;
}

function drawAdminTools_User_CronJob($admin,$domain){
	global $adm_login;
	global $adm_pass;
	global $addrlink;
	global $pro_mysql_user_cron_table;
	global $pro_mysql_subdomain_table;

	$dom_name = $domain["name"];
	checkLoginPassAndDomain($adm_login,$adm_pass,$dom_name);

	$num_subdomains = sizeof($domain["subdomains"]);
	if($num_subdomains == 0){
		return _("No subdomain for this domain: impossible to edit cron jobs!");
	}
	$subdom_popup = array();
	for($i=0;$i<$num_subdomains;$i++){
		$subdom_popup[] = $domain["subdomains"][$i]["name"];
	}

	$out = "";

	$minute_popup = array();
	$minute_display = array();
/* I found that enabling every 10 minutes would be too agressive, so I disabled it
	$minute_popup[] = "*";
	$minute_display[] = _("Every minutes");
	$minute_popup[] = "0/2";
	$minute_display[] = _("Each 2 minutes");
	$minute_popup[] = "0/3";
	$minute_display[] = _("Each 3 minutes");
	$minute_popup[] = "0/4";
	$minute_display[] = _("Each 4 minutes");
	$minute_popup[] = "0/5";
	$minute_display[] = _("Each 5 minutes");
	$minute_popup[] = "0/10"; */
	$minute_display[] = _("Each 10 minutes");
	$minute_popup[] = "0/15";
	$minute_display[] = _("Each 15 minutes");
	$minute_popup[] = "0/20";
	$minute_display[] = _("Each 20 minutes");
	$minute_popup[] = "0/30";
	$minute_display[] = _("Each 30 minutes");
	for($i=0;$i<60;$i++){
		$minute_popup[] = "is".$i;
		$minute_display[] = _("When the minute is: ").$i;
	}

	$hour_popup = array();
	$hour_display = array();
	$hour_popup[] = "*";
	$hour_display[] = _("Every hour");
	$hour_popup[] = "0/2";
	$hour_display[] = _("Each 2 hours");
	$hour_popup[] = "0/3";
	$hour_display[] = _("Each 3 hours");
	$hour_popup[] = "0/4";
	$hour_display[] = _("Each 4 hours");
	$hour_popup[] = "0/5";
	$hour_display[] = _("Each 5 hours");
	$hour_popup[] = "0/10";
	$hour_display[] = _("Each 10 hours");
	$hour_popup[] = "0/15";
	$hour_display[] = _("Each 15 hours");
	$hour_popup[] = "0/20";
	$hour_display[] = _("Each 20 hours");
	$hour_popup[] = "0/30";
	$hour_display[] = _("Each 30 hours");
	for($i=0;$i<24;$i++){
		$hour_popup[] = "is".$i;
		$hour_display[] = _("When the hour is: ").$i;
	}

	$dayofmonth_popup = array();
	$dayofmonth_display = array();
	$dayofmonth_popup[] = "*";
	$dayofmonth_display[] = _("Every day");
	for($i=1;$i<29;$i++){
		$dayofmonth_popup[] = "is".$i;
		$dayofmonth_display[] = _("Every month on the: ").$i;
	}

	$dayofweek_popup = array();
	$dayofweek_display = array();
	$dayofweek_popup[] = "*";
	$dayofweek_display[] = _("Every day");
	$dayofweek_popup[] = "1";
	$dayofweek_display[] = _("Monday");
	$dayofweek_popup[] = "2";
	$dayofweek_display[] = _("Tuesday");
	$dayofweek_popup[] = "3";
	$dayofweek_display[] = _("Wednesday");
	$dayofweek_popup[] = "4";
	$dayofweek_display[] = _("Thursday");
	$dayofweek_popup[] = "5";
	$dayofweek_display[] = _("Friday");
	$dayofweek_popup[] = "6";
	$dayofweek_display[] = _("Saturday");
	$dayofweek_popup[] = "7";
	$dayofweek_display[] = _("Sunday");


	$dsc = array(
		"title" => _("List of your cron jobs:"),
		"new_item_title" => _("New cron job") ,
		"new_item_link" => _("new cron job") ,
		"edit_item_title" => _("Cron job configuration:") ,
		"table_name" => $pro_mysql_user_cron_table,
		"action" => "user_cron_editor",
		"forward" => array("adm_login","adm_pass","addrlink"),
		"id_fld" => "id",
		"list_fld_show" => "cron_name",
		"create_item_callback" => "userCronCreateCallback",
		"delete_item_callback" => "userCronDeleteCallback",
		"edit_item_callback" => "userCronEditCallback",
		"where_list" => array(
			"domain_name" => $domain["name"]),
		"order_by" => "cron_name",
		"cols" => array(
			"id" => array(
				"type" => "id",
				"display" => "no",
				"legend" => _("Login:") ),
			"cron_name" => array (
				"type" => "text",
				"disable_edit" => "yes",
				"check" => "dtc_login_or_email",
				"legend" => _("Cron job name:") ),
			"subdomain_name" => array (
				"type" => "popup",
				"values" => $subdom_popup,
				"legend" => _("Subdomain:")
				),
			"minute" => array (
				"type" => "popup",
				"legend" => _("Minutes:"),
				"display_replace" => $minute_display,
				"values" => $minute_popup
				),
			"hour" => array (
				"type" => "popup",
				"legend" => _("Hour:"),
				"display_replace" => $hour_display,
				"values" => $hour_popup
				),
			"day_of_month" => array (
				"type" => "popup",
				"legend" => _("Day of the month:"),
				"values" => $dayofmonth_popup,
				"display_replace" => $dayofmonth_display
				),
			"dow" => array (
				"type" => "popup",
				"legend" => _("Day of the week:"),
				"values" => $dayofweek_popup,
				"display_replace" => $dayofweek_display
				),
			"uri" => array (
				"type" => "text",
				"check" => "page_url",
				"legend" => _("Address of the job on your site:")
				)
		)
	);
	$out .= dtcListItemsEdit($dsc);

	return $out;
}

?>
