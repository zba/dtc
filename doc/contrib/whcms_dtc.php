<?php

function dtc_ConfigOptions() {
	# Should return an array of the module options for each product - maximum of 24
    $configarray = array(
	 "Package Name" => array( "Type" => "text", "Size" => "25", ),
	 "Web Space Quota" => array( "Type" => "text", "Size" => "5", "Description" => "MB" ),
	 "FTP Access" => array( "Type" => "yesno", "Description" => "Tick to grant access" ),
     "Subdomains" => array( "Type" => "dropdown", "Options" => "1,2,5,10,25,50,Unlimited"),
	);
	return $configarray;
}

function dtc_CreateAccount($params) {

    # ** The variables listed below are passed into all module functions **

    $serviceid = $params["serviceid"]; # Unique ID of the product/service in the WHMCS Database
    $pid = $params["pid"]; # Product/Service ID
    $producttype = $params["producttype"]; # Product Type: hostingaccount, reselleraccount, server or other
    $domain = $params["domain"];
	$username = $params["username"];
	$password = $params["password"];
    $clientsdetails = $params["clientsdetails"]; # Array of clients details - firstname, lastname, email, country, etc...
    $customfields = $params["customfields"]; # Array of custom field values for the product
    $configoptions = $params["configoptions"]; # Array of configurable option values for the product

    # Product module option settings from ConfigOptions array above
    $configoption1 = $params["configoption1"];
    $configoption2 = $params["configoption2"];
    $configoption3 = $params["configoption3"];
    $configoption4 = $params["configoption4"];

    # Additional variables if the product/service is linked to a server
    $server = $params["server"]; # True if linked to a server
    $serverid = $params["serverid"];
    $serverip = $params["serverip"];
    $serverusername = $params["serverusername"];
    $serverpassword = $params["serverpassword"];
    $serveraccesshash = $params["serveraccesshash"];
    $serversecure = $params["serversecure"]; # If set, SSL Mode is enabled in the server config

	# Code to perform action goes here...

	if ($successful) {
		$result = "success";
	} else {
		$result = "Error Message Goes Here...";
	}
	return $result;
}

function dtc_TerminateAccount($params) {

	# Code to perform action goes here...

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error Message Goes Here...";
	}
	return $result;
}
/*
function dtc_SuspendAccount($params) {

	# Code to perform action goes here...

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error Message Goes Here...";
	}
	return $result;
}

function dtc_UnsuspendAccount($params) {

	# Code to perform action goes here...

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error Message Goes Here...";
	}
	return $result;
}

function dtc_ChangePassword($params) {

	# Code to perform action goes here...

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error Message Goes Here...";
	}
	return $result;
}
*/

function dtc_ChangePackage($params) {

	# Code to perform action goes here...

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error Message Goes Here...";
	}
	return $result;
}

function dtc_ClientArea($params) {
	$code = "<form action=\"https://".$serverip."/dtc/\" method=\"post\" target=\"_blank\">
<input type=\"hidden\" name=\"adm_login\" value=\"".$params["username"]."\" />
<input type=\"hidden\" name=\"adm_pass\" value=\"".$params["password"]."\" />
<input type=\"submit\" value=\"Login to Control Panel\" />
<input type=\"button\" value=\"Login to Webmail\" onClick=\"window.open('https://".$serverip."/squirrelmail/')\" />
</form>";
	return $code;
}

function dtc_AdminLink($params) {
	$code = "<form action=\"http://".$params["serverip"]."/dtcadmin/\" method=\"post\" target=\"_blank\">
<input type=\"submit\" value=\"Login to Control Panel\" />
</form>";
	return $code;
}

function dtc_LoginLink($params) {
	echo "<a href=\"https://".$params["serverip"]."/dtc/?adm_login=".$params["username"]."&adm_pass=".$params["password"]."\" target=\"_blank\" style=\"color:#cc0000\">login to control panel</a>";
}

/*
function dtc_AdminCustomButtonArray() {
	# This function can define additional functions your module supports, the example here is a reboot button and then the reboot function is defined below
    $buttonarray = array(
	 "Reboot Server" => "reboot",
	);
	return $buttonarray;
}


function dtc_reboot($params) {

	# Code to perform action goes here...

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error Message Goes Here...";
	}
	return $result;
}
*/

?>
