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
	if(file_exists("$dtcshared_path/fpdf/fpdf.php")){
		define('FPDF_FONTPATH',"$dtcshared_path/fpdf/font/");
		require("$dtcshared_path/fpdf/fpdf.php");
	}else{
		die("FPDF library not found. Pleasse install it in $dtcshared_path/fpdf");
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

$q = "SELECT * FROM $pro_mysql_product_table WHERE id='".$completedorder["product_id"]."';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Could not found the product");
}
$product = mysql_fetch_array($r);

$q = "SELECT * FROM $pro_mysql_pay_table WHERE id='".$completedorder["payment_id"]."';";
$r = mysql_query($q)or die("Cannot query $q line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
$n = mysql_num_rows($r);
if($n != 1){
	die("Could not find payment in table");
}
$pay = mysql_fetch_array($r);
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
//		$pt_date = $pt_date * 10000;
//		$pt_date += $completedorder["id"];
		$pt_date_plus = 100000000 + $completedorder["id"];
		$text_number = $pt_date . $pt_date_plus;
		$this->Cell(40,22,"Number: $text_number");
		$this->SetXY(130,34);
		$this->Cell(40,22,"Payid: ".$pay["id"]);
		$this->SetXY(130,40);
		$this->Cell(40,22,"Payment date: ".$pt_date);
		//header("Content-Disposition: attachment; filename=\"".$pt_date_plus."_gplhost.pdf\"");
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
		
		// The table
		$this->SetFont('Arial','B',11);
		$this->Cell(80,7,"Product","1",0,"L");
		$this->Cell(20,7,"Start date","1",0,"L");
		$this->Cell(20,7,"End date","1",0,"L");
		$this->Cell(15,7,"Price","1",0,"L");
		$this->Cell(25,7,"Pay Gateway","1",0,"L");
		if($use_vat == "yes"){
			$this->Cell(30,7,"Total with VAT","1",0,"L");
		}else{
			$this->Cell(30,7,"Total","1",0,"L");
		}
		$this->Ln();
		$this->SetFont('Arial','',10);
		$this->Cell(80,7,$product["name"],"1",0,"L");
		$this->Cell(20,7,$completedorder["last_expiry_date"],"1",0,"L");
		$date_expire = calculateExpirationDate($completedorder["last_expiry_date"],$product["period"]);
		$this->Cell(20,7,$date_expire,"1",0,"L");
		$this->Cell(15,7,$product["price_dollar"]." ".$secpayconf_currency_letters,"1",0,"L");
		$this->Cell(25,7,$pay["paiement_cost"]." ".$secpayconf_currency_letters,"1",0,"L");
		$this->Cell(30,7,$pay["paiement_total"]." ".$secpayconf_currency_letters,"1",0,"L");
		$this->Ln();

		// VAT calculation
		if($use_vat == "yes"){
			$vat = round(($pay["paiement_total"] * $pay["vat_rate"] / 100),2);
			$without_vat = $pay["paiement_total"] - $vat;

			$this->SetX(120);
			$this->SetFont('Arial','B',12);
			$this->Cell(50,7,"Total VAT (".$pay["vat_rate"]."%):","1",0,"L");
			$this->SetFont('Arial','',12);
			$this->Cell(30,7,$vat." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();
			
			$this->SetX(120);
			$this->SetFont('Arial','B',12);
			$this->Cell(50,7,"Total excluding VAT:","1",0,"L");
			$this->SetFont('Arial','',12);
			$this->Cell(30,7,$without_vat." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();
			
			$this->SetX(120);
			$this->SetFont('Arial','B',12);
			$this->Cell(50,7,"Total paid:","1",0,"L");
			$this->SetFont('Arial','',12);
			$this->Cell(30,7,$pay["paiement_total"]." ".$secpayconf_currency_letters,"1",0,"L");
			$this->Ln();
		}

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
//$pdf->Cell(40,10,"Product");
$pdf->Output();

?>
