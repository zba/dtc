<?php

$panel_type="client";
require_once("../shared/autoSQLconfig.php");
// All shared files between DTCadmin and DTCclient
require_once("$dtcshared_path/dtc_lib.php");
require_once("login.php");

// The FPDF lib
if(file_exists("/usr/share/fpdf/fpdf.php")){
	define('FPDF_FONTPATH','/usr/share/fpdf/font/');
	require('/usr/share/fpdf/fpdf.php');
}else{
	if(file_exists("/usr/local/share/fpdf/fpdf.php")){
		define('FPDF_FONTPATH','/usr/local/share/fpdf/font/');
		require('/usr/local/share/fpdf/fpdf.php');
	}else{
		if(file_exists("$dtcshared_path/fpdf/fpdf.php")){
			define('FPDF_FONTPATH',"$dtcshared_path/fpdf/font/");
			require("$dtcshared_path/fpdf/fpdf.php");
		}else{
			die("FPDF library not found. Pleasse install it in $dtcshared_path/fpdf");
		}
	}
}

if(!isset($_REQUEST["download_pass"])){
  die("Download password not set!");
}
if(!isset($_REQUEST["id"])){
  die("Invoice ID not set!");
}
if(!isRandomNum($_REQUEST["download_pass"])){
  die("Download pass don't seem correct!");
}
if(!isRandomNum($_REQUEST["id"])){
  die("Invoice ID don't seem correct!");
}

global $invoice_dtctext_number;
$invoice_dtctext_number = "1";
// Get the completed order from table
$q = "SELECT * FROM $pro_mysql_completedorders_table WHERE id='".$_REQUEST["id"]."' AND download_pass='".$_REQUEST["download_pass"]."';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Could not found the invoice or download_pass not correct");
}
$completedorder = mysql_fetch_array($r);

// Get the client file
$q = "SELECT * FROM $pro_mysql_client_table WHERE id='".$completedorder["id_client"]."';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Could not found the client file");
}
$client = mysql_fetch_array($r);

// Guess the company ID depending on the service location, then client country
$company_id = findInvoicingCompany ($completedorder["country_code"],$client["country"]);

// Get the company information
$q = "SELECT * FROM $pro_mysql_companies_table WHERE id='$company_id' LIMIT 1";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Could not get company informations");
}
$company = mysql_fetch_array($r);

if($completedorder["product_id"] != 0){
	$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$completedorder["product_id"]."';";
	$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
	$n = mysql_num_rows($r);
	if($n != 1){
		die("Could not find the product");
	}
	$product = mysql_fetch_array($r);
	$price_dollar = $product["price_dollar"];
	$price_dollar += $product["setup_fee"];
}else{
	$servs = explode("|",$completedorder["services"]);
	$lasts = explode("|",$completedorder["last_expiry_date"]);
	$n_servs = sizeof($servs);
	if($n_servs < 1){
		die("Could not find the product");
	}
	$product = array();
	$price_dollar = 0;
	for($j=0;$j<$n_servs;$j++){
		$attrs = explode(":",$servs[$j]);
		switch($attrs[0]){
		case "vps":
			$ind = 3;
			break;
		case "server":
			$ind = 2;
			break;
		default:
			die("Could not find the product");
			break;
		}
		$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$attrs[$ind]."';";
		$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
		$n = mysql_num_rows($r);
		if($n != 1){
			die("Could not find the product");
		}
		$one_prod = mysql_fetch_array($r);
		$one_prod["last_expiry_date"] = $lasts[$j];
		$price_dollar += $one_prod["price_dollar"];
		$price_dollar += $one_prod["setup_fee"];
		$product[] = $one_prod;
	}
}
$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$completedorder["payment_id"]."';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Could not find payment in table");
}
$pay = mysql_fetch_array($r);

$ze_date = explode("-",$completedorder["date"]);
$pt_date = $ze_date[0].$ze_date[1].$ze_date[2];
$pt_date_plus = 100000000 + $completedorder["id"];
$text_number = $pt_date . $pt_date_plus;
$invoice_dtctext_number = $text_number;

//$cc_europe
// Check if the customer is a company, is in EU, and has VAT number, in which case there will be no VAT
$eu_vat_warning = "no";
if($company["vat_number"] != "" && $company["vat_rate"] != "0.00"){
	$use_vat = "yes";
	// Both companies are in europe, in different countries, and customer as a VAT number,
	// then there is no VAT and the customer shall pay the VAT in it's own country
	if( isset($cc_europe[ $client["country"] ]) && isset($cc_europe[ $company["country"] ]) && $client["country"] != $company["country"] && $client["is_company"] && $client["vat_num"] != ""){
		$use_vat = "no";
		$eu_vat_warning = "yes";
	}else{
		$use_vat = "yes";
	}
}else{
	$use_vat = "no";
}
get_secpay_conf();

class zPDF extends FPDF{
	function Header (){
		global $company;
		global $cc_code_array;
		global $conf_generated_file_path;
		global $client;
		global $product;
		global $completedorder;
		global $pay;
		global $eu_vat_warning;
		global $use_vat;
		global $secpayconf_currency_letters;
		global $price_dollar;
		global $invoice_dtctext_number;

		// First line
		$first_line = $company["name"];
		$first_line .= ", " . str_replace("\n",", ",str_replace("\r","",$company["address"]));
		$first_line .= ", " . $cc_code_array[ $company["country"] ];
		$this->SetXY(10, 10);
		$this->SetFont('Arial','',8);
		$this->Cell(30,20,$first_line);
		// Company logo
		$this->SetXY(10,20);
		if($company["logo_path"] != "none" && $company["logo_path"] != ""){
			$this->Image("$conf_generated_file_path/invoice_pics/".$company["logo_path"],10,22,80);
		}
		$this->SetXY(130,20);
		$this->SetFont('Arial','BI',24);
		$this->Cell(40,22,"Invoice");
		$this->SetXY(130,28);
		$this->SetFont('Arial','I',12);
		$ze_date = explode("-",$completedorder["date"]);
		$pt_date = $ze_date[0].$ze_date[1].$ze_date[2];
		$pt_date_plus = 100000000 + $completedorder["id"];
		$text_number = $pt_date . $pt_date_plus;
		$invoice_dtctext_number = $text_number;
		$this->Cell(40,22,"Number: $text_number");
		$this->SetXY(130,34);
		$this->Cell(40,22,"Payid: ".$pay["id"]);
		$this->SetXY(130,40);
		$this->Cell(40,22,"Payment date: ".$pt_date);
		// From:
		$this->SetXY(10,50);
		$this->SetFont('Arial','BU',12);
		$this->Cell(30,7,"From:");
		$this->Ln();
		$this->SetFont('Arial','',12);
		$this->Cell(120,6,$company["name"]);
		$this->Ln();
		$addr = str_replace("\r","",$company["address"]);
		$addr_ar = explode("\n",$addr);
		$nbr_line = sizeof($addr_ar);
		for($i=0;$i<$nbr_line;$i++){
			$this->Cell(30,5,$addr_ar[$i]);
			$this->Ln();
		}
		$this->Cell(120,5,$cc_code_array[ $company["country"] ]);
		$this->Ln();
		if($company["vat_number"] != "" && $company["vat_rate"] != "0.00"){
			$this->Cell(120,6,"VAT number: ".$company["vat_number"]);
			$this->Ln();
		}
		$this->Ln();$this->Ln();
		$left = $this->GetY();
		// To:
		$this->SetXY(105,50);
		$this->SetFont('Arial','BU',12);
		$this->Cell(30,7,"To:");
		$this->Ln();
		$this->SetX(105);
		$this->SetFont('Arial','',12);
		$this->Cell(120,6,$client["christname"]." ".$client["familyname"]);
		$this->Ln();$this->SetX(105);
		if($client["is_company"]){
			$this->Cell(120,6,$client["company_name"]);
			$this->Ln();$this->SetX(105);
		}
		$this->Cell(120,6,$client["addr1"]);
		$this->Ln();$this->SetX(105);
		if($client["addr2"] != ""){
			$this->Cell(120,6,$client["addr2"]);
			$this->Ln();$this->SetX(105);
		}
		if($client["addr3"] != ""){
			$this->Cell(120,6,$client["addr3"]);
			$this->Ln();$this->SetX(105);
		}
		if($client["country"] == "US"){
			$this->Cell(120,6,$client["city"]);
			$this->Ln();$this->SetX(105);
			$this->Cell(120,6,$client["state"]. " " .$client["zipcode"]);
			$this->Ln();$this->SetX(105);
		}else{
			$this->Cell(120,6,$client["zipcode"]. " " .$client["city"]);
			$this->Ln();$this->SetX(105);
			if($client["state"] != ""){
				$this->Cell(120,6,$client["state"]);
				$this->Ln();$this->SetX(105);
			}
		}
		$this->Cell(120,6,$cc_code_array[$client["country"]]);
		$this->Ln();$this->SetX(105);
			if($eu_vat_warning == "yes"){
				$this->Cell(120,6,"VAT num: ".$client["vat_num"]);
				$this->Ln();$this->SetX(105);
			}
		$this->Ln();$this->Ln();
		$right = $this->GetY();
		$this->SetXY(10,max($left,$right));

		// VAT calculation
		if($use_vat == "yes"){
			$without_vat = round(($pay["paiement_total"] / (1 + ($pay["vat_rate"] / 100))),2);
			$vat = $pay["paiement_total"] - $without_vat;
		}else{
			$without_vat = $pay["paiement_total"];
			$vat = $pay["paiement_total"] - $without_vat;
		}
		$gateway_cost = $without_vat - $price_dollar;
		
		// The table
		$this->SetFont('Arial','B',11);
		$this->Cell(110,7,"Product","1",0,"L");
		$this->Cell(20,7,"Start date","1",0,"L");
		$this->Cell(20,7,"End date","1",0,"L");
		$this->Cell(35,7,"Price","1",0,"L");
		$this->SetFont('Arial','',9);
		// Single item invoice?
		if($completedorder["product_id"] != 0){
			$pname = $product["name"];
			$pprice = $product["price_dollar"];
			if($product["setup_fee"] != 0){
				$pname = $product["name"]." ("._("Product: ").$product["price_dollar"]." ".$secpayconf_currency_letters.", "._("Setup fee: ").$product["setup_fee"]." ".$secpayconf_currency_letters.")";
				$pprice += $product["setup_fee"];
			}else{
				$pname = $product["name"];
			}
			$this->Ln();
			$this->SetFont('Arial','',9);
			$this->Cell(110,7,$pname,"1",0,"L");
			$this->Cell(20,7,$completedorder["last_expiry_date"],"1",0,"L");
			$date_expire = calculateExpirationDate($completedorder["last_expiry_date"],$product["period"]);
			$this->Cell(20,7,$date_expire,"1",0,"L");
			$this->Cell(35,7,$pprice." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();
		}else{
			$num_products = sizeof($product);
			for($i=0;$i<$num_products;$i++){
				$pname = $product[$i]["name"];
				$pprice = $product[$i]["price_dollar"];
				if($product[$i]["setup_fee"] != 0){
					$pname .= "("._("Setup fee: ").$product[$i]["setup_fee"]." ".$secpayconf_currency_letters.")";
				}
				$this->Ln();
				$this->Cell(110,7,$pname,"1",0,"L");
				$this->Cell(20,7,$product[$i]["last_expiry_date"],"1",0,"L");
				$date_expire = calculateExpirationDate($product[$i]["last_expiry_date"],$product[$i]["period"]);
				$this->Cell(20,7,$date_expire,"1",0,"L");
				$this->Cell(35,7,$pprice." ".$secpayconf_currency_letters,"1",0,"L");
			}
			$this->Ln();
		}
		$this->Cell(150,7,"Payment Gateway (".$pay["secpay_site"].")","1",0,"L");
		$this->Cell(35,7,$gateway_cost." ".$secpayconf_currency_letters,"1",0,"L");
		$this->Ln();

		// Print the VAT total, etc.
		if($use_vat == "yes"){
			$this->SetX(90);
			$this->SetFont('Arial','B',12);
			$this->Cell(70,7,"Total excluding VAT/GST:","1",0,"L");
			$this->SetFont('Arial','',12);
			$this->Cell(35,7,$without_vat." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();

			$this->SetX(90);
			$this->SetFont('Arial','B',12);
			$this->Cell(70,7,"Total VAT/GST (".$pay["vat_rate"]."%):","1",0,"L");
			$this->SetFont('Arial','',12);
			$this->Cell(35,7,$vat." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();

			$this->SetX(90);
			$this->SetFont('Arial','B',12);
			$this->Cell(70,7,"Total paid with VAT/GST:","1",0,"L");
			$this->Cell(35,7,$pay["paiement_total"]." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();
		}else{
			$this->SetX(90);
			$this->SetFont('Arial','B',12);
			$this->Cell(70,7,"Total paid:","1",0,"L");
			$this->Cell(35,7,$pay["paiement_total"]." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();
		}
		$this->Ln();


		if($eu_vat_warning == "yes"){
			$this->Cell(190,7,"Export in the EU: invoice without VAT, and customer shall pay VAT in it's own country.","1",0,"L");
			$this->Ln();
		}

		// Free text
		$this->SetFont('Arial','',10);
		$this->MultiCell(190,5,stripslashes($company["text_after"]));
	}
	function Footer (){
		global $company;
		// The footer
		$this->SetXY(10,255);
		$this->SetFont('Courier','',6);
		$this->MultiCell(190,3,stripslashes($company["footer"]),"0","C");
	}
}
$pdf=new zPDF('P','mm','A4');
$comp = str_replace(" ","_",$company["name"]);
if(strlen($client["company_name"]) > 0){
	$cl = $client["company_name"] . "_";
}else{
	$cl = "";
}
$cl = $cl . str_replace(" ","_",$client["familyname"]) . "_" . str_replace(" ","_",$client["christname"]);
$pdf->Output($completedorder["date"].'_'.$invoice_dtctext_number.'_'.$comp.'_'.$cl.'_'.$pay["paiement_total"]."_".$secpayconf_currency_letters.'.pdf','I');

?>
