<?php


function worldPayButton($pay_id,$amount){
	global $wp_instid;
	global $wp_curency;
	global $pro_mysql_client_table;
	global $wp_testmode;
	global $wp_accId1;
	global $lang;
	global $conf_administrative_site;
	global $wp_callback_url;

	global $pro_mysql_pay_table;

	$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='$pay_id';";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)die("Client id not found !");
	$pay_row = mysql_fetch_array($r);

	$q = "SELECT * FROM $pro_mysql_client_table WHERE id='$client_id';";
	$r = mysql_query($q)or die("Cannot query: \"$q\" !".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)die("Client id not found !");
	$ar = mysql_fetch_array($r);

	$out = '
<form action="https://select.worldpay.com/wcc/purchase" method="POST">
<input type="hidden" name="MC_callback" value="'.$conf_administrative_site.$wp_callback_url.'">
<input type="hidden" name="instId" value="'.$wp_instid.'">
<input type="hidden" name="cartId" value="'.$pay_id.'">
<input type="hidden" name="amount" value="'.$amount.'">
<input type="hidden" name="currency" value="'.$wp_curency.'">
<input type="hidden" name="desc" value="'.$text_info.'">
<input type="hidden" name="testMode" value="'.$wp_testmode.'">

<input type="hidden" name="fixContact" value="yes">
<input type="hidden" name="email" value="'.$ar["email"].'">
<input type="hidden" name="name" value="'.$ar["familyname"].', '.$ar["christname"].'">
<input type="hidden" name="address" value="'.$ar["addr1"].' '.$ar["addr2"].' '.$ar["addr3"].','.$ar["city"].' '.$ar["state"].'">
<input type="hidden" name="postcode" value="'.$ar["zipcode"].'">
<input type="hidden" name="country" value="'.$ar["country"].'">
<input type="hidden" name="tel" value="'.$ar["phone"].'">
<input type="hidden" name="fax" value="'.$ar["fax"].'">

<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="noLanguageMenu" value="yes">
';
	if($wp_accId1 != ""){
		$out .= '<input type=hidden name="accId1" value="'.$wp_accId1.'">';
	}
	$out .= '<input type=submit value="'.$button_text.'"> </form>';
	return $out;

}

function logErrorInCallback($error_step){
	return $_REQUEST["rawAuthMessage"];
}

function worldPayCallBack(){
	global $wp_instid;
	global $wp_curency;
	global $pro_mysql_client_table;
	global $pro_mysql_command_table;
	global $wp_testmode;
	global $wp_callback_pass;
	global $lang;
	global $PHP_SELF;

	$repost_form = '
<FORM ACTION="'.$PHP_SELF.'" METHOD="POST">
<INPUT TYPE=HIDDEN NAME="instId" VALUE="<WPDISPLAY ITEM=instId>">
<INPUT TYPE=HIDDEN NAME="email" VALUE="<WPDISPLAY ITEM=email>">
<INPUT TYPE=HIDDEN NAME="transTime" VALUE="<WPDISPLAY ITEM=transTime>">
<INPUT TYPE=HIDDEN NAME="country" VALUE="<WPDISPLAY ITEM=country>">
<INPUT TYPE=HIDDEN NAME="rawAuthCode" VALUE="<WPDISPLAY ITEM=rawAuthCode>">
<INPUT TYPE=HIDDEN NAME="amount" VALUE="<WPDISPLAY ITEM=amount>">
<INPUT TYPE=HIDDEN NAME="installation" VALUE="<WPDISPLAY ITEM=installation>">
<INPUT TYPE=HIDDEN NAME="tel" VALUE="<WPDISPLAY ITEM=tel>">
<INPUT TYPE=HIDDEN NAME="address" VALUE="<WPDISPLAY ITEM=address>">
<INPUT TYPE=HIDDEN NAME="futurePayId" VALUE="<WPDISPLAY ITEM=futurePayId>">
<INPUT TYPE=HIDDEN NAME="MC_log" VALUE="<WPDISPLAY ITEM=MC_log>">
<INPUT TYPE=HIDDEN NAME="rawAuthMessage" VALUE="<WPDISPLAY ITEM=rawAuthMessage>">
<INPUT TYPE=HIDDEN NAME="authAmount" VALUE="<WPDISPLAY ITEM=authAmount>">
<INPUT TYPE=HIDDEN NAME="amountString" VALUE="<WPDISPLAY ITEM=amountString>">
<INPUT TYPE=HIDDEN NAME="cardType" VALUE="<WPDISPLAY ITEM=cardType>">
<INPUT TYPE=HIDDEN NAME="AVS" VALUE="<WPDISPLAY ITEM=AVS>">
<INPUT TYPE=HIDDEN NAME="cost" VALUE="<WPDISPLAY ITEM=cost>">
<INPUT TYPE=HIDDEN NAME="currency" VALUE="<WPDISPLAY ITEM=currency>">
<INPUT TYPE=HIDDEN NAME="testMode" VALUE="<WPDISPLAY ITEM=testMode>">
<INPUT TYPE=HIDDEN NAME="authAmountString" VALUE="<WPDISPLAY ITEM=authAmountString>">
<INPUT TYPE=HIDDEN NAME="fax" VALUE="<WPDISPLAY ITEM=fax>">
<INPUT TYPE=HIDDEN NAME="lang" VALUE="<WPDISPLAY ITEM=lang>">
<INPUT TYPE=HIDDEN NAME="transStatus" VALUE="<WPDISPLAY ITEM=transStatus>">
<INPUT TYPE=HIDDEN NAME="compName" VALUE="<WPDISPLAY ITEM=compName>">
<INPUT TYPE=HIDDEN NAME="authCurrency" VALUE="<WPDISPLAY ITEM=authCurrency>">
<INPUT TYPE=HIDDEN NAME="postcode" VALUE="<WPDISPLAY ITEM=postcode>">
<INPUT TYPE=HIDDEN NAME="authCost" VALUE="<WPDISPLAY ITEM=authCost>">
<INPUT TYPE=HIDDEN NAME="countryMatch" VALUE="<WPDISPLAY ITEM=countryMatch>">
<INPUT TYPE=HIDDEN NAME="cartId" VALUE="<WPDISPLAY ITEM=cartId>">
<INPUT TYPE=HIDDEN NAME="transId" VALUE="<WPDISPLAY ITEM=transId>">
<INPUT TYPE=HIDDEN NAME="callbackPW" VALUE="<WPDISPLAY ITEM=callbackPW>">
<INPUT TYPE=HIDDEN NAME="authMode" VALUE="<WPDISPLAY ITEM=authMode>">
<INPUT TYPE=HIDDEN NAME="countryString" VALUE="<WPDISPLAY ITEM=countryString>">
<INPUT TYPE=HIDDEN NAME="name" VALUE="<WPDISPLAY ITEM=name>">
<INPUT TYPE=SUBMIT VALUE="Return to web site">
</FORM>';

	// Check the transStatus
	if($_REQUEST["transStatus"] != "Y"){
		// See what's going wrong here ! :)
		if($_REQUEST["transStatus"] != "C"){
			// Transaction has been canceled
		}
		return;
	}
	echo "transStatus = Y<br>";

	if($_REQUEST["cartId"] == "" || !is_set($_REQUEST["cartId"])){
		// No command id
		return;
	}
	$q = "SELECT * FROM $pro_mysql_command_table WHERE id='".$_REQUEST["cartId"]."';";
	$r = mysql_query($q)or die("Cannot query \"$q\" !".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1)die("Command id not found in db !!!");
	$cmd = mysql_fetch_array($ar);
	echo "Command found in db = ok<br>";

	// Check the callbackPW
	if($wp_callback_pass != ""){
		if($_REQUEST["callbackPW"] != $wp_callback_pass){
			// Password does not match, abbord...
			return;
		}
	}
	echo "Password check = ok<br>";

	// Do a basic ip check if field $wp_servers_ip is setup
	if($wp_servers_ip != ""){
		$ipfrom = $_SERVER["REMOTE_ADDR"];
		$autorized_ips = explode("|",$wp_servers_ip);
		$n = sizeof($autorized_ips);
		$check=false;
		for($i=0;$i<$n;$i++){
			if($autorized_ips[$i] == $ipfrom)	$check=true;
		}
		if($check == false){
			// Ip check failed
			return;
		}
	}
	echo "IP check = ok<br>";

	$amnt = explode('n',$_REQUEST["ConvertedAmount"]);
	$transaction_price = $amnt[1];
	$transaction_currency = $amnt[0];
	if($cmd["price"] != $transaction_price || $cmd["price_devise"] != $_REQUEST["currency"]){
		// Price or currency does not match !!
		return;
	}
	echo "Price and curency = ok<br>";

	$out .= "<WPDISPLAY ITEM=banner>":
/*	Autorised transfaction example :
	instId=38290
	email=tiq%40uk.worldpay.com
	transTime=999178402000&
	country=GB&
	rawAuthCode=A&
	amount=14.99&
	installation=38290&
	tel=0123+456789012&
	address=Test+Road%0D%0ATest+Town%0D%0ATest+City&
	futurePayId=76486&
	MC_log=2379&
	rawAuthMessage=authorised+(testMode+always+Yes)&
	authAmount=23.11&
	amountString=%26%23163%3B14.99&
	cardType=Visa&
	AVS=0001&
	cost=14.99&
	currency=GBP&
	testMode=100&
	authAmountString=EUR23.11&
	fax=01234+5678901&
	lang=en&
	transStatus=Y&
	compName=Ian+Richardson&
	authCurrency=EUR&
	postcode=AB1+2CD&authCost=23.11&
	desc=Test+Item&
	countryMatch=S&
	cartId=Test+Item&
	transId=12227758&
	callbackPW=38290&
	M_var1=fred&
	authMode=E&
	countryString=United+Kingdom&
	name=WorldPay+Test */

/*	instId=38290&
	email=tiq%40uk.worldpay.com&
	country=GB&
	amount=14.99&
	installation=38290&
	tel=0123+456789012&
	address=Test+Road%0D%0ATest+Town%0D%0ATest+City&
	MC_log=2379&
	amountString=%26%23163%3B14.99&
	cost=14.99&
	currency=GBP&
	testMode=100&
	fax=01234+5678901&
	transStatus=C&
	compName=Ian+Richardson&
	postcode=AB1+2CD&
	desc=Test+Item&
	cartId=Test+Item&
	callbackPW=38290&
	M_var1=fred&
	authMode=A&
	countryString=United+Kingdom&
	name=WorldPay+Test
*/

}

?>
