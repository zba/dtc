<?php

require_once 'XML/Serializer.php';
require_once "XML/Unserializer.php";

function exportTransactions(){
	global $secpayconf_currency_letters;
	get_secpay_conf();
	//$secpayconf_currency_letters = "EUR";
	$request_date = $_REQUEST["date"];

	$ar = array(
		"SIGNONMSGSRSV1" => array(
			"SONRQ" => array(
				"SATUS" => array(
					"CODE" => "0",
					"SEVERITY" => "INFO"
					),
				"DTSERVER" => date("YmdHis").".000[UTC]",
				"USERPASS" => "1234",
				"LANGUAGE" => "ENG"
				)
			),
		"BANKMSGSRSV1" => array(
			"STMTTRNRS" => array(
				"TRNUID" => "12345",
				"STATUS" => array(
					"CODE" => "0",
					"SEVERITY" => "INFO"
					)
				),
				"STMTR" => array(
					"CURDEF" => $secpayconf_currency_letters,
				),
				"BANKTRANLIST" => array(
					"DTSTART" => "",
					"DTEND" => "",
					"STMTTRN" => array(
						"TRNTYPE" => "CREDIT",
						"DTPOSTED" => "",
						"DTUSER" => "",
						"TRNAMT" => "200.12",
						"NAME" => "John Doe",
						"MEMO" => "Titi"
					)
				)
			)
		);

	// Serialize into a XML document
	$options = array(
		"indent"		=> "\t",
		"linebreak"		=> "\n",
		"addDecl"		=> true,   
		"encoding"		=> "UTF-8",
		"rootName"		=> "OFX",
		"defaultTagName"	=> "item",
		"attributesArray"	> "_attributes"
	);
	$serializer = new XML_Serializer($options);
	$serializer->serialize($ar);
	$xml = $serializer->getSerializedData();
	return $xml;
}

header ("Content-type: xml/OFX");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");	// Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// always modified
header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");	// HTTP/1.0

die( exportTransactions()."\n" );

?>